@extends('layouts.master')

@section('title', 'Email Verified')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 mt-5">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-body text-center p-5">
                <div class="mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" fill="#28a745" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM6.97 11.03a.75.75 0 0 0 1.07 0l4.285-4.285a.75.75 0 0 0-1.06-1.06L7.5 9.44 5.803 7.743a.75.75 0 1 0-1.06 1.06l2.227 2.227z"/>
                    </svg>
                </div>
                <h2 class="text-success mb-3">Email Verified!</h2>
                <p class="lead">Congratulations, <strong>{{ $user->name }}</strong> 🎉</p>
                <p class="mb-4">Your email <strong>{{ $user->email }}</strong> has been successfully verified. You can now enjoy full access to your account.</p>
                <a href="{{ route('login') }}" class="btn btn-success px-4">Tap Here To Login</a>
            </div>
        </div>
    </div>
</div>
@endsection
