<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Icash extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	public  $timestamps = false;
	protected $table = 'adt_icashcard_user';
	protected $primaryKey='icc_id_pk';
	protected $fillable = array('icc_tran_id','icc_kyc','icc_userId','icc_username',
		'icc_usermname','icc_userlname','icc_usermothername','icc_user_dob','icc_useremail',
		'icc_usermobile','icc_userbalance','icc_usercity','icc_useraddress','icc_userpincode','icc_useridprooftype',
		'icc_useridproof','icc_useridproofurl','icc_useraddrprooftype','icc_userproof','icc_userproofurl','icc_created_by',
		'icc_created_at','icc_usercardno','icc_mmid','icc_userkycstatus','icc_topuplimit','icc_facelimit',
		'icc_consumedlimit','icc_securitykey','icc_userstate');

}
