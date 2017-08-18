<?php
/**
 * Created by PhpStorm.
 * User: breeze
 * Date: 20/04/2017
 * Time: 10:30
 */

namespace App\Http\Controllers\Tools;

/***
 * 工具类 非业务相关公共方法
 * Class CommonTools
 * @package App\Http\Controllers\Tools
 */
class CommonTools
{
	/***
	 * 本地服务器上传文件到远程服务器
	 *
	 * @param $remote_uri
	 * @param $src_path
	 *
	 * @return mixed
	 */
	public static function remote_send ($remote_uri, $src_path)
	{
		header('content-type:text/html;charset=utf8');

		$curl = curl_init();
		//        $data = array('file'=>'@'. $src_path);
		$data = ['file' => new \CURLFile($src_path)];
		curl_setopt($curl, CURLOPT_URL, $remote_uri);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		$result = curl_exec($curl);
		curl_close($curl);

		return $result;
	}

	/****
	 * 远程服务器接收文件
	 *
	 * @param $dst_path
	 */
	public static function remote_recv ($dst_path)
	{
		if ($_FILES) {
			$filename = $_FILES['filename']['name'];
			$tmpname  = $_FILES['filename']['tmp_name'];
			if (move_uploaded_file($tmpname, $dst_path . $filename)) {
				echo 'SUCCESS';
			} else {
				$data = json_encode($_FILES);
				echo $data;
			}
		} else {
			echo 'FAILED';
		}
	}
}