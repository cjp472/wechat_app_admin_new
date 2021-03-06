<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\Utils;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class BShellController extends Controller
{

	public static function insertApiLogTest ()
	{

		$created_at = Utils::getTime();

		if (Utils::isEmptyString('apppcHqlTPT3482') || Utils::isEmptyString('u_582411eca3329_ti3rjKbo')) {
			return -1;
		}
		//每天一张新表
		$logsDateTable = 't_api_logs_test_' . date('Y_m_d', time());
		$logdata       = [
			'app_id'      => 'apppcHqlTPT3482',
			'user_id'     => 'u_582411eca3329_ti3rjKbo',
			'uri'         => 'http://wxdd198a901fa24220.h5.inside.xiaoe-tech.com/homepage',
			'referer'     => '',
			'app_version' => '0.1',
			'agent'       => '',
			'client'      => '1',
			'created_at'  => $created_at,
		];
		try {
			DB::connection('mysql_log')->table($logsDateTable)->insert($logdata);
		} catch (\Exception $e) {
			try {
				//创建当日新表
				DB::connection('mysql_log')->statement("create table if not exists  $logsDateTable  like t_api_logs");
				//插入日志数据到当日新表
				DB::connection('mysql_log')->table($logsDateTable)->insert($logdata);
			} catch (\Exception $e) {
				//预留方案，不做实际处理
				//插入日志数据到总表中（补充方案：管理台从总表查询此类记录转移到每日分表中）
				//DB::connection('mysql_log')->table('t_api_logs')->insert($logdata);
			}
		}

		return 1;//$insertResult;
	}

	/**
	 *插入中间表的按天统计全网数据
	 *
	 * @param sum_day 日期参数 >0
	 * @param nosql   0默认执行sql操作 ； 1 不执行sql操作
	 * @params nofor 自定义单日统计
	 * @params asy default 0 async \ 1 no async
	 *
	 * @return redirect
	 */
	public function insertTDashPage ()
	{
		set_time_limit(600); // set timeout seconds
		$stime    = microtime(true);
		$sum_day  = Input::get('sum_day', 0);
		$nosql    = Input::get('nosql', 0);
		$thistime = date('Y-m-d', time());
		$nofor    = Input::get('nofor', 0);

		if ($nofor == 0) {
			for ($i = 0; $i < $sum_day; $i++) {
				$datetime = date('Y-m-d', strtotime("-" . $i . " days", time()));
				//dump($datetime);

				$dayCount = DB::connection('mysql')->select("select count(*) as day_count from t_users 
where date(created_at) = '$datetime' ")[0];
				$sumCount = DB::connection('mysql')->select("select count(*) as sum_count from t_users 
where date(created_at) <= '$datetime' ")[0];
				//                $dayPrice = DB::connection('mysql')->select("select sum(price) as income from t_purchase
				//where date(created_at) = '$datetime' and generate_type = 0 ")[0];
				$dayPrice = DB::connection('mysql')->select("select sum(price) as income from t_orders
where order_state = 1 and date(created_at) = '$datetime' ")[0];
				$sumPrice = DB::connection('mysql')->select("select sum(price) as sum_income from t_orders 
where date(created_at) <= '$datetime' and order_state = '1' ")[0];

				$day_count  = $dayCount->day_count ? $dayCount->day_count : 0;
				$sum_count  = $sumCount->sum_count ? $sumCount->sum_count : 0;
				$income     = $dayPrice->income ? $dayPrice->income : 0;
				$sum_income = $sumPrice->sum_income ? $sumPrice->sum_income : 0;
				//dump($day_count);dump($sum_count);dump($income);dump($sum_income);

				if ($nosql == 0) {
					$nowTime = Utils::getTime();
					$result  = DB::connection('mysql_stat')->insert
					("
            insert into t_dash_page set date='$datetime', day_count='$day_count', sum_count='$sum_count', income='$income', sum_income='$sum_income', created_at='$nowTime'
            on duplicate key
            update  day_count='$day_count', sum_count='$sum_count', income='$income', sum_income='$sum_income', update_at='$nowTime'
            ");
					if ($result) {
						$logdata = "$datetime day_count $day_count sum_count $sum_count income $income sum_income $sum_income ";
						$logpath = 'crontab_h_Page_' . $thistime . '.log';
						unset($day_count);
						unset($sum_count);
						unset($income);
						unset($sum_income);
					}
				}
			}
		} else {
			$i = $sum_day;

			$datetime = date('Y-m-d', strtotime("-" . $i . " days", time()));
			dump($datetime);

			$dayCount = DB::connection('mysql')->select("select count(*) as day_count from t_users 
where date(created_at) = '$datetime' ")[0];
			$sumCount = DB::connection('mysql')->select("select count(*) as sum_count from t_users 
where date(created_at) <= '$datetime' ")[0];
			$dayPrice = DB::connection('mysql')->select("select sum(price) as income from t_orders 
where date(created_at) = '$datetime' and order_state = '1' ")[0];
			$sumPrice = DB::connection('mysql')->select("select sum(price) as sum_income from t_orders 
where date(created_at) <= '$datetime' and order_state = '1' ")[0];

			$day_count  = $dayCount->day_count ? $dayCount->day_count : 0;
			$sum_count  = $sumCount->sum_count ? $sumCount->sum_count : 0;
			$income     = $dayPrice->income ? $dayPrice->income : 0;
			$sum_income = $sumPrice->sum_income ? $sumPrice->sum_income : 0;
			//            dump($day_count);dump($sum_count);dump($income);dump($sum_income);

			if ($nosql == 0) {
				$nowTime = Utils::getTime();
				$result  = DB::connection('mysql_stat')->insert
				("
        insert into t_dash_page set date='$datetime', day_count='$day_count', sum_count='$sum_count', income='$income', sum_income='$sum_income', created_at='$nowTime'
        on duplicate key
        update  day_count='$day_count', sum_count='$sum_count', income='$income', sum_income='$sum_income', update_at='$nowTime'
        ");
				if ($result) {
					$logdata = "$datetime day_count $day_count sum_count $sum_count income $income sum_income $sum_income ";
					$logpath = 'crontab_h_Page_' . $thistime . '.log';
					unset($day_count);
					unset($sum_count);
					unset($income);
					unset($sum_income);
				}
			}

		}

		$ntime     = microtime(true);
		$totaltime = $ntime - $stime;
		dump($totaltime);

	}

	/**
	 *插入中间表天活跃用户数+天新增用户数+天新增收入的统计数据
	 *
	 * @param sum_day 日期参数 >0
	 * @param nosql   0默认执行sql操作 ； 1 不执行sql操作
	 * @param nofor   自定义单日统计
	 * @param area    区间参数，单位小时
	 * @param noto    1 循环跳转 、0 不跳转
	 *
	 * @return redirect
	 */
	public function insertTDashStatDayCount ()
	{
		set_time_limit(600); // set timeout seconds
		$stime    = microtime(true);
		$sum_day  = Input::get('sum_day', 0);
		$nosql    = Input::get('nosql', 0);
		$nofor    = Input::get('nofor', 0);
		$noto     = Input::get('noto', 0);
		$thistime = date('Y-m-d', time());

		if ($nofor == 0) {
			for ($i = 0; $i < $sum_day; $i++) {
				$datetime   = date('Y-m-d', strtotime("-" . $i . " days", time()));
				$table_date = date('Y_m_d', strtotime("-" . $i . " days", time()));
				$logs_table = 't_api_logs_' . $table_date;

				$start_time = date('Y-m-d 00:00:00', strtotime($datetime));
				$end_time   = date('Y-m-d 23:59:59', strtotime($datetime));

				//todo:1.查找 当天及以前 所有的appid
				$appid_all   = DB::connection("mysql_config")->select("
            select distinct(app_id) from t_app_conf where created_at <= '$end_time'
            ");
				$appid_array = "";
				foreach ($appid_all as $item) {
					$appid_array[] = $item->app_id;
				}
				//$queryAppid = implode("','", $appid_array);
				//dump($queryAppid);

				//dump($datetime);dump($logs_table);
				//检测分表是否存在
				//                $table_created = DB::connection("mysql_log")->select("show tables like '$logs_table'");
				//                $logs_table = $table_created? $logs_table : 't_api_logs'; //改为查询旧表
				//appid对应的天活跃用户
				$active_count = DB::connection("mysql_log")->select("
            select app_id, count(distinct(user_id)) as value from $logs_table  group by app_id
            ");
				//dump($active_count);
				$active_count = $this->deal_null_data($appid_array, $active_count);
				//dump($active_count);

				//appid对应的天新增用户
				$add_count = DB::connection("mysql")->select("
select app_id, count(*) as value from t_users
where created_at >= '$start_time' and created_at <= '$end_time' group by app_id
");
				$add_count = $this->deal_null_data($appid_array, $add_count);

				//appid对应的天新增收入
				$income = DB::connection("mysql")->select("
select app_id, sum(price) as value from t_orders
where created_at >= '$start_time' and created_at <= '$end_time' and order_state = '1' group by app_id
            ");
				$income = $this->deal_null_data($appid_array, $income);
				//dump($income);
				//插入数据到中间表

				foreach ($appid_array as $appid) {
					$nowTime = Utils::getTime();
					if ($nosql == 0) {
						$result = DB::connection("mysql_stat")->insert
						("
                insert into t_dash_stat_daycount set app_id = '$appid', date='$datetime', hour='0',
                add_count='$add_count[$appid]', active_count='$active_count[$appid]', income='$income[$appid]', created_at='$nowTime'
                on duplicate key
                update add_count='$add_count[$appid]', active_count='$active_count[$appid]', income='$income[$appid]', update_at='$nowTime'
                ");
					} else {
						$result = 1;
					}
					if ($result) {
						$logdata = "$appid $datetime add_count $add_count[$appid] active_count $active_count[$appid] iincome $income[$appid] ";
						$logpath = 'crontab_h_StatDayCount_' . $thistime . '.log';
						unset($add_count[ $appid ]);
						unset($active_count[ $appid ]);
						unset($income[ $appid ]);
					}

				}///dump($active_count);

				unset($appid_all);
				unset($active_count);
				unset($add_count);
				unset($income);

			}
		} else {
			//自定义单日统计
			$area       = Input::get('area', 0);
			$day_count  = $sum_day + $area;
			$datetime   = date('Y-m-d', strtotime("-" . $day_count . " days", time()));
			$table_date = date('Y_m_d', strtotime("-" . $day_count . " days", time()));
			$logs_table = 't_api_logs_' . $table_date;
			dump($datetime);//dump($logs_table);

			//todo:1.查找 当天及以前 所有的appid
			$appid_all   = DB::connection("mysql_config")->select("
            select distinct(app_id) from t_app_conf where date(created_at) <= '$datetime'
            ");
			$appid_array = "";
			foreach ($appid_all as $item) {
				$appid_array[] = $item->app_id;
			}
			//$queryAppid = implode("','", $appid_array);
			//dump($queryAppid);

			//检测分表是否存在
			$table_created = DB::connection("mysql_log")->select("show tables like '$logs_table'");
			$logs_table    = $table_created ? $logs_table : 't_api_logs'; //改为查询旧表
			//appid对应的天活跃用户
			$active_count = DB::connection("mysql_log")->select("
        select app_id, count(distinct(user_id)) as value from $logs_table
          where app_id in ( select distinct(app_id) from db_ex_config.t_app_conf where date(created_at) <= '$datetime'
          ) and  date(created_at) = '$datetime' group by app_id
        ");
			//dump($active_count);
			//            dump(self::deal_Notnull_data($appid_array,$active_count));
			$active_count = $this->deal_null_data($appid_array, $active_count);
			//dump($active_count);

			//appid对应的天新增用户
			$add_count = DB::connection("mysql")->select("
        select app_id, count(*) as value from t_users
        where app_id in ( select distinct(app_id) from db_ex_config.t_app_conf where date(created_at) <= '$datetime'
        ) and date(created_at) = '$datetime' group by app_id
        ");
			//            dump(self::deal_Notnull_data($appid_array,$add_count));
			$add_count = $this->deal_null_data($appid_array, $add_count);
			//dump($add_count);

			//appid对应的天新增收入
			$income = DB::connection("mysql")->select("
        select app_id, sum(price) as value from t_orders
        where app_id in ( select distinct(app_id) from db_ex_config.t_app_conf where date(created_at) <= '$datetime'
        ) and date(created_at) = '$datetime' 
         and order_state = '1' group by app_id
        ");
			//            dump(self::deal_Notnull_data($appid_array,$income));
			$income = $this->deal_null_data($appid_array, $income);
			//dump($income);

			//插入数据到中间表

			foreach ($appid_array as $appid) {
				$nowTime = Utils::getTime();
				if ($nosql == 0) {
					$result = DB::connection("mysql_stat")->insert
					("
            insert into t_dash_stat_daycount set app_id = '$appid', date='$datetime', hour='0',
            add_count='$add_count[$appid]', active_count='$active_count[$appid]', income='$income[$appid]', created_at='$nowTime'
            on duplicate key
            update add_count='$add_count[$appid]', active_count='$active_count[$appid]', income='$income[$appid]', update_at='$nowTime'
            ");
				} else {
					$result = 1;
				}
				if ($result) {
					$logdata = "$appid $datetime add_count $add_count[$appid] active_count $active_count[$appid] iincome $income[$appid] ";
					$logpath = 'crontab_h_StatDayCount_' . $thistime . '.log';
					unset($add_count[ $appid ]);
					unset($active_count[ $appid ]);
					unset($income[ $appid ]);
				}

			}///dump($active_count);

			//手跑可自动跳转 noto=1
			if ($day_count > $area && $noto == 1) {
				$sum_day = $sum_day - 1;
				$sec     = 3;
				sleep($sec);
				dump(" sleeping: $sec s..... ");

				return redirect('/BShell/insertTDashStatDayCount?nofor=1&noto=1&nosql=' . $nosql . '&area=' . $area . '&sum_day=' . $sum_day);
			}
		}

		$ntime     = microtime(true);
		$totaltime = $ntime - $stime;

		return $totaltime;
		//        dump($totaltime);

	}

	//历史单天付费活跃用户数统计

	public function deal_null_data ($appIds, $queryData)
	{
		$tempData = "";
		foreach ($appIds as $appid) {
			$isMatch = false;
			foreach ($queryData as $item) {
				if ($item->app_id == $appid) {
					$isMatch            = true;
					$tempData[ $appid ] = $item->value;
				}
			}
			if (!$isMatch) {
				$tempData[ $appid ] = 0;
			}
		}

		return $tempData;
	}

	//统计（ 日付费活跃用户 ）并插入中间表

	/**
	 *插入中间表天付费活跃用户数的统计数据
	 *
	 * @param sum_day 日期参数 >0
	 * @param nosql   0默认执行sql操作 ； 1 不执行sql操作
	 *
	 * @return redirect
	 */
	public function insertTDashStatPaidActiveDayCount ()
	{
		set_time_limit(600); // set timeout seconds
		$stime    = microtime(true);
		$sum_day  = Input::get('sum_day', 0);
		$nosql    = Input::get('nosql', 0);
		$thistime = date('Y-m-d', time());

		for ($i = 0; $i < $sum_day; $i++) {
			$datetime   = date('Y-m-d', strtotime("-" . $i . " days", time()));
			$table_date = date('Y_m_d', strtotime("-" . $i . " days", time()));
			$logs_table = 't_api_logs_' . $table_date;
			//            dump($datetime);//dump($logs_table);

			//todo:1.查找 当天及以前注册的 所有的appid
			$appid_all   = DB::connection("mysql_config")->select("
            select distinct(app_id) from t_app_conf where date(created_at) <= '$datetime'
            ");
			$appid_array = "";
			foreach ($appid_all as $item) {
				$appid_array[] = $item->app_id;
			}
			//$queryAppid = implode("','", $appid_array);
			//dump($queryAppid);

			//插入数据到中间表
			self::insertPayActiveDayCount($appid_array, $datetime, $logs_table, $nosql, $thistime);

		}

		$ntime     = microtime(true);
		$totaltime = $ntime - $stime;
		//        dump($totaltime);

	}

	public function insertPayActiveDayCount ($app_id_array, $datetime, $logs_table, $nosql, $thistime)
	{
		$logpath = 'crontab_h_StatPaidActiveDayCount_' . $thistime . '.log';

		$query_time = date('Y-m-d 23:59:59', strtotime($datetime));

		//待优化
		$payArray = DB::select("
SELECT t1.app_id, count(*) as count from (
  SELECT
    app_id,
    user_id
  FROM db_ex_logs.$logs_table
  GROUP BY app_id, user_id
) t1 JOIN (
  SELECT app_id, user_id from db_ex_business.t_purchase
  where is_deleted != 1 and created_at <= '$query_time' GROUP BY app_id, user_id
) t2 on t1.app_id = t2.app_id and t1.user_id = t2.user_id
  GROUP BY t1.app_id
");
		//        dump($payArray);

		foreach ($payArray as $item) {
			$app_id   = $item->app_id;
			$payCount = $item->count;
			$nowTime  = Utils::getTime();

			if ($nosql == 0) {
				$result = DB::connection("mysql_stat")->insert("
insert into t_dash_stat_daycount set app_id = '$app_id', date='$datetime', hour='0',
paid_active_count='$payCount', created_at='$nowTime'
on duplicate key
update  paid_active_count='$payCount',  update_at='$nowTime'
      ");
			} else {
				$result = 1;
			}

			if ($result) {
				$logdata = "$app_id $datetime paid_active_count $payCount ";
				//                unset($paidactive_count);
			}
		}

		unset($payArray);

	}

	//历史单天付费用户数统计

	public function insertTDashStatPaidActiveOneDayCount ()
	{
		set_time_limit(600); // set timeout seconds
		$stime   = microtime(true);
		$sum_day = Input::get('sum_day', 0);
		$nosql   = Input::get('nosql', 0);

		$thistime = date('Y-m-d', time());
		//单天付费(活跃)用户数统计
		$datetime   = date('Y-m-d', strtotime("-" . $sum_day . " days", time()));
		$table_date = date('Y_m_d', strtotime("-" . $sum_day . " days", time()));
		$logs_table = 't_api_logs_' . $table_date;
		//        dump($datetime);//dump($logs_table);

		//todo:1.查找 当前天及以前注册 所有的appid
		$appid_all   = DB::connection("mysql_config")->select("
            select distinct(app_id) from t_app_conf where date(created_at) <= '$datetime'
            ");
		$appid_array = "";
		foreach ($appid_all as $item) {
			$appid_array[] = $item->app_id;
		}

		//$queryAppid = implode("','", $appid_array);
		//dump($queryAppid);

		//插入数据到中间表
		self::insertPayActiveDayCount($appid_array, $datetime, $logs_table, $nosql, $thistime);

		$ntime     = microtime(true);
		$totaltime = $ntime - $stime;
		//        dump($totaltime);

	}

	//统计（ 日付费用户 ）并插入中间表

	/**
	 *插入中间表天付费用户数的统计数据
	 *
	 * @param sum_day 日期参数 >0
	 * @param nosql   0默认执行sql操作 ； 1 不执行sql操作
	 *
	 * @return redirect
	 */
	public function insertTDashStatPayDayCount ()
	{
		set_time_limit(600); // set timeout seconds
		$stime    = microtime(true);
		$sum_day  = Input::get('sum_day', 0);
		$nosql    = Input::get('nosql', 0);
		$thistime = date('Y-m-d', time());

		for ($i = 0; $i < $sum_day; $i++) {
			$datetime = date('Y-m-d', strtotime("-" . $i . " days", time()));
			dump($datetime);//dump($logs_table);

			//todo:1.查找 当天及以前 所有的appid
			$appid_all   = DB::connection("mysql_config")->select("
            select distinct(app_id) from t_app_conf where date(created_at) <= '$datetime'
            ");
			$appid_array = "";
			foreach ($appid_all as $item) {
				$appid_array[] = $item->app_id;
			}

			//$queryAppid = implode("','", $appid_array);
			//dump($queryAppid);

			//插入数据到中间表
			self::insertPayDayCount($appid_array, $datetime, $nosql, $thistime);

		}

		$ntime     = microtime(true);
		$totaltime = $ntime - $stime;
		dump($totaltime);

	}

	public function insertPayDayCount ($appid_array, $datetime, $nosql, $thistime)
	{
		//插入数据到中间表
		foreach ($appid_array as $appid) {
			//统计付费用户数据
			$pay_count = DB::connection('mysql')->select("
select count(distinct(user_id)) as value from t_purchase where app_id='$appid' and is_deleted != 1 and date(created_at) = '$datetime' ")[0];
			$pay_count = $pay_count->value;    // dump($appid .' >> '. $pay_count);
			//            if($pay_count) dump($appid .' >> '. $pay_count);

			$nowTime = Utils::getTime();
			if ($nosql == 0) {
				$result = DB::connection("mysql_stat")->insert
				("
            insert into t_dash_stat_daycount set app_id = '$appid', date='$datetime', hour='0',
             pay_count='$pay_count', created_at='$nowTime'
            on duplicate key
            update  pay_count='$pay_count', update_at='$nowTime'
            ");
			} else {
				$result = 1;
			}

			if ($result) {
				$logdata = "$appid $datetime pay_count $pay_count ";
				$logpath = 'crontab_h_StatPayDayCount_' . $thistime . '.log';
				unset($pay_count);
			}

		}

	}

	public function insertTDashStatPayOneDayCount ()
	{
		set_time_limit(600); // set timeout seconds
		$stime   = microtime(true);
		$sum_day = Input::get('sum_day', 0);
		$nosql   = Input::get('nosql', 0);

		$thistime = date('Y-m-d', time());

		//单天付费(活跃)用户数统计
		$datetime = date('Y-m-d', strtotime("-" . $sum_day . " days", time()));
		dump($datetime);//dump($logs_table);

		//todo:1.查找 当天及以前 所有的appid
		$appid_all   = DB::connection("mysql_config")->select("
            select distinct(app_id) from t_app_conf where date(created_at) <= '$datetime'
            ");
		$appid_array = "";
		foreach ($appid_all as $item) {
			$appid_array[] = $item->app_id;
		}
		//$queryAppid = implode("','", $appid_array);
		//dump($queryAppid);

		//插入数据到中间表
		self::insertPayDayCount($appid_array, $datetime, $nosql, $thistime);

		$ntime     = microtime(true);
		$totaltime = $ntime - $stime;
		dump($totaltime);

	}

	//统计（小时付费活跃用户）并插入中间表

	/**
	 * 插入中间表总用户|总收入统计数据
	 *
	 * @param  sum_day 时间参数；>0
	 * @param  nosql   0 默认执行sql操作；1 不执行sql操作
	 * @param  nofor   自定义时间段
	 * @param  area    区间参数，单位天
	 * @param  noto    1 循环跳转 、0 不跳转
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function insertTDashStatSumCountIncome ()
	{
		set_time_limit(3600); // set timeout seconds
		//$stime = microtime(true);
		$sum_day = Input::get('sum_day', 0);
		$nosql   = Input::get('nosql', 0);
		$nofor   = Input::get('nofor', 0);
		$noto    = Input::get('noto', 0);
		//$data[] = "";
		$thistime = date('Y-m-d', time());
		if ($nofor == 0) {
			for ($day_count = 0; $day_count < $sum_day; $day_count++) {
				$datetime = date('Y-m-d', strtotime("-" . $day_count . " days", time()));

				//todo:1.查找 当天及以前 所有的appid
				$appid_all   = DB::connection("mysql_config")->select("
            select distinct(app_id) from t_app_conf where date(created_at) <= '$datetime'
            ");
				$appid_array = "";
				foreach ($appid_all as $item) {
					$appid_array[] = $item->app_id;
				}
				//$queryAppid = implode("','", $appid_array);
				//dump($queryAppid);

				dump($datetime);

				//appid对应的总用户
				$sum_count = DB::connection("mysql")->select("
            select app_id, count(*) as value from t_users 
            where app_id in ( select distinct(app_id) from db_ex_config.t_app_conf where date(created_at) <= '$datetime'
            ) and date(created_at) <= '$datetime' group by app_id
            ");
				$sum_count = $this->deal_null_data($appid_array, $sum_count);
				//dump($sum_count);

				//appid对应总收入
				$sum_income = DB::connection("mysql")->select("
            select app_id, sum(price) as value from t_orders
            where app_id in ( select distinct(app_id) from db_ex_config.t_app_conf where date(created_at) <= '$datetime'
            ) and date(created_at) <= '$datetime' and order_state = '1' group by app_id
            ");//
				$sum_income = $this->deal_null_data($appid_array, $sum_income);
				//dump($sum_income);

				//插入数据到中间表

				foreach ($appid_array as $appid) {
					$nowTime = Utils::getTime();
					if ($nosql == 0) {//保存最新总数到日统计表
						$result = DB::connection("mysql_stat")->insert
						("
                insert into t_dash_stat_daycount set app_id = '$appid', date='$datetime', hour='0', 
                sum_count='$sum_count[$appid]', sum_income='$sum_income[$appid]' ,created_at='$nowTime'    
                on duplicate key 
                update sum_count='$sum_count[$appid]', sum_income='$sum_income[$appid]' ,update_at='$nowTime'
                ");

					} else {
						$result = 1;
					}

					if ($result) {
						$logdata = "$appid $datetime  sum_count $sum_count[$appid] sum_income $sum_income[$appid]";
						$logpath = 'crontab_h_dayCount_' . $thistime . '.log';
					}
					unset($sum_count[ $appid ]);
					unset($sum_income[ $appid ]);

				}//dump($add_count);dump($active_count);dump($sum_count);dump($income);dump($sum_income);
			}
		} else {
			//自定义时间段
			$area      = Input::get('area', 0);
			$day_count = $sum_day + $area;
			$datetime  = date('Y-m-d', strtotime("-" . $day_count . " days", time()));
			dump($datetime);

			//todo:1.查找 当天及以前 所有的appid
			$appid_all   = DB::connection("mysql_config")->select("
            select distinct(app_id) from t_app_conf where date(created_at) <= '$datetime'
            ");
			$appid_array = "";
			foreach ($appid_all as $item) {
				$appid_array[] = $item->app_id;
			}
			//$queryAppid = implode("','", $appid_array);
			//dump($queryAppid);

			//appid对应的总用户
			$sum_count = DB::connection("mysql")->select("
            select app_id, count(*) as value from t_users 
            where app_id in ( select distinct(app_id) from db_ex_config.t_app_conf where date(created_at) <= '$datetime'
            ) and date(created_at) <= '$datetime' group by app_id
            ");
			$sum_count = $this->deal_null_data($appid_array, $sum_count);
			//dump($sum_count);

			//appid对应总收入
			$sum_income = DB::connection("mysql")->select("
            select app_id, sum(price) as value from t_orders
            where app_id in ( select distinct(app_id) from db_ex_config.t_app_conf where date(created_at) <= '$datetime'
            ) and date(created_at) <= '$datetime' and order_state = '1' group by app_id
            ");//
			$sum_income = $this->deal_null_data($appid_array, $sum_income);
			//dump($sum_income);

			//插入数据到中间表

			foreach ($appid_array as $appid) {
				$nowTime = Utils::getTime();
				if ($nosql == 0) {   //最终总数到日统计表
					$result = DB::connection("mysql_stat")->insert
					("
                insert into t_dash_stat_daycount set app_id = '$appid', date='$datetime', hour='0', 
                sum_count='$sum_count[$appid]', sum_income='$sum_income[$appid]' ,created_at='$nowTime'    
                on duplicate key 
                update sum_count='$sum_count[$appid]', sum_income='$sum_income[$appid]' ,update_at='$nowTime'
                ");

				} else {
					$result = 1;
				}

				if ($result) {
					$logdata = "$appid $datetime  sum_count $sum_count[$appid] sum_income $sum_income[$appid]";
					$logpath = 'crontab_h_dayCount_' . $thistime . '.log';
				}
				unset($sum_count[ $appid ]);
				unset($sum_income[ $appid ]);
			}

			//手跑可自动跳转 noto=1
			if ($day_count > $area && $noto == 1) {
				$sum_day = $sum_day - 1;
				$sec     = 3;
				sleep($sec);
				dump(" sleeping: $sec s..... ");

				return redirect('/BShell/SumCountIncomeDayCount?nofor=1&noto=1&nosql=' . $nosql . '&area=' . $area . '&sum_day=' . $sum_day);
			}
		}

		/*$ntime = microtime(true);
		$totaltime = $ntime-$stime;
		dump('totaltime: '.$totaltime);*/

		//return $appid_array;

	}

	/**
	 * 插入中间表小时付费活跃用户统计数据
	 *
	 * @param  sum_hour 时间参数；>0
	 * @param  nosql    0 默认执行sql操作；1 不执行sql操作
	 * @param  nofor    0 默认执行for循环；1 联合area参数循环出区间时间段内小时数据统计
	 * @param  area     区间参数，单位小时
	 * @param  noto     1 循环跳转 、0 不跳转
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function insertTDashStatPaidActive ()
	{
		set_time_limit(600); // set timeout seconds
		$stime    = microtime(true);
		$sum_hour = Input::get('sum_hour', 0);
		$nosql    = Input::get('nosql', 0);
		$nofor    = Input::get('nofor', 0);
		//$data[] = "";
		$thistime = date('Y-m-d', time());
		if ($nofor == 0) {
			for ($hour_count = 0; $hour_count < $sum_hour; $hour_count++) {
				$datetime        = date('Y-m-d', strtotime("-" . $hour_count . " hours", time()));
				$tmp_hour        = date('H', strtotime("-" . $hour_count . " hours", time()));
				$logs_table_date = date('Y_m_d', strtotime("-" . $hour_count . "hours", time()));

				//todo:1.查找 当天及以前 所有的appid
				$appid_all   = DB::connection("mysql_config")->select("
            select distinct(app_id) from t_app_conf where date(created_at) <= '$datetime'
            ");
				$appid_array = "";
				foreach ($appid_all as $item) {
					$appid_array[] = $item->app_id;
				}
				//$queryAppid = implode("','", $appid_array);
				//dump($queryAppid);

				//dump($tmp_hour);
				$current_hour = date('Y-m-d H:00:00', strtotime("+1 hours -" . $hour_count . " hours", time()));
				//dump($current_hour);
				//appid对应的小时付费活跃用户

				//插入数据到中间表
				self::insertHourPaidActive($logs_table_date, $appid_array, $datetime, $tmp_hour, $current_hour, $nosql, $thistime);
			}
		} else //if($nofor == 1 )
		{
			//用于手动补充历史数据
			$noto = Input::get('into', 0);
			////区域数据处理
			//批量处理
			if ($noto == 0) {
				$area      = Input::get('area', 1);
				$area_hour = $sum_hour + $area;
				for ($hour_count = $sum_hour; $hour_count < $area_hour; $hour_count++) {
					$datetime        = date('Y-m-d', strtotime("-" . $hour_count . " hours", time()));
					$tmp_hour        = date('H', strtotime("-" . $hour_count . " hours", time()));
					$logs_table_date = date('Y_m_d', strtotime("-" . $hour_count . "hours", time()));

					//todo:1.查找 当天及以前 所有的appid
					$appid_all   = DB::connection("mysql_config")->select("
            select distinct(app_id) from t_app_conf where date(created_at) <= '$datetime'
            ");
					$appid_array = "";
					foreach ($appid_all as $item) {
						$appid_array[] = $item->app_id;
					}
					//$queryAppid = implode("','", $appid_array);
					//dump($queryAppid);

					//dump($tmp_hour);
					$current_hour = date('Y-m-d H:00:00', strtotime("+1 hours -" . $hour_count . " hours", time()));
					dump($current_hour);

					//appid对应的小时付费活跃用户

					//插入数据到中间表
					self::insertHourPaidActive($logs_table_date, $appid_array, $datetime, $tmp_hour, $current_hour, $nosql, $thistime);

				}

			} else //循环处理
			{
				$area       = Input::get('area', 0);
				$hour_count = $sum_hour + $area;

				$datetime        = date('Y-m-d', strtotime("-" . $hour_count . " hours", time()));
				$tmp_hour        = date('H', strtotime("-" . $hour_count . " hours", time()));
				$logs_table_date = date('Y_m_d', strtotime("-" . $hour_count . "hours", time()));

				//todo:1.查找 当天及以前 所有的appid
				$appid_all   = DB::connection("mysql_config")->select("
        select distinct(app_id) from t_app_conf where date(created_at) <= '$datetime'
        ");
				$appid_array = "";
				foreach ($appid_all as $item) {
					$appid_array[] = $item->app_id;
				}
				//$queryAppid = implode("','", $appid_array);
				//dump($queryAppid);

				//dump($tmp_hour);
				$current_hour = date('Y-m-d H:00:00', strtotime("+1 hours -" . $hour_count . " hours", time()));
				dump($current_hour);

				//appid对应的小时付费活跃用户

				//插入数据到中间表
				self::insertHourPaidActive($logs_table_date, $appid_array, $datetime, $tmp_hour, $current_hour, $nosql, $thistime);

				//手跑可自动跳转 noto=1
				if ($hour_count > $area) {
					$ntime     = microtime(true);
					$totaltime = $ntime - $stime;
					dump('totaltime: ' . $totaltime);

					$sum_hour = $sum_hour - 1;
					$sec      = 3;
					sleep($sec);
					dump(" sleeping:'$sec's..... ");

					return redirect('/BShell/insertTDashStatPaidActive?nofor=1&noto=1&nosql=' . $nosql . '&area=' . $area . '&sum_hour=' . $sum_hour);
				}

			}

		}

		$ntime     = microtime(true);
		$totaltime = $ntime - $stime;

		return $totaltime;
		//        dump('totaltime: '.$totaltime);
		//return $appid_array;

	}

	//统计（小时付费用户 | 小时付费总用户）并插入中间表

	public function insertHourPaidActive ($logs_table_date, $appid_array, $datetime, $tmp_hour, $current_hour, $nosql, $thistime)
	{
		$logs_table = 't_api_logs_' . $logs_table_date;
		//检测分表是否存在
		//        $table_created = DB::connection("mysql_log")->select("show tables like '$logs_table'");
		//        $logs_table = $table_created? $logs_table : 't_api_logs'; //改为查询旧表

		$start_time = $datetime . " " . $tmp_hour . ":00:00";
		$end_time   = $datetime . " " . $tmp_hour . ":59:59";

		$pay_count = DB::connection("mysql")->select("
SELECT t1.app_id, count(*) as count from (
  SELECT
    app_id,
    user_id
  FROM db_ex_logs.$logs_table
  WHERE created_at >= '$start_time' and created_at <= '$end_time'
  GROUP BY app_id, user_id
) t1 JOIN (
  SELECT app_id, user_id from db_ex_business.t_purchase
  where is_deleted != 1 and created_at <= '$end_time' GROUP BY app_id, user_id
) t2 on t1.app_id = t2.app_id and t1.user_id = t2.user_id
GROUP BY t1.app_id
");

		//插入数据到中间表
		foreach ($pay_count as $item) {
			//            //统计付费活跃用户数据
			//            $paidactive_count = DB::connection('mysql')->select("
			//select count(distinct(user_id)) as value from t_purchase where user_id in (
			//    select distinct(user_id) as user_id from db_ex_logs.$logs_table
			//                where app_id ='$appid' and date(created_at) = '$datetime' and hour(created_at) = '$tmp_hour'
			//) and app_id='$appid' and created_at < '$current_hour'
			//")[0];
			//            $paidactive_count = $paidactive_count->value;
			$appid            = $item->app_id;
			$paidactive_count = $item->count;
			//                if($paidactive_count) dump($appid.' ::  '.$paidactive_count.' / ');//.count(explode("','",$user_ids)));

			$nowTime = Utils::getTime();
			if ($nosql == 0) {
				$result = DB::connection("mysql_stat")->insert("
insert into t_dash_stat set app_id = '$appid', date='$datetime', hour='$tmp_hour',
paid_active_count='$paidactive_count' ,created_at='$nowTime'
on duplicate key
update paid_active_count='$paidactive_count',update_at='$nowTime'
");
			} else {
				$result = 1;
			}
			//logs
			if ($result) {
				$logdata = "$appid $datetime $tmp_hour  paid_active_count $paidactive_count";
				$logpath = 'crontab_h_StatPaidActive_' . $thistime . '.log';
				unset($paidactive_count[ $appid ]);
			}

		}
		unset($pay_count);
	}

	/**
	 * 插入中间表小时(、总)付费用户统计数据
	 *
	 * @param  sum_hour 时间参数；>0
	 * @param  nosql    0 默认执行sql操作；1 不执行sql操作
	 * @param  nofor    0 默认执行for循环；1 联合area参数循环出区间时间段内小时数据统计
	 * @param  area     区间参数，单位小时
	 * @param  noto     1 循环跳转 、0 不跳转
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function insertTDashStatPayCount ()
	{
		set_time_limit(600); // set timeout seconds
		$stime    = microtime(true);
		$sum_hour = Input::get('sum_hour', 0);
		$nosql    = Input::get('nosql', 0);
		$nofor    = Input::get('nofor', 0);
		//$data[] = "";

		$thistime = date('Y-m-d', time());
		if ($nofor == 0) {
			for ($hour_count = 0; $hour_count < $sum_hour; $hour_count++) {
				$datetime = date('Y-m-d', strtotime("-" . $hour_count . " hours", time()));
				$tmp_hour = date('H', strtotime("-" . $hour_count . " hours", time()));
				//dump($tmp_hour);
				$current_hour = date('Y-m-d H:00:00', strtotime("+1 hours -" . $hour_count . " hours", time()));
				dump($current_hour);

				//todo:1.查找 当天及以前 所有的appid
				$appid_all   = DB::connection("mysql_config")->select("
            select distinct(app_id) from t_app_conf where date(created_at) <= '$datetime'
            ");
				$appid_array = "";
				foreach ($appid_all as $item) {
					$appid_array[] = $item->app_id;
				}
				//$queryAppid = implode("','", $appid_array);
				//dump($queryAppid);

				//插入数据到中间表
				self::insertHourPayCount($hour_count, $datetime, $tmp_hour, $appid_array, $current_hour, $nosql, $thistime);
			}
		} else //if($nofor == 1 )
		{
			//区域数据处理//用于手动补充历史数据
			$noto = Input::get('noto', 0);
			//批量处理
			if ($noto == 0) {
				$area      = Input::get('area', 1);
				$area_hour = $sum_hour + $area;
				for ($hour_count = $sum_hour; $hour_count < $area_hour; $hour_count++) {
					$datetime = date('Y-m-d', strtotime("-" . $hour_count . " hours", time()));
					$tmp_hour = date('H', strtotime("-" . $hour_count . " hours", time()));
					//dump($tmp_hour);
					$current_hour = date('Y-m-d H:00:00', strtotime("+1 hours -" . $hour_count . " hours", time()));
					dump($current_hour);

					//todo:1.查找 当天及以前 所有的appid
					$appid_all   = DB::connection("mysql_config")->select("
            select distinct(app_id) from t_app_conf where date(created_at) <= '$datetime'
            ");
					$appid_array = "";
					foreach ($appid_all as $item) {
						$appid_array[] = $item->app_id;
					}
					//$queryAppid = implode("','", $appid_array);
					//dump($queryAppid);

					//插入数据到中间表
					self::insertHourPayCount($hour_count, $datetime, $tmp_hour, $appid_array, $current_hour, $nosql, $thistime);

				}
			} else //循环处理
			{
				$area       = Input::get('area', 0);
				$hour_count = $sum_hour + $area;
				$datetime   = date('Y-m-d', strtotime("-" . $hour_count . " hours", time()));
				$tmp_hour   = date('H', strtotime("-" . $hour_count . " hours", time()));
				//dump($tmp_hour);
				$current_hour = date('Y-m-d H:00:00', strtotime("+1 hours -" . $hour_count . " hours", time()));
				dump($current_hour);

				//todo:1.查找 当天及以前 所有的appid
				$appid_all   = DB::connection("mysql_config")->select("
            select distinct(app_id) from t_app_conf where date(created_at) <= '$datetime'
            ");
				$appid_array = "";
				foreach ($appid_all as $item) {
					$appid_array[] = $item->app_id;
				}
				//$queryAppid = implode("','", $appid_array);
				//dump($queryAppid);

				//插入数据到中间表
				self::insertHourPayCount($hour_count, $datetime, $tmp_hour, $appid_array, $current_hour, $nosql, $thistime);

				//手跑可自动跳转 noto=1
				if ($hour_count > $area) {
					$ntime     = microtime(true);
					$totaltime = $ntime - $stime;
					dump('totaltime: ' . $totaltime);

					$sum_hour = $sum_hour - 1;
					$sec      = 3;
					sleep($sec);
					dump(" sleeping:'$sec's..... ");

					return redirect('/BShell/insertTDashStatPayCount?nofor=1&noto=1&nosql=' . $nosql . '&area=' . $area . '&sum_hour=' . $sum_hour);
				}
			}

		}

		$ntime     = microtime(true);
		$totaltime = $ntime - $stime;
		dump('totaltime: ' . $totaltime);
		//return $appid_array;

	}

	//重跑单个appid用户中间表小时统计数据

	public function insertHourPayCount ($hour_count, $datetime, $tmp_hour, $appid_array, $current_hour, $nosql, $thistime)
	{
		//appid对应的小时付费用户
		$pay_count = DB::connection("mysql")->select("
            select app_id, count(DISTINCT user_id) as value from t_purchase
            where app_id in ( select distinct(app_id) from db_ex_config.t_app_conf where date(created_at) <= '$datetime'
            ) and is_deleted != 1 and date(created_at) = '$datetime' and hour(created_at) = '$tmp_hour' group by app_id
            ");
		//        dump($this->deal_Notnull_data($appid_array, $pay_count));
		$pay_count = $this->deal_null_data($appid_array, $pay_count);
		//dump($pay_count);

		//appid对应的小时总付费用户
		$sum_pay = DB::connection("mysql")->select("
            select app_id, count(DISTINCT user_id) as value from t_purchase 
            where app_id in ( select distinct(app_id) from db_ex_config.t_app_conf where date(created_at) <= '$datetime'
            ) and is_deleted != 1 and created_at < '$current_hour' group by app_id
            ");
		//        dump($this->deal_Notnull_data($appid_array, $sum_pay));
		$sum_pay = $this->deal_null_data($appid_array, $sum_pay);
		//dump($sum_pay);

		//插入数据到中间表
		foreach ($appid_array as $appid) {
			$nowTime = Utils::getTime();
			if ($nosql == 0) {
				$result  = DB::connection("mysql_stat")->insert
				("
                    insert into t_dash_stat set app_id = '$appid', date='$datetime', hour='$tmp_hour',
                    pay_count='$pay_count[$appid]' ,sum_pay='$sum_pay[$appid]' ,created_at='$nowTime'
                    on duplicate key
                    update pay_count='$pay_count[$appid]',sum_pay='$sum_pay[$appid]',update_at='$nowTime'
                      ");
				$result2 = 0;
				if ($result && ($hour_count == 0 || $tmp_hour == 23)) {//插入最新|最终总付费用户数到日统计表
					$result2 = DB::connection("mysql_stat")->insert
					("
                    insert into t_dash_stat_daycount set app_id = '$appid', date='$datetime', hour='0' ,
                    sum_pay='$sum_pay[$appid]' ,created_at='$nowTime'
                    on duplicate key
                    update sum_pay='$sum_pay[$appid]',update_at='$nowTime'
                      ");
				}
			} else {
				$result  = 1;
				$result2 = 1;
			}
			//logs
			if ($result) {
				$logdata = "$appid $datetime $tmp_hour pay_count $pay_count[$appid] sum_pay $sum_pay[$appid]";
				$logpath = 'crontab_h_StatPayUserCount_' . $thistime . '.log';
			}
			if ($result2) {
				$logdata = "$appid $datetime $tmp_hour sum_pay $sum_pay[$appid] ";
				$logpath = 'crontab_h_dayCount_' . $thistime . '.log';
			}
			unset($pay_count[ $appid ]);
			unset($sum_pay[ $appid ]);
		}

	}

	/**
	 * 插入中间表小时记统计数据
	 *
	 * @param  sum_hour 时间参数；>0
	 * @param  nosql    0 默认执行sql操作；1 不执行sql操作
	 * @param  nofor    自定义时间段
	 * @param  noto     default 0 \ 1 redirect
	 * @param  area     default 0 \ sum_hour+area
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function insertTDashStat ()
	{
		//        session(['stat_hour' => 'h_21']);
		set_time_limit(86400); // set timeout seconds
		$stime    = microtime(true);
		$sum_hour = Input::get('sum_hour', 0);
		$nosql    = Input::get('nosql', 0);
		$nofor    = Input::get('nofor', 0);
		$noto     = Input::get('noto', 0);
		//$data[] = "";
		$thistime = date('Y-m-d', time()); //dump(session('stat_hour'));
		if ($nofor == 0) {
			for ($hour_count = 0; $hour_count < $sum_hour; $hour_count++) {
				$datetime        = date('Y-m-d', strtotime("-" . $hour_count . " hours", time()));
				$tmp_hour        = date('H', strtotime("-" . $hour_count . " hours", time()));
				$logs_table_date = date('Y_m_d', strtotime("-" . $hour_count . "hours", time()));

				$start_time = $datetime . " " . $tmp_hour . ":00:00";
				$end_time   = $datetime . " " . $tmp_hour . ":59:59";

				//todo:1.查找 当天及以前 所有的appid
				$appid_all   = DB::connection("mysql_config")->select("
            select app_id from t_app_conf where wx_app_type = 1 and date(created_at) <= '$datetime'
            ");
				$appid_array = "";
				foreach ($appid_all as $item) {
					$appid_array[] = $item->app_id;
				}
				//$queryAppid = implode("','", $appid_array);
				//dump($queryAppid);

				//dump($tmp_hour);
				$current_hour = date('Y-m-d H:00:00', strtotime("+1 hours -" . $hour_count . " hours", time()));
				//                dump($current_hour);

				//appid对应的小时新增用户
				$add_count = DB::connection("mysql")->select("
SELECT app_id, count(*) as value from t_users
where created_at >= '$start_time' and created_at <='$end_time'
GROUP BY app_id
");
				$add_count = $this->deal_null_data($appid_array, $add_count);
				//dump($add_count);

				//appid对应的小时活跃用户
				$logs_table = 't_api_logs_' . $logs_table_date;
				//检测分表是否存在
				//                $table_created = DB::connection("mysql_log")->select("show tables like '$logs_table'");
				//                $logs_table = $table_created? $logs_table : 't_api_logs'; //改为查询旧表

				$active_count = DB::connection("mysql_log")->select("
select app_id, count(distinct(user_id)) as value, created_at from $logs_table
where created_at >= '$start_time' and created_at <= '$end_time' group by app_id
");//dump($active_count);
				$active_count = $this->deal_null_data($appid_array, $active_count);
				//dump($active_count);

				//appid对应的小时总用户
				$sum_count = DB::connection("mysql")->select("
select app_id, count(*) as value from t_users
where created_at < '$current_hour' group by app_id
            ");
				$sum_count = $this->deal_null_data($appid_array, $sum_count);
				//dump($sum_count);

				//appid对应的小时收入
				$income = DB::connection("mysql")->select("
select app_id, sum(price) as value from t_orders
where  created_at >= '$start_time' and created_at <='$end_time' and order_state = '1'
group by app_id
");
				$income = $this->deal_null_data($appid_array, $income);
				//dump($income);

				//appid对应小时总收入
				$sum_income = DB::connection("mysql")->select("
            select app_id, sum(price) as value from t_orders
            where created_at < '$current_hour' and order_state = '1' group by app_id
            ");//
				$sum_income = $this->deal_null_data($appid_array, $sum_income);
				//dump($sum_income);

				//插入数据到中间表

				foreach ($appid_array as $appid) {
					$nowTime = Utils::getTime();
					if ($nosql == 0) {
						$result = DB::connection("mysql_stat")->insert
						("
                insert into t_dash_stat set app_id = '$appid', date='$datetime', hour='$tmp_hour', 
                add_count='$add_count[$appid]', active_count='$active_count[$appid]', sum_count='$sum_count[$appid]', income='$income[$appid]', sum_income='$sum_income[$appid]' ,created_at='$nowTime'    
                on duplicate key 
                update add_count='$add_count[$appid]', active_count='$active_count[$appid]', sum_count='$sum_count[$appid]', income='$income[$appid]', sum_income='$sum_income[$appid]' ,update_at='$nowTime'
                ");

					} else {
						$result = 1;
					}
					if ($result) {
						$logdata = "$appid $datetime $tmp_hour  add_count $add_count[$appid] active_count $active_count[$appid] sum_count $sum_count[$appid] income $income[$appid] sum_income $sum_income[$appid]";
						$logpath = 'crontab_h_Stat_' . $thistime . '.log';
					}

				}//dump($add_count);dump($active_count);dump($sum_count);dump($income);dump($sum_income);

				unset($appid_all);
				unset($add_count);
				unset($active_count);
				unset($sum_count);
				unset($income);
				unset($sum_income);
			}
		} else {
			//自定义时间段
			$area            = Input::get('area', 0);
			$hour_count      = $sum_hour + $area;
			$datetime        = date('Y-m-d', strtotime("-" . $hour_count . " hours", time()));
			$tmp_hour        = date('H', strtotime("-" . $hour_count . " hours", time()));
			$logs_table_date = date('Y_m_d', strtotime("-" . $hour_count . "hours", time()));
			//dump($tmp_hour);
			$current_hour = date('Y-m-d H:00:00', strtotime("+1 hours -" . $hour_count . " hours", time()));
			dump($current_hour);

			//todo:1.查找 当天及以前 所有的appid
			$appid_all   = DB::connection("mysql_config")->select("
            select distinct(app_id) from t_app_conf where date(created_at) <= '$datetime'
            ");
			$appid_array = "";
			foreach ($appid_all as $item) {
				$appid_array[] = $item->app_id;
			}
			//$queryAppid = implode("','", $appid_array);
			//dump($queryAppid);

			//appid对应的小时新增用户
			$add_count = DB::connection("mysql")->select("
            select app_id, count(*) as value from t_users
            where app_id in ( select distinct(app_id) from db_ex_config.t_app_conf where date(created_at) <= '$datetime'
            ) and date(created_at) = '$datetime' and hour(created_at) = '$tmp_hour' group by app_id
            ");
			$add_count = $this->deal_null_data($appid_array, $add_count);
			//dump($add_count);

			//appid对应的小时活跃用户
			$logs_table = 't_api_logs_' . $logs_table_date;
			//检测分表是否存在
			$table_created = DB::connection("mysql_log")->select("show tables like '$logs_table'");
			$logs_table    = $table_created ? $logs_table : 't_api_logs'; //改为查询旧表
			$active_count  = DB::connection("mysql_log")->select("
            select app_id, count(distinct(user_id)) as value from $logs_table
            where app_id in ( select distinct(app_id) from db_ex_config.t_app_conf where date(created_at) <= '$datetime'
            ) and date(created_at) = '$datetime' and hour(created_at) = '$tmp_hour' group by app_id
            ");//dump($active_count);
			$active_count  = $this->deal_null_data($appid_array, $active_count);
			//dump($active_count);

			//appid对应的小时总用户
			$sum_count = DB::connection("mysql")->select("
            select app_id, count(*) as value from t_users 
            where app_id in ( select distinct(app_id) from db_ex_config.t_app_conf where date(created_at) <= '$datetime'
            ) and created_at < '$current_hour' group by app_id
            ");
			$sum_count = $this->deal_null_data($appid_array, $sum_count);
			//dump($sum_count);

			//appid对应的小时收入
			$income = DB::connection("mysql")->select("
            select app_id, sum(price) as value from t_orders
            where app_id in ( select distinct(app_id) from db_ex_config.t_app_conf where date(created_at) <= '$datetime'
            ) and date(created_at) = '$datetime' and hour(created_at) = '$tmp_hour'
             and order_state = '1' group by app_id
            ");
			$income = $this->deal_null_data($appid_array, $income);
			//dump($income);

			//appid对应小时总收入
			$sum_income = DB::connection("mysql")->select("
            select app_id, sum(price) as value from t_orders
            where app_id in ( select distinct(app_id) from db_ex_config.t_app_conf where date(created_at) <= '$datetime'
            ) and created_at < '$current_hour' and order_state = '1' group by app_id
            ");//
			$sum_income = $this->deal_null_data($appid_array, $sum_income);
			//dump($sum_income);

			//插入数据到中间表

			foreach ($appid_array as $appid) {
				$nowTime = Utils::getTime();
				if ($nosql == 0) {
					$result = DB::connection("mysql_stat")->insert
					("
                insert into t_dash_stat set app_id = '$appid', date='$datetime', hour='$tmp_hour', 
                add_count='$add_count[$appid]', active_count='$active_count[$appid]', sum_count='$sum_count[$appid]', income='$income[$appid]', sum_income='$sum_income[$appid]' ,created_at='$nowTime'    
                on duplicate key 
                update add_count='$add_count[$appid]', active_count='$active_count[$appid]', sum_count='$sum_count[$appid]', income='$income[$appid]', sum_income='$sum_income[$appid]' ,update_at='$nowTime'
                ");

				} else {
					$result = 1;
				}

				if ($result) {
					$logdata = "$appid $datetime $tmp_hour  add_count $add_count[$appid] active_count $active_count[$appid] sum_count $sum_count[$appid] income $income[$appid] sum_income $sum_income[$appid]";
					$logpath = 'crontab_h_Stat_' . $thistime . '.log';
				}
				unset($add_count[ $appid ]);
				unset($active_count[ $appid ]);
				unset($sum_count[ $appid ]);
				unset($income[ $appid ]);
				unset($sum_income[ $appid ]);

			}

			//手跑可自动跳转 noto=1
			if ($hour_count > $area && $noto == 1) {
				$sum_hour = $sum_hour - 1;
				$sec      = 3;
				sleep($sec);
				dump(" sleeping:'$sec's..... ");

				return redirect('/BShell/insertTDashStat?nofor=1&noto=1&nosql=' . $nosql . '&area=' . $area . '&sum_hour=' . $sum_hour);
			}

		}

		$ntime     = microtime(true);
		$totaltime = $ntime - $stime;

		//        dump('totaltime: '.$totaltime);
		return "SUCCESS";

		//return $appid_array;

	}
	//重跑单用户单天统计数据

	/**
	 * @param appid 用户参数
	 * @params sum_hour 统计当前时间小时
	 *
	 * @noto   1 自动跳转，0，只执行当前参数
	 * @area   默认0 sum_hour+area
	 * @nosql  默认0 ，1 不写数据库
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function runOneAppStatHourCount ()
	{   //$app_id = AppUtils::getAppID(); dump($app_id);
		set_time_limit(600); //timeout second
		$stime    = microtime(true);
		$appid    = Input::get('appid', '0');
		$sum_hour = Input::get('sum_hour', '0');
		$noto     = Input::get('noto', '0');
		$area     = Input::get('area', '0');
		$nosql    = Input::get('nosql', '0');

		$thistime = date('Y-m-d', time());
		if ($appid) {
			$hour_count      = $sum_hour + $area;
			$datetime        = date('Y-m-d', strtotime("-" . $hour_count . " hours", time()));
			$tmp_hour        = date('H', strtotime("-" . $hour_count . " hours", time()));
			$logs_table_date = date('Y_m_d', strtotime("-" . $hour_count . "hours", time()));
			//dump($tmp_hour);
			$current_hour = date('Y-m-d H:00:00', strtotime("+1 hours -" . $hour_count . " hours", time()));
			dump($current_hour);

			//appid对应的小时新增用户
			$add_count = DB::connection("mysql")->select("
        select count(*) as value from t_users
        where app_id = '$appid' and date(created_at) = '$datetime' and hour(created_at) = '$tmp_hour' 
        ")[0];

			//appid对应的小时活跃用户
			$logs_table = 't_api_logs_' . $logs_table_date;
			//检测分表是否存在
			$table_created = DB::connection("mysql_log")->select("show tables like '$logs_table'");
			$logs_table    = $table_created ? $logs_table : 't_api_logs'; //改为查询旧表
			$active_count  = DB::connection("mysql_log")->select("
        select app_id, count(distinct(user_id)) as value from $logs_table
        where app_id = '$appid' and date(created_at) = '$datetime' and hour(created_at) = '$tmp_hour' 
        ")[0];

			//appid对应的小时收入
			$income = DB::connection("mysql")->select("
        select app_id, sum(price) as value from t_orders
        where app_id = '$appid' and date(created_at) = '$datetime' and hour(created_at) = '$tmp_hour'
         and order_state = '1' 
        ")[0];

			//appid对应的小时付费用户
			$pay_count = DB::connection("mysql")->select("
            select app_id, count(DISTINCT user_id) as value from t_purchase
            where app_id = '$appid' and date(created_at) = '$datetime' and hour(created_at) = '$tmp_hour' 
            ")[0];

			//插入数据到中间表
			$nowTime = Utils::getTime();
			if ($nosql == 0) {
				$result = DB::connection("mysql_stat")->insert
				("
        insert into t_dash_stat set app_id = '$appid', date='$datetime', hour='$tmp_hour', 
        add_count='$add_count->value', pay_count='$pay_count->value', active_count='$active_count->value',
        income='$income->value', created_at='$nowTime'    
        on duplicate key 
        update add_count='$add_count->value', pay_count='$pay_count->value', active_count='$active_count->value',
         income='$income->value', update_at='$nowTime'
        ");

			} else {
				$result = 1;
			}
			if ($result) {
				$logdata = "$appid $datetime $tmp_hour  add_count=$add_count->value pay_count=$pay_count->value active_count=$active_count->value income=$income->value ";
				$logpath = 'crontab_h_OneAppStatHour_' . $thistime . '.log';
			}
			unset($add_count);
			unset($active_count);
			unset($pay_count);
			unset($income);
			unset($active_count);

			$ntime     = microtime(true);
			$totaltime = $ntime - $stime;
			dump('totaltime: ' . $totaltime);

			if ($hour_count > $area && $noto == 1) {
				$sum_hour = $sum_hour - 1;
				$sec      = 3;
				sleep($sec);
				dump(" sleeping:'$sec's..... ");

				return redirect('/BShell/runOneAppStatHourCount?appid=' . $appid . '&nosql=' . $nosql . '&area=' . $area . '&noto=1&sum_hour=' . $sum_hour);
			}
		}

	}

	public function runOneAppStatHourSum ()
	{   //$app_id = AppUtils::getAppID(); dump($app_id);
		set_time_limit(600); //timeout second
		$stime    = microtime(true);
		$appid    = Input::get('appid', '0');
		$sum_hour = Input::get('sum_hour', '0');
		$noto     = Input::get('noto', '0');
		$area     = Input::get('area', '0');
		$nosql    = Input::get('nosql', '0');

		$thistime = date('Y-m-d', time());
		if ($appid) {
			$hour_count      = $sum_hour + $area;
			$datetime        = date('Y-m-d', strtotime("-" . $hour_count . " hours", time()));
			$tmp_hour        = date('H', strtotime("-" . $hour_count . " hours", time()));
			$logs_table_date = date('Y_m_d', strtotime("-" . $hour_count . "hours", time()));
			//dump($tmp_hour);
			$current_hour = date('Y-m-d H:00:00', strtotime("+1 hours -" . $hour_count . " hours", time()));
			dump($current_hour);

			$logs_table = 't_api_logs_' . $logs_table_date;
			//检测分表是否存在
			$table_created = DB::connection("mysql_log")->select("show tables like '$logs_table'");
			$logs_table    = $table_created ? $logs_table : 't_api_logs'; //改为查询旧表

			//appid对应的小时总用户
			$sum_count = DB::connection("mysql")->select("
        select app_id, count(*) as value from t_users 
        where app_id = '$appid' and created_at < '$current_hour' 
        ")[0];

			//appid对应小时总收入
			$sum_income = DB::connection("mysql")->select("
        select app_id, sum(price) as value from t_orders
        where app_id = '$appid' and created_at < '$current_hour' and order_state = '1' 
        ")[0];

			//appid对应的小时总付费用户
			$sum_pay = DB::connection("mysql")->select("
            select app_id, count(DISTINCT user_id) as value from t_purchase 
            where app_id = '$appid' and created_at < '$current_hour' 
            ")[0];

			//统计付费活跃用户数据
			$paidactive_count = DB::connection('mysql')->select("
select count(distinct(user_id)) as value from t_purchase where user_id in (
    select distinct(user_id) as user_id from db_ex_logs.$logs_table 
                where app_id ='$appid' and date(created_at) = '$datetime' and hour(created_at) = '$tmp_hour'
) and app_id='$appid' and created_at < '$current_hour' ")[0];

			//插入数据到中间表
			$nowTime = Utils::getTime();
			if ($nosql == 0) {
				$result = DB::connection("mysql_stat")->insert
				("
        insert into t_dash_stat set app_id = '$appid', date='$datetime', hour='$tmp_hour', 
          paid_active_count='$paidactive_count->value',sum_count='$sum_count->value', sum_pay='$sum_pay->value', sum_income='$sum_income->value', created_at='$nowTime'    
        on duplicate key 
        update  paid_active_count='$paidactive_count->value', sum_count='$sum_count->value', sum_pay='$sum_pay->value', sum_income='$sum_income->value', update_at='$nowTime'
        ");

			} else {
				$result = 1;
			}
			if ($result) {
				$logdata = "$appid $datetime $tmp_hour paid_active_count=$paidactive_count->value sum_count=$sum_count->value sum_pay=$sum_pay->value sum_income=$sum_income->value ";
				$logpath = 'crontab_h_OneAppStatHour_' . $thistime . '.log';
			}
			unset($sum_count);
			unset($sum_pay);
			unset($sum_income);
			unset($paidactive_count);

			$ntime     = microtime(true);
			$totaltime = $ntime - $stime;
			dump('totaltime: ' . $totaltime);

			if ($hour_count > $area && $noto == 1) {
				$sum_hour = $sum_hour - 1;
				$sec      = 3;
				sleep($sec);
				dump(" sleeping:'$sec's..... ");

				return redirect('/BShell/runOneAppStatHourSum?appid=' . $appid . '&nosql=' . $nosql . '&area=' . $area . '&noto=1&sum_hour=' . $sum_hour);
			}
		}

	}

	/**
	 * @param appid 用户参数
	 * @params sum_day 统计当前时间小时
	 *
	 * @noto   1 自动跳转，0，只执行当前参数
	 * @area   默认0 sum_dayr+area
	 * @nosql  默认0 ，1 不写数据库
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function runOneAppStatDayCount ()
	{
		set_time_limit(600); //timeout second
		$stime   = microtime(true);
		$appid   = Input::get('appid', '0');
		$sum_day = Input::get('sum_day', '0');
		$noto    = Input::get('noto', '0');
		$area    = Input::get('area', '0');
		$nosql   = Input::get('nosql', '0');

		$thistime = date('Y-m-d', time());
		if ($appid) {
			//自定义单日统计
			$current_day = $sum_day + $area;
			$datetime    = date('Y-m-d', strtotime("-" . $current_day . " days", time()));
			$table_date  = date('Y_m_d', strtotime("-" . $current_day . " days", time()));
			$logs_table  = 't_api_logs_' . $table_date;
			dump($datetime);//dump($logs_table);

			//检测分表是否存在
			$table_created = DB::connection("mysql_log")->select("show tables like '$logs_table'");
			$logs_table    = $table_created ? $logs_table : 't_api_logs'; //改为查询旧表

			//appid对应的天活跃用户
			$active_count = DB::connection("mysql_log")->select("
        select count(distinct(user_id)) as value from $logs_table
          where app_id = '$appid' and  date(created_at) = '$datetime' 
        ")[0];
			//appid对应的天新增用户
			$add_count = DB::connection("mysql")->select("
        select count(*) as value from t_users
        where app_id = '$appid' and date(created_at) = '$datetime' 
        ")[0];//dump($add_count->value);
			//appid对应的天新增收入
			$income = DB::connection("mysql")->select("
        select sum(price) as value from t_orders
        where app_id = '$appid' and date(created_at) = '$datetime'  and order_state = '1' 
        ")[0];

			//统计当天付费用户数据
			$pay_count = DB::connection('mysql')->select("
select count(distinct(user_id)) as value from t_purchase where app_id='$appid' and  date(created_at) = '$datetime' ")[0];

			$nowTime = Utils::getTime();
			if ($nosql == 0) {
				$result = DB::connection("mysql_stat")->insert
				("
        insert into t_dash_stat_daycount set app_id = '$appid', date='$datetime', hour='0',
        add_count='$add_count->value', pay_count='$pay_count->value', active_count='$active_count->value', 
         income='$income->value', created_at='$nowTime' 
        on duplicate key
        update add_count='$add_count->value', pay_count='$pay_count->value', active_count='$active_count->value',
         income='$income->value',   update_at='$nowTime' 
        ");
			} else {
				$result = 1;
			}
			if ($result) {
				$logdata = "$appid $datetime add_count=$add_count->value pay_count=$pay_count->value active_count=$active_count->value income=$income->value ";
				$logpath = 'crontab_h_OneAppStatDay_' . $thistime . '.log';
				unset($add_count);
				unset($active_count);
				unset($pay_count);
				unset($income);
				unset($active_count);
			}

			$ntime     = microtime(true);
			$totaltime = $ntime - $stime;
			dump('totaltime: ' . $totaltime);

			if ($current_day > $area && $noto == 1) {
				$sum_day = $sum_day - 1;
				$sec     = 3;
				sleep($sec);
				dump(" sleeping:'$sec's..... ");

				return redirect('/BShell/runOneAppStatDayCount?appid=' . $appid . '&nosql=' . $nosql . '&area=' . $area . '&noto=1&sum_day=' . $sum_day);
			}
		}

	}

	//补空值

	public function runOneAppStatDaySum ()
	{
		set_time_limit(600); //timeout second
		$stime   = microtime(true);
		$appid   = Input::get('appid', '0');
		$sum_day = Input::get('sum_day', '0');
		$noto    = Input::get('noto', '0');
		$area    = Input::get('area', '0');
		$nosql   = Input::get('nosql', '0');

		$thistime = date('Y-m-d', time());
		if ($appid) {
			//自定义单日统计
			$current_day = $sum_day + $area;
			$datetime    = date('Y-m-d', strtotime("-" . $current_day . " days", time()));
			$table_date  = date('Y_m_d', strtotime("-" . $current_day . " days", time()));
			$logs_table  = 't_api_logs_' . $table_date;
			dump($datetime);//dump($logs_table);

			//检测分表是否存在
			$table_created = DB::connection("mysql_log")->select("show tables like '$logs_table'");
			$logs_table    = $table_created ? $logs_table : 't_api_logs'; //改为查询旧表

			//appid对应的总用户
			$sum_count = DB::connection("mysql")->select("
            select count(*) as value from t_users 
            where app_id = '$appid' and date(created_at) <= '$datetime' 
            ")[0];
			//appid对应总收入
			$sum_income = DB::connection("mysql")->select("
            select sum(price) as value from t_orders
            where app_id = '$appid' and date(created_at) <= '$datetime' and order_state = '1' 
            ")[0];//

			//appid对应的总付费用户
			$sum_pay = DB::connection("mysql")->select("
            select count(DISTINCT user_id) as value from t_purchase 
            where app_id ='$appid' and created_at <= '$datetime' 
            ")[0];

			//统计当天活跃付费用户数据
			$paidactive_count = DB::connection('mysql')->select("
select count(distinct(user_id)) as value from t_purchase where user_id in (
                select distinct(user_id) as user_id from db_ex_logs.$logs_table
              where app_id ='$appid' and  date(created_at) = '$datetime'
) and app_id='$appid' and  date(created_at) <= '$datetime' ")[0];

			$nowTime = Utils::getTime();
			if ($nosql == 0) {
				$result = DB::connection("mysql_stat")->insert
				("
        insert into t_dash_stat_daycount set app_id = '$appid', date='$datetime', hour='0',
        paid_active_count='$paidactive_count->value', sum_count='$sum_count->value', sum_pay='$sum_pay->value', sum_income='$sum_income->value', created_at='$nowTime' 
        on duplicate key
        update paid_active_count='$paidactive_count->value', sum_count='$sum_count->value', sum_pay='$sum_pay->value', sum_income='$sum_income->value', update_at='$nowTime' 
        ");
			} else {
				$result = 1;
			}
			if ($result) {
				$logdata = "$appid $datetime paid_active_count=$paidactive_count->value sum_count=$sum_count->value sum_pay=$sum_pay->value sum_income=$sum_income->value ";
				$logpath = 'crontab_h_OneAppStatDay_' . $thistime . '.log';
				unset($sum_count);
				unset($sum_pay);
				unset($sum_income);
				unset($paidactive_count);
			}

			$ntime     = microtime(true);
			$totaltime = $ntime - $stime;
			dump('totaltime: ' . $totaltime);

			if ($current_day > $area && $noto == 1) {
				$sum_day = $sum_day - 1;
				$sec     = 3;
				sleep($sec);
				dump(" sleeping:'$sec's..... ");

				return redirect('/BShell/runOneAppStatDaySum?appid=' . $appid . '&nosql=' . $nosql . '&area=' . $area . '&noto=1&sum_day=' . $sum_day);
			}
		}

	}

	//去空值

	/**
	 * 插入中间表小时记统计数据 |获取全网相关数据；
	 * !important 此方法查询全网小时记统计数据，用于与仪表盘数据进行对比，查询所得数据暂时无存储处理
	 *
	 * @param  sum_day 时间参数； >0
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function insertTDashStatTest ()
	{
		set_time_limit(600); // set timeout seconds
		//$stime = microtime(true);
		$sum_hour = Input::get('sum_hour', 0);
		//$data[] = "";

		for ($hour_count = 0; $hour_count < $sum_hour; $hour_count++) {
			$datetime        = date('Y-m-d', strtotime("-" . $hour_count . " hours", time()));
			$tmp_hour        = date('H', strtotime("-" . $hour_count . " hours", time()));
			$logs_table_date = date('Y_m_d', strtotime("-" . $hour_count . "hours", time()));

			/*if ($hour_count == -1) {
				$datetime = date('Y-m-d', strtotime("+1 hours", time()));
				$tmp_hour = date('H', strtotime("+1 hours", time()));
				$logs_table_date = date('Y_m_d', strtotime("+1 hours", time()));
			}*/

			$current_hour = date('Y-m-d H:00:00', strtotime("+1 hours -" . $hour_count . " hours", time()));
			dump($current_hour);

			//appid对应的小时新增用户
			$add_count        = DB::connection("mysql")->select("select count(*) as value from t_users
            where date(created_at) = '$datetime' and hour(created_at) = '$tmp_hour'")[0];
			$add_count->value = $add_count->value ? $add_count : 0;
			//            dump($add_count);

			//appid对应的小时活跃用户
			$logs_table          = 't_api_logs_' . $logs_table_date;
			$active_count        = DB::connection("mysql_log")->select("select count(distinct(user_id)) as value from $logs_table
            where date(created_at) = '$datetime' and hour(created_at) = '$tmp_hour'")[0];
			$active_count->value = $active_count->value ? $active_count : 0;
			//            dump($active_count);

			//                //appid对应的小时总用户
			$sum_count        = DB::connection("mysql")->select("select count(*) as value from t_users where created_at < '$current_hour'")[0];
			$sum_count->value = $sum_count->value ? $sum_count->value : 0;
			//            dump($sum_count);

			//                //appid对应的小时收入
			$income        = DB::connection("mysql")->select("select sum(price) as value from t_orders 
            where date(created_at) = date($datetime) and hour(created_at) = '$tmp_hour' and order_state = '1'")[0];
			$income->value = $income->value ? $income->value : 0;
			//            dump($income);

			//                //appid对应小时总收入
			$sum_income        = DB::connection("mysql")->select("select sum(price) as value from t_orders 
where created_at < '$current_hour' and order_state = '1' ")[0];
			$sum_income->value = $sum_income->value ? $sum_income->value : 0;
			//            dump($sum_income);

		}

		/*$result = DB::connection("mysql_stat_")->insert("
				insert into t_dash_stat_？ set app_id = '$appid', date='$datetime', hour='$tmp_hour',
				add_count='$add_count->value', active_count='$active_count->value', sum_count='$sum_count->value', income='$income->value', sum_income='$sum_income->value' ,created_at=time()
				on duplicate key
				update add_count='$add_count->value', active_count='$active_count->value', sum_count='$sum_count->value', income='$income->value', sum_income='$sum_income->value' ,update_at=time()
				");
		if($result) {
			unset($add_count);unset($active_count);unset($sum_count);unset($income);unset($sum_income);
		}*/

		/*$ntime = microtime(true);
		$totaltime = $ntime-$stime;
		dump('totaltime: '.$totaltime);*/

	}

	//处理user_id

	public function deal_Notnull_data ($appIds, $queryData)
	{
		$tempData = "";
		foreach ($appIds as $appid) {
			$isMatch = false;
			foreach ($queryData as $item) {
				if ($item->app_id == $appid) {
					$isMatch            = true;
					$tempData[ $appid ] = $item->value;
				}
			}
			if (!$isMatch) {
				//$tempData[$appid] = 0;
			}
		}

		return $tempData;
	}

	//模拟客户端插入日志数据
	//8674535//	apppcHqlTPT3482	u_582411eca3329_ti3rjKbo	http://wxdd198a901fa24220.h5.inside.xiaoe-tech.com/homepage		0.1		1	2016-11-30 08:03:05

	public function deal_user_data ($appIds, $queryData)
	{
		$tempData = "";
		foreach ($appIds as $appid) {
			$isMatch = false;
			foreach ($queryData as $item) {
				if ($item->app_id == $appid) {
					$isMatch            = true;
					$userid_array       = explode(',', $item->value);
					$queryUserid        = implode("','", $userid_array);
					$tempData[ $appid ] = $queryUserid;
				}
			}
			if (!$isMatch) {
				$tempData[ $appid ] = 0;
			}
		}

		return $tempData;
	}

}








