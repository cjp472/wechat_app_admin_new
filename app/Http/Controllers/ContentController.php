<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\MessagePush;
use App\Http\Controllers\Tools\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use League\Flysystem\Exception;

class ContentController extends Controller
{
	private $request;

	public function __construct (Request $request)
	{
		$this->request = $request;
		//todo:这里不要加appid
	}

	//内容创建页面
	public function getCreateContent ()
	{
		$video_upload = Utils::getVideoUpload(AppUtils::getAppID());
		$uploadmax    = Utils::getVideoMax();
		$isHadSetTemp = MessagePush::isHadSetTemp(AppUtils::getAppID());

		return view('admin.createContent', compact('video_upload', 'uploadmax', 'isHadSetTemp'));
	}

	//创建音频页面
	public function createAudio ()
	{
		//        $url = env("TEMPLATE_ID").$this->app_id;

		//查询所有的包
		$package_list = $this->getAllPackages();
		$result       = AppUtils::getModuleInfo(AppUtils::getAppID());
		$isHadSetTemp = MessagePush::isHadSetTemp(AppUtils::getAppID());

		return view('admin.uploadAudio', compact('package_list', 'result', 'isHadSetTemp'));
	}

	//编辑音频页面

	public function getAllPackages ()
	{
		//查询所有的包
		$package_list = \DB::table('t_pay_products')
			->where('app_id', '=', AppUtils::getAppID())->where('state', '<', '2')
			->orderby('created_at', 'desc')
			->get();

		return $package_list;
	}

	//拉取音频列表

	public function editAudio ()
	{
		$resource_id = Input::get('id', '');
		$audio       = \DB::table('t_audio')
			->where('id', '=', $resource_id)
			->where('app_id', '=', AppUtils::getAppID())
			->first();

		$single_sale = 0;

		if ($audio->payment_type != 1)//若音频为单卖,则判断其是否为专栏外单卖
		{

			//TODO:在关系表t_pro_res_realtion中查找该resource_id的记录
			$is_exist = \DB::table('t_pro_res_relation')
				->where('resource_id', '=', $resource_id)
				->where('app_id', '=', AppUtils::getAppID())
				->where('resource_type', '=', 2)
				->where('relation_state', '=', 0)
				->first();
			if (count($is_exist)) {
				if ($audio->payment_type == 2) {
					$single_sale = 1;
				}

				$audio->product_id   = $is_exist->product_id;
				$audio->product_name = $is_exist->product_name;
			}
		}
		//获取所有的产品包
		$package_list = $this->getAllPackages();
		$app_id       = AppUtils::getAppID();
		$result       = AppUtils::getModuleInfo($app_id);

		//获取无音效资源
		$noEffect = \DB::table('t_audio_attachment')
			->where('id', '=', $resource_id)
			->where('app_id', '=', AppUtils::getAppID())
			->first();

		$isHadSetTemp = MessagePush::isHadSetTemp(AppUtils::getAppID());

		return view('admin.editAudio', compact('audio', 'noEffect', 'package_list', 'result', 'single_sale', 'isHadSetTemp'));
	}

	//手动触发发单条音频文件压缩转码处理

