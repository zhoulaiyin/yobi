<!DOCTYPE html>
<html>
<head>
    <meta charset="gb2312" />
    <meta name="applicable-device" content="pc">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <meta http-equiv="Cache-Control" content="no-transform" />
    <title>sortable</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            position: relative;
            color: #464637;
            min-height: 100%;
            font-size: 20px;
            font-family: 'Roboto', sans-serif;
            font-weight: 300;

        }
        html {
            background-image: -webkit-linear-gradient(bottom, #F4E2C9 20%, #F4D7C9 100%);
            background-image: -ms-linear-gradient(bottom, #F4E2C9 20%, #F4D7C9 100%);
            background-image: linear-gradient(to bottom, #F4E2C9 20%, #F4D7C9 100%);
        }
    </style>
    <style>
        .example {
            position: relative;
            height: 320px;
        }
        .example .brick:nth-child(20n+1) {
            background: #1abc9c;
        }
        .example .brick {
            opacity: 1;
            cursor: pointer;
            position: relative;
        }
        .example .brick.small {
            width: 140px;
            height: 140px;
        }
    </style>
    <style>

        h1 {
            color: #FF3F00;
            font-size: 20px;
            font-family: 'Roboto', sans-serif;
            font-weight: 300;
            text-align: center;
        }

        ul {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .container {
            width: 80%;
            margin: auto;
            min-width: 1100px;
            max-width: 1300px;
            position: relative;
        }

        @media (min-width: 750px) and (max-width: 970px){
            .container {
                width: 100%;
                min-width: 750px;
            }
        }

        .sortable-ghost {
            opacity: .2;
        }

        #foo .sortable-drag {
            background: #daf4ff;
        }

        img {
            border: 0;
            vertical-align: middle;
        }

        .logo {
            top: 55px;
            left: 30px;
            position: absolute;
        }

        .title {
            color: #fff;
            padding: 3px 10px;
            display: inline-block;
            position: relative;
            background-color: #FF7373;
            z-index: 1000;
        }
        .title_xl {
            padding: 3px 15px;
            font-size: 40px;
        }

        .tile {
            width: 22%;
            min-width: 245px;
            color: #FF7270;
            padding: 10px 30px;
            text-align: center;
            margin-top: 15px;
            margin-left: 5px;
            margin-right: 30px;
            background-color: #fff;
            display: inline-block;
            vertical-align: top;
        }
        .tile__name {
            cursor: move;
            padding-bottom: 10px;
            border-bottom: 1px solid #FF7373;
        }

        .tile__list {
            margin-top: 10px;
        }
        .tile__list:last-child {
            margin-right: 0;
            min-height: 80px;
        }

        .tile__list img {
            cursor: move;
            margin: 10px;
            border-radius: 100%;
        }

        .block {
            opacity: 1;
            position: absolute;
        }
        .block__list {
            padding: 20px 0;
            max-width: 360px;
            margin-top: -8px;
            margin-left: 5px;
            background-color: #fff;
        }
        .block__list-title {
            margin: -20px 0 0;
            padding: 10px;
            text-align: center;
            background: #5F9EDF;
        }
        .block__list li { cursor: move; }

        .block__list_words li {
            background-color: #fff;
            padding: 10px 40px;
        }
        .block__list_words .sortable-ghost {
            opacity: 0.4;
            background-color: #F4E2C9;
        }

        .block__list_words li:first-letter {
            text-transform: uppercase;
        }

        .block__list_tags {
            padding-left: 30px;
        }

        .block__list_tags:after {
            clear: both;
            content: '';
            display: block;
        }
        .block__list_tags li {
            color: #fff;
            float: left;
            margin: 8px 20px 10px 0;
            padding: 5px 10px;
            min-width: 10px;
            background-color: #5F9EDF;
            text-align: center;
        }
        .block__list_tags li:first-child:first-letter {
            text-transform: uppercase;
        }



        #editable {}
        #editable li {
            position: relative;
        }

        #editable i {
            -webkit-transition: opacity .2s;
            transition: opacity .2s;
            opacity: 0;
            display: block;
            cursor: pointer;
            color: #c00;
            top: 10px;
            right: 40px;
            position: absolute;
            font-style: normal;
        }

        #editable li:hover i {
            opacity: 1;
        }


        #filter {}
        #filter button {
            color: #fff;
            width: 100%;
            border: none;
            outline: 0;
            opacity: .5;
            margin: 10px 0 0;
            transition: opacity .1s ease;
            cursor: pointer;
            background: #5F9EDF;
            padding: 10px 0;
            font-size: 20px;
        }
        #filter button:hover {
            opacity: 1;
        }

        #filter .block__list {
            padding-bottom: 0;
        }

        .drag-handle {
            margin-right: 10px;
            font: bold 20px Sans-Serif;
            color: #5F9EDF;
            display: inline-block;
            cursor: move;
            cursor: -webkit-grabbing;  /* overrides 'move' */
        }

        #todos input {
            padding: 5px;
            font-size: 14px;
            font-family: 'Roboto', sans-serif;
            font-weight: 300;
        }

        #nested ul li {
            background-color: rgba(0,0,0,.05);
        }
    </style>
