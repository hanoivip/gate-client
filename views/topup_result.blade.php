@extends('hanoivip::layouts.app')

@section('title', 'Kết quả nạp thẻ')

@section('content')

@if (!empty($message))
<p>{{ $message }}</p>
@endif

@if (!empty($error_message))
<p>{{ $error_message }}</p>
@endif

<a href="{{ route('topup') }}">{{__('hanoivip::topup.ui.newtopupbtn')}}</button>

@endsection
