@extends('webi.layout')

@section('title')
    Echarts测试页面
@endsection

@section('css')

@endsection

@section('content')

    <div id="column" style="width: 100%;height:400px;"></div>

    <div id="pie" style="width: 100%;height:400px;"></div>


@endsection

@section('js')

    <script type="text/javascript">

        var stat_data = {!! $statdata !!};

        var commpie_data = new Array();

        if( !$.isEmptyObject(stat_data) ){
            $.each(stat_data,function(k,v){

                //饼图数据
                commpie_data.push({
                    value:v.sale_money,
                    name:v.cal_date
                });

            });
        }

        //存储echarts对象
        var echarts_obj = {
            '11': '',
            '22': ''
        };

        //横向柱状图
        echarts_obj['11'] = echarts.init(document.getElementById('column'));
        var crosswise_option = {
            title: {
                text: '世界人口总量',
                subtext: '数据来自网络'
            },
            dataZoom: [
                {
                    type: 'slider',
                    show: true,
                    yAxisIndex: [0],
                    left: '97%',
                    start: 30,
                    end: 100
                },
                {
                    type: 'inside',
                    yAxisIndex: [0],
                    start: 30,
                    end: 100
                }
            ],
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow'
                }
            },
            legend: {
                data: ['2011年', '2012年','2017年']
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: {
                type: 'value',
                boundaryGap: [0, 0.01]
            },
            yAxis: {
                type: 'category',
                data: ['巴西','印尼','美国','印度','中国','世界人口(万)']
            },
            series: [
                {
                    name: '2011年',
                    type: 'bar',
                    data: [18203, 23489, 29034, 104970, 131744, 630230]
                },
                {
                    name: '2012年',
                    type: 'bar',
                    data: [19325, 23438, 31000, 121594, 134141, 681807]
                },
                {
                    name: '2017年',
                    type: 'line',
                    data: [20325, 24438, 33000, 126594, 138141, 701807]
                }
            ]
        };

        echarts_obj['11'].setOption(crosswise_option);

        //普通饼图
        echarts_obj['22'] = echarts.init(document.getElementById('pie'));

        echarts_obj['22'].setOption(
            {
                noDataLoadingOption: {
                    text: '暂无数据',
                    effect: 'bubble',
                    effectOption: {
                        effect: {
                            n: 0
                        }
                    }
                },
                title : {
                    text: '每日销售统计—饼图',
                    subtext: '客户数据',
                    x:'center'
                },
                tooltip : {
                    trigger: 'item',
                    formatter: "{a} <br/>{b} : {c} ({d}%)"
                },
                legend: {
                    orient: 'vertical',
                    left: 'left',
                    data: []
                },
                series : [
                    {
                        name: '销售量',
                        type: 'pie',
                        radius : '55%',
                        center: ['50%', '60%'],
                        data:commpie_data,
                        itemStyle: {
                            emphasis: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        }
                    }
                ]
            }
        );

        /**
         * 所有的鼠标事件包含参数 params，这是一个包含点击图形的数据信息的对象，如下格式：

         {
             // 当前点击的图形元素所属的组件名称，
             // 其值如 'series'、'markLine'、'markPoint'、'timeLine' 等。
             componentType: string,
             // 系列类型。值可能为：'line'、'bar'、'pie' 等。当 componentType 为 'series' 时有意义。
             seriesType: string,
             // 系列在传入的 option.series 中的 index。当 componentType 为 'series' 时有意义。
             seriesIndex: number,
             // 系列名称。当 componentType 为 'series' 时有意义。
             seriesName: string,
             // 数据名，类目名
             name: string,
             // 数据在传入的 data 数组中的 index
             dataIndex: number,
             // 传入的原始数据项
             data: Object,
             // sankey、graph 等图表同时含有 nodeData 和 edgeData 两种 data，
             // dataType 的值会是 'node' 或者 'edge'，表示当前点击在 node 还是 edge 上。
             // 其他大部分图表中只有一种 data，dataType 无意义。
             dataType: string,
             // 传入的数据值
             value: number|Array
             // 数据图形的颜色。当 componentType 为 'series' 时有意义。
             color: string
         }
         */

        for (k in echarts_obj) {
            bind(k);
        }

        function bind(uid) {
            echarts_obj[uid].on('click', function (params) {
                chartsClick(uid,params);
            });
        }

        function chartsClick(uid,params) {
            console.log('uid--'+uid);
            console.log(params);
        }

    </script>

@endsection