<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tools\AppUtils;
use DB;
use Illuminate\Http\Request;
use Session;

class OverviewController extends Controller
{
	protected $app_id;

	public function __construct ()
	{
		$this->app_id = AppUtils::getAppID();
	}

	public function index ()
	{
		$date = date('Y-m-d');
		// 今日付费用户 今日活跃用户  今日总收入  总付费用户
		$data = DB::connection('mysql_stat')->table('t_dash_stat_daycount')
			->where('app_id', $this->app_id)
			->where('date', $date)
			->first(['add_payer as day_user', 'active_count', 'income as day_income','count_payer as sum_user']);
		if (!$data) {
			$data               = new \stdClass();
			$data->day_user     = 0;
			$data->active_count = 0;
			$data->day_income   = 0;
			$data->sum_user   = 0;
		}

		// 可提现余额
		$account_balance       = DB::connection("db_ex_finance")->table('t_usable_balance')
			->where('app_id', $this->app_id)
			->value('account_balance');
		$data->account_balance = $account_balance ? $account_balance : 0;

		// 流量账户余额
		$app_info      = DB::connection('mysql_config')->table('t_app_conf')
			->where('app_id', '=', $this->app_id)
			->where('wx_app_type', '=', 1)
			->first();
		$data->balance = $app_info->balance ? $app_info->balance : 0;

		//用户的商户名
		$name = DB::connection("mysql_config")->select("
            SELECT
                wx_app_name
            FROM
                t_merchant_conf
            WHERE
                merchant_id = (
                    SELECT
                        merchant_id
                    FROM
                        t_app_conf
                    WHERE
                        app_id = '{$this->app_id}'
                    AND wx_app_type = 1
                )    
	    ");
		if ($name) {
			$data->name = $name[0]->wx_app_name ? $name[0]->wx_app_name : '小鹅通知识店铺';
		} else {
			$data->name = '小鹅通知识店铺';
		}
		// 店铺地址
		if ($app_info->use_collection === 1) {
			$data->url = AppUtils::getUrlHeader($this->app_id) . env("DOMAIN_DUAN_NAME") . "/{$this->app_id}";
		} else {
			$data->url = AppUtils::getUrlHeader($this->app_id) . $app_info->wx_app_id . '.' . env("DOMAIN_NAME");
		}

		// 提示消息
		$message      = DB::connection('mysql_config')->table('t_message_reminder')
			->where('app_id', $this->app_id)
			->where('place', 0)
			->first();
		$message_info = [];
		if (!$message) {
			$message_info['app_id']  = $this->app_id;
			$message_info['content'] = "他们做得不错";
			$insert                  = DB::connection('mysql_config')->table('t_message_reminder')->insert($message_info);
			if ($insert) {
				$message          = new \stdClass();
				$message->content = $message_info['content'];
				$message->status  = 0;
			}
		}
		$data->message = $message;

		//优惠券上线提示
		$message_coupon = DB::connection('mysql_config')->table('t_message_reminder')
			->where('app_id', $this->app_id)
			->where('place', 12)
			->first();
		if (!$message_coupon) {
			$message_info['app_id']  = $this->app_id;
			$message_info['content'] = "好消息！优惠券上线啦！";
			$message_info['place']   = 12;
			$message_info['status']  = 0;
			$insert                  = DB::connection('mysql_config')->table('t_message_reminder')->insert($message_info);
			if ($insert) {
				$message_coupon          = new \stdClass();
				$message_coupon->app_id  = $this->app_id;
				$message_coupon->content = $message_info['content'];
				$message_coupon->place   = $message_info['place'];
				$message_coupon->status  = $message_info['status'];
			}
		}
		$data->message_coupon = $message_coupon;

		return view('admin.guide.index', [
			'data' => $data,
		]);
	}

	public function closeMessageReminder (Request $request)
	{
		$status = $request->input('status', 0);
		$place  = $request->input('place', 0);
		if ($status) {
			$update = DB::connection('mysql_config')->table('t_message_reminder')
				->where('app_id', $this->app_id)
				->where('place', $place)
				->update(['status' => 1]);
			if ($update) {
				return json_encode(['code' => 0, 'msg' => '请求成功', 'data' => []]);
			} else {
				return json_encode(['code' => -1, 'msg' => '请求失败', 'data' => []]);
			}
		} else {
			return json_encode(['code' => -2, 'msg' => '参数有误', 'data' => []]);
		}
	}

	public function redirectCurrentUrl ($id)
	{
		// 声明一级不需要跳转子权限页的父类权限
		$parent_id = [101, 102, 103, 105, 128];

		// 可跳转链接
		$permission = [
			101 => '/package_list_page?prompt=1',
			102 => '/marketing',
			103 => '/community_operate',
			105 => '/dashboard',
			108 => '/interfacesetting',
			109 => '/shopIndexDiy',
			110 => '/sharesetting',
			111 => '/wxaccountsetting',
			112 => '/manage_function',
			122 => '/customer',
			123 => '/pay_admin',
			124 => '/message',
			125 => '/feedback',
			126 => '/income/company',
			127 => '/income/person',
			128 => '/order_list',
			129 => '/withdraw_page',
			130 => '/accountview',
			999 => '/accountmanage',
			131 => '/admin/child',
			132 => '/companymodel',
			133 => '/mini/index',
		];

		if (!$id) return response()->json(['code' => -1, 'msg' => '参数错误', 'data' => []]);

		// 没有权限就跳转登陆
		$access = Session::get('access', []);
		if (!$access) return redirect('/login');

		if (!array_key_exists($id, $access)) return response()->json(['code' => -1, 'msg' => '参数错误', 'data' => []]);

		if ($access[ $id ] < 0) return response()->json(['code' => -2, 'msg' => '无访问权限', 'data' => []]);

		// 直接跳转至对应路由
		if ($access[ $id ]) {
			if (in_array($id, $parent_id)) return redirect($permission[ $id ]);

			//获取可用的子权限
			$privilege = DB::connection('mysql_config')->table('t_admin_privilege')->whereNull('deleted_at')->where('parent_id', $id)->pluck('id');

			foreach ($privilege as $v) {
				if ($access[ $v ]) return redirect($permission[ $v ]);
			}
		}
	}

}
