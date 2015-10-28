<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class State extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
 
	public  $timestamps = false;
	protected $table = 'adt_state';
	protected $primaryKey='adt_state_id';
	protected $fillable = array('adt_state_name','adt_state_count','adt_state_status');
		

}
