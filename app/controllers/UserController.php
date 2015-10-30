<?php

class UserController extends BaseController {

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
	
	public function postCreate()
	{
		$postdata=file_get_contents("php://input");
		if(!empty($postdata))
		{
			
			$userName=Input::get('userName');
			$userEmail=Input::get('userEmail');
			$userMobile=Input::get('userMobile');
			$userAddress1=Input::get('userAddress1');
			$userAddress2=Input::get('userAddress2');
			$userCity=Input::get('userCity');
			$userState=Input::get('userState');
			$userPincode=Input::get('userPincode');
			$userType=Input::get('userType');
			$currentUserId=Input::get('currentUserId');
			$currentUserIdPk=Input::get('currentUserIdPk');
			$parentId=Input::get('parentId');
			$parentIdPk=Input::get('parentIdPk');
			$lockbalance=Input::get('lock_balance');
			$mainbalance=Input::get('main_balance');
			$usercount=Input::get('user_count');
			$userStatus=Input::get('userStatus');
			$products=Input::get('products');
			$createdat=Commonmodel::dateandtime();
			$clientIp=Input::get('clientIp');
		
			
			if($userEmail!=""&&$userMobile!=""&&$userAddress1!=""&&$userAddress2!=""&&$userCity!=""&&$userState!=""&&$userPincode!=""&&$userType!=""&&$currentUserId!=""&&$currentUserIdPk!=""&&$parentIdPk!=""&&$userStatus!=""&&$products!=""&&$userName!='')
			{
				if(empty($lockbalance))
				{
					$lockbalance=0;
				}
				
				if(empty($mainbalance))
				{
					$mainbalance=0;
				}
				
				$checklockbalance=Userfinance::where('ufin_user_id','=',$currentUserId)->pluck('ufin_lock_balance');
				$checkmainbalance=Userfinance::where('ufin_user_id','=',$currentUserId)->pluck('ufin_main_balance');
				if($checkmainbalance<$checklockbalance)
				{
					return Response::json(array('status' => 'failure', 'message' => 'Your lock balance is too low to create this user with mentioned amount'));
				}
				else 
				{
					$checkbalancecreate=$checkmainbalance-$checklockbalance;
					if($checkbalancecreate<$mainbalance)
					{
						return Response::json(array('status' => 'failure', 'message' => 'You have Insufficient Balance '));
					}
					else
					{
							$newbalance=$checkmainbalance-$mainbalance;
							if(!empty($products))
							{
								$emailcheck=User::where('UD_USER_EMAIL',$userEmail)->get();
								if(count($emailcheck)>0)
								{
									return Response::json(array('status' => 'failure', 'message' => 'Email ID Already Exist'));
								}
								else
								{
										$mobilenumcheck=User::where('UD_USER_MOBILE',$userMobile)->get();
										if(count($mobilenumcheck)>0)
										{
											return Response::json(array('status' => 'failure', 'message' => 'Phone Number Already Exist'));
										}
										else
										{
											if($userType!='SP')
											{
												$usercountdetails=count(User::where('UD_USER_TYPE','=',$userType)->get());
												$currentcountcheck=Balancedetection::where('bal_usr_roll','=',$userType)->pluck('user_reg_count');
											}
											else
											{
												$getusercount=Balancedetection::where('bal_usr_roll','=','SP')->pluck('user_reg_count');
												$checkstatedetailsf=count(User::where('UD_USER_TYPE','=',$userType)->where('UD_USER_STATE','=',$userState)->get());
												$stateusercountdetails=count(State::where('adt_state_name','=',$userState)->get());
												if(count($checkstatedetailsf)>$getusercount)
												{
													$usercountdetails=count(User::where('UD_USER_TYPE','=',$userType)->where('UD_USER_STATE','=',$userState)->get());
													$currentcountcheck=State::where('adt_state_name','=',$userState)->pluck('adt_state_count');
													$statpatuserid=State::where('adt_state_name','=',$userState)->pluck('adt_state_code');
												}
												elseif(count($checkstatedetailsf)<=$getusercount)
												{
													$usercountdetails=count(User::where('UD_USER_TYPE','=',$userType)->where('UD_USER_STATE','=',$userState)->get());
													$currentcountcheck=Balancedetection::where('bal_usr_roll','=',$userType)->pluck('user_reg_count');
													$statpatuserid=State::where('adt_state_name','=',$userState)->pluck('adt_state_code');
												}
												else 
												{
													return Response::json(array('status' => 'failure', 'message' => 'You Exceed gthe limit for creating State Partner'));
												}
											}
											
											if($usercountdetails>=$currentcountcheck)
											{
												return Response::json(array('status' => 'failure', 'message' => 'You Reached Limit for creating Sub Role'));
											}
											else
											{
												
												if(empty($usercount))
												{
													
													if($userType=='SAS')
													{
														$usercount=Balancedetection::where('bal_usr_roll','=','SA')->pluck('user_reg_count');
													}
													elseif($userType=='SP')
													{
														$usercount=Balancedetection::where('bal_usr_roll','=','SD')->pluck('user_reg_count');
													}
													elseif($userType=='SPS')
													{
														$usercount=Balancedetection::where('bal_usr_roll','=','SP')->pluck('user_reg_count');
													}
													elseif($userType=='SD')
													{
														$usercount=Balancedetection::where('bal_usr_roll','=','D')->pluck('user_reg_count');
													}
													elseif($userType=='SDS')
													{
														$usercount=Balancedetection::where('bal_usr_roll','=','SD')->pluck('user_reg_count');
													}
													elseif($userType=='D')
													{
														$usercount=Balancedetection::where('bal_usr_roll','=','FR')->pluck('user_reg_count');
													}
													elseif($userType=='DS')
													{
														$usercount=Balancedetection::where('bal_usr_roll','=','D')->pluck('user_reg_count');
													}
													elseif($userType=='FR')
													{
														$usercount=Balancedetection::where('bal_usr_roll','=','SFR')->pluck('user_reg_count');
													}
													elseif($userType=='FRS')
													{
														$usercount=Balancedetection::where('bal_usr_roll','=','FR')->pluck('user_reg_count');
													}
													elseif($userType=='SFR')
													{
														$usercount=Balancedetection::where('bal_usr_roll','=','SFRS')->pluck('user_reg_count');
													}
													else 
													{
													
													}
													
												}
												
												$userkey=md5($userType.$userName.str_random(2));
												$userkeysend=$userType.$userName.str_random(2);
												$input=array
												(
											
													'UD_USER_ID'=>0,
													'UD_USER_KEY'=>$userkey,
													'UD_USER_NAME'=>Input::get('userName'),
													'UD_PARENT_ID'=>Input::get('parentIdPk'),
													'UD_USER_TYPE'=>Input::get('userType'),
													'UD_USER_EMAIL'=>Input::get('userEmail'),
													'UD_USER_MOBILE'=>Input::get('userMobile'),
													'UD_USER_ADDRESS1'=>Input::get('userAddress1'),
													'UD_USER_ADDRESS2'=>Input::get('userAddress2'),
													'UD_USER_CITY'=>Input::get('userCity'),
													'UD_USER_STATE'=>Input::get('userState'),
													'UD_USER_PINCODE'=>Input::get('userPincode'),
													'UD_USER_STATUS'=>Input::get('userStatus'),
													'UD_CREATED_AT'=>$createdat,
													'UD_CREATED_BY'=>Input::get('currentUserId'),
													'UD_USER_LINK'=>Input::get('UD_USER_LINK'),
													'UD_USER_CREATE_COUNT'=>$usercount,
														
												);
												
												$userid=DB::table('adt_user_details')->insertGetId($input);
												
												if($userType!='SP')
												{
													$useridgen=$userType.$userid;
												}
												else
												{
													$useridgen=$statpatuserid.$userid;
												}
												
												$useridupdate=array
												(
														'UD_USER_ID'=>$useridgen,
												);
												
												User::where('UD_ID_PK',$userid)->update($useridupdate);
												$len=count($products);
												
												for($j=0; $j<$len; $j++)
												{
													
													$productinput=array
													(
														'upa_prod_code'=>$products[$j]['prodCode'],
														'upa_ud_user_id'=>$useridgen,
														'upa_access_status'=>$products[$j]['prodStatus'],
														'upa_created_at'=>$createdat,
														'upa_created_by'=>$currentUserId,
													);
													
													
													$newproduct= new Userproductaccess;
													$newproduct->create($productinput);
												}
												
												
												$userfinance=array
												(
											
													'ufin_user_id_pk_fk'=>$userid,
													'ufin_user_id'=>$useridgen,
													'ufin_main_balance'=>$mainbalance,
													'ufin_comm_earned'=>0,
													'ufin_total_credited'=>0,
													'ufin_total_used'=>0,
													'ufin_total_comm'=>0,
													'ufin_fee_perc'=>0,
													'ufin_lock_balance'=>$lockbalance,
													
												);
												
												$objfinance= new Userfinance;
												$objfinance->create($userfinance);
												
												$balanceupdate=array
												(
														'ufin_main_balance'=>$newbalance,
												);
												
												Userfinance::where('ufin_user_id',$parentId)->update($balanceupdate);
												Mail::send('users.mails.welcome', array('userid'=>$useridgen,'password'=>$userkeysend), function($message)
												{
													$message->to(Input::get('userEmail'), Input::get('userName'))->subject('Reg: User ID and Password For you AppanDUkan Account');
												});
												return Response::json(array('status' => 'success', 'message' => 'User have Been Created Successfully'));
												
											}
										}
									
								}
								

							}
							
							else
							{
								return Response::json(array('status' => 'failure', 'message' => 'Atlease Fill Once Product to Register the User'));
							}
					}
			}
			}
			else
			{
				return Response::json(array('status' => 'failure', 'message' => 'Fill All Manditary Fields'));
			}
			
		}
			
	}
	
	
	
	
	public function postForgetpass()
	{
		$postdata=file_get_contents("php://input");
		if(!empty($postdata))
		{
			$email=Input::get('email');
			if($email)
			{
				$checkemail=User::where('UD_USER_EMAIL','=',$email)->get();
				if(count($checkemail)>0)
				{
					foreach ($checkemail as $checkemails){}
					
					
					$usernewkey=md5($checkemails->UD_ID_PK.$checkemails->UD_USER_ID);
					$usernewkeygen=$checkemails->UD_ID_PK.$checkemails->UD_USER_ID;
					$pass=array
					(
						'UD_USER_KEY'=>$usernewkey,	
						'USER_FLAG'=>1,							
					);
					Session::put('usremail',$checkemails->UD_USER_EMAIL);
					Mail::send('users.mails.forget', array('password'=>$usernewkeygen), function($message)
					{
						$message->to(Session::get('usremail'))->subject('Reg: New Password For Appandukan Account');
					});
					Session::forget('usremail');
					User::where('UD_ID_PK',$checkemails->UD_ID_PK)->update($pass);
					return Response::json(array('status' => 'success', 'message' => 'Password Rest is Successfull. You New Password is Send To your Mail'));
					
				}
				else
				{
					return Response::json(array('status' => 'failure', 'message' => 'Email Does Not Exist'));
				}
			}
			else
			{
				return Response::json(array('status' => 'failure', 'message' => 'Email Field is Manditory'));
			}
		}
		else 
		{
			return Response::json(array('status' => 'failure', 'message' => 'There no inout to Access'));
		}
	}
	
	
	public function postLogin()
	{
		$postdata=file_get_contents("php://input");
		if(!empty($postdata))
		{
			$userId=Input::get('userId');
			$userPassword=md5(Input::get('userKey'));
			$logincheck=User::where('UD_USER_ID',$userId)->where('UD_USER_KEY',$userPassword)->get();
			if(count($logincheck)>0)
			{
				$userdetails=User::where('UD_USER_ID',$userId)->where('UD_USER_KEY',$userPassword)->get();
				$parentid=User::where('UD_USER_ID',$userId)->where('UD_USER_KEY',$userPassword)->pluck('UD_PARENT_ID');
				$checkbalance=Userfinance::where('ufin_user_id',$userId)->pluck('ufin_main_balance');
				$parentname=User::where('UD_ID_PK',$parentid)->pluck('UD_USER_ID');
				
				if(!empty($checkbalance)&&$checkbalance=='null')
				{
					$balance=$checkbalance;
				}
				else
				{
					$balance=0;
				}
				
				foreach ($userdetails as $userdetailsnew){}
				if($userdetailsnew->USER_FLAG==1)
				{
					$updateflag=array
					(
						'USER_FLAG'=>0	
					);
					$userflagnew=1;
				}
				else
				{
					$updateflag=array
					(
							'USER_FLAG'=>0
					);
					$userflagnew=0;
				}
				
				User::where('UD_USER_ID','=',$userId)->update($updateflag);
				
				foreach($userdetails as $userdetailss)
				{
				}
				return Response::json($input=array
					(
						'userCity'=> $userdetailss->UD_USER_CITY,
						'userStatus'=> $userdetailss->UD_USER_STATUS	,
						'parentIdPk'=> $userdetailss->UD_PARENT_ID,
						'currentBalance'=> $balance,
						'userName'=> $userdetailss->UD_USER_NAME,
						'message'=> "User Logged-in Successfully",
						'userId'=> $userdetailss->UD_USER_ID,
						'parentId'=> $parentname,
						'userPincode'=> $userdetailss->UD_USER_PINCODE,
						'userState'=> $userdetailss->UD_USER_STATE,
						'userMobile'=> $userdetailss->UD_USER_MOBILE,
						'userIdPk'=> $userdetailss->UD_ID_PK,
						'userEmail'=> $userdetailss->UD_USER_EMAIL,
						'userType'=> $userdetailss->UD_USER_TYPE,
						'userAddress1'=> $userdetailss->UD_USER_ADDRESS1,
						'userAddress2'=> $userdetailss->UD_USER_ADDRESS2,
						'userFlag'=> $userflagnew,
						'status'=> "success"
					));
			}
			else
			{
				return Response::json(array('status' => 'failure', 'message' => 'Invalid Login Credentials'));	
			}
		}
		else
		{
			return Response::json(array('status' => 'failure', 'message' => 'Fill All Manditary Fields'));
		}
	}
	
	
	public function getSubuser($id)
	{
		
		if(!empty($id))
		{
			$getsubusers=User::where('UD_PARENT_ID',$id)->get();
			$parentname=User::where('UD_ID_PK',$id)->pluck('UD_USER_ID');
			if(count($getsubusers)>0)
			{
				foreach($getsubusers as $userdetailss)
				{
					
					$checkbalance=Userfinance::where('ufin_user_id',$userdetailss->UD_USER_ID)->pluck('ufin_main_balance');
					
					if(!empty($checkbalance)&&$checkbalance=='null')
					{
						$balance=$checkbalance;
					}
					else
					{
						$balance=0;
					}
					
					$input[]=array
					(
						'userCity'=> $userdetailss->UD_USER_CITY,
						'userStatus'=> $userdetailss->UD_USER_STATUS	,
						'parentIdPk'=> $userdetailss->UD_PARENT_ID,
						'currentBalance'=> $balance,
						'userName'=> $userdetailss->UD_USER_NAME,
						'userId'=> $userdetailss->UD_USER_ID,
						'parentId'=> $parentname,
						'userPincode'=> $userdetailss->UD_USER_PINCODE,
						'userState'=> $userdetailss->UD_USER_STATE,
						'userMobile'=> $userdetailss->UD_USER_MOBILE,
						'userIdPk'=> $userdetailss->UD_ID_PK,
						'userEmail'=> $userdetailss->UD_USER_EMAIL,
						'userType'=> $userdetailss->UD_USER_TYPE,
						'userAddress1'=> $userdetailss->UD_USER_ADDRESS1,
						'userAddress2'=> $userdetailss->UD_USER_ADDRESS2,
						'status'=> "success"
					);
				}
				return Response::json(array('status' => 'success', 'subUsers' =>$input));	
			}
			else
			{
				return Response::json(array('status' => 'failure', 'message' => 'You Don"t Have any Sub Users'));	
			}
		}
		else
		{
			return Response::json(array('status' => 'failure', 'message' => 'Fill All Manditary Fields'));
		}
			
	}
	
	
	public function getProductsavailable($id)
	{
		
		if(!empty($id))
		{
			$getsubusers=Userproductaccess::where('upa_ud_user_id',$id)->get();
			if(count($getsubusers)>0)
			{
				foreach($getsubusers as $getsubuserss)
				{
					$getproducts[]=Commonmodel::getproducts($getsubuserss->upa_prod_code);
					$getcatcode[]=Commonmodel::getcatcode($getsubuserss->upa_prod_code);	
				
				}
				
				$category=array_unique($getcatcode);
				
				foreach($category as $categorys)
				{
					$getcat[]=Commonmodel::getcategory($categorys);	
				}
				
				$getcat=array_filter($getcat);
				$getcatmultidemisional=Commonmodel::is_multi2($getcat);
				
				
				if(empty($getcatmultidemisional))
				{	

					$final_category=Commonmodel::converarray2to1($getcat);
				}
				elseif(!empty($getcatmultidemisional))
				{
					$final_category=$getcat;
				}
				else
				{
					$final_category=array();
				}
				
				
				$getproductsmultidemsional=Commonmodel::is_multi2($getproducts);
				if(empty($getproductsmultidemsional))
				{	
		
					$final_products=Commonmodel::converarray2to1($getproducts);
				}
				elseif(!empty($getproductsmultidemsional))
				{
					$final_products=$getproducts;
				}
				else
				{
					$final_products=array();
				}
				
				return Response::json(array('categories'=>$final_category, 'message'=> 'Products fetched', 'status'=> 'success','products'=>$final_products,));
			}
			else
			{
				return Response::json(array('status' => 'failure', 'message' => 'You Don"t have Access to Any Products'));
			}
		}
		else
		{
			return Response::json(array('status' => 'failure', 'message' => 'Fill All Manditary Fields'));
		}
			
	}
	
