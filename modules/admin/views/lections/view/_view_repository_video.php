<?
use yii\grid\GridView;
use yii\helpers\Html;
use app\models\LectionsSearch;
use app\models\User;
use yii\helpers\Url;

$this->title = 'Мультимедиа-лекторий | '. $model['LECTION']['lection_name'];
$this->registerMetaTag(['name' => 'keywords', 'content' => $model['LECTION']['keywords']]);
$this->registerMetaTag(['name' => 'description', 'content' => $model['LECTION']['description']]);


//ML_TODO: DOWORK просмотр лекции в админке; воспроизведение команд
$arLection = [
    'video'=> $model['LECTION'],
    'demonstrations'=> $model['DEMO_ITEMS'],
    'commands' => $model['COMMANDS_ITEMS'],
];
$json = json_encode($arLection, JSON_UNESCAPED_UNICODE);

if ($model['LECTION']['file_src'])
{
    $video_src = $model['LECTION']['file_src'];
    $JSscript_str = '';
    $video_ext = pathinfo($model['LECTION']['file_src'])['extension'];
}
?>
<script>
    currentDemo = null;

    $(document).ready(function(){
        var json = <?=$json?>;

        //console.log('json');
        //console.log(json.commands);

        //добавляем иконки из списка json
        for (var i = 0; i < json.demonstrations.length; i++)
        {
            $('<div class="demo-icon-item"></div>')
                .append('<img data-src="" ' +
                    'class="img-rounded icon_item" ' +
                    'id="'+json.demonstrations[i].id+'"' +
                    'alt="'+json.demonstrations[i].id+'" ' +
                    'data-slide_time="'+json.demonstrations[i].time+'"'+
                    'data-slider_id="'+json.demonstrations[i].id+'" '+
                    'src="'+json.demonstrations[i].src+'">' +
                    '<span>'+timeFormat(Math.round(json.demonstrations[i].time))+'</span>')
                .appendTo('.icon_list');

        }

        //переключение слайда по клику
        $(document).delegate('.icon_item','click', function () {
            console.log($(this).data('slider_id'));
            console.log($(this).data('slide_time'));
            console.log($(this).attr('src'));
            var video = document.getElementById("video");
            video.play();
            video.currentTime = $(this).data('slide_time');

            $('.icon_item').css('border', '1px solid #000000;');
        });


        $("#video").on("timeupdate", function(event){
            onTrackedVideoFrame(this.currentTime, this.duration);
        });
        function onTrackedVideoFrame(currentTime, duration){
            $("#current").text(currentTime); //Change #current to currentTime
            $("#duration").text(duration);

            //переключение демонстрациий по текущему времени видео
            //вывод демонстрациии
            for (var i = 0; i < json.demonstrations.length; i++)
            {
                console.log(json.demonstrations[i]);
                //if (json.demonstrations[i].time <= currentTime && json.demonstrations[i+1].time >= currentTime)
                if (json.demonstrations[i].time <= currentTime)
                {
                    console.log('55');
                    if(json.demonstrations[i].type == 'slide')
                    {

                        var slide = document.getElementById("demo_view_block");
                        slide.innerHTML = '';
                        var newCanvas = document.createElement('canvas');
                        newCanvas.setAttribute('id', 'demo_play_canvas');
                        slide.appendChild(newCanvas);


                        var canvas = document.getElementById("demo_play_canvas");
                        ctx = canvas.getContext('2d');

                        canvas.width = canvas.offsetWidth;
                        canvas.height = canvas.offsetHeight;

                        var pic = new Image();

                        pic.src = json.demonstrations[i].src;

                        ctx.drawImage(pic, 0, 0, canvas.width, canvas.height);
                        //ctx.clearRect(0, 0, canvas.width, canvas.height);
                        $('.icon_item').css('border', '1px solid #000000');
                        $('#'+json.demonstrations[i].id).css('border', '3px solid #ff0000');

                        currentDemo = json.demonstrations[i].src;
                    }
                }
            }

            if(json.commands.length > 0)
            {
                //выполнение команды по текущему времени видео
                for (var i = 0; i < json.commands.length; i++)
                {
                    if (json.commands[i].time <= currentTime)
                    {
                        playCommand(json.commands[i]);
                    }
                }
            }
        }
    });

    //удобое представление времени
    var timeFormat = (function (){
        function num(val){
            val = Math.floor(val);
            return val < 10 ? '0' + val : val;
        }

        return function (ms){
            var sec = ms
                , hours = sec / 3600  % 24
                , minutes = sec / 60 % 60
                , seconds = sec % 60
            ;

            return num(hours) + ":" + num(minutes) + ":" + num(seconds);
        };
    })();


    // Воспроизведение команд
    function playCommand(command)
    {
        var currentCommandArr = JSON.parse(command.command);
        switch(currentCommandArr.command)
        {
            case 'draw_line':
                draw_line(
                    currentCommandArr.wCanvas,
                    currentCommandArr.hCanvas,
                    currentCommandArr.x1,
                    currentCommandArr.y1,
                    currentCommandArr.x2,
                    currentCommandArr.y2,
                    currentCommandArr.color
                );
                break;
            case 'clear_slide':
                console.log('clear_slide');
                clear_slide();
                break;
            default:
                console.warn('nothing');
                break;
        }
    }

    function draw_line(width,height,x1,y1,x2,y2,color)
    {
        var canvas = document.getElementById("demo_play_canvas");
        ctx = canvas.getContext('2d');

        /*
        var widthRatioSize=canvas.offsetWidth/width;
        var heightRatioSize=canvas.offsetHeight/height;
        ctx.strokeStyle = color_2D;
        ctx.beginPath();
        ctx.moveTo(x1*widthRatioSize, y1*heightRatioSize);
        ctx.lineTo(x2*widthRatioSize, y2*heightRatioSize);
        ctx.stroke();
        */
        ctx.strokeStyle = color;
        ctx.beginPath();
        ctx.moveTo(x1, y1);
        ctx.lineTo(x2, y2);
        ctx.stroke();
    }

    function clear_slide()
    {
        var canvas = document.getElementById("demo_play_canvas");
        ctx = canvas.getContext('2d');
        canvas.width = canvas.offsetWidth;
        canvas.height = canvas.offsetHeight;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        var pic = new Image();

        pic.src = currentDemo;
        ctx.drawImage(pic, 0, 0, canvas.width, canvas.height);
    }
