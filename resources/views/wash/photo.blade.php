<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1,maximum-scale=1">
    <title>{{ $title }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- Line liff JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- boostrap-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        #fullscreenImage {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            display: none;
            background: rgba(0, 0, 0, 0.8);
        }

        #fullscreenImage img {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            max-width: 90%;
            max-height: 90%;
        }

        #prev,
        #next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: white;
            padding: 10px;
            border: none;
            cursor: pointer;
        }

        #prev {
            left: 10px;
        }

        #next {
            right: 10px;
        }


        .switchBtn {
            background-color: transparent;
            color: black;
            font-weight: bold
        }
    </style>

</head>

<body>
    <!--自適應顯示照片 !-->
    <div class="container">
        <div class="row">
            <!-- 電腦4張 平板2張 手機2張 !-->
            @foreach ($photos as $photo)
                <div class="col-md-3 col-sm-6 col-6">
                    <img src="{{ asset('storage/' . $photo) }}" class="img-fluid">
                </div>
            @endforeach
        </div>
    </div>

    <div id="fullscreenImage">
        <img src="" alt="Fullscreen Image">
        <button class="switchBtn" id="prev">＜</button>
        <button class="switchBtn" id="next">＞</button>
    </div>

    <script>
        $(document).ready(function() {
            var images = $('.img-fluid');
            var currentIndex = 0;
            var startX;

            images.click(function() {
                currentIndex = images.index(this);
                $('#fullscreenImage img').attr('src', $(this).attr('src'));
                $('#fullscreenImage').fadeIn();
            });

            $('#prev').click(function() {
                currentIndex = (currentIndex > 0) ? currentIndex - 1 : images.length - 1;
                $('#fullscreenImage img').attr('src', $(images[currentIndex]).attr('src'));
            });

            $('#next').click(function() {
                currentIndex = (currentIndex < images.length - 1) ? currentIndex + 1 : 0;
                $('#fullscreenImage img').attr('src', $(images[currentIndex]).attr('src'));
            });

            $('#fullscreenImage').on('touchstart', function(e) {
                startX = e.originalEvent.touches[0].clientX;
            });

            $('#fullscreenImage').on('touchend', function(e) {
                var endX = e.originalEvent.changedTouches[0].clientX;
                var diffX = endX - startX;

                if (diffX > 0) {
                    $('#prev').click();
                } else if (diffX < 0) {
                    $('#next').click();
                }
            });

            $('#fullscreenImage').click(function(e) {
                if (e.target !== this) return;
                $(this).fadeOut();
            });
        });
    </script>

</body>

</html>
