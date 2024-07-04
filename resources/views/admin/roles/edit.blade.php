@extends('layouts.logged')
@section('content')
@include('admin.roles.role-form', ['role' => $role])
@endsection
