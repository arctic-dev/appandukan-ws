<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Rechargedetails extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
 
	public  $timestamps = false;
	protected $table = 'adt_recharge_details';
	protected $primaryKey='rd_id_pk';
	protected $fillable = array('rd_prod_code','rd_service_provider','rd_number','rd_amount','rd_sfcommission','rd_commission','rd_dcommission','rd_created_at','rd_created_by','rd_created_type','rd_result','rd_trans_id','rd_auth_code','rd_client_ip','rd_provider','rd_refund_status','rd_refund_at');
	

}
