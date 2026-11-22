@extends('layouts.admin')
@section('title', 'Ubah Kategori')
@section('content')
<h1>Ubah Kategori: {{ $category->name }}</h1>
@include('admin.categories._form')
@endsection
