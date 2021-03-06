<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\ExcelUtils;
use App\Http\Controllers\Tools\ResContentComm;
use App\Http\Controllers\Tools\Utils;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use DB;
use Illuminate\Support\Facades\Input;

class DistributionController extends Controller
{
	private $app_id;

	public function __construct ()
	{
		$this->app_id = AppUtils::getAppID();
	}

	//index
	public function index ()
	{
		// 查询配置记录
		$switch = DB::connection('mysql_config')->table('t_app_module')
			->where('app_id', $this->app_id)
			->value('has_distribute');

		$distribute_data = DB::connection('mysql')->table('t_distribute_config')
			->where('app_id', $this->app_id)
			->first();
		if ($distribute_data === null) {
			// 创建一条配置记录
			$data['app_id']                      = $this->app_id;
			$data['distribute_percent']          = -1;
			$data['superior_distribute_percent'] = -1;
			$data['has_recruit']                 = 1;

			$insert = DB::connection('mysql')->table('t_distribute_config')
				->insert($data);
		}

		return view('admin.marketing.saler', [
			'switch' => $switch,
		]);
	}

	// 总控制开关
	public function baseSwitch (Request $request)
	{
		$switch = $request->input('switch', '');
		if (Utils::isEmptyString($switch))
			return response()->json(['code' => -2, 'msg' => 'switch is required']);

		$is_enable_chosen = \DB::table('t_distribute_config')
			->where('app_id', '=', $this->app_id)
			->value('is_enable_chosen');
		if ($is_enable_chosen == 1) return ['code' => -4, 'msg' => 'the chosen is on'];

		// 查询数据库信息
		$old_switch = DB::connection('mysql_config')->table('t_app_module')
			->where('app_id', $this->app_id)
			->value('has_distribute');
		if ($switch == $old_switch)
			return response()->json(['code' => -1, 'msg' => '数据错误，无法更新开关']);

		DB::beginTransaction();
		$res = DB::table('db_ex_config.t_app_module')
			->where('app_id', $this->app_id)
			->update(['has_distribute' => $switch, 'updated_at' => Utils::getTime()]);
		if (!$res) return ['code' => 1064, 'msg' => 'db error'];

		$res2 = DB::table('t_distribute_config')
			->update(['on_off_time' => Utils::getTime()]);
		if (!$res2) {
			\DB::rollBack();

			return ['code' => 1064, 'msg' => 'db error'];
		}

		DB::commit();

		return ['code' => 0, 'msg' => 'ok'];
	}

	/*********************设置**********************/
	public function set ()
	{
		// 获取用户设置
		$info = DB::connection('mysql')->table('t_distribute_config')
			->where('app_id', $this->app_id)
			->first();

		if ($info->period) {
			$info->period = $info->period / 24 / 60;
		}

		return view('admin.marketing.salerSet', [
			'info' => $info,
		]);
	}

	// 修改设置信息
	public function setEdit (Request $request)
	{
		// 验证器验证传过来的数据
		$this->validate($request, [
			'has_choose'       => 'required|boolean', //添加是否上架内容分销 by Kris 2017.06.14
			'recruit'          => 'required|boolean',
			'audit'            => 'required|boolean',
			'period'           => 'required|integer|min:1|max:999',
			'persent'          => 'required|min:1|max:50',
			'superior_persent' => 'required|min:0|max:50',
			'has_invite'       => 'required|boolean',
		], [
			'required' => ':attribute 为必填项',
			'boolean'  => '请选择正确的 :attribute',
			'integer'  => ':attribute 必须为大于0的整数',
			'min'      => ':attribute 不符合要求',
			'max'      => ':attribute 不符合要求',
		], [
			'has_choose'       => '上架设置', //添加是否上架内容分销 by Kris 2017.06.14
			'recruit'          => '推广员招募',
			'audit'            => '推广员审核',
			'period'           => '客户关系有效时间',
			'persent'          => '佣金比例',
			'superior_persent' => '邀请奖励比例',
			'has_invite'       => '邀请好友',
		]);

		$data['has_recruit']                 = $request->input('recruit');
		$data['has_check']                   = $request->input('audit');
		$data['period']                      = $request->input('period') * 24 * 60;
		$data['distribute_percent']          = $request->input('persent');
		$data['superior_distribute_percent'] = $request->input('superior_persent');
		$data['has_invite']                  = $request->input('has_invite');
		$data['is_enable_chosen']            = $request->input('has_choose'); //添加是否上架内容分销 by Kris 2017.06.14
		$data['updated_at']                  = date('Y-m-d H:i:s', time());
		// 判断是否存在配置信息，存在就更新，否则就插入
		$info = DB::connection('mysql')->table('t_distribute_config')
			->where('app_id', $this->app_id)
			->first();
		if ($info) {
			$update = DB::connection('mysql')->table('t_distribute_config')
				->where('app_id', $this->app_id)->update($data);

			// 如果更新默认比例成功，同时直接更新分销市场小于该比例的梯次比例
			// 先获取内容分销的商品
			$resource_id = DB::table('t_resource_chosen_middle')->where('app_id', $this->app_id)->where('is_enable_chosen', 1)->pluck('resource_id');
			// 获取所有入选内容分销的商品的一级的分销比例
			// 强行直接更掉所有的商城资源的分销比例
			DB::table('t_xiaoe_app_distribute')->where('app_id', $this->app_id)->whereIn('resource_id', $resource_id)
				->where('distribute_percent', '<', $data['distribute_percent'])->update(['distribute_percent' => $data['distribute_percent']]);

			if ($update) {
				return response()->json(['code' => 0, 'msg' => '更新成功']);
			} else {
				return response()->json(['code' => -1, 'msg' => '更新失败']);
			}
		} else {
			$data['app_id'] = $this->app_id;
			$insert         = DB::connection('mysql')->table('t_distribute_config')
				->insert($data);
			if ($insert) {
				return response()->json(['code' => 0, 'msg' => '更新成功']);
			} else {
				return response()->json(['code' => -1, 'msg' => '更新失败']);
			}
		}

	}

	/****************招募计划******************/
	public function recruit ()
	{
		$url = AppUtils::getUrlHeader($this->app_id) . env("DOMAIN_DUAN_NAME") . '/' . $this->app_id . '/statement/-1';

		$info = DB::connection('mysql')->table('t_distribute_config')
			->where('app_id', $this->app_id)
			->first();

		return view('admin.marketing.salerPlan', [
			'info' => $info,
			'url'  => $url,
		]);
	}

	// 修改招募计划
	public function recruitEdit (Request $request)
	{
		$data['title']       = $request->input('title', '');
		$data['org_content'] = $request->input('content', '');
		$data['descrb']      = $request->input('descrb', '');
		$data['updated_at']  = date('Y-m-d H:i:s', time());

		//分离文本

		if (array_key_exists('descrb', $data)) {
			$data['descrb'] = ResContentComm::sliceUE($data['descrb']);
			if ($data['descrb'] == false) return response()->json(['code' => -2, 'msg' => "上传失败，复制的内容有问题，请尝试分开复制，或记录复制内容并联系技术小哥"]);
		}
		// 查询招募信息
		$info = DB::connection('mysql')->table('t_distribute_config')
			->select('title', 'org_content')
			->where('app_id', $this->app_id)
			->first();
		if ($info) {
			$update = DB::connection('mysql')->table('t_distribute_config')
				->where('app_id', $this->app_id)->update($data);

			if ($update) {
				return response()->json(['code' => 0, 'msg' => '更新成功']);
			} else {
				return response()->json(['code' => -1, 'msg' => '更新失败']);
			}
		} else {
			$data['app_id'] = $this->app_id;
			$insert         = DB::connection('mysql')->table('t_distribute_config')
				->insert($data);
			if ($insert) {
				return response()->json(['code' => 0, 'msg' => '更新成功']);
			} else {
				return response()->json(['code' => -1, 'msg' => '更新失败']);
			}
		}
	}

