<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Usercreditrecharge extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
 
	public  $timestamps = false;
	protected $table = 'adt_credit_recharge';
	protected $primaryKey='cr_id_pk';
	protected $fillable = array('cr_user_id','cr_type','cr_open_bal','cr_amount','cr_fee','cr_new_bal','cr_created_by','cr_created_at');
		

}
