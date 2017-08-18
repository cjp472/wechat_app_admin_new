<?php
/**
 * Created by PhpStorm.
 * User: breeze
 * Date: 30/03/2017
 * Time: 16:49
 */

namespace App\Http\Controllers\APIController;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tools\APIUtils;
use App\Http\Controllers\Tools\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * 注册api控制器
 * Class DoneDistributeController
 * @package App\Http\Controllers
 */
class ApiSignController extends Controller
{
	/***
	 * 手机端注册接口
	 */
	public function mobile_sign (Request $request)
	{
		//验证参数完整性
		//调用api注册接口
		$info = $request->all();
		Utils::logFrom($info,'login.log');
		// 验证器验证传过来的数据
		//union_id mobile_openid wx_nick_name gender logo
		$this->validate($request, [
			'union_id'      => 'required',
			'mobile_openid' => 'required',
			'wx_nick_name'  => 'required',
			'gender'        => 'required',
			'logo'          => 'required',
		], [
			'required' => ':attribute 不能为空',
		]);

		$union_id      = $request->input('union_id');
		$openid        = $request->input('open_id', '');
		$mobile_openid = $request->input('mobile_openid');
		$wx_nick_name  = $request->input('wx_nick_name');
		$gender        = $request->input('gender');
		$logo          = $request->input('logo');

		$mgr_info = DB::connection('mysql_config')->table('t_mgr_login')->where('union_id', $union_id)->first();
		Utils::logFrom($mgr_info,'login.log');
		if ($mgr_info && (count($mgr_info) > 0)) {
			$merchant_id = $mgr_info->merchant_id;
			if ($merchant_id) {
				return json_encode(['code' => -1, 'msg' => '用户已注册']);
			} else {
				// 生成商户id
				$merchant_id = 'mch' . str_random(8);
				// 补入merchant_id
				//                $update = DB::connection('mysql_config')->table('t_mgr_login')->where('openid',$openid)->update(['merchant_id'=>$merchant_id]);
				$updateSql = "update t_mgr_login set merchant_id = '{$merchant_id}',sign_client = 1 WHERE union_id = '{$union_id}' limit 1";
				$update    = DB::connection('mysql_config')->update($updateSql);
			}
		} else {
			// 添加登陆表记录
			$merchant_id = APIUtils::addLogin($union_id, $openid, $mobile_openid, $wx_nick_name, $gender, $logo);
		}

		// 完成注册
		if ($merchant_id) {
			$data = APIUtils::api_sign($merchant_id);
		} else {
			return json_encode(['code' => -1, 'msg' => '添加登陆表记录失败']);
		}

		$data = json_decode($data);

		return json_encode(['code' => $data->ret, 'msg' => $data->msg]);
	}

}