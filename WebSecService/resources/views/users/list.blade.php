@extends('layouts.master')
@section('title', 'Users')
@section('content')
<div class="row mt-2">
    <div class="col col-10">
        <h1>Users</h1>
    </div>
</div>

<!-- البحث -->
<form>
    <div class="row mb-3">
        <div class="col col-sm-3">
            <input name="keywords" type="text" class="form-control" placeholder="Search Keywords" value="{{ request()->keywords }}" />
        </div>
        <div class="col col-sm-2">
            <button type="submit" class="btn btn-primary w-100">Search</button>
        </div>
        <div class="col col-sm-2">
            <button type="reset" class="btn btn-danger w-100">Reset</button>
        </div>
    </div>
</form>

<!-- جدول المستخدمين -->
<div class="card mt-2">
  <div class="card-body">
    <table class="table table-striped">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Name</th>
          <th scope="col">Email</th>
          <th scope="col">Roles</th>
          <th scope="col">Credit</th> <!-- إضافة عمود للكريدت -->
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $user)
        <tr>
          <td scope="col">{{$user->id}}</td>
          <td scope="col">{{$user->name}}</td>
          <td scope="col">{{$user->email}}</td>
          <td scope="col">
            @foreach($user->roles as $role)
              <span class="badge bg-primary">{{$role->name}}</span>
            @endforeach
          </td>
          <td scope="col">{{$user->credit ?? 0}} </td> <!-- عرض الكريدت -->
          <td scope="col">
            <!-- زر تعديل و تغيير كلمة المرور و الحذف -->
            @can('edit_users')
            <a class="btn btn-primary btn-sm" href="{{ route('users_edit', [$user->id]) }}">Edit</a>
            @endcan

            @can('admin_users')
            <a class="btn btn-secondary btn-sm" href="{{ route('edit_password', [$user->id]) }}">Change Password</a>
            @endcan

            @can('delete_users')
            <a class="btn btn-danger btn-sm" href="{{ route('users_delete', [$user->id]) }}">Delete</a>
            @endcan

          

                @can('add_credit')
                <!-- نموذج لإضافة كريدت للمستخدم -->
                <form action="{{ route('user.addCredit', [$user->id]) }}" method="POST">
                    @csrf
                    <div class="form-group mb-2">
                        <label for="credit">Add Credit</label>
                        <input type="number" name="credit" class="form-control" id="credit" required min="1" placeholder="Enter amount" />
                    </div>
                    <button type="submit" class="btn btn-success btn-sm">Add Credit</button>
                </form>
                @endcan

                <!-- رسائل النجاح أو الفشل -->
                @if(session('success'))
                <div class="alert alert-success mt-2">
                    {{ session('success') }}
                </div>
                @endif
                @if(session('error'))
                <div class="alert alert-danger mt-2">
                    {{ session('error') }}
                </div>
                @endif
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@endsection
