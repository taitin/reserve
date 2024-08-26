<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1,maximum-scale=1">
    <title>預約洗車時間變更</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- 在<head>標籤中加入Bootstrap的CSS連結 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
        integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <!-- Line liff JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script charset="utf-8" src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>

    <style>
        body {
            padding: 20px;
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .form-control {
            border-radius: 0;
        }

        .btn {
            border-radius: 0;
        }

        .form-group label {
            font-weight: bold;
            /* 確保內容靠左對齊 */
        }
    </style>
</head>
</head>

<body>
    <form action="/wash/{{ $wash->id }}/time_adjust" method="post">
        @csrf
        <h1>請選擇1~3組 日期時間供客戶選擇</h1>
        <div class="form-group">
            <label for="entryTime">洗車日期1</label>
            <input required type="date" class="form-control" id="entry_time1" name="date1"
                min="{{ date('Y-m-d') }}" value="{{ $wash->date }}">
        </div>
        <div class="form-group">
            <label for="exitTime">預約時間1</label>
            <select id="time1" name="time1">
                <option></option>
            </select>

        </div>
        <div class="form-group">
            <label for="entryTime">洗車日期2</label>
            <input required type="date" class="form-control" id="entry_time2" name="date2"
                min="{{ date('Y-m-d') }}" value="{{ $wash->date }}">
        </div>
        <div class="form-group">
            <label for="exitTime">預約時間2</label>
            <select id="time2" name="time2">
                <option></option>
            </select>

        </div>
        <div class="form-group">
            <label for="entryTime">洗車日期3</label>
            <input required type="date" class="form-control" id="entry_time" name="date3" min="{{ date('Y-m-d') }}"
                value="{{ $wash->date }}">
        </div>
        <div class="form-group">
            <label for="exitTime">預約時間3</label>
            <select id="time3" name="time3" value="{{ $wash->date }}">
                <option></option>
            </select>

        </div>
        <button type="submit" class="btn btn-info btn-block submit-btn">送出時間</button>
    </form>
</body>
<script>
    // 計算總金額的函數
    function calculateTotalAmount() {
        var totalAmount = 0;
        // 根據車型計算基本費用
        var carType = $('select[name="car_type"]').val();
        switch (carType) {
            case 'house':
                totalAmount += 1500;
                break;
            case '5p':
                totalAmount += 1600;
                break;
            case '7p':
                totalAmount += 1700;
                break;
        }
        $('#basicAmount').html(totalAmount + '元');
        // 加值服務的費用
        var additionServices = document.querySelectorAll('input[name="addition_services[]"]:checked');
        additionServices.forEach(function(service) {
            totalAmount += parseInt($(service).data('price'));
        });

        // 顯示總金額

        $('#totalAmount').html(totalAmount + '元');


    }

    function changeCity() {
        var city = $('#city').val();
        $('.city_options').hide();
        $('.city_options').removeAttr('name');
        $('#select_' + city).show();
        $('#select_' + city).attr('name', 'parking');
    }
    //根據日期計算可預約時間
    function calculateAvailableTime(index) {
        var entryTime = $('#entry_time' + index).val();
        var date = new Date(entryTime);
        var day = date.getDay();

        $.get('/wash/get_available_time', {
            date: entryTime
        }, function(data) {
            var availableTimes = [];
            // if (day == 0) {
            //     availableTimes = ['10:00', '11:00', '12:00'];
            // } else {
            //     availableTimes = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00'];
            // }
            availableTimes = data.available_times;
            var select = $('#time' + index);
            select.empty();
            availableTimes.forEach(function(time) {
                select.append('<option value="' + time + '">' + time + '</option>');
            });
        }, 'json');



    }

    $(function() {


        $('#entry_time1').change(function() {
            calculateAvailableTime(1);
        });
        $('#entry_time2').change(function() {
            calculateAvailableTime(2);
        });
        $('#entry_time3').change(function() {
            calculateAvailableTime(3);
        });
        calculateAvailableTime(1);

        calculateAvailableTime(2);

        calculateAvailableTime(3);


    });
    // 初始化計算一次總金額
</script>

</html>