</script>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1 class="h1-responsive"><?=$this->title?></h1>
        </div>
    </div>
    <hr>
    <?
    //Yii::$app->userHelperClass->pre($model);
    ?>
    <ul class="nav nav-tabs tabs-light-green darken-1" role="tablist">
        <li class="nav-item">
            <a class="nav-link waves-light active" href="/admin/lections/index/">Лекции</a>
        </li>
        <li class="nav-item">
            <a class="nav-link waves-light" href="/admin/lections/slides/">Слайды</a>
        </li>
        <li class="nav-item">
            <a class="nav-link waves-light" href="/admin/scenarios/" role="tab">Сценарии</a>
        </li>
        <li class="nav-item">
            <a class="nav-link waves-light" href="/admin/video/" role="tab">Видео</a>
        </li>
    </ul>
    <br><br><br>
    <?
    //Yii::$app->userHelperClass->pre($model);
    ?>
    <div class="row">
        <div class="col-md-6 ">
            <div class="embed-responsive embed-responsive-16by9">
                <video id="video" controls="controls" poster="<?=$model['LECTION']['poster']?>" preload="none">
                    <source src="<?=$video_src?>" type="video/<?=$video_ext?>"/>
                </video>
                <!--
                <video id="video" controls="" poster="/repository/user/lections/1/1342067789683.jpg" preload="none">
                    <source src="/repository/user/video/1/martynko.mp4" type="video/mp4">
                    <source src="/repository/user/video/1/martynko.webm" type="video/webm">
                </video>
                -->
            </div>
        </div>
        <div class="col-md-6">
            <div class="demo-block card card-body" id="demo_view_block">
                <canvas id="demo_play_canvas"></canvas>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <p>Просмотров: <?=$model['LECTION']['view_count']?></p>
            <br>
            <div id="current">0:00</div>
            <div id="duration">0:00</div>
            <br>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="slide-list-block card card-body">
                <div class="thumb icon_list" id="icon_list">
                </div>
            </div>
            <?
            if($model['LECTION']['content'])
            {
                ?>
                <div class="lection-description-block card card-body">
                    <p><?=$model['LECTION']['content']?></p>
                </div>
                <?
            }
            ?>

            <div class="other-lections-block card card-body">
                <p>Похожие лекции</p>
            </div>
        </div>

    </div>
</div>