	public function getUser($id)
	{
		
		if(!empty($id))
		{
			$getsubusers=User::where('UD_ID_PK',$id)->get();
			$parentname1=User::where('UD_ID_PK',$id)->pluck('UD_PARENT_ID');
			$parentname=User::where('UD_ID_PK',$parentname1)->pluck('UD_USER_ID');
			if(count($getsubusers)>0)
			{
				foreach($getsubusers as $userdetailss)
				{}
					
					$checkbalance=Userfinance::where('ufin_user_id',$userdetailss->UD_USER_ID)->pluck('ufin_main_balance');
					
					if(!empty($checkbalance)&&$checkbalance=='null')
					{
						$balance=$checkbalance;
					}
					else
					{
						$balance=0;
					}
					
				
				return Response::json($input=array
					(
						'userCity'=> $userdetailss->UD_USER_CITY,
						'userStatus'=> $userdetailss->UD_USER_STATUS	,
						'parentIdPk'=> "".$userdetailss->UD_PARENT_ID."",
						'currentBalance'=> $balance,
						'userName'=> $userdetailss->UD_USER_NAME,
						'message'=> "User details returned successfully",
						'userId'=> $userdetailss->UD_USER_ID,
						'parentId'=> $parentname,
						'userPincode'=> $userdetailss->UD_USER_PINCODE,
						'userState'=> $userdetailss->UD_USER_STATE,
						'userMobile'=> $userdetailss->UD_USER_MOBILE,
						'userIdPk'=> $userdetailss->UD_ID_PK,
						'userEmail'=> $userdetailss->UD_USER_EMAIL,
						'userType'=> $userdetailss->UD_USER_TYPE,
						'userAddress1'=> $userdetailss->UD_USER_ADDRESS1,
						'userAddress2'=> $userdetailss->UD_USER_ADDRESS2,
						'user_count'=> $userdetailss->UD_USER_CREATE_COUNT,
						'status'=> "success"
					));	
			}
			else
			{
				return Response::json(array('status' => 'failure', 'message' => 'You Don"t Have any Sub Users'));	
			}
		}
		else
		{
			return Response::json(array('status' => 'failure', 'message' => 'Fill All Manditary Fields'));
		}
			
	}
	
	
	
