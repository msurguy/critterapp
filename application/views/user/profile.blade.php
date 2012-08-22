@layout('templates.template')

@section('content')
  <div class="span4 offset4">
    <p class="lead"> What's happening, {{ Auth::User()->name }} ?</p>
    <div class="row">
      <div class="span4 well">
      {{ Form::open('user/index', 'POST'); }}
        {{ Form::token() }}
        {{ $errors->first('critt', Alert::error(":message")) }}
        <textarea class="span4" id="new_critt" name="new_critt" rows="5" placeholder="Type in your new critt"></textarea>
        <button type="submit" class="btn btn-info">Post New Critt</button>
        {{ Form::close() }}
      </div>
    </div>

    <div class="row">
      <div class="span4 well">
        <div class="row">
          <div class="span1"><a href="{{action('others@show', array(Auth::User()->username));}}" class="thumbnail"><img src="../img/user.jpg" alt=""></a></div>
          <div class="span3">
            <h3>{{Auth::User()->username}}</h3>
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