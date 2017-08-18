<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tools\AppUtils;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class AdminKController extends Controller
{
	private $app_id;

	public function __construct ()
	{
		$this->app_id = AppUtils::getAppID();
	}

	/*********************** 更换管理员页面  *************************/
	public function changeAdmin ()
	{
		$type = trim(Input::get('type'));
		if ($type == 3) {
			session(['openid' => '']);
		}

		return View('admin.changeAdmin', compact('type'));
	}

	/*********************** 主账户找回密码页面  *************************/
	public function changePasswordPage ()
	{
		$open_id     = AppUtils::getOpenId();
		$merchant_id = DB::connection('mysql_config')->table('t_mgr_login')
			->where('openid', $open_id)
			->select('merchant_id')
			->first();
		$phone       = DB::connection('mysql_config')->table('t_merchant_conf')
			->where('merchant_id', $merchant_id->merchant_id)
			->select('phone')
			->first();
		$phone       = $phone->phone;

		return View('admin.accountSetting.changePassword', compact('phone'));
	}

	/*********************** 增加/修改主账户操作  *************************/
	public function addAdminAccount ()
	{
		//        密码
		$params['password'] = trim(Input::get('password'));
		//        手机验证码
		$phone = trim(Input::get('phone'));
		//        手机验证码
		$identify_code = trim(Input::get('identify_code'));
		//        是否是忘记密码提交操作
		$only_password = trim(Input::get('only_password'));
		//        是否是编辑
		$page_type = trim(Input::get('page_type'));
		$open_id   = AppUtils::getOpenId();
		if (empty($params['password'])) {
			return response()->json(['code' => 1, 'msg' => '没有传入密码']);
		} else if (strlen($params['password']) < 6) {
			return response()->json(['code' => 1, 'msg' => '传入密码应大于等于6位']);
		}
		//        若不是只更改密码
		if (empty($only_password)) {
			//            若不是编辑
			if (!$page_type) {
				$params['name'] = trim(Input::get('name'));
				if (empty($params['name'])) {
					return response()->json(['code' => 1, 'msg' => '没有传入主账号']);
				}

				//                唯一检测
				$exsitAdminUser = \DB::connection("mysql_config")
					->select("select username from t_admin_user where username = ? and is_deleted = 0 ", [$params['name']]);

				$existMgr = \DB::connection("mysql_config")
					->select("select name from t_mgr_login where name = ?", [$params['name']]);

				if (!empty($exsitAdminUser) || !empty($existMgr)) {
					return response()->json(['ret' => 1, 'msg' => '存在重复']);
				}
			} else {
				//                若是编辑，则要获取新密码和旧密码
				$enter_password = trim(Input::get('enter_password'));

				if (empty($enter_password)) {
					return response()->json(['code' => 1, 'msg' => '请传入旧密码']);
				}
				if (empty($params['password'])) {
					return response()->json(['code' => 1, 'msg' => '请传入新密码']);
				}
				$original_password = DB::connection('mysql_config')->table('t_mgr_login')
					->where('openid', $open_id)
					->select('password')
					->first();
				if (!Hash::check($enter_password, $original_password->password)) {
					return response()->json(['code' => 1, 'msg' => '原密码错误']);
				}
			}
		} //若是忘记密码页面
		else {
			if (empty($identify_code)) {
				return response()->json(['code' => 1, 'msg' => '没有传入手机验证码']);
			}
			if (empty($phone)) {
				return response()->json(['code' => 1, 'msg' => '没有手机号']);
			}

			$code_correct = $this->isCodeCorrect($phone, $identify_code);
			if (!$code_correct) {
				return response()->json(['code' => 1, 'msg' => '验证码不正确']);
			}
		}

		//        hash密码
		$params['password'] = Hash::make($params['password']);
		$update             = DB::connection('mysql_config')->table('t_mgr_login')
			->Where('openid', $open_id)
			->update($params);

		if (!$update) {
			return response()->json(['code' => 1, 'msg' => '查询失败']);
		} else {
			return response()->json(['code' => 0, 'msg' => '操作成功']);
		}

	}

	/*********************** 验证码验证  *************************/
	private function isCodeCorrect ($phone, $code)
	{
		$check = DB::connection("mysql_config")->
		select("select id from t_mgr_verify_codes where openid = ? and used = '0' and phone =? and code = ? and expire_at > now()",
			[
				AppUtils::getOpenId(),
				$phone,
				$code,
			]);
		if ($check) {
			return 1;
		} else {
			return 0;
		}
	}

	/*********************** 检验账号唯一性操作  *************************/
	public function isAcountRepeat ()
	{
		$name = trim(Input::get('name'));

		//                唯一检测
		$exsitAdminUser = \DB::connection("mysql_config")
			->select("select username from t_admin_user where username = ? and is_deleted = 0 ", [$name]);

		$existMgr = \DB::connection("mysql_config")
			->select("select name from t_mgr_login where name = ?", [$name]);

		if (empty($exsitAdminUser) && empty($existMgr)) {
			return response()->json(['ret' => 0]);
		} else {
			return response()->json(['ret' => 1]);
		}

	}

	/*********************** 更改绑定的微信商户  *************************/
	public function changeWxAccount (Request $request)
	{
		$code = trim(Input::get('code'));

		return AppUtils::changeWx($code, $request);
	}

	/*********************** 手机号换绑  *************************/
	public function changePhone (Request $request)
	{
		$open_id  = AppUtils::getOpenId();
		$app_id   = AppUtils::getAppID();
		$phone    = trim(Input::get('phone'));
		$contacts = trim(Input::get('contacts'));
		//        $identify_code=trim(Input::get('identify_code'));
		if (empty($phone)) {
			return response()->json(['code' => '1', 'msg' => '请传入手机号']);
		}
		if (empty($contacts)) {
			return response()->json(['code' => '1', 'msg' => '请传入管理员名称']);
		}
		//        if(empty($identify_code)){
		//            return response()->json(['code' => '1','msg' => '请传入验证码']);
		//        }
		//        验证验证码是否正确
		//        $code_correct=$this->isCodeCorrect($phone,$identify_code);
		//        if($code_correct==0){
		//            return response()->json(['code' => '1','msg' => '验证不正确，请重新输入或重新获取验证码']);
		//        }

		if (preg_match("/^1[34578]{1}\d{9}$/", $phone)) {
			$mgr_login_new = session("mgr_login_new");

			$merchant_id = \DB::connection("mysql_config")
				->table('t_app_conf')
				->where('app_id', $app_id)
				->select('merchant_id')
				->first();
			$update1     = \DB::connection("mysql_config")
				->table('t_merchant_conf')
				->where('merchant_id', $merchant_id->merchant_id)
				->update(['phone' => $phone, 'name' => $contacts]);
			$update2     = \DB::connection("mysql_config")
				->table('t_mgr_login')
				->where('merchant_id', $merchant_id->merchant_id)
				->update($mgr_login_new);
			if (!$update1) {
				return response()->json(['code' => '1', 'msg' => '换绑手机号失败']);
			}
			//            dump($mgr_login_new);
			if (!$update2) {
				return response()->json(['code' => '1', 'msg' => '换绑微信号失败']);
			}
			session(['openid' => '']);

			return response()->json(['code' => '0', 'msg' => '更新成功']);

		} else {
			return response()->json(['code' => '1', 'msg' => '手机号格式错误']);
		}
	}

}