	public function getAudioList ()
	{
		//音频列表
		$resource_attr  = Input::get('resource_attr', 'title');
		$search_content = Input::get('search_content', '');
		$app_id         = AppUtils::getAppID();
		if (empty($search_content)) {
			$audios = \DB::table('t_audio')
				->where('app_id', '=', $app_id)
				->where('audio_state', '!=', 2)
				->orderby('start_at', 'desc')
				->paginate(10);
		} else {
			$audios = \DB::table('t_audio')
				->where($resource_attr, 'like', '%' . $search_content . '%')
				->where('app_id', '=', $app_id)
				->where('audio_state', '!=', 2)
				->orderby('start_at', 'desc')
				->paginate(10);
		}

		if ($audios) {
			//生成资源访问链接
			if (session('wxapp_join_statu') == 1 || session('is_collection') == 1) {
				if (session('is_collection') == 0) {
					$pageUrl = AppUtils::getUrlHeader($app_id) . session('wx_app_id') . '.' . env('DOMAIN_NAME');
				} else {
					$pageUrl = AppUtils::getUrlHeader($app_id) . env('DOMAIN_DUAN_NAME');
				}
				foreach ($audios as $key => $audio) {
					$audio->pageurl = $pageUrl . Utils::getContentUrl(2, 2, $audio->id, $audio->product_id, '');

				}
			}

			//            if(session('is_collection') == 1){
			//                $pageUrl = env('APP_HTTP').env('DOMAIN_DUAN_NAME').'/'.$app_id;
			//
			//                foreach ($audios as $key => $audio) {
			//                    $audio->pageurl = $pageUrl.Utils::getContentUrl(2, 2, $audio->id, $audio->product_id,'');
			//
			//                }
			//            }

			//查询音频播放统计数据
			foreach ($audios as $key => $audio) {
				$audio_count = \DB::select("select sum(play_count) as playSum, sum(finish_count) as finishSum from t_audio_analyse
 where app_id='$app_id' and audio_id='$audio->id' ");//dump($audio_count);

				$audio->playcount     = $audio_count[0]->playSum ? $audio_count[0]->playSum : 0;
				$audio->finishcount   = $audio_count[0]->finishSum ? $audio_count[0]->finishSum : 0;
				$audio->finishpercent = $audio_count[0]->finishSum && $audio_count[0]->playSum ? round(($audio_count[0]->finishSum / $audio_count[0]->playSum), 4) * 100 : '0.00';

				//                    if ($audio_count) {} else {}
				//                    $audio->playcount = 26543543530;
				//                    $audio->finishcount = 352543253250;
				//                    $audio->finishpercent = 100.00;

				//  查询每一条音频在关系表中的状态
				$relation = \DB::table('t_pro_res_relation')
					->where('app_id', '=', $app_id)
					->where('resource_id', '=', $audio->id)
					->where('resource_type', '=', 2)
					->where('relation_state', '！=', 1)
					->first();
				if (empty($relation) || empty($relation->product_id) || empty($relation->product_name)) {
					//  查不到数据，单卖
					$audios[ $key ]->product_id   = '';
					$audios[ $key ]->product_name = '';
				} else {
					//  查到数据，专栏内单卖
					$audios[ $key ]->product_id   = $relation->product_id;
					$audios[ $key ]->product_name = $relation->product_name;
				}
			}

		}   //dump($audios);
		//查询总数
		$total = \DB::select("select count(1) as count from t_audio where app_id = '$app_id'")[0];

		$app_id = AppUtils::getAppID();
		//判断是否开放功能
		$result = AppUtils::getModuleInfo($app_id);

		return view('admin.audioList', compact('audios', 'resource_attr', 'search_content', 'total', 'result'));
	}

	//手动触发批量音频文件压缩转码处理

	public function oneAudioasync ()
	{
		$app_id    = Input::get('app_id');
		$id        = Input::get('id');
		$audio_url = Input::get('audio_url');
		Utils::asyncThread(env("HOST_URL") . '/mp3tom3u8?app_id=' . $app_id . '&id=' . $id . '&cdn_url=' . $audio_url);
	}

	//上传资源

	public function batchStartMp3tom3u8 ()
	{
		$date = Input::get('date', '');
		//按日期排查
		if (!$date) {
			$date = date('Y-m-d', time());
		}
		$reurl = '?date=' . $date;
		$appid = Input::get('appid', '');
		if ($appid) $reurl .= '&appid=' . $appid;

		Utils::asyncThread(env("HOST_URL") . '/batchmp3tom3u8' . $reurl);
	}

	//保存资源编辑

	public function uploadResource ()
	{
		$resource_type = Input::get('resource_type', '');

		$params = Input::get('params', '');

		$noEffectAudio = Input::get('noEffectAudio', '');

		//若为单笔,则价格不能为零
		if (array_key_exists('payment_type', $params)
			&& array_key_exists('piece_price', $params)
			&& $params['payment_type'] == 2
			&& $params['piece_price'] == 0) {
			return response()->json(['code' => -521, 'msg' => '上传失败，单价需大于0元!']);
		}

		//若客户使用的是"个人运营模式"即use_collection=1,则限制其piece_price不能超过1000元
		$model_result = \DB::connection("mysql_config")->table("t_app_conf")
			->where("app_id", "=", AppUtils::getAppIdByOpenId(AppUtils::getOpenId()))
			->where("wx_app_type", "=", 1)
			->first();

		if ($model_result->use_collection == 1) {
			if (array_key_exists('piece_price', $params) && $params['piece_price'] > 100000) {
				//TODO:返回操作失败,个人运营模式下 定价不能超过1000元
				return response()->json(['code' => -521, 'msg' => '上传失败，单价不能超过1000元!']);
			}
		}

		$addpackage = 1; //资源包新增参数
		if ($resource_type == 'video') {

			$externalParams   = Input::get('resource_params', '');
			$public_file_id   = $externalParams['public_video'];
			$public_size_text = $externalParams['public_size_text'];
			$public_size_text = explode("M", $public_size_text)[0]; //去掉M

			$params['file_id']    = $public_file_id;
			$params['video_size'] = $public_size_text;
			$params['video_url']  = ''; //暂时置空,转码后获取url

			//判断是否上传了视频贴片,如果没上传用视频封面做贴片
			if (!array_key_exists('patch_img_url', $params)) {
				$params['patch_img_url']            = $params['img_url'];
				$params['patch_img_url_compressed'] = $params['img_url_compressed'];
			}

			//转码之前,视频置为下架
			$params['video_state'] = 1;
			$addpackage            = 0; //资源包新增参数

		}

		//分离descrb
		$params['descrb'] = $this->sliceUE($params['descrb']);
		if ($params['descrb'] == false) {
			//编辑器内容有问题 给前端返回提示信息并取消上传
			return response()->json(['code' => -2, 'msg' => '上传失败，复制的内容有问题，请尝试分开复制，或记录复制内容并联系技术小哥']);
		}
		//插入当前时间
		$app_id               = AppUtils::getAppID();
		$current_time         = Utils::getTime();
		$params['created_at'] = $current_time;
		$params['updated_at'] = $current_time;

		$relation_at  = 0;//资源关系更新参数
		$resourceType = [
			'article' => '1',
			'audio'   => '2',
			'video'   => '3',
			'alive'   => '4',
		];

		if ($resource_type == 'audio') {
			$table_name = 't_audio';
			$id         = Utils::getUniId('a_');
			if (array_key_exists('audio_url', $params) && array_key_exists('audio_size', $params)) {
				/*if ($params['audio_size'] > env('MAX_AUDIO_SIZE'))
				{
					$this->audioDeal($params['audio_url'], $id);
				}*/
				$params['audio_compress_size'] = $params['audio_size'];
				Utils::asyncThread(env("HOST_URL") . '/mp3tom3u8?app_id=' . $app_id . '&id=' . $id . '&cdn_url=' . $params['audio_url']);
			}
			$addpackage = 1; //资源包新增参数
		} else if ($resource_type == 'video') {
			$table_name = 't_video';
			$id         = Utils::getUniId('v_');
			//            $this->videoDeal($params['video_url'], $id);
		}

		$params['id'] = $id;

		$params['app_id'] = $app_id;
		if (array_key_exists('payment_type', $params) && $params['payment_type'] == 3) {
			$relation_at               = 1;
			$relation['app_id']        = $params['app_id'];
			$relation['product_id']    = $params['product_id'];
			$relation['product_name']  = $params['product_name'];
			$relation['resource_type'] = $resourceType[ $resource_type ];
			$relation['resource_id']   = $params['id'];
			$relation['created_at']    = Utils::getTime();

			// 如果用户开启了消息推送
			if (!empty($params['push_state'])) {
				if ($params['push_state'] == 1) $params['push_state'] = 1;
			}

			$is_single_sale = Input::get('is_single_sale', 0);
			if ($is_single_sale == 1)//属于该专栏的该音频资源可以单卖
			{
				//在表t_pro_res_relation中插入该关系
				//                $result_relation = \DB::table('t_pro_res_relation')->insert($relation);
				$params['payment_type'] = 2;
			}
		}

		$result = \DB::table($table_name)->insert($params);

		//获取资源中所有图片大小,并更新至image_size_total中
		$item = \DB::table($table_name)->where('app_id', '=', $app_id)->where('id', '=', $id)->first();
		if ($item) {
			Utils::updateAudioImgTotalSize($item);
		}

		if ($resource_type == 'audio' && $noEffectAudio && !empty($noEffectAudio['audio_url'])) {
			//插入无音效音频
			$noEffectAudio['app_id']     = $app_id;
			$noEffectAudio['id']         = $id;
			$noEffectAudio['created_at'] = Utils::getTime();
			$noEffectResult              = \DB::table('t_audio_attachment')->insert(
				$noEffectAudio
			);
			if (!$noEffectResult) {
				//插入失败
				return response()->json(['code' => -1, 'msg' => '新增音频（无音效）失败']);
			}
		}

		if ($result) {
			//更新资源关系
			if ($relation_at == 1) $relation_add = \DB::table('t_pro_res_relation')->insert($relation);
			//图片压缩
			if (array_key_exists('img_url', $params)) $this->imageDeal($params['img_url'], $table_name, $id);//,160,120,60);
			if ($resource_type == 'audio' && array_key_exists('sign_url', $params)) $this->imageDealo($params['sign_url'], $table_name, $id, 600, 800, 60, 'sign_url_compressed');
		}

		//新增资源默认为上架状态情况下
		if ($addpackage) //资源包新增参数
		{
			//产品包资源数加1
			if ($resource_type != 'package' && $params['payment_type'] == 3) {
				$product_id    = $params['product_id'];
				$update_result = \DB::update("UPDATE t_pay_products SET resource_count=resource_count+1 WHERE id='$product_id' and app_id = '$app_id'");
			}
		}

		if ($result > 0 || $result) {
			return response()->json(['code' => 0, 'msg' => '新增成功']);
		} else {
			return response()->json(['code' => -1, 'msg' => '新增失败']);
		}
	}

	//查询所有的专栏

	public function sliceUE ($html)
	{
		$content = [];
		$out     = explode('<', $html);
		for ($i = 0; $i < count($out); $i++) {
			$in = explode('>', $out[ $i ]);
			for ($j = 0; $j < count($in); $j++) {
				$length = count($content);
				if (strstr($in[ $j ], 'img')) {
					$content[ $length ]["type"] = 1;
					try {
						if (!isset(explode('src="', $in[ $j ])[1])) {
							throw new Exception("复制的内容有问题，请尝试分开复制，或记录复制内容并联系技术小哥");
						} else {
							$content[ $length ]["value"] = explode('"', explode('src="', $in[ $j ])[1])[0];
						}
					} catch (Exception $e) {
						//var_dump($e->getMessage());
						return false;
					}

				} else {
					//                    $text=str_replace(array("\r", "\n", "\r\n"),'',$in[$j]);
					//                    $text = $in[$j];
					//                    if(!empty($text))
					//                    {
					//                        $content[$length]["type"]=0;
					//                        $content[$length]["value"]=$text;
					//                    }
					$content[ $length ]["type"]  = 0;
					$content[ $length ]["value"] = $in[ $j ];
				}
			}
		}

		return json_encode($content, JSON_UNESCAPED_UNICODE);
	}

	/**
	 *图片处理
	 *
	 * @param $image_url
	 * @param $table_name
	 * @param $image_id
	 */
	public function imageDeal ($image_url, $table_name, $image_id)
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
	public function imageDealo ($image_url, $table_name, $image_id, $image_width, $image_height, $image_quality, $compressed)
	{
		$host_url = env('HOST_URL');
		$app_id   = AppUtils::getAppID();

		Utils::asyncThread($host_url . '/downloadImaged?image_id=' . $image_id . '&app_id='
			. $app_id . '&table_name=' . $table_name . '&file_url=' . $image_url
			. '&image_width=' . $image_width . '&image_height=' . $image_height . '&image_quality=' . $image_quality
			. '&compressed=' . $compressed);

	}

	public function saveResourceEdit ()
	{
		$resource_type = Input::get('resource_type', '');
		$resource_id   = Input::get('id', '');

		$params = Input::get('params', '');
		//        dd($params);
		//无音效音频
		$noEffectParams = Input::get('noEffectParams', '');
		$is_single_sale = Input::get('is_single_sale', -1);

		//若为单笔,则价格不能为零
		if (array_key_exists('payment_type', $params)
			&& array_key_exists('piece_price', $params)
			&& $params['payment_type'] == 2
			&& $params['piece_price'] == 0) {
			return response()->json(['code' => -521, 'msg' => '上传失败，单价需大于0元!']);
		}

		//若客户使用的是"个人运营模式"即use_collection=1,则限制其piece_price不能超过200元
		$model_result = \DB::connection("mysql_config")->table("t_app_conf")
			->where("app_id", "=", AppUtils::getAppIdByOpenId(AppUtils::getOpenId()))
			->where("wx_app_type", "=", 1)->first();

		if ($model_result->use_collection == 1) {
			if (array_key_exists('piece_price', $params) && $params['piece_price'] > 100000) {
				//TODO:返回操作失败,个人运营模式下 定价不能超过1000元
				return response()->json(['code' => -521, 'msg' => '编辑失败，"个人运营模式"下单价不能超过1000元!']);
			}
		}

		//资源关系表更新\新增
		$relation_at  = 0;
		$relation_add = 0;
		$resourceType = [
			'article' => '1',
			'audio'   => '2',
			'video'   => '3',
			'alive'   => '4',
		];

		if ($resource_type == 'video') {
			$externalParams = Input::get('resource_params', '');
			if (!empty($externalParams)) {
				$public_file_id   = $externalParams['public_video'];
				$public_size_text = $externalParams['public_size_text'];
				$public_size_text = explode("M", $public_size_text)[0]; //去掉M

				$params['file_id']    = $public_file_id;
				$params['video_size'] = $public_size_text;
				//                $params['video_url'] = ''; //暂时置空,转码后获取url
			}
		}

		//分离descrb
		if (array_key_exists('descrb', $params)) {
			$params['descrb'] = $this->sliceUE($params['descrb']);
			if ($params['descrb'] == false) {
				//编辑器内容有问题 给前端返回提示信息并取消上传
				return response()->json(['code' => -2, 'msg' => '上传失败，复制的内容有问题，请尝试分开复制，或记录复制内容并联系技术小哥']);
			}
		}
		//        echo "line:375";
		//插入当前时间
		$app_id               = AppUtils::getAppID();
		$current_time         = Utils::getTime();
		$params['updated_at'] = $current_time;

		//        echo "line:381";

		if ($resource_type == 'audio') {
			$table_name = 't_audio';
			if (array_key_exists('audio_url', $params) && array_key_exists('audio_size', $params))//压缩+转码
			{
				/*if ($params['audio_size'] > env('MAX_AUDIO_SIZE'))
				{
					$this->audioDeal($params['audio_url'], $resource_id);
				}*/
				Utils::asyncThread(env("HOST_URL") . '/mp3tom3u8?app_id=' . $app_id . '&id=' . $resource_id . '&cdn_url=' . $params['audio_url']);
			}

		} else if ($resource_type == 'video') {
			$table_name = 't_video';
		} else if ($resource_type == 'article') {
			$table_name = 't_image_text';
		}

		//        echo "line:404";
		$old = \DB::select("select * from " . $table_name . " where app_id = ? and id = ?", [$app_id, $resource_id]);
		/****************消息推送***************/
		if (!empty($old['push_state'])) {
			if ($old['push_state'] == 2 || $params['push_state'] == 2) {
				Utils::array_remove($params, 'push_state');
			}
		}

		//当payment_type发生改变时
		if (array_key_exists('payment_type', $params)) {
			//            echo "line:409";
			//            echo "into if(array_key_exists)";

			$oldType = $old[0]->payment_type;
			$newType = $params['payment_type'];
			if ($resource_type == 'audio') $editPackage = $old[0]->audio_state;
			else if ($resource_type == 'video') $editPackage = $old[0]->video_state;
			else if ($resource_type == 'article') $editPackage = $old[0]->display_state;
			$old_is_single_sale = 0;

			//判断是否为专栏外单卖
			if ($oldType == 2) {
				$is_exist = \DB::select("select * from t_pro_res_relation where app_id = ? and resource_type = 2 and resource_id = ? and relation_state=0", [$app_id, $resource_id]);
				if (count($is_exist)) {
					$old_is_single_sale = 1;
				}
			}

			//            echo $oldType."*******niqushi**********".$newType;
			//单个变为产品包
			if ($oldType != 3 && $newType == 3) {
				//资源关系
				//$relation_add = 1;
				//TODO:检测该resource_id在资源关系表中是否存在记录,若有则更新专栏信息,若无则标志新增关系
				$is_exist = \DB::select("select * from t_pro_res_relation where app_id = ? and resource_type = 2 and resource_id = ? ", [$app_id, $resource_id]);
				if (count($is_exist)) {
					$relation_at                = 1; //更新
					$relation['updated_at']     = Utils::getTime();
					$relation['relation_state'] = 0;

				} else {
					$relation_add           = 1;
					$relation['created_at'] = Utils::getTime();
					$relation['app_id']     = $app_id;

					$relation['resource_id'] = $resource_id;
				}

				$relation['product_id']    = $params['product_id'];
				$relation['resource_type'] = '2';
				$relation['product_name']  = $params['product_name'];

				if ($is_single_sale == 1)//该资源可以专栏外单卖
				{
					$params['payment_type'] = 2;
				}
			} //产品包变为单个
			else if (($oldType == 3 || $old_is_single_sale == 1) && $newType != 3) {
				$old_relation = \DB::select("select * from t_pro_res_relation where app_id = ? and product_id = ? and resource_type=$resourceType[$resource_type] and resource_id = ? and relation_state = 0",
					[$app_id, $old[0]->product_id, $resource_id]);
				if ($old_relation) {
					$relation_at = 2; //更新
					//                    $relation['updated_at'] = Utils::getTime();
					//                    $relation['relation_state'] = 0;

					//清除params中有关专栏的信息
					//                    $params['product_name'] = "";
					//                    $params['product_id'] = "";
				}
			} //产品包之间转换
			else if ($oldType == 3 && $newType == 3) {
				//+1
				//资源关系
				//                $relation_add = 1;
				//-1
				$old_relation = \DB::select("select * from t_pro_res_relation where app_id = ? and resource_type=$resourceType[$resource_type] and resource_id = ?",
					[$app_id, $resource_id]);
				if ($old_relation) {
					$relation_at                = 1; //更新
					$relation['updated_at']     = Utils::getTime();
					$relation['relation_state'] = 0;
				} else {
					$relation_add       = 1;
					$relation['app_id'] = $app_id;

					$relation['resource_id'] = $resource_id;
					$relation['created_at']  = Utils::getTime();
				}
				$relation['product_id']    = $params['product_id'];
				$relation['resource_type'] = '2';
				$relation['product_name']  = $params['product_name'];

				if ($is_single_sale == 1)//该资源可以专栏外单卖
				{
					$params['payment_type'] = 2;
				}

			}

			//资源为上架状态，更行相关产品包计数
			if ($editPackage == 0) {
				//单个变为产品包
				if ($oldType != 3 && $newType == 3) {
					$updateCount = \DB::update("update t_pay_products set resource_count=resource_count+1 
                where app_id = ? and id = ?", [$app_id, $params['product_id']]);
				} //产品包变为单个
				else if ($oldType == 3 && $newType != 3) {
					$updateCount = \DB::update("update t_pay_products set resource_count=resource_count-1 
                where app_id = ? and id = ?", [$app_id, $old[0]->product_id]);
					//将资源的专栏id 和 专栏名称 置空
					$updateResource = \DB::update("update " . $table_name . " set product_id=null,product_name=null 
                where app_id = ? and id = ?", [$app_id, $resource_id]);
				} //产品包之间转换
				else if ($oldType == 3 && $newType == 3) {
					//+1
					$updateCount = \DB::update("update t_pay_products set resource_count=resource_count+1 
                where app_id = ? and id = ?", [$app_id, $params['product_id']]);
					//-1
					$updateCount = \DB::update("update t_pay_products set resource_count=resource_count-1 
                where app_id = ? and id = ?", [$app_id, $old[0]->product_id]);
				}
			}

		} else {
			//dd("没有payment_type");
		}

		if (array_key_exists('audio_state', $params) || array_key_exists('video_state', $params) || array_key_exists('display_state', $params)) {
			$resourceInfo = \DB::select("select * from " . $table_name . " where app_id = ? and id = ?", [$app_id, $resource_id]);
			if ($resource_type == 'audio') {
				$editPackage  = $resourceInfo[0]->audio_state;
				$params_state = $params['audio_state'];
			} else if ($resource_type == 'video') {
				$editPackage  = $resourceInfo[0]->video_state;
				$params_state = $params['video_state'];
			} else if ($resource_type == 'article') {
				$editPackage  = $resourceInfo[0]->display_state;
				$params_state = $params['display_state'];
			}

		}

		$result = \DB::table($table_name)
			->where('id', '=', $resource_id)
			->where('app_id', '=', $app_id)
			->update($params);

		//获取资源中所有图片大小,并更新至image_size_total中
		$item = \DB::table($table_name)->where('app_id', '=', $app_id)->where('id', '=', $resource_id)->first();
		if ($item) {
			Utils::updateAudioImgTotalSize($item);
		}

		//更新无音效资源
		if ($noEffectParams) {
			$noEffectParams['updated_at'] = $current_time;
			$noEffectResult               = \DB::table('t_audio_attachment')
				->where('id', '=', $resource_id)
				->where('app_id', '=', $app_id)
				->update($noEffectParams);
			if (!$noEffectResult) {
				return response()->json(['code' => -1, 'msg' => '修改失败']);
			}
		}

		////上下架删除资源等操作 需同步更新专栏期数值
		if (array_key_exists('audio_state', $params) || array_key_exists('video_state', $params) || array_key_exists('display_state', $params)) {
			//$result && $resourceInfo[0]->payment_type == 3
			if ($result) {
				////下架资源操作时 同步相关专栏期数值
				if ($params_state == 1) {
					if ($editPackage == 0) {
						$updatePackage = \DB::update("update t_pay_products set resource_count=resource_count-1
                  where app_id = ? and id = ?", [$app_id, $resourceInfo[0]->product_id]);
					} else {
						$updatePackage = 1;
					}

					if ($updatePackage) return response()->json(['code' => 0, 'msg' => '下架成功']);
				}
				////上架架资源操作时 同步相关专栏期数值
				if ($params_state == 0) {
					$updatePackage = \DB::update("update t_pay_products set resource_count=resource_count+1
                  where app_id = ? and id = ?", [$app_id, $resourceInfo[0]->product_id]);

					if ($updatePackage) return response()->json(['code' => 0, 'msg' => '上架成功']);
				}
				////删除资源操作state == 2 需同步更新专栏期数值
				if ($params_state == 2) {
					if ($editPackage == 0) {
						$updatePackage = \DB::update("update t_pay_products set resource_count=resource_count-1
                  where app_id = ? and id = ?", [$app_id, $resourceInfo[0]->product_id]);
					} else {
						$updatePackage = 1;
					}

					//更新资源关系表中记录为删除状态
					if ($resourceInfo[0]->payment_type != 1) {
						//检测在资源表中是否存在记录
						$is_exist = \DB::select("select * from t_pro_res_relation where app_id = ? and resource_type = 2 and resource_id = ? and relation_state=0", [$app_id, $resource_id]);
						if (count($is_exist)) {
							$relation_at = \DB::table("t_pro_res_relation")
								->where('app_id', '=', $app_id)
								->where('resource_type', '=', 2)
								->where('resource_id', '=', $resource_id)
								->update(['relation_state' => '1']);
						}
					}

					if ($updatePackage) return response()->json(['code' => 0, 'msg' => '删除成功' . $editPackage]);

				}
			}
		}

		//如果产品包名编辑,修改资源表中对应的产品包名
		try {
			$product_name = $params['name'];
		} catch (\Exception $e) {
			$product_name = "";
		}

		if ($result) {
			//资源关系更新
			if ($relation_at == 1) {
				$relation_at = \DB::table('t_pro_res_relation')
					->where('app_id', '=', $app_id)
					->where('resource_type', '=', $resourceType[ $resource_type ])
					->where('resource_id', '=', $resource_id)
					//                    ->where('relation_state','=','0')
					->update($relation);
			}
			//删除关系
			if ($relation_at == 2) {
				//解除旧的资源关系 一对多不指定专栏//->where('product_id','=',$old[0]->product_id)
				$relation_at = \DB::table("t_pro_res_relation")
					->where('app_id', '=', $app_id)
					->where('resource_type', '=', $resourceType[ $resource_type ])
					->where('resource_id', '=', $resource_id)
					->update(['relation_state' => '1']);
			}
			if ($relation_add) {
				$relation_time = Utils::getTime();
				/*//$package = array('$params[product_id]');
				if(count(package)){foreach($package as $product_id){}}
				 */
				$relation_add = \DB::connection('mysql')->insert("insert into t_pro_res_relation SET 
app_id = '$app_id',product_name='$params[product_name]', product_id = '$params[product_id]', resource_type = $resourceType[$resource_type], resource_id = '$resource_id', created_at = '$relation_time'
on duplicate key 
update relation_state = '0' ,updated_at = '$relation_time'
");
				//                $relation_add = \DB::table("t_pro_res_relation")->insert($relation);
			}

			//图片压缩
			if (array_key_exists('img_url', $params)) $this->imageDeal($params['img_url'], $table_name, $resource_id);//,160,120,60);
			if (array_key_exists('sign_url', $params)) $this->imageDealo($params['sign_url'], $table_name, $resource_id, 600, 800, 60, 'sign_url_compressed');
		}

		if ($resource_type == 'package' && !empty($product_name)) {
			$audio_update_result      = \DB::update("UPDATE t_audio SET product_name='$product_name' WHERE product_id='$resource_id' and app_id = '$app_id'");
			$video_update_result      = \DB::update("UPDATE t_video SET product_name='$product_name' WHERE product_id='$resource_id' and app_id = '$app_id'");
			$image_text_update_result = \DB::update("UPDATE t_image_text SET product_name='$product_name' WHERE product_id='$resource_id' and app_id = '$app_id'");
			$alive_update_result      = \DB::update("UPDATE t_alive SET product_name='$product_name' WHERE product_id='$resource_id' and app_id = '$app_id'");
		}

		//修改了package包的状态,要修改对应的资源表对应的包的状态
		if ($resource_type == 'package' && array_key_exists('state', $params)) {
			$state                   = $params['state'];
			$audio_update_state      = \DB::update("UPDATE t_audio SET product_state='$state' WHERE product_id='$resource_id' and app_id = '$app_id'");
			$video_update_state      = \DB::update("UPDATE t_video SET product_state='$state' WHERE product_id='$resource_id' and app_id = '$app_id'");
			$image_text_update_state = \DB::update("UPDATE t_image_text SET product_state='$state' WHERE product_id='$resource_id' and app_id = '$app_id'");
			$alive_update_state      = \DB::update("UPDATE t_alive SET product_state='$state' WHERE product_id='$resource_id' and app_id = '$app_id'");
		}

		//修改了package包的价格,要修改对应的资源表对应的包的状态
		if ($resource_type == 'package' && array_key_exists('price', $params)) {
			$price                   = $params['price'];
			$audio_update_price      = \DB::update("UPDATE t_audio SET piece_price='$price' WHERE product_id='$resource_id' and app_id = '$app_id'");
			$video_update_price      = \DB::update("UPDATE t_video SET piece_price='$price' WHERE product_id='$resource_id' and app_id = '$app_id'");
			$image_text_update_price = \DB::update("UPDATE t_image_text SET piece_price='$price' WHERE product_id='$resource_id' and app_id = '$app_id'");
			$alive_update_price      = \DB::update("UPDATE t_alive SET piece_price='$price' WHERE product_id='$resource_id' and app_id = '$app_id'");
		}

		if ($result >= 0 || $result) {
			return response()->json(['code' => 0, 'msg' => '修改成功']);
		} else {
			return response()->json(['code' => -1, 'msg' => '修改失败']);
		}
	}

	public function test ()
	{
		return view('test');
	}

	/**
	 * 音频处理
	 *
	 * @param $audio_url
	 * @param $audio_id
	 */
	public function audioDeal ($audio_url, $audio_id)
	{
		$host_url = env('HOST_URL');
		$app_id   = AppUtils::getAppID();

		Utils::asyncThread($host_url . '/downloadAudio?audio_id=' . $audio_id . '&app_id=' . $app_id . '&file_url=' . $audio_url);
		//        Utils::asyncThread($host_url.'/downloadVideo');
	}

	//分隔文本编辑器的内容

	/**
	 * 视频处理
	 *
	 * @param $video_url
	 * @param $video_id
	 */
	public function videoDeal ($video_url, $video_id)
	{
		$host_url = env('HOST_URL');
		$app_id   = AppUtils::getAppID();

		Utils::asyncThread($host_url . '/downloadVideo?video_id=' . $video_id . '&app_id=' . $app_id . '&file_url=' . $video_url);
		//        Utils::asyncThread($host_url.'/downloadVideo');
	}

	//腾讯云点播上传
	//    public function getSign()
	//    {
	//        $file_name = Input::get('f', '');
	//        $t = Input::get('t', '');
	//        $e = Input::get('e', '');
	//        $r = Input::get('r', '');
	//        $file_type = Input::get('ft', '');
	//        $file_sha = Input::get('fs', '');
	//        $fc = Input::get('tc', '');
	//        $uid = Input::get('uid', '');
	//        $s = Input::get('s', '');
	//
	//        $argStr = "f=".$f."&t=".$t."&e=".$e."&r=".$r."&ft=".$ft."&fs=".$fs."&fc=".$fc."&uid=".$uid."&s=".$s;
	//        $argStr = Input::get('argStr', '');
	//        $file_name = $f.".".$file_type;

	// Step 1：获取签名所需信息获取得到的签名所需信息，如下
	//        $secret_id = env('SecretId', '');
	//        $secret_key = env('SecretKey', '');

	// Step 2：设置签名有效时间
	//        $current = time();
	//        $expired = $current + 86400;  // 签名有效期：1天

	// Step 3：根据客户端所提交的文件信息，拼装参数列表
	//        $file_name = "tencent_test.mp4";
	//        $file_sha = "a9993e364706816aba3e25717850c26c9cd0d89d";
	//        $uid="lkdfsjkhfjks";

	//        $arg_list = array(
	//            "s" => $secret_id,
	//            "t" => $current,
	//            "e" => $expired,
	//            "f" => $file_name,
	//            "fs" => $file_sha,
	//            "ft" => $file_type,
	//            "uid" => $uid,
	//            "r" => rand());
	//
	//        // Step 4：生成签名
	//        $orignal = http_build_query($arg_list);
	//        $result = base64_encode(hash_hmac('SHA1', $orignal, $secret_key, true).$orignal);

	//        $argStr = "f=".$f."&ft=".$ft."&fs=".$fs;
	//        $argStr = Input::get('argStr', '');
	//        $secretKey = env('SecretKey');
	//        $bin = hash_hmac('SHA1', $argStr, $secretKey, true);
	//        $result = base64_encode($bin.$argStr);
	//        return response()->json(['code' => 0, 'result' => $result]);
	//    }

	//腾讯云点播上传

	public function getSign ()
	{
		$argStr    = Input::get('args', '');
		$secretKey = env('SecretKey');
		$result    = base64_encode(hash_hmac('sha1', $argStr, $secretKey, true));

		return response()->json(['code' => 0, 'result' => $result]);
	}

	//腾讯云点播上传
	public function getSignV4 ()
	{
		// Step 1：获取签名所需信息获取得到的签名所需信息，如下
		$secretId   = env('SecretId', '');
		$secret_key = env('SecretKey', '');

		$currentTimeStamp = time();
		$expireTime       = time() + 86400;   //签名有效期1天
		$random           = rand();
		$isTranscode      = 1;

		// 向参数列表填入参数
		$arg_list = [
			"secretId"         => $secretId,
			"currentTimeStamp" => $currentTimeStamp,
			"expireTime"       => $expireTime,
			"random"           => $random,
			"isTranscode"      => $isTranscode,
		];

		// 计算签名
		$orignal   = http_build_query($arg_list);
		$signature = base64_encode(hash_hmac('SHA1', $orignal, $secret_key, true) . $orignal);

		return response()->json(['code' => 0, 'result' => $signature]);
	}

	public function getPicUrl ()
	{
		//        $action = 'CreateScreenShot';
		//        $private_params = array(
		//            'pullset.0.fileId'=> '14651978969383094681',
		//            'width'=>200,
		//            'height'=>200
		//        );

		$private_params = ['fileId' => '14651978969389715426'];
		$result         = $this->videoApi('DescribeVodPlayUrls', $private_params);
		var_dump($result);
	}

	//测试获取视频截图接口
	public function videoApi ($action, $private_params)
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

		/*下面这两个参数为 DescribeInstances 接口的私有参数，用于查询特定的虚拟机列表*/
		//        $PRIVATE_PARAMS = array(
		//            'pullset.0.fileId'=> '14651978969383094681',
		//            'width'=>200,
		//            'height'=>200
		//        );

		$PRIVATE_PARAMS = $private_params;

		/***********************************************************************************/

		return $this->CreateRequest($HttpUrl, $HttpMethod, $COMMON_PARAMS, $secretKey, $PRIVATE_PARAMS, $isHttps);

	}

	public function CreateRequest ($HttpUrl, $HttpMethod, $COMMON_PARAMS, $secretKey, $PRIVATE_PARAMS, $isHttps)
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

	/**
	 * 批量更新视频长度+码率
	 */
	public function updateVideoLengthAndVbitrate ()
	{
		set_time_limit(3600);
		$videoList = DB::table('t_video')
			->select('app_id', 'id', 'file_id')
			->where('is_transcode', 1)
			->where('video_mp4_size', '=', 0)
			->whereNotNull('file_id')
			->get();

		$count = 0;
		foreach ($videoList as $item) {
			$count++;

			$app_id   = $item->app_id;
			$video_id = $item->id;
			$file_id  = $item->file_id;
			//            dump($file_id);
			$duration = $this->getVideoLength($file_id);
			$vbitrate = $this->getVideoVbitrate($file_id);
			//            dump($vbitrate);

			$update = [];
			if ($duration > 0) {
				$update['video_length'] = $duration;
			}
			if ($vbitrate['20'] > 0) {
				$update['video_mp4_vbitrate'] = $vbitrate['20'];
				$update['video_mp4_size']     = number_format($vbitrate['20'] / 8 * $duration / 1024 / 1024, 2);
			}
			if ($vbitrate['30'] > 0) {
				$update['video_mp4_high_vbitrate'] = $vbitrate['30'];
				$update['video_mp4_high_size']     = number_format($vbitrate['30'] / 8 * $duration / 1024 / 1024, 2);
			}
			if (count($update) == 0) {
				continue;
			}

			//            dump($update);
			DB::table('t_video')
				->where('app_id', '=', $app_id)
				->where('id', '=', $video_id)
				->where('file_id', '=', $file_id)
				->where('is_transcode', 1)
				->whereNotNull('file_id')
				->update($update);
		}
	}

	/**
	 * 批量获取视频长度
	 *
	 * @param $file_id
	 *
	 * @return int
	 */
	public function getVideoLength ($file_id)
	{
		$duration       = 0;
		$private_params = ['fileIds.1' => $file_id];

		$resultArray = $this->videoApi('DescribeVodInfo', $private_params);
		if ($resultArray['code'] == 0) {
			$duration = $resultArray['fileSet'][0]['duration'];
		}

		return $duration;
	}

	/**
	 * 批量获取视频码率
	 *
	 * @param $file_id
	 */
	public function getVideoVbitrate ($file_id)
	{
		$private_params = ['fileId' => $file_id];

		$resultArray = $this->videoApi('DescribeVodPlayUrls', $private_params);

		$video_vbitrate['20']      = 0;
		$video_vbitrate['30']      = 0;
		$video_vbitrate['20_url']  = "";
		$video_vbitrate['30_url']  = "";
		$video_vbitrate['230']     = 0;
		$video_vbitrate['230_url'] = "";
		if ($resultArray['code'] == 0) {
			$playSet = $resultArray['playSet'];
			for ($i = 0; $i < count($playSet); $i++) {
				if ($playSet[ $i ]['definition'] == 0)//原视频
				{
					$video_url = $playSet[ $i ]['url'];
				} else if ($playSet[ $i ]['definition'] == 20)//标清mp4
				{
					$video_vbitrate['20']     = $playSet[ $i ]['vbitrate'];
					$video_vbitrate['20_url'] = $playSet[ $i ]['url'];
				} else if ($playSet[ $i ]['definition'] == 230)//高清m3u8
				{
					$video_vbitrate['230']     = $playSet[ $i ]['vbitrate'];
					$video_vbitrate['230_url'] = $playSet[ $i ]['url'];
				} else if ($playSet[ $i ]['definition'] == 30)//高清mp4
				{
					$video_vbitrate['30_url'] = $playSet[ $i ]['url'];
					//添加码率
					$video_vbitrate['30'] = $playSet[ $i ]['vbitrate'];
				} else//其他格式 暂时没有提供支持
				{

				}
			}
		}

		return $video_vbitrate;
	}

	/**
	 * 批量更新直播的视频长度+码率
	 */
	public function updateAliveLengthAndVbitrate ()
	{
		$videoList = DB::table('t_alive')
			->select('app_id', 'id', 'file_id')
			->where('is_transcode', 1)
			->whereNotNull('file_id')
			->get();

		$count = 0;
		foreach ($videoList as $item) {
			$count++;

			$app_id   = $item->app_id;
			$video_id = $item->id;
			$file_id  = $item->file_id;
			//            dump($file_id);
			$duration = $this->getVideoLength($file_id);
			$vbitrate = $this->getVideoVbitrate($file_id);
			//            dump($vbitrate);

			$update = [];
			//            if ($duration > 0) {
			//                $update['video_length'] = $duration;
			//            }
			//            if ($vbitrate['20'] > 0) {
			//                $update['video_mp4_vbitrate'] = $vbitrate['20'];
			//                $update['video_mp4_size'] = number_format($vbitrate['20'] / 8 * $duration / 1024 / 1024, 2);
			//            }
			if ($vbitrate['230'] > 0) {
				$update['alive_m3u8_high_vbitrate'] = $vbitrate['230'];
				$update['alive_m3u8_high_size']     = number_format($vbitrate['230'] / 8 * $duration / 1024 / 1024, 2);
			}

			//            dump($update);
			DB::table('t_alive')
				->where('app_id', '=', $app_id)
				->where('id', '=', $video_id)
				->where('file_id', '=', $file_id)
				->where('is_transcode', 1)
				->whereNotNull('file_id')
				->update($update);
		}
	}

	public function transCodeNotify ()
	{

		$params = Input::all();

		$task = $this->searchTaskFromTrans($params);
		if ($task != 'transcode') {
			return;
		}

		$file_id = $this->searchFileId($params);

		$video_length = $this->getVideoLength($file_id);

		//        $video_length = $this->searchduration($params);

		$video_url      = '';
		$video_mp4      = '';
		$video_mp4_high = '';
		$video_hls      = '';
		//根据file_id 查询视频链接
		$private_params = ['fileId' => $file_id];

		$resultArray = $this->videoApi('DescribeVodPlayUrls', $private_params);

		if ($resultArray['code'] == 0) {
			$playSet                = $resultArray['playSet'];
			$video_mp4_size         = 0;
			$video_mp4_high_bitrate = 0;
			for ($i = 0; $i < count($playSet); $i++) {
				if ($playSet[ $i ]['definition'] == 0)//原视频
				{
					$video_url = $playSet[ $i ]['url'];
				} else if ($playSet[ $i ]['definition'] == 20)//标清mp4
				{
					$video_mp4 = $playSet[ $i ]['url'];

					$video_mp4_bitrate = $playSet[ $i ]['vbitrate'];
					$video_mp4_size    = $video_mp4_bitrate / 8 * $video_length / 1024 / 1024;//视频大小(M)= kbps码率/8 * 视频时长/1024

					//update数据库t_video,video_mp4_high_vbitrate='$video_mp4_high_bitrate',video_mp4_size='$video_mp4_size'
					$result = \DB::update("update t_video set video_mp4_vbitrate='$video_mp4_bitrate',video_mp4_size='$video_mp4_size' where file_id='$file_id' ");

				} else if ($playSet[ $i ]['definition'] == 230)//高清m3u8
				{
					$video_hls = $playSet[ $i ]['url'];
				} else if ($playSet[ $i ]['definition'] == 30)//高清mp4
				{
					$video_mp4_high = $playSet[ $i ]['url'];
					//添加码率
					//                    $video_mp4_bitrate = $playSet[$i]['vbitrate'];
					$video_mp4_high_bitrate = $playSet[ $i ]['vbitrate'];
					$video_mp4_high_size    = $video_mp4_high_bitrate / 8 * $video_length / 1024 / 1024;//视频大小(M)= kbps码率/8 * 视频时长/1024

					//update数据库t_video,video_mp4_high_vbitrate='$video_mp4_high_bitrate',video_mp4_size='$video_mp4_size'
					$result = \DB::update("update t_video set video_mp4_high_vbitrate='$video_mp4_high_bitrate',video_mp4_high_size='$video_mp4_high_size' where file_id='$file_id' ");

				} else//其他格式 暂时没有提供支持
				{
				}
			}
		}

		if (empty($file_id)) {
			return;
		}

		//        $video_length = $resultArray['duration'];
		//        $video_length = 1;
		if (!empty($file_id) && (empty($video_url) || empty($video_mp4))) {
			$result = \DB::update("update t_video set video_state = 1, is_transcode = 2 where file_id = '$file_id' and video_state!=2 limit 1");

			//往视频文件中间表t_video_middle_transcode中插入记录
			$data['file_id']     = $file_id;
			$data['video_url']   = $video_url;
			$data['video_mp4']   = $video_mp4;
			$data['source_type'] = 0;
			$insert_result       = Utils::insertRecordVideoTranscode($data);

			return;
		}

		try {
			//更新数据库
			$result = \DB::update("update t_video set video_url='$video_url',video_mp4='$video_mp4',video_hls='$video_hls',
video_mp4_high='$video_mp4_high',video_length='$video_length',video_state=0,is_transcode=1 where file_id='$file_id' and video_state!=2 limit 1");

			//往视频文件中间表t_video_middle_transcode中插入记录
			$data['file_id']        = $file_id;
			$data['video_url']      = $video_url;
			$data['video_mp4']      = $video_mp4;
			$data['video_hls']      = $video_hls;
			$data['video_mp4_high'] = $video_mp4_high;
			$data['video_length']   = $video_length;
			$data['source_type']    = 0;
			$insert_result          = Utils::insertRecordVideoTranscode($data);

			//更新专栏数
			if ($result) {
				$last = \DB::select("select app_id,payment_type,product_id from t_video where file_id = ? ", [$file_id]);
				if ($last && count($last) > 0 && $last[0]->payment_type == 3) {
					$update = \DB::update("update t_pay_products set resource_count=resource_count+1 where 
                    app_id = ? and id = ? limit 1", [$last[0]->app_id, $last[0]->product_id]);
				}
			}
		} catch (\Exception $e) {
		}
	}

	//从视频转码返回结果中找到file_id

	/***
	 * 在回调中查找task
	 *
	 * @param $array
	 *
	 * @return string
	 */
	public function searchTaskFromTrans ($array)
	{
		$result = "";
		foreach ($array as $key => $value) {
			if ($key == '$sh' && strpos($value, 'task') !== false && strpos($value, 'transcode') !== false) {
				$result = 'transcode';
			}
		}
		if (empty($result)) {
			try {
				$result = $array['task'];
			} catch (\Exception $e) {
			}
		}

		return $result;
	}

	//从视频转麻烦会结果中找到duration即时长

	public function searchFileId ($array)
	{
		$file_id = "";
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$file_id = $this->searchFileId($value);
			} else {
				if ($key == 'file_id') {
					$file_id = $value;
					break;
				}
			}
		}

		return $file_id;
	}

	public function searchduration ($array)
	{
		$duration = "";
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$duration = $this->searchduration($value);
			} else {
				if ($key == 'duration') {
					$duration = $value;
					break;
				}
			}
		}

		return $duration;
	}

	//测试用的接口

	public function getProductsInfo ()
	{
		$p_id   = $_GET['id'];
		$app_id = $_GET['app_id'];
		//        $results = \DB::table('t_video')
		//            ->where('app_id', '=', $app_id)
		//            ->where('product_id','=',$p_id)
		//            ->get();

		$results = \DB::select("select * from t_video where app_id='appuAhZGRFx3075' and product_id='p_58242a910c179_tIMKE7B6'");
		var_dump($results);
	}

	//直播视频转码成功回调
	public function aliveTranscode ()
	{

		$params = Input::all();

		$task = $this->searchTaskFromTrans($params);
		if ($task != 'transcode') {
			return;
		}

		$fileId = $this->searchFileId($params);
		//        $length = $params['image_video']['duration'];
		//        $length = $params['duration'];
		$length = $this->getVideoLength($fileId);
		//根据fileId查询hls地址
		$getParams   = ['fileId' => $fileId];
		$resultArray = $this->videoApi('DescribeVodPlayUrls', $getParams);
		if ($resultArray['code'] == 0) {
			$m3u8url = '';//获取m3u8链接
			for ($i = 0; $i < count($resultArray['playSet']); $i++) {
				if ($resultArray['playSet'][ $i ]['definition'] == 230) {
					$m3u8url            = $resultArray['playSet'][ $i ]['url'];
					$alive_m3u8_bitrate = $resultArray['playSet'][ $i ]['vbitrate'];
					$alive_m3u8_size    = $alive_m3u8_bitrate / 8 * $length / 1024 / 1024;//视频大小(M)= kbps码率/8 * 视频时长/1024

					//update数据库t_video,video_mp4_high_vbitrate='$video_mp4_high_bitrate',video_mp4_size='$video_mp4_size'
					$result = \DB::update("update t_alive set alive_m3u8_high_vbitrate='$alive_m3u8_bitrate',alive_m3u8_high_size='$alive_m3u8_size' where file_id='$fileId' ");

				}
			}

			if (empty($m3u8url))//转码失败
			{
				$update = \DB::update("update t_alive set is_transcode = ? where file_id = ?",
					[2, $fileId]);

				//往视频文件中间表t_video_middle_transcode中插入记录
				$data['file_id']     = $fileId;
				$data['m3u8url']     = $m3u8url;
				$data['source_type'] = 1;
				$insert_result       = Utils::insertRecordVideoTranscode($data);

				return;
			}

			//下载m3u8文件到服务器
			$filename = basename($m3u8url);
			$myurl    = storage_path('app/public/temp') . '/' . $filename;
			set_time_limit(24 * 60 * 60);
			$m3u8File = fopen($m3u8url, "rb");
			if ($m3u8File) {
				$myFile = fopen($myurl, "wb");
				if ($myFile) {
					while (!feof($m3u8File)) {
						fwrite($myFile, fread($m3u8File, 1024 * 8), 1024 * 8);
					}
				}
				fclose($m3u8File);
				if ($myFile) {
					fclose($myFile);
				}
			}

			//下载完后获取文件内容并更新
			$listOld = file_get_contents($myurl);
			$listNew = "";
			//转为数组批量替换,最后一个为空不要
			$arrs = explode("\n", $listOld);

			for ($i = 0; $i < count($arrs) - 1; $i++) {
				if (!strstr($arrs[ $i ], "#")) {
					if (strpos($m3u8url, '/f0.f230.m3u8') > 0) {
						$urlHeader  = substr($m3u8url, 0, strpos($m3u8url, '/f0.f230.m3u8'));
						$arrs[ $i ] = $urlHeader . "/" . $arrs[ $i ];
					} else {
						$arrs[ $i ] = "http://" . explode('_', $filename)[0] . ".vod.myqcloud.com/" . $arrs[ $i ];
					}
				}
				$listNew = $listNew . $arrs[ $i ] . "\n";
			}

			$video_length = 0;
			for ($i = 0; $i < count($arrs) - 1; $i++) {
				if (strstr($arrs[ $i ], "#EXTINF:")) {
					$temp         = str_replace(',', '', str_replace('#EXTINF:', '', $arrs[ $i ]));
					$video_length += (float)$temp;
				}
			}

			//往视频文件中间表t_video_middle_transcode中插入记录
			$data['file_id']     = $fileId;
			$data['m3u8url']     = $m3u8url;
			$data['source_type'] = 1;
			$insert_result       = Utils::insertRecordVideoTranscode($data);

			//更新DB字段
			$update = \DB::update("update t_alive set state = ?,is_transcode = ?,list_file_content = ?,
            video_length = ? where file_id = ? and state!= ?", [0, 1, $listNew, $video_length, $fileId, 2]);

			if ($update) {
				//更新专栏数
				$last = \DB::select("select app_id,payment_type,product_id from t_alive where file_id = ? ", [$fileId]);
				if ($last && count($last) > 0 && $last[0]->payment_type == 3) {
					$update = \DB::update("update t_pay_products set resource_count=resource_count+1 where 
                app_id = ? and id = ?", [$last[0]->app_id, $last[0]->product_id]);
				}
			}

		}
	}
}








