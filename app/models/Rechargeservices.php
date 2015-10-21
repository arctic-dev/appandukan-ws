<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Rechargeservices extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	public  $timestamps = false;
	protected $table = 'adt_recharge_mast';
	protected $primaryKey='rm_id_pk';
	protected $fillable = array('rm_name','rm_prod_code','rm_status',
		'rm_provider','rm_cyberplat_pa','rm_cyberplat_pr','rm_cyberplat_ps','rm_ezypay_opcode',
		'rm_ezypay_prcode','rm_commission','rm_dcommission','rm_scommission','rm_sfcommission','rm_fcommission',
		'rm_sdcommission','rm_rll_code');

}
