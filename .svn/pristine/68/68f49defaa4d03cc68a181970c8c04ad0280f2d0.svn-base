<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/26 0026
 * Time: 下午 2:47
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\Mobile_Detect;
use App\Http\Controllers\View;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
	private $request;

	public function __construct (Request $request)
	{
		$this->request = $request;
	}

	//官网
	public function homePage ()
	{
		//        require_once 'Mobile_Detect.php';gyft
		$detect = new Mobile_Detect();
		if ($detect->isMobile()) {
			dd("is Mobile");
		} else {
			return view('admin.homePage');
		}
	}
}