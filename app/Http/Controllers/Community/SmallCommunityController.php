<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\ImageUtils;
use App\Http\Controllers\Tools\MessagePush;
use App\Http\Controllers\Tools\ResContentComm;
use App\Http\Controllers\Tools\StringConstants;
use App\Http\Controllers\Tools\Utils;
use App\Http\Controllers\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class SmallCommunityController extends Controller
{

	private $app_id;

	public function __construct ()
	{
		$this->app_id = AppUtils::getAppID();
	}

	//社群列表  页面
	public function communityList ()
	{
		$app_id = $this->app_id;
		$ruler  = trim(Input::get("ruler", "-1")); //维度
		$search = trim(Input::get("search", ""));//搜索内容

		$whereRaw = '';
		$whereRaw .= self::whereAdd($whereRaw) . "app_id = '$app_id'";

		if (($ruler == '-1') || ($ruler == ''))
			$whereRaw .= self::whereAdd($whereRaw) . "community_state <> 2";
		else
			$whereRaw .= self::whereAdd($whereRaw) . "community_state = '$ruler'";
		if (!Utils::isEmptyString($search))
			$whereRaw .= self::whereAdd($whereRaw) . "title like '%" . $search . "%'";
		$communityList = \DB::table("t_community")
			->select()
			->whereRaw("$whereRaw")
			->orderBy('created_at', 'desc')
			->paginate(10);
		$count         = \DB::table("t_community")
			->select()
			->whereRaw("$whereRaw")
			->count();

		$app_info = AppUtils::getAppConfInfo($app_id);  // app 信息
		foreach ($communityList as $one) {

			//生成资源访问链接
			if ($app_info) {
				if (!empty($app_info->wx_app_id) || $app_info->use_collection == 1) {
					if ($app_info->use_collection == 0) {
						$pageUrl = AppUtils::getUrlHeader($app_id) . $app_info->wx_app_id . '.' . env('DOMAIN_NAME');
					} else {
						$pageUrl = AppUtils::getUrlHeader($app_id) . env('DOMAIN_DUAN_NAME');
					}
					//查询该资源关联的专栏或会员
					$product_id   = $this->getProductIdByResId($one->id);
					$one->pageUrl = $pageUrl . Utils::getContentUrl(2, 7, $one->id, $product_id, '');
				}
			}

			//查找该社群的用户数量
			$one->users_count = \DB::table('t_community_user')
				->where('app_id', '=', $app_id)
				->where('community_id', '=', $one->id)
				->count();

			//查找该社区的群主
			$admin = \DB::table('t_community_user')
				->where('app_id', '=', $app_id)
				->where('community_id', '=', $one->id)
				->where('type', '=', 1)
				->first();
			if ($admin) {
				$one->admin      = 0;
				$one->admin_name = $admin->nick_name;
			} else $one->admin = 1;

			//查找该社群的关联专栏
			$info               = $this->getProductByCommunityId($one->id);
			$one->product_count = $info[0];
			unset($info[0]);
			$one->product_name_str = $info;
		}

		return view('admin.communityOperate.communityList', compact("communityList", "count", "ruler", "search"));
	}

	//新增社群页面

	private function whereAdd ($str)
	{
		return $str ? ' and ' : '';
	}

	//新增社群接口

	private function getProductIdByResId ($resource_id)
	{
		$app_id           = AppUtils::getAppID();
		$pro_res_relation = \DB::table("db_ex_business.t_pro_res_relation")
			->where('app_id', '=', $app_id)
			->where('resource_id', '=', $resource_id)
			->where('relation_state', '=', 0)
			->first();
		if ($pro_res_relation) {
			return $pro_res_relation->product_id;
		} else {
			return '';
		}
	}

	// 编辑社群页面

	/**
	 * 用社群id得到该社群所关联的专栏信息（正常的关联状态  如果已经删除则查不到）
	 * 参数:id(社群id)
	 * 该方法仅供 communityList 方法调用
	 * 返回关联专栏的总数  和  专栏的类型（专栏或会员）  和  专栏的名字
	 **/
	private function getProductByCommunityId ($id)
	{

		$app_id       = $this->app_id;
		$product_info = \DB::table('t_pro_res_relation')
			->where('app_id', '=', $app_id)
			->where('resource_id', '=', $id)
			->where('resource_type', '=', 7)
			->where('relation_state', '=', 0)
			->get();

		$product_count = count($product_info);
		$info[0]       = $product_count;

		$i = 1;
		foreach ($product_info as $item) {
			$is_member = \DB::table('t_pay_products')
				->where('app_id', '=', $app_id)
				->where('id', '=', $item->product_id)
				->value('is_member');
			if ($is_member === 1) {//是会员
				$info[ $i ]['type'] = 'member';

			} else {
				$info[ $i ]['type'] = 'product';
			}
			$info[ $i ]['name'] = $item->product_name;
			$i                  += 1;
		}

		return $info;
	}

	//更新社群操作

	public function createCommunity ()
	{
		$app_id       = $this->app_id;
		$pay_products = \DB::table('t_pay_products')
			->select('name', 'id')
			->where('app_id', '=', $app_id)
			->where('state', '=', 0)
			->where('is_member', '=', 0)
			->get();
		$member       = \DB::table('t_pay_products')
			->select('name', 'id')
			->where('app_id', '=', $app_id)
			->where('state', '=', 0)
			->where('is_member', '=', 1)
			->get();
		//        $use_collection = DB::connection('mysql_config')->table('t_app_conf')
		//            ->where('app_id',$this->app_id)
		//            ->value('use_collection');//判断是否个人模式，决定是否显示服务号推送切换开关

		$page_type = 0;  // page_type  =  0 表示新建页面   1  表示编辑页面

		return view('admin.communityOperate.manageCommunity', compact('pay_products', 'member', 'page_type'));
	}

	//设置群主接口

	public function uploadCommunity (Request $request)
	{

		$data['app_id'] = $this->app_id;
		$data['id']     = Utils::getUniId('c_') . random_int(1000, 9999);

		$data['title'] = Input::get('title');
		if (empty($data['title']))
			return response()->json(['code' => 2, 'msg' => 'title required']);

		$data['describe'] = Input::get('describe');
		if (empty($data['describe']))
			return response()->json(['code' => 4, 'msg' => 'describe required']);

		$data['img_url'] = Input::get('img_url');
		if (empty($data['img_url']))
			return response()->json(['code' => 8, 'msg' => 'img_url required']);

		//if(array_key_exists('img_url', $data)) ResContentComm::imageDeal($data['img_url'], 't_community', $data['id']);//,160,120,60);
		if (array_key_exists('img_url', $data)) ImageUtils::resImgCompress($request, 'db_ex_business.t_community', $data['app_id'], $data['id'], $data['img_url']);

		$piece_price = Input::get('piece_price');
		$product_id  = Input::get('product_id');

		if ($product_id)
			$product_id_arr = \GuzzleHttp\json_decode($product_id, true);

		if (Utils::isEmptyString($piece_price) && empty($product_id_arr))
			return response()->json(['code' => 16, 'msg' => 'piece_price or  product_id is required']);

		if ($piece_price != null) {   // 价格为0 或者 数字
			$data['piece_price'] = $piece_price;
			if ($piece_price == 0)
				$data['payment_type'] = 1;
			else
				$data['payment_type'] = 2;
			$res = Utils::checkPiecePrice($data);
			if ($res !== 0)
				return response()->json(['code' => 32, 'msg' => $res]);

		} else {                    //没有价格 只为专栏或会员
			if (!(Utils::isEmptyString($product_id)))
				$data['payment_type'] = 3;
		}

		$data['community_state'] = Input::get('community_state');
		if (Utils::isEmptyString($data['community_state']))
			return response()->json(['code' => 64, 'msg' => 'community_state is required']);

		$data['created_at'] = Utils::getTime();
		$data['updated_at'] = Utils::getTime();

		$data2 = [];
		if (!empty($product_id_arr)) {
			$length = count($product_id_arr);
			for ($i = 0; $i < $length; $i += 1) {
				$pro['app_id']        = $this->app_id;
				$pro['product_id']    = $product_id_arr[ $i ][ $i ];
				$pro['product_name']  = $this->getProductNameById($product_id_arr[ $i ]);
				$pro['resource_type'] = 7;
				$pro['resource_id']   = $data['id'];
				$pro['created_at']    = Utils::getTime();
				$pro['updated_at']    = Utils::getTime();
				$data2[]              = $pro;
			}
		}

		//        return response()->json(['code'=>1,'msg' => $data2]);

		return $this->saveCommunity($data, $data2);

	}

	//上下架社群

	private function getProductNameById ($id)
	{
		$app_id       = $this->app_id;
		$product_name = \DB::table('t_pay_products')
			->where('app_id', '=', $app_id)
			->where('id', '=', $id)
			->value('name');

		return $product_name;
	}

	//设置群主弹窗需要的用户

	private function saveCommunity ($data, $data2)
	{

		$is_new = \DB::table('t_community')
			->where('app_id', '=', $this->app_id)
			->get();
		if (!$is_new) $is_new = 1;
		else $is_new = 0;

		if ($data2) {              //有专栏
			\DB::beginTransaction();

			$res1 = \DB::table('t_community')->insert($data);
			if (!$res1) return response()->json(['code' => 1024, 'msg' => 'db error']);

			$res2 = \DB::table('t_pro_res_relation')->insert($data2);
			if (!$res2) {
				\DB::rollBack();

				return response()->json(['code' => 1024, 'msg' => 'db error', 'res' => $res2]);
			}

			\DB::commit();

			return response()->json(['code' => 0, 'msg' => 'ok', 'is_new' => $is_new, 'id' => $data['id']]);

		} else {
			//无专栏
			$res = \DB::table('t_community')->insert($data);
			if ($res) return response()->json(['code' => 0, 'msg' => 'ok', 'is_new' => $is_new, 'com_id' => $data['id']]);
		}
	}

	//获取扫描设置群主二维码链接

	public function editCommunity ()
	{
		$app_id = $this->app_id;
		$id     = Input::get('id');
		if (empty($id))
			return response()->json(['code' => 1, 'id' => 'id required']);

		$data = \DB::table('t_community')
			->where('app_id', '=', $app_id)
			->where('id', '=', $id)
			->first();

		$relation = \DB::table('t_pro_res_relation')
			->select('product_id', 'product_name')
			->where('app_id', '=', $app_id)
			->where('resource_type', '=', 7)
			->where('relation_state', '=', 0)
			->where('resource_id', '=', $id)
			->get();
		foreach ($relation as $one) {
			$one->type = \DB::table('t_pay_products')
				->where('app_id', '=', $app_id)
				->where('id', '=', $one->product_id)
				->value('is_member');
		}

		$pay_products = \DB::table('t_pay_products')
			->select('name', 'id')
			->where('app_id', '=', $app_id)
			->where('state', '=', 0)
			->where('is_member', '=', 0)
			->get();

		$member = \DB::table('t_pay_products')
			->select('name', 'id')
			->where('app_id', '=', $app_id)
			->where('state', '=', 0)
			->where('is_member', '=', 1)
			->get();

		$page_type = 1;

		return view('admin.communityOperate.manageCommunity', compact('data', 'relation', 'pay_products', 'member', 'page_type'));
	}

	public function updateCommunity (Request $request)
	{
		$app_id    = $this->app_id;
		$id        = Input::get('id');
		$old_price = \DB::table('t_community')
			->where('app_id', '=', $app_id)
			->where('id', '=', $id)
			//            ->first();
			->value('piece_price');

		if (empty($id))
			return response()->json(['code' => 1, 'id' => 'id required']);

		$data['title'] = Input::get('title');
		if (empty($data['title']))
			return response()->json(['code' => 2, 'msg' => 'title required']);

		$data['describe'] = Input::get('describe');
		if (empty($data['describe']))
			return response()->json(['code' => 4, 'msg' => 'describe required']);

		$data['img_url'] = Input::get('img_url');
		if (empty($data['img_url']))
			return response()->json(['code' => 8, 'msg' => 'img_url required']);
		//图片压缩
		//        if(array_key_exists('img_url', $data)) ResContentComm::imageDeal($data['img_url'], 't_community', $id);//,160,120,60);
		if (array_key_exists('img_url', $data)) ImageUtils::resImgCompress($request, 'db_ex_business.t_community', $app_id, $id, $data['img_url']);

		$piece_price = Input::get('piece_price');
		$product_id  = Input::get('product_id');

		if ($product_id)
			$product_id_arr = \GuzzleHttp\json_decode($product_id, true);

		if (Utils::isEmptyString($piece_price) && empty($product_id_arr))
			return response()->json(['code' => 16, 'msg' => 'piece_price or  product_id is required']);

		if ($piece_price != null) {
			$data['piece_price'] = $piece_price;
			if ($piece_price == 0)
				$data['payment_type'] = 1;
			else
				$data['payment_type'] = 2;
			$res = Utils::checkPiecePrice($data);
			if ($res !== 0)
				return response()->json(['code' => 32, 'msg' => $res]);
		} else {
			if (!(Utils::isEmptyString($product_id)))
				$data['payment_type'] = 3;
			$data['piece_price'] = null;  //编辑  如果只关联则 将钱数取消
		}

		$data['community_state'] = Input::get('community_state');
		if (Utils::isEmptyString($data['community_state']))
			return response()->json(['code' => 32, 'msg' => 'community_state is required']);

		$data['updated_at'] = Utils::getTime();

		\DB::beginTransaction();    //开启事务
		$res = \DB::table('t_community')
			->where('app_id', '=', $app_id)
			->where('id', '=', $id)
			->update($data);
		if (!$res) return response()->json(['code' => 1024, 'msg' => 'db error']);

		$relation_info = \DB::table('t_pro_res_relation')// 查找已有的所有关系（包括已删除的）
		->select('product_id')
			->where('app_id', '=', $app_id)
			->where('resource_type', '=', 7)
			->where('resource_id', $id)
			->get();

		if ($relation_info) {
			foreach ($relation_info as $one) {
				$table[] = $one->product_id;
			}
		} else $table = [];

		if ($product_id_arr) {  // 表单提交的专栏id转换的数组
			$i = 0;
			foreach ($product_id_arr as $k => $v) {
				$out[] = $v[ $i ];
				$i     += 1;
			}
			unset($i);
		} else $out = [];

		if (empty($out)) {
			if (empty($table)) {  // 如果表里已经有的 和 表单上传的都为空  则不进行操作

			} else {               //表里原有的  表单未上传   则将表单内的全部置为删除态
				$res = $this->setRelationState($id, $table, 1); //传入 社群id 数组  状态码
				if ($res) {
					\DB::rollBack();

					return response()->json(['code' => 1024, 'msg' => 'db error']);
				}
			}
		} else {
			if (empty($table)) {  //关系表中没有  则直接插入
				$data2 = [];
				foreach ($out as $k => $v) {
					$pro['app_id']        = $this->app_id;
					$pro['product_id']    = $v;
					$pro['product_name']  = $this->getProductNameById($v);
					$pro['resource_type'] = 7;
					$pro['resource_id']   = $id;
					$pro['created_at']    = Utils::getTime();
					$pro['updated_at']    = Utils::getTime();
					$data2[]              = $pro;
				}
				$res2 = \DB::table('t_pro_res_relation')->insert($data2);
				if (!$res2) {
					\DB::rollBack();

					return response()->json(['code' => 1024, 'msg' => 'db error']);
				}
			} else {  // 表里和传入的数组都不为空

				foreach ($out as $k => $v) {
					if (!in_array($v, $table)) {
						$out_new[] = $v;
						unset($out[ $k ]);
					}
				}
				foreach ($table as $k => $v) {
					if (!in_array($v, $out)) {
						$table_new[] = $v;
						unset($table[ $k ]);
					}
				}

				if (!empty($table_new)) {
					$res = $this->setRelationState($id, $table_new, 1);
					if ($res) {
						\DB::rollBack();

						return response()->json(['code' => 1024, 'msg' => 'db error']);
					}
				}

				if (!empty($out_new)) {
					$data2 = [];
					foreach ($out_new as $k => $v) {
						$pro['app_id']        = $this->app_id;
						$pro['product_id']    = $v;
						$pro['product_name']  = $this->getProductNameById($v);
						$pro['resource_type'] = 7;
						$pro['resource_id']   = $id;
						$pro['created_at']    = Utils::getTime();
						$pro['updated_at']    = Utils::getTime();
						$data2[]              = $pro;
					}
					$res2 = \DB::table('t_pro_res_relation')->insert($data2);
					if (!$res2) {
						\DB::rollBack();

						return response()->json(['code' => 1024, 'msg' => 'db error']);
					}
				}

				if (!empty($out)) {
					$res3 = $this->setRelationState($id, $out, 0);
					if ($res3) {
						\DB::rollBack();

						return response()->json(['code' => 1024, 'msg' => 'db error']);
					}
				}
			}
		}

		// 提交事务
		\DB::commit();
		$ret = ['code' => 0, 'msg' => 'ok'];
		if (($old_price === 0) && ($piece_price > 0)) $ret = ['code' => 0, 'msg' => 'ok', 'free' => 1];

		return response()->json($ret);
	}

	// 根据专栏id返回专栏的名字
	// 新建和保存社群需要此方法

	/**
	 * 传入 社群id 专栏id数组 状态
	 * 如果更新错误（数据库操作） 则返回假
	 */
	private function setRelationState ($id, $arr, $state)
	{
		foreach ($arr as $k => $v) {
			$res = \DB::table('t_pro_res_relation')
				->where('app_id', '=', $this->app_id)
				->where('resource_id', '=', $id)
				->where('product_id', '=', $v)
				->where('resource_type', '=', 7)
				->update(['relation_state' => $state, 'updated_at' => Utils::getTime()]);
			if (!$res) return $res;
		}
	}

	public function setCommunityAdmin ()
	{
		$app_id       = $this->app_id;
		$community_id = Input::get('community_id');
		if (empty($community_id))
			return response()->json(['code' => 1, 'msg' => 'community_id is required']);
		$user_id = Input::get('user_id');
		if (empty($user_id))
			return response()->json(['code' => 2, 'msg' => 'user_id is required']);

		\DB::beginTransaction();

		// 原有的群主信息
		$now_admin_user_id = \DB::table('t_community_user')
			->where('app_id', '=', $app_id)
			->where('community_id', '=', $community_id)
			->where('type', '=', 1)
			->where('state', '=', 0)
			->value('user_id');
		if ($now_admin_user_id) { //如果该群已经有群主
			//置位原来的群主
			$res1 = \DB::table('t_community_user')
				->where('app_id', '=', $app_id)
				->where('community_id', '=', $community_id)
				->where('user_id', '=', $now_admin_user_id)
				->update(['type' => 0, 'can_delete_feeds' => 0, 'can_delete_comment' => 0, 'can_move_blacklist' => 0, 'can_set_select' => 0, 'can_set_notice' => 0]);
			if (!$res1)
				return response()->json(['code' => 1024, 'msg' => 'db error']);
		}

		$set_admin_user = \DB::table('t_community_user')
			->where('app_id', '=', $app_id)
			->where('community_id', '=', $community_id)
			->where('user_id', '=', $user_id)
			->first();
		if ($set_admin_user) { //想设置的群主已经在关系表中 则更新

			$res3 = \DB::table('t_community_user')
				->where('app_id', '=', $app_id)
				->where('community_id', '=', $community_id)
				->where('user_id', '=', $user_id)
				->update(['state' => 0, 'type' => 1, 'can_delete_feeds' => 1, 'can_delete_comment' => 1, 'can_move_blacklist' => 1, 'can_set_select' => 1, 'can_set_notice' => 1]);
			if (!$res3) {
				\DB::rollBack();

				return response()->json(['code' => 1024, 'msg' => 'db error']);
			} else {
				\DB::commit();

				return response()->json(['code' => 0, 'msg' => 'ok']);
			}

		} else { //没在关系表中  则插入
			$data['app_id']       = $app_id;
			$data['community_id'] = $community_id;
			$data['user_id']      = $user_id;
			$data['nick_name']    = \DB::table('t_users')->where('app_id', '=', $app_id)->where('user_id', '=', $user_id)->value('wx_nickname');
			$data['wx_avatar']    = \DB::table('t_users')->where('app_id', '=', $app_id)->where('user_id', '=', $user_id)->value('wx_avatar');

			$data['state']              = 0;
			$data['type']               = 1;
			$data['can_delete_feeds']   = 1;
			$data['can_delete_comment'] = 1;
			$data['can_move_blacklist'] = 1;
			$data['can_set_select']     = 1;
			$data['can_set_notice']     = 1;

			$data['created_at'] = Utils::getTime();

			$res4 = \DB::table('t_community_user')->insert($data);
			if (!$res4) {
				\DB::rollBack();

				return response()->json(['code' => 1024, 'msg' => 'db error']);
			} else {
				\DB::commit();

				return response()->json(['code' => 0, 'msg' => 'ok']);
			}
		}
	}

	public function changeCommunityState ()
	{
		$app_id          = $this->app_id;
		$id              = Input::get('id');
		$community_state = Input::get('community_state');

		if (Utils::isEmptyString($id))
			return response()->json(['code' => 1, 'msg' => 'id is required']);
		if (Utils::isEmptyString($community_state))
			return response()->json(['code' => 2, 'msg' => 'community_state is required']);

		$res = \DB::table('t_community')
			->where('app_id', '=', $app_id)
			->where('id', '=', $id)
			->update(['community_state' => $community_state]);

		if ($res)
			return response()->json(['code' => 0, 'msg' => 'state is required']);
		else
			return response()->json(['code' => 1024, 'msg' => 'db error']);
	}

	public function getUserInfo ()
	{
		$app_id = $this->app_id;
		$step   = Input::get('step');
		$search = Input::get("search");

		if (!$step) $step = 1;
		$step = ($step - 1) * 10;

		if (!empty($search)) {
			$whereRaw = " app_id = '" . $app_id . "'and (wx_nickname like '%" . $search . "%'" . " or phone like '%" . $search . "%')";
			$data     = \DB::table("db_ex_business.t_users")
				->select('user_id', 'wx_nickname', 'wx_avatar')
				->whereRaw($whereRaw)
				->skip($step)
				->take(10)
				->get();
		} else {
			$data = \DB::table('t_users')
				->select('user_id', 'wx_nickname', 'wx_avatar')
				->where('app_id', '=', $app_id)
				->skip($step)
				->take(10)
				->get();
		}
		if ($data)
			return response()->json(['code' => 0, 'msg' => 'ok', 'data' => $data]);
	}

	//根据资源id获取专栏id

	public function getCommunityLinkSetAdmin ()
	{

		$app_id       = $this->app_id;
		$community_id = Input::get('community_id');
		if (Utils::isEmptyString($community_id))
			return response()->json(['code' => 1, 'msg' => 'community_id is required']);

		$id = Utils::getUniId('dr_', 8);

		$data['id']          = $id;
		$data['app_id']      = $app_id;
		$data['related_id']  = $community_id;
		$data['record_type'] = 1;

		$res = \DB::table('t_disposable_record')->insert($data);
		if (!$res) return response()->json(['code' => 1, 'msg' => 'db error']);

		$arr['id']            = $id;
		$arr['community_id']  = $community_id;
		$arr['share_user_id'] = '';
		$str                  = \GuzzleHttp\json_encode($arr);
		$str                  = Utils::urlSafe_b64encode($str);

		$app_info = AppUtils::getAppConfInfo($app_id);  // app 信息
		$url      = '';
		//生成资源访问链接
		if ($app_info) {
			if (!empty($app_info->wx_app_id) || $app_info->use_collection == 1) {
				if ($app_info->use_collection == 0) {
					$url = AppUtils::getUrlHeader($app_id) . $app_info->wx_app_id . '.' . env('DOMAIN_NAME');
				} else {
					$url = AppUtils::getUrlHeader($app_id) . env('DOMAIN_DUAN_NAME') . '/' . $app_id;

				}
				$url .= '/setOwner/';
				$url .= $str;
			}
		}

		return response()->json(['code' => 0, 'data' => $url]);
	}

	//保存数据  新建社群接口需调用此方法

	public function isCommunityHaveAdmin ()
	{
		$app_id       = $this->app_id;
		$community_id = $community_id = Input::get('community_id');
		if (Utils::isEmptyString($community_id))
			return response()->json(['code' => 1, 'msg' => 'community_id is required']);

		$now_admin_user_id = \DB::table('t_community_user')
			->where('app_id', '=', $app_id)
			->where('community_id', '=', $community_id)
			->where('type', '=', 1)
			->where('state', '=', 0)
			->get();

		if ($now_admin_user_id)
			return response()->json(['code' => 0, 'msg' => 'the community exists admin']);
		else
			return response()->json(['code' => 1, 'msg' => 'the community is not exists admin']);

	}


	/******************************
	 * 以下是动态的相关接口处理
	 ******************************/

	/**
	 * 动态列表
	 * @return View
	 * 1-community_id : 社群id
	 * 2-state(0-全部动态;1-精选动态;2-群主动态)
	 * 3-search_content
	 */
	public function dynamicList ()
	{

		$community_id   = Input::get("community_id");
		$search_content = Input::get('search_content', '');
		$state          = Input::get("state", 0);

		//在表t_community_feeds中查询该社群的所有动态
		$whereRaw = " 1=1 ";
		if (!Utils::isEmptyString($search_content)) {
			$whereRaw .= " and content like '" . "%" . $search_content . "%'";
		}
		if ($state == 1) {
			$whereRaw .= " and is_chosen = 1";
		}
		if ($state == 2) {
			$whereRaw .= " and user_type = 1";
		}

		$dynamicList = \DB::table("db_ex_business.t_community_feeds")
			->where("app_id", '=', $this->app_id)
			->where("community_id", '=', $community_id)
			->where("feeds_state", '!=', 2)
			->whereRaw($whereRaw)
			->orderBy("created_at", "desc")
			->paginate(10);

		//获取每条动态的用户信息
		foreach ($dynamicList as $key => $dynamic) {
			$user_id = $dynamic->user_id;
			//在表t_community_user中查询用户的信息
			$user_info = $this->getCommunityUserInfo($user_id, $community_id);
			if ($user_info) {
				$dynamicList[ $key ]->nick_name = $user_info->nick_name;
			} else {
				$dynamicList[ $key ]->nick_name = '';
			}
		}
		//获取该社群的详情
		$communityInfo = $this->getCommunityInfo($community_id);

		$count_notices = $this->getNoticeCount($community_id);
		if ($communityInfo) {
			$communityInfo->count_notices = $count_notices;
		}

		return view('admin.communityOperate.dynamicList', compact('search_content', 'state', 'communityInfo', 'dynamicList', 'community_id'));
	}

	private function getCommunityUserInfo ($user_id, $community_id)
	{
		$user_info = \DB::table("db_ex_business.t_community_user")
			->where("app_id", '=', $this->app_id)
			->where("community_id", '=', $community_id)
			->where("user_id", '=', $user_id)
			->where("state", '=', 0)
			->first();

		return $user_info;
	}

	private function getCommunityInfo ($id)
	{
		//在表t_community中查询动态的详情
		$community_info = \DB::table("db_ex_business.t_community")
			->where("app_id", '=', $this->app_id)
			->where("id", '=', $id)
			->first();

		return $community_info;
	}

	private function getNoticeCount ($community_id)
	{

		$noticeList = \DB::table("db_ex_business.t_community_feeds")
			->where("app_id", '=', $this->app_id)
			->where("community_id", '=', $community_id)
			->where("is_notice", '=', 1)
			->where("feeds_state", '!=', 2)
			->get();
		$count      = count($noticeList);

		return $count;

	}

	/**
	 * 创建动态
	 * 参数:community_id(社群id)
	 */
	public function createDynamic ()
	{

		$community_id = Input::get("community_id");
		//查询该社群的群主,若有则允许发布动态,无则不允许发布动态
		$has_group_owner = $this->getCommunityRoomer($community_id);
		if (count($has_group_owner) == 0) {//该社群没有设置群主
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "该社群没有设置群主,不能创建动态"));
		}
		$type            = 0;
		$is_message_push = MessagePush::checkMessagePush($this->app_id);
		$is_set_temp     = MessagePush::isHadSetTemp($this->app_id);//判断是否设置行业类型
		// 查询已推送状态(数量)
		$date       = date('Y-m-d');
		$has_push   = DB::table('t_community_feeds')
			->where('app_id', $this->app_id)->where('community_id', $community_id)
			->where('user_type', 1)->whereIn('push_state', [1, 2])->where('created_at', 'like', "{$date}%")->count('id');
		$valid_push = 3 - $has_push ? 3 - $has_push : 0;

		return view('admin.communityOperate.manageDynamic', compact('type', 'community_id', 'is_set_temp', 'is_message_push', 'has_push', 'valid_push'));
	}

	private function getCommunityRoomer ($community_id)
	{
		//在表t_community_user中查询该社群的群主
		$roomerInfo = \DB::table("db_ex_business.t_community_user")
			->where("app_id", '=', $this->app_id)
			->where("community_id", '=', $community_id)
			->where("state", '=', 0)
			->where("type", '=', 1)
			->first();

		return $roomerInfo;
	}

	/**
	 * 编辑动态
	 * 参数:id(社群动态的主键id)
	 */
	public function editDynamic ()
	{

		$id           = Input::get("id");
		$dynamic_info = $this->getDynamicInfo($id);
		//查询该社群的群主,若有则允许发布动态,无则不允许发布动态
		$has_group_owner = $this->getCommunityRoomer($dynamic_info->community_id);
		if (count($has_group_owner) == 0) {//该社群没有设置群主
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "该社群没有设置群主,不能创建动态"));
		}

		$type = 1;

		return view('admin.communityOperate.manageDynamic', compact('type', 'dynamic_info'));
	}

	private function getDynamicInfo ($id)
	{
		//在表t_community_feeds中查询动态的详情
		$dynamic_info = \DB::table("db_ex_business.t_community_feeds")
			->where("app_id", '=', $this->app_id)
			->where("id", '=', $id)
			->first();

		return $dynamic_info;
	}

	/**
	 * 创建动态
	 * 参数:params
	 */
	public function uploadDynamic (Request $request)
	{
		$params = Input::get("params", '');

		$ret = $this->saveDynamicInfo($params, StringConstants::RESOURCE_ADD);

		if ($ret == '0') {
			return $this->result('新增动态成功!');
		} else {
			$msg = $ret;

			return response()->json(Utils::pack("0", StringConstants::Code_Failed, $msg));
		}
	}

	private function saveDynamicInfo ($params, $operator_type)
	{
		if (!array_key_exists('community_id', $params)) {
			return "保存失败,未上传关联的社群id";
		}
		if (array_key_exists('file_url', $params)) {
			//将http改为https
			$data               = explode('/', $params['file_url']);
			$data[0]            = 'https:';
			$params['file_url'] = implode('/', $data);
		}
		if (Utils::isEmptyString($params['community_id'])) {
			return "保存失败,请填写社群id!";
		}
		if (Utils::isEmptyString($params['title'])) {
			return "保存失败,请填写动态标题!";
		}
		if (Utils::isEmptyString($params['org_content'])) {
			return "保存失败,请填写动态内容!";
		}

		$params['content'] = ResContentComm::sliceUE($params['content']);
		if ($params['content'] == false) {
			//编辑器内容有问题 给前端返回提示信息并取消上传
			return "上传失败，复制的内容有问题，请尝试分开复制，或记录复制内容并联系技术小哥";
		}

		if ($operator_type == StringConstants::RESOURCE_ADD) {//新增动态
			$params['id']         = Utils::getUniId("d_") . random_int(1000, 9999);
			$params['app_id']     = $this->app_id;
			$params['feeds_type'] = StringConstants::DYNAMIC_TYPE_TEXTAREA;//富文本动态类型
			$params['created_at'] = Utils::getTime();
			$params['send_type']  = StringConstants::DYNAMIC_CHANNEL_TYPE_PC;//pc端发送
			$params['push_state'] = Input::get('push_state', '0');//获取当前服务号推送状态

			//查询该社群的群主信息
			$roomer = $this->getCommunityRoomer($params['community_id']);
			if ($roomer) {
				$params['user_id']   = $roomer->user_id;
				$params['user_type'] = StringConstants::DYNAMIC_USER_TYPE_ROOMER;
			}

			$result = \DB::table("db_ex_business.t_community_feeds")->insert($params);

			if ($result) {
				//该社群的动态数加一
				$id         = $params['community_id'];
				$result_add = \DB::update(" update db_ex_business.t_community set feeds_count = feeds_count+1 where app_id = '$this->app_id' and id='$id' limit 1");

			}
			$msg = "新增动态失败!";
		} else {//编辑动态
			$dynamic_id           = $params['id'];
			$params['updated_at'] = Utils::getTime();

			$result = \DB::table("db_ex_business.t_community_feeds")
				->where("app_id", '=', $this->app_id)
				->where("id", '=', $dynamic_id)
				->update($params);
			$msg    = "更新动态失败!dynamic_id:" . $dynamic_id;
		}

		if ($result) {
			return StringConstants::Code_Succeed;
		} else {
			return $msg;
		}
	}

	private function result ($data)
	{
		if (Utils::isEmptyString($data)) {
			return response()->json(Utils::pack("", StringConstants::Code_Failed, StringConstants::Msg_Failed));
		} else {
			return response()->json(Utils::pack($data));
		}
	}

	/**
	 * 编辑动态
	 * 参数:params
	 */
	public function updateDynamic ()
	{
		$params = Input::get("params", '');

		$ret = $this->saveDynamicInfo($params, StringConstants::RESOURCE_EDIT);

		if ($ret == '0') {
			return $this->result('编辑动态成功!');
		} else {
			$msg = $ret;

			return response()->json(Utils::pack("0", StringConstants::Code_Failed, $msg));
		}
	}

	//发送小纸条

	/**
	 * 动态详情
	 * 参数:id(社群动态id)
	 */
	public function dynamicDetail ()
	{
		$id = Input::get("id");
		//操作步骤:
		//1.获取动态的详细信息
		$dynamicInfo = $this->getDynamicInfo($id);
		//2.查询该动态的所有主评论(在评论表t_community_feeds_comment中)
		//3.查询每条主评论的所有附属评论信息
		if ($dynamicInfo) {

			$community_id    = $dynamicInfo->community_id;
			$feeds_id        = $id;
			$mainCommentList = $this->getCommentList($feeds_id, $community_id, StringConstants::COMMENT_TYPE_MAIN, '');
			foreach ($mainCommentList as $key => $mainComment) {
				//                //查询每条主评论的所有附属评论信息
				//                $subCommentList = $this->getCommentList($feeds_id,$community_id,StringConstants::COMMENT_TYPE_SUB,$mainComment->id);
				//                //获取每条评论的评论人和被评论人的昵称
				//                foreach ($subCommentList as $key2=>$subComment){
				//                    //查询评论人的昵称
				//                    $From_user_info = $this->getCommunityUserInfo($subComment->user_id,$community_id);
				//                    if($From_user_info){
				//                        $subCommentList[$key2]->From_nick_name = $From_user_info->nick_name;//评论人的昵称
				//                        $subCommentList[$key2]->From_wx_avatar = $From_user_info->wx_avatar;//评论人的头像
				//                    }else{
				//                        $subCommentList[$key2]->From_nick_name = '';
				//                        $subCommentList[$key2]->From_wx_avatar = '';
				//
				//                    }
				//                    //查询被评论人的昵称
				//                    $To_user_info = $this->getCommunityUserInfo($subComment->reply_user_id,$community_id);
				//                    if($To_user_info){
				//                        $subCommentList[$key2]->To_nick_name = $To_user_info->nick_name;//被评论人的昵称
				//                    }else{
				//                        $subCommentList[$key2]->To_nick_name = '';
				//
				//                    }
				//                }
				//
				//                $mainCommentList[$key]->subCommentList = $subCommentList;

				//查询主评论的评论人昵称
				$main_comment_user_info = $this->getCommunityUserInfo($mainComment->user_id, $community_id);

				if ($main_comment_user_info) {

					$mainCommentList[ $key ]->nick_name = $main_comment_user_info->nick_name;
					$mainCommentList[ $key ]->wx_avatar = $main_comment_user_info->wx_avatar;
				} else {
					$mainCommentList[ $key ]->nick_name = '';
					$mainCommentList[ $key ]->wx_avatar = '';
				}

				if ($mainComment->type == 1) {//附属评论,查询被回复的人的信息
					$To_comment_user_info = $this->getCommunityUserInfo($mainComment->reply_user_id, $community_id);
					if ($To_comment_user_info) {
						$mainCommentList[ $key ]->to_nick_name = $To_comment_user_info->nick_name;
						$mainCommentList[ $key ]->to_wx_avatar = $To_comment_user_info->wx_avatar;
					} else {
						$mainCommentList[ $key ]->to_nick_name = '';
						$mainCommentList[ $key ]->to_wx_avatar = '';
					}
				}
			}

			//获取该社群的详情
			$communityInfo = $this->getCommunityInfo($community_id);
			if ($communityInfo) {
				$dynamicInfo->community_name = $communityInfo->title;
			} else {
				$dynamicInfo->community_name = '';
			}

			//获取发动态人头像
			$publisherInfo = $this->getCommunityUserInfo($dynamicInfo->user_id, $community_id);
			if ($publisherInfo) {
				$dynamicInfo->publisher_wx_avatar = $publisherInfo->wx_avatar;
				$dynamicInfo->publisher_nick_name = $publisherInfo->nick_name;
			} else {
				$dynamicInfo->publisher_nick_name = '';
				$dynamicInfo->publisher_wx_avatar = '';
			}

			//获取社群群主信息
			$roomer = $this->getCommunityRoomer($community_id);
			if ($roomer) {
				$dynamicInfo->roomer_wx_avatar = $roomer->wx_avatar;
				$dynamicInfo->roomer_user_id   = $roomer->user_id;
			} else {
				$dynamicInfo->roomer_user_id   = '';
				$dynamicInfo->roomer_wx_avatar = '';
			}

			//获取动态点赞状态
			$dynamicPraiseInfo = $this->getDynamicPraiseInfo($dynamicInfo);
			if ($dynamicPraiseInfo) {
				$dynamicInfo->praise_state = $dynamicPraiseInfo->praise_state;//0-未点赞;1-已点赞
			} else {
				$dynamicInfo->praise_state = 0;//未点赞
			}

		}

		return view('admin.communityOperate.dynamicDetail', compact('dynamicInfo', 'mainCommentList'));
	}

	//查询动态的点赞记录

	private function getCommentList ($feeds_id, $community_id, $type, $mainCommentId)
	{

		$whereRaw = " 1=1 ";
		if ($type == StringConstants::COMMENT_TYPE_SUB) {//查询附属评论
			$whereRaw .= " and main_comment_id = " . $mainCommentId;
		}

		$commentList = \DB::table("db_ex_business.t_community_feeds_comment")
			->where("app_id", '=', $this->app_id)
			->where("community_id", '=', $community_id)
			->where("feeds_id", '=', $feeds_id)
			//            ->where("type",'=',$type)
			->where("state", '=', 0)
			->whereRaw($whereRaw)
			->orderBy("created_at", 'desc')
			->paginate(10);

		return $commentList;
	}

	//获取该社区的群公告数量

	private function getDynamicPraiseInfo ($dynamicInfo)
	{

		$id           = $dynamicInfo->id;
		$community_id = $dynamicInfo->community_id;
		//查询群主信息
		$roomerInfo = $this->getCommunityRoomer($community_id);
		if ($roomerInfo) {
			$user_id = $roomerInfo->user_id;
			$app_id  = $this->app_id;

			$result = \DB::table("db_ex_business.t_comment_praise")
				->where("app_id", '=', $app_id)
				->where("type", '=', 3)
				->where("record_id", '=', $community_id)
				->where("comment_id", '=', $id)
				->where("user_id", '=', $user_id)
				->first();

			return $result;
		} else {
			return 0;
		}
	}

	//查询社群动态的所有评论

	/**
	 * 改变动态的状态
	 * 参数:1-id(社群动态id)
	 * 2-is_chosen 0：普通状态  1：精选状态
	 * 3-feeds_state  0：可见  1：隐藏  2：删除
	 * 4-is_notice 0：普通状态  1：公告状态
	 */
	public function changeDynamicState ()
	{

		$id          = Input::get("id");
		$is_chosen   = Input::get("is_chosen", '-1');
		$feeds_state = Input::get("feeds_state", '-1');
		$is_notice   = Input::get("is_notice", '-1');

		if ($is_chosen != -1) {
			$params['is_chosen'] = $is_chosen;
		}
		if ($feeds_state != -1) {
			$params['feeds_state'] = $feeds_state;
			if ($feeds_state == 2) {
				$dynamicInfo = $this->getDynamicInfo($id);

				//该社群的动态数减一
				$result_add = \DB::update(" update db_ex_business.t_community set feeds_count = feeds_count-1 where app_id = '$this->app_id' and id='$dynamicInfo->community_id' limit 1");
			}
		}
		if ($is_notice != -1) {
			$params['is_notice'] = $is_notice;
			if ($is_notice == 1) {
				//判断该社群的公告数量大于2
				$dynamicInfo = $this->getDynamicInfo($id);
				if ($dynamicInfo) {

					$count_notice = $this->getNoticeCount($dynamicInfo->community_id);
					if ($count_notice >= 2) {
						return response()->json(Utils::pack("0", StringConstants::Code_Failed, "群公告数量达到了上线!"));
					}
				}
			}
		}
		$params['updated_at'] = Utils::getTime();

		$update = \DB::table("db_ex_business.t_community_feeds")
			->where('app_id', '=', $this->app_id)
			->where('id', '=', $id)
			->update($params);
		if ($update) {
			return $this->result("更新状态成功");
		} else {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "更新状态失败!"));
		}
	}

	//查询用户信息

	/**
	 * 评论动态
	 * 参数:
	 * 1-id(动态id)
	 * 2-comment_content
	 * 3-comment_type(0-主评论;1-附属评论)
	 * 4-comment_id(当comment_type=1时有值)
	 */
	public function commentDynamic ()
	{

		$id              = Input::get("id");
		$comment_content = Input::get("comment_content");
		$comment_type    = Input::get("comment_type", '-1');
		$comment_id      = Input::get("comment_id");
		//TODO:处理步骤
		//1.根据动态id在表t_community_feeds中查询动态的所属社群id
		//2.查询该社群的群主信息,若无则返回不能评论
		//3.根据comment_type的类型来处理要生成的评论字段,若为附属评论,则根据comment_id查询出该被评论的用户id

		if ($comment_type == '-1') {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "传入的评论类型有误"));
		}
		if (Utils::isEmptyString($comment_content)) {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "评论内容不能为空"));
		}

		$dynamicInfo = $this->getDynamicInfo($id);
		if ($dynamicInfo) {
			//查询社群的群主信息
			$roomer = $this->getCommunityRoomer($dynamicInfo->community_id);
			if ($roomer) {
				$params['comment']      = $comment_content;
				$params['app_id']       = $this->app_id;
				$params['community_id'] = $dynamicInfo->community_id;
				$params['feeds_id']     = $id;
				$params['user_id']      = $roomer->user_id;
				$params['type']         = $comment_type;
				$params['send_type']    = StringConstants::DYNAMIC_CHANNEL_TYPE_PC;//PC端评论
				$params['created_at']   = Utils::getTime();
				if ($comment_type == StringConstants::COMMENT_TYPE_SUB) {//附属评论

					//则根据comment_id查询出该被评论的用户id
					$commentInfo = $this->getCommentInfo($comment_id);
					if ($commentInfo) {
						$params['reply_user_id'] = $commentInfo->user_id;
						if ($commentInfo->type == StringConstants::COMMENT_TYPE_MAIN) {//被回复的为主评论
							$params['main_comment_id'] = $commentInfo->id;
						} else {
							$params['main_comment_id'] = $commentInfo->main_comment_id;
							//向主评论的人发送消息
							//查询被回复人的昵称
							$reply_user_info = $this->getCommunityUserInfo($params['reply_user_id'], $dynamicInfo->community_id);
							if ($reply_user_info) {
								$reply_user_nick_name = $reply_user_info->nick_name;
							} else {
								$reply_user_nick_name = '';
							}
							$comment_content = "回复" . $reply_user_nick_name . ":" . $comment_content;
							//查询发主评论的人
							$comment = $this->getCommentInfo($params['main_comment_id']);
							if ($comment) {
								$result = $this->notifyByMessage($id, $dynamicInfo->community_id, $comment_content, $comment->user_id, $params['user_id']);
							}
						}

						//向被评论的人发送消息
						$result = $this->notifyByMessage($id, $dynamicInfo->community_id, $comment_content, $params['reply_user_id'], $params['user_id']);

						if ($commentInfo->user_id == $roomer->user_id) {//被回复的评论为群主评论
							return response()->json(Utils::pack("0", StringConstants::Code_Failed, "评论失败,管理台不能回复群主信息"));
						}
					}
				} else {//主评论
					//向发布动态的人发送消息
					$result                  = $this->notifyByMessage($id, $dynamicInfo->community_id, $comment_content, $params['user_id'], $params['user_id']);
					$params['reply_user_id'] = $params['user_id'];
				}

				//往评论表t_community_feeds_comment中插入一条记录
				$result_insert = \DB::table("db_ex_business.t_community_feeds_comment")->insert($params);
				if ($result_insert) {//返回成功
					//该动态的评论数加一
					$result_update = \DB::update("update db_ex_business.t_community_feeds set comment_count = comment_count+1 where app_id = '$this->app_id' and id='$id' limit 1");

					return $this->result("评论成功");
				} else {//失败
					return response()->json(Utils::pack("0", StringConstants::Code_Failed, "评论失败"));
				}
			} else {
				return response()->json(Utils::pack("0", StringConstants::Code_Failed, "该社群没有设置群主,不能评论"));
			}
		} else {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "该社群动态信息不存在"));
		}

	}

	//根据社群id查询社群的详细信息

	private function getCommentInfo ($id)
	{
		$commentInfo = \DB::table("db_ex_business.t_community_feeds_comment")
			->where("app_id", '=', $this->app_id)
			->where("id", '=', $id)
			->first();

		return $commentInfo;
	}

	//查询评论信息

	private function notifyByMessage ($id, $community_id, $notify_content, $user_id, $send_user_id)
	{

		//        $commentInfo = $this->getCommentInfo($comment_id);

		if ($user_id == $send_user_id) {
			return 0;
		} else {
			if (!empty($notify_content)) {
				//  1、获取app_id，user_id， send_nick_name， content_clickable， skip_type， skip_target， source， src_id， type， content， state， send_at， created_at
				$data = [];

				$data['app_id'] = AppUtils::getAppID();
				$data['source'] = 1;
				$data['type']   = 0;
				$data['src_id'] = $id;//回复的评论id
				//根据用户id获取用户昵称
				//在表t_community_user中查询用户的信息
				$user_info = $this->getCommunityUserInfo($send_user_id, $community_id);
				if ($user_info) {
					$data['send_nick_name'] = $user_info->nick_name;
				} else {
					$data['send_nick_name'] = '';
				}
				//            $data['send_nick_name'] = "发送人昵称";
				$data['send_user_id']      = $send_user_id;
				$data['content']           = $notify_content;
				$data['state']             = 0;
				$data['content_clickable'] = "点击查看";
				$data['skip_type']         = 4;

				$url                 = Utils::getContentUrl(2, 7, $id, $community_id, '');
				$url                 = Utils::getExtraInfoContentUrl($url, 1);
				$data['skip_target'] = $url;

				$data['send_at']    = date('Y-m-d H:i:s', time());
				$data['created_at'] = date('Y-m-d H:i:s', time());

				$data['user_id'] = $user_id;
				$insert          = \DB::table("t_messages")->insertGetId($data);

				return $insert;
			} else {
				return "内容为空";
			}
		}
	}

	//查询社群的群主

	/**
	 * 查询社群的群主
	 * 参数:community_id
	 */
	public function queryCommunityRoomer ()
	{
		$community_id = Input::get("community_id");
		$roomerInfo   = $this->getCommunityRoomer($community_id);
		if ($roomerInfo) {//有群主
			return $this->result($roomerInfo);
		} else {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "该社群没有设置群主,不能创建动态"));
		}
	}

	//根据id查询动态的详细信息

	/**
	 * 删除评论
	 * 参数:1-id(评论id)
	 */
	public function deleteDynamicComment ()
	{
		$id         = Input::get("id");
		$comment_id = $id;

		//置位评论记录状态state为删除
		$params['state']      = StringConstants::COMMENT_STATE_DELETE;
		$params['updated_at'] = Utils::getTime();
		$result_delete        = \DB::table("db_ex_business.t_community_feeds_comment")
			->where("app_id", '=', $this->app_id)
			->where("id", '=', $id)
			->where("state", '!=', StringConstants::COMMENT_STATE_DELETE)
			->update($params);
		if ($result_delete) {
			//对应动态的评论数减一
			$commentInfo = $this->getCommentInfo($id);
			if ($commentInfo) {
				$id            = $commentInfo->feeds_id;
				$result_update = \DB::update("update db_ex_business.t_community_feeds set comment_count = comment_count-1 where app_id = '$this->app_id' and id='$id' limit 1");
			}

			//将小纸条中的该评论相关消息置位为删除
			$data['state']         = 1;//消息被撤回
			$result_delete_message = \DB::table("db_ex_business.t_messages")
				->where("src_id", '=', $comment_id)
				->where("app_id", '=', $this->app_id)
				->where("state", '!=', 1)
				->update($data);

			return $this->result("删除评论成功");
		} else {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "删除评论失败"));
		}
	}

	//保存动态信息

	/**
	 * 修改社群用户的状态
	 * 参数:1-community_id;2-user_id;3-state(0-移出黑名单;2-加入黑名单)
	 */
	public function changeUserState ()
	{
		$community_id = Input::get("community_id");
		$user_id      = Input::get("user_id");
		$state        = Input::get("state", '-1');

		//检测该用户是否是群主  如果是群主  则返回错误
		$type = \DB::table("db_ex_business.t_community_user")
			->where("app_id", '=', $this->app_id)
			->where("community_id", '=', $community_id)
			->where("user_id", '=', $user_id)
			->value('type');
		if ($type == 1)
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "操作失败"));

		if ($state != '-1') {
			$params['state'] = $state;
		}
		$params['updated_at'] = Utils::getTime();

		$result_update = \DB::table("db_ex_business.t_community_user")
			->where("app_id", '=', $this->app_id)
			->where("community_id", '=', $community_id)
			->where("user_id", '=', $user_id)
			->where("state", '!=', $state)
			->update($params);
		if ($result_update) {
			return $this->result("操作成功");
		} else {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "操作失败"));
		}

	}

	/**
	 * 动态点赞
	 * 参数:1-id(社群动态id)2-state(0-取消点赞;1-点赞)
	 */
	public function dynamicPraise ()
	{
		$id    = Input::get("id");
		$state = Input::get("state", '-1');

		if ($state == '-1') {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "操作失败"));
		}

		//查询动态信息
		$dynamicInfo = $this->getDynamicInfo($id);
		if ($dynamicInfo) {
			$community_id = $dynamicInfo->community_id;
			//查询群主信息
			$roomerInfo = $this->getCommunityRoomer($community_id);
			if ($roomerInfo) {
				$user_id = $roomerInfo->user_id;
				$app_id  = $this->app_id;
				//在点赞表t_comment_praise中生成或更新记录
				try {
					$params['app_id']       = $app_id;
					$params['type']         = 3;//社群
					$params['record_id']    = $community_id;//所属社群的id
					$params['comment_id']   = $id;//对应的动态id
					$params['user_id']      = $user_id;
					$params['praise_state'] = $state;
					$params['created_at']   = Utils::getTime();
					$result                 = \DB::table("db_ex_business.t_comment_praise")->insert($params);

				} catch (\Exception $e) {
					$params['updated_at']   = Utils::getTime();
					$params['praise_state'] = $state;
					$result                 = \DB::table("db_ex_business.t_comment_praise")
						->where("app_id", '=', $app_id)
						->where("type", '=', 3)
						->where("record_id", '=', $community_id)
						->where("comment_id", '=', $id)
						->where("user_id", '=', $user_id)
						->update($params);
				}

				//在表t_community_feeds中点赞数加、减1
				if ($state == 0) {//取消点赞
					$result_update = \DB::update("update db_ex_business.t_community_feeds set zan_num = zan_num-1 where app_id = '$this->app_id' and id='$id' limit 1");
				} else {//点赞
					$result_update = \DB::update("update db_ex_business.t_community_feeds set zan_num = zan_num+1 where app_id = '$this->app_id' and id='$id' limit 1");
				}

				return $this->result($result);
			} else {
				return response()->json(Utils::pack("0", StringConstants::Code_Failed, "该社群未设置群主"));
			}
		} else {
			return response()->json(Utils::pack("0", StringConstants::Code_Failed, "该动态不存在"));
		}
	}

	/****************************用户列表*************************/

	/**
	 * 获取社群的用户列表
	 * 参数:
	 * 1-community_id
	 * 2-user_type(0-全部用户;1-黑名单)
	 * 3-search_type(0-用户昵称;1-手机号)
	 * 4-search_content
	 */
	public function userList ()
	{

		$community_id   = Input::get("community_id");
		$user_type      = Input::get("user_type", '-1');
		$search_type    = Input::get("search_type", '-1');
		$search_content = Input::get("search_content", '');

		$whereRaw = " 1=1 ";
		if ($user_type == '1') {//黑名单
			$whereRaw .= " and state=2 ";
		}
		if (!Utils::isEmptyString($search_content)) {

			//            if($search_type == 1){//通过手机号查询用户

			$user_id_list = $this->getUserIdByPhone($search_content);
			$whereRaw     .= " and (user_id in (" . implode(',', $user_id_list) . ") or nick_name like '%" . $search_content . "%')";
			//            }
			//            if($search_type == 0){//通过用户昵称查询
			//                $whereRaw .=" and nick_name like '%".$search_content."%'";
			//            }
		}

		$userList = \DB::table("db_ex_business.t_community_user")
			->where("app_id", '=', $this->app_id)
			->where("community_id", '=', $community_id)
			->where("state", '!=', 1)//剔除被删除的用户
			->whereRaw($whereRaw)
			->orderBy("type", 'desc')
			->orderBy("created_at", "desc")
			->paginate(10);

		//查询用户的手机号(在表t_users)
		foreach ($userList as $key => $user) {
			$userInfo = \DB::table("db_ex_business.t_users")
				->where("app_id", '=', $this->app_id)
				->where("user_id", '=', $user->user_id)
				->first();
			if ($userInfo) {
				$userList[ $key ]->phone = $userInfo->phone;
			} else {
				$userList[ $key ]->phone = '';
			}
			//查询用户在社群的动态数
			$dynamicList                     = $this->getCommunityUserDynamicList($community_id, $user->user_id);
			$userList[ $key ]->dynamic_count = count($dynamicList);
		}

		//获取该社群的详情
		$communityInfo = $this->getCommunityInfo($community_id);

		return view('admin.communityOperate.userList', compact('userList', 'communityInfo', 'user_type', 'search_content'));
	}

	//查询用户在社群的动态数

	private function getUserIdByPhone ($phone)
	{
		$whereRaw = " phone like '%" . $phone . "%' ";

		$userList = \DB::table("db_ex_business.t_users")
			->where("app_id", '=', $this->app_id)
			->whereRaw($whereRaw)
			->get();

		$id_list = ['1'];
		if ($userList) {
			foreach ($userList as $key => $user) {
				$id_list[] = "'" . $user->user_id . "'";
			}
		}

		return $id_list;
	}

	//根据电话号码在表t_users中模糊查询用户

	private function getCommunityUserDynamicList ($community_id, $user_id)
	{

		$dynamicList = \DB::table("db_ex_business.t_community_feeds")
			->where("app_id", '=', $this->app_id)
			->where("community_id", '=', $community_id)
			->where("user_id", '=', $user_id)
			->where("feeds_state", '!=', 2)
			->get();

		return $dynamicList;
	}

	public function checkFeedsMessagePush ($community_id)
	{
		$app_id = AppUtils::getAppID();
		if (!$community_id) return response()->json(['code' => -2, 'msg' => '参数错误', 'data' => []]);

		$is_message_push = MessagePush::checkMessagePush($app_id);
		if ($is_message_push === false) return response()->json(['code' => -2, 'msg' => '个人版请开启模板消息推送开关', 'data' => []]);

		// 查询该社群下的今天已推送动态
		$date = date('Y-m-d');
		$info = DB::table('t_community_feeds')
			->where('app_id', $app_id)->where('community_id', $community_id)->where('user_type', 1)->whereIn('push_state', [1, 2])->where('created_at', 'like', "{$date}%")
			->orderBy('created_at', 'desc')
			->first();
		if ($info) {
			$time = time() - strtotime($info->created_at);
			if ($time <= 600) return response()->json(['code' => -1, 'msg' => '距离上次推送时间小于十分钟', 'data' => []]);
		}

		return response()->json(['code' => 0, 'msg' => '请求成功', 'data' => []]);
	}

	private function getStateRelation ($com_id, $p_id)
	{
		$res = \DB::table('t_pro_res_relation')
			->where('app_id', '=', $this->app_id)
			->where('product_id', '=', $p_id)
			->where('resource_id', '=', $com_id)
			->where('resource_type', '=', 7)
			->value('relation_state');

		return $res;
	}

	/* 弃用
	public function setting($id){
		if (!$id) return response()->json(['code'=>-1,'msg' => 'param is wrong']);

		$info = DB::table('t_community')->select('id','title','is_feeds_push','is_comment_push')->where('app_id',$this->app_id)->where('id',$id)->where('community_state','!=',2)->first();
		if (!$info) return response()->json(['code'=>-1,'msg' => '该社群不存在']);

		$info->is_industry = MessagePush::isHadSetTemp($this->app_id);


		return view('admin.communityOperate.functionSet',[
			'info'=>$info,
		]);
	}

	public function setFunction(Request $request){
		$this->validate($request,[
			'params.id' => 'required|string',
			'params.is_feeds_push'=>'required|boolean',
//            'params.is_comment_push' => 'required|boolean'
		]);

		$id = $request->input('params.id');
		$data['is_feeds_push'] = $request->input('params.is_feeds_push');
//        $data['is_comment_push'] = $request->input('params.is_comment_push');
		$data['updated_at'] = date('Y-m-d H:i:s',time());

		$info = DB::table('t_community')->select('is_feeds_push')
			->where('app_id',$this->app_id)->where('id',$id)->where('community_state','!=',2)->first();

		if (!$info) return response()->json(['code'=>-1,'msg' => '该社群不存在']);

		$update = DB::table('t_community')->where('app_id',$this->app_id)->where('id',$id)->update($data);

		if ($update){
			return response()->json(['code'=>0,'msg' => '更新成功']);
		}else {
			return response()->json(['code'=>-2,'msg' => 'db error']);
		}
	}*/

}