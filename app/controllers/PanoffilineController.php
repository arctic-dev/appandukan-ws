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
			$frlist=DB::table('adt_user_details')->select('UD_USER_ID','UD_ID_PK')->where('UD_PARENT_ID',Input::get('currentUserIdPk'))->get();
			
				//print_r($frlist); 
				$arr=array();
				if(!empty($frlist))
				{
				foreach ($frlist as $user) {
				$arr[]=$user->UD_USER_ID;
				$arr1[]=$user->UD_ID_PK;
				}

				$rtlist=DB::table('adt_user_details')->select('UD_USER_ID','UD_ID_PK')->whereIn('UD_PARENT_ID',$arr1)->get();
				//print_r($rtlist); exit;
				if(!empty($rtlist))
				{
				foreach ($rtlist as $retailer) {
				$arr[]=$retailer->UD_USER_ID;
				$arr2[]=$retailer->UD_ID_PK;
					
				}
				$srlist=DB::table('adt_user_details')->select('UD_USER_ID','UD_ID_PK')->whereIn('UD_PARENT_ID',$arr2)->get();
				//print_r($arr2);
				foreach ($srlist as $subretailer) {
				$arr[]=$subretailer->UD_USER_ID;
				//$arr2[]=$user->UD_ID_PK;
					
				}
			}
				//print_r($arr); exit;
			
			
				$Panhistory=DB::table('adt_pan_49a')->whereIn('pan_created_by',$arr)->get();
				
		//print_r($Rechargetotal); exit;
			$response=array(
					"status"=>"success",
					"Panhistory"=>$Panhistory,
					
				);
		}
		else
		{
		$response=array
					(
					"status"=>"failure",
					"message"=>"no PAN form has been submitted yet",
					);	
		}
		return Response::json($response);
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
					$checkbalance=Userfinance::where('ufin_user_id','=',$currentUserId)->where('ufin_main_balance','<=','106')->get();
					$couponcheck=Coupons::orderBy('pc_coupon_no','ASC')->first()->pluck('pc_coupon_no');
					if($couponcheck)
					{
						$getbalance=Userfinance::where('ufin_user_id','=',$currentUserId)->pluck('ufin_main_balance');
						if(count($checkbalance)>0)
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

	}
	
	public function getForm($id)
	{
			//$postdata=file_get_contents("php://input");
			if(!empty($id))
			{
				$userid=$id;
			
			
				$check=Panoffiline::where('pan_created_by','=',$userid)->get();
				if(count($check)>0)
				{
					for($i=0; $i<count($check); $i++)
					{
						echo $check[$i]['idPk'];
						
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
					return Response::json(array('status' => 'success', 'panForms' => $getdetails, 'message' => 'User PAN forms returned successfully'));
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

}