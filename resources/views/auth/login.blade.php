@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('login.submit') }}">
    @csrf

    <div>
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}">
    </div>

    <div>
        <label>Password</label>
        <input type="password" name="password">
    </div>

    <button type="submit">Login</button>
</form>

@if($errors->any())
    <div>
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif
@endsection