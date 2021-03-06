<?php
/**
 * Created by PhpStorm.
 * User: fuhaiwen
 * Date: 2017/3/23
 * Time: 16:10
 */

namespace App\Http\Controllers\Tools;

use Illuminate\Support\Facades\Config;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Request;
use App\Http\Controllers\AliveVideo\TimRestApi;

use Intervention\Image\Facades\Image;

class ResContentComm
{

	public function __construct ()
	{
	}

	//获取符合条件的资源id集合
	static public function getResourceIdList ($state, $search_content, $resource_type)
	{
		$whereRaw         = " 1=1 ";
		$resource_id_list = ['1'];
		if (!Utils::isEmptyString($search_content)) {
			$whereRaw .= " and title like '" . "%" . $search_content . "%'";
		}

		if ($resource_type < 0 || $resource_type > 4) {
			$resource_type = 0;
		}
		switch ($resource_type) {
			case StringConstants::SINGLE_GOODS_ALL://全部资源
				//从4个资源表中取出符合条件的记录
				ResContentComm::queryArticleIdList($whereRaw, $state, $resource_id_list);
				ResContentComm::queryAudioIdList($whereRaw, $state, $resource_id_list);
				ResContentComm::queryVideoIdList($whereRaw, $state, $resource_id_list);
				ResContentComm::queryAliveIdList($whereRaw, $state, $resource_id_list);

				break;
			case StringConstants::SINGLE_GOODS_ARTICLE://图文
				//从图文中取出符合条件的记录
				ResContentComm::queryArticleIdList($whereRaw, $state, $resource_id_list);
				break;
			case StringConstants::SINGLE_GOODS_AUDIO://音频
				//从音频中取出符合条件的记录
				ResContentComm::queryAudioIdList($whereRaw, $state, $resource_id_list);
				break;
			case StringConstants::SINGLE_GOODS_VIDEO://视频
				//从视频中取出符合条件的记录
				ResContentComm::queryVideoIdList($whereRaw, $state, $resource_id_list);
				break;
			case StringConstants::SINGLE_GOODS_ALIVE://直播
				//从直播中取出符合条件的记录
				ResContentComm::queryAliveIdList($whereRaw, $state, $resource_id_list);
				break;
		}

		return $resource_id_list;
	}

	//从图文中取出符合条件的记录
	static public function queryArticleIdList ($whereRaw, $state, &$resource_id_list)
	{

		//        $resource_id_list = [];
		if ($state != -1) {
			$whereRaw .= " and display_state = " . $state;
		}
		//查询记录从t_image_text中
		$article_list = \DB::table("db_ex_business.t_image_text")
			->where("app_id", '=', AppUtils::getAppID())
			->where("display_state", '!=', 2)
			->whereRaw($whereRaw)
			->get();
		if ($article_list) {
			foreach ($article_list as $key => $article) {
				$resource_id_list[] = "'" . $article->id . "'";
			}
		}

		return 1;
	}

	//从音频中取出符合条件的记录
	static public function queryAudioIdList ($whereRaw, $state, &$resource_id_list)
	{
		//        $resource_id_list = [];
		if ($state != -1) {
			$whereRaw .= " and audio_state = " . $state;
		}
		//查询记录从t_image_text中
		$article_list = \DB::table("db_ex_business.t_audio")
			->where("app_id", '=', AppUtils::getAppID())
			->where("audio_state", '!=', 2)
			->whereRaw($whereRaw)
			->get();
		if ($article_list) {
			foreach ($article_list as $key => $article) {
				$resource_id_list[] = "'" . $article->id . "'";
			}
		}

		return 1;
	}

	//从视频中取出符合条件的记录
	static public function queryVideoIdList ($whereRaw, $state, &$resource_id_list)
	{
		//        $resource_id_list = [];
		if ($state != -1) {
			$whereRaw .= " and video_state = " . $state;
		}
		//查询记录从t_image_text中
		$article_list = \DB::table("db_ex_business.t_video")
			->where("app_id", '=', AppUtils::getAppID())
			->where("video_state", '!=', 2)
			->whereRaw($whereRaw)
			->get();
		if ($article_list) {
			foreach ($article_list as $key => $article) {
				$resource_id_list[] = "'" . $article->id . "'";
			}
		}

		return 1;
	}