	public function getSubuserfinance($id)
	{
		
		if(!empty($id))
		{
			$getsubusers=User::where('UD_PARENT_ID',$id)->get();
			if(count($getsubusers)>0)
			{
				foreach($getsubusers as $userdetailss)
				{
					
					$checkbalance=Userfinance::where('ufin_user_id',$userdetailss->UD_USER_ID)->get();
					if(!empty($checkbalance))
					{
						foreach($checkbalance as $checkbalances)
						{	
							
							$input[]=array
							(
								'totalCredited'=> $checkbalances->ufin_total_credited,
								'totalUsed'=> $checkbalances->ufin_total_used	,
								'currentBalance'=> $checkbalances->ufin_main_balance,
								'userIdPk'=> $checkbalances->ufin_user_id_pk_fk,
								'userId'=> $checkbalances->ufin_user_id,
								'totalCommissionEarned'=> $checkbalances->ufin_comm_earned,
								'lock_balance'=> $checkbalances->ufin_lock_balance,
							);
						}
					}
					
				}
				return Response::json(array('subUsers' =>$input,'message'=>'User Finance returned successfully' ,'status'=>'success'));	
			}
			else
			{
				return Response::json(array('status' => 'failure', 'message' => 'You Don"t Have any Sub Users'));	
			}
		}
		else
		{
			return Response::json(array('status' => 'failure', 'message' => 'Fill All Manditary Fields'));
		}
			
	}
	
