@extends('layouts.auth')

@section('title', 'Login')

@section('content')
  <div class="auth-form-light text-left p-5">
    <div class="brand-logo">
      <img src="{{ asset('/images/logo.svg') }}">
    </div>
    <h4>Hello! let's get started</h4>
    <h6 class="font-weight-light">Sign in to continue.</h6>
    <form class="pt-3" method="POST" action="{{ route('login') }}">
      @csrf

      <div class="form-group">
        <input type="email" id="email" name="email" class="form-control form-control-lg" id="exampleInputEmail1"
          placeholder="Username">
        @error('email')
          <span class="text-danger">{{$message}}</span>
        @enderror
      </div>
      <div class="form-group">
        <input type="password" id="password" name="password" class="form-control form-control-lg"
          id="exampleInputPassword1" placeholder="Password">
        @error('password')
          <span class="text-danger">{{$message}}</span>
        @enderror
      </div>
      <div class="mt-3 d-grid gap-2">
        <button class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn" type="submit">SIGN
          IN</button>
      </div>
      <div class="my-2 d-flex justify-content-between align-items-center">
        <div class="form-check">
          <div class="form-check">
            <label class="form-check-label text-muted">
              <input type="checkbox" class="form-check-input" name="remember"> Keep me signed in </label>
          </div>
        </div>
        <a href="#" class="auth-link text-primary">Forgot password?</a>
      </div>
      <div class="mb-2 d-grid gap-2">
        <a href="{{ route('google-login') }}" class="btn btn-block btn-google auth-form-btn">
          <i class="mdi mdi-google me-2"></i>Connect using google </a>
      </div>
      <div class="text-center mt-4 font-weight-light"> Don't have an account? <a href="{{route('register')}}"
          class="text-primary">Create</a>
      </div>
    </form>
  </div>
@endsection