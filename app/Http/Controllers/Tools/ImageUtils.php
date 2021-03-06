<?php
/**
 * Created by PhpStorm.
 * User: breeze
 * Date: 13/07/2017
 * Time: 17:04
 */

namespace App\Http\Controllers\Tools;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImageUtils
{
	/**
	 * banner图压缩
	 *
	 * @param Request $request
	 * @param         $app_id
	 * @param         $id
	 * @param         $image_url
	 */
	public static function bannerImgCompress (Request $request, $app_id, $id, $image_url)
	{
		self::ImageCompress($request, 't_banner', $app_id, $id, $image_url, 'img_url_compressed', 750, 280, 80);
	}

	/**
	 * 压缩图片
	 *
	 * @param Request $request
	 * @param         $table_name
	 * @param         $app_id
	 * @param         $id
	 * @param         $image_url
	 * @param         $compress_field
	 * @param int     $image_width
	 * @param int     $image_height
	 * @param int     $image_quality
	 * @param null    $query_id
	 */
	public static function ImageCompress (Request $request, $table_name, $app_id, $id, $image_url, $compress_field,
		$image_width = 320, $image_height = 240, $image_quality = 80, $query_id = null)
	{
		$imageObj = self::getImageObj($table_name, $app_id, $id, $image_url, $compress_field,
			$image_width, $image_height, $image_quality, $query_id);
		self::pushImageList($request, $imageObj);
	}

	/**
	 * 获取可以被压缩的图片对象
	 *
	 * @param      $table_name     //表名
	 * @param      $app_id         //app_id
	 * @param      $id             //资源id
	 * @param      $image_url      //源url
	 * @param      $compress_field //更新的压缩字段
	 * @param int  $image_width    //图片高度
	 * @param int  $image_height   //图片宽度
	 * @param int  $image_quality  //图片质量
	 * @param null $query_id       //主键app_id、query_id
	 *
	 * @return \stdClass
	 */
	public static function getImageObj ($table_name, $app_id, $id, $image_url, $compress_field,
		$image_width = 160, $image_height = 120, $image_quality = 60, $query_id = null)
	{
		$imageObj                 = new \stdClass();
		$imageObj->table_name     = $table_name;
		$imageObj->app_id         = $app_id;
		$imageObj->id             = $id;
		$imageObj->image_url      = $image_url;
		$imageObj->compress_field = $compress_field;
		$imageObj->image_width    = $image_width;
		$imageObj->image_height   = $image_height;
		$imageObj->image_quality  = $image_quality;
		$imageObj->query_id       = $query_id;

		return $imageObj;
	}

	/***
	 * 图片对象放入request的图片列表里
	 *
	 * @param Request $request
	 * @param         $imageObj
	 */
	public static function pushImageList (Request $request, $imageObj)
	{
		if (!empty($request)) {
			if (empty($request->image_list)) {
				$request->image_list = [];
			}
			$request->image_list[] = $imageObj;
		}
	}

	/**
	 * 资源 缩略图 压缩  //专栏、会员、音频、视频、图文、直播
	 *
	 * @param Request $request
	 * @param         $table_name
	 * @param         $app_id
	 * @param         $id
	 * @param         $image_url
	 */
	public static function resImgCompress (Request $request, $table_name, $app_id, $id, $image_url)
	{
		self::ImageCompress($request, $table_name, $app_id, $id, $image_url, 'img_url_compressed', 160, 120, 80);
	}

	/**
	 * 店铺装修分享链接配图压缩
	 *
	 * @param Request $request
	 * @param         $app_id
	 * @param         $image_url
	 */
	public static function shareImgCompress (Request $request, $app_id, $image_url)
	{
		self::ImageCompress($request, 'db_ex_config.t_app_conf', $app_id, 1, $image_url, 'wx_share_image_compressed',
			200, 200, 60, 'wx_app_type');
	}

	/**
	 * 公众号设置图片压缩
	 *
	 * @param Request $request
	 * @param         $app_id
	 * @param         $image_url
	 */
	public static function wxAccountImgCompress (Request $request, $app_id, $image_url)
	{
		self::ImageCompress($request, 'db_ex_config.t_app_conf', $app_id, 1, $image_url, 'wx_qr_url_compressed',
			100, 100, 80, 'wx_app_type');
	}

	/**
	 * 音频日签图片压缩
	 *
	 * @param Request $request
	 * @param         $app_id
	 * @param         $id
	 * @param         $image_url
	 */
	public static function audioSignImgCompress (Request $request, $app_id, $id, $image_url)
	{
		self::ImageCompress($request, 't_audio', $app_id, $id, $image_url, 'sign_url_compressed', 800, 600, 60);
	}

