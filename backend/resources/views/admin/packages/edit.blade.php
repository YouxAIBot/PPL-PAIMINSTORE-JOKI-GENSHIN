@extends('layouts.admin')
@section('title', 'Ubah Paket')
@section('content')
<h1>Ubah Paket: {{ $package->name }}</h1>
@include('admin.packages._form')
@endsection
