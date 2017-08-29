<?php
/**
 * Created by PhpStorm.
 * User: breeze
 * Date: 9/30/16
 * Time: 11:54
 */

namespace App\Http\Controllers\Tools;

use DB;
use Session;

class AppUtils
{

	private static $baseKey = [
		'marketing_saler',         //营销中心->推广员功能
		'marketing_invite_card',         //营销中心->邀请卡
	];

	//判断时间是否有效
	private static $growKey = [
		'resource_category',    //分类导航
		//        'gift_buy',             //购买赠送 // 实际没有用到 取的app_module
		'try_audio',            //试听分享
		'vip_period',           //会员开通
		'message_push',         //用户定向推送
		'question_products',    //付费问答
		//        'marketing_saler',         //营销中心->推广员功能
		//        'marketing_invite_card',         //营销中心->邀请卡
		'active_manage',         //活动管理
	];

	//把秒转化成时间格式
	private static $proKey   = [
		'home_title',           //首页名称自定义
		'daily_sign',           //日签分享
		'live_video',            //直播上传视频
		'coupon',               //优惠券
	];
	private static $noPerKey = [
		'message_push',         //用户定向推送
	];

	//获取客户绑定的手机号码

	/*********************** 更改绑定的微信商户  *************************/
	public static function changeWx ($code, $request)
	{
		$app_id = AppUtils::getAppID();
		//根据code获取access_token和openid
		$url         = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . env('QRCODE_APP_ID', '')
			. "&secret=" . env('QRCODE_SECRET', '') . "&code=" . $code . "&grant_type=authorization_code";
		$resultArray = json_decode(file_get_contents($url), true);
		//根据access_token和openid获取用户所有信息
		$infoUrl     = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $resultArray["access_token"]
			. "&openid=" . $resultArray["openid"];
		$infoArray   = json_decode(file_get_contents($infoUrl), true);
		$merchant_id = \DB::connection("mysql_config")
			->table('t_app_conf')
			->where('app_id', $app_id)
			->select('merchant_id')
			->first();
		//        unionid查重
		$exist_union_id = \DB::connection("mysql_config")
			->table('t_mgr_login')
			->where('union_id', $infoArray['unionid'])
			->value('union_id');
		if ($exist_union_id) {
			//该微信号已绑定当前或其他管理台账号
			//            dump("该微信号已绑定当前或其他管理台账号");
			return redirect('/changeAdmin?wx_is_used=1&type=1');
		} else {
			$mgr_login_new['union_id']      = $infoArray['unionid'];
			$mgr_login_new['openid']        = $infoArray['openid'];
			$mgr_login_new['nick_name']     = $infoArray['nickname'];
			$mgr_login_new['gender']        = $infoArray['sex'];
			$mgr_login_new['logo']          = $infoArray['headimgurl'];
			$mgr_login_new['mobile_openid'] = null;
			$mgr_login_new['name']          = null;
			//
			session(['mgr_login_new' => $mgr_login_new]);

			return redirect('/changeAdmin?type=2');
		}
	}

	//获取客户name

	/**
	 * 通过session获取商户下的小程序appid
	 * @return string
	 */
	public static function getAppID ()
	{
		//        return "appabcdefgh1234";
		//        return "pdt_1475069376";
		$app_id = session('app_id', '');

		if (empty($app_id)) {
			abort("100001");
		}

		return $app_id;
	}

	public static function isValidByTime ($updatedAt, $seconds = 6000)
	{
		if (Utils::isValidTimeInDB($updatedAt) && Utils::getTimestamp($updatedAt) + $seconds > time()) {
			return true;
		} else {
			return false;
		}
	}

	public static function dataformat ($num)
	{
		$hour   = floor($num / 3600);
		$minute = floor(($num - 3600 * $hour) / 60);
		$second = floor((($num - 3600 * $hour) - 60 * $minute) % 60);
		if ($second == 0) {
			$second = '00';
		}
		if ($hour > 9 && $minute > 9) {
			$time = $hour . ':' . $minute . ':' . $second;
		} else if ($hour <= 9 && $minute <= 9) {
			$time = '0' . $hour . ':' . '0' . $minute . ':' . $second;
		} else if ($hour > 9 && $minute <= 9) {
			$time = $hour . ':' . '0' . $minute . ':' . $second;
		} else {
			$time = '0' . $hour . ':' . $minute . ':' . $second;
		}

		return $time;
	}

