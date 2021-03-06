<?php

namespace App\Http\Controllers\ResManage;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\ImageUtils;
use App\Http\Controllers\Tools\ResContentComm;
use App\Http\Controllers\Tools\StringConstants;
use App\Http\Controllers\Tools\Utils;
use DB;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Input;

//kevin
//kevin

class PackageController extends Controller
{

	private $request;

	public function __construct (Request $request)
	{
		$this->request = $request;
	}

	/**
	 * 专栏列表
	 */
	public function getPackageList ()
	{

		$state          = Input::get("state", 0);
		$search_content = Input::get("search_content", '');
		$is_distribute  = Input::get("is_distribute", -1);

		$whereRaw = '1=1';
		if ($state != -1)
			$whereRaw .= " and state = " . $state;
		if ($is_distribute != -1)
			$whereRaw .= " and is_distribute = " . $is_distribute;
		if (!Utils::isEmptyString($search_content))
			$whereRaw .= " and name like '" . "%" . $search_content . "%'";

		//获取专栏列表
		$app_id = AppUtils::getAppID();
		//初始化排序权重值
		$is_init = $this->initPackageWeight();

		$package_on_url_list = [];
		$packageListInfo     = \DB::table('t_pay_products')->where('app_id', '=', $app_id)
			->where('state', '!=', '2')
			->where('is_distribute', '!=', '2')
			->where('is_member', '=', StringConstants::NOT_MEMBER)
			->whereRaw($whereRaw)
			->orderby('order_weight', 'desc')
			->orderby('created_at', 'desc')
			->paginate(10);
		//更新专栏期数
		foreach ($packageListInfo as $key => $item) {
			//专栏链接
			$package_url                 = $this->getPackageUrl($item->id);
			$package_on_url_list[ $key ] = $package_url;
		}

		return View("admin.resManage.packageList", compact("packageListInfo", "package_on_url_list", "search_content", "state", "is_distribute"));
	}

	private function initPackageWeight ()
	{
		$app_id = AppUtils::getAppID();
		//初始化排序权重值
		$total  = \DB::select("select count(1) as count from t_pay_products where app_id = '$app_id'")[0];
		$weight = \DB::select("select count(1) as count from t_pay_products where app_id = '$app_id' and order_weight = 0 ")[0];
		$ret    = 1;

		if ($total && $weight) {
			if ($weight->count == 1) {//给最新部署的专栏初始化排序值，默认为最靠前
				$ret = \DB::update("update t_pay_products set order_weight = $total->count where order_weight = 0 and app_id = '$app_id'");
			}
			if ($weight->count == $total->count) {
				$package_list = \DB::table('t_pay_products')->where('app_id', '=', AppUtils::getAppID())
					->orderby('created_at', 'desc')->get();
				$weight       = $total->count;
				foreach ($package_list as $key => $value) {
					$ret    = \DB::update("update t_pay_products set order_weight = $weight where id = '$value->id' and app_id = '$app_id'");
					$weight -= 1;
				}
			} else if ($weight->count < $total->count && $weight->count > 1) {
				$package_list = \DB::table('t_pay_products')->where('app_id', '=', AppUtils::getAppID())->where('order_weight', '=', 0)
					->orderby('created_at', 'desc')->get();
				$weight       = $total->count;
				foreach ($package_list as $key => $value) {
					$ret    = \DB::update("update t_pay_products set order_weight = $weight where id = '$value->id' and app_id = '$app_id'");
					$weight -= 1;
				}
			}

		}

		return $ret;
	}

	private function getPackageUrl ($package_id)
	{
		$app_id  = AppUtils::getAppID();
		$appInfo = AppUtils::getAppConfInfo($app_id);
		if ($appInfo) {
			$skip_target = '';
			if ($appInfo->use_collection == 0 && !empty($appInfo->wx_app_id)) {
				$skip_target = AppUtils::getUrlHeader($app_id) . $appInfo->wx_app_id . '.' . env('DOMAIN_NAME');
			} else if ($appInfo->use_collection == 1) {
				$skip_target = AppUtils::getUrlHeader($app_id) . env('DOMAIN_DUAN_NAME');
			}

			$package_url = $skip_target . Utils::contentUrl('', 3, '', '', $package_id, $app_id);

			return $package_url;
		} else {
			return '';
		}
	}

