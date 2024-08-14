<!DOCTYPE html>
<html>

<head>
    <title>洗車到達時間</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1,maximum-scale=1">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

</head>

<body>
    <form action="/wash/arrange" method="post">
        @csrf
        <h1>請輸入師傅預計到達時間</h1>
        <input type="hidden" name="wash_id" value="{{ $wash->id }}">
        <input type="datetime-local" name="arrive_at" value="">
        <input type="submit" value="通知客戶">
    </form>
</body>

</html>
