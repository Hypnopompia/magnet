<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Magnet</title>

    <!-- Styles -->
    <link href="/css/app.css" rel="stylesheet">
    <style type="text/css">
        .card {
            border-radius: 3px;
            -webkit-box-shadow: 3px 3px 17px 2px rgba(0,0,0,0.47);
            -moz-box-shadow: 3px 3px 17px 2px rgba(0,0,0,0.47);
            box-shadow: 3px 3px 17px 2px rgba(0,0,0,0.47);
        }

        #boards .board {
            margin-bottom: 10px;
        }

        #cards .card p {
            margin: 10px;
        }
    </style>


    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
<!--                 <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button> -->

                <!-- Branding Image -->
                @if (Auth::guest())
                <a class="navbar-brand" href="{{ url('/') }}">
                    Magnet
                </a>
                @else
                <a class="navbar-brand" href="{{ url('/home') }}">
                    Magnet
                </a>
                @endif

            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    &nbsp;
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ url('/login') }}">Login</a></li>
                        <li><a href="{{ url('/register') }}">Register</a></li>
                    @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="{{ url('/refreshboards') }}"
                                        onclick="event.preventDefault();
                                                 document.getElementById('refreshboards-form').submit();">
                                        Refresh Boards
                                    </a>

                                    <form id="refreshboards-form" action="{{ url('/refreshboards') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                                <li>
                                    <a href="{{ url('/logout') }}"
                                        onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')

    <!-- Scripts -->
    <script src="/js/app.js"></script>
    <!-- <script src="https://code.jquery.com/jquery-3.1.0.min.js" integrity="sha256-cCueBR6CsyA4/9szpPfrX3s49M9vUU5BgtiJj06wt/s=" crossorigin="anonymous"></script> -->
    <script src="/js/jquery.grid-a-licious.js"></script>
    <script>
        $(document).ready(function () {
            $("#cards").gridalicious({
                gutter: 20,
                selector: '.card',
                animate: true
            });
        });
    </script>
</body>
</html>
