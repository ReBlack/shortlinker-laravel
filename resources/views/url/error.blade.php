@extends('layouts.app')

@section('title', $exception->getStatusCode() . ' - Ошибка')

@section('content')
<div class="main-container">
    <h1 class="page-title">{{ $exception->getStatusCode() }}</h1>
    <div class="alert alert-danger">
        {{ $exception->getMessage() ?: 'Страница не найдена' }}
    </div>
</div>
@endsection
