<?php

/**
 *  "Others" controller is responsible for showing the profiles of other users with info about their profile
 */

class Others_Controller extends Base_Controller {
	
	// if the user types in /others in the URL, redirect to the home page which shows the feed or the login/signup page
	public function action_index()
	{
		return Redirect::home();
	}

	/**
	 *  get_show takes in a username, finds the user's id from the username, gets the information about the user from the 
	 *	followers and critts table and outputs it into the others.profile view
	 */
	public function action_show($username)
	{
		// we get the user's id that matches the username
		$user_id = User::where('username','=', $username)->only('id');

		// declare some default values for variables
		$following = null;
		$followers = 0;

		// if the username is not found, display an error
		if ($user_id == null) {
			echo "This username does not exist.";
		} else {
			if (Auth::user()){
				// if the user tries to go to his/her own profile, redirect to user's profile action.
				if ($user_id == Auth::user()->id){
					return Redirect::to_action('user@index');
				} 
				// check if the current user is already following $username
				$following = (Follower::where('user_id', '=', Auth::user()->id)->where('following_id','=',$user_id)->get()) ? true : false ; 
			}

			// eager load the critts with user data
			$allcritts = Critt::with('user') -> where('user_id', '=', $user_id);
			// order the critts and split them in chunks of 10 per page
			$critts = $allcritts -> order_by('created_at','desc') -> paginate(10);
			// count the critts
			$critts_count = $allcritts -> count();
			// count the followers
			$followers = Follower::where('following_id','=',$user_id)->count();

			// bind data to the view
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