	public function postUserbalanceadd()
	{
		$postdata=file_get_contents("php://input");
		if(!empty($postdata))
		{
			$currentdate=Commonmodel::dateandtime();
			$userIdPk=Input::get('userIdPk');
			$userId=Input::get('userId');
			$currentUserId=Input::get('currentUserId');
			$currentUserIdPk=Input::get('currentUserIdPk');
			$parentId=Input::get('parentId');
			$parentIdPk=Input::get('parentIdPk');
			$clientIP=Input::get('clientIP');
			$amount=Input::get('amount');
			$feePerc=Input::get('feePerc');
			
			$balancefromuser=Userfinance::where('ufin_user_id','=',$currentUserId)->get();
			$balancetouser=Userfinance::where('ufin_user_id','=',$userId)->get();
			
			if(count($balancefromuser)>0)
			{
				foreach($balancefromuser as $balancefromusers)
				{
					$formuserbalance=$balancefromusers->ufin_main_balance;
				}
				
				if($formuserbalance>$amount)
				{
					if(count($balancetouser)>0)
					{
						foreach($balancetouser as $balancetouser)
						{
							
							$btcurrentmainbal=$balancetouser->ufin_main_balance;
							$btcurrenttotalcredited=$balancetouser->ufin_total_credited;
						}
						
						if($feePerc>0)
						{
							$newper=$feePerc/100;
							$finalper=$amount*$newper;
							$amount=$amount-$finalper;
						}
						else
						{
							$finalper=$feePerc;
						}
						
						
						$updateuserbalance=array
						(
							'ufin_main_balance'=>$btcurrentmainbal+$amount,
							'ufin_total_credited'=>$btcurrenttotalcredited+$amount,
						);
						//print_r($updateuserbalance);
						Userfinance::where('ufin_user_id','=',$userId)->update($updateuserbalance);
						
						$creditcharge=array
						(
							'cr_user_id'=>$userId,
							'cr_type'=>'CR',
							'cr_open_bal'=>$btcurrentmainbal,
							'cr_amount'=>$amount,
							'cr_fee'=>$finalper,
							'cr_new_bal'=>$btcurrentmainbal+$amount,
							'cr_created_by'=>$currentUserId,
							'cr_created_at'=>$currentdate,
						);
						
						$creditchargeinsert=new Usercreditrecharge;
						$creditchargeinsert->create($creditcharge);
						return Response::json(array('status' => 'success', 'message' => 'User Credit Added Successfully'));	
					}
					else
					{
						return Response::json(array('status' => 'failure', 'message' => 'No User With This ID'));	
					}
				}
				else
				{
					return Response::json(array('status' => 'failure', 'message' => 'You do not have sufficient balance to transfer the amount'));	
				}
			}
			
		}
		else
		{
			return Response::json(array('status' => 'failure', 'message' => 'Fill All Manditary Fields'));	
		}
		
	}
	
