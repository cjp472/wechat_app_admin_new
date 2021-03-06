<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tools\AppUtils;
use App\Http\Controllers\Tools\Utils;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
	private $request;
	private $app_id;

	public function __construct (Request $request)
	{
		$this->request = $request;
		$this->app_id  = AppUtils::getAppID();
	}

	//插入数据库中
	public function create (Request $request)
	{
		$app_id          = $this->app_id;
		$invoice_type    = $request->input('invoice_type', 0);
		$invoice_title   = $request->input('invoice_title');
		$tax_file_number = $request->input('tax_file_number');
		//        $invoice_content = $request->input('invoice_content',1);
		$invoice_amount          = $request->input('invoice_amount', 0);
		$contact                 = $request->input('contact');
		$phone                   = $request->input('phone');
		$address                 = $request->input('address');
		$value_added_tax_address = $request->input('value_added_tax_address', null);
		$value_added_tax_phone   = $request->input('value_added_tax_phone', null);
		$value_added_tax_bank    = $request->input('value_added_tax_bank', null);
		$bank_account            = $request->input('value_added_tax_account', null);
		$remark                  = $request->input('remark', null);
		$applied_at              = date('Y-m-d H:i:s');
		$created_at              = date('Y-m-d H:i:s');
		//查询充值情况
		$balance = \DB::connection('db_ex_finance')->table('t_balance_charge')
			->where('app_id', $app_id)
			->whereIn('charge_type', [101, 103])
			->where('state', 0)
			->sum('fee');
		//查询该用户一共申请成功的发票总额
		$invoiced_money = \DB::connection('mysql_chain')->table('t_invoice')
			->where('app_id', $app_id)
			->where('state', '!=', 3)
			->sum('invoice_amount');
		if ($balance - $invoiced_money < $invoice_amount * 100)//充值金额-已经成功申请成功发票总额>=发票金额
		{
			$remain_balance = ($balance - $invoiced_money) / 100;

			return response()->json(['code' => -1, 'msg' => '可开发票余额不足', 'data' => [
				'data' => $remain_balance,
			]]);
		} else if ($invoice_amount * 100 < 50000) {
			return response()->json(['code' => -1, 'msg' => '可开发票金额不得小于500元', 'data' => [
				'data' => $invoice_amount,
			]]);
		} else {
			$res['invoice_id'] = Utils::getTaxId();
			$res['app_id']     = $app_id;
			//获取业务名称
			$name                 = \DB::connection('mysql_config')->table('t_app_conf')
				->select('wx_app_name')
				->where('app_id', $app_id)
				->where('wx_app_type', 1)
				->first();
			$res['customer_name'] = $name->wx_app_name;
			$res['invoice_type']  = $invoice_type;
			if ($invoice_title) {
				$res['invoice_title'] = $invoice_title;
			}
			if ($tax_file_number) {
				$res['tax_file_number'] = $tax_file_number;
			}
			if ($invoice_amount == 4800) {
				$res['invoice_content'] = 2;
			} else {
				$res['invoice_content'] = 1;
			}
			if ($invoice_amount) {
				$res['invoice_amount'] = $invoice_amount * 100;
			}
			$res['contact'] = $contact;
			$res['phone']   = $phone;
			$res['address'] = $address;
			if ($invoice_type == 2) {
				$res['value_added_tax_address'] = $value_added_tax_address;
				$res['value_added_tax_phone']   = $value_added_tax_phone;
				$res['value_added_tax_bank']    = $value_added_tax_bank;
				$res['bank_account']            = $bank_account;
			}
			$res['applied_at']    = $applied_at;
			$res['created_at']    = $created_at;
			$res['client_remark'] = $remark;
			$insert               = \DB::connection('mysql_chain')->table('t_invoice')
				->insert($res);
			if ($insert) {
				return response()->json(['code' => 1, 'msg' => '添加成功', 'data' => []]);
			} else {
				return response()->json(['code' => 0, 'msg' => '添加失败', 'data' => []]);
			}
		}
	}

	//获取发票信息列表
	public function getInvoiceInfo (Request $request)
	{
		$app_id = $this->app_id;
		//        $app_id = 'appydKaEx6P7499';
		$where = "app_id = '{$app_id}' ";
		//得到数据
		$info_list = \DB::connection('mysql_chain')->table('t_invoice')
			->whereRaw($where)
			->orderBy('created_at', 'desc')
			->paginate(10);

		return view("admin.invoiceManage.invoiceList", [
			'data' => $info_list,
		]);
	}

}