	//从直播中取出符合条件的记录
	static public function queryAliveIdList ($whereRaw, $state, &$resource_id_list)
	{
		//        $resource_id_list = [];
		if ($state != -1) {
			$whereRaw .= " and state = " . $state;
		}
		//查询记录从t_image_text中
		$article_list = \DB::table("db_ex_business.t_alive")
			->where("app_id", '=', AppUtils::getAppID())
			->where("state", '!=', 2)
			->whereRaw($whereRaw)
			->get();
		if ($article_list) {
			foreach ($article_list as $key => $article) {
				$resource_id_list[] = "'" . $article->id . "'";
			}
		}

		return 1;
	}

	//分隔文本编辑器的内容
	static public function sliceUE ($html)
	{
		$html    = str_replace('&quot;', "'", $html);
		$content = [];
		$match   = "/<(p|section|img|div).*?>/";
		preg_match_all($match, $html, $arr);
		$html   = preg_replace("/<(p|section|img|div|iframe|a).*?>/", 'S_P_L_I_T', $html);
		$length = count($arr[0]);
		for ($i = 0; $i < $length; $i++) {
			$url = $arr[0][ $i ];
			try {
				if (strstr($url, '<img') && strstr($url, 'src')) {
					$url = explode('"', explode('src="', $url)[1])[0];
				} else if (strstr($url, 'background') && strstr($url, 'url(')) {
					if (strstr($url, "url('")) {
						$url = explode("')", explode("url('", $url)[1])[0];
					} else if (strstr($url, 'url("')) {
						$url = explode('")', explode('url("', $url)[1])[0];
					} else {
						$url = explode(")", explode("url(", $url)[1])[0];
					}
				} else {
					continue;
				}
			} catch (\Exception $e) {
				return false;
			}

			$size                      = count($content);
			$content[ $size ]["type"]  = 1;
			$content[ $size ]["value"] = $url;
		}
		if (strstr($html, 'S_P_L_I_T')) {
			$html = explode('S_P_L_I_T', $html);
			$size = count($html);
			for ($i = 0; $i < $size; $i++) {
				$text_desc = str_replace(array("\r", "\n", "\r\n"), "", $html[$i]);
				if (empty($text_desc)) {
					continue;
				}
				$length                      = count($content);
				$content[ $length ]["type"]  = 0;
				$content[ $length ]["value"] = $text_desc;
			}
		} else {
			$text_desc = str_replace(array("\r", "\n", "\r\n"), "", $html);
			if (!empty($text_desc)) {
				$size                      = count($content);
				$content[ $size ]["type"]  = 0;
				$content[ $size ]["value"] = $text_desc;
			}
		}
		/*
				$out = explode('<img', $html);
				//dump($out);
				for ($i = 0; $i < count($out); $i++) {
					$in = explode('>', $out[$i]);
					for ($j = 0; $j < count($in); $j++) {
						$length = count($content);
						if (strstr($in[$j], 'src')) {
							$content[$length]["type"] = 1;
							try{
								if(!isset(explode('src="', $in[$j])[1])){
									throw new \Exception("复制的内容有问题，请尝试分开复制，或记录复制内容并联系技术小哥");
								}else{
									$content[$length]["value"] = explode('"', explode('src="', $in[$j])[1])[0];
								}
							}catch (\Exception $e){
								return false;
							}

						} else {
							$content[$length]["type"] = 0;
							$content[$length]["value"] = $in[$j];
						}
					}
				}
		*/

		//        dump($content);

		return json_encode($content, JSON_UNESCAPED_UNICODE);
	}

	/**
	 *图片处理
	 *
	 * @param $image_url
	 * @param $table_name
	 * @param $image_id
	 */
	static public function imageDeal ($image_url, $table_name, $image_id)
	{
		$host_url = env('HOST_URL');
		$app_id   = AppUtils::getAppID();

		Utils::asyncThread($host_url . '/downloadImage?image_id=' . $image_id . '&app_id='
			. $app_id . '&table_name=' . $table_name . '&file_url=' . $image_url);

	}

	/**
	 *图片处理
	 *
	 * @param $image_url
	 * @param $table_name
	 * @param $image_id
	 * @param $image_width   压缩尺寸 宽度
	 * @param $image_height  压缩尺寸 高度
	 * @param $image_quality 压缩参数 质量值
	 * @param $compressed    缩略图存储字段
	 */
	static public function imageDealo ($image_url, $table_name, $image_id, $image_width, $image_height, $image_quality, $compressed)
	{
		$host_url = env('HOST_URL');
		$app_id   = AppUtils::getAppID();

		Utils::asyncThread($host_url . '/downloadImaged?image_id=' . $image_id . '&app_id='
			. $app_id . '&table_name=' . $table_name . '&file_url=' . $image_url
			. '&image_width=' . $image_width . '&image_height=' . $image_height . '&image_quality=' . $image_quality
			. '&compressed=' . $compressed);

	}