	public function getBalancedetection()
	{
		
		
			$balanedetection=Balancedetection::all();
			if(count($balanedetection)>0)
			{
				
				
				
				return Response::json($input=array
						(
								'SA'=> "".$balanedetection[0]->user_reg_count."",
								'SAS'=>"".$balanedetection[1]->user_reg_count."",
								'SP'=>"".$balanedetection[2]->user_reg_count."",
								'SPS'=>"".$balanedetection[3]->user_reg_count."",
								'SD'=>"".$balanedetection[4]->user_reg_count."",
								'SDS'=>"".$balanedetection[5]->user_reg_count."",
								'D'=>"".$balanedetection[6]->user_reg_count."",
								'DS'=>"".$balanedetection[7]->user_reg_count."",
								'FR'=>"".$balanedetection[8]->user_reg_count."",
								'FRS'=>"".$balanedetection[9]->user_reg_count."",
								'SFR'=>"".$balanedetection[10]->user_reg_count."",
								'SFRS'=>"".$balanedetection[11]->user_reg_count."",
						));	
				
			}
			else
			{
				return Response::json(array('status' => 'failure', 'message' => 'There is no records'));
			}
		
		
	}
	
	public function postUpdatelockbalance()
	{
		$postdata=file_get_contents("php://input");
		if(!empty($postdata))
		{
			$currentdate=Commonmodel::dateandtime();
			$userIdPk=Input::get('userIdPk');
			$userId=Input::get('userId');
			$currentUserId=Input::get('currentUserId');
			$currentUserIdPk=Input::get('currentUserIdPk');
			$parentId=Input::get('parentId');
			$parentIdPk=Input::get('parentIdPk');
			$clientIP=Input::get('clientIP');
			$amount=Input::get('amount');
			$feePerc=Input::get('feePerc');
			
			$currentbalance=Userfinance::where('ufin_user_id','=',$userId)->pluck('ufin_main_balance');
			if($currentbalance>$amount)
			{
				$balanceupdt=array
				(
						'ufin_lock_balance'=>$amount,
				);
				Userfinance::where('ufin_user_id','=',$userId)->update($balanceupdt);
				return Response::json(array('status' => 'success', 'message' => 'Your Lock Balance Updated Successfully'));
			}
			else
			{
				return Response::json(array('status' => 'failure', 'message' => 'Yor main balance is too low . Your Lock balance cannot be updated'));
			}
				
		}
		else
		{
			return Response::json(array('status' => 'failure', 'message' => 'Fill All Manditary Fields'));
		}
		
	}
	
