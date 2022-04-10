/**
 *  报表移动控制
 */
var _We_DRAG = {};
//触发鼠标的操作对象信息

_We_DRAG.drag = function(){
    var FD = FreeDrag.create(document.getElementById(WeBI.p_id),{
        mode:1,
        animation:500, //number 单位：ms，定义排序动画的时间
        draggable: '.oper-touch', //定义触发移动节点的class
        dragNodeClass: '.list-inner', //定义移动节点的class
        zoomable: '.icon-drag', //定义触发缩放节点的class
        zoomNodeClass: '.list-inner', //定义缩放节点的class
        x_interval:10, //x轴方向上元素之间的间隔
        y_interval:10, //Y轴方向上元素之间的间隔
        draggingClass: "", //拖动元素的class
        onMove:function(re){

            var grid_obj = re.handle_node;
            var title_obj = re.handle_node.children[0].children[0];
            var chart_obj = re.handle_node.children[0].children[1];

            var uid = chart_obj.getAttribute('id').substring(6);

            if( re.match_mode == 2 ){//放大缩小
                chart_obj.style.height = (grid_obj.offsetHeight-title_obj.offsetHeight) + 'px';

                if ( WeBI.op.bi_obj[uid] ) { //让报表自适应
                    WeBI.op.bi_obj[uid].resize();
                }

                if( WeBI.webi_dt['module'][uid]['attribute_json']['backgroundImage'] != '' ){ //让图片自适应
                    document.getElementById('chart_'+uid).style.backgroundSize = grid_obj.offsetWidth+'px '+chart_obj.style.height+'px';
                }

            }

        },
        onEnd:function(re){
            var grid_arr = {};

            var main_width = document.getElementById(WeBI.p_id).offsetWidth;
            var main_height = document.getElementById(WeBI.p_id).offsetHeight;

            var lis_We_Main = document.querySelectorAll('.top-grid');

            for(var i=0; i < lis_We_Main.length; i++){

               var uid = lis_We_Main[i].id.substring(5);
                if( uid == '$uid' ){
                    continue;
                }
                grid_arr[uid] = {
                        'top': lis_We_Main[i].offsetTop,
                        'height': lis_We_Main[i].offsetHeight,
                        'width': lis_We_Main[i].offsetWidth,
                        'top_percent': (lis_We_Main[i].offsetTop/main_height).toFixed(4),
                        'height_percent': (lis_We_Main[i].offsetHeight/main_height).toFixed(4),
                        'width_percent': (lis_We_Main[i].offsetWidth/main_width).toFixed(4),
                        'left_percent': (lis_We_Main[i].offsetLeft/main_width).toFixed(4)
                };

                WeBI.op.singleCount(uid);//更新module数据
            }

            _We_M.save_position(grid_arr);//保存位置信息
        }
    });
};



