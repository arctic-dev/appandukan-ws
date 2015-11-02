<?php

class LedgerreportController extends BaseController {

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
	
	public function postReportledger()
	{
		$postdata=file_get_contents("php://input");
		if(!empty($postdata))
		{
			$getlegersub='';
			$userIdPk=Input::get('userIdPk');
			$userId=Input::get('userId');
			$userType=Input::get('userType');
			if($userIdPk!=''||$userId!='')
			{
				
				if($userType=='SA'||$userType=='SAS')
				{
					$getleger=Ledgerreport::leftjoin('adt_user_details','adt_ledger_report.lr_created_by','=','adt_user_details.UD_USER_ID')->get();
				}
				elseif($userType=='SPS'||$userType=='SDS'||$userType=='DS'||$userType=='FRS'||$userType=='FRS')
				{
					$getleger=Ledgerreport::leftjoin('adt_user_details','adt_ledger_report.lr_created_by','=','adt_user_details.UD_USER_ID')->where('adt_user_details.UD_USER_TYPE','=',$userType)->where('adt_ledger_report.lr_created_by','=',$userId)->get();
				}
				elseif($userType=='SP')
				{
					
					$getleger=Ledgerreport::leftjoin('adt_user_details','adt_ledger_report.lr_created_by','=','adt_user_details.UD_USER_ID')->where('adt_user_details.UD_USER_TYPE','=',$userType)->where('adt_ledger_report.lr_created_by','=',$userId)->get();
					$getsubuserid=User::select('UD_USER_ID')->where('UD_USER_TYPE','=','SPS')->where('UD_PARENT_ID','=',$userIdPk)->get();
					if(count($getsubuserid)>0)
					{
						foreach($getsubuserid as $getsubuserids)
						{
							$getsubuseridss[]=$getsubuserids['UD_USER_ID'];
						}
						if(count($getsubuseridss)==1)
						{
							$getlegersub=Ledgerreport::leftjoin('adt_user_details','adt_ledger_report.lr_created_by','=','adt_user_details.UD_USER_ID')->where('adt_ledger_report.lr_created_by',$getsubuseridss)->get();
						}
						elseif(count($getsubuseridss)>1)
						{
							$getlegersub=Ledgerreport::leftjoin('adt_user_details','adt_ledger_report.lr_created_by','=','adt_user_details.UD_USER_ID')->whereBetween('adt_ledger_report.lr_created_by',$getsubuseridss)->get();
						}
						
					
					}
					
					
				}
				elseif($userType=='SD')
				{
					$getleger=Ledgerreport::leftjoin('adt_user_details','adt_ledger_report.lr_created_by','=','adt_user_details.UD_USER_ID')->where('adt_user_details.UD_USER_TYPE','=',$userType)->where('adt_ledger_report.lr_created_by','=',$userId)->get();
					$getsubuserid=User::select('UD_USER_ID')->where('UD_USER_TYPE','=','SDS')->where('UD_PARENT_ID','=',$userIdPk)->get();
					if(count($getsubuserid)>0)
					{
						foreach($getsubuserid as $getsubuserids)
						{
							$getsubuseridss[]=$getsubuserids['UD_USER_ID'];
						}
						if(count($getsubuseridss)==1)
						{
						$getlegersub=Ledgerreport::leftjoin('adt_user_details','adt_ledger_report.lr_created_by','=','adt_user_details.UD_USER_ID')->where('adt_ledger_report.lr_created_by',$getsubuseridss)->get();
						}
						elseif(count($getsubuseridss)>1)
						{
							$getlegersub=Ledgerreport::leftjoin('adt_user_details','adt_ledger_report.lr_created_by','=','adt_user_details.UD_USER_ID')->whereBetween('adt_ledger_report.lr_created_by',$getsubuseridss)->get();
						}
					}
				}
				elseif($userType=='D')
				{
					$getleger=Ledgerreport::leftjoin('adt_user_details','adt_ledger_report.lr_created_by','=','adt_user_details.UD_USER_ID')->where('adt_user_details.UD_USER_TYPE','=',$userType)->where('adt_ledger_report.lr_created_by','=',$userId)->get();
					$getsubuserid=User::select('UD_USER_ID')->where('UD_USER_TYPE','=','DS')->where('UD_PARENT_ID','=',$userIdPk)->get();
					if(count($getsubuserid)>0)
					{
						foreach($getsubuserid as $getsubuserids)
						{
							$getsubuseridss[]=$getsubuserids['UD_USER_ID'];
						}
						if(count($getsubuseridss)==1)
						{
						$getlegersub=Ledgerreport::leftjoin('adt_user_details','adt_ledger_report.lr_created_by','=','adt_user_details.UD_USER_ID')->where('adt_ledger_report.lr_created_by',$getsubuseridss)->get();
						}
						elseif(count($getsubuseridss)>1)
						{
							$getlegersub=Ledgerreport::leftjoin('adt_user_details','adt_ledger_report.lr_created_by','=','adt_user_details.UD_USER_ID')->whereBetween('adt_ledger_report.lr_created_by',$getsubuseridss)->get();
						}
							
					
					}
				}
				elseif($userType=='FR')
				{
					$getleger=Ledgerreport::leftjoin('adt_user_details','adt_ledger_report.lr_created_by','=','adt_user_details.UD_USER_ID')->where('adt_user_details.UD_USER_TYPE','=',$userType)->where('adt_ledger_report.lr_created_by','=',$userId)->get();
					$getsubuserid=User::select('UD_USER_ID')->where('UD_USER_TYPE','=','FRS')->where('UD_PARENT_ID','=',$userIdPk)->get();
					if(count($getsubuserid)>0)
					{
						foreach($getsubuserid as $getsubuserids)
						{
							$getsubuseridss[]=$getsubuserids['UD_USER_ID'];
						}
						
						if(count($getsubuseridss)==1)
						{
						$getlegersub=Ledgerreport::leftjoin('adt_user_details','adt_ledger_report.lr_created_by','=','adt_user_details.UD_USER_ID')->where('adt_ledger_report.lr_created_by',$getsubuseridss)->get();
						}
						elseif(count($getsubuseridss)>1)
						{
							$getlegersub=Ledgerreport::leftjoin('adt_user_details','adt_ledger_report.lr_created_by','=','adt_user_details.UD_USER_ID')->whereBetween('adt_ledger_report.lr_created_by',$getsubuseridss)->get();
						}
					
					}
				}
				elseif($userType=='SFR')
				{
					
					$getleger=Ledgerreport::leftjoin('adt_user_details','adt_ledger_report.lr_created_by','=','adt_user_details.UD_USER_ID')->where('adt_user_details.UD_USER_TYPE','=',$userType)->where('adt_ledger_report.lr_created_by','=',$userId)->get();
					$getsubuserid=User::select('UD_USER_ID')->where('UD_USER_TYPE','=','SFRS')->where('UD_PARENT_ID','=',$userIdPk)->get();
					if(count($getsubuserid)>0)
					{
						foreach($getsubuserid as $getsubuserids)
						{
							$getsubuseridss[]=$getsubuserids['UD_USER_ID'];
						}
						
						if(count($getsubuseridss)==1)
						{
							$getlegersub=Ledgerreport::leftjoin('adt_user_details','adt_ledger_report.lr_created_by','=','adt_user_details.UD_USER_ID')->where('adt_ledger_report.lr_created_by',$getsubuseridss)->get();
						}
						elseif(count($getsubuseridss)>1)
						{
							$getlegersub=Ledgerreport::leftjoin('adt_user_details','adt_ledger_report.lr_created_by','=','adt_user_details.UD_USER_ID')->whereBetween('adt_ledger_report.lr_created_by',$getsubuseridss)->get();
						}
						
						
						
							
					}
				}
					
					
					
				elseif($userType=='SPS')
				{
					$getleger=Ledgerreport::leftjoin('adt_user_details','adt_ledger_report.lr_created_by','=','adt_user_details.UD_USER_ID')->where('adt_user_details.UD_USER_TYPE','=',$userType)->where('adt_ledger_report.lr_created_by','=',$userId)->get();
					$getsubuserid=User::select('UD_USER_ID')->where('UD_USER_TYPE','=','SPS')->where('UD_PARENT_ID','=',$userIdPk)->get();
					if(count($getsubuserid)>0)
					{
						foreach($getsubuserid as $getsubuserids)
						{
							$getsubuseridss[]=$getsubuserids['UD_USER_ID'];
						}
						
						if(count($getsubuseridss)==1)
						{
							$getlegersub=Ledgerreport::leftjoin('adt_user_details','adt_ledger_report.lr_created_by','=','adt_user_details.UD_USER_ID')->where('adt_ledger_report.lr_created_by',$getsubuseridss)->get();
						}
						elseif(count($getsubuseridss)>1)
						{
							$getlegersub=Ledgerreport::leftjoin('adt_user_details','adt_ledger_report.lr_created_by','=','adt_user_details.UD_USER_ID')->whereBetween('adt_ledger_report.lr_created_by',$getsubuseridss)->get();
						}
						
							
					}
				}	
				
				
				
				
				if(count($getleger)>0||$getlegersub!='')
				{
					
					
					
					if(!empty($getlegersub))
					{
						return Response::json(array('subuser' => $getlegersub, 'mainuser' => $getleger));
					}
					else
					{
						return Response::json(array('mainuser' => $getleger));
					}
					
					
				}
				else
				{
					return Response::json(array('status' => 'failure', 'message' => 'No Ledger Reports for this ID'));
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
	
	
	
	
	
	

	public function postDailybusiness()
	{
		$postdata=file_get_contents("php://input");
		if(!empty($postdata))
		{
			$userIdPk=Input::get('userIdPk');
			$userId=Input::get('userId');
			$date=Input::get('lr_date');
			$statdate=$date." 00:00:00";
			$enddate=$date." 23:59:59";
			if($userIdPk!=''||$userId!='')
			{
				$legcount=Ledgerreport::where('lr_created_by','=',$userId)->whereBetween('lr_date', array($statdate,$enddate))->get();
				if(count($legcount)>0)
				{
					$dbamout=0;
					$cramout=0;
					$pbamout=0;
					foreach($legcount as $legcount)
					{
						$dbamout=$dbamout+$legcount->lr_debit_amount;
						$cramout=$cramout+$legcount->lr_credit_amount;
						$pbamout=$pbamout+$legcount->lr_post_balance;
					}
					
					$input=array
					(
						'debitamount'=>$dbamout,
						'creditamount'=>$cramout,
						'postbalance'=>$pbamout,
					);
					return Response::json($input);
				}
				else
				{
					return Response::json(array('status' => 'failure', 'message' => 'No Ledger Reports for this ID'));
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
	
	
	
	public function postDailycomission()
	{
		$postdata=file_get_contents("php://input");
		if(!empty($postdata))
		{
			$userIdPk=Input::get('userIdPk');
			$userId=Input::get('userId');
			$date=Input::get('lr_date');
			$statdate=$date." 00:00:00";
			$enddate=$date." 23:59:59";
			if($userIdPk!=''||$userId!='')
			{
				$legcount=Ledgerreport::where('lr_created_by','=',$userId)->whereBetween('lr_date', array($statdate,$enddate))->get();
				if(count($legcount)>0)
				{
					$dbamout=0;
					$cramout=0;
					$pbamout=0;
					foreach($legcount as $legcount)
					{
						$dbamout=$dbamout+$legcount->lr_debit_amount;
						$cramout=$cramout+$legcount->lr_credit_amount;
						$pbamout=$pbamout+$legcount->lr_post_balance;
					}
						
					$input=array
					(
							'debitamount'=>$dbamout,
							'creditamount'=>$cramout,
							'postbalance'=>$pbamout,
					);
					return Response::json($input);
				}
				else
				{
					return Response::json(array('status' => 'failure', 'message' => 'No Ledger Reports for this ID'));
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
	
	
	
		
}
