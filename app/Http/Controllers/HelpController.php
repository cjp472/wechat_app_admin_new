<?php
/**
 * Created by PhpStorm.
 * User: xiaoe
 * Date: 2016/11/17
 * Time: 16:49
 */

namespace App\Http\Controllers;

class HelpController extends Controller
{
	private $request;

	public function __construct ()
	{

	}

	//文档首页
	public function index ()
	{
		return view('help.helpIndex');
	}

	//接入文档
	public function join ()
	{
		return view('help.helpDocument');
	}

	//使用说明
	public function instructions ()
	{
		return view('help.Instructions');
	}

	//帮助答疑
	public function qs ()
	{
		return view('help.helpQuestions');
	}

	//说明文档
	public function explainDoc ()
	{
		return view('help.ExplainDoc');
	}

	//系统升级说明
	public function systemUpdate ()
	{
		return view('help.systemUpdateDoc');
	}
}












