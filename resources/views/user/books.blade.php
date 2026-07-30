@extends('layouts.app-navbar')
@section('title', 'Koleksi Buku - Digishelf')

@section('content')

@include('components.books-grid', ['books' => $books, 'showSearch' => true])

@endsection