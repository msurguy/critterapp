<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Critter, from @msurguy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    {{ Asset::container('bootstrapper')->styles(); }}

    <link href="./css/font-awesome.css" rel="stylesheet" >
    <style>
      .artwork {
        margin-top:30px;
        margin-bottom: 30px;
      }

    </style>

    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
  </head>
<body>
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="{{URL::base();}}">Critter</a>
          <div class="btn-group pull-right">
            @if (Auth::guest())
              <a class="btn" href="{{ URL::to_action('home@login')}}">
                <i class="icon-user"></i> Login
              </a>
            @else
            Welcome, <strong>{{ HTML::link_to_action('user@index', Auth::user()->username) }} </strong> |
                {{ HTML::link_to_action('home@logout', 'Logout') }}
            @endif
          </div>
          <div class="nav-collapse">
            <ul class="nav">
              <li class="active"><a href="{{URL::base();}}">Home</a></li>
              @if (!Auth::guest())
                <li>{{ HTML::link_to_action('user@index', "Profile") }} </li>
              @endif
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
    <div class="container">
      <div class="row">
        @yield_content('content')
      </div>
    </div>
    <div class="container">
      <hr />
      <div class="row">
        <div class="span12">
        <p>Critter from you.</p>
        </div>
      </div>
    </div>
  {{ Asset::container('bootstrapper')->scripts(); }}
  @section('scripts')
  @yield_section
  </body>
</html>