	/**
	 * 创建专栏
	 */
	public function createPackage ()
	{

		$app_id = AppUtils::getAppID();
		//应用权限
		$appModuleInfo = AppUtils::getModuleInfo($app_id);

		// 分类数据
		if (!empty($appModuleInfo) && $appModuleInfo[0]->resource_category == 1) {
			// 分类数据信息
			$category_info = Utils::categoryInfo();
		}

		$type = 0;//新增页面

		return View("admin.resManage.managePackage", compact("type", "appModuleInfo", "category_info", "app_id"));
	}

	/**
	 * 编辑专栏
	 * 参数： package_id(专栏id)
	 */
	public function editPackage ()
	{
		$package_id = Input::get('id', '');
		//查询专栏信息
		$package_info = $this->getPackageInfo($package_id);
		$type         = 1;//编辑页面

		//应用权限
		$appModuleInfo = AppUtils::getModuleInfo(AppUtils::getAppID());

		// 分类数据
		if (!empty($appModuleInfo) && $appModuleInfo[0]->resource_category == 1) {
			// 分类数据信息
			$category_info = Utils::categoryInfo();
			// 所属分类信息
			$package_category = $this->packageCategoryInfo($package_id);
		}

		return View("admin.resManage.managePackage", compact("package_info", "type", "category_info", "package_category"));
	}

	private function getPackageInfo ($package_id)
	{
		$package = DB::table('db_ex_business.t_pay_products')
			->where('app_id', AppUtils::getAppID())
			->where('id', $package_id)
			->first();

		//查询专栏期数
		$resource_list = DB::table("t_pro_res_relation")
			->where('app_id', AppUtils::getAppID())
			->where('product_id', $package_id)
			->where('resource_type', '<=', 4)
			->where('relation_state', StringConstants::RELATION_NORMAL)
			->get();
		if ($package) {
			$package->resource_count = count($resource_list);
		}

		return $package;
	}

	//保存专栏信息

	private function packageCategoryInfo ($package_id)
	{
		$app_id = AppUtils::getAppID();
		// 所属分类信息
		$package_category = DB::connection('mysql')->table('t_category_resource')
			->select('category_id')
			->where('app_id', '=', $app_id)
			->where('category_id', '!=', '0')
			->where('resource_id', '=', $package_id)
			->where('state', '=', 1)
			->pluck('category_id');

		return $package_category;
	}

	//分类信息-新增

