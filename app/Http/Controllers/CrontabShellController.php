<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\Utils;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class CrontabShellController extends Controller
{
	public function MoveProResRelationTest ()
	{
		$relations = DB::connection('mysql')->select("
insert into t_pro_res_relation
select app_id,product_id, product_name, resource_type,resource_id, 0, now(), now() from
(
select app_id,product_id, product_name, '4' as resource_type, id as resource_id from t_alive where payment_type=3  and state!=2
) t1
UNION ALL
select app_id,product_id, product_name, resource_type,resource_id, 0, now(), now() from
(
select app_id,product_id, product_name, '2' as resource_type, id as resource_id from t_audio where payment_type=3  and audio_state!=2
) t1
UNION ALL
select app_id,product_id, product_name, resource_type,resource_id, 0, now(), now() from
(
select app_id,product_id, product_name, '3' as resource_type, id as resource_id from t_video where payment_type=3  and video_state!=2
) t1
UNION ALL
select app_id,product_id, product_name, resource_type,resource_id, 0, now(), now() from
(
select app_id,product_id, product_name, '1' as resource_type, id as resource_id from t_image_text where payment_type=3  and display_state!=2
) t1
");
	}

	//test 勿动

	/**
	 * 手动转移历史未编辑资源关系到新的资源关系表
	 *
	 * @param day   default 0 today\ * day ago
	 * @param noto  default 0 redirect nexturl \1 non redirect
	 * @param nosql defalut 0 update \ 1 just dump
	 */
	public function MoveProResRelation ()
	{
		$stime = microtime(true);
		set_time_limit(600); // set timeout seconds
		$nosql = Input::get('nosql', 0);
		$day   = Input::get('day', 0);
		$noto  = Input::get('noto', 0);

		$pcount    = [];
		$datetime  = date('Y-m-d', time());
		$logs_date = date('Y_m_d', time());
		$logpath   = 'crontab_h_MoveProResRelation_' . $logs_date . '.log';

		if ($day == 0) {
			$day = DB::connection('mysql_config')->select("select date(created_at) as firstday from t_app_conf
where app_id<>'' and created_at<>'0000-00-00 00:00:00' and created_at<>'1970-01-01 00:00:00' order by created_at asc limit 0,1 ");
			$day = $day[0]->firstday;
		}
		if ($day) {
			//todo:1.查找 当天及以前 所有的appid
			$appid_all = DB::connection("mysql_config")->select("
            select DISTINCT(app_id) as app_id from t_app_conf where date(created_at) = '$day'
            ");
			if ($noto) dump(count($appid_all));//date_sub(curdate(), interval $day day)

			if ($appid_all) {
				foreach ($appid_all as $item) {// app_id,product_id,resource_type,resource_id
					//查询单appid所有的资源关系数据
					$relations = DB::connection('mysql')->select("
select app_id,product_id,resource_type,resource_id from
(
select app_id,product_id,'4' as resource_type, id as resource_id from t_alive where app_id='$item->app_id' and payment_type=3  and state!=2
) t1
UNION ALL
select app_id,product_id,resource_type,resource_id from
(
select app_id,product_id,'2' as resource_type, id as resource_id from t_audio where app_id='$item->app_id' and payment_type=3  and audio_state!=2
) t1
UNION ALL
select app_id,product_id,resource_type,resource_id from
(
select app_id,product_id,'3' as resource_type, id as resource_id from t_video where app_id='$item->app_id' and payment_type=3  and video_state!=2
) t1
UNION ALL
select app_id,product_id,resource_type,resource_id from
(
select app_id,product_id,'1' as resource_type, id as resource_id from t_image_text where app_id='$item->app_id' and payment_type=3  and display_state!=2
) t1
");
					if ($noto) dump($relations);
					if ($relations) {
						$relation_time = Utils::getTime();
						foreach ($relations as $relation) {
							if ($nosql == 0) {//update relation_state = '0' ,updated_at = '$relation_time'
								\DB::connection('mysql')->insert("insert into t_pro_res_relation SET
app_id = '$relation->app_id', product_id = '$relation->product_id', resource_type = $relation->resource_type, resource_id = '$relation->resource_id', created_at = '$relation_time'
on duplicate key
update updated_at = '$relation_time'
");
							} else {
								$pcount[ $item->app_id ][] = "app_id = '$relation->app_id', product_id = '$relation->product_id', resource_type = $relation->resource_type, resource_id = '$relation->resource_id' ";
							}

							self::nullUnset($relation);

						}

					}
					self::nullUnset($relations);
				}

			}

			self::nullUnset($appid_all);
			$totaltime = microtime(true) - $stime;
			dump('totaltime: ' . $totaltime);
			self::nullUnset($stime);
			self::nullUnset($totaltime);
			self::nullUnset($logpath);

			if ($nosql && $pcount) {
				dump($pcount);
				self::nullUnset($pcount);
			}

			if ($noto == 0 && $day <> $datetime) {
				$day = DB::connection('mysql_config')->select("select date(created_at) as nextday from t_app_conf
where app_id<>'' and date(created_at)>'$day' order by created_at asc limit 0,1 ");

				if ($day) {
					$day = $day[0]->nextday;
					//$re_url = env('HOST_URL');
					$re_url = "/BShell/MoveProResRelation?";
					if ($nosql) $re_url = $re_url . "nosql=$nosql&";
					if ($day) $re_url = $re_url . "day=$day";
					self::nullUnset($noto);
					self::nullUnset($day);
					self::nullUnset($nosql);

					return redirect($re_url);
					//self::nullUnset($re_url);
				}

			}

		}

	}

	public function nullUnset ($var)
	{
		$var = null;
		unset($var);
	}

	/**
	 * 定时?更新专栏期数
	 *
	 * @param day   default 0 today\ * day ago
	 * @param noto  default 0 redirect nexturl \1 non redirect
	 * @param nosql defalut 0 update \ 1 just dump
	 * @param asy   default 0 \ 1 no async
	 */
	public function updatePackageResourceCount ()
	{
		$stime = microtime(true);
		set_time_limit(600); // set timeout seconds
		$nosql     = Input::get('nosql', 0);
		$day       = Input::get('day', 0);
		$noto      = Input::get('noto', 0);
		$asy       = Input::get('asy', 0);
		$pcount    = [];
		$datetime  = date('Y-m-d', time());
		$logs_date = date('Y_m_d', time());
		$logpath   = 'crontab_h_PackageResourceCount_' . $logs_date . '.log';

		if ($noto == 0 && $day == 0) {
			$day = DB::connection('mysql_config')->select("select date(created_at) as firstday from t_app_conf
where app_id<>'' and created_at<>'0000-00-00 00:00:00' and created_at<>'1970-01-01 00:00:00' order by created_at asc limit 0,1 ");
			$day = $day[0]->firstday;
		}
		if ($day) {
			//todo:1.查找 当天及以前 所有的appid
			$appid_all = DB::connection("mysql_config")->select("
            select DISTINCT(app_id) as app_id from t_app_conf where date(created_at) = '$day'
            ");
			if ($asy) dump(count($appid_all));//date_sub(curdate(), interval $day day)

			if ($appid_all) {
				foreach ($appid_all as $item) {
					$package_list_on = \DB::table('t_pay_products')->select('app_id', 'id', 'name')->where('app_id', '=', $item->app_id)
						->where('state', '=', '0')->orderby('order_weight', 'desc')->orderby('created_at', 'desc')->get();
					//更新专栏期数
					if ($package_list_on) {
						//dump(count($package_list_on));dump($package_list_on);
						foreach ($package_list_on as $package) {
							$resource_count = \DB::select(" select sum(count) as count from (
select case when count is not null then count else 0 end as count from (
 select count(*) as count from t_alive where app_id='$item->app_id' and payment_type=3 and product_id='$package->id' and state=0
  ) t1
 UNION ALL
select case when count is not null then count else 0 end as count from (
 select count(*) as count from t_audio where app_id='$item->app_id' and payment_type=3 and product_id='$package->id' and audio_state=0
  ) t1
 UNION ALL
select case when count is not null then count else 0 end as count from (
 select count(*) as count from t_video where app_id='$item->app_id' and payment_type=3 and product_id='$package->id' and video_state=0
  ) t1
 UNION ALL
select case when count is not null then count else 0 end as count from (
 select count(*) as count from t_image_text where app_id='$item->app_id' and payment_type=3 and product_id='$package->id' and display_state=0
  ) t1
 )t2
 ")[0];
							if ($nosql == 0) {
								\DB::table('t_pay_products')->where('app_id', '=', $item->app_id)->where('id', '=', $package->id)->update(['resource_count' => $resource_count->count]);
							} else {
								$pcount[ $item->app_id ][] = "count = $resource_count->count ; name = $package->name";
							}

							self::nullUnset($resource_count);
							self::nullUnset($package);
						}
						self::nullUnset($package_list_on);
					}
					//off
					$package_list_off = \DB::table('t_pay_products')->select('id', 'name')->where('app_id', '=', $item->app_id)
						->where('state', '=', '1')->orderby('order_weight', 'desc')->orderby('created_at', 'desc')->get();
					//更新专栏期数
					if ($package_list_off) {
						//dump(count($package_list_off));dump($package_list_off);
						foreach ($package_list_off as $package) {
							$resource_count = \DB::select(" select sum(count) as count from (
select case when count is not null then count else 0 end as count from (
 select count(*) as count from t_alive where app_id='$item->app_id' and payment_type=3 and product_id='$package->id' and state=1
  ) t1
 UNION ALL
select case when count is not null then count else 0 end as count from (
 select count(*) as count from t_audio where app_id='$item->app_id' and payment_type=3 and product_id='$package->id' and audio_state=1
  ) t1
 UNION ALL
select case when count is not null then count else 0 end as count from (
 select count(*) as count from t_video where app_id='$item->app_id' and payment_type=3 and product_id='$package->id' and video_state=1
  ) t1
 UNION ALL
select case when count is not null then count else 0 end as count from (
 select count(*) as count from t_image_text where app_id='$item->app_id' and payment_type=3 and product_id='$package->id' and display_state=1
  ) t1
 )t2
 ")[0];
							if ($nosql == 0) {
								\DB::table('t_pay_products')->where('app_id', '=', $item->app_id)->where('id', '=', $package->id)->update(['resource_count' => $resource_count->count]);
							} else {
								$pcount[ $item->app_id ][] = "count = $resource_count->count ; name = $package->name";
							}

							self::nullUnset($resource_count);
							self::nullUnset($package);
						}
						self::nullUnset($package_list_off);
					}
					//sleep(1);dump(" sleeping: 1 s..... ");
					self::nullUnset($item);
				}
			}
			self::nullUnset($appid_all);

			$totaltime = microtime(true) - $stime;
			if ($asy) dump('totaltime: ' . $totaltime);
			self::nullUnset($stime);
			self::nullUnset($totaltime);
			self::nullUnset($logpath);

			if ($nosql && $pcount) {
				if ($asy) dump($pcount);
				self::nullUnset($pcount);
			}

			if ($noto == 0 && $day <> $datetime) {
				$day = DB::connection('mysql_config')->select("select date(created_at) as nextday from t_app_conf
where app_id<>'' and date(created_at)>'$day' order by created_at asc limit 0,1 ");

				if ($day) {
					$day = $day[0]->nextday;
					//$re_url = env('HOST_URL');
					$re_url = "/BShell/updatePackageResourceCount?";
					if ($nosql) $re_url = $re_url . "nosql=$nosql&";
					if ($asy) $re_url = $re_url . "asy=$asy&";
					if ($day) $re_url = $re_url . "day=$day";

					if ($asy == 0) {
						Utils::asyncThread($re_url);
					} else {
						return redirect($re_url);
					}
					self::nullUnset($re_url);
				}

			}
			self::nullUnset($noto);
			self::nullUnset($day);
			self::nullUnset($nosql);
			self::nullUnset($asy);
		}

	}

	/**
	 * 手动更新单个appid 下的专栏期数值
	 *
	 * @param appid
	 *
	 * @nosql default 0 update \ 1 just dump
	 */
	public function updateOneAppPackageCount ()
	{
		$stime = microtime(true);
		set_time_limit(600); // set timeout seconds
		$nosql     = Input::get('nosql', 0);
		$app_id    = Input::get('appid', 0);
		$pcount    = [];
		$datetime  = date('Y-m-d', time());
		$logs_date = date('Y_m_d', time());
		$logpath   = 'crontab_h_PackageResourceCount_' . $logs_date . '.log';

		$package_list_on = \DB::table('t_pay_products')->select('app_id', 'id', 'name')->where('app_id', '=', $app_id)
			->where('state', '=', '0')->orderby('order_weight', 'desc')->orderby('created_at', 'desc')->get();
		//更新专栏期数
		if ($package_list_on) {
			//dump(count($package_list_on));dump($package_list_on);
			foreach ($package_list_on as $package) {
				$resource_count = \DB::select(" select sum(count) as count from (
select case when count is not null then count else 0 end as count from (
select count(*) as count from t_alive where app_id='$app_id' and payment_type=3 and product_id='$package->id' and state=0
) t1
UNION ALL
select case when count is not null then count else 0 end as count from (
select count(*) as count from t_audio where app_id='$app_id' and payment_type=3 and product_id='$package->id' and audio_state=0
) t1
UNION ALL
select case when count is not null then count else 0 end as count from (
select count(*) as count from t_video where app_id='$app_id' and payment_type=3 and product_id='$package->id' and video_state=0
) t1
UNION ALL
select case when count is not null then count else 0 end as count from (
select count(*) as count from t_image_text where app_id='$app_id' and payment_type=3 and product_id='$package->id' and display_state=0
) t1
)t2
")[0];
				if ($nosql == 0) {
					\DB::table('t_pay_products')->where('app_id', '=', $app_id)->where('id', '=', $package->id)->update(['resource_count' => $resource_count->count]);
				} else {
					$pcount[ $app_id ][] = "count = $resource_count->count ; name = $package->name";
				}

				self::nullUnset($resource_count);
				self::nullUnset($package);
			}
			self::nullUnset($package_list_on);
		}
		//off
		$package_list_off = \DB::table('t_pay_products')->select('id', 'name')->where('app_id', '=', $app_id)
			->where('state', '=', '1')->orderby('order_weight', 'desc')->orderby('created_at', 'desc')->get();
		//更新专栏期数
		if ($package_list_off) {
			//dump(count($package_list_off));dump($package_list_off);
			foreach ($package_list_off as $package) {
				$resource_count = \DB::select(" select sum(count) as count from (
select case when count is not null then count else 0 end as count from (
select count(*) as count from t_alive where app_id='$app_id' and payment_type=3 and product_id='$package->id' and state=1
) t1
UNION ALL
select case when count is not null then count else 0 end as count from (
select count(*) as count from t_audio where app_id='$app_id' and payment_type=3 and product_id='$package->id' and audio_state=1
) t1
UNION ALL
select case when count is not null then count else 0 end as count from (
select count(*) as count from t_video where app_id='$app_id' and payment_type=3 and product_id='$package->id' and video_state=1
) t1
UNION ALL
select case when count is not null then count else 0 end as count from (
select count(*) as count from t_image_text where app_id='$app_id' and payment_type=3 and product_id='$package->id' and display_state=1
) t1
)t2
")[0];
				if ($nosql == 0) {
					\DB::table('t_pay_products')->where('app_id', '=', $app_id)->where('id', '=', $package->id)->update(['resource_count' => $resource_count->count]);
				} else {
					$pcount[ $app_id ][] = "count = $resource_count->count ; name = $package->name";
				}

				self::nullUnset($resource_count);
				self::nullUnset($package);
			}
			self::nullUnset($package_list_off);
		}
		//sleep(1);dump(" sleeping: 1 s..... ");

		$totaltime = microtime(true) - $stime;
		dump('totaltime: ' . $totaltime);
		self::nullUnset($stime);
		self::nullUnset($totaltime);
		self::nullUnset($logpath);

		if ($nosql && $pcount) {
			dump($pcount);
			self::nullUnset($pcount);
		}

		self::nullUnset($nosql);
		self::nullUnset($app_id);

	}

}








