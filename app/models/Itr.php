<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Itr extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
 
	public  $timestamps = false;
	protected $table = 'adt_itr_forms';
	protected $primaryKey='itr_id_pk';
	protected $fillable = array('itr_name','itr_pan','itr_bankstatement','itr_form16','itr_tdscertificate','itr_addrproof','itr_previtr','itr_bankname','itr_bankacctype','itr_bankaccno','itr_bankifsc','itr_fyear','itr_mobileno','itr_email','itr_createdby','itr_createdat','itr_status','itr_clientip');
		

}
