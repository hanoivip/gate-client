@extends('hanoivip::admin.layouts.admin')

@section('title', 'Kết quả')

@section('content')

@if (!empty($message))
<p>{{ $message }}</p>
@endif

@if (!empty($error_message))
<p>{{ $error_message }}</p>
@endif


@endsection
