<?php

class LedgerController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	| Route::get('/', 'HomeController@showWelcome');
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
	
	public function postDashboard()
	{
		$userID = Input::get('created_by');
		$userRole = User::where('UD_USER_ID','=',$userID)->get();
		foreach($userRole as $ur){
			$userType = $ur->UD_USER_TYPE;
		}
		if( ($userType == "SA") || ($userType == "SAS") ){
			$successRecAmount = 0;
			$failureRecAmount = 0;
			$startDate = date('Y-m-d')." 00:00:00";
			$endDate = date('Y-m-d')." 23:59:59";
			
			$successRechargeRecords = LedgerReport::where('lr_comment','=','Recharge')->where('lr_status','=','success')->whereBetween('lr_date',array($startDate,$endDate))->get();
			foreach($successRechargeRecords as $srr){
				$successRecAmount = $successRecAmount + $srr->lr_debit_amount;
				
			}
			$failureRechargeRecords = LedgerReport::where('lr_comment','=','Recharge')->where('lr_status','=','failure')->whereBetween('lr_date',array($startDate,$endDate))->get();
			foreach($failureRechargeRecords as $srr){
				$failureRecAmount = $failureRecAmount + $srr->lr_debit_amount;
			}
			//User count
			$SA = count(User::where('UD_USER_TYPE','=','SA')->get());
			$SAS = count(User::where('UD_USER_TYPE','=','SAS')->get());
			$SP = count(User::where('UD_USER_TYPE','=','SP')->get());
			$SPS = count(User::where('UD_USER_TYPE','=','SPS')->get());
			$SD = count(User::where('UD_USER_TYPE','=','SD')->get());
			$SDS = count(User::where('UD_USER_TYPE','=','SDS')->get());
			$D = count(User::where('UD_USER_TYPE','=','D')->get());
			$DS = count(User::where('UD_USER_TYPE','=','DS')->get());
			$FR = count(User::where('UD_USER_TYPE','=','FR')->get());
			$FRS = count(User::where('UD_USER_TYPE','=','FRS')->get());
			$SFR = count(User::where('UD_USER_TYPE','=','SFR')->get());
			$SFRS = count(User::where('UD_USER_TYPE','=','SFRS')->get());
			
			//User Balance
			$SAbalance = Commonmodel::fetchUserdetails('SA');			
			$SASbalance = Commonmodel::fetchUserdetails('SAS');
			$SPbalance = Commonmodel::fetchUserdetails('SP');
			$SPSbalance = Commonmodel::fetchUserdetails('SPS');
			$SDbalance = Commonmodel::fetchUserdetails('SD');
			$SDSbalance = Commonmodel::fetchUserdetails('SDS');
			$Dbalance = Commonmodel::fetchUserdetails('D');
			$DSbalance = Commonmodel::fetchUserdetails('DS');
			$FRbalance = Commonmodel::fetchUserdetails('FR');
			$FRSbalance = Commonmodel::fetchUserdetails('FRS');
			$SFRbalance = Commonmodel::fetchUserdetails('SFR');
			$SFRSbalance = Commonmodel::fetchUserdetails('SFRS');
			//Ezypay
			$getbalance=Rechargeurl::balance();
			$ezbalance=$getbalance[0];
			$output=array(
					'Success Recharge Amount' => $successRecAmount,
					'Failure Recharge Amount' => $failureRecAmount,
					'SA' => $SA,
					'SAS' => $SAS,
					'SP' => $SP,
					'SPS' => $SPS,
					'SD' => $SD,
					'SDS' => $SDS,
					'D' => $D,
					'DS' => $DS,
					'FR' => $FR,
					'FRS' => $FRS,
					'SFR' => $SFR,
					'SFRS' => $SFRS,
					'Ezpay Balance' => $ezbalance,
					'SA Balance' => $SAbalance,
					'SAS Balance' => $SASbalance,
					'SP Balance' => $SPbalance,
					'SPS Balance' => $SPSbalance,
					'SD Balance' => $SDbalance,
					'SDS Balance' => $SDSbalance,
					'D Balance' => $Dbalance,
					'DS Balance' => $DSbalance,
					'FR Balance' => $FRbalance,
					'FRS Balance' => $FRSbalance,
					'SFR Balance' => $SFRbalance,
					'SFRS Balance' => $SFRSbalance,
			);
		}
		elseif( ($userType == "FR") || ($userType == "FRS")){
			$FRbalance = DB::table('adt_user_finance')->leftjoin('adt_user_details', 'adt_user_finance.ufin_user_id','=', 'adt_user_details.UD_USER_ID')->where('adt_user_details.UD_USER_ID','=',$userID)->get();
			$fraBalance=0;
			foreach($FRbalance as $fr){
				$fraBalance = $fr->ufin_main_balance;
			}
			$output = array(
					'Franchise Balance' => $fraBalance,
			);
		}
		return Response::json($output);
	}
	
}