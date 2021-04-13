@extends('hanoivip::layouts.app')

@section('title', 'Kết quả nạp thẻ')

@section('content')

@if (empty($message))
<p>Nạp thẻ có lỗi, vui lòng thử lại sau. </p>
@else
<p>Nạp thẻ không thành công: {{ $message }} </p>
@endif


@endsection
