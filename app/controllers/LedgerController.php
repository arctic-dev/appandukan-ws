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
		$userIDPK = Input::get('userIDPK');
		$userRole = User::where('UD_USER_ID','=',$userID)->get();
		foreach($userRole as $ur){
			$userType = $ur->UD_USER_TYPE;
		}
		if( ($userType == "SA") || ($userType == "SAS") )
		{
			$successRecAmount = 0;
			$failureRecAmount = 0;
			$startDate = date('Y-m-d')." 00:00:00";
			$endDate = date('Y-m-d')." 23:59:59";
			
			//recharge stats
			$successRechargeRecords = LedgerReport::where('lr_comment','=','Recharge')->where('lr_status','=','success')->whereBetween('lr_date',array($startDate,$endDate))->get();
			foreach($successRechargeRecords as $srr)
			{
				$successRecAmount = $successRecAmount + $srr->lr_debit_amount;
				
			}
			$failureRechargeRecords = LedgerReport::where('lr_comment','=','Recharge')->where('lr_status','=','failure')->whereBetween('lr_date',array($startDate,$endDate))->get();
			foreach($failureRechargeRecords as $srr)
			{
				$failureRecAmount = $failureRecAmount + $srr->lr_debit_amount;
			}
			//icash balance
			$icashBalance = Userfinance::where('ufin_user_id','=',$userID)->get();
			$icashBal = 0;
			foreach($icashBalance as $bal)
			{
				$icashBal = $icashBal + $bal->ufin_icash_balance;
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
					'icash Balance' => $icashBal,
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
		elseif( ($userType == "FR") || ($userType == "FRS") || ($userType == "SFR")){
			$FRbalance = DB::table('adt_user_finance')->leftjoin('adt_user_details', 'adt_user_finance.ufin_user_id','=', 'adt_user_details.UD_USER_ID')->where('adt_user_details.UD_USER_ID','=',$userID)->get();
			$fraBalance=0;
			foreach($FRbalance as $fr){
				$fraBalance = $fr->ufin_main_balance;
				$icashBalance = $fr->ufin_icash_balance;
			}
			$FRSaccounts = count(User::where('UD_PARENT_ID','=',$userIDPK)->where('UD_USER_TYPE','=','SFR')->get());
			$SFRbalance = DB::table('adt_user_finance')->leftjoin('adt_user_details', 'adt_user_finance.ufin_user_id','=', 'adt_user_details.UD_USER_ID')->where('adt_user_details.UD_USER_TYPE','=','SFR')->get();
			$sfrbal=0;
			foreach($SFRbalance as $frs){
				$sfrbal = $sfrbal + $frs->ufin_main_balance;
			}
			$output = array(
					'Franchise Balance' => $fraBalance,
					'icash Balance' => $icashBalance,
					'Sub franchise accounts' => $FRSaccounts,
					'Sub franchise balance' => $sfrbal,
			);
		}
		return Response::json($output);
	}
	
	public function postDailycommission()
	{
		$date = Input::get('date');
		$usertype = Input::get('usertype');
		$startDate = $date." 00:00:00";
		$endDate = $date." 23:59:59";
		
		$dailyCommission = DB::table('adt_recharge_ledger')->leftjoin('adt_user_details', 'adt_recharge_ledger.rchlgr_fr_id','=', 'adt_user_details.UD_ID_PK')->where('adt_user_details.UD_USER_TYPE','=',$usertype)->whereBetween('adt_recharge_ledger.rchlgr_date',array($startDate,$endDate))->get();
		//$output = array();
		return Response::json($dailyCommission);
	}
	
	public function postDailybusiness()
	{
		$date = Input::get('date');
		$usertype = Input::get('usertype');
		$startDate = $date." 00:00:00";
		$endDate = $date." 23:59:59";
	
		$dailyCommission = DB::table('adt_recharge_ledger')->leftjoin('adt_user_details', 'adt_recharge_ledger.rchlgr_fr_id','=', 'adt_user_details.UD_ID_PK')->where('adt_user_details.UD_USER_TYPE','=',$usertype)->whereBetween('adt_recharge_ledger.rchlgr_date',array($startDate,$endDate))->get();
		//$output = array();
		return Response::json($dailyCommission);
	}
}