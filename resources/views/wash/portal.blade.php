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
                liffId: "{{ myConfig('line_message.LINE_LIFF_ID') }}" // Use own liffId
            }).then(() => {
                if (!liff.isLoggedIn()) {
                    liff.login();
                } else {
                    liff.getProfile()
                        .then(profile => {
                            const name = profile.displayName;
                            setMember(profile.userId);

                        })
                        .catch((err) => {
                            console.log('error', err);
                        });
                }
            }).catch((err) => {
                console.log('初始化失敗');
            });
        });

        function setMember(social_id) {


            $.post('/wash/set_member', {
                social_id: social_id
            }, function(data) {
                location.href = data.line_url;

            }, 'json');
        }


        var profile = [];
    </script>
    <style>
        body {
            /* padding: 20px; */
            font-family: Arial, sans-serif;
            background-color: #134B70;
            /* 主背景色改為深藍色 */
            color: #FFFFFF;
            /* 全局文字顏色改為白色 */
        }


        .form-group label {
            color: #FFFFFF;
            font-weight: bold;
        }

        .form-control {
            border-radius: 0;
            background-color: #FFFFFF;
            /* 表單輸入框背景改為藍色 */
            color: #134B70;
            /* 表單文字顏色為白色 */
            border: 1px solid #FFFFFF;
        }


        .btn {
            border-radius: 0;
            background-color: #508C9B;
            /* 按鈕背景色為黃綠色 */
            color: #FFFFFF;
            /* 按鈕文字顏色為深藍色 */
            font-weight: bold;
        }

        .btn:hover {
            background-color: #FFFFFF;
            /* 按鈕懸停時背景色變為白色 */
            color: #134B70;
            /* 按鈕文字顏色深藍 */
        }



        .welcome,
        .service-details,
        #totalAmount {
            background-color: #FFFFFF;
            color: #134B70;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-size: 1em;
            font-weight: bold;
            text-align: left;
            padding-left: 20px;
            /* display: flex; */
            /* justify-content: space-between; */
            /* align-items: flex-start; */
        }

        .service-details div {
            width: 88%;
        }

        .main {
            color: #508C9B
        }


        #basicAmount {
            margin-top: 30px;
            font-style: 1.2em;
            width: 50%;
            font-weight: bold;
        }

        #basicAmount,
        #totalAmount {
            font-weight: bold;
            color: #508C9B;
            /* 金額顏色為黃綠色 */
        }

        #basicAmount span,
        #totalAmount span {
            font-weight: bold;
            color: red;
            /* 金額顏色為黃綠色 */
        }

        .addition-service {
            background-color: #FFFFFF;
            color: #134B70;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-size: 1em;
            font-weight: bold;
            text-align: left;
            padding-left: 10px;
            /* Reduced padding */
        }

        .addition-service label {
            color: #134B70;
            /* display: flex;
            justify-content: space-between;
            align-items: center; */
        }

        select {
            font-size: 1em
        }

        label {
            margin-bottom: .1rem;
            font-size: 1em;

        }

        .form-control {
            font-size: 1em;

        }

        .form-group {
            margin-bottom: 0.6rem;
        }

        /*
        .addition-service input[type="checkbox"] {
            margin-right: 10px;
            transform: scale(2);
            width: 10px;
        } */

        /* .addition-service div {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            height: 45px;
        } */
        /*
        .addition-service .price {
            color: #FF4D4D;
            font-weight: bold;
            margin-left: 10px;
        }

        .addition-service .desc {
            width: 88%;
            text-align: left
        } */

        img {
            margin-top: 15px;
            margin-bottom: 15px;
        }

        h3 {
            color: #d0da4e;
        }

        .map-container {
            position: relative;
            width: 100%;
            height: 400px;
            background-color: #EEE;
            border: 1px solid #d0da4e;
        }

        .bottom-panel {
            background-color: #134B70;
            margin-top: 5px
        }

        .form-control {
            background-color: #FFFFFF;
            color: #134B70;
            border: 1px solid #FFFFFF;
            border-radius: 5px;
        }

        .btn {
            background-color: #508C9B;
            color: #FFFFFF;
            border-radius: 5px;
            width: 60%;
            margin-left: 20%;

        }

        .btn:hover {
            background-color: #FFFFFF;
            color: #134B70;
        }

        .parking-info {
            background-color: #FFFFFF;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: left;
        }

        .parking-info .place {
            color: #134B70;
            font-size: 18px;
            text-align: left;

        }

        .white {
            color: #FFFFFF;
        }

        .parking-info .address {
            color: #508C9B;
            font-size: 16px;
            text-align: left;

        }


        .parking-info .distance {
            color: #134B70;
            font-size: 16px;
            text-align: left;

        }

        #map {
            height: 400px;
            width: 100%;
        }

        /* 搜尋框的樣式 */
        #search-box {
            margin-top: 10px;
            padding: 10px;
            background-color: #FFFFFF;
            border: 1px solid #d0da4e;
            width: 100%;
        }

        #step2 {
            display: none;
        }

        .alert {
            background-color: #FF4D4D;
            color: white;
            font-size: 20px;
            display: none;
        }
    </style>
