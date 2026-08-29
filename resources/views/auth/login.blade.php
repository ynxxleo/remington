@extends('layouts/fullLayoutMaster')

@section('title', 'Login Page')

@section('page-style')
  {{-- Page Css files --}}
  <link rel="stylesheet" href="{{ asset(mix('css/base/pages/authentication.css')) }}">
@endsection

@section('content')
  <div class="auth-wrapper auth-basic px-2">
    <aside class="nova-auth-visual" aria-hidden="true">
      <img src="{{ asset('images/nova/hero-orbit.png') }}" alt="">
      <div><span>Intelligent market access</span><h2>One secure view of every opportunity.</h2><p>Trade, exchange and manage your portfolio without losing focus.</p></div>
    </aside>
    <div class="auth-inner my-2">
      <!-- Login basic -->
      <div class="card mb-0">
        <div class="card-body">
            <a href="{{ route('home') }}" class="nova-auth-brand" aria-label="{{ siteName() }} home">@include('partials.site-wordmark')</a>
          <div class="nova-auth-kicker mb-1">Secure account access</div>
          <h4 class="card-title mb-1">Welcome back</h4>
          <p class="card-text mb-2">Sign in to access your portfolio and trading workspace.</p>

          @if (session('status'))
            <div class="alert alert-success mb-1 rounded-0" role="alert">
              <div class="alert-body">
                {{ session('status') }}
              </div>
            </div>
          @endif

          <form class="auth-login-form mt-2" method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-1">
              <label for="login-email" class="form-label">Email / Username</label>
              <input type="text" class="form-control @error('email') is-invalid @enderror @error('username') is-invalid @enderror"
               id="login-email" name="email"
                placeholder="john@example.com" aria-describedby="login-email" tabindex="1" autofocus
                value="{{ old('email') }}" />
              @error('email')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
              @enderror
            </div>

            <div class="mb-1">
              <div class="d-flex justify-content-between">
                <label class="form-label" for="login-password">Password</label>
                @if (Route::has('password.request'))
                  <a href="{{ route('password.request') }}">
                    <small>Forgot Password?</small>
                  </a>
                @endif
              </div>
              <div class="input-group input-group-merge form-password-toggle">
                <input type="password" class="form-control form-control-merge" id="login-password" name="password"
                  tabindex="2" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                  aria-describedby="login-password" />
                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
              </div>
            </div>
            <div class="mb-1">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember" name="remember" tabindex="3"
                  {{ old('remember') ? 'checked' : '' }} />
                <label class="form-check-label" for="remember"> Remember Me </label>
              </div>
            </div>
            <button type="submit" class="btn btn-primary w-100" tabindex="4">Sign in</button>
          </form>

          <div class="nova-auth-note"><i class="bi bi-shield-check"></i><span>Protected access. Always verify the address in your browser before entering your credentials.</span></div>

          <p class="text-center mt-2">
            <span>New on our platform?</span>
            @if (Route::has('register'))
              <a href="{{ route('register') }}">
                <span>Create an account</span>
              </a>
            @endif
          </p>

          {{-- <div class="divider my-2">
            <div class="divider-text">or</div>
          </div>

          <div class="auth-footer-btn d-flex justify-content-center">
            <a href="#" class="btn btn-facebook">
              <i data-feather="facebook"></i>
            </a>
            <a href="#" class="btn btn-twitter white">
              <i data-feather="twitter"></i>
            </a>
            <a href="#" class="btn btn-google">
              <i data-feather="mail"></i>
            </a>
            <a href="#" class="btn btn-github">
              <i data-feather="github"></i>
            </a>
          </div> --}}
        </div>
      </div>
      <!-- /Login basic -->
    </div>
  </div>
@endsection
