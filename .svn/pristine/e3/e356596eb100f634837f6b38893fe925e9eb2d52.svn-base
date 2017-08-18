<?php
//tests

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/26 0026
 * Time: 下午 2:47
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\Utils;
use App\Http\Controllers\View;
use DB;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Input;

class ChannelAdminController extends Controller
{
	private $request;
	private $app_id;
	private $domain_name;

	public function __construct (Request $request)
	{
		$this->request     = $request;
		$this->app_id      = AppUtils::getAppID();
		$this->domain_name = "";
	}

	//渠道分发
	public function channelAdmin ()
	{

		$channel_attr  = Input::get('channel_attr', '');
		$resource_type = Input::get('resource_type', '');
		if ($resource_type == 'all' || $resource_type == '') {
			$resource_type_arr = [0, 1, 2, 3, 4];
			$channel_type_arr  = [0, 1];
		} else if ($resource_type == 8) {
			$channel_type      = 1;
			$channel_type_arr  = [1];
			$resource_type_arr = [0];
		} else {
			$channel_type      = 0;
			$channel_type_arr  = [0];
			$resource_type_arr = [$resource_type];
		}

		$search_content = Input::get('search_content', '');
		$orderView      = Input::get('order_view', '');
		$orderBy        = 'created_at';//默认改为创建时间
		$orderSort      = 'desc';
		switch ($orderView) {
			case 1:
				$orderBy = 'created_at';
				break;
			case 2:
				$orderBy = 'view_count';
				break;
			case 3:
				$orderBy = 'open_count';
				break;
			case 10:
				$orderBy   = 'created_at';
				$orderSort = 'asc';
				break;
			case 20:
				$orderBy   = 'view_count';
				$orderSort = 'asc';
				break;
			case 30:
				$orderBy   = 'open_count';
				$orderSort = 'asc';
				break;
		}

		if (empty($search_content)) {
			$channels = \DB::table('t_channels')
				->select(\DB::raw('*,t_channels.id as channelId'))
				->where('app_id', '=', $this->app_id)
				->whereIn('channel_type', $channel_type_arr)
				->whereIn('resource_type', $resource_type_arr)
				//                  ->where('generate_type','=',0)
				->groupby('t_channels.id')
				->orderby($orderBy, $orderSort)
				->orderby('id', 'desc')
				->paginate(10);
			if ($resource_type == 'all' || $resource_type == '')
				$sum_count = \DB::select("select sum(view_count) as sumView, sum(open_count) as sumOpen from t_channels where  app_id = '$this->app_id' ")[0];
			else if ($resource_type == 8)
				$sum_count = \DB::select("select sum(view_count) as sumView, sum(open_count) as sumOpen from t_channels where  channel_type = '$channel_type' and app_id = '$this->app_id'  ")[0];
			else
				$sum_count = \DB::select("select sum(view_count) as sumView, sum(open_count) as sumOpen from t_channels where  channel_type = '$channel_type' and app_id = '$this->app_id' and resource_type = '$resource_type' ")[0];

		} else {
			$channels = \DB::table('t_channels')
				->select(\DB::raw('*,t_channels.id as channelId'))
				->where('app_id', '=', $this->app_id)
				->whereIn('channel_type', $channel_type_arr)
				->whereIn('resource_type', $resource_type_arr)
				//                  ->where('generate_type','=',0)
				->where($channel_attr, 'like', '%' . $search_content . '%')
				->groupby('t_channels.id')
				->orderby($orderBy, $orderSort)
				->orderby('id', 'desc')
				->paginate(10);
			if ($resource_type == 'all' || $resource_type == '')
				$sum_count = \DB::select("select sum(view_count) as sumView, sum(open_count) as sumOpen from t_channels where  app_id = '$this->app_id' and $channel_attr like '%$search_content%' ")[0];
			else if ($resource_type == 8)
				$sum_count = \DB::select("select sum(view_count) as sumView, sum(open_count) as sumOpen from t_channels where  channel_type = '$channel_type' and  app_id = '$this->app_id' and $channel_attr like '%$search_content%' ")[0];
			else
				$sum_count = \DB::select("select sum(view_count) as sumView, sum(open_count) as sumOpen from t_channels where  channel_type = '$channel_type' and app_id = '$this->app_id' and resource_type = '$resource_type' and $channel_attr like '%$search_content%' ")[0];
		}
		//        //获取到渠道的id
		//        $cId = array();
		//        foreach ($channels as $value){
		//            $cId[] = $value->channelId;
		//        }
		//        //通过id去获取对应渠道开通的数量
		//        $channelCount = array();
		//        foreach ($cId as $key=>$cid){
		//            $temp = \DB::select("select count(*) as count from t_purchase where channel_id = '$cid'");
		//            //dump($temp);
		//            $channelCount[$key] = $temp[0]->count;
		//        }

		$sumView = '0';
		$sumOpen = '0';
		if ($sum_count) {
			$sumView = $sum_count->sumView;
			$sumOpen = $sum_count->sumOpen;
		}
		//查询所有的包
		$package_list = $this->getAllPackages();

		$image_text_list = $this->getAllResource(1);
		$audio_list      = $this->getAllResource(2);
		$video_list      = $this->getAllResource(3);
		$alive_list      = $this->getAllResource(4);

		//查询渠道来源
		if (count($channels) > 0) {
			foreach ($channels as $key => $channel) {

				if ($channel->generate_type == 0) {
					$channel->generate_type = "自主创建";
				} else if ($channel->generate_type == 1) {

					//去分销申请表中根据渠道id去查询申请人信息
					$applier = \DB::table('t_sales')
						->where('channel_id', '=', $channel->id)
						->first();
					if ($applier) {
						$channel->generate_type = $applier->applier;
					}
				}
				$channels[ $key ] = $channel;
			}
		}

		return view('admin.channelAdmin', compact('channels', 'resource_type', 'channelCount', 'package_list', 'image_text_list', 'audio_list', 'video_list', 'alive_list', 'channel_attr', 'search_content', 'orderView', 'sumView', 'sumOpen'));

	}

