<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\Utils;
use Illuminate\Support\Facades\Input;

class ScienceController extends Controller
{
	/**
	 **1---------专栏：18个
	 **2---------套餐：5个
	 **3---------大套餐：1个
	 **/
	private $app_id;
	private $wenan;
	private $yingshe;

	public function __construct ()
	{
		$this->app_id  = 'app6uMOq3u41326';
		$this->wenan   =
			[
				1 =>
					[
						1  => ['name' => '邀请码1-1', 'card_wish' => '和孩子一起，听科学家讲科学！'],
						2  => ['name' => '邀请码1-2', 'card_wish' => '和孩子一起，听科学家讲科学！'],
						3  => ['name' => '邀请码1-3', 'card_wish' => '和孩子一起，听科学家讲科学！'],
						4  => ['name' => '邀请码1-4', 'card_wish' => '和孩子一起，听科学家讲科学！'],
						5  => ['name' => '邀请码1-5', 'card_wish' => '和孩子一起，听科学家讲科学！'],
						6  => ['name' => '邀请码1-6', 'card_wish' => '和孩子一起，听科学家讲科学！'],
						7  => ['name' => '邀请码1-7', 'card_wish' => '和孩子一起，听科学家讲科学！'],
						8  => ['name' => '邀请码1-8', 'card_wish' => '和孩子一起，听科学家讲科学！'],
						9  => ['name' => '邀请码1-9', 'card_wish' => '和孩子一起，听科学家讲科学！'],
						10 => ['name' => '邀请码1-10', 'card_wish' => '和孩子一起，听科学家讲科学！'],
						11 => ['name' => '邀请码1-11', 'card_wish' => '和孩子一起，听科学家讲科学！'],
						12 => ['name' => '邀请码1-12', 'card_wish' => '和孩子一起，听科学家讲科学！'],
						13 => ['name' => '邀请码1-13', 'card_wish' => '和孩子一起，听科学家讲科学！'],
						14 => ['name' => '邀请码1-14', 'card_wish' => '和孩子一起，听科学家讲科学！'],
						15 => ['name' => '邀请码1-15', 'card_wish' => '和孩子一起，听科学家讲科学！'],
						16 => ['name' => '邀请码1-16', 'card_wish' => '和孩子一起，听科学家讲科学！'],
						17 => ['name' => '邀请码1-17', 'card_wish' => '和孩子一起，听科学家讲科学！'],
						18 => ['name' => '邀请码1-18', 'card_wish' => '和孩子一起，听科学家讲科学！'],
					],
				2 =>
					[
						1 => ['name' => '邀请码2-1', 'card_wish' => '和孩子一起，听科学家讲科学！'],
						2 => ['name' => '邀请码2-2', 'card_wish' => '和孩子一起，听科学家讲科学！'],
						3 => ['name' => '邀请码2-3', 'card_wish' => '和孩子一起，听科学家讲科学！'],
						4 => ['name' => '邀请码2-4', 'card_wish' => '和孩子一起，听科学家讲科学！'],
						6 => ['name' => '邀请码2-6', 'card_wish' => '和孩子一起，听科学家讲科学！'],
					],
				3 =>
					[
						12 => ['name' => '邀请码3-12', 'card_wish' => '和孩子一起，听科学家讲科学！'],
					],
			];
		$this->yingshe =
			[
				1 => [1  => 'p_58789ffeed783_mCOgaP4q', 2 => 'p_5878a0d73a4d5_yPP6dcYO', 3 => 'p_5878a1739fe38_drCTHqq9',
					  4  => 'p_5878a1e902ba2_2B2dX9X5', 5 => 'p_587f2ff707d6e_17AaCiSA', 6 => 'p_587f734195550_0UDZXNqX',
					  7  => 'p_587f736d0fa9a_9c2g61k6', 8 => 'p_587f73d5a887f_7HAGCoxZ', 9 => 'p_587f73f692503_K52VYRk9',
					  10 => 'p_587f741f6c9e3_31RqFLnx', 11 => 'p_587f743e4bfe0_6JJw4pez', 12 => 'p_587f77dba8e15_kcEko20j',
					  13 => 'p_587f782462f2b_IS7Hux0N', 14 => 'p_587f7845421a3_Gy3mb13B', 15 => 'p_587f7867a00cd_UmYOjnEW',
					  16 => 'p_587f78d42f960_86KbdN8O', 17 => 'p_587f78fa97e32_QMOFuCtA', 18 => 'p_587f791368708_w1EulTXn'],
				2 => [1 => 'p_5880684008cae_5nuVSkLj', 2 => 'p_588068e7544b2_VWt2AR6k', 3 => 'p_588069921fde6_iuoliTUU',
					  4 => 'p_58806a04e5a7f_9DwRz8xr', 6 => 'p_58806a99bb61e_2KMi2CWp'],
				3 => [12 => 'p_58806ad26aa1b_0BVfv0IN'],
			];
	}

