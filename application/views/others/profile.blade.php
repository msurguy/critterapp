@layout('templates.template')

@section('content')
<div class="span4 offset4">
  <p class="lead">{{$username;}} Wrote</p>
<div class="row">
  <div class="span4 well">
    <div class="row">
      <div class="span1"><a href="{{action('others@show', array($username));}}" class="thumbnail"><img src="../img/user.jpg" alt=""></a></div>
      <div class="span3">
        <h3>@{{$username;}} </h3> 
        @if ( Auth::user() && ($following == false))
          {{ Form::open('user/follow', 'POST');}}
          {{ Form::token(); }}
          <input type="hidden" name="id" id="postvalue" value="{{$user_id}}" />
          {{ Form::submit('Follow');}}
          {{ Form::close();}}
        @endif
        @if ( Auth::user() && ($following == true))
          {{ Form::open('user/unfollow', 'POST');}}
          {{ Form::token(); }}
          <input type="hidden" name="id" id="postvalue" value="{{$user_id}}" />
          {{ Form::submit('Unfollow');}}
          {{ Form::close();}}
        @endif
        <h3>First Last Name</h3>
        {{ Badges::warning($count.' critts');}} {{ Badges::info($followers.' followers');}}
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="span4 well">
    <p class="lead"> Previously Critted:</p>

    @foreach ($critts -> results as $critt)
      <hr />
      <div>
        <p>{{ $critt->critt }}</p>
        <span class="badge pull-right">At {{$critt->updated_at}}</span>
        <p>&nbsp;</p>
      </div>
    @endforeach
    <hr />
    {{ $critts -> pager(true); }}
  </div>
</div>
</div>
@endsection

@section('scripts')
  {{ HTML::script('js/charcounter.js');}}
  {{ HTML::script('js/app.js');}}
@endsection