<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CouponController extends Controller
{
	private $app_id;

	public function __construct ()
	{
		$this->app_id = AppUtils::getAppID();
	}

	// 优惠券列表页
	public function index (Request $request)
	{
		//当前日期
		$nowDateTime = date("Y-m-d H:i:s");

		$app_info = AppUtils::getAppConfInfo($this->app_id);  // app 信息

		//查询条件的初始化
		$select_state = $request->input('select_state', '');//状态选择
		$start_time   = $request->input('start_time', '');//搜索起始日期
		$end_time     = $request->input('end_time', '');//搜索结束日期
		$coupon_name  = $request->input('coupon_name', '');

		$where = "app_id = '$this->app_id' ";//为了research查询条件
		//设定回显和分页参数
		$search_array = [];
		if ($select_state) $search_array['select_state'] = $select_state;//状态

		if ($start_time) {
			$search_array['start_time'] = $start_time;//有效起始日期
			$where                      .= " and '{$start_time}'<= valid_at ";
		}

		if ($end_time) {
			$search_array['end_time'] = $end_time;//有效结束日期
			$end_time                 = date('Y-m-d 23:59:59', strtotime($end_time));
			$where                    .= " and '{$end_time}'>=invalid_at ";
		}
		if ($coupon_name) {
			$search_array['coupon_name'] = $coupon_name;//优惠券名称
			$where                       .= " and title like '%$coupon_name%' ";
		}

		//根据t_coupon表获取不同的信息
		//状态
		if ($select_state) {
			if ($select_state == "1")//领取中
			{
				$where .= " and invalid_at>='{$nowDateTime}'and manual_stop_at is NULL and count-has_received>0 ";
			} else if ($select_state == "2")//已领完
			{
				$where .= " and invalid_at>='{$nowDateTime}'and manual_stop_at is NULL and count=has_received ";
			} else if ($select_state == "3")//已结束
			{
				$where .= " and (invalid_at<'{$nowDateTime}' or manual_stop_at is not NULL)";
			}
		}

		/**
		 * 得到优惠券名称、0-用户领取，1-商家发放、优惠价格、要求价格，0-所有商品
		 *优惠券有效时间、优惠券失效时间、限领张数、发行量。
		 * 缺少已使用、状态。
		 **/
		$resInfo = DB::connection('mysql')->table('t_coupon')
			->where('app_id', $this->app_id)
			->whereRaw($where)
			->where('is_delete', 0)
			->select('id', 'title', 'type', 'spread_type', 'price', 'bind_res_count', 'require_price', 'valid_at', 'invalid_at', 'manual_stop_at', 'receive_rule', 'count', 'has_received')
			->orderBy('created_at', 'desc')
			->paginate(10);

		//组装数据
		foreach ($resInfo as $item) {
			//添加url
			$item->url = '';
			//        "https://{wx_app_id}.h5.xiaoe-tech.com/coupon/get/{con_id}";   // 企业版优惠券地址
			//        "https://h5.inside.xiaoeknow.com/{app_id}/coupon/get/{con_id}";   // 个人版优惠券地址
			if ($app_info && $item->spread_type != 1) {
				if (!empty($app_info->wx_app_id) || $app_info->use_collection == 1) {
					if ($app_info->use_collection == 0) {
						$pageUrl = AppUtils::getUrlHeader($this->app_id) . $app_info->wx_app_id . '.' . env('DOMAIN_NAME');
					} else {
						$pageUrl = AppUtils::getUrlHeader($this->app_id) . env('DOMAIN_DUAN_NAME') . "/{$this->app_id}";
					}
					//查询该资源关联的专栏或会员
					$url       = "{$pageUrl}/coupon/get/{$item->id}";
					$item->url = $url;
				}
			}

			$count_is_used = DB::connection('mysql')->table('t_coupon_user')
				->where('cou_id', $item->id)
				->where('app_id', $this->app_id)
				->where('is_use', 1)
				->count('id');
			$item->is_use  = $count_is_used;

			//状态：1、领取中=发行量-领取量>0&&在有效期内；
			//      2、已领完=（发行量==已领取）&&（在有效日期内）
			//      3、a、过了有效期；b、动手结束
			if ($nowDateTime > $item->invalid_at || $item->manual_stop_at)
				$item->coupon_state = 3;
			else if ($item->count == $item->has_received)
				$item->coupon_state = 2;
			else
				$item->coupon_state = 1;
		}

		return view("admin.marketing.coupon.couponIndex", [
			'resInfo'      => $resInfo,    // 优惠券列表
			'search_array' => $search_array,    // 搜索内容回显
		]);
	}

	// 结束优惠券
	public function endCoupon ($id)
	{
		$res = DB::connection('mysql')->table('t_coupon')
			->select('manual_stop_at')
			->where('app_id', $this->app_id)
			->where('id', $id)
			->value('manual_stop_at');

		if (!Utils::isEmptyString($res))
			return response()->json(['code' => -2, 'msg' => 'error', 'data' => []]);//-2已经结束不能再结束
		else {
			$nowDateTime = date("Y-m-d H:i:s");
			$update      = DB::connection('mysql')->table('t_coupon')
				->where('app_id', $this->app_id)
				->where('id', $id)
				->update(['manual_stop_at' => $nowDateTime]);
			if ($update) {
				return response()->json(['code' => 0, 'msg' => '成功手动结束', 'data' => []]);
			} else {
				return response()->json(['code' => -1, 'msg' => '手动结束失败', 'data' => []]);
			}
		}
	}

	// 选择优惠券视图
	public function select ()
	{
		return view("admin.marketing.coupon.couponCreate");
	}

	//进入创建优惠券视图
	public function create (Request $request)
	{
		$type = $request->input('type', 0);

		$data = new \stdClass();
		if ($type == 0) {
			//提示（不再提示）查询
			$message = DB::connection('mysql_config')->table('t_message_reminder')
				->where('app_id', $this->app_id)
				->where('place', 2)
				->first();

			$message_info = [];
			if (!$message) {
				$message_info['app_id']  = $this->app_id;
				$message_info['content'] = "提示：保存成功后，只能修改发行量和商品范围，且发行量和商品范围只能增加，不能减少";
				$message_info['place']   = 2;
				$insert                  = DB::connection('mysql_config')->table('t_message_reminder')->insert($message_info);
				if ($insert) {
					$message          = new \stdClass();
					$message->content = $message_info['content'];
					$message->place   = 2;
					$message->status  = 0;
				}
			}

			$data->message   = $message;
			$data->type      = (int)$type;
			$data->page_type = 0;
		} else {
			//提示（不再提示）查询
			$message = DB::connection('mysql_config')->table('t_message_reminder')
				->where('app_id', $this->app_id)
				->where('place', 3)
				->first();

			$message_info = [];
			if (!$message) {
				$message_info['app_id']  = $this->app_id;
				$message_info['content'] = "提示：保存成功后，只能修改发行量，且发行量只能增加，不能减少";
				$message_info['place']   = 3;
				$insert                  = DB::connection('mysql_config')->table('t_message_reminder')->insert($message_info);
				if ($insert) {
					$message          = new \stdClass();
					$message->content = $message_info['content'];
					$message->place   = 3;
					$message->status  = 0;
				}
			}

			$data->message   = $message;
			$data->type      = (int)$type;
			$data->page_type = 0;
		}

		return view("admin.marketing.coupon.newGoods", [
			'data' => $data,
		]);
	}

	// 获取优惠券可用的产品包
	public function addProducts (Request $request)
	{
		$search_array  = [];
		$whereProduct  = "app_id = '$this->app_id' ";
		$where         = "app_id = '{$this->app_id}' ";
		$data['state'] = $request->input('state');
		$data['kw']    = $request->input('kw', '');
		if ($data['kw']) $search_array['kw'] = $data['kw'];
		if ($data['state'] != null) $search_array['state'] = $data['state'];
		if ($data['state'] == 0 && !Utils::isEmptyString($data['kw'])) $whereProduct .= " and title like '%{$data['kw']}%' ";
		if ($data['state'] == 1 || $data['state'] == 2) {
			if (!$data['kw']) $where .= " and name like '%{$data['kw']}%' ";
		}

		// 会员列表
		$memberList = DB::connection('mysql')->table('t_pay_products')
			->select(DB::raw('id, img_url, img_url_compressed, name, 6 as goods_type, created_at'))
			->whereRaw($where)
			->where('state', 0)
			->where('is_member', 1)
			->where('price', '>', 0)
			->orderBy('created_at', 'desc')
			->get();

		// 专栏列表
		$specialList = DB::connection('mysql')->table('t_pay_products')
			->select(DB::raw('id, img_url, img_url_compressed, name, 5 as goods_type, created_at'))
			->whereRaw($where)
			->where('state', 0)
			->where('is_member', 0)
			->where('price', '>', 0)
			->orderBy('created_at', 'desc')
			->get();

		$sql = "
        SELECT
            *
        FROM
            (
                        SELECT
                            id,img_url,img_url_compressed,created_at,title AS name,2 AS goods_type
                        FROM
                            t_audio
                        WHERE
                            $whereProduct
                        AND audio_state = 0 AND payment_type = 2 AND piece_price > 0
                UNION ALL
                    (
                        SELECT
                            id,img_url,img_url_compressed,created_at,title AS name,3 AS goods_type
                        FROM
                            t_video
                        WHERE
                            $whereProduct
                        AND video_state = 0 AND payment_type = 2 AND piece_price > 0
                    )
                UNION ALL
                    (
                        SELECT
                            id,img_url,img_url_compressed,created_at,title AS name,1 AS goods_type
                        FROM
                            t_image_text
                        WHERE
                            $whereProduct
                        AND display_state = 0 AND payment_type = 2 AND piece_price > 0
                    )
                UNION ALL
                    (
                        SELECT
                            id,img_url,img_url_compressed,created_at,title AS name,4 AS goods_type
                        FROM
                            t_alive
                        WHERE
                            $whereProduct
                        AND state = 0 AND payment_type = 2 AND piece_price > 0
                    )
            ) v1
        ORDER BY
            created_at DESC  
        ";

		$productList = DB::connection('mysql')->select($sql);
		$resInfo     = ['0' => $productList, '1' => $specialList, '2' => $memberList];

		return view("admin.marketing.coupon.newGoodsList", [
			'resInfo'      => $resInfo,
			'search_array' => $search_array,
		]);

	}

	//点击创建优惠券
	public function createCoupon (Request $request)
	{
		//验证
		$this->validate($request, [
			'title'      => 'required',
			'price'      => 'required|integer',
			'valid_at'   => 'required|date',
			'invalid_at' => 'required|date',
			'count'      => 'required',
		], [
			'required' => ':attribute 为必填项',
			'integer'  => ':attribute 必须为不小于0的整数',
			'date'     => ':attribute 必须为有效日期',
		], [
			'title'      => '优惠券名称',
			'price'      => '面额',
			'valid_at'   => '有效起始日期',
			'invalid_at' => '有效截止日期',
			'count'      => '发行量',
		]);

		//插入
		$data['app_id']        = AppUtils::getAppID();
		$data['id']            = Utils::getCouId();
		$data['title']         = $request->input('title', '');//优惠券名称
		$data['type']          = $request->input('type', 0);//优惠券类型 0-商品 1-店铺
		$data['price']         = $request->input('price', 0);//面额
		$data['require_price'] = $request->input('require_price', 0);
		$data['valid_at']      = $request->input('valid_at', '0000-00-00 00:00:00');//起始有效时间
		$data['invalid_at']    = $request->input('invalid_at', '0000-00-00 00:00:00');//终止有效时间
		$data['count']         = $request->input('count', 0);//发行量
		$data['receive_rule']  = $request->input('receive_rule', 1);//每人限领
		$data['spread_type']   = $request->input('spread_type', 0);//推广方式，0-用户，1-商家
		$data['created_at']    = date("Y-m-d H:i:s");//创建日期
		$data['is_show']       = $request->input('is_show', 0);//是否在商品详情页展示  默认0 不展示，1 展示

		//        if($data['price'] >= $data['require_price']) return response()->json(['code'  => -1, 'msg'   => '面额不能大于等于要求价格', 'data'  =>[]]);

		if ($data['type'] == 0) {
			$resource = $request->input('resource');
			if (!$resource) return response()->json(['code' => -1, 'msg' => '请添加商品范围', 'data' => []]);

			$data['bind_res_count'] = count($resource);//得到绑定的资源数量
		}
		$insert = DB::connection('mysql')->table('t_coupon')->insert($data);

		if ($insert) {
			if ($data['type'] == 0) {
				$resource = $request->input('resource');

				if ($resource) {
					$resource = array_unique($resource);
				}

				foreach ($resource as $v) {
					$type = explode('--', $v);
					if ($type[1] > 0) {
						DB::connection('mysql')->table('t_coupon_resource')->insert([
							'app_id'      => $this->app_id,
							'cou_id'      => $data['id'],
							'bind_type'   => (int)$type[1],
							'resource_id' => $type[0],
							'created_at'  => date('Y-m-d H:i:s'),
						]);
					}
				}
			}

			return response()->json(['code' => 0, 'msg' => '新建优惠券成功', 'data' => []]);
		} else {
			return response()->json(['code' => -1, 'msg' => '新建优惠券失败', 'data' => []]);
		}

	}

	//进入修改页面
	public function edit (Request $request)
	{
		//        验证
		$this->validate($request, [
			'id' => 'required',
		], [
			'required' => ':attribute 为必填项',
		]);
		$app_id = AppUtils::getAppID();
		$id     = $request->input('id');
		//应用权限
		$appModuleInfo = AppUtils::getModuleInfo($app_id);

		$data = new \stdClass();
		//        $data->message = $message;
		$coupon_info       = DB::connection('mysql')->table('t_coupon')
			->where('app_id', $this->app_id)
			->where('id', $id)
			->first();
		$data->coupon_info = $coupon_info;

		$type = $coupon_info->type;
		if ($type == 0) {

			//提示（不再提示）查询
			$message = DB::connection('mysql_config')->table('t_message_reminder')
				->where('app_id', $this->app_id)
				->where('place', 2)
				->first();

			$message_info = [];
			if (!$message) {
				$message_info['app_id']  = $this->app_id;
				$message_info['content'] = "提示：保存成功后，只能修改发行量和商品范围，且发行量和商品范围只能增加，不能减少";
				$message_info['place']   = 2;
				$insert                  = DB::connection('mysql_config')->table('t_message_reminder')->insert($message_info);
				if ($insert) {
					$message          = new \stdClass();
					$message->content = $message_info['content'];
					$message->place   = 2;
					$message->status  = 0;
				}
			}

			$data->message   = $message;
			$data->type      = (int)$type;
			$data->page_type = 0;
		} else {

			//提示（不再提示）查询
			$message = DB::connection('mysql_config')->table('t_message_reminder')
				->where('app_id', $this->app_id)
				->where('place', 3)
				->first();

			$message_info = [];
			if (!$message) {

				$message_info['app_id']  = $this->app_id;
				$message_info['content'] = "提示：保存成功后，只能修改发行量，且发行量只能增加，不能减少";
				$message_info['place']   = 3;
				$insert                  = DB::connection('mysql_config')->table('t_message_reminder')->insert($message_info);
				if ($insert) {
					$message          = new \stdClass();
					$message->content = $message_info['content'];
					$message->place   = 3;
					$message->status  = 0;
				}
			}

			$data->message   = $message;
			$data->type      = (int)$type;
			$data->page_type = 0;
		}

		// 查询对应的绑定关系
		$resource_list = DB::connection('mysql')->table('t_coupon_resource')
			->select('resource_id', 'bind_type')
			->where('app_id', $app_id)
			->where('cou_id', $id)
			->where('relation_state', 0)
			->get();

		// 查对应的资源信息
		$res_info = [];
		foreach ($resource_list as $v) {
			$sql = '';
			switch ($v->bind_type) {
				case '1':
					$sql = "select id,title,img_url,img_url_compressed,1 as good_type from t_image_text where app_id = ? and display_state = 0 and id=?";
					break;
				case '2':
					$sql = "select id,title,img_url,img_url_compressed,2 as good_type from t_audio where app_id = ? and audio_state = 0 and id=?";
					break;
				case '3':
					$sql = "select id,title,img_url,img_url_compressed,3 as good_type from t_video where app_id = ? and video_state = 0 and id = ?";
					break;
				case '4':
					$sql = "select id,title,img_url,img_url_compressed,4 as good_type from t_alive where app_id = ? and state = 0 and id = ?";
					break;
				case '5':
					$sql = "select id,name as title ,img_url,img_url_compressed,5 as good_type from t_pay_products where app_id = ? and state = 0 and id = ?";
					break;
				case '6':
					$sql = "select id,name as title ,img_url,img_url_compressed,6 as good_type from t_pay_products where app_id = ? and state = 0 and is_member = 1 and id = ?";
					break;
				default:
					$sql = "";
			}

			if ($sql) {
				$res = DB::connection('mysql')->select($sql, [$app_id, $v->resource_id]);
				if ($res && count($res) > 0) {
					$res_info[] = $res[0];
				}
			}
		}
		$data->type      = $coupon_info->type;
		$data->page_type = 1;
		$data->res_info  = $res_info;

		return view("admin.marketing.coupon.editCoupon", [
			'data' => $data,
		]);
	}

	//修改优惠券信息
	public function editCoupon (Request $request)
	{
		$data['app_id'] = AppUtils::getAppID();
		$data['id']     = $request->input('id');
		$data['count']  = $request->input('count');//发行量

		//得到更新前的优惠券数据
		$old_coupon_info = DB::connection('mysql')->table('t_coupon')
			->select('type', 'count', 'price', 'bind_res_count')
			->where('app_id', $this->app_id)
			->where('id', $data['id'])
			->first();

		if ($old_coupon_info) {
			if ($data['count'] < $old_coupon_info->count) {
				return response()->json(['code' => -3, 'msg' => '当前发行量小于原先发行量', 'data' => []]);
			}
			$resource = [];
			if ($old_coupon_info->type == 0) {          //商品的时候，处理资源问题
				$resource = $request->input('resource');
				if ($resource) {
					$resource = array_unique($resource);
				}

			}

			// 获得该优惠券已经绑定的资源
			$old_resource = DB::connection('mysql')->table('t_coupon_resource')
				->select('bind_type', 'resource_id')
				->where('app_id', $this->app_id)
				->where('cou_id', $data['id'])
				->where('relation_state', 0)
				->get();
			//            dump($old_resource);
			$arr = [];
			foreach ($old_resource as $v) {
				$arr[] = "{$v->resource_id}--{$v->bind_type}";
			}
			//  获得新绑定的商品
			$new_resource = [];
			foreach ($resource as $v) {
				if (!in_array($v, $arr)) {
					$new_resource[] = $v;
				}
			}
			foreach ($new_resource as $v) {
				$type = explode('--', $v);
				DB::connection('mysql')->table('t_coupon_resource')->insert([
					'app_id'      => $this->app_id,
					'cou_id'      => $data['id'],
					'bind_type'   => $type[1],
					'resource_id' => $type[0],
					'created_at'  => date('Y-m-d H:i:s'),
				]);
			}
			// 更新优惠券绑定的商品数量
			$data['bind_res_count'] = $old_coupon_info->bind_res_count + count($new_resource);
			$update                 = DB::connection('mysql')->table('t_coupon')
				->where('app_id', $this->app_id)
				->where('id', $data['id'])
				->update($data);

			if ($update) {
				return response()->json(['code' => 0, 'msg' => '更新优惠券成功', 'data' => []]);
			} else {
				return response()->json(['code' => -1, 'msg' => '更新优惠券失败', 'data' => []]);
			}
		} else {
			return response()->json(['code' => -2, 'msg' => '更新优惠券失败', 'data' => []]);
		}
	}

	public function closeMessageReminder ($place)
	{
		$update = DB::connection('mysql_config')->table('t_message_reminder')
			->where('app_id', $this->app_id)
			->where('place', (int)$place)
			->update(['status' => 1]);
		if ($update) {
			return response()->json(['code' => 0, 'msg' => '请求成功', 'data' => []]);
		} else {
			return response()->json(['code' => -1, 'msg' => '请求失败', 'data' => []]);
		}
	}

	/*//发放计划
	public function planIndex(Request $request)
	{
		//search查询拼接

		$start_time = $request->input('start_time', '');//搜索起始日期
		$end_time = $request->input('end_time', '');//搜索结束日期
		$plan_name = $request->input('plan_name', '');

		$where = "app_id = '{$this->app_id}' ";//为了research查询条件

		//设定回显和分页参数
		$search_array = [];
		$search_array['start_time'] = $start_time;
		$search_array['end_time']=$end_time;
		$search_array['plan_name']=$plan_name;
		if ($start_time) {
			$where .= " and '{$start_time}' <= created_at ";
		}
		if ($end_time) {
			$end_time = date('Y-m-d 23:59:59', strtotime($end_time));
			$where .= " and '{$end_time}' >= created_at ";
		}
		if($plan_name)
		{
			$where.=" and title like '%{$plan_name}%' ";
		}

		$res_info=DB::connection('mysql')->table('t_coupon_plan')
			->select('cou_id','cou_name', 'resource_id', 'resource_type', 'title','send_at', 'send_state', 'resource_name')
			->whereRaw($where)
			->orderBy('created_at','desc')
			->paginate(10);
//        echo "<pre>";var_dump($res_info); exit;
		return view("admin.marketing.coupon.planIndex",[
			'resInfo' => $res_info,    // 优惠券列表
			'search_array' => $search_array,    // 搜索内容回显
		]);
	}
	//获取目标人群
	public function getResource(Request $request)
	{
		$name=$request->input('name','');

		$where="app_id = '{$this->app_id}' ";

		//设定回显和分页参数
		$search_array=[];

		if($name)
		{
			$where.=" and name like '%{$name}%' ";
			$search_array['name']=$name;
		}

		$res_info=DB::connection('mysql')->table('t_pay_products')
			->select('app_id', 'id', 'name', 'purchase_count', 'is_member')
			->whereRaw($where)
			->where('state', 0)
			->where('price', '>', 0)
			->orderBy('created_at','desc')
			->get();
		return  view("admin.marketing.coupon.planMembers",
			[
				'res_info'=>$res_info,
				'search_array'=>$search_array
			]);


	}
	//得到优惠券
	public function getCoupons(Request $request)
	{
		$name=$request->input('name','');

		$where="app_id = '{$this->app_id}'";

		//设定回显和分页参数
		$search_array=[];

		if($name) {
			$where.=" and title like '%{$name}%' ";
			$search_array['name']=$name;
		}
		$now_time=date("Y-m-d H:i:s");
//        $where.=" and ( (valid_at <= '{$now_time}' and  invalid_at > '{$now_time}') or '{$now_time}'< invalid_at) ";
		$res_info=DB::connection('mysql')->table('t_coupon')
			->select('app_id', 'id', 'title', 'price', 'count','has_received','require_price')
			->whereRaw($where)
			->where('spread_type', 1)
			->where('count','!=','has_received')
			->where('invalid_at','>', $now_time)
			->whereNull('manual_stop_at')
			->orderBy('created_at','desc')
			->get();
		return  view("admin.marketing.coupon.planCoupons",
			 [
				 'res_info'=>$res_info,
				 'search_array'=>$search_array
		]);
	}
	public function addCouponPlan(Request $request){
//        验证
		$this->validate($request,[
			'params.title'=>'required',
			'params.cou_id'=>'required',
			'params.cou_name'=>'required',
			'params.resource_id'=>'required',
			'params.resource_name'=>'required',
			'params.count'=>'required',
		],[
			'required'=>':attribute 为必填项'
		]);
//
		$params = $request->input('params',[]);

		// 查询该资源的订购人数
		$count = DB::connection('mysql')->table('t_purchase')
			->where('app_id',$this->app_id)->where('payment_type',3)->where('product_id',$params['resource_id'])
			->where('expire_at','>',date('Y-m-d H:i:s'))->where('created_at','<=',date('Y-m-d H:i:s'))->where('is_deleted',0)
			->count('user_id');
		if ($count < $params['count']) return json_encode(['code'=>-1,'msg'=>'参数错误','data'=>[]]);

		// 查剩余优惠券数量
		$coupon = DB::connection('mysql')->table('t_coupon')->where('app_id',$this->app_id)->where('id',$params['cou_id'])
			->where('spread_type',1)->where('invalid_at','>',date('Y-m-d H:i:s'))->where('is_delete',0)->first();
		// 查优惠券
		if ($coupon){
			if ($count > $coupon->count - $coupon->has_received) return json_encode(['code'=>-1,'msg'=>'优惠券不足','data'=>[]]);
		}else {
			return json_encode(['code'=>-1,'msg'=>'无效的优惠券信息','data'=>[]]);
		}

		// 组装数据
		$params['id'] = Utils::getCouponPlanId();
		$params['app_id'] = $this->app_id;
		$params['count'] = $count;
		$params['created_at'] = date('Y-m-d H:i:s');

		$insert = DB::connection('mysql')->table('t_coupon_plan')->insert($params);

		if ($insert) {
			//存一个一次性session  用户后置操作 查询
			$request->session()->flash('plan_id', $params['id']);

			return json_encode(['code'=>0,'msg'=>'创建成功','data'=>[]]);
		}else{
			return json_encode(['code'=>-2,'msg'=>'创建失败','data'=>[]]);
		}
	}
	public function createPlanPage(){
		return view('admin.marketing.coupon.createPlan');
	}*/

}
