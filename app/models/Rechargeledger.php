<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Rechargeledger extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	public  $timestamps = false;
	protected $table = 'adt_recharge_ledger';
	protected $primaryKey='rchlgr_id_pk';
	protected $fillable = array('rchlgr_lr_id', 'rchlgr_date', 'rchlgr_sa_id', 'rchlgr_sa_commission', 'rchlgr_sp_id', 'rchlgr_sp_commission', 'rchlgr_sd_id', 'rchlgr_sd_commission', 'rchlgr_d_id', 'rchlgr_d_commission', 'rchlgr_fr_id', 'rchlgr_fr_commission', 'rchlgr_sfr_id', 'rchlgr_sfr_commission', 'rchlgr_id_pk');

}
?>