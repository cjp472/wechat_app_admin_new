<?php

namespace App\Http\Controllers\ResManage;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\MessagePush;
use App\Http\Controllers\Tools\StringConstants;
use App\Http\Controllers\Tools\Utils;
use DB;
use Illuminate\Support\Facades\Input;

class GoodsManageController extends Controller
{

	static public function audioPlayInfo ($res_id)
	{
		$app_id = AppUtils::getAppID();

		//查询音频列表中制定app_id的且没有删除的所有记录
		$audio = \DB::table('t_audio')
			->where('app_id', '=', $app_id)
			->where('id', '=', $res_id)
			->where('audio_state', '!=', 2)
			->first();
		//
		//查询音频播放统计数据
		$audio_count = \DB::select("select sum(play_count) as playSum, sum(finish_count) as finishSum from t_audio_analyse
        where app_id='$app_id' and audio_id='$res_id' ");//dump($audio_count);
		//评论数 $audio->comment_count

		if ($audio_count && $audio) {

			//播放量 audio->playcount
			$audio->playcount = $audio_count[0]->playSum ? $audio_count[0]->playSum : 0;

			//完成量 $audio->finishcount
			$audio->finishcount = $audio_count[0]->finishSum ? $audio_count[0]->finishSum : 0;

			//完播率 $audio->finishpercent
			$audio->finishpercent = $audio_count[0]->finishSum && $audio_count[0]->playSum ? round(($audio_count[0]->finishSum / $audio_count[0]->playSum), 4) * 100 : '0.00';

			//分享量 $audio->share_count

			//试听数 $audio->try_sign_count

			//日签点击量 $audio->click_sign_count

			//                    if ($audio_count) {} else {}
			//                    $audio->playcount = 26543543530;
			//                    $audio->finishcount = 352543253250;
			//                    $audio->finishpercent = 100.00;
		}

		return $audio;
	}

	/**上移、下移专栏;
	 *参数:
	 *1-package_id;
	 *2-order_type(0-上移,1-下移)
	 **/
	public function changePackageWeight ()
	{
		$resource_id = Input::get('package_id');
		$weightOrder = Input::get('order_type', -1);

		$resourceInfo = $this->getPackageInfo($resource_id);
		//插入当前时间
		$data['updated_at'] = Utils::getTime();
		if ($weightOrder == 0) {  //加权往前
			//专栏上移
			$msg    = "专栏上移";
			$result = $this->packageMove($resourceInfo, StringConstants::PACKAGE_MOVE_UP);//上移
			if ($result == 0) {
				$msg = "当前位置为最顶部,上移";
			}
		} else {  //减权退后
			//专栏下移
			$msg    = "专栏下移";
			$result = $this->packageMove($resourceInfo, StringConstants::PACKAGE_MOVE_DOWN);//下移
			if ($result == 0) {
				$msg = "当前位置为最低部,下移";
			}
		}

		if ($result) {
			return $this->result($result);
		} else {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, $msg . "失败!"));

		}
	}

	private function getPackageInfo ($package_id)
	{
		$package = \DB::table('db_ex_business.t_pay_products')
			->where('id', '=', $package_id)
			->where('app_id', '=', AppUtils::getAppID())
			->first();

		return $package;
	}

	private function packageMove ($resourceInfo, $order_type)
	{
		$app_id   = AppUtils::getAppID();
		$whereRaw = '1=1';
		$whereRaw .= " and is_member = " . $resourceInfo->is_member;
		if ($order_type == StringConstants::PACKAGE_MOVE_UP) {//上移
			$whereRaw    .= " and order_weight > " . $resourceInfo->order_weight;
			$order_value = 'asc';
		} else if ($order_type == StringConstants::PACKAGE_MOVE_DOWN) {//下移
			$whereRaw    .= " and order_weight < " . $resourceInfo->order_weight;
			$order_value = 'desc';
		} else {
			return -1;
		}

		$resourceNext = \DB::table('db_ex_business.t_pay_products')
			->where('app_id', '=', $app_id)
			//            ->where('state', '=', 0)
			->whereRaw($whereRaw)
			->orderBy('order_weight', $order_value)
			->first();
		if ($resourceNext) {
			$data['order_weight'] = $resourceInfo->order_weight;
			$data['updated_at']   = Utils::getTime();
			$resulto              = \DB::table('db_ex_business.t_pay_products')
				->where('id', '=', $resourceNext->id)
				->where('app_id', '=', $app_id)
				->update($data);
			if ($resulto >= 0 || $resulto) {
				$result = \DB::table('db_ex_business.t_pay_products')
					->where('id', '=', $resourceInfo->id)
					->where('app_id', '=', $app_id)
					->update(['order_weight' => $resourceNext->order_weight, 'updated_at' => Utils::getTime()]);
			} else {
				$result = -1;
			}
		} else {
			$result = 0;
		}

		return $result;
	}

	private function result ($data)
	{
		if (Utils::isEmptyString($data)) {
			return response()->json(Utils::pack("", StringConstants::Code_Failed, StringConstants::Msg_Failed));
		} else {
			return response()->json(Utils::pack($data));
		}
	}

	/**上架、下架;
	 *参数:
	 *1-goods_id;
	 *2-goods_type(0-专栏,1-图文,2-音频,3-视频,4-直播);
	 *3-operate_type(0-上架,1-下架)
	 **/
	public function changeGoodsState ()
	{
		$goods_id     = Input::get("goods_id", '');
		$goods_type   = Input::get("goods_type", -1);
		$operate_type = Input::get("operate_type", -1);

		if ($goods_type > 4 || $goods_type < 0) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "商品类型有误!"));
		}
		if ($operate_type > 1 || $operate_type < 0) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "操作类型有误!"));
		}

		switch ($goods_type) {
			case 0://专栏
				$table_name      = 'db_ex_business.t_pay_products';
				$params['state'] = $operate_type;
				break;
			case 1://图文
				$table_name              = 'db_ex_business.t_image_text';
				$params['display_state'] = $operate_type;
				break;
			case 2://音频
				$table_name            = 'db_ex_business.t_audio';
				$params['audio_state'] = $operate_type;
				break;
			case 3://视频
				$table_name            = 'db_ex_business.t_video';
				$params['video_state'] = $operate_type;
				break;
			case 4://直播
				$table_name      = 'db_ex_business.t_alive';
				$params['state'] = $operate_type;
				break;
		}

		if ($goods_type == StringConstants::SINGLE_GOODS_VIDEO || $goods_type == StringConstants::SINGLE_GOODS_ALIVE) {

			//获取商品信息
			$goods_info = Utils::getResourceInfo($goods_id, $goods_type);
			if ($goods_info) {
				if ($goods_info->is_transcode == 0) {
					if ($goods_type == StringConstants::SINGLE_GOODS_VIDEO || ($goods_type == StringConstants::SINGLE_GOODS_ALIVE && $goods_info->alive_type == 1))
						return response()->json(Utils::pack("0", StringConstants::Code_Failed, "转码中,请稍后再试!"));
				} else if ($goods_info->is_transcode == 2) {
					return response()->json(Utils::pack("0", StringConstants::Code_Failed, "当前资源转码失败,请重新上传!"));
				}
			}
		}

		$params['updated_at'] = Utils::getTime();

		//更新状态
		$result = \DB::table($table_name)
			->where("app_id", '=', AppUtils::getAppID())
			->where("id", '=', $goods_id)
			->update($params);

		if ($result) {
			return $this->result($result);
		} else {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "操作失败!"));
		}

	}

	/**移除;
	 *参数:
	 *1-goods_id;
	 *2-goods_type(1-图文,2-音频,3-视频,4-直播,5-活动,6-专栏);
	 *3-channel_type(0-单品,1-专栏内资源， 2-会员内资源);
	 *4-package_id(当channel_type= 1 或2 时有值)
	 **/
	public function moveGoods ()
	{

		$goods_id     = Input::get("goods_id", '');
		$goods_type   = Input::get("goods_type", '');
		$channel_type = Input::get("channel_type", '');
		if ($channel_type == StringConstants::RESOURCE_CHANNEL_PACKAGE || $channel_type == StringConstants::RESOURCE_CHANNEL_MEMBER) { //专栏内移除 或 会员内移除
			$package_id = Input::get("package_id", '');
			//处理步骤:
			//1.判断该商品goods_id在其他专栏中是否存在。
			//若不存在,则判断该商品是否是在专栏外单外的,即在资源表中payment_type==2
			//若是单卖的,则直接将关系表中的该记录置位为删除,并且专栏资源数减一
			//若不是单卖的,则直接将关系表中的该记录置位为删除并且将该资源标记位删除,并且专栏资源数减一
			//若存在其他的专栏内,则直接将关系表中的该记录置位为删除,并且专栏资源数减一
			$is_exist = $this->is_exist_in_other_package($goods_id, $package_id, $goods_type, $channel_type);
			if ($is_exist || $goods_type == StringConstants::SINGLE_GOODS_PACKAGE) {//存在或资源类型为专栏
				//将关系表中的该记录置位为删除
				$set_state_result = $this->setResourceState($package_id, $goods_id, $goods_type);
				if ($set_state_result) {
					$sub_count = $this->updatePackageResourceCount($package_id);//专栏资源数减一

					return $this->result($set_state_result);
				} else {
					return response()->json(Utils::pack("0", StringConstants::Code_Failed, "移除失败!"));
				}
			} else {//不存在
				//在资源表中查询资源的信息
				$resource_info = Utils::getResourceInfo($goods_id, $goods_type);
				if ($resource_info) {
					if ($resource_info->payment_type != 3) {//单卖的,则直接将关系表中的该记录置位为删除
						$set_state_result = $this->setResourceState($package_id, $goods_id, $goods_type);
						if ($set_state_result) {
							$sub_count = $this->updatePackageResourceCount($package_id);//专栏资源数减一

							return $this->result($set_state_result);
						} else {
							return response()->json(Utils::pack("0", StringConstants::Code_Failed, "移除失败!"));
						}
					} else if ($resource_info->payment_type == 3) {//不是单卖的,则直接将关系表中的该记录置位为删除并且将该资源标记位删除
						$set_state_result = $this->setResourceState($package_id, $goods_id, $goods_type);
						if ($set_state_result) {
							$sub_count = $this->updatePackageResourceCount($package_id);//专栏资源数减一
							//将该资源标记位删除
							$goods_state_result = $this->setGoodsState($goods_id, $goods_type, StringConstants::SINGLE_GOODS_DELETE);
							if ($goods_state_result) {
								return $this->result($goods_state_result);
							} else {
								return response()->json(Utils::pack("0", StringConstants::Code_Failed, "移除失败!"));
							}
						} else {
							return response()->json(Utils::pack("0", StringConstants::Code_Failed, "删除关系表记录失败!"));
						}
					}
				} else {
					return response()->json(Utils::pack("0", StringConstants::Code_Failed, "商品信息不存在!"));
				}
			}
		} else if ($channel_type == StringConstants::RESOURCE_CHANNEL_SINGLE) {//单品移除
			//处理步骤:
			//1.判断该商品goods_id在关系表t_pro_res_relation中是否存在。
			//若不存在,则直接将该资源标记位删除
			//若存在,则将该资源的payment_type=3
			$is_exist = $this->is_exist_in_other_package($goods_id, '', $goods_type, $channel_type);
			if (!$is_exist) {//不存在
				//则直接将该资源标记位删除
				$goods_state_result = $this->setGoodsState($goods_id, $goods_type, StringConstants::SINGLE_GOODS_DELETE);
				if ($goods_state_result) {
					return $this->result($goods_state_result);
				} else {
					return response()->json(Utils::pack("0", StringConstants::Code_Failed, "移除失败!"));
				}
			} else {//存在
				//将该资源的payment_type=3
				$goods_state_result = $this->setGoodsState($goods_id, $goods_type, StringConstants::SINGLE_GOODS_PAYMENT_TYPE_UPDATE);
				if ($goods_state_result) {
					return $this->result($goods_state_result);
				} else {
					return response()->json(Utils::pack("0", StringConstants::Code_Failed, "移除失败!"));
				}
			}
		} else {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "渠道类型有误!"));
		}
	}

	private function is_exist_in_other_package ($goods_id, $package_id, $goods_type, $channel_type)
	{
		$app_id   = AppUtils::getAppID();
		$whereRaw = "1=1";
		if ($channel_type == StringConstants::RESOURCE_CHANNEL_PACKAGE || $channel_type == StringConstants::RESOURCE_CHANNEL_MEMBER) {
			$whereRaw .= " and product_id != '" . $package_id . "'";
		}

		$is_exist = \DB::table("db_ex_business.t_pro_res_relation")
			->where("app_id", '=', $app_id)
			->where("resource_id", '=', $goods_id)
			->where("resource_type", '=', $goods_type)
			->where("relation_state", '=', StringConstants::RELATION_NORMAL)
			->whereRaw($whereRaw)
			->get();
		$count    = count($is_exist);

		return $count;
	}

	private function setResourceState ($package_id, $goods_id, $goods_type)
	{
		$app_id = AppUtils::getAppID();

		$params['relation_state'] = StringConstants::RELATION_DELETED;
		$params['updated_at']     = Utils::getTime();
		$result                   = \DB::table("db_ex_business.t_pro_res_relation")
			->where("app_id", '=', $app_id)
			->where("resource_id", '=', $goods_id)
			->where("resource_type", '=', $goods_type)
			->where("product_id", '=', $package_id)
			->where("relation_state", '=', StringConstants::RELATION_NORMAL)
			->update($params);

		return $result;
	}

	private function updatePackageResourceCount ($package_id)
	{
		$app_id = AppUtils::getAppID();

		$update_result = \DB::update("UPDATE db_ex_business.t_pay_products SET resource_count=resource_count-1 WHERE id='$package_id' and app_id = '$app_id'");

		return $update_result;

	}

	private function setGoodsState ($goods_id, $goods_type, $operator_type)
	{
		$app_id     = AppUtils::getAppID();
		$table_name = "";
		switch ($goods_type) {
			case 1://图文
				$table_name = "db_ex_business.t_image_text";
				if ($operator_type == StringConstants::SINGLE_GOODS_DELETE) {
					$params["display_state"] = StringConstants::SINGLE_GOODS_DELETE;
				}
				break;
			case 2://音频
				$table_name = "db_ex_business.t_audio";
				if ($operator_type == StringConstants::SINGLE_GOODS_DELETE) {
					$params["audio_state"] = StringConstants::SINGLE_GOODS_DELETE;
				}
				break;
			case 3://视频
				$table_name = "db_ex_business.t_video";
				if ($operator_type == StringConstants::SINGLE_GOODS_DELETE) {
					$params["video_state"] = StringConstants::SINGLE_GOODS_DELETE;
				}
				break;
			case 4://直播
				$table_name = "db_ex_business.t_alive";
				if ($operator_type == StringConstants::SINGLE_GOODS_DELETE) {
					$params["state"] = StringConstants::SINGLE_GOODS_DELETE;
				}
				break;
		}
		$params['updated_at'] = Utils::getTime();
		if ($operator_type == StringConstants::SINGLE_GOODS_PAYMENT_TYPE_UPDATE) {
			$params['payment_type'] = StringConstants::SINGLE_GOODS_PAYMENT_TYPE_UPDATE;
		}

		$result = \DB::table($table_name)
			->where("app_id", '=', $app_id)
			->where("id", '=', $goods_id)
			->update($params);

		return $result;
	}

	/**设为试听;
	 *参数:
	 *1-package_id;
	 *2-resource_type(2-音频);
	 *3-resource_id
	 *4-try_state(0-取消试听,1-设为试听)
	 **/
	public function setPackageResourceTry ()
	{
		//在表t_pro_res_relation中设置该音频为试听
		$app_id        = AppUtils::getAppID();
		$package_id    = Input::get("package_id", '');
		$resource_type = Input::get("resource_type", 2);
		$resource_id   = Input::get("resource_id", '');
		$try_state     = Input::get("try_state", '');

		if ($resource_type > StringConstants::SINGLE_GOODS_ALIVE || $resource_type < StringConstants::SINGLE_GOODS_ARTICLE) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "商品类型有误!"));
		}

		//校验音频的支付方式:即payment_type =3
		$resource_info = Utils::getResourceInfo($resource_id, $resource_type);
		if ($resource_info) {
			if ($resource_info->payment_type != 3) {
				return response()->json(Utils::pack("0", StringConstants::Code_Failed, "专栏外单卖的音频不能设置为试听!"));
			}
		} else {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "未查询到该商品!"));
		}

		$params['updated_at'] = Utils::getTime();
		$params['is_try']     = $try_state;//设置音频试听状态

		$result = \DB::table("db_ex_business.t_pro_res_relation")
			->where("app_id", '=', $app_id)
			->where("product_id", '=', $package_id)
			//            ->where("resource_type",'!=',StringConstants::SINGLE_GOODS_ALIVE)
			->where("resource_type", '=', $resource_type)
			->where("resource_id", '=', $resource_id)
			->where("is_try", '!=', $try_state)
			->update($params);

		if ($result) {
			return $this->result($result);
		} else {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "设置试听状态失败!"));
		}
	}

	/**
	 *结束直播;
	 * 参数:1-alive_id;
	 */
	public function endAlive ()
	{
		$alive_id = Input::get("id", '');
		//核实该直播信息是否存在
		$is_exist = $this->aliveInfo($alive_id);
		if ($is_exist) {
			$params["manual_stop_at"] = Utils::getTime();
			$params["updated_at"]     = Utils::getTime();

			//更新直播表t_alive中manual_stop_at字段的值为当前时间
			$update = \DB::table("db_ex_business.t_alive")
				->where("app_id", '=', AppUtils::getAppID())
				->where("id", '=', $alive_id)
				->update($params);
			if ($update) {
				return $this->result($update);
			} else {
				return response()->json(Utils::pack("0", StringConstants::Code_Failed, "结束直播失败!"));
			}
		} else {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "没有该直播的相关数据!"));
		}
	}

	//设置资源的可复制状态

	private function aliveInfo ($alive_id)
	{
		$alive_info = \DB::table("db_ex_business.t_alive")
			->where("app_id", '=', AppUtils::getAppID())
			->where("id", '=', $alive_id)
			->first();

		return $alive_info;
	}

	//对应的专栏资源数减1

	/**
	 * 设置直播的配置（直播人次显示，打赏提醒设置）
	 * 函数名：setAliveConfig
	 * 参数：1、id
	 *      2、config_show_view_count
	 *      3、config_show_reward
	 * 返回：1、json格式
	 * 作者：Kris
	 * 时间：2017.07.25
	 */
	public function setAliveConfig ()
	{
		$alive_id               = Input::get('id', '');
		$config_show_view_count = Input::get('config_show_view_count', 0);
		$config_show_reward     = Input::get('config_show_reward', 0);

		$is_exist = $this->aliveInfo($alive_id);
		if ($is_exist) {
			$params['config_show_view_count'] = $config_show_view_count;
			$params['config_show_reward']     = $config_show_reward;
			$params["updated_at"]             = Utils::getTime();

			//更新直播表t_alive中manual_stop_at字段的值为当前时间
			$update = \DB::table("db_ex_business.t_alive")
				->where("app_id", '=', AppUtils::getAppID())
				->where("id", '=', $alive_id)
				->update($params);
			if ($update) {
				return $this->result($update);
			} else {
				return response()->json(Utils::pack("0", StringConstants::Code_Failed, "设置直播配置失败!"));
			}
		} else {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "没有该直播的相关数据!"));
		}
	}

	//查询资源goods_id在其他专栏是否被拥有

	/**
	 * 查询分成比例
	 */
	public function queryProfitRatio ()
	{
		$alive_id = Input::get("alive_id", '');

		//核实该直播信息是否存在
		$is_exist = $this->aliveInfo($alive_id);
		if ($is_exist) {

			//  查询直播表t_alive中$first_distribute_percent字段的值
			$query = \DB::table("db_ex_business.t_alive")
				->where("app_id", '=', AppUtils::getAppID())
				->where("id", '=', $alive_id)
				->get();

			if ($query && count($query) > 0) {
				$distribute_percent = $query[0]->distribute_percent;

				return response()->json(Utils::pack(["distribute_percent" => $distribute_percent]));
			} else {
				return response()->json(Utils::pack("0", StringConstants::Code_Failed, "没有该直播的相关数据!"));
			}

		} else {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "没有该直播的相关数据!"));
		}

		return "";
	}

	//查询直播信息

	/**
	 * 设置分成比例;
	 * 参数:
	 * 1-alive_id;
	 * 2-分成比例(百分制:1-50)
	 */
	public function setProfitRatio ()
	{
		$alive_id           = Input::get("alive_id", '');
		$distribute_percent = Input::get("distribute_percent", '');

		if ($distribute_percent < 0 || $distribute_percent > 50) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "直播分成比例区间(0-50)!!"));
		}

		//核实该直播信息是否存在
		$is_exist = $this->aliveInfo($alive_id);
		if ($is_exist) {
			$params["distribute_percent"] = $distribute_percent;//设置直播的分成比例(1-100%)
			$params["updated_at"]         = Utils::getTime();

			//更新直播表t_alive中$first_distribute_percent字段的值
			$update = \DB::table("db_ex_business.t_alive")
				->where("app_id", '=', AppUtils::getAppID())
				->where("id", '=', $alive_id)
				->update($params);
			if ($update) {
				return $this->result($update);
			} else {
				return response()->json(Utils::pack("0", StringConstants::Code_Failed, "设置直播分成比例失败!"));
			}
		} else {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "没有该直播的相关数据!"));
		}
	}

	//将该资源标记位删除

	/**
	 * 置顶;
	 * 参数:
	 * 1-package_id;
	 * 2-resource_type(0-专栏,1-图文,2-音频,3-视频,4-直播,5-活动,6-专栏);
	 * 3-resource_id
	 * 4-top_state(0-取消置顶,1-设置置顶)
	 */
	public function setPackageResourceTop ()
	{
		//在表t_pro_res_relation中设置该资源为top_state状态
		$app_id        = AppUtils::getAppID();
		$package_id    = Input::get("package_id", '');
		$resource_type = Input::get("resource_type", -1);
		$resource_id   = Input::get("resource_id", '');
		$top_state     = Input::get("top_state", '');

		if ($resource_type < 0 || $resource_type > 6) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "商品类型有误!"));
		}

		$params['updated_at'] = Utils::getTime();
		$params['is_top']     = $top_state;//设置资源的置顶状态

		$result = \DB::table("db_ex_business.t_pro_res_relation")
			->where("app_id", '=', $app_id)
			->where("product_id", '=', $package_id)
			->where("resource_type", '=', $resource_type)
			->where("resource_id", '=', $resource_id)
			->where("is_top", '!=', $top_state)
			->update($params);

		if ($result) {
			return $this->result($result);
		} else {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "设置置顶状态失败!"));
		}
	}

	//将关系表中的该记录置位为删除

	/**
	 * 修改专栏完结状态;
	 * 参数:
	 * 1-package_id;
	 * 2-finished_state(0-未完结,1-已完结)
	 */
	public function savePackageFinishedState ()
	{
		$package_id     = Input::get("package_id");
		$finished_state = Input::get("finished_state");

		//核实该专栏信息
		$is_exist = $this->getPackageInfo($package_id);
		if ($is_exist) {
			$params['finished_state'] = $finished_state;
			$params['updated_at']     = Utils::getTime();
			//更新专栏完结状态
			$update = \DB::table("db_ex_business.t_pay_products")
				->where("app_id", '=', AppUtils::getAppID())
				->where("id", '=', $package_id)
				->update($params);
			if ($update) {
				return $this->result($update);
			} else {
				return response()->json(Utils::pack("0", StringConstants::Code_Failed, "更新专栏完结状态失败!"));
			}
		} else {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "没有该专栏的相关数据!"));
		}
	}

	//查询资源goods_id在关系表t_pro_res_relation中是否和其他专栏有关(除package_id外)

	/**
	 * 查询商品存在的记录;
	 * 参数:
	 * 1-goods_id;
	 * 2-goods_type(1-图文,2-音频,3-视频,4-直播);
	 * 3-channel_type(0-单品,1-专栏内资源);
	 * 4-package_id(当channel_type=1时有值)
	 */
	public function queryGoodsState ()
	{
		$goods_id     = Input::get("goods_id", '');
		$goods_type   = Input::get("goods_type", '');
		$channel_type = Input::get("channel_type", '');
		$records_list = [];
		$is_exist     = 0;
		if ($channel_type == StringConstants::RESOURCE_CHANNEL_PACKAGE || $channel_type == StringConstants::RESOURCE_CHANNEL_MEMBER) {//专栏内移除时,查询在其他的记录(专栏外单卖的)
			$package_id = Input::get("package_id", '');

			$query_other_package = $this->queryGoods($goods_id, $package_id, $goods_type, $channel_type);
			foreach ($query_other_package as $key => $goods) {
				$records_list[ $key + 1 ]['id']    = $goods->product_id;
				$records_list[ $key + 1 ]['title'] = $goods->product_name;
			}
			$goods_info = Utils::getResourceInfo($goods_id, $goods_type);
			if ($goods_info) {
				if ($goods_info->payment_type == 2) {
					$records_list[0]['id']    = $goods_id;
					$records_list[0]['title'] = $goods_info->title;
					//                    $records_list[0]['img_url'] = $goods_info->img_url;
					//                    $records_list[0]['start_at'] = $goods_info->start_at;
					//                    $records_list[0]['piece_price'] = $goods_info->piece_price;
				}
			}
			if (count($records_list) > 0) {
				$is_exist = 1;
			} else {
				$is_exist = 0;
			}

		} else if ($channel_type == StringConstants::RESOURCE_CHANNEL_SINGLE) {//单品移除,查询在其他地方的记录(专栏)
			$query_other_package = $this->queryGoods($goods_id, '', $goods_type, $channel_type);
			foreach ($query_other_package as $key => $goods) {
				$records_list[ $key ]['id']    = $goods->product_id;
				$records_list[ $key ]['title'] = $goods->product_name;
			}
			if (count($query_other_package) == 0) {
				$is_exist = 0;
			} else {
				$is_exist = 1;//存在
			}
		}

		$data['records_list'] = $records_list;
		$data['is_exist']     = $is_exist;

		return $this->result($data);
	}

	//专栏移动

	private function queryGoods ($goods_id, $package_id, $goods_type, $channel_type)
	{
		$app_id   = AppUtils::getAppID();
		$whereRaw = "1=1";
		if ($channel_type == StringConstants::RESOURCE_CHANNEL_PACKAGE || $channel_type == StringConstants::RESOURCE_CHANNEL_MEMBER) {
			$whereRaw .= " and product_id != '" . $package_id . "'";
		}
		$list = \DB::table("db_ex_business.t_pro_res_relation")
			->where("app_id", '=', $app_id)
			->where("resource_id", '=', $goods_id)
			->where("resource_type", '=', $goods_type)
			->where("relation_state", '=', StringConstants::RELATION_NORMAL)
			->whereRaw($whereRaw)
			->get();

		return $list;
	}

	//查询专栏信息

	/**
	 * 设置专栏内容是否再最新列显示
	 * 参数:1-id(专栏、会员id)
	 * 2-h5_newest_hide(是否在最新列表展示，0-显示,1-隐藏)
	 */
	public function setH5NewestHide ()
	{
		$resource_id = Input::get('id');
		$hide_state  = Input::get('h5_newest_hide');

		$data['h5_newest_hide'] = $hide_state;
		$app_id                 = AppUtils::getAppID();
		//插入当前时间
		$data['updated_at'] = Utils::getTime();
		$result             = \DB::table('t_pay_products')
			->where('id', '=', $resource_id)
			->where('app_id', '=', $app_id)
			->update($data);

		if ($result >= 0 || $result) {
			return response()->json(['code' => 0, 'msg' => '修改成功']);
		} else {
			return response()->json(['code' => -1, 'msg' => '修改失败']);
		}
	}

	/**
	 *设置资源是否可以被复制;
	 * 参数:
	 * 1-id(资源id);
	 * 2-resource_type(1-图文;2-音频;3-视频;4-直播);
	 * 3-can_select(是否可以被复制，0-不允许,1-允许)
	 */
	public function setResourceSelectCan ()
	{
		$id            = Input::get("id", '');
		$resource_type = Input::get("resource_type", '');
		$can_select    = Input::get("can_select", '-1');

		if ($can_select == '-1') {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "操作的类型有误!"));
		}

		if ($resource_type < StringConstants::SINGLE_GOODS_ARTICLE || $resource_type > StringConstants::SINGLE_GOODS_ALIVE) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "资源类型有误!"));
		}

		//查询资源id对应的内容是否存在
		$resource_info = Utils::getResourceInfo($id, $resource_type);
		if ($resource_info) {
			//更新数据库中资源的字段can_select字段的值为$can_select;
			$update_result = $this->setSelectState($id, $resource_type, $can_select);
			if ($update_result) {
				return $this->result($update_result);
			} else {
				return response()->json(Utils::pack("0", StringConstants::Code_Failed, "更新资源复制状态失败!"));
			}
		} else {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "该资源不存在!"));
		}
	}

	//查询音频评论播放信息

	private function setSelectState ($id, $resource_type, $can_select)
	{
		$app_id = AppUtils::getAppID();

		$params['can_select'] = $can_select;
		$params['updated_at'] = Utils::getTime();

		$table_name_array = [
			'1' => 'db_ex_business.t_image_text',
			'2' => 'db_ex_business.t_audio',
			'3' => 'db_ex_business.t_video',
			'4' => 'db_ex_business.t_alive',
		];

		$update = \DB::table($table_name_array[ $resource_type ])
			->where('app_id', '=', $app_id)
			->where('id', '=', $id)
			->update($params);

		return $update;
	}

	/**
	 * @param        $product_id
	 * @param string $date
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function checkGoodsMessagePush ($product_id, $date = "")
	{
		$app_id = AppUtils::getAppID();
		$today  = date('Y-m-d');
		if (!$date || strtotime($date) < strtotime($today)) {
			$date = $today;
		} else {
			$date = date('Y-m-d', strtotime($date));
		}
		if (!$product_id) return response()->json(['code' => -2, 'msg' => '参数错误', 'data' => []]);

		$is_message_push = MessagePush::checkMessagePush($app_id);
		if ($is_message_push === false) return response()->json(['code' => -1, 'msg' => '个人版请开启模板消息推送开关', 'data' => []]);

		// 查询该产品包下的所有资源（不包含会员）
		$resource_id  = DB::table('t_pro_res_relation')->where('app_id', $app_id)
			->where('product_id', $product_id)->where('resource_type', '<', 5)->where('relation_state', 0)->pluck('resource_id');
		$resource_id  = implode("','", $resource_id);
		$resource_str = " and id in ('$resource_id') ";

		$sql  = "
            select * from (
              SELECT id,start_at FROM t_audio
              WHERE app_id = '{$app_id}' {$resource_str} AND audio_state IN (0, 1) and push_state in (1,2) and  start_at like '%{$date}%'
            UNION ALL
              SELECT id,start_at FROM t_video
              WHERE app_id = '{$app_id}' {$resource_str} AND video_state IN (0, 1) and push_state in (1,2) and  start_at like '%{$date}%'
            UNION ALL
              SELECT id,start_at FROM t_image_text
              WHERE app_id = '{$app_id}' {$resource_str} AND display_state IN (0, 1) and push_state in (1,2) and  start_at like '%{$date}%'
            UNION ALL
              SELECT id,zb_start_at as start_at FROM t_alive
              WHERE app_id = '{$app_id}' {$resource_str} AND state IN (0, 1) and push_state in (1,2) and  zb_start_at like '%{$date}%'
            )v1 order BY start_at
        ";
		$info = DB::select($sql);

		$has_push   = count($info);
		$valid_push = (3 - $has_push) > 0 ? 3 - $has_push : 0;

		$data['has_push']   = $has_push;
		$data['valid_push'] = $valid_push;

		return response()->json(['code' => 0, 'msg' => '请求成功', 'data' => $data]);
	}

}