	/**
	 * 批量获取视频长度
	 *
	 * @param $file_id
	 *
	 * @return int
	 */
	static public function getVideoLength ($file_id)
	{
		$duration       = 0;
		$private_params = ['fileIds.1' => $file_id];

		$resultArray = ResContentComm::videoApi('DescribeVodInfo', $private_params);
		if ($resultArray['code'] == 0) {
			$duration = $resultArray['fileSet'][0]['duration'];
		}

		return $duration;
	}

	//测试获取视频截图接口
	static public function videoApi ($action, $private_params)
	{

		/*DescribeInstances 接口的 URL地址为 cvm.api.qcloud.com，可从对应的接口说明 “1.接口描述” 章节获取该接口的地址*/
		$HttpUrl = "vod.api.qcloud.com";

		/*除非有特殊说明，如MultipartUploadVodFile，其它接口都支持GET及POST*/
		$HttpMethod = "GET";

		/*是否https协议，大部分接口都必须为https，只有少部分接口除外（如MultipartUploadVodFile）*/
		$isHttps = true;

		/*需要填写你的密钥，可从  https://console.qcloud.com/capi 获取 SecretId 及 $secretKey*/
		$secretKey = env('SecretKey');

		/*下面这五个参数为所有接口的 公共参数；对于某些接口没有地域概念，则不用传递Region（如DescribeDeals）*/
		$COMMON_PARAMS = [
			'Nonce'     => rand(),
			'Timestamp' => time(null),
			'Action'    => $action,
			'SecretId'  => env('SecretId'),
			'Region'    => 'gz',
		];

		$PRIVATE_PARAMS = $private_params;

		/***********************************************************************************/

		return ResContentComm::CreateRequest($HttpUrl, $HttpMethod, $COMMON_PARAMS, $secretKey, $PRIVATE_PARAMS, $isHttps);

	}

	static public function CreateRequest ($HttpUrl, $HttpMethod, $COMMON_PARAMS, $secretKey, $PRIVATE_PARAMS, $isHttps)
	{
		$FullHttpUrl = $HttpUrl . "/v2/index.php";

		/***************对请求参数 按参数名 做字典序升序排列，注意此排序区分大小写*************/
		$ReqParaArray = array_merge($COMMON_PARAMS, $PRIVATE_PARAMS);
		ksort($ReqParaArray);

		/**********************************生成签名原文**********************************
		 * 将 请求方法, URI地址,及排序好的请求参数  按照下面格式  拼接在一起, 生成签名原文，此请求中的原文为
		 * GETcvm.api.qcloud.com/v2/index.php?Action=DescribeInstances&Nonce=345122&Region=gz
		 * &SecretId=AKIDz8krbsJ5yKBZQ    ·1pn74WFkmLPx3gnPhESA&Timestamp=1408704141
		 * &instanceIds.0=qcvm12345&instanceIds.1=qcvm56789
		 * ****************************************************************************/
		$SigTxt = $HttpMethod . $FullHttpUrl . "?";

		$isFirst = true;
		foreach ($ReqParaArray as $key => $value) {
			if (!$isFirst) {
				$SigTxt = $SigTxt . "&";
			}
			$isFirst = false;

			/*拼接签名原文时，如果参数名称中携带_，需要替换成.*/
			if (strpos($key, '_')) {
				$key = str_replace('_', '.', $key);
			}

			$SigTxt = $SigTxt . $key . "=" . $value;
		}

		/*********************根据签名原文字符串 $SigTxt，生成签名 Signature******************/
		$Signature = base64_encode(hash_hmac('sha1', $SigTxt, $secretKey, true));

		/***************拼接请求串,对于请求参数及签名，需要进行urlencode编码********************/
		$Req = "Signature=" . urlencode($Signature);
		foreach ($ReqParaArray as $key => $value) {
			$Req = $Req . "&" . $key . "=" . urlencode($value);
		}

		/*********************************发送请求********************************/
		if ($HttpMethod === 'GET') {
			if ($isHttps === true) {
				$Req = "https://" . $FullHttpUrl . "?" . $Req;
			} else {
				$Req = "http://" . $FullHttpUrl . "?" . $Req;
			}

			$Rsp = file_get_contents($Req);

		}

		//        var_export(json_decode($Rsp,true)) ;
		return json_decode($Rsp, true);
	}

}