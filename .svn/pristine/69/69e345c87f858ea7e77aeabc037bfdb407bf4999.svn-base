<?php
/**
 * Created by PhpStorm.
 * User: xiaoe
 * Date: 2017/1/10
 * Time: 15:20
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\AppUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class VisitCountController extends Controller
{
	private $request;

	public function __construct (Request $request)
	{
		$this->request = $request;
		$this->app_id  = AppUtils::getAppID();

	}

	/**
	 *PV UV 查询
	 * searchurl url
	 * searchattr pv uv
	 * searchtime 2017-01-10
	 */
	public function visitSearch ()
	{
		//
		$search_url  = Input::get('search_url', '');
		$search_attr = Input::get('search_attr', 'PV');
		$search_time = Input::get('search_time', '');
		$top         = Input::get('top', 20);
		//$top = 20;
		$topsearch = $search_url ? '' : $top; //echo $topsearch;return;

		$datetime     = date('Y-m-d', time());
		$start_time   = $end_time = '';
		$time_warning = '';
		if ($search_time) {
			if ($search_time > $datetime) {
				//$time_warning = '穿越失败——请选择正确日期哦亲';
				$table_date  = date('Y_m_d', strtotime("-1 days", time()));
				$start_time  = date('Y-m-d', strtotime("-1 days", time())) . ' 16:00:00';
				$table_start = 't_visit_' . $table_date;
				$table_date  = date('Y_m_d', time());
				$end_time    = date('Y-m-d', time()) . ' 16:00:00';
				$table_end   = 't_visit_' . $table_date;
				$search_time = $datetime;
			} else {   //echo strtotime("$search_time -1 days");
				$table_date  = date('Y_m_d', strtotime("$search_time -1 days"));
				$start_time  = date('Y-m-d', strtotime("$search_time -1 days")) . ' 16:00:00';
				$table_start = 't_visit_' . $table_date;
				$table_date  = date('Y_m_d', strtotime("$search_time"));
				$end_time    = date('Y-m-d', strtotime("$search_time")) . ' 16:00:00';;
				$table_end = 't_visit_' . $table_date;
			}

		} else {
			$table_date  = date('Y_m_d', strtotime("-1 days", time()));
			$start_time  = date('Y-m-d', strtotime("-1 days", time())) . ' 16:00:00';
			$table_start = 't_visit_' . $table_date;
			$table_date  = date('Y_m_d', time());
			$end_time    = date('Y-m-d', time()) . ' 16:00:00';;
			$table_end = 't_visit_' . $table_date;
		}
		$table_start_created = \DB::connection('h5_log')->select("show tables like '$table_start'");
		//if($table_start_created) {echo $table_start;} else{ echo ' none ';}
		$table_end_created = \DB::connection('h5_log')->select("show tables like '$table_end'");
		//if($table_end_created) {echo $table_end;} else{ echo ' none ';}
		//dump( $table_start, '  --  ', $table_end );

		$search = '';
		if ($table_start_created && $table_end_created) {
			//pv_count search
			if ($search_attr == 'PV') {
				if ($search_url) {
					$search = \DB::select("select '$search_url' as target_url, sum(pv) as sumpv, sum(uv) as sumuv from
(
select count(id) as pv, count(distinct(user_id)) as uv from db_h5_log.$table_start where app_id='$this->app_id' and hour(created_at) > '16'
 and (target_url = '$search_url' or target_url like '$search_url%') 

UNION ALL 

select count(id) as pv, count(distinct(user_id)) as uv from db_h5_log.$table_end where app_id='$this->app_id' and hour(created_at) < '16'
 and (target_url = '$search_url' or target_url like '$search_url%') 
) t_
");
				} else {
					$search = \DB::select("select target_url, sum(pv) as sumpv, sum(uv) as sumuv from
(
select target_url, count(id) as pv, count(distinct(user_id)) as uv from db_h5_log.$table_start where app_id='$this->app_id' and hour(created_at) > '16'
 group by target_url

UNION ALL 

select target_url, count(id) as pv, count(distinct(user_id)) as uv from db_h5_log.$table_end where app_id='$this->app_id' and hour(created_at) < '16'
 group by target_url
) t_  group by target_url order by sumpv desc limit $topsearch
");

				}
			}

		} else {
			//$time_warning = '亲查询日期超出范围咯';
		}

		//dump($search);
		return view('admin.VisitSearch', compact('search', 'search_url', 'search_time', 'start_time', 'end_time', 'time_warning'));
	}

}