	//查询所有的专栏
	public function getAllPackages ()
	{
		//查询所有的包
		$package_list = \DB::table('t_pay_products')
			->where('app_id', '=', AppUtils::getAppID())
			->orderby('created_at', 'desc')
			->get();

		return $package_list;
	}

	//查询所有的资源  type=1,2,3,4 对应图文、音频、视频 、直播
	public function getAllResource ($type)
	{
		// 吴晓波频道特殊逻辑，所有付费音频均可创建渠道
		$wxb_app_id = 'appe0MEs6qX8480';
		$app_id     = AppUtils::getAppID();
		switch ($type) {
			case 1:
				// 更改自kevin
				$image_text_list = \DB::table('t_image_text')
					->where('app_id', $app_id)
					->where('display_state', '0')
					->where(function($query) use ($wxb_app_id, $app_id) {
						if ($app_id == $wxb_app_id) return $query->where('payment_type', '>', 1);

						return $query->where('payment_type', 2);
					})
					->orderby('created_at', 'desc')
					->get();

				foreach ($image_text_list as $v) {
					//查找表t_pro_res_relation中relation_state为0对应的product_id
					$res_product_id = \DB::table('t_pro_res_relation')
						->where(['app_id' => AppUtils::getAppID(), 'resource_type' => '1', 'resource_id' => $v->id])
						->select('product_id')
						->where('relation_state', '=', '0')
						->orderBy('created_at', 'desc')
						->first();
					if ($res_product_id) {
						$v->product_id = $res_product_id->product_id;
					} else {
						$v->product_id = '';
					}
				}

				return $image_text_list;
				break;
			case 2:
				$audio_list = \DB::table('t_audio')
					->where('app_id', $app_id)
					->where('audio_state', '0')
					->where(function($query) use ($wxb_app_id, $app_id) {
						if ($app_id == $wxb_app_id) return $query->where('payment_type', '>', 1);

						return $query->where('payment_type', 2);
					})
					->orderby('created_at', 'desc')
					->get();
				foreach ($audio_list as $v) {
					//查找表t_pro_res_relation中relation_state为0对应的product_id
					$res_product_id = \DB::table('t_pro_res_relation')
						->where(['app_id' => AppUtils::getAppID(), 'resource_type' => '2', 'resource_id' => $v->id])
						->select('product_id')
						->where('relation_state', '=', '0')
						->orderBy('created_at', 'desc')
						->first();
					if ($res_product_id) {
						$v->product_id = $res_product_id->product_id;
					} else {
						$v->product_id = '';
					}

				}

				return $audio_list;
				break;
			case 3:
				$video_list = \DB::table('t_video')
					->where('app_id', $app_id)
					->where('video_state', '0')
					->where(function($query) use ($wxb_app_id, $app_id) {
						if ($app_id == $wxb_app_id) return $query->where('payment_type', '>', 1);

						return $query->where('payment_type', 2);
					})
					->orderby('created_at', 'desc')
					->get();
				foreach ($video_list as $v) {
					//查找表t_pro_res_relation中relation_state为0对应的product_id
					$res_product_id = \DB::table('t_pro_res_relation')
						->where(['app_id' => AppUtils::getAppID(), 'resource_type' => '3', 'resource_id' => $v->id])
						->select('product_id')
						->where('relation_state', '=', '0')
						->orderBy('created_at', 'desc')
						->first();
					if ($res_product_id) {
						$v->product_id = $res_product_id->product_id;
					} else {
						$v->product_id = '';
					}

				}

				return $video_list;
				break;
			case 4:
				$alive_list = \DB::table('t_alive')
					->where('app_id', $app_id)
					->where('state', '0')
					->where(function($query) use ($wxb_app_id, $app_id) {
						if ($app_id == $wxb_app_id) return $query->where('payment_type', '>', 1);

						return $query->where('payment_type', 2);
					})
					->orderby('created_at', 'desc')
					->get();
				foreach ($alive_list as $v) {
					//查找表t_pro_res_relation中relation_state为0对应的product_id
					$res_product_id = \DB::table('t_pro_res_relation')
						->where(['app_id' => AppUtils::getAppID(), 'resource_type' => '4', 'resource_id' => $v->id])
						->select('product_id')
						->where('relation_state', '=', '0')
						->orderBy('created_at', 'desc')
						->first();
					if ($res_product_id) {
						$v->product_id = $res_product_id->product_id;
					} else {
						$v->product_id = '';
					}

				}

				return $alive_list;
				break;
		}
	}

