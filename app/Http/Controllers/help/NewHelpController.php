<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/1
 * Time: 9:40
 */

namespace App\Http\Controllers\help;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\Utils;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Searchy;
//require_once "D:/real Branch/vendor/autoload.php";
//use Fukuball\Jieba\Jieba;
//use Fukuball\Jieba\Finalseg;

class NewHelpController extends Controller
{
	public function __construct ()
	{
	}

	/**
	 * 帮助中心公共页
	 */
	public function helpCenter ()
	{

		return View('helpCenter.commonPage');
	}

	/**
	 * 帮助中心首页
	 */
	public function index ()
	{
		//获取常见问题
		$usual_document = DB::connection('mysql_help')->table('t_help_article as t1')->where('t1.is_show', 1)->where('t1.state', 0)
			->select('t1.id', 't1.name', 't1.img_url', 't1.summary', 't1.category_id', 't2.pid as first_id')
			->join('t_help_category as t2', 't2.id', '=', 't1.category_id')->where('t2.status', 0)->where('t2.level', 2)
			->orderBy('t1.weight', 'desc')
			->get();
		//        dump($usual_document);
		//        exit;
		//        foreach ($usual_document as $k=>$v)
		//        {
		//            $second_id = $v->category_id;//二级目录id
		//            $first_id = DB::connection('mysql_help')->table('t_help_category')->where('id',$second_id)->where('status',0)
		//                ->where('level',2)->value('pid');//一级目录id
		//
		//            $usual_document[$k]->first_id = $first_id;
		//        }

		//获取功能导航
		$nav_index = DB::connection('mysql_help')->table('t_help_category')->where('level', 1)->where('status', 0)->where('type', 0)->where('is_show', 1)
			->select('id', 'title', 'img_url')->orderBy('category_weight', 'desc')->get();

		//获取热门专题
		$hot_index = DB::connection('mysql_help')->table('t_help_category')->where('level', 2)->where('status', 0)->where('type', 0)->where('is_hot', 1)
			->select('id', 'title', 'img_url', 'pid')->orderBy('category_weight', 'desc')->get();

		return View('helpCenter.index', [
			'usual_document' => $usual_document,
			'nav_index'      => $nav_index,
			'hot_index'      => $hot_index,
		]);
	}

	/**
	 * 帮助中心新手专区
	 */
	public function freshMan ()
	{
		$freshMan = DB::connection('mysql_help')->table('t_help_category')->where('type', 1)->where('status', 0)
			->select('id', 'title')->orderBy('category_weight', 'desc')->get();
		//添加子文档
		foreach ($freshMan as $k => $v) {
			$id       = $v->id;
			$doc_info = DB::connection('mysql_help')->table('t_help_article as t1')->where('t1.zone_id', $id)->where('t1.state', 0)
				->select('t1.id', 't1.name', 't1.video_url', 't1.video_length', 't1.category_id', 't2.pid')->join('t_help_category as t2', 't2.id', '=', 't1.category_id')->where('status', 0)->where('t2.level', 2)->orderBy('weight', 'desc')->get();
			foreach ($doc_info as $key => $value) {
				$doc_info[ $key ]->video_length = AppUtils::dataformat($doc_info[ $key ]->video_length);
			}
			//dump($doc_info);
			$freshMan[ $k ]->document_list = $doc_info;

		}

		//        dump($freshMan);

		return View('helpCenter.freshMan', [
			'freshMan' => $freshMan,
		]);
	}

	/**
	 * 帮助中心问题汇总
	 */
	public function problem ()
	{
//        Jieba::init();
//        Finalseg::init();
//        $seg_list = Jieba::cut('我来到北京清华大学', false);
//        dump($seg_list);
		$first_id       = Input::get('first_id', '');
		$second_id      = Input::get('second_id', '');
		$document_id    = Input::get('document_id', '');
		$search_content = Input::get('search_content', '');
		$nav_info       = DB::connection('mysql_help')->table('t_help_category')->where('level', 1)->where('status', 0)
			->select('id', 'title')->orderBy('category_weight', 'desc')->get();
		$sec_info       =DB::connection('mysql_help')->table('t_help_category')->where('level',2)->where('status',0)
            ->select('id','title','pid')->orderBy('category_weight','desc')->get();

		if (Utils::isEmptyString($first_id) && Utils::isEmptyString($second_id) && !Utils::isEmptyString($document_id)) {
			$second_index = DB::connection('mysql_help')->table('t_help_article as t1')->where('t1.id', $document_id)
				->select('t1.category_id', 't2.title')->join('t_help_category as t2', 't2.id', '=', 't1.category_id')->orderBy('t2.category_weight', 'desc')->first();
			$second_id    = $second_index->category_id;
			$first_index  = DB::connection('mysql_help')->table('t_help_category as t1')->where('t1.id', $second_id)->select('t1.pid', 't2.title')->join('t_help_category as t2', 't2.id', '=', 't1.pid')->orderBy('t2.category_weight', 'desc')->first();
			$first_id     = $first_index->pid;
			//            dump($first_index,$second_index);
		}

		foreach ($nav_info as $k => $v) {
			//添加二级目录
			$firstId                     = $v->id;
            $nav_info[$k]->second_cate=[];
			foreach ($sec_info as $k1=>$v1){
			    if($sec_info[$k1]->pid==$firstId){
			        Array_push($nav_info[$k]->second_cate,$sec_info[$k1]);

			    }
            }
//			$nav_info[ $k ]->second_cate = DB::connection('mysql_help')->table('t_help_category')->where('level', 2)->where('status', 0)
//				->where('pid', $firstId)
//				->select('id', 'title')->orderBy('category_weight', 'desc')->get();
		}

		if (!empty($first_id) && empty($second_id)) {
			$second    = DB::connection('mysql_help')->table('t_help_category')->where('level', 2)->where('status', 0)
				->where('pid', $first_id)
				->select('id', 'title')->orderBy('category_weight', 'desc')->first();
			$second_id = $second->id;
		}

		if (empty($first_id) && empty($second_id) && empty($document_id)) {
			$first     = DB::connection('mysql_help')->table('t_help_category')->where('level', 1)->where('status', 0)
				->select('id', 'title')->orderBy('category_weight', 'desc')->first();
			$first_id  = $first->id;
			$second    = DB::connection('mysql_help')->table('t_help_category')->where('level', 2)->where('status', 0)
				->where('pid', $first_id)
				->select('id', 'title')->orderBy('category_weight', 'desc')->first();
			$second_id = $second->id;
		}

		return View('helpCenter.problem', [
			"nav_info"       => $nav_info,
			"first_id"       => $first_id,
			"second_id"      => $second_id,
			"document_id"    => $document_id,
			"search_content" => $search_content,
		]);
	}