	//科学队长邀请码
	public function scienceInviteCode ()
	{
		set_time_limit(3600);
		ini_set('memory_limit', '1024M');

		//先取出管理台产生的邀请码,根据type+product生成对应的批次个数
		$result1 = \DB::connection("mysql_kxdz")->select("select type,product,count(*) as count,buy_time
        from coupon where buyer_id='0' and (status='1' or status='2') group by type,product");
		foreach ($result1 as $key => $value) {
			$batch                    = [];
			$batch['app_id']          = $this->app_id;
			$batch['name']            = $this->wenan[ $value->type ][ $value->product ]['name'];//批次名称,需要文案
			$batch['count']           = $value->count;
			$batch['generate_type']   = 0;
			$batch['buy_user_id']     = null;
			$batch['group_config_id'] = null;
			$batch['payment_type']    = 3;
			$batch['resource_type']   = 2;
			$batch['resource_id']     = null;
			$batch['product_id']      = $this->yingshe[ $value->type ][ $value->product ];
			//从我们数据库拉取数据
			$temp                 = \DB::select("select name,img_url,price from t_pay_products where app_id=? and id=?",
				[$this->app_id, $batch['product_id']]);
			$batch['target_name'] = empty($temp) ? null : $temp[0]->name;
			$batch['img_url']     = empty($temp) ? null : $temp[0]->img_url;
			$batch['price']       = empty($temp) ? null : $temp[0]->price;

			$batch['start_at']       = null;
			$batch['stop_at']        = null;
			$batch['reason']         = null;
			$batch['card_title']     = $batch['target_name'];
			$batch['card_desc']      = null;
			$batch['card_cover_url'] = $batch['img_url'];
			$batch['period']         = null;
			$batch['created_at']     = date('Y-m-d H:i:s', $value->buy_time);
			$batchId                 = \DB::table("t_gift_batch")->insertGetId($batch);

			//插入邀请码表
			$result2 = \DB::connection("mysql_kxdz")->select("select * from coupon where type=? and product=?
            and buyer_id='0' and (status='1' or status='2')", [$value->type, $value->product]);
			foreach ($result2 as $codeKey => $codeValue) {
				$code                     = [];
				$code['app_id']           = $this->app_id;
				$code['batch_id']         = $batchId;
				$code['first_half_code']  = substr($codeValue->code, 0, 12);
				$code['second_half_code'] = substr($codeValue->code, 12, 4);
				$code['code']             = substr($codeValue->code, 0, 16);
				$code['qr_code_url']      = "http://wx21df74ead4dca012.h5.xiaoe-tech.com/giftcode/" . $code['code'];
				$code['error_counts']     = 0;
				$code['last_tried_at']    = null;
				$code['lock_time']        = null;
				//邀请码状态
				if ($codeValue->status == 1) {
					$code['state'] = 0;
				} else {
					$code['state'] = 1;
				}

				$code['card_name']  = null;
				$code['card_wish']  = $this->wenan[ $value->type ][ $value->product ]['card_wish'];
				$code['user_id']    = $codeValue->accepter_id;
				$code['share_at']   = null;
				$code['used_at']    = date('Y-m-d H:i:s', $codeValue->consume_time);
				$code['period']     = null;
				$code['created_at'] = date('Y-m-d H:i:s', $codeValue->buy_time);
				try {
					$insertCode = \DB::table("t_gift_code")->insert($code);
				} catch (\Exception $e) {
				}
			}
		}

		//再取出团购数据,每条就是一个批次+一个码
		$result3 = \DB::connection("mysql_kxdz")->select("select * from coupon where buyer_id!='0' 
        and (status='1' or status='2') ");
		foreach ($result3 as $key => $value) {
			$batch                    = [];
			$batch['app_id']          = $this->app_id;
			$batch['name']            = '购买邀请码赠送';
			$batch['count']           = 1;
			$batch['generate_type']   = 2;
			$batch['buy_user_id']     = $value->buyer_id == 0 ? null : $value->buyer_id;
			$batch['group_config_id'] = null;
			$batch['payment_type']    = 3;
			$batch['resource_type']   = 2;
			$batch['resource_id']     = null;
			$batch['product_id']      = $this->yingshe[ $value->type ][ $value->product ];
			//从我们数据库找数据
			$temp                 = \DB::select("select name,img_url,price from t_pay_products where app_id=? and id=?",
				[$this->app_id, $batch['product_id']]);
			$batch['target_name'] = empty($temp) ? null : $temp[0]->name;
			$batch['img_url']     = empty($temp) ? null : $temp[0]->img_url;
			$batch['price']       = empty($temp) ? null : $temp[0]->price;

			$batch['start_at']       = null;
			$batch['stop_at']        = null;
			$batch['reason']         = null;
			$batch['card_title']     = $batch['target_name'];
			$batch['card_desc']      = null;
			$batch['card_cover_url'] = $batch['img_url'];
			$batch['period']         = null;
			$batch['created_at']     = date('Y-m-d H:i:s', $value->buy_time);
			$batchId                 = \DB::table("t_gift_batch")->insertGetId($batch);

			$code                     = [];
			$code['app_id']           = $this->app_id;
			$code['batch_id']         = $batchId;
			$code['first_half_code']  = substr($value->code, 0, 12);
			$code['second_half_code'] = substr($value->code, 12, 4);
			$code['code']             = substr($value->code, 0, 16);
			$code['qr_code_url']      = "http://wx21df74ead4dca012.h5.xiaoe-tech.com/giftcode/" . $code['code'];;
			$code['error_counts']  = 0;
			$code['last_tried_at'] = null;
			$code['lock_time']     = null;
			//邀请码状态
			if ($value->status == 1) {
				$code['state'] = 0;
			} else {
				$code['state'] = 1;
			}

			$code['card_name']  = null;
			$code['card_wish']  = $this->wenan[ $value->type ][ $value->product ]['card_wish'];
			$code['user_id']    = $value->accepter_id;
			$code['share_at']   = null;
			$code['used_at']    = date('Y-m-d H:i:s', $value->consume_time);
			$code['period']     = null;
			$code['created_at'] = date('Y-m-d H:i:s', $value->buy_time);
			try {
				$insertCode = \DB::table("t_gift_code")->insert($code);
			} catch (\Exception $e) {
			}
		}
	}

	//科学队长订单:40000
	public function scienceOrder ()
	{
		set_time_limit(3600);
		ini_set('memory_limit', '1024M');
		$result = \DB::connection("mysql_kxdz")->select("select * from orders order by id");
		if ($result) {
			foreach ($result as $key => $value) {
				$order                  = [];
				$order['app_id']        = $this->app_id;
				$order['order_id']      = $value->unique_order_id;
				$order['user_id']       = $value->member_id;
				$order['payment_type']  = 3;
				$order['resource_type'] = 2;
				$order['resource_id']   = null;
				$order['product_id']    = $this->yingshe[ $value->type ][ $value->order_product ];
				$order['count']         = 1;
				$order['channel_id']    = null;
				$order['share_user_id'] = null;
				$order['share_type']    = null;
				$order['purchase_name'] = $value->order_name;
				//从我们数据库选取资源配图
				$temp             = \DB::select("select img_url from t_pay_products where app_id=? and id=?",
					[$this->app_id, $order['product_id']]);
				$order['img_url'] = empty($temp) ? null : $temp[0]->img_url;

				$order['price'] = $value->money * 100;
				//订单状态
				if ($value->status == 3)//成功
				{
					$order['order_state'] = 1;
				} else {
					$order['order_state'] = 0;
				}

				$order['out_order_id'] = null;
				$order['wx_app_type']  = '1';
				$order['period']       = null;
				$order['created_at']   = date('Y-m-d H:i:s', $value->create_time);
				try {
					$insertOrder = \DB::table("t_orders")->insert($order);
				} catch (\Exception $e) {
				}
			}
		}
	}

	//科学队长用户
	public function scienceUser ()
	{
		set_time_limit(3600);
		ini_set('memory_limit', '1024M');

		$result = \DB::connection("mysql_kxdz")->select("select * from member order by id limit ?,10000",
			[Input::get("start")]);
		if ($result) {
			foreach ($result as $key => $value) {
				$user                          = [];
				$user['app_id']                = $this->app_id;
				$user['user_id']               = $value->id;
				$user['wx_open_id']            = $value->openid;
				$user['wx_union_id']           = null;
				$user['wx_app_open_id']        = null;
				$user['collection_open_id']    = null;
				$user['wx_session_key']        = null;
				$user['wx_session_key_expire'] = '0000-00-00 00:00:00';
				$user['wx_name']               = null;
				$user['wx_nickname']           = $value->nickname;
				$user['wx_avatar']             = null;
				$user['wx_avatar_wx']          = null;
				$user['wx_avatar_md5']         = null;
				$user['need_update_avatar']    = 1;
				$user['wx_email']              = null;
				$user['wx_gender']             = $value->sex;
				$user['wx_language']           = null;
				$user['wx_city']               = null;
				$user['wx_province']           = null;
				$user['wx_country']            = null;
				$user['wx_update_at']          = null;
				$user['age']                   = null;
				$user['birth']                 = $value->birthday;
				$user['phone']                 = $value->mobile;
				$user['address']               = $value->location;
				$user['job']                   = null;
				$user['company']               = null;
				$user['industry']              = null;
				$user['tags']                  = null;
				$user['state_msg_time']        = null;
				$user['record_ip']             = null;
				$user['record_agent']          = null;
				$user['last_visit_at']         = null;
				$user['open_effect']           = 1;
				$user['created_at']            = Utils::getTime();
				try {
					$insertUser = \DB::table("t_users")->insert($user);
				} catch (\Exception $e) {
				}
			}
		}
	}

	//科学队长订购关系:4W+,全部都是类型为1的小专栏
	public function sciencePurchase ()
	{
		set_time_limit(3600);
		ini_set('memory_limit', '1024M');
		$result = \DB::connection("mysql_kxdz")->select("
        select order_id,member_id,product_id,starttime,endtime,money,`usage` from
        (
        select member_id,order_id,product_id,starttime,endtime from subscribe order by id limit ?,10000
        )t1
        left join
        (
        select id,money,`usage` from orders
        )t2
        on t1.order_id=t2.id", [Input::get("start")]);
		if ($result) {
			foreach ($result as $key => $value) {
				$purchase                  = [];
				$purchase['app_id']        = $this->app_id;
				$purchase['user_id']       = $value->member_id;
				$purchase['payment_type']  = 3;
				$purchase['resource_type'] = 0;
				$purchase['product_id']    = $this->yingshe[1][ $value->product_id ];
				if ($value->usage == 1) {
					$purchase['generate_type'] = 0;
				} else {
					$purchase['generate_type'] = 1;
				}
				$purchase['resource_id']   = null;
				$purchase['channel_id']    = null;
				$purchase['share_user_id'] = null;
				$purchase['share_type']    = null;

				//从我们数据库取数据
				$temp                      = \DB::select("select name,img_url from t_pay_products where app_id=? and id=?",
					[$this->app_id, $purchase['product_id']]);
				$purchase['purchase_name'] = $temp[0]->name;
				$purchase['img_url']       = empty($temp) ? null : $temp[0]->img_url;
				$purchase['price']         = $value->money * 100;
				$purchase['order_id']      = $value->order_id;
				$purchase['remark']        = '科学队长导入';
				$purchase['wx_app_type']   = 1;
				$purchase['expire_at']     = null;
				$purchase['is_deleted']    = 0;
				$purchase['created_at']    = date('Y-m-d H:i:s', $value->starttime);
				try {
					$insertPurchase = \DB::table("t_purchase")->insert($purchase);
				} catch (\Exception $e) {
				}
			}
		}
	}

	//科学队长根据订单类型为2,3的添加对应的订购记录
	public function scienceReAdd ()
	{
		set_time_limit(3600);
		ini_set('memory_limit', '1024M');
		$result = \DB::connection("mysql_kxdz")->select("select * from orders where (type='2' or type='3')
        and status='3' ");
		foreach ($result as $key => $value) {
			$purchase                  = [];
			$purchase['app_id']        = $this->app_id;
			$purchase['user_id']       = $value->member_id;
			$purchase['payment_type']  = 3;
			$purchase['resource_type'] = 0;
			$purchase['product_id']    = $this->yingshe[ $value->type ][ $value->order_product ];
			if ($value->usage == 1) {
				$purchase['generate_type'] = 0;
			} else {
				$purchase['generate_type'] = 1;
			}
			$purchase['resource_id']   = null;
			$purchase['channel_id']    = null;
			$purchase['share_user_id'] = null;
			$purchase['share_type']    = null;
			$purchase['purchase_name'] = $value->order_name;

			//从我们数据库取数据
			$temp                    = \DB::select("select img_url from t_pay_products where app_id=? and id=?",
				[$this->app_id, $purchase['product_id']]);
			$purchase['img_url']     = empty($temp) ? null : $temp[0]->img_url;
			$purchase['price']       = $value->money * 100;
			$purchase['order_id']    = $value->id;
			$purchase['remark']      = '科学队长导入';
			$purchase['wx_app_type'] = 1;
			$purchase['expire_at']   = null;
			$purchase['is_deleted']  = 0;
			$purchase['created_at']  = date('Y-m-d H:i:s', $value->create_time);
			try {
				$insertPurchase = \DB::table("t_purchase")->insert($purchase);
			} catch (\Exception $e) {
			}
		}
	}

	//更新处理
	public function scienceDeal ()
	{
		//大套餐
		$result = \DB::select("select user_id from t_purchase where app_id=? and product_id=?", [$this->app_id, $this->yingshe[3][12]]);
		foreach ($result as $key => $value) {
			$update = \DB::update("update t_purchase set price='0',generate_type='3' where app_id=? and user_id=?
            and (product_id=? or product_id=? or product_id=? or product_id=? or product_id=? or product_id=?
            or product_id=?)", [$this->app_id, $value->user_id, $this->yingshe[1][5], $this->yingshe[1][6],
				$this->yingshe[1][7], $this->yingshe[1][8], $this->yingshe[1][9], $this->yingshe[1][10],
				$this->yingshe[1][11]]);
		}

		//套餐1
		$result = \DB::select("select user_id from t_purchase where app_id=? and product_id=?", [$this->app_id, $this->yingshe[2][1]]);
		foreach ($result as $key => $value) {
			$update = \DB::update("update t_purchase set price='0',generate_type='3' where app_id=? and user_id=?
            and (product_id=? or product_id=? or product_id=? or product_id=?)",
				[$this->app_id, $value->user_id, $this->yingshe[1][1], $this->yingshe[1][2], $this->yingshe[1][3],
					$this->yingshe[1][4]]);
		}

		//套餐2
		$result = \DB::select("select user_id from t_purchase where app_id=? and product_id=?", [$this->app_id, $this->yingshe[2][2]]);
		foreach ($result as $key => $value) {
			$update = \DB::update("update t_purchase set price='0',generate_type='3' where app_id=? and user_id=?
            and (product_id=? or product_id=? or product_id=?)",
				[$this->app_id, $value->user_id, $this->yingshe[1][5], $this->yingshe[1][6], $this->yingshe[1][7]]);
		}

		//套餐3
		$result = \DB::select("select user_id from t_purchase where app_id=? and product_id=?", [$this->app_id, $this->yingshe[2][3]]);
		foreach ($result as $key => $value) {
			$update = \DB::update("update t_purchase set price='0',generate_type='3' where app_id=? and user_id=?
            and (product_id=? or product_id=? or product_id=? or product_id=?)",
				[$this->app_id, $value->user_id, $this->yingshe[1][8], $this->yingshe[1][9], $this->yingshe[1][10],
					$this->yingshe[1][11]]);
		}

		//套餐4
		$result = \DB::select("select user_id from t_purchase where app_id=? and product_id=?", [$this->app_id, $this->yingshe[2][4]]);
		foreach ($result as $key => $value) {
			$update = \DB::update("update t_purchase set price='0',generate_type='3' where app_id=? and user_id=?
            and (product_id=? or product_id=? or product_id=? or product_id=?)",
				[$this->app_id, $value->user_id, $this->yingshe[1][12], $this->yingshe[1][13], $this->yingshe[1][14],
					$this->yingshe[1][15]]);
		}

		//套餐6
		$result = \DB::select("select user_id from t_purchase where app_id=? and product_id=?", [$this->app_id, $this->yingshe[2][6]]);
		foreach ($result as $key => $value) {
			$update = \DB::update("update t_purchase set price='0',generate_type='3' where app_id=? and user_id=?
            and (product_id=? or product_id=? or product_id=?)",
				[$this->app_id, $value->user_id, $this->yingshe[1][16], $this->yingshe[1][17], $this->yingshe[1][18]]);
		}
	}
}








