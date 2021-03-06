<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\V4UploadUtils;
use Illuminate\Support\Facades\DB;

/**
 * 更换上传V3到V4相关逻辑
 * Class CosV3ToV4Controller
 * @package App\Http\Controllers
 */
class CosV3ToV4Controller extends Controller
{
	public function makeAppDirInCos ()
	{
		//1.搜索所有已注册业务
		$appList = DB::connection('mysql_config')->table('t_app_conf')
			->select('app_id')
			->where('wx_app_type', '=', '1')
			->pluck('app_id');

		//        $appList = ['apppcHqlTPT3482'];

		//2.根据业务id生成对应文件夹
		foreach ($appList as $app_id) {
			V4UploadUtils::createAppAllV4Folder($app_id);
		}
	}

}





