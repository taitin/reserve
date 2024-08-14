<!DOCTYPE html>
<html>

<head>
    <title>洗車付款</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1,maximum-scale=1">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

</head>

<body>
    @if ($wash->price != 0 && $wash->status == 'paid')
        <h1>付款資訊已送出，請等待客戶付款</h1>
    @else
        <form action="/wash/{{ $wash->id }}/set_amount" method="post">
            @csrf
            <h1></h1>
            <input type="hidden" name="wash_id" value="{{ $wash->id }}">
            <h1>車牌:{{ $wash->license }}</h1>
            <h1>地點:{{ $wash->parking }}</h1>

            <h1>車型:{{ $wash->model }}</h1>
            {{-- <h2>其他服務:</h2> --}}
            <input type="number" name="amount" value="1500">
            <input type="submit" value="設定付款金額">
        </form>
    @endif
</body>

</html>
