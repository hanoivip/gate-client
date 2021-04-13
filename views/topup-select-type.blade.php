@extends('hanoivip::layouts.app')

@section('title', 'Nạp thẻ (bước 1)')

@section('content')

@if (!empty($error_message))
<p>{{ $error_message }}</p>
@endif


<br/>
Chọn loại thẻ:
@foreach ($cardtypes as $cardcode => $detail)
	@if ($detail['available'])
	<form method="GET" action="{{ route('topup.by.type') }}">
    	<div>
    		<p>{{$detail['title']}}</p>
    		<input type="hidden" name="type" value="{{$cardcode}}"/>
    		@if ($detail['need_dvalue'])
    			<br/>
    			Cho biết giá trị: (chọn sai mất {{config('gate.declare_wrong_cutoff', 0)}}% giá trị thật, nếu không có giá trị thẻ của bạn trong danh sách hãy liên hệ hỗ trợ)
            	<select id="dvalue" name="dvalue">
            		@foreach ($detail['supported_values'] as $svalue => $stitle)
            			<option value={{$svalue}}>{{$stitle}}</option>
            		@endforeach
            	</select>
            @else
            	<input type="hidden" name="dvalue" id="dvalue" value="0"/>	
    		@endif
    	</div>
    	<br/>
    	<button type="submit">Nạp {{$detail['title']}}</button>
	</form>
	@else
		<p>Kênh nạp {{$detail['title']}} đang bảo trì</p>
	@endif	
@endforeach


@endsection
