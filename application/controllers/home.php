<?php

class Home_Controller extends Base_Controller {

	public $restful = true;

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

	public function post_index()
	{
		$new_user = array(
	        'name'		=> Input::get('name'),
	        'username'  => Input::get('new_username'),
	        'password'  => Input::get('new_password')
    	);
   
    	$rules = array(
	        'name'		=>	'required|min:3|max:255',
	        'username'  =>	'required|min:3|max:128|alpha_dash|unique:users',
	        'password'	=>	'required|min:3|max:128'
    	);
    
	    $validation = Validator::make($new_user, $rules);
	    if ( $validation -> fails() )
	    {   
	        return Redirect::home()
	                ->with('user', Auth::user())
	                ->with_errors($validation)
	                ->with_input('except', array('new_password'));
	    }
	    $new_user['password'] = Hash::make($new_user['password']);
	    $user = new User($new_user);
	    $user->save();
	    return Redirect::to_action('home@login') -> with('success_message', true);
	}

	public function get_login()
	{
    	return View::make('home.login');
	}

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

	public function get_logout()
	{
		Auth::logout();
		return Redirect::to_action('home@login') -> with('logout_message', true);
	}

}