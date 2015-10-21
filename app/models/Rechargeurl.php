<?php

class Rechargeurl {

 public static function authcode()
 {	
	 $authcode="8519fe9d959b425192";
	 return $authcode;
 }
 
 public static function rechargehistory($data)
 {
        $url = "http://localhost/restapi/recharge/history";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));                       
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $responsepurge = curl_exec($ch);
        return json_decode($responsepurge);

 }
 
 public static function rechargenumber($productid,$mobno,$amt,$id)
 {
  
		$authcode=Rechargeurl::authcode();
		$product=$productid;
		$MobileNumber=$mobno;
		$Amount=$amt;
		$RequestId=$id;
		$url = "http://103.29.232.110:8089/Ezypaywebservice/PushRequest.aspx?AuthorisationCode=".$authcode."&product=".$product."&MobileNumber=".$MobileNumber."&Amount=".$Amount."&RequestId=".$RequestId."";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));                       
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responsepurge = curl_exec($ch);
		$responce=explode('~',$responsepurge);
        return $responce;

 }
 
 
 public static function rechargepostpaidnumber($productid,$mobno,$amt,$id)
 {

		$authcode=Rechargeurl::authcode();
		$product=$productid;
		$MobileNumber=$mobno;
		$Amount=$amt;
		$RequestId=$id;
		$circle=0;
		$accountno=0;
		$stdcode=0;
		$url = "http://103.29.232.110:8089/Ezypaywebservice/postpaidpush.aspx?AuthorisationCode=".$authcode."&product=".$product."&MobileNumber=".$MobileNumber."&Amount=".$Amount."&RequestId=".$RequestId."&Circle=".$circle."&AcountNo=".$accountno."&StdCode=".$stdcode."";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));                       
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responsepurge = curl_exec($ch);
		$responce=explode('~',$responsepurge);
        return $responce;

 }
 
 
  
 public static function balance()
 {
  
		$authcode=Rechargeurl::authcode();
		$url = "http://103.29.232.110:8089/Ezypaywebservice/GetBalance.aspx?AuthorisationCode=".$authcode."";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));                       
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responsepurge = curl_exec($ch);
		$responce=explode('~',$responsepurge);
        return $responce;

 }
 
 
  public static function status()
 {
  		$RequestId=0;
		$authcode=Rechargeurl::authcode();
		$url = "http://103.29.232.110:8030/Ezypaywebservice/transactionEnquiry.aspx?AuthorisationCode=".$authcode."&RequestId=".$RequestId."";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));                       
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responsepurge = curl_exec($ch);
        return $responsepurge;

 }
 
 
 

 
        
}