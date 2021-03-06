<?php

namespace App\Http\Controllers\ResManage;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\ResContentComm;
use App\Http\Controllers\Tools\StringConstants;
use App\Http\Controllers\Tools\Utils;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

//kevin
//kevin

class MemberController extends Controller
{

	/**
	 * 会员列表
	 */
	public function getMemberList ()
	{

		$state          = Input::get("state", 0);
		$search_content = Input::get("search_content", '');
		$is_distribute  = Input::get("is_distribute", -1);
		$whereRaw       = '1=1';
		if ($state != -1)
			$whereRaw .= " and state = " . $state;

		if ($is_distribute != -1)
			$whereRaw .= " and is_distribute = " . $is_distribute;
		if (!Utils::isEmptyString($search_content))
			$whereRaw .= " and name like '" . "%" . $search_content . "%'";

		//获取专栏列表
		$app_id = AppUtils::getAppID();
		//初始化排序权重值
		$is_init = $this->initMemberWeight();

		$member_on_url_list = [];
		$memberListInfo     = \DB::table('t_pay_products')->where('app_id', '=', $app_id)
			->where('state', '!=', '2')
			->where('is_member', '=', StringConstants::AS_MEMBER)
			->whereRaw($whereRaw)
			->orderby('order_weight', 'desc')
			->orderby('created_at', 'desc')
			->paginate(10);

		//获取会员访问连接
		foreach ($memberListInfo as $key => $item) {
			//会员链接
			$member_url                 = $this->getMemberUrl($item->id);
			$member_on_url_list[ $key ] = $member_url;
		}

		return View("admin.resManage.memberList", compact("memberListInfo", "member_on_url_list", "search_content", "state", "is_distribute"));
	}

	private function initMemberWeight ()
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

	private function getMemberUrl ($member_id)
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

			$package_url = $skip_target . Utils::contentUrl('', 3, '', '', $member_id, $app_id);

