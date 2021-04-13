@extends('hanoivip::admin.layouts.admin')

@section('title', 'THem moi chinh sach nap the')

@section('content')

<form method="post" action="{{route('ecmin.policy.new.do')}}">
	{{csrf_field()}}
	Ten dip khuyen mai: <input type="text" name="title" required/>
	<select name="type">
		<option value="0">Khuyen mai nap the</option>
		<option value="1">Khuyen mai GATE</option>
		<option value="2">Khuyen mai ZING</option>
		<option value="3">Khuyen mai MOMO</option>
	</select>
	Tham so: <input type="text" name="params"/>
	Bat dau: <input type="date" name="start_time"/>
	Ket thuc: <input type="date" name="end_time"/>
	<button type="submit">OK</button>
</form>

@endsection