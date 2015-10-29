<?php

class IcashController extends BaseController {

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
		$data=Icash::orderBy('icc_created_at','DESC')->get();
		if(count($data)>0)
		{
			$response=array(
				"status"=>"success",
				"cardhistory"=>$data
				);
		}
		else
		{
			$response=array(
				"status"=>"failure",
				"message"=>"no data found"
				);
		}
		return Response::json($response);
	}
	public function getTopuphistory()
	{
		$data=Icashtopup::orderBy('icc_created_at','DESC')->get();
		if(count($data)>0)
		{
			$response=array(
				"status"=>"success",
				"topuphistory"=>$data
				);
		}
		else
		{
			$response=array(
				"status"=>"failure",
				"message"=>"no data found"
				);
		}
		return Response::json($response);
	}
	public function getTransferhistory()
	{
		$data=Icashtransaction::orderBy('icc_createdat','DESC')->get();
		if(count($data)>0)
		{
			$response=array(
				"status"=>"success",
				"topuphistory"=>$data
				);
		}
		else
		{
			$response=array(
				"status"=>"failure",
				"message"=>"no data found"
				);
		}
		return Response::json($response);
	}
	public function postCreate()
	{
		$postdata=Input::all();
		if(Input::get('kyc')=="1")
		{
			$userexist=DB::table('adt_icashcard_user')->where('icc_usermobile',Input::get('icc_usermobile'))->get();
		if(count($userexist)>0)
		{
			$response=array(
				'status' =>"1" , 
				'errors'=>"user registered already",
				'tranid'=>$userexist[0]->icc_tran_id,
				);
			
		}
		else
		{
			$retailer=User::where('UD_USER_MOBILE',Input::get('icc_usermobile'))->get();
		if(count($retailer)>0)
		{
			$iccuser_id=Input::get('usercreatedby');
		}
		else
		{
			$iccuser_id="";
		}
			$input=array(
				'icc_kyc'=>Input::get('kyc'),
				'icc_username'=>Input::get('username'),
		
		
		'icc_usermname'=>Input::get('usermiddlename'),
		'icc_userlname'=>Input::get('userlastname'),
		'icc_userId'=>$iccuser_id,
		'icc_usermothername'=>Input::get('usermothermaidename'),
		'icc_user_dob'=>Input::get('userdateofbirth'),
		'icc_useremail'=>Input::get('useremail'),
		'icc_usermobile'=>Input::get('icc_usermobile'),
		'icc_usercity'=>Input::get('usercity'),
		'icc_userstate'=>Input::get('userstate'),
		'icc_useraddress'=>Input::get('useraddress'),
		'icc_userpincode'=>Input::get('userpincode'),
		'icc_useridprooftype'=>Input::get('useridprooftype'),
		'icc_useridproof'=>Input::get('useridproof'),
		'icc_useridproofurl'=>Input::get('useridproofurl'),
		'icc_useraddrprooftype'=>Input::get('useraddrprooftype'),
		'icc_userproof'=>Input::get('useraddrproof'),
		'icc_userproofurl'=>Input::get('useraddrproofurl'),
		'icc_created_by'=>Input::get('usercreatedby'),
		'icc_created_at'=>date('Y-m-d H:i:sa'),
		);
			$data=new Icash;
			$data->create($input);
			$response=array(
				'status' =>"0" , 
				'message'=>"user registered successfully",
				);

		}

		return Response::json($response);
	}
	else
	{
		$rules=array(
			"username"=>"required",
			"usermiddlename"=>"required",     
			"userlastname"=>"required",
			"usermothermaidename"=>"required",
			"userdateofbirth"=>"required",
			"useremail"=>"required",
			"usermobile"=>"required",
			"userstate"=>"required",
			"usercity"=>"required",
			"useraddress"=>"required",
			"userpincode"=>"required",
			"useridprooftype"=>"required",
			"useridproof"=>"required",
			"useridproofurl"=>"required",
			"useraddrprooftype"=>"required",
			"useraddrproof"=>"required",
			"useraddrproofurl"=>"required",
			);
		$validator=Validator::make($postdata,$rules);
			if($validator->fails())
			{
				$response=array(
					"status"=>"failure",
					"error"=>$validator->errors(),
					);
			}	
			else
			{
				$retailer=User::where('UD_USER_MOBILE',Input::get('icc_usermobile'))->get();
		if(count($retailer)>0)
		{
			$iccuser_id=Input::get('usercreatedby');
		}
		else
		{
			$iccuser_id="";
		}
			$input=array(
				'icc_kyc'=>Input::get('kyc'),
				'icc_username'=>Input::get('username'),
		'icc_usermname'=>Input::get('usermiddlename'),
		'icc_userlname'=>Input::get('userlastname'),
		'icc_userId'=>$iccuser_id,
		'icc_usermothername'=>Input::get('usermothermaidename'),
		'icc_user_dob'=>Input::get('userdateofbirth'),
		'icc_useremail'=>Input::get('useremail'),
		'icc_usermobile'=>Input::get('icc_usermobile'),
		'icc_usercity'=>Input::get('usercity'),
		'icc_userstate'=>Input::get('userstate'),
		'icc_useraddress'=>Input::get('useraddress'),
		'icc_userpincode'=>Input::get('userpincode'),
		'icc_useridprooftype'=>Input::get('useridprooftype'),
		'icc_useridproof'=>Input::get('useridproof'),
		'icc_useridproofurl'=>Input::get('useridproofurl'),
		'icc_useraddrprooftype'=>Input::get('useraddrprooftype'),
		'icc_userproof'=>Input::get('useraddrproof'),
		'icc_userproofurl'=>Input::get('useraddrproofurl'),
		'icc_created_by'=>Input::get('usercreatedby'),
		'icc_created_at'=>date('Y-m-d H:i:sa'),
		);
			$data=new Icash;
			$data->create($input);
			$response=array(
				'status' =>"0" , 
				'message'=>"user registered successfully",
				);

		}
	}

		return Response::json($response);
	
	}
	

