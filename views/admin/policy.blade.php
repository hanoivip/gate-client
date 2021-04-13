@extends('hanoivip::admin.layouts.admin')

@section('title', 'Cac chinh sach dang ap dung')

@section('content')

@if (empty($policies))
<p>Chua co chuong trinh KM nao </p>
@else
    @foreach ($policies as $pol)
    	<form method="post" action="{{route('ecmin.policy.delete.do')}}">
        	{{csrf_field()}}
        	Chuong trinh: {{$pol->info()->title}} <br/>
        	Bat dau:{{\Carbon\Carbon::parse($pol->info()->start_time)->format('d/M/Y m:H')}} <br/>
        	Ket thuc: {{\Carbon\Carbon::parse($pol->info()->end_time)->format('d/M/Y m:H')}} <br/>
        	Tham so: {{$pol->info()->params}}
        	<input type="hidden" name="pid" value="{{$pol->info()->id}}"/>
        	<button type="submit">Del</button>
    	</form>
    @endforeach
@endif    

<a href="{{route('ecmin.policy.new')}}">New</a>

@endsection