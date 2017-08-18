<?php

namespace App\Http\Controllers;
require '../vendor/tencentyun/cos-php-sdk-v4-master/include.php';

use App\Http\Controllers\Tools\CommonTools;
use App\Http\Controllers\Tools\GlobalString;
use App\Http\Controllers\Tools\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use qcloudcos\Auth;

class UploadController extends Controller
{
	/**
	 * 获取签名
	 */
	public function getUploadSign ()
	{
		if (isset($_GET['sign_type']) && $_GET['sign_type'] == "appSign") {
			//            if(empty($_GET['expired']) || empty($_GET['bucketName'])){
			//                header("HTTP/1.1 400 Bad Request");
			//                echo '{"code":10001,"message":"缺少expired或bucketName"}';
			//                return;
			//            }
			//            $expired = $_GET['expired'];
			//            $bucketName = $_GET['bucketName'];
			$expired    = time() + 600;
			$bucketName = GlobalString::V4_COS_BUCKET_NAME;
			$sign       = Auth::createReusableSignature($expired, $bucketName);
			$json       = ['code' => '0', 'message' => '成功', 'data' => ['sign' => $sign]];
			echo json_encode($json);
		} else if (isset($_GET['sign_type']) && $_GET['sign_type'] == "appSign_once") {
			//            if(empty($_GET['path']) || empty($_GET['bucketName'])){
			//                header("HTTP/1.1 400 Bad Request");
			//                echo '{"code":10001,"message":"缺少path或bucketName"}';
			//                return;
			//            }
			$path = $_GET['path'];
			//            $bucketName = $_GET['bucketName'];
			$bucketName = GlobalString::V4_COS_BUCKET_NAME;
			$sign       = Auth::createNonreusableSignature($bucketName, $path);
			$json       = ['code' => '0', 'message' => '成功', 'data' => ['sign' => $sign]];
			echo json_encode($json);
		} else {
			header("HTTP/1.1 400 Bad Request");
			echo '{"code":10001,"message":"未指定签名方式"}';
		}

	}

	/**
	 * 上传验证文件到本地服务器
	 *
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function uploadVerifyFile (Request $request)
	{
		$targetPath = env('MP_VERIFY_PATH');
		$app_id     = Input::get("app_id");
		if (!empty($_FILES)) {
			$tempFile   = $_FILES['Filedata']['tmp_name'];
			$targetFile = rtrim($targetPath, '/') . '/' . $_FILES['Filedata']['name'];
			// 文件类型检查
			$fileTypes = ['txt']; // File extensions
			$fileParts = pathinfo($_FILES['Filedata']['name']);
			if (!in_array($fileParts['extension'], $fileTypes)) {
				return response()->json(['ret' => -10, 'msg' => '请上传文本类型!']);
			}

			$resultMove = move_uploaded_file($tempFile, $targetFile);
			if ($resultMove) {
				$update = \DB::connection("mysql_config")->update("update t_app_conf set wx_bus_verify_txt=?
                where app_id=? and wx_app_type='1'", [$_FILES['Filedata']['name'], $app_id]);

				return response()->json(['ret' => 0, 'msg' => '上传成功!']);
			}
		}

		return response()->json(['ret' => -1, 'msg' => '上传失败!']);
	}

	/**
	 * 跨服务器上传验证文件到服务器
	 *
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function remote_uploadVerifyFile (Request $request)
	{
		$targetPath = env('MP_VERIFY_PATH');
		$app_id     = Input::get("app_id");
		if (!empty($_FILES)) {
			$tempFile   = $_FILES['Filedata']['tmp_name'];
			$targetFile = rtrim($targetPath, '/') . '/' . $_FILES['Filedata']['name'];
			// 文件类型检查
			$fileTypes = ['txt']; // File extensions
			$fileParts = pathinfo($_FILES['Filedata']['name']);
			if (!in_array($fileParts['extension'], $fileTypes)) {
				return response()->json(['ret' => -10, 'msg' => '请上传文本类型!']);
			}

			$resultMove = move_uploaded_file($tempFile, $targetFile);
			if ($resultMove) {
				$remoto_uri    = env('REMOTE_UPLOAD_URI');
				$remote_result = CommonTools::remote_send($remoto_uri, $targetFile);
				if ($remote_result == "SUCCESS") {
					$update = \DB::connection("mysql_config")->update("
update t_app_conf set wx_bus_verify_txt=? where app_id=? and wx_app_type='1'
", [$_FILES['Filedata']['name'], $app_id]);

					return response()->json(['ret' => 0, 'msg' => '上传成功!']);
				} else {
					Utils::log($app_id . " " . $_FILES['Filedata']['name'] . " upload error:" . $remote_result);
				}
			}
		}

		return response()->json(['ret' => -1, 'msg' => '上传失败!']);
	}
}
