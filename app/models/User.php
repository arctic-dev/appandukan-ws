<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
 
	public  $timestamps = false;
	protected $table = 'adt_user_details';
	protected $primaryKey='UD_ID_PK';
	protected $fillable = array('UD_USER_ID','UD_USER_KEY','UD_USER_NAME','UD_USER_TYPE','UD_PARENT_ID','UD_USER_EMAIL','UD_USER_MOBILE','UD_USER_ADDRESS1','UD_USER_ADDRESS2','UD_USER_CITY','UD_USER_STATE','UD_USER_PINCODE','UD_USER_STATUS','UD_USER_SLUG','UD_CREATED_AT','UD_CREATED_BY','UD_EDITED_AT','UD_EDITED_BY');
		

}