	public function submitChannel ()
	{
		$wx_id_result   = \DB::connection("mysql_config")->select("select wx_app_id,use_collection from t_app_conf where app_id = '$this->app_id' and wx_app_type = 1");
		$wx_id          = $wx_id_result[0]->wx_app_id;
		$use_collection = $wx_id_result[0]->use_collection;

		if (Utils::isEmptyString($wx_id) && $use_collection != 1) {
			return response()->json(['code' => 2, 'msg' => '添加失败，请先完成公众号接入！']);
		}
		$channel_name   = $_POST['channel_name'];
		$payment_type   = $_POST['payment_type'];
		$resource_type  = $_POST['resource_type'];
		$resource_id    = $_POST['resource_id'];
		$product_id     = $_POST['package_id'];
		$channel_type   = $_POST['channel_type'];
		$resource_title = $_POST['resource_title'];

		$page_type = $payment_type;

		if ($payment_type == 2) {
			if ($product_id != '') {
				$payment_type = 3;//付费产品包
			}
		}

		//获取当前时间
		$current_time = Utils::getTime();

		$sql_array = ['app_id'        => $this->app_id, 'name' => $channel_name, 'channel_type' => $channel_type, 'payment_type' => $payment_type,
					  'resource_type' => $resource_type, 'resource_id' => $resource_id, 'resource_title' => $resource_title, 'product_id' => $product_id,
					  'created_at'    => $current_time, 'updated_at' => $current_time];

		try {

			DB::beginTransaction();
			//保存数据库
			$channel_id = \DB::table('t_channels')->insertGetId(
				$sql_array
			);

			//            $MaxId = \DB::select("select max(id) as id from t_channels where id = '$channel_id'");

			if ($channel_type == 1) { //渠道类型为首页
				if ($use_collection == 1)//个人版 
				{
					$channel_url = AppUtils::getUrlHeader($this->app_id) . env("DOMAIN_DUAN_NAME") . "/" . $this->app_id . '/homepage/' . Utils::contentUrlHome($channel_id);
				} else {
					$channel_url = AppUtils::getUrlHeader($this->app_id) . $wx_id . "." . env("DOMAIN_NAME") . '/homepage/' . Utils::contentUrlHome($channel_id);
				}
			} else {
				if ($use_collection == 1)//个人版
				{
					$channel_url = AppUtils::getUrlHeader($this->app_id) . env("DOMAIN_DUAN_NAME") . Utils::contentUrl($channel_id, $page_type, $resource_type, $resource_id, $product_id, $this->app_id);
				} else {
					$channel_url = AppUtils::getUrlHeader($this->app_id) . $wx_id . "." . env("DOMAIN_NAME") . Utils::contentUrl($channel_id, $page_type, $resource_type, $resource_id, $product_id, $this->app_id);
				}
			}

			$updateResult = \DB::table('t_channels')
				->where('app_id', $this->app_id)
				->where('id', $channel_id)
				->update(['acc_url' => $channel_url]);
			if ($updateResult && !empty($channel_url)) {
				DB::commit();

				return response()->json(['code' => 0, 'msg' => '添加成功']);
			} else {
				DB::rollback();

				return response()->json(['code' => 2, 'msg' => '添加失败，请联系技术小哥！']);
			}

		} catch (\Exception $e) {
			$message = $e->getMessage();
			if (strstr($message, "Duplicate entry")) {
				return response()->json(['code' => 1, 'msg' => '该渠道名已存在，请修改渠道名后再提交！']);
			} else {
				return response()->json(['code' => 2, 'msg' => '出问题啦，请联系技术小哥！']);
			}
		}

	}

