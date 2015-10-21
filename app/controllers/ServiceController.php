<?php

class ServiceController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function getIndex()
	{
		$output=array(
			'status' => 'success', 
			'message' =>'data fetched' , 
			'data' =>'hi' , 
			);
		return Response::json($output);
	}
	public function postService()
	{
		$postdata=Input::all();
		$rules=array(
			"prodCode"=>"required");
		$message=array("prodCode.required"=>"please send a product");

		$validator=Validator::make($postdata,$rules,$message);
		if($validator->fails())
		{
			return Response::json(array('status'=>"failed",'message'=>$validator->errors()));
		}
		else
		{
			$response=Rechargeservices::where('rm_prod_code',Input::get('prodCode'))->get();
			//print_r($response);
			return Response::json(array('status'=>"success",'serviceprepaid'=>$response));
		}
	}
	public function postCommission()
 {
 $postdata = file_get_contents('php://input');
 $id = Input::get('id');
 $type = Input::get('type');
 $postdata = array(
 $type => Input::get('value'),
 );
  if($id == '' && $type == '' && $value == '')
  {
   return Response::json(array('status'=>"failed",'message'=>"ID required"));
  }
  else
  {
   if($postdata){
    $res = Rechargeservices::find($id)->update($postdata);
    return Response::json(array('status'=>"Success", 'message'=>"Updated Successfully"));
   }else{
    return Response::json(array('status'=>"Failed", 'message'=>"Failed to update"));
   }
  }
 }

}
