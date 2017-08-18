<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\Utils;
use Closure;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExcelMiddleware
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure                 $next
	 *
	 * @return mixed
	 */
	public function handle ($request, Closure $next)
	{
		return $next($request);
	}

	public function terminate ($request, $response)
	{
		$app_id = AppUtils::getAppID();

		if ($response->original == 1) {
			ini_set('memory_limit', '1024M');
			ini_set('max_execution_time', 600);

			$excel_id = $request->session()->get('excel_id', '');

			if ($excel_id) {
				$excel_info = DB::connection('mysql')->table('t_excel_export')
					->where('app_id', $app_id)->where('excel_id', $excel_id)
					->first();

				$start_time = $excel_info->start_time;
				$end_time   = $excel_info->end_time;

				Utils::log('off_line_' . 'memory1=' . memory_get_usage() / 1024 / 1024);
				Utils::log("off_line_" . $start_time . " ~ " . $end_time);

				//                $excelData[] = ['订单号','头像','用户ID','昵称','手机号','真实姓名','订单类型','订单内容','订单总额(元)','订单时间'];
				$excelData[] = ['订单号', '头像', '用户ID', '昵称', '手机号', '真实姓名', '公司', '职位', '地址', '订单类型', '订单内容', '订单总额(元)', '订单时间'];

				$orderInfo = DB::select("
SELECT user_id, out_order_id, payment_type, resource_type, purchase_name, price, created_at FROM t_orders
WHERE app_id = '$app_id' and order_state = '1'
AND created_at >= '$start_time' and created_at < '$end_time'
order by created_at desc
");
				//            dump($orderInfo);
				Utils::log("off_line_" . 'memory2=' . memory_get_usage() / 1024 / 1024);

				$userInfo = DB::select("
SELECT user_id, wx_avatar, wx_nickname, phone, wx_name, company, job, address from t_users
where app_id = '$app_id' and user_id in (
SELECT DISTINCT user_id FROM t_orders
WHERE app_id = '$app_id' and order_state = '1'
AND created_at >= '$start_time' and created_at < '$end_time'
)");
				//            dump($userInfo);
				Utils::log("off_line_" . 'memory3=' . memory_get_usage() / 1024 / 1024);

				$userList = [];
				foreach ($userInfo as $item) {
					$userList[ $item->user_id ] = $item;
				}
				//        dump($orderInfo);
				//        dump($userList);
				//      exit;
				Utils::log("off_line_" . 'memory4=' . memory_get_usage() / 1024 / 1024);

				foreach ($orderInfo as $value) {
					$temp_text = "";
					//判断资源类型
					if ($value->payment_type == 2) {
						switch ($value->resource_type) {
							case 1:
								$temp_text = '图文';
								break;
							case 2:
								$temp_text = '音频';
								break;
							case 3:
								$temp_text = '视频';
								break;
							case 4:
								$temp_text = '直播';
								break;
						}
					} else if ($value->payment_type == 3) {
						$temp_text = '付费产品包';
					} else if ($value->payment_type == 4) {
						$temp_text = '团购';
					} else if ($value->payment_type == 6) {
						$temp_text = '产品包的购买赠送';
					} else if ($value->payment_type == 5) {
						switch ($value->resource_type) {
							case 1:
								$temp_text = '图文-购买赠送';
								break;
							case 2:
								$temp_text = '音频-购买赠送';
								break;
							case 3:
								$temp_text = '视频-购买赠送';
								break;
							case 4:
								$temp_text = '直播-购买赠送';
								break;
						}
					}
					//tp.user_id, tp.out_order_id, tp.payment_type, tp.resource_type, tp.purchase_name, tp.price, tp.created_at,
					//tu.user_id, tu.wx_avatar, tu.wx_nickname, tu.phone, tu.wx_name, tu.company, tu.job, tu.address
					if (array_key_exists($value->user_id, $userList)) {
						$rowData = [
							$value->out_order_id,
							$userList[ $value->user_id ]->wx_avatar,
							$value->user_id,
							$userList[ $value->user_id ]->wx_nickname,
							$userList[ $value->user_id ]->phone,
							$userList[ $value->user_id ]->wx_name,
							$userList[ $value->user_id ]->company,
							$userList[ $value->user_id ]->job,
							$userList[ $value->user_id ]->address,
							$temp_text,
							$value->purchase_name,
							$value->price / 100,
							$value->created_at,
						];

					} else {
						$userList[ $value->user_id ] = new \stdClass();
						$rowData                     = [
							$value->out_order_id,
							$userList[ $value->user_id ]->wx_avatar = '未知',
							$value->user_id,
							$userList[ $value->user_id ]->wx_nickname = '未知',
							$userList[ $value->user_id ]->phone = '未知',
							$userList[ $value->user_id ]->wx_name = '未知',
							$userList[ $value->user_id ]->company = '未知',
							$userList[ $value->user_id ]->job = '未知',
							$userList[ $value->user_id ]->address = '未知',
							$temp_text,
							$value->purchase_name,
							$value->price / 100,
							$value->created_at,
						];
					}

					$excelData[] = $rowData;
				}
				unset($rowData);
				unset($userList);
				unset($userInfo);
				unset($orderInfo);
				Utils::log("off_line_" . 'memory5=' . memory_get_usage() / 1024 / 1024);

				//                dump($excelData);
				Excel::create($excel_info->excel_id, function($excel) use ($excelData) {
					$excel->sheet("订单数据", function($sheet) use ($excelData) {
						//标题
						$rows   = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];
						$widths = [20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20];
						for ($i = 0; $i < count($rows); $i++) {
							//宽度
							$sheet->setWidth([$rows[ $i ] => $widths[ $i ]]);
						}
						$sheet->fromArray($excelData);
						Utils::log("off_line_" . 'memory7=' . memory_get_usage() / 1024 / 1024);

					});
				})->store("csv");
				Utils::log("off_line_" . 'memory6=' . memory_get_usage() / 1024 / 1024);

				// 上传数据文件到腾讯云
				$path = storage_path("exports/" . $excel_info->excel_id . ".csv");

				$url = Utils::uploadExcel($path, $app_id);
				Utils::log("off_line_" . $url);
				if ($url) {
					//生成导出记录
					$data['excel_url'] = $url;
					$data['state']     = 1;
					$update            = DB::connection('mysql')->table('t_excel_export')
						->where('app_id', $app_id)->where('excel_id', $excel_id)
						->update($data);
					if ($update) {
						Utils::log("off_line_" . "离线数据更新成功：" . $excel_id);
					} else {
						Utils::log("off_line_" . "离线数据更新失败：" . $excel_id);
					}
				}
			} else {
				Utils::log('获取session 中excel_id 失败');
			}

		}

	}

}
