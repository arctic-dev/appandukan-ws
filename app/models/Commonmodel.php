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

	public static function fetchUserdetails($id) 
	{
		$SAbalance = DB::table('adt_user_finance')->leftjoin('adt_user_details', 'adt_user_finance.ufin_user_id','=', 'adt_user_details.UD_USER_ID')->where('adt_user_details.UD_USER_TYPE','=',$id)->get();
		if(count($SAbalance)>0)
		{
			$balnce = 0;
			foreach($SAbalance as $bal)
			{
				$balnce = $balnce + $bal->ufin_main_balance;
			}
			return $balnce;
		}
		else
		{
			return 0;
		}
	}

	public static function fetchRechargedetails($userid)
	{
		$RegDetails = DB::table('adt_recharge_details')->leftjoin('adt_user_details', 'adt_recharge_details.rd_created_by','=', 'adt_user_details.UD_USER_ID')->where('adt_recharge_details.rd_created_by','=',$userid)->get();
		if(count($RegDetails)>0)
		{
			$amnt = 0;
			foreach($RegDetails as $bal)
			{
				$amnt = $amnt + $bal->rd_amount;
			}
			return $amnt;
		}
		else
		{
			return 0;
		}
	}

	public static function arr($XML)
	{
		$xml_parser = xml_parser_create();
		xml_parse_into_struct($xml_parser, $XML, $vals);
		xml_parser_free($xml_parser);
		// wyznaczamy tablice z powtarzajacymi sie tagami na tym samym poziomie
		$_tmp='';
		foreach ($vals as $xml_elem) {
			$x_tag=$xml_elem['tag'];
			$x_level=$xml_elem['level'];
			$x_type=$xml_elem['type'];
			if ($x_level!=1 && $x_type == 'close') {
				if (isset($multi_key[$x_tag][$x_level]))
					$multi_key[$x_tag][$x_level]=1;
				else
					$multi_key[$x_tag][$x_level]=0;
			}
			if ($x_level!=1 && $x_type == 'complete') {
				if ($_tmp==$x_tag)
					$multi_key[$x_tag][$x_level]=1;
				$_tmp=$x_tag;
			}
		}
		// jedziemy po tablicy
		foreach ($vals as $xml_elem) {
			$x_tag=$xml_elem['tag'];
			$x_level=$xml_elem['level'];
			$x_type=$xml_elem['type'];
			if ($x_type == 'open')
				$level[$x_level] = $x_tag;
			$start_level = 1;
			$php_stmt = '$xml_array';
			if ($x_type=='close' && $x_level!=1)
				$multi_key[$x_tag][$x_level]++;
			while ($start_level < $x_level) {
				$php_stmt .= '[$level['.$start_level.']]';
				if (isset($multi_key[$level[$start_level]][$start_level]) && $multi_key[$level[$start_level]][$start_level])
					$php_stmt .= '['.($multi_key[$level[$start_level]][$start_level]-1).']';
				$start_level++;
			}
			$add='';
			if (isset($multi_key[$x_tag][$x_level]) && $multi_key[$x_tag][$x_level] && ($x_type=='open' || $x_type=='complete')) {
				if (!isset($multi_key2[$x_tag][$x_level]))
					$multi_key2[$x_tag][$x_level]=0;
				else
					$multi_key2[$x_tag][$x_level]++;
				$add='['.$multi_key2[$x_tag][$x_level].']';
			}
			if (isset($xml_elem['value']) && trim($xml_elem['value'])!='' && !array_key_exists('attributes', $xml_elem)) {
				if ($x_type == 'open')
					$php_stmt_main=$php_stmt.'[$x_type]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
				else
					$php_stmt_main=$php_stmt.'[$x_tag]'.$add.' = $xml_elem[\'value\'];';
				eval($php_stmt_main);
			}
			if (array_key_exists('attributes', $xml_elem)) {
				if (isset($xml_elem['value'])) {
					$php_stmt_main=$php_stmt.'[$x_tag]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
					eval($php_stmt_main);
				}
				foreach ($xml_elem['attributes'] as $key=>$value) {
					$php_stmt_att=$php_stmt.'[$x_tag]'.$add.'[$key] = $value;';
					eval($php_stmt_att);
				}
			}
		}
		return $xml_array;
	}
	

	 
}
