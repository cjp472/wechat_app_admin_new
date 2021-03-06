<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class AliveController extends Controller
{
	private $app_id;

	public function __construct ()
	{
		$this->app_id = AppUtils::getAppID();
	}

	//直播首页
	public function alive ()
	{
		$ruler  = trim(Input::get("ruler"));//维度
		$search = trim(Input::get("search"));//搜索内容
		//获取搜索集,总数作为页脚参考
		if (empty($search)) {
			$allInfo = \DB::table("t_alive")->select()->where("app_id", "=", $this->app_id)
				->where("state", "!=", 2)->orderby("created_at", "desc")->paginate(10);
		} else {
			if ($ruler == 0) //直播名称
			{
				$allInfo = \DB::table("t_alive")->select()->where("app_id", "=", $this->app_id)
					->where("state", "!=", 2)->where("title", "like", "%" . $search . "%")
					->orderBy("created_at", "desc")->paginate(10);
			} else if ($ruler == 1) //时间
			{
				$allInfo = \DB::table("t_alive")->select()->where("app_id", "=", $this->app_id)
					->where("state", "!=", 2)->where("created_at", "like", "%" . $search . "%")
					->orderBy("created_at", "desc")->paginate(10);
			} else if ($ruler == 2) //专栏名称
			{
				$allInfo = \DB::table("t_alive")->select()->where("app_id", "=", $this->app_id)
					->where("state", "!=", 2)->where("product_name", "like", "%" . $search . "%")
					->orderBy("created_at", "desc")->paginate(10);
			} else //所有信息
			{
				$allInfo = \DB::table("t_alive")->select()->where("app_id", "=", $this->app_id)
					->where("state", "!=", 2)->orderBy("created_at", "desc")->paginate(10);
			}
		}
		//有搜索集下一步，没有就为空数组
		$data = [];
		if ($allInfo) {

			$pageUrl = '';
			if (session('wxapp_join_statu') == 1 || session('is_collection') == 1) {
				if (session('is_collection') == 0) {
					$pageUrl = AppUtils::getUrlHeader($this->app_id) . session('wx_app_id') . '.' . env('DOMAIN_NAME');
				} else {
					$pageUrl = AppUtils::getUrlHeader($this->app_id) . env('DOMAIN_DUAN_NAME');
				}
			}
			foreach ($allInfo as $key => $value) {
				$data[ $key ]['id']                 = $value->id;
				$data[ $key ]['img_url']            = $value->img_url;
				$data[ $key ]['img_url_compressed'] = $value->img_url_compressed;
				$data[ $key ]['title']              = $value->title;
				$data[ $key ]['state']              = $value->state;

				if ($pageUrl) {
					$data[ $key ]['pageurl'] = $pageUrl . Utils::getContentUrl(2, 4, $value->id, $value->product_id);
				}

				//直播状态
				if (Utils::getTime() < $value->zb_start_at) {
					$data[ $key ]['zb_state'] = '即将直播';
				} else {
					if (empty($value->manual_stop_at) || $value->manual_stop_at == '0000-00-00')//没有手动结束过
					{
						if (Utils::getTime() >= $value->zb_stop_at) {
							$data[ $key ]['zb_state'] = '直播结束';
						} else {
							$data[ $key ]['zb_state'] = '直播中';
						}
					} else //有手动结束过
					{
						$data[ $key ]['zb_state'] = '直播结束';
					}
				}

				$data[ $key ]['start_at']    = $value->start_at;
				$data[ $key ]['zb_start_at'] = $value->zb_start_at;

				//  查询每一条直播在关系表中的状态
				$relation = \DB::table('t_pro_res_relation')
					->where('app_id', '=', $this->app_id)
					->where('resource_id', '=', $value->id)
					->where('resource_type', '=', 4)
					->where('relation_state', '！=', 1)
					->first();
				if (empty($relation) || empty($relation->product_id) || empty($relation->product_name)) {
					//  查不到数据，单卖
					$data[ $key ]['product_id']   = '';
					$data[ $key ]['product_name'] = '无';
				} else {
					//  查到数据，专栏内单卖
					$data[ $key ]['product_id']   = $relation->product_id;
					$data[ $key ]['product_name'] = $relation->product_name;
				}

				if ($value->payment_type == 1) {
					$data[ $key ]['payment_type'] = '免费';
				} else if ($value->payment_type == 2) {
					if (empty($relation) || empty($relation->product_id) || empty($relation->product_name)) {
						$data[ $key ]['payment_type'] = '单卖';
						$data[ $key ]['product_name'] = '无';
					} else {
						$data[ $key ]['payment_type'] = '专栏外单卖';
						$data[ $key ]['product_name'] = $relation->product_name;
					}
				} else {
					$data[ $key ]['payment_type'] = '专栏';
				}

				$data[ $key ]['piece_price']  = round($value->piece_price / 100, 2);
				$data[ $key ]['is_transcode'] = $value->is_transcode;

				/*获取直播人员列表 - start*/
				//        $data[$key]['zb_user_name']=empty($value->zb_user_name)?'无':$value->zb_user_name;
				$zbUsersList = \DB::table("t_alive_role")->select()->where("alive_id", "=", $value->id)
					->where("state", "!=", 1)->orderBy("created_at", "desc")->get();

				foreach ($zbUsersList as $k => $v) {
					$data[ $key ]['zb_user_name'][ $k ] = $v->role_name . " : " . $v->user_name;
				}
				/*获取直播人员列表 - end*/
			}
		}

		return View('admin.aliveList', compact('data', 'search', 'ruler', 'allInfo'));

	}

	//新增直播页面
	public function addAlive ()
	{
		//获取所有专栏
		$packages = \DB::select("select * from t_pay_products where app_id=? and state<2 order by created_at", [$this->app_id]);
		//应用权限
		$appModuleInfo = AppUtils::getModuleInfo($this->app_id);
		//        dd($appModuleInfo);

		// 分类数据
		if (!empty($appModuleInfo) && $appModuleInfo[0]->resource_category == 1) {
			// 分类数据信息
			$category_info = DB::connection('mysql')->table('t_category')
				->where('app_id', '=', AppUtils::getAppID())
				->where('id', '!=', '0')
				->orderby('weight', 'desc')
				->Lists('category_name', 'id');
		}

		return View("admin.addAlive", compact('packages', 'appModuleInfo', 'category_info'));
	}

	//新增直播操作
	public function doAddAlive ()
	{
		$params     = Input::get("params");
		$roleParams = Input::get("roleParams");

		$category_type = Input::get('category_type', '');

		$params['app_id'] = $this->app_id;
		$params['id']     = Utils::getUniId("l_");
		//获取房间名
		$params['room_id'] = Utils::createGroupChatRoom();

		$params['descrb'] = $this->sliceUE($params['descrb']);

		//如果是视频直播,未转码+隐藏;如果是语音直播,已转码+显示
		if (array_key_exists("file_id", $params)) {
			$params['state']        = 1;
			$params['is_transcode'] = 0;
		} else {
			$params['state']        = 0;
			$params['is_transcode'] = 1;
		}
		//新增时压缩图片先和原图片链接一致,保证前端加载正常
		$params['img_url_compressed'] = $params['img_url'];

		$params['created_at'] = Utils::getTime();

		$relation_at = 0;//资源关系更新参数
		$package_add = 0;
		//更新资源关系
		if ($params['payment_type'] == 3) {
			$relation_at               = 1;
			$relation['app_id']        = $params['app_id'];
			$relation['product_id']    = $params['product_id'];
			$relation['product_name']  = $params['product_name'];
			$relation['resource_type'] = '4'; //直播
			$relation['resource_id']   = $params['id'];
			$relation['created_at']    = Utils::getTime();
			//更新相关专栏期数
			if ($params['state'] == 0 && $params['is_transcode'] == 1) $package_add = 1;

			$is_single_sale = Input::get('is_single_sale', 0);
			if ($is_single_sale == 1)//属于该专栏的该音频资源可以单卖
			{
				//在表t_pro_res_relation中插入该关系
				//                $result_relation = \DB::table('t_pro_res_relation')->insert($relation);
				$params['payment_type'] = 2;
			}

		}

		//插直播表
		$insert = \DB::table("t_alive")->insert($params);
		//再插直播角色表
		if ($roleParams) {
			foreach ($roleParams as $key => $value) {
				$value["app_id"]     = $this->app_id;
				$value["alive_id"]   = $params['id'];
				$value["state"]      = 0;
				$value["created_at"] = Utils::getTime();
				$insertRole          = \DB::table("t_alive_role")->insert($value);
			}
		}

		//开启异步线程压缩
		$this->imageDeal($params['img_url'], "t_alive", $params['id']);//,160,120,60);

		if ($insert) {
			if ($relation_at) $relation_add = \DB::table('t_pro_res_relation')->insert($relation);
			if ($package_add) \DB::update("update t_pay_products set resource_count=resource_count+1 where id = '$params[product_id]' ");
		}

		$result_insert = true;
		if ($insert == false) $result_insert = false;

		// 获得分类数据
		$category_info = DB::connection('mysql')->table('t_category')
			->where('app_id', '=', $this->app_id)
			->where('id', '!=', '0')
			->pluck('id');

		// 存入四条分类记录
		foreach ($category_info as $v) {
			$insert = DB::connection('mysql')->table('t_category_resource')->insert(
				['app_id' => $this->app_id, 'category_id' => $v, 'resource_id' => $params['id'], 'resource_type' => 4, 'state' => 0, 'created_at' => Utils::getTime()]
			);

			if ($insert == false) $result_insert = false;
		}
		// 同时插入首页分类记录  默认显示
		$insert = DB::connection('mysql')->table('t_category_resource')->insert(
			['app_id' => $this->app_id, 'category_id' => '0', 'resource_id' => $params['id'], 'resource_type' => 4, 'state' => 1, 'created_at' => Utils::getTime()]
		);

		// 如果有分类信息 添加（更新）
		if (!empty($category_type)) {
			// 再更新所有分类为现在的状态
			DB::connection('mysql')->table('t_category_resource')
				->where('app_id', '=', $this->app_id)
				->where('resource_id', '=', $params['id'])
				->where('resource_type', '=', 4)
				->whereIn('category_id', $category_type)
				->update(['state' => 1]);
		}

		if ($result_insert) {
			return response()->json(['ret' => 0]);
		} else {
			return response()->json(['ret' => 1]);
		}
	}

	//直播下架

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

	//删除直播

	/**
	 *图片处理
	 *
	 * @param $image_url
	 * @param $table_name
	 * @param $image_id
	 * @param $image_width   压缩尺寸 宽度 (默认 160)
	 * @param $image_height  压缩尺寸 高度 (默认 120)
	 * @param $image_quality 压缩参数 质量值 (默认 60), $image_width, $image_height, $image_quality
	 */
	public function imageDeal ($image_url, $table_name, $image_id)
	{
		$host_url = env('HOST_URL');
		$app_id   = AppUtils::getAppID();

		/*/压缩尺寸设定
		$image_width = $image_width? $image_width : 160;
		$image_height = $image_height? $image_height : 120;
		$image_quality = $image_quality? $image_quality : 60;*/
		Utils::asyncThread($host_url . '/downloadImage?image_id=' . $image_id . '&app_id='
			. $app_id . '&table_name=' . $table_name . '&file_url=' . $image_url);
		//. '&image_width=' . $image_width . '&image_height=' . $image_height . '&image_quality=' . $image_quality);

	}

	//直播上架

	public function offSale ()
	{
		$id   = Input::get("id");
		$info = \DB::select("select payment_type,product_id from t_alive where app_id = ? and id = ?", [$this->app_id, $id]);
		//先更新
		$update = \DB::update("update t_alive set state = 1 where app_id = ? and id = ?", [$this->app_id, $id]);
		if ($update >= 0) {
			//如果是专栏资源数要减一
			if ($info[0]->payment_type == 3) {
				$dec = \DB::update("update t_pay_products set resource_count=resource_count-1 where app_id = ? and id = ?",
					[$this->app_id, $info[0]->product_id]);
			}

			return response()->json(['ret' => 0]);
		} else {
			return response()->json(['ret' => 1]);
		}
	}

	//编辑直播页面

	public function delSale ()
	{
		$id = Input::get("id");

		$info = \DB::select("select payment_type,product_id,state from t_alive where app_id = ? and id = ?", [$this->app_id, $id]);
		//先更新
		$update = \DB::update("update t_alive set state = 2 where app_id = ? and id = ?", [$this->app_id, $id]);
		if ($update >= 0) {
			//如果是专栏资源数要减一
			if ($info[0]->payment_type == 3 && $info[0]->state == 0) //可见状态下删除，同步专栏期数
			{
				$dec = \DB::update("update t_pay_products set resource_count=resource_count-1 where app_id = ? and id = ?",
					[$this->app_id, $info[0]->product_id]);
			}

			//更新资源关系表中记录为删除状态
			if ($info[0]->payment_type != 1) {
				//检测在资源表中是否存在记录
				$is_exist = \DB::select("select * from t_pro_res_relation where app_id = ? and resource_type = 4 and resource_id = ? and relation_state=0", [$this->app_id, $id]);
				if (count($is_exist)) {
					$relation_at = \DB::table("t_pro_res_relation")
						->where('app_id', '=', $this->app_id)
						->where('resource_type', '=', 4)
						->where('resource_id', '=', $id)
						->update(['relation_state' => '1']);
				}
			}

			return response()->json(['ret' => 0]);
		} else {
			return response()->json(['ret' => 1]);
		}
	}

	//更新直播

	public function onSale ()
	{
		$id = Input::get("id");

		$info = \DB::select("select payment_type,product_id from t_alive where app_id = ? and id = ?", [$this->app_id, $id]);
		//先更新
		$update = \DB::update("update t_alive set state = 0 where app_id = ? and id = ?", [$this->app_id, $id]);

		if ($update >= 0) {
			//如果是专栏资源数要加一
			if ($info[0]->payment_type == 3) {
				$add = \DB::update("update t_pay_products set resource_count=resource_count+1 where app_id = ? and id = ?",
					[$this->app_id, $info[0]->product_id]);
			}

			return response()->json(['ret' => 0]);
		} else {
			return response()->json(['ret' => 1]);
		}
	}

	//压缩

	public function editAlive ()
	{
		$id          = Input::get("id");
		$alive       = \DB::table('t_alive')->where('app_id', '=', $this->app_id)->where('id', '=', $id)->first();
		$alive_roles = \DB::table("t_alive_role")->where('app_id', '=', $this->app_id)->where('alive_id', '=', $id)
			->where('state', '=', '0')->get();
		$packages    = \DB::select("select * from t_pay_products where app_id = ? and state<2 order by created_at", [$this->app_id]);

		$single_sale = 0;

		if ($alive->payment_type != 1)//若音频为单卖,则判断其是否为专栏外单卖
		{

			//TODO:在关系表t_pro_res_realtion中查找该resource_id的记录
			$is_exist = \DB::table('t_pro_res_relation')
				->where('resource_id', '=', $id)
				->where('app_id', '=', AppUtils::getAppID())
				->where('resource_type', '=', 4)
				->where('relation_state', '=', 0)
				->first();
			if (count($is_exist)) {
				if ($alive->payment_type == 2) {
					$single_sale = 1;
				}

				$alive->product_id   = $is_exist->product_id;
				$alive->product_name = $is_exist->product_name;
			}

		}

		//应用权限
		$appModuleInfo = AppUtils::getModuleInfo($this->app_id);

		// 分类数据
		if (!empty($appModuleInfo) && $appModuleInfo[0]->resource_category == 1) {
			// 分类数据信息
			$category_info = DB::connection('mysql')->table('t_category')
				->where('app_id', '=', $this->app_id)
				->where('id', '!=', '0')
				->orderby('weight', 'desc')
				->Lists('category_name', 'id');
			// 所属分类信息
			$package_category = DB::connection('mysql')->table('t_category_resource')
				->select('category_id')
				->where('app_id', '=', $this->app_id)
				->where('category_id', '!=', '0')
				->where('resource_id', '=', $id)
				->where('resource_type', '=', 4)
				->where('state', '=', 1)
				->pluck('category_id');
		}

		return View("admin.editAlive", compact('alive', 'alive_roles', 'packages', 'appModuleInfo', 'single_sale', 'category_info', 'package_category'));
	}

	//搜索主播

	public function updateAlive ()
	{
		$params         = Input::get("params");
		$roleParams     = Input::get("roleParams");
		$is_single_sale = Input::get('is_single_sale', -1);
		$category_type  = Input::get('category_type', '');

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

		$params['descrb'] = $this->sliceUE($params['descrb']);
		//如果改了视频,先置为1
		$editPackage = 0;
		if (array_key_exists("file_id", $params)) {
			$params['state']        = 1;
			$params['is_transcode'] = 0;
			$editPackage            = 1; //产品包操作参数
		}

		$old     = \DB::select("select * from t_alive where app_id = ? and id = ?", [$this->app_id, $params['id']]);
		$oldType = $old[0]->payment_type;
		$newType = $params['payment_type'];

		//如果修改了封面，异步重新压缩更新
		if (array_key_exists("img_url", $params)) {
			$this->imageDeal($params['img_url'], "t_alive", $params["id"]);//,160,120,60);
		}

		//资源关系表更新\新增
		$relation_at  = 0;
		$relation_add = 0;

		$old_is_single_sale = 0;
		$app_id             = $this->app_id;
		$resource_id        = $params['id'];

		//判断是否为专栏外单卖
		if ($oldType == 2) {
			$is_exist = \DB::select("select * from t_pro_res_relation where app_id = ? and resource_type = 4 and resource_id = ? and relation_state=0", [$app_id, $resource_id]);
			if (count($is_exist)) {
				$old_is_single_sale = 1;
			}
		}

		//单个变为产品包
		if ($oldType != 3 && $newType == 3) {
			//资源关系
			//$relation_add = 1;
			//TODO:检测该resource_id在资源关系表中是否存在记录,若有则更新专栏信息,若无则标志新增关系
			$is_exist = \DB::select("select * from t_pro_res_relation where app_id = ? and resource_type = 4 and resource_id = ?", [$app_id, $resource_id]);
			if (count($is_exist)) {
				$relation_at                = 1; //更新
				$relation['updated_at']     = Utils::getTime();
				$relation['relation_state'] = 0;
			} else {
				$relation_add           = 1;
				$relation['created_at'] = Utils::getTime();
				$relation['app_id']     = $app_id;
				//                $relation['product_id'] = $params['product_id'];
				//                $relation['resource_type'] = '4';
				//                $relation['product_name'] = $params['product_name'];
				$relation['resource_id'] = $resource_id;
			}

			$relation['product_id']    = $params['product_id'];
			$relation['resource_type'] = '4';
			$relation['product_name']  = $params['product_name'];

			if ($is_single_sale == 1)//该资源可以专栏外单卖
			{
				$params['payment_type'] = 2;
			}
		} //产品包变为单个
		else if (($oldType == 3 || $old_is_single_sale == 1) && $newType != 3) {
			$old_relation = \DB::select("select * from t_pro_res_relation where app_id = ? and product_id = ? and resource_type=4 and resource_id = ? and relation_state = 0",
				[$app_id, $old[0]->product_id, $resource_id]);
			if ($old_relation) {
				$relation_at = 2; //更新
				//                $relation['relation_state'] = 0;
				//                $relation['updated_at'] = Utils::getTime();

				//清除params中有关专栏的信息
				//                $params['product_name'] = "";
				//                $params['product_id'] = "";
			}
		} //产品包之间转换
		else if ($oldType == 3 && $newType == 3) {
			//+1
			//资源关系
			//            $relation_add = 1;
			//-1
			$old_relation = \DB::select("select * from t_pro_res_relation where app_id = ? and resource_type=4 and resource_id = ?",
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
			$relation['resource_type'] = '4';
			$relation['product_name']  = $params['product_name'];

			if ($is_single_sale == 1)//该资源可以专栏外单卖
			{
				$params['payment_type'] = 2;
			}
		}

		//旧的更新，新的插入
		$updateRole = \DB::update("update t_alive_role set state='1' where app_id=? and alive_id=?", [$this->app_id,
			$params['id']]);
		if ($roleParams) {
			foreach ($roleParams as $key => $value) {
				$value["app_id"]     = $this->app_id;
				$value["alive_id"]   = $params['id'];
				$value["state"]      = 0;
				$value["created_at"] = date('Y-m-d H:i:s');
				$insert              = \DB::table("t_alive_role")->insert($value);
			}
		}

		$update = \DB::table("t_alive")->where("app_id", "=", $this->app_id)
			->where("id", '=', $params['id'])->update($params);

		if ($update >= 0) {
			//如果改了视频,先置为1 ：自动改为下架
			if ($editPackage) {
				//如果是专栏资源数要减一
				if ($oldType == 3) {
					$dec = \DB::update("update t_pay_products set resource_count=resource_count-1 where app_id = ? and id = ?",
						[$this->app_id, $old[0]->product_id]);
					//重置state旧值
					if ($dec) {
						$old[0]->state = 1;
					}
				}

			}
			//如果修改涉及专栏,专栏表资源数要对应变化
			if ($old[0]->state == 0)//直播可见（上架）状态 则更新相关产品包计数
			{
				if ($oldType != 3 && $newType == 3) //单个变为产品包
				{
					$updateCount = \DB::update("update t_pay_products set resource_count=resource_count+1 
                where app_id = ? and id = ?", [$this->app_id, $params['product_id']]);
				} else if ($oldType == 3 && $newType != 3)//产品包变为单个
				{
					$updateCount = \DB::update("update t_pay_products set resource_count=resource_count-1 
                where app_id = ? and id = ?", [$this->app_id, $old[0]->product_id]);
				} else if ($oldType == 3 && $newType == 3)//产品包之间转换
				{
					//+1
					$updateCount = \DB::update("update t_pay_products set resource_count=resource_count+1 
                where app_id = ? and id = ?", [$this->app_id, $params['product_id']]);
					//-1
					$updateCount = \DB::update("update t_pay_products set resource_count=resource_count-1 
                where app_id = ? and id = ?", [$this->app_id, $old[0]->product_id]);
				}
			}

			//资源关系更新
			if ($relation_at == 1) {
				$relation_at = \DB::table('t_pro_res_relation')
					->where('app_id', '=', $app_id)
					->where('resource_type', '=', 4)
					->where('resource_id', '=', $resource_id)
					//                    ->where('relation_state','=','0')
					->update($relation);
			}
			//删除关系
			if ($relation_at == 2) {
				//解除旧的资源关系 一对多不指定专栏//->where('product_id','=',$old[0]->product_id)
				$relation_at = \DB::table("t_pro_res_relation")
					->where('app_id', '=', $app_id)
					->where('resource_type', '=', 4)
					->where('resource_id', '=', $resource_id)
					->update(['relation_state' => '1']);
			}
			if ($relation_add) {
				$relation_time = Utils::getTime();
				/*//$package = array('$params[product_id]');
				if(count(package)){foreach($package as $product_id){}}
				 */
				$relation_add = \DB::connection('mysql')->insert("insert into t_pro_res_relation SET 
app_id = '$this->app_id',product_name='$params[product_name]', product_id = '$params[product_id]', resource_type = '4', resource_id = '$params[id]', created_at = '$relation_time'
on duplicate key 
update relation_state = '0' ,updated_at = '$relation_time'
");
			}
		}

		$result_insert = true;
		// 获得分类数据$resource_id
		$category_info     = DB::connection('mysql')->table('t_category')
			->where('app_id', '=', $this->app_id)
			->where('id', '!=', '0')
			->pluck('id');
		$category_resource = DB::connection('mysql')->table('t_category_resource')
			->select('category_id')
			->where('app_id', '=', $this->app_id)
			->where('resource_id', '=', $resource_id)
			->where('resource_type', '=', 4)
			->whereIn('category_id', $category_info)
			->get();

		if (count($category_resource) == 0) {
			foreach ($category_info as $value) {
				$insert = DB::connection('mysql')->table('t_category_resource')->insert(
					['app_id' => $this->app_id, 'category_id' => $value, 'resource_id' => $resource_id, 'resource_type' => 4, 'state' => 0, 'created_at' => date('Y-m-d H:i:s', time())]
				);
				if ($insert == false) $result_insert = false;
			}
		}

		// 首先 重置所有分类（四个）的状态为0
		$now                  = Utils::getTime();
		$result_app_categorys = DB::connection('mysql')->table('t_category_resource')
			->where('app_id', '=', $this->app_id)
			->where('resource_id', '=', $resource_id)
			->whereIn('category_id', $category_info)
			->where('resource_type', '=', 4)
			->update(['state' => 0, 'updated_at' => $now]);
		//        dd($category_type);
		if (!empty($category_type)) {
			// 更新所有提交的分类数据
			$result_app_category_avalite = DB::connection('mysql')->table('t_category_resource')
				->where('app_id', '=', $this->app_id)
				->where('resource_id', '=', $resource_id)
				->whereIn('category_id', $category_type)
				->where('resource_type', '=', 4)
				->update(['state' => 1]);
		}

		// 如果没有首页分类的数据 也插入一条
		$home_page_category = DB::connection('mysql')->table('t_category_resource')
			->where('app_id', '=', $this->app_id)
			->where('resource_id', '=', $resource_id)
			->where('category_id', '=', '0')
			->where('resource_type', '=', 4)
			->first();
		if (empty($home_page_category)) {
			// 同时插入一条首页分类数据  默认显示
			$insert = DB::connection('mysql')->table('t_category_resource')->insert(
				['app_id' => $this->app_id, 'category_id' => '0', 'resource_id' => $resource_id, 'resource_type' => 4, 'state' => 1, 'created_at' => Utils::getTime()]
			);
			if ($insert == false) $result_insert = false;
		}

		if ($result_insert) {
			return response()->json(['ret' => 0]);
		} else {
			return response()->json(['ret' => 1]);
		}
	}

	//结束直播

	public function zbSearch ()
	{
		/**
		 * 原始代码
		 */
		//        $search=Input::get("search");
		//        $whereRaw = "";
		//        if(!Utils::isEmptyString('search')){
		//            $whereRaw .= " app_id = '".AppUtils::getAppID()."'and (wx_nickname like '%".$search."%'"." or phone like '%".$search."%')";
		//        }
		////        $result=\DB::select("select * from t_users where app_id = ? and (wx_nickname regexp ? or phone regexp ?)
		////        order by created_at desc limit 30",[$this->app_id,$search,$search]);
		//        $result = \DB::table("db_ex_business.t_users")
		////            ->where("app_id,",'=',$this->app_id)
		//            ->whereRaw($whereRaw)
		////            ->orderBy("created_at",'desc')
		//            ->take(30)
		//            ->get();
		//        $data=[];
		//        if($result)
		//        {
		//            foreach ($result as $key => $value)
		//            {
		//                $data[$key]['user_id']=$value->user_id;
		//                $data[$key]['wx_avatar']=$value->wx_avatar;
		//                $data[$key]['wx_nickname']=$value->wx_nickname;
		//                $data[$key]['wx_gender']=empty($value->wx_gender)?'无':($value->wx_gender==1?'男':'女');
		//                $data[$key]['phone']=empty($value->phone)?'无':$value->phone;
		//            }
		//        }
		//        return response()->json(['data'=>$data]);
		/**
		 * 原生分页查找
		 */
		//        $allInfo=\DB::table('t_users')
		//            ->where('app_id',AppUtils::getAppID())
		//            ->where(function ($query) use ($search){
		//                $query->where("wx_nickname",'like',"%".$search."%")
		//                ->orwhere('phone','like',"%".$search."%");
		//            })
		//            ->paginate(5);
		//        $data=[];
		//        if($allInfo){
		//            foreach ($allInfo as $key=>$value){
		//                $data[$key]['user_id']=$value->user_id;
		//                $data[$key]['wx_avatar']=$value->wx_avatar;
		//                $data[$key]['wx_nickname']=$value->wx_nickname;
		//                $data[$key]['wx_gender']=empty($value->wx_gender)?'无':($value->wx_gender==1?'男':'女');
		//                $data[$key]['phone']=empty($value->phone)?'无':$value->phone;
		//            }
		//        }
		//        return response()->json(['data'=>$data,'allInfo'=>$allInfo]);
		/**
		 * 非原生分页查找
		 * 参数：1、search
		 *      2、page
		 *      2、pageSize
		 * author: Kris
		 */
		$search      = Input::get('search', '');
		$page_index  = Input::get('page', '1');
		$page_size   = Input::get('pageSize', '10');
		$total_count = \DB::table('t_users')
			->where('app_id', AppUtils::getAppID())
			->where(function($query) use ($search) {
				$query->where('wx_nickname', 'like', "%" . $search . "%")
					->orwhere('phone', 'like', "%" . $search . "%");
			})
			->count();
		$total_page  = ceil($total_count / $page_size);

		$search_result = \DB::table('t_users')
			->where('app_id', AppUtils::getAppID())
			->where(function($query) use ($search) {
				$query->where('wx_nickname', 'like', "%" . $search . "%")
					->orwhere('phone', 'like', "%" . $search . "%");
			})
			->skip(($page_index - 1) * $page_size)
			->take($page_size)
			->get();
		$data          = [];
		if ($search_result) {
			foreach ($search_result as $key => $value) {
				$data[ $key ]['user_id']     = $value->user_id;
				$data[ $key ]['wx_avatar']   = $value->wx_avatar;
				$data[ $key ]['wx_nickname'] = $value->wx_nickname;
				$data[ $key ]['wx_gender']   = empty($value->wx_gender) ? '无' : ($value->wx_gender == 1 ? '男' : '女');
				$data[ $key ]['phone']       = empty($value->phone) ? '无' : $value->phone;
			}
		}
		$page_offset = [
			'total_pages'  => $total_page,
			'total_count'  => $total_count,
			'current_page' => $page_index,
			'page_size'    => $page_size,
		];

		return ['data' => $data, 'page_offset' => $page_offset];
	}

	//给讲师发消息

	public function endAlive ()
	{
		$id     = Input::get("id");
		$update = \DB::update("update t_alive set manual_stop_at = ? where app_id = ? and id = ?",
			[Utils::getTime(), $this->app_id, $id]);
		if ($update >= 0) {
			return response()->json(['ret' => 0]);
		} else {
			return response()->json(['ret' => 1]);
		}
	}

	//查看直播评论

	public function sendMessage ($params)
	{
		$userIds   = explode('|', $params['zb_user_id']);
		$userNames = explode(',', $params['zb_user_name']);
		for ($i = 0; $i < count($userIds); $i++) {
			$msg                   = [];
			$msg['app_id']         = $this->app_id;
			$msg['type']           = 0;
			$msg['user_id']        = $userIds[ $i ];
			$msg['send_nick_name'] = '系统管理员';
			$msg['source']         = 0;
			$msg['skip_type']      = 0;
			$msg['content']        = "您已成为" . $params['zb_start_at'] . "开始的" . $params["title"] . "的讲师。
            本次课程直播会在" . $params['zb_stop_at'] . "结束。如需提前结束直播，可在页面的“···”内点击“结束”按钮来结束直播。" .
				"直播结束后课程变为可回放状态。如课程计划有变动，请联系管理员在PC端管理台修改课程计划。";
			$msg['send_at']        = Utils::getTime();
			$msg['state']          = 0;
			$msg['created_at']     = Utils::getTime();
		}
	}

	//改变评论状态

	public function aliveComment (Request $request)
	{
		$ruler   = Input::get("ruler", "");
		$search  = Input::get("search", "");
		$aliveId = Input::get("alive_id", '');
		$reurl   = Input::get('reurl');

		$allInfo = DB::table("t_alive_interact as v1")
			->select('v1.*', 'v2.wx_nickname', 'v2.wx_avatar')
			->leftjoin('t_users as v2', function($join) use ($ruler, $search) {
				$join->on('v1.app_id', '=', 'v2.app_id')
					->on('v1.user_id', '=', 'v2.user_id')
					->where(function($query) use ($ruler, $search) {
						if ($ruler == 2) {
							return $query->where('v2.wx_nickname', 'like', "%{$search}%");
						}
					});
			})
			->where("v2.app_id", $this->app_id)
			->where("v1.app_id", $this->app_id)
			->where("alive_id", '=', $aliveId)
			->where(function($query) use ($ruler, $search) {
				if ($ruler == 0) {
					return $query->where('org_msg_content', 'like', "%{$search}%");
				}
			})
			->where(function($query) use ($ruler, $search) {
				if ($ruler == 1) {
					return $query->where('v1.created_at', 'like', "%{$search}%");
				}
			})
			->paginate(10);

		//有搜索集下一步，没有就为空数组
		$data = [];
		if ($allInfo) {
			foreach ($allInfo as $key => $v) {
				$data[ $key ]['id'] = $v->id;

				//用户信息
				$data[ $key ]['wx_avatar']   = empty($v->wx_avatar) ? '../images/default.png' : $v->wx_avatar;
				$data[ $key ]['wx_nickname'] = empty($v->wx_nickname) ? '无' : $v->wx_nickname;
				$data[ $key ]['user_type']   = $v->user_type == 0 ? '听众' : '讲师';
				$data[ $key ]['user_id']     = $v->user_id;

				//消息内容
				if ($v->content_type == 0) {
					$data[ $key ]['msg_content'] = urldecode($v->msg_content);
				} else if ($v->content_type == 1) {
					$data[ $key ]['msg_content'] = '【表情】';
				} else if ($v->content_type == 2) {
					$data[ $key ]['msg_content'] = '【图片】';
				} else {
					$data[ $key ]['msg_content'] = '【语音】';
				}

				$data[ $key ]['msg_state']  = $v->msg_state;
				$data[ $key ]['created_at'] = $v->created_at;
			}
		}

		return View('admin.aliveComment', compact('data', 'search', 'ruler', 'aliveId', 'allInfo', 'reurl'));

	}

	//分隔文本编辑器的内容

	public function changeAliveComment ()
	{
		$aliveId     = Input::get("alive_id");
		$id          = Input::get("id");
		$targetState = Input::get("targetState");
		if ($targetState == 0) //要显示
		{
			$update = \DB::update("update t_alive_interact set msg_state ='0' where app_id = ? and alive_id = ?
            and id = ?", [$this->app_id, $aliveId, $id]);
			//对应资源评论数加一
			$updateCount = \DB::update("update t_alive set comment_count=comment_count+1 where app_id = ?
            and id= ?", [$this->app_id, $aliveId]);
			if ($updateCount >= 0) {
				return response()->json(['ret' => 0]);
			} else {
				return response()->json(['ret' => 1]);
			}
		} else //要隐藏
		{
			$update = \DB::update("update t_alive_interact set msg_state ='1' where app_id = ? and alive_id = ?
            and id = ?", [$this->app_id, $aliveId, $id]);
			//对应资源评论数减一
			$updateCount = \DB::update("update t_alive set comment_count=comment_count-1 where app_id = ?
            and id= ?", [$this->app_id, $aliveId]);
			if ($updateCount >= 0) {
				return response()->json(['ret' => 0]);
			} else {
				return response()->json(['ret' => 1]);
			}
		}

	}

	/**
	 * 异步导出音频，有三个地方可以导出 （单品列表，专栏详情，会员详情）；
	 * @return array
	 */
	public function asyncDownloadAliveVoice ()
	{
		$app_id    = $this->app_id;
		$alive_id  = Input::get('alive_id');
		$aliveInfo = DB::table('t_alive')
			->where('app_id', '=', $app_id)
			->where('id', '=', $alive_id)
			->first();
		if (!$aliveInfo) return ['code' => 4096, 'msg' => 'the alive not exists'];

		if ($aliveInfo->alive_type != 0 && $aliveInfo->alive_type != 1 && $aliveInfo->alive_type != 3) {
			return ['code' => 5, 'msg' => '这个直播没有语音'];
		}

		if (!$aliveInfo->manual_stop_at) {
			if ($aliveInfo->zb_stop_at > Utils::getTime())
				return ['code' => 6, 'msg' => '这个直播还没结束啊'];
		}

		$teacherVoice = DB::select("select * from t_alive_interact 
        where app_id=? and alive_id=? and content_type =3 and user_type=1 order by created_at asc", [$app_id, $alive_id]);

		if (!$teacherVoice) return ['code' => 4, 'msg' => '该直播没有音频啊~~~~~~'];

		if (($aliveInfo->complete_state == 0) && (!empty($aliveInfo->complete_voice_url)))
			return ['code' => 0, 'msg' => 'ok', 'data' => ['url' => $aliveInfo->complete_voice_url]];

		if ($aliveInfo->complete_state == 1)
			return ['code' => 1, 'msg' => '语音文件正在处理中'];

		if ($aliveInfo->complete_state == 2) {  // 异步处理
			Utils::asyncThread(env('HOST_URL') . '/downloadAliveVoice?app_id=' . $app_id . '&alive_id=' . $alive_id);

			return ['code' => 2, 'msg' => '已经开始处理'];
		}

		if ($aliveInfo->complete_state == 3)   // 异步处理出错
			return ['code' => 3, 'msg' => '处理出错或者处理超时'];

	}

	//获取已添加的嘉宾列表
	public function getAddedGuestList (Request $request)
	{
		$this->validate($request, [
			'id' => 'required',
		]);
		$id         = $request->get('id');
		$alive_role = DB::table('t_alive_role')
			->select('t_alive_role.alive_id', 't_alive_role.role_name', 't_alive_role.user_id', 't_users.wx_avatar', 't_users.wx_nickname as user_name')
			->join('t_users', 't_alive_role.user_id', '=', 't_users.user_id')
			->where('t_alive_role.app_id', '=', $this->app_id)
			->where('t_alive_role.alive_id', '=', $id)
			->where('t_alive_role.state', '=', 0)
			->get();

		return ['code' => 0, 'msg' => 'ok', 'data' => $alive_role];
	}

	//获取所有的嘉宾列表
	public function getAllGuestList (Request $request)
	{
		$this->validate($request, [
			'alive_id' => 'required',
		]);
		$alive_id = $request->get('alive_id');

		$search      = Input::get('search', '');
		$page_index  = Input::get('page', '1');
		$page_size   = Input::get('pageSize', '10');
		$total_count = \DB::table('t_users')
			->where('app_id', $this->app_id)
			->where(function($query) use ($search) {
				$query->where('wx_nickname', 'like', "%" . $search . "%")
					->orwhere('phone', 'like', "%" . $search . "%");
			})
			->count();
		$total_page  = ceil($total_count / $page_size);

		$search_result = \DB::table('t_users')
			->select('user_id', 'wx_avatar', 'wx_nickname', 'wx_gender', 'phone')
			->where('app_id', $this->app_id)
			->where(function($query) use ($search) {
				$query->where('wx_nickname', 'like', "%" . $search . "%")
					->orwhere('phone', 'like', "%" . $search . "%");
			})
			->skip(($page_index - 1) * $page_size)
			->take($page_size)
			->get();
		$data          = [];
		if ($search_result) {
			foreach ($search_result as $key => $value) {
				$data[ $key ]['user_id']     = $value->user_id;
				$data[ $key ]['wx_avatar']   = $value->wx_avatar;
				$data[ $key ]['wx_nickname'] = $value->wx_nickname;
				$data[ $key ]['wx_gender']   = empty($value->wx_gender) ? '无' : ($value->wx_gender == 1 ? '男' : '女');
				$data[ $key ]['phone']       = empty($value->phone) ? '无' : $value->phone;
				$state                       = DB::table('t_alive_role')
					->where('app_id', '=', $this->app_id)
					->where('alive_id', '=', $alive_id)
					->where('user_id', '=', $value->user_id)
					->where('state', '=', 0)
					->first();
				if ($state) $data[ $key ]['state'] = 0;
				else $data[ $key ]['state'] = 1;
			}
		}
		$page_offset = [
			'total_pages'  => $total_page,
			'total_count'  => $total_count,
			'current_page' => $page_index,
			'page_size'    => $page_size,
		];

		return ['code' => 0, 'msg' => 'ok', 'data' => $data, 'page_offset' => $page_offset];
	}

	//添加嘉宾
	public function addAliveGuest ()
	{

		$params = Input::get('params', []);

		//        $params[0]['alive_id']  = 'ehjqwlkrjlrqw33';
		//        $params[0]['role_name'] = '2';
		//        $params[0]['user_name'] = '333';
		//        $params[0]['user_id']   = 'u_weqwe_ewqeeeeeeqwda';
		//        $params[1]['alive_id']  = 'ehjqwlkrjlrqw33';t_audio
		//        $params[1]['role_name'] = '2111';
		//        $params[1]['user_name'] = '333111';
		//        $params[1]['user_id']   = 'u_weqwe_ewqeeeeeeqwda111';

		if (!count($params) > 0)
			return ['code' => 0, 'msg' => 'no change'];

		$alive_id = $params[0]['alive_id'];

		DB::beginTransaction();
		$com_data['app_id']     = $this->app_id;
		$com_data['state']      = 0;
		$com_data['created_at'] = Utils::getTime();
		$com_data['updated_at'] = Utils::getTime();

		$insert = [];
		foreach ($params as $k => $v) {
			$old = DB::table('t_alive_role')
				->where('app_id', '=', $this->app_id)
				->where('alive_id', '=', $alive_id)
				->where('user_id', '=', $v['user_id'])
				->first();
			if ($old) {
				$res = DB::table('t_alive_role')
					->where('app_id', '=', $this->app_id)
					->where('alive_id', '=', $alive_id)
					->where('user_id', '=', $v['user_id'])
					->update(['state' => 0, 'updated_at' => $com_data['updated_at']]);
				if (!$res) {
					DB::rollBack();

					return ['code' => 1064, 'msg' => 'db error update'];
				}
			} else {
				$insert[] = array_merge($v, $com_data);
			}
		}

		if (count($insert) > 0) {
			$res = DB::table('t_alive_role')
				->insert($insert);
			if (!$res) {
				DB::rollBack();

				return ['code' => 1064, 'msg' => 'db error insert'];
			}
		}

		DB::commit();

		return ['code' => 0, 'msg' => 'ok'];
	}

	//移除嘉宾
	public function deleteAliveGuest (Request $request)
	{
		$this->validate($request, [
			'alive_id' => 'required',
			'user_id'  => 'required',
		]);

		$alive_id = $request->get('alive_id');
		$user_id  = $request->get('user_id');

		$res = DB::table('t_alive_role')
			->where('app_id', '=', $this->app_id)
			->where('alive_id', '=', $alive_id)
			->where('user_id', '=', $user_id)
			->update(['state' => 1, 'updated_at' => Utils::getTime()]);

		if (!$res) return ['code' => 1064, 'msg' => 'db error'];

		return ['code' => 0, 'msg' => 'ok'];
	}

	//生成邀请嘉宾链接
	public function inviteGuestUrl (Request $request)
	{

		$this->validate($request, [
			'alive_id' => 'required',
		]);

		$alive_id = $request->get('alive_id');

		$res = \DB::table('t_alive')
			->where('app_id', '=', $this->app_id)
			->where('id', '=', $alive_id)
			->first();

		// 邀请讲师start
		$tempParam             = [];
		$tempParam['title']    = $res->title;
		$tempParam['alive_id'] = $alive_id;
		//        $user_info = self::getAdminUserByAppId($this->app_id);
		//        $tempParam['user_id'] = $user_info->user_id;
		//        $tempParam['wx_nickname'] = $user_info->wx_nickname;
		$tempParam['inviteImg'] = $res->img_url_compressed;

		$url_long = 'https://' . env('DOMAIN_DUAN_NAME') . '/' . $this->app_id . '/teacherInvitationPre/' . Utils::urlSafe_b64encode(json_encode($tempParam));
		$url      = Utils::getShortUrlByLongUrl($url_long);

		return ['code' => 0, 'msg' => 'ok', 'url' => $url, 'url_long' => $url_long];

		// 邀请讲师end

		//        $data['app_id'] = $this->app_id;
		//        $data['id'] = Utils::getUniId('dr_');
		//        $data['related_id'] = $alive_id;
		//        $data['record_type'] = 0;
		//        $data['expire_at'] = Utils::getTime(7200);
		//        $data['created_at'] = Utils::getTime();
		//        $data['updated_at'] = Utils::getTime();
		//
		//        $res = DB::table('t_disposable_record')
		//                ->insert($data);
		//        if(!$res) return ['code' => 1 , 'msg' => '一次性记录生成失败'];

		//        $url_params['user_id'] = self::getAdminUserIdByAppId($this->app_id);
		//        $url_params['id'] = $data['id'];
		//        $url_params['alive_id'] = $alive_id;

	}

	/**
	 * @param Request $request =>  $alive_id   $params
	 *                         更新该直播的讲师  先将已有的置1  然后插入新的数据 有则update无则insert
	 *
	 * @return array code => 0 操作成功
	 */

	public function saveAddedGuest (Request $request)
	{
		$this->validate($request, [
			'alive_id' => 'required',
		]);
		$params   = Input::get('params', []);
		$alive_id = Input::get('alive_id', '');

		//        $params[0]['alive_id']  = 'ehjqwlkrjlrqw33';
		//        $params[0]['role_name'] = '2';
		//        $params[0]['user_name'] = '333';
		//        $params[0]['user_id']   = 'u_weqwe_ewqeeeeeeqwda';

		if (!count($params) > 0) {
			DB::update("update t_alive_role set state='1' where app_id=? and alive_id=?", [$this->app_id, $alive_id]);

			return ['code' => 0, 'msg' => 'no change'];
		}

		$alive_id = $params[0]['alive_id'];
		DB::beginTransaction();

		DB::update("update t_alive_role set state='1' where app_id=? and alive_id=?", [$this->app_id, $alive_id]);

		$com_data['app_id']     = $this->app_id;
		$com_data['state']      = 0;
		$com_data['created_at'] = Utils::getTime();
		$com_data['updated_at'] = Utils::getTime();

		$insert = [];
		foreach ($params as $k => $v) {
			$old = DB::table('t_alive_role')
				->where('app_id', '=', $this->app_id)
				->where('alive_id', '=', $alive_id)
				->where('user_id', '=', $v['user_id'])
				->first();
			if ($old) {

				$res = DB::update("update t_alive_role set state='0',role_name=?,updated_at=? where app_id=? and alive_id=? and user_id=? limit 1", [$v['role_name'], Utils::getTime(), $this->app_id, $alive_id, $v['user_id']]);

				//                $res = DB::table('t_alive_role')
				//                    ->where('app_id','=',$this->app_id)
				//                    ->where('alive_id','=',$alive_id)
				//                    ->where('user_id','=',$v['user_id'])
				//                    ->take(1)
				//                    ->update(['state1' => 0 , 'role_name' => $v['role_name'] , 'updated_at' => Utils::getTime()]);
				if (!$res > 0) {
					DB::rollBack();

					return ['code' => 1064, 'msg' => 'db error update', 'res' => $res, 'user_id' => $v['user_id']];
				}
			} else {
				$insert[] = array_merge($v, $com_data);
			}
		}

		if (count($insert) > 0) {
			$res = DB::table('t_alive_role')
				->insert($insert);
			if (!$res) {
				DB::rollBack();

				return ['code' => 1064, 'msg' => 'db error insert'];
			}
		}

		DB::commit();

		return ['code' => 0, 'msg' => 'ok'];
	}

	public function neo_alive_info ()
	{

		$app_id   = Input::get('app_id');
		$alive_id = Input::get('alive_id');
		$type     = Input::get('type');

		if (Utils::isEmptyString($app_id) || Utils::isEmptyString($alive_id))
			return ['code' => 1, 'msg' => 'params error'];

		$alive_info_old = \DB::table('t_alive')
			->where('app_id', '=', $app_id)
			->where('id', '=', $alive_id)
			->first();

		if (!$alive_info_old) return ['code' => 2, 'msg' => '没有这个直播'];

		if ($type === '0')
			$update_data = ['push_state' => 2, 'updated_at' => Utils::getTime()];
		else if ($type === '1')
			$update_data = ['list_file_content' => '', 'updated_at' => Utils::getTime()];
		else
			return ['code' => 0, 'msg' => 'no type', 'data' => $alive_info_old];

		$res = \DB::table('t_alive')
			->where('app_id', '=', $app_id)
			->where('id', '=', $alive_id)
			->update($update_data);

		if (!$res) return ['code' => 1064, 'msg' => 'db error', 'data' => ['old_data' => $alive_info_old]];

		$alive_info_new = \DB::table('t_alive')
			->where('app_id', '=', $app_id)
			->where('id', '=', $alive_id)
			->first();

		return ['code' => 0, 'msg' => 'ok', 'data' => ['old_data' => $alive_info_old, 'new_data' => $alive_info_new]];
	}

	public function neo_has_stream_alive ()
	{

		$app_id = Input::get('app_id');
		$type   = Input::get('type');

		if (Utils::isEmptyString($app_id) || Utils::isEmptyString($type))
			return ['code' => 1, 'msg' => 'params error'];

		if (($type == 0) || ($type == 1))
			$update_data = ['has_stream_alive' => $type, 'updated_at' => Utils::getTime()];
		else
			return ['code' => 2, 'msg' => 'type is not 0 or 1'];

		$res = DB::table('db_ex_config.t_app_module')
			->where('app_id', '=', $app_id)
			->update($update_data);

		if (!$res) return ['code' => 1064, 'msg' => 'db error'];

		return ['code' => 0, 'msg' => 'ok'];
	}

	public function clear_manual_stop_at ()
	{
		$app_id   = Input::get('app_id');
		$alive_id = Input::get('alive_id');

		if (Utils::isEmptyString($app_id) || Utils::isEmptyString($alive_id))
			return ['code' => 1, 'msg' => 'params error'];

		$res = DB::table('t_alive')
			->where('app_id', '=', $app_id)
			->where('id', '=', $alive_id)
			->update(['manual_stop_at' => null, 'updated_at' => Utils::getTime()]);
		if (!$res) return ['code' => 1064, 'msg' => 'db error'];

		return ['code' => 0, 'msg' => 'ok'];
	}

	private function getAdminUserByAppId ($app_id)
	{
		$merchant_id = \DB::table('db_ex_config.t_app_conf')
			->where('app_id', '=', $app_id)
			->where('wx_app_type', '=', 0)
			->value('merchant_id');

		$union_id = \DB::table('db_ex_config.t_mgr_login')
			->where('merchant_id', '=', $merchant_id)
			->value('union_id');

		$user_info = \DB::table('t_users')
			->select('wx_nickname', 'user_id')
			->where('app_id', '=', $app_id)
			->where('universal_union_id', '=', $union_id)
			->first();

		return $user_info;
	}

}