	/**
	 * 帮助中心文档详情
	 */
	public function getDocumentDetail ()
	{
		$document_id = Input::get('document_id', '');
		$first_id    = Input::get('first_id', '');
		$second_id   = Input::get('second_id', '');
		//        dump($document_id,$first_id,$second_id);
		//        dump(Utils::isEmptyString('afhklhk'));
		if (Utils::isEmptyString($first_id) && Utils::isEmptyString($second_id) && !Utils::isEmptyString($document_id)) {
			$second_index = DB::connection('mysql_help')->table('t_help_article as t1')->where('t1.id', $document_id)
				->select('t1.category_id', 't2.title')->join('t_help_category as t2', 't2.id', '=', 't1.category_id')->orderBy('t2.category_weight', 'desc')->first();
			$second_id    = $second_index->category_id;
			$first_index  = DB::connection('mysql_help')->table('t_help_category as t1')->where('t1.id', $second_id)->select('t1.pid', 't2.title')->join('t_help_category as t2', 't2.id', '=', 't1.pid')->orderBy('t2.category_weight', 'desc')->first();
			$first_id     = $first_index->pid;
			//            dump($first_index,$second_index);
		}

		if (Utils::isEmptyString($first_id) && Utils::isEmptyString($second_id) && Utils::isEmptyString($document_id)) {
			return response()->json(['code' => -1, 'msg' => '不能全部为空']);
		}
		$first_index = DB::connection('mysql_help')->table('t_help_category')->where('id', $first_id)->where('status', 0)
			->select('id', 'title')->first();

		if (Utils::isEmptyString($second_id)) {
			$second_index = DB::connection('mysql_help')->table('t_help_category')->where('pid', $first_id)->where('status', 0)
				->select('id', 'title')->orderBy('category_weight', 'desc')->first();
			$second_id    = $second_index->id;
		} else {
			$second_index = DB::connection('mysql_help')->table('t_help_category')->where('id', $second_id)->where('status', 0)
				->select('id', 'title')->first();
		}

		if (!Utils::isEmptyString($document_id)) {
			$documen_detail = DB::connection('mysql_help')->table('t_help_article')->where('id', $document_id)->where('state', 0)
				->select('name', 'descrb', 'org_content', 'updated_at', 'video_url')->first();

			return response()->json(['code' => 0, 'msg' => 'success', 'data' => ['first_index' => $first_index, 'second_index' => $second_index, 'document_detail' => $documen_detail]]);
		} else {
			$document_list = DB::connection('mysql_help')->table('t_help_article')->where('category_id', $second_id)->where('state', 0)
				->select('id', 'name')->orderBy('weight', 'desc')->get();
			//            dump($document_list);
			//            exit();
			return response()->json(['code' => 0, 'msg' => 'success', 'data' => ['first_index' => $first_index, 'second_index' => $second_index, 'document_list' => $document_list]]);
		}

		//return response()->json(['code' => 0, 'msg' => '数据库读取失败',]);

	}

	/**
	 * 搜索
	 */
	public function searchByContent ()
	{
		$search_content = Input::get('search_content', '');

		//        $search_result = DB::connection('mysql_help')->table('t_help_article')->where('name','like','%'.$search_content.'%')->orWhere('org_content','like','%'.$search_content.'%')->where('state',0)
		//            ->select('id','name')->orderBy('weight','desc')->get();
		$search_result = Searchy::search('db_help_document.t_help_article as t1')->fields('name')
            ->select('t1.id', 't1.name', 't1.category_id as second_index', 't2.pid as first_index')
            ->query($search_content)->getQuery()->join('db_help_document.t_help_category as t2', 't2.id', '=', 't1.category_id')
            ->where('state', '<>', 2)
            ->having('relevance', '>', 0)
            ->where('state', '<>', 1)->limit(50)->get();
		if (Utils::isEmptyString($search_content)) {
			$search_result = DB::connection('mysql_help')->table('t_help_article as t1')->select('t1.id', 't1.name', 't1.category_id as second_index', 't2.pid as first_index')->join('t_help_category as t2', 't2.id', '=', 't1.category_id')->orderBy('weight', 'desc')->take(6)->get();
		}

		//        dump($search_result);
		//        exit();
		return response()->json(['code' => 0, 'msg' => 'success', 'data' => $search_result]);
	}
}