			return $package_url;
		} else {
			return '';
		}
	}

	/**
	 * 创建会员
	 */
	public function createMember ()
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

		return View("admin.resManage.manageMember", compact("memberInfo", "type", "category_info"));
	}

	/**
	 * 编辑会员
	 * 参数:id-会员id
	 */
	public function editMember ()
	{

		$member_id = Input::get('id', '');
		//查询专栏信息
		$memberInfo = $this->getMemberInfo($member_id);
		$type       = 1;//编辑页面

		//应用权限
		$appModuleInfo = AppUtils::getModuleInfo(AppUtils::getAppID());

		// 分类数据
		if (!empty($appModuleInfo) && $appModuleInfo[0]->resource_category == 1) {
			// 分类数据信息
			$category_info = Utils::categoryInfo();
			// 所属分类信息
			$member_category = $this->memberCategoryInfo($member_id);
		}

		return View("admin.resManage.manageMember", compact("memberInfo", "type", "category_info", "member_category"));
	}

	private function getMemberInfo ($member_id)
	{
		$member = \DB::table('db_ex_business.t_pay_products')
			->where('id', '=', $member_id)
			->where('app_id', '=', AppUtils::getAppID())
			->where('is_member', '=', StringConstants::AS_MEMBER)
			->first();

		return $member;
	}

	//查询会员拥有的单品

	private function memberCategoryInfo ($package_id)
	{
		$app_id = AppUtils::getAppID();
		// 所属分类信息
		$package_category = \DB::connection('mysql')->table('t_category_resource')
			->select('category_id')
			->where('app_id', '=', $app_id)
			->where('category_id', '!=', '0')
			->where('resource_id', '=', $package_id)
			->where('state', '=', 1)
			->pluck('category_id');

		return $package_category;
	}

	//查询会员拥有的专栏

	/**
	 * 会员详情页
	 * 参数:id(会员id)
	 */
	public function memberDetail ()
	{
		$member_id      = Input::get('id', '');
		$search_content = Input::get('search_content', '');
		$resource_type  = Input::get("resource_type", 0);
		$state          = Input::get("state", -1);
		$source_type    = Input::get("source_type", 0);     //  0-单品列表<默认值> ; 1-专栏列表
		$app_id         = AppUtils::getAppID();
		//查询会员信息
		$member_info = $this->getMemberInfo($member_id);
		//会员访问链接
		$member_url = $this->getMemberUrl($member_id);
		//默认查询会员拥有的单品列表
		$single_list_member = $this->getSingleListMember($member_id, $search_content, $resource_type, $state);
		if ($single_list_member) {
			foreach ($single_list_member as $key => $single) {
				//如果资源类型为音频
				if ($single->resource_type == 2) {
					//查询音频评论播放信息
					$audio = GoodsManageController::audioPlayInfo($single->id);
					if ($audio) {
						$single_list_member[ $key ]->comment_count    = $audio->comment_count;
						$single_list_member[ $key ]->playcount        = $audio->playcount;
						$single_list_member[ $key ]->finishcount      = $audio->finishcount;
						$single_list_member[ $key ]->finishpercent    = $audio->finishpercent;
						$single_list_member[ $key ]->share_count      = $audio->share_count;
						$single_list_member[ $key ]->try_sign_count   = $audio->try_sign_count;
						$single_list_member[ $key ]->click_sign_count = $audio->click_sign_count;
					} else {
						$single_list_member[ $key ]->comment_counts   = 0;
						$single_list_member[ $key ]->playcount        = 0;
						$single_list_member[ $key ]->finishcount      = 0;
						$single_list_member[ $key ]->finishpercent    = 0;
						$single_list_member[ $key ]->share_count      = 0;
						$single_list_member[ $key ]->try_sign_count   = 0;
						$single_list_member[ $key ]->click_sign_count = 0;
					}
				}

			}
		}

		$package_list_member = $this->getPackageListMember($member_id, $search_content, $state);

		//查询是否开启了小程序
		$isHasLittleProgram = Utils::isHasLittleProgram();

		return View("admin.resManage.memberDetail", compact("member_info", 'source_type', 'package_list_member', 'single_list_member', "member_url", 'member_id', "resource_type", "search_content", "state", "isHasLittleProgram"));
	}

	//根据专栏名称搜索

	private function getSingleListMember ($member_id, $search_content, $resource_type, $state)
	{

		$app_id   = AppUtils::getAppID();
		$whereRaw = '1=1';

		if ($state != -1 && $state != 0 && $state != 1) {
			$state = -1;
		}

		//获取符合条件的资源id集合
		$resource_id_list = ResContentComm::getResourceIdList($state, $search_content, $resource_type);

		if ($resource_id_list) {
			$whereRaw .= " and resource_id in (" . implode(',', $resource_id_list) . ")";
		}

		//在关系表中查找该会员的所有单品信息
		// $singlesList = \DB::table("db_ex_business.t_pro_res_relation")
		//     ->where('app_id','=',AppUtils::getAppID())
		//     ->where('product_id','=',$member_id)
		//     ->where('resource_type','<=',4)
		//     ->where('relation_state','=',StringConstants::RELATION_NORMAL)
		//     ->whereRaw($whereRaw)
		//     ->orderBy('is_top','desc')
		//     ->orderBy('created_at','desc')
		//     ->paginate(10);

		//kevin
		switch ($resource_type) {
			case StringConstants::SINGLE_GOODS_ALL://全部资源
				$res_list_sql = "
            SELECT start_at, resource_id,resource_type, product_name,product_id, title,relation_state,is_try
            FROM(
              SELECT a.resource_id, a.product_id,b.start_at,a.resource_type,a.product_name,b.title,a.relation_state ,a.is_try from t_pro_res_relation as a join t_image_text as b
            on b.id = a.resource_id and a.app_id = b.app_id where a.app_id = '$app_id' and a.product_id = '$member_id' and b.title like '%{$search_content}%' and a.relation_state = 0

            union all
              SELECT a.resource_id, a.product_id,b.start_at,a.resource_type,a.product_name,b.title,a.relation_state ,a.is_try from t_pro_res_relation as a join t_audio as b
            on b.id = a.resource_id and a.app_id = b.app_id where a.app_id = '$app_id' and a.product_id = '$member_id' and b.title like '%{$search_content}%' and a.relation_state = 0

            union all
            SELECT a.resource_id, a.product_id,b.start_at,a.resource_type,a.product_name,b.title,a.relation_state ,a.is_try from t_pro_res_relation as a join t_video as b
            on b.id = a.resource_id and a.app_id = b.app_id where a.app_id = '$app_id' and a.product_id = '$member_id' and b.title like '%{$search_content}%' and a.relation_state = 0

            union all
            SELECT a.resource_id,a.product_id,b.start_at,a.resource_type,a.product_name,b.title,a.relation_state ,a.is_try from t_pro_res_relation as a join t_alive as b
            on b.id = a.resource_id and a.app_id = b.app_id where a.app_id = '$app_id' and a.product_id = '$member_id' and b.title like '%{$search_content}%' and a.relation_state = 0
           ) tt1 order by start_at desc";
				break;
			case StringConstants::SINGLE_GOODS_ARTICLE://图文
				$res_list_sql = "SELECT a.resource_id, a.product_id,b.start_at,a.resource_type,a.product_name,b.title,a.relation_state ,a.is_try from t_pro_res_relation as a join t_image_text as b
            on b.id = a.resource_id and a.app_id = b.app_id where a.app_id = '$app_id' and a.product_id = '$member_id' and b.title like '%{$search_content}%' and a.relation_state = 0 order by start_at desc";
				break;
			case StringConstants::SINGLE_GOODS_AUDIO://音频
				$res_list_sql = "SELECT a.resource_id, a.product_id,b.start_at,a.resource_type,a.product_name,b.title,a.relation_state ,a.is_try from t_pro_res_relation as a join t_audio as b
            on b.id = a.resource_id and a.app_id = b.app_id where a.app_id = '$app_id' and a.product_id = '$member_id' and b.title like '%{$search_content}%' and a.relation_state = 0";
				break;
			case StringConstants::SINGLE_GOODS_VIDEO://视频
				$res_list_sql = "SELECT a.resource_id, a.product_id,b.start_at,a.resource_type,a.product_name,b.title,a.relation_state ,a.is_try from t_pro_res_relation as a join t_video as b
            on b.id = a.resource_id and a.app_id = b.app_id where a.app_id = '$app_id' and a.product_id = '$member_id' and b.title like '%{$search_content}%' and a.relation_state = 0 order by start_at desc";
				break;
			case StringConstants::SINGLE_GOODS_ALIVE://直播
				$res_list_sql = "SELECT a.resource_id, a.product_id,b.start_at,a.resource_type,a.product_name,b.title,a.relation_state ,a.is_try from t_pro_res_relation as a join t_alive as b
            on b.id = a.resource_id and a.app_id = b.app_id where a.app_id = '$app_id' and a.product_id = '$member_id' and b.title like '%{$search_content}%' and a.relation_state = 0 order by start_at desc";
				break;
		}
		//        $res_list_sql="
		//        SELECT start_at, resource_id,resource_type, product_name,product_id, title,relation_state,is_try
		//        FROM(
		//              SELECT a.resource_id, a.product_id,b.start_at,a.resource_type,a.product_name,b.title,a.relation_state ,a.is_try from t_pro_res_relation as a join t_image_text as b
		//            on b.id = a.resource_id and a.app_id = b.app_id where a.relation_state = 0 and a.resource_type = 1 and b.title like '%{$search_content}%'
		//
		//            union all
		//              SELECT a.resource_id, a.product_id,b.start_at,a.resource_type,a.product_name,b.title,a.relation_state ,a.is_try from t_pro_res_relation as a join t_audio as b
		//            on b.id = a.resource_id and a.app_id = b.app_id where a.relation_state = 0 and a.resource_type = 2 and b.title like '%{$search_content}%'
		//
		//            union all
		//            SELECT a.resource_id, a.product_id,b.start_at,a.resource_type,a.product_name,b.title,a.relation_state ,a.is_try from t_pro_res_relation as a join t_video as b
		//            on b.id = a.resource_id and a.app_id = b.app_id where a.relation_state = 0 and a.resource_type = 3 and b.title like '%{$search_content}%'
		//
		//            union all
		//            SELECT a.resource_id,a.product_id,b.start_at,a.resource_type,a.product_name,b.title,a.relation_state ,a.is_try from t_pro_res_relation as a join t_alive as b
		//            on b.id = a.resource_id and a.app_id = b.app_id where a.relation_state = 0 and a.resource_type = 4 and b.title like '%{$search_content}%'
		//           ) tt1 where product_id = '$member_id' and $whereRaw order by start_at desc
		//            ";
		$singlesList = \DB::select($res_list_sql);
		//kevin

		foreach ($singlesList as $key => $single) {
			//单品的基本信息

			$single_detail_info = Utils::getResourceInfo($single->resource_id, $single->resource_type);

			if ($single_detail_info) {
				//单品上下架状态
				switch ($single->resource_type) {
					case StringConstants::SINGLE_GOODS_ARTICLE://图文
						$single_detail_info->state = $single_detail_info->display_state;
						break;
					case StringConstants::SINGLE_GOODS_AUDIO://音频
						$single_detail_info->state = $single_detail_info->audio_state;
						break;
					case StringConstants::SINGLE_GOODS_VIDEO://视频
						$single_detail_info->state = $single_detail_info->video_state;
						break;
				}
				$pageUrl  = '';
				$app_info = AppUtils::getAppConfInfo(AppUtils::getAppID());

				if ($app_info) {
					//生成资源访问链接
					if (!empty($app_info->wx_app_id) || $app_info->use_collection == 1) {
						if ($app_info->use_collection == 0) {
							$pageUrl = AppUtils::getUrlHeader($app_id) . $app_info->wx_app_id . '.' . env('DOMAIN_NAME');
						} else {
							$pageUrl = AppUtils::getUrlHeader($app_id) . env('DOMAIN_DUAN_NAME');
						}

						$pageUrl = $pageUrl . Utils::getContentUrl(2, $single->resource_type, $single->resource_id, $member_id, '');
					}
				}

				$single_detail_info->pageUrl = $pageUrl;

				//查询资源被打赏的总额    by Kris 2017.06.13
				$reward_sum = \DB::select("select sum(reward_price) as reward_sum from t_reward_detail where app_id='$app_id' and resource_id='$single->resource_id' ");
				if (count($reward_sum) > 0) {
					$single_detail_info->reward_sum = $this->fen2Yuan($reward_sum[0]->reward_sum);
				} else {
					$single_detail_info->reward_sum = 0;
				}
				//查询被打赏的讲师具体信息  by Kris 2017.06.13
				$reward_datail = \DB::select("select t_users.wx_nickname as name,sum(t_reward_detail.reward_price) as reward
                                        from t_reward_detail LEFT JOIN t_users ON
                                        t_reward_detail.app_id=t_users.app_id AND t_reward_detail.rewarded_user_id=t_users.user_id
                                        where t_reward_detail.app_id='$app_id' and t_reward_detail.resource_id='$single->resource_id' group by t_reward_detail.rewarded_user_id ");
				foreach ($reward_datail as $reward) {
					$reward->reward = $this->fen2Yuan($reward->reward);
				}
				$single_detail_info->lecturers = $reward_datail;

			}
			$singlesList[ $key ]                = $single_detail_info;
			$singlesList[ $key ]->resource_type = $single->resource_type;
			// $singlesList[$key]->is_top = $single->is_top;
			$singlesList[ $key ]->is_try = $single->is_try;

			//播放评论详细信息
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
		// dd($paginator);
		//kevin
		return $paginator;
	}

	//查询专栏信息

	private function fen2Yuan ($orgValue)
	{
		$number = number_format($orgValue / 100, 2);
		$number = str_replace(",", "", $number);

		return $number;
	}

	//查询会员信息

	private function getPackageListMember ($member_id, $search_content, $state)
	{

		$whereRaw = "1=1";
		if (!Utils::isEmptyString($search_content)) {
			$resource_package_id_list = $this->getPackageIdList($search_content);
			$whereRaw                 .= " and resource_id in (" . implode(',', $resource_package_id_list) . ")";
		}

		//在关系表t_pro_res_relation中查询该会员的所有专栏信息
		$package_list = \DB::table("db_ex_business.t_pro_res_relation")
			->where("product_id", "=", $member_id)
			->where("app_id", '=', AppUtils::getAppID())
			->where("resource_type", '=', StringConstants::PRO_RES_PACKAGE)
			->where('relation_state', '=', StringConstants::RELATION_NORMAL)
			->whereRaw($whereRaw)
			->orderBy('is_top', 'desc')
			->orderBy('updated_at', 'desc')
			->paginate(10);
		foreach ($package_list as $key => $package) {
			if ($package) {
				//查询专栏信息
				$package = $this->getPackageInfo($package->resource_id);
				if (empty($package)) {
					continue;
				}
				//查询专栏的访问链接
				$package->package_url = $this->getMemberUrl($package->id);
				$package_list[ $key ] = $package;
			}
		}

		return $package_list;
	}

	//专栏所属分类

	private function getPackageIdList ($search_content)
	{
		$whereRaw = "1=1";
		$whereRaw .= " and name like '" . "%" . $search_content . "%'";

		$package_list = \DB::table("db_ex_business.t_pay_products")
			->where("state", '!=', 2)
			->where("is_member", '=', 0)
			->whereRaw($whereRaw)
			->get();

		$id_list = ['1'];
		if ($package_list) {
			foreach ($package_list as $key => $package) {
				$id_list[] = "'" . $package->id . "'";
			}
		}

		return $id_list;

	}

	//拼接会员连接

	private function getPackageInfo ($package_id)
	{
		$package = \DB::table('db_ex_business.t_pay_products')
			->where('id', '=', $package_id)
			->where('app_id', '=', AppUtils::getAppID())
			->where('is_member', '=', StringConstants::NOT_MEMBER)
			->first();

		return $package;
	}

	//初始化排序权重值

	/**
	 * 会员的专栏列表
	 *参数:id(会员id)
	 */
	public function getPackageListOfMember ()
	{
		$member_id           = Input::get('id', '');
		$search_content      = Input::get('search_content', '');
		$state               = Input::get("state", -1);
		$package_list_member = $this->getPackageListMember($member_id, $search_content, $state);

		$is_distribute = 0;
		$member_info   = $this->getMemberInfo($member_id);
		if ($member_info) {
			$is_distribute = $member_info->is_distribute;
		}

		return View("admin.resManage.packageListOfMember", compact("package_list_member", "is_distribute"));
	}

	/**
	 * 会员的单品列表
	 * 参数:id(会员id)
	 */
	public function getResourceListOfMember ()
	{
		$member_id      = Input::get('id', '');
		$search_content = Input::get('search_content', '');
		$resource_type  = Input::get("resource_type", 0);
		$state          = Input::get("state", -1);

		//在关系表中查询该会员的所有单品
		$single_list_member = $this->getSingleListMember($member_id, $search_content, $resource_type, $state);

		$is_distribute = 0;
		$member_info   = $this->getMemberInfo($member_id);
		if ($member_info) {
			$is_distribute = $member_info->is_distribute;
		}


		//        return $this->result($single_list_member);

		return View("admin.resManage.resourceListOfMember", compact("single_list_member", "member_id", "resource_type", "search_content", "state", "is_distribute"));
	}

	public function setIsCompleteInfo (Request $request)
	{
		$app_id      = AppUtils::getAppID();//获取app_id
		$id          = $request->input('id', 0);
		$is_complete = DB::connection('mysql')->table('t_pay_products')
			->select('is_complete_info')
			->where('app_id', $app_id)
			->where('id', $id)
			->value('is_complete_info');
		if ($is_complete)
			$is_complete = 0;
		else
			$is_complete = 1;
		$update = DB::connection('mysql')->table('t_pay_products')
			->where('app_id', $app_id)
			->where('id', $id)
			->update(['is_complete_info' => $is_complete]);
		if ($update) {
			return response()->json(['code' => 0, 'msg' => '您已经修改成功', 'data' => []]);
		} else {
			return response()->json(['code' => -1, 'msg' => '修改失败', 'data' => []]);
		}
	}

	//金额由分转成元

	private function result ($data)
	{
		if (Utils::isEmptyString($data)) {
			return response()->json(Utils::pack("", StringConstants::Code_Failed, StringConstants::Msg_Failed));
		} else {
			return response()->json(Utils::pack($data));
		}
	}
}
