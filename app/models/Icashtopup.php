<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Icashtopup extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	public  $timestamps = false;
	protected $table = 'adt_icashcard_topup';
	protected $primaryKey='icc_top_id';
	protected $fillable = array('icc_topup_amount','icc_cardno','icc_mobileno',
		'icc_securitykey','icc_servicecharge','icc_created_by','icc_created_at','icc_tranid');

}
