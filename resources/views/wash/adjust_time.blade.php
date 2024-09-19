<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1,maximum-scale=1">
    <title>預約洗車時間或車型變更</title>
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
    <form id="timeForm" action="/wash/{{ $wash->id }}/time_adjust" method="post">
        @csrf
        <h1>預約申請調整</h1>
        <h2>請選擇欲調整「車型」或「預約進場時間」供客戶重新確認</h2>
        <div class="gray">
            <h3>客戶預約時間：{{ zhDate($wash->date . ' ' . $wash->time) }}</h3>
            <h3>客戶取車時間：{{ zhDate($wash->exit_date . ' ' . $wash->exit_time) }}</h3>
        </div>
        <div class="form-group">
            <label for="entryTime">時段申請建議 1</label>
            <input type="date" class="form-control" id="entry_time1" name="date1" min="{{ date('Y-m-d') }}"
                value="{{ $wash->date }}">
        </div>
        <div class="form-group">
            <select id="time1" name="time1">
                <option></option>
            </select>

        </div>
        <div class="form-group">
            <label for="entryTime">時段申請建議2</label>
            <input type="date" class="form-control" id="entry_time2" name="date2" min="{{ date('Y-m-d') }}"
                value="{{ $wash->date }}">
        </div>
        <div class="form-group">
            <select id="time2" name="time2">
                <option></option>
            </select>

        </div>
        <div class="form-group">
            <label for="entryTime">時段申請建議3</label>
            <input type="date" class="form-control" id="entry_time3" name="date3" min="{{ date('Y-m-d') }}"
                value="{{ $wash->date }}">
        </div>
        <div class="form-group">
            <select id="time3" name="time3" value="{{ $wash->date }}">
                <option></option>
            </select>

        </div>

        <div>
            客戶預約車型：{{ carType($wash->car_type) }}<br />
            客戶預約車款：{{ $wash->model }}
        </div>

        <div class="form-group">
            <select name="car_type" value="{{ $wash->car_type }}">
                @foreach (config('wash.car_types') as $key => $car_type)
                    <option value="{{ $key }}" {{ $key == $wash->car_type ? 'selected' : '' }}>
                        {{ $car_type }}</option>
                @endforeach
            </select>

        </div>



        <button type="button" onclick="submitForm(0)" class="btn btn-info btn-block submit-btn">確認送出</button>
        <button type="button" onclick="setReturn();closeWindow()"
            class="btn btn-info btn-block submit-btn">返回預約申請回覆</button>

    </form>
</body>
<script>
    function setReturn() {
        $.post('/wash/set_return', {
            wash_id: {{ $wash->id }},
            '_token': '{{ csrf_token() }}'
        }, function(data) {
            console.log(data);
        }, 'json');

    }


    function submitForm(change_car_type) {

        // 提交表單
        document.getElementById('timeForm').submit();
    }

    function closeWindow() {
        liff.closeWindow();
        window.close();
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
            // availableTimes = ['10:00', '11:00', '12:00'];
            // } else {
            // availableTimes = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00'];
            // }
            availableTimes = data.available_times;
            var select = $('#time' + index);
            select.empty();
            select.append('<option value="">--請選擇時間--</option>');

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