	/**
	 * 视频贴片图片压缩
	 *
	 * @param Request $request
	 * @param         $app_id
	 * @param         $id
	 * @param         $image_url
	 */
	public static function videoPatchImgCompress (Request $request, $app_id, $id, $image_url)
	{
		self::ImageCompress($request, 't_video', $app_id, $id, $image_url, 'patch_img_url_compressed', 750, 420, 60);
	}

	/**
	 * 直播封面图片压缩
	 *
	 * @param Request $request
	 * @param         $app_id
	 * @param         $id
	 * @param         $image_url
	 */
	public static function aliveImgCompress (Request $request, $app_id, $id, $image_url)
	{
		self::ImageCompress($request, 't_alive', $app_id, $id, $image_url, 'alive_img_url', 750, 240, 60);
	}

	/**
	 * 答主头像图片压缩
	 *
	 * @param Request $request
	 * @param         $app_id
	 * @param         $id
	 * @param         $image_url
	 */
	public static function queHeadImgCompress (Request $request, $app_id, $id, $image_url)
	{
		self::ImageCompress($request, 't_que_answerer', $app_id, $id, $image_url, 'answerer_avatar', 80, 80, 80, 'answerer_id');
	}

	/***********************************************************************
	 *************************处理图片资源压缩接口及工具******************************
	 **********************************************************************/

	/**
	 * 单个图片压缩处理
	 *
	 * @param $table_name
	 * @param $app_id
	 * @param $id
	 * @param $image_url
	 * @param $compress_field
	 * @param $image_width
	 * @param $image_height
	 * @param $image_quality
	 * @param $query_id
	 */
	public static function SingleImageCompress ($table_name, $app_id, $id, $image_url, $compress_field, $image_width,
		$image_height, $image_quality, $query_id)
	{
		//压缩原图
		$tar_image = Utils::imageCompress($image_url, $image_width, $image_height, $image_quality);

		//上传压缩图
		$img_url_compressed = Utils::uploadCompressImage($tar_image, $app_id);

		//更新数据库
		if (empty($query_id)) {
			$query_id = 'id';
		}
		$updateResult = DB::table($table_name)
			->where('app_id', '=', $app_id)
			->where($query_id, '=', $id)
			->update([$compress_field => $img_url_compressed]);
		//删除本地文件
		@unlink($tar_image);
	}

	/**
	 * 获取需要处理的banner图列表
	 * @return mixed
	 */
	public static function getDealBannerImgList ()
	{
		$end_time = Utils::getTime(-600);
		$list     = DB::select("
SELECT * from t_banner where image_url is not NULL and image_url != ''
and (img_url_compressed is NULL or image_url = '' or image_url = img_url_compressed)
and created_at < '$end_time'
");

		return $list;
	}

	/**
	 * 获取需要处理的专栏列表
	 * @return mixed
	 */
	public static function getDealProImgList ()
	{
		$end_time = Utils::getTime(-600);
		$list     = DB::select("
SELECT * from t_pay_products where state != 2 and img_url is not null and img_url != ''
and (img_url_compressed is NULL  or img_url_compressed = '' or img_url = img_url_compressed)
and created_at < '$end_time'
");

		return $list;
	}

	/**
	 * 获取需要处理的音频列表
	 * @return mixed
	 */
	public static function getDealAudioImgList ()
	{
		$end_time = Utils::getTime(-600);
		$list     = DB::select("
SELECT * from t_audio where audio_state != 2 and img_url is not null and img_url != ''
and (img_url_compressed is NULL  or img_url_compressed = '' or img_url = img_url_compressed)
and created_at < '$end_time'
");

		return $list;
	}

	/**
	 * 获取需要处理的视频列表
	 * @return mixed
	 */
	public static function getDealVideoImgList ()
	{
		$end_time = Utils::getTime(-600);
		$list     = DB::select("
SELECT * from t_video where video_state != 2 and img_url is not null and img_url != ''
and (img_url_compressed is NULL  or img_url_compressed = '' or img_url = img_url_compressed)
and created_at < '$end_time'
");

		return $list;
	}

	/**
	 * 获取需要处理的图文列表
	 * @return mixed
	 */
	public static function getDealTextImgList ()
	{
		$end_time = Utils::getTime(-600);
		$list     = DB::select("
SELECT * from t_image_text where display_state != 2 and img_url is not null and img_url != ''
and (img_url_compressed is NULL  or img_url_compressed = '' or img_url = img_url_compressed)
and created_at < '$end_time'
");

		return $list;
	}

	/**
	 * 获取需要处理的直播列表
	 * @return mixed
	 */
	public static function getDealAliveImgList ()
	{
		$end_time = Utils::getTime(-600);
		$list     = DB::select("
SELECT * from t_alive where state != 2 and img_url is not null and img_url != ''
and (img_url_compressed is NULL  or img_url_compressed = '' or img_url = img_url_compressed)
and created_at < '$end_time'
");

		return $list;
	}
}