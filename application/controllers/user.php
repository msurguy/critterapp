<?php

class User_Controller extends Base_Controller {

	public $restful = true;

	public function __construct(){

	    	$this->filter('before', 'auth');
	    	//$this->data['user'] = Auth::user();
	}

	public function auth(){
	    	if (Auth::guest()) return Redirect::to_action('home@login');
	}

	public function get_index()
	{
		$allcritts = Critt::where('user_id', '=', Auth::user()->id);
		$critts = $allcritts ->order_by('updated_at', 'desc')->paginate(10);
		$count = $allcritts -> count();
		$followers = Follower::where('following_id','=',Auth::user()->id)->count();
	
		return View::make('user.profile')
			-> with('count', $count)
			-> with('followers', $followers)
			-> with('critts', $critts);
	}

	public function post_index()
	{
		$new_critt = array(
        	'critt'		=> Input::get('new_critt')
	    );
	   
	    $rules = array(
	        'critt'		=> 'required|min:3|max:321'
	    );
	    
	    $validation = Validator::make($new_critt, $rules);
	    if ( $validation -> fails() )
	    {   
	        return Redirect::to_action('user@index')
	                ->with('user', Auth::User())
	                ->with_errors($validation)
	                ->with_input();
	    }
	    // create the new critt after passing validation
	    $new_critt['user_id'] = Auth::user()->id;
	    $critt = new Critt($new_critt);
	    $critt->save();
	    return Redirect::to_action('user@index');
	}

	public function post_follow()
	{
		$following_id = Input::get('id');
		$follower_id = Auth::user()->id;
		$new_follower = array(
			'user_id' => $follower_id, 
			'following_id' => $following_id
			);
		$follower = new Follower($new_follower);
		$follower -> save();
		return Redirect::back();
	}

	public function post_unfollow()
	{
		$following_id = Input::get('id');
		$follower_id = Auth::user()->id;

		Follower::where('user_id', '=', $follower_id)->where('following_id', '=', $following_id)->delete();
		return Redirect::back();
	}


}