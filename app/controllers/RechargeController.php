<?php

class RechargeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/
	public function getIndex()
	{
		
		return Response::json(array('status' => 'failure', 'message' => 'You can not Hit Us Directly'));
	}
	
	public function postRecharge()
	{
		$postdata=file_get_contents("php://input");
		if(!empty($postdata))
		{
			
			$currenttime=Commonmodel::dateandtime();
			$currentUserId=Input::get('currentUserId');
			$currentUserIdPk=Input::get('currentUserIdPk');
			$prodCode=Input::get('prodCode');
			$amount=Input::get('amount');
			$number=Input::get('number');
			$provider=Input::get('provider');
			$clientIp=Input::get('clientIp');
			$limit=Input::get('limit');
			$requestid=0;
			
			if($currentUserId!=""&&$currentUserIdPk!=""&&$prodCode!=""&&$amount!=""&&$number!=""&&$provider!=""&&$clientIp!="")
			{
				
				$checkbalance=Userfinance::where('ufin_user_id','=',$currentUserId)->where('ufin_main_balance','>=',$amount)->get();
				$checkproductcode=Rechargeservices::where('rm_name','=',$provider)->where('rm_prod_code','=',$prodCode)->get();

				if(count($checkproductcode)>0)
				{
					
					$admin_commission=$checkproductcode[0]->rm_commission;
					$fran_commission=$checkproductcode[0]->rm_dcommission;
					$retailser_commission=$checkproductcode[0]->rm_scommission;
					$sub_retail_commission=$checkproductcode[0]->rm_fcommission;
					$sub_distributer=$checkproductcode[0]->rm_sdcommission;
				
					$admin_commission_store=$amount*$admin_commission;
					$fran_commission_store=$admin_commission_store*$fran_commission;
					$retailser_commission_store=$fran_commission_store*$retailser_commission;
					$sub_retail_commission_store=$retailser_commission_store*$sub_retail_commission;
					$admin_distributer_store=$sub_retail_commission_store*$sub_distributer;
					
					if(count($checkbalance)>0)
					{
						$getstatus=Rechargeurl::rechargenumber($checkproductcode[0]->rm_ezypay_opcode,$number,$amount,$requestid);
						$createdtype=User::where('UD_USER_ID','=',$currentUserId)->pluck('UD_USER_TYPE');				
						
						$currentbalnce=$checkbalance[0]->ufin_main_balance;
	
						if($getstatus[4]!='Transaction Successful'&&$getstatus[4]!='Transaction Pending'&&$getstatus[4]!='Timeout')
						{
								return Response::json(array('message' => $getstatus[4]));	
						}
						elseif($getstatus[4]=='Transaction Successful')
						{
							
								$input=array
								(
								'rd_prod_code'=>$prodCode,
								'rd_service_provider'=>$provider,
								'rd_number'=>$number,
								'rd_amount'=>$amount,
								'rd_created_at'=>$currenttime,
								'rd_created_by'=>$currentUserId,
								'rd_created_type'=>$createdtype,
								'rd_result'=>"successfull",
								'rd_trans_id'=>$getstatus[0],
								'rd_client_ip'=>Input::get('clientIp'),
								);
								
								$recharenew= new Rechargedetails;
								$recharenew->create($recharenew);
								$currentbalnce=$currentbalnce-$amount;
								$balance=array
								(
									'ufin_main_balance'=>$currentbalnce,
								);
								
								Userfinance::where('ufin_user_id',$currentUserId)->update($balance);
								
								$comission=array
								(
									'lr_date'=>$currenttime,
									'lr_trans_type'=>'Debited',
									'lr_comment'=>'Debited by Easy Pay API',
									'lr_debit_amount'=>$amount,
									'lr_post_balance'=>$currentbalnce,
									'lr_created_by'=>$currentUserId,
									'lr_prod_code'=>$prodCode,
								);
								
								$comissionnew= new Comission;
								$comissionnew->create($comission);
								return Response::json(array('status' => 'success', 'message' => 'Recharge Done Successfully'));	
						}
						elseif($getstatus[4]!='Transaction Pending'||$getstatus[4]!='Timeout')
						{
								$input=array
								(
								'rd_prod_code'=>$prodCode,
								'rd_service_provider'=>$provider,
								'rd_number'=>$number,
								'rd_amount'=>$amount,
								'rd_created_at'=>$currenttime,
								'rd_created_by'=>$currentUserId,
								'rd_created_type'=>$createdtype,
								'rd_result'=>"pending",
								'rd_trans_id'=>$getstatus[0],
								'rd_client_ip'=>Input::get('clientIp'),
								);
								
								$recharenew= new Rechargedetails;
								$recharenew->create($input);
								$currentbalnce=$currentbalnce-$amount;
								$balance=array
								(
									'ufin_main_balance'=>$currentbalnce,
								);
								
								Userfinance::where('ufin_user_id',$currentUserId)->update($balance);
								$comission=array
								(
									'lr_date'=>$currenttime,
									'lr_trans_type'=>'Debited',
									'lr_comment'=>'Debited by Easy Pay API',
									'lr_debit_amount'=>$amount,
									'lr_post_balance'=>$currentbalnce,
									'lr_created_by'=>$currentUserId,
									'lr_prod_code'=>$prodCode,
								);
								
								$comissionnew= new Comission;
								$comissionnew->create($comission);
								return Response::json(array('status' => 'success', 'message' => 'Transaction is Pending'));	
						}
						
						else
						{
								return Response::json(array('status' => 'failure', 'message' => 'Contact Your Service Provider'));	
						}
					}
					else
					{
						return Response::json(array('status' => 'failure', 'message' => 'You Do not have Sufficient Balance'));		
					}
				}
				else
				{
					return Response::json(array('status' => 'failure', 'message' => 'There is No Such Product ID'));		
				}
				
			}
			else
			{
				return Response::json(array('status' => 'failure', 'message' => 'Fill all manditary fields'));
			}
		}
		else
		{
			return Response::json(array('status' => 'failure', 'message' => 'Fill all manditary fields'));
		}
	}
	
	
	public function postPostpaidrecharge()
	{
		$postdata=file_get_contents("php://input");
		if(!empty($postdata))
		{
			
			$currenttime=Commonmodel::dateandtime();
			$currentUserId=Input::get('currentUserId');
			$currentUserIdPk=Input::get('currentUserIdPk');
			$prodCode=Input::get('prodCode');
			$amount=Input::get('amount');
			$number=Input::get('number');
			$provider=Input::get('provider');
			$clientIp=Input::get('clientIp');
			$limit=Input::get('limit');
			$requestid=0;
			
			if($currentUserId!=""&&$currentUserIdPk!=""&&$prodCode!=""&&$amount!=""&&$number!=""&&$provider!=""&&$clientIp!="")
			{
				
				$checkbalance=Userfinance::where('ufin_user_id','=',$currentUserId)->where('ufin_main_balance','>=',$amount)->get();
				$checkproductcode=Rechargeservices::where('rm_name','=',$provider)->where('rm_prod_code','=',$prodCode)->get();
				if(count($checkproductcode)>0)
				{
					
					$admin_commission=$checkproductcode[0]->rm_commission;
					$fran_commission=$checkproductcode[0]->rm_dcommission;
					$retailser_commission=$checkproductcode[0]->rm_scommission;
					$sub_retail_commission=$checkproductcode[0]->rm_fcommission;
					$sub_distributer=$checkproductcode[0]->rm_sdcommission;
				
					$admin_commission_store=$amount*$admin_commission;
					$fran_commission_store=$admin_commission_store*$fran_commission;
					$retailser_commission_store=$fran_commission_store*$retailser_commission;
					$sub_retail_commission_store=$retailser_commission_store*$sub_retail_commission;
					$admin_distributer_store=$sub_retail_commission_store*$sub_distributer;
					
					if(count($checkbalance)>0)
					{
						$getstatus=Rechargeurl::rechargepostpaidnumber($checkproductcode[0]->rm_ezypay_opcode,$number,$amount,$requestid);	
						$createdtype=User::where('UD_USER_ID','=',$currentUserId)->pluck('UD_USER_TYPE');
						$currentbalnce=$checkbalance[0]->ufin_main_balance;
	
						if($getstatus[4]!='Transaction Successful'&&$getstatus[4]!='Transaction Pending'&&$getstatus[4]!='Timeout')
						{
								return Response::json(array('message' => $getstatus[4]));	
						}
						elseif($getstatus[4]=='Transaction Successful')
						{
							
								$input=array
								(
								'rd_prod_code'=>$prodCode,
								'rd_service_provider'=>$provider,
								'rd_number'=>$number,
								'rd_amount'=>$amount,
								'rd_created_at'=>$currenttime,
								'rd_created_by'=>$currentUserId,
								'rd_created_type'=>$createdtype,
								'rd_result'=>"successfull",
								'rd_trans_id'=>$getstatus[0],
								'rd_client_ip'=>Input::get('clientIp'),
								);
								
								$recharenew= new Rechargedetails;
								$recharenew->create($input);
								$currentbalnce=$currentbalnce-$amount;
								$balance=array
								(
									'ufin_main_balance'=>$currentbalnce,
								);
								
								Userfinance::where('ufin_user_id',$currentUserId)->update($balance);
								
								$comission=array
								(
									'lr_date'=>$currenttime,
									'lr_trans_type'=>'Debited',
									'lr_comment'=>'Debited by Easy Pay API',
									'lr_debit_amount'=>$amount,
									'lr_post_balance'=>$currentbalnce,
									'lr_created_by'=>$currentUserId,
									'lr_prod_code'=>$prodCode,
								);
								
								$comissionnew= new Comission;
								$comissionnew->create($comission);
								return Response::json(array('status' => 'success', 'message' => 'Recharge Done Successfully'));	
						}
						elseif($getstatus[4]!='Transaction Pending'||$getstatus[4]!='Timeout')
						{
								$input=array
								(
								'rd_prod_code'=>$prodCode,
								'rd_service_provider'=>$provider,
								'rd_number'=>$number,
								'rd_amount'=>$amount,
								'rd_created_at'=>$currenttime,
								'rd_created_by'=>$currentUserId,
								'rd_created_type'=>$createdtype,
								'rd_result'=>"pending",
								'rd_trans_id'=>$getstatus[0],
								'rd_client_ip'=>Input::get('clientIp'),
								'rd_provider'=>'Easypay',
								);
								
								$recharenew= new Rechargedetails;
								$recharenew->create($recharenew);
								$currentbalnce=$currentbalnce-$amount;
								$balance=array
								(
									'ufin_main_balance'=>$currentbalnce,
								);
								
								Userfinance::where('ufin_user_id',$currentUserId)->update($balance);
								
								$comission=array
								(
									'lr_date'=>$currenttime,
									'lr_trans_type'=>'Debited',
									'lr_comment'=>'Debited by Easy Pay API',
									'lr_debit_amount'=>$amount,
									'lr_post_balance'=>$currentbalnce,
									'lr_created_by'=>$currentUserId,
									'lr_prod_code'=>$prodCode,
								);
								
								$comissionnew= new Comission;
								$comissionnew->create($comission);
								return Response::json(array('status' => 'success', 'message' => 'Transaction is Pending'));	
						}
						
						else
						{
								return Response::json(array('status' => 'failure', 'message' => 'Contact Your Service Provider'));	
						}
					}
					else
					{
						return Response::json(array('status' => 'failure', 'message' => 'You Do not have Sufficient Balance'));		
					}
				}
				else
				{
					return Response::json(array('status' => 'failure', 'message' => 'There is No Such Product ID'));		
				}
				
			}
			else
			{
				return Response::json(array('status' => 'failure', 'message' => 'Fill all manditary fields'));
			}
		}
		else
		{
			return Response::json(array('status' => 'failure', 'message' => 'Fill all manditary fields'));
		}
	}
	
	
	public function getBalance()
	{
		$getbalance=Rechargeurl::balance();	
		$balance=$getbalance[0];
		$date=$getbalance[1];
		$status=$getbalance[2];
		
		return Response::json(array('balance' => $balance, 'status' => $status));
		
	}
	
	public function getStatus()
	{
		$getbalance=Rechargeurl::status();	
		$formatter = Formatter::make($getbalance, Formatter::JSON);
	//	$json  = $getbalance->toJson();
		$xml = XmlParser::load($formatter);
		print_r($formatter);
		exit;
		return Response::json(array('balance' => $balance, 'status' => $status));
		
	}
	
}