	public function getCheckmobilenumber($id)
	{
		if(!empty($id))
		{
			$checkmobile=DB::table('adt_user_details')->where('UD_USER_MOBILE','=',$id)->get();
			if(count($checkmobile)>0)
			{
				return Response::json(array('status' => 'failure', 'message' => 'Mobile Number already Exist'));	
			}
			else
			{
				return Response::json(array('status' => 'success', 'message' => 'Mobile Number is allowed'));	
			}
		}
		else
		{
			return Response::json(array('status' => 'failure', 'message' => 'Fill All Manditary Fields'));	
		}
		
	}
	
	
	public function getCheckuserid($id)
	{
		if(!empty($id))
		{
			$checkmobile=DB::table('adt_user_details')->where('UD_USER_ID','=',$id)->get();
			if(count($checkmobile)>0)
			{
				return Response::json(array('status' => 'failure', 'message' => 'User ID already Exist'));	
			}
			else
			{
				return Response::json(array('status' => 'success', 'message' => 'User ID is allowed'));	
			}
		}
		else
		{
			return Response::json(array('status' => 'failure', 'message' => 'Fill All Manditary Fields'));	
		}
		
	}
	
	public function postCheckuserid()
	{
		$postdata=file_get_contents("php://input");
		if(!empty($postdata))
		{
			$email=Input::get('userEmail');
			$checkmobile=DB::table('adt_user_details')->where('UD_USER_EMAIL','=',$email)->get();
			if(count($checkmobile)>0)
			{
				return Response::json(array('status' => 'failure', 'message' => 'Email already Exist'));	
			}
			else
			{
				return Response::json(array('status' => 'success', 'message' => 'Email is allowed'));	
			}
		}
		else
		{
			return Response::json(array('status' => 'failure', 'message' => 'Fill All Manditary Fields'));	
		}
		
	}
	