	//渠道开通详情页
	public function openDetail (Request $request)
	{
		$id    = Input::get("id", 0);
		$title = Input::get("title", "");

		// 时间搜索
		$start_time = $request->input('start_time', '2016');
		$end_time   = $request->input('end_time', date('Y-m-d H:i:s', time()));
		if (!$start_time) $start_time = '2016';
		if (!$end_time) $end_time = date('Y-m-d H:i:s', time());
		$real_end_time = date('Y-m-d 23:59:59', strtotime($end_time));
		if ($id != 0) {

			//查询渠道信息
			$channel_result = \DB::table('t_channels')->where('id', '=', $id)->first();

			if ($channel_result) {
				$results = DB::table('t_orders')
					->select('t_orders.app_id', 't_orders.user_id', 't_orders.purchase_name', 't_orders.payment_type', 't_orders.resource_type',
						't_orders.product_id', 't_orders.resource_id', 't_orders.order_id', 't_orders.created_at', 't_orders.price', 't_orders.share_user_id')
					->where('t_orders.app_id', '=', $this->app_id)
					->where('t_orders.channel_id', '=', $id)
					->where('t_orders.order_state', '=', '1')
					->where('created_at', '>', $start_time)
					->where('created_at', '<', $real_end_time)
					->orderby('t_orders.created_at', 'desc')
					->paginate(10);

				$user_info = [];
				foreach ($results as $key => $value) {
					$temp = DB::table('t_users')->select('wx_nickname', 'wx_avatar')
						->where('app_id', '=', $this->app_id)
						->where('user_id', '=', $value->user_id)->first();
					if (empty($temp)) {
						$temp              = new \stdClass();
						$temp->wx_nickname = '无';
						$temp->wx_avatar   = '';
					}
					$user_info[] = $temp;
				}
			}
		}

		if (!empty($results)) {
			return view('admin.openDetail', compact('results', 'user_info', 'title', 'id', 'start_time', 'end_time'));
		} else {
			return view('admin.openDetail');
		}

	}

	// 官网渠道详情页
	public function homeChannel ()
	{
		$id    = $_GET['id'];
		$title = $_GET['title'];

		$page = Input::get('page', 0);
		//需要付费的音频资源
		$results_audio = \DB::table('t_audio')->select(\DB::raw('app_id,id,title,payment_type,1 as type'))
			->where('payment_type', '=', 2)
			->where('app_id', '=', $this->app_id);
		//需要付费的视频资源
		$results_video = \DB::table('t_video')->select(\DB::raw('app_id,id,title,payment_type,2 as type'))
			->where('payment_type', '=', 2)
			->where('app_id', '=', $this->app_id);
		//需要付费的图文资源
		$results_image_text = \DB::table('t_image_text')->select(\DB::raw('app_id,id,title,payment_type,3 as type'))
			->where('payment_type', '=', 2)
			->where('app_id', '=', $this->app_id);
		//需要付费的所有资源
		$results = \DB::table('t_pay_products')->select(\DB::raw('app_id,id,name as title,3 as payment_type,4 as type'))
			->where('app_id', '=', $this->app_id)
			->union($results_audio)->union($results_video)->union($results_image_text)->get();
		$idArray = [];
		foreach ($results as $value) {
			if ($value->payment_type == 2) {//单笔付费的
				$countResults = \DB::table('t_purchase')->select(\DB::raw('count(*) as count'))
					->where('app_id', '=', $this->app_id)
					->where('channel_id', '=', $id)
					->where('resource_id', '=', $value->id)
					->get();
			} else { //专栏付费的
				$countResults = \DB::table('t_purchase')->select(\DB::raw('count(*) as count'))
					->where('app_id', '=', $this->app_id)
					->where('channel_id', '=', $id)
					->where('product_id', '=', $value->id)
					->get();
			}
			$idArray[] = $countResults[0]->count;
		}

		if (!empty($results)) {
			return view('admin.HomeOpenDetail', compact('results', 'title', 'idArray'));
		} else {
			return view('admin.HomeOpenDetail');
		}

		//        if(!empty($results)){
		//            if($id){
		//                return view('admin.openDetail',compact('results','user_info','search_content','order_attr','id_title','id_name'));
		//            }else{
		//                return view('admin.payAdmin',compact('results','user_info','search_content','order_attr','id_title','id_name'));
		//            }
		//        }else{
		//            if($id){
		//                return view('admin.openDetail');
		//            }else{
		//                return view('admin.payAdmin');
		//            }
		//        }
	}

