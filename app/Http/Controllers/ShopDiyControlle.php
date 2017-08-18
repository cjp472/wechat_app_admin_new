<?php
/**
 * Created by PhpStorm.
 * User: Neo
 * Date: 2017/6/15
 * Time: 下午3:37
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\ImageUtils;
use App\Http\Controllers\Tools\StringConstants;
use App\Http\Controllers\Tools\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopDiyControlle extends Controller
{

	/**
	 * 搜索模块定义值
	 */
	const imageText = 1;
	const audio = 2;
	const video = 3;
	const alive = 4;
	const activity = 5;
	const column = 6;
	const community = 7;
	const member = 8;

	public function shopIndexDiy ()
	{
		return View('admin.shopDiy.diyIndex');
	}

	//获取首页自定义配置
	public function loadDiySetting ()
	{
		$appId = AppUtils::getAppID();

		/**
		 * 获取当前业务版本
		 * 1-基础版;2-成长版;3-专业版
		 */
		$version_type = (int)AppUtils::get_version_type();

		$diyStatus = false;

		//配置表开关
		$has_shop_diy      = false;
		$resource_category = false;
		$has_que           = false;

		$index_sort = [];
		$index_data = [];

		$diy_index_sort = [];
		$diyData        = [];

		//读取配置
		$config = DB::select("select * from db_ex_config.t_app_module where app_id = ?", [$appId]);
		if ($config && $config != null && count($config) > 0) {
			if ($config[0]->has_shop_diy == 1) {
				$has_shop_diy = true;
			}
			if ($config[0]->resource_category == 1) {
				$resource_category = true;
			}
			if ($config[0]->has_que == 1) {
				$has_que = true;
			}
		}

		//banner图diy数据
		$banner_diy_data = false;

		if ($has_shop_diy) {
			$type    = 1;
			$diyInfo = DB::select("select * from t_shop_index_diy where app_id = ? and type = ? limit 1", [$appId, $type]);
			if ($diyInfo && $diyInfo != null && count($diyInfo) > 0) {
				$diyInfo = $diyInfo[0];
				//解析自定义配置
				$diy_index_sort = json_decode($diyInfo->index_sort, true);
				$diy_index_data = json_decode($diyInfo->index_data, true);
				if (is_array($diy_index_sort) && is_array($diy_index_data) && count($diy_index_sort) === count($diy_index_data)) {
					$diyStatus = true;
					$diyData   = $diy_index_data;
				}

				//自定义的banner图数据
				foreach ($diy_index_sort as $diy_index_key => $diy_index_val) {
					if ($diy_index_val == StringConstants::IndexBannerPart) {
						$banner_diy_data = $diy_index_data[ $diy_index_key ];
					}
				}

			}
		}

		if ($diyStatus) {
			if ($version_type == 3) {
				//专业版完全使用自定义配置
				$index_sort = $diy_index_sort;
			} else {
				//非专业版只取banner图数据
				if ($banner_diy_data) {
					$index_sort[] = StringConstants::IndexBannerPart;
				}
				if ($resource_category) {
					$index_sort[] = StringConstants::IndexCategoryPart;
				}
				$index_sort[] = StringConstants::IndexCommunityPart;
				$index_sort[] = StringConstants::IndexMemberPart;
				if ($has_que) {
					$index_sort[] = StringConstants::IndexQuestionPart;
				}
				$index_sort[] = StringConstants::IndexAlivePart;
				$index_sort[] = StringConstants::IndexRecommendPart;

				$diyData = [];
				foreach ($index_sort as $item) {
					if ($item == StringConstants::IndexBannerPart) {
						$diyData[] = $banner_diy_data;
					} else {
						$diyData[] = false;
					}
				}
			}
		} else {
			//使用默认配置
			$index_sort[] = StringConstants::IndexBannerPart;
			if ($resource_category) {
				$index_sort[] = StringConstants::IndexCategoryPart;
			}
			$index_sort[] = StringConstants::IndexCommunityPart;
			$index_sort[] = StringConstants::IndexMemberPart;
			if ($has_que) {
				$index_sort[] = StringConstants::IndexQuestionPart;
			}
			$index_sort[] = StringConstants::IndexAlivePart;
			$index_sort[] = StringConstants::IndexRecommendPart;
		}

		if (count($diyData) != count($index_sort)) {
			$diyData = [];
			foreach ($index_sort as $item) {
				$diyData[] = false;
			}
		}

		foreach ($index_sort as $sortKey => $sortVal) {

			//专业版或者banner图，则可用自定义
			if ($version_type == 3 || $sortVal == StringConstants::IndexBannerPart) {
				$diyInputStatus = $diyStatus;
			} else {
				$diyInputStatus = false;
			}

			if ($sortVal == StringConstants::IndexBannerPart) {
				/**
				 * banner图模块
				 */
				$index_data[] = $this->getIndexBannerPart($diyInputStatus, $diyData[ $sortKey ]);
			} else if ($sortVal == StringConstants::IndexCategoryPart) {
				/**
				 * 分类导航模块
				 */
				$index_data[] = $this->getIndexCategoryPart($diyInputStatus, $diyData[ $sortKey ], $resource_category);
			} else if ($sortVal == StringConstants::IndexCommunityPart) {
				/**
				 * 社群模块
				 */
				$index_data[] = $this->getIndexCommunityPart($diyInputStatus, $diyData[ $sortKey ]);
			} else if ($sortVal == StringConstants::IndexMemberPart) {
				/**
				 * 会员及专栏模块
				 */
				$index_data[] = $this->getIndexProductPart($diyInputStatus, $diyData[ $sortKey ]);
			} else if ($sortVal == StringConstants::IndexQuestionPart) {
				/**
				 * 问答模块
				 */
				$index_data[] = $this->getIndexQuestionPart($diyInputStatus, $diyData[ $sortKey ], $has_que);
			} else if ($sortVal == StringConstants::IndexAlivePart) {
				/**
				 * 直播模块
				 */
				$index_data[] = $this->getIndexAlivePart($diyInputStatus, $diyData[ $sortKey ]);
			} else if ($sortVal == StringConstants::IndexRecommendPart) {
				/**
				 * 最新模块
				 */
				$index_data[] = $this->getIndexRecommendPart($diyInputStatus, $diyData[ $sortKey ]);
			}
		}

		//返回false则把该模块剔除掉，不返回给前端
		for ($i = 0; $i < count($index_data); $i++) {
			$data_key = $i;
			$data_val = $index_data[ $i ];
			if (!$data_val) {
				array_splice($index_sort, $data_key, 1);
				array_splice($index_data, $data_key, 1);
				$i--;
			}

		}

		$moduleConfig    = [
			'resource_category' => $resource_category,
			'has_que'           => $has_que,
		];
		$add_module_part = $this->addModulePart($index_sort, $moduleConfig);

		$result = [
			'add_module_part' => $add_module_part,
			'index_sort'      => $index_sort,
			'index_data'      => $index_data,
			'version_type'    => $version_type,
		];

		return Utils::jsonResponse($result);
	}

	//保存配置中产生的新内容

	public function getIndexBannerPart ($diyStatus, $diyData)
	{
		$appId   = AppUtils::getAppID();
		$nowTime = Utils::getTime();

		$list       = [];
		$bannerInfo = [];

		if ($diyStatus) {
			$diyIdArr = [];
			foreach ($diyData["list"] as $item) {
				if (array_key_exists("id", $item) && $item['id']) {
					$diyIdArr[] = $item['id'];
				}
			}
			$diyIdCondition = implode("','", $diyIdArr);
			$diyIdCondition = " and id in ('$diyIdCondition') ";

			$order_by_condition = implode(",", $diyIdArr);
			$order_by           = "order by find_in_set(id,'$order_by_condition') ";

			$bannerInfo = DB::select("select
            id,title,image_url,img_url_compressed,skip_target,skip_type,skip_title from t_banner 
              where state_offline=0 and app_id=? $diyIdCondition $order_by"
				, [$appId]);
		} else {
			$bannerInfo = DB::select("select 
              id,title,image_url,img_url_compressed,skip_target,skip_type,skip_title from t_banner 
              where state_offline=0 and app_id=? and ?>=start_at 
              and ((stop_at is not null and ?<=stop_at) or (stop_at is null)) order by weight desc,created_at desc"
				, [$appId, $nowTime, $nowTime]);
		}

		if (count($bannerInfo) > 0) {
			$list = $bannerInfo;
		}

		if (count($list) == 0) {
			return false;
		}

		$result = [
			'list' => $list,
		];

		return $result;
	}

	//保存首页自定义配置

	public function getIndexCategoryPart ($diyStatus, $diyData, $switch)
	{

		if ($switch) {
			//暂不能配置，返回空对象
			$result = new \stdClass();
		} else {
			$result = false;
		}

		return $result;
	}

	//检查保存配置的格式

	public function getIndexCommunityPart ($diyStatus, $diyData)
	{
		$appId = AppUtils::getAppID();

		$part_title = "小社群";
		$status     = 0;
		$list       = [];

		$communityInfo = [];

		if ($diyStatus) {
			$part_title = $diyData['part_title'];
			$status     = $diyData['status'];

			if ($status == 1) {
				$diyIdArr = [];
				foreach ($diyData["list"] as $item) {
					if (array_key_exists("id", $item) && $item['id']) {
						$diyIdArr[] = $item['id'];
					}
				}
				$diyIdCondition = implode("','", $diyIdArr);
				$diyIdCondition = " and id in ('$diyIdCondition') ";

				$order_by_condition = implode(",", $diyIdArr);
				$order_by           = "order by find_in_set(id,'$order_by_condition') ";

				$communityInfo = DB::select("select id,title,img_url_compressed from t_community
                where app_id = ? and community_state=0 $diyIdCondition $order_by", [$appId]);

				if (count($communityInfo) == 0) {
					return false;
				}
			}
		}

		$list = $communityInfo;

		$result = [
			'part_title' => $part_title,
			'status'     => $status,
			'list'       => $list,
		];

		return $result;
	}

	//banner模块

	public function getIndexProductPart ($diyStatus, $diyData)
	{

		$appId   = AppUtils::getAppID();
		$nowTime = Utils::getTime();

		$part_title = "频道";
		$status     = 0;
		$list       = [];

		$info = [];

		if ($diyStatus) {
			$part_title = $diyData['part_title'];
			$status     = $diyData['status'];

			if ($status == 1) {
				$diyIdArr = [];
				foreach ($diyData["list"] as $item) {
					if (array_key_exists("id", $item) && $item['id']) {
						$diyIdArr[] = $item['id'];
					}
				}
				$diyIdCondition = implode("','", $diyIdArr);
				$diyIdCondition = " and id in ('$diyIdCondition') ";

				$order_by_condition = implode(",", $diyIdArr);
				$order_by           = "order by find_in_set(id,'$order_by_condition') ";

				$info = DB::select("select id,name as title,img_url_compressed,is_member
                                from t_pay_products 
                                where state=0 and app_id=? $diyIdCondition $order_by",
					[$appId]);

				if (count($info) == 0) {
					return false;
				}
			}

		}

		$list = $info;

		$result = [
			'part_title' => $part_title,
			'status'     => $status,
			'list'       => $list,
		];

		return $result;
	}

	//分类导航模块

	public function getIndexQuestionPart ($diyStatus, $diyData, $switch)
	{

		$appId   = AppUtils::getAppID();
		$que_num = DB::select("select * from t_que_products where app_id = ? and state = 0", [$appId]);

		if ($que_num != null && $que_num && count($que_num) > 0) {

		} else {
			$switch = false;
		}

		if ($switch) {
			//暂不能配置，返回空对象
			$result = new \stdClass();
		} else {
			$result = false;
		}

		return $result;
	}

	//社群模块

	public function getIndexAlivePart ($diyStatus, $diyData)
	{
		$appId = AppUtils::getAppID();

		$part_title = "直播";
		$status     = 0;
		$list       = [];

		$info = [];

		if ($diyStatus) {
			$part_title = $diyData['part_title'];
			$status     = $diyData['status'];

			if ($status == 1) {
				$diyIdArr = [];
				foreach ($diyData["list"] as $item) {
					if (array_key_exists("id", $item) && $item['id']) {
						$diyIdArr[] = $item['id'];
					}
				}
				$diyIdCondition = implode("','", $diyIdArr);
				$diyIdCondition = " and id in ('$diyIdCondition') ";

				$order_by_condition = implode(",", $diyIdArr);
				$order_by           = "order by find_in_set(id,'$order_by_condition') ";

				$info = DB::select("
                  select id,title,img_url_compressed
                  from t_alive
                  where state=0 and app_id=? $diyIdCondition $order_by", [$appId]);

				if (count($info) == 0) {
					return false;
				}
			}

		}

		$list = $info;

		$result = [
			'part_title' => $part_title,
			'status'     => $status,
			'list'       => $list,
		];

		return $result;
	}

	//会员/专栏模块

	public function getIndexRecommendPart ($diyStatus, $diyData)
	{
		//暂不能配置，返回空对象
		$result = new \stdClass();;

		return $result;
	}

	//问答模块

	public function addModulePart ($index_sort, $moduleConfig)
	{
		$result = [];
		/**
		 * 获取当前业务版本
		 * 1-基础版;2-成长版;3-专业版
		 */
		$version_type = AppUtils::get_version_type();

		$version_type = 2;

		$version_name = "基础版";
		if ($version_type == 2) {
			$version_name = "成长版";
		} else if ($version_type == 3) {
			$version_name = "专业版";
		}

		//1-可用模块
		$userful_part = [];
		//2-已不可再添加
		$used_part = [];
		//3-未配置
		$unsetting_part = [];
		//4-版本没有
		$version_miss_part = [];

		$add_part   = [];
		$add_part[] = StringConstants::IndexBannerPart;
		if ($version_type == 2 || $version_type == 3) {
			$add_part[] = StringConstants::IndexCategoryPart;//成长版及以上
		} else {
			$version_miss_part[] = StringConstants::IndexCategoryPart;
		}
		$add_part[] = StringConstants::IndexCommunityPart;
		$add_part[] = StringConstants::IndexMemberPart;
		if ($version_type == 2 || $version_type == 3) {
			$add_part[] = StringConstants::IndexQuestionPart;//成长版及以上
		} else {
			$version_miss_part[] = StringConstants::IndexQuestionPart;
		}
		$add_part[] = StringConstants::IndexAlivePart;
		$add_part[] = StringConstants::IndexRecommendPart;

		//未配置的模块
		$unsettiong = [];
		if (!$moduleConfig['resource_category']) {
			array_push($unsettiong, StringConstants::IndexCategoryPart);
		}
		if (!$moduleConfig['has_que']) {
			array_push($unsettiong, StringConstants::IndexQuestionPart);
		} else {
			$appId = AppUtils::getAppID();

			$que_num = DB::select("select * from t_que_products where app_id = ? and state = 0", [$appId]);
			if ($que_num != null && $que_num && count($que_num) > 0) {
			} else {
				array_push($unsettiong, StringConstants::IndexQuestionPart);
			}
		}

		foreach ($add_part as $item) {
			//2-已不可再添加,暂时都只能添加一个
			if (in_array($item, $index_sort)) {
				array_push($used_part, $item);
			} //3-未配置
			else if (in_array($item, $unsettiong)) {
				array_push($unsetting_part, $item);
			} //1-可用模块
			else {
				//剩下的就是可以配置的
				array_push($userful_part, $item);
			}
		}

		foreach ($userful_part as $item) {
			$result[] = [
				'module' => $item,
				'status' => 1,
				'msg'    => '',
			];
		}

		foreach ($used_part as $item) {
			$result[] = [
				'module' => $item,
				'status' => 2,
				'msg'    => '该模块已添加，不能继续添加',
			];
		}

		foreach ($unsetting_part as $item) {
			$result[] = [
				'module' => $item,
				'status' => 3,
				'msg'    => '该模块尚未配置，请配置后再添加',
			];
		}

		foreach ($version_miss_part as $item) {
			$msg = $version_name . "不能使用该模块";

			$result[] = [
				'module' => $item,
				'status' => 4,
				'msg'    => $msg,
			];
		}

		return $result;
	}

	//直播模块

	public function saveNewPart (Request $request)
	{

		$appId = AppUtils::getAppID();

		$banner_part_input = $request->input('banner_part');

		$banner_part = [];

		if ($banner_part_input && count($banner_part_input) > 0) {
			DB::beginTransaction();

			$banner_id_arr = [];

			foreach ($banner_part_input as $banner_val) {

				$banner_id    = Utils::getUniId("b_");
				$current_time = Utils::getTime();

				$banner_parm = [
					'app_id'             => $appId,
					'id'                 => $banner_id,
					'title'              => $banner_val['title'],
					'image_url'          => $banner_val['image_url'],
					'img_url_compressed' => $banner_val['img_url_compressed'],
					'skip_type'          => $banner_val['skip_type'],
					'skip_target'        => $banner_val['skip_target'],
					'skip_title'         => $banner_val['skip_title'],
					'created_at'         => $current_time,
				];

				DB::table('t_banner')->insert($banner_parm);

				$banner_id_arr[] = $banner_id;
				ImageUtils::bannerImgCompress($request, $appId, $banner_id, $banner_val['image_url']);
			}
			DB::commit();

			foreach ($banner_id_arr as $item) {
				$banner_part[] = [
					'id' => $item,
				];
			}
		}

		$result = [
			'banner_part' => $banner_part,
		];

		return Utils::jsonResponse($result);
	}

	//最新模块

	public function saveDiySetting (Request $request)
	{

		$index_sort = $request->input('index_sort');
		$index_data = $request->input('index_data');
		$appId      = AppUtils::getAppID();
		$errorCode  = 1;

		try {
			$index_sort = json_decode($index_sort, true);
			$index_data = json_decode($index_data, true);

			if (is_array($index_sort) && is_array($index_data) && count($index_sort) === count($index_data)) {

				//检查参数是否符合规则
				$checkParm = $this->checkSaveParm($index_sort, $index_data);

				if ($checkParm) {

					$insertSort = $index_sort;
					$insertData = [];
					//验证通过，拼接成所需格式存入
					foreach ($index_sort as $index_sort_key => $index_sort_val) {
						if ($index_sort_val == StringConstants::IndexBannerPart) {
							$list = [];
							//如果有banner图
							if (count($index_data[ $index_sort_key ]['list']) > 0) {
								foreach ($index_data[ $index_sort_key ]['list'] as $index_data_item) {
									//更新banner图信息
									$updateResult = $this->updateBannerList($request, $index_data_item);
									$list[]['id'] = $index_data_item['id'];
								}
								$insertData[] = [
									'list' => $list,
								];
							} else {
								//没有banner图，不存该模块
								array_splice($insertSort, $index_sort_key, 1);
							}

						} else if (
							($index_sort_val == StringConstants::IndexCommunityPart) ||
							($index_sort_val == StringConstants::IndexMemberPart) ||
							($index_sort_val == StringConstants::IndexAlivePart)
						) {

							$part_title = $index_data[ $index_sort_key ]['part_title'];
							$status     = $index_data[ $index_sort_key ]['status'];
							$list       = [];

							if ($status == 1) {
								foreach ($index_data[ $index_sort_key ]['list'] as $index_data_item) {
									$list[]['id'] = $index_data_item['id'];
								}
							}
							$insertData[] = [
								'part_title' => $part_title,
								'status'     => $status,
								'list'       => $list,
							];
						} else if (
							($index_sort_val == StringConstants::IndexCategoryPart) ||
							($index_sort_val == StringConstants::IndexQuestionPart) ||
							($index_sort_val == StringConstants::IndexRecommendPart)
						) {
							$insertData[] = new \stdClass();
						}
					}

					$insertSort = json_encode($insertSort);
					$insertData = json_encode($insertData);
					$nowTime    = Utils::getTime();

					DB::statement("INSERT INTO t_shop_index_diy 
                    (app_id, index_sort, index_data, created_at,updated_at) 
                    VALUES (?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE index_sort = ?,index_data = ?,updated_at = ?",
						[$appId, $insertSort, $insertData, $nowTime, $nowTime, $insertSort, $insertData, $nowTime]);

					return Utils::jsonResponse(null);
				} else {
					$errorCode = 2;
				}

			} else {
				$errorCode = 3;
			}
		} catch (\Exception $e) {
			$errorCode = 4;
		}

		$logName = "homeDiyError.log";

		Utils::logFrom("首页自定义saveDiySetting报错", $logName);
		Utils::logFrom("errorCode:" . $errorCode, $logName);
		Utils::logFrom($appId, $logName);
		Utils::logFrom(print_r($index_sort, 1), $logName);
		Utils::logFrom(print_r($index_data, 1), $logName);

		return Utils::jsonResponse(null, $errorCode, "保存失败:" . $errorCode);

	}

	//新增模块的信息

	public function checkSaveParm ($index_sort, $index_data)
	{

		if (count($index_sort) == count($index_data)) {
			foreach ($index_sort as $sort_key => $sort_val) {
				//banner图要都有id
				if ($sort_val == StringConstants::IndexBannerPart) {

					if (array_key_exists("list", $index_data[ $sort_key ])
					) {
						//如果有banner图的话
						if ($index_data[ $sort_key ]['list'] && count($index_data[ $sort_key ]['list']) > 0) {
							foreach ($index_data[ $sort_key ]['list'] as $listItem) {
								if (array_key_exists("id", $listItem) &&
									array_key_exists("title", $listItem) &&
									array_key_exists("image_url", $listItem) &&
									array_key_exists("img_url_compressed", $listItem) &&
									array_key_exists("skip_type", $listItem) &&
									array_key_exists("skip_target", $listItem) &&
									array_key_exists("skip_title", $listItem) &&
									$listItem['id']
								) {
								} else {
									Utils::log("checkSaveParm1");

									return false;
								}
							}
						}
					} else {
						Utils::log("checkSaveParm2");

						return false;
					}
				} else if (
					($sort_val == StringConstants::IndexCommunityPart) ||
					($sort_val == StringConstants::IndexMemberPart) ||
					($sort_val == StringConstants::IndexAlivePart)
				) {
					if (array_key_exists("part_title", $index_data[ $sort_key ]) &&
						array_key_exists("status", $index_data[ $sort_key ]) &&
						array_key_exists("list", $index_data[ $sort_key ]) &&
						$index_data[ $sort_key ]['list']
					) {
						if ($index_data[ $sort_key ]['status'] == 1) {
							foreach ($index_data[ $sort_key ]['list'] as $listItem) {
								if (array_key_exists("id", $listItem) && $listItem['id']) {
								} else {
									Utils::log("checkSaveParm3");

									return false;
								}
							}
						}
					}
				} //分类导航，问答，最新暂不能编辑，不进行验证
				else if (
					($sort_val == StringConstants::IndexCategoryPart) ||
					($sort_val == StringConstants::IndexQuestionPart) ||
					($sort_val == StringConstants::IndexRecommendPart)
				) {
				} else {
					Utils::log("checkSaveParm4");

					return false;
				}
			}
		}

		return true;
	}

	//读取模块配置列表 1-图文、2-音频、3-视频、4-直播、5-活动、6-专栏、7-社群 8-会员

	public function updateBannerList (Request $request, $index_data_item)
	{
		$appId = AppUtils::getAppID();

		$updateParm = [
			$index_data_item['title'],
			$index_data_item['image_url'],
			$index_data_item['img_url_compressed'],
			$index_data_item['skip_target'],
			$index_data_item['skip_type'],
			$index_data_item['skip_title'],
			$appId,
			$index_data_item['id'],
		];

		////判断图片是有更改,如果更改准备压缩
		$modify     = false;
		$bannerInfo = DB::select("
select * from t_banner where app_id = ? and id = ? limit 1
", [$appId, $index_data_item['id']]);
		if ($bannerInfo && count($bannerInfo) > 0) {
			if (empty($bannerInfo[0]->image_url)
				|| $bannerInfo[0]->image_url != $index_data_item['image_url']) {   //如果压缩链接为空,或者源链接改变
				$modify = true;
			}
		}

		$update = DB::update("update t_banner
        set title = ?,image_url = ?,img_url_compressed = ?,skip_target = ?,skip_type = ?,skip_title = ?
        where app_id = ? and id = ? limit 1", $updateParm);

		if ($modify) {
			ImageUtils::bannerImgCompress($request, $appId, $index_data_item['id'], $index_data_item['image_url']);
		}

		return $update;
	}

	//搜索模块配置列表 1-图文、2-音频、3-视频、4-直播、5-活动、6-专栏、7-社群 8-会员

	public function getDiyModule (Request $request)
	{
		$module = $request->input('module');

		$id_arr = $request->input('id_arr');

		$idCondition = "";
		if ($id_arr && count($id_arr) > 0) {
			$idCondition = implode("','", $id_arr);
			$idCondition = " and id not in ('$idCondition') ";
		}

		if ($module && is_array($module) && count($module) > 0) {
			$appId = AppUtils::getAppID();

			$module_data = [];

			foreach ($module as $moduleItem) {

				$info = [];
				if ($moduleItem == $this::imageText) {
					$info = DB::select("select id,title,img_url_compressed,created_at as show_time,display_state,app_id  
                    from t_image_text where app_id = ? and display_state = 0 $idCondition order by show_time desc", [$appId]);
				} else if ($moduleItem == $this::audio) {
					$info = DB::select("select id,title,img_url_compressed,created_at as show_time,audio_state,app_id  
                    from t_audio where app_id = ? and audio_state = 0 $idCondition order by show_time desc", [$appId]);
				} else if ($moduleItem == $this::video) {
					$info = DB::select("select id,title,img_url_compressed,created_at as show_time,video_state,app_id   
                    from t_video where app_id = ? and video_state = 0 $idCondition order by show_time desc", [$appId]);
				} else if ($moduleItem == $this::alive) {
					$info = DB::select("select id,title,img_url_compressed,created_at as show_time,state,app_id 
                    from t_alive where app_id = ? and state = 0 $idCondition order by show_time desc", [$appId]);
				} else if ($moduleItem == $this::activity) {
					$info = DB::select("select id,title,img_url_compressed,created_at as show_time,activity_state,app_id  
                    from t_activity where app_id = ? and activity_state = 0 $idCondition order by show_time desc", [$appId]);
				} else if ($moduleItem == $this::column) {
					$info = DB::select("select id,name as title,img_url_compressed,created_at as show_time,state,app_id,is_member  
                    from t_pay_products where app_id = ? and state = 0 and is_member =0 $idCondition order by show_time desc", [$appId]);
				} else if ($moduleItem == $this::community) {
					$info = DB::select("select id,title,img_url_compressed,created_at as show_time,community_state,app_id  
                    from t_community where app_id = ? and community_state = 0 $idCondition order by show_time desc", [$appId]);
				} else if ($moduleItem == $this::member) {
					$info = DB::select("select id,name as title,img_url_compressed,created_at as show_time,state,app_id,is_member  
                    from t_pay_products where app_id = ? and state = 0 and is_member =1 $idCondition order by show_time desc", [$appId]);
				} else {
					return Utils::jsonResponse(null, StringConstants::Code_Failed, "参数错误");
				}

				if ($info && $info != null && count($info) > 0) {
					$module_data[] = $info;
				} else {
					$module_data[] = [];
				}
			}

			$module_sort = $module;

			$module_sort = [];

			foreach ($module as $key => $val) {
				$module_sort[] = (int)$val;
			}

			$result = [
				'module_sort' => $module_sort,
				'module_data' => $module_data,
			];

			return Utils::jsonResponse($result);

		}

		return Utils::jsonResponse(null, StringConstants::Code_Failed, "参数错误");

	}

	//获取banner图联动列表 0-不跳转，1-图文，2-音频，3-视频，4-直播，5-外部链接，6-频道（专栏及会员）

	public function searchDiyModule (Request $request)
	{
		$module         = $request->input('module');
		$search_content = $request->input('search_content');
		$id_arr         = $request->input('id_arr');

		$idCondition = "";
		if ($id_arr && count($id_arr) > 0) {
			$idCondition = implode("','", $id_arr);
			$idCondition = " and id not in ('$idCondition') ";
		}

		if ($module != null) {
			$appId = AppUtils::getAppID();

			$search_content = "%" . $search_content . "%";

			if (!$search_content) {
				$idCondition = "";
			}

			$info = [];
			if ($module == $this::imageText) {
				$info = DB::select("select id,title,img_url_compressed,created_at as show_time,display_state,app_id 
                    from t_image_text where app_id = ? and display_state = 0 and title like ? $idCondition order by show_time desc", [$appId, $search_content]);
			} else if ($module == $this::audio) {
				$info = DB::select("select id,title,img_url_compressed,created_at as show_time,audio_state,app_id 
                    from t_audio where app_id = ? and audio_state = 0 and title like ? $idCondition order by show_time desc", [$appId, $search_content]);
			} else if ($module == $this::video) {
				$info = DB::select("select id,title,img_url_compressed,created_at as show_time,video_state,app_id  
                    from t_video where app_id = ? and video_state = 0 and title like ? $idCondition order by show_time desc", [$appId, $search_content]);
			} else if ($module == $this::alive) {
				$info = DB::select("select id,title,img_url_compressed,created_at as show_time,state,app_id  
                    from t_alive where app_id = ? and state = 0 and title like ? $idCondition order by show_time desc", [$appId, $search_content]);
			} else if ($module == $this::activity) {
				$info = DB::select("select id,title,img_url_compressed,created_at as show_time,activity_state,app_id 
                    from t_activity where app_id = ? and activity_state = 0 and title like ? $idCondition order by show_time desc", [$appId, $search_content]);
			} else if ($module == $this::column) {
				$info = DB::select("select id,name as title,img_url_compressed,created_at as show_time,state,app_id,is_member 
                    from t_pay_products where app_id = ? and state = 0 and is_member =0 and name like ? $idCondition order by show_time desc", [$appId, $search_content]);
			} else if ($module == $this::community) {
				$info = DB::select("select id,title,img_url_compressed,created_at as show_time,community_state,app_id 
                    from t_community where app_id = ? and community_state = 0 and title like ? $idCondition order by show_time desc", [$appId, $search_content]);
			} else if ($module == $this::member) {
				$info = DB::select("select id,name as title,img_url_compressed,created_at as show_time,state,app_id,is_member 
                    from t_pay_products where app_id = ? and state = 0 and is_member =1 and name like ? $idCondition order by show_time desc", [$appId, $search_content]);
			} else {
				return Utils::jsonResponse(null, StringConstants::Code_Failed, "参数错误");
			}

			if ($info && $info != null && count($info) > 0) {
				$module_data = $info;
			} else {
				$module_data = [];
			}

			$result = [
				'module'      => (int)$module,
				'module_data' => $module_data,
			];

			return Utils::jsonResponse($result);

		}

		return Utils::jsonResponse(null, StringConstants::Code_Failed, "参数错误");

	}

	//更新banner图

	public function getBannerResource ()
	{
		$skip_type_sort = [0, 2, 3, 1, 4, 6, 5];
		$list           = [];

		$appId = AppUtils::getAppID();

		for ($key = 0; $key < count($skip_type_sort); $key++) {
			$item = $skip_type_sort[ $key ];

			$resultList = [];

			if ($item == 0) {
			} else if ($item == 1) {
				$resultList = DB::select("select id as skip_target,title as skip_title
                from t_image_text where app_id = ? and display_state = 0", [$appId]);
			} else if ($item == 2) {
				$resultList = DB::select("select id as skip_target,title as skip_title
                from t_audio where app_id = ? and audio_state = 0", [$appId]);
			} else if ($item == 3) {
				$resultList = DB::select("select id as skip_target,title as skip_title
                from t_video where app_id = ? and video_state = 0", [$appId]);
			} else if ($item == 4) {
				$resultList = DB::select("select id as skip_target,title as skip_title
                from t_alive where app_id = ? and state = 0", [$appId]);
			} else if ($item == 5) {
			} else if ($item == 6) {
				$resultList = DB::select("select id as skip_target,name as skip_title
                from t_pay_products where app_id = ? and state = 0", [$appId]);
			}

			if ($resultList && $resultList != null && count($resultList) > 0) {
				$list[] = $resultList;
			} else if ($item == 0 || $item == 5) {
				$list[] = [];
			} else {
				array_splice($skip_type_sort, $key, 1);
				$key--;
			}

		}

		if (count($skip_type_sort) != count($list)) {
			return Utils::jsonResponse(null, StringConstants::Code_Failed, "参数错误");
		}

		$result = [
			'skip_type_sort' => $skip_type_sort,
			'list'           => $list,
		];

		return Utils::jsonResponse($result);

	}
}