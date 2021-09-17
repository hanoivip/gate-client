@extends('hanoivip::admin.layouts.admin')

@section('title', 'Lich su nap & chuyen. OldFlow')

@section('content')

<div class="content">
<div class="title">Lịch sử nạp thẻ</div>
@if (empty($submits))
<p>Chưa từng nạp thẻ</p>
@else

<table>
	<tr>
		<th>Trạng thái</th>
		<th>Mã Thẻ</th>
		<th>Giá trị đã chọn</th>
		<th>Giá trị thẻ</th>
		<th>Phạt</th>
		<th>Thực nhận</th>
		<th>Thời gian</th>
	</tr>
    @foreach ($submits as $submit)
    <tr>
        <td>{{$submit->status}}</td>
        <td>{{$submit->password}}</td>
        <td>{{$submit->dvalue}}</td>
        <td>{{$submit->value}}</td>
        <td>{{$submit->penalty}}</td>
        <td>{{ min($submit->dvalue, $submit->value) * (100 - $submit->penalty) / 100 }}</td>
        <td>{{$submit->time}}</td>
    </tr>
    @endforeach
</table>

@endif
<div class="title">Lịch sử nhận khuyến mãi và mua xu game</div>
@if (empty($mods))
<p>Chưa từng chuyển xu</p>
@else

<table>
<tr>
    <th>Loại</th>
    <th>Số xu</th>
    <th>Lý do</th>
    <th>Thời gian</th>
</tr>
@foreach ($mods as $mod)
<tr>
    <td>{{$mod->acc_type}}</td>
    <td>{{$mod->balance}}</td>
    <td>{{$mod->reason}}</td>
    <td>{{$mod->time}}</td>
</tr>
@endforeach
</table>

@endif

	
@endsection