	//试听渠道
	public function tryListener (Request $request)
	{
		$sql = "
                   SELECT
                     v1.*,ifnull(v2.receive_count, 0) AS receive_count
                   FROM (
                          SELECT *
                          FROM t_experience_channel
                          WHERE app_id = ?
                        ) v1 LEFT JOIN (
                         SELECT
                             app_id,count(id) AS receive_count,channel
                           FROM t_experience_records
                           WHERE app_id = ?
                           GROUP BY channel
                       ) v2 ON v1.app_id = v2.app_id AND v1.channel = v2.channel
        ";

		$ListInfo = DB::connection('mysql')->select($sql, [$this->app_id, $this->app_id]);

		//        dd($ListInfo);
		if ($ListInfo) {
			// 手动分页
			$page    = $request->input('page', '');
			$total   = count($ListInfo);
			$perPage = 10;
			// 判断当前页数
			if ($page) {
				$current_page = $page;
				$current_page = $current_page <= 0 ? 1 : $current_page;
			} else {
				$current_page = 1;
			}
			//手动切割结果集
			$item = array_slice($ListInfo, ($current_page - 1) * $perPage, $perPage);
			//        echo '<pre>';
			$paginator = new LengthAwarePaginator($item, $total, $perPage, $current_page, [
				'path'     => Paginator::resolveCurrentPath(), //生成路径
				'pageName' => 'page',
			]);
		} else {
			$paginator = [];
		}
		// 统计开通量
		foreach ($paginator as $v) {
			$sql        = "
                SELECT ifnull(sum(v2.count),0) as count
                FROM (
                       SELECT *
                       FROM t_experience_records
                       WHERE app_id = ? AND channel = ?
                     ) v1 INNER JOIN t_orders v2 ON v1.app_id = v2.app_id AND v1.user_id = v2.user_id
                WHERE v2.app_id = ? and v2.order_state = 1 AND v2.payment_type IN (3,6) AND v2.product_id = ?
            ";
			$open_count = DB::connection('mysql')->select($sql, [$v->app_id, $v->channel, $v->app_id, "p_5857d53b3342a_Tm6TjjTD"]);
			$open_count = $open_count[0]->count;

			$v->open_count = $open_count;
		}

		return view('admin.tryListenerChannel', [
			'data' => $paginator,
		]);
	}

	public function AddListenChannel ($name)
	{
		if (!$name) return response()->json(['code' => -1, 'msg' => '无效的渠道名称', 'data' => []]);

		// 判断新试听渠道是否已存在
		$info = DB::connection('mysql')->table('t_experience_channel')->where('app_id', $this->app_id)->where('channel_name', $name)->first();
		if ($info) return response()->json(['code' => -1, 'msg' => '渠道已存在', 'data' => []]);

		// 拼出渠道链接头部
		$app_info = DB::connection('mysql_config')->table('t_app_conf')->where('app_id', $this->app_id)->where('wx_app_type', 1)->first();
		if ($app_info->use_collection == 1)//个人版
		{
			$channel_url = AppUtils::getUrlHeader($this->app_id) . env("DOMAIN_DUAN_NAME") . "/{$this->app_id}";
		} else {
			$channel_url = AppUtils::getUrlHeader($this->app_id) . $app_info->wx_app_id . "." . env("DOMAIN_NAME");
		}

		// 新建试听渠道
		$data                    = [];
		$data['app_id']          = $this->app_id;
		$data['channel_name']    = $name;
		$data['channel']         = Utils::generateRandomCode(6, 'ALL');
		$data['experience_name'] = "seven-day-experience";
		$data['purchase_name']   = "七天体验";
		$data['url']             = $channel_url . "/experience/" . "{$data['experience_name']}/" . $data['channel'];
		$data['created_at']      = date('Y-m-d H:i:s');

		$insert = DB::connection('mysql')->table('t_experience_channel')->insert($data);

		if ($insert) {
			return response()->json(['code' => 0, 'msg' => 'success', 'data' => []]);
		} else {
			return response()->json(['code' => -1, 'msg' => 'fail!', 'data' => []]);
		}

	}

}