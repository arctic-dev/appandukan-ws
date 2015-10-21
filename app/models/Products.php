<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Products extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
 
	public  $timestamps = false;
	protected $table = 'adt_products';
	protected $primaryKey='prod_id_pk';
	protected $fillable = array('prod_short_name','prod_full_name','prod_code','prod_catg_code','prod_status','prod_edited_at','prod_edited_by');
	

}
