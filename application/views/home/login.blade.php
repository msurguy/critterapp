@layout('templates.template')
@section('content')
<div class="span4 offset4">
  <div class="row">
    <div class="span4">
      <p class="lead">Sign in to Critter</p>
      {{ Form::open('home/login', 'POST',array('class'=>'well')); }}
        {{ Form::token() }}
        @if (Session::has('login_errors'))
            {{ Alert::error("Username or password incorrect.") }}
        @endif

        @if (Session::has('success_message'))
            {{ Alert::success("Account created Successfully, please log in now") }}
        @endif

        @if (Session::has('logout_message'))
            {{ Alert::success("You have been logged out!") }}
        @endif
        {{ Form::text('username', Input::old('username'), array('class' => 'span3', 'placeholder' => 'Username'));}}
        {{ Form::password('password', array('class' => 'span3', 'placeholder' => 'Password'));}}
        {{ Form::labelled_checkbox('remember', 'Remember Me');}}
        {{ Form::submit('Login', array('class'=>'btn-info'));}}
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection