<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Userproductaccess extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
 
	public  $timestamps = false;
	protected $table = 'adt_user_product_access';
	protected $primaryKey='upa_id_pk';
	protected $fillable = array('upa_prod_code','upa_ud_user_id','upa_access_status','upa_created_at','upa_created_by','upa_edited_at','upa_edited_by','upa_photo');
	

}
