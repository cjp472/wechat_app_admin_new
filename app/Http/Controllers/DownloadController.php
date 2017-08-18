<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\GlobalString;
use App\Http\Controllers\Tools\Utils;
use App\Http\Controllers\Tools\V4UploadUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class DownloadController extends Controller
{
	//

	/**
	 * 下载视频并转换格式上传
	 *
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
	 */
	public function downloadVideo (Request $request)
	{
		$file_url = $request->input('file_url');
		$video_id = $request->input('video_id');
		$app_id   = $request->input('app_id');
		if (Utils::isEmptyString($app_id)) {
			return;
		}
		$new_url = Utils::downloadFileFromNet($file_url);
		$tar_url = Utils::videoTransCoding($new_url);
		if (!file_exists($tar_url)) {
			return $tar_url;
		}
		$dst_url = Utils::uploadMp4Video($tar_url, $app_id);

		if (!Utils::isEmptyString($video_id) && !Utils::isEmptyString($app_id)) {
			$result = DB::connection('mysql')->table('t_video')->where('app_id', $app_id)->where('id', $video_id)->update([
				'video_mp4' => $dst_url,
			]);
		}

		return $dst_url;
	}

	/***
	 * 下载图片并压缩上传
	 *
	 * @param Request $request
	 *
	 * @return string
	 */
	public function downloadImage (Request $request)
	{
		$file_url   = $request->input('file_url');
		$image_id   = $request->input('image_id');
		$app_id     = $request->input('app_id');
		$table_name = $request->input('table_name');

		$new_url = Utils::downloadFileFromNet($file_url);

		$tar_url = Utils::imageCompress($new_url);

		$dst_url = Utils::uploadCompressImage($tar_url, $app_id);

		if (!Utils::isEmptyString($app_id)
			&& !Utils::isEmptyString($image_id)
			&& !Utils::isEmptyString($table_name)) {
			$result = DB::connection('mysql')->table($table_name)->where('app_id', $app_id)->where('id', $image_id)->update([
				'img_url_compressed' => $dst_url,
			]);
		}

		return $dst_url;
	}

	/***
	 * 下载图片并压缩上传
	 *
	 * @param Request $request
	 *
	 * @return string
	 */
	public function downloadImaged (Request $request)
	{

		$file_url    = $request->input('file_url');
		$image_field = $request->input('image_field', 'id'); //关联字段
		$image_id    = $request->input('image_id'); //关联字段值
		$app_id      = $request->input('app_id');
		$table_name  = $request->input('table_name'); //存储表
		$compressed  = $request->input('compressed'); //存储缩略图链接
		$db          = $request->input('db', 'mysql'); //存储库

		$image_width   = $request->input('image_width', 160);
		$image_height  = $request->input('image_height', 120);
		$image_quality = $request->input('image_quality', 60);

		$new_url = Utils::downloadFileFromNet($file_url);

		$tar_url = Utils::imageCompress($new_url, $image_width, $image_height, $image_quality);

		$dst_url = Utils::uploadCompressImage($tar_url, $app_id);
		if (!Utils::isEmptyString($app_id)
			&& !Utils::isEmptyString($image_id)
			&& !Utils::isEmptyString($table_name)) {
			$result = DB::connection($db)->table($table_name)->where('app_id', $app_id)
				->where($image_field, $image_id)->update([
					$compressed => $dst_url,
				]);
		}

		return $dst_url;
	}

	/**
	 * 下载音频
	 *
	 * @param Request $request
	 *
	 * @return string|void
	 */
	public function downloadAudio (Request $request)
	{
		$file_url = $request->input('file_url');
		$audio_id = $request->input('audio_id');
		$app_id   = $request->input('app_id');
		if (Utils::isEmptyString($app_id)) {
			return;
		}
		$new_url = Utils::downloadFileFromNet($file_url);
		$tar_url = Utils::audioCompressing($new_url);
		if (!file_exists($tar_url)) {
			return $tar_url;
		}
		$dst_url = Utils::uploadMp3Audio($tar_url, $app_id);

		if (!Utils::isEmptyString($audio_id) && !Utils::isEmptyString($app_id)) {
			$result = DB::connection('mysql')->table('t_audio')->where('app_id', $app_id)->where('id', $audio_id)->update([
				'audio_compress_url' => $dst_url,
			]);
		}

		return $dst_url;
	}

	public function test ()
	{
		$file_url = "http://wxresource-10011692.file.myqcloud.com/audio_resource/audio/7eeb4e01a51295fc16f38dd3dd9f46fd.mp4";

		//        Utils::asyncThread('http://localhost/downloadVideo?file_url='.$file_url);
		$destination_folder = storage_path('app/public/video/');
		$tar_url            = $destination_folder . basename($file_url);
		$dst_url            = Utils::uploadMp4Video($tar_url);
		dd($dst_url);
	}

	//mp3  压缩 + 转码
	public function mp3tom3u8 ()
	{
		set_time_limit(1200);

		$app_id  = Input::get("app_id");
		$id      = Input::get("id");
		$cdn_url = Input::get("cdn_url");

		//创建文件夹
		$filename = basename($cdn_url);//md5.mp3
		$prefix   = explode('.', $filename)[0];//文件前缀  md5
		$myDir    = storage_path('app/public/temp/' . $prefix);// /storage/app/public/temp/md5
		if (!file_exists($myDir)) {
			mkdir("$myDir", 0777, true);
		}

		//把cdn文件下载到myPath文件夹里，存在会重写
		$myFilePath = $myDir . '/' . $filename;// /storage/app/public/temp/md5/md5.mp3
		set_time_limit(24 * 60 * 60);
		$cdnFile = fopen($cdn_url, "rb");
		if ($cdnFile) {
			$myFile = fopen($myFilePath, "wb");
			if ($myFile) {
				while (!feof($cdnFile)) {
					fwrite($myFile, fread($cdnFile, 1024 * 8), 1024 * 8);
				}
			}
			fclose($cdnFile);
			if ($myFile) {
				fclose($myFile);
			}
		}
		//        $output = [];
		//        $status = [];
		//2.下载完之后执行ffmpeg命令,压缩后的音频执行转码
		$yasuo = 'ffmpeg -y -i ' . $myFilePath . ' -ab 64k -ar 22050 -ac 2 ' . $myDir . '/' . $prefix . 'press.mp3';
		exec($yasuo, $output, $status);

		//获取压缩后文件大小 在服务器本地storage/app/public/temp/文件名+press.mp3
		//TODO:
		$audio_compress_size = filesize($myDir . '/' . $prefix . 'press.mp3');
		$audio_compress_size = $audio_compress_size / 1024 / 1024;
		//更新音频压缩后大小
		$yasuoUpdate = \DB::update("update t_audio set audio_compress_size = '$audio_compress_size' where app_id = '$app_id' and id = '$id'");

		$zhuanma = "ffmpeg -i " . $myDir . "/" . $prefix . "press.mp3 -ab 64k -c:a aac -strict -2 -f hls -hls_time 10 -hls_list_size 0 " .
			$myDir . "/" . $prefix . ".m3u8";
		exec($zhuanma, $output, $status);

		//3.为应用创建对应腾讯云app_id下的文件mp3目录
		$QcloudNewPath = $app_id . '/audio/' . $prefix; //    apppcHqlTPT3482/audio/md5
		//        Cosapi::createFolder(env("COS_BUCKET_NAME"),$QcloudNewPath,$bizAttr = null);
		V4UploadUtils::createV4Folder($QcloudNewPath);

		//4.把所有ts文件上传到腾讯云
		$allTs = glob($myDir . "/*.ts");
		for ($i = 0; $i < count($allTs); $i++) {
			//            Cosapi::upload(env("COS_BUCKET_NAME"),$allTs[$i],"/".$QcloudNewPath."/".basename($allTs[$i])
			//            ,$bizAttr = null, $slicesize = null, $insertOnly = null);
			V4UploadUtils::uploadFile($allTs[ $i ], "/" . $QcloudNewPath . "/" . basename($allTs[ $i ])
				, $bizAttr = null, $slicesize = null, $insertOnly = null);
		}

		//5.把压缩后的音频上传到腾讯云并update
		//        $yasuoResult=Cosapi::upload(env("COS_BUCKET_NAME"),$myDir."/".$prefix."press.mp3",
		//        "/".$QcloudNewPath."/".$prefix."press.mp3",$bizAttr = null, $slicesize = null, $insertOnly = null);
		$yasuoResult = V4UploadUtils::uploadFile($myDir . "/" . $prefix . "press.mp3",
			"/" . $QcloudNewPath . "/" . $prefix . "press.mp3",
			$bizAttr = null, $slicesize = null, $insertOnly = null);
		if ($yasuoResult['code'] == 0 || $yasuoResult['code'] == '-4018' || $yasuoResult['code'] == '-177') {
			$fileRoot           = 'http://' . GlobalString::V4_COS_BUCKET_NAME . '-' . GlobalString::V4_COS_APP_ID . '.file.myqcloud.com';
			$audio_compress_url = $fileRoot . "/" . $QcloudNewPath . "/" . $prefix . "press.mp3";

			$yasuoUpdate = \DB::update("update t_audio set audio_compress_url = ? where app_id = ? and id = ?",
				[$audio_compress_url, $app_id, $id]);
		}

		//6.更新m3u8文件并上传
		$m3u8Old = file_get_contents($myDir . "/" . $prefix . ".m3u8");
		$m3u8New = "";
		//转为数组批量替换,最后一个为空不要
		$arrs = explode("\n", $m3u8Old);
		for ($i = 0; $i < count($arrs) - 1; $i++) {
			if (!strstr($arrs[ $i ], "#")) {
				$fileRoot   = 'http://' . GlobalString::V4_COS_BUCKET_NAME . '-' . GlobalString::V4_COS_APP_ID . '.file.myqcloud.com';
				$arrs[ $i ] = $fileRoot . "/" . $QcloudNewPath . "/" . $arrs[ $i ];
			}
			$m3u8New = $m3u8New . $arrs[ $i ] . "\n";
		}
		$fp = fopen($myDir . "/" . $prefix . ".m3u8", "w");
		fwrite($fp, $m3u8New);
		fclose($fp);

		//判断音频长度,正确才把m3u8更新了
		$audioLength = 0;
		for ($i = 0; $i < count($arrs) - 1; $i++) {
			if (strstr($arrs[ $i ], "#EXTINF:")) {
				$temp        = str_replace(',', '', str_replace('#EXTINF:', '', $arrs[ $i ]));
				$audioLength += (float)$temp;
			}
		}
		$trueLength = \DB::select("select audio_length from t_audio where app_id = ? and id = ?", [$app_id, $id]);
		if (empty($trueLength) || count($trueLength) == 0) {
			return;
		}
		if (($audioLength - $trueLength[0]->audio_length > 10) || ($audioLength - $trueLength[0]->audio_length < -10)) {
			return;
		}

		//        $zhuanmaResult=Cosapi::upload(env("COS_BUCKET_NAME"),$myDir."/".$prefix.".m3u8",
		//        "/".$QcloudNewPath."/".$prefix.".m3u8",$bizAttr = null, $slicesize = null, $insertOnly = null);
		$zhuanmaResult = V4UploadUtils::uploadFile($myDir . "/" . $prefix . ".m3u8",
			"/" . $QcloudNewPath . "/" . $prefix . ".m3u8",
			$bizAttr = null, $slicesize = null, $insertOnly = null);

		if ($zhuanmaResult['code'] == 0 || $zhuanmaResult['code'] == '-4018' || $zhuanmaResult['code'] == '-177') {
			$fileRoot      = 'http://' . GlobalString::V4_COS_BUCKET_NAME . '-' . GlobalString::V4_COS_APP_ID . '.file.myqcloud.com';
			$zhuanmaUpdate = \DB::update("update t_audio set m3u8_url = ? where app_id = ? and id = ?",
				[$fileRoot . "/" . $QcloudNewPath . "/" . $prefix . ".m3u8", $app_id, $id]);
		}

		//删除文件夹
		Utils::delDirAndFile($myDir, 1);
	}

	//批量转换音频为m3u8,每次一条
	public function stuphinNewbee ()
	{
		/*
		app8yMcUdml8133--咔咔studio,
		appExnUp92u2514--美研社之家,
		appG1VMUALC2470--张德芬,              1
		appHVYHiicr5056--心乐土,
		appjdkoKBJ16226--良师雅集,
		appJG5cnloi7745--心灵圈子,
		appkrjfyD6q7315--书单服务号,
		appp7SI9foD2454--创新创业课,
		appPYz6OBVI9510--思想食堂,
		appQMK2OEBh1031--德科地产,
		appsHHhyqKN4694--邱老师的择食瘦孕课堂,
		appXlF7yxps4671--聪课资讯,
		appz4sJJLw19326--宝宝加油服务号*/
		$audio = \DB::select("select app_id,id,audio_url from t_audio where app_id = ? and m3u8_url is null 
        order by created_at limit 1", ['appG1VMUALC2470']);
		if (empty($audio[0]->app_id) || empty($audio[0]->id) || empty($audio[0]->audio_url)) {
			return 1;
		}

		//创建服务器文件夹
		$filename = basename($audio[0]->audio_url);//md5.mp3
		$prefix   = explode('.', $filename)[0];//文件前缀  md5
		$myDir    = storage_path('app/public/temp/' . $prefix);// /storage/app/public/temp/md5
		if (!file_exists($myDir)) {
			mkdir("$myDir", 0777, true);
		}

		//把cdn文件下载到myPath文件夹里，存在会重写
		$myFilePath = $myDir . '/' . $filename;// /storage/app/public/temp/md5/md5.mp3
		set_time_limit(24 * 60 * 60);
		$cdnFile = fopen($audio[0]->audio_url, "rb");
		if ($cdnFile) {
			$myFile = fopen($myFilePath, "wb");
			if ($myFile) {
				while (!feof($cdnFile)) {
					fwrite($myFile, fread($cdnFile, 1024 * 8), 1024 * 8);
				}
			}
			fclose($cdnFile);
			if ($myFile) {
				fclose($myFile);
			}
		}

		//下载完之后执行ffmpeg命令
		$zhuanma = "ffmpeg -i " . $myFilePath . "  -c:a aac -strict -2 -f hls -hls_time 10 -hls_list_size 0 " .
			$myDir . "/" . $prefix . ".m3u8";
		exec($zhuanma, $output, $status);

		//3.为应用创建对应腾讯云app_id下的文件mp3目录
		$QcloudNewPath = $audio[0]->app_id . '/audio/' . $prefix; //    apppcHqlTPT3482/audio/md5
		//        Cosapi::createFolder(env("COS_BUCKET_NAME"),$QcloudNewPath,$bizAttr = null);
		V4UploadUtils::createV4Folder($QcloudNewPath);

		//4.把所有ts文件上传到腾讯云
		$allTs = glob($myDir . "/*.ts");
		for ($i = 0; $i < count($allTs); $i++) {
			//            Cosapi::upload(env("COS_BUCKET_NAME"),$allTs[$i],"/".$QcloudNewPath."/".basename($allTs[$i])
			//            ,$bizAttr = null, $slicesize = null, $insertOnly = null);
			V4UploadUtils::uploadFile($allTs[ $i ],
				"/" . $QcloudNewPath . "/" . basename($allTs[ $i ])
				, $bizAttr = null, $slicesize = null, $insertOnly = null);
		}

		//5.更新m3u8文件并上传
		$m3u8Old = file_get_contents($myDir . "/" . $prefix . ".m3u8");
		$m3u8New = "";
		//转为数组批量替换,最后一个为空不要
		$arrs = explode("\n", $m3u8Old);
		for ($i = 0; $i < count($arrs) - 1; $i++) {
			if (!strstr($arrs[ $i ], "#")) {
				$fileRoot   = 'http://' . GlobalString::V4_COS_BUCKET_NAME . '-' . GlobalString::V4_COS_APP_ID . '.file.myqcloud.com';
				$arrs[ $i ] = $fileRoot . "/" . $QcloudNewPath . "/" . $arrs[ $i ];
			}
			$m3u8New = $m3u8New . $arrs[ $i ] . "\n";
		}
		$fp = fopen($myDir . "/" . $prefix . ".m3u8", "w");
		fwrite($fp, $m3u8New);
		fclose($fp);

		//        $zhuanmaResult=Cosapi::upload(env("COS_BUCKET_NAME"),$myDir."/".$prefix.".m3u8",
		//        "/".$QcloudNewPath."/".$prefix.".m3u8",$bizAttr = null, $slicesize = null, $insertOnly = null);
		$zhuanmaResult = V4UploadUtils::uploadFile($myDir . "/" . $prefix . ".m3u8",
			"/" . $QcloudNewPath . "/" . $prefix . ".m3u8",
			$bizAttr = null, $slicesize = null, $insertOnly = null);

		if ($zhuanmaResult['code'] == 0 || $zhuanmaResult['code'] == '-4018' || $zhuanmaResult['code'] == '-177') {
			$fileRoot      = 'http://' . GlobalString::V4_COS_BUCKET_NAME . '-' . GlobalString::V4_COS_APP_ID . '.file.myqcloud.com';
			$zhuanmaUpdate = \DB::update("update t_audio set m3u8_url = ? where app_id = ? and id = ?",
				[$fileRoot . "/" . $QcloudNewPath . "/" . $prefix . ".m3u8",
					$audio[0]->app_id, $audio[0]->id]);
		}

		return 0;
	}

	//批量压缩并转换音频为m3u8,每次一条
	public function batchMp3tom3u8 ()
	{
		//time_out set second;
		set_time_limit(1200);

		$date = Input::get('date', '');
		//按日期排查
		if (!$date) {
			$date = date('Y-m-d', time());
		}

		$reurl    = '?date=' . $date;
		$whereRaw = " ";
		$appid    = Input::get('appid', '');
		if ($appid) {
			$whereRaw = " and app_id = '$appid'";    //按appid排查
			$reurl    .= '&appid=' . $appid;
		}

		$audio = \DB::select("select app_id,id,audio_url from t_audio where date(created_at) = '$date' $whereRaw and m3u8_url is null and audio_size=0 
        order by created_at limit 1");
		if (empty($audio[0]->app_id) || empty($audio[0]->id) || empty($audio[0]->audio_url)) {
		} else {
			//开始逐个处理未压缩转码
			set_time_limit(1200);

			$app_id  = $audio[0]->app_id;
			$id      = $audio[0]->id;
			$cdn_url = $audio[0]->audio_url;

			//创建文件夹
			$filename = basename($cdn_url);//md5.mp3
			$prefix   = explode('.', $filename)[0];//文件前缀  md5
			$myDir    = storage_path('app/public/temp/' . $prefix);// /storage/app/public/temp/md5
			if (!file_exists($myDir)) {
				mkdir("$myDir", 0777, true);
			}

			//把cdn文件下载到myPath文件夹里，存在会重写
			$myFilePath = $myDir . '/' . $filename;// /storage/app/public/temp/md5/md5.mp3
			set_time_limit(24 * 60 * 60);
			$cdnFile = fopen($cdn_url, "rb");
			if ($cdnFile) {
				$myFile = fopen($myFilePath, "wb");
				if ($myFile) {
					while (!feof($cdnFile)) {
						fwrite($myFile, fread($cdnFile, 1024 * 8), 1024 * 8);
					}
				}
				fclose($cdnFile);
				if ($myFile) {
					fclose($myFile);
				}
			}

			//2.下载完之后执行ffmpeg命令,压缩后的音频执行转码
			$yasuo = 'ffmpeg -y -i ' . $myFilePath . ' -ab 64k -ar 22050 -ac 2 ' . $myDir . '/' . $prefix . 'press.mp3';
			exec($yasuo, $output, $status);

			//获取压缩后文件大小 在服务器本地storage/app/public/temp/文件名+press.mp3
			//TODO:
			$audio_compress_size = filesize($myDir . '/' . $prefix . 'press.mp3');
			$audio_compress_size = $audio_compress_size / 1024 / 1024;

			//更新音频压缩后大小
			$yasuoUpdate = \DB::update("update t_audio set audio_compress_size = '$audio_compress_size' where app_id = '$app_id' and id = '$id'");

			$zhuanma = "ffmpeg -i " . $myDir . "/" . $prefix . "press.mp3 -c:a aac -strict -2 -f hls -hls_time 10 -hls_list_size 0 " .
				$myDir . "/" . $prefix . ".m3u8";
			exec($zhuanma, $output, $status);

			//3.为应用创建对应腾讯云app_id下的文件mp3目录
			$QcloudNewPath = $app_id . '/audio/' . $prefix; //    apppcHqlTPT3482/audio/md5
			//            Cosapi::createFolder(env("COS_BUCKET_NAME"),$QcloudNewPath,$bizAttr = null);
			V4UploadUtils::createV4Folder($QcloudNewPath);

			//4.把所有ts文件上传到腾讯云
			$allTs = glob($myDir . "/*.ts");
			for ($i = 0; $i < count($allTs); $i++) {
				//                Cosapi::upload(env("COS_BUCKET_NAME"),$allTs[$i],"/".$QcloudNewPath."/".basename($allTs[$i])
				//                    ,$bizAttr = null, $slicesize = null, $insertOnly = null);
				V4UploadUtils::uploadFile($allTs[ $i ],
					"/" . $QcloudNewPath . "/" . basename($allTs[ $i ])
					, $bizAttr = null, $slicesize = null, $insertOnly = null);
			}

			//5.把压缩后的音频上传到腾讯云并update

			//            $yasuoResult=Cosapi::upload(env("COS_BUCKET_NAME"),$myDir."/".$prefix."press.mp3",
			//                "/".$QcloudNewPath."/".$prefix."press.mp3",$bizAttr = null, $slicesize = null, $insertOnly = null);
			$yasuoResult = V4UploadUtils::uploadFile($myDir . "/" . $prefix . "press.mp3",
				"/" . $QcloudNewPath . "/" . $prefix . "press.mp3",
				$bizAttr = null, $slicesize = null, $insertOnly = null);

			if ($yasuoResult['code'] == 0 || $yasuoResult['code'] == '-4018' || $yasuoResult['code'] == '-177') {
				$fileRoot    = 'http://' . GlobalString::V4_COS_BUCKET_NAME . '-' . GlobalString::V4_COS_APP_ID . '.file.myqcloud.com';
				$yasuoUpdate = \DB::update("update t_audio set audio_compress_url = ? where app_id = ? and id = ?",
					[$fileRoot . "/" . $QcloudNewPath . "/" . $prefix . "press.mp3", $app_id, $id]);
			}

			//6.更新m3u8文件并上传
			$m3u8Old = file_get_contents($myDir . "/" . $prefix . ".m3u8");
			$m3u8New = "";
			//转为数组批量替换,最后一个为空不要
			$arrs = explode("\n", $m3u8Old);
			for ($i = 0; $i < count($arrs) - 1; $i++) {
				if (!strstr($arrs[ $i ], "#")) {
					$fileRoot   = 'http://' . GlobalString::V4_COS_BUCKET_NAME . '-' . GlobalString::V4_COS_APP_ID . '.file.myqcloud.com';
					$arrs[ $i ] = $fileRoot . "/" . $QcloudNewPath . "/" . $arrs[ $i ];
				}
				$m3u8New = $m3u8New . $arrs[ $i ] . "\n";
			}
			$fp = fopen($myDir . "/" . $prefix . ".m3u8", "w");
			fwrite($fp, $m3u8New);
			fclose($fp);

			//判断音频长度,正确才把m3u8更新了
			$audioLength = 0;
			for ($i = 0; $i < count($arrs) - 1; $i++) {
				if (strstr($arrs[ $i ], "#EXTINF:")) {
					$temp        = str_replace(',', '', str_replace('#EXTINF:', '', $arrs[ $i ]));
					$audioLength += (float)$temp;
				}
			}
			$trueLength = \DB::select("select audio_length from t_audio where app_id = ? and id = ?", [$app_id, $id]);
			if (($audioLength - $trueLength[0]->audio_length > 10) || ($audioLength - $trueLength[0]->audio_length < -10)) {
				return;
			}

			//            $zhuanmaResult=Cosapi::upload(env("COS_BUCKET_NAME"),$myDir."/".$prefix.".m3u8",
			//                "/".$QcloudNewPath."/".$prefix.".m3u8",$bizAttr = null, $slicesize = null, $insertOnly = null);
			$zhuanmaResult = V4UploadUtils::uploadFile($myDir . "/" . $prefix . ".m3u8",
				"/" . $QcloudNewPath . "/" . $prefix . ".m3u8",
				$bizAttr = null, $slicesize = null, $insertOnly = null);

			if ($zhuanmaResult['code'] == 0 || $zhuanmaResult['code'] == '-4018' || $zhuanmaResult['code'] == '-177') {
				$fileRoot      = 'http://' . GlobalString::V4_COS_BUCKET_NAME . '-' . GlobalString::V4_COS_APP_ID . '.file.myqcloud.com';
				$zhuanmaUpdate = \DB::update("update t_audio set m3u8_url = ? where app_id = ? and id = ?",
					[$fileRoot . "/" . $QcloudNewPath . "/" . $prefix . ".m3u8", $app_id, $id]);
			}

			//删除文件夹
			Utils::delDirAndFile($myDir, 1);

			//循环跳转到下一次处理
			Utils::asyncThread(env("HOST_URL") . '/batchmp3tom3u8' . $reurl);

		}

	}

	//更新cdn
	public function audioCompressUrl ()
	{
		$compress = \DB::select("select * from t_audio where audio_compress_url like '%.cos.%'");
		foreach ($compress as $key => $value) {
			$new    = str_replace('.cos.', '.file.', $value->audio_compress_url);
			$update = \DB::update("update t_audio set audio_compress_url = ? where app_id = ? and id = ? limit 1",
				[$new, $value->app_id, $value->id]);
		}
	}

	//更新m3u8url
	public function audioM3u8Url ()
	{
		set_time_limit(24 * 60 * 60);
		$result = \DB::select("select * from t_audio where m3u8_url like '%.cos.%'");
		foreach ($result as $key => $value) {
			//先下载
			$filename = basename($value->m3u8_url);//          1ea94eae8eaf562e5097530e6ba3199b.m3u8
			if (strstr($filename, '.mp3')) {
				continue;
			}
			$myDir      = storage_path('m3u8');//                   /storage/m3u8
			$myFilePath = $myDir . '/' . $filename;//              /storage/m3u8/1ea94eae8eaf562e5097530e6ba3199b.m3u8

			$cosFile = fopen($value->m3u8_url, "rb");
			if ($cosFile) {
				$myFile = fopen($myFilePath, "wb");
				if ($myFile) {
					while (!feof($cosFile)) {
						fwrite($myFile, fread($cosFile, 1024 * 8), 1024 * 8);
					}
				}
				fclose($cosFile);
				if ($myFile) {
					fclose($myFile);
				}
			}

			//开始读文件并替换
			$content = file_get_contents($myFilePath);
			$new     = str_replace('.cos.', '.file.', $content);
			$fp      = fopen($myFilePath, "w");
			fwrite($fp, $new);
			fclose($fp);

			//上传腾讯云并覆盖
			//            $callback=Cosapi::upload(env("COS_BUCKET_NAME"),$myFilePath,substr($value->m3u8_url,45)
			//            ,$bizAttr = null, $slicesize = null,0);
			//            $callback = UploadUtils::uploadFile($myFilePath, substr($value->m3u8_url,45)
			//                ,$bizAttr = null, $slicesize = null, 0);
			$callback = V4UploadUtils::uploadFile($myFilePath, substr($value->m3u8_url, 45)
				, $bizAttr = null, $slicesize = null, 0);
			if ($callback['code'] == 0 || $callback['code'] == '-4018' || $callback['code'] == '-177') {
				$update = \DB::update("update t_audio set m3u8_url = ? where app_id = ? and id = ? limit 1",
					[str_replace('.cos.', '.file.', $value->m3u8_url), $value->app_id, $value->id]);
			}
		}
	}

	//下载直播语音
	public function downloadAliveVoice ()
	{
		//        $start_time_10min = time() + 600;
		$appId   = Input::get('app_id');
		$aliveId = Input::get('alive_id');

		$res = \DB::table('t_alive')
			->where('app_id', '=', $appId)
			->where('id', '=', $aliveId)
			->update(['complete_state' => 1]);

		//讲师语音
		$teacherVoice = DB::select("select * from t_alive_interact 
        where app_id=? and alive_id=? and content_type =3 and user_type=1 order by created_at asc", [$appId, $aliveId]);

		if (!($teacherVoice != null && count($teacherVoice) > 0)) {
			\DB::table('t_alive')->where('app_id', '=', $appId)->where('id', '=', $aliveId)
				->update(['complete_state' => 3]);
			exit;
		}

		//解析语音链接
		$voiceUrl = [];
		foreach ($teacherVoice as $voiceKey => $voiceVal) {

			$more_info = json_decode($voiceVal->more_info, true);
			if (array_key_exists("url", $more_info)) {
				if ($more_info['url'] && (strpos($more_info['url'], ".mp3") || strpos($more_info['url'], ".aac"))) {
					$voiceUrl[] = $more_info['url'];
				}
			}
		}

		if (!count($voiceUrl) > 0) {
			\DB::table('t_alive')->where('app_id', '=', $appId)->where('id', '=', $aliveId)
				->update(['complete_state' => 3]);
			exit;
		}

		$voiceArr = [];

		foreach ($voiceUrl as $key => $val) {

			$filePath = null;
			if (strpos($val, ".mp3")) {
				$filePath = $aliveId . "_" . $key . ".mp3";
			} else if (strpos($val, ".aac")) {
				$filePath = $aliveId . "_" . $key . ".aac";
			}

			if ($filePath) {
				$filePath_mp3 = $aliveId . "_" . $key . ".mp3";

				$voiceArr[] = [
					'url'          => $val,
					//原文件路径
					'filePath'     => $filePath,
					//MP3后的路径
					'filePath_mp3' => $filePath_mp3,
				];
			}
		}

		if (!count($voiceArr) > 0) return response()->json(['code' => 8, 'msg' => '下载源文件前出错']);
		//将文件下载到本地
		foreach ($voiceArr as $voiceKey => $voiceVal) {

			if ($voiceVal['url'] && $voiceVal['filePath']) {
				Utils::downloadFileFromNetAlive(storage_path('sound/' . $voiceVal['filePath']), $voiceVal['url']);
			}
		}

		// aac的全转成mp3
		foreach ($voiceArr as $voiceKey => $voiceVal) {

			if (strpos($voiceVal['filePath'], ".aac") && file_exists(storage_path('sound/' . $voiceVal['filePath']))) {
				$src_url = storage_path('sound/' . $voiceVal['filePath']);
				$mp3_url = storage_path('sound/' . $voiceVal['filePath_mp3']);

				$mp3_cmd = "ffmpeg  -i " . $src_url . " -ab 80k -ar 44100 -ac 2 -acodec libmp3lame " . $mp3_url;
				exec($mp3_cmd . " 2>&1", $output, $status);

				if (file_exists($src_url)) {
					unlink($src_url);
				}
			}
		}

		//筛选出正确的文件
		$useful_file_arr = [];
		foreach ($voiceArr as $voiceKey => $voiceVal) {

			$mp3_url = storage_path('sound/' . $voiceVal['filePath_mp3']);
			if (file_exists($mp3_url)) {
				$file_size = filesize($mp3_url);
				if ($file_size > 0) {
					$useful_file_arr[] = $mp3_url;
				} else {
					//文件大小小于0，删掉空文件
					unlink($mp3_url);
				}
			}
		}

		//合并成一个音频文件
		if (!count($useful_file_arr) > 0) {
			\DB::table('t_alive')->where('app_id', '=', $appId)->where('id', '=', $aliveId)
				->update(['complete_state' => 3]);
			exit;
		}

		$useful_file_str    = implode("|", $useful_file_arr);
		$complete_file_path = storage_path('sound/' . $aliveId . "_complete.mp3");
		$concat_cmd         = 'ffmpeg -i "concat:' . $useful_file_str . '" -acodec copy ' . $complete_file_path;
		exec($concat_cmd . " 2>&1", $output, $status);
		//删除掉短的音频
		foreach ($useful_file_arr as $useful_file_key => $useful_file_val) {

			unlink($useful_file_val);
		}

		//上传完整音频
		if (!file_exists($complete_file_path)) {
			\DB::table('t_alive')->where('app_id', '=', $appId)->where('id', '=', $aliveId)
				->update(['complete_state' => 3]);
			exit;
		}

		$file_size = filesize($complete_file_path);
		if (!$file_size > 0) {
			\DB::table('t_alive')->where('app_id', '=', $appId)->where('id', '=', $aliveId)
				->update(['complete_state' => 3]);
			exit;
		}

		$cosBase     = "/" . $appId . env('SoundPath');
		$fileMD5     = md5_file($complete_file_path);
		$cosFileName = $fileMD5 . '.mp3';

		$ret = V4UploadUtils::uploadFile($complete_file_path, $cosBase . $cosFileName);
		unlink($complete_file_path);
		if ($ret['code'] == 0) {  // 上传到腾讯云的结果
			$cosUrl = $ret['data']['source_url'];
			\DB::table('t_alive')
				->where('app_id', '=', $appId)
				->where('id', '=', $aliveId)
				->update(['complete_voice_url' => $cosUrl, 'complete_state' => 0]);

		} else {
			\DB::table('t_alive')
				->where('app_id', '=', $appId)
				->where('id', '=', $aliveId)
				->update(['complete_state' => 3]);
		}

		exit;

	}

}
