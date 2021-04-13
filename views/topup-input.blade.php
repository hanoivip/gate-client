@extends('hanoivip::layouts.app')

@section('title', 'Nạp thẻ (bước 2)')

@section('content')

@if ($params['available'])


<form method="POST" action="{{ route('webTopup') }}">
{{ csrf_field() }}
	@if ($params['has_captcha'])
		<img src="data:image/png;base64,{{$params['captcha']}}" 
		style="width: 166px;float: left;height: 52px;background-color: #fff;padding: 2px;">
		<input type="text" name="captcha" value=""/>
		<a href="{{ route('topup.recaptcha') }}">Mã khác</a>
	@endif
	Seri thẻ: <input type="text" name="serial" value=""/>
	@if ($errors->has('serial'))
        <span class="help-block">
            <strong>{{ $errors->first('serial') }}</strong>
        </span>
    @endif
	Mã thẻ: <input type="text" name="password" value=""/>
	@if ($errors->has('password'))
        <span class="help-block">
            <strong>{{ $errors->first('password') }}</strong>
        </span>
    @endif
    Giá trị đã khai báo: {{ $params['dvalue'] }} (chú ý báo sai sẽ mất {{ config('gate.wrong_declared_cutoff', 0) }} gía trị thật, nếu báo sai hãy click [Làm lại] để báo lại)
	<button type="submit">Nạp</button>
</form>

<a href="{{ route('topup.cancel') }}">Làm lại</a>

@else
<p>Hiện tại các hướng nạp cho loại thẻ này đang bận. Mời bạn thử lại trong 10 phút nữa.</p>
@endif

@endsection
