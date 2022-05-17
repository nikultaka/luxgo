@extends('Admin.layouts.Auth.index')
@section('authTitle', 'Admin | Login')
@section('authContent')
<div class="login-header login-caret">
    <div class="login-content">
        <a href="{{route('admin-login')}}" class="logo">
            <img src="{{ asset('assets/admin/theme/images/logo.png')}}" width="60%" alt=""/>
        </a>
        <p class="description">Enter your email address and password to access <b> Luxgo's admin panel. </b></p>
        <!-- progress bar indicator -->
        <div class="login-progressbar-indicator">
            <h3>43%</h3>
            <span>logging in...</span>
        </div>
    </div>
</div>

<div class="login-progressbar">
    <div></div>
</div>

<div class="login-form">
    <div class="login-content">
    @if (\Session::has('error'))
        <div class="form-login-error" style="display: block;">
        <h3>Invalid login</h3>
                <p>{!! \Session::get('error') !!}</p><br/>
        </div>
    @endif
       @if (count($errors))
                <div class="form-login-error" style="display: block;">
                <h3>Invalid login</h3>
                    @foreach ($errors->all() as $error)
                        <p style="padding:0px">{{ $error }}</p><br/>
                    @endforeach
            </div>
        @endif
        <form method="POST" action="{{ route('admin-login-proccess') }}">
         @csrf
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="entypo-user"></i>
                    </div>
                    <input type="text" class="form-control" name="email" id="email" placeholder="Email" autocomplete="off" />
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="entypo-key"></i>
                    </div>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Password" autocomplete="off" />
                </div>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block btn-login">
                    <i class="entypo-login"></i>
                    Login In
                </button>
            </div>
            
            <!-- Implemented in v1.1.4 -->
            {{-- <div class="form-group">
                <em>- or -</em>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-default btn-lg btn-block btn-icon icon-left facebook-button">
                    Login with Facebook
                    <i class="entypo-facebook"></i>
                </button>
            </div> --}}
        </form>
        

        <div class="login-bottom-links">            
            <a href="extra-forgot-password.html" class="link">Forgot your password?</a>
            <br />
            <a href="#">ToS</a>  - <a href="#">Privacy Policy</a>
        </div>
    </div>
</div>
@endsection
