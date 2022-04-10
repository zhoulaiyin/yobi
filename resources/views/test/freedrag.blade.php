<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="gb2312" />
    <meta name="applicable-device" content="pc">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <meta http-equiv="Cache-Control" content="no-transform" />
    <title>自适应排版—freedrag</title>
    <style>
        body{
            font-family: 微软雅黑;
        }
        .notice{
            float:left;
            width: 200px;
        }
        .notice .title{
            font-size: 20px;
            font-weight: bold;
        }
        .content{
            float:right;
            width: 900px;
            height: 800px;
            margin-top:10px;
            border: 1px solid #CDCDCD;
            position: relative;
            margin: 0 auto;
            background-color: #F6E3CE;
        }
        .child{
            position: absolute;
            background-color: #FF8000;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            touch-action: none;
            z-index: 1;
        }
        .child-selected{
            box-shadow: 0 0 0 2px rgba(81,130,227,.15),inset 0 0 0 2px #108ee9 !important;
        }
        .child .cssTransforms {
            transition-property: transform;
        }
        child .react-item {
            transition: all .2s ease;
            transition-property: all;
            transition-property: left,top;
        }
        .react-item-create {
            position: absolute;
            background-color: #BCF5A9;
            touch-action: none;
            z-index: 0;
            opacity: 0.8;
        }

    </style>
</head>
<body>

<div class="notice">
    <p class="title">初始位置信息</p>
    <p id="init"></p>
    <p class="title" >动态位置信息</p>
    <p id="title"></p>
</div>

<div class='content' id="parent">

</div>

<script type="text/javascript" src="/js/webi/free.drag.js"></script>

<script type="text/javascript">

    var G_elems = {
        "111": {
            "id": "111",
            "left": 10,
            "top": 10,
            "width": 280,
            "height": 200
        },
        "222": {
            "id": "222",
            "left": 330,
            "top": 60,
            "width": 180,
            "height": 100
        },
        "333": {
            "id": "333",
            "left": 550,
            "top": 10,
            "width": 300,
            "height": 210
        },
        "444": {
            "id": "444",
            "left": 50,
            "top": 250,
            "width": 450,
            "height": 150
        },
        "555": {
            "id": "555",
            "left": 520,
            "top": 250,
            "width": 260,
            "height": 150
        }
    }

    var FD;

    var init = function() {
        for ( id in G_elems){
            var elem = document.createElement('div');
            elem.id = id;
            elem.className = 'child cssTransforms react-item';
            elem.style.top = G_elems[id].top+'px';
            elem.style.left = G_elems[id].left+'px';
            elem.style.width = G_elems[id].width+'px';
            elem.style.height = G_elems[id].height+'px';

            var titleDiv = document.createElement('div');
            titleDiv.innerText = id;
            titleDiv.className = 'react-title';
            titleDiv.style.width = '100%';
            titleDiv.style.height = '30px';
            titleDiv.style.backgroundColor = '#A9E2F3';
            titleDiv.style.cursor = 'pointer';

            var chartDiv = document.createElement('div');
            chartDiv.className = 'react-chart';
            chartDiv.style.width = '100%';
            chartDiv.style.height = (G_elems[id].height-30)+'px';

            var dragEl = document.createElement('div');
            dragEl.className = 'icon-drag';
            dragEl.style.width = '10px';
            dragEl.style.height ='10px';
            dragEl.style.position ='absolute';
            dragEl.style.bottom ='0px';
            dragEl.style.right = '0px';
            dragEl.style.backgroundColor = '#CBC1B8';
            dragEl.style.cursor = 'se-resize';

            elem.appendChild(titleDiv);
            elem.appendChild(chartDiv);
            elem.appendChild(dragEl);
            document.getElementById('parent').appendChild(elem);
        }

        var FD = FreeDrag.create(document.getElementById('parent'),{
            animation:500, //number 单位：ms，定义排序动画的时间
            draggable: '.react-title', //定义触发移动节点的class
            dragNodeClass: '.child', //定义移动节点的class
            scaleable: '.icon-drag', //定义触发缩放节点的class
            scaleNodeClass: '.child', //定义缩放节点的class
            x_interval:10, //x轴方向上元素之间的间隔
            y_interval:10, //Y轴方向上元素之间的间隔
            dragClass: "child-selected", //拖动元素的class
            onStart:function(re){
//                console.log('自动重排==触发回调====onStart');
            },
            onMove:function(re){
//                console.log(re);
//                console.log(re.handle_node.children);
//                if( re.match_mode == 2 ){
//                    re.handle_node.children[1].style.height = (re.handle_node.offsetHeight-re.handle_node.children[0].offsetHeight)+'px';
//                }
            },
            onEnd:function(re){
//                console.log('自动重排==触发回调====onEnd');
            }
        });
    };

    init();

</script>

</body>
</html>
