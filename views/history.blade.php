@extends('hanoivip::layouts.app')

@section('title', 'Lịch sử nạp thẻ và chuyển xu')

@section('content')


<p>Lịch sử nạp thẻ</p>
<table>
@foreach ($submits as $submit)
<tr>
    <td>{{$submit->status}}</td>
    <td>{{$submit->password}}</td>
    <td>{{$submit->dvalue}}</td>
    <td>{{$submit->penalty}}</td>
    <td>{{$submit->value}}</td>
    <td>{{$submit->time}}</td>
</tr>
@endforeach
</table>

<p>Lịch sử nhận khuyến mãi và mua xu game</p>
<table>
@foreach ($mods as $mod)
<tr>
    <td>{{$mod->acc_type}}</td>
    <td>{{$mod->balance}}</td>
    <td>{{$mod->reason}}</td>
    <td>{{$mod->time}}</td>
</tr>
@endforeach
</table>

@endsection