public function postUpdateuser()

	{
	$userexist=DB::table('adt_icashcard_user')->where('icc_usermobile',Input::get('usermobile'))->get();
	if(!empty($userexist))
	{
	$update=array(
	'icc_tran_id'=>Input::get('tranid'),
		'icc_usercardno'=>Input::get('cardno'),
		'icc_userbalance'=>Input::get('balance'),
		 );	
	$userupdate=DB::table('adt_icashcard_user')->where('icc_usermobile',Input::get('usermobile'))->update($update);
	
$response=array(
		"status"=>"0",
		"message"=>"user updated Successfully");
	}
	else
	{
		$response=array(
		"status"=>"1",
		"message"=>"user not found");
	
	}
	return Response::json($response);

			
	}	

	public function postLoginupdate()
	{
		$userexist=Icash::where('icc_usermobile',Input::get('mobilenumber'))->get();
			if(!empty($userexist))
			{
			$input=array(
			'icc_mmid' =>Input::get('mmid') , 
			'icc_userbalance'=>Input::get('balance'),
			'icc_tranlimit' =>Input::get('transactionlimit') , 
			'icc_consumed' =>Input::get('consumedlimit') , 
			'icc_remaining' =>Input::get('remaininglimit') , 
			'icc_userkycstatus' =>Input::get('kycstatus') , 
			'icc_securitykey'=>Input::get('security_key')
			);	

			$userupdate=Icash::where('icc_usermobile',Input::get('mobilenumber'))->update($input);
		
			}
			$userbalance=Icash::where('icc_usermobile',Input::get('mobilenumber'))->get();
			foreach ($userbalance as $user) {
				
			}
			if($user->icc_userbalance>0.00)
			{
				$response=array(
					'cardno'=>$user->icc_usercardno,
					'security_key'=>$user->icc_securitykey,
					'status'=>"success"
					);
			}
			else
			{
				$retailer=Icash::where('icc_userId',Input::get('request'))->get();
				foreach ($retailer as $ret) {
					# code...
				}

				$response=array(
					'cardno'=>$ret->icc_usercardno,
					'security_key'=>$ret->icc_securitykey,
					'status'=>"success"
					);
			}
			return Response::json($response);



	}
	public function postAddbene()
	{
		//print_r(Input::all()); exit;
		/*$postdata=Input::all();
		$rules=array(
			"bename"=>"required",
			"benmobile"=>"required",
			"cardno"=>"required",
			);
			*/

			$input=array(
		"icc_cardno"=>Input::get('cardno'),
"icc_created_by"=>Input::get('beneid'),
"icc_bene_id"=>Input::get('beneid'),
"icc_bentranid"=>Input::get('tranid'),
"icc_benname"=>Input::get('bename'),
"icc_benmmid"=>(Input::has('benmmid'))?Input::get('benmmid'):null,
"icc_mobilenumber"=>(Input::has('benmobile'))?Input::get('benmobile'):null,
"icc_benbenkname"=>(Input::has('benbankname'))?Input::get('benbankname'):null,
"icc_benbranchname"=>(Input::has('benbranchname'))?Input::get('benbranchname'):null,
"icc_bencity"=>(Input::has('bencity'))?Input::get('bencity'):null,
"icc_benstate"=>(Input::has('benstate'))?Input::get('benstate'):null,
"icc_benifsc"=>(Input::has('benifsc'))?Input::get('benifsc'):null,
"icc_benaccno"=>(Input::has('benaccno'))?Input::get('benaccno'):null,
"icc_createdby"=>Input::get('created_by'),
"icc_create_at"=>date("Y-m-d h:i:sa"),
"icc_benstatus"=>0
	);
			$bene=new Icashben;
			if($bene->create($input))
			{
				$beneoutput=Icashben::all();
				}
				else
				{
					echo "error";
				}
			
				return Response::json($beneoutput);
	}

	public function postRemovebene()
	{
		$input=Icashben::where('icc_bene_id',Input::get('beneid'))
		->where('icc_cardno',Input::get('cardno'))->get();
		if($input)
		{
			$array=array
			(
				"icc_ben_status"=>1
				);
			$input=Icashben::where('icc_bene_id',Input::get('beneid'))
		->where('icc_cardno',Input::get('cardno'))->update($array);
		}
	}
	public function postUpdateben()
	{
		if(Input::get('flag')==2)
		{

			$input=array(
		"icc_cardno"=>Input::get('cardno'),
"icc_benname"=>Input::get('bename'),
"icc_mobilenumber"=>(Input::has('benmobile'))?Input::get('benmobile'):null,
"icc_benbenkname"=>(Input::has('benbankname'))?Input::get('benbankname'):null,
"icc_benbranchname"=>(Input::has('benbranchname'))?Input::get('benbranchname'):null,
"icc_bencity"=>(Input::has('bencity'))?Input::get('bencity'):null,
"icc_benstate"=>(Input::has('benstate'))?Input::get('benstate'):null,
"icc_benifsc"=>(Input::has('benifsc'))?Input::get('benifsc'):null,
"icc_benaccno"=>(Input::has('benaccno'))?Input::get('benaccno'):null,
"icc_create_at"=>date("Y-m-d h:i:sa"),
	);
			$bene=Icashben::where('icc_bene_id',Input::get('benid'))->get();
			if($bene)
			{
				$beneoutput=Icashben::where('icc_bene_id',Input::get('benid'))->update($input);
				}
				else
				{
					echo "error";
				}
			
				
	}
	else
	{
		$input=array(
		"icc_cardno"=>Input::get('cardno'),
"icc_benname"=>Input::get('bename'),
"icc_benmmid"=>Input::get('benmmid'),
"icc_mobilenumber"=>(Input::has('benmobile'))?Input::get('benmobile'):null,
	);
			$bene=Icashben::where('icc_bene_id',Input::get('benid'))->get();
			if($bene)
			{
				$beneoutput=Icashben::where('icc_bene_id',Input::get('benid'))->update($input);
				}
				else
				{
					echo "error";
				}

	}
}
public function postTopup()
{
$input=Icash::where('icc_usercardno',Input::get('cardno'))->get();
	if($input)
	{
		$array=array(
			"icc_topuplimit"=>Input::get('topuplimit'),
			"icc_facelimit"=>Input::get('facelimit'),
			"icc_consumedlimit"=>Input::get('currentlimit')
			);
		$update=Icash::where('icc_usercardno',Input::get('cardno'))->update($array);

	}
}
	public function postTopupnew()
	{
		$checkbalance=Userfinance::where('ufin_user_id','=',Input::get('created_by'))->get();
					$getbalance=Userfinance::where('ufin_user_id','=',Input::get('created_by'))->pluck('ufin_main_balance');
			if(count($checkbalance)>0)
			{	

		$array=array(
			"icc_topup_amount"=>Input::get('topup'),
			"icc_cardno"=>Input::get('cardno'),
			"icc_mobileno"=>Input::get('mobileno'),
			"icc_securitykey"=>Input::get('security_key'),
			"icc_servicecharge"=>Input::get('service'),
			"icc_created_by"=>Input::get('created_by'),
			"icc_tranid"=>Input::get('tranid'),
			"icc_created_at"=>date('Y-m-d h:i:sa')
			);
		$newtopup=new Icashtopup;
		$oldbalance=$getbalance;
		$newtop=$newtopup->create($array);
		$getbalance=$getbalance-Input::get('topup');
						
		$balance=array
				(
				'ufin_main_balance'=>$getbalance,
				'ufin_total_used'=>$oldbalance,
				);
						
			Userfinance::where('ufin_user_id',Input::get('created_by'))->update($balance);
		return Response::json(array('status' => 'Success', 'message' => 'Request Product Have Been Added'));
					

	}
}

	public function postTransaction()
	{
		$array=array(
			"icc_cardno"=>Input::get('cardno'),
			"icc_trantype"=>Input::get('trantype'),
			"icc_trandesc"=>Input::get('trandesc'),
			"icc_tranmobile"=>Input::get('tranmobile'),
			"icc_ifsc"=>Input::get('ifsccode'),
			"icc_tranamount"=>Input::get('tranamount'),
			"icc_service"=>Input::get('servicecharge'),
			"icc_remark"=>Input::get('remark'),
			"icc_benid"=>Input::get('beneid'),
			"icc_tranid"=>Input::get('tranid'),
			"icc_created_by"=>Input::get('createdby'),
			"icc_createdat"=>date('Y-m-d h:i:sa'),
			"icc_securitykey"=>Input::get('security_key')
			);
		$newtopup=new Icashtransaction;
		$newtop=$newtopup->create($array);
	}
	public function postTranstatus()
	{
		$input=Icashtransaction::where('icc_cardno',Input::get('cardno'))->where('icc_tranid',Input::get('tranid'))->get();
		if($input)
		{
		$array=array(
			"icc_transtatus"=>Input::get('transtatus')
			);
		$newtopup=Icashtransaction::where('icc_cardno',Input::get('cardno'))->where('icc_tranid',Input::get('tranid'))->update($array);
		
			}
		}
	public function postNeftcancel()
	{
		$input=Icashtransaction::where('icc_cardno',Input::get('cardno'))->where('icc_tranid',Input::get('tranid'))->get();
		if($input)
		{
		$array=array(
			"icc_neftstatus"=>Input::get('status')
			);
		$newtopup=Icashtransaction::where('icc_cardno',Input::get('cardno'))->where('icc_tranid',Input::get('tranid'))->update($array);
		print_r($input);
			}
}

