<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\Utils;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class UserController extends Controller
{
	private $request;

	public function __construct (Request $request)
	{
		$this->request = $request;
	}

	// 账户一览
	public function accountView (Request $request)
	{
		//        dd($request->session()->all());

		$app_id = AppUtils::getAppID();

		// 从数据库t_app_conf中获取前端传来的app_id对应的version_type
		$user_local_version = \DB::connection('mysql_config')->table('t_app_conf')->where('app_id', $app_id)->where('wx_app_type', '1')->first();
		if (!empty($user_local_version)) {
			session(['version_type' => $user_local_version->version_type]);
		}

		if ($user_local_version->use_collection == 0) $model = 'company';

		$app_balance         = $this->get_app_balance();                //账户余额
		$total_month_expense = $this->get_month_expense();      //本月消费
		$total_month_space   = $this->get_month_storage();        //本月存储使用量
		$total_month_sms     = $this->get_month_sms();              //本月短信发送量
		$total_month_flow    = $this->get_month_flow_data();       //本月累积播放流量
		$record_list         = $this->get_record_list();                //结算记录
		$record_nums         = $this->get_record_nums();                //结算记录总数目

		return View('admin.accountView', compact('data', 'model', 'app_balance', 'total_month_expense', 'total_month_space', 'total_month_sms', 'total_month_flow', 'record_list', 'record_nums'));
	}

	private function get_app_balance ()
	{

		$app_id       = AppUtils::getAppID();
		$package_type = \DB::connection('mysql_config')
			->table('t_app_conf')
			->where('app_id', '=', $app_id)
			->where('wx_app_type', '=', 1)
			->first();

		if ($package_type) {
			$balance = number_format($package_type->balance / 100, 2, '.', ',');

			return $balance;
		} else {
			return 0.00;
		}
	}

	private function get_month_expense ()
	{

		$end_date = Utils::getTime();
		$app_id   = AppUtils::getAppID();

		$end_arr = explode("-", $end_date);

		$start_year  = intval($end_arr[0]);
		$start_month = $end_arr[1];
		$start_day   = '01';
		$start_date  = $start_year . '-' . $start_month . '-' . $start_day;
		$end_date    = $end_arr[0] . '-' . $end_arr[1] . '-' . $end_arr[2];
		$result      = \DB::connection('db_ex_finance')->select("select sum(fee) as total_fee from t_balance_charge where app_id='$app_id' and charge_at >='$start_date' and charge_at <= '$end_date' and charge_type in(201,202,203,204,205) and state=2");

		if (count($result) > 0) {
			$month_total_expense = $result[0]->total_fee;
		} else {
			$month_total_expense = 0.00;
		}
		//        $total_storage_expense = $this->get_month_storage()*0.9;
		//        $total_sms_expense = $this->get_month_sms()*0.05;
		//        $total_flow_data_expense = $this->get_month_flow_data()*0.6;
		//
		//
		//        $month_total_expense = $total_storage_expense + $total_sms_expense + $total_flow_data_expense;
		$month_total_expense = number_format($month_total_expense / 100, 2, '.', ',');

		return $month_total_expense;
	}

	private function get_month_storage ()
	{

		$start_date = $this->get_month_firstday();
		$end_date   = Utils::getTime();
		$app_id     = AppUtils::getAppID();

		//本月总存储空间
		$storage = \DB::select("select sum(extra/1024) size_space from db_ex_finance.t_balance_charge where app_id = '$app_id' and  created_at>='$start_date' and created_at<='$end_date' and charge_type = 203 and state=2");

		//视频存储空间
		//        $sp_storage = \DB::select("select count(*) num_sp,FORMAT(sum(video_size/1024),2) size_sp_space, floor(sum(video_size/1024))*0.9 bal_sp_space from db_ex_business.t_video where app_id = '$app_id' and  created_at>='$start_date' and created_at<='$end_date'");

		$total_storage = $storage[0]->size_space;
		$total_storage = number_format($total_storage, 2, '.', ',');

		return $total_storage;
	}

	private function get_month_firstday ()
	{
		$end_date = Utils::getTime();

		$end_arr = explode("-", $end_date);

		$start_year  = intval($end_arr[0]);
		$start_month = $end_arr[1];
		$start_day   = '01';
		$start_date  = $start_year . '-' . $start_month . '-' . $start_day . ' 00:00:00';

		return $start_date;
	}

	private function get_month_sms ()
	{

		$start_date = $this->get_month_firstday();
		$end_date   = Utils::getTime();
		$app_id     = AppUtils::getAppID();
		$openid     = AppUtils::getOpenIdByAppId($app_id);
		//短信发送量:条
		//前台验证码
		//        $web_sms_total = \DB::select("select count(*) num_sms,floor(count(*)*0.05) bal_sms from db_ex_business.t_verify_codes where created_at>='$start_date' and created_at<='$end_date' and app_id = '$app_id'");
		//        //管理台验证码
		//        $admin_sms_total = \DB::connection('mysql_config')->select("select count(*) num_sms,floor(count(*)*0.05) bal_sms from db_ex_config.t_mgr_verify_codes where created_at>='$start_date' and created_at<='$end_date' and openid = '$openid'");

		$sms_total = \DB::select("select sum(extra) sms_total from db_ex_finance.t_balance_charge where app_id = '$app_id' and  created_at>='$start_date' and created_at<='$end_date' and charge_type = 204 and state=2");

		$total_sms = $sms_total[0]->sms_total;

		return $total_sms;
	}

	private function get_month_flow_data ()
	{

		$start_date = $this->get_month_firstday();
		$end_date   = Utils::getTime();
		$app_id     = AppUtils::getAppID();
		//        //本月音频流量
		//        $yp_flow_result = \DB::select("select  count(*) num_yp_flow, FORMAT(sum(size)*2.6/1024,2) size_yp_flow, floor(sum(size)*2.6/1024)*0.6 bal_yp_flow from t_data_usage where created_at>='$start_date' and created_at<'$end_date' and  resource_type=1  and app_id = '$app_id' ");
		//
		//        //本月视频流量
		//        $sp_flow_result = \DB::select("select  count(*) num_sp_flow, FORMAT(sum(size)/1024,2) size_sp_flow, floor(sum(size)/1024)*0.6 bal_sp_flow, floor(sum(size)/10/1024) size_sp_flow_xishu, floor(sum(size)/10/1024)*0.6 bal_sp_flow_xishu from db_ex_business.t_data_usage where created_at>='$start_date' and created_at<'$end_date' and  resource_type in(2,3)  and app_id='$app_id'");

		$flow_total = \DB::select("select sum(extra) flow_total from db_ex_finance.t_balance_charge where app_id = '$app_id' and  created_at>='$start_date' and created_at<='$end_date' and charge_type = 202 and state=2");

		$total_flow = $flow_total[0]->flow_total;
		$total_flow = number_format($total_flow / 1024.00, 2, '.', ',');

		return $total_flow;
	}

	public function get_record_list ()
	{
		$result = \DB::connection('db_ex_finance')
			->table('t_balance_charge')
			->where('app_id', '=', AppUtils::getAppID())
			->where('state', '!=', 1)
			->orderBy('charge_time', 'desc')
			->orderBy('serice_number', 'desc')
			->paginate(10);

		return $result;
	}
	/*********************** 新充值页面 - end *************************/

	// 账户一览 ---充值页面
	public function get_record_nums ()
	{
		$app_id = AppUtils::getAppID();
		$result = \DB::connection('db_ex_finance')->select("select count(*) as num from t_balance_charge where app_id = '$app_id'");
		if ($result) {
			$num = $result[0]->num;
		} else {
			$num = 0;
		}

		return $num;
	}

	//获取客户结算记录

	public function update_version_page ()
	{

		return View('admin.updateVersionPage', compact('data'));
	}

	//获取客户结算记录总数

	public function upgradeAccount ()
	{

		return View('admin.accountSetting.upgradeAccount');
	}

	//获取客户账户余额

	public function open_growUp_version_page ()
	{

		return View('admin.growUpVersionPage', compact('data'));
	}

	//获取该月的第一天

	public function open_vip_version_page ()
	{

		return View('admin.vipVersionPage', compact('data'));

	}

	//获取客户该月的消费金额

	public function charge_protocol_page ()
	{

		return View('admin.chargeProtocol', compact('data'));

	}

	//获取客户该月的存储使用量

	/*********************** 新充值页面 - start *************************/
	public function openNewGrowUpVersionPage ()
	{
		return View('admin.upgradeVersion.newGrowUpVersionPage');
	}

	//获取客户该月的短信发送量

	public function openNewVipVersionPage ()
	{
		return View('admin.upgradeVersion.newVipVersionPage');
	}

	//获取客户该月的累积播放流量

	public function get_recharge_page ()
	{

		$app_balance = $this->get_app_balance();    //账户余额

		return View('admin.rechargePage', compact('data', 'app_balance'));
	}

	//流量明细详情页

	public function flow_detail_list ()
	{
		$charge_at = Input::get('charge_at');
		$id        = Input::get('id');
		$fee_sum   = Input::get('fee_sum');

		//        $start_date = date("Y-m-d",strtotime("-1 day"))." 00:00:00";
		//        $end_date = date("Y-m-d",time())." 00:00:00";
		//从t_resource_reord中查询昨天的全部流量资源明细
		$result_list = \DB::connection('db_ex_finance')
			->table('t_resource_record')
			->where('detail_type', '=', '2')
			->where('app_id', '=', AppUtils::getAppID())
			->where('charge_at', '=', $charge_at)
			//            ->where('created_at','<',$end_date)
			->orderBy('day_datause', 'desc')
			->paginate(10);

		return VIEW('admin.flowDetailList', compact('result_list', 'charge_at', 'id', 'fee_sum'));
	}

	//存储量明细详情页
	public function storage_detail_list ()
	{
		//
		$charge_at = Input::get('charge_at');
		$id        = Input::get('id');
		$fee_sum   = Input::get('fee_sum');

		//        $start_date = date("Y-m-d",strtotime("-1 day"))." 00:00:00";
		//        $end_date = date("Y-m-d",time())." 00:00:00";
		//从t_resource_reord中查询昨天的全部存储量资源明细
		$result_list = \DB::connection('db_ex_finance')
			->table('t_resource_record')
			->where('detail_type', '=', '1')
			->where('app_id', '=', AppUtils::getAppID())
			->where('charge_at', '=', $charge_at)
			//            ->where('created_at','<',$end_date)
			->orderBy('day_storage', 'desc')
			->paginate(10);

		//        $orderid = Utils::getOrderId();
		return VIEW('admin.storageDetailList', compact('result_list', 'charge_at', 'id', 'fee_sum'));
	}

	//短信明细详情页面
	function sms_detail_list ()
	{

		$charge_at = Input::get('charge_at');
		$id        = Input::get('id');
		$fee_sum   = Input::get('fee_sum');
		$app_id    = AppUtils::getAppID();

		$start_time = $charge_at . " 00:00:00";
		$end_time   = $charge_at . " 23:59:59";

		//        $web_sms_total = \DB::select("select * from db_ex_business.t_verify_codes where created_at>='$start_time' and created_at<='$end_time' and app_id='$app_id'");
		$web_sms_total = \DB::table('db_ex_business.t_verify_codes')
			->where('created_at', '>=', $start_time)
			->where('created_at', '<=', $end_time)
			->where('app_id', '=', $app_id)
			->orderBy('created_at', 'desc')
			->paginate(10);
		if (count($web_sms_total) > 0) {
			foreach ($web_sms_total as $key => $web_sms) {

				//从表t_users中查询用户信息
				$userinfo = \DB::table('db_ex_business.t_users')
					->where('app_id', '=', $app_id)
					->where('user_id', '=', $web_sms->user_id)
					->first();
				if ($userinfo) {
					$userinfo->phone     = $web_sms->phone;
					$result_list[ $key ] = $userinfo;
				}
			}
		}

		return View('admin.smsDetailList', compact('web_sms_total', 'result_list', 'charge_at', 'id', 'fee_sum'));
	}

	//账户设置
	public function accountManage ()
	{
		$openid = AppUtils::getOpenId();

		//设置灰度用户"账户一览"可见
		$result_huidu = \DB::connection("mysql_config")->table('t_app_module')->where('app_id', '=', AppUtils::getAppID())->first();
		if ($result_huidu) {
			if ($result_huidu->is_show_accountview == 1) {
				session(["is_huidu" => 1]);
			} else {
				session(["is_huidu" => 0]);
			}
		} else {
			session(["is_huidu" => 0]);
		}
		session(["is_huidu" => 1]);

		//先取出注册信息
		$merchantConfig = \DB::connection("mysql_config")->select("
        select name,phone,wx_app_name from
        (
        select merchant_id from t_mgr_login where openid = ?
        )t1
        left join
        (
        select merchant_id,name,phone,wx_app_name from t_merchant_conf
        )t2
        on t1.merchant_id=t2.merchant_id
        where t2.merchant_id is not null", [$openid])[0];

		if ($merchantConfig->wx_app_name == '') {
			$merchantConfig->wx_app_name = '小鹅通知识店铺';
		}

		//再取出子账户信息
		$allInfo = \DB::connection("mysql_config")->table("t_admin_user")->select()
			->where("app_id", "=", AppUtils::getAppIdByOpenId($openid))
			->where("is_deleted", "=", 0)
			->orderBy("created_at", "desc")->paginate(10);
		$data    = [];
		if ($allInfo) {
			foreach ($allInfo as $key => $value) {
				$data[ $key ]['id']        = $value->id;
				$data[ $key ]['role_name'] = empty($value->role_name) ? '无' : $value->role_name;
				$data[ $key ]['username']  = $value->username;
				$data[ $key ]['adder']     = empty($value->adder) ? '无' : $value->adder;
			}
		}

		//最后取出管理员信息
		$admin = \DB::connection("mysql_config")->select("select name from t_mgr_login where openid = ?",
			[$openid])[0];

		$use_collection = DB::connection('mysql_config')->table('t_app_conf')->where('app_id', AppUtils::getAppID())->where('wx_app_type', 1)->value('use_collection');
		if ($use_collection == 0) {
			$model = 'company';
		} else {
			$model = 'person';
		}

		return View('admin.accountManage', compact('merchantConfig', 'allInfo', 'data', 'admin', 'model'));
	}

	public function childAccount ()
	{
		$openid = AppUtils::getOpenId();

		//设置灰度用户"账户一览"可见
		$result_huidu = \DB::connection("mysql_config")->table('t_app_module')->where('app_id', '=', AppUtils::getAppID())->first();
		if ($result_huidu) {
			if ($result_huidu->is_show_accountview == 1) {
				session(["is_huidu" => 1]);
			} else {
				session(["is_huidu" => 0]);
			}
		} else {
			session(["is_huidu" => 0]);
		}
		session(["is_huidu" => 1]);

		//先取出注册信息
		$merchantConfig = \DB::connection("mysql_config")->select("
        select name,phone,wx_app_name from
        (
        select merchant_id from t_mgr_login where openid = ?
        )t1
        left join
        (
        select merchant_id,name,phone,wx_app_name from t_merchant_conf
        )t2
        on t1.merchant_id=t2.merchant_id
        where t2.merchant_id is not null", [$openid])[0];

		if ($merchantConfig->wx_app_name == '') {
			$merchantConfig->wx_app_name = '小鹅通知识店铺';
		}

		//再取出子账户信息
		$allInfo = \DB::connection("mysql_config")->table("t_admin_user")->select()
			->where("app_id", "=", AppUtils::getAppIdByOpenId($openid))
			->where("is_deleted", "=", 0)
			->orderBy("created_at", "desc")->paginate(10);
		$data    = [];
		if ($allInfo) {
			foreach ($allInfo as $key => $value) {
				$data[ $key ]['id']        = $value->id;
				$data[ $key ]['role_name'] = empty($value->role_name) ? '无' : $value->role_name;
				$data[ $key ]['username']  = $value->username;
				$data[ $key ]['adder']     = empty($value->adder) ? '无' : $value->adder;
			}
		}

		//最后取出管理员信息
		$admin = \DB::connection("mysql_config")->select("select name from t_mgr_login where openid = ?",
			[$openid])[0];

		$use_collection = DB::connection('mysql_config')->table('t_app_conf')->where('app_id', AppUtils::getAppID())->where('wx_app_type', 1)->value('use_collection');
		if ($use_collection == 0) {
			$model = 'company';
		} else {
			$model = 'person';
		}

		return View('admin.accountSetting.childAccount', compact('merchantConfig', 'allInfo', 'data', 'admin', 'model'));
	}

	public function editWXName ()
	{
		$name   = Input::get('name');
		$openid = AppUtils::getOpenId();

		if ($name == '') {
			return response()->json(['code' => -1, 'msg' => '商户名不能为空']);
		}

		$merchant_id = \DB::connection("mysql_config")
			->table('t_mgr_login')
			->select('merchant_id')
			->where('openid', $openid)
			->first()->merchant_id;

		$update = \DB::connection("mysql_config")
			->table('t_merchant_conf')
			->where('merchant_id', $merchant_id)
			->update([
				'wx_app_name' => $name,
				'updated_at'  => date('Y-m-d H:i:s'),
			]);

		if ($update > 0) {
			return response()->json(['code' => 1, 'msg' => '保存成功']);
		} else {
			return response()->json(['code' => 0, 'msg' => '保存失败']);
		}
	}

	//小程序设置首页
	public function smallProgramSetting ()
	{
		return View('admin.smallProgramSetting');
	}

	//设置公众号支付信息页面
	public function set_wxpay_page ()
	{
		$h5 = \DB::connection("mysql_config")->table("t_app_conf")
			->where("app_id", "=", AppUtils::getAppIdByOpenId(AppUtils::getOpenId()))
			->where("wx_app_type", "=", 1)->first();

		return view('admin.editpayinfo', compact('h5'));
	}

	//个人模式
	public function personModel ()
	{
		$app_id = AppUtils::getAppID();
		$info   = DB::connection('mysql_config')->table('t_app_conf')
			->select('app_id', 'use_collection', 'isNewer')->where('wx_app_type', 1)->where('app_id', $app_id)->first();

		$info->url = AppUtils::getUrlHeader($app_id) . env('DOMAIN_DUAN_NAME') . "/{$app_id}/";

		return View('admin.accountSetting.personmodel', [
			'info' => $info,
		]);
	}

	//企业模式
	public function companyModel ()
	{
		$change = Input::get('change', '0');

		$h5 = \DB::connection("mysql_config")->table("t_app_conf")
			->where("app_id", "=", AppUtils::getAppIdByOpenId(AppUtils::getOpenId()))
			->where("wx_app_type", "=", 1)->first();

		$accessOpen = 0;

		//判断运营模式   0企业版 1 个人版
		if ($h5->use_collection == 0) {
			//判断认证是否有效
			$accessToken  = $h5->wx_access_token;
			$accessUpdate = $h5->wx_access_token_refresh_at;
			if (!AppUtils::isValidByTime($accessUpdate)) {
				// $accessToken过期了,去刷新下
				$remote_url = env('BUZ_HOST') . '/require/refresh_access_token/' . $h5->app_id;
				$curl       = Utils::curl_file_post_contents($remote_url);
				if ($curl) {
					$h5          = \DB::connection("mysql_config")->table("t_app_conf")
						->where("app_id", "=", AppUtils::getAppIdByOpenId(AppUtils::getOpenId()))
						->where("wx_app_type", "=", 1)->first();
					$accessToken = $h5->wx_access_token;
				}
			}
			$url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=' . $accessToken;

			try {
				$jsonResult = file_get_contents($url);
			} catch (\Exception $e) {
				return null;
			}

			$resultArray = json_decode($jsonResult, true);
			if (count($resultArray) == 2) {
				$accessOpen = 1;
			}
			//            dump($resultArray,count($resultArray));
			//            exit();
			return View('admin.accountSetting.bindcompanymodel', compact('h5', 'accessOpen'));
		} else {
			//判断是否已绑定
			if ($change == 1) {
				return View('admin.accountSetting.bindcompanymodel', compact('h5', 'accessOpen'));
			} else {
				return View('admin.accountSetting.companymodel', compact('h5', 'accessOpen'));
			}
		}
	}

	//更新商户的运营模式(变更为个人版)
	public function updateCollection ()
	{
		$app_id = Input::get("app_id");
		//TODO:更新表t_app_conf中use_collection的值为1即个人运营模式
		$update = \DB::connection("mysql_config")->update("update t_app_conf set use_collection='1',updated_at=?,bind_at=?
        where app_id=? and wx_app_type='1' limit 1", [Utils::getTime(), Utils::getTime(), $app_id]);
		if ($update) {
			session(['is_collection' => 1]);

			return response()->json(['ret' => 0]);
		} else {
			return response()->json(['ret' => 1]);
		}
	}

	//更改授权态
	public function updateIfAuth ()
	{
		$app_id = Input::get("app_id");
		$update = \DB::connection("mysql_config")->update("update t_app_conf set ifauth='1',updated_at=?
        where app_id=? and wx_app_type='1' limit 1", [Utils::getTime(), $app_id]);
		if ($update) {
			return response()->json(['ret' => 0]);
		} else {
			return response()->json(['ret' => 1]);
		}
	}

	public function checkAuthResult ()
	{
		$app_id    = Input::get("app_id");
		$is_update = \DB::connection("mysql_config")
			->table('t_app_conf')
			->where('app_id', $app_id)
			->where('wx_app_type', '=', 1)
			->where('wx_app_id', '!=', '')
			->get();
		if (count($is_update)) {
			return response()->json(['ret' => 0]);
		} else {
			return response()->json(['ret' => 1]);
		}

	}

	//绑定账号密码
	public function bind ()
	{
		$phone       = trim(Input::get("phoneInSetting"));
		$password    = Hash::make(trim(Input::get("password")));
		$oldpassword = trim(Input::get("oldpassword"));
		$openid      = AppUtils::getOpenId();
		if ($oldpassword)//判断现有密码是否输入正确
		{
			$exist = \DB::connection("mysql_config")->select("select * from t_mgr_login where openid = ?", [$openid]);
			if ($exist) {
				if (Hash::check($oldpassword, $exist[0]->password)) {
				} else {
					return response()->json(['ret' => -1]);
				}
			}
		}
		$update = \DB::connection("mysql_config")->
		update("update t_mgr_login set name = ?,password = ? where openid = ? ", [$phone, $password, $openid]);
		if ($update >= 0) {
			return response()->json(['ret' => 0]);
		} else {
			return response()->json(['ret' => 1]);
		}
	}

	//更新微信信息
	public function updateWxInfo ()
	{
		$data = Input::get("params");

		$app_id = AppUtils::getAppIdByOpenId(AppUtils::getOpenId());

		$record = \DB::connection("mysql_config")->table("t_app_conf")
			->where('app_id', '=', $app_id)
			->where('wx_app_type', 1)
			->first();

		//如果不存在微信h5的记录则插入
		if (empty($record)) {
			DB::connection('mysql_config')->insert("
insert into t_app_conf
(
app_id, wx_app_type, secrete_key, set_id, merchant_id, wx_app_name,
wx_access_token_refresh_at, wx_js_ticket,
wx_share_title, wx_share_content, wx_share_image, wx_qr_url, wx_share_image_compressed, wx_qr_url_compressed, created_at
)
select
app_id, 1,  secrete_key, set_id, merchant_id, wx_app_name,
wx_access_token_refresh_at, wx_js_ticket,
wx_share_title, wx_share_content, wx_share_image, wx_qr_url, wx_share_image_compressed, wx_qr_url_compressed, now()
FROM t_app_conf where app_id = '$app_id' and wx_app_type = 0
;
            ");
		}

		if (array_key_exists('wx_app_id', $data) && empty($record->wx_app_id)) {
			$data['bind_at'] = Utils::getTime();
		}
		//压缩图片 1 微信
		if (array_key_exists('wx_share_image', $data)) {
			$this->imageDeal($data['wx_share_image'], 't_app_conf', 1, 200, 200, 60, 'wx_share_image_compressed');
		}
		if (array_key_exists('wx_qr_url', $data)) {
			$this->imageDeal($data['wx_qr_url'], 't_app_conf', 1, 100, 100, 60, 'wx_qr_url_compressed');
		}

		$update = DB::connection("mysql_config")->table("t_app_conf")
			->where('app_id', '=', $app_id)
			->where('wx_app_type', 1)
			->update($data);
		if ($update >= 0) {
			//成功入驻则更新接入状态session
			if (array_key_exists('wx_app_id', $data) && array_key_exists('wx_mchid', $data) && array_key_exists('wx_mchkey', $data)) {
				session(['wxapp_join_statu' => AppUtils::getWxAppJoinStatus(session('openid'))]);
			}
			$wx_secrete_key = DB::connection('mysql_config')->table('t_app_conf')
				->select('wx_secrete_key')
				->where('app_id', $app_id)
				->where('wx_app_type', 1)
				->first();

			if (!empty($wx_secrete_key->wx_secrete_key)) {
				return response()->json(['ret' => 0]);
			}

			return response()->json(['ret' => 0, 'url' => env('AUTH_PAGE_URL')]);
		} else {
			return response()->json(['ret' => 1]);
		}
	}

	/**
	 *图片处理
	 *
	 * @param $image_url
	 * @param $table_name
	 *                       & param $image_field 关联字段
	 * @param $image_id
	 * @param $image_width   压缩尺寸 宽度 (默认 160)
	 * @param $image_height  压缩尺寸 高度 (默认 120)
	 * @param $image_quality 压缩参数 质量值 (默认 60)
	 * @param $compressed    缩略图存储字段
	 *                       & db 存储数据库
	 */
	public function imageDeal ($image_url, $table_name, $image_id, $image_width, $image_height, $image_quality, $compressed)
	{
		$host_url = env('HOST_URL');
		$app_id   = AppUtils::getAppID();

		//压缩尺寸设定
		$image_width   = $image_width ? $image_width : 100;
		$image_height  = $image_height ? $image_height : 100;
		$image_quality = $image_quality ? $image_quality : 60;
		Utils::asyncThread($host_url . '/downloadImaged?image_field=wx_app_type&image_id=' . $image_id . '&app_id='
			. $app_id . '&table_name=' . $table_name . '&file_url=' . $image_url
			. '&image_width=' . $image_width . '&image_height=' . $image_height . '&image_quality=' . $image_quality
			. '&compressed=' . $compressed . '&db=mysql_config');;

	}

	/**
	 * 更新小程序信息
	 */
	public function updateSmallProgram ()
	{
		$data = Input::get("params");

		$app_id = AppUtils::getAppIdByOpenId(AppUtils::getOpenId());

		$record = DB::connection("mysql_config")->table("t_app_conf")
			->where('app_id', '=', $app_id)
			->where('wx_app_type', 0)
			->first();

		//如果不存在微信小程序的记录则插入
		if (empty($record)) {
			DB::connection('mysql_config')->insert("
insert into t_app_conf
(
app_id, wx_app_type, secrete_key, set_id, merchant_id, wx_app_name,
wx_access_token_refresh_at, wx_js_ticket,
wx_share_title, wx_share_content, wx_share_image, wx_qr_url, wx_share_image_compressed, wx_qr_url_compressed, created_at
)
select
app_id, 0,  secrete_key, set_id, merchant_id, wx_app_name,
wx_access_token_refresh_at, wx_js_ticket,
wx_share_title, wx_share_content, wx_share_image, wx_qr_url, wx_share_image_compressed, wx_qr_url_compressed, now()
FROM t_app_conf where app_id = '$app_id' and wx_app_type = 1
;
            ");
		}

		//压缩图片 0 小程序
		if (array_key_exists('wx_share_image', $data)) {
			$this->imageDeal($data['wx_share_image'], 't_app_conf', 0, 200, 200, 60, 'wx_share_image_compressed');
		}
		if (array_key_exists('wx_qr_url', $data)) {
			$this->imageDeal($data['wx_qr_url'], 't_app_conf', 0, 100, 100, 60, 'wx_qr_url_compressed');
		}

		$update = \DB::connection("mysql_config")->table("t_app_conf")
			->where('app_id', '=', $app_id)
			->where('wx_app_type', 0)
			->update($data);
		if ($update >= 0) {
			$wx_secrete_key = DB::connection('mysql_config')->table('t_app_conf')
				->select('wx_secrete_key')
				->where('app_id', $app_id)
				->where('wx_app_type', 0)
				->first();

			if (!empty($wx_secrete_key->wx_secrete_key)) {
				return response()->json(['ret' => 0]);
			}

			return response()->json(['ret' => 0, 'url' => env('AUTH_PAGE_URL')]);
		} else {
			return response()->json(['ret' => 1]);
		}
	}

	//新增主账号

	public function doAddPrimary ()
	{
		$addName     = Input::get("addName");
		$addPassword = Input::get("addPassword");
		DB::beginTransaction();
		try {
			$exsit = \DB::connection("mysql_config")->select("select username from t_admin_user where
            username = ? and is_deleted = 0", [$addName]);
			if ($exsit) {
				return response()->json(['ret' => 1]);
			}

			$update = \DB::connection("mysql_config")->update("update t_mgr_login set name = ?,password = ?
            where openid = ? limit 1", [$addName, Hash::make($addPassword), AppUtils::getOpenId()]);
			DB::commit();

			return response()->json(['ret' => 0]);
		} catch (\Exception $e) {
			DB::rollback();

			return response()->json(['ret' => 1]);
		}

	}

	//更新主账号密码
	public function updatePrimary ()
	{
		$editName        = Input::get("editName");
		$editOldPassword = Input::get("editOldPassword");
		$editNewPassword = Input::get("editNewPassword");
		//先检验旧密码是否正确
		$oldCheck = \DB::connection("mysql_config")->
		select("select password from t_mgr_login where name = ?", [$editName]);
		if (Hash::check($editOldPassword, $oldCheck[0]->password)) {
			$update = \DB::connection("mysql_config")->update("update t_mgr_login set password = ?
            where name = ? limit 1", [Hash::make($editNewPassword), $editName]);
			if ($update >= 0) {
				return response()->json(['ret' => 0]);
			} else {
				return response()->json(['ret' => 1]);
			}
		} else {
			return response()->json(['ret' => 2]);
		}
	}

	//更新商户信息(变为企业版)
	public function updateMerchant ()
	{
		$wx_mchid  = Input::get("wx_mchid");
		$wx_mchkey = Input::get("wx_mchkey");
		$app_id    = Input::get("app_id");
		$update    = \DB::connection("mysql_config")->update("update t_app_conf set wx_mchid=?,wx_mchkey=?,use_collection=0,pay_directory_verified=0
        where app_id=? and wx_app_type='1' limit 1", [$wx_mchid, $wx_mchkey, $app_id]);
		if ($update >= 0) {
			//向app端发送验证支付配置的请求
			//            $wholeUrl = env('APP_HTTPS')."/".$app_id."/1";
			//发包
			//            $ch = curl_init();
			//            curl_setopt($ch, CURLOPT_URL, $wholeUrl);
			//            curl_setopt($ch, CURLOPT_HEADER, 0);
			//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			//            curl_setopt($ch, CURLOPT_POST, 1);
			//            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			//            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			//            $ret = curl_exec($ch);
			//            curl_close($ch);
			$wholeUrl = env('APP_HTTPS') . "/" . $app_id . "/1";


			$jsonString = file_get_contents($wholeUrl);
			//            dump($jsonString);
			$ret = json_decode($jsonString);
			//            dump($ret);
			//            exit;
			if (array_key_exists('code', $ret)) {
				if ($ret->code == 0) {
					$wx_app_id = DB::connection("mysql_config")->table('t_app_conf')
						->where('app_id', '=', $app_id)
						->where('wx_app_type', '=', 1)
						->where('use_collection', '=', '0')
						->first();
					if (!empty($wx_app_id)) {
						$url = AppUtils::getUrlHeader(AppUtils::getAppID()) . $wx_app_id->wx_app_id . env("DOMAIN_NAME");

						return response()->json(['ret' => 0, 'url' => $url]);
					} else {

						return response()->json(['ret' => 0]);
					}
				} else {
					//将支付配置的相关信息清空
					$update = \DB::connection("mysql_config")->update("update t_app_conf set wx_mchid=?,wx_mchkey=?,use_collection=0
        where app_id=? and wx_app_type='1' limit 1", ['', '', $app_id]);


					return response()->json(['ret' => -1, 'msg' => "输入的微信商户API密钥错误,请确认"]);
				}
			} else {
				return response()->json(['ret' => -2, 'msg' => "保存失败"]);
			}
		} else {
			return response()->json(['ret' => -3, 'msg' => "更新商户信息失败,请重试"]);
		}
	}

	public function huidu ()
	{

		return view('admin.huidu');
	}

	// 查询用户信息:通过手机号
	public function query_account_by_phone ()
	{
		$phone = Input::get("phone", '');
		if (!Utils::isValidPhoneNumber($phone)) {
			return response()->json(Utils::pack("", -1, "手机号格式错误"));
			//            $result_merchant = $this->getMerchartIdByname($phone);
		} else {
			$result_merchant = $this->getMerchartIdByphone($phone);

		}
		//在表t_merchant_conf中通过phone查找用户的merchant_id
		if ($result_merchant) {
			return response()->json(['code' => 0, 'data' => $result_merchant]);
		} else {
			return response()->json(Utils::pack("", -2, "暂无数据"));
		}
	}

	//设置灰度

	private function getMerchartIdByphone ($phone)
	{
		$result_merchant = DB::connection('mysql_config')->select("
select t1.name, t1.company, t1.phone, t2.use_collection, t1.merchant_id, t2.wx_app_id, t2.wx_mchid from (
select * from t_merchant_conf where phone = '$phone'
) t1 join t_app_conf t2 on t1.merchant_id = t2.merchant_id and t2.wx_app_type = 1
");

		if ($result_merchant && count($result_merchant) > 0) {
			$data                   = [];
			$data['name']           = $result_merchant[0]->name;
			$data['company']        = $result_merchant[0]->company;
			$data['phone']          = $result_merchant[0]->phone;
			$data['use_collection'] = $result_merchant[0]->use_collection;
			$data['wx_app_id']      = $result_merchant[0]->wx_app_id;
			$data['wx_mchid']       = $result_merchant[0]->wx_mchid;
			$data['merchant_id']    = $result_merchant[0]->merchant_id;

			return $data;
		} else {
			return 0;
		}
	}
	//    public function delete_account_by_phone(){
	//        $phone = Input::get("phone",'');
	//        if(!Utils::isValidPhoneNumber($phone)){
	////            return response()->json(Utils::pack("",-1, "手机号格式错误"));
	//            $merchat = $this->getMerchartIdByname($phone);
	//        }else{
	//            $merchat = $this->getMerchartIdByphone($phone);
	//
	//        }
	//
	////        $merchat = $this->getMerchartIdByphone($phone);
	//        if($merchat){
	//            \DB::beginTransaction();
	//
	//            //删除t_app_conf中数据
	//            $result1 = \DB::delete("delete from db_ex_config.t_app_conf  where merchant_id = '$merchat->merchant_id' ");
	//            if($result1){
	//                //删除t_merchant_conf表中数据
	//                $result2 = \DB::delete("delete from db_ex_config.t_merchant_conf  where merchant_id = '$merchat->merchant_id' ");
	//                $result3 = \DB::delete("delete from db_ex_config.t_mgr_login  where merchant_id = '$merchat->merchant_id' ");
	//
	//                $app_id = $this->getAppIdByMerchantId($merchat->merchant_id);
	//                $result4 = \DB::delete("delete from db_ex_finance.t_balance_charge  where app_id = '$app_id' ");
	//                $result5 = \DB::delete("delete from db_ex_finance.t_resource_record  where app_id = '$app_id' ");
	//
	//                if($result2&&$result3){
	//                    \DB::commit();
	//                    return $this->result(1);
	//
	//                }else{
	//                    \DB::rollBack();
	//
	//                    return response()->json(Utils::pack("",-1, "删除失败"));
	//                }
	//
	//            }else{
	//                \DB::rollBack();
	//
	//                return response()->json(Utils::pack("",-1, "删除失败"));
	//            }
	//        }
	//    }

	public function set_huidu_by_phone ()
	{
		$phone = Input::get("phone", '');
		$type  = Input::get("type", '');
		if (!Utils::isValidPhoneNumber($phone)) {
			return response()->json(Utils::pack("", -1, "手机号格式错误"));
			//            $merchat = $this->getMerchartIdByname($phone);
		} else {
			$merchat = $this->getMerchartIdByphone($phone);

		}

		//        $merchat = $this->getMerchartIdByphone($phone);
		if ($merchat) {
			$merId = $merchat['merchant_id'];
			if ($merchat['use_collection'] == 1) {
				return response()->json(Utils::pack("", -1, "已经是个人模式"));
			}
			if (!empty($merchat['wx_app_id'])) {
				return response()->json(Utils::pack("", -1, "绑定过微信公众号"));
			}
			if (!empty($merchat['wx_mchid'])) {
				return response()->json(Utils::pack("", -1, "绑定过商户信息"));
			}

			if ($type == 0) {
				//设置用户的use_collection为521即灰度用户
				$result = \DB::connection("mysql_config")->update("
update t_app_conf set use_collection = 1
where merchant_id = '$merId' and wx_app_type=1
and wx_app_id is not NULL and wx_mchid is not null and wx_mchkey is not NULL
");
				if ($result) {
					return $this->result(1);
				} else {
					return response()->json(Utils::pack("", -1, "更新失败"));
				}
			} else if ($type == 1) {
				//设置用户的is_show_accountview为1即灰度用户
				//先查询是否存在该用户记录t_app_module
				$app_id = $this->getappIdByMerId($merId);
				if ($app_id) {
					$result_module = \DB::connection('mysql_config')->table('t_app_module')->where('app_id', '=', $app_id)->first();
					if ($result_module) {
						if ($result_module->is_show_accountview == 0) {
							$result = \DB::connection("mysql_config")->update("update t_app_module set is_show_accountview = 1 where app_id = '$app_id' ");
							if ($result) {
								return $this->result(1);
							} else {
								return response()->json(Utils::pack("", -1, "更新失败"));
							}
						} else {
							return response()->json(Utils::pack("", -1, "该用户之前已经开启了!请勿重复开启!"));
						}

					} else {
						//insert一条新数据
						$params['app_id']              = $app_id;
						$params['is_show_accountview'] = 1;
						$params['created_at']          = Utils::getTime();
						$params['updated_at']          = Utils::getTime();
						$result_insert                 = \DB::connection('mysql_config')->table('t_app_module')->insert($params);
						if ($result_insert) {
							return $this->result(1);

						} else {
							return response()->json(Utils::pack("", -1, "更新失败"));
						}
					}
				} else {
					return response()->json(Utils::pack("", -1, "app_id为空"));

				}

			}

		}

	}

	public function result ($data)
	{
		if (Utils::isEmptyString($data)) {
			return response()->json(Utils::pack("", StringConstants::Code_Failed, StringConstants::Msg_Failed));
		} else {
			return response()->json(Utils::pack($data));
		}
	}

	private function getappIdByMerId ($merId)
	{
		$result_appId = \DB::connection('mysql_config')->table('t_app_conf')->where('merchant_id', '=', $merId)->where('wx_app_type', '=', 1)->first();
		if ($result_appId) {
			return $result_appId->app_id;
		} else {
			return 0;
		}
	}

	/**
	 * 检查升级成长版、专业版或充值的订单有无遗漏
	 */
	function check_orders_state ()
	{
		//  根据appId取出所有订单（状态为未支付）

		//  检查每一个订单的状态是否正确

		//  全部正确，则不用处理；否则一个一个处理
	}

	public function confirmOrderGetPaytype ()
	{
		$r_id         = Input::get('r_id');  //资源id
		$app_id       = AppUtils::getAppID();
		$out_order_id = \DB::table('t_orders')
			->where('resource_id', '=', $r_id)
			->where('order_state', '=', 1)
			->where('app_id', '=', $app_id)
			->value('out_order_id');

		$type           = \DB::table('t_out_orders')
			->where('out_order_id', '=', $out_order_id)
			->where('app_id', '=', $app_id)
			//        ->where('pre_pay_state','=',1)
			->value('type');
		$use_collection = \DB::table('t_out_orders')
			->where('out_order_id', '=', $out_order_id)
			->where('app_id', '=', $app_id)
			//        ->where('pre_pay_state','=',1)
			->value('use_collection');

		return response()->json(['type' => $type, 'use_collection' => $use_collection]);

	}

	public function confirmOrder ()
	{ //第二步

		//验证支付信息  设置 新字段pay_directory_verified 为 1
		$r_id = Input::get('r_id');

		$app_id = AppUtils::getAppID();

		//        $app_info = DB::connection("mysql_config")->table('t_app_conf')
		//            ->where('app_id','=',$app_id)
		//            ->where('wx_app_type','=',1)
		//            ->first();
		//        if($app_info -> pay_directory_verified == '1')
		//            return response()->json(['code' => 32,'msg' => '您已经验证成功']); //已经验证支付信息

		$update = \DB::connection("mysql_config")->update("update t_app_conf set pay_directory_verified = 1
        where app_id=? and wx_app_type='1' limit 1", [$app_id]);

		if ($update > 0)
			return response()->json(['code' => 0, 'msg' => '您已经验证成功 谢谢']);
		else
			return response()->json(['code' => 1, 'msg' => '服务器繁忙 请稍后再试']);

	}



	// 旧登陆注册逻辑  弃用
	//
	//    //退出登录
	//    public function loginOut()
	//    {
	//        AppUtils::clearLoginStatus();
	//        Cookie::forget("with_app_id");
	//        return response()->json(['ret' => 0]);
	//    }
	//    //发送验证码
	//    public function sendMsg()
	//    {
	//        $phone = trim(Input::get("phone"));//手机号码
	//        $checkCode = random_int(100000, 999999);//验证码
	//        $minutes = '5';//失效分钟
	//
	//        $sdkappid = "1400014102";
	//        $appkey = "878d0c777c06a29eff31a6302d4140f7";
	//        $rnd = random_int(100000, 999999);
	//        $wholeUrl = "https://yun.tim.qq.com/v3/tlssmssvr/sendsms?sdkappid=" . $sdkappid . "&random=" . $rnd;
	//
	//        $tel = new stdClass();
	//        $tel->nationcode = "86";
	//        $tel->phone = $phone;
	//        $jsondata = new stdClass();
	//        $jsondata->tel = $tel;
	//        $jsondata->type = "0";
	//        $jsondata->msg = $checkCode . "为您的登录验证码，请于" . $minutes . "分钟内填写。如非本人操作，请忽略本短信。";
	//        $jsondata->sig = md5($appkey . $phone);
	//        $jsondata->extend = "";     // 根据需要添加，一般保持默认
	//        $jsondata->ext = "";        // 根据需要添加，一般保持默认
	//        //包体
	//        $curlPost = json_encode($jsondata);
	//        //发包
	//        $ch = curl_init();
	//        curl_setopt($ch, CURLOPT_URL, $wholeUrl);
	//        curl_setopt($ch, CURLOPT_HEADER, 0);
	//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	//        curl_setopt($ch, CURLOPT_POST, 1);
	//        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
	//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	//        $ret = curl_exec($ch);
	//
	//        if ($ret === false) {
	//            return response()->json(['ret' => 1]);
	//        } else {
	//            //插验证码的表
	//            $codeInfo = [];
	//            $codeInfo['openid'] = AppUtils::getOpenId();
	//            $codeInfo['type'] = '0';
	//            $codeInfo['used'] = '0';
	//            $codeInfo['phone'] = $phone;
	//            $codeInfo['code'] = $checkCode;
	//            $codeInfo['expire_at'] = date('Y-m-d H:i:s', time() + 5 * 60);
	//            $codeInfo['created_at'] = date('Y-m-d H:i:s', time());
	//            $insert = \DB::connection("mysql_config")->table("t_mgr_verify_codes")->insertGetId($codeInfo);
	//            return response()->json(['ret' => 0]);
	//        }
	//    }
	//    //校验短信码
	//    public function identify()
	//    {
	//        $check = \DB::connection("mysql_config")->
	//        select("select id from t_mgr_verify_codes where openid = ? and used = '0' and phone =?
	//        and code = ? and expire_at > now()",
	//            [AppUtils::getOpenId(), trim(Input::get("phoneInIdentify")), trim(Input::get("code"))]);
	//        if ($check) {
	//            return response()->json(['ret' => 0]);
	//        } else {
	//            return response()->json(['ret' => 1]);
	//        }
	//    }
	//    //登录首页
	//    public function login(){
	//        $type = Input::get("type");
	//        $mobileDetect = new Mobile_Detect();
	//        $isMobile = $mobileDetect->isMobile();
	//        //体验账号
	//        if ($type == 1) {
	//
	//            return view('admin.login', compact('type','isMobile'));
	//        } else {
	//            //普通登录 或者 体验账号再次登录
	//            if (empty(session("app_id")) || session("app_id") == env('TEST_APP_ID')) {
	//                return view('admin.login', compact('type','isMobile'));
	//            } else {  //正常账号再次登陆
	//                $version_type = Input::get("version_type", null);
	//                $current_type = session("version_type", null);
	//
	//                if ($version_type > $current_type) {
	//                    if ($version_type == 2) {
	//                        return redirect('/open_growUp_version_page');
	//                    } elseif ($version_type == 3) {
	//                        return redirect('/open_vip_version_page');
	//                    }
	//                } else {
	//                    return redirect('/accountview');
	//                }
	//            }
	//        }
	//    }
	//    //微信二维码登录,分新老用户以及是否有balance_type共4种情况
	//    public function codeinfo(Request $request){
	////        dump($request->all());
	////        array:3 [▼
	////  "version_type" => "null"
	////  "code" => "021WDqDV0Xn2BW1KGSFV0wwyDV0WDqDf"
	////  "state" => ""
	////]
	////        exit;
	//        $code = $_GET["code"];//扫码
	//        $version_type=$_GET["version_type"];//购买套餐类型
	//
	//        //根据code获取access_token和openid
	//        $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".env('QRCODE_APP_ID', '')
	//            ."&secret=".env('QRCODE_SECRET', '')."&code=".$code."&grant_type=authorization_code";
	//        $resultArray = json_decode(file_get_contents($url), true);
	////        dump($resultArray);
	//
	////        dd($resultArray);
	//        //根据access_token和openid获取用户所有信息
	//        $infoUrl="https://api.weixin.qq.com/sns/userinfo?access_token=".$resultArray["access_token"]
	//            ."&openid=".$resultArray["openid"];
	//        $infoArray = json_decode(file_get_contents($infoUrl), true);
	////        dump($infoArray);
	////        exit;
	//
	////        array:10 [▼
	////  "openid" => "omJa5v0Vb6vVyhzp0JB94EuNnPyg"
	////  "nickname" => "Marcus"
	////  "sex" => 1
	////  "language" => "zh_CN"
	////  "city" => "Luoyang"
	////  "province" => "Henan"
	////  "country" => "CN"
	////  "headimgurl" => "http://wx.qlogo.cn/mmopen/ajNVdqHZLLDlwJeh3llBmgzO8ia8uw2x04krb9AawSVgITf36VEgLfZvmMbziak53txFrqR2Ho1qheAmM4MsnYAA/0"
	////  "privilege" => []
	////  "unionid" => "ozStBt7DM1KUCr8tiy7kT68PRe-A"
	////]
	////        dump($infoArray);
	////        exit;
	//
	//        //判断扫码用户是否超级管理员
	//        if (SuperUtils::checkIsAdmin($infoArray["openid"]))
	//        {
	//            AppUtils::setSuperOpenId($infoArray["openid"]);
	//            return redirect('/to_super_page');
	//        }
	//
	//        //获取扫码者信息并插入
	//        $mgrLogin = [];
	//        $mgrLogin['union_id'] = $infoArray["unionid"];
	//        $mgrLogin['openid'] = $infoArray["openid"];
	//        $mgrLogin['nick_name'] = $infoArray["nickname"];
	//        $mgrLogin['gender'] = $infoArray["sex"];
	//        $mgrLogin['logo'] = $infoArray["headimgurl"];
	//        $mgrLogin['created_at'] = Utils::getTime();
	//        $mgrLogin['updated_at'] = Utils::getTime();
	////        var_dump($_COOKIE);
	////        dd($request);
	//        if (array_key_exists('channel',$_COOKIE)) {
	//            $mgrLogin['channel'] =  $_COOKIE['channel'];
	//        } else {
	//            $mgrLogin['channel'] = "unknown";
	//        }
	//
	//        $mgr_info = DB::connection('mysql_config')->table('t_mgr_login')->where('openid',$infoArray['openid'])->first();
	//        if ($mgr_info){
	//            if (!$mgr_info->union_id){
	//                DB::connection('mysql_config')->table('t_mgr_login')->where('openid',$infoArray['openid'])->update(['union_id'=>$infoArray['unionid']]);
	//            }
	//        }
	////        dd($mgrLogin);
	//        try {
	//            $insert = \DB::connection("mysql_config")->table("t_mgr_login")->insert($mgrLogin);
	//        } catch(\Exception $e)
	//        {}
	//
	//        //设置登录态,先看有没有红包模块的功能
	//        AppUtils::setLoginStatus($mgrLogin['openid'], $mgrLogin['nick_name'], $mgrLogin['gender'], $mgrLogin['logo'],
	//            AppUtils::getAccessAuth(AppUtils::getAppIdByOpenId($mgrLogin['openid']),null,0));
	//        AppUtils::setScanOpenid($mgrLogin['openid']);
	//
	//        //判断用户是否注册
	//        $ifSign = \DB::connection("mysql_config")->select("select merchant_id from t_mgr_login where openid = ?", [$mgrLogin['openid']]);
	//
	//        if (empty($ifSign[0]->merchant_id)) {   //未注册
	//            return redirect('/sign?version_type='.$version_type);
	//        } else {
	//            if($version_type == 2) {
	//                if(session("version_type") < 2) {
	//                    return redirect('/open_growUp_version_page');
	//                } else {
	//                    return redirect('/accountview');
	//                }
	//            } elseif($version_type == 3) {
	//                if(session("version_type") < 3) {
	//                    return redirect('/open_vip_version_page');
	//                } else {
	//                    return redirect('/accountview');
	//                }
	//            } else {
	//                return redirect('/accountview');
	//            }
	//        }
	//    }
	//    //微信黑色二维扫码页面
	//    public function signUp()
	//    {
	//        $redirect_uri = env('QRCODE_REDIRECT_URL', '');
	//        $redirect_uri = urlencode($redirect_uri);
	//        $appID = env('QRCODE_APP_ID', '');
	//        $scope = "snsapi_login";
	//        $url = "https://open.weixin.qq.com/connect/qrconnect?appid=" . $appID
	//            . "&redirect_uri=" . $redirect_uri
	//            . "&response_type=code&scope=" . $scope
	//            . "&state=STATE#wechat_redirect";
	//        $result = file_get_contents($url);
	//        //替换文本
	//        $result = str_replace("/connect/qrcode/", "https://open.weixin.qq.com/connect/qrcode/", $result);
	//        return $result; //返回页面
	//    }
	//    //登陆操作
	//    public function doLogin()
	//    {
	//        $username = trim(Input::get("username"));
	//        $password = trim(Input::get("password"));
	//        if (empty($username) || empty($password))
	//        {
	//            return response()->json(['ret' => 2]);
	//        }
	//        //从主账号判断
	//        $exist = \DB::connection("mysql_config")->select("select * from t_mgr_login where name = ?", [$username]);
	//        if ($exist) {
	//            if (Hash::check($password, $exist[0]->password)) {
	//                AppUtils::setLoginStatus($exist[0]->openid, $exist[0]->nick_name, $exist[0]->gender, $exist[0]->logo,
	//                    AppUtils::getAccessAuth(AppUtils::getAppIdByOpenId($exist[0]->openid),null,0));
	//                if($username == 'test'){//测试账号头像特殊化
	//                    session(['wx_app_name' => env('TEST_NICK_NAME', '')]);
	//                    session(['wx_share_image' => env('TEST_LOGO', '')]);
	//                }
	//                return response()->json(['ret' => 0,'current_version_type'=>session("version_type")]);
	//            } else {
	//                return response()->json(['ret' => 1]);
	//            }
	//        }else {//从子账号表判断
	//            $otherCheck = \DB::connection("mysql_config")->select("select * from t_admin_user
	//            where username = ? and is_deleted = 0 ", [$username]);
	//            if($otherCheck) {
	//                if (Hash::check($password, $otherCheck[0]->password_encrypt)) {
	//                    //先去找主账户信息
	//                    $loginInfo=\DB::connection("mysql_config")->select("
	//                    select openid,nick_name,gender,logo from
	//                    (
	//                    select merchant_id,app_id from t_app_conf where app_id = ? and wx_app_type = '1'
	//                    )t1
	//                    left join
	//                    (
	//                    select merchant_id,openid,nick_name,gender,logo from t_mgr_login
	//                    )t2
	//                    on t1.merchant_id=t2.merchant_id
	//                    where t2.merchant_id is not null",[$otherCheck[0]->app_id]);
	//
	//                    if(count($loginInfo) == 0){
	//                        return response()->json(['ret' => 1]);
	//                    }
	//
	//                    AppUtils::setLoginStatus($loginInfo[0]->openid, $loginInfo[0]->nick_name, $loginInfo[0]->gender,
	//                        $loginInfo[0]->logo,AppUtils::getAccessAuth($otherCheck[0]->app_id,$otherCheck[0]->group_id,1));
	//                    AppUtils::setSubName($username);
	//                    if($otherCheck[0]->app_id == env("TEST_APP_ID"))//测试账号特殊化
	//                    {
	//                        session(['wx_app_name' => env('TEST_NICK_NAME', '')]);
	//                        session(['wx_share_image' => env('TEST_LOGO', '')]);
	//                    }
	//                    return response()->json(['ret' => 0,'current_version_type'=>session("version_type")]);
	//                } else {
	//                    return response()->json(['ret' => 1]);
	//                }
	//            } else {
	//                return response()->json(['ret' => 1]);
	//            }
	//        }
	//    }
	//    //注册页面
	//    public function sign()
	//    {
	//        return View('admin.sign');
	//    }
	//    //新增注册
	//    public function identifySubmit()
	//    {
	//        //小鹅通体验号不让注册
	//        if(AppUtils::getOpenId() == env("TEST_OPENID"))
	//        {
	//            AppUtils::setLoginStatus("","","","","");
	//            return redirect("/login");
	//        }
	//
	//        //获取扫码主账号信息,判断客户是否重复注册
	//        $mgr_config_info = AppUtils::getMgrLoginInfo(AppUtils::getOpenId());
	//        if($mgr_config_info){
	//            if(empty($mgr_config_info->merchant_id)){
	//
	//                //插入商户配置表
	//                $merchantConfig=[];
	//                $merchantConfig['merchant_id']='mch'.str_random(8);
	//                $merchantConfig['certify_status']='2';
	//                $merchantConfig['name']=trim(Input::get("contactPerson"));
	//                $merchantConfig['phone']=trim(Input::get("phoneInIdentify"));
	//                $merchantConfig['wx_app_name']=trim(Input::get("officialAccount"));
	//                $merchantConfig['created_at']=Utils::getTime();
	//                $insertMerchant=\DB::connection("mysql_config")->table("t_merchant_conf")->insert($merchantConfig);
	//
	//                //插入应用配置表,2条
	//                $appConfig=[];
	//                $appConfig['app_id']='app'.str_random(8).random_int(1000, 9999);
	//                $appConfig['wx_app_type']=0;
	//                $appConfig['secrete_key']=str_random(32);
	//                $appConfig['merchant_id']=$merchantConfig['merchant_id'];
	//                $appConfig['wx_app_name']=trim(Input::get("officialAccount"));
	//                $appConfig['balance']=5000;
	//                $appConfig['version_type']=1;
	//                $appConfig['created_at']=Utils::getTime();
	//                /**生成小程序对应记录*/
	//                $insertApp=\DB::connection("mysql_config")->table("t_app_conf")->insert($appConfig);
	//                /**生成H5对应记录*/
	//                $appConfig['wx_app_type'] = 1;
	//                $appConfig['use_collection'] = 1;
	//                $insertH5=\DB::connection("mysql_config")->table("t_app_conf")->insert($appConfig);
	//                AppUtils::setAppId($appConfig['app_id']);
	//
	//                //更新登录表
	//                $updateLogin = \DB::connection("mysql_config")->
	//                update("update t_mgr_login set merchant_id = ? where openid = ? limit 1",
	//                    [$merchantConfig['merchant_id'], AppUtils::getOpenId()]);
	//
	//                //更新验证码表
	//                $updateCode = \DB::connection("mysql_config")->
	//                update("update t_mgr_verify_codes set used = '1' where code = ?", [trim(Input::get('checkCode'))]);
	//
	//                //更新账户余额,由于账户余额balance默认值为5000分,故无需update
	//                $app_id = $appConfig['app_id'];
	//
	//                //扣费表增加一条赠送记录
	//                $charge=[];
	//                $charge['id']=Utils::getOrderId();
	//                $charge['app_id']=$appConfig['app_id'];
	//                $charge['charge_type']=102;
	//                $charge['fee']=5000;
	//                $charge['account_balance']=5000;
	//                $charge['charge_at']=date('Y-m-d',time());
	//                $charge['charge_time']=Utils::getTime();
	//                $charge['created_at']=Utils::getTime();
	//                $insertCharge=\DB::connection("db_ex_finance")->table("t_balance_charge")->insert($charge);
	//
	////        //为应用创建对应腾讯云app_id目录(V3)
	////        $paths=[$appConfig['app_id'],$appConfig['app_id'].'/audio',$appConfig['app_id'].'/audio_compressed',
	////        $appConfig['app_id'].'/image',$appConfig['app_id'].'/image/compress',$appConfig['app_id'].'/image/ueditor',
	////        $appConfig['app_id'].'/image_compressed',$appConfig['app_id'].'/video',$appConfig['app_id'].'/video/mp4',
	////        $appConfig['app_id'].'/video/source',$appConfig['app_id'].'/sound'];
	////        for($i=0;$i<count($paths);$i++)
	////        {
	////            $folderCreate=Cosapi::createFolder(env("COS_BUCKET_NAME"),$paths[$i],$bizAttr = null);
	////        }
	////        if ($folderCreate['message'] == 'SUCCESS'){
	////            return response()->json(['ret' => 0]);
	////        }else{
	////            return response()->json(['ret' => 1]);
	////        }
	//
	//                // 如果用户完成注册，为其添加一个默认专栏，和两个图文。
	//                if ($insertH5){
	//
	//                    // 专栏数据
	//                    $product = [];
	//                    $product['app_id'] = $appConfig['app_id']; // 应用id
	//                    $product['id'] = Utils::getUniId('p_');  // 专栏标识id
	//                    $product['name'] = '关于小鹅通(体验内容，支付后可提现)';  // 产品包名
	//                    $product['img_url'] = "http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/1216698be5fd077f15d4f3c16c94d03c.jpg";  // 图片url
	//                    $product['img_url_compressed'] = "http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/compress/1216698be5fd077f15d4f3c16c94d03c.jpg";
	//                    $product['summary'] = '简要介绍小鹅通能为您做什么。';  // 简介
	//                    $product['descrb'] = <<<'DES'
	//[{"type":0,"value":"\n小鹅通是专注知识服务与社群运营的聚合型工具，为知识分享者们提供自己的知识服务平台。\n\n小鹅通由前端展示页面与后台管理页面组成，可以理解为嵌入微信等APP的知识小店。\n\n"},{"type":1,"value":"http:\/\/wechatappdev-10011692.file.myqcloud.com\/appTCVlUyvG2205\/image\/ueditor\/88116100_1490103610.jpg"},{"type":0,"value":"\n\n在使用小鹅通的过程中如果遇到了问题，您可以查看"},{"type":0,"value":"a href=\"http:\/\/mp.weixin.qq.com\/s\/BJBghr7vSr5J4EIDHJJrCQ\" target=\"_self\" style=\"color: rgb(42, 117, 237); font-size: 16px; text-decoration: underline;\""},{"type":0,"value":"《常见问题》。\n\n\n如果您对我们有任何意见或者建议，欢迎随时反馈给我们：\n\n产品鹅初号机：\nTEL：18124689845\n微信：exiaomei1994\n\n产品鹅贰号机：\nTEL：18126391294\n微信：chanpine2\n\n官网网址：https:\/\/www.xiaoe-tech.com\/ \n\n微信公众号：小鹅通（微信ID：xiaoeservice）\n"}]
	//DES;
	//                    $product['org_content'] = <<<'CON'
	//<p style="white-space: normal;"><br/></p><p style="white-space: normal;">小鹅通是专注知识服务与社群运营的聚合型工具，为知识分享者们提供自己的知识服务平台。</p><p style="white-space: normal;"><br/></p><p style="white-space: normal;">小鹅通由前端展示页面与后台管理页面组成，可以理解为嵌入微信等APP的知识小店。</p><p style="margin-left: 0.5em; margin-right: 0.5em;"><br/></p><p style="margin-left: 0.5em; margin-right: 0.5em;"><img src="http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/ueditor/88116100_1490103610.jpg" title=".jpg" alt="bg_banner3.jpg"/><br/></p><p style="margin-left: 0.5em; margin-right: 0.5em;"><br/></p><p style="margin-left: 0.5em; margin-right: 0.5em;"><span style="color: rgb(70, 71, 73); font-size: 16px;">在使用小鹅通的过程中如果遇到了问题，您可以查看</span><a href="http://mp.weixin.qq.com/s/BJBghr7vSr5J4EIDHJJrCQ" target="_self" style="color: rgb(42, 117, 237); font-size: 16px; text-decoration: underline;"><span style="font-size: 16px;">《常见问题》</span></a><span style="color: rgb(70, 71, 73); font-size: 16px;">。<br/></span></p><p style="margin-left: 0.5em; margin-right: 0.5em;"><br/></p><p style="margin-left: 0.5em; margin-right: 0.5em;"><span style="color: rgb(70, 71, 73); font-size: 16px;">如果您对我们有任何意见或者建议，欢迎随时反馈给我们：</span></p><p style="margin-left: 0.5em; margin-right: 0.5em;"><br/></p><p style="margin-right: 0.5em; margin-left: 0.5em; white-space: normal;"><span style="color: rgb(70, 71, 73); font-size: 16px;">产品鹅初号机：</span></p><p style="margin-right: 0.5em; margin-left: 0.5em; white-space: normal;"><span style="color: rgb(70, 71, 73); font-size: 16px;">TEL：18124689845</span></p><p style="margin-right: 0.5em; margin-left: 0.5em; white-space: normal;"><span style="color: rgb(70, 71, 73); font-size: 16px;">微信：exiaomei1994</span></p><p style="margin-right: 0.5em; margin-left: 0.5em; white-space: normal;"><br/></p><p style="margin-right: 0.5em; margin-left: 0.5em; white-space: normal;"><span style="color: rgb(70, 71, 73); font-size: 16px;">产品鹅贰号机：</span></p><p style="margin-right: 0.5em; margin-left: 0.5em; white-space: normal;"><span style="color: rgb(70, 71, 73); font-size: 16px;">TEL：18126391294</span></p><p style="margin-right: 0.5em; margin-left: 0.5em; white-space: normal;"><span style="color: rgb(70, 71, 73); font-size: 16px;">微信：chanpine2</span></p><p style="margin-left: 0.5em; margin-right: 0.5em;"><br/></p><p style="margin-left: 0.5em; margin-right: 0.5em;">官网网址：https://www.xiaoe-tech.com/&nbsp;</p><p style="margin-left: 0.5em; margin-right: 0.5em;"><br/></p><p style="margin-left: 0.5em; margin-right: 0.5em;">微信公众号：小鹅通（微信ID：xiaoeservice）</p>
	//CON;
	//                    $product['period'] = null;
	//                    $product['price'] = 100;
	//                    $product['state'] = 0;
	//                    $product['resource_count'] = 1;
	//                    $product['created_at'] = Utils::getTime();
	//                    $product['updated_at'] = Utils::getTime();
	//
	//                    // 图文数据
	//                    $image1 = [];
	//                    $image1['app_id'] = $appConfig['app_id']; // 应用id
	//                    $image1['id'] = Utils::getUniId('i_');
	//                    $image1['title'] = "专栏教程";
	//                    $image1['img_url'] = "http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/81012b12cac8d53e12f9bf333084b363.jpg";
	//                    $image1['img_url_compressed'] = "http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/81012b12cac8d53e12f9bf333084b363.jpg";
	//                    $image1['content'] = <<<'AAA'
	//[{"type":0,"value":"小鹅通是什么？\n\n\n小鹅通是专注知识服务与社群运营的聚合型工具，为知识分享者们提供自己的知识服务平台。\n\n小鹅通由前端展示页面与后台管理页面组成，可以理解为嵌入微信等APP的知识小店。\n\n搭建付费专栏\n\n若您想做知识服务产品的连续化、系列化输出，或已积累一定数量单品内容想要打包售卖，可以选择小鹅通“专栏售卖”这种付费形式。一个专栏内可汇聚同一类别或不同类别的内容，例如：图文付费专栏，或混搭了音频、直播等内容承载形式的付费专栏。\n\n现在我们来搭建您的第一个付费专栏。\n\nSTEP 1. 登陆管理台admin.xiaoe-tech.com\/login；\n\n点击左侧内容列表-专栏-新增专栏，跳转至专栏创建页面。\n\n"},{"type":1,"value":"http:\/\/wechatappdev-10011692.file.myqcloud.com\/appTCVlUyvG2205\/image\/ueditor\/87837800_1490103020.png"},{"type":0,"value":"\n\nSTEP 2. 为您的付费专栏添加名称及填写专栏简介；\n\n专栏简介将显示在专栏详情页、专栏分享提示信息等处。\n\n"},{"type":1,"value":"http:\/\/wechatappdev-10011692.file.myqcloud.com\/appTCVlUyvG2205\/image\/ueditor\/97020100_1490103218.png"},{"type":0,"value":"\n\nSTEP 3. 完善专栏描述、专栏封面，设置专栏价格。\n\n"},{"type":1,"value":"http:\/\/wechatappdev-10011692.file.myqcloud.com\/appTCVlUyvG2205\/image\/ueditor\/10033200_1490103803.png"},{"type":0,"value":"\n\nSTEP 4. 若您设置了首页分类导航功能，可以选择该专栏想显示的相关分类。点击保存，您已拥有了第一个专属付费专栏。\n\n"},{"type":1,"value":"http:\/\/wechatappdev-10011692.file.myqcloud.com\/appTCVlUyvG2205\/image\/ueditor\/45571500_1490103435.png"},{"type":0,"value":"\n\n"}]
	//AAA;
	//                    $image1['org_content'] = <<<'BBB'
	//<p>小鹅通是什么？</p><p><br/></p><p><br/></p><p>小鹅通是专注知识服务与社群运营的聚合型工具，为知识分享者们提供自己的知识服务平台。</p><p><br/></p><p>小鹅通由前端展示页面与后台管理页面组成，可以理解为嵌入微信等APP的知识小店。</p><p><br/></p><p><strong>搭建付费专栏</strong></p><p><br/></p><p>若您想做知识服务产品的连续化、系列化输出，或已积累一定数量单品内容想要打包售卖，可以选择小鹅通“专栏售卖”这种付费形式。一个专栏内可汇聚同一类别或不同类别的内容，例如：图文付费专栏，或混搭了音频、直播等内容承载形式的付费专栏。</p><p><br/></p><p>现在我们来搭建您的第一个付费专栏。</p><p><br/></p><p>STEP 1. 登陆管理台admin.xiaoe-tech.com/login；</p><p><br/></p><p>点击左侧内容列表-专栏-新增专栏，跳转至专栏创建页面。</p><p><br/></p><p><img src="http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/ueditor/87837800_1490103020.png" title=".png" alt="5.png"/></p><p><br/></p><p>STEP 2. 为您的付费专栏添加名称及填写专栏简介；</p><p><br/></p><p>专栏简介将显示在专栏详情页、专栏分享提示信息等处。</p><p><br/></p><p><img src="http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/ueditor/97020100_1490103218.png" title=".png" alt="6.png"/></p><p><br/></p><p>STEP 3. 完善专栏描述、专栏封面，设置专栏价格。</p><p><br/></p><p><img src="http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/ueditor/10033200_1490103803.png" title=".png" alt="7.png"/></p><p><br/></p><p>STEP 4. 若您设置了首页分类导航功能，可以选择该专栏想显示的相关分类。点击保存，您已拥有了第一个专属付费专栏。</p><p><br/></p><p><img src="http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/ueditor/45571500_1490103435.png" title=".png" alt="8.png"/></p><p><br/></p>
	//BBB;
	//                    $image1['img_size_total'] = 0;
	//                    $image1['payment_type'] = 3;
	//                    $image1['product_id'] = $product['id'];
	//                    $image1['product_name'] = '关于小鹅通(体验内容，支付后可提现)';
	//                    $image1['created_at'] = Utils::getTime();
	//                    $image1['updated_at'] = Utils::getTime();
	//                    $image1['start_at'] = Utils::getTime();
	//
	//                    // 图文数据
	//                    $image2 = [];
	//                    $image2['app_id'] = $appConfig['app_id']; // 应用id
	//                    $image2['id'] = Utils::getUniId('i_');
	//                    $image2['title'] = "图文教程(体验内容，支付后可提现)";
	//                    $image2['img_url'] = "http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/4e1b9e6b2a4b45a4ec57c1d6e5c306e7.jpg";
	//                    $image2['img_url_compressed'] = "http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/compress/4e1b9e6b2a4b45a4ec57c1d6e5c306e7.jpg";
	//                    $image2['content'] = <<<'AAA'
	//[{"type":0,"value":"小鹅通是什么？\n\n\n小鹅通是专注知识服务与社群运营的聚合型工具，为知识分享者们提供自己的知识服务平台。\n\n小鹅通由前端展示页面与后台管理页面组成，可以理解为嵌入微信等APP的知识小店。\n\n搭建付费图文\n\n作为入门，我们来搭建一篇付费图文内容作为小店的第一款商品。\n\nSTEP 1. 登陆管理台admin.xiaoe-tech.com\/login\n\n点击左侧内容列表-图文-新增图文，跳转至内容创建页面；\n\n"},{"type":1,"value":"http:\/\/wechatappdev-10011692.file.myqcloud.com\/appTCVlUyvG2205\/image\/ueditor\/62231000_1490102191.png"},{"type":0,"value":"\n\nSTEP 2. 为您的付费图文添加名称，选择收费形式。\n\n        若选择专栏，则需将图文移动至所属专栏，方便做系列化产品的输出。\n\n        若选择单卖，则表明该单品不隶属于任何系列，需要为其单独定价。\n   \n        也可选择免费作为试阅。\n\n"},{"type":1,"value":"http:\/\/wechatappdev-10011692.file.myqcloud.com\/appTCVlUyvG2205\/image\/ueditor\/24271500_1490102350.png"},{"type":0,"value":"\n\nSTEP 3. 完善封面信息、详细内容等。\n\n"},{"type":1,"value":"http:\/\/wechatappdev-10011692.file.myqcloud.com\/appTCVlUyvG2205\/image\/ueditor\/76407600_1490102436.png"},{"type":0,"value":"\n\nSTEP 4. 调整上架时间，若需立即售卖，请选择早于目前自然日的时间段。\n\n"},{"type":1,"value":"http:\/\/wechatappdev-10011692.file.myqcloud.com\/appTCVlUyvG2205\/image\/ueditor\/58726100_1490102528.png"},{"type":0,"value":"\n\nSTEP 5. 点击默认。恭喜！您已经拥有自己的第一款付费产品了，现在请移步前端展示页面欣赏预览。\n\n"}]
	//AAA;
	//                    $image2['org_content'] = <<<'BBB'
	//<p>小鹅通是什么？</p><p><br/></p><p><br/></p><p>小鹅通是专注知识服务与社群运营的聚合型工具，为知识分享者们提供自己的知识服务平台。</p><p><br/></p><p>小鹅通由前端展示页面与后台管理页面组成，可以理解为嵌入微信等APP的知识小店。</p><p><br/></p><p><strong>搭建付费图文</strong></p><p><br/></p><p>作为入门，我们来搭建一篇付费图文内容作为小店的第一款商品。</p><p><br/></p><p>STEP 1. 登陆管理台admin.xiaoe-tech.com/login</p><p><br/></p><p>点击左侧内容列表-图文-新增图文，跳转至内容创建页面；</p><p><br/></p><p><img src="http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/ueditor/62231000_1490102191.png" title=".png" alt="1.png"/></p><p><br/></p><p>STEP 2. 为您的付费图文添加名称，选择收费形式。</p><p><br/></p><p>&nbsp; &nbsp; &nbsp; &nbsp; 若选择专栏，则需将图文移动至所属专栏，方便做系列化产品的输出。</p><p><br/></p><p>&nbsp; &nbsp; &nbsp; &nbsp; 若选择单卖，则表明该单品不隶属于任何系列，需要为其单独定价。</p><p>&nbsp; &nbsp;</p><p>&nbsp; &nbsp; &nbsp; &nbsp; 也可选择免费作为试阅。</p><p><br/></p><p><img src="http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/ueditor/24271500_1490102350.png" title=".png" alt="2.png"/></p><p><br/></p><p>STEP 3. 完善封面信息、详细内容等。</p><p><br/></p><p><img src="http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/ueditor/76407600_1490102436.png" title=".png" alt="3.png"/></p><p><br/></p><p>STEP 4. 调整上架时间，若需立即售卖，请选择早于目前自然日的时间段。</p><p><br/></p><p><img src="http://wechatappdev-10011692.file.myqcloud.com/appTCVlUyvG2205/image/ueditor/58726100_1490102528.png" title=".png" alt="4.png"/></p><p>&nbsp;</p><p>STEP 5. 点击默认。恭喜！您已经拥有自己的第一款付费产品了，现在请移步前端展示页面欣赏预览。</p><p><br/></p>
	//BBB;
	//                    $image2['img_size_total'] = 0;
	//                    $image2['payment_type'] = 2;
	//                    $image2['piece_price'] = 100;
	//                    $image2['created_at'] = Utils::getTime();
	//                    $image2['updated_at'] = Utils::getTime();
	//                    $image2['start_at'] = Utils::getTime();
	//
	//
	//                    $res1 = [];
	//                    $res1['app_id'] = $appConfig['app_id'];
	//                    $res1['product_id'] = $product['id'];
	//                    $res1['product_name'] = "关于小鹅通(体验内容，支付后可提现)";
	//                    $res1['resource_type'] = 1;
	//                    $res1['resource_id'] = $image1['id'];
	//                    $res1['created_at'] = Utils::getTime();
	//
	//                    $res2 = $res1;
	//                    $res2['resource_id'] = $image2['id'];
	//                    // 事务
	//                    DB::transaction(function() use($product,$image1,$image2,$res1,$res2){
	//                        DB::table('t_pay_products')->insert($product);
	//                        DB::table('t_image_text')->insert($image1);
	//                        DB::table('t_image_text')->insert($image2);
	//                        DB::table('t_pro_res_relation')->insert($res1);
	//                    });
	//                }
	//
	////        UploadUtils::createAppAllV3Folder($appConfig['app_id']);
	//                V4UploadUtils::createAppAllV4Folder($appConfig['app_id']);
	//            }else{
	//                return response()->json(['ret' => -1,'msg' => "该微信已被注册"]);
	//            }
	//        }else{
	//            return response()->json(['ret' => -2,'msg' => "微信扫码失败"]);
	//        }
	//
	//        return response()->json(['ret' => 0]);
	//    }

	//返回支付方式

	private function getMerchartIdByname ($name)
	{
		$result_merchant = \DB::connection('mysql_config')->table('t_merchant_conf')->where('name', 'like', '%' . $name . '%')->orderBy('created_at', 'desc')->get();
		if ($result_merchant) {
			$data            = [];
			$data['name']    = $result_merchant[0]->name;
			$data['company'] = $result_merchant[0]->company;
			$data['phone']   = $result_merchant[0]->phone;

			return $result_merchant[0];
		} else {
			return 0;
		}
	}

	private function getAppIdByMerchantId ($merchantId)
	{
		$result = \DB::connection('mysql_config')->table('t_app_conf')->where('merchant_id', '=', $merchantId)->where('wx_app_type', '=', '1')->first();
		if ($result) {
			return $result->app_id;
		} else {
			return -1;
		}
	}

}





