<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/9
 * Time: 9:48
 */

namespace App\Http\Controllers;

class MemberController extends Controller
{

	public function getMemberList ()
	{

		return View('admin.memberList', compact('data'));   //  会员页面
	}

}