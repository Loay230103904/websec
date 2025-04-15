@extends('layouts.master')

@section('title', 'Forgot Password')

@section('content')
<div class="container mt-4">
    <h2>Forgot Your Password?</h2>
    <form method="POST" action="{{ route('forgot_password.submit') }}">
        @csrf
        <div class="form-group mt-3">
            <label for="email">Enter your email address</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Send Temporary Password</button>
    </form>
</div>
@endsection