	public static function getAppPhone ()
	{

		//先从表t_app_conf中根据appid和wx_app_type=1查找到merchant_id
		$app_id      = AppUtils::getAppID();
		$merchant_id = \DB::connection("mysql_config")->select("select merchant_id from t_app_conf where app_id = '$app_id' and wx_app_type = 1");
		if ($merchant_id) {
			$merchant_id = $merchant_id[0]->merchant_id;
		} else {
			return -1;
		}

		//然后再在表t_merchant_conf中通过merchant_id查找到phone
		$phone = \DB::connection("mysql_config")->select("select phone from t_merchant_conf where merchant_id = '$merchant_id'");

		if ($phone) {
			$phone = $phone[0]->phone;
		} else {
			return -1;
		}

		return $phone;
	}

	public static function getAppNameByAppID ()
	{

		//先从表t_app_conf中根据appid和wx_app_type=1查找到merchant_id
		$app_id      = AppUtils::getAppID();
		$merchant_id = \DB::connection("mysql_config")->select("select merchant_id from t_app_conf where app_id = '$app_id' and wx_app_type = 1");
		if ($merchant_id) {
			$merchant_id = $merchant_id[0]->merchant_id;
		} else {
			return -1;
		}

		//然后再在表t_merchant_conf中通过merchant_id查找到phone
		$name = \DB::connection("mysql_config")->select("select name from t_merchant_conf where merchant_id = '$merchant_id'");

		if ($name) {
			$name = $name[0]->name;
		} else {
			return -1;
		}

		return $name;
	}

	public static function setLoginStatus ($openid, $nick_name, $gender, $avatar, $access)
	{
		session(['openid' => $openid]);
		session(['nick_name' => $nick_name]);
		session(['gender' => $gender]);
		session(['avatar' => $avatar]);
		session(['access' => $access]);
		session(['app_id' => self::getAppIdByOpenId($openid)]);

		//通过appid查询客户当前的package_type并写session
		$show_type = \DB::connection('mysql_config')
			->table('t_app_module')
			->where('app_id', '=', self::getAppIdByOpenId($openid))
			->first();

		if ($show_type) {
			if ($show_type->is_show_accountview == 1) {
				session(["is_huidu" => 1]);
			} else {
				session(["is_huidu" => 0]);
			}
		} else {
			session(["is_huidu" => 0]);
		}
		session(["is_huidu" => 1]);

		$app_conf = DB::connection('mysql_config')->table('t_app_conf')
			->where('app_id', '=', self::getAppIdByOpenId($openid))
			->where('wx_app_type', 1)
			->first();

		if ($app_conf) {
			session(['version_type' => $app_conf->version_type]);
			session(['wxapp_join_statu' => self::getWxAppJoinStatus($openid)]);
			session(['is_collection' => $app_conf->use_collection]);
		}
	}

