<?php

// The routing is rather simple.
// First we match any request to "others/username" and redirect to action "show" of controller "others"
// Then all other routes are automagically assigned by Controller::detect(), no need to create them by hand,  
// Laravel looks at our controllers and knows that "home" controller is responsible for "home" routes, 
// "others" controller is responsible for "others" route, and etc.

Route::any('others/(:any)', 'others@show');
Route::controller(Controller::detect());

// Everything below this line is default except the Auth filter that basically redirects the user to the login
// in case of unauthorized access

Event::listen('404', function()
{
	return Response::error('404');
});

Event::listen('500', function()
{
	return Response::error('500');
});


Route::filter('before', function()
{
	// Do stuff before every request to your application...
});

Route::filter('after', function($response)
{
	// Do stuff after every request to your application...
});

Route::filter('csrf', function()
{
	if (Request::forged()) return Response::error('500');
});

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::to_action('home@login');
});