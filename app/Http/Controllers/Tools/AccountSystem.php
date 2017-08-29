<?php
/**
 * Created by PhpStorm.
 * User: keven
 * Date: 05/03/16
 * Time: 09:39
 */

namespace App\Http\Controllers\Tools;

class AccountSystem
{
	/**
	 * 备注：type：
	 *101-开通充值如+4800
	 *102-开通赠送如流量费
	 *103-账户自己充值
	 * 201:开通扣费如-4800
	 * 202:流量扣费
	 * 203:存储扣费
	 * 204:短信扣费
	 * 205:提成扣费',
	 */
	// 1-账户充值(个人)
	const TYPE_ACCOUNT_RECHARGE = 103;
	const TYPE_OPEN_PRESENT = 102;
	const TYPE_GROWUP_OPEN_PRESENT = 104;
	const TYPE_VIP_OPEN_PRESENT = 105;
	const TYPE_OPEN_RECHARGE = 101;

	// 2-账户扣费(个人)
	const TYPE_CHARGE_OPEN = 201;

	/**
	 * 账户流水
	 */
	public static function account_money_record ($app_id, $charge_type, $fee, $order_id)
	{
		//1.记下该笔流水记录

		$params      = [];
		$now         = Utils::getTime();
		$charge_date = date('Y-m-d', time());

		if ($charge_type == AccountSystem::TYPE_CHARGE_OPEN) {
			$fee = -$fee;
		}

		//更新账户余额
		$result = \DB::update("update db_ex_config.t_app_conf set balance = balance + '$fee' where app_id = '$app_id' and wx_app_type = 1");

		if ($result) {

			//        $now = $params['charge_at'] = Utils::getTime();
			$params['app_id']          = $app_id;
			$params['charge_type']     = $charge_type;
			$params['account_balance'] = self::query_account_money($app_id);
			$account_balance           = self::query_account_money($app_id);

			$params['fee'] = abs($fee);
			$fee           = abs($fee);
			$params['id']  = $order_id;
			//            $order_id = Utils::getOrderId();

			//        $result = \DB::table('db_ex_finance.t_balance_charge')->insert($params);
			$result = \DB::insert("insert into db_ex_finance.t_balance_charge(id,app_id,charge_type,fee,charge_at,created_at,account_balance,charge_time)
VALUES ('$order_id','$app_id','$charge_type','$fee','$charge_date','$now','$account_balance','$now')");

			if ($result == 0) {
				return 0;
			}

			return 1;
		} else {
			return 0;
		}

		////        $now = $params['charge_at'] = Utils::getTime();
		//        $params['app_id'] = $app_id;
		//        $params['charge_type'] = $charge_type;
		//        $params['fee'] = $fee;
		//        $params['id'] = Utils::getOrderId();
		//        $order_id = Utils::getOrderId();
		//
		////        $result = \DB::table('db_ex_finance.t_balance_charge')->insert($params);
		//        $result = \DB::insert("insert into db_ex_finance.t_balance_charge(id,app_id,charge_type,fee,charge_at,created_at)
		//VALUES ('$order_id','$app_id','$charge_type','$fee','$charge_date','$now')");
		//
		//        if($result == 0){
		//            return 0;
		//        }
		//
		//
		//        return 1;
	}

	/**
	 * 查询账户余额
	 */
	public static function query_account_money ($appId)
	{
		$account = \DB::select("select balance from db_ex_config.t_app_conf where app_id = '$appId' and wx_app_type = 1 limit 1");

		if (!empty($account) && is_array($account)) {
			$account = $account[0]->balance;

			return $account;
		} else {
			return 0;
		}
	}

	/**
	 * 扣费流水记录
	 */
	public static function chargeback_account_money ()
	{

		//1.记下该笔流水记录

		return 0;
	}

}