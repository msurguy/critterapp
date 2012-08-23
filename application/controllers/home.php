<?php

/**
 *  Home controller is responsible for showing the home page, processing input from the home page,
 *	Showing the login form and processing the login form, and logout.
 */

class Home_Controller extends Base_Controller {

	// Since we will use get and post, we need to make controller to be RESTful.
	public $restful = true;

	public function __construct() 
    {
        $this->filter('before', 'csrf')->on('post');
    }

	/**
	 *  get_index checks if the user is logged in already, if not, it shows a default home page with options to 
	 *	login or sign up, if logged in it will show a critter feed from all other users in the system
	 */
	public function get_index()
	{
		if (Auth::guest()){
			return View::make('home.index');
		} else {
			$critts = Critt::with('user') -> order_by('created_at','desc') -> paginate(10) ;
			$count = Critt::count() ;
			return View::make('home.critterfeed')
				-> with('count', $count)
				-> with('critts', $critts);
		}
	}

	/**
	 *  post_index processes the sign up form on the home page and saves new user's credentials into the database
	 */
	public function post_index()
	{
		// Get the input fields from the form into an array
		$new_user = array(
	        'name'		=> Input::get('name'),
	        'username'  => Input::get('new_username'),
	        'password'  => Input::get('new_password')
    	);
   
   		// Create the array of validation rules
    	$rules = array(
	        'name'		=>	'required|min:3|max:255',
	        'username'  =>	'required|min:3|max:128|alpha_dash|unique:users',
	        'password'	=>	'required|min:3|max:128'
    	);
    
    	// Make the validator
	    $validation = Validator::make($new_user, $rules);
	    if ( $validation -> fails() )
	    {   
	        return Redirect::home()
	                ->with('user', Auth::user())
	                ->with_errors($validation)
	                ->with_input('except', array('new_password'));
	    }
	    // hash the password
	    $new_user['password'] = Hash::make($new_user['password']);

	    // create new user and redirect to the login page with a success message
	    $user = new User($new_user);
	    $user->save();
	    return Redirect::to_action('home@login') -> with('success_message', true);
	}

	/**
	 *  get_login shows the login page
	 */
	public function get_login()
	{
    	return View::make('home.login');
	}

	/**
	 *  post_login processes the login page form and loggs the user in if the credentials match the ones in the database
	 */
	public function post_login()
	{
		
		$remember = Input::get('remember');
 		$credentials = array(
 			'username' => Input::get('username'), 
 			'password' => Input::get('password'),
 			'remember' => !empty($remember) ? $remember : null
 		);
 		
    	if (Auth::attempt( $credentials ))
		{
		 	return Redirect::to_action('user@index');
		}else{
			return Redirect::to_action('home@login')
			-> with_input('only', array('new_username')) 
			-> with('login_errors', true);
        }
	}

	// get_logout logs the user out by clearing the session and redirects to login page with a logout message
	public function get_logout()
	{
		Auth::logout();
		return Redirect::to_action('home@login') -> with('logout_message', true);
	}

}