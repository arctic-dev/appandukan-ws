<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Comission extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	public  $timestamps = false;
	protected $table = 'adt_ledger_report';
	protected $primaryKey='lr_id_pk';
	protected $fillable = array('lr_date','lr_trans_type','lr_comment','lr_debit_amount','lr_credit_amount','lr_post_balance','lr_created_by','lr_prod_code');

}
