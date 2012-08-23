@layout('templates.template')

@section('content')
<div class="span8 offset2">
    <div class="row artwork hidden-phone" style="font-size:80px; text-align: center;">
		<div class="span2"><i class="icon-group"></i></div>
		<div class="span2"><i class="icon-comments-alt"></i></div>
		<div class="span2"><i class="icon-globe"></i></div>
		<div class="span2"><i class="icon-thumbs-up"></i></div>
	</div>
	<hr />
	<div class="row">
		<div class="span4">
		<p class="lead"> Sign in</p>
			{{ Form::open('home/login', 'POST',array('class'=>'well')); }}
			{{ Form::token() }}
			@if (Session::has('login_errors'))
			{{ Alert::error("Username or password incorrect.") }}
			@endif
			{{ Form::text('username', Input::old('username'), array('class' => 'span3', 'placeholder' => 'Username'));}}
			{{ Form::password('password', array('class' => 'span3', 'placeholder' => 'Password'));}}
			{{ Form::labelled_checkbox('remember', 'Remember Me');}}
			{{ Form::submit('Login to Critter', array('class'=>'btn-info'));}}
			{{ Form::close() }}
		</div>
		<div class="span4">
			<p class="lead">New to Critter? Sign up!</p>
			{{ Form::open('/', 'POST',array('class'=>'well')); }}
			{{ Form::token(); }}
			{{ $errors->first('name', Alert::error(":message")) }}
			{{ Form::text('name', Input::old('name'), array('class' => 'span3', 'placeholder' => 'Full Name'));}}
			{{ $errors->first('username', Alert::error(":message")) }}
			{{ Form::text('new_username', Input::old('new_username'), array('class' => 'span3', 'placeholder' => 'Username'));}}
			{{ $errors->first('password', Alert::error(":message")) }}
			{{ Form::password('new_password', array('class' => 'span3', 'placeholder' => 'New Password'));}}
			{{ Form::submit('Sign up for Critter', array('class'=>'btn-warning'));}}
			{{ Form::close() }}
		</div>
	</div>
</div>
@endsection