<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf_token" content="{{ csrf_token() }}"/>
    <title>OS to WOO Importer</title>

    <!-- Bootstrap core CSS -->
    <link href="https://bootswatch.com/flatly/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.css"/>
    @yield('header')
</head>

<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">Os2Woo Importer</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="dropdown @if(Request::is('products')) active @endif" >
                    <a href="#" class="dropdown-toggle" data-hover="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Products <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li @if(Request::is('products')) class="active" @endif><a href="/products">Import All Products</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="#">Import Cross-Sells</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="#">Imported Report</a></li>
                        <li><a href="#">Errors Report</a></li>

                    </ul>
                </li>
                <li @if(Request::is('customers')) class="active" @endif><a href="/customers">Customers</a></li>
                <li class="dropdown @if(Request::is('orders')) active @endif" >
                    <a href="#" class="dropdown-toggle" data-hover="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Orders <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li @if(Request::is('orders')) class="active" @endif><a href="/orders">Import All Orders</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="#">Update Order Dates</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="#">Imported Report</a></li>
                        <li><a href="#">Errors Report</a></li>

                    </ul>
                </li>
                <li @if(Request::is('coupons')) class="active" @endif><a href="/coupons">Coupons</a></li>
                {{--<li @if(Request::is('reports')) class="active" @endif><a href="/reports">Reports</a></li>--}}
            </ul>
        </div>
        <!--/.nav-collapse -->
    </div>
</nav>

<div class="container" v-cloak>
    @yield('content')
</div>
<!-- /.container -->

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/vue/0.12.4/vue.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
<script src="/assets/js/bootstrap-hover-dropdown.min.js"></script>
@yield('footer')
<script src="/assets/js/app.js"></script>
</body>
</html>
