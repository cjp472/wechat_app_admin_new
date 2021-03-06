<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tools\Utils;
use DB;
use Illuminate\Http\Request;

class AssistController extends Controller
{
	//生成短链接页面
	public function short ()
	{

		return view('admin.marketing.shortLink');
	}

	public function getShortUrl (Request $request)
	{
		$longUrl = $request->input('url', '');
		if (!$longUrl) return response()->json(['code' => -1, 'msg' => '链接不能为空', 'data' => []]);

		//        $url = "https://h5.inside.xiaoeknow.com/appCollection/payRecords";
		//        $url1 = "https://wxdd198a901fa24220.h5.inside.xiaoe-tech.com/payRecords";

		// 验证是否是小鹅通链接
		$data = explode('/', $longUrl);
		if (count($data) < 3) return response()->json(['code' => -1, 'msg' => '不是小鹅通链接哦', 'data' => []]);
		$data   = $data[2];
		$eleUrl = explode('.', $data);

		$isTrue = true;
		//        if (!in_array('h5',$eleUrl)) $isTrue = false;
		//        if (!in_array('h5',$eleUrl) && !in_array('h5b',$eleUrl)) $isTrue = false;
		if (!in_array('com', $eleUrl)) $isTrue = false;
		if (!in_array('xiaoeknow', $eleUrl) && !in_array('xiaoe-tech', $eleUrl)) $isTrue = false;
		if (!$isTrue) return response()->json(['code' => -1, 'msg' => '不是小鹅通链接哦', 'data' => []]);

		// 分表表名
		$md5       = md5($longUrl);
		$ascCode   = ord($md5);
		$tableName = 'db_h5_st.t_h5_st_' . ($ascCode % 10);

		$result = false;
		if ($tableName) {
			$stsInDB = DB::select("select * from " . $tableName . " where long_url = ? limit 1", [$longUrl]);
			if (count($stsInDB) > 0) {
				// 有链接
				$result   = true;
				$shortUrl = $stsInDB[0]->short_url;
			} else {
				// 没有链接 新建
				$firstCharacter = $ascCode % 10;
				$tryTime        = 0;
				while ($tryTime < 5) {
					$newCode             = $firstCharacter . Utils::generateRandomCode(8, 'ALL');
					$newSt               = [];
					$newSt['short_code'] = $newCode;
					$shortUrl            = env('ShortHost') . '/st/' . $newCode;
					$newSt['short_url']  = $shortUrl;
					$newSt['long_url']   = $longUrl;
					$currentTime         = Utils::getTime();
					$newSt['created_at'] = $currentTime;
					$newSt['updated_at'] = $currentTime;
					try {
						$result = DB::table($tableName)->insert($newSt);
						if ($result) {
							break;
						}
					} catch (\Exception $e) {

					}
					$tryTime++;
				}
			}
		}

		if (!$result) return response()->json(['code' => -2, 'msg' => 'db error', 'data' => []]);

		return response()->json(['code' => 0, 'msg' => 'success', 'data' => [
			'url' => $shortUrl,
		]]);
	}
}
