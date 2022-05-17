<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="description" content="Neon Admin Panel" />
	<meta name="author" content="" />
	<link rel="icon" href="{{ asset('assets/admin/theme/images/logo.png')}}">
	<title>@yield('authTitle')</title>
	<link rel="stylesheet" href="{{ asset('assets/admin/theme/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css')}}">
	<link rel="stylesheet" href="{{ asset('assets/admin/theme/css/font-icons/entypo/css/entypo.css')}}">
	<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic">
	<link rel="stylesheet" href="{{ asset('assets/admin/theme/css/bootstrap.css')}}">
	<link rel="stylesheet" href="{{ asset('assets/admin/theme/css/neon-core.css')}}">
	<link rel="stylesheet" href="{{ asset('assets/admin/theme/css/neon-theme.css')}}">
	<link rel="stylesheet" href="{{ asset('assets/admin/theme/css/neon-forms.css')}}">
	<link rel="stylesheet" href="{{ asset('assets/admin/theme/css/custom.css')}}">
	<script src="{{ asset('assets/admin/theme/js/jquery-1.11.3.min.js')}}"></script>
</head>
<body class="page-body login-page login-form-fall" data-url="http://neon.dev">
<script type="text/javascript">
var baseurl = '';
</script>

<div class="login-container">
    @yield('authContent')
</div>


	<!-- Bottom scripts (common) -->
	<script src="{{ asset('assets/admin/theme/js/gsap/TweenMax.min.js')}}"></script>
	<script src="{{ asset('assets/admin/theme/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js')}}"></script>
	<script src="{{ asset('assets/admin/theme/js/bootstrap.js')}}"></script>
	<script src="{{ asset('assets/admin/theme/js/joinable.js')}}"></script>
	<script src="{{ asset('assets/admin/theme/js/resizeable.js')}}"></script>
	<script src="{{ asset('assets/admin/theme/js/neon-api.js')}}"></script>
	<script src="{{ asset('assets/admin/theme/js/jquery.validate.min.js')}}"></script>
	<script src="{{ asset('assets/admin/theme/js/neon-login.js')}}"></script>


	<!-- JavaScripts initializations and stuff -->
	<script src="{{ asset('assets/admin/theme/js/neon-custom.js')}}"></script>


	<!-- Demo Settings -->
	<script src="{{ asset('assets/admin/theme/js/neon-demo.js')}}"></script>

</body>
</html>