public function postUpdatekyc()
	{
		$userexist=Icash::where('icc_usercardno',Input::get('cardno'))->get();
		if($userexist)
		{
	$input=array(
				'icc_kyc'=>Input::get('kyc'),
				'icc_username'=>Input::get('username'),
				'icc_usermname'=>Input::get('usermiddlename'),
				'icc_userlname'=>Input::get('userlastname'),
				'icc_usermothername'=>Input::get('usermothermaidename'),
				'icc_user_dob'=>Input::get('userdateofbirth'),
				'icc_useremail'=>Input::get('useremail'),
				'icc_usermobile'=>Input::get('icc_usermobile'),
				'icc_usercity'=>Input::get('usercity'),
				'icc_useraddress'=>Input::get('useraddress'),
				'icc_userpincode'=>Input::get('userpincode'),
				'icc_useridprooftype'=>Input::get('useridprooftype'),
				'icc_useridproof'=>Input::get('useridproof'),
				'icc_useridproofurl'=>Input::get('useridproofurl'),
				'icc_useraddrprooftype'=>Input::get('useraddrprooftype'),
				'icc_userproof'=>Input::get('useraddrproof'),
				'icc_userproofurl'=>Input::get('useraddrproofurl'),
	);
	$update=Icash::where('icc_usercardno',Input::get('cardno'))->update($input);
}

}

