  @extends('layouts.master')
  @section('title', 'Login')
  @section('content')



  @if(session('message'))
      <div class="alert alert-success text-center">
          {{ session('message') }}
      </div>
  @endif

  <style>
    
    </style>

  <div class="d-flex justify-content-center align-items-center vh-50 bg-white">
    <div class="card shadow-lg border-0 rounded-4 p-4" style="width: 200%; max-width: 600px;">
        <div class="card-body">
            <h3 class="text-center mb-4 text-primary">Welcome Back 👋</h3>
            <form action="{{ route('do_login') }}" method="post">
                {{ csrf_field() }}

                {{-- Errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Email --}}
                <div class="form-group mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control rounded-pill" id="email" name="email" placeholder="Enter your email" required>
                </div>

                {{-- Password --}}
                <div class="form-group mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control rounded-pill" id="password" name="password" placeholder="Enter your password" required>
                </div>

                {{-- Forgot password --}}
                <div class="mb-3 text-end">
                    <a href="{{ route('forgot_password') }}" class="text-decoration-none text-muted">Forgot Password?</a>
                </div>

                {{-- Login button --}}
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary rounded-pill">Login</button>
                </div>

                {{-- Divider --}}
                <div class="text-center mb-3 text-muted">or continue with</div>

                {{-- Social Buttons --}}
                <div class="d-flex justify-content-between gap-2">
                    <a href="{{ route('auth.redirect', ['provider' => 'github']) }}" class="btn btn-dark w-50 rounded-pill">
                        <i class="bi bi-github"></i> GitHub
                    </a>
                    <a href="{{ route('auth.redirect', ['provider' => 'google']) }}" class="btn btn-danger w-50 rounded-pill">
                        <i class="bi bi-google"></i> Google
                    </a>
                    <a href="{{ route('auth.redirect', ['provider' => 'facebook']) }}" class="btn btn-primary w-50 rounded-pill">
    <i class="bi bi-facebook"></i> Facebook
</a>
<a href="{{ route('auth.redirect', ['provider' => 'facebook']) }}" class="btn btn-info text-white w-50 rounded-pill">
    <i class="bi bi-microsoft "></i> Microsift
</a>

                </div>
            </form>
        </div>
    </div>
</div>


  @endsection
