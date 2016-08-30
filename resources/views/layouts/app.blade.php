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
        #wrapper {
            width: 90%;
            max-width: 800px;
            min-width: 800px;
            margin: 50px auto;
        }

        #columns {
            -webkit-column-count: 3;
            -webkit-column-gap: 10px;
            -webkit-column-fill: auto;
            -moz-column-count: 3;
            -moz-column-gap: 10px;
            -moz-column-fill: auto;
            column-count: 3;
            column-gap: 15px;
            column-fill: auto;
        }

        .pin {
            display: inline-block;
            background: #FEFEFE;
            border: 2px solid #FAFAFA;
            box-shadow: 0 1px 2px rgba(34, 25, 25, 0.4);
            margin: 0 0px 15px;
            -webkit-column-break-inside: avoid;
            -moz-column-break-inside: avoid;
            column-break-inside: avoid;
            padding: 0px;
            padding-bottom: 5px;
            background: -webkit-linear-gradient(45deg, #FFF, #F9F9F9);
            opacity: 1;

            -webkit-transition: all .2s ease;
            -moz-transition: all .2s ease;
            -o-transition: all .2s ease;
            transition: all .2s ease;
        }

        .pin img {
            width: 100%;
            border-bottom: 1px solid #ccc;
            padding-bottom: 15px;
            margin-bottom: 5px;
        }

        .pin p {
            font: 12px/18px Arial, sans-serif;
            color: #333;
            margin: 5px;
        }

        @media (min-width: 960px) {
            #columns {
                -webkit-column-count: 4;
                -moz-column-count: 4;
                column-count: 4;
            }
        }

        @media (min-width: 1100px) {
            #columns {
                -webkit-column-count: 5;
                -moz-column-count: 5;
                column-count: 5;
            }
        }

        #columns:hover .pin:not(:hover) {
            opacity: 0.4;
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
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    Magnet
                </a>
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
</body>
</html>
