<?php

class ItrController extends BaseController {

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
	public function getItrhistory($name)
	{
		$data=Itr::where('itr_createdby',$name)->get();
		if(count($data)>0)
		{
			$Response=array(
				"status"=>"success",
				"itrhistory"=>$data
				);
		}
		else
		{
			$Response=array(
				"status"=>"failure",
				"message"=>"no data found"
				);
		}
		return Response::json($Response);
	}
	public function postProductentry()
	{
		$postdata=file_get_contents("php://input");
		if(!empty($postdata))
		{
			$inputid=Input::get('userId');
			$userlink=Input::get('userlink');
			$currenttime=Commonmodel::dateandtime();
			$getimage=explode("/",$userlink);
			$stroreimage=$getimage[6];
			$productcode=Input::get('prodcode');
			if($inputid!=''&&$userlink!=''&&$productcode!='')
			{
				$getrecords=Userproductaccess::where('upa_ud_user_id',$inputid)->where('upa_prod_code',$productcode)->get();
				if(count($getrecords)>0)
				{
					return Response::json(array('status' => 'failure', 'message' => 'Already You have This Products'));
				}
				else
				{
					$checkbalance=Userfinance::where('ufin_user_id','=',$inputid)->where('ufin_main_balance','<','100')->get();
					$getbalance=Userfinance::where('ufin_user_id','=',$inputid)->pluck('ufin_main_balance');
					if(count($checkbalance)>0)
					{
						return Response::json(array('status' => 'failure', 'message' => 'You Do not Have Sufficient Balance'));
					}
					else
					{
						
						$input=array
						(
							'upa_ud_user_id'=>$inputid,
							'upa_prod_code'=>$productcode,
							'upa_created_at'=>$currenttime,
							'upa_photo'=>$stroreimage,
						);
						$getbalance=$getbalance-99;
						$product=new Userproductaccess;
						$product->create($input);
						$balance=array
									(
										'ufin_main_balance'=>$getbalance,
									);
						
						Userfinance::where('ufin_user_id',$inputid)->update($balance);
						return Response::json(array('status' => 'Success', 'message' => 'Request Product Have Been Added'));
					}
				}
				
			}
			else
			{
				return Response::json(array('status' => 'failure', 'message' => 'Fill All Manditary Field'));
			}
				
		}
	}
	
	public function postItrregister()
	{
	
		$postdata=file_get_contents("php://input");
		if(!empty($postdata))
		{	
			$inputid=Input::get('userId');
			$itr_name=Input::get('name');
			$itr_pan=Input::get('pan');
			$itr_bankstatement=Input::get('bankstatement');
			$itr_form16=Input::get('form16');
			$itr_tdscertificate=Input::get('tds');
			$itr_addrproof=Input::get('addrproof');
			$itr_previtr=Input::get('itrcopy');
			$itr_bankname=Input::get('bankacname');
			$itr_bankacctype=Input::get('actype');
			$itr_bankaccno=Input::get('accno');
			$itr_bankifsc=Input::get('ifsccode');
			$itr_fyear=Input::get('fyear');
			$itr_mobileno=Input::get('mobileno');
			$itr_email=Input::get('email');
			$itr_createdby=Input::get('userId');
			$itr_createdat=Commonmodel::dateandtime();
			$itr_status=1;
			$itr_clientip=Input::get('clientip');
			
		//	echo $itr_name,"<br/>", $itr_pan ,"<br/>", $itr_bankstatement; exit;
			
			if($itr_name!=''&&$itr_pan!=''&&$itr_bankstatement!=''&&$itr_addrproof!=''&&$itr_mobileno!=''&&$itr_email!='')
			{
				$detectyear=Itr::where('itr_mobileno','=',$itr_mobileno)->pluck('itr_createdat');
				$enreytear=Commonmodel::detectyear($itr_createdat);
				$currentyear=Commonmodel::detectyear($detectyear);
				if($enreytear==$currentyear)
				{
					
					return Response::json(array('status' => 'failure', 'message' => 'You have Already Applyied For IRT this year'));
				}
				else
				{
					$checkbalance=Userfinance::where('ufin_user_id','=',$inputid)->where('ufin_main_balance','<','100')->get();
					$getbalance=Userfinance::where('ufin_user_id','=',$inputid)->pluck('ufin_main_balance');
					if(count($checkbalance)>0)
					{
						return Response::json(array('status' => 'failure', 'message' => 'You Do not Have Sufficient Balance TO Apply for IRT'));
					}
					else
					{
						$input=array
						(
						
							'itr_name'=>$itr_name,
							'itr_pan'=>$itr_pan,
							'itr_bankstatement'=>$itr_bankstatement,
							'itr_form16'=>$itr_form16,
							'itr_tdscertificate'=>$itr_tdscertificate,
							'itr_addrproof'=>$itr_addrproof,
							'itr_previtr'=>$itr_previtr,
							'itr_bankname'=>$itr_bankname,
							'itr_bankacctype'=>$itr_bankacctype,
							'itr_bankaccno'=>$itr_bankaccno,
							'itr_bankifsc'=>$itr_bankifsc,
							'itr_fyear'=>$itr_fyear,
							'itr_mobileno'=>$itr_mobileno,
							'itr_email'=>$itr_email,
							'itr_createdby'=>$itr_createdby,
							'itr_createdat'=>$itr_createdat,
							'itr_status'=>$itr_status,
							'itr_clientip'=>$itr_clientip,
						);
						
						$obj=new Itr;
						$obj->create($input);
						$getbalance=$getbalance-99;
						$balance=array
									(
										'ufin_main_balance'=>$getbalance,
									);
						
						Userfinance::where('ufin_user_id',$inputid)->update($balance);
						return Response::json(array('status' => 'Success', 'message' => 'Your PAN Card Details Have been Registered Successfully'));
					}
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
	
	
}