	/**
	 * 专栏详情页面
	 */
	public function packageDetail ()
	{

		$package_id     = Input::get('id', '');
		$search_content = Input::get('search_content', '');
		$resource_type  = Input::get("resource_type", 0);
		$state          = Input::get("state", -1);

		$app_id = AppUtils::getAppID();
		//查询专栏信息
		$package_info = $this->getPackageInfo($package_id);
        //dump($package_info);
		//专栏访问链接
		$package_url = $this->getPackageUrl($package_id);
		//专栏内单品总浏览量
		$package_singles_view_count = 0;
		//  kevin
		switch ($resource_type) {
			case StringConstants::SINGLE_GOODS_ALL://全部资源
				$res_list_sql = "
            SELECT start_at, resource_id,resource_type, product_name,product_id, title,relation_state,is_try
            FROM(
              SELECT a.resource_id, a.product_id,b.start_at,a.resource_type,a.product_name,b.title,a.relation_state ,a.is_try from t_pro_res_relation as a join t_image_text as b
            on b.id = a.resource_id and a.app_id = b.app_id where a.app_id = '$app_id' and a.product_id = '$package_id' and b.title like '%{$search_content}%' and a.relation_state = 0

            union all
              SELECT a.resource_id, a.product_id,b.start_at,a.resource_type,a.product_name,b.title,a.relation_state ,a.is_try from t_pro_res_relation as a join t_audio as b
            on b.id = a.resource_id and a.app_id = b.app_id where a.app_id = '$app_id' and a.product_id = '$package_id' and b.title like '%{$search_content}%' and a.relation_state = 0

            union all
            SELECT a.resource_id, a.product_id,b.start_at,a.resource_type,a.product_name,b.title,a.relation_state ,a.is_try from t_pro_res_relation as a join t_video as b
            on b.id = a.resource_id and a.app_id = b.app_id where a.app_id = '$app_id' and a.product_id = '$package_id' and b.title like '%{$search_content}%' and a.relation_state = 0

            union all
            SELECT a.resource_id,a.product_id,b.start_at,a.resource_type,a.product_name,b.title,a.relation_state ,a.is_try from t_pro_res_relation as a join t_alive as b
            on b.id = a.resource_id and a.app_id = b.app_id where a.app_id = '$app_id' and a.product_id = '$package_id' and b.title like '%{$search_content}%' and a.relation_state = 0
           ) tt1 order by start_at desc";
				break;
			case StringConstants::SINGLE_GOODS_ARTICLE://图文
				$res_list_sql = "SELECT a.resource_id, a.product_id,b.start_at,a.resource_type,a.product_name,b.title,a.relation_state ,a.is_try from t_pro_res_relation as a join t_image_text as b
            on b.id = a.resource_id and a.app_id = b.app_id where a.app_id = '$app_id' and a.product_id = '$package_id' and b.title like '%{$search_content}%' and a.relation_state = 0 order by start_at desc";
				break;
			case StringConstants::SINGLE_GOODS_AUDIO://音频
				$res_list_sql = "SELECT a.resource_id, a.product_id,b.start_at,a.resource_type,a.product_name,b.title,a.relation_state ,a.is_try from t_pro_res_relation as a join t_audio as b
            on b.id = a.resource_id and a.app_id = b.app_id where a.app_id = '$app_id' and a.product_id = '$package_id' and b.title like '%{$search_content}%' and a.relation_state = 0";
				break;
			case StringConstants::SINGLE_GOODS_VIDEO://视频
				$res_list_sql = "SELECT a.resource_id, a.product_id,b.start_at,a.resource_type,a.product_name,b.title,a.relation_state ,a.is_try from t_pro_res_relation as a join t_video as b
            on b.id = a.resource_id and a.app_id = b.app_id where a.app_id = '$app_id' and a.product_id = '$package_id' and b.title like '%{$search_content}%' and a.relation_state = 0 order by start_at desc";
				break;
			case StringConstants::SINGLE_GOODS_ALIVE://直播
				$res_list_sql = "SELECT a.resource_id, a.product_id,b.start_at,a.resource_type,a.product_name,b.title,a.relation_state ,a.is_try from t_pro_res_relation as a join t_alive as b
            on b.id = a.resource_id and a.app_id = b.app_id where a.app_id = '$app_id' and a.product_id = '$package_id' and b.title like '%{$search_content}%' and a.relation_state = 0 order by start_at desc";
				break;
		}
		/*     $res_list_sql="
			 SELECT start_at, resource_id,resource_type, product_name,product_id, title,relation_state,is_try
			 FROM(
				   SELECT a.resource_id, a.product_id,b.start_at,a.resource_type,a.product_name,b.title,a.relation_state ,a.is_try from t_pro_res_relation as a join t_image_text as b
				 on b.id = a.resource_id and a.app_id = b.app_id where a.product_id = '$package_id' and $whereRaw and a.relation_state = 0

				 union all
				   SELECT a.resource_id, a.product_id,b.start_at,a.resource_type,a.product_name,b.title,a.relation_state ,a.is_try from t_pro_res_relation as a join t_audio as b
				 on b.id = a.resource_id and a.app_id = b.app_id where a.product_id = '$package_id' and $whereRaw and a.relation_state = 0

				 union all
				 SELECT a.resource_id, a.product_id,b.start_at,a.resource_type,a.product_name,b.title,a.relation_state ,a.is_try from t_pro_res_relation as a join t_video as b
				 on b.id = a.resource_id and a.app_id = b.app_id where a.product_id = '$package_id' and $whereRaw and a.relation_state = 0

				 union all
				 SELECT a.resource_id,a.product_id,b.start_at,a.resource_type,a.product_name,b.title,a.relation_state ,a.is_try from t_pro_res_relation as a join t_alive as b
				 on b.id = a.resource_id and a.app_id = b.app_id where a.product_id = '$package_id' and $whereRaw and a.relation_state = 0
				) tt1 order by start_at desc
				 ";*/

		$singlesList = \DB::select($res_list_sql);
		//kevin
		if ($singlesList) {
			foreach ($singlesList as $key => $single) {
				//单品的基本信息
				$single_detail_info = Utils::getResourceInfo($single->resource_id, $single->resource_type);
				if (count($single_detail_info) > 0) {

					//单品上下架状态
					switch ($single->resource_type) {
						case StringConstants::SINGLE_GOODS_ARTICLE://图文
							$singlesList[ $key ]->state = $single_detail_info->display_state;
							break;
						case StringConstants::SINGLE_GOODS_AUDIO://音频
							$singlesList[ $key ]->state = $single_detail_info->audio_state;
							break;
						case StringConstants::SINGLE_GOODS_VIDEO://视频
							$singlesList[ $key ]->state        = $single_detail_info->video_state;
							$singlesList[ $key ]->is_transcode = $single_detail_info->is_transcode;
							break;
						case StringConstants::SINGLE_GOODS_ALIVE://直播
							$singlesList[ $key ]->state                  = $single_detail_info->state;
							$singlesList[ $key ]->alive_type             = $single_detail_info->alive_type;
							$singlesList[ $key ]->is_transcode           = $single_detail_info->is_transcode;
							$singlesList[ $key ]->config_show_view_count = $single_detail_info->config_show_view_count;
							$singlesList[ $key ]->config_show_reward     = $single_detail_info->config_show_reward;
							break;
					}

					$singlesList[ $key ]->title        = $single_detail_info->title;//单品名称
					$singlesList[ $key ]->img_url      = $single_detail_info->img_url;//单品封面图
					$singlesList[ $key ]->start_at     = $single_detail_info->start_at;//单品上架时间
					$singlesList[ $key ]->view_count   = $single_detail_info->view_count;//单品浏览量
					$singlesList[ $key ]->payment_type = $single_detail_info->payment_type;//单品售卖方式
					$singlesList[ $key ]->piece_price  = $single_detail_info->piece_price;//单品价格
					$singlesList[ $key ]->can_select   = $single_detail_info->can_select;//单品复制状态

					$package_singles_view_count += $single_detail_info->view_count;
				}

				$pageUrl = '';

				$app_info = AppUtils::getAppConfInfo(AppUtils::getAppID());
				//生成资源访问链接
				if ($app_info) {
					if (!empty($app_info->wx_app_id) || $app_info->use_collection == 1) {
						if ($app_info->use_collection == 0) {
							$pageUrl = AppUtils::getUrlHeader($app_id) . $app_info->wx_app_id . '.' . env('DOMAIN_NAME');
						} else {
							$pageUrl = AppUtils::getUrlHeader($app_id) . env('DOMAIN_DUAN_NAME');
						}

						$pageUrl = $pageUrl . Utils::getContentUrl(2, $single->resource_type, $single->resource_id, $package_id, '');
					}
				}

				$singlesList[ $key ]->pageUrl = $pageUrl;

				//如果资源类型为音频
				if ($single->resource_type == 2) {
					//查询音频评论播放信息
					$audio = GoodsManageController::audioPlayInfo($single->resource_id);
					if ($audio) {
						$singlesList[ $key ]->comment_counts   = $audio->comment_count;
						$singlesList[ $key ]->playcount        = $audio->playcount;
						$singlesList[ $key ]->finishcount      = $audio->finishcount;
						$singlesList[ $key ]->finishpercent    = $audio->finishpercent;
						$singlesList[ $key ]->share_count      = $audio->share_count;
						$singlesList[ $key ]->try_sign_count   = $audio->try_sign_count;
						$singlesList[ $key ]->click_sign_count = $audio->click_sign_count;
					} else {
						$singlesList[ $key ]->comment_counts   = 0;
						$singlesList[ $key ]->playcount        = 0;
						$singlesList[ $key ]->finishcount      = 0;
						$singlesList[ $key ]->finishpercent    = 0;
						$singlesList[ $key ]->share_count      = 0;
						$singlesList[ $key ]->try_sign_count   = 0;
						$singlesList[ $key ]->click_sign_count = 0;
					}
				}

				//查询资源被打赏的总额
				// by Kris 2017.06.13
				$reward_sum = \DB::select("select sum(reward_price) as reward_sum from t_reward_detail where app_id='$app_id' and resource_id='$single->resource_id' ");
				if (count($reward_sum) > 0) {
					$singlesList[ $key ]->reward_sum = $this->fen2Yuan($reward_sum[0]->reward_sum);
				} else {
					$singlesList[ $key ]->reward_sum = 0;
				}
				//查询被打赏的讲师具体信息
				// by Kris 2017.06.13
				$reward_detail = \DB::select("select t_users.wx_nickname as name,sum(t_reward_detail.reward_price) as reward
                                        from t_reward_detail LEFT JOIN t_users ON
                                        t_reward_detail.app_id=t_users.app_id AND t_reward_detail.rewarded_user_id=t_users.user_id
                                        where t_reward_detail.app_id='$app_id' and t_reward_detail.resource_id='$single->resource_id' group by t_reward_detail.rewarded_user_id ");
				foreach ($reward_detail as $reward) {
					$reward->reward = $this->fen2Yuan($reward->reward);
				}
				$singlesList[ $key ]->lecturers = $reward_detail;
			}
		}
		// kevin 分页
		if ($singlesList) {
			//手动分页
			$page    = Input::get('page', '');
			$total   = count($singlesList);
			$perPage = 10;
			// 判断当前页数
			if ($page) {
				$current_page = $page;
				$current_page = $current_page <= 0 ? 1 : $current_page;
			} else {
				$current_page = 1;
			}
			$path = Paginator::resolveCurrentPath();
			//手动切割结果集
			$item      = array_slice($singlesList, ($current_page - 1) * $perPage, $perPage);
			$paginator = new LengthAwarePaginator($item, $total, $perPage, $current_page, [
				'path'     => $path, //生成路径
				'pageName' => 'page',
			]);
		} else {
			$paginator = [];
		}

		//查询是否开启了小程序
		$isHasLittleProgram = Utils::isHasLittleProgram();

		return View("admin.resManage.packageDetail", compact("paginator", "package_info", 'package_singles_view_count', "package_url", "singlesList", "resource_type", "search_content", "state", "isHasLittleProgram"));
	}

	//分类信息-编辑

	private function fen2Yuan ($orgValue)
	{
		$number = number_format($orgValue / 100, 2);
		$number = str_replace(",", "", $number);

		return $number;
	}

	//查询专栏信息

	/**
	 * 保存上传专栏;
	 * 参数:
	 * 1-params(基本信息+上架信息);
	 * 2-category_type(分类信息)
	 */
	public function uploadPackage ()
	{
		$params        = Input::get("params", '');
		$category_type = Input::get("category_type", '');//分类信息

		$ret = $this->savePackageInfo($params, $category_type, StringConstants::PACKAGE_ADD);

		if ($ret == '0') {
			return $this->result('新增专栏成功!');
		} else {
			$msg = $ret;

			return response()->json(Utils::pack("0", StringConstants::Code_Failed, $msg));
		}
	}

	//专栏所属分类

	private function savePackageInfo ($params, $category_type, $operator_type)
	{

		$app_id = AppUtils::getAppID();

		//参数合法性判断
		if (Utils::isEmptyString($params['name'])) {
			return "保存失败,专栏名称不能为空!";
		}
		if (Utils::isEmptyString($params['summary'])) {
			return "保存失败,专栏简介不能为空!";
		}
		if (Utils::isEmptyString($params['img_url'])) {
			return "保存失败,专栏封面不能为空!";
		}
		//若为单笔,则价格不能为零
		$ret = Utils::checkPiecePrice($params);
		if ($ret != '0') {
			return $ret;
		}
		//若客户使用的是"个人运营模式"即use_collection=1,则限制其piece_price不能超过1000元
		$ret = Utils::checkPersonModelPrice($params);
		if ($ret != '0') {
			return $ret;
		}

		//修改t_app_module中的caption_define字段的"column_pay_hint":"开通会员"值
		if (array_key_exists('is_member', $params)) {
			//查询该客户的配置信息
			$app_module = AppUtils::getModuleInfo(AppUtils::getAppID());
			if ($app_module) {
				if (empty($app_module[0]->caption_define)) {
					$caption_define = '{"column_pay_hint":"' . "开通会员" . '"}';
				} else {
					$temp                  = json_decode($app_module[0]->caption_define);
					$temp->column_pay_hint = "开通会员";
					$caption_define        = json_encode($temp, JSON_UNESCAPED_UNICODE);
				}

				//更新t_app_module中的caption_define字段
				$data['caption_define'] = $caption_define;
				$data['updated_at']     = Utils::getTime();
				$update_result          = \DB::table("db_ex_config.t_app_module")->where("app_id", '=', $app_id)->update($data);

			}
		}

		//分离descrb
		$params['descrb'] = ResContentComm::sliceUE($params['descrb']);
		if ($params['descrb'] == false) {
			//编辑器内容有问题 给前端返回提示信息并取消上传
			return "保存失败，复制的内容有问题，请尝试分开复制，或记录复制内容并联系技术小哥";
		}
		//插入当前时间
		$current_time     = Utils::getTime();
		$params['app_id'] = $app_id;

		if ($operator_type == StringConstants::PACKAGE_ADD) {//专栏新增
			$id                   = Utils::getUniId('p_');
			$params['id']         = $id;
			$params['created_at'] = $current_time;
			//新建专栏默认是未上架状态 默认为0则是上架状态
			//$params['state'] = 1;

			//新增专栏记录
			$result = \DB::table("db_ex_business.t_pay_products")->insert($params);
			$msg    = "新增专栏失败!";

			/***分类信息-新增***/
			$this->createCategoryPackage($id, $category_type);

		} else {//专栏编辑
			$id                   = $params['id'];
			$params['updated_at'] = $current_time;
			$result               = \DB::table("db_ex_business.t_pay_products")
				->where('id', '=', $id)
				->where('app_id', '=', $app_id)
				->update($params);

			//修改了package包的价格,要修改对应的资源表对应的包的状态
			if (array_key_exists('price', $params)) {
				$price                   = $params['price'];
				$resource_id             = $id;
				$audio_update_price      = \DB::update("UPDATE t_audio SET piece_price='$price' WHERE product_id='$resource_id' and payment_type =3 and app_id = '$app_id'");
				$video_update_price      = \DB::update("UPDATE t_video SET piece_price='$price' WHERE product_id='$resource_id' and payment_type = 3 and app_id = '$app_id'");
				$image_text_update_price = \DB::update("UPDATE t_image_text SET piece_price='$price' WHERE product_id='$resource_id' AND payment_type = 3 and app_id = '$app_id'");
				$alive_update_price      = \DB::update("UPDATE t_alive SET piece_price='$price' WHERE product_id='$resource_id' and payment_type = 3 and app_id = '$app_id'");
			}

			//修改了专栏的name,要修改对应的关系表中的专栏名
			if (array_key_exists('name', $params)) {
				$product_name = $params['name'];
				$update_name  = \DB::update("
update t_pro_res_relation set product_name = ? where app_id = ? and product_id=?
", [$product_name, $app_id, $id]);
			}

			$msg = "更新专栏失败!resource_id:" . $id;
			/****分类信息-编辑*****/
			$this->saveCategoryPackage($id, $category_type, $params);
		}

		if ($result) {
			$table_name = "t_pay_products";
			//图片压缩
			//            if(array_key_exists('img_url', $params)) ResContentComm::imageDeal($params['img_url'], $table_name, $id);//,160,120,60);
			if (array_key_exists('img_url', $params)) ImageUtils::resImgCompress($this->request, $table_name, $app_id, $id, $params['img_url']);

			return StringConstants::Code_Succeed;
		} else {
			return $msg;
		}
	}

	//    //分类数据信息
	//    private function categoryInfo(){
	//        // 分类数据信息
	//        $category_info = \DB::connection('mysql')->table('t_category')
	//            ->where('app_id', '=', AppUtils::getAppID())
	//            ->where('id', '!=', '0')
	//            ->orderby('weight', 'desc')
	//            ->Lists('category_name', 'id');
	//
	//        return $category_info;
	//    }

	//初始化排序权重值

	private function createCategoryPackage ($resource_id, $category_type)
	{
		$app_id = AppUtils::getAppID();

		$category_info = DB::connection('mysql')->table('t_category')
			->where('app_id', '=', $app_id)
			->where('id', '!=', '0')
			->pluck('id');

		// 直接存入四條分類記錄
		foreach ($category_info as $value) {
			$insert = DB::connection('mysql')->table('t_category_resource')->insert(
				['app_id' => $app_id, 'category_id' => $value, 'resource_id' => $resource_id, 'resource_type' => 0, 'state' => 0, 'created_at' => date('Y-m-d H:i:s', time())]);
		}

		// 同时插入一条首页分类数据  默认显示
		$insert = DB::connection('mysql')->table('t_category_resource')->insert(
			['app_id' => $app_id, 'category_id' => '0', 'resource_id' => $resource_id, 'resource_type' => 0, 'state' => 1, 'created_at' => date('Y-m-d H:i:s', time())]
		);

		// 如果有分类信息 添加
		//        dd($category_type);
		if (!empty($category_type)) {
			// 再更新所有分类为现在的状态
			DB::connection('mysql')->table('t_category_resource')
				->where('app_id', '=', $app_id)
				->where('resource_id', '=', $resource_id)
				->where('resource_type', '=', 0)
				->whereIn('category_id', $category_type)
				->update(['state' => 1]);
		}
	}

	//更新专栏期数

	private function saveCategoryPackage ($resource_id, $category_type, $params)
	{
		$app_id = AppUtils::getAppID();

		//        if (array_key_exists('state', $params)){
		$category_info = DB::connection('mysql')->table('t_category')
			->where('app_id', '=', $app_id)
			->where('id', '!=', '0')
			->pluck('id');
		// 获得分类关系表中所有的该资源的分类数据
		$relation = DB::connection('mysql')->table('t_category_resource')
			->select('category_id')
			->where('app_id', '=', $app_id)
			->where('resource_id', '=', $resource_id)
			->where('resource_type', '=', 0)
			->whereIn('category_id', $category_info)
			->get();
		//            dd($relation);
		// 如果不存在该资源的分类数据，则全部插入，默认不显示
		if (count($relation) == 0) {
			foreach ($category_info as $value) {
				$insert = DB::connection('mysql')->table('t_category_resource')->insert(
					['app_id' => $app_id, 'category_id' => $value, 'resource_id' => $resource_id, 'resource_type' => 0, 'state' => 0, 'created_at' => date('Y-m-d H:i:s', time())]
				);
			}
		}

		// 首先 重置所有分类（四个）的状态为0
		$now                  = Utils::getTime();
		$result_app_categorys = DB::connection('mysql')->table('t_category_resource')
			->where('app_id', '=', $app_id)
			->where('resource_id', '=', $resource_id)
			->whereIn('category_id', $category_info)
			->where('resource_type', '=', 0)
			->update(['state' => 0, 'updated_at' => $now]);

		if (!empty($category_type)) {

			// 更新所有提交的分类数据
			$result_app_category_avalite = DB::connection('mysql')->table('t_category_resource')
				->where('app_id', '=', $app_id)
				->where('resource_id', '=', $resource_id)
				->whereIn('category_id', $category_type)
				->where('resource_type', '=', 0)
				->update(['state' => 1]);
		}
		//        }

		// 如果没有首页分类的数据 也插入一条
		$home_page_category = DB::connection('mysql')->table('t_category_resource')
			->where('app_id', '=', $app_id)
			->where('resource_id', '=', $resource_id)
			->where('category_id', '=', '0')
			->where('resource_type', '=', 0)
			->first();
		if (empty($home_page_category)) {
			// 同时插入一条首页分类数据  默认显示
			$insert = DB::connection('mysql')->table('t_category_resource')->insert(
				['app_id' => $app_id, 'category_id' => '0', 'resource_id' => $resource_id, 'resource_type' => 0, 'state' => 1, 'created_at' => Utils::getTime()]
			);
		}
	}

	//拼接专栏连接

	private function result ($data)
	{
		if (Utils::isEmptyString($data)) {
			return response()->json(Utils::pack("", StringConstants::Code_Failed, StringConstants::Msg_Failed));
		} else {
			return response()->json(Utils::pack($data));
		}
	}

	/**
	 * 保存编辑资源;
	 * 参数:
	 * 1-params(基本信息+上架信息);
	 * 2-category_type(分类信息)
	 */
	public function updatePackage ()
	{
		$params        = Input::get("params", '');
		$category_type = Input::get("category_type", '');//分类信息

		$ret = $this->savePackageInfo($params, $category_type, StringConstants::PACKAGE_EDIT);

		if ($ret == '0') {
			return $this->result('编辑专栏成功!');
		} else {
			$msg = $ret;

			return response()->json(Utils::pack("0", StringConstants::Code_Failed, $msg));
		}
	}

	//金额由分转成元

	private function updatePackageCount ($package_id)
	{

		$app_id = AppUtils::getAppID();

		$resource_count = \DB::select(" select sum(count) as count from (
                                                select case when count is not null then count else 0 end as count from
                                                (select count(*) as count from t_alive where app_id='$app_id' and payment_type in(2,3) and product_id='$package_id' and state!=2
                                                ) t1
                                                UNION ALL
                                                select case when count is not null then count else 0 end as count from
                                                (select count(*) as count from t_audio where app_id='$app_id' and payment_type in(2,3) and product_id='$package_id' and audio_state!=2
                                                ) t1
                                                UNION ALL
                                                select case when count is not null then count else 0 end as count from
                                                (select count(*) as count from t_video where app_id='$app_id' and payment_type in(2,3) and product_id='$package_id' and video_state!=2
                                                ) t1
                                                UNION ALL
                                                select case when count is not null then count else 0 end as count from
                                                (select count(*) as count from t_image_text where app_id='$app_id' and payment_type in(2,3) and product_id='$package_id' and display_state!=2
                                                ) t1)t2")[0];
		if ($resource_count) {
			//更新期数
			$ret = \DB::table('t_pay_products')->where('app_id', '=', $app_id)->where('id', '=', $package_id)->update(['resource_count' => $resource_count->count]);
			$ret = $resource_count->count;
		} else {
			$ret = 0;
		}

		return $ret;
	}

	//专栏开关

    public function visibleSwitch(){
//        $app_id = AppUtils::getAppID();
        $package_id = Input::get('id', '');
        $visible_on = Input::get('visible_on',0);

        if(empty($package_id)||!in_array($visible_on,[0,1])){
            return response()->json(['code' => -1, 'msg' => '传入参数错误']);
        }

        $package = DB::table('db_ex_business.t_pay_products')
            ->where('app_id', AppUtils::getAppID())
            ->where('id', $package_id)
            ->update(['visible_on'=>$visible_on,'updated_at'=>date('Y-m-d H:i:s')]);
//dump($package_id,$visibleOn,$package,$app_id);
        if ($package) {
            return response()->json(['code'=>0,'msg'=>'修改开关成功 ']);
        }else {
            return response()->json(['code'=>-2,'msg'=>'修改开关失败']);
        }
    }
}
