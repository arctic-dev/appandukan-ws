<?php

class PanoffilineController extends BaseController {

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
				$clientIp=Input::get('clientIp');
				$limit=Input::get('limit');
				$usercount=User::select('UD_USER_ID','UD_USER_TYPE')->where('UD_USER_ID','=',$currentUserId)->get();
				if(count($usercount)>0)
				{
					if($usercount[0]->UD_USER_TYPE=='SA'||$usercount[0]->UD_USER_TYPE=='SAS')
					{
						$panc=Panoffiline::all();
						if(count($panc)>0)
						{
								foreach($panc as $pancc)
								{
									$getdetails[]=array
									(
									
											"idPk"=> $pancc->pan_id_pk,
											"couponNo"=>$pancc->pan_coupon_no,
											"title"=> $pancc->pan_title,
											"firstName"=> $pancc->pan_first_name,
											"middleName"=> $pancc->pan_middle_name,
											"lastName"=> $pancc->pan_last_name,
											"nameAbbrv"=> $pancc->pan_name_abbrv,
											"dob"=> $pancc->pan_dob,
											"fatherFname"=> $pancc->pan_father_fname,
											"fatherMname"=> $pancc->pan_father_mname,
											"fatherLname"=> $pancc->pan_father_lname,
											"countryCode"=> "".$pancc->pan_country_code."",
											"areaCode"=> "".$pancc->pan_area_code."",
											"contactNo"=> $pancc->pan_contact_no,
											"emailId"=> $pancc->pan_email_id,
											"createdAt"=> $pancc->pan_created_at,
											"createdBy"=> $pancc->pan_created_by,
											"refundStatus"=> $pancc->pan_refund_status,
											"refundAt"=> $pancc->pan_refund_at,
											"refundBy"=> $pancc->pan_refund_by,
									);
								}
								return Response::json( $getdetails);
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

	public function postCreate()
	{
			$postdata=file_get_contents("php://input");
			if(!empty($postdata))
			{
				$contactNo=Input::get('contactNo');
				$currentUserId=Input::get('currentUserId');
				$currenttime=Commonmodel::dateandtime();
				
			}
			
			$check=Panoffiline::where('pan_contact_no','=',$contactNo)->get();
			if(count($check)>0)
			{
				return Response::json(array('status' => 'failure', 'message' => 'You Have ALready Submitted your Regisstration form for PAN Card'));
			}
			else
			{
				$checktype=User::where('UD_USER_ID','=',$currentUserId)->pluck('UD_USER_TYPE');
				if($checktype)
				{
					if($checktype!='FRS')
					{
						$checkbalance=DB::table('adt_user_finance')->where('ufin_user_id','=',$currentUserId)->where('ufin_main_balance','<=','106')->get();
						$getbalance=Userfinance::where('ufin_user_id','=',$currentUserId)->pluck('ufin_main_balance');
					}
					else
					{
						$getnewuserdetails=User::where('UD_USER_ID','=',$currentUserId)->where('UD_USER_TYPE','=','FRS')->pluck('UD_PARENT_ID');
						$checkbalance=DB::table('adt_user_finance')->where('ufin_user_id','=',$getnewuserdetails)->where('ufin_main_balance','<=','106')->get();
						$getbalance=Userfinance::where('ufin_user_id','=',$getnewuserdetails)->pluck('ufin_main_balance');
					}
					$couponcheck=Coupons::orderBy('pc_coupon_no','ASC')->first()->pluck('pc_coupon_no');
					if($couponcheck)
					{
						if($checkbalance||empty($getbalance)||!empty($checkbalance)||$getbalance<=0)
						{
							
							return Response::json(array('status' => 'failure', 'message' => 'You Do not Have Sufficient Balance'));
						}
						else
						{
							$input=array
							(
								'pan_coupon_no'=>$couponcheck,
								'pan_title'=>Input::get('title'),
								'pan_first_name'=>Input::get('firstName'),
								'pan_middle_name'=>Input::get('lastName'),
								'pan_last_name'=>Input::get('middleName'),
								'pan_name_abbrv'=>Input::get('nameAbbrv'),
								'pan_dob'=>Input::get('dob'),
								'pan_father_fname'=>Input::get('fatherFname'),
								'pan_father_mname'=>Input::get('fatherMname'),
								'pan_father_lname'=>Input::get('fatherLname'),
								'pan_country_code'=>Input::get('countryCode'),
								'pan_area_code'=>Input::get('areaCode'),
								'pan_contact_no'=>Input::get('contactNo'),
								'pan_email_id'=>Input::get('emailId'),
								'pan_created_at'=>$currenttime,
								'pan_created_by'=>Input::get('currentUserId'),
							);

							$getbalance=$getbalance-106;
							$product=new Panoffiline;
							$product->create($input);
							$balance=array
										(
											'ufin_main_balance'=>$getbalance,
										);
							$procode=Products::where('prod_short_name','=','PANM')->pluck('prod_code');
							$panledger=array
							(
									'lr_date'=>$currenttime,
									'lr_trans_type'=>'DB',
									'lr_comment'=>'Pan',
									'lr_debit_amount'=>'106',
									'lr_post_balance'=>$getbalance,
									'lr_created_by'=>Input::get('currentUserId'),
									'lr_prod_code'=>$procode,
							);
							
							$panlegcreta = new Ledgerreport;
							$panlegcreta->create($panledger);
							Coupons::where('pc_coupon_no', '=',$couponcheck)->delete();
							Userfinance::where('ufin_user_id',$currentUserId)->update($balance);
							return Response::json(array('status' => 'fuccess', 'message' => 'Your Document for PAN card have Been Submitted Successfully'));
						}
						
						
					}
					else
					{
						return Response::json(array('status' => 'failure', 'message' => 'You Don"t have any coupon to register pancard'));
					}
				}
				else
				{
					return Response::json(array('status' => 'failure', 'message' => 'You Don"t have any coupon to register pancard'));
				}
			
			}

	}
	
	public function getForm($id,$id2)
	{
			//$postdata=file_get_contents("php://input");
			if(!empty($id))
			{
				$getlegersub='';
				$getdetails='';
				$userid=$id;
				$userIdPk=$id2;
				$check=Panoffiline::where('pan_created_by','=',$userid)->get();			
				$getsubuserid=User::select('UD_USER_ID')->where('UD_USER_TYPE','=','FRS')->where('UD_PARENT_ID','=',$userIdPk)->get();
				if(count($getsubuserid)>0)
				{
					foreach($getsubuserid as $getsubuserids)
					{
						$getsubuseridss[]=$getsubuserids['UD_USER_ID'];
					}
					
					
					if(count($getsubuseridss)==1)
					{
						
						$getlegersub=Panoffiline::where('pan_created_by',$getsubuseridss)->get();
					}
					elseif(count($getsubuseridss)>1)
					{
						
						$getlegersub=Panoffiline::whereBetween('pan_created_by',$getsubuseridss)->get();
						
					}
					
						
				}
			
				if(count($check)>0||$getsubuseridss!='')
				{
					if(count($check)>0)
					{
						for($i=0; $i<count($check); $i++)
						{
							$getdetails[]=array
							(
							
								"idPk"=> $check[$i]->pan_id_pk,
								"couponNo"=>$check[$i]->pan_coupon_no,
								"title"=> $check[$i]->pan_title,
								"firstName"=> $check[$i]->pan_first_name,
								"middleName"=> $check[$i]->pan_middle_name,
								"lastName"=> $check[$i]->pan_last_name,
								"nameAbbrv"=> $check[$i]->pan_name_abbrv,
								"dob"=> $check[$i]->pan_dob,
								"fatherFname"=> $check[$i]->pan_father_fname,
								"fatherMname"=> $check[$i]->pan_father_mname,
								"fatherLname"=> $check[$i]->pan_father_lname,
								"countryCode"=> "".$check[$i]->pan_country_code."",
								"areaCode"=> "".$check[$i]->pan_area_code."",
								"contactNo"=> $check[$i]->pan_contact_no,
								"emailId"=> $check[$i]->pan_email_id,
								"createdAt"=> $check[$i]->pan_created_at,
								"createdBy"=> $check[$i]->pan_created_by,
								"refundStatus"=> $check[$i]->pan_refund_status,
								"refundAt"=> $check[$i]->pan_refund_at,
								"refundBy"=> $check[$i]->pan_refund_by,
							);
						}
					}
					
					if(count($getlegersub)>0)
					{
						for($i=0; $i<count($getlegersub); $i++)
						{
							
						$getdetailssubuser[]=array
						(
						
								"idPk"=> $getlegersub[$i]->pan_id_pk,
								"couponNo"=>$getlegersub[$i]->pan_coupon_no,
								"title"=> $getlegersub[$i]->pan_title,
								"firstName"=> $getlegersub[$i]->pan_first_name,
								"middleName"=> $getlegersub[$i]->pan_middle_name,
								"lastName"=> $getlegersub[$i]->pan_last_name,
								"nameAbbrv"=> $getlegersub[$i]->pan_name_abbrv,
								"dob"=> $getlegersub[$i]->pan_dob,
								"fatherFname"=> $getlegersub[$i]->pan_father_fname,
								"fatherMname"=> $getlegersub[$i]->pan_father_mname,
								"fatherLname"=> $getlegersub[$i]->pan_father_lname,
										"countryCode"=> "".$getlegersub[$i]->pan_country_code."",
												"areaCode"=> "".$getlegersub[$i]->pan_area_code."",
												"contactNo"=> $getlegersub[$i]->pan_contact_no,
												"emailId"=> $getlegersub[$i]->pan_email_id,
												"createdAt"=> $getlegersub[$i]->pan_created_at,
												"createdBy"=> $getlegersub[$i]->pan_created_by,
												"refundStatus"=> $getlegersub[$i]->pan_refund_status,
												"refundAt"=> $getlegersub[$i]->pan_refund_at,
												"refundBy"=> $getlegersub[$i]->pan_refund_by,
										);
						}
					}
					
					return Response::json(array('status' => 'success', 'mainuser' => $getdetails, 'subuser' => $getdetailssubuser, 'message' => 'User PAN forms returned successfully'));
				}
				else
				{
					return Response::json(array('status' => 'failure', 'message' => 'Till Now there is No Pan Card Registred With you'));
				}
			}
			else
			{
				return Response::json(array('status' => 'failure', 'message' => 'You Don"t have any data to Access the pancard'));
			}

	}
	
	public function postImportcoupon()
	{
		$postdata=file_get_contents("php://input");
			if(!empty($postdata))
			{
				$coupons=Input::get('coupon');
				if($coupons)
				{
					foreach($coupons as $coupon)
					{
						
						$checkcouponexist=DB::table('adt_pan_coupons')->where('pc_coupon_no','=',$coupon['pc_coupon_no'])->get();
						if(!count($checkcouponexist)>0)
						{
							$insertcoupon= new Coupons;
							$input=array
							(
								'pc_coupon_no'=>$coupon['pc_coupon_no']
							);
							$insertcoupon->create($input);
							
						}
						
					}
					
					return Response::json(array('status' => 'success', 'message' => 'Coupon Imported Successfully'));
					
				}
			}
			else
			{
				return Response::json(array('status' => 'failure', 'message' => 'You Don"t have any data to Import in Coupon'));
			}
	}
	public function postRefund()
	{
		$postdata=Input::all();
		$panIDPK = Input::get('pan_id_pk');
		$currenttime=Commonmodel::dateandtime();
		if(!$panIDPK)
		{
			return Response::json(array('status'=>"failed",'message'=>'Please send a PAN ID'));
		}
		else
		{
			$findrefuncid=Panoffiline::find($panIDPK);
			if($findrefuncid)
			{
				$getcurrentuser=Panoffiline::where('pan_id_pk','=',$panIDPK)->pluck('pan_created_by');
				$response=Panoffiline::where('pan_id_pk',$panIDPK)->get();
				if($response)
				{
					foreach($response as $res)
					{
						$userfinID = $res->pan_created_by;
						$panCoupenNo = $res->pan_coupon_no;
						$mainBalance = Userfinance::where('ufin_user_id',$userfinID)->pluck('ufin_main_balance');
						$panTotBal = $mainBalance + 106;
						$balanceDebit = array(
								'ufin_main_balance' => $panTotBal,
						);
						if($panCoupenNo != '0')
						{
							Userfinance::where('ufin_user_id','=',$userfinID)->update($balanceDebit);
							$coupenReset = array(
									'pan_coupon_no' => '',
							);
							Panoffiline::where('pan_id_pk','=',$panIDPK)->update($coupenReset);
							$panCoupenUpdate = array(
									'pc_coupon_no' => $panCoupenNo,
							);
							
							$procode=Products::where('prod_short_name','=','PANM')->pluck('prod_code');
							$panledger=array
							(
									'lr_date'=>$currenttime,
									'lr_trans_type'=>'CR',
									'lr_comment'=>'Pan Refund',
									'lr_credit_amount'=>'106',
									'lr_post_balance'=>$panTotBal,
									'lr_created_by'=>$getcurrentuser,
									'lr_prod_code'=>$procode,
							);
								
							$panlegcreta = new Ledgerreport;
							$panlegcreta->create($panledger);
							
							$coupen = new Coupons;
							$coupen->create($panCoupenUpdate);
							return Response::json(array('status'=>"success",'message'=>'Amount debited successfully'));
						}
						else
						{
							return Response::json(array('status'=>"failure",'message'=>'PAN Card has been already applied for refund'));
						}
					}
				}
			}
			else
			{
				return Response::json(array('status'=>"failure",'message'=>'No User found for this ID'));
			}
		}
		
		
	}
	
}