<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1,maximum-scale=1">
    <title>我要預約洗車</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- 在<head>標籤中加入Bootstrap的CSS連結 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
        integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <!-- Line liff JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script charset="utf-8" src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
    <script>
        $(function() {
            liff.init({
                liffId: "{{ env('LINE_LIFF_ID') }}" // Use own liffId
            }).then(() => {
                if (!liff.isLoggedIn()) {
                    liff.login();
                } else {
                    liff.getProfile()
                        .then(profile => {
                            const name = profile.displayName;
                            $('#social_id').val(profile.userId);
                            getLastProfile()
                        })
                        .catch((err) => {
                            console.log('error', err);
                        });
                }
            }).catch((err) => {
                console.log('初始化失敗');
            });
        });

        function getLastProfile() {
            var social_id = $('#social_id').val();

            $.get('/wash/get_profile/' + social_id, {}, function(data) {

                if (data.result) {
                    $('#phone').val(data.data.phone);
                    $('#license').val(data.data.license);
                    $('#model').val(
                        data.data.model);
                    $('#car_type').val(data.data.car_type);
                    calculateTotalAmount();
                }
            }, 'json');
        }
    </script>
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
    <form onsubmit="return validateLicense()" action="/wash" method="post">
        @csrf
        <input type="hidden" id="social_id" name="social_id" value="">
        <div class="form-group">
            <label for="phone">手機號碼</label>
            <input required type="text" placeholder="0912345678" class="form-control" id="phone" name="phone">
        </div>
        <div class="form-group">
            <label for="license">車牌(如ABC-123)</label>
            <input required type="text" placeholder="ABC-123" class="form-control" id="license" name="license">
        </div>

        <div class="form-group">
            <label for="entryTime">洗車日期</label>
            <input required type="date" class="form-control" id="entry_time" name="date"
                min="{{ date('Y-m-d') }}">
        </div>
        <div class="form-group">
            <label for="exitTime">預約時間</label>
            <select id="time" name="time">
                <option></option>
            </select>

        </div>
        <div class="form-group">
            <label for="model">車款(廠牌及型號)</label>
            <input required type="text" placeholder="如 Toyota Rav4" class="form-control" id="model"
                name="model">
        </div>

        <div class="form-group">
            <label for="model">車型</label>
            <select name="car_type" id="car_type">
                <option value="house">轎車</option>
                <option value="5p">5人座休旅車</option>
                <option value="7p">7人座休旅車</option>
            </select>
        </div>


        <div class="form-group service-details">
            <p>
                根據您選擇的車型，本洗車服務提供的基本費用如下：
            <div id="basicAmount"></div>
            此費用包含<br />
            1.車身及玻璃深層清潔<br />
            2.手工打蠟瓷土美容<br />
            3.輪胎外框清潔<br />
            4.輪胎上蠟
            </p>
            <div id="serviceDetails"></div>
        </div>

        <div class="form-group radio addition-service ">
            <label for="additionService">額外加值服務的內容</label>
            <div><label for="service1">
                    <input type="checkbox" data-price="600" id="service1" name="addition_services[]" value="車輛前檔玻璃潑水">
                    車輛前檔玻璃潑水：600元</label>
            </div>
            <div><label for="service2">
                    <input type="checkbox" data-price="1500" id="service2" name="addition_services[]" value="全車玻璃潑水">
                    全車玻璃潑水：1500元</label>
            </div>
            <div><label for="service3">
                    <input type="checkbox" data-price="1500" id="service3" name="addition_services[]" value="鍍膜維護劑">
                    鍍膜維護劑：1500元</label>
            </div>
        </div>
        <div class="form-group ">
            <label for="totalAmount">總金額</label>
            <h3 id="totalAmount"></h3>
        </div>
        <button type="submit" class="btn btn-info btn-block submit-btn">送出預約</button>
    </form>
</body>
<script>
    function validateLicense() {
        var license = document.getElementById('license').value;
        if (!license.includes('-')) {
            alert('車牌格式不正確，必須包含 "-"。');
            return false; // 阻止表單提交
        }
        return true; // 允許表單提交
    }
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
    function calculateAvailableTime() {
        var entryTime = $('#entry_time').val();
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
            var select = $('#time');
            select.empty();
            availableTimes.forEach(function(time) {
                select.append('<option value="' + time + '">' + time + '</option>');
            });
        }, 'json');



    }

    $(function() {


        $('#entry_time').change(function() {
            calculateAvailableTime();
        });






        calculateTotalAmount();
        // 當表單送出時，檢查車牌格式

        $('select').change(function() {
            calculateTotalAmount();
        });
        $('.addition-service input').on('click', function() {
            calculateTotalAmount();
        });
        changeCity();

        $('#city').change(function() {
            changeCity()
        });
    });
    // 初始化計算一次總金額
</script>

</html>
