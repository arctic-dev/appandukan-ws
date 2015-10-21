<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Icashtransaction extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	public  $timestamps = false;
	protected $table = 'adt_icc_transaction';
	protected $primaryKey='icc_tran_id';
	protected $fillable = array('icc_cardno','icc_trantype','icc_trandesc',
		'icc_tranmobile','icc_ifsc','icc_tranamount','icc_service','icc_remark',
		'icc_benid','icc_tranid','icc_created_by','icc_createdat','icc_securitykey','icc_transtatus',
		'icc_neftstatus');

}
