<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/14
 * Time: 11:12
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\StringConstants;
use App\Http\Controllers\Tools\Utils;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class H5_SaleController extends Controller
{

	/**
	 * 分销主页
	 */
	public function saleHomePage ()
	{

		$applier = \DB::table('t_sales_users')->where('xiaoe_openid', '=', Utils::getUserOpenID())->first();

		$openid = Utils::getUserOpenID();

		return View('admin_H5.saleHomePage')->with('is_applied', $applier);
	}

	/**
	 * 获取搜索内容
	 */
	public function saleSearchContent ()
	{

		$data = [];

		$contentSearch = Input::get("contentSearch");

		$innerhtml = "";//返回前台的拼接页面
		//在t_app_conf中模糊查询客户app name
		$result_app_name = \DB::connection('mysql_config')->table('t_app_conf')
			->where('wx_app_name', 'like', '%' . $contentSearch . '%')
			->get();

		if (count($result_app_name) > 0) {

			foreach ($result_app_name as $key_1 => $value_1) {
				$skip_target      = '';
				$main_skip_target = '';
				$every_appid      = $value_1->app_id;

				if ($value_1->use_collection == 0 && !empty($value_1->wx_app_id)) {
					$skip_target      = AppUtils::getUrlHeader($every_appid) . $value_1->wx_app_id . '.' . env('DOMAIN_NAME');
					$main_skip_target = $skip_target . '/homepage/' . Utils::contentUrlHome($value_1->wx_app_id);

				} else if ($value_1->use_collection == 1) {
					$skip_target      = AppUtils::getUrlHeader($every_appid) . env('DOMAIN_DUAN_NAME') . "/" . $value_1->app_id;
					$main_skip_target = $skip_target . '/homepage/' . Utils::contentUrlHome($value_1->app_id);
				}
				//拼接页面

				$innerhtml .= '<div class="sale_main_page">';
				$innerhtml .= '<a class="sale_main_page_link" href="' . $main_skip_target . '">';
				$innerhtml .= '<div class="sale_main_page_desc">';
				$innerhtml .= '<div class="main_page_icon_wrapper">';
				$innerhtml .= '<img class="main_page_icon" src="' . $value_1->wx_share_image_compressed . '">';
				$innerhtml .= '</div>';
				$innerhtml .= '<div  data-app_id="' . $value_1->app_id . '" class="main_page_name">' . $value_1->wx_app_name . '</div>';
				$innerhtml .= '</div></a><div data-app_image="' . $value_1->wx_share_image_compressed . '" data-app_id="' . $value_1->app_id . '" data-app_name="' . $value_1->wx_app_name . '" onclick="$searchContent.applyMainPageSale(this);" class="main_page_sale_apply">申请分销主页</div></div>';

				//在表t_pay_products查询该app_id下的所有允许分销的专栏
				$appId = $value_1->app_id;

				$result_packages = \DB::table('t_pay_products')
					->where('app_id', '=', $appId)
					->where('state', '=', 0)
					->get();
				$count_packages  = count($result_packages);
				//若该主页下无相关专栏则
				if ($count_packages == 0) {
					$innerhtml .= '<div class="sale_column_number">该公众号没有专栏可供分销</div>';
				} else {

					$innerhtml .= '<div class="sale_column_number">该公众号有' . $count_packages . '个专栏可供分销</div>';
					foreach ($result_packages as $key_2 => $package) {

						//专栏跳转链接
						$package_skip_target = $skip_target . Utils::contentUrl($appId, 3, '', '', $package->id);

						//                       {{--每一个分销专栏的条目--}}

						$innerhtml .= '<div class="sale_product" data-package_id="' . $package->id . '">';
						$innerhtml .= '<a class="sale_product_link" href="' . $package_skip_target . '">';
						$innerhtml .= '<div class="sale_product_desc">';
						$innerhtml .= '<div class="sale_product_icon_wrapper">';
						$innerhtml .= '<img class="sale_product_icon" src="' . $package->img_url . '"/>';
						$innerhtml .= '</div>';
						$innerhtml .= '<div class="sale_product_name">' . $package->name . '</div>';
						$innerhtml .= '</div>';
						$innerhtml .= '</a>';
						$innerhtml .= '<div data-package_image="' . $package->img_url . '" data-product_id="' . $package->id . '"data-app_id="' . $value_1->app_id . '" data-app_name="' . $value_1->wx_app_name . '" data-product_name="' . $package->name . '" onclick="$searchContent.applyProductSale(this);"  class="sale_product_apply">申请分销</div></div>';

					}
				}

				$innerhtml .= '<div class="divide_line"></div>';

			}

		} else {
			//TODO:(暂时不对专栏进行模糊搜索)在专栏资源中模糊查询
			return response()->json(Utils::wechat_pack("1", StringConstants::Code_Failed, "没有相关的数据"));
		}

		return $this->result($innerhtml);

	}

	/**
	 * @param $data
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function result ($data)
	{
		if (Utils::isEmptyString($data)) {
			return response()->json(Utils::wechat_pack("1", StringConstants::Code_Failed, StringConstants::Msg_Failed));
		} else {
			return response()->json(Utils::wechat_pack($data));
		}
	}

	/**
	 * 获取用户的分销列表
	 */

	public function getSaleList ()
	{

		$wx_open_id = Utils::getUserOpenID();

		$saleList = \DB::table("t_sales")->where("xiaoe_openid", "=", $wx_open_id)->orderBy("apply_at", "desc")->get();
		//        $saleList=\DB::table("t_sales")->orderBy("apply_at","desc")->get();

		//通过channelid在渠道表中查询相关分销的浏览量和开通量,
		if (count($saleList) > 0) {
			foreach ($saleList as $key => $sale_record) {

				$result = \DB::table('t_channels')
					->where('id', '=', $sale_record->channel_id)
					->first();
				if ($result) {
					$sale_record->view_count = $result->view_count;
					$sale_record->open_count = $result->open_count;
				} else {
					$sale_record->view_count = 0;
					$sale_record->open_count = 0;
				}
				$saleList[ $key ] = $sale_record;
			}
		}

		$StateClass = ['0' => 'apply_ing', '1' => 'apply_ok', '2' => 'apply_fail'];

		return VIEW('admin_H5.mySalePage')->with('saleList', $saleList)->with('StateClass', $StateClass);

	}

	//发送验证码

	/**
	 * 用户的分销申请处理
	 */
	public function applySale ()
	{

		//接收用户传过来的参数
		$params = Input::get('params', '');

		$applierSessionInfo = Utils::getApplierInfo();

		$params['xiaoe_openid']    = $applierSessionInfo['id'];
		$params['xiaoe_img_url']   = $applierSessionInfo['avatar'];
		$params['xiaoe_nick_name'] = $applierSessionInfo['nickname'];

		$params['created_at'] = Utils::getTime();
		$params['apply_at']   = Utils::getTime();
		$params['id']         = Utils::getOrderId(8);
		$phone                = $params['phone'];

		$code = Input::get('code');

		$is_need_code = Input::get('is_change_phone');
		if ($is_need_code) {
			if (Utils::isEmptyString($code)) {
				return response()->json(Utils::wechat_pack("0", StringConstants::Code_Failed, "验证码未填写!"));
			}
			//判断验证码是否有效
			//在库mysql_config中的表t_mgr_verify_codes中验证验证码填写是否正确
			$ret = \DB::connection("mysql_config")->select("select * from t_mgr_verify_codes where phone = '$phone' and code = '$code' and expire_at > now()");

			if (!$ret) {
				return response()->json(Utils::wechat_pack("1", StringConstants::Code_Failed, "验证码错误!"));
			} else {
				$id     = $ret[0]->id;
				$result = \DB::connection("mysql_config")->update("update t_mgr_verify_codes set used = 1 where id = '$id'");
			}
		}
		if (Utils::isEmptyString($phone)) {
			return response()->json(Utils::wechat_pack("0", StringConstants::Code_Failed, "手机号未填写!"));
		}
		if (!Utils::isValidPhoneNumber($phone)) {
			return response()->json(Utils::wechat_pack("0", StringConstants::Code_Failed, "手机号码格式不对!"));
		}
		if (Utils::isEmptyString($params['applier'])) {
			return response()->json(Utils::wechat_pack("0", StringConstants::Code_Failed, "真实姓名未填写!"));
		}
		//        if (Utils::isEmptyString($params['applier'])) {
		//            return response()->json(Utils::pack("0", StringConstants::Code_Failed, "真实姓名未填写!"));
		//        }
		//        if (Utils::isEmptyString($params['applier'])) {
		//            return response()->json(Utils::pack("0", StringConstants::Code_Failed, "真实姓名未填写!"));
		//        }

		DB::beginTransaction();

		$result_apply_sale = \DB::table('t_sales')->insert($params);

		if ($result_apply_sale) {
			//检查该openid的用户是否已存在
			$applier = \DB::table('t_sales_users')->where('xiaoe_openid', '=', Utils::getUserOpenID())->first();
			if (count($applier)) {
				$applier = $params['applier'];
				$phone   = $params['phone'];
				$openid  = $params['xiaoe_openid'];

				$result_applier_info = \DB::update("update t_sales_users set applier = '$applier',phone = '$phone', updated_at=now() where xiaoe_openid='$openid'");
				if ($result_applier_info) {
					DB::commit();

					return $this->result($result_applier_info);
				} else {
					DB::rollback();

					return response()->json(Utils::wechat_pack(0, StringConstants::Code_Failed, "更新申请人信息失败!"));
				}

			} else {

				$applier_info_params['xiaoe_openid']    = $applierSessionInfo['id'];
				$applier_info_params['xiaoe_img_url']   = $applierSessionInfo['avatar'];
				$applier_info_params['xiaoe_nick_name'] = $applierSessionInfo['nickname'];

				$applier_info_params['created_at'] = Utils::getTime();
				$applier_info_params['phone']      = $phone;
				$applier_info_params['applier']    = $params['applier'];
				//                $applier_info_params =

				$result_applier_info = \DB::table('t_sales_users')->insert($applier_info_params);
				if ($result_applier_info) {
					DB::commit();

					return $this->result($result_applier_info);
				} else {
					DB::rollback();

					return response()->json(Utils::wechat_pack(0, StringConstants::Code_Failed, "新增申请人信息失败!"));
				}
			}
		} else {
			DB::rollback();

			return response()->json(Utils::wechat_pack(0, StringConstants::Code_Failed, "生成分销记录错误!"));
		}
	}

	public function send_apply_sms ()
	{

		$phone = Input::get('phone');
		if (Utils::isEmptyString($phone)) {
			return response()->json(Utils::wechat_pack("0", StringConstants::Code_Failed, "手机号未填写!"));
		}
		if (!Utils::isValidPhoneNumber($phone)) {
			return response()->json(Utils::wechat_pack("0", StringConstants::Code_Failed, "手机号码格式不对!"));
		}
		$checkCode = random_int(100000, 999999);//验证码
		$minutes   = '5';//失效分钟
		//        $content = $checkCode . "为您的登录验证码，请于" . $minutes . "分钟内填写。如非本人操作，请忽略本短信。";
		$content = $checkCode . "为您申请成为分销商的验证码，5分钟内有效。请确认该申请为您本人操作。";

		//测试手机号:18607097605
		//        $phone = "18607097605";
		$ret = Utils::sendsms($phone, $content);

		if ($ret === false) {
			return response()->json(Utils::wechat_pack("1", StringConstants::Code_Failed, "发送验证码失败!"));
		} else {
			//插验证码的表
			$codeInfo               = [];
			$codeInfo['openid']     = Utils::getUserOpenID();
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

	/**
	 * 查询分销记录详情
	 */
	public function query_sale_detail ()
	{

		$sale_record_id = Input::get("id");

		$result = '';
		if ($sale_record_id) {
			$result = \DB::table('t_sales')
				->where('id', '=', $sale_record_id)
				->first();
			if ($result) {
				$count = \DB::table('t_channels')
					->where('id', '=', $result->channel_id)
					->first();
				if ($count) {
					$result->view_count = $count->view_count;
					$result->open_count = $count->open_count;
				} else {
					$result->view_count = 0;
					$result->open_count = 0;
				}
			}
		}

		return VIEW('admin_H5.detailPage')->with("result", $result);
	}

	public function submitSuccess ()
	{
		return view('admin_H5.submitSuccess');

	}

}



