	/*******************业绩统计************************/
	public function achieveList (Request $request)
	{
		// 获取筛选条件
		$start_time = $request->input('start_time', '');
		$end_time   = $request->input('end_time', '');
		$phone      = $request->input('phone', '');

		$search_array = [];
		// 设定回显及分页参数
		if ($start_time) $search_array['start_time'] = $start_time;
		if ($end_time) $search_array['end_time'] = $end_time;
		if ($phone) $search_array['phone'] = $phone;

		// 拼接where 条件
		$where = " app_id = '{$this->app_id}'";
		// 时间筛选
		if ($start_time) $where .= " and date >= '{$start_time}'";
		if ($end_time) {
			$end_time = date('Y-m-d 23:59:59', strtotime($end_time));
			$where    .= " and date <= '{$end_time}'";
		}

		// 手机号筛选
		$user_info = [];
		$user_id   = [];
		if ($phone) {
			$user_data = DB::connection('mysql')->table('t_distribute_user')
				->select('user_id', 'phone', 'wx_name', 'wx_nickname', 'wx_avatar')
				//                ->where('app_id',$this->app_id)
				->whereRaw(" phone like '%{$phone}%'")
				->get();
			if ($user_data) {
				foreach ($user_data as $v) {
					$user_info[ $v->user_id ] = $v;
					$user_id[]                = $v->user_id;
				}
				$user_id = array_unique($user_id);
			}
		}

		// 如果根据电话号查询
		if ($phone) {
			$ListInfo = DB::connection('mysql_stat')->table('t_distribute_stat')
				->select(DB::Raw('user_id,sum(order_count) as order_count,sum(order_price) as order_price,sum(sub_order_count) as sub_order_count,sum(sub_order_price) as sub_order_price,sum(commision) as commision'))
				->whereRaw($where)
				->whereIn('user_id', $user_id)
				->orderBy('created_at', 'desc')
				->groupBy('user_id')
				->get();
			if ($ListInfo) {
				foreach ($ListInfo as $v) {
					if (array_key_exists($v->user_id, $user_info)) {
						$v->img         = $user_info[ $v->user_id ]->wx_avatar ? $user_info[ $v->user_id ]->wx_avatar : '../images/default.png';
						$v->wx_name     = $user_info[ $v->user_id ]->wx_name ? $user_info[ $v->user_id ]->wx_name : '未知';
						$v->wx_nickname = $user_info[ $v->user_id ]->wx_nickname ? $user_info[ $v->user_id ]->wx_nickname : "未知";
						$v->phone       = $user_info[ $v->user_id ]->phone ? $user_info[ $v->user_id ]->phone : '--';
					} else {
						$v->img         = '../images/default.png';
						$v->wx_name     = '未知';
						$v->wx_nickname = '未知';
						$v->phone       = '--';
					}
				}
			}
		} else {
			// 先根据条件查询获得数据，再查询用户数据
			$ListInfo = DB::connection('mysql_stat')->table('t_distribute_stat')
				->select(DB::Raw('user_id,sum(order_count) as order_count,sum(order_price) as order_price,sum(sub_order_count) as sub_order_count,sum(sub_order_price) as sub_order_price,sum(commision) as commision'))
				->whereRaw($where)
				->orderBy('created_at', 'desc')
				->groupBy('user_id')
				->get();
			if ($ListInfo) {
				foreach ($ListInfo as $v) {
					$user_id[] = $v->user_id;
				}
				$user_id = array_unique($user_id);
				// 查询并插入对应的用户数据
				if ($user_id) {
					$user_data = DB::connection('mysql')->table('t_distribute_user')->select('user_id', 'phone', 'wx_name', 'wx_nickname', 'wx_avatar')
						//                        ->where('app_id', '=', $this->app_id)
						->whereIn('user_id', $user_id)
						->get();
					if ($user_data) {
						foreach ($user_data as $v) {
							$user_info[ $v->user_id ] = $v;
						}
						foreach ($ListInfo as $v) {
							if (array_key_exists($v->user_id, $user_info)) {
								$v->img         = $user_info[ $v->user_id ]->wx_avatar ? $user_info[ $v->user_id ]->wx_avatar : '../images/default.png';
								$v->wx_name     = $user_info[ $v->user_id ]->wx_name ? $user_info[ $v->user_id ]->wx_name : '未知';
								$v->wx_nickname = $user_info[ $v->user_id ]->wx_nickname ? $user_info[ $v->user_id ]->wx_nickname : "未知";
								$v->phone       = $user_info[ $v->user_id ]->phone ? $user_info[ $v->user_id ]->phone : '--';
							} else {
								$v->img         = '../images/default.png';
								$v->wx_name     = '未知';
								$v->wx_nickname = '未知';
								$v->phone       = '--';
							}
						}
						//                        dump($ListInfo);
						//                        exit;
					}

				} else {
					foreach ($ListInfo as $v) {
						$v->img         = '../images/default.png';
						$v->wx_name     = '未知';
						$v->wx_nickname = '未知';
						$v->phone       = '--';
					}
				}
			}
		}

		// 手动分页
		$page    = $request->input('page', '');
		$total   = count($ListInfo);
		$perPage = 10;
		// 判断当前页数
		if ($page) {
			$current_page = $page;
			$current_page = $current_page <= 0 ? 1 : $current_page;
		} else {
			$current_page = 1;
		}
		//手动切割结果集
		$item = array_slice($ListInfo, ($current_page - 1) * $perPage, $perPage);
		//        echo '<pre>';
		$paginator = new LengthAwarePaginator($item, $total, $perPage, $current_page, [
			'path'     => Paginator::resolveCurrentPath(), //生成路径
			'pageName' => 'page',
		]);

		//        dd($paginator);
		//可导出年份
		//        $export_times = DB::connection('mysql_stat')->select("select date_format(created_at,'%Y-%m') AS yearMonth from t_distribute_stat where app_id='$this->app_id'group by yearMonth order by created_at desc ");

		return view('admin.marketing.salerCount', [
			'paginator'    => $paginator,
			'search_array' => $search_array,
			//            'export_times' => $export_times,
		]);
	}

