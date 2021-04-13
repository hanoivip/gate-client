@extends('hanoivip::admin.layouts.admin')

@section('title', 'Kết quả')

@section('content')

<a href="{{route('ecmin.income.today')}}">Doanh số trong ngày</a>

<a href="{{route('ecmin.income.thisMonth')}}">Doanh số trong tháng</a>

<form method="post" action="{{route('ecmin.income.byTime')}}">
	{{csrf_field()}}
	<input type="date" name="start_time" id="start_time"/>
	<input type="date" name="end_time" id="end_time"/>
	<button type="submit">OK</button>
</form>

@endsection
