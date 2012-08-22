<?php

class Others_Controller extends Base_Controller {

	public $restful = true;

	public function get_index()
	{
		return Redirect::home();
	}

	public function get_show($username)
	{

		$user_id = User::where('username','=', $username)->only('id');

		$following = null;
		$followers = 0;

		if ($user_id == null) {
			echo "This username does not exist.";
		} else {
		if (Auth::user()){
			if ($user_id == Auth::user()->id){
				return Redirect::to_action('user@index');
			} 
			$following = (Follower::where('user_id', '=', Auth::user()->id)->where('following_id','=',$user_id)->get()) ? true : false ; 
		}

		$allcritts = Critt::with('user') -> where('user_id', '=', $user_id);
		$critts = $allcritts -> order_by('created_at','desc') -> paginate(10);
		$critts_count = $allcritts -> count();
		$followers = Follower::where('following_id','=',$user_id)->count();

		return View::make('others.profile')
				-> with('username', $username)
				-> with('user_id', $user_id)
				-> with('following', $following)
				-> with('followers', $followers)
				-> with('count', $critts_count)
				-> with('critts', $critts);
		}
	}

}
