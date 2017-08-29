<?php
/**
 * Created by PhpStorm.
 * User: breeze
 * Date: 9/30/16
 * Time: 11:54
 */

namespace App\Http\Controllers\Tools;

//use App\Http\Requests\Request;

use Qcloud_cos\Cosapi;

/**
 * @deprecated V3已弃用,请使用V4UploadUtils
 * Class UploadUtils
 * @package    App\Http\Controllers\Tools
 */
class UploadUtils
{

	/**
	 * 上传文件
	 *
	 * @param      $srcPath
	 * @param      $dstPath
	 * @param null $bizAttr
	 * @param null $sliceSize
	 * @param null $insertOnly
	 *
	 * @return array|mixed
	 */
	public static function uploadFile ($srcPath, $dstPath, $bizAttr = null,
		$sliceSize = null, $insertOnly = null)
	{

		$bucketName = GlobalString::COS_BUCKET_NAME;

		Cosapi::setTimeout(600);

		$ret = Cosapi::upload($bucketName, $srcPath, $dstPath,
			$bizAttr, $sliceSize, $insertOnly);

		return $ret;
	}

	/**
	 * 查询文件属性
	 *
	 * @param $filePath
	 *
	 * @return array|mixed
	 */
	public static function statFile ($filePath)
	{
		//        $bucketName = env('COS_BUCKET_NAME');
		$bucketName = 'wxresource';

		$ret = Cosapi::stat($bucketName, $filePath);

		return $ret;
	}

	/**
	 * 通过全路径 查询文件属性
	 *
	 * @param $allFilePath
	 *
	 * @return array|mixed
	 */
	public static function statFileInfo ($allFilePath)
	{
		if (str_contains($allFilePath, "wechatappdev-10011692")) {
			$filePath   = substr($allFilePath, 47);
			$bucketName = 'wechatappdev';
		} else {
			$filePath   = substr($allFilePath, 45);
			$bucketName = 'wxresource';
		}

		$ret = Cosapi::stat($bucketName, $filePath);

		return $ret;
	}

	/**
	 * 创建一个业务对应的所有文件夹及层级
	 * 为应用创建对应腾讯云app_id目录(V4)
	 *
	 * @param $app_id
	 */
	public static function createAppAllV3Folder ($app_id)
	{
		$paths = [
			$app_id,
			$app_id . '/audio',
			$app_id . '/audio_compressed',
			$app_id . '/image',
			$app_id . '/image/compress',
			$app_id . '/image/ueditor',
			$app_id . '/image_compressed',
			$app_id . '/video',
			$app_id . '/video/mp4',
			$app_id . '/video/source',
			$app_id . '/video/sound',
		];

		foreach ($paths as $path) {
			$result = UploadUtils::createV3Folder($path, $app_id);

			if ($result['code'] == 0 && $result['message'] = 'SUCCESS') {
			} else {
			}
		}
	}

	/**
	 * 创建v3版本的文件夹
	 *
	 * @param $folder
	 * @param $app_id
	 *
	 * @return array|mixed
	 */
	public static function createV3Folder ($folder, $app_id = null)
	{
		$result = Cosapi::createFolder(GlobalString::COS_BUCKET_NAME, $folder, $app_id);

		return $result;
	}
}