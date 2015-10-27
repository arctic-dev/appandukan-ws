<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Balancedetection extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
 
	public  $timestamps = false;
	protected $table = 'adt_balance_detection';
	protected $primaryKey='bal_dtn_pk';
	protected $fillable = array('bal_usr_roll','bal_detn_amt','user_reg_count');
		

}
