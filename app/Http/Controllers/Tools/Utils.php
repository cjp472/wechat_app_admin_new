<?php
/**
 * Created by PhpStorm.
 * User: zooter
 * Date: 16/3/16
 * Time: 上午10:08
 */

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\AliveVideo\TimRestApi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Intervention\Image\Facades\Image;

class Utils
{

	const ResourceTypeNoType = 0;
	const ResourceTypeImageText = 1;

	//往视频文件中间表t_video_middle_transcode中插入记录
	const ResourceTypeAudio = 2;
	const ResourceTypeVideo = 3;
	const ResourceTypeLive = 4;
	const PaymentTypeFree = 1;
	const PaymentTypeSingle = 2;
	const PaymentTypeProduct = 3;
	const PaymentTypeGroup = 4;

	public function __construct ()
	{
	}

	/**
	 * //curl请求(post)
	 *
	 * @param $wholeUrl
	 * @param $curlPost
	 *
	 * @return mixed
	 */
	public static function curl_file_post_contents ($wholeUrl, $curlPost = '')
	{
		//发包
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $wholeUrl);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$ret = curl_exec($ch);

		return $ret;
	}

	static public function insertRecordVideoTranscode ($params)
	{
		$params['created_at'] = Utils::getTime();
		//查询该file_id是否在中间表中存在
		$is_exist = \DB::table("db_ex_business.t_video_middle_transcode")
			->where("file_id", '=', $params["file_id"])
			->first();
		if ($is_exist) {
			$result = \DB::table("db_ex_business.t_video_middle_transcode")->where("file_id", '=', $params["file_id"])->update($params);
		} else {
			$result = \DB::table("db_ex_business.t_video_middle_transcode")->insert($params);
		}


		return $result;
	}

	static public function getTime ($addSeconds = 0)
	{
		$seconds = time() + $addSeconds;

		return date('Y-m-d H:i:s', $seconds);
	}

	static public function logFrom ($data, $logPath)
	{

		$enableLog = env('Utils_log', false);

		if ($enableLog) {

			if (!is_string($data)) {
				$msg = var_export($data, true);
			} else {
				$msg = $data;
			}

			$timeStamp = time();
			$timeStr   = date('y-m-d G:i:s', $timeStamp);
			error_log("[$timeStr] $msg\n", '3', storage_path('logs/' . $logPath));
		}
	}

	static public function jsonResponse ($data = null, $code = StringConstants::Code_Succeed, $msg = '')
	{
		$data = Utils::replaceAllNullToEmptyString($data);

		return response()->json(Utils::pack($data, $code, $msg));
	}

	static function replaceAllNullToEmptyString ($data)
	{
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				$data[ $key ] = Utils::replaceAllNullToEmptyString($value);
			}
		} else if (is_object($data)) {
			foreach ($data as $key => $value) {
				$data->$key = Utils::replaceAllNullToEmptyString($value);
			}
		} else if ($data === null) {
			$data = '';
		}

		return $data;
	}

	static public function pack ($data, $code = StringConstants::Code_Succeed, $msg = StringConstants::Msg_Succeed)
	{
		$app_id  = AppUtils::getAppID();
		$package = ['code'   => $code, 'msg' => $msg,
					'app_id' => $app_id,
					'data'   => $data];

		return $package;
	}

	static public function wechat_pack ($data, $code = StringConstants::Code_Succeed, $msg = StringConstants::Msg_Succeed)
	{
		$package = ['code' => $code, 'msg' => $msg,
					'data' => $data];

		return $package;
	}

	static public function getUserOpenID ()
	{

		$userInfo = session('wechat.oauth_user');

		$wx_open_id = $userInfo['id'];

		return $wx_open_id;
	}

	static public function getApplierInfo ()
	{

		return session('wechat.oauth_user');
	}

	static public function getAppAccountMoney ()
	{

		$accountBalance = \DB::connection("db_ex_finance")
			->table('t_usable_balance')
			->where('app_id', AppUtils::getAppID())
			->first();
		if ($accountBalance) {
			return $accountBalance->account_balance;
		} else {
			return 0;
		}
	}

	static public function setUserVersion ($version)
	{
		session(['version' => $version]);

	}

	static public function logError ($data)
	{

		if (!is_string($data)) {
			$msg = var_export($data, true);
		} else {
			$msg = $data;
		}

		$timeStamp = time();
		$timeStr   = date('y-m-d G:i:s', $timeStamp);
		error_log("[$timeStr] $msg\n", '3', '../storage/logs/error.log');
	}

	static public function result ($data)
	{
		if (Utils::isEmptyString($data)) {
			return response()->json(Utils::pack("", StringConstants::Code_Failed, StringConstants::Msg_Failed));
		} else {
			return response()->json(Utils::pack($data));
		}
	}

	static public function isEmptyString ($str)
	{
		if (!isset($str) || $str == '') {
			return true;
		} else {
			return false;
		}
	}

	static public function sendsms ($phone, $content)
	{

		$sdkappid = "1400014102";
		$appkey   = "878d0c777c06a29eff31a6302d4140f7";
		$rnd      = random_int(100000, 999999);
		$wholeUrl = "https://yun.tim.qq.com/v3/tlssmssvr/sendsms?sdkappid=" . $sdkappid . "&random=" . $rnd;

		$tel              = new \stdClass();
		$tel->nationcode  = "86";
		$tel->phone       = $phone;
		$jsondata         = new \stdClass();
		$jsondata->tel    = $tel;
		$jsondata->type   = "0";
		$jsondata->msg    = $content;
		$jsondata->sig    = md5($appkey . $phone);
		$jsondata->extend = "";     // 根据需要添加，一般保持默认
		$jsondata->ext    = "";        // 根据需要添加，一般保持默认
		//包体
		$curlPost = json_encode($jsondata);
		//发包
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $wholeUrl);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$ret = curl_exec($ch);

		return $ret;
	}

	static public function isValidPageNumber ($pageNumber)
	{
		return (Utils::isValidNumber($pageNumber) && intval($pageNumber) > 0);
	}

	static public function isValidNumber ($number)
	{
		if (is_numeric($number)) {
			return true;
		} else {
			return false;
		}
	}

	// 获取优惠券id

	static public function isValidPhoneNumber ($phone)
	{
		if (preg_match('/^(0|86|17951)?(13[0-9]|15[012356789]|17[013678]|18[0-9]|14[57])[0-9]{8}$/', $phone)) {
			//验证通过
			return true;
		} else {
			//手机号码格式不对
			return false;
		}
	}

	// 获取优惠券id

	static public function isValidEmail ($email)
	{
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return true;
		} else {
			return false;
		}
	}

	static public function isValidIdCardNumber ($idCardNumber)
	{
		if (preg_match('/^\d{17}(\d|X|x)$/', $idCardNumber)) {
			return true;
		} else {
			return false;
		}
	}

	static public function isValidTimeInDB ($dbTime)
	{
		if ($dbTime == null || Utils::isEmptyString($dbTime)) {
			return false;
		}
		$timestamp = Utils::getTimestamp($dbTime);
		if ($timestamp == false || $timestamp == -1 || $timestamp == 0) {
			return false;
		}

		return true;
	}

	static public function getTimestamp ($dbTime)
	{
		return strtotime($dbTime);
	}

	static public function getLoginId ($prefix = 'login_', $suffixLength = 8)
	{
		return uniqid($prefix, false) . '_' . Utils::generateRandomCode($suffixLength, 'ALL');
	}

	static public function generateRandomCode ($len = 6, $format = 'NUMBER')
	{
		switch ($format) {
			case 'ALL':
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
				break;
			case 'CHAR':
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
				break;
			case 'NUMBER':
				$chars = '0123456789';
				break;
			default :
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
				break;
		}
		mt_srand((double)microtime() * 1000000 * getmypid());
		$code = "";
		while (strlen($code) < $len) {
			$code .= substr($chars, (mt_rand() % strlen($chars)), 1);
		}

		//        Log::info("code".$code);
		return $code;
	}

	static public function getUniId ($prefix = 'e_', $suffixLength = 8)
	{
		return uniqid($prefix, false) . '_' . Utils::generateRandomCode($suffixLength, 'ALL');
	}

	static public function getOrderId ($suffixLength = 8)
	{
		return date("YmdGi", time()) . Utils::generateRandomCode($suffixLength, 'ALL');
	}

	static public function getExcelId ($prefix = 'ex_', $suffixLength = 6)
	{
		return uniqid($prefix, false) . '-' . Utils::generateRandomCode($suffixLength, 'ALL');
	}

	static public function getCouId ($prefix = 'cou_', $suffixLength = 6)
	{
		return uniqid($prefix, false) . '-' . Utils::generateRandomCode($suffixLength, 'ALL');
	}

	//查找cdn上的文件
	//    static function searchCosFile($bucketName,$path){
	//        $result = Cosapi::stat($bucketName, $path);
	//        return $result;
	//    }

	static public function getCouponPlanId ($prefix = 'cp_', $suffixLength = 6)
	{
		return uniqid($prefix, false) . '-' . Utils::generateRandomCode($suffixLength, 'ALL');
	}

	static public function getIp ()
	{
		$onlineip = '';
		if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
			$onlineip = getenv('HTTP_CLIENT_IP');
		} else if (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
			$onlineip = getenv('HTTP_X_FORWARDED_FOR');
		} else if (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
			$onlineip = getenv('REMOTE_ADDR');
		} else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
			$onlineip = $_SERVER['REMOTE_ADDR'];
		}

		return $onlineip == "::1" ? "127.0.0.1" : $onlineip;
	}

	static public function getTimeFromNow ($timeStamp)
	{
		$timeDiff = time() - $timeStamp;
		if ($timeDiff < 60) {
			return '刚刚';
		} else if ($timeDiff < 3600) {
			return floor($timeDiff / 60) . '分钟前';
		} else if ($timeDiff < 3600 * 24) {
			return floor($timeDiff / 3600) . '小时前';
		} else if ($timeDiff < 3600 * 24 * 30) {
			return floor($timeDiff / 3600 * 24) . '天前';
		} else {
			return '1个月前';
		}
	}

	static public function formatMoney ($money, $decimals = 1)
	{
		if ($money < 100) {
			$temp      = $money / 100;
			$result[0] = $temp;
			$result[1] = '元';
		} else if ($money < 10000 * 100) {
			$temp      = $money / 100;
			$result[0] = number_format($temp, $decimals);
			$result[1] = '元';
		} else if ($money < 10000 * 10000 * 100) {
			$temp      = $money / 100 / 10000;
			$result[0] = number_format($temp, $decimals);
			$result[1] = '万';
		} else if ($money < 10000 * 10000 * 10000 * 100) {
			$temp      = $money / 100 / 10000 / 10000;
			$result[0] = number_format($temp, $decimals);
			$result[1] = '亿';
		} else {
			$temp      = $money / 100;
			$result[0] = number_format($temp, $decimals);
			$result[1] = '元';
		}

		return $result;
	}

	static public function encryptMd5 ($baseValue)
	{
		return md5($baseValue);
	}

	/**
	 * 获取随机码列表
	 *
	 * @param $count
	 * @param $len
	 * @param $isAllowDuplicate
	 *
	 * @return array
	 */
	static public function generateRandomCodeArray ($count, $len, $isAllowDuplicate)
	{
		$codes_array = [];
		for ($i = 0; $i < $count;) {
			$code = self::getRandom($len);
			if (!$isAllowDuplicate) {
				//判断数组中是否包含这个值
				if (!in_array($code, $codes_array)) {
					$codes_array[ count($codes_array) ] = $code;
					$i++;
				}
			} else {
				$codes_array[ count($codes_array) ] = $code;
				$i++;
			}

		}

		return $codes_array;
	}

	static public function getRandom ($len = 6, $format = 'NUMBER')
	{
		switch ($format) {
			case 'ALL':
				/****默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1****/
				$chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
				break;
			case 'CHAR':
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
				break;
			case 'NUMBER':
				$chars = '0123456789';
				break;
			default :
				$chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
				break;
		}
		$code = "";
		while (strlen($code) < $len) {

			$code .= substr($chars, (rand(0, strlen($chars) - 1)), 1);
		}

		return $code;
	}

	/**
	 * 开启异步线程
	 *
	 * @param $url
	 * @param $file_url
	 */
	public static function asyncThread ($url)
	{
		$url = $url . '&url_app_id=' . (empty(AppUtils::getOpenId()) ? "" : AppUtils::getAppIdByOpenId(AppUtils::getOpenId()));
		$port = env("HOST_PORT");
		$fp   = fsockopen("localhost", $port, $errorNo, $errorStr, 30);
		if (!$fp) {
			echo "$errorStr ($errorNo) <br />\n";

			return;
		} else {
			//            stream_set_blocking($fp,0);//开启非阻塞模式
			$out = "GET " . $url . " HTTP/1.1\r\n";
			$out .= "Host: localhost\r\n";
			$out .= "Connection: Close\r\n\r\n";
			fwrite($fp, $out);
			/*while (!feof($fp)) {
				$loginfo = fgets($fp, 128);
				if ($loginfo) {
				}
			}*/
			fclose($fp);
		}
	}

	static public function log ($data)
	{

		$enableLog = env('Utils_log', false);

		if ($enableLog) {

			if (!is_string($data)) {
				$msg = var_export($data, true);
			} else {
				$msg = $data;
			}

			$timeStamp = time();
			$timeStr   = date('y-m-d G:i:s', $timeStamp);
			$date      = date('y-m-d', $timeStamp);
			error_log("[$timeStr] $msg\n", '3', storage_path('logs/debug_' . $date . '.log'));
		}
	}

	static public function downloadFileFromNetAlive ($localFilePath, $file_url)
	{

		if (!isset($file_url)) die();

		$curl = Utils::curl_file_get_contents($file_url);

		file_put_contents($localFilePath, $curl);

		return $localFilePath;
	}

	static public function curl_file_get_contents ($durl)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $durl);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		//        curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
		//        curl_setopt($ch, CURLOPT_REFERER, _REFERER_);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$r = curl_exec($ch);
		curl_close($ch);
		return $r;
	}

	/**
	 * 音频压缩
	 *
	 * @param     $src_url
	 * @param int $bitrate
	 *
	 * @return string
	 */
	public static function audioCompressing ($src_url, $bitrate = 64000)
	{

		$destination_folder = storage_path('app/public/audio/');
		$prefix = substr(strrchr(basename($src_url), '.'), 1);
		$tar_url            = $destination_folder . time() . '_' . basename($src_url, '.'.$prefix) . '.mp3';

		$cmd = 'ffmpeg -y -i ' . $src_url . ' -ab ' . $bitrate . ' -ar 44100 -ac 2 -f mp3 ' . $tar_url;
		exec($cmd . " 2>&1", $output, $status);

		return $tar_url;
	}

	/**
	 * m3u8音频压缩
	 *
	 * @param $src_url
	 * @param $prefix
	 *
	 * @return string
	 */
	public static function audioM3u8Compressing ($src_url, $prefix)
	{

		$destination_folder = storage_path('app/public/audio/' . $prefix . '/');
		if (!is_dir($destination_folder)) { //如果目录不存在则创建
			@mkdir($destination_folder, 0777, true);
		}
		$tar_url = $destination_folder . $prefix . ".m3u8";

		$cmd = 'ffmpeg -y -i ' . $src_url . ' -ab 64k -c:a aac -strict -2 -f hls -hls_time 10 -hls_list_size 0 ' . $tar_url;
		exec($cmd . " 2>&1", $output, $status);

		return $tar_url;
	}

	/**
	 * 视频转码为H.264
	 *
	 * @param $src_url
	 *
	 * @return string
	 */
	public static function videoTransCoding ($src_url)
	{

		$destination_folder = storage_path('app/public/video/');
		$tar_url            = $destination_folder . basename($src_url);

		$cmd = 'ffmpeg  -i ' . $src_url . ' -c:v libx264 -strict -2 ' . $tar_url;
		exec($cmd . " 2>&1", $output, $status);

		return $tar_url;
	}

	/**
	 * 上传压缩音频
	 *
	 * @param      $video_path
	 * @param      $app_id
	 * @param null $prefix
	 *
	 * @return string
	 */
	public static function uploadMp3Audio ($video_path, $app_id, $prefix = null)
	{

		if (empty($prefix)) {
			$dst_path = '/' . $app_id . env('AUDIO_COMPRESS_PATH') . "" . basename($video_path);
		} else {
			$dst_path = '/' . $app_id . env('AUDIO_COMPRESS_PATH') . "" . $prefix . "/" . basename($video_path);
		}

		$uploadResult = V4UploadUtils::uploadFile($video_path, $dst_path, null, null, 1);

		if ($uploadResult['code'] == 0 || $uploadResult['code'] == '-4018' || $uploadResult['code'] == '-177') {

			$fileRoot = 'http://' . GlobalString::V4_COS_BUCKET_NAME . '-' . GlobalString::V4_COS_APP_ID . '.file.myqcloud.com';

			return $fileRoot . "" . $dst_path;
		} else {
			return '';
		}

	}

	/**
	 * 上传转码视频
	 *
	 * @param $video_path
	 *
	 * @return string
	 */
	public static function uploadMp4Video ($video_path, $app_id)
	{

		$dst_path = '/' . $app_id . env('MP4_VIDEO_PATH') . "" . basename($video_path);

		$uploadResult = V4UploadUtils::uploadFile($video_path, $dst_path);

		if ($uploadResult['code'] == 0 || $uploadResult['code'] == '-4018' || $uploadResult['code'] == '-177') {

			$fileRoot = 'http://' . GlobalString::V4_COS_BUCKET_NAME . '-' . GlobalString::V4_COS_APP_ID . '.file.myqcloud.com';

			return $fileRoot . "" . $dst_path;
		} else {
			return '';
		}
	}

	//创建直播房间

	/**
	 * 上传压缩图片
	 *
	 * @param $image_path
	 * @param $app_id
	 *
	 * @return string
	 */
	public static function uploadCompressImage ($image_path, $app_id)
	{
		$dst_path = '/' . $app_id . env('IMAGE_COMPRESS_PATH') . "" . basename($image_path);
		$uploadResult = V4UploadUtils::uploadFile($image_path, $dst_path);


		if ($uploadResult['code'] == 0 || $uploadResult['code'] == '-4018' || $uploadResult['code'] == '-177') {

			$fileRoot = 'http://' . GlobalString::V4_COS_BUCKET_NAME . '-' . GlobalString::V4_COS_APP_ID . '.file.myqcloud.com';

			return $fileRoot . "" . $dst_path;
		} else {
			return '';
		}

	}

	//分类数据信息

	/**
	 * 上传excel文件
	 *
	 * @param $src_path
	 * @param $app_id
	 *
	 * @return string
	 */
	public static function uploadExcel ($src_path, $app_id)
	{
		$dst_path = '/' . $app_id . env('EXCEL_PATH') . "" . basename($src_path);
		$uploadResult = V4UploadUtils::uploadFile($src_path, $dst_path);


		if ($uploadResult['code'] == 0 || $uploadResult['code'] == '-4018' || $uploadResult['code'] == '-177') {

			$fileRoot = 'http://' . GlobalString::V4_COS_BUCKET_NAME . '-' . GlobalString::V4_COS_APP_ID . '.file.myqcloud.com';

			return $fileRoot . "" . $dst_path;
		} else {
			return '';
		}

	}

	/**
	 * 压缩图片保存到本地
	 *
	 * @param     $image_path
	 * @param int $image_width
	 * @param int $image_height
	 * @param int $image_quality
	 *
	 * @return string
	 */
	public static function imageCompress ($image_path, $image_width = 160, $image_height = 120, $image_quality = 80)
	{
		ini_set('memory_limit', '100M');

		$destination_folder = storage_path('app/public/image/');
		$tar_url            = $destination_folder . basename($image_path);

		try {
			Image::make($image_path)->resize($image_width, $image_height)->save($tar_url, $image_quality);
		} catch (\Exception $ex) {
			return $image_path;
		}


		return $tar_url;
	}

	/**
	 * 压缩图片保存到本地
	 *
	 * @param     $imageData
	 * @param     $file_name
	 * @param int $image_width
	 * @param int $image_height
	 * @param int $image_quality
	 *
	 * @return string
	 */
	public static function imageCompressRemote ($imageData, $file_name, $image_width = 160, $image_height = 120, $image_quality = 80)
	{
		ini_set('memory_limit', '100M');

		$destination_folder = storage_path('app/public/image/');
		$tar_url            = $destination_folder . $file_name . ".png";

		try {
			Image::make($imageData)->resize($image_width, $image_height)->save($tar_url, $image_quality);
		} catch (\Exception $ex) {
			return null;
		}


		return $tar_url;
	}

	/**
	 * 渠道URL生成
	 *
	 * @param $channel_id
	 * @param $type 2资源;专栏3
	 * @param $resource_type
	 * @param $resource_id
	 * @param $product_id
	 * @param $app_id
	 *
	 * @return string
	 */
	public static function contentUrl ($channel_id, $type, $resource_type, $resource_id, $product_id, $app_id)
	{
		if (!empty($channel_id)) {
			$params["channel_id"] = $channel_id;
		}
		$params["type"]          = $type;
		$params["resource_type"] = $resource_type;
		$params["resource_id"]   = $resource_id;
		$params["product_id"]    = $product_id;
		$params["app_id"]        = $app_id;

		$paramsJsonStr = json_encode($params);
		//dump($paramsJsonStr);
		$paramsBase64Str = Utils::urlSafe_b64encode($paramsJsonStr);

		return '/content_page/' . $paramsBase64Str;
	}

	//获取用户今天新增视频数量

	/**
	 * @param $string
	 *
	 * @return mixed|string
	 * base64编码
	 */
	static public function urlSafe_b64encode ($string)
	{
		$data = base64_encode($string);
		$data = str_replace(['+', '/', '='], ['-', '_', ''], $data);

		return $data;
	}

	//返回限定用户每天新增视频限制数量

	public static function contentUrlHome ($channel_id)
	{
		$params["channel_id"] = $channel_id;

		$paramsJsonStr = json_encode($params);

		$paramsBase64Str = Utils::urlSafe_b64encode($paramsJsonStr);

		return $paramsBase64Str;
	}

	//更新音频中图片大小

	public static function createGroupChatRoom ()
	{
		//创建api实例
		$api        = new TimRestApi();
		$identifier = env('AliveVideoAdminId');
		$api->init(env('AliveVideoAppId'), $identifier);
		//获取管理员签名文件
		$api->generate_user_sig($identifier,
			180 * 24 * 60 * 60,
			storage_path('key/private_key'),
			storage_path('alive_video/signature/linux-signature64'));

		//房间类型，直播房间
		$group_type = "AVChatRoom";
		//房间名
		$group_name = "TestGroup";
		//所有者id
		$owner_id = $identifier;

		//得到一個json结构：{"ActionStatus":"OK","ErrorCode":0,"GroupId":"@TGS#aGPR76GER"}
		$ret = $api->group_create_group($group_type, $group_name, $owner_id);
		//        $resultObj = json_decode($ret, true);
		//        return $resultObj['GroupId'];
		$data['room_id'] = $ret['GroupId'];

		return $data['room_id'];
	}

	//更新视频中图片大小

	public static function categoryInfo ()
	{
		// 分类数据信息
		$category_info = \DB::connection('mysql')->table('t_category')
			->where('app_id', '=', AppUtils::getAppID())
			->where('id', '!=', '0')
			->orderby('weight', 'desc')
			->Lists('category_name', 'id');

		return $category_info;
	}

	//更新图文中图片大小

	/**
	 * 删除目录及目录下所有文件或删除指定文件
	 *
	 * @param string   $path   待删除目录路径
	 * @param bool|int $delDir 是否删除目录，1或true删除目录，0或false则只删除文件保留目录（包含子目录）
	 *
	 * @return bool 返回删除状态
	 */
	public static function delDirAndFile ($path, $delDir = false)
	{
		if (strlen($path) < 20)//防止删除服务器根目录
		{
			return false;
		}
		$handle = opendir($path);//目录下是否有内容
		if ($handle)//有内容
		{
			while (false !== ($item = readdir($handle)))//递归删除
			{
				if ($item != "." && $item != "..")
					is_dir("$path/$item") ? self::delDirAndFile("$path/$item", $delDir) : self::unlinkStoragePath("$path/$item");
			}
			closedir($handle);
			if ($delDir)
				return rmdir($path);
		} else//没内容,直接删除文件夹就ok
		{
			if (file_exists($path)) {
				return self::unlinkStoragePath($path);
			} else {
				return false;
			}
		}
	}

	//若为单笔则价格不能为0

	/**
	 * 删除storage_path下文件
	 *
	 * @param $path
	 *
	 * @return bool
	 */
	public static function unlinkStoragePath ($path)
	{
		if (!@unlink($path)) {
			return @unlink(self::getStoragePath($path));
		} else {
			return true;
		}
	}

	//若客户使用的是"个人运营模式"即use_collection=1,则限制其piece_price不能超过1000元

	/**
	 * 获取绝对路径
	 *
	 * @param $path
	 *
	 * @return string
	 */
	public static function getStoragePath ($path)
	{
		if (strpos($path, storage_path()) !== false) {
			return substr($path, strlen(storage_path()) + 1);
		} else {
			return $path;
		}
	}

	static public function getVideoUpload ($appid)
	{
		$today  = date('Y-m-d', time());
		$upload = \DB::select("select count(*) as count from t_video where app_id='$appid' and date(created_at) = '$today' ")[0];

		return $upload->count;
	}

	static public function getVideoMax ()
	{
		return 200;
	}

	static public function updateAudioImgTotalSize ($item)
	{
		$total_image_size = 0;

		$app_id              = $item->app_id;
		$id                  = $item->id;
		$img_url_compressed  = $item->img_url;
		$sign_url_compressed = $item->sign_url;

		$descrb = $item->descrb;

		if (!empty($descrb)) {
			$json = json_decode($descrb);
			if (!empty($json)) {
				foreach ($json as $jsonItem) {
					if ($jsonItem->type == 1) {
						$imageList[] = $jsonItem->value;
					}
				}
			}
		}
		$imageList[] = $img_url_compressed;
		$imageList[] = $sign_url_compressed;

		foreach ($imageList as $imageItem) {
			if (!empty($imageItem)) {
				$fileInfo = V4UploadUtils::statFileInfo($imageItem);
				if ($fileInfo['code'] == 0 && array_key_exists('filesize', $fileInfo['data'])) {
					$size = number_format($fileInfo['data']['filesize'] / 1024 / 1024, 3);

					//                        dump($size);
					$total_image_size = (float)$total_image_size + (float)$size;
				}
			}
		}
		//            dump($app_id. " ". $id. " ". $total_image_size);

		$updateResult = DB::connection('mysql')->table('t_audio')
			->where('app_id', '=', $app_id)
			->where('id', '=', $id)
			->update(['img_size_total' => $total_image_size]);


		return $updateResult;
	}

	static public function updateVideoImgTotalSize ($item)
	{
		$total_image_size = 0;

		$app_id                   = $item->app_id;
		$id                       = $item->id;
		$img_url_compressed       = $item->img_url;
		$patch_img_url_compressed = $item->patch_img_url;

		$descrb = $item->descrb;

		if (!empty($descrb)) {
			$json = json_decode($descrb);
			if (!empty($json)) {
				foreach ($json as $jsonItem) {
					if ($jsonItem->type == 1) {
						$imageList[] = $jsonItem->value;
					}
				}
			}
		}
		$imageList[] = $img_url_compressed;
		$imageList[] = $patch_img_url_compressed;

		foreach ($imageList as $imageItem) {
			if (!empty($imageItem)) {
				$fileInfo = V4UploadUtils::statFileInfo($imageItem);
				if ($fileInfo['code'] == 0 && array_key_exists('filesize', $fileInfo['data'])) {
					$size = number_format($fileInfo['data']['filesize'] / 1024 / 1024, 3);

					//                        dump($size);
					$total_image_size = (float)$total_image_size + (float)$size;
				}
			}
		}
		//            dump($app_id. " ". $id. " ". $total_image_size);

		$updateResult = DB::connection('mysql')->table('t_video')
			->where('app_id', '=', $app_id)
			->where('id', '=', $id)
			->update(['img_size_total' => $total_image_size]);


		return $updateResult;
	}

	static public function updateImageTextTotalSize ($item)
	{
		$total_image_size = 0;

		$app_id             = $item->app_id;
		$id                 = $item->id;
		$img_url_compressed = $item->img_url;

		$descrb = $item->content;

		if (!empty($descrb)) {
			$json = json_decode($descrb);
			if (!empty($json)) {
				foreach ($json as $jsonItem) {
					if ($jsonItem->type == 1) {
						$imageList[] = $jsonItem->value;
					}
				}
			}
		}
		$imageList[] = $img_url_compressed;

		foreach ($imageList as $imageItem) {
			if (!empty($imageItem)) {
				$fileInfo = V4UploadUtils::statFileInfo($imageItem);
				if ($fileInfo['code'] == 0 && array_key_exists('filesize', $fileInfo['data'])) {
					$size = number_format($fileInfo['data']['filesize'] / 1024 / 1024, 3);

					//                        dump($size);
					$total_image_size = (float)$total_image_size + (float)$size;
				}
			}
		}
		//            dump($app_id. " ". $id. " ". $total_image_size);

		$updateResult = DB::connection('mysql')->table('t_image_text')
			->where('app_id', '=', $app_id)
			->where('id', '=', $id)
			->update(['img_size_total' => $total_image_size]);


		return $updateResult;
	}

	// 资源类型：0-无（不通过资源的购买 如团购）、1-图文、2-音频、3-视频、4-直播

	static public function checkPiecePrice ($params)
	{
		if ((array_key_exists('payment_type', $params)
				&& array_key_exists('piece_price', $params)
				&& $params['payment_type'] == 2
				&& $params['piece_price'] == 0) || (
				array_key_exists('payment_type', $params)
				&& array_key_exists('piece_price', $params)
				&& $params['payment_type'] == 2
				&& $params['piece_price'] >= 100000000)
		) {
			return "上传失败，单价需大于0元或低于100万!";
		} else {
			return 0;
		}
	}

	static public function checkPersonModelPrice ($params)
	{

		return 0;

		//若客户使用的是"个人运营模式"即use_collection=1,则限制其piece_price不能超过1000元
		//        $model_result=\DB::connection("mysql_config")->table("t_app_conf")
		//            ->where("app_id","=",AppUtils::getAppIdByOpenId(AppUtils::getOpenId()))
		//            ->where("wx_app_type","=",1)
		//            ->first();
		//
		//        if ($model_result->use_collection == 1) {
		//            if(array_key_exists('piece_price',$params) && $params['piece_price'] >100000 || array_key_exists('price',$params) && $params['price'] >100000){
		//                //TODO:返回操作失败,个人运营模式下 定价不能超过1000元
		//                return "上传失败，单价不能超过1000元!";
		//            }else{
		//                return 0;
		//            }
		//        }else{
		//            return 0;
		//        }
	}

	/**
	 * 支持中文字符串截取 防乱码
	 */
	static public function msubstr ($str, $start = 0, $length, $charset = "utf-8", $suffix = true)
	{
		switch ($charset) {
			case 'utf-8':
				$char_len = 3;
				break;
			case 'UTF8':
				$char_len = 3;
				break;
			default:
				$char_len = 2;
		}
		//小于指定长度，直接返回
		if (strlen($str) <= ($length * $char_len)) {
			return $str;
		}
		if (function_exists("mb_substr")) {
			$slice = mb_substr($str, $start, $length, $charset);
		} else if (function_exists('iconv_substr')) {
			$slice = iconv_substr($str, $start, $length, $charset);
		} else {
			$re['utf-8']  = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
			$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
			$re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
			$re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
			preg_match_all($re[ $charset ], $str, $match);
			$slice = join("", array_slice($match[0], $start, $length));
		}
		if ($suffix)
			return $slice;

		return $slice;
	}

	/**
	 * @param             $payment_type
	 * @param             $app_id
	 * @param             $resourceType
	 * @param             $resourceId
	 * @param             $productId
	 * @param null|string $channelId
	 *
	 * @return string 获取内容页的url
	 * 获取内容页的url
	 */
	static public function getContentUrlWithAppId ($payment_type, $app_id, $resourceType, $resourceId, $productId, $channelId = "")
	{
		$payment_type            = $payment_type == 1 ? "2" : "$payment_type"; //保持与前端生成格式一致
		$params["type"]          = $payment_type;
		$params["resource_type"] = $resourceType;
		$params["resource_id"]   = $resourceId;
		$params["app_id"]        = $app_id;
		$params["product_id"]    = $productId ? $productId : ""; //保持与前端生成格式一致
		if (Utils::isValidId($channelId)) {
			$params['channel_id'] = $channelId;
		}

		$paramsJsonStr = json_encode($params);

		$paramsBase64Str = Utils::urlSafe_b64encode($paramsJsonStr);

		return '/content_page/' . $paramsBase64Str;
	}

	static public function isValidId ($id)
	{

		if (strlen($id) > 0) {
			return true;
		}

		return false;
	}
	// 付费类型：1-免费、2-单笔、3-付费产品包、4-团购

	/**
	 * @param      $payment_type
	 * @param      $resourceType
	 * @param      $resourceId
	 * @param      $productId
	 * @param null $channelId
	 *
	 * @return string
	 * 获取内容页的url
	 */
	static public function getContentUrl ($payment_type, $resourceType, $resourceId, $productId, $channelId = "")
	{
		$payment_type            = $payment_type == 1 ? "2" : "$payment_type"; //保持与前端生成格式一致
		$params["type"]          = $payment_type;
		$params["resource_type"] = $resourceType;
		$params["resource_id"]   = $resourceId;
		$params["app_id"]        = AppUtils::getAppID();
		$params["product_id"]    = $productId ? $productId : ""; //保持与前端生成格式一致
		if (Utils::isValidId($channelId)) {
			$params['channel_id'] = $channelId;
		}

		$paramsJsonStr = json_encode($params);

		$paramsBase64Str = Utils::urlSafe_b64encode($paramsJsonStr);

		return '/content_page/' . $paramsBase64Str;
	}

	static function getExtraInfoContentUrl ($rawUrl, $extraInfo)
	{

		$delimiter = 'content_page/';
		$urlArrays = explode($delimiter, $rawUrl);
		if (count($urlArrays) != 2) {
			return $rawUrl;
		}
		$encodedData = $urlArrays[1];
		$decodedData = Utils::urlSafe_b64decode($encodedData);
		$decodedData = json_decode($decodedData, true);
		if (!is_array($decodedData)) {
			return $rawUrl;
		}
		$decodedData['extra_data'] = $extraInfo;
		$newEncodeData             = self::urlSafe_b64encode(json_encode($decodedData));
		if (self::isEmptyString($newEncodeData)) {
			return $rawUrl;
		}
		$newUrl = $urlArrays[0] . $delimiter . $newEncodeData;

		return $newUrl;
	}

	static public function urlSafe_b64decode ($string)
	{
		$data = str_replace(['-', '_'], ['+', '/'], $string);
		$mod4 = strlen($data) % 4;
		if ($mod4) {
			$data .= substr('====', $mod4);
		}

		return base64_decode($data);
	}

	static public function getResourceInfo ($resource_id, $resource_type)
	{

		if ($resource_type < 0 || $resource_type > 4) {

			return 0;
		}

		switch ($resource_type) {
			case 1:
				$articleInfo = Utils::getArticleInfo($resource_id);

				return $articleInfo;
			case 2:
				$audioInfo = Utils::getAudioInfo($resource_id);

				return $audioInfo;
			case 3:
				$videoInfo = Utils::getVideoInfo($resource_id);

				return $videoInfo;
			case 4:
				$aliveInfo = Utils::getAliveInfo($resource_id);

				return $aliveInfo;
		}
	}

	//获取资源信息

	static public function getArticleInfo ($id)
	{
		$app_id = AppUtils::getAppID();

		$articleInfo = \DB::table("db_ex_business.t_image_text")
			->where('app_id', '=', $app_id)
			->where('id', '=', $id)
			->first();

		return $articleInfo;
	}

	//获取图文资源的信息

	static public function getAudioInfo ($id)
	{
		$app_id = AppUtils::getAppID();

		$audioInfo = \DB::table("db_ex_business.t_audio")
			->where('app_id', '=', $app_id)
			->where('id', '=', $id)
			->first();

		return $audioInfo;
	}

	//获取音频资源的信息

	static public function getVideoInfo ($id)
	{
		$app_id = AppUtils::getAppID();

		$videoInfo = \DB::table("db_ex_business.t_video")
			->where('app_id', '=', $app_id)
			->where('id', '=', $id)
			->first();

		return $videoInfo;
	}

	//获取视频资源的信息

	static public function getAliveInfo ($id)
	{
		$app_id = AppUtils::getAppID();

		$aliveInfo = \DB::table("db_ex_business.t_alive")
			->where('app_id', '=', $app_id)
			->where('id', '=', $id)
			->first();
		//直播状态
		$aliveInfo->alive_state = self::getAliveState($aliveInfo->zb_start_at, $aliveInfo->video_length, $aliveInfo->manual_stop_at, $aliveInfo->zb_stop_at, $aliveInfo->push_state, $aliveInfo->rewind_time, $aliveInfo->alive_type);

		return $aliveInfo;
	}

	//获取直播资源的信息

	/**直播状态
	 *
	 * @param $zb_start_at      直播开始时间
	 * @param $total_time       视频长度
	 * @param $manual_stop_at   手动结束时间
	 * @param $zb_stop_at       直播结束时间
	 * @param $push_state       推流状态
	 * @param $rewind_time      状态时间
	 * @param $alive_type       直播类型
	 *
	 * @return int
	 */
	public static function getAliveState ($zb_start_at, $total_time, $manual_stop_at, $zb_stop_at, $push_state, $rewind_time, $alive_type)
	{
		if ($alive_type == 2) {
			return self::getAliveState2($zb_start_at, $manual_stop_at, $zb_stop_at, $push_state, $rewind_time);
		} else if ($alive_type == 1) {
			return self::getAliveState1($zb_start_at, $total_time, $manual_stop_at, $zb_stop_at);
		} else {
			return self::getAliveState1($zb_start_at, $zb_stop_at - $zb_start_at, $manual_stop_at, $zb_stop_at);
		}
	}

	//PHP发送post请求

	/**在线直播的状态_
	 *
	 * @param $zb_start_at
	 * @param $manual_stop_at
	 * @param $zb_stop_at
	 * @param $push_state   推流状态
	 * @param $rewind_time  断流时间
	 *
	 * @return int
	 */
	public static function getAliveState2 ($zb_start_at, $manual_stop_at, $zb_stop_at, $push_state, $rewind_time)
	{
		$alive_state = 0; //直播状态:0-还未开始  1-直播中  2-互动时间  3-直播结束了（回播） 4-离开

		//判断直播是否已经开始了
		if (!$manual_stop_at && time() - strtotime($zb_start_at) < 0 && $push_state == 2) return 0;
		//        if (!$manual_stop_at && time() - strtotime($zb_stop_at) < 0 && $push_state == 2) return 0;

		$alive_state = 1; //播放已开始
		//判断播放是否结束了
		if ($manual_stop_at && strtotime($manual_stop_at) < time()) {//手动结束
			if ($push_state != 1 && strtotime($rewind_time) + 300 < time()) { //已经断流，
				$alive_state = 3;//直播结束
			} else {
				$alive_state = 2;//但还未到回播时间，即为互动时间
			}
		}
		if (empty($manual_stop_at) && strtotime($zb_stop_at) < time() && $push_state != 1) { //设定直播时间已经到了,并且断流
			if (strtotime($rewind_time) + 300 < time()) { //断流超过5分钟
				$alive_state = 3;//直播结束
			} else {
				$alive_state = 4;//等待推流
			}
		}
		if (empty($manual_stop_at) && strtotime($zb_stop_at) > time() && $push_state != 1) {//直播时间内断流等待
			$alive_state = 4;//等待推流
		}

		return $alive_state;
	}

	/**在线直播的状态_
	 *
	 * @param $zb_start_at
	 * @param $manual_stop_at
	 * @param $total_time
	 * @param $zb_stop_at
	 *
	 * @return int
	 */
	public static function getAliveState1 ($zb_start_at, $total_time, $manual_stop_at, $zb_stop_at)
	{
		$alive_state = 0; //直播状态:0-还未开始  1-直播中  2-视频播放结束  3-直播结束了

		//判断直播是否已经开始了
		if (time() - strtotime($zb_start_at) > 0) {
			$alive_state = 1; //播放已开始
			//判断播放是否结束了
			$total_time = floatval($total_time);

			//判断视频是否结束了
			if (time() - strtotime($zb_start_at) - $total_time >= 0) {
				$alive_state = 2;
			}

			//判断直播是否结束了
			if (empty($manual_stop_at) && !empty($zb_stop_at)) {

				if (time() - strtotime($zb_stop_at) >= 0) {
					$alive_state = 3;
				};

			} else if (!empty($manual_stop_at) && !empty($zb_stop_at)) {

				if ((time() - strtotime(($zb_stop_at > $manual_stop_at ? $manual_stop_at : $zb_stop_at))) >= 0) {
					$alive_state = 3;
				};
			}
		}

		return $alive_state;
	}

	public static function send_post ($url, $post_data)
	{
		//        $postdata = http_build_query($post_data);
		$options = [
			'http' => [
				'method'  => 'POST',//or GET
				'header'  => 'Content-type:application/x-www-form-urlencoded',
				'content' => $post_data,
				'timeout' => 15 * 60 // 超时时间（单位:s）
			],
		];
		$context = stream_context_create($options);
		$result  = file_get_contents($url, false, $context);

		return $result;
	}

	/**
     *发送get请求
     * $url 请求地址
     * $get_data get数据
    */
    public static function send_get($url, $get_data=[]) {
        if( is_array($get_data) ) {
            if (count($get_data) > 0) {
                $params = http_build_query($get_data);
                $url .= '?'.$params;
            }
        } else {
            return '参数格式不合法';
        }
        $options = array(
            'http' => array(
                'method' => 'GET',
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'timeout' => 15 * 60 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }

	//判断价格 单位是分  默认是100万元和0元

	/**
	 * @param $string
	 *
	 * @return mixed
	 * 过滤非utf-8字符
	 */
	static public function filterUtf8 ($string)
	{
		if ($string) {
			//先把正常的utf8替换成英文逗号
			$result = preg_replace('%(
[\x09\x0A\x0D\x20-\x7E]
| [\xC2-\xDF][\x80-\xBF]
| \xE0[\xA0-\xBF][\x80-\xBF]
| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}
| \xED[\x80-\x9F][\x80-\xBF]
| \xF0[\x90-\xBF][\x80-\xBF]{2}
| [\xF1-\xF3][\x80-\xBF]{3}
| \xF4[\x80-\x8F][\x80-\xBF]{2}
)%xs', ',', $string);
			//转成字符数字
			$charArr = explode(',', $result);
			//过滤空值、重复值以及重新索引排序
			$findArr = array_values(array_flip(array_flip(array_filter($charArr))));

			return $findArr ? str_replace($findArr, "", $string) : $string;
		}

		return $string;
	}

	//h5资源url转换为小程序url

	public static function array_remove (&$data, $key)
	{
		if (!array_key_exists($key, $data)) {
			return '';
		}
		$keys  = array_keys($data);
		$index = array_search($key, $keys);
		if ($index !== false) {
			$output = array_splice($data, $index, 1);
			$output = $output[ $key ];
		}

		return $output;
	}

	public static function checkPrice ($price, $max = 100000000, $min = 0)
	{
		if ($price > $max || $price < $min)
			return '上传失败，单价需大于0元或低于' . $max / 1000000 . '万!';

		return 0;
	}

	public static function resourceH5UrlTransToAppUrl ($resource_url)
	{
		if (strpos($resource_url, "content_page") == false) { //非资源链接
			return "";
		}
		$delimiter = 'content_page/';
		$urlArrays = explode($delimiter, $resource_url);

		if (count($urlArrays) == 1) {
			$resource_params = $urlArrays[0];
		} else if (count($urlArrays) == 2) {
			$resource_params = $urlArrays[1];
		} else {
			return "";
		}
		$paramStr  = self::urlSafe_b64decode($resource_params);
		$bizData   = json_decode($paramStr);
		$page_path = "";
		//判断参数
		if (property_exists($bizData, 'type')
			&& property_exists($bizData, 'resource_type')
			&& property_exists($bizData, 'resource_id')
			&& property_exists($bizData, 'product_id')
		) {
			$type         = $bizData->type;
			$resourceType = $bizData->resource_type;
			$resourceId   = $bizData->resource_id;
			$productId    = $bizData->product_id;

			$page_path = ""; //对应的小程序页面路径
			if ($type == 3) { //跳专栏
				$page_path = "page/home/columnist/columnist?id=" . $productId;
			} else if ($type == 2) {//跳资源页
				switch ($resourceType) {
					case StringConstants::SINGLE_GOODS_ARTICLE:
						$page_path = "page/home/content/content_img_text/content_img_text?id=" . $resourceId;
						break;
					case StringConstants::SINGLE_GOODS_AUDIO:
						$page_path = "page/home/content/content_audio/content_audio?id=" . $resourceId;
						break;
					case StringConstants::SINGLE_GOODS_VIDEO:
						$page_path = "page/home/content/content_video/content_video?id=" . $resourceId;
						break;
					case StringConstants::SINGLE_GOODS_ALIVE:
						$page_path = "page/home/content/content_alive/content_alive_desc/content_alive_desc?id=" . $resourceId;
						break;
				}
			}

		}

		return $page_path;
	}

	/**
	 * 查询用户是否开启了小程序(必须是企业模式)
	 */
	public static function isHasLittleProgram ()
	{
		$hasLittleProgram = false;
		$app_id           = AppUtils::getAppID();

		//查询是否开启了独立小程序(企业模式小程序)
		$url         = env('MINI_STATUS', '') . "?app_id={$app_id}";
		$status_info = json_decode(file_get_contents($url));
		if ($status_info) {
			if (is_object($status_info)
				&& property_exists($status_info, "code")
				&& $status_info->code === 0
			) {
				$result_data = $status_info->data;
				if (is_object($result_data)
					&& property_exists($result_data, "current_status")
				) {
					$status = $status_info->data->current_status->operate_type;
					if ($status == 7) {
						$hasLittleProgram = true;
					}
				}
			}
		}

		return $hasLittleProgram;
	}

	/**
	 * @param $m3u8_path
	 * return listNew
	 *
	 * @return string
	 */
	public static function getAliveList ($m3u8_path)
	{
		//下载m3u8文件到服务器
		$downUrl = Utils::downloadFileFromNet($m3u8_path);

		//下载完后获取文件内容并更新
		$urlHeader = substr($m3u8_path, 0, strpos($m3u8_path, '/' . basename($m3u8_path)));
		$listOld   = file_get_contents($downUrl);
		$listNew   = "";
		//转为数组批量替换,最后一个为空不要
		$arrs = explode("\n", $listOld);
		for ($i = 0; $i < count($arrs) - 1; $i++) {
			if (!strstr($arrs[ $i ], "#")) {
				$arrs[ $i ] = $urlHeader . "/" . $arrs[ $i ];
			}
			$listNew = $listNew . $arrs[ $i ] . "\n";
		}

		@unlink($downUrl);

		return $listNew;
	}

	/**
	 * 下载网络文件
	 *
	 * @param $file_url
	 *
	 * @return string
	 */
	public static function downloadFileFromNet ($file_url)
	{
		if (!isset($file_url) || empty($file_url)) {
			exit;
		}

		// maximum execution time in seconds
		set_time_limit(24 * 60 * 60);


		// folder to save downloaded files to. must end with slash
		$destination_folder = storage_path('app/public/temp/');
		$newfname           = $destination_folder . basename($file_url);
		$file               = fopen($file_url, "rb");

		if ($file) {

			$newf = fopen($newfname, "wb");

			if ($newf) {

				while (!feof($file)) {
					fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
				}

			}
		}

		if ($file) {
			fclose($file);
		}

		if ($newf) {
			fclose($newf);
		}

		return $newfname;
	}

	/**
	 * @param string $prefix
	 * @param int    $suffixLength
	 *
	 * @return string 返回发票ID
	 */
	static public function getTaxId ($prefix = 'tax_', $suffixLength = 5)
	{
		return uniqid($prefix, false) . '-' . Utils::generateRandomCode($suffixLength, 'ALL');
	}

	/**
	 * @param $longUrl
	 *
	 * @return null
	 * 根据长链接查询短链接(查询 + 新建)
	 */
	public static function getShortUrlByLongUrl ($longUrl)
	{
		$md5       = md5($longUrl);
		$ascCode   = ord($md5);
		$tableName = 'db_h5_st.t_h5_st_' . ($ascCode % 10);

		if ($tableName) {
			$stsInDB = DB::select("select * from " . $tableName . " where long_url = ? limit 1", [$longUrl]);
			if (count($stsInDB) > 0) {
				// 有链接
				return $stsInDB[0]->short_url;
			} else {
				// 没有链接 新建
				$md5            = md5($longUrl);
				$ascCode        = ord($md5);
				$firstCharacter = $ascCode % 10;
				$tryTime        = 0;
				while ($tryTime < 5) {
					$newCode             = $firstCharacter . Utils::generateRandomCode(8, 'ALL');
					$newSt               = [];
					$newSt['short_code'] = $newCode;
					$shortUrl            = env('ShortHost') . '/st/' . $newCode;
					$newSt['short_url']  = $shortUrl;
					$newSt['long_url']   = $longUrl;
					$currentTime         = Utils::getTime();
					$newSt['created_at'] = $currentTime;
					$newSt['updated_at'] = $currentTime;
					try {
						$result = DB::table($tableName)->insert($newSt);
						if ($result) {
							return $shortUrl;
						}
					} catch (\Exception $e) {

					}
					$tryTime++;
				}
			}
		}

		return null;
	}

	public static function zb_time ($time)
	{
		$arr = [3600, 5400, 7200, 9000, 10800, 86400, 172800];

		if (in_array($time, $arr))
			return $time;

		if ($time < head($arr))
			return head($arr);

		if ($time > last($arr))
			return 315360000;

		foreach ($arr as $k => $v) {
			if ($v > $time) {
				$left  = $time - $arr[ $k - 1 ];
				$right = $v - $time;

				if ($left < $right) return $arr[ $k - 1 ];

				return $v;
			}
		}

	}

	/**
	 * @return string
	 * 获取服务器的内网ip
	 */
	public static function getServerInsideAddress ()
	{
		$server = Request::instance()->server();
		if (key_exists('SERVER_ADDR', $server)) {
			return $server['SERVER_ADDR'];
		} else {
			return '';
		}
	}

}