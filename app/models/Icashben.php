<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Icashben extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	public  $timestamps = false;
	protected $table = 'adt_icash_bene';
	protected $primaryKey='icc_ben_id_pk';
	protected $fillable = array('icc_bene_id','icc_benname','icc_benmmid',
		'icc_mobilenumber','icc_benbenkname','icc_benbranchname','icc_bencity','icc_benstate',
		'icc_benifsc','icc_benaccno','icc_createdby','icc_create_at','icc_ben_status','icc_cardno',
		'icc_bentranid');

}
