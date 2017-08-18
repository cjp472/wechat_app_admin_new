<?php

namespace App\Http\Controllers\help;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tools\Utils;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class HelpController extends Controller
{

	//    帮助中心导航栏
	public function helpPage ()
	{
		$help_doc_list = DB::connection('mysql_help')->table('t_help')
			->select('id', 'name', 'pid')
			->where('pid', '0')
			->where('state', '1')
			->orderBy('weight', 'desc')
			->get();
		foreach ($help_doc_list as $key => $value) {
			$help_doc_list[ $key ]->sublist = DB::connection('mysql_help')->table('t_help')
				->where('pid', $value->id)
				->where('state', '1')
				->select('name', 'id', 'pid', 'link_type', 'out_line', 'weight')
				->orderBy('weight', 'desc')
				->get();
		}

		//        dump($help_doc_list);

		return view('help.helpPage', compact('help_doc_list'));

	}

	//    帮助中心导航栏
	public function helpPageMobile ()
	{
		$help_doc_list = DB::connection('mysql_help')->table('t_help')
			->select('id', 'name', 'pid')
			->where('pid', '0')
			->where('state', '1')
			->orderBy('weight', 'desc')
			->get();
		foreach ($help_doc_list as $key => $value) {
			$help_doc_list[ $key ]->sublist = DB::connection('mysql_help')->table('t_help')
				->where('pid', $value->id)
				->where('state', '1')
				->select('name', 'id', 'pid', 'link_type', 'out_line', 'weight')
				->orderBy('weight', 'desc')
				->get();
		}

		//        dump($help_doc_list);

		return view('help.helpPageMobile', compact('help_doc_list'));

	}

	//    帮助中心内容拉取
	public function helpContent ()
	{
		$id = Input::get('id', '');
		if (Utils::isEmptyString($id)) {
			return response()->json(['code' => -1, 'msg' => 'id不存在']);
		}
		$data = DB::connection('mysql_help')->table('t_help')
			->select('org_content', 'head_name')
			->where('id', $id)
			->first();
		if ($data) {
			return response()->json(['code' => 0, 'msg' => '数据库读取成功', 'data' => $data]);
		} else {
			return response()->json(['code' => -1, 'msg' => '数据库读取失败']);
		}
	}
}