</head>
</head>

<body>

</body>
<script>
    var projects = [];
    var additions = [];


    function getProjects() {
        $.get('/wash/get_projects', {
            car_type: $('#car_type').val()
        }, function(data) {
            projects = data.projects;
            var select = $('#project');
            select.empty();
            for (var key in projects) {
                project = projects[key];
                select.append('<option value="' + project.id + '">' + project.name + '</option>');
            };
            $('#project_id').val(profile.project_id);
            getAdditions();
        }, 'json');
    }

    function getAdditions() {
        $.get('/wash/get_additions', {
            car_type: $('#car_type').val()
        }, function(data) {
            additions = data.additions;
            var select = $('#additions');
            select.empty();
            for (var key in additions) {
                addition = additions[key];
                select.append('<label onclick="calculateTotalAmount()" for="service' + addition.id +
                    '"><input type="checkbox" data-price="0" id="service' + addition.id +
                    '" name="addition_services[]" value="' + addition.id + '">' + addition.name +
                    '：<span></span></label></div>');
            };
            calculateTotalAmount();
            $('.addition-service input').on('click', function() {
                calculateTotalAmount();
            });
        }, 'json');
    }



    function validateLicense() {
        var license = document.getElementById('license').value;
        if (!license.includes('-')) {
            $('#step1alert').text('車牌格式不正確，必須包含 "-"。').show();
            return false; // 阻止表單提交
        }
        return true; // 允許表單提交
    }
    // 計算總金額的函數
    function calculateTotalAmount() {

        var totalAmount = 0;
        // 根據車型計算基本費用
        var carType = $('select[name="car_type"]').val();
        var project_id = $('select[name="project_id"]').val();

        if (!project_id) {
            return;
        }

        if ($('#is_member').val() == 1) {
            var use_price = 'discount_price';
            $('#price-cavas').html(
                '原價：<del id="orgAmount">元</del> 特價：<span id="basicAmount" style="color: red"> 元</span>');
        } else {
            var use_price = 'price';
            $('#price-cavas').html('<span id="basicAmount" style="color: red"> 元</span>');

        }


        totalAmount += parseInt(projects[project_id][use_price][carType]);


        var user_time = 0;
        //計算總需時間
        user_time += parseFloat(projects[project_id]['use_times'][carType]);
        //js nl2br

        $('#service_desc').html((addLineNumbers(projects[project_id].description)))

        $('#orgAmount').html(parseInt(projects[project_id].price[carType]) + '元');

        $('#basicAmount').html(totalAmount + ' 元');

        for (var key in additions) {


            $('#service' + key).attr('data-price', additions[key][use_price][carType]);
            $('#service' + key).parent().find('span').html(additions[key][use_price][carType] + ' 元');


        }

        // 加值服務的費用
        var additionServices = document.querySelectorAll('input[name="addition_services[]"]:checked');
        additionServices.forEach(function(service) {
            totalAmount += parseInt(additions[$(service).val()][use_price][carType]);
            user_time += parseFloat(additions[$(service).val()]['use_time']);
        });
        // 顯示總金額

        $('#totalAmount').html(totalAmount + ' 元 / 需時' + user_time + '小時');
        //exit time 必須 > entry time+user_time

        if (user_time == 0) {
            user_time = 2;
        }
        $entry_date = $('#entry_time').val();
        if (!$entry_date) {
            return;
        }
        $entry_time = $('#time').val();

        //最小可離開時間 為 $entry_time + user_time
        min_exit_time = new Date($entry_date + ' ' + $entry_time);
        //如果user_time>=48 則天從
        if (user_time >= 48) {
            min_exit_time = new Date($entry_date + ' 09:00');
            min_exit_time.setDate(min_exit_time.getDate() + 1);
        }

        min_exit_time.setHours(min_exit_time.getHours() + user_time);
        $('#exit_date').attr('min', formatDate(min_exit_time));

        select_exit_time = new Date($('#exit_date').val() + ' 09:00');
        if (min_exit_time.getTime() > select_exit_time.getTime()) {
            $('#exit_date').val(formatDate(min_exit_time));
        }
        $exit_date = $('#exit_date').val();
        $exit_time = $('#exit_time').val();
        //離場時間選項，必須扣除 進場時間+user_time 之前的選項
        var select = $('#exit_time');
        select.empty();
        availableTimes = {!! json_encode(config('wash.business_times')) !!};
        availableTimes.forEach(function(time) {
            select_time = new Date($exit_date + ' ' + time);
            console.log({
                time,
                select_time,
                min_exit_time,
                select
            })
            if (select_time.getTime() >= min_exit_time.getTime()) {
                console.log({
                    time
                })

                select.append('<option value="' + time + '">' + time + '</option>');
            }

        });












    }

    // 格式化日期為 YYYY-MM-DD
    function formatDate(date) {
        var year = date.getFullYear();
        var month = (date.getMonth() + 1).toString().padStart(2, '0');
        var day = date.getDate().toString().padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function addLineNumbers(text) {
        // 將字符串按換行符分割成多行
        const lines = text.split('\n');

        // 遍歷每一行，並在每行前面加上行號和點號
        const numberedLines = lines.map((line, index) => `${index + 1}. ${line}`);

        // 將處理後的行重新組合成一個字符串
        return nl2br(numberedLines.join('\n'));
    }

    function nl2br(str) {
        if (typeof str === 'string') {
            return str.replace(/\n/g, '<br>');
        }
        return str;
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
        $('#exit_date').val(entryTime)
        var date = new Date(entryTime);
        var day = date.getDay();
        // var select = $('#exit_time');
        // select.empty();
        // availableTimes = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'];
        // availableTimes.forEach(function(time) {
        //     select.append('<option value="' + time + '">' + time + '</option>');
        // });


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
            calculateTotalAmount();
        }, 'json');



    }

    function nextStep() {
        var name = document.getElementById('name').value;
        var phone = document.getElementById('phone').value;
        var license = document.getElementById('license').value;
        var model = document.getElementById('model').value;


        //若上述欄位有任一欄位為空值，則阻止表單提交
        if (!name) {
            $('#step1alert').text('請輸入姓名').show();
            return false; // 阻止表單提交
        }
        if (!phone) {
            $('#step1alert').text('請輸入手機號碼').show();
            return false; // 阻止表單提交
        }
        $licence = validateLicense();;
        if (!$licence) {
            return false; // 阻止表單提交
        }
        if (!model) {
            $('#step1alert').text('請輸入車款').show();
            return false; // 阻止表單提交
        }

        $.get('/wash/check_member/' + license, {}, function(data) {
            if (data.result) {
                $('#is_member').val(1);
            } else {
                $('#is_member').val(0);
            }
            calculateTotalAmount()
        }, 'json');



        $('#step1alert').text('').fadeOut();

        $('#cont').fadeOut();
        $('#step2').show();
        $('#phone').focus()

        //scroll to top step2
        $('html, body').animate({
            scrollTop: $('#step2').offset().top - 50
        }, 1000);



    }

    $(function() {

        getProjects();
        $('#entry_time').change(function() {
            calculateAvailableTime();
        });

        $('#exit_date').change(function() {
            calculateTotalAmount();
        });


        $('#car_type').change(function() {
            getProjects();
        });


        calculateTotalAmount();
        // 當表單送出時，檢查車牌格式

        $('select').change(function() {
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