	/**
	 * 通过openid去数据库中取对应的appid
	 *
	 * @param $openid
	 *
	 * @return string
	 */
	public static function getAppIdByOpenId ($openid)
	{
		$app_conf = DB::connection('mysql_config')->select("
select * from t_mgr_login left join t_app_conf on t_mgr_login.merchant_id = t_app_conf.merchant_id where t_mgr_login.openid = ?
", [$openid]);

		if (count($app_conf) == 0) {
			return "";
		}

		session(['app_id' => $app_conf[0]->app_id]);

		return $app_conf[0]->app_id;
	}

	/**
	 * 通过app_id去数据库中取得对应的wx_app_id，用于作为客户是否接入平台的判断
	 *
	 * @param $openid
	 *
	 * @return string
	 * 公众号接入状态 0未接入、1成功接入、9接入未完成
	 */
	public static function getWxAppJoinStatus ($openid)
	{
		//先重置
		session(['wx_app_id' => '']);
		session(['wx_app_name' => '']);
		session(['wx_share_image' => '']);

		$app_id = self::getAppIdByOpenId($openid);
		$h5     = DB::connection("mysql_config")->select("select * from t_app_conf where app_id = ? and wx_app_type = 1", [$app_id]);

		if (count($h5) == 0) {
			return 0;
		}
		if (!empty($h5[0]->wx_app_id)) {
			session(['wx_app_id' => $h5[0]->wx_app_id]);
		}
		if (!empty($h5[0]->wx_mchid) && !empty($h5[0]->wx_mchkey)) {
			session(['wx_app_name' => $h5[0]->wx_app_name]);
			session(['wx_share_image' => $h5[0]->wx_share_image]);

			return 1;
		} else {
			return 9;
		}
	}

	public static function clearLoginStatus ()
	{
		Session::flush();
	}

	public static function getNickName ()
	{
		return session('nick_name', '');
	}

	public static function getGender ()
	{
		return session('gender', '');
	}

	/**子账号名称*
	 *
	 * @param $sub_name
	 */
	public static function setSubName ($sub_name)
	{
		session(['sub_name' => $sub_name]);
	}

	public static function getSubName ()
	{
		return session('sub_name', '');
	}

	/**
	 * 获取扫码openid
	 *
	 * @param $scan_open_id
	 */
	public static function setScanOpenid ($scan_open_id)
	{
		session(['scan_open_id' => $scan_open_id]);
	}

	public static function getScanOpenid ()
	{
		return session('scan_open_id', '');
	}

	/**
	 * 通过appid去数据库中去对应的openid
	 */
	public static function getOpenIdByAppId ($app_id)
	{
		$app_conf = DB::connection('mysql_config')->select("
select * from t_app_conf  left join t_mgr_login on t_mgr_login.merchant_id = t_app_conf.merchant_id where t_app_conf.app_id = ? and wx_app_type = 1
", [$app_id]);

		if (count($app_conf) == 0) {
			return "";
		}

		//        session(['app_id' => $app_conf[0]->app_id]);

		return $app_conf[0]->openid;
	}

	public static function setAppId ($app_id)
	{
		session(['app_id' => $app_id]);
	}

	public static function setOpenId ($openid)
	{
		session(['openid' => $openid]);
	}

	public static function setSuperOpenId ($openid)
	{
		session(['super_openid' => $openid]);
	}

	public static function getSuperOpenId ()
	{
		return session('super_openid', '');
	}

	/**
	 * 获取当前业务的wxappId
	 * @return mixed
	 */
	public static function getWxAppId ()
	{
		return session('wx_app_id', '');
	}

	/**
	 * 获取认证状态;2-已认证
	 * @return string
	 */
	public static function getCertification ()
	{
		$check = \DB::connection("mysql_config")->select("
        select certify_status,t1.merchant_id,wx_app_id,wx_secrete_key,wx_app_name,
        name,company,phone,username,password from
        (
        select merchant_id,certify_status,wx_app_id,wx_secrete_key,wx_app_name,name,company,phone
        from t_merchant_conf
        )t1
        left join
        (
        select merchant_id,openid,name as username,password from t_mgr_login where openid = ?
        )t2
        on t1.merchant_id=t2.merchant_id
        where t2.merchant_id is not null", [AppUtils::getOpenId()]);
		//返回到页面的数据
		$certifyStatus = empty($check) ? '0' : $check[0]->certify_status;

		return $certifyStatus;
	}

	public static function getOpenId ()
	{
		return session('openid', '');
	}

	/**
	 * 获取该应用是否绑定开放平台
	 *
	 * @param $app_id
	 *
	 * @return bool
	 */
	public static function isOpenStatus ($app_id)
	{
		$user = DB::connection('mysql')->table('t_users')->where('app_id', $app_id)
			->orderBy('wx_union_id', 'desc')->first();

		if (!empty($user) && count($user) > 0 && !empty($user->wx_union_id)) {
			return true;
		}

		return false;
	}

	//获取客户配置信息

	/**
	 * 是否直播白名单
	 */
	public static function isWhiltList ($app_id)
	{
		return true;
		if (EnvSetting::environment == '.env' || EnvSetting::environment == '.env.dev') {
			return true;
		}

		$whiltList = [
			'apprnDA0ZDw4581',  //准现网十点读书
			'appTCVlUyvG2205',  //小鹅内容付费

			'appuAhZGRFx3075',  //十点读书现网
			'appUzEnkPEJ8854',  //许岑(美貌大世界)
			'appXZCQF1531816',  //良声英语学院
			'appLqQD18WM7060',  //坚小持读书会
			'app0FWMk07H3974',  //最新课程表
			'appbTVHoqql4573',  //十步智库
			'appYFaSxTlB9414',  //玖时光
		];

		foreach ($whiltList as $item) {
			if ($app_id == $item) {
				return true;
			}
		}

		return false;
	}

	/**
	 * 获取客户的主账号信息
	 */
	public static function getMgrLoginInfo ($openId)
	{
		$mgrLoginInfo = \DB::table("db_ex_config.t_mgr_login")
			->where("openid", '=', $openId)
			->first();

		return $mgrLoginInfo;
	}

	public static function getAppConfInfo ($app_id)
	{
		$app_info = \DB::connection("mysql_config")->table("t_app_conf")
			->where("app_id", "=", $app_id)
			->where("wx_app_type", "=", 1)->first();

		return $app_info;
	}

	/**
	 * 获取代收模式token
	 * @return mixed
	 */
	public static function getCollectionToken ()
	{
		$collection_conf = \DB::connection('mysql_config')->table('t_key_value')
			->select('value', 'updated_at as token_time')
			->where('key', 'collection_access_token')
			->first();

		return $collection_conf;
	}

	/**
	 * 判断账号是否已存在
	 *
	 * @param $name
	 *
	 * @return bool
	 */
	static public function checkUsernameRepeat ($name)
	{
		if (!$name) return false;

		$sql  = "
            SELECT
                name
            FROM
                (
                    SELECT
                        username AS name
                    FROM
                        t_admin_user
                    WHERE
                        username = ?
                    UNION ALL
                        SELECT
                            name
                        FROM
                            t_mgr_login
                        WHERE
                            name = ?
                ) v1
        ";
		$info = DB::connection('mysql_config')->select($sql, [$name, $name]);

		if ($info && count($info) > 0) {
			return false;
		}

		return true;
	}

	/**************权限****************/
	public static function getPrivilege ()
	{
		$privilege = DB::connection('mysql_config')->table('t_admin_privilege')
			->whereNull('deleted_at')->orderBy('id')->get();

		return $privilege;
	}

	/**
	 *获取登录权限
	 * 类型:0-超级管理员/主账户登录/扫码,
	 *     1-子账户登录
	 *     2-体验账号
	 */
	public static function getAccessAuth ($app_id, $id = null, $type)
	{
		$access = [];
		if ($type == 1) {

			$access      = DB::connection('mysql_config')->table('t_admin_user')->where('app_id', $app_id)->where('id', $id)->value('privilege');
			$access      = json_decode($access, true);
			$access[999] = 0;
		} else if ($type == 0 || $type == 2) {
			// 主账号直接取出所有权限列表
			$access = DB::connection('mysql_config')->table('t_admin_privilege')->select(DB::raw('1 as value,id'))
				->whereNull('deleted_at')->orderBy('id')->pluck('value', 'id');

			$access[999] = 1; // 管理员权限
		}
		// 如果是个人模式，置空企业模式订单的列表的权限
		$use_collection = DB::connection('mysql_config')->table('t_app_conf')->where('app_id', $app_id)->where('wx_app_type', 1)->value('use_collection');
		if ($use_collection) {
			$access[126] = 0;
		}

		return $access;
	}

	/**
	 * 获取该业务的url拼接头
	 * if 老用户 then http
	 * else 新用户 then https
	 *
	 * @param $app_id
	 *
	 * @return string
	 */
	public static function getUrlHeader ($app_id)
	{
		$isNewer = self::getIsNew($app_id);

		if ($isNewer == 1) {
			return "http://";
		} else {
			return "https://";
		}
	}

	/*********************权限控制******************************/

	//基础版key
	/**
	 * 获取该业务是否新用户 0新用户 1老用户
	 *
	 * @param $app_id
	 *
	 * @return string
	 */
	public static function getIsNew ($app_id)
	{
		$isNewerInfo = DB::connection('mysql_config')->table('t_app_conf')
			->select('isNewer')
			->where('app_id', '=', $app_id)
			->where('wx_app_type', '=', 1)
			->first();

		if ($isNewerInfo) {
			$isNewer = $isNewerInfo->isNewer;
		} else {
			$isNewer = 0;
		}

		return $isNewer;
	}

	//成长版key

	/**
	 * 获取业务开关对应的key的值
	 *
	 * @param $app_id
	 * @param $key '可查看数据库中app_module字段'
	 *
	 * @return bool
	 */
	public static function getModuleValueByAppId ($app_id, $key)
	{
		//        $app_id = session('app_id', '');
		if (!empty($app_id)) {
			$appModuleInfo = AppUtils::getModuleInfo($app_id);
			if ($appModuleInfo && count($appModuleInfo) > 0) {
				$result = $appModuleInfo[0]->$key;

				return $result;
			}
		}

		return 0;
	}

	//专业版key

	/**
	 * 获取功能模块信息
	 */
	public static function getModuleInfo ($app_id)
	{
		$moduleInfo = DB::connection("mysql_config")
			->select("select * from t_app_module where app_id = ?", [$app_id]);

		return $moduleInfo;
	}

	//企业版专用,非个人版使用

	/**
	 * 判断功能是否显示
	 *
	 * @param $key  '功能模块对应的key'
	 * @param $type 'version_type'-根据版本控制;'app_module'-根据业务开关控制
	 *
	 * @return bool
	 */
	public static function IsPageVisual ($key, $type)
	{
		//0.首先判断个人版是否能用
		if (in_array($key, self::$noPerKey)) {
			if (self::getCollection() == 1) {
				return false;
			}
		}
		//1.版本控制是否显示
		//2.开关控制是否显示
		if ($type == "version_type") {
			return AppUtils::IsVersionVisual($key);
		} else if ($type == "app_module") {
			return AppUtils::IsModuleVisual($key);
		}

		return false;
	}

	/**
	 * 获得用户企业模式
	 * @return mixed
	 */
	public static function getCollection ()
	{
		if (Session::has('is_collection')) {
			$use_collection = Session::get('is_collection');
		} else {
			$use_collection = DB::connection('mysql_config')->where('app_id', AppUtils::getAppID())->where('wx_app_type', 1)->value('use_collection');
		}

		return $use_collection;
	}

	/**
	 * 判断根据版本控制的业务
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	public static function IsVersionVisual ($key)
	{
		/**0.定义key*/
		$baseKey = self::$baseKey;
		$growKey = array_merge($baseKey, self::$growKey);
		$proKey  = array_merge($growKey, self::$proKey);

		/**1.判断版本功能*/
		$version_type = AppUtils::get_version_type();
		if ($version_type == 3) { //专业版
			if (in_array($key, $proKey)) return true;
		} else if ($version_type == 2) { //成长版
			if (in_array($key, $growKey)) return true;
		} else {    //基础班
			if (in_array($key, $baseKey)) return true;
		}

		return false;
	}

	/**
	 * 获取当前业务版本
	 * 1-基础版;2-成长版;3-专业版
	 */
	public static function get_version_type ()
	{
		return session('version_type', 1);
	}

	/***
	 * 根据key值判断开关是否开启
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	public static function IsModuleVisual ($key)
	{
		$moduleResult = AppUtils::getModuleValue($key);

		return $moduleResult;
	}

	/**
	 * 获取业务开关对应的key的值
	 *
	 * @param $key '可查看数据库中app_module字段'
	 *
	 * @return bool
	 */
	public static function getModuleValue ($key)
	{
		$app_id = session('app_id', '');
		if (!empty($app_id)) {
			$appModuleInfo = AppUtils::getModuleInfo($app_id);
			if ($appModuleInfo && count($appModuleInfo) > 0) {
				$result = $appModuleInfo[0]->$key;

				return $result;
			}
		}

		return 0;
	}

	/**
	 * 判断提现是否展示
	 * @deprecated
	 */
	public static function IsVisualWithDraw ()
	{
		$result = false;
		//获取账户余额
		$accountBalance = \DB::connection("db_ex_finance")
			->table('t_usable_balance')
			->where('app_id', AppUtils::getAppID())
			->first();
		if ($accountBalance && $accountBalance->account_balance > 0) {
			$result = true;
		}
		//获取提现记录
		$sqlCon = \DB::connection("mysql_config")->table('t_withdraw_record')
			->where('app_id', AppUtils::getAppID())
			->where('cash_statue', '!=', -1)
			->get();
		if ($sqlCon && count($sqlCon) > 0) {
			$result = true;
		}

		return $result;
	}

	/**
	 *是否是准线网或者小鹅通
	 */
	public static function isOursApp ()
	{
		$app_id     = AppUtils::getAppID();
		$app_module = DB::connection('mysql_config')->select("select has_stream_alive from t_app_module where app_id=? limit 1", [$app_id]);
		if ($app_module) {
			if ($app_module[0]->has_stream_alive) {
				return true;
			}
		}

		return false;
	}

	/**
	 * 获取更新时间和当前时间 的 时间间隔描述
	 *
	 * @param $timeStamp
	 *
	 * @return string
	 */
	public static function getTimeIntervalString ($timeStamp)
	{

		$timeStamp = strtotime($timeStamp);
		$interval  = time() - $timeStamp;
		if ($interval < 60) {
			return $interval . "秒前";
		}

	}
}






