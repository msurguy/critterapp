@layout('templates.template')

@section('content')
<div class="span6 offset3 well">
  <p class="lead">Users have critterred {{$count;}} critts:</p>
  @foreach ($critts -> results as $critt)
    <hr />
    <div class="row">
      <div class="span1"><a href="{{action('others@show', array($critt->user->username));}}" class="thumbnail"><img src="img/user.jpg" alt=""></a>
      </div>
      <div class="span5">
        <h3><a href="{{action('others@show', array($critt->user->username));}}">{{$critt->user->username}}</a></h3>
        <p>{{ $critt->critt; }}</p>
        <span class="badge pull-right">At {{$critt->updated_at;}}</span>
        <span class="badge badge-warning">{{Critt::where('user_id','=',$critt->user->id)->count();}} critts</span>
        <span class="badge badge-info">{{Follower::where('following_id','=',$critt->user->id)->count();}} followers</span>
      </div>
    </div>
  @endforeach
  <hr />
  {{ $critts -> pager(true); }}
</div>
@endsection