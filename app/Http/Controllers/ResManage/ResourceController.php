<?php

namespace App\Http\Controllers\ResManage;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\AudioUtils;
use App\Http\Controllers\Tools\ImageUtils;
use App\Http\Controllers\Tools\MessagePush;
use App\Http\Controllers\Tools\ResContentComm;
use App\Http\Controllers\Tools\StringConstants;
use App\Http\Controllers\Tools\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ResourceController extends Controller
{

	private $request;

	public function __construct (Request $request)
	{
		$this->request = $request;
	}

	/**
	 * 单品资源列表
	 * 参数:
	 * 1.资源类型-resource_type:0-全部,1-图文,2-音频,3-视频,4-直播
	 * 2.搜索内容-search_content.
	 * 3.page-分页页码
	 * 4.state-资源状态
	 */
	public function getResourceList ()
	{

		$search_content = Input::get('search_content', '');
		$resource_type  = Input::get("resource_type", 0);
		$state          = Input::get("state", 0);
		$page_index     = Input::get("page", 1);
		$app_id         = AppUtils::getAppID();

		if ($state != '-1' && $state != '0' && $state != '1') {
			$state = -1;
		}

		//判断参数的合法性

		//单品有四类资源:1-图文、2-音频、3-视频、4-直播
		//汇集成一个集合,然后在调用分页函数

		$resource_data = $this->getResourceListByState('', $state, $search_content, $resource_type, $page_index, StringConstants::SINGLE_LIST);
		$resourceList  = $resource_data['resource_list'];
		$page_offset   = $resource_data['page_offset'];

		//  获取资源中音频的播放评论信息

		//1:获取资源的订阅量,查询表t_purchase;2:资源链接;3.播放评论详细信息
		foreach ($resourceList as $key => $resource) {

			//获取资源订阅数
			$purchase_count_info = \DB::select("select count(*) as count from db_ex_business.t_purchase where app_id = '$app_id' and payment_type = 2 and product_id='$resource->id'");
			if (count($purchase_count_info) > 0) {
				$resourceList[ $key ]->purchase_count = $purchase_count_info[0]->count;

			} else {
				$resourceList[ $key ]->purchase_count = 0;
			}

			//资源访问链接
			$pageUrl  = '';
			$app_info = AppUtils::getAppConfInfo(AppUtils::getAppID());
			//生成资源访问链接
			if ($app_info) {
				if (!empty($app_info->wx_app_id) || $app_info->use_collection == 1) {

					if ($app_info->use_collection == 0) {
						$pageUrl = AppUtils::getUrlHeader($app_id) . $app_info->wx_app_id . '.' . env('DOMAIN_NAME');
					} else {
						$pageUrl = AppUtils::getUrlHeader($app_id) . env('DOMAIN_DUAN_NAME');
					}
					//查询该资源关联的专栏或会员
					$product_id = $this->getProductIdByResId($resource->id);
					$pageUrl    = $pageUrl . Utils::getContentUrl(2, $resource->resource_type, $resource->id, $product_id, '');
				}
			}

			$resourceList[ $key ]->pageUrl = $pageUrl;
			//播放评论详细信息
			//如果资源类型为音频
			if ($resource->resource_type == 2) {
				//查询音频评论播放信息
				$audio = GoodsManageController::audioPlayInfo($resource->id);
				if ($audio) {
					$resourceList[ $key ]->comment_counts   = $audio->comment_count;
					$resourceList[ $key ]->playcount        = $audio->playcount;
					$resourceList[ $key ]->finishcount      = $audio->finishcount;
					$resourceList[ $key ]->finishpercent    = $audio->finishpercent;
					$resourceList[ $key ]->share_count      = $audio->share_count;
					$resourceList[ $key ]->try_sign_count   = $audio->try_sign_count;
					$resourceList[ $key ]->click_sign_count = $audio->click_sign_count;
				} else {
					$resourceList[ $key ]->comment_counts   = 0;
					$resourceList[ $key ]->playcount        = 0;
					$resourceList[ $key ]->finishcount      = 0;
					$resourceList[ $key ]->finishpercent    = 0;
					$resourceList[ $key ]->share_count      = 0;
					$resourceList[ $key ]->try_sign_count   = 0;
					$resourceList[ $key ]->click_sign_count = 0;
				}
			}

			//查询资源goods_id在其他专栏是否被拥有
			$query_other_package = $this->queryGoods($resource->id, $resource->resource_type);
			$query_package_list  = [];
			foreach ($query_other_package as $key2 => $goods) {
				//查询专栏信息
				$package_info = $this->getPackageInfo($goods->product_id);
				if ($package_info) {
					$query_package_list[] = [
						'id'        => $goods->product_id,
						'title'     => $goods->product_name,
						'is_member' => $package_info->is_member,
					];
				}
			}
			$resourceList[ $key ]->query_package_list = $query_package_list;

			//查询资源被打赏的总额    by Kris 2017.06.12
			$reward_sum = \DB::select("select sum(reward_price) as reward_sum from t_reward_detail where app_id='$app_id' and resource_id='$resource->id' ");
			if (count($reward_sum) > 0) {
				$resourceList[ $key ]->reward_sum = $this->fen2Yuan($reward_sum[0]->reward_sum);
			} else {
				$resourceList[ $key ]->reward_sum = 0;
			}
			//查询被打赏的讲师具体信息   by Kris 2017.06.12
			$reward_datail = \DB::select("select t_users.wx_nickname as name,sum(t_reward_detail.reward_price) as reward 
                                        from t_reward_detail LEFT JOIN t_users ON 
                                        t_reward_detail.app_id=t_users.app_id AND t_reward_detail.rewarded_user_id=t_users.user_id 
                                        where t_reward_detail.app_id='$app_id' and t_reward_detail.resource_id='$resource->id' group by t_reward_detail.rewarded_user_id ");
			foreach ($reward_datail as $reward) {
				$reward->reward = $this->fen2Yuan($reward->reward);
			}
			$resourceList[ $key ]->lecturers = $reward_datail;

		}

		//查询是否开启了小程序
		$isHasLittleProgram = Utils::isHasLittleProgram();

		return View("admin.resManage.resourceList", compact("resourceList", 'search_content', 'resource_type', 'page_offset', 'state', 'isHasLittleProgram'));
	}

	/**
	 * 获取指定状态下的单品列表
	 * 参数:
	 * 1-搜索内容;
	 * 2-资源类型(0-全部,1-图文,2-音频,3-视频,4-直播);
	 * 3-单品状态 state(0-上架;1-下架);
	 * 4-page_index(分页页码)
	 * 5-operator_type(操作类型,1-单品;2-专栏;3-会员;4-单品列表)
	 */
	private function getResourceListByState ($package_id, $state, $search_content, $resource_type, $page_index, $operator_type)
	{

		$app_id     = AppUtils::getAppID();
		$whereRaw   = '1=1';
		$table_name = '';

		if (!Utils::isEmptyString($search_content)) {
			$whereRaw .= " and title like '" . "%" . $search_content . "%'";
		}

		if ($operator_type == StringConstants::SINGLE_LIST) {//单品列表
			$whereRaw  .= " and payment_type != 3 ";
			$page_size = 10;
		} else if ($operator_type == StringConstants::CHOICE_CHANNEL_SINGLE) {//单品
			//取出payment_type=3的所有单品
			$whereRaw .= " and payment_type = 3 ";
			//resource_id not in 关系表(非试听)中;
			$is_try                    = 1;
			$relation_resource_id_list = $this->getResourceIdList($package_id, $is_try);
			$whereRaw                  .= " and id not in ( " . implode(',', $relation_resource_id_list) . ")";
			$page_size                 = 5;
		} else if ($operator_type == StringConstants::CHOICE_CHANNEL_PACKAGE) {//专栏
			//取出payment_type=2且resource_id not in 关系表中;
			$is_try = -1;
			//            $package_id = "";//暂不支持一对多(一个单品属于多个专栏)
			$relation_resource_id_list = $this->getResourceIdList($package_id, $is_try);
			//            $whereRaw .= " and payment_type !=3 and id not in (".implode(',',$relation_resource_id_list).")";
			$whereRaw  .= " and id not in (" . implode(',', $relation_resource_id_list) . ")";
			$page_size = 15;
		}

		$startRow = ($page_index - 1) * $page_size;

		if ($resource_type == StringConstants::SINGLE_GOODS_ALL) {//全部资源

			$whereRawArticles = $whereRaw;
			if ($state != -1) {
				$whereRawArticles = $whereRaw . " and display_state = " . $state;
			}

			$articles = \DB::table("db_ex_business.t_image_text")
				->select('id', \DB::raw('display_state as resource_state'), \DB::raw('1 as resource_type'), \DB::raw('1 as is_transcode'), \DB::raw('-1 as alive_type'), \DB::raw('-1 as manual_stop_at'), \DB::raw('-1 as zb_stop_at'), 'title', 'piece_price', 'start_at', 'img_url', 'can_select', \DB::raw('0 as config_show_view_count'), \DB::raw('0 as config_show_reward'))
				->where('app_id', '=', $app_id)
				//                ->where('payment_type','!=',3)
				->where('display_state', '!=', 2)
				->whereRaw($whereRawArticles);

			$whereRawAudios = $whereRaw;
			if ($state != -1) {
				$whereRawAudios = $whereRaw . " and audio_state = " . $state;
			}
			$audios = \DB::table("db_ex_business.t_audio")
				->select('id', \DB::raw('audio_state as resource_state'), \DB::raw('2 as resource_type'), \DB::raw('1 as is_transcode'), \DB::raw('-1 as alive_type'), \DB::raw('-1 as manual_stop_at'), \DB::raw('-1 as zb_stop_at'), 'title', 'piece_price', 'start_at', 'img_url', 'can_select', \DB::raw('0 as config_show_view_count'), \DB::raw('0 as config_show_reward'))
				->where('app_id', '=', $app_id)
				//                ->where('payment_type','!=',3)
				->where('audio_state', '!=', 2)
				->whereRaw($whereRawAudios);

			$whereRawVideos = $whereRaw;
			if ($state != -1) {
				$whereRawVideos = $whereRaw . " and video_state = " . $state;
			}
			$videos = \DB::table("db_ex_business.t_video")
				->select('id', \DB::raw('video_state as resource_state'), \DB::raw('3 as resource_type'), \DB::raw('-1 as alive_type'), \DB::raw('-1 as manual_stop_at'), \DB::raw('-1 as zb_stop_at'), 'is_transcode', 'title', 'piece_price', 'start_at', 'img_url', 'can_select', \DB::raw('0 as config_show_view_count'), \DB::raw('0 as config_show_reward'))
				->where('app_id', '=', $app_id)
				//                ->where('payment_type','!=',3)
				->where('video_state', '!=', 2)
				->whereRaw($whereRawVideos);

			if ($state != -1) {
				$whereRaw .= " and state = " . $state;
			}
			$sql = \DB::table('db_ex_business.t_alive')
				->select('id', \DB::raw('state as resource_state'), \DB::raw('4 as resource_type'), 'alive_type', 'manual_stop_at', 'zb_stop_at', 'is_transcode', 'title', 'piece_price', 'start_at', 'img_url', 'can_select', 'config_show_view_count', 'config_show_reward')
				->where('app_id', '=', $app_id)
				//                ->where('payment_type','<',-100)
				->where(function($query) use ($operator_type) {// 08-04 加入逻辑：分离单品和直播。
					if ($operator_type == StringConstants::SINGLE_LIST) {
						return $query->where('payment_type', '<', -100);
					}

				})
				->where('state', '!=', 2)
				->whereRaw($whereRaw)
				->union($videos)
				->union($articles)
				->union($audios);

			$selectCustomResultTotal = count($sql->get());
			$total_pages             = ceil($selectCustomResultTotal / $page_size);

			$page_offset = [
				'total_pages'  => $total_pages,
				'total_count'  => $selectCustomResultTotal,
				'current_page' => $page_index,
				'page_size'    => $page_size,
			];

			$page_offset = json_encode($page_offset);

			$resource_list         = $sql->orderBy('start_at', 'desc')->skip($startRow)->take($page_size)->get();
			$data['resource_list'] = $resource_list;
			$data['page_offset']   = $page_offset;

			//            dd($data);
			return $data;

		} else if ($resource_type == StringConstants::SINGLE_GOODS_ARTICLE) {//图文
			$table_name = 'db_ex_business.t_image_text';
			$whereRaw   .= " and display_state != 2 ";
			if ($state != -1) {
				$whereRaw .= " and display_state = " . $state;
			}
			$row_state      = 'display_state as resource_state';
			$row_transcode  = '1 as is_transcode';
			$row_alive_type = '1 as alive_type';
			$row_type       = '1 as resource_type';
		} else if ($resource_type == StringConstants::SINGLE_GOODS_AUDIO) {//音频
			$table_name = 'db_ex_business.t_audio';
			$whereRaw   .= " and audio_state != 2 ";
			if ($state != -1) {
				$whereRaw .= " and audio_state = " . $state;
			}
			$row_state      = 'audio_state as resource_state';
			$row_transcode  = '1 as is_transcode';
			$row_alive_type = '1 as alive_type';
			$row_type       = '2 as resource_type';
		} else if ($resource_type == StringConstants::SINGLE_GOODS_VIDEO) {//视频
			$table_name = 'db_ex_business.t_video';
			$whereRaw   .= " and video_state != 2 ";
			if ($state != -1) {
				$whereRaw .= " and video_state = " . $state;
			}
			$row_state      = 'video_state as resource_state';
			$row_transcode  = 'is_transcode';
			$row_alive_type = '1 as alive_type';
			$row_type       = '3 as resource_type';
		} else if ($resource_type == StringConstants::SINGLE_GOODS_ALIVE) {//直播
			$table_name = 'db_ex_business.t_alive';
			$whereRaw   .= " and state != 2 ";
			if ($state != -1) {
				$whereRaw .= " and state = " . $state;
			}
			$row_state      = 'state as resource_state';
			$row_transcode  = 'is_transcode';
			$row_alive_type = 'alive_type';
			$row_type       = '4 as resource_type';
		}

		if ($table_name == 'db_ex_business.t_alive') {
			$sqlNo = \DB::table($table_name)
				->select('id', \DB::raw($row_state), \DB::raw($row_type), \DB::raw($row_transcode), \DB::raw($row_alive_type), 'manual_stop_at', 'zb_stop_at', 'title', 'piece_price', 'start_at', 'img_url', 'can_select', 'config_show_view_count', 'config_show_reward')
				->where('app_id', '=', $app_id)
				//            ->where('payment_type','!=',3)
				->whereRaw($whereRaw);
		} else {
			$sqlNo = \DB::table($table_name)
				->select('id', \DB::raw($row_state), \DB::raw($row_type), \DB::raw($row_transcode), \DB::raw($row_alive_type), 'title', 'piece_price', 'start_at', 'img_url', 'can_select')
				->where('app_id', '=', $app_id)
				//            ->where('payment_type','!=',3)
				->whereRaw($whereRaw);
		}

		$selectCustomResultTotal = $sqlNo->count();
		$total_pages             = ceil($selectCustomResultTotal / $page_size);

		$resource_list = $sqlNo->orderBy('start_at', 'desc')
			->skip($startRow)->take($page_size)
			->get();

		$page_offset = [
			'total_pages'  => $total_pages,
			'total_count'  => $selectCustomResultTotal,
			'current_page' => $page_index,
			'page_size'    => 10,
		];

		$page_offset = json_encode($page_offset);

		$data['resource_list'] = $resource_list;
		$data['page_offset']   = $page_offset;

		return $data;
	}

	/*
	 * 个人模式服务号推送开关切换
	 *需要前端带给person_message_push（当前服务号通知状态 ） resource_type  resource_id
	 * */

	private function getResourceIdList ($package_id, $is_try)
	{
		$app_id = AppUtils::getAppID();
		if ($is_try != -1) {//试听
			$resource_id_list = \DB::select("select distinct(resource_id) from db_ex_business.t_pro_res_relation where app_id = '$app_id' and relation_state=0 and is_try = 1");
		} else {
			if ($package_id == "") {
				$resource_id_list = \DB::select("select distinct(resource_id) from db_ex_business.t_pro_res_relation where app_id = '$app_id' and relation_state=0");
			} else {
				$resource_id_list = \DB::select("select distinct(resource_id) from db_ex_business.t_pro_res_relation where app_id = '$app_id' and product_id='$package_id' and relation_state=0");
			}
			//            $resource_id_list = \DB::select("select distinct(resource_id) from db_ex_business.t_pro_res_relation where app_id = '$app_id' and relation_state=0");
		}

		$id_list = ['1'];
		if ($resource_id_list) {
			foreach ($resource_id_list as $key => $resource_id) {
				$id_list[] = "'" . $resource_id->resource_id . "'";
			}
		}

		return $id_list;
	}

	private function getProductIdByResId ($resource_id)
	{
		$app_id           = AppUtils::getAppID();
		$pro_res_relation = \DB::table("db_ex_business.t_pro_res_relation")
			->where('app_id', '=', $app_id)
			->where('resource_id', '=', $resource_id)
			->where('relation_state', '=', 0)
			->first();
		if ($pro_res_relation) {
			return $pro_res_relation->product_id;
		} else {
			return '';
		}
	}

	private function queryGoods ($goods_id, $goods_type)
	{
		$app_id = AppUtils::getAppID();

		$list = \DB::table("db_ex_business.t_pro_res_relation")
			->where("app_id", '=', $app_id)
			->where("resource_id", '=', $goods_id)
			->where("resource_type", '=', $goods_type)
			->where("relation_state", '=', StringConstants::RELATION_NORMAL)
			->get();

		return $list;
	}

	private function getPackageInfo ($package_id)
	{
		$package_info = \DB::table("db_ex_business.t_pay_products")
			->where("app_id", '=', AppUtils::getAppID())
			->where("id", '=', $package_id)
			->first();

		return $package_info;
	}

	private function fen2Yuan ($orgValue)
	{
		$number = number_format($orgValue / 100, 2);
		$number = str_replace(",", "", $number);

		return $number;
	}

	/**
	 * 创建资源页面
	 * * 参数
	 * 1：type：1-article；2-audio；3-video；4-alive
	 * 2-upload_channel_type(新增的渠道:1-单品新增;2-专栏新增;3-会员新增)
	 */
	public function createResource ()
	{
		$app_id              = AppUtils::getAppID();
		$resource_type       = Input::get('type', -1);
		$upload_channel_type = Input::get('upload_channel_type', '');
		$is_set_temp         = MessagePush::isHadSetTemp($app_id);//判断是否设置行业类型
		if ($resource_type < -1 || $resource_type > 4) {
			return "无效的资源类型!";
		}

		//page_type是传给前端的,告诉前端该页面得类型(新增还是编辑)
		$page_type = 0;

		$use_collection = DB::connection('mysql_config')->table('t_app_conf')
			->where('app_id', $app_id)->where('wx_app_type', 1)
			->value('use_collection');

		//拉取分类
		//应用权限
		$appModuleInfo = AppUtils::getModuleInfo($app_id);

		// 分类数据
		if (!empty($appModuleInfo) && $appModuleInfo[0]->resource_category == 1) {
			// 分类数据信息
			$category_info = Utils::categoryInfo();
		}

		$type = 0;//新增页面
		switch ($resource_type) {
			case -1:
				return View("admin.resManage.resTypeSelect", compact("page_type", 'upload_channel_type'));
			case 1:
				return View("admin.resManage.manageArticle", compact("page_type", 'upload_channel_type', 'app_id', 'is_set_temp', 'category_info', 'use_collection'));
			case 2:
				return View("admin.resManage.manageAudio", compact("page_type", 'upload_channel_type', 'app_id', 'is_set_temp', 'category_info', 'use_collection'));
			case 3:
				return View("admin.resManage.manageVideo", compact("page_type", 'upload_channel_type', 'app_id', 'is_set_temp', 'category_info', 'use_collection'));
			case 4:
				return View("admin.resManage.manageAlive", compact("page_type", 'upload_channel_type', 'app_id', 'is_set_temp', 'category_info', 'type'));
		}
	}


	//在资源关系表t_pro_res_relation中查询非该会员id所有的专栏数据列表

	/**
	 * 编辑资源页面
	 * 参数1：type：1-article；2-audio；3-video；4-alive
	 * 参数2：resource_id:
	 */
	public function editResource ()
	{
		$resource_type       = Input::get('type', '');
		$resource_id         = Input::get('id', '');
		$upload_channel_type = Input::get('upload_channel_type', '');
		$app_id              = AppUtils::getAppID();
		$is_set_temp         = MessagePush::isHadSetTemp($app_id);//判断是否设置行业类型
		//参数合法性判断
		if ($resource_type < 0 || $resource_type > 4) {
			return "无效的资源类型";
		}

		//page_type是传给前端的,告诉前端该页面得类型(新增还是编辑)
		$page_type = 1;

		$resource_info = Utils::getResourceInfo($resource_id, $resource_type);

		$use_collection = DB::connection('mysql_config')->table('t_app_conf')
			->where('app_id', $app_id)->where('wx_app_type', 1)
			->value('use_collection');
		$type           = 1;//编辑页面

		//应用权限
		$appModuleInfo = AppUtils::getModuleInfo(AppUtils::getAppID());

		// 分类数据
		if (!empty($appModuleInfo) && $appModuleInfo[0]->resource_category == 1) {
			// 分类数据信息
			$category_info = Utils::categoryInfo();
			// 所属分类信息
			$package_category = $this->resourceCategoryInfo($resource_id);
		}
		switch ($resource_type) {
			case 1:
				//                $push_state = DB::connection('mysql')->table('t_image_text')->where('app_id',$app_id)->where('id',$resource_id)->value('push_state');
				return View("admin.resManage.manageArticle", compact("resource_info", "page_type", "upload_channel_type", 'is_set_temp', 'category_info', 'package_category', 'use_collection', 'push_state'));
			case 2:
				//                $push_state = DB::connection('mysql')->table('t_audio')->where('app_id',$app_id)->where('id',$resource_id)->value('push_state');
				return View("admin.resManage.manageAudio", compact("resource_info", "page_type", "upload_channel_type", 'is_set_temp', 'category_info', 'package_category', 'use_collection', 'push_state'));
			case 3:
				//                $push_state = DB::connection('mysql')->table('t_video')->where('app_id',$app_id)->where('id',$resource_id)->value('push_state');
				return View("admin.resManage.manageVideo", compact("resource_info", "page_type", "upload_channel_type", 'is_set_temp', 'category_info', 'package_category', 'use_collection', 'push_state'));
			case 4:
				//获取直播讲师列表
				//                $alive_roles=\DB::table("t_alive_role")->where('app_id','=',$app_id)->where('alive_id','=',$resource_id)
				//                    ->where('state','=','0')->get();

				$resource_info->zb_stop_at = Utils::zb_time(strtotime($resource_info->zb_stop_at) - strtotime($resource_info->zb_start_at));

				//                                return (array)$resource_info;

				return View("admin.resManage.manageAlive", compact("resource_info", "page_type", "upload_channel_type", 'is_set_temp', 'category_info', 'package_category', 'type'));
		}
	}

	//保存视频资源的时候主动查询转码状态

	private function resourceCategoryInfo ($package_id)
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

	//保存视频直播时主动查询转码状态

	/**
	 * 保存上传资源;
	 * 参数:
	 * 1-params(基本信息+上架信息);
	 * 2-resource_type(1-图文,2-音频,3-视频,4-直播);
	 * 3-upload_channel_type(新增的渠道:1-单品新增;2-专栏新增;3-会员新增)
	 * 4-package_id(当upload_channel_type=2或3时有值)
	 * 5-resource_params(当资源为视频、直播时,有值)
	 * 6-roleParams(直播讲师列表)
	 */
	public function uploadResource ()
	{

		$params = Input::get("params", '');
		//        dump($params);
		//        exit;
		$category_type       = Input::get("category_type", '');//分类信息
		$resource_type       = Input::get("resource_type", '');
		$upload_channel_type = Input::get("upload_channel_type", '');
		$package_id          = Input::get("package_id", '');
		$externalParams      = Input::get("resource_params", '');
		$roleParams          = Input::get("roleParams");//直播讲师列表

		//新增资源:操作步骤
		//1.根据upload_channel_type的值做不同的处理,
		//upload_channel_type = 1:若piece_price = 0 ,payment_type=1,若不等于0,payment_type=2
		//upload_channel_type = 2:payment_type=3,并在关系表t_pro_res_relation生成一条关系记录
		//2.生成资源信息(根据resource_type在对应的资源表中新插入一条记录)

		$ret = $this->saveResourceInfo($params, $category_type, $resource_type, $upload_channel_type, $package_id, $externalParams, $roleParams, StringConstants::RESOURCE_ADD);

		if ($resource_type == StringConstants::SINGLE_GOODS_ALIVE) {
			if (starts_with($ret, 'l_')) {
				return ['code' => 0, 'alive_id' => $ret];
			} else {
				$msg = $ret;

				return response()->json(Utils::pack("0", StringConstants::Code_Failed, $msg));
			}
		}
		if ($ret == '0') {
			return $this->result('新增资源成功!');
		} else {
			$msg = $ret;

			return response()->json(Utils::pack("0", StringConstants::Code_Failed, $msg));
		}
	}

	//查询资源goods_id在其他专栏是否被拥有

	/**保存资源信息【不要一直往这个方法加东西了，可以独立出去独立出去，还加！良心不痛吗】
	 * todo::心疼宝宝3秒钟
	 *
	 * @param $params              //资源参数
	 * @param $category_type       //分类信息
	 * @param $resource_type       //资源类型
	 * @param $upload_channel_type (编辑的渠道:1-单品;2-专栏;3-会员)
	 * @param $package_id
	 * @param $externalParams
	 * @param $roleParams          //直播讲师列表
	 * @param $operator_type       //保存方式：添加、保存
	 *
	 * @return int|string
	 */
	private function saveResourceInfo ($params, $category_type, $resource_type, $upload_channel_type, $package_id, $externalParams, $roleParams, $operator_type)
	{
		$app_id = AppUtils::getAppID();
		//参数合法性判断
		//新增资源:操作步骤
		//1.根据upload_channel_type的值做不同的处理,
		//upload_channel_type = 1:若piece_price = 0 ,payment_type=1,若不等于0,payment_type=2
		//upload_channel_type = 2:payment_type=3,并在关系表t_pro_res_relation生成一条关系记录,对应的专栏资源数加1
		//2.生成资源信息(根据resource_type在对应的资源表中新插入一条记录)

		//编辑资源:操作步骤
		//--根据resource_type的值,去更新对应资源的信息

		//最后在保存资源成功后,图片压缩
		if (Utils::isEmptyString($params['title'])) {//判断资源名
			return "保存失败，请填写活动名称!";
		}
		if (Utils::isEmptyString($params['img_url'])) {//判断资源封面图
			return "保存失败，请上传资源封面图!";
		}
		if (Utils::isEmptyString($params['start_at'])) {//判断上架时间
			return "保存失败，请选择上架时间!";
		}
		if ($resource_type == StringConstants::SINGLE_GOODS_AUDIO) {
			if (Utils::isEmptyString($params['audio_url'])) {//判断音频
				return "保存失败，请上传音频!";
			}
		}
		//        if($resource_type == StringConstants::SINGLE_GOODS_VIDEO){
		//            if(Utils::isEmptyString($params['video_url'])){//判断视频
		//                return "保存失败，请上传视频!";
		//            }
		//        }
		//        if($resource_type == StringConstants::SINGLE_GOODS_ALIVE){
		//            if($params['alive_type'] == 1 && Utils::isEmptyString($params['alive_video_url'])){//判断直播视频
		//                return "保存失败，请上传视频!";
		//            }
		//        }

		if ($upload_channel_type == StringConstants::ADD_CHANNEL_SINGLE) {//单品新增
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
		}

		//根据resource_type来判断表名
		$table_name_arr = [
			1 => "db_ex_business.t_image_text",
			2 => "db_ex_business.t_audio",
			3 => "db_ex_business.t_video",
			4 => "db_ex_business.t_alive",
		];
		//根据resource_type来判断状态字段
		$field_arr  = [
			1 => "display_state",
			2 => "audio_state",
			3 => "video_state",
			4 => "state",
		];
		$table_name = $table_name_arr[ $resource_type ];
		$state      = $field_arr[ $resource_type ];
		//分离descrb
		if (array_key_exists('descrb', $params)) {
			$params['descrb'] = ResContentComm::sliceUE($params['descrb']);
			if ($params['descrb'] == false) {
				//编辑器内容有问题 给前端返回提示信息并取消上传
				return "上传失败，复制的内容有问题，请尝试分开复制，或记录复制内容并联系技术小哥";
			}
		}
		if (array_key_exists('content', $params)) {
			$params['content'] = ResContentComm::sliceUE($params['content']);
			if ($params['content'] == false) {
				//编辑器内容有问题 给前端返回提示信息并取消上传
				return "上传失败，复制的内容有问题，请尝试分开复制，或记录复制内容并联系技术小哥";
			}
		}
		if (array_key_exists('try_content', $params)) {
			$params['try_content'] = ResContentComm::sliceUE($params['try_content']);
			if ($params['content'] == false) {
				//编辑器内容有问题 给前端返回提示信息并取消上传
				return "上传失败，复制的内容有问题，请尝试分开复制，或记录复制内容并联系技术小哥";
			}
		}

		//新增时压缩图片先和原图片链接一致,保证前端加载正常
		$params['img_url_compressed'] = $params['img_url'];
		//插入当前时间
		$current_time = Utils::getTime();
		if ($operator_type == StringConstants::RESOURCE_ADD) {//新增资源
			$resource_id_arr      = [
				1 => 'i_',
				2 => 'a_',
				3 => 'v_',
				4 => 'l_',
			];
			$resource_id          = Utils::getUniId($resource_id_arr[ $resource_type ]);
			$params['id']         = $resource_id;
			$params['created_at'] = $current_time;

			//            kevin
			if ($resource_type == StringConstants::SINGLE_GOODS_ALIVE) {
				//            kevin

				//获取房间名
				$params['room_id'] = Utils::createGroupChatRoom();
			}
			/***分类信息-新增***/
			$this->createCategoryresource($resource_id, $category_type, $resource_type);

			if ($resource_type == StringConstants::SINGLE_GOODS_VIDEO) {
				$video_upload = Utils::getVideoUpload(AppUtils::getAppID());
				$uploadmax    = Utils::getVideoMax();
				if ($video_upload > $uploadmax) {
					return "上传失败,限量每天新增 $uploadmax 个视频，敬请明天再传！";
				}
			}
		} else {//编辑资源
			$resource_id          = $params['id'];
			$params['updated_at'] = $current_time;
			/****分类信息-编辑*****/
			$this->saveCategoryresource($resource_id, $category_type, $params, $resource_type);
			if ($resource_type == StringConstants::SINGLE_GOODS_ALIVE) {
				//旧的更新，新的插入
				//角色设置与直播设置分离   老逻辑是将前面设置的讲师全部置1  然后插入所有的新的   本次改版将  卒
				//                $updateRole=\DB::update("update t_alive_role set state='1' where app_id=? and alive_id=?",[$app_id,
				//                    $params['id']]);

				//若该直播已通知（if_push=1），则不允许更改if_push和push_ahead的值
				$alive_info = Utils::getAliveInfo($params['id']);
				if ($alive_info) {
					if ($alive_info->if_push == 2) {
						if (array_key_exists('if_push', $params)) {
							Utils::array_remove($params, 'if_push');
						}
						if (array_key_exists('push_ahead', $params)) {
							Utils::array_remove($params, 'push_ahead');
						}
					}

				}
			}
		}
		$params['app_id'] = $app_id;

		if ($resource_type == StringConstants::SINGLE_GOODS_AUDIO) {//音频-开启异步压缩
			if (array_key_exists('audio_url', $params) && array_key_exists('audio_size', $params) && array_key_exists('audio_length', $params))//压缩+转码
			{
				$params['m3u8_url']           = '';
				$params['audio_compress_url'] = $params['audio_url'];

				AudioUtils::audioCompress($this->request, $app_id, $resource_id, $params['audio_url'], $params['audio_length']);
				//                Utils::asyncThread(env("HOST_URL").'/mp3tom3u8?app_id='.$app_id.'&id='.$resource_id.'&cdn_url='.$params['audio_url']);
			}
		}
		if ($resource_type == StringConstants::SINGLE_GOODS_VIDEO) {//视频

			if (array_key_exists('file_id', $params)) {
				Utils::array_remove($params, 'file_id');
			}

			//            $resource_info = Utils::getResourceInfo($resource_id, $resource_type);
			//            if($resource_info->fild_id!=$externalParams['public_video']) {
			$public_file_id   = $externalParams['public_video'];
			$public_size_text = $externalParams['public_size_text'];
			$public_size_text = explode("M", $public_size_text)[0]; //去掉M
			if (!empty($public_file_id) && !empty($public_size_text)) {
				$params['file_id']    = $public_file_id;
				$params['video_size'] = $public_size_text;
				$params['video_url']  = ''; //暂时置空,转码后获取url
				//转码之前,视频置为下架
				if ($upload_channel_type == StringConstants::ADD_CHANNEL_SINGLE || $operator_type == StringConstants::RESOURCE_ADD) {//单品来源的新增、编辑或专栏(会员)内新增视频时
					$params['video_state'] = 1;
				} else {
					if (array_key_exists('video_state', $params)) {
						Utils::array_remove($params, 'video_state');
					}
				}
				$params['is_transcode'] = 0;

				$this->modifyVideoParams($public_file_id, $params);

				if ($params['is_transcode'] == 2) {//转码失败
					//在中间表t_video_middle_transcode中查询该file_id记录;若不存在则该视频出于转码中,反之则根据具体字段来判别转码状态
					$this->queryVideoTranscode($params, 0);

				}
			}

			//判断是否上传了视频贴片,如果没上传用视频封面做贴片
			if (!array_key_exists('patch_img_url', $params)) {
				$params['patch_img_url']            = $params['img_url'];
				$params['patch_img_url_compressed'] = $params['img_url_compressed'];
			}
			//            }
		}
		if ($resource_type == StringConstants::SINGLE_GOODS_ALIVE) {//直播

			//如果是视频直播,未转码+隐藏;如果是语音直播,已转码+显示
			if ($operator_type == StringConstants::RESOURCE_EDIT) {   //编辑直播，获取直播状态
				$aliveInfo   = \DB::table('t_alive')->where('app_id', $params['app_id'])->where('id', $params['id'])->first();
				$alive_state = Utils::getAliveState($aliveInfo->zb_start_at, $aliveInfo->video_length, $aliveInfo->manual_stop_at, $aliveInfo->zb_stop_at, $aliveInfo->push_state, $aliveInfo->rewind_time, $aliveInfo->alive_type);

				if (2 == $aliveInfo->alive_type)
					$params['alive_type'] = $aliveInfo->alive_type;
			} else {
				$aliveInfo   = new \stdClass();
				$alive_state = 0;
			}

			//$params['zb_stop_at'] 这个值啊 现在传的是预计直播时长 单位是秒
			$params['zb_stop_at'] = date('Y-m-d H:i:s', ($params['zb_stop_at'] + strtotime($params['zb_start_at'])));

			//            return $params;

			if ($params['alive_type'] == 1) {//视频直播
				//                $version_type = AppUtils::get_version_type();
				if (!AppUtils::IsPageVisual('live_video', 'version_type')) {
					return "基础版不支持视频直播";
				}

				if (array_key_exists('file_id', $params)) {
					Utils::array_remove($params, 'file_id');
				}

				//直播视频的file_id,video_size
				$public_file_id   = $externalParams['public_video'];
				$public_size_text = $externalParams['public_size_text'];
				$public_size_text = explode("M", $public_size_text)[0]; //去掉M
				if (!empty($public_file_id) && !empty($public_size_text)) {
					$params['file_id']    = $public_file_id;
					$params['video_size'] = $public_size_text;
					if ($upload_channel_type == StringConstants::ADD_CHANNEL_SINGLE || $operator_type == StringConstants::RESOURCE_ADD) {//单品来源的新增、编辑
						$params['state'] = 1;
					} else {
						if (array_key_exists('state', $params)) {
							Utils::array_remove($params, 'state');
						}
					}
					$params['is_transcode'] = 0;

					$this->modifyAliveParams($public_file_id, $params);

					if ($params['is_transcode'] == 2) {//转码失败
						//在中间表t_video_middle_transcode中查询该file_id记录;若不存在则该视频出于转码中,反之则根据具体字段来判别转码状态
						$this->queryVideoTranscode($params, 1);
					}
				}
			} else if ($params['alive_type'] == 2 && AppUtils::isOursApp()) {   //推流直播
				if (!AppUtils::IsPageVisual('live_video', 'version_type')) {
					return "基础版不支持视频直播";
				}
				$this->aliveParams($params, $operator_type, $alive_state);
			} else {//语音直播
				$params['state']        = 0;
				$params['is_transcode'] = 1;
			}

			//处理参数可否修改，添加直播开始后的参数修改限制
			$check_res = $this->checkParams($params, $operator_type, $aliveInfo, $alive_state);
			if (!$check_res['state']) {
				return $check_res['msg'];
			}
			//  角色设置与直播设置分离  故  注释之
			//            //插直播角色表
			//            if($roleParams)
			//            {
			//                foreach($roleParams as $key => $value)
			//                {
			//                    $value["app_id"]=$app_id;
			//                    $value["alive_id"]=$resource_id;
			//                    $value["state"]=0;
			//                    $value["created_at"]=Utils::getTime();
			//                    $insertRole=\DB::table("t_alive_role")->insert($value);
			//                    if($insertRole){
			//                    }else{
			//                    }
			//                }
			//            }
		}
		if ($upload_channel_type == StringConstants::ADD_CHANNEL_SINGLE) {//单品新增
			if ($params['piece_price'] == 0) {
				$params['payment_type'] = 1;
			} else {
				$params['payment_type'] = 2;
			}
		}

		if ($upload_channel_type == StringConstants::ADD_CHANNEL_PACKAGE || $upload_channel_type == StringConstants::ADD_CHANNEL_MEMBER) { // 专栏新增或会员新增

			//判断params中是否存在piece_price字段,有则剔除
			if (array_key_exists('piece_price', $params)) {
				Utils::array_remove($params, 'piece_price');
			}
			if ($operator_type == StringConstants::RESOURCE_ADD) {

				$params['payment_type'] = 3;
				$params['product_id']   = $package_id;
				$package_info           = $this->getPackageInfo($package_id);
				if ($package_info) {
					$params['product_name'] = $package_info->name;
				} else {
					$params['product_name'] = '';
				}
				//在关系表t_pro_res_relation生成一条关系记录,对应的专栏资源数加1
				$insert_pro_res_relation = $this->createProResRelation($package_id, $resource_id, $resource_type);
				if ($insert_pro_res_relation) {
					//对应的专栏资源数加1
					$update_resource_count = $this->updatePackageResourceCount($package_id);
					if ($update_resource_count) {
					} else {
						return "对应的专栏资源数加1失败!";
					}
				} else {
					return "生成关系记录失败";
				}
			}
		}

		if ($operator_type == StringConstants::RESOURCE_ADD) {//新增
			$result = \DB::table($table_name)->insert($params);
			$msg    = "新增资源失败!";

		} else {//编辑
			$result = \DB::table($table_name)
				->where("app_id", '=', $app_id)
				->where("id", '=', $resource_id)
				->where($state, '!=', 2)
				->update($params);
			$msg    = "更新资源失败!resource_id:" . $resource_id;
		}

		if ($result) {
			//获取资源中所有图片大小,并更新至image_size_total中
			$item = \DB::table($table_name)->where('app_id', '=', $app_id)->where('id', '=', $resource_id)->first();
			if ($item) {
				switch ($resource_type) {
					case 1://图文
						Utils::updateImageTextTotalSize($item);
						break;
					case 2://音频
						Utils::updateAudioImgTotalSize($item);
						break;
					case 3://视频
						Utils::updateVideoImgTotalSize($item);
						break;
				}
			}
			//图片压缩
			if (array_key_exists('img_url', $params)) ImageUtils::resImgCompress($this->request, $table_name, $app_id, $resource_id, $params['img_url']);
			if (array_key_exists('sign_url', $params)) ImageUtils::audioSignImgCompress($this->request, $app_id, $resource_id, $params['sign_url']);
			if (array_key_exists('patch_img_url', $params)) ImageUtils::videoPatchImgCompress($this->request, $app_id, $resource_id, $params['patch_img_url']);
			if (array_key_exists('alive_img_url', $params)) ImageUtils::aliveImgCompress($this->request, $app_id, $resource_id, $params['alive_img_url']);

			if ($resource_type == StringConstants::SINGLE_GOODS_ALIVE) { // 如果是直播 返回直播的id
				return $params['id'];
			}

			return StringConstants::Code_Succeed;
		} else {
			return $msg;
		}
	}

	//得出关系表中资源id的集合

	private function createCategoryresource ($resource_id, $category_type, $resource_type)
	{
		$app_id = AppUtils::getAppID();

		$category_info = \DB::connection('mysql')->table('t_category')
			->where('app_id', '=', $app_id)
			->where('id', '!=', '0')
			->pluck('id');

		// 直接存入四條分類記錄
		foreach ($category_info as $value) {
			$insert = \DB::connection('mysql')->table('t_category_resource')->insert(
				['app_id' => $app_id, 'category_id' => $value, 'resource_id' => $resource_id, 'resource_type' => $resource_type, 'state' => 0, 'created_at' => date('Y-m-d H:i:s', time())]);
		}

		// 同时插入一条首页分类数据  默认显示
		$insert = DB::connection('mysql')->table('t_category_resource')->insert(
			['app_id' => $app_id, 'category_id' => '0', 'resource_id' => $resource_id, 'resource_type' => $resource_type, 'state' => 1, 'created_at' => date('Y-m-d H:i:s', time())]
		);

		// 如果有分类信息 添加
		//        dd($category_type);
		if (!empty($category_type)) {
			// 再更新所有分类为现在的状态
			\DB::connection('mysql')->table('t_category_resource')
				->where('app_id', '=', $app_id)
				->where('resource_id', '=', $resource_id)
				->where('resource_type', '=', $resource_type)
				->whereIn('category_id', $category_type)
				->update(['state' => 1]);
		}
	}

	private function saveCategoryresource ($resource_id, $category_type, $params, $resource_type)
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
			->where('resource_type', '=', $resource_type)
			->whereIn('category_id', $category_info)
			->get();
		//            dd($relation);
		// 如果不存在该资源的分类数据，则全部插入，默认不显示
		if (count($relation) == 0) {
			foreach ($category_info as $value) {
				$insert = DB::connection('mysql')->table('t_category_resource')->insert(
					['app_id' => $app_id, 'category_id' => $value, 'resource_id' => $resource_id, 'resource_type' => $resource_type, 'state' => 0, 'created_at' => date('Y-m-d H:i:s', time())]
				);
			}
		}

		// 首先 重置所有分类（四个）的状态为0
		$now                  = Utils::getTime();
		$result_app_categorys = DB::connection('mysql')->table('t_category_resource')
			->where('app_id', '=', $app_id)
			->where('resource_id', '=', $resource_id)
			->whereIn('category_id', $category_info)
			->where('resource_type', '=', $resource_type)
			->update(['state' => 0, 'updated_at' => $now]);

		if ($result_app_categorys) {
		} else {
		}
		if (!empty($category_type)) {

			// 更新所有提交的分类数据
			$result_app_category_avalite = DB::connection('mysql')->table('t_category_resource')
				->where('app_id', '=', $app_id)
				->where('resource_id', '=', $resource_id)
				->whereIn('category_id', $category_type)
				->where('resource_type', '=', $resource_type)
				->update(['state' => 1]);

			if ($result_app_category_avalite) {
			} else {
			}
		}
		//        }

		// 如果没有首页分类的数据 也插入一条
		$home_page_category = DB::connection('mysql')->table('t_category_resource')
			->where('app_id', '=', $app_id)
			->where('resource_id', '=', $resource_id)
			->where('category_id', '=', '0')
			->where('resource_type', '=', $resource_type)
			->first();
		if (empty($home_page_category)) {
			// 同时插入一条首页分类数据  默认显示
			$insert = DB::connection('mysql')->table('t_category_resource')->insert(
				['app_id' => $app_id, 'category_id' => '0', 'resource_id' => $resource_id, 'resource_type' => $resource_type, 'state' => 1, 'created_at' => Utils::getTime()]
			);
		}
	}

	private function modifyVideoParams ($file_id, &$params)
	{

		$video_length                      = ResContentComm::getVideoLength($file_id);
		$params['video_length']            = $video_length;
		$params['video_url']               = '';
		$params['video_mp4_vbitrate']      = '';
		$params['video_mp4_size']          = '';
		$params['video_mp4']               = '';
		$params['video_hls']               = '';
		$params['video_mp4_high_size']     = '';
		$params['video_mp4_high_vbitrate'] = '';
		$params['video_mp4_high']          = '';

		$video_url      = '';
		$video_mp4      = '';
		$video_mp4_high = '';
		$video_hls      = '';
		//根据file_id 查询视频链接
		$private_params = ['fileId' => $file_id];

		$resultArray = ResContentComm::videoApi('DescribeVodPlayUrls', $private_params);

		if ($resultArray['code'] == 0 && array_key_exists('playSet', $resultArray)) {
			$playSet = $resultArray['playSet'];
			for ($i = 0; $i < count($playSet); $i++) {
				if ($playSet[ $i ]['definition'] == 0)//原视频
				{
					$video_url           = $playSet[ $i ]['url'];
					$params['video_url'] = $video_url;
				} else if ($playSet[ $i ]['definition'] == 20)//标清mp4
				{
					$video_mp4         = $playSet[ $i ]['url'];
					$video_mp4_bitrate = $playSet[ $i ]['vbitrate'];
					$video_mp4_size    = $video_mp4_bitrate / 8 * $video_length / 1024 / 1024;//视频大小(M)= kbps码率/8 * 视频时长/1024

					$params['video_mp4_vbitrate'] = $video_mp4_bitrate;
					$params['video_mp4_size']     = $video_mp4_size;
					$params['video_mp4']          = $video_mp4;
				} else if ($playSet[ $i ]['definition'] == 230)//高清m3u8
				{
					$video_hls           = $playSet[ $i ]['url'];
					$params['video_hls'] = $video_hls;

				} else if ($playSet[ $i ]['definition'] == 30)//高清mp4
				{
					$video_mp4_high = $playSet[ $i ]['url'];
					//添加码率
					$video_mp4_high_bitrate = $playSet[ $i ]['vbitrate'];
					$video_mp4_high_size    = $video_mp4_high_bitrate / 8 * $video_length / 1024 / 1024;//视频大小(M)= kbps码率/8 * 视频时长/1024

					$params['video_mp4_high_size']     = $video_mp4_high_size;
					$params['video_mp4_high_vbitrate'] = $video_mp4_high_bitrate;
					$params['video_mp4_high']          = $video_mp4_high;
				} else//其他格式 暂时没有提供支持
				{
				}
			}

			if (empty($video_url) || empty($video_mp4)) {
				$params['video_state']  = 1;
				$params['is_transcode'] = 2;
			} else {
				$params['video_state']  = 0;
				$params['is_transcode'] = 1;
			}

		}
	}

	private function queryVideoTranscode (&$params, $type)
	{
		$transcode_info = \DB::table("db_ex_business.t_video_middle_transcode")
			->where("file_id", '=', $params['file_id'])
			->first();
		if ($transcode_info) {
			if ($type == 0) {//视频

				if (empty($transcode_info->video_url) || empty($transcode_info->video_mp4)) {
					$params['is_transcode'] = 2;
				} else {
					$params['is_transcode'] = 1;
				}
			} else if ($type == 1) {//直播视频
				if (empty($transcode_info->m3u8url)) {
					$params['is_transcode'] = 2;
				} else {
					$params['is_transcode'] = 1;
				}
			}
		} else {
			$params['is_transcode'] = 0;
		}
	}

	//在中间表t_video_middle_transcode中查询该file_id记录;若不存在则该视频出于转码中,反之则根据具体字段来判别转码状态

	private function modifyAliveParams ($fileId, &$params)
	{

		$length                             = ResContentComm::getVideoLength($fileId);
		$params['video_length']             = $length;
		$params['alive_m3u8_high_vbitrate'] = '';
		$params['alive_m3u8_high_size']     = '';
		$params['list_file_content']        = '';

		//根据fileId查询hls地址
		$getParams   = ['fileId' => $fileId];
		$resultArray = ResContentComm::videoApi('DescribeVodPlayUrls', $getParams);
		if ($resultArray['code'] == 0 && array_key_exists('playSet', $resultArray)) {
			$m3u8url = '';//获取m3u8链接
			for ($i = 0; $i < count($resultArray['playSet']); $i++) {
				if ($resultArray['playSet'][ $i ]['definition'] == 230) {
					$m3u8url            = $resultArray['playSet'][ $i ]['url'];
					$alive_m3u8_bitrate = $resultArray['playSet'][ $i ]['vbitrate'];
					$alive_m3u8_size    = $alive_m3u8_bitrate / 8 * $length / 1024 / 1024;//视频大小(M)= kbps码率/8 * 视频时长/1024

					$params['alive_m3u8_high_vbitrate'] = $alive_m3u8_bitrate;
					$params['alive_m3u8_high_size']     = $alive_m3u8_size;

				}
			}

			if (empty($m3u8url))//转码失败
			{
				$params['is_transcode'] = 2;

				return;
			} else {

				$listNew = Utils::getAliveList($m3u8url);

				$params['state']             = 0;
				$params['is_transcode']      = 1;
				$params['list_file_content'] = $listNew;
				$params['video_length']      = $length;
			}
		}
	}

	//分类信息-新增

	/**
	 * @param $params        直播参数
	 * @param $operator_type 新建或者更新
	 * @param $alive_state   直播状态
	 */
	private function aliveParams (&$params, $operator_type, $alive_state)
	{
		$params['state']        = 0;
		$params['is_transcode'] = 1;
		if ($operator_type == StringConstants::RESOURCE_EDIT) {//更新资源
			$new_alive_param = false;
			//            if($alive_state == 0){//判断是否有记录
			//                $new_alive_param = true;
			//            }
		} else if ($operator_type == StringConstants::RESOURCE_ADD) {//新增资源
			$new_alive_param = true;
		} else {
			$new_alive_param = false;
		}
		if ($new_alive_param) {//新的推流信息
			$params_zb_stop = strtotime($params['zb_stop_at']);//直播结束时间
			$txtime         = strtoupper(base_convert($params_zb_stop + env('QCLOUD_EXPIRY'), 10, 16));//推流地址有效时间默认24小时
			$key            = env("QCLOUD_PKEY");  //推流加密key
			$bizid          = env("QCLOUD_BIZID"); //腾讯云BIZID
			$channel_id     = $bizid . '_' . Utils::getRandom(16, 'ALL');    //推流的房间号
			$txSecret       = md5($key . $channel_id . $txtime);  //加密链
			//推流地址
			$push_url = 'rtmp://' . $bizid . ".livepush.myqcloud.com/live/" . $channel_id . "?bizid=" . $bizid . "&txSecret=" . $txSecret . "&txTime=" . $txtime . "&record=hls";
			//直播地址
			$play_url[]           = 'rtmp://' . $bizid . ".liveplay.myqcloud.com/live/" . $channel_id;
			$play_url[]           = 'http://' . $bizid . ".liveplay.myqcloud.com/live/" . $channel_id . ".flv";
			$play_url[]           = 'http://' . $bizid . ".liveplay.myqcloud.com/live/" . $channel_id . ".m3u8";
			$params['txtime']     = date("Y-m-d H:i:s", $params_zb_stop + env('QCLOUD_EXPIRY'));
			$params['channel_id'] = $channel_id;
			$params['push_url']   = $push_url;
			$params['play_url']   = json_encode($play_url);
			$params['push_state'] = 2;
		}
	}

	//分类信息-编辑

	/**
	 * @param $params         直播参数
	 * @param $operator_type  操作类型 修改或者添加
	 * @param $aliveInfo      直播信息
	 * @param $alive_state    直播状态
	 *
	 * @return array
	 */
	private function checkParams ($params, $operator_type, $aliveInfo, $alive_state)
	{
		//        if($operator_type != StringConstants::RESOURCE_EDIT) return ['state'=>true];    //不是编辑状态则返回true
		//        if($alive_state == 0 ) return ['state'=>true];        // 还没开始直播，返回true
		//        if($params['zb_start_at'] != $aliveInfo->zb_start_at ) return ['state'=>false,"msg"=>'直播不是即将开始状态，不可修改开始时间。'];
		//        if($params['alive_type'] != $aliveInfo->alive_type) return ['state'=>false,"msg"=>'直播不是即将开始状态，不可修改直播类型。'];
		return ['state' => true];
	}

	//直播所属分类

	private function createProResRelation ($package_id, $resource_id, $resource_type)
	{
		$app_id = AppUtils::getAppID();

		$package_info = $this->getPackageInfo($package_id);
		if ($package_info) {
			$package_name = $package_info->name;
		} else {
			$package_name = '';
		}

		//先查询是否存在被删除的记录,有则恢复。无则新增。
		$relation_info = $this->queryProResRelation($package_id, $resource_id, $resource_type);
		if ($relation_info) {//存在
			//恢复relation_state=0,即正常
			$result = $this->updateProResRelationState($package_id, $resource_id, $resource_type);
		} else {//不存在
			//新增记录

			$params['app_id']        = $app_id;
			$params['product_id']    = $package_id;
			$params['product_name']  = $package_name;
			$params['resource_type'] = $resource_type;
			$params['resource_id']   = $resource_id;
			$params['created_at']    = Utils::getTime();
			$result                  = \DB::table("db_ex_business.t_pro_res_relation")->insert($params);
		}

		return $result;
	}

	//对应的专栏资源数加1

	private function queryProResRelation ($package_id, $resource_id, $resource_type)
	{
		$app_id = AppUtils::getAppID();

		$relation_info = \DB::table("db_ex_business.t_pro_res_relation")
			->where("app_id", '=', $app_id)
			->where("product_id", '=', $package_id)
			->where("resource_type", '=', $resource_type)
			->where("resource_id", '=', $resource_id)
			->first();

		return $relation_info;
	}

	//在关系表t_pro_res_relation生成一条关系记录

	private function updateProResRelationState ($package_id, $resource_id, $resource_type)
	{
		$app_id = AppUtils::getAppID();

		$params['relation_state'] = StringConstants::RELATION_NORMAL;
		$params['updated_at']     = Utils::getTime();
		$update                   = \DB::table("db_ex_business.t_pro_res_relation")
			->where("app_id", '=', $app_id)
			->where("product_id", '=', $package_id)
			->where("resource_type", '=', $resource_type)
			->where("resource_id", '=', $resource_id)
			->update($params);

		return $update;
	}

	//更新关系记录为正常

	private function updatePackageResourceCount ($package_id)
	{
		$app_id = AppUtils::getAppID();

		$update_result = \DB::update("UPDATE db_ex_business.t_pay_products SET resource_count=resource_count+1 WHERE id='$package_id' and app_id = '$app_id'");

		return $update_result;

	}

	//查询关系记录

	private function result ($data)
	{
		if (Utils::isEmptyString($data)) {
			return response()->json(Utils::pack("", StringConstants::Code_Failed, StringConstants::Msg_Failed));
		} else {
			return response()->json(Utils::pack($data));
		}
	}

	//获取专栏信息

	/**
	 * 保存编辑资源;
	 * 参数:
	 * 1-params(基本信息+上架信息);
	 * 2-resource_type(1-图文,2-音频,3-视频,4-直播);
	 * 3-resource_params(当资源为视频、直播时,有值)
	 * 4-roleParams(直播讲师列表)
	 * 5-upload_channel_type(编辑的渠道:1-单品;2-专栏;3-会员)
	 */
	public function updateResource ()
	{
		$params              = Input::get("params", '');
		$category_type       = Input::get("category_type", '');//分类信息
		$resource_type       = Input::get("resource_type", '');
		$upload_channel_type = Input::get("upload_channel_type", '');
		$package_id          = Input::get("package_id", '');
		$externalParams      = Input::get("resource_params", '');
		$roleParams          = Input::get("roleParams");//直播讲师列表

		$ret = $this->saveResourceInfo($params, $category_type, $resource_type, $upload_channel_type, $package_id, $externalParams, $roleParams, StringConstants::RESOURCE_EDIT);

		if ($resource_type == StringConstants::SINGLE_GOODS_ALIVE) {
			if (starts_with($ret, 'l_')) {
				return ['code' => 0, 'alive_id' => $ret];
			} else {
				$msg = $ret;

				return response()->json(Utils::pack("0", StringConstants::Code_Failed, $msg));
			}
		}

		if ($ret == '0') {
			return $this->result('编辑资源成功!');
		} else {
			$msg = $ret;

			return response()->json(Utils::pack("0", StringConstants::Code_Failed, $msg));
		}
	}

	//根据资源id获取专栏id

	/**
	 * 选择已有的单品;
	 * 参数:
	 * 1-channel_type(1-单品,2-专栏,3-会员)
	 * 2-搜索内容-search_content;
	 * 3-resource_type(0-全部,1-图文,2-音频,3-视频,4-直播,6-专栏);
	 * 4-page(分页页码);
	 * 5-package_id(当channel_type=2、3时,有值)
	 */
	public function choiceResourceList ()
	{
		$channel_type   = Input::get("channel_type");
		$search_content = Input::get("search_content");
		$resource_type  = Input::get("resource_type");
		$page_index     = Input::get("page", 1);
		$package_id     = Input::get("package_id");//暂时不用
		if ($channel_type == StringConstants::CHOICE_CHANNEL_SINGLE) {//单品-选择已有的单品列表
			//处理步骤
			//取出payment_type=3的所有单品
			$resource_data = $this->getResourceListByState('', '-1', $search_content, $resource_type, $page_index, StringConstants::CHOICE_CHANNEL_SINGLE);

		} else if ($channel_type == StringConstants::CHOICE_CHANNEL_PACKAGE || $channel_type == StringConstants::CHOICE_CHANNEL_MEMBER) {//专栏-选择已有的单品列表  或者 会员

			if ($resource_type == StringConstants::SINGLE_GOODS_PACKAGE) {

				//在资源关系表t_pro_res_relation中查询非该会员id所有的专栏数据列表
				$resource_data = $this->getPackageList($package_id, $resource_type, $search_content, $page_index);
			} else {
				//处理步骤
				//取出payment_type=2且resource_id not in 关系表中;
				$resource_data = $this->getResourceListByState($package_id, '-1', $search_content, $resource_type, $page_index, StringConstants::CHOICE_CHANNEL_PACKAGE);
			}
		}

		return $this->result($resource_data);
	}

	private function getPackageList ($package_id, $resource_type, $search_content, $page_index)
	{

		$app_id    = AppUtils::getAppID();
		$whereRaw  = " 1=1 ";
		$page_size = 15;

		if (!Utils::isEmptyString($search_content)) {
			$whereRaw .= " and name like '" . "%" . $search_content . "%'";
		}

		$is_try                    = -1;
		$relation_resource_id_list = $this->getResourceIdList($package_id, $is_try);
		$whereRaw                  .= " and id not in ( " . implode(',', $relation_resource_id_list) . ")";

		$startRow = ($page_index - 1) * $page_size;

		$sqlNo = \DB::table("db_ex_business.t_pay_products")
			->where('app_id', '=', $app_id)
			->where('state', '!=', 2)
			->where('is_member', '=', 0)
			->whereRaw($whereRaw);

		$selectCustomResultTotal = $sqlNo->count();
		$total_pages             = ceil($selectCustomResultTotal / $page_size);

		$resource_list = $sqlNo->orderBy('created_at', 'desc')
			->skip($startRow)->take($page_size)
			->get();

		$page_offset = [
			'total_pages'  => $total_pages,
			'total_count'  => $selectCustomResultTotal,
			'current_page' => $page_index,
			'page_size'    => 15,
		];

		$page_offset = json_encode($page_offset);

		$data['resource_list'] = $resource_list;
		$data['page_offset']   = $page_offset;

		return $data;
	}

	/**
	 * 提交选择的已有单品;
	 * 参数:
	 * 1-channel_type(1-单品,2-专栏,3-会员);
	 * 3-resource_list(选中的资源id集合);
	 * 4-package_id(当channel_type=2、3时,有值)
	 * 5-piece_price(但channel_type=1时有值)
	 */
	public function submitChoiceResource ()
	{

		$channel_type = Input::get("channel_type");
		//        $resource_type = Input::get("resource_type");
		$resource_list = Input::get("resource_list");
		$package_id    = Input::get("package_id");
		$piece_price   = Input::get("piece_price", 0);
		$submit_type   = Input::get("submit_type", 0);
		$payment_type  = Input::get("payment_type", 2);
		//        exit;

		if ($channel_type < StringConstants::CHOICE_CHANNEL_SINGLE || $channel_type > StringConstants::CHOICE_CHANNEL_MEMBER) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "渠道来源有误!"));
		}

		if ($submit_type < StringConstants::GOODS_SINGLE_SALE || $submit_type > StringConstants::GOODS_SINGLE_UNSALE) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "设置单卖来源有误!"));
		}

		if ($channel_type == StringConstants::CHOICE_CHANNEL_SINGLE && $submit_type == StringConstants::GOODS_SINGLE_SALE && $payment_type == StringConstants::PAYMENT_TYPE_SINGLE) {
			if ($piece_price == 0 || $piece_price == 0.0 || $piece_price == 0.00) {
				return response()->json(Utils::pack("0", StringConstants::Code_Failed, "定价不能为零!"));
			}
		}

		//处理步骤
		//若channel_type为单品,则直接将resource_id_list中的资源设置单价并将payment_type=2
		//若channel_type为专栏,则在关系表t_pro_res_relation中添加一条记录(或是将原先被删除的置位为正常)

		if ($channel_type == StringConstants::CHOICE_CHANNEL_SINGLE) {//单品
			foreach ($resource_list as $key => $resource) {
				//直接将resource_id_list中的资源设置单价并将payment_type=2
				$resource_type         = $resource['resource_type'];
				$resource_id           = $resource['resource_id'];
				$params['piece_price'] = $piece_price;
				if ($submit_type == StringConstants::GOODS_SINGLE_SALE) {//设为单卖
					$params['payment_type'] = $payment_type;
				} else {//取消单卖
					$params['payment_type'] = 3;
				}
				$params['updated_at'] = Utils::getTime();
				switch ($resource_type) {
					case StringConstants::SINGLE_GOODS_ARTICLE://图文
						$table_name = "db_ex_business.t_image_text";
						break;
					case StringConstants::SINGLE_GOODS_AUDIO://音频
						$table_name = "db_ex_business.t_audio";
						break;
					case StringConstants::SINGLE_GOODS_VIDEO://视频
						$table_name = "db_ex_business.t_video";
						break;
					case StringConstants::SINGLE_GOODS_ALIVE://直播
						$table_name = "db_ex_business.t_alive";
						break;
				}
				$update = \DB::table($table_name)
					->where("app_id", '=', AppUtils::getAppID())
					->where("id", '=', $resource_id)
					->update($params);
				if ($update) {
				} else {
					return response()->json(Utils::pack("0", StringConstants::Code_Failed, "更新失败!"));
				}
			}

			return $this->result("更新成功!");

		} else if ($channel_type <= StringConstants::CHOICE_CHANNEL_MEMBER) { // 专栏或专栏
			//在关系表t_pro_res_relation中添加一条记录(或是将原先被删除的置位为正常)
			foreach ($resource_list as $key => $resource) {
				//专栏数resource_count+1
				$ret             = $this->updatePackageResourceCount($package_id);
				$resource_type   = $resource['resource_type'];
				$resource_id     = $resource['resource_id'];
				$create_relation = $this->createProResRelation($package_id, $resource_id, $resource_type);
				if ($create_relation) {
				} else {
					return response()->json(Utils::pack("0", StringConstants::Code_Failed, "新增关系记录失败!"));
				}
			}

			return $this->result("新增关系记录成功");

		}
	}

	//用户服务号所属行业设置（教育/培训） 生成模板id

	/**
	 * 获取专栏、会员列表;
	 * 参数:1-search_content
	 */
	public function getPackageMemberList ()
	{
		$search_content = Input::get("search_content", '');

		$app_id   = AppUtils::getAppID();
		$whereRaw = " 1=1 ";

		if (!Utils::isEmptyString($search_content)) {
			$whereRaw .= " and name like '" . "%" . $search_content . "%'";
		}

		$packageListInfo = \DB::table('t_pay_products')
			->where('app_id', '=', $app_id)
			->where('state', '!=', '2')
			->whereRaw($whereRaw)
			->orderby('order_weight', 'desc')
			->orderby('created_at', 'desc')
			->paginate(10);

		return $this->result($packageListInfo);
	}

	public function isGetIndustry ()
	{
		$app_id = AppUtils::getAppID();

		$result = MessagePush::getIndustry($app_id);

		return $result;
	}

	public function makePageUrl ()
	{

		$app_id = AppUtils::getAppID();
		$r_id   = Utils::getUniId('i_');

		// 图文数据
		$image2                       = [];
		$image2['app_id']             = $app_id; // 应用id
		$image2['id']                 = $r_id;
		$image2['title']              = "验证支付信息专用商品，购买后请删除";
		$image2['img_url']            = "http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/4e1b9e6b2a4b45a4ec57c1d6e5c306e7.jpg";
		$image2['img_url_compressed'] = "http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/compress/4e1b9e6b2a4b45a4ec57c1d6e5c306e7.jpg";
		$image2['content']            = <<<'AAA'
[{"type":0,"value":"小鹅通是什么？\n\n\n小鹅通是专注知识服务与社群运营的聚合型工具，为知识分享者们提供自己的知识服务平台。\n\n小鹅通由前端展示页面与后台管理页面组成，可以理解为嵌入微信等APP的知识小店。\n\n搭建付费图文\n\n作为入门，我们来搭建一篇付费图文内容作为小店的第一款商品。\n\nSTEP 1. 登陆管理台admin.xiaoe-tech.com\/login\n\n点击左侧内容列表-图文-新增图文，跳转至内容创建页面；\n\n"},{"type":1,"value":"http:\/\/wechatappdev-10011692.file.myqcloud.com\/appTCVlUyvG2205\/image\/ueditor\/62231000_1490102191.png"},{"type":0,"value":"\n\nSTEP 2. 为您的付费图文添加名称，选择收费形式。\n\n        若选择专栏，则需将图文移动至所属专栏，方便做系列化产品的输出。\n\n        若选择单卖，则表明该单品不隶属于任何系列，需要为其单独定价。\n   \n        也可选择免费作为试阅。\n\n"},{"type":1,"value":"http:\/\/wechatappdev-10011692.file.myqcloud.com\/appTCVlUyvG2205\/image\/ueditor\/24271500_1490102350.png"},{"type":0,"value":"\n\nSTEP 3. 完善封面信息、详细内容等。\n\n"},{"type":1,"value":"http:\/\/wechatappdev-10011692.file.myqcloud.com\/appTCVlUyvG2205\/image\/ueditor\/76407600_1490102436.png"},{"type":0,"value":"\n\nSTEP 4. 调整上架时间，若需立即售卖，请选择早于目前自然日的时间段。\n\n"},{"type":1,"value":"http:\/\/wechatappdev-10011692.file.myqcloud.com\/appTCVlUyvG2205\/image\/ueditor\/58726100_1490102528.png"},{"type":0,"value":"\n\nSTEP 5. 点击默认。恭喜！您已经拥有自己的第一款付费产品了，现在请移步前端展示页面欣赏预览。\n\n"}]
AAA;
		$image2['org_content']        = <<<'BBB'
<p>小鹅通是什么？</p><p><br/></p><p><br/></p><p>小鹅通是专注知识服务与社群运营的聚合型工具，为知识分享者们提供自己的知识服务平台。</p><p><br/></p><p>小鹅通由前端展示页面与后台管理页面组成，可以理解为嵌入微信等APP的知识小店。</p><p><br/></p><p><strong>搭建付费图文</strong></p><p><br/></p><p>作为入门，我们来搭建一篇付费图文内容作为小店的第一款商品。</p><p><br/></p><p>STEP 1. 登陆管理台admin.xiaoe-tech.com/login</p><p><br/></p><p>点击左侧内容列表-图文-新增图文，跳转至内容创建页面；</p><p><br/></p><p><img src="http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/ueditor/62231000_1490102191.png" title=".png" alt="1.png"/></p><p><br/></p><p>STEP 2. 为您的付费图文添加名称，选择收费形式。</p><p><br/></p><p>&nbsp; &nbsp; &nbsp; &nbsp; 若选择专栏，则需将图文移动至所属专栏，方便做系列化产品的输出。</p><p><br/></p><p>&nbsp; &nbsp; &nbsp; &nbsp; 若选择单卖，则表明该单品不隶属于任何系列，需要为其单独定价。</p><p>&nbsp; &nbsp;</p><p>&nbsp; &nbsp; &nbsp; &nbsp; 也可选择免费作为试阅。</p><p><br/></p><p><img src="http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/ueditor/24271500_1490102350.png" title=".png" alt="2.png"/></p><p><br/></p><p>STEP 3. 完善封面信息、详细内容等。</p><p><br/></p><p><img src="http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/ueditor/76407600_1490102436.png" title=".png" alt="3.png"/></p><p><br/></p><p>STEP 4. 调整上架时间，若需立即售卖，请选择早于目前自然日的时间段。</p><p><br/></p><p><img src="http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/ueditor/58726100_1490102528.png" title=".png" alt="4.png"/></p><p>&nbsp;</p><p>STEP 5. 点击默认。恭喜！您已经拥有自己的第一款付费产品了，现在请移步前端展示页面欣赏预览。</p><p><br/></p>
BBB;
		$image2['img_size_total']     = 0;
		$image2['display_state']      = 1;
		$image2['payment_type']       = 2;
		$image2['piece_price']        = 1;
		$image2['created_at']         = Utils::getTime();
		$image2['updated_at']         = Utils::getTime();
		$image2['start_at']           = Utils::getTime();

		$res = \DB::connection('mysql')->table('t_image_text')->insert($image2);
		if (!$res)
			return response()->json(['code' => 1, 'msg' => '创建资源失败']);

		//资源访问链接
		$pageUrl = '';

		$app_info = AppUtils::getAppConfInfo(AppUtils::getAppID());
		//生成资源访问链接
		if ($app_info) {
			if (!empty($app_info->wx_app_id) || $app_info->use_collection == 1) {

				//                if($app_info->use_collection == 0){   // 0  是企业模式
				$pageUrl = AppUtils::getUrlHeader($app_id) . $app_info->wx_app_id . '.' . env('DOMAIN_NAME');
				//                }else{
				//                    $pageUrl = AppUtils::getUrlHeader($app_id).'h5.inside.xiaoeknow.com';
				//                }
				//查询该资源关联的专栏或会员
				$product_id = $this->getProductIdByResId($r_id);
				$pageUrl    = $pageUrl . Utils::getContentUrl(2, 1, $r_id, $product_id, '');
			}
		}
		//        echo $pageUrl;
		if ($pageUrl) return response()->json(['code' => 0, 'r_id' => $r_id, 'pageurl' => $pageUrl]);

	}

}








