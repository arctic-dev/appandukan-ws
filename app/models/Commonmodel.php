<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Commonmodel extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;
 
	 public  $timestamps = false;
 

	
	
	public static function currenttime()
	{
		date_default_timezone_set("Asia/Kolkata");
		//$date= date('Y-m-d H:i:s'); //Returns IST 0000-00-00 00:00:00
   		$time=  date(' H:i:s'); //Returns IST 
		return $time;
	}
	
	public static function currentdate()
	{
		date_default_timezone_set("Asia/Kolkata");
		$date= date('Y-m-d');
		return $date;
	}
	
	public static function dateandtime()
	{
		date_default_timezone_set("Asia/Kolkata");
		$dateandtime= date('Y-m-d H:i:s'); //Returns IST 0000-00-00 00:00:00
		return $dateandtime;
	}
	
	public static function timeconversion($date)
	{
		$newdate= date('Y-m-d', strtotime($date));
		return $newdate;
	}
	
	public static function detectyear($date)
	{
		$newdate= date('Y', strtotime($date));
		return $newdate;
	}
	
	public static function getproducts($productcode)
	{
		$input=array();
		$products=DB::table('adt_products')->where('prod_code',$productcode)->get();
		
			foreach($products as $catcodes)
			{
				$input=array
				(
					'catgCode'=>$catcodes->prod_catg_code,
					'prodCode'=>$catcodes->prod_code,
					'prodStatus'=>"".$catcodes->prod_status."",
					'fullName'=>$catcodes->prod_full_name,
					'shortName'=>$catcodes->prod_short_name,
				);
			}
			
		return $input;
		
		
	}
	
	public static function getcatcode($productcode)
	{
	
		$catcode=DB::table('adt_products')->where('prod_code',$productcode)->pluck('prod_catg_code');
		return $catcode;
	}
	
	public static function getcategory($category)
	{
		$input=array();
		$categorydetails=DB::table('adt_categories')->where('catg_code',$category)->where('catg_status','=','1')->get();
		foreach($categorydetails as $categorydetailss)
		{
			$input=array
			(
			'catgCode'=>$categorydetailss->catg_code,
			'fullName'=>$categorydetailss->catg_full_name,
			);
			
		}
		
		return $input;
	}
	
	
	public static function converarray2to1($input_array) {
    $output_array = array();

    for ($i = 0; $i < count($input_array); $i++) {
      for ($j = 0; $j < count($input_array[$i]); $j++) {
        $output_array[] =$input_array[$i][$j]; 
      }
    }

    return $output_array;
	}
	
	

	
	
	public static function is_multi2($a) {
    foreach ($a as $v) {
        if (is_array($v)) return true;
    }
    return false;
}



	
	

	 
}
