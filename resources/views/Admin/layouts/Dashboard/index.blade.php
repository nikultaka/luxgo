<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="description" content="Neon Admin Panel" />
	<meta name="author" content="" />
    
    <link rel="icon" href="{{ asset('assets/admin/theme/images/logo.png')}}">
	<title>@yield('adminTitle')</title>
	<script src="{{ asset('assets/admin/theme/js/jquery-1.11.3.min.js')}}"></script>
	<link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
	<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
	<link rel="stylesheet" href="{{ asset('assets/admin/theme/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css')}}">
	<link rel="stylesheet" href="{{ asset('assets/admin/theme/css/font-icons/entypo/css/entypo.css')}}">
	<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic">
	<link rel="stylesheet" href="{{ asset('assets/admin/theme/css/bootstrap.css')}}">
	<link rel="stylesheet" href="{{ asset('assets/admin/theme/css/neon-core.css')}}">
	<link rel="stylesheet" href="{{ asset('assets/admin/theme/css/neon-theme.css')}}">
	<link rel="stylesheet" href="{{ asset('assets/admin/theme/css/neon-forms.css')}}">
	<link rel="stylesheet" href="{{ asset('assets/admin/theme/css/custom.css')}}">
	<link rel="stylesheet" href="{{ asset('assets/admin/css/style.css')}}">
	<script src="{{ asset('assets/admin/theme/js/toastr.js')}}"></script>
	<!-- Sweet Alert CDN -->
    
	
	@yield('adminHeaderSection')
	<script type="text/javascript">
        var BASE_URL = "{{ url('/') }}";
        var ADMIN = "{{ ADMIN }}";
    </script>
</head>
<body class="page-body" data-url="http://neon.dev">
	<!-- Preloader -->
    <div class="preloader" style="display: none"></div>

<div class="page-container"><!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->
	
    {{-- sidebar --}}
     @include('Admin.layouts.Dashboard.sidebar')
	<div class="main-content">
		{{-- sidebar --}}
        @include('Admin.layouts.Dashboard.header')
		
		<hr />
				<ol class="breadcrumb bc-3" >
                    <li> <a href="index.html"><i class="fa-home"></i>Home</a> </li>
                    <li><a href="ui-panels.html">@yield('breadcrumbFirst')</a></li>
					<li class="active"><strong>@yield('breadcrumbSecond')</strong></li>
				</ol>
		<h2>@yield('pageTitle')</h2>
		<br />
				
        @yield('adminMainContent')

		<!-- Footer -->
         @include('Admin.layouts.Dashboard.footer')
	</div>		
</div>

	<!-- Bottom scripts (common) -->
	<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js" integrity="sha512-37T7leoNS06R80c8Ulq7cdCDU5MNQBwlYoy1TX/WUsLFC2eYNqtKlV0QjH7r8JpG/S0GUMZwebnVFLPd6SU5yg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.js"></script>
	<script src="{{ asset('assets/admin/js/common.js')}}"></script>
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
	@yield('adminFooterSection')

</body>
</html>