	public function getDeactivateuser($id)
	{
		if(!empty($id))
		{
			$finduser=User::find($id);
			if($finduser)
			{
				$updatestatus=array
				(
					'UD_USER_STATUS'=>'DA',			
				);
				User::where('UD_ID_PK','=',$id)->update($updatestatus);
				return Response::json(array('status' => 'success', 'message' => 'User Deactivated Successfully'));
			}
			else
			{
				return Response::json(array('status' => 'failure', 'message' => 'ID Does Not Exist'));
			}
		}
		else
		{
			return Response::json(array('status' => 'failure', 'message' => 'Fill All Manditary Fields'));
		}
	}
	
	public function postUpdateuser()
	{
		$postdata=file_get_contents("php://input");
		if(!empty($postdata))
		{
			$userName=Input::get('userName');
			$userEmail=Input::get('userEmail');
			$userMobile=Input::get('userMobile');
			$currentUserId=Input::get('currentUserId');
			$currentUserIdPk=Input::get('currentUserIdPk');
			$parentId=Input::get('parentId');
			$parentIdPk=Input::get('parentIdPk');
			$userIdPk=Input::get('userIdPk');
			$userAddress1=Input::get('userAddress1');
			$userAddress2=Input::get('userAddress2');
			$userCity=Input::get('userCity');
			$userState=Input::get('userState');
			$userPincode=Input::get('userPincode');
			$userStatus=Input::get('userStatus');
			$userType=Input::get('userType');
			$products=Input::get('products');
			$userKey=Input::get('userKey');
			$usercount=Input::get('user_count');
			$editeddate=Commonmodel::dateandtime();
			if($userName!=""&&$userEmail!=""&&$userMobile!=""&&$currentUserId!=""&&$currentUserIdPk!=""&&$parentId!=""&&$parentIdPk!=""&&$userIdPk!=""&&$userAddress1!=""&&$userAddress2!=""&&$userCity!=""&&$userState!=""&&$userState!=""&&$userPincode!=""&&$userStatus!=""&&$userType!='')
			{
				$checkueremail=User::where('UD_ID_PK','!=',$userIdPk)->where('UD_USER_EMAIL','=',$userEmail)->get();
				if(count($checkueremail)>0)
				{
					return Response::json(array('status' => 'failure', 'message' => 'Email ID Already Exist'));	
				}
				else
				{
					$checkmobile=User::where('UD_ID_PK','!=',$userIdPk)->where('UD_USER_MOBILE','=',$userMobile)->get();
					if(count($checkmobile)>0)
					{
						return Response::json(array('status' => 'failure', 'message' => 'Mobile Number Already Exist'));	
					}
					else
					{
						$checkuser=User::where('UD_ID_PK','!=',$userIdPk)->get();
						if(count($checkuser)==0)
						{
							return Response::json(array('status' => 'failure', 'message' => 'User ID Already Exist'));	
						}
						else
						{
						
							if($userKey)
							{
										if(empty($usercount))
										{
											$usercount=0;
										}
										
										$input=array(
									
											
											'UD_USER_NAME'=>Input::get('userName'),
											'UD_USER_KEY'=>md5(Input::get('userKey')),
											'UD_PARENT_ID'=>Input::get('parentIdPk'),
											'UD_USER_TYPE'=>Input::get('userType'),
											'UD_USER_EMAIL'=>Input::get('userEmail'),
											'UD_USER_MOBILE'=>Input::get('userMobile'),
											'UD_USER_ADDRESS1'=>Input::get('userAddress1'),
											'UD_USER_ADDRESS2'=>Input::get('userAddress2'),
											'UD_USER_CITY'=>Input::get('userCity'),
											'UD_USER_STATE'=>Input::get('userState'),
											'UD_USER_PINCODE'=>Input::get('userPincode'),
											'UD_USER_STATUS'=>Input::get('userStatus'),
											'UD_EDITED_AT'=>$editeddate,
											'UD_EDITED_BY'=>Input::get('currentUserId'),
											'UD_USER_CREATE_COUNT'=>$usercount,
										);
										
										User::where('UD_ID_PK','=',$userIdPk)->update($input);
										$getnewuserid=User::where('UD_ID_PK','=',$userIdPk)->pluck('UD_USER_ID');
																	
										if(count($products)>0)
										{
											Userproductaccess::where('UD_ID_PK', '=', $userIdPk)->delete();
											$len=count($products);
											for($j=0; $j<$len; $j++)
											{
												
												$productinput=array
												(
													'upa_prod_code'=>$products[$j]['prodCode'],
													'upa_ud_user_id'=>$products[$j]['prodStatus'],
													'upa_access_status'=>$getnewuserid,
													'upa_created_at'=>$editeddate,
													'upa_created_by'=>$currentUserId,
												);
												$newproduct= new Userproductaccess;
												$newproduct->create($productinput);
											}
										}
									
									return Response::json(array('status' => 'success', 'message' => 'User updated Successfully'));	
									
							}
							else
							{
								
										$input=array(
									
											
											'UD_USER_NAME'=>Input::get('userName'),
											'UD_PARENT_ID'=>Input::get('parentIdPk'),
											'UD_USER_TYPE'=>Input::get('userType'),
											'UD_USER_EMAIL'=>Input::get('userEmail'),
											'UD_USER_MOBILE'=>Input::get('userMobile'),
											'UD_USER_ADDRESS1'=>Input::get('userAddress1'),
											'UD_USER_ADDRESS2'=>Input::get('userAddress2'),
											'UD_USER_CITY'=>Input::get('userCity'),
											'UD_USER_STATE'=>Input::get('userState'),
											'UD_USER_PINCODE'=>Input::get('userPincode'),
											'UD_USER_STATUS'=>Input::get('userStatus'),
											'UD_EDITED_AT'=>$editeddate,
											'UD_EDITED_BY'=>Input::get('currentUserId'),
											'UD_USER_CREATE_COUNT'=>$usercount,
										);
										
										
										User::where('UD_ID_PK','=',$userIdPk)->update($input);
										$getnewuserid=User::where('UD_ID_PK','=',$userIdPk)->pluck('UD_USER_ID');
										
										if(count($products)>0)
										{
											Userproductaccess::where('upa_ud_user_id', '=', $getnewuserid)->delete();
											$len=count($products);
											for($j=0; $j<$len; $j++)
											{
												
												$productinput=array
												(
													'upa_prod_code'=>$products[$j]['prodCode'],
													'upa_ud_user_id'=>$getnewuserid,
													'upa_access_status'=>$products[$j]['prodStatus'],
													'upa_created_at'=>$editeddate,
													'upa_created_by'=>$currentUserId,
												);
												
												$newproduct= new Userproductaccess;
												$newproduct->create($productinput);
											}
										}
										
										return Response::json(array('status' => 'success', 'message' => 'User updated Successfully'));	
									
							}
						}
					}
				}
			
			}
			else
			{
				return Response::json(array('status' => 'failure', 'message' => 'Fill All Manditary Fields'));	
			}

		}
		else
		{
			return Response::json(array('status' => 'failure', 'message' => 'Fill All Manditary Fields'));	
		}
		
	}
	
	public function getState()
	{
		$staesp=State::all();
		if(count($staesp)>0)
		{
			return Response::json($staesp);
		}
		else
		{
			return Response::json(array('status' => 'failure', 'message' => 'There is No state in State Table'));
		}
		
	}
	
	public function postStateupdate()
	{

		$postdata=file_get_contents("php://input");
		if(!empty($postdata))
		{
			$userid=Input::get('userId');
			$userCount=Input::get('userCount');
			$updatecount=array
			(
					'adt_state_count'=>$userCount,
			);
			
			User::where('adt_state_id','=',$userid)->update($updatecount);
			return Response::json(array('status' => 'success', 'message' => 'User Count Updated Successfully'));
		}
		else
		{
			return Response::json(array('status' => 'failure', 'message' => 'There is No state in State Table'));
		}
	}
	
	
	
	
	
	
}