	// 业绩导出
	public function achieveExport (Request $request)
	{
		$export_time = $request->input('export_time', '');
		$version     = $request->input('version', '2003');
		//        dd($request->all());
		ini_set('memory_limit', '1024M');
		set_time_limit(600);
		$export_time = $export_time ? $export_time : date('Y-m', time());
		$excelData[] = ['序号', '推广员昵称', '推广员姓名', '推广员手机号码', '个人订单数', '个人订单金额', '下级订单数', '下级订单金额', '合计佣金'];

		//内容
		$result = DB::connection('mysql_stat')->select("
            SELECT t2.wx_nickname,t2.wx_name,t2.phone,sum(t1.order_count) as order_count,sum(t1.order_price) as order_price,sum(t1.sub_order_count) as sub_order_count,sum(t1.sub_order_price) as sub_order_price,sum(t1.commision) as commision 
            FROM t_distribute_stat t1
                 LEFT JOIN db_ex_business.t_distribute_user t2
                 ON t1.app_id = t2.app_id AND t1.user_id = t2.user_id
            WHERE t1.app_id = '{$this->app_id}' AND t1.date like '%$export_time%' group by t1.user_id order by t1.created_at desc  
        ");

		$i = 0;
		foreach ($result as $v) {
			$i++;
			$rowData     = [
				$i,
				$v->wx_nickname,
				$v->wx_name,
				$v->phone,
				$v->order_count,
				$v->order_price * 0.01,
				$v->sub_order_count,
				$v->sub_order_price * 0.01,
				$v->commision * 0.01,
			];
			$excelData[] = $rowData;
		}

		$title = "{$export_time}业绩统计";
		// 处理数据格式
		$excelData = ExcelUtils::getCorrectData($excelData);
		// 下载
		if ($excelData) {
			if ($version == 2003) {
				ExcelUtils::downExcel($title, $excelData);
			} else {
				ExcelUtils::downloadGbkCsv($title, $excelData);
			}
		}

	}

	/*********************推广**************************/
	public function recordsList (Request $request)
	{
		// 获取筛选条件
		$start_time = $request->input('start_time', '');
		$end_time   = $request->input('end_time', '');
		$phone      = $request->input('phone', '');

		$search_array = [];
		// 设定回显及分页参数
		if ($start_time) $search_array['start_time'] = $start_time;
		if ($end_time) $search_array['end_time'] = $end_time;
		if ($phone) $search_array['phone'] = $phone;

		// 拼接where 条件
		$where = " app_id = '{$this->app_id}' and share_type = 5";
		// 时间筛选
		if ($start_time) $where .= " and created_at >= '{$start_time}'";
		if ($end_time) {
			$end_time = date('Y-m-d 23:59:59', strtotime($end_time));
			$where    .= " and created_at <= '{$end_time}'";
		}

		// 手机号筛选
		$user_info = [];
		$user_id   = [];
		if ($phone) {
			$user_data = DB::connection('mysql')->table('t_distribute_user')
				->select('user_id', 'phone', 'wx_name', 'wx_nickname', 'wx_avatar')
				//                ->where('app_id',$this->app_id)
				->whereRaw(" phone like '%{$phone}%'")
				->get();
			if ($user_data) {
				foreach ($user_data as $v) {
					$user_info[ $v->user_id ] = $v;
					$user_id[]                = $v->user_id;
				}
				$user_id = array_unique($user_id);
			}
		}
		// 如果根据电话号查询
		if ($phone) {
			$ListInfo = DB::connection('mysql')->table('t_distribute_detail')
				->select('created_at', 'source', 'distribute_name', 'price', 'share_user_id', 'distribute_percent', 'distribute_price', 'superior_distribute_user_id', 'superior_distribute_percent', 'superior_distribute_price', 'status')
				->whereRaw($where)
				->whereIn('share_user_id', $user_id)
				->orderBy('created_at', 'desc')
				->paginate(10);
			if ($ListInfo) {
				foreach ($ListInfo as $v) {
					if (array_key_exists($v->share_user_id, $user_info)) {
						$v->wx_name = $user_info[ $v->share_user_id ]->wx_name ? $user_info[ $v->share_user_id ]->wx_name : '未知';

						$houZhui = '<“小鹅通内容分销”推广员>';
						if ($v->source == 1) {//来自内容分销
							$v->wx_name .= $houZhui;
						}

						$v->phone = $user_info[ $v->share_user_id ]->phone ? $user_info[ $v->share_user_id ]->phone : "--";
					} else {
						$v->wx_name = '未知';
						$v->phone   = '--';
					}
				}
			}
		} else {
			// 先根据条件查询获得数据，再查询用户数据
			$ListInfo = DB::connection('mysql')->table('t_distribute_detail')
				->select('created_at', 'source', 'distribute_name', 'price', 'share_user_id', 'distribute_percent', 'distribute_price', 'superior_distribute_user_id', 'superior_distribute_percent', 'superior_distribute_price', 'status')
				->whereRaw($where)
				->orderBy('created_at', 'desc')
				->paginate(10);
			if ($ListInfo) {
				foreach ($ListInfo as $v) {
					$user_id[] = $v->share_user_id;
				}
				$user_id = array_unique($user_id);
				// 查询并插入对应的用户数据
				if ($user_id) {
					$user_data = DB::connection('mysql')->table('t_distribute_user')->select('user_id', 'phone', 'wx_name', 'wx_nickname', 'wx_avatar')
						//                        ->where('app_id', '=', $this->app_id)
						->whereIn('user_id', $user_id)
						->get();
					if ($user_data) {
						foreach ($user_data as $v) {
							$user_info[ $v->user_id ] = $v;
						}
						foreach ($ListInfo as $v) {
							if (array_key_exists($v->share_user_id, $user_info)) {
								$v->wx_name = $user_info[ $v->share_user_id ]->wx_name ? $user_info[ $v->share_user_id ]->wx_name : '未知';

								$houZhui = '<“小鹅通内容分销”推广员>';
								if ($v->source == 1) {//来自内容分销
									$v->wx_name .= $houZhui;
								}

								$v->phone = $user_info[ $v->share_user_id ]->phone ? $user_info[ $v->share_user_id ]->phone : '--';
							} else {
								$v->wx_name = '未知';
								$v->phone   = '--';
							}
						}
					}

				}
			}
		}

		// 查询上级分销员的信息
		$super_user_id   = [];
		$super_user_info = [];
		if ($ListInfo) {
			foreach ($ListInfo as $v) {
				if (property_exists($v, 'superior_distribute_user_id')) {
					$super_user_id[] = $v->superior_distribute_user_id;
				}
			}
			//            dump($super_user_id);
			// 如果存在上级分销员，就查询对应的数据
			if ($super_user_id) {
				$super_user_data = DB::connection('mysql')->table('t_distribute_user')->select('user_id', 'phone', 'wx_name', 'wx_nickname', 'wx_avatar')
					->where('app_id', '=', $this->app_id)
					->whereIn('user_id', $super_user_id)
					->get();
				if ($super_user_data) {
					foreach ($super_user_data as $v) {
						$super_user_info[ $v->user_id ] = $v;
					}
					foreach ($ListInfo as $v) {
						if (array_key_exists($v->superior_distribute_user_id, $super_user_info)) {
							$v->super_wx_name = $super_user_info[ $v->superior_distribute_user_id ]->wx_name ? $super_user_info[ $v->superior_distribute_user_id ]->wx_name : '未知';
							$v->super_phone   = $super_user_info[ $v->superior_distribute_user_id ]->phone ? $super_user_info[ $v->superior_distribute_user_id ]->phone : '--';
						}
					}
					//                    dump($ListInfo);
					//                    exit;
				}
			}
		}

		//可导出年份
		//        $export_times = DB::select("select date_format(created_at,'%Y-%m') AS yearMonth from t_distribute_detail where app_id='$this->app_id'group by yearMonth order by created_at desc ");

		return view('admin.marketing.salerRecord', [
			'ListInfo'     => $ListInfo,
			'search_array' => $search_array,
			//            'export_times'=>$export_times
		]);
	}

	// 推广导出
	public function recordsExport (Request $request)
	{
		$export_time = $request->input('export_time', '');
		$version     = $request->input('version', '2003');
		//        dd($request->all());
		ini_set('memory_limit', '1024M');
		set_time_limit(600);

		$export_time = $export_time ? $export_time : date('Y-m', time());
		$excelData[] = ['序号', '交易时间', '订单号', '商品名称', '成交金额', '推广员姓名', '推广员手机号', '佣金比例', '佣金',
			'上级推广员姓名', '上级推广员手机号', '邀请比例', '邀请佣金', '状态'];

		// 拼接where 条件
		$where = " app_id = '{$this->app_id}' and share_type = 5 and created_at like '%$export_time%' ";

		$ListInfo = DB::connection('mysql')->table('t_distribute_detail')
			->select('created_at', 'order_id', 'distribute_name', 'price', 'share_user_id', 'distribute_percent', 'distribute_price', 'superior_distribute_user_id', 'superior_distribute_percent', 'superior_distribute_price', 'status')
			->whereRaw($where)
			->orderBy('created_at', 'desc')
			->get();

		$user_id = [];
		if ($ListInfo) {
			foreach ($ListInfo as $v) {
				if ($v->share_user_id) {
					$user_id[] = $v->share_user_id;
				}
				if ($v->superior_distribute_user_id) {
					$user_id[] = $v->superior_distribute_user_id;
				}
			}
			$user_id = array_unique($user_id);
			// 查询并插入对应的用户数据
			if ($user_id) {
				$user_data = DB::connection('mysql')->table('t_distribute_user')->select('user_id', 'phone', 'wx_name')
					->where('app_id', '=', $this->app_id)
					->whereIn('user_id', $user_id)
					->get();
				$user_info = [];
				if ($user_data) {
					foreach ($user_data as $v) {
						$user_info[ $v->user_id ] = $v;
					}
					foreach ($ListInfo as $v) {
						if (array_key_exists($v->share_user_id, $user_info)) {
							$v->wx_name = $user_info[ $v->share_user_id ]->wx_name ? $user_info[ $v->share_user_id ]->wx_name : '未知';
							$v->phone   = $user_info[ $v->share_user_id ]->phone ? $user_info[ $v->share_user_id ]->phone : '--';
						} else {
							$v->wx_name = '未知';
							$v->phone   = '--';
						}
						if ($v->superior_distribute_user_id) {
							if (array_key_exists($v->superior_distribute_user_id, $user_info)) {
								$v->super_wx_name = $user_info[ $v->share_user_id ]->wx_name ? $user_info[ $v->share_user_id ]->wx_name : '未知';
								$v->super_phone   = $user_info[ $v->share_user_id ]->phone ? $user_info[ $v->share_user_id ]->phone : '--';
							} else {
								$v->super_wx_name = '未知';
								$v->super_phone   = '--';
							}
						} else {
							$v->super_wx_name = '';
							$v->super_phone   = '';
						}

					}
				}

			}
		}

		$i = 0;
		foreach ($ListInfo as $v) {
			if ($v->status == 1) {
				$v->status = "已经算";
			} else {
				$v->status = "未结算";
			}
			if ($v->distribute_percent) {
				$v->distribute_percent = $v->distribute_percent . '%';
			}

			if ($v->superior_distribute_percent) {
				$v->superior_distribute_percent = $v->superior_distribute_percent . '%';
			}

			$i++;
			$rowData     = [
				$i,
				$v->created_at,
				$v->order_id,
				$v->distribute_name,
				$v->price * 0.01,
				$v->wx_name,
				$v->phone,
				$v->distribute_percent,
				$v->distribute_price * 0.01,
				$v->super_wx_name,
				$v->super_phone,
				$v->superior_distribute_percent,
				$v->superior_distribute_price * 0.01,
				$v->status,
			];
			$excelData[] = $rowData;
		}

		$title = "{$export_time}推广记录";
		// 处理数据格式
		$excelData = ExcelUtils::getCorrectData($excelData);
		// 下载
		if ($excelData) {
			if ($version == 2003) {
				ExcelUtils::downExcel($title, $excelData);
			} else {
				ExcelUtils::downloadGbkCsv($title, $excelData);
			}
		}
	}

	/********************商品列表**********************/
	public function goodsList (Request $request)
	{
		$name = $request->input('name', '');
		$sql  = "
    SELECT id, img_url_compressed, name, price, created_at, goods_type, is_member, ifnull(sum, 0) as sum, has_distribute,distribute_poster,is_distribute_show_userinfo,first_distribute_default, first_distribute_percent, superior_distribute_default, superior_distribute_percent
    FROM (
        SELECT t1.id , t1.img_url_compressed, t1.name, t1.price, t1.created_at, t1.goods_type, t1.is_member, t2.sum, t1.has_distribute,t1.is_distribute_show_userinfo,t1.distribute_poster,t1.first_distribute_default, t1.first_distribute_percent, t1.superior_distribute_default, t1.superior_distribute_percent
        FROM (
        SELECT app_id, id, img_url_compressed, name, price, created_at, 0 as goods_type,is_member, has_distribute,distribute_poster,is_distribute_show_userinfo, first_distribute_default,
      first_distribute_percent, superior_distribute_default, superior_distribute_percent
        FROM t_pay_products
        WHERE app_id = '{$this->app_id}' AND state IN (0, 1) AND price > 0 AND is_distribute = 0 AND name like '%{$name}%'
    ) t1 LEFT JOIN (
        SELECT app_id, order_id, payment_type, product_id, sum(count) AS sum FROM t_orders
        WHERE app_id = '{$this->app_id}' AND order_state = 1
        GROUP BY product_id
    ) t2 on t1.app_id = t2.app_id AND t1.id = t2.product_id

UNION ALL

SELECT t1.id , t1.img_url_compressed, t1.title as name, t1.piece_price as price, t1.created_at, t1.goods_type,t1.is_member, t2.sum, t1.has_distribute,t1.is_distribute_show_userinfo,t1.distribute_poster,
t1.first_distribute_default, t1.first_distribute_percent, t1.superior_distribute_default, t1.superior_distribute_percent from (
  SELECT app_id, id, img_url_compressed, title, piece_price, created_at, 2 as goods_type,-1 as is_member, has_distribute,distribute_poster ,is_distribute_show_userinfo,first_distribute_default,
  first_distribute_percent, superior_distribute_default, superior_distribute_percent FROM t_audio
  WHERE app_id = '{$this->app_id}' AND audio_state IN (0, 1) and payment_type = 2 AND piece_price > 0 AND title like '%{$name}%'
) t1 LEFT JOIN (
  SELECT app_id, resource_id, sum(count) AS sum FROM t_orders
  WHERE app_id = '{$this->app_id}' AND order_state = 1 and resource_type = 2
  GROUP BY resource_id
  ) t2 on t1.app_id = t2.app_id and t1.id = t2.resource_id

UNION ALL

SELECT t1.id , t1.img_url_compressed, t1.title as name, t1.piece_price as price, t1.created_at, t1.goods_type,t1.is_member, t2.sum, t1.has_distribute,t1.is_distribute_show_userinfo,t1.distribute_poster,
t1.first_distribute_default, t1.first_distribute_percent, t1.superior_distribute_default, t1.superior_distribute_percent from (
  SELECT app_id, id, img_url_compressed, title, piece_price, created_at, 3 as goods_type,-1 as is_member, has_distribute,distribute_poster,is_distribute_show_userinfo, first_distribute_default,
  first_distribute_percent, superior_distribute_default, superior_distribute_percent FROM t_video
  WHERE app_id = '{$this->app_id}' AND video_state IN (0, 1) and payment_type = 2 AND piece_price > 0 AND title like '%{$name}%'
) t1 LEFT JOIN (
  SELECT app_id, resource_id, sum(count) AS sum FROM t_orders
  WHERE app_id = '{$this->app_id}' AND order_state = 1 and resource_type = 3
  GROUP BY resource_id
) t2 on t1.app_id = t2.app_id and t1.id = t2.resource_id

UNION ALL
    SELECT t1.id , t1.img_url_compressed, t1.title as name, t1.piece_price as price, t1.created_at, t1.goods_type,t1.is_member , t2.sum, t1.has_distribute,t1.is_distribute_show_userinfo,t1.distribute_poster,t1.first_distribute_default, t1.first_distribute_percent, t1.superior_distribute_default, t1.superior_distribute_percent
    FROM (
      SELECT app_id, id, img_url_compressed, title, piece_price, created_at, 1 as goods_type,-1 as is_member, has_distribute,distribute_poster, is_distribute_show_userinfo,first_distribute_default,
      first_distribute_percent, superior_distribute_default, superior_distribute_percent FROM t_image_text
      WHERE app_id = '{$this->app_id}' AND display_state IN (0, 1) and payment_type = 2 AND piece_price > 0 AND title like '%{$name}%'
    ) t1 LEFT JOIN (
      SELECT app_id, resource_id, sum(count) AS sum FROM t_orders
      WHERE app_id = '{$this->app_id}' AND order_state = 1 and resource_type = 1
      GROUP BY resource_id
    ) t2 on t1.app_id = t2.app_id and t1.id = t2.resource_id

UNION ALL
    SELECT t1.id , t1.img_url_compressed, t1.title as name, t1.piece_price as price, t1.created_at, t1.goods_type,t1.is_member, t2.sum, t1.has_distribute,t1.is_distribute_show_userinfo,t1.distribute_poster,
    t1.first_distribute_default, t1.first_distribute_percent, t1.superior_distribute_default, t1.superior_distribute_percent
    from (
      SELECT app_id, id, img_url_compressed, title, piece_price, created_at, 4 as goods_type,-1 as is_member, has_distribute,distribute_poster,is_distribute_show_userinfo,first_distribute_default,
      first_distribute_percent, superior_distribute_default, superior_distribute_percent
      FROM t_alive
      WHERE app_id = '{$this->app_id}' AND state IN (0, 1) and payment_type = 2 AND piece_price > 0 AND title like '%{$name}%'
    ) t1 LEFT JOIN (
      SELECT app_id, resource_id, sum(count) AS sum
      FROM t_orders
      WHERE app_id = '{$this->app_id}' AND order_state = 1 AND resource_type = 4
      GROUP BY resource_id
    ) t2 on t1.app_id = t2.app_id and t1.id = t2.resource_id
) tt1 order by created_at desc
";

		$ListInfo = DB::connection('mysql')->select($sql);

		//        dd($ListInfo);
		if ($ListInfo) {
			// 手动分页
			$page    = $request->input('page', '');
			$total   = count($ListInfo);
			$perPage = 10;
			// 判断当前页数
			if ($page) {
				$current_page = $page;
				$current_page = $current_page <= 0 ? 1 : $current_page;
			} else {
				$current_page = 1;
			}
			//手动切割结果集
			$item = array_slice($ListInfo, ($current_page - 1) * $perPage, $perPage);
			//        echo '<pre>';
			$paginator = new LengthAwarePaginator($item, $total, $perPage, $current_page, [
				'path'     => Paginator::resolveCurrentPath(), //生成路径
				'pageName' => 'page',
			]);
		} else {
			$paginator = [];
		}

		foreach ($paginator as $v) {
			if ($v->is_member == 1) {
				$v->goods_type = 5;
			}
		}
		//        $info = $paginator->toArray()['data'];
		//        dump($info);
		//        dump($paginator);
		//        exit;

		// 默认比例, <0 未设置。
		$configInfo = DB::connection('mysql')->table('t_distribute_config')
			->select('distribute_percent', 'superior_distribute_percent')
			->where('app_id', $this->app_id)->first();

		return view('admin.marketing.salerGoodsList', [
			//            'info'=>$info,
			'configInfo' => $configInfo,
			'paginator'  => $paginator,
			'name'       => $name,
		]);
	}

	public function goodsSetting (Request $request)
	{
		$config = DB::connection('mysql')->table('t_distribute_config')
			->select('distribute_percent', 'superior_distribute_percent')
			->where('app_id', '=', $this->app_id)
			->first();
		if ($config->distribute_percent <= 0 && $config->superior_distribute_percent <= 0)
			return response()->json(['code' => -2, 'msg' => '未设置默认比例，无法修改']);

		$id         = trim($request->input('id', ''));
		$goods_type = $request->input('goods', '');

		$data['has_distribute']              = $request->input('has_distribute', '');
		$data['first_distribute_default']    = $request->input('default', '');
		$data['first_distribute_percent']    = $request->input('persent', '');
		$data['superior_distribute_default'] = $request->input('superior_default', '');
		$data['superior_distribute_percent'] = $request->input('superior_persent', '');
		$data['distribute_poster']           = $request->input('distribute_poster', '');
		$data['is_distribute_show_userinfo'] = $request->input('is_distribute_show_userinfo', 0);

		switch ($goods_type) {
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
			default:
				$table_name = "";
		}

		// 如果使用自定义比例，则自定义比例不能为空
		if ($data['first_distribute_default'] == 1 && !$data['first_distribute_percent']) {
			return response()->json(['code' => -1, 'msg' => '自定义比例不能为空']);
		}
		if ($data['superior_distribute_default'] == 1 && !$data['superior_distribute_percent']) {
			return response()->json(['code' => -1, 'msg' => '自定义比例不能为空']);
		}

		// 如果使用默认值,清空传入的自定义比例
		if ($data['first_distribute_default'] == 0) unset($data['first_distribute_percent']);
		if ($data['superior_distribute_default'] == 0) unset($data['superior_distribute_percent']);

		if ($table_name && $id) {
			// 更新
			$data['updated_at'] = date('Y-m-d H:i:s', time());
			$update             = DB::connection('mysql')->table($table_name)
				->where('app_id', $this->app_id)
				->where('id', $id)
				->update($data);

			// 同时查询是否参与内容分销推广
			$info = DB::table('t_resource_chosen_middle')->where('app_id', $this->app_id)->where('resource_id', $id)->where('is_enable_chosen', 1)->first();
			// 如果是内容分销商品，强行更新一波
			if ($info) {
				if (array_key_exists('first_distribute_percent', $data)) {
					$distribute_percent = $data['first_distribute_percent'];
				} else {
					$distribute_percent = DB::table('t_distribute_config')->where('app_id', $this->app_id)->value('distribute_percent');
				}
				// 强行直接更掉所有的商城资源的分销比例
				DB::table('t_xiaoe_app_distribute')->where('app_id', $this->app_id)->where('resource_id', $id)
					->where('distribute_percent', '<', $distribute_percent)->update(['distribute_percent' => $distribute_percent]);
			}

			if ($update) {
				return response()->json(['code' => 0, 'msg' => '更新成功']);
			} else {
				return response()->json(['code' => -2, 'msg' => '更新失败']);
			}
		} else {
			return response()->json(['code' => -1, 'msg' => '参数有误']);
		}
	}


	/*********************推广员*******************************/
	// 审核列表
	public function auditList (Request $request)
	{

		// 获取筛选条件
		$start_time = $request->input('start_time', '');
		$end_time   = $request->input('end_time', '');
		$phone      = $request->input('phone', '');
		$status     = $request->input('status', '');

		$search_array = [];
		// 设定回显及分页参数
		if ($start_time) $search_array['start_time'] = $start_time;
		if ($end_time) $search_array['end_time'] = $end_time;
		if ($phone) $search_array['phone'] = $phone;

		// 拼接where 条件
		$where = "app_id = '{$this->app_id}'";
		//        dump($where);
		// 时间筛选
		if ($start_time) $where .= " and apply_at >= '{$start_time}'";
		if ($end_time) {
			$end_time = date('Y-m-d 23:59:59', strtotime($end_time));
			$where    .= " and apply_at <= '{$end_time}'";
		}

		if ($phone) $where .= " and phone like '%{$phone}%'";
		// 状态筛选
		if ($status === "0") {
			$where                  .= " and status = 0";
			$search_array['status'] = $status;
		} else if ($status == 1) {
			$where                  .= " and status = 1";
			$search_array['status'] = $status;
		} else {
			$where .= " and status in (0,1)";;
		}

		//        dump($where);
		//        exit;

		// 获取数据
		$ListInfo = DB::connection('mysql')->table('t_distribute_user')
			->select('user_id', 'wx_avatar', 'wx_nickname', 'wx_name', 'phone', 'apply_at', 'status')
			->whereRaw($where)
			//            ->get();
			->paginate(10);

		//        dd($ListInfo);

		return view("admin.marketing.salerCheck", [
			'ListInfo'     => $ListInfo,
			'search_array' => $search_array,
		]);

	}

	// 审核操作
	public function auditing (Request $request)
	{
		$user_id        = $request->input('id', '');
		$data['status'] = $request->input('status', '');
		$reject_reason  = $request->input('reject_reason', '');
		//        dd($request->all());
		if ($data['status'] && $user_id) {
			// 查询用户信息
			$old_status = DB::connection('mysql')->table('t_distribute_user')
				->where('app_id', $this->app_id)
				->where('user_id', $user_id)
				->value('status');
			//            dd($old_status);
			if ($old_status !== 0) {
				return response(['code' => -1, 'msg' => '无效的用户信息']);
			} else {
				if ($old_status == $data['status']) {
					return response(['code' => -1, 'msg' => '用户信息有误']);
				} else {
					// 审核
					if ($data['status'] == 1) {
						// 拒绝用户的同时给其发一个小纸条
						$info['app_id']         = $this->app_id;
						$info['type']           = 0;
						$info['user_id']        = $user_id;
						$info['send_nick_name'] = "推广管理员";
						$info['source']         = 0;
						$info['content']        = "非常抱歉，您的推广员申请没有通过商户审核。拒绝理由：{$reject_reason}。";
						$info['send_at']        = date('Y-m-d H:i:s');
						$info['created_at']     = date('Y-m-d H:i:s');

						$insert = DB::connection('mysql')->table('t_messages')->insert($info);

					} else {
						// 通过审核 更新最近审核时间
						$data['passed_at'] = date('Y-m-d H:i:s');
					}

					$update = DB::connection('mysql')->table('t_distribute_user')
						->where('app_id', $this->app_id)->where('user_id', $user_id)
						->update($data);

					// 流水表记录
					$insert = DB::connection('mysql')->table('t_distribute_apply')
						->insert([
							'app_id'        => $this->app_id,
							'user_id'       => $user_id,
							'type'          => $data['status'],
							'reject_reason' => $reject_reason,
							'created_at'    => date('Y-m-d H:i:s', time()),
						]);

					if ($update && $insert) {
						return response(['code' => 0, 'msg' => '更新成功']);
					} else {
						return response(['code' => -2, 'msg' => '更新失败']);
					}
				}
			}
		} else {
			return response(['code' => -1, 'msg' => '参数有误']);
		}

	}

	//推广员列表
	public function salerList (Request $request)
	{
		// 获取筛选条件
		$start_time = $request->input('start_time', '');
		$end_time   = $request->input('end_time', '');
		$phone      = $request->input('phone', '');

		$search_array = [];
		// 设定回显及分页参数
		if ($start_time) $search_array['start_time'] = $start_time;
		if ($end_time) $search_array['end_time'] = $end_time;
		if ($phone) $search_array['phone'] = $phone;

		// 拼接where 条件
		// 拼接where 条件
		$where = " app_id = '{$this->app_id}' and status = 2";
		// 时间筛选
		if ($start_time) $where .= " and passed_at >= '{$start_time}'";
		if ($end_time) {
			$end_time = date('Y-m-d 23:59:59', strtotime($end_time));
			$where    .= " and passed_at <= '{$end_time}'";
		}

		if ($phone) $where .= " and phone like '%{$phone}%'";

		$ListInfo = DB::connection('mysql')->table('t_distribute_user')
			->select('user_id', 'wx_avatar', 'wx_nickname', 'wx_name', 'phone', 'superior_user_id', 'passed_at')
			->whereRaw($where)
			->orderBy('passed_at', 'desc')
			->paginate(10);

		$user_id       = [];
		$super_user_id = [];
		if ($ListInfo) {
			foreach ($ListInfo as $v) {
				//                dump($v);
				$user_id[] = $v->user_id;
				if ($v->superior_user_id != '') {
					$super_user_id[] = $v->superior_user_id;
				}
			}
			//            dump($super_user_id);
			// 查上级分销员姓名
			if ($super_user_id) {
				$super_name = DB::connection('mysql')->table('t_distribute_user')
					->select('user_id', 'wx_name')
					->where('app_id', $this->app_id)
					->where('status', '!=', 3)
					->whereIn('user_id', $super_user_id)
					->pluck('wx_name', 'user_id');
				//                dump($super_name);
				if ($super_name) {
					foreach ($ListInfo as $v) {
						if (array_key_exists($v->superior_user_id, $super_name)) {
							$v->super_name = $super_name[ $v->superior_user_id ];
						} else {
							$v->super_name = '';
						}
					}
				}
			} else {
				foreach ($ListInfo as $v) {
					$v->super_name = '';
				}
			}
			// 查累计成交笔数和成交金额
			$charges = [];
			if ($user_id) {
				$charge = DB::connection('mysql_stat')->table('t_distribute_stat')
					->select(DB::raw('user_id,sum(order_count) as count,sum(order_price) as count_price'))
					->whereIn('user_id', $user_id)
					->where('app_id', $this->app_id)
					->groupby('user_id')
					->get();
				if ($charge) {
					foreach ($charge as $v) {
						$charges[ $v->user_id ] = $v;
					}

					foreach ($ListInfo as $v) {
						if (array_key_exists($v->user_id, $charges)) {
							$v->count       = $charges[ $v->user_id ]->count;
							$v->count_price = $charges[ $v->user_id ]->count_price;
						} else {
							$v->count       = 0;
							$v->count_price = 0;
						}
					}
				} else {
					foreach ($ListInfo as $v) {
						$v->count       = 0;
						$v->count_price = 0;
					}
				}
			}
		}
		//        dump($ListInfo);
		//        exit;
		return view('admin.marketing.salerList', [
			'ListInfo'     => $ListInfo,
			'search_array' => $search_array,
		]);
	}

	// 清退
	public function salerDelete (Request $request)
	{
		$user_id = $request->input('user_id', '');

		$info = DB::connection('mysql')->table('t_distribute_user')
			->where('app_id', $this->app_id)->where('user_id', $user_id)
			->first();

		if ($user_id && $info->status == 2) {
			// 清退操作
			$update = DB::connection('mysql')->table('t_distribute_user')
				->where('app_id', $this->app_id)->where('user_id', $user_id)
				->update([
					'status'      => 3,
					'cancel_time' => date('Y-m-d H:i:s', time()),
				]);

			// 同时 给其上级推广员发一个小纸条
			if ($info->superior_user_id) {
				$data['app_id']     = $this->app_id;
				$data['type']       = 0;
				$data['user_id']    = $info->superior_user_id;
				$data['source']     = 0;
				$data['content']    = "通知：您的好友 {$info->wx_name} 已被商户清退，无法继续推广商品，将不继续在成功邀请的列表中展示。";
				$data['send_at']    = date('Y-m-d H:i:s');
				$data['created_at'] = date('Y-m-d H:i:s');

				$insert = DB::connection('mysql')->table('t_messages')->insert($data);

			}

			if ($update) {
				// 清空上级分销员信息
				$super_update = DB::connection('mysql')->table('t_distribute_user')
					->where('app_id', $this->app_id)
					->where('superior_user_id', $user_id)
					->update(['superior_user_id' => '']);

				// 流水表记录
				$insert = DB::connection('mysql')->table('t_distribute_apply')
					->insert([
						'app_id'     => $this->app_id,
						'user_id'    => $user_id,
						'type'       => 3,
						'created_at' => date('Y-m-d H:i:s', time()),
					]);

				return response()->json(['code' => 0, 'msg' => '清退成功']);

			} else {
				return response()->json(['code' => -1, 'msg' => '更新失败']);
			}
		} else {
			return response()->json(['code' => -1, 'msg' => '数据有误']);
		}
	}

	/*********************excel 导出数据 日期接口******************/
	public function dateList (Request $request)
	{
		$export_times = ExcelUtils::getFreeMonths(" 2017-02");

		return $export_times;
	}

	//内容分销页面
	public function chosen ()
	{

		$is_enable_chosen = \DB::table('t_distribute_config')
			->where('app_id', '=', $this->app_id)
			->value('is_enable_chosen');

		$distribute_data = \DB::table('t_xiaoe_app_distribute')
			->select('distribute_id', 'start_order_num', 'end_order_num', 'distribute_percent', 'updated_time')
			->where('app_id', '=', $this->app_id)
			->get();
		if ($distribute_data) {
			$is_have_distribute_data = 1;
			$last_update_time        = $distribute_data[0]->updated_time;
			$t_update                = strtotime($last_update_time);
			$t_90                    = strtotime("-90 days");
			if ($t_update < $t_90)
				$is_enable_edit = 0;    //可编辑
			else
				$is_enable_edit = 1;    //不可编辑

		} else {
			$is_have_distribute_data = 0;
			$is_enable_edit          = 0;
			$last_update_time        = '';
		}

		$alive = \DB::table('t_alive')
			->select('title', 'id', \DB::raw('4 as type'))
			->where('app_id', '=', $this->app_id)
			->where('state', '=', 0)
			->where('piece_price', '>', 0)
			->get();

		$column = \DB::table('t_pay_products')
			->select('name', 'id', \DB::raw('5 as type'))
			->where('app_id', '=', $this->app_id)
			->where('state', '=', 0)
			->where('is_member', '=', 0)
			->where('price', '>', 0)
			->get();

		$member = \DB::table('t_pay_products')
			->select('name', 'id', \DB::raw('6 as type'))
			->where('app_id', '=', $this->app_id)
			->where('state', '=', 0)
			->where('is_member', '=', 1)
			->where('price', '>', 0)
			->get();

		$img_text = \DB::table("t_image_text")
			->select('title', 'id', \DB::raw('1 as type'))
			->where('app_id', '=', $this->app_id)
			->where('display_state', '=', 0)
			->where('app_id', '=', $this->app_id)
			->where('piece_price', '>', 0);

		$audios = \DB::table("t_audio")
			->select('title', 'id', \DB::raw('2 as type'))
			->where('audio_state', '=', 0)
			->where('app_id', '=', $this->app_id)
			->where('piece_price', '>', 0);

		$course = \DB::table("t_video")
			->select('title', 'id', \DB::raw('3 as type'))
			->where('video_state', '=', 0)
			->where('app_id', '=', $this->app_id)
			->where('piece_price', '>', 0)
			->union($img_text)
			->union($audios)
			->get();

		$resource_chosen = \DB::table('t_resource_chosen_middle')
			->select('resource_id as id', 'resource_type as type', 'resource_name as name')
			->where('app_id', '=', $this->app_id)
			->where('is_enable_chosen', '=', 1)
			->get();
		foreach ($resource_chosen as $one) {
			switch ($one->type) {
				case  1:

				case  2:

				case  3:
					$one->resource_type_name = '课程';
					break;
				case  4:
					$one->resource_type_name = '直播';
					break;
				case  5:
					$one->resource_type_name = '专栏';
					break;
				case  6:
					$one->resource_type_name = '会员';
					break;
			}
		}

		return view('admin.marketing.chosen', [
			'switch'           => '22', 'is_enable_chosen' => $is_enable_chosen, 'is_have_distribute_data' => $is_have_distribute_data, 'distribute_data' => $distribute_data,
			'alive'            => $alive, 'column' => $column, 'member' => $member, 'course' => $course, 'resource_chosen' => $resource_chosen,
			'last_update_time' => $last_update_time, 'is_enable_edit' => $is_enable_edit,
		]);
	}

	// 小鹅通内容分销上、下架设置
	public function chosenEnable ()
	{
		$is_enable_chosen = Input::get('is_enable_chosen');
		if (Utils::isEmptyString($is_enable_chosen))
			return ['code' => 1, 'msg' => 'is_enable_chosen is required'];

		$has_distribute = DB::connection('mysql_config')->table('t_app_module')
			->where('app_id', $this->app_id)
			->value('has_distribute');

		if ($has_distribute == 0)
			return ['code' => 2, 'msg' => 'has_distribute is off'];

		if ($is_enable_chosen != '1')
			return ['code' => 4, 'msg' => 'the chosen is not can off'];

		$res = \DB::table('t_distribute_config')
			->where('app_id', '=', $this->app_id)
			->update(['is_enable_chosen' => $is_enable_chosen]);

		if (!$res) return ['code' => 1024, 'msg' => 'de error'];

		return ['code' => 0, 'msg' => 'ok', 'data' => ['is_enable_chosen' => $is_enable_chosen]];
	}

	public function addResourceChosen ()
	{
		$id   = Input::get('id', '');
		$type = Input::get('type', '');
		if (Utils::isEmptyString($id) || Utils::isEmptyString($type))
			return ['code' => 1, 'msg' => 'params is required'];

		$old = \DB::table('t_resource_chosen_middle')
			->where('app_id', '=', $this->app_id)
			->where('resource_type', '=', $type)
			->where('resource_id', '=', $id)
			->first();
		if ($old) { // 如果中间表中已经有该资源
			if ($old->is_enable_chosen == 1) { // 并且 该资源是 可被选择的  则 直接返回已存在
				return ['code' => 2, 'msg' => 'the resource have exists'];
			} else { // 则更新该字段：is_enable_chosen
				$res = \DB::table('t_resource_chosen_middle')
					->where('app_id', '=', $this->app_id)
					->where('resource_type', '=', $type)
					->where('resource_id', '=', $id)
					->update(['is_enable_chosen' => 1, 'updated_at' => Utils::getTime()]);
				if (!$res) return ['code' => 1064, 'msg' => 'db error'];

				return ['code' => 0, 'msg' => 'ok'];
			}
		}

		$old_count = \DB::table('t_resource_chosen_middle')
			->where('app_id', '=', $this->app_id)
			->where('is_enable_chosen', '=', 1)
			->count();
		if ($old_count >= 20)
			return ['code' => 4, 'msg' => 'the resource more than 20'];

		switch ($type) {
			case  1:
				$table_name = 't_image_text';
				$title_name = 'title';
				$price_name = 'piece_price';
				break;
			case  2:
				$table_name = 't_audio';
				$title_name = 'title';
				$price_name = 'piece_price';
				break;
			case  3:
				$table_name = 't_video';
				$title_name = 'title';
				$price_name = 'piece_price';
				break;
			case  4:
				$table_name = 't_alive';
				$title_name = 'title';
				$price_name = 'piece_price';
				break;
			case  5:
			case  6:
				$title_name = 'name';
				$table_name = 't_pay_products';
				$price_name = 'price';
				break;
		}
		$res                   = \DB::table($table_name)
			->select('app_id', 'id as resource_id', \DB::raw($type . ' as resource_type'), $title_name . ' as resource_name', $price_name . ' as price', 'img_url', 'img_url_compressed')
			->where('app_id', '=', $this->app_id)
			->where('id', '=', $id)
			->first();
		$res->app_name         = \DB::table('db_ex_config.t_app_conf')
			->where('app_id', '=', $this->app_id)
			->where('wx_app_type', '=', 1)
			->value('wx_app_name');
		$res->is_enable_chosen = 1;
		$res->created_at       = Utils::getTime();
		$res->updated_at       = Utils::getTime();
		$data                  = (array)$res;
		foreach ($data as $k => $v) {
			if (Utils::isEmptyString($v))
				return ['code' => 8, 'msg' => $k . ' is required'];
		}
		$res = \DB::table('t_resource_chosen_middle')
			->insert($data);
		if (!$res) return ['code' => 1024, 'msg' => 'resource insert error'];

		return ['code' => 0, 'msg' => 'ok'];
	}

	//小鹅通内容分销梯度分销比例设置 和 添加商品
	public function setXiaoeDistribute ()
	{
		$app_id = $this->app_id;
		if (!$app_id) return ['code' => -2, 'msg' => 'app_id is required'];

		$distribute_data = \DB::table('t_xiaoe_app_distribute')
			->select('distribute_id', 'start_order_num', 'end_order_num', 'distribute_percent', 'updated_time')
			->where('app_id', '=', $this->app_id)
			->get();

		if ($distribute_data) {
			$last_update_time = $distribute_data[0]->updated_time;
			$t_update         = strtotime($last_update_time);
			$t_90             = strtotime("-90 days");
			if ($t_update > $t_90) return ['code' => -4, 'msg' => 'last update time < 90 days'];
		}

		$data1['start_order_num']    = 1;
		$data1['end_order_num']      = Input::get('end_order_num1');
		$data1['distribute_percent'] = Input::get('distribute_percent1');

		$data2['start_order_num']    = Input::get('start_order_num2');
		$data2['end_order_num']      = Input::get('end_order_num2');
		$data2['distribute_percent'] = Input::get('distribute_percent2');

		$data3['start_order_num']    = Input::get('start_order_num3');
		$data3['end_order_num']      = env('MYSQL_INT_MAX_VALUE');
		$data3['distribute_percent'] = Input::get('distribute_percent3');

		$edit = Input::get('edit', '');
		//        if(Utils::isEmptyString($data1['end_order_num']) || Utils::isEmptyString($data2['distribute_percent'])  || Utils::isEmptyString($data3['start_order_num']))
		//            return ['code' => 0 , 'msg' => 'ok'];

		//数据校验
		if (($data2['start_order_num'] != $data1['end_order_num'] + 1) || ($data3['start_order_num'] != $data2['end_order_num'] + 1))
			return ['code' => 1, 'msg' => 'num is not correct'];  // 区间填写错误

		if (($data1['distribute_percent'] <= 0) || ($data2['distribute_percent'] < $data1['distribute_percent']) || ($data3['distribute_percent'] < $data2['distribute_percent']))
			return ['code' => 2, 'msg' => 'percent is not correct'];  // 分成比例填写错误

		// 三个数据共有的三个值
		if ($edit != 'edit') {
			$data['app_id']     = $this->app_id;
			$data['created_at'] = Utils::getTime();

			$data1 = array_add($data1, 'distribute_id', Utils::getUniId('xd_'));
			$data2 = array_add($data2, 'distribute_id', Utils::getUniId('xd_'));
			$data3 = array_add($data3, 'distribute_id', Utils::getUniId('xd_'));
		} else {
			$distribute_id1 = Input::get('distribute_id1', '');
			$distribute_id2 = Input::get('distribute_id2', '');
			$distribute_id3 = Input::get('distribute_id3', '');
		}

		$data['updated_time'] = Utils::getTime();

		// 插入数据库前的数据组装
		$data1 = array_merge($data1, $data);

		$data2 = array_merge($data2, $data);

		$data3 = array_merge($data3, $data);

		if ($edit == 'edit') {
			\DB::beginTransaction();             // 开启事务

			$res1 = \DB::table('t_xiaoe_app_distribute')
				->where('app_id', '=', $this->app_id)
				->where('distribute_id', '=', $distribute_id1)
				->update($data1);
			if (!$res1) return ['code' => 1024, 'msg' => 'db error'];

			$res2 = \DB::table('t_xiaoe_app_distribute')
				->where('app_id', '=', $this->app_id)
				->where('distribute_id', '=', $distribute_id2)
				->update($data2);
			if (!$res2) {
				\DB::rollBack();

				return ['code' => 1024, 'msg' => 'db error'];
			}

			$res3 = \DB::table('t_xiaoe_app_distribute')
				->where('app_id', '=', $this->app_id)
				->where('distribute_id', '=', $distribute_id3)
				->update($data3);
			if (!$res3) {
				\DB::rollBack();

				return ['code' => 1024, 'msg' => 'db error'];
			}

			\DB::commit();

			return ['code' => 0, 'msg' => 'ok'];

		} else {
			$res = \DB::table('t_xiaoe_app_distribute')
				->insert([$data1, $data2, $data3]);
			if (!$res) return ['code' => 1024, 'msg' => 'db error'];

			return ['code' => 0, 'msg' => 'ok'];
		}
	}

	public function JudgeDistributePercent ($default, $percent = 0)
	{
		if ($default < 1 && $percent === 0) return response()->json(['code' => -2, 'msg' => '无效的参数']);
		// 如果采用默认比例，查询该业务的默认分销比例
		if ($default) {
			$percent = DB::table('t_distribute_config')->where('app_id', $this->app_id)->value('distribute_percent');
		}

		// 获取入选内容分销的商品
		$resource_id = DB::table('t_resource_chosen_middle')->where('app_id', $this->app_id)->where('is_enable_chosen', 1)->pluck('resource_id');

		// 获取所有入选内容分销的商品的一级的分销比例
		$chosen_percent = DB::table('t_xiaoe_app_distribute')->where('app_id', $this->app_id)->whereIn('resource_id', $resource_id)->where('start_order_num', 1)->pluck('distribute_percent');

		foreach ($chosen_percent as $v) {
			if ($v < $percent) {
				return response()->json(['code' => 0, 'msg' => '请求成功']);
			}
		}

		return response()->json(['code' => -1, 'msg' => '请求成功，不需要弹窗']);
	}

}
