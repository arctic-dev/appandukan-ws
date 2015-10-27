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
			$frlist=DB::table('adt_user_details')->select('UD_USER_ID','UD_ID_PK')->where('UD_PARENT_ID',Input::get('currentUserIdPk'))->get();
			
				//print_r($frlist); exit;
				$arr=array();
				if(!empty($frlist))
				{
				foreach ($frlist as $user) {
				$arr[]=$user->UD_USER_ID;
				$arr1[]=$user->UD_ID_PK;
				}

				$rtlist=DB::table('adt_user_details')->select('UD_USER_ID','UD_ID_PK')->whereIn('UD_PARENT_ID',$arr1)->get();
				//print_r($rtlist);
				$arr2=array();
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
			$Rechargetotal=0;
			$Rechargecommission=0;
			
				$Rechargehistory[]=DB::table('adt_recharge_details')->whereIn('rd_created_by',$arr)->where('rd_prod_code',Input::get('prodCode'))->get();
				$Rechargetotal+=DB::table('adt_recharge_details')->whereIn('rd_created_by',$arr)->where('rd_prod_code',Input::get('prodCode'))->sum('rd_amount');
				$Rechargecommission+=DB::table('adt_recharge_details')->whereIn('rd_created_by',$arr)->where('rd_prod_code',Input::get('prodCode'))->sum('rd_commission');
			
		//print_r($Rechargetotal); exit;
			$response=array(
					"status"=>"success",
					"Rechargehistory"=>$Rechargehistory,
					"Rechargetotal"=>$Rechargetotal,
					"Rechargecommission"=>$Rechargecommission,

				);
		}
		else
		{
		$response=array(
					"status"=>"failure",
					"message"=>"no Recharge has Been Done",

				);	
		}
		return Response::json($response);
	}

	

}
