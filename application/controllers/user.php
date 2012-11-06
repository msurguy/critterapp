<?php

// User controller is responsible for showing logged in user's profile, posting a critt and following/unfollowing other users

class User_Controller extends Base_Controller {

	// Since we will use get and post, we need to make controller to be RESTful.
	public $restful = true;

	// Attach an Auth filter to every route in this controller to make sure the user is logged in
	public function __construct(){
		$this->filter('before', 'auth');
	}
	
	/*
	* get_index displays a current user's profile with paginated critts and data on critt count 
	* and followers count
	*/ 

	public function get_index()
	{
		// get all critts of current user
		$allcritts = Critt::where('user_id', '=', Auth::user()->id);

		// order the critts by date and paginate into chunks of 10 critts
		$critts = $allcritts ->order_by('updated_at', 'desc')->paginate(10);

		// count all critts
		$count = $allcritts -> count();

		// count all followers
		$followers = Follower::where('following_id','=',Auth::user()->id)->count();
	
		// make the user.profile view
		return View::make('user.profile')
			-> with('count', $count)
			-> with('followers', $followers)
			-> with('critts', $critts);
	}

	/**
	 * post_index validates input for the new critt and creates the critt in the DB
	 */
	public function post_index()
	{
		// get filtered input for the critt
		$new_critt = array(
        	'critt'		=> htmlspecialchars(Input::get('new_critt'))
	    );
	   
	    // assign validation rules
	    $rules = array(
	        'critt'		=> 'required|min:3|max:321'
	    );
	    
	    // Make the validator
	    $validation = Validator::make($new_critt, $rules);
	    if ( $validation -> fails() )
	    {   
	        return Redirect::to_action('user@index')
	                ->with('user', Auth::User())
	                ->with_errors($validation)
	                ->with_input();
	    }

	    // attach current user's ID to the user_id in critts table
	    $new_critt['user_id'] = Auth::user()->id;

	    // create the new critt and get back to the user's profile
	    $critt = new Critt($new_critt);
	    $critt->save();
	    return Redirect::to_action('user@index');
	}

	/**
	 *	post_follow is called when the "Follow" button is pressed on another user's profile, 
	 *	creating a record in the DB in the form of a primary key (user_id, following_id) defining a relationship
	 */
	public function post_follow()
	{
		// get the id of the user to be followed 
		$following_id = Input::get('id');
		// get the id of the currently logged in user
		$follower_id = Auth::user()->id;

		// define a relationship
		$new_follower = array(
			'user_id' => $follower_id, 
			'following_id' => $following_id
			);
		// save the follower reltionship
		$follower = new Follower($new_follower);
		$follower -> save();
		return Redirect::back();
	}

	/**
	 *	post_unfollow is called when the "Unfollow" button is pressed on another user's profile, 
	 *	deleting a relationship in the DB
	 */
	public function post_unfollow()
	{
		
		$following_id = Input::get('id');
		$follower_id = Auth::user()->id;

		Follower::where('user_id', '=', $follower_id)->where('following_id', '=', $following_id)->delete();
		return Redirect::back();
	}


}