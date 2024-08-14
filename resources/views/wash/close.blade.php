<!DOCTYPE html>
<html>

<head>
    <title>快點洗</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1,maximum-scale=1">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <script charset="utf-8" src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>

</head>

<body>

    <h1>{{ $message }}</h1>
    <input type="button" value="關閉頁面" onclick="closeWindow()">


    <script>
        function closeWindow() {
            liff.closeWindow();
            window.close();
        }
        window.onload = function() {
            setTimeout(closeWindow, 3000);
        }
    </script>
</body>


</html>