</head>

<body>

<div class='content'>

    <section class='example' style="display: none;">

        <div class='gridly' id="gridly">

            <div class='brick small'>
                <a class='delete' href='#'>&times;</a>
            </div>
            <div class='brick small'>
                <a class='delete' href='#'>&times;</a>
            </div>
            <div class='brick small'>
                <a class='delete' href='#'>&times;</a>
            </div>
            <div class='brick small'>
                <a class='delete' href='#'>&times;</a>
            </div>
            <div class='brick small'>
                <a class='delete' href='#'>&times;</a>
            </div>
            <div class='brick small'>
                <a class='delete' href='#'>&times;</a>
            </div>
            <div class='brick small'>
                <a class='delete' href='#'>&times;</a>
            </div>
            <div class='brick small'>
                <a class='delete' href='#'>&times;</a>
            </div>
            <div class='brick small'>
                <a class='delete' href='#'>&times;</a>
            </div>
            <div class='brick small'>
                <a class='delete' href='#'>&times;</a>
            </div>

        </div>

        <p class='actions'>
            <a class='add button' href='javascript:void(0);'>Add</a>
        </p>

    </section>

    <div class="container" style="margin-top: 100px">
        <div id="filter" style="margin-left: 30px">
            <div><div data-force="5" class="layer title title_xl">Editable list</div></div>
            <div style="margin-top: -8px; margin-left: 10px" class="block__list block__list_words">
                <ul id="editable">
                    <li id="111" >这是第一个元素<i class="js-remove">✖</i></li>
                    <li id="222" >这是第二个元素<i class="js-remove">✖</i></li>
                    <li id="333" >这是第三个元素<i class="js-remove">✖</i></li>
                </ul>

                <button id="addUser">Add</button>
            </div>
        </div>
    </div>

</div>
<script type="text/javascript" src="/js/webi/free.drag.js"></script>
<script type="text/javascript">

    var list = document.querySelectorAll('.brick');
    var list_length = list.length;
    for ( var k=0; k<list_length;k++){
        var index = parseInt(k)+1;
        list[k].setAttribute('id','gridly_'+index);
    }

    FreeDrag.create(document.getElementById('editable'),{
        mode: 3, //元素拖拽模式：0.自由拖拽（不考虑元素重叠） 1.自动重排（类流式布局） 2.网格布局  3.表格排序
        scale: 0, //是否改变父级元素宽高：0.否  1.是
//        draggable: '', //定义哪些元素可以进行拖放
        animation:500, //number 单位：ms，定义排序动画的时间；
        x_interval:10, //x轴方向上元素之间的间隔（用于网格布局）
        y_interval:10, //Y轴方向上元素之间的间隔（用于1,2模式）
        dragClass: "zlyClass", //拖动元素的class
        chosenClass: "sortable-chosen", //selector 格式为简单css选择器的字符串，当选中列表单元时会给该单元增加一个class
        ghostClass: "sortable-ghost" ,//副本元素样式
        onStart:function(FD,evt){
            console.log('触发回调====onStart');
            console.log(arguments);
        },
        onMove:function(FD,evt){
            console.log('触发回调====onMove');
            console.log(arguments);
        },
        onEnd:function(FD,evt){
            console.log('触发回调====onEnd');
            console.log(arguments);
        }
    });

</script>


</body>

</html>

