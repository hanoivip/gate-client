@extends('hanoivip::layouts.app')

@section('title', 'Kết quả nạp thẻ')

@section('content')

@if (!empty($message))
<p>{{ $message }}</p>
@else
<p>Topup success!</p>
@endif

<a href="{{ route('jtopup') }}">{{__('hanoivip::topup.ui.newtopupbtn')}}</button>

@endsection
