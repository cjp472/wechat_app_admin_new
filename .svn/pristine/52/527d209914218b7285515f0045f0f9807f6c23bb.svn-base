<?php
/**
 * Created by PhpStorm.
 * User: fuhaiwen
 * Date: 2017/1/16
 * Time: 11:09
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\StringConstants;
use App\Http\Controllers\Tools\Utils;
use App\Http\Controllers\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class WithdrawAdminController extends Controller
{
	private $request;
	private $app_id;

	public function __construct (Request $request)
	{
		$this->request = $request;
		$this->app_id  = AppUtils::getAppID();
	}

	//请求提现记录页面
	public function withdrawPage ()
	{

		//具体日期
		$start_time = Input::get('start_time', "");
		$end_time   = Input::get('end_time', "");
		//状态
		$cash_status = Input::get('cash_status', -1);

		$accountBalance = $this->getAccountBalance();
		if ($start_time != "" || $end_time != "" || ($cash_status >= 0 && $cash_status <= 4)) {
			$withdrawList = $this->getWithdrawList($start_time, $end_time, $cash_status);
		} else {
			$withdrawList = $this->getwithdrawlist_in();
		}

		$phone = AppUtils::getAppPhone();
		$name  = AppUtils::getAppNameByAppID();

		//获取申请人信息--姓名和电话
		if (!empty($withdrawList)) {
			$user_info = [];
			foreach ($withdrawList as $key => $value) {
				$temp = \DB::connection("mysql_config")
					->table('t_bind_account_wx')
					->where('app_id', '=', $this->app_id)
					->where('bind_account_wx_id', '=', $value->bind_account_wx_id)
					->first();
				if (empty($temp)) {
					$temp              = new \stdClass();
					$temp->real_name   = '无';
					$temp->wx_avatar   = '';
					$temp->wx_nickname = '';
				}
				$temp->phone = $phone;
				$temp->name  = $name;
				$user_info[] = $temp;
			}
		}

		$authenticateInfo = $this->getCertificateInfo();
		if (empty($authenticateInfo)) {
			$bool_authenticate = 1;
		} else {
			$bool_authenticate = 0;
		}

		return view('admin.withdrawList', compact(
			'accountBalance', 'withdrawList', 'user_info', 'bool_authenticate', 'start_time', 'end_time', 'cash_status'
		));

	}

	//请求申请提现页面

	public function getAccountBalance ()
	{

		//        $accountBalance=\DB::connection("db_ex_finance")->select("select account_balance from t_usable_balance where app_id='$this->app_id'");
		$accountBalance = \DB::connection("db_ex_finance")
			->table('t_usable_balance')
			->where('app_id', $this->app_id)
			->first();
		if ($accountBalance) {
			$accountBalance = $accountBalance->account_balance;
		} else {
			return 0.00;
		}
		$accountBalance = number_format($accountBalance / 100, 2, '.', ',');

		return $accountBalance;
	}

	//请求绑定提现微信账号页面

	public function getWithdrawList ($startDate, $endDate, $cash_status)
	{

		$sqlCon = \DB::connection("mysql_config")->table('t_withdraw_record')
			->where('app_id', $this->app_id)->where('cash_statue', '!=', -1);
		if ($startDate !== null && strlen($startDate) != 0) {
			$startDate .= " 00:00:00";
			$sqlCon    = $sqlCon->where('cash_time', '>=', $startDate);
		}
		if ($endDate !== null && strlen($endDate) != 0) {
			$endDate .= " 23:59:59";
			$sqlCon  = $sqlCon->where('cash_time', '<=', $endDate);
		}
		if ($cash_status >= 0 && $cash_status <= 4) {
			$sqlCon = $sqlCon->where('cash_statue', '=', $cash_status);
		}

		$withdrawList = $sqlCon->select('serial_number', 'app_id', 'cash_time', 'total_cash_money', 'commission_charge', 'real_cash_money', 'bind_account_wx_id', 'cash_statue', 'updated_time')
			->orderBy('cash_time', 'desc')
			->paginate(10);

		return $withdrawList;

	}

	//1.获取“可提现余额”

	public function getwithdrawlist_in ()
	{
		//        $nowDateChuo = time();//当前时间戳
		//        $endDate = date('Y-m-d',$nowDateChuo)." 23:59:59";
		//        $startDate = date('Y-m-d',$nowDateChuo)." 00:00:00";
		$sqlCon = \DB::connection("mysql_config")->table('t_withdraw_record')
			->where('app_id', $this->app_id)->where('cash_statue', '!=', -1);

		$withdrawList = $sqlCon->select('serial_number', 'app_id', 'cash_time', 'total_cash_money', 'commission_charge', 'real_cash_money', 'bind_account_wx_id', 'cash_statue', 'updated_time')
			->orderBy('cash_time', 'desc')
			->paginate(10);

		return $withdrawList;
	}

	//初始状态下拉取提现记录列表,无参数

	public function getCertificateInfo ()
	{

		//查询该appid在表t_bind_account_wx中是否存在
		$result = \DB::connection("mysql_config")
			->table('t_bind_account_wx')
			->where('app_id', $this->app_id)
			->where('bind_status', '0')
			->first();

		return $result;
	}

	//2.获取提现记录列表：参数：1-起止时间，2-状态

	public function applyWithdrawPage ()
	{

		//可提现余额
		$account_amount_total = $this->getAccountBalance();
		//绑定的微信账号信息
		$app_id           = $this->app_id;
		$bind_wx_account  = DB::connection("mysql_config")
			->table('t_bind_account_wx')
			->where('app_id', $app_id)
			->where('bind_status', 0)
			->first();
		$time             = date('Y-m-d');
		$total_cash_money = DB::connection("mysql_config")
			->table("t_withdraw_record")
			->where("app_id", $app_id)
			->where('cash_time', 'like', "%{$time}%")
			->where('cash_statue', '!=', -1)
			//            ->where(DB::raw("date_format(cash_time,'%Y-%m-%d')=date_format(now(),'
			//            %Y-%m-%d')"))
			->sum("total_cash_money");

		//        if(( $cash_money + $total_cash_money ) > 2000000){
		////            return response()->json(["code"=>-1,"msg"=>"提现总金额超出20000"]);
		//        }else{
		////            return response()->json
		//        }
		$total_cash_money = $total_cash_money / 100;

		return view('admin.applywithdraw', compact('account_amount_total', 'bind_wx_account', 'total_cash_money'));
	}

	//3.查看单条提现记录详情：参数:提现流水号

	public function bindWxAccountPage ()
	{

		$app_id = AppUtils::getAppID();
		$phone  = AppUtils::getAppPhone();

		return view('admin.bindWxAccount', compact('phone', 'app_id'));
	}

	//4.判断该客户是否有绑定提现微信账号信息

	public function getWithdrawDetail ()
	{

		//url参数提现流水号
		$serial_number = Input::get('serial_number', '');
		//在表t_withdraw_record中查询提现详细信息
		//        $withdrawDetail = \DB::connection("mysql_config")
		//            ->table('t_withdraw_record')
		//            ->where('serial_number','=',$serial_number)
		//            ->first();
		$withdrawDetail = \DB::connection("mysql_config")->select("select * from t_withdraw_record where serial_number=$serial_number limit 1")[0];
		if (count($withdrawDetail) == 0) {

			return response()->json(Utils::pack($serial_number, StringConstants::Code_Failed, "未查询到该提现详情!"));
		}

		return view('admin.withdrawDetail', compact('withdrawDetail'));
	}

	//绑定提现微信账号

	public function bindWxAccount ()
	{
		$wx_avatar   = Input::get('wx_avatar');
		$wx_nickname = Input::get('wx_nickname');
		$wx_open_id  = Input::get('wx_open_id');
		$sms_code    = Input::get('sms_code');

		$phone = AppUtils::getAppPhone();
		//        $phone = "18607097605";//测试电话
		if ($phone == -1) {
			return response()->json(Utils::pack("1", StringConstants::Code_Failed, "手机号获取失败!"));
		}

		if (Utils::isEmptyString($wx_avatar)) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "微信头像为空!"));
		}
		if (Utils::isEmptyString($wx_nickname)) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "微信昵称未填写!"));
		}
		if (Utils::isEmptyString($wx_open_id)) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "扫码未成功!"));
		}
		if (Utils::isEmptyString($sms_code)) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "验证码未填写!"));
		}

		//在库mysql_config中的表t_mgr_verify_codes中验证验证码填写是否正确
		$ret = \DB::connection("mysql_config")->select("select * from t_mgr_verify_codes where phone = '$phone' and code = '$sms_code' and expire_at > now()");

		if (!$ret) {
			return response()->json(Utils::pack("1", StringConstants::Code_Failed, "验证码错误!"));
		}

		$bind_result = \DB::connection("mysql_config")->select("select * from t_bind_account_wx where wx_open_id = '$wx_open_id' and app_id='$this->app_id'");

		if ($bind_result) {
			$bind_status = $bind_result[0]->bind_status;
			if ($bind_status == 0) {
				return response()->json(Utils::pack("0", StringConstants::Code_Failed, "该微信账号已绑定,请勿重复绑定!"));
			} else {
				//修改该记录bind_status状态为0即绑定
				$ret = \DB::connection("mysql_config")->update("update t_bind_account_wx set bind_status=0 where wx_open_id = '$wx_open_id' and app_id='$this->app_id'");
				if (!$ret) {
					return response()->json(Utils::pack($ret, StringConstants::Code_Failed, "操作失败!"));
				}

				return $this->result($ret);

			}
		} else {

			$proposer_phone     = $phone;
			$proposer_name      = AppUtils::getAppNameByAppID();
			$bind_account_wx_id = Utils::getOrderId();
			$certifyResult      = \DB::connection("mysql_config")->insert("insert into t_bind_account_wx (app_id,bind_account_wx_id,created_time,proposer_name,wx_avatar,wx_nickname,wx_open_id,proposer_phone,bind_status) values ('$this->app_id','$bind_account_wx_id',now(),'$proposer_name','$wx_avatar','$wx_nickname','$wx_open_id','$proposer_phone',0)");
			if ($certifyResult) {
				return $this->result($certifyResult);
			} else {
				return response()->json(Utils::pack('10011', StringConstants::Code_Failed, "绑定操作失败!"));
			}
		}
	}

	//5.添加认证信息

	public function result ($data)
	{
		if (Utils::isEmptyString($data)) {
			return response()->json(Utils::pack("1", StringConstants::Code_Failed, StringConstants::Msg_Failed));
		} else {
			return response()->json(Utils::pack($data));
		}
	}

	//6.编辑认证信息

	public function addCertificateInfo ()
	{

		$real_name         = Input::get('real_name', '');
		$wx_nickname       = Input::get('wx_nickname', '');
		$wx_open_id        = Input::get('wx_open_id', '');
		$id_card           = Input::get('id_card', '');
		$id_card_front_img = Input::get('id_card_front_img', '');
		$id_card_rear_img  = Input::get('id_card_rear_img', '');
		$sms_code          = Input::get('sms_code', '');

		$phone = AppUtils::getAppPhone();
		if ($phone == -1) {
			return response()->json(Utils::pack("1", StringConstants::Code_Failed, "手机号获取失败!"));
		}

		//在库mysql_config中的表t_mgr_verify_codes中验证验证码填写是否正确
		$ret = \DB::connection("mysql_config")->select("select * from t_mgr_verify_codes where phone = '$phone' and code = '$sms_code'");

		if (!$ret) {
			return response()->json(Utils::pack("1", StringConstants::Code_Failed, "验证码错误!"));
		}

		$app_id = $this->app_id;

		if (Utils::isEmptyString($real_name)) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "请输入真实姓名!"));
		}
		if (Utils::isEmptyString($wx_nickname)) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "请输入微信昵称!"));
		}
		if (Utils::isEmptyString($wx_open_id)) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "请扫码!"));
		}
		if (Utils::isEmptyString($id_card)) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "请输入身份证号码!"));
		}
		if (Utils::isEmptyString($id_card_front_img)) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "请上传身份证正面照!"));
		}
		if (Utils::isEmptyString($id_card_rear_img)) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "请上传身份证背面照!"));
		}

		$authenticate_result = \DB::connection("mysql_config")->select("select * from t_bind_account_wx where real_name = '$real_name' and authenticate_status !=2");
		if ($authenticate_result) {

			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "该用户正在认证中!"));
		} else {

			$bind_account_wx_id = Utils::getOrderId();
			$certifyResult      = \DB::connection("mysql_config")->insert("insert into t_bind_account_wx (app_id,bind_account_wx_id,created_time,real_name,wx_nickname,wx_open_id,id_card,id_card_front_img,id_card_rear_img,authenticate_status) values ('$app_id','$bind_account_wx_id',now(),'$real_name','$wx_nickname','$wx_open_id','$id_card','$id_card_front_img','$id_card_rear_img',0)");

			return $this->result($certifyResult);
		}
	}

	//7.删除认证信息

	public function editCertificateInfo ()
	{

		//注:信息编辑后需将状态变为审核中

		$real_name          = Input::get('real_name', '');
		$wx_nickname        = Input::get('wx_nickname', '');
		$wx_open_id         = Input::get('wx_open_id', '');
		$id_card            = Input::get('id_card', '');
		$id_card_front_img  = Input::get('id_card_front_img', '');
		$id_card_rear_img   = Input::get('id_card_rear_img', '');
		$bind_account_wx_id = Input::get('bind_account_wx_id', '');

		$app_id = $this->app_id;

		if (Utils::isEmptyString($real_name)) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "请输入真实姓名!"));
		}
		if (Utils::isEmptyString($wx_nickname)) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "请输入微信昵称!"));
		}
		if (Utils::isEmptyString($wx_open_id)) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "请扫码!"));
		}
		if (Utils::isEmptyString($id_card)) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "请输入身份证号码!"));
		}
		if (Utils::isEmptyString($id_card_front_img)) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "请上传身份证正面照!"));
		}
		if (Utils::isEmptyString($id_card_rear_img)) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "请上传身份证背面照!"));
		}

		$authenticate_result = \DB::connection("mysql_config")->select("select * from t_authenticate_info where bind_account_wx_id = '$bind_account_wx_id'");
		if ($authenticate_result) {

			$certifyResult = \DB::connection("mysql_config")->update("update t_authenticate_info 
                              set created_time = now(),
                                  real_name = '$real_name',
                                  wx_nickname = '$wx_nickname',
                                  wx_open_id = '$wx_open_id',
                                  id_card = '$id_card',
                                  id_card_front_img = '$id_card_front_img',
                                  id_card_rear_img = '$id_card_rear_img',
                                  authenticate_status = 0 where bind_account_wx_id = '$bind_account_wx_id'");

			return $this->result($certifyResult);

		} else {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "该用户信息不存在!"));
		}

	}

	//8.“提现”申请确认

	public function delCertificateInfo ()
	{

		$bind_account_wx_id = Input::get('bind_account_wx_id', '');

		$authenticate_result = \DB::connection("mysql_config")->select("select * from t_authenticate_info where bind_account_wx_id = '$bind_account_wx_id'");
		if ($authenticate_result) {

			$certifyResult = \DB::connection("mysql_config")->delete("delete from t_authenticate_info where bind_account_wx_id = '$bind_account_wx_id'");

			return $this->result($certifyResult);

		} else {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "该用户信息不存在!"));
		}

	}

	//发送验证码

	public function confirmWithdraw ()
	{

		//接收url传参
		$withdraw_amount       = Input::get('withdraw_amount', '');
		$bind_account_wx_id    = Input::get('bind_account_wx_id', '');
		$account_balance_total = Input::get('account_balance', '');

		if (Utils::isEmptyString($withdraw_amount)) {
			if (!Utils::isValidNumber($withdraw_amount)) {
				return response()->json(Utils::pack("0", StringConstants::Code_Failed, "提现金额输入错误!"));
			}
		}
		if (Utils::isEmptyString($bind_account_wx_id)) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "请扫码绑定微信号!"));
		}

		//校验bind_account_wx_id对应的记录是否是绑定中的
		$result_authenticate = \DB::connection("mysql_config")
			->table('t_bind_account_wx')
			->where('bind_account_wx_id', $bind_account_wx_id)
			->where('wx_open_id', '!=', '')
			->where('bind_status', '0')
			->first();
		if (count($result_authenticate) == 0) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "未成功绑定提现微信账户!"));
		}

		//校验可提现金额是否准确
		//今日提现总金额
		$time             = date('Y-m-d');
		$total_cash_money = DB::connection("mysql_config")
			->table("t_withdraw_record")
			->where("app_id", $this->app_id)
			->where('cash_time', 'like', "%{$time}%")
			->where('cash_statue', '!=', -1)
			//            ->where(DB::raw("date_format(cash_time,'%Y-%m-%d')=date_format(now(),'
			//            %Y-%m-%d')"))
			->sum("total_cash_money");

		$accountBalance = \DB::connection("db_ex_finance")
			->table('t_usable_balance')
			->where('app_id', $this->app_id)
			->first();
		$AccountBalance = $accountBalance->account_balance;
		if ($account_balance_total <= $AccountBalance) {
			if ($withdraw_amount + $total_cash_money > 2000000) {
				//一天提现超过2万
				return response()->json(Utils::pack("0", StringConstants::Code_Failed, "一日内提现超过两万，提现失败!"));
			}
			//先生成提现记录,然后再扣除库db_ex_finance中表t_usable_balance中的"可提现金额"字段account_balance.
			if ($account_balance_total >= $withdraw_amount) {
				/****此处记录提现记录插入库db_ex_config中的提现记录表中(t_withdraw_record)***/
				$serial_number = Utils::getOrderId(7);//流水号
				$cash_statue   = StringConstants::Cash_Status_INIT;//提现状态--初始态
				//提现手续费
				$commission_charge = 0.006 * $withdraw_amount;
				//实际提现到账金额
				//$real_cash_money = (1-0.006)*$withdraw_amount;
				$real_cash_money = $withdraw_amount;
				$cashCardResult  = \DB::connection("mysql_config")->insert("insert into t_withdraw_record (serial_number,cash_time,total_cash_money,commission_charge,real_cash_money,app_id,bind_account_wx_id,cash_statue) values ('$serial_number',now(),'$withdraw_amount','$commission_charge','$real_cash_money','$this->app_id','$bind_account_wx_id','$cash_statue')");

				if ($cashCardResult) {
					$flag_account_amount = DB::connection("db_ex_finance")->update("update t_usable_balance set account_balance=account_balance-'$withdraw_amount' where app_id = '$this->app_id' and account_balance >= '$withdraw_amount' limit 1");

					if ($flag_account_amount == 1) {
						//更新提现记录状态为申请中
						$cash_params['cash_statue']  = StringConstants::Cash_Status_Checking;
						$cash_params['updated_time'] = Utils::getTime();
						$update_cash_records         = \DB::table('db_ex_config.t_withdraw_record')
							->where('serial_number', '=', $serial_number)
							->update($cash_params);
						if ($update_cash_records) {
							return $this->result($flag_account_amount);
						} else {
							return response()->json(Utils::pack("0", StringConstants::Code_Failed, "更新提现状态失败，请稍后再试!"));
						}
					} else {
						return response()->json(Utils::pack("0", StringConstants::Code_Failed, "提现操作失败，请稍后再试!"));
					}
				} else {
					return response()->json(Utils::pack("0", StringConstants::Code_Failed, "提现失败!"));
				}
			} else {
				return response()->json(Utils::pack("0", StringConstants::Code_Failed, "账户余额不足!"));
			}
		} else {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "可提现金额出现异常,请和客服联系!"));
		}
	}

	//查询提现绑定微信号

	public function sendSms ()
	{

		$phone = AppUtils::getAppPhone();
		if ($phone == -1) {
			return response()->json(Utils::pack("1", StringConstants::Code_Failed, "手机号获取失败"));
		}
		$checkCode = random_int(100000, 999999);//验证码
		$minutes   = '5';//失效分钟
		//        $content = $checkCode . "为您的登录验证码，请于" . $minutes . "分钟内填写。如非本人操作，请忽略本短信。";
		$content = $checkCode . "为您绑定提现微信账号的验证码，" . $minutes . "分钟内有效。请确认该绑定操作已经过您的许可。";

		//测试手机号:18607097605
		//        $phone = "18607097605";
		$ret = Utils::sendsms($phone, $content);

		if ($ret === false) {
			return response()->json(Utils::pack("1", StringConstants::Code_Failed, "发送验证码失败!"));
		} else {
			//插验证码的表
			$codeInfo               = [];
			$codeInfo['openid']     = AppUtils::getOpenId();
			$codeInfo['type']       = '0';
			$codeInfo['used']       = '0';
			$codeInfo['phone']      = $phone;
			$codeInfo['code']       = $checkCode;
			$codeInfo['expire_at']  = date('Y-m-d H:i:s', time() + 5 * 60);
			$codeInfo['created_at'] = date('Y-m-d H:i:s', time());
			$insert                 = \DB::connection("mysql_config")->table("t_mgr_verify_codes")->insertGetId($codeInfo);

			return $this->result($insert);
		}
	}

	public function querySaomiaoResult ()
	{
		$app_id      = AppUtils::getAppID();
		$now_time    = date('Y-m-d H:i:s', time() - 120);
		$usable_time = date('Y-m-d H:i:s', time() + 300);

		$binding_wx_acount = \DB::connection("mysql_config")
			->table('t_bind_account_wx')
			->where('app_id', $app_id)
			->where('wx_open_id', '!=', '')
			->where('updated_time', '>=', $now_time)
			->where('updated_time', '<=', $usable_time)
			->first();
		if ($binding_wx_acount) {
			$innerhtml = "";
			$innerhtml .= "<span class='cash_weixin'>微信昵称</span>";
			$innerhtml .= '<span class="code_input_2">';
			$innerhtml .= '<span id="wx_nickname" data-bind_acount_wx_id=' . $binding_wx_acount->bind_account_wx_id . '>' . $binding_wx_acount->wx_nickname . '</span>';
			$innerhtml .= '<a href="/bind_wx_account_page" class="chang_wx_account">重新绑定</a>';
			$innerhtml .= "</span>";
			$innerhtml .= "<div class='wx_imgname'>";

			$innerhtml .= "<span class='cash_weixin'>微信头像</span>";
			$innerhtml .= '<span class="code_input"><img id="wx_avatar" data-wx_open_id="' . $binding_wx_acount->wx_open_id . '" class="wx_img" src="';
			$innerhtml .= $binding_wx_acount->wx_avatar ? $binding_wx_acount->wx_avatar : '../images/default.png';
			//            $innerhtml .= $binding_wx_acount->wx_avatar;
			$innerhtml .= '"></span>';
			$innerhtml .= '</div>';

			return $this->result($innerhtml);
		} else {
			return response()->json(Utils::pack("1", StringConstants::Code_Failed, StringConstants::Msg_Failed));
		}
	}

	public function createWxAccountByAppid ()
	{
		$app_id = Input::get('app_id', '');
		if (empty($app_id)) {
			$app_id = AppUtils::getAppID();
		}
		$bind_account_wx_id = Utils::getOrderId();
		$proposer_name      = AppUtils::getAppNameByAppID();
		$proposer_phone     = AppUtils::getAppPhone();

		$ret = \DB::connection("mysql_config")->insert("insert into t_bind_account_wx(app_id,bind_account_wx_id,proposer_name,proposer_phone,created_time) values('$app_id','$bind_account_wx_id','$proposer_name','$proposer_phone',now())");
		if ($ret) {
			return $this->result($bind_account_wx_id);
		} else {
			return response()->json(Utils::pack("1", StringConstants::Code_Failed, StringConstants::Msg_Failed));
		}
	}
}