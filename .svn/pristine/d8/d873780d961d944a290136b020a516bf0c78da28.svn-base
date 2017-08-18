<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\StringConstants;
use App\Http\Controllers\Tools\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ExperienceController extends Controller
{
	private $request;
	private $app_id;

	public function __construct (Request $request)
	{
		$this->request = $request;
		$this->app_id  = AppUtils::getAppID();
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * 获取试听会员列表
	 */
	public function experience ()
	{

		$allInfo = \DB::table("t_experience_config")->select()->where("app_id", "=", $this->app_id)
			->orderBy("created_at", "desc")->paginate(10);

		$app_id     = "appe0MEs6qX8480";
		$product_id = "p_5857d53b3342a_Tm6TjjTD";

		$data = [];
		if ($allInfo) {

			foreach ($allInfo as $key => $value) {
				$data[ $key ]['purchase_name'] = $value->purchase_name;
				$data[ $key ]['period']        = ($value->period) / 24 / 3600;
				$experience_name               = $value->experience_name;
				$data[ $key ]['url']           = $this->montageUrl($experience_name);

				//领取量
				$record1   = DB::select("SELECT * FROM t_experience_records WHERE app_id = ? AND experience_name = ?", [$app_id, $experience_name]);
				$recordNum = count($record1);

				//当前是非体验会员的人
				//                $members = DB::select("SELECT * FROM t_purchase WHERE app_id = ? AND product_id = ? AND payment_type = 3 AND generate_type != 2", [$app_id, $product_id]);
				//                $members_user_id = [];
				//                foreach ($members as $item) {
				//                    $members_user_id[] = $item->user_id;
				//                }
				$data[ $key ]['record_num'] = $recordNum;
				if ($recordNum != 0) {
					//                    $sql = "SELECT * FROM t_purchase_modify WHERE product_id = ? AND app_id = ? AND payment_type = ? AND ";
					//                    $num = 0;
					//                    foreach ($record1 as $resultItem) {
					//                        $user_id = $resultItem->user_id;
					//                        if (in_array($user_id, $members_user_id)) {
					//                            $num++;
					//                            $order_id = $resultItem->id;
					//                            if ($num > 1) {
					//                                $sql = $sql . " OR (user_id='$user_id' AND order_id= '$order_id')";
					//                            } else {
					//                                $sql = $sql . "( (user_id='$user_id' AND order_id= '$order_id')";
					//                            }
					//                        }
					//                    }
					//                    if ($num > 0) {
					//                        $sql = $sql." )";
					//                        $record2 = DB::select($sql, [$product_id, $app_id, 3]);
					//                        $purchaseNum = count($record2);
					//                    } else {
					//                        $purchaseNum = 0;
					//                    }

					$purchaseNumRecord = DB::select("select count(*) as total from t_purchase where app_id=? and product_id=?
        and generate_type in (0, 1) and user_id in
        (
        select user_id from t_experience_records where experience_name=?
        )", [$app_id, $product_id, $experience_name]);
					$purchaseNum       = $purchaseNumRecord[0]->total;
				} else {
					$purchaseNum = 0;
				}
				$data[ $key ]['purchase_num'] = $purchaseNum;
			}
		}

		return View('admin.experience', compact('allInfo', 'data'));
	}

	public function montageUrl ($experience_name)
	{
		//        $app_id = "appe0MEs6qX8480";
		//        $config_record = DB::connection('mysql_config')->table('t_app_conf')->where('app_id', $app_id)->where('wx_app_type', 1)->first();
		//        $url = "";
		//        if ($config_record != null) {
		//            $use_collection = $config_record->use_collection;
		//            if ($use_collection == 0 && !is_null($config_record->wx_app_id)) {
		//                $wx_app_id = $config_record->wx_app_id;
		//                if ($config_record->isNewer == '1') {
		//                    $url = "http://" . $wx_app_id . ".h5.inside.xiaoe-tech.com/experience/" . $experience_name . "/";
		//                } else {
		//                    $url = "https://" . $wx_app_id . ".h5.inside.xiaoe-tech.com/experience/" . $experience_name . "/";
		//                }
		//            } else {
		//                if ($config_record->isNewer == '1') {
		//                    $url = "http://h5.xiaoeknow.com/" . $app_id;
		//                } else {
		//                    $url = "https://h5.xiaoeknow.com/" . $app_id;
		//                }
		//            }
		//        }
		$url = "http://wxb8dd23217f4a3780.h5.xiaoe-tech.com/experience/" . $experience_name . "/";

		return $url;
	}

	public function doaddExperience ()
	{
		$app_id          = "appe0MEs6qX8480";
		$product_id      = "p_5857d53b3342a_Tm6TjjTD";
		$experience_name = Utils::generateRandomCode(8, 'ALL');
		$data            = Input::get("params");

		$result_data['period']          = $data['period'] * 24 * 3600;
		$result_data['purchase_name']   = $data['purchase_name'];
		$result_data['app_id']          = $app_id;
		$result_data['experience_name'] = $experience_name;
		$result_data['product_id']      = $product_id;
		$result_data['payment_type']    = 3;
		$result_data['resource_type']   = 0;
		$record                         = DB::select("SELECT * FROM t_pay_products WHERE app_id = ? AND id = ? limit 1", [$app_id, $product_id]);

		if (count($record) == 0) {
			return Utils::jsonResponse(null, StringConstants::Code_Wrong_Para, StringConstants::Msg_Failed);
		}
		$img_url = $record[0]->img_url;

		$created_at                = Utils::getTime();
		$updated_at                = Utils::getTime();
		$result_data['img_url']    = $img_url;
		$result_data['created_at'] = $created_at;
		$result_data['updated_at'] = $updated_at;

		$result = DB::table('t_experience_config')->insert($result_data);

		$url = $this->montageUrl($experience_name);

		if ($result) {
			return response()->json(['ret' => 0, 'url' => $url]);
		} else {
			return response()->json(['ret' => 1]);
		}
	}

	//新增邀请码页面

	public function addExperience ()
	{
		return View("admin.experienceAdd");
	}

}





