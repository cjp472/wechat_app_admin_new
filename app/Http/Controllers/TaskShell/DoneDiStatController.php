<?php
/**
 * Created by PhpStorm.
 * User: breeze
 * Date: 30/03/2017
 * Time: 16:49
 */

namespace App\Http\Controllers\TaskShell;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tools\Utils;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

/**
 * 分销中间表数据统计离线脚本
 * Class DoneDistributeController
 * @package App\Http\Controllers
 */
class DoneDiStatController extends Controller
{
	/**
	 * 分销业绩统计中间表数据脚本
	 */
	public function doDistributeStat ()
	{

		$day        = Input::get('day', 1);
		$start_date = date('Y-m-d 23:50:00', strtotime("-$day day"));


		//        $statResult = DB::select("
		//SELECT t1.app_id, t1.share_user_id, t1.date, t1.order_count, t1.order_price,
		//  t2.sub_order_count, t2.sub_order_price, (t1.commision + t2.sub_commision) as sum_commision from (
		//  SELECT
		//    app_id,
		//    share_user_id,
		//    date(created_at)      AS date,
		//    count(*)              AS order_count,
		//    sum(price)            AS order_price,
		//    sum(distribute_price) AS commision
		//  FROM t_orders
		//  WHERE order_state = 1 AND use_collection = 1 AND share_user_id IS NOT NULL
		//        AND share_type = 5 AND created_at >= '$start_date'
		//  GROUP BY app_id, share_user_id, date
		//) t1 left join (
		//  SELECT
		//    app_id,
		//    superior_distribute_user_id,
		//    date(created_at)               AS date,
		//    count(*)                       AS sub_order_count,
		//    sum(price)                     AS sub_order_price,
		//    sum(superior_distribute_price) AS sub_commision
		//  FROM t_orders
		//  WHERE order_state = 1 AND use_collection = 1 AND share_user_id IS NOT NULL AND superior_distribute_user_id IS NOT NULL
		//        AND share_type = 5 AND created_at >= '$start_date'
		//  GROUP BY app_id, superior_distribute_user_id, date
		//  ) t2 on t1.app_id = t2.app_id and t1.date = t2.date and t1.share_user_id = t2.superior_distribute_user_id
		//");
		$statResult = DB::select("
SELECT app_id, userid, date, sum(order_count) as order_count, sum(order_price) as order_price,
  sum(sub_order_count) as sub_order_count, sum(sub_order_price) as sub_order_price,
  (sum(sub_comision) + sum(comision)) as sum_comision from (
  SELECT
    app_id,
    share_user_id         AS userid,
    date(created_at)      AS date,
    count(*)              AS order_count,
    sum(price)            AS order_price,
    sum(distribute_price) AS comision,
    0                     AS sub_order_count,
    0                     AS sub_order_price,
    0                     AS sub_comision
  FROM t_orders
  WHERE order_state = 1 AND use_collection = 1 AND share_user_id IS NOT NULL AND distribute_price > 0
        AND share_type = 5 AND created_at >= '$start_date'
  GROUP BY app_id, share_user_id, date
  UNION ALL
  SELECT
    app_id,
    #     share_user_id,
    superior_distribute_user_id    AS userid,
    date(created_at)               AS date,
    0                              AS order_count,
    0                              AS order_price,
    0                              AS commision,
    count(*)                       AS sub_order_count,
    sum(price)                     AS sub_order_price,
    sum(superior_distribute_price) AS sub_comision
  FROM t_orders
  WHERE order_state = 1 AND use_collection = 1 AND share_user_id IS NOT NULL AND superior_distribute_user_id IS NOT NULL
        AND share_type = 5 AND created_at >= '$start_date' AND distribute_price > 0 AND
        superior_distribute_price > 0
  GROUP BY app_id, superior_distribute_user_id, date
) tt1 GROUP BY app_id, userid, date
");

		if ($statResult && count($statResult) > 0) {
			foreach ($statResult as $item) {
				$app_id          = $item->app_id;
				$user_id         = $item->userid;
				$date            = $item->date;
				$order_count     = $item->order_count;
				$order_price     = $item->order_price;
				$sub_order_count = $item->sub_order_count;
				$sub_order_price = $item->sub_order_price;
				$commision       = $item->sum_comision;
				$created_at      = Utils::getTime();

				$result = DB::insert("
insert into db_ex_stat.t_distribute_stat set
app_id = '$app_id', user_id = '$user_id', date = '$date', order_count = '$order_count', order_price = '$order_price',
sub_order_count = '$sub_order_count', sub_order_price = '$sub_order_price', commision = '$commision', created_at = '$created_at'
ON duplicate key update order_count = '$order_count', order_price = '$order_price',
sub_order_count = '$sub_order_count', sub_order_price = '$sub_order_price', commision = '$commision'
");
			}
		}

	}
}