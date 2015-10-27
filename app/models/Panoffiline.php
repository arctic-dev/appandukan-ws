<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Panoffiline extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
 
	public  $timestamps = false;
	protected $table = 'adt_pan_49a';
	protected $primaryKey='pan_id_pk';
	protected $fillable = array('pan_coupon_no','pan_title','pan_first_name','pan_middle_name','pan_last_name','pan_name_abbrv','pan_dob','pan_father_fname','pan_father_mname','pan_father_lname','pan_country_code','pan_area_code','pan_contact_no','pan_email_id','pan_created_at','pan_created_by','pan_refund_status','pan_refund_at','pan_refund_by');
	

}
