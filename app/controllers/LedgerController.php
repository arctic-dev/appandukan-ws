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
					'Success_Recharge_Amount' => $successRecAmount,
					'Failure_Recharge_Amount' => $failureRecAmount,
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
					'Ezpay_Balance' => $ezbalance,
					'icash_Balance' => $icashBal,
					'SA_Balance' => $SAbalance,
					'SAS_Balance' => $SASbalance,
					'SP_Balance' => $SPbalance,
					'SPS_Balance' => $SPSbalance,
					'SD_Balance' => $SDbalance,
					'SDS_Balance' => $SDSbalance,
					'D_Balance' => $Dbalance,
					'DS_Balance' => $DSbalance,
					'FR_Balance' => $FRbalance,
					'FRS_Balance' => $FRSbalance,
					'SFR_Balance' => $SFRbalance,
					'SFRS_Balance' => $SFRSbalance,
			);
		}
		elseif($userType == "SP")
		{
			$SPbalance = DB::table('adt_user_finance')->leftjoin('adt_user_details', 'adt_user_finance.ufin_user_id','=', 'adt_user_details.UD_USER_ID')->where('adt_user_details.UD_PARENT_ID','=',$userIDPK)->get();
			$spartBalance=0;
			if($SPbalance)
			{
				foreach($SPbalance as $sp)
				{
					$spaBalance = $sp->ufin_main_balance;
				}
				$Daccounts = count(User::where('UD_PARENT_ID','=',$userIDPK)->get());
				$SDbalance = DB::table('adt_user_finance')->leftjoin('adt_user_details', 'adt_user_finance.ufin_user_id','=', 'adt_user_details.UD_USER_ID')->where('adt_user_details.UD_PARENT_ID','=',$userIDPK)->get();
				$FRtotalregamount = Commonmodel::fetchRechargedetails($userID);
				
				$disbal=0;
				if($SDbalance)
				{		
					foreach($SDbalance as $frs)
					{
						$disbal = $disbal + $frs->ufin_main_balance;
					}
					$output = array(
							'Statepartner_Balance' => $spaBalance,
							'Distributor_accounts' => $Daccounts,
							'Distributor_balance' => $disbal,
					);
				}
				else 
				{
					$output = array(
							'Statepartner_Balance' => $spaBalance,
							'Distributor_accounts' => $Daccounts,
							'Distributor_balance' => '0',
					);
				}
			}
			else
			{
				$output = array(
						'status' => 'failure',
						'message' => 'No results found',
				);
			}
		}
		elseif($userType == "SD")
		{
			$SDbalance = DB::table('adt_user_finance')->leftjoin('adt_user_details', 'adt_user_finance.ufin_user_id','=', 'adt_user_details.UD_USER_ID')->where('adt_user_details.UD_USER_ID','=',$userID)->get();
			$sdiBalance=0;
			if($SDbalance)
			{
				foreach($SDbalance as $sp)
				{
					$sdiBalance = $sp->ufin_main_balance;
				}
				$Daccounts = count(User::where('UD_PARENT_ID','=',$userIDPK)->get());
				$Disbalance = DB::table('adt_user_finance')->leftjoin('adt_user_details', 'adt_user_finance.ufin_user_id','=', 'adt_user_details.UD_USER_ID')->where('adt_user_details.UD_PARENT_ID','=',$userIDPK)->get();
				$FRtotalregamount = Commonmodel::fetchRechargedetails($userID);
		
				$disbal=0;
				if($Disbalance)
				{
					foreach($Disbalance as $frs)
					{
						$disbal = $disbal + $frs->ufin_main_balance;
					}
					$output = array(
							'Super_Distributer_Balance' => $sdiBalance,
							'Distributor_accounts' => $Daccounts,
							'Distributor_balance' => $disbal,
					);
				}
				else
				{
					$output = array(
							'status' => 'failure',
							'message' => 'No results found',
					);
				}
			}
			else
			{
				$output = array(
						'status' => 'failure',
						'message' => 'No results found',
				);
			}
		}
		elseif($userType == "D")
		{
			$SDbalance = DB::table('adt_user_finance')->leftjoin('adt_user_details', 'adt_user_finance.ufin_user_id','=', 'adt_user_details.UD_USER_ID')->where('adt_user_details.UD_USER_ID','=',$userID)->get();
			$sdiBalance=0;
			if($SDbalance)
			{
				foreach($SDbalance as $sp)
				{
					$sdiBalance = $sp->ufin_main_balance;
				}
				$Daccounts = count(User::where('UD_PARENT_ID','=',$userIDPK)->get());
				$Disbalance = DB::table('adt_user_finance')->leftjoin('adt_user_details', 'adt_user_finance.ufin_user_id','=', 'adt_user_details.UD_USER_ID')->where('adt_user_details.UD_PARENT_ID','=',$userIDPK)->get();
				$FRtotalregamount = Commonmodel::fetchRechargedetails($userID);
				$disbal=0;
				if($Disbalance)
				{
					foreach($Disbalance as $frs)
					{
						$disbal = $disbal + $frs->ufin_main_balance;
					}
					$output = array(
							'Distributer_Balance' => $sdiBalance,
							'Franchise_accounts' => $Daccounts,
							'Franchise_balance' => $disbal,
					);
				}
				else
				{
					$output = array(
							'status' => 'failure',
							'message' => 'No results found',
					);
				}
			}
			else
			{
				$output = array(
						'status' => 'failure',
						'message' => 'No results found',
				);
			}
		}
		elseif( ($userType == "FR") || ($userType == "FRS"))
		{
			$FRbalance = DB::table('adt_user_finance')->leftjoin('adt_user_details', 'adt_user_finance.ufin_user_id','=', 'adt_user_details.UD_USER_ID')->where('adt_user_details.UD_USER_ID','=',$userID)->get();
			$fraBalance=0;
			if($FRbalance)
			{
				foreach($FRbalance as $fr)
				{
					$fraBalance = $fr->ufin_main_balance;
					$icashBalance = $fr->ufin_icash_balance;
				}
				$FRSaccounts = count(User::where('UD_PARENT_ID','=',$userIDPK)->get());
				$SFRbalance = DB::table('adt_user_finance')->leftjoin('adt_user_details', 'adt_user_finance.ufin_user_id','=', 'adt_user_details.UD_USER_ID')->where('adt_user_finance.ufin_user_id','=',$userID)->get();
				$FRtotalregamount = Commonmodel::fetchRechargedetails($userID);
				
				$sfrbal=0;
				if($FRbalance)
				{		
					foreach($SFRbalance as $frs)
					{
						$sfrbal = $sfrbal + $frs->ufin_main_balance;
					}
					$output = array(
							'Franchise_Balance' => $fraBalance,
							'icash_Balance' => $icashBalance,
							'Sub_franchise_accounts' => $FRSaccounts,
							'Sub_franchise_balance' => $sfrbal,
							'Total_recharge_amount' => $FRtotalregamount,
					);
				}
				else 
				{
					$output = array(
							'status' => 'failure',
							'message' => 'No results found',
					);
				}
			}
			else
			{
				$output = array(
						'status' => 'failure',
						'message' => 'No results found',
				);
			}
			
		}
		elseif( ($userType == "SFR") || ($userType == "SFRS"))
		{
			$FRbalance = DB::table('adt_user_finance')->leftjoin('adt_user_details', 'adt_user_finance.ufin_user_id','=', 'adt_user_details.UD_USER_ID')->where('adt_user_details.UD_USER_ID','=',$userID)->get();
			$fraBalance=0;
			if($FRbalance)
			{
				$FRtotalregamount = Commonmodel::fetchRechargedetails($userID);
				foreach($FRbalance as $fr)
				{
					$fraBalance = $fr->ufin_main_balance;
					$icashBalance = $fr->ufin_icash_balance;
				}
				$output = array(
						'Franchise_Balance' => $fraBalance,
						'icash_Balance' => $icashBalance,
						'Total_recharge_amount' => $FRtotalregamount,
				);
			}
			else
			{
				$output = array(
						'status' => 'failure',
						'message' => 'No results found',
				);
			}
		}
		return Response::json($output);
	}
	
	public function postDailycommission()
	{
		$date = Input::get('date');
		$usertype = Input::get('usertype');
		if(($date == '') || ($usertype == ''))
		{
			$output[] = array(
					'status' => 'failure',
					'message' => 'Input reponse wrong',
			);
		}
		else
		{
			$startDate = $date." 00:00:00";
			$endDate = $date." 23:59:59";
			$dailyCommission = DB::table('adt_recharge_ledger')->leftjoin('adt_user_details', 'adt_recharge_ledger.rchlgr_fr_id','=', 'adt_user_details.UD_ID_PK')->leftjoin('adt_ledger_report', 'adt_recharge_ledger.rchlgr_lr_id','=', 'adt_ledger_report.lr_id_pk')->where('adt_user_details.UD_USER_TYPE','=',$usertype)->whereBetween('adt_recharge_ledger.rchlgr_date',array($startDate,$endDate))->get();
			if($dailyCommission)
			{
				foreach($dailyCommission as $ds)
				{
					$amount = $ds->rchlgr_fr_commission;
					$userid = $ds->rchlgr_fr_id;
					$usertype = $ds->UD_USER_TYPE;
					$date = $ds->rchlgr_date;
					$type = $ds->lr_comment;
					$output[] = array(
							'Commission_Amount' => $amount,
							'User_Id' => $userid,
							'User_type' => $usertype,
							'Date' => $date,
							'Type' => $type,
					);
				}		
			}
			else
			{
				$output = array(
						'status' => 'failure',
						'message' => 'No results found',
						
				);
			}
		}
		return Response::json($output);
	}
	
	public function postDailybusiness()
	{
		$date = Input::get('date');
		$usertype = Input::get('usertype');
		if(($date == '') || ($usertype == ''))
		{
			$output[] = array(
					'status' => 'failure',
					'message' => 'Input reponse wrong',
			);
		}
		else
		{
			$startDate = $date." 00:00:00";
			$endDate = $date." 23:59:59";
			$dailyBussiness = DB::table('adt_user_details')->leftjoin('adt_ledger_report', 'adt_ledger_report.lr_created_by','=', 'adt_user_details.UD_USER_ID')->where('adt_user_details.UD_USER_TYPE','=',$usertype)->whereBetween('adt_ledger_report.lr_date',array($startDate,$endDate))->get();
			if($dailyBussiness)
			{
				foreach($dailyBussiness as $ds)
				{
					$amount = $ds->lr_debit_amount;
					$userid = $ds->UD_ID_PK;
					$usertype = $ds->UD_USER_TYPE;
					$date = $ds->lr_date;
					$output[] = array(
							'Amount' => $amount,
							'User_Id' => $userid,
							'User_type' => $usertype,
							'Date' => $date,
					);
				}		
			}
			else
			{
				$output = array(
						'status' => 'failure',
						'message' => 'No results found',
						
				);
			}
		}
		return Response::json($output);
	}

	public function postDailyrecharge()
	{
		$date = Input::get('date');
		$usertype = Input::get('usertype');
		if(($date == '') || ($usertype == ''))
		{
			$output[] = array(
					'status' => 'failure',
					'message' => 'Input reponse wrong',
			);
		}
		else
		{
			$startDate = $date." 00:00:00";
			$endDate = $date." 23:59:59";
			$dailyRecharge = DB::table('adt_recharge_ledger')->leftjoin('adt_user_details', 'adt_recharge_ledger.rchlgr_fr_id','=', 'adt_user_details.UD_ID_PK')->leftjoin('adt_recharge_details', 'adt_recharge_details.rd_created_by','=', 'adt_user_details.UD_USER_ID')->where('adt_user_details.UD_USER_TYPE','=',$usertype)->whereBetween('adt_recharge_details.rd_created_at',array($startDate,$endDate))->get();	
			if($dailyRecharge)
			{
				foreach($dailyRecharge as $ds)
				{
					$userid = $ds->UD_ID_PK;
					$type = $ds->UD_USER_TYPE;
					$serviceprovider = $ds->rd_service_provider;
					$mobile = $ds->UD_USER_MOBILE;
					$amount = $ds->rd_amount;
					$sfcommission = $ds->rd_sfcommission;
					$commission = $ds->rd_commission;
					$dcommission = $ds->rd_dcommission;
					$date = $ds->rd_created_at;
					$rby = $ds->rd_created_type;
					$rbyid = $ds->rd_created_by;
					$status = $ds->rd_result;
					$output[] = array(
							'user_Id' => $userid,
							'type' => $type,
							'service_provider' => $serviceprovider,
							'mobile_number' => $mobile,
							'amount' => $amount,
							'sfcommission' => $sfcommission,
							'commissiom' => $commission,
							'dcommission' => $dcommission,
							'Date' => $date,
							'By' => $rby,
							'By_ID' => $rbyid,
							'status' => $status,		
					);
				}
				
			}else
			{
				$output = array(
						'status' => 'failure',
						'message' => 'No results found',
							
				);
			}
		}
		return Response::json($output);
	}
	
	public function postDailypancard()
	{
		$date = Input::get('date');
		$usertype = Input::get('usertype');
		if(($date == '') || ($usertype == ''))
		{
			$output[] = array(
					'status' => 'failure',
					'message' => 'Input reponse wrong',
			);
		}
		else
		{
			$startDate = $date." 00:00:00";
			$endDate = $date." 23:59:59";
			$selectuserID = DB::table('adt_pan_49a')->leftjoin('adt_user_details', 'adt_pan_49a.pan_created_by','=', 'adt_user_details.UD_USER_ID')->distinct()->whereBetween('pan_created_at',array($startDate,$endDate))->groupBy('pan_created_by')->get();
			if($selectuserID)
			{
				foreach ($selectuserID as $uid)
				{
					$userid = $uid->pan_created_by;
					$usertype = $uid->UD_USER_TYPE;
					$date = $uid->pan_created_at;
					$dailyPancard =DB::table('adt_pan_49a')->leftjoin('adt_user_details', 'adt_pan_49a.pan_created_by','=', 'adt_user_details.UD_USER_ID')->where('adt_pan_49a.pan_created_by','=',$userid)->where('adt_user_details.UD_USER_TYPE','=',$usertype)->whereBetween('adt_pan_49a.pan_created_at',array($startDate,$endDate))->get();
					foreach($dailyPancard as $did)
					{
						$noofcards = count($dailyPancard);
					}
					$output[] = array(
							'Numberofpancard' => $noofcards,
							'user_Id' => $userid,
							'usertype' => $usertype,
							'date' => $date,
							'type' => 'Pancard request',
					);
				}
			}
			else
			{
				$output = array(
						'status' => 'failure',
						'message' => 'No results found',
				);
			}
		}
		return Response::json($output);
	}
	
	public function postDailybalancetransfer()
	{
		$date = Input::get('date');
		$usertype = Input::get('usertype');
		if(($date == '') || ($usertype == ''))
		{
			$output[] = array(
					'status' => 'failure',
					'message' => 'Input reponse wrong',
					);
		}
		else
		{
			$startDate = $date." 00:00:00";
			$endDate = $date." 23:59:59";
			$dailyBaltransfer = DB::table('adt_credit_recharge')->leftjoin('adt_user_details', 'adt_credit_recharge.cr_user_id','=', 'adt_user_details.UD_USER_ID')->where('adt_user_details.UD_USER_TYPE','=',$usertype)->whereBetween('adt_credit_recharge.cr_created_at',array($startDate,$endDate))->get();
			if($dailyBaltransfer)
			{
				foreach($dailyBaltransfer as $ds)
				{
					$date = $ds->cr_created_at;
					$id = $ds->cr_id_pk;
					$type = $ds->cr_type;
					$amountgiven = $ds->cr_amount;
					$usertype = $ds->UD_USER_TYPE;
					$userid = $ds->UD_USER_ID;
					$output[] = array(
							'date' => $date,
							'Id' => $id,
							'type' => $type,
							'Amount_Given' => $amountgiven,
							'usertype' => $usertype,
							'user_Id' => $userid,	
					);
				}
			}
			else
			{
				$output = array(
						'status' => 'failure',
						'message' => 'No results found',
				);
			}
		}
		return Response::json($output);
	}
	
	public function postDailymoneytransfer()
	{
		$date = Input::get('date');
		$usertype = Input::get('usertype');
		if(($date == '') || ($usertype == ''))
		{
			$output[] = array(
					'status' => 'failure',
					'message' => 'Input reponse wrong',
			);
		}
		else
		{
			$startDate = $date." 00:00:00";
			$endDate = $date." 23:59:59";
			$dailyMoneytransfer = DB::table('adt_icc_transaction')->leftjoin('adt_icash_bene', 'adt_icc_transaction.icc_tranid','=', 'adt_icash_bene.icc_bentranid')->leftjoin('adt_user_details', 'adt_icc_transaction.icc_created_by','=', 'adt_user_details.UD_USER_ID')->where('adt_user_details.UD_USER_TYPE','=',$usertype)->whereBetween('adt_icc_transaction.icc_createdat',array($startDate,$endDate))->get();
			if($dailyMoneytransfer)
			{
				foreach($dailyMoneytransfer as $ds)
				{
					$id = $ds->icc_tran_id;
					$cardnumber = $ds->icc_cardno;
					$transtype = $ds->icc_trantype;
					$transamount = $ds->icc_tranamount;
					$fee = $ds->icc_service;
					//$tamount = $ds->;
					$bid = $ds->icc_benid;
					$baccno = $ds->icc_benaccno;
					$bankname = $ds->icc_benbenkname;
					$branchname = $ds->icc_benbranchname;
					//$regmobno = $ds->;
					//$bbicode = $ds->;
					$b_mobile = $ds->icc_mobilenumber;
					$remark = $ds->icc_remark;
					$created = $ds->icc_createdat;
					$status = $ds->icc_transtatus;
					$byid = $ds->icc_created_by;
					$bytype = $ds->UD_USER_TYPE;
					
					$output[] = array(
							'ID' => $id,
							'C_Card_Number' => $cardnumber,
							'Trans_Type' => $transtype,
							'Trans_Amount' => $transamount,
							'fee' => $fee,
							//'T_Amount' => $tamount,
							'B_ID' => $bid,
							'B_Ac_No' => $baccno,
							'B_bank_name' => $bankname,
							'B_branch_name' => $branchname,
							//'Reg_Mob_No_Trsf' => $regmobno,
							//'B_B_Icode' => $bbicode,
							'B_Mobile' => $b_mobile,
							'Created' => $created,
							'remark' => $remark,
							'Status' => $status,
							'By_Id' => $byid,
							'BY_Type' => $bytype,
					);
				}
			}
			else
			{
				$output = array(
						'status' => 'failure',
						'message' => 'No results found',
				);
			}
		}
		return Response::json($output);
	}
	
	public function postMoneytransfer()
	{
		$dailyMoneytransfer = DB::table('adt_icc_transaction')->leftjoin('adt_icash_bene', 'adt_icc_transaction.icc_tranid','=', 'adt_icash_bene.icc_bentranid')->leftjoin('adt_user_details', 'adt_icc_transaction.icc_created_by','=', 'adt_user_details.UD_USER_ID')->orderby('adt_icc_transaction.icc_createdat', 'DESC')->get();
		if($dailyMoneytransfer)
		{
			foreach($dailyMoneytransfer as $ds)
			{
				$date = Commonmodel::timeconversion($ds->icc_createdat);
				$transamount = $ds->icc_tranamount;
				$fee = $ds->icc_service;
				
				$output[] = array(
						'Date' => $date,
						'Success' => $transamount,
						'Commission' => $fee,
						'Total' => $transamount + $fee,
				);
			}
		}
		else
		{
			$output = array(
					'status' => 'failure',
					'message' => 'No results found',
			);
		}
		return Response::json($output);
	}
	
	public function postSacommission()
	{
		$userid = Input::get('userid');
		$rechagedate = DB::table('adt_recharge_ledger')->leftjoin('adt_ledger_report', 'adt_recharge_ledger.rchlgr_lr_id','=', 'adt_ledger_report.lr_id_pk')->select('adt_ledger_report.lr_date','adt_ledger_report.lr_debit_amount')->get();
		foreach($rechagedate as $rd)
		{
			$rg_strdate[] = Commonmodel::timeconversion($rd->lr_date);
	
		}
		$rcgDate = array_unique($rg_strdate);
	
		if(count($rcgDate)>0)
		{
			foreach ($rcgDate as $rc)
			{
				$rg_strdate = Commonmodel::timeconversion($rc)." 00:00:00";
				$rg_enddate = Commonmodel::timeconversion($rc)." 23:59:59";
				$dailyCommission = DB::table('adt_recharge_ledger')->leftjoin('adt_ledger_report', 'adt_recharge_ledger.rchlgr_lr_id','=', 'adt_ledger_report.lr_id_pk')->whereBetween('adt_recharge_ledger.rchlgr_date',array($rg_strdate,$rg_enddate))->get();
				
				$rgamount = 0;
					$frcommision = 0;
					$drcommission = 0;
					$date=0;
					foreach($dailyCommission as $ds)
					{
						$rgamount = $rgamount + $ds->lr_debit_amount;
						$frcommision = $frcommision + $ds->rchlgr_fr_commission;
						$drcommission = $drcommission + $ds->rchlgr_d_commission;						
						$date = Commonmodel::timeconversion($ds->rchlgr_date);
					}
					$output[] = array(
							'Date' => $date,
							'Success' => $rgamount,
							'FRC_Commission' => $frcommision,
							'DIS_Commission' => $drcommission,
					);
			}	
				
		}
		else
		{
			$output = array(
					'status' => 'failure',
					'message' => 'No results found',
			);
		}
		return Response::json($output);
	}
	
	public function postUsercommission()
	{
		$userid = Input::get('userid');
		$userRole = User::where('UD_ID_PK','=',$userid)->get();
		foreach($userRole as $ur){
			$userType = $ur->UD_USER_TYPE;
		}
		if(($userType == 'FR') || ($userType == 'FRS')){
			$rechagedate = DB::table('adt_recharge_ledger')->leftjoin('adt_ledger_report', 'adt_recharge_ledger.rchlgr_lr_id','=', 'adt_ledger_report.lr_id_pk')->select('adt_ledger_report.lr_date','adt_ledger_report.lr_debit_amount')->where('adt_recharge_ledger.rchlgr_fr_id','=',$userid)->get();
			foreach($rechagedate as $rd)
			{
				$rg_strdate[] = Commonmodel::timeconversion($rd->lr_date);
			}
			$rcgDate = array_unique($rg_strdate);
			
			if(count($rcgDate)>0)
			{
				foreach ($rcgDate as $rc)
				{
					$rg_strdate = Commonmodel::timeconversion($rc)." 00:00:00";
					$rg_enddate = Commonmodel::timeconversion($rc)." 23:59:59";
					$dailyCommission = DB::table('adt_recharge_ledger')->leftjoin('adt_ledger_report', 'adt_recharge_ledger.rchlgr_lr_id','=', 'adt_ledger_report.lr_id_pk')->whereBetween('adt_recharge_ledger.rchlgr_date',array($rg_strdate,$rg_enddate))->get();
			
					$rgamount = 0;
					$frcommision = 0;
					$date=0;
					foreach($dailyCommission as $ds)
					{
						$rgamount = $rgamount + $ds->lr_debit_amount;
						$frcommision = $frcommision + $ds->rchlgr_fr_commission;
						$date = Commonmodel::timeconversion($ds->rchlgr_date);
					}
					$output[] = array(
							'Date' => $date,
							'Success' => $rgamount,
							'Commission' => $frcommision,
					);
				}
					
			}
			else
			{
				$output = array(
						'status' => 'failure',
						'message' => 'No results found',
				);
			}
		}elseif(($userType == 'SFR') || ($userType == 'SFRS')){
			$rechagedate = DB::table('adt_recharge_ledger')->leftjoin('adt_ledger_report', 'adt_recharge_ledger.rchlgr_lr_id','=', 'adt_ledger_report.lr_id_pk')->select('adt_ledger_report.lr_date','adt_ledger_report.lr_debit_amount')->where('adt_recharge_ledger.rchlgr_sfr_id','=',$userid)->get();
			$rechagedate = DB::table('adt_recharge_ledger')->leftjoin('adt_ledger_report', 'adt_recharge_ledger.rchlgr_lr_id','=', 'adt_ledger_report.lr_id_pk')->select('adt_ledger_report.lr_date','adt_ledger_report.lr_debit_amount')->where('adt_recharge_ledger.rchlgr_fr_id','=',$userid)->get();
			foreach($rechagedate as $rd)
			{
				$rg_strdate[] = Commonmodel::timeconversion($rd->lr_date);
			}
			$rcgDate = array_unique($rg_strdate);
				
			if(count($rcgDate)>0)
			{
				foreach ($rcgDate as $rc)
				{
					$rg_strdate = Commonmodel::timeconversion($rc)." 00:00:00";
					$rg_enddate = Commonmodel::timeconversion($rc)." 23:59:59";
					$dailyCommission = DB::table('adt_recharge_ledger')->leftjoin('adt_ledger_report', 'adt_recharge_ledger.rchlgr_lr_id','=', 'adt_ledger_report.lr_id_pk')->whereBetween('adt_recharge_ledger.rchlgr_date',array($rg_strdate,$rg_enddate))->get();
						
					$rgamount = 0;
					$frcommision = 0;
					$date=0;
					foreach($dailyCommission as $ds)
					{
						$rgamount = $rgamount + $ds->lr_debit_amount;
						$frcommision = $frcommision + $ds->rchlgr_sfr_commission;
						$date = Commonmodel::timeconversion($ds->rchlgr_date);
					}
					$output[] = array(
							'Date' => $date,
							'Success' => $rgamount,
							'Commission' => $frcommision,
					);
				}
					
			}
			else
			{
				$output = array(
						'status' => 'failure',
						'message' => 'No results found',
				);
			}
		}elseif(($userType == 'SD') || ($userType == 'SDS')){
			$rechagedate = DB::table('adt_recharge_ledger')->leftjoin('adt_ledger_report', 'adt_recharge_ledger.rchlgr_lr_id','=', 'adt_ledger_report.lr_id_pk')->select('adt_ledger_report.lr_date','adt_ledger_report.lr_debit_amount')->where('adt_recharge_ledger.rchlgr_sd_id','=',$userid)->get();
			$rechagedate = DB::table('adt_recharge_ledger')->leftjoin('adt_ledger_report', 'adt_recharge_ledger.rchlgr_lr_id','=', 'adt_ledger_report.lr_id_pk')->select('adt_ledger_report.lr_date','adt_ledger_report.lr_debit_amount')->where('adt_recharge_ledger.rchlgr_fr_id','=',$userid)->get();
			foreach($rechagedate as $rd)
			{
				$rg_strdate[] = Commonmodel::timeconversion($rd->lr_date);
			}
			$rcgDate = array_unique($rg_strdate);
				
			if(count($rcgDate)>0)
			{
				foreach ($rcgDate as $rc)
				{
					$rg_strdate = Commonmodel::timeconversion($rc)." 00:00:00";
					$rg_enddate = Commonmodel::timeconversion($rc)." 23:59:59";
					$dailyCommission = DB::table('adt_recharge_ledger')->leftjoin('adt_ledger_report', 'adt_recharge_ledger.rchlgr_lr_id','=', 'adt_ledger_report.lr_id_pk')->whereBetween('adt_recharge_ledger.rchlgr_date',array($rg_strdate,$rg_enddate))->get();
						
					$rgamount = 0;
					$frcommision = 0;
					$date=0;
					foreach($dailyCommission as $ds)
					{
						$rgamount = $rgamount + $ds->lr_debit_amount;
						$frcommision = $frcommision + $ds->rchlgr_sd_commission;
						$date = Commonmodel::timeconversion($ds->rchlgr_date);
					}
					$output[] = array(
							'Date' => $date,
							'Success' => $rgamount,
							'Commission' => $frcommision,
					);
				}
					
			}
			else
			{
				$output = array(
						'status' => 'failure',
						'message' => 'No results found',
				);
			}
		}elseif(($userType == 'D') || ($userType == 'DS') ){
			$rechagedate = DB::table('adt_recharge_ledger')->leftjoin('adt_ledger_report', 'adt_recharge_ledger.rchlgr_lr_id','=', 'adt_ledger_report.lr_id_pk')->select('adt_ledger_report.lr_date','adt_ledger_report.lr_debit_amount')->where('adt_recharge_ledger.rchlgr_d_id','=',$userid)->get();
			$rechagedate = DB::table('adt_recharge_ledger')->leftjoin('adt_ledger_report', 'adt_recharge_ledger.rchlgr_lr_id','=', 'adt_ledger_report.lr_id_pk')->select('adt_ledger_report.lr_date','adt_ledger_report.lr_debit_amount')->where('adt_recharge_ledger.rchlgr_fr_id','=',$userid)->get();
			foreach($rechagedate as $rd)
			{
				$rg_strdate[] = Commonmodel::timeconversion($rd->lr_date);
			}
			$rcgDate = array_unique($rg_strdate);
				
			if(count($rcgDate)>0)
			{
				foreach ($rcgDate as $rc)
				{
					$rg_strdate = Commonmodel::timeconversion($rc)." 00:00:00";
					$rg_enddate = Commonmodel::timeconversion($rc)." 23:59:59";
					$dailyCommission = DB::table('adt_recharge_ledger')->leftjoin('adt_ledger_report', 'adt_recharge_ledger.rchlgr_lr_id','=', 'adt_ledger_report.lr_id_pk')->whereBetween('adt_recharge_ledger.rchlgr_date',array($rg_strdate,$rg_enddate))->get();
						
					$rgamount = 0;
					$frcommision = 0;
					$date=0;
					foreach($dailyCommission as $ds)
					{
						$rgamount = $rgamount + $ds->lr_debit_amount;
						$frcommision = $frcommision + $ds->rchlgr_d_commission;
						$date = Commonmodel::timeconversion($ds->rchlgr_date);
					}
					$output[] = array(
							'Date' => $date,
							'Success' => $rgamount,
							'Commission' => $frcommision,
					);
				}
					
			}
			else
			{
				$output = array(
						'status' => 'failure',
						'message' => 'No results found',
				);
			}
		}elseif(($userType == 'SP') || ($userType == 'SPS')){
			$rechagedate = DB::table('adt_recharge_ledger')->leftjoin('adt_ledger_report', 'adt_recharge_ledger.rchlgr_lr_id','=', 'adt_ledger_report.lr_id_pk')->select('adt_ledger_report.lr_date','adt_ledger_report.lr_debit_amount')->where('adt_recharge_ledger.rchlgr_sp_id','=',$userid)->get();
			$rechagedate = DB::table('adt_recharge_ledger')->leftjoin('adt_ledger_report', 'adt_recharge_ledger.rchlgr_lr_id','=', 'adt_ledger_report.lr_id_pk')->select('adt_ledger_report.lr_date','adt_ledger_report.lr_debit_amount')->where('adt_recharge_ledger.rchlgr_fr_id','=',$userid)->get();
			foreach($rechagedate as $rd)
			{
				$rg_strdate[] = Commonmodel::timeconversion($rd->lr_date);
			}
			$rcgDate = array_unique($rg_strdate);
				
			if(count($rcgDate)>0)
			{
				foreach ($rcgDate as $rc)
				{
					$rg_strdate = Commonmodel::timeconversion($rc)." 00:00:00";
					$rg_enddate = Commonmodel::timeconversion($rc)." 23:59:59";
					$dailyCommission = DB::table('adt_recharge_ledger')->leftjoin('adt_ledger_report', 'adt_recharge_ledger.rchlgr_lr_id','=', 'adt_ledger_report.lr_id_pk')->whereBetween('adt_recharge_ledger.rchlgr_date',array($rg_strdate,$rg_enddate))->get();
						
					$rgamount = 0;
					$frcommision = 0;
					$date=0;
					foreach($dailyCommission as $ds)
					{
						$rgamount = $rgamount + $ds->lr_debit_amount;
						$frcommision = $frcommision + $ds->rchlgr_sp_commission;
						$date = Commonmodel::timeconversion($ds->rchlgr_date);
					}
					$output[] = array(
							'Date' => $date,
							'Success' => $rgamount,
							'Commission' => $frcommision,
					);
				}
					
			}
			else
			{
				$output = array(
						'status' => 'failure',
						'message' => 'No results found',
				);
			}
		}elseif(($userType == 'SA') || ($userType == 'SAS')){
			$rechagedate = DB::table('adt_recharge_ledger')->leftjoin('adt_ledger_report', 'adt_recharge_ledger.rchlgr_lr_id','=', 'adt_ledger_report.lr_id_pk')->select('adt_ledger_report.lr_date','adt_ledger_report.lr_debit_amount')->get();
			$rechagedate = DB::table('adt_recharge_ledger')->leftjoin('adt_ledger_report', 'adt_recharge_ledger.rchlgr_lr_id','=', 'adt_ledger_report.lr_id_pk')->select('adt_ledger_report.lr_date','adt_ledger_report.lr_debit_amount')->get();
			foreach($rechagedate as $rd)
			{
				$rg_strdate[] = Commonmodel::timeconversion($rd->lr_date);
			
			}
			$rcgDate = array_unique($rg_strdate);
			
			if(count($rcgDate)>0)
			{
				foreach ($rcgDate as $rc)
				{
					$rg_strdate = Commonmodel::timeconversion($rc)." 00:00:00";
					$rg_enddate = Commonmodel::timeconversion($rc)." 23:59:59";
					$dailyCommission = DB::table('adt_recharge_ledger')->leftjoin('adt_ledger_report', 'adt_recharge_ledger.rchlgr_lr_id','=', 'adt_ledger_report.lr_id_pk')->whereBetween('adt_recharge_ledger.rchlgr_date',array($rg_strdate,$rg_enddate))->get();
			
					$rgamount = 0;
					$frcommision = 0;
					$drcommission = 0;
					$date=0;
					foreach($dailyCommission as $ds)
					{
						$rgamount = $rgamount + $ds->lr_debit_amount;
						$frcommision = $frcommision + $ds->rchlgr_fr_commission;
						$drcommission = $drcommission + $ds->rchlgr_d_commission;
						$date = Commonmodel::timeconversion($ds->rchlgr_date);
					}
					$output[] = array(
							'Date' => $date,
							'Success' => $rgamount,
							'FRC_Commission' => $frcommision,
							'DIS_Commission' => $drcommission,
					);
				}
			
			}
			else
			{
				$output = array(
						'status' => 'failure',
						'message' => 'No results found',
				);
			}
		}
		return Response::json($output);
	}
	
	
}