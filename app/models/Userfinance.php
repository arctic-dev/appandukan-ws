<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Userfinance extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
 
	public  $timestamps = false;
	protected $table = 'adt_user_finance';
	protected $primaryKey='ufin_id_pk';
	protected $fillable = array('ufin_user_id_pk_fk','ufin_user_id','ufin_main_balance',
		'ufin_comm_earned','ufin_total_credited','ufin_total_used','ufin_total_comm',
		'ufin_fee_perc','ufin_edited_at','ufin_edited_by','ufin_icash_balance','ufin_icash_credited','ufin_icash_used');
		

}
