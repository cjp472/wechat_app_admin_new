<?php
/**
 * Created by PhpStorm.
 * User: bowuting
 * Date: 2017/7/10
 * Time: 下午2:40
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\Utils;
use App\Http\Controllers\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ChosenController extends Controller
{
	private $request;
	private $app_id;

	public function __construct (Request $request)
	{
		$this->request = $request;
		$this->app_id  = AppUtils::getAppID();
	}

	public function homepage ()
	{

		$page_type        = Input::get("page_type", "goods");    //goods， record
		$is_enable_chosen = \DB::table('t_distribute_config')
			->where('app_id', '=', $this->app_id)
			->value('is_enable_chosen');
		if ($page_type == 'goods') {
			$temp     = $this->getGoodsList();
			$data     = $temp[0];
			$count    = count($data);
			$classify = $temp[1];

			// 十点读书特殊逻辑 ---- 始终返回数量为1
			if ($this->app_id == 'appuAhZGRFx3075') $count = 1;
			return view('admin.chosen.homepage', compact("page_type", "is_enable_chosen", 'data', "count", 'classify'));
		} else if ($page_type == 'record') {
			$data_arr = $this->getRecordList('');
			$ListInfo = $data_arr[0];
			$search   = $data_arr[1];

			return view('admin.chosen.homepage', compact("page_type", "is_enable_chosen", 'ListInfo', 'search'));
		}
	}

	private function getGoodsList ()
	{
		$data = \DB::table('t_resource_chosen_middle')
			->select('resource_id', 'img_url_compressed', 'resource_name', 'resource_type', 'price', 'distribute_content_url', 'is_chosen', 'distribute_content')
			->where('app_id', '=', $this->app_id)
			->where('is_enable_chosen', '=', 1)
			->get();

		if (count($data) > 0) {
			foreach ($data as $k => $v) {
				$percent        = $this->getDistributePercent($v->resource_id, $v->resource_type, '');
				$v->max_precent = $percent['max'];    //0 说明没有设置
				$v->min_precent = $percent['min'];    //0 说明没有设置
				$percent_all    = $this->getDistributePercent($v->resource_id, $v->resource_type, 'all');
				if (count($percent_all) == 3) {
					$distribute['start1']   = $percent_all[0]->start_order_num;
					$distribute['end1']     = $percent_all[0]->end_order_num;
					$distribute['percent1'] = $percent_all[0]->distribute_percent;
					$distribute['start2']   = $percent_all[1]->start_order_num;
					$distribute['end2']     = $percent_all[1]->end_order_num;
					$distribute['percent2'] = $percent_all[1]->distribute_percent;
					$distribute['start3']   = $percent_all[2]->start_order_num;
					$distribute['end3']     = '以上';
					$distribute['percent3'] = $percent_all[2]->distribute_percent;
					$v->distribute_data     = $distribute;
				} else {
					$v->distribute_data = [];
				}
				$classify_res = $this->getClassifyName($v->resource_id, $v->resource_type);
				if ($classify_res) {
					$v->classify_name = $classify_res->name;
					$v->classify_id   = $classify_res->classify_id;
				} else {
					$v->classify_name = '';
					$v->classify_id   = '';
				}

				if ($v->resource_type == 6) { //会员
					$v->period = \DB::table('t_pay_products')
						->where('app_id', '=', $this->app_id)
						->where('id', '=', $v->resource_id)
						->value('period');
				}
				if (Utils::isEmptyString($v->distribute_content)) $v->distribute_content_state = 0;
				else {
					$v->distribute_content_state = 1;
					$v->content_qrcode_url       = env('H5B') . '/mobile_distribute_content_new?app_id=' . $this->app_id . '&resource_id=' . $v->resource_id . '&resource_type=' . $v->resource_type;
				}
				$v->purchase_count = \DB::table('t_distribute_resource')
					->where('app_id', '=', $this->app_id)
					->where('resource_id', '=', $v->resource_id)
					->where('resource_type', '=', $v->resource_type)
					->sum('distribute_order_num');
			}

		}

		$classify = \DB::table('t_classify')
			->select('id', 'name')
			->where('state', '=', 0)
			->whereNotIn('id', [1, 2, 3])
			->get();

		return [$data, $classify];
	}

	private function getDistributePercent ($resource_id, $resource_type, $all_data = '')
	{
		$res = \DB::table('t_xiaoe_app_distribute')
			->where('app_id', '=', $this->app_id)
			->where('resource_id', '=', $resource_id)
			->where('resource_type', '=', $resource_type)
			->orderBy('updated_time', 'desc')
			->orderBy('start_order_num', 'asc')
			->take(3)
			->get();

		if ($all_data == 'all') {
			if (count($res) != 3)
				return [];

			return $res;
		}

		if (count($res) == 3) {
			$percent['min'] = $res[0]->distribute_percent;
			$percent['max'] = $res[2]->distribute_percent;
		} else {
			$percent['min'] = 0;
			$percent['max'] = 0;
		}

		return $percent;

	}

	/*
	 * 推广文案
	 */

	private function getClassifyName ($resource_id, $resource_type)
	{
		$res = \DB::table('t_cla_res_relation')
			//                ->select('t_classify.name')
			->join('t_classify', 't_cla_res_relation.classify_id', '=', 't_classify.id')
			->where('app_id', '=', $this->app_id)
			->where('resource_id', '=', $resource_id)
			->where('resource_type', '=', $resource_type)
			->where('t_cla_res_relation.state', '=', 0)
			->pluck('t_classify.name');
		//            ->get();
		//        return $res;

		$res = \DB::table('t_cla_res_relation')
			->select('classify_id', 't_classify.name')
			->join('t_classify', 't_cla_res_relation.classify_id', '=', 't_classify.id')
			->where('app_id', '=', $this->app_id)
			->where('resource_id', '=', $resource_id)
			->where('resource_type', '=', $resource_type)
			->where('t_cla_res_relation.state', '=', 0)
			->whereNotIn('t_cla_res_relation.classify_id', [1, 2, 3])
			->orderBy('t_cla_res_relation.updated_at', 'desc')
			->first();

		return $res;
	}

	//提交文案

	public function getRecordList ($search_content)
	{
		$where = "app_id = " . "'" . $this->app_id . "'" . " and share_type = 5 and source = 1";

		//根据商品名称查询
		if (!Utils::isEmptyString($search_content)) {
			$where .= " and distribute_name like '%$search_content%'";
		}
		$ListInfo = DB::connection('mysql')->table('t_distribute_detail')
			->select('order_id', 'distribute_name', 'price', 'share_user_id', 'distribute_percent', 'distribute_price', 'created_at', 'status', 'resource_type', 'product_id', 'payment_type')
			->whereRaw($where)
			->orderBy('created_at', 'desc')
			->paginate(10);

		$user_id   = [];
		$user_info = [];

		if ($ListInfo) {
			foreach ($ListInfo as $v) {
				$user_id[] = $v->share_user_id;

			}
			$user_id = array_unique($user_id);

			//查询并插入对应的用户数据
			if ($user_id) {
				$user_data = DB::table('t_distribute_user')
					->select('wx_name', 'user_id')
					->whereIn('user_id', $user_id)
					->get();
				if ($user_data) {
					foreach ($user_data as $v) {
						$user_info[ $v->user_id ] = $v;
					}
					foreach ($ListInfo as $v) {
						if (array_key_exists($v->share_user_id, $user_info)) {
							$v->wx_name = $user_info[ $v->share_user_id ]->wx_name ? $user_info[ $v->share_user_id ]->wx_name : '未知';
						} else {
							$v->wx_name = '这个人很懒 还没取名字';
						}
					}
				}
			}
			//查询并插入对应的商品图片
			foreach ($ListInfo as $v) {
				if ($v->payment_type == 3) {
					$info = $this::getPayProductsInfo($v->product_id);
				} else if ($v->payment_type == 2) {
					$info = Utils::getResourceInfo($v->product_id, $v->resource_type);
				}

				if ($info) {
					$v->img_url_compressed = $info->img_url_compressed;
				} else {
					$v->img_url_compressed = '';
				}
			}
		}

		return [$ListInfo, $search_content];
	}

	private static function getPayProductsInfo ($id)
	{
		$app_id = AppUtils::getAppID();

		$Info = \DB::table("db_ex_business.t_pay_products")
			->where('app_id', '=', $app_id)
			->where('id', '=', $id)
			->first();

		return $Info;
	}

	public function getGoodsListPage ()
	{
		$temp     = $this->getGoodsList();
		$data     = $temp[0];
		$count    = count($data);
		$classify = $temp[1];

		// 十点读书特殊逻辑 ---- 始终返回数量为1
		if ($this->app_id == 'appuAhZGRFx3075') $count = 1;
		return view('admin.chosen.distributeGoodsList', compact("data", "count", "classify"));
	}

	public function mangeContent ()
	{

		$resource_id   = Input::get('resource_id', '');
		$resource_type = Input::get('resource_type', '');

		if (Utils::isEmptyString($resource_type) || Utils::isEmptyString($resource_id))
			return ['您的页面被外星人偷走了'];

		$distribute_info = \DB::table('t_resource_chosen_middle')
			->select('distribute_title', 'distribute_content')
			->where('app_id', '=', $this->app_id)
			->where('resource_id', '=', $resource_id)
			->where('resource_type', '=', $resource_type)
			->first();

		if ($distribute_info) {
			$content = $distribute_info->distribute_content;
			$title   = $distribute_info->distribute_title;
		} else {
			$content = '';
			$title   = '';
		}

		if (Utils::isEmptyString($content)) $type = 0;
		else $type = 1;

		return view('admin.chosen.manageDistributeContent', compact('type', 'content', 'title'));
	}

	//商品设置分类

	public function commitContent ()
	{
		$resource_id   = Input::get('resource_id', '');
		$resource_type = Input::get('resource_type', '');
		$content       = Input::get('distribute_content', '');
		$title         = Input::get('distribute_title', '');

		if (Utils::isEmptyString($resource_type) || Utils::isEmptyString($resource_id) || Utils::isEmptyString($content) || Utils::isEmptyString($title))
			return ['code' => -1024, 'msg' => 'params is required'];

		$res = \DB::table('t_resource_chosen_middle')
			->where('app_id', '=', $this->app_id)
			->where('resource_id', '=', $resource_id)
			->where('resource_type', '=', $resource_type)
			->update(['distribute_content' => $content, 'distribute_title' => $title, 'updated_at' => Utils::getTime()]);
		if (!$res) return ['code' => 1064, 'msg' => 'db error'];

		return ['code' => 0, 'msg' => 'ok'];
	}

	//添加精选商品

	public function searchResource ()
	{
		$search   = Input::get('search', '');
		$whereRaw = "app_id = '" . $this->app_id . "' and state = 0 and price > 0 and is_distribute = 0 ";
		if (!Utils::isEmptyString($search))
			$whereRaw .= " and name like '%" . $search . "%'";

		$middle_resource_id_arr = \DB::table('t_resource_chosen_middle')
			->where('app_id', '=', $this->app_id)
			->whereIn('resource_type', [5, 6])
			->pluck('resource_id');

		$resource = \DB::table('t_pay_products')
			->select('id', 'is_member', 'img_url_compressed', 'name', 'created_at')
			->whereRaw($whereRaw)
			->whereNotIn('id', $middle_resource_id_arr)
			->get();
		foreach ($resource as $v) {
			if ($v->is_member == 1) $v->type = 6;
			else $v->type = 5;
		}

		return $resource;

	}

	public function setClassify ()
	{
		$class_id      = Input::get('class_id', '');
		$resource_id   = Input::get('resource_id', '');
		$resource_type = Input::get('resource_type', '');

		$old = \DB::table('t_cla_res_relation')
			->where('app_id', '=', $this->app_id)
			->where('resource_id', '=', $resource_id)
			->where('resource_type', '=', $resource_type)
			->where('classify_id', '=', $class_id)
			->first();

		\DB::beginTransaction();             // 开启事务

		if ($old) { // 如果旧的关系存在 且状态非0  则更新
			if ($old->state != 0) {
				$res = \DB::table('t_cla_res_relation')
					->where('app_id', '=', $this->app_id)
					->where('resource_id', '=', $resource_id)
					->where('resource_type', '=', $resource_type)
					->where('classify_id', '=', $class_id)
					->update(['state' => 0, 'updated_at' => Utils::getTime()]);
				if (!$res) return ['code' => 1064, 'msg' => 'db error'];
			}
		} else {  //如果旧的关系不存在 则直接插入
			$data['id']            = Utils::getUniId('c_');
			$data['classify_id']   = $class_id;
			$data['app_id']        = $this->app_id;
			$data['state']         = 0;
			$data['created_at']    = Utils::getTime();
			$data['updated_at']    = Utils::getTime();
			$data['resource_type'] = $resource_type;
			$data['resource_id']   = $resource_id;
			$res                   = \DB::table('t_cla_res_relation')
				->insert($data);
			if (!$res) return ['code' => 1064, 'msg' => 'db error'];
		}

		$res2 = \DB::table('t_cla_res_relation')
			->where('app_id', '=', $this->app_id)
			->where('resource_id', '=', $resource_id)
			->where('resource_type', '=', $resource_type)
			->whereNotIn('classify_id', [1, 2, 3])
			->where('classify_id', '!=', $class_id)
			->update(['state' => 2, 'updated_at' => Utils::getTime()]);
		if ($res2 < 0) {
			\DB::rollBack();

			return ['code' => 10642, 'msg' => 'db error'];
		}
		\DB::commit();

		return ['code' => 0, 'msg' => 'ok'];

	}

	public function addResourceChosen ()
	{
		$params = Input::get('params', []);
		$new_count = count($params);
		if ($new_count < 1) return ['code' => 1, 'msg' => 'no data'];


		$old_count = \DB::table('t_resource_chosen_middle')
			->where('app_id',  $this->app_id)
			->where('is_chosen', 1)
			->count();

		// 十点读书特殊逻辑 ---- 去掉20个限制
		if ($this->app_id != 'appuAhZGRFx3075'){
			if ($new_count + $old_count > 20)
				return ['code' => 2, 'msg' => 'the chosen sum of resources > 20'];
		}

		$data                       = [];
		$app_info                   = AppUtils::getAppConfInfo($this->app_id);
		$insert['app_name']         = $app_info->wx_app_name;
		$insert['is_enable_chosen'] = 1;
		$insert['created_at']       = Utils::getTime();
		$insert['updated_at']       = Utils::getTime();

		foreach ($params as $k => $v) {
			//先看这个id在中间表有没有，有的话则更新  无则插入
			$old = \DB::table('t_resource_chosen_middle')
				->where('app_id', '=', $this->app_id)
				->where('resource_type', '=', $v['type'])
				->where('resource_id', '=', $v['id'])
				->first();
			if ($old) {
				if ($old->is_enable_chosen != 1) {
					// 则更新该字段：is_enable_chosen
					$res = \DB::table('t_resource_chosen_middle')
						->where('app_id', '=', $this->app_id)
						->where('resource_type', '=', $v['type'])
						->where('resource_id', '=', $v['id'])
						->update(['is_enable_chosen' => 1, 'updated_at' => Utils::getTime()]);
					if (!$res) return ['code' => 1064, 'msg' => 'db error'];
				}
			} else {
				$res_info = \DB::table('t_pay_products')
					//                ->select('app_id','id as resource_id',\DB::raw($v['type'].' as resource_type'),$title_name . ' as resource_name',$price_name . ' as price','img_url','img_url_compressed')
					->select('app_id', 'id as resource_id', \DB::raw($v['type'] . ' as resource_type'), 'name as resource_name', 'price as price', 'img_url', 'img_url_compressed', 'purchase_count')
					->where('app_id', '=', $this->app_id)
					->where('id', '=', $v['id'])
					->first();
				$data[]   = array_merge($insert, (array)$res_info);
			}
		}
		//                $res = \DB::table('t_resource_chosen_middle')
		//                        ->insert($data);
		//                if(!$res) return ['code' => 11064,'msg' => 'resource insert error'];
		//                unset($data);
		if (count($data) > 0) {
			$res = \DB::table('t_resource_chosen_middle')
				->insert($data);
			if (!$res) return ['code' => 1064, 'msg' => 'resource insert error'];
		}

		return ['code' => 0, 'msg' => 'ok'];
	}

	//获取商品的梯度比例

	public function data_1 ()
	{
		$data = \DB::table('t_resource_chosen_middle')
			->select('app_id', 'resource_id', 'resource_type', 'img_url')
			->get();
		$i    = 0;
		foreach ($data as $k => $v) {
			if ($v->img_url == '') {

				switch ($v->resource_type) {
					case 1:
						$table = 't_image_text';
						break;
					case 2:
						$table = 't_audio';
						break;
					case 3:
						$table = 't_video';
						break;
					case 4:
						$table = 't_alive';
						break;
					case 5:
						$table = 't_pay_products';
						break;
					case 6:
						$table = 't_pay_products';
						break;
				}
				$img['img_url']            = \DB::table($table)
					->where('app_id', '=', $v->app_id)
					->where('id', '=', $v->resource_id)
					->value('img_url');
				$img['img_url_compressed'] = \DB::table($table)
					->where('app_id', '=', $v->app_id)
					->where('id', '=', $v->resource_id)
					->value('img_url_compressed');

				$res = \DB::table('t_resource_chosen_middle')
					->where('app_id', '=', $v->app_id)
					->where('resource_type', '=', $v->resource_type)
					->where('resource_id', '=', $v->resource_id)
					->update($img);
				$i   += 1;
				if (!$res) return ['msg' => $i . '难受 出错了！'];

				echo $i . '成功';
			}

		}

		return ['msg' => '舒服 操作成功'];
	}

	//设置商品的梯度比例

	public function data_2 ()
	{

		$data = \DB::table('t_resource_chosen_middle')
			->select('app_id', 'resource_id', 'resource_type')
			->where('is_chosen', '=', 1)
			->get();
		echo '中间表上架有' . count($data) . '条数据' . "<br>";
		$data_2 = \DB::table('t_xiaoe_app_distribute')
			->distinct()
			->pluck('app_id');
		$data_3 = \DB::table('t_resource_chosen_middle')
			->select('app_id', 'resource_id', 'resource_type')
			->whereIn('app_id', $data_2)
			->where('is_chosen', '=', 1)
			->get();
		echo "其中原有业务已设置梯度的有" . count($data_3) . '条' . "<br>";

		echo '开始复制数据' . '<br>';
		$insert_2 = [];

		foreach ($data_3 as $v) {
			$insert_1 = \DB::table('t_xiaoe_app_distribute')
				->where('app_id', '=', $v->app_id)
				->get();
			foreach ($insert_1 as $one) {
				$one->resource_id   = $v->resource_id;
				$one->resource_type = $v->resource_type;
				$one->created_at    = Utils::getTime();
				$one->updated_time  = Utils::getTime();
				$one->distribute_id = Utils::getUniId('xd_');
				$insert_2[]         = (array)$one;
			}
		}
		$count = count($insert_2);
		echo '一共产生' . $count . '条数据';

		$res = $res = \DB::table('t_xiaoe_app_distribute')
			->insert($insert_2);
		if ($res) echo '成功插入' . $count . '~~~' . '<br>';
		else echo '失败了' . '<br>';

		exit;
	}

	// 小鹅通内容分销打开开关

	public function getXiaoeDistribute ()
	{
		$resource_id   = Input::get('resource_id');
		$resource_type = Input::get('resource_type');
		if (Utils::isEmptyString($resource_id) || Utils::isEmptyString($resource_type))
			return ['code' => -1024, 'msg' => ' params is required'];

		$res = $this->getDistributePercent($resource_id, $resource_type, 'all');
		if (count($res) == 3) {
			$data['start_1']  = $res[0]->start_order_num;
			$data['end_1']    = $res[0]->end_order_num;
			$data['percent1'] = $res[0]->distribute_percent;
			$data['start_2']  = $res[1]->start_order_num;
			$data['end_2']    = $res[1]->end_order_num;
			$data['percent2'] = $res[1]->distribute_percent;
			$data['start_3']  = $res[2]->start_order_num;
			$data['end_3']    = '以上';
			$data['percent3'] = $res[2]->distribute_percent;

			$data['edit'] = 'edit';
		} else {
			$data['edit'] = '';
		}

		return ['code' => 0, 'msg' => 'ok', 'data' => $data];
	}

	public function setXiaoeDistribute ()
	{

		$data1         = Input::get('data1');
		$data2         = Input::get('data2');
		$data3         = Input::get('data3');
		$resource_id   = Input::get('resource_id');
		$resource_type = Input::get('resource_type');
		$edit          = Input::get('edit', '');

		//        return ['code' => 4 , ];

		if (Utils::isEmptyString($resource_id) || Utils::isEmptyString($resource_type))
			return ['code' => -1024, 'msg' => 'params is required'];

		$data1['start_order_num'] = 1;
		$data3['end_order_num']   = env('MYSQL_INT_MAX_VALUE');

		//数据校验
		if (($data2['start_order_num'] != $data1['end_order_num'] + 1) || ($data3['start_order_num'] != $data2['end_order_num'] + 1))
			return ['code' => 1, 'msg' => 'num is not correct'];  // 区间填写错误

		if (($data1['distribute_percent'] <= 0) || ($data2['distribute_percent'] < $data1['distribute_percent']) || ($data3['distribute_percent'] < $data2['distribute_percent']))
			return ['code' => 2, 'msg' => 'percent is not correct'];  // 分成比例填写错误

		switch ($resource_type) {
			case 0:
				$table_name = "t_pay_products";
				break;
			case 1:
				$table_name = "t_image_text";
				break;
			case 2:
				$table_name = "t_audio";
				break;
			case 3:
				$table_name = "t_video";
				break;
			case 4:
				$table_name = "t_alive";
				break;
			case 5:
				$table_name = "t_pay_products";
				break;
			case 6:
				$table_name = "t_pay_products";
				break;
			default:
				$table_name = "";
		}

		$distribute_info    = \DB::table($table_name)
			->select('has_distribute', 'first_distribute_default', 'first_distribute_percent')
			->where('app_id', '=', $this->app_id)
			->where('id', '=', $resource_id)
			->first();
		$distribute_percent = 0;
		if ($distribute_info->has_distribute == 1) {
			if ($distribute_info->first_distribute_default == 1)
				$distribute_percent = $distribute_info->first_distribute_percent;
			else
				$distribute_percent = \DB::table('t_distribute_config')
					->where('app_id', '=', $this->app_id)
					->value('distribute_percent');
		}

		if ($distribute_percent > $data1['distribute_percent'])
			return ['code' => 4, 'msg' => 'xiaoe distribute must > default distribute', 'data' => ['distribute_percent' => $distribute_percent, 'distribute_default' => $distribute_info->first_distribute_default]];

		// 三个数据公有的三个值
		if ($edit != 'edit') {   //非更新  新加
			$data['app_id']        = $this->app_id;
			$data['resource_id']   = $resource_id;
			$data['resource_type'] = $resource_type;
			$data['created_at']    = Utils::getTime();

			$data1 = array_add($data1, 'distribute_id', Utils::getUniId('xd_'));
			$data2 = array_add($data2, 'distribute_id', Utils::getUniId('xd_'));
			$data3 = array_add($data3, 'distribute_id', Utils::getUniId('xd_'));
		}

		$data['updated_time'] = Utils::getTime();

		$data1 = array_merge($data1, $data);
		$data2 = array_merge($data2, $data);
		$data3 = array_merge($data3, $data);

		if ($edit == 'edit') {
			$distribute_id_arr = \DB::table('t_xiaoe_app_distribute')
				->where('app_id', '=', $this->app_id)
				->where('resource_id', '=', $resource_id)
				->where('resource_type', '=', $resource_type)
				->orderBy('start_order_num', 'asc')
				->pluck('distribute_id');
			\DB::beginTransaction();             // 开启事务

			$res1 = \DB::table('t_xiaoe_app_distribute')
				->where('distribute_id', $distribute_id_arr[0])
				->update($data1);
			if (!$res1) return ['code' => 1064, 'msg' => 'res1 db error'];

			$res2 = \DB::table('t_xiaoe_app_distribute')
				->where('distribute_id', $distribute_id_arr[1])
				->update($data2);
			if (!$res2) {
				\DB::rollBack();

				return ['code' => 1064, 'msg' => 'res2 db error'];
			}

			$res3 = \DB::table('t_xiaoe_app_distribute')
				->where('distribute_id', $distribute_id_arr[2])
				->update($data3);
			if (!$res3) {
				\DB::rollBack();

				return ['code' => 1064, 'msg' => 'res3 db error'];
			}

			\DB::commit(); //提交事务

			return ['code' => 0, 'msg' => 'ok'];

		} else {

			$old_distribute = DB::table('t_xiaoe_app_distribute')
				->where('app_id', '=', $this->app_id)
				->where('resource_id', '=', $resource_id)
				->where('resource_type', '=', $resource_type)
				->get();
			if (count($old_distribute) >= 3)
				return ['code' => 8, 'msg' => '该商品已经设置比例了'];

			$res = \DB::table('t_xiaoe_app_distribute')
				->insert([$data1, $data2, $data3]);
			if (!$res) return ['code' => 1064, 'msg' => 'db error'];

			return ['code' => 0, 'msg' => 'ok'];
		}
	}

	public function chosenEnable ()
	{
		$is_enable_chosen = Input::get('is_enable_chosen');
		if (Utils::isEmptyString($is_enable_chosen))
			return ['code' => 1, 'msg' => 'is_enable_chosen is required'];

		if ($is_enable_chosen != '1')
			return ['code' => 4, 'msg' => 'the chosen is not can off'];

		//这里的关联性不是特别强  没有用事务
		$res0 = DB::connection('mysql_config')->table('t_app_module')
			->where('app_id', $this->app_id)
			->update(['has_distribute' => 1, 'updated_at' => Utils::getTime()]);
		if (!$res0) return ['code' => 1064, 'msg' => $res0 . ' res0 db error'];

		$old = \DB::table('t_distribute_config')
			->where('app_id', '=', $this->app_id)
			->first();
		if ($old) {
			$res = \DB::table('t_distribute_config')
				->where('app_id', '=', $this->app_id)
				->update(['is_enable_chosen' => $is_enable_chosen, 'updated_at' => Utils::getTime()]);
		} else { //分销配置还没有该记录  插入一条
			$data['app_id']                      = $this->app_id;
			$data['distribute_percent']          = 50;
			$data['superior_distribute_percent'] = 50;
			$data['created_at']                  = Utils::getTime();
			$data['updated_at']                  = Utils::getTime();
			$data['is_enable_chosen']            = 1;
			$res                                 = \DB::table('t_distribute_config')
				->insert($data);
		}

		if (!$res) return ['code' => 1064, 'msg' => $res . ' res db error'];

		return ['code' => 0, 'msg' => 'ok', 'data' => ['is_enable_chosen' => $is_enable_chosen]];
	}

	//获取专栏、会员信息

	/**函数名：getRecordsList
	 * 作用：展示推广订单列表
	 * 参数：
	 * $request
	 * $search
	 * 返回值：
	 * 作者：lyric
	 * 时间：2017/07/10  17:52
	 */
	public function getRecordListPage (Request $request)
	{
		//获取筛选条件
		$search = $request->input('search', '');

		$data_arr = $this->getRecordList($search);
		$ListInfo = $data_arr[0];
		$search   = $data_arr[1];

		//        return $ListInfo;
		return view('admin.chosen.distributeRecordList', compact('ListInfo', 'search'));
	}

	// 获得商品的推广员 分销信息

	public function getGoodsDistributeInfo ($resource_type, $resource_id)
	{
		// 获得该资源的分销信息
		switch ($resource_type) {
			case '1':
				$sql = "select id,has_distribute,first_distribute_default,first_distribute_percent from t_image_text where app_id = ? and display_state = 0 and id=?";
				break;
			case '2':
				$sql = "select id,has_distribute,first_distribute_default,first_distribute_percent from t_audio where app_id = ? and audio_state = 0 and id=?";
				break;
			case '3':
				$sql = "select id,has_distribute,first_distribute_default,first_distribute_percent from t_video where app_id = ? and video_state = 0 and id = ?";
				break;
			case '4':
				$sql = "select id,has_distribute,first_distribute_default,first_distribute_percent from t_alive where app_id = ? and state = 0 and id = ?";
				break;
			case '5':
				$sql = "select id,has_distribute,first_distribute_default,first_distribute_percent from t_pay_products where app_id = ? and state = 0 and id = ?";
				break;
			case '6':
				$sql = "select id,has_distribute,first_distribute_default,first_distribute_percent from t_pay_products where app_id = ? and state = 0 and is_member = 1 and id = ?";
				break;
			default:
				$sql = "";
		}

		if ($sql) {
			$res_info = DB::select($sql, [$this->app_id, $resource_id]);
			if ($res_info && count($res_info) === 1) {
				$res_info = $res_info[0];
			} else {
				return response()->json(['code' => -1, 'msg' => '无效的资源信息', 'data' => '']);
			}
		} else {
			return response()->json(['code' => -1, 'msg' => '无效的资源类型', 'data' => '']);
		}

		// 如果使用默认比例。查询分销配置表
		if ($res_info->first_distribute_default === 0) {
			$res_info->first_distribute_percent = DB::table('t_distribute_config')->where('app_id', $this->app_id)->value('distribute_percent');
		}

		return response()->json(['code' => 0, 'msg' => '请求成功', 'data' => $res_info]);
	}

}