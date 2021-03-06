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
		$output=array(
			'status' => 'success', 
			'message' =>'data fetched' , 
			'data' =>'hi' , 
			);
		return Response::json($output);
	}
	public function postHistory()
	{
				
				
			$postdata=file_get_contents("php://input");
			if(!empty($postdata))
			{
				$currentUserId=Input::get('currentUserId');
				$currentUserIdPk=Input::get('currentUserIdPk');
				$prodCode=Input::get('prodCode');
				$clientIp=Input::get('clientIp');
				$limit=Input::get('limit');
				$usercount=User::select('UD_USER_ID','UD_USER_TYPE')->where('UD_USER_ID','=',$currentUserId)->get();
				if(count($usercount)>0)
				{
					if($usercount[0]->UD_USER_TYPE=='SA'||$usercount[0]->UD_USER_TYPE=='SAS')
					{
						$panc=Recharge::where('rd_prod_code','=',$prodCode)->get();
							if(count($panc)>0)
							{
								return Response::json($panc);
							}
							else
							{
								return Response::json(array('status' => 'failure', 'message' => 'You Din"t Create Any Pan card till Now'));
							}
						}
					}
					
					
					
					else
					{
						return Response::json(array('status' => 'failure', 'message' => 'You can"t Access the Pan Card'));
					}

			}
			else
			{
				return Response::json(array('status' => 'failure', 'message' => 'You Don"t have any coupon to register pancard'));
			}
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
						
					if(count($checkbalance)>0)
					{
						$getstatus=Rechargeurl::rechargenumber($checkproductcode[0]->rm_ezypay_opcode,$number,$amount,$requestid);
						$createdtype=User::where('UD_USER_ID','=',$currentUserId)->pluck('UD_USER_TYPE');
						
						if($createdtype=='FR'||$createdtype=='SFR')
						{
							$currentbalnce=Userfinance::where('ufin_user_id','=',$currentUserId)->pluck('ufin_main_balance');
						}
						elseif($createdtype=='FRS'||$createdtype=='SFRS')
						{
							$parentid=User::where('UD_USER_ID','=',$currentUserId)->pluck('UD_PARENT_ID');
							$currentbalnce=User::where('ufin_user_id_pk_fk','=',$parentid)->pluck('ufin_main_balance');
						}
						
						
						if($getstatus[4]!='Transaction Successful'&&$getstatus[4]!='Transaction Pending'&&$getstatus[4]!='Timeout')
						{
							return Response::json(array('message' => $getstatus[4]));
						}
						elseif($getstatus[4]=='Transaction Successful'||$getstatus[4]!='Transaction Pending'||$getstatus[4]!='Timeout')
						{
							if($getstatus[4]=='Transaction Successful')
							{
								$updatestatusvar="Success";
							}
							else
							{
								$updatestatusvar="Pending";
							}	
							$input=array
							(
									'rd_prod_code'=>$prodCode,
									'rd_service_provider'=>$provider,
									'rd_number'=>$number,
									'rd_amount'=>$amount,
									'rd_created_at'=>$currenttime,
									'rd_created_by'=>$currentUserId,
									'rd_created_type'=>$createdtype,
									'rd_result'=>$updatestatusvar,
									'rd_trans_id'=>"1234567",
									'rd_client_ip'=>Input::get('clientIp'),
									'rd_provider'=>"Ezypay",
							);
							
							//'rd_trans_id'=>$getstatus[0],
							$recharenew= new Rechargedetails;
							$recharenew->create($input);
						
							
							if($createdtype=='FR'||$createdtype=='SFR')
							{
								
								$currentbalnce=$currentbalnce-$amount;
								$balance=array
								(
										'ufin_main_balance'=>$currentbalnce,
								);
								Userfinance::where('ufin_user_id',$currentUserId)->update($balance);
								
							}
							elseif($createdtype=='FRS'||$createdtype=='SFRS')
							{
								$parentid=User::where('UD_USER_ID','=',$currentUserId)->pluck('UD_PARENT_ID');
								$currentbalnce=User::where('ufin_user_id_pk_fk','=',$parentid)->pluck('ufin_main_balance');
								$currentbalnce=$currentbalnce-$amount;
								$balance=array
								(
										'ufin_main_balance'=>$currentbalnce,
								);
								Userfinance::where('ufin_user_id',$currentbalnce)->update($balance);
							}
							
							
							
							
	
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
						
							$getlastinseertedcomid=Comission::insertGetId($comission);
							$getcomission=Rechargeservices::where('rm_name','=',$provider)->get();
							
							if($getcomission)
							{
								
								
								foreach($getcomission as $getcomissions){}
								
								$admincomission=$getcomissions->rm_commission;
								$admincomissionvalue=$admincomission/100;
								$admincomissionamount=$amount*((1*$admincomissionvalue)/(1+$admincomissionvalue));
								
								$statepartner=$getcomissions->rm_scommission;
								$statepartnervalue=$statepartner/100;
								$statepartneramount=$amount*(1*$statepartnervalue)/(1+$statepartnervalue);
								
								
								$statedistributer=$getcomissions->rm_sdcommission;
								$statedistributervalue=$admincomission/100;
								$statedistributeramount=$amount*(1*$statedistributervalue)/(1+$statedistributervalue);
								
								$distributer=$getcomissions->rm_dcommission;
								$distributervalue=$statedistributer/100;
								$distributeramount=$amount*(1*$distributervalue)/(1+$distributervalue);
								
								$franchise=$getcomissions->rm_fcommission;
								$franchisevalue=$franchise/100;
								$franchiseamount=$amount*(1*$franchisevalue)/(1+$franchisevalue);
								
								$subfranchise=$getcomissions->rm_sfcommission;
								$subfranchisevalue=$subfranchise/100;
								$subfranchiseamount=$amount*(1*$subfranchisevalue)/(1+$subfranchisevalue);
								
								
								
							
								
								$getusertype=User::where('UD_USER_ID','=',$currentUserId)->pluck('UD_USER_TYPE');
								if($getusertype=='FR'||$getusertype=='FRS')
								{
									if($getusertype=='FR')
									{
										$fr_commission_id=User::where('UD_USER_ID','=',$currentUserId)->pluck('UD_ID_PK');
										$fr_commission_balance=Userfinance::where('ufin_user_id_pk_fk','=',$fr_commission_id)->pluck('ufin_main_balance');
									}
									elseif($getusertype=='FRS')
									{
										$frs_commission_id=User::where('UD_USER_ID','=',$currentUserId)->pluck('UD_ID_PK');
										$frs_commission_balance=Userfinance::where('ufin_user_id_pk_fk','=',$frs_commission_id)->pluck('ufin_main_balance');
										$fr_commission_id=User::where('UD_USER_ID','=',$frs_commission_id)->pluck('UD_PARENT_ID');
										$fr_commission_balance=Userfinance::where('ufin_user_id_pk_fk','=',$fr_commission_id)->pluck('ufin_main_balance');
									}
									
									
									$fr_commission_balance=$fr_commission_balance+$franchiseamount;
									$sd_commission_id=User::where('UD_ID_PK','=',$fr_commission_id)->pluck('UD_PARENT_ID');
									$sd_commission_balance=Userfinance::where('ufin_user_id_pk_fk','=',$sd_commission_id)->pluck('ufin_main_balance');
									$sd_commission_balance=$sd_commission_balance+$distributeramount;
									$d_commission_id=User::where('UD_ID_PK','=',$sd_commission_id)->pluck('UD_PARENT_ID');
									$d_commission_balance=Userfinance::where('ufin_user_id_pk_fk','=',$d_commission_id)->pluck('ufin_main_balance');
									$d_commission_balance=$d_commission_balance+$distributeramount;
									$sp_commissio_id=User::where('UD_ID_PK','=',$d_commission_id)->pluck('UD_PARENT_ID');
									$sp_commissio_balance=Userfinance::where('ufin_user_id_pk_fk','=',$sp_commissio_id)->pluck('ufin_main_balance');
									$sp_commissio_balance=$sp_commissio_balance+$statepartneramount;
									$sa_commission_id=User::where('UD_ID_PK','=',$sp_commissio_id)->pluck('UD_PARENT_ID');
									$sa_commission_balance=Userfinance::where('ufin_user_id_pk_fk','=',$sa_commission_id)->pluck('ufin_main_balance');
									$sa_commissio_balance=$sa_commission_balance+$admincomissionamount;
									
									
									$sabalance1=array
									(
											'ufin_main_balance'=>$sa_commissio_balance,
									);
									
									Userfinance::where('ufin_user_id_pk_fk',$sa_commission_id)->update($sabalance1);
									$spbalance1=array
									(
											'ufin_main_balance'=>$sp_commissio_balance,
									);
									Userfinance::where('ufin_user_id_pk_fk',$sp_commissio_id)->update($spbalance1);
									
									$sd_commission_balance1=array
									(
											'ufin_main_balance'=>$sp_commissio_balance,
									);
									Userfinance::where('ufin_user_id_pk_fk',$sd_commission_id)->update($sd_commission_balance1);
								
									$d_commission_balance1=array
									(
											'ufin_main_balance'=>$sp_commissio_balance,
									);
									Userfinance::where('ufin_user_id_pk_fk',$d_commission_id)->update($d_commission_balance1);
									
									
									if($getusertype=='FR')
									{
										
										$f_commission_balance1=array
										(
												'ufin_main_balance'=>$fr_commission_balance,
										);
										
										Userfinance::where('ufin_user_id_pk_fk',$fr_commission_id)->update($f_commission_balance1);
										
									}
									elseif($getusertype=='FRS')
									{
										
										$f_commission_balance1=array
										(
												'ufin_main_balance'=>$fr_commission_balance,
										);
										Userfinance::where('ufin_user_id_pk_fk',$fr_commission_id)->update($f_commission_balance1);
									}
									
								
									
									$rechargeledgerupdate=array
									(
											'rchlgr_lr_id'=>$getlastinseertedcomid,
											'rchlgr_date'=>$currenttime,
											'rchlgr_sa_id'=>$sa_commission_id,
											'rchlgr_sa_commission'=>$admincomissionamount,
											'rchlgr_sp_id'=>$sp_commissio_id,
											'rchlgr_sp_commission'=>$statepartneramount,
											'rchlgr_sd_id'=>$sd_commission_id,
											'rchlgr_sd_commission'=>$statedistributeramount,
											'rchlgr_d_id'=>$d_commission_id,
											'rchlgr_d_commission'=>$distributeramount,
											'rchlgr_fr_id'=>$fr_commission_id,
											'rchlgr_fr_commission'=>$franchiseamount,
												
									);
									$newregcomittion=New Rechargeledger;
									$newregcomittion->create($rechargeledgerupdate);
									
									
								}
								elseif($getusertype=='SFR'||$getusertype=='SFRS')
								{
									
									if($getusertype=='SFR')
									{
										$sfr_commission_id=User::where('UD_USER_ID','=',$currentUserId)->pluck('UD_ID_PK');
										$sfr_commission_balance=Userfinance::where('ufin_user_id_pk_fk','=',$sfr_commission_id)->pluck('ufin_main_balance');
									}
									elseif($getusertype=='SFRS')
									{
										$sfrs_commission_id=User::where('UD_USER_ID','=',$currentUserId)->pluck('UD_ID_PK');
										$sfrs_commission_balance=Userfinance::where('ufin_user_id_pk_fk','=',$sfr_commission_id)->pluck('ufin_main_balance');
										$sfr_commission_id=User::where('UD_USER_ID','=',$sfrs_commission_id)->pluck('UD_PARENT_ID');
										$sfr_commission_balance=Userfinance::where('ufin_user_id_pk_fk','=',$sfr_commission_id)->pluck('ufin_main_balance');
									}
									
									
									$sfr_commission_balance=$sfr_commission_balance+$subfranchiseamount;
									$fr_commission_id=User::where('UD_ID_PK','=',$sfr_commission_id)->pluck('UD_PARENT_ID');
									$fr_commission_balance=Userfinance::where('ufin_user_id_pk_fk','=',$fr_commission_id)->pluck('ufin_main_balance');
									$fr_commission_balance=$fr_commission_balance+$franchiseamount;
									$d_commission_id=User::where('UD_ID_PK','=',$fr_commission_id)->pluck('UD_PARENT_ID');
									$d_commission_balance=Userfinance::where('ufin_user_id_pk_fk','=',$d_commission_id)->pluck('ufin_main_balance');
									$d_commission_balance=$d_commission_balance+$distributeramount;
									$sd_commission_id=User::where('UD_ID_PK','=',$d_commission_id)->pluck('UD_PARENT_ID');
									$sd_commission_balance=Userfinance::where('ufin_user_id_pk_fk','=',$sd_commission_id)->pluck('ufin_main_balance');
									$sd_commission_balance=$sd_commission_balance+$distributeramount;
									$sp_commissio_id=User::where('UD_ID_PK','=',$sa_commission_id)->pluck('UD_PARENT_ID');
									$sp_commissio_balance=Userfinance::where('ufin_user_id_pk_fk','=',$sp_commissio_id)->pluck('ufin_main_balance');
									$sp_commissio_balance=$sp_commissio_balance+$statepartneramount;
									$sa_commission_id=User::where('UD_ID_PK','=',$fr_commission_id)->pluck('UD_PARENT_ID');
									$sa_commission_balance=Userfinance::where('ufin_user_id_pk_fk','=',$sa_commission_id)->pluck('ufin_main_balance');
									$sa_commission_balance=$sa_commission_balance+$admincomissionamount;
									
								
									
								$sabalance1=array
									(
											'ufin_main_balance'=>$sa_commissio_balance,
									);
									Userfinance::where('ufin_user_id_pk_fk',$sa_commission_id)->update($sabalance1);
									$spbalance1=array
									(
											'ufin_main_balance'=>$sp_commissio_balance,
									);
									Userfinance::where('ufin_user_id_pk_fk',$sp_commissio_id)->update($spbalance1);
									
									$sd_commission_balance1=array
									(
											'ufin_main_balance'=>$sp_commissio_balance,
									);
									Userfinance::where('ufin_user_id_pk_fk',$sd_commission_id)->update($sd_commission_balance1);
								
									$d_commission_balance1=array
									(
											'ufin_main_balance'=>$sp_commissio_balance,
									);
									Userfinance::where('ufin_user_id_pk_fk',$d_commission_id)->update($d_commission_balance1);
									
										
									$f_commission_balance1=array
									(
											'ufin_main_balance'=>$fr_commission_balance,
									);
									Userfinance::where('ufin_user_id_pk_fk',$fr_commission_id)->update($f_commission_balance1);
								
									
									$sfr_commission_balance1=array
									(
											'ufin_main_balance'=>$sfr_commission_balance,
									);
									Userfinance::where('ufin_user_id_pk_fk',$fr_commission_id)->update($sfr_commission_balance1);
										
									$getcomission=array
									(
											'rchlgr_lr_id'=>$getlastinseertedcomid,
											'rchlgr_sa_id'=>$sa_commission_id,
											'rchlgr_sa_commission'=>$admincomissionamount,
											'rchlgr_sp_id'=>$sp_commissio_id,
											'rchlgr_sp_commission'=>$statepartneramount,
											'rchlgr_sd_id'=>$sd_commission_id,
											'rchlgr_sd_commission'=>$statedistributeramount,
											'rchlgr_d_id'=>$d_commission_id,
											'rchlgr_d_commission'=>$distributeramount,
											'rchlgr_fr_id'=>$fr_commission_id,
											'rchlgr_fr_commission'=>$franchiseamount,
											'rchlgr_sfr_id'=>$sfr_commission_id,
											'rchlgr_sfr_commission'=>$subfranchiseamount,
												
									);
									
									
									$newregcomittion=New Rechargeledger;
									$newregcomittion->create($getcomission);
									
								} 
								
								
							}
							
							return Response::json(array('status' => 'success', 'message' => 'Recharge Done Successfully'));
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
						elseif($getstatus[4]=='Transaction Successful'||$getstatus[4]!='Transaction Pending'||$getstatus[4]!='Timeout')
						{
							if($getstatus[4]=='Transaction Successful')
							{
								$updatestatusvar="Success";
							}
							else
							{
								$updatestatusvar="Pending";
							}

							$input=array
							(
									'rd_prod_code'=>$prodCode,
									'rd_service_provider'=>$provider,
									'rd_number'=>$number,
									'rd_amount'=>$amount,
									'rd_created_at'=>$currenttime,
									'rd_created_by'=>$currentUserId,
									'rd_created_type'=>$createdtype,
									'rd_result'=>$updatestatusvar,
									'rd_trans_id'=>$getstatus[0],
									'rd_client_ip'=>Input::get('clientIp'),
									'rd_provider'=>"Ezypay",
							);
								
							//
							$recharenew= new Rechargedetails;
							$recharenew->create($input);
							
								
							if($createdtype=='FR'||$createdtype=='SFR')
							{
							
								$currentbalnce=$currentbalnce-$amount;
								$balance=array
								(
										'ufin_main_balance'=>$currentbalnce,
								);
								Userfinance::where('ufin_user_id',$currentUserId)->update($balance);
							
							}
							elseif($createdtype=='FRS'||$createdtype=='SFRS')
							{
								$parentid=User::where('UD_USER_ID','=',$currentUserId)->pluck('UD_PARENT_ID');
								$currentbalnce=User::where('ufin_user_id_pk_fk','=',$parentid)->pluck('ufin_main_balance');
								$currentbalnce=$currentbalnce-$amount;
								$balance=array
								(
										'ufin_main_balance'=>$currentbalnce,
								);
								Userfinance::where('ufin_user_id',$currentbalnce)->update($balance);
							}
								
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
							
							$getlastinseertedcomid=Comission::insertGetId($comission);
							$getcomission=Rechargeservices::where('rm_name','=',$provider)->get();
								
							if($getcomission)
							{
							
							
								foreach($getcomission as $getcomissions){}
								
								$admincomission=$getcomissions->rm_commission;
								$admincomissionvalue=$admincomission/100;
								$admincomissionamount=$amount*((1*$admincomissionvalue)/(1+$admincomissionvalue));
								
								$statepartner=$getcomissions->rm_scommission;
								$statepartnervalue=$statepartner/100;
								$statepartneramount=$amount*(1*$statepartnervalue)/(1+$statepartnervalue);
								
								
								$statedistributer=$getcomissions->rm_sdcommission;
								$statedistributervalue=$admincomission/100;
								$statedistributeramount=$amount*(1*$statedistributervalue)/(1+$statedistributervalue);
								
								$distributer=$getcomissions->rm_dcommission;
								$distributervalue=$statedistributer/100;
								$distributeramount=$amount*(1*$distributervalue)/(1+$distributervalue);
								
								$franchise=$getcomissions->rm_fcommission;
								$franchisevalue=$franchise/100;
								$franchiseamount=$amount*(1*$franchisevalue)/(1+$franchisevalue);
								
								$subfranchise=$getcomissions->rm_sfcommission;
								$subfranchisevalue=$subfranchise/100;
								$subfranchiseamount=$amount*(1*$subfranchisevalue)/(1+$subfranchisevalue);
							
							
							
								$getusertype=User::where('UD_USER_ID','=',$currentUserId)->pluck('UD_USER_TYPE');
								if($getusertype=='FR'||$getusertype=='FRS')
								{
									if($getusertype=='FR')
									{
										$fr_commission_id=User::where('UD_USER_ID','=',$currentUserId)->pluck('UD_ID_PK');
										$fr_commission_balance=Userfinance::where('ufin_user_id_pk_fk','=',$fr_commission_id)->pluck('ufin_main_balance');
									}
									elseif($getusertype=='FRS')
									{
										$frs_commission_id=User::where('UD_USER_ID','=',$currentUserId)->pluck('UD_ID_PK');
										$frs_commission_balance=Userfinance::where('ufin_user_id_pk_fk','=',$frs_commission_id)->pluck('ufin_main_balance');
										$fr_commission_id=User::where('UD_USER_ID','=',$frs_commission_id)->pluck('UD_PARENT_ID');
										$fr_commission_balance=Userfinance::where('ufin_user_id_pk_fk','=',$fr_commission_id)->pluck('ufin_main_balance');
									}
										
										
									$fr_commission_balance=$fr_commission_balance+$franchiseamount;
									$sd_commission_id=User::where('UD_ID_PK','=',$fr_commission_id)->pluck('UD_PARENT_ID');
									$sd_commission_balance=Userfinance::where('ufin_user_id_pk_fk','=',$sd_commission_id)->pluck('ufin_main_balance');
									$sd_commission_balance=$sd_commission_balance+$distributeramount;
									$d_commission_id=User::where('UD_ID_PK','=',$sd_commission_id)->pluck('UD_PARENT_ID');
									$d_commission_balance=Userfinance::where('ufin_user_id_pk_fk','=',$d_commission_id)->pluck('ufin_main_balance');
									$d_commission_balance=$d_commission_balance+$distributeramount;
									$sp_commissio_id=User::where('UD_ID_PK','=',$d_commission_id)->pluck('UD_PARENT_ID');
									$sp_commissio_balance=Userfinance::where('ufin_user_id_pk_fk','=',$sp_commissio_id)->pluck('ufin_main_balance');
									$sp_commissio_balance=$sp_commissio_balance+$statepartneramount;
									$sa_commission_id=User::where('UD_ID_PK','=',$sp_commissio_id)->pluck('UD_PARENT_ID');
									$sa_commission_balance=Userfinance::where('ufin_user_id_pk_fk','=',$sa_commission_id)->pluck('ufin_main_balance');
									$sa_commissio_balance=$sa_commission_balance+$admincomissionamount;
										
										
									$sabalance1=array
									(
											'ufin_main_balance'=>$sa_commissio_balance,
									);
									Userfinance::where('ufin_user_id_pk_fk',$sa_commission_id)->update($sabalance1);
									$spbalance1=array
									(
											'ufin_main_balance'=>$sp_commissio_balance,
									);
									Userfinance::where('ufin_user_id_pk_fk',$sp_commissio_id)->update($spbalance1);
										
									$sd_commission_balance1=array
									(
											'ufin_main_balance'=>$sp_commissio_balance,
									);
									Userfinance::where('ufin_user_id_pk_fk',$sd_commission_id)->update($sd_commission_balance1);
							
									$d_commission_balance1=array
									(
											'ufin_main_balance'=>$sp_commissio_balance,
									);
									Userfinance::where('ufin_user_id_pk_fk',$d_commission_id)->update($d_commission_balance1);
										
										
									if($getusertype=='FR')
									{
							
										$f_commission_balance1=array
										(
												'ufin_main_balance'=>$fr_commission_balance,
										);
										Userfinance::where('ufin_user_id_pk_fk',$fr_commission_id)->update($f_commission_balance1);
							
									}
									elseif($getusertype=='FRS')
									{
							
										$f_commission_balance1=array
										(
												'ufin_main_balance'=>$fr_commission_balance,
										);
										Userfinance::where('ufin_user_id_pk_fk',$fr_commission_id)->update($f_commission_balance1);
									}
										
							
										
									$rechargeledgerupdate=array
									(
											'rchlgr_lr_id'=>$getlastinseertedcomid,
											'rchlgr_date'=>$currenttime,
											'rchlgr_sa_id'=>$sa_commission_id,
											'rchlgr_sa_commission'=>$admincomissionamount,
											'rchlgr_sp_id'=>$sp_commissio_id,
											'rchlgr_sp_commission'=>$statepartneramount,
											'rchlgr_sd_id'=>$sd_commission_id,
											'rchlgr_sd_commission'=>$statedistributeramount,
											'rchlgr_d_id'=>$d_commission_id,
											'rchlgr_d_commission'=>$distributeramount,
											'rchlgr_fr_id'=>$fr_commission_id,
											'rchlgr_fr_commission'=>$franchiseamount,
							
									);
									$newregcomittion=New Rechargeledger;
									$newregcomittion->create($rechargeledgerupdate);
										
										
								}
								elseif($getusertype=='SFR'||$getusertype=='SFRS')
								{
										
									if($getusertype=='SFR')
									{
										$sfr_commission_id=User::where('UD_USER_ID','=',$currentUserId)->pluck('UD_ID_PK');
										$sfr_commission_balance=Userfinance::where('ufin_user_id_pk_fk','=',$sfr_commission_id)->pluck('ufin_main_balance');
									}
									elseif($getusertype=='SFRS')
									{
										$sfrs_commission_id=User::where('UD_USER_ID','=',$currentUserId)->pluck('UD_ID_PK');
										$sfrs_commission_balance=Userfinance::where('ufin_user_id_pk_fk','=',$sfr_commission_id)->pluck('ufin_main_balance');
										$sfr_commission_id=User::where('UD_USER_ID','=',$sfrs_commission_id)->pluck('UD_PARENT_ID');
										$sfr_commission_balance=Userfinance::where('ufin_user_id_pk_fk','=',$sfr_commission_id)->pluck('ufin_main_balance');
									}
										
										
									$sfr_commission_balance=$sfr_commission_balance+$subfranchiseamount;
									$fr_commission_id=User::where('UD_ID_PK','=',$sfr_commission_id)->pluck('UD_PARENT_ID');
									$fr_commission_balance=Userfinance::where('ufin_user_id_pk_fk','=',$fr_commission_id)->pluck('ufin_main_balance');
									$fr_commission_balance=$fr_commission_balance+$franchiseamount;
									$d_commission_id=User::where('UD_ID_PK','=',$fr_commission_id)->pluck('UD_PARENT_ID');
									$d_commission_balance=Userfinance::where('ufin_user_id_pk_fk','=',$d_commission_id)->pluck('ufin_main_balance');
									$d_commission_balance=$d_commission_balance+$distributeramount;
									$sd_commission_id=User::where('UD_ID_PK','=',$d_commission_id)->pluck('UD_PARENT_ID');
									$sd_commission_balance=Userfinance::where('ufin_user_id_pk_fk','=',$sd_commission_id)->pluck('ufin_main_balance');
									$sd_commission_balance=$sd_commission_balance+$distributeramount;
									$sp_commissio_id=User::where('UD_ID_PK','=',$sa_commission_id)->pluck('UD_PARENT_ID');
									$sp_commissio_balance=Userfinance::where('ufin_user_id_pk_fk','=',$sp_commissio_id)->pluck('ufin_main_balance');
									$sp_commissio_balance=$sp_commissio_balance+$statepartneramount;
									$sa_commission_id=User::where('UD_ID_PK','=',$fr_commission_id)->pluck('UD_PARENT_ID');
									$sa_commission_balance=Userfinance::where('ufin_user_id_pk_fk','=',$sa_commission_id)->pluck('ufin_main_balance');
									$sa_commission_balance=$sa_commission_balance+$admincomissionamount;
										
							
										
									$sabalance1=array
									(
											'ufin_main_balance'=>$sa_commissio_balance,
									);
									Userfinance::where('ufin_user_id_pk_fk',$sa_commission_id)->update($sabalance1);
									$spbalance1=array
									(
											'ufin_main_balance'=>$sp_commissio_balance,
									);
									Userfinance::where('ufin_user_id_pk_fk',$sp_commissio_id)->update($spbalance1);
										
									$sd_commission_balance1=array
									(
											'ufin_main_balance'=>$sp_commissio_balance,
									);
									Userfinance::where('ufin_user_id_pk_fk',$sd_commission_id)->update($sd_commission_balance1);
							
									$d_commission_balance1=array
									(
											'ufin_main_balance'=>$sp_commissio_balance,
									);
									Userfinance::where('ufin_user_id_pk_fk',$d_commission_id)->update($d_commission_balance1);
										
							
									$f_commission_balance1=array
									(
											'ufin_main_balance'=>$fr_commission_balance,
									);
									Userfinance::where('ufin_user_id_pk_fk',$fr_commission_id)->update($f_commission_balance1);
							
										
									$sfr_commission_balance1=array
									(
											'ufin_main_balance'=>$sfr_commission_balance,
									);
									Userfinance::where('ufin_user_id_pk_fk',$fr_commission_id)->update($sfr_commission_balance1);
							
									$getcomission=array
									(
											'rchlgr_lr_id'=>$getlastinseertedcomid,
											'rchlgr_sa_id'=>$sa_commission_id,
											'rchlgr_sa_commission'=>$admincomissionamount,
											'rchlgr_sp_id'=>$sp_commissio_id,
											'rchlgr_sp_commission'=>$statepartneramount,
											'rchlgr_sd_id'=>$sd_commission_id,
											'rchlgr_sd_commission'=>$statedistributeramount,
											'rchlgr_d_id'=>$d_commission_id,
											'rchlgr_d_commission'=>$distributeramount,
											'rchlgr_fr_id'=>$fr_commission_id,
											'rchlgr_fr_commission'=>$franchiseamount,
											'rchlgr_sfr_id'=>$sfr_commission_id,
											'rchlgr_sfr_commission'=>$subfranchiseamount,
							
									);
										
										
									$newregcomittion=New Rechargeledger;
									$newregcomittion->create($getcomission);
										
								}
							
							
							}
								
							return Response::json(array('status' => 'success', 'message' => 'Recharge Done Successfully'));
							
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
		if($getbalance)
		{
		$new_array=Commonmodel::arr($getbalance);
		$finalarray=$new_array['RESPONSERECHARGEINFO']['SOPERATOR'];
			foreach ($finalarray as $new_arrays)
			{
				$status=$new_arrays['DESCRIPTION'];
				if($status='APPAN DUKAN MARKETING')
				{
					$input=array
					(
							'rd_result'=>'Success',
					);
					Recharge::where('rd_trans_id','=',$new_arrays['RESPONSEID'])->update($input);
				}
			}
			
			return Response::json(array('status' => 'Success', 'message' => 'Status Updated Successfully'));
		}
		else
		{
			return Response::json(array('status' => 'failure', 'message' => 'No Transaction History'));
		}
	}
	

}