public function getAgenthistory($name)
{
$data=Icashtransaction::where('icc_created_by',$name)->orderBy('icc_tran_id','DESC')->get();
//print_r($data);
if(count($data)!=0)
{
	$response=array(
		"status"=>"success",
		"transactionhistory"=>$data,
		);

}
else
{
		$response=array(
		"status"=>"failure",
		"message"=>"no transaction has been made yet"
		);
}
return Response::json($response);
}	

public function getTopupcard($name)
{
	$user=Userfinance::where('ufin_user_id',$name)->select('ufin_icash_balance')->get();
	//print_r($user); exit;

	if(count($user)>0)
	{
		foreach ($user as $users) {
			# code...
		}
		$response=array(
			"impsbalance"=>$users->ufin_icash_balance,
			
			"status"=>"success"
			);
	}
	else
	{
		$response=array(
			"message"=>"retailer doesn't have icash account",
			"status"=>"failure"
			);
	}
	return Response::json($response);
}
public function getTopupval($name,$val)
{
	$user=Userfinance::where('ufin_user_id',$name)->get();
	//print_r($user);exit;
	
	//echo $newval;
	if(count($user)>0)
	{
		foreach ($user as $users) {
			# code...
		}
		if($users->ufin_main_balance>$val)
		{
		$arr=array
		(
		'ufin_main_balance'=>$users->ufin_main_balance-$val,
		'ufin_icash_balance'=>$users->ufin_icash_balance+$val,
		'ufin_icash_credited'=>$users->ufin_icash_credited_val+$val,
		);	
		$user=Userfinance::where('ufin_user_id',$name)->update($arr);
	
		$response=array(
			"cardno"=>"balance added",
			"status"=>"success"
			);
	}
	else
	{
		$response=array(
			"message"=>"Balance is low",
			"status"=>"failure"
			);
	}
}
	return Response::json($response);
}
public function getTransfer($name,$val)
{
	$user=Userfinance::where('ufin_user_id',$name)->get();
	//print_r($user);exit;
	
	//echo $newval;
	if(count($user)>0)
	{
		foreach ($user as $users) {
			# code...
		}
		if($users->ufin_icash_balance>$val)
		{
		$arr=array
		(
		'ufin_icash_balance'=>$users->ufin_icash_balance-$val,
		'ufin_icash_used'=>$users->ufin_icash_used+$val,
		);	
		$user=Userfinance::where('ufin_user_id',$name)->update($arr);
	
		$response=array(
			"cardno"=>" balance remitted",
			"status"=>"success"
			);
	}
	else
	{
		$response=array(
			"message"=>"Balance is low",
			"status"=>"failure"
			);
	}
}
	return Response::json($response);
}

public function getBalance($balance)
{
	$update=Userfinance::where('ufin_user_id_pk_fk',1)->update(array('ufin_icash_balance'=>$balance));
}


	
	

}
