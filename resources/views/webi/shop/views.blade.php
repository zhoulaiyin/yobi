@extends('webi.layout')

@section('css')
    <link rel="stylesheet" href="/css/webi/list.css?v=201803221330">
    <link rel="stylesheet" href="/libs/webuploader/webuploader.css" type="text/css"/>
    <style>
        .cur-list{
            padding-left: 18px;
            color: #fff;
            background: #70a7ff;
        }
        .webuploader-pick{
            background: #fff;
            height:100%;
            margin: 0 auto;
            width: 100%;
        }
        .dropdown-menu{
            min-width: 60px;
            margin-top: -10px;
        }
        .linked-content{
            position: relative;
            width:100%;
            float: left;
            display: block;
            overflow: hidden;
            height: 100%;
        }
        .linked-list {
            display:none;
            float: right;
            width:25%;
            right: -15px;
            /*position: absolute;*/
            overflow-y: auto;
            background-color: #F7F7F7;
            height: 100%;
            min-height: 550px;
        }
        .linked-list li{
            margin: 0px 18px 0px;
            height:40px;
            line-height: 40px;
            text-align: center;
            margin-bottom: 7px;
            border-radius: 5px;
            background: #d9e2f2;
        }
        .linked-list li a{
            line-height:40px;
            height:40px;
        }
        .input-search{
            border-bottom: 1px solid #ddd;
            width: 97%;
            float: left;
            position: relative;
            padding-top: 5px;
            margin-left: 1%;
        }
        .input-search i{
            position: absolute;
            right: 0;
            bottom: 0;
            width: 40px;
            height: 34px;
            background: url(/images/webi/bi/icon-search.png) no-repeat center center;
            cursor: pointer;
        }

        .ul-list{
            padding-top: 10px;
        }
        .add-list a:hover{
            text-decoration:none;
        }
        /*right-cont ??????100%*/
        .right-cont-w{
            position: relative;
            width: 98.4%;
            float: left;
            /*left: 50px;*/
            height: 550px;
            overflow-x: scroll;
            white-space: nowrap;
        }
        .right-cont{
            top: 58px;
            position: fixed;
            width: 63%;
            overflow-x: scroll;
            cursor: pointer;
            height: 550px;
            white-space: nowrap;
        }
        .test-t{
            border: solid 1px #ddd;
            text-align: center;
            width: 240px;
            height:36px;
            padding: 10px;
            margin: 10px 0 ;
            -webkit-border-radius: 15px;
            -moz-border-radius: 15px;
            -ms-border-radius:15px;
            -o-border-radius:15px;
        }
        .parallel{
            display:inline-block
        }
        /*group-nav li ???????????????*/
        .liclick {
            border-left: 2px solid #70a7ff;
            background: #fff;
            border-bottom: 1px solid #fff;
        }
        .liclick>div >a{
            color: #70a7ff !important;
        }
        .input-search .form-control{
            border:none;
            -webkit-box-shadow:none;
        }
        .ul-list li span{
            width: 100%;
            display: block;
            padding: 0 20px;
            overflow: hidden;
            text-overflow:ellipsis;
            white-space: nowrap;
        }
        .fl {
            float: left;
        }
        .fr {
            float: left !important;
        }
        .right-cont-fl{
            width: 200px;
            padding-top: 55px;
            position: relative;
            display:inline-block;
            float: left;
        }
        .right-cont-fl span ,.right-cont-fr-text span{
            height: 30px;
            line-height: 30px;
            border: 1px solid #ddd;
            border-radius: 15px;
            -moz-border-radius:15px;
            -ms-border-radius:15px;
            -o-border-radius:15px;
            padding: 0 15px;
            width: 85%;
            display: block;
            margin-left: 10%;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .right-cont-fr-text span {
            margin-left: 0;
            width: 100%;
        }
        .right-cont-fl .glyphicon,.right-cont-fr-text .glyphicon{
            position: absolute;
            right: 5%;
            padding-right: 15px;
            top:56.4px;
            color: #999999;
            width: 10px;
            height: 10px;
            margin: 10px;
            display: block;
            cursor: pointer;
            background:  url("/images/webi/jointype1.png") 0    -147px  no-repeat;
        }
        .right-cont-fr-text .glyphicon{
            position: absolute;
            right: 5px;
            top:-12px;
        }
        .right-cont-fl em{
            height: 2px;
            background: #ddd;
            width: 5%;
            display: block;
            margin-top: 15px;
        }
        .right-cont-fr{
            width: 350px;
            padding: 0px;
            /*padding-top: 70px;*/
            display:inline-block;
            float: none;
            position: absolute;
        }
        .right-cont-fr ul,.right-cont-fr li{
            display: block;
        }
        .right-cont-fr li {
            padding-bottom: 62.7px;
            position: relative;
        }
        .right-cont-fr li:first-child .fight-cont-fl-left {
            border-top-right-radius: 15px;
        }
        .right-cont-fr li:first-child em{
            border-bottom-left-radius:0;
            margin-top:-5px;
            border-left: none;
        }
        .right-cont-fr li:last-child .fight-cont-fl-left {
            border-right:  none;
        }
        .right-cont-fr-img{
            width: 40%;
            position: relative;
            margin-top: -13px;
        }
        .right-cont-fr li:first-child .imgbox{
            top:-3px !important;
        }
        .right-cont-fr li .imgbox{
            width: 44px;
            height: 28px;
            display: block;
            position: absolute;
            top:4px;
            left: 50%;
            margin-left: -14px;
        }
        .right-cont-fr-img span{
            width: 44px;
            height: 28px;
            display: block;
            position: absolute;
            top:-4px;
            left: 50%;
            margin-left: -17px;
            cursor: pointer;
        }
        .right-cont-fr-img .left-img{
            background: #fff url("/images/webi/jointype1.png") 0   -28px no-repeat;
        }
        .right-cont-fr-img .right-img{
            background: #fff url("/images/webi/jointype1.png") 0    -56px  no-repeat;
        }
        .right-cont-fr-img .inner-img{
            background: #fff url("/images/webi/jointype1.png") no-repeat;
        }
        .right-cont-fr-img em{
            height: 19px;
            border-bottom: 2px solid #ddd;
            display: block;
            width: 100%;
            border-left: 2px solid #ddd;
            border-bottom-left-radius: 15px;
        }
        .right-cont-fr-text{
            width: 60%;
            margin-left: 0;
            margin-top: -12px;
        }
        .fight-cont-fl-left{
            /*height: 60px;*/
            height: 56.8px;
            position: absolute;
            left: -3px;
            width: 5px;
            border-right: 2px solid #ddd;
        }
        .bomb-box{
            width: 500px;
            overflow: hidden;
            padding: 5px 10px;
        }
        .bomb-box  li{
            width: 33.33%;
            float: left;
            height: 80px;
            margin: 0 auto;
            text-align: center;
            cursor: pointer;
        }
        .bomb-box li span{
            margin: 20px auto 0;
            width: 44px;
            height: 28px;
            display: block;
        }
        .bomb-box li .inner-img{
            background: #fff url("/images/webi/jointype1.png") no-repeat;
        }
        .bomb-box li .left-img{
            background: #fff url("/images/webi/jointype1.png") 0   -28px no-repeat;
        }
        .bomb-box li .right-img{
            background: #fff url("/images/webi/jointype1.png") 0    -56px  no-repeat;
        }
        /*.bomb-box ul li???????????????????????????*/
        .clickli{
            background: #f1f1f1;
        }
        .clickli  .inner-img{
            background: #f1f1f1 url("/images/webi/jointype1.png") no-repeat !important;
        }
        .clickli  .left-img{
            background: #f1f1f1 url("/images/webi/jointype1.png") 0   -28px no-repeat !important;
        }
        .clickli  .right-img{
            background: #f1f1f1 url("/images/webi/jointype1.png") 0    -56px  no-repeat !important;
        }
        .bomb-box li p{
            line-height: 35px;
            height: 35px;
        }
        .bomb-box-option{
            width: 100%;
        }
        .bomb-box-option ul{
            overflow: hidden;
        }
        .bomb-box-option ul li{
            margin: 2px 0;
            padding: 0 10px;
            height: 30px;
            line-height: 30px;
            display: block;
            width: 100%;
            background: #fff;
        }
        .bomb-box-option ul li:first-child{
            background: #f1f1f1;
        }
        /*.bomb-box-option ul li:first-child .bomb-box-equal{*/
            /*font-size: 0;*/
        /*}*/
        .bomb-box-option ul li:first-child .bomb-box-delete i{
            display: none;
        }
        .bomb-box-option select{
            width: 100%;
            border:none;
            padding-left: 10px;
            height: 30px;
            line-height: 30px;
            background: url("/images/webi/incon-down.png") no-repeat scroll right 10px center transparent;
        }
        .bomb-box-select{
            width: 42%;
            position: relative;
        }
        .bomb-box-select select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }
        .bomb-box-option p{
            text-align: center;
            color: #b9b9b9;
            height: 30px;
            line-height: 30px;
        }
        /*bomb-box-select ???select ????????????*/
        .hover-option{
            color: #f94c43;
            border:1px solid #f94c43 !important; ;
            background: url("/images/webi/incon-down-h.png") no-repeat scroll right 10px center transparent !important;
        }
        .bomb-box-equal{
            width: 8%;
            text-align: center;
        }
        .bomb-box-but{
            border-top: 1px solid #ddd;
            height: 55px;
            padding-top: 10px;
        }
        .bomb-box-but button{
            height: 35px;
            line-height: 35px;
            width: 75px;
            text-align: center;
            margin-left: 10px;
            background: #fff;
            border-radius:5px;
            -moz-border-radius:5px;
            -ms-border-radius:5px;
            -o-border-radius:5px;
        }
        .bomb-box-but .confirm{
            background: #70a7ff;
            border-color: #70a7ff;
            color: #ffff;
            margin-left: 300px;
        }
        .bomb-box-delete{
            width: 8%;
        }
        .glyphicon-trash{
            cursor: pointer;
        }
        .top-title2{
            padding: 0 20px;
            width: 100%;
            height: 58px;
            box-shadow: 0 0 6px 6px rgba(0,0,0,.05);
            line-height: 58px;
            z-index: 22;
            background: #fff;
        }
        .left-list2{
            height: 100%;
            width: 109%;
            z-index: 2;
            float: left;
            overflow-y: scroll;
            margin-right: -17px;
        }
        .left-list2::-webkit-scrollbar {
            display: none;
        }
        .left-list2 .add-list {
            padding: 14px 30px 14px 12px;
            height: 66px;
            cursor: pointer;
        }
        .left-list2 ul li {
            height: 48px;
            line-height: 48px;
            padding-left: 20px;
            cursor: pointer;
        }
        .left-list2 ul li a{
            color: #fff;
        }
        .layui-icon-spread-left{
            position: absolute;
            top: 7px;
            width: 26.5%;
            right: 0;
            font-size: 18px !important;
        }
        .layui-icon-shrink-right{
            position: relative;
            top: 7px;
            font-size: 18px !important;
        }
        .li-lable{
            box-shadow: 0 0 0 2px rgba(81,130,227,.15),inset 0 0 0 2px #f88407 !important;
        }
        .left-button{
            float: left;
            text-align: center;
            color: #fff;
            font-size: 14px;
        }
        .button-group{
            text-align: center;
            max-width: 270px;
            height: 33px;
            padding: 10px;
            margin-left: 26%;
            font-size: 14px;
            float: right;
        }
        .left-button a{
            color: #fff;
        }
        .overview {
            padding: 0 7px;
            background: #5cd489;
            margin-top: 12px;
            width: 140px;
            height: 32px;
            text-align: center;
            line-height: 32px;
            color: #fff;
            border-radius: 20px;
            display: block;
            float: left;
            margin-right: 20px;
        }
        #upload-source div:last-child,.upload-source-div div:last-child{
            height: 100% !important;
            width: 100% !important;
        }

    </style>
@endsection

@section('content')
    <div id="wrapper">
        <div class="content" style="padding-top:0;width:100%;overflow: hidden;">

            <div class="left-sider">
                <div class="left-logo">
                    <a href="/webi/list/index"><img src="/images/webi/logo.png" alt="WeBI" width="179px;" height="59px;"></a>
                </div>
                <!--????????????-->
                <div class="left-list2">
                        <div class="add-list">
                            <div onclick="_GROUP.add()">
                                <span>????????? <i>+ </i></span>
                            </div>
                        </div>
                        <ul id="group-nav">

                        </ul>
                </div>
            </div>

            <div class="right-sider">
                <div class="top-title2" >
                    <div class="left-button">
                        <span>???????????????:&nbsp;&nbsp;</span>
                        <div class="data-source">
                            <select class="data-source-option" id="data-source-option">
                                <option value="0">??????????????????</option>
                                <option value="1">??????????????????</option>
                                <option value="2">Excel?????????</option>
                                <option value="3">MYSQL?????????</option>
                            </select>
                        </div>
                        <div class="source-name-remind" data-source="0"></div>
                    </div>
                    <div class="button-group">
                        <button class="btn btn-default" role="button" onclick="JavaScript:history.go(-1)" style="float: right;margin-top: 3px;">??????</button>
                    </div>
                </div>
                <!--?????????-->
                <div class="linked-content" id="linked-content">
                    <input type="hidden" id="groupId" value=""/>
                    {{--????????????--}}
                    <div class="right-cont-w" id="right-content"></div>

                    {{--????????????--}}
                    <div>
                        <div>
                            <div class="layui-icon layui-icon-spread-left" id="left-side-hide" style="display: none;;cursor:pointer;"  onclick="_G.hideTable();"></div>
                            <div class="layui-icon layui-icon-shrink-right" id="left-side-show" style="display: inline-block;;cursor:pointer;"  onclick="_G.showTable(1);"></div>
                        </div>
                        <div class="linked-list" >
                            <div style="width: 100%;cursor: pointer; height: 50px">
                                <div class="input-search">
                                    <input type="text" id="search" class="form-control" placeholder="?????????" />
                                    <i class="search" onclick="_G.searchTableList()"></i>
                                </div>
                            </div>
                            <div>
                                <ul class="ul-list">
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{--??????????????????????????????--}}
                <div id="layer-source-box" class="layer-source-box" style="display: none">
                    <div class="source-header">
                        <div class="source-header-name"><h3>?????????</h3></div>
                    </div>
                    <div id="source-list-box">
                        {{--excel--}}
                        <div id="layer-source-excel" style="display: none;">
                            {{--???????????????--}}
                            <div class="left-source">
                                <table class="layui-table source-table" lay-skin="line">
                                    <tbody class="source-layui-table-header">
                                    <tr>
                                        <td class="operation-source">??????</td>
                                        <td>???????????????</td>
                                        <td>??????????????????</td>
                                        <td>????????????</td>
                                        <td>??????</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            {{--???????????????\???????????????--}}
                            <div class="right-source">
                                {{--???????????????--}}
                                <div  id="ant-right-excel" class="ant-right-upload">
                                    <form class="layui-form layui-form-pane source-content source-excel">
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">???????????????</label>
                                            <div class="layui-input-block">
                                                <input value="" class="layui-input source-name" id="source-name">
                                            </div>
                                        </div>
                                        <div class="layui-form-item" id="upload-content-button">
                                            <div class="upload-source" id="upload-source" style="width: 100%;height:300px;">
                                                <div id="upload-rate"></div>
                                                <div class="table-name" id="table-name"><p></p></div>
                                                <div class="upload-button">
                                                    <i class="layui-icon layui-icon-upload-drag"></i>
                                                </div>
                                                <p>????????????????????????????????????????????????</p>
                                                <p class="text-one">?????????*.xls,*.xlsx??????</p>
                                            </div>
                                        </div>
                                    </form>
                                    <div><button class="btn btn-success" id="source-button-upload">??????</button></div>
                                </div>
                            </div>
                        </div>
                        {{--SQL--}}
                        <div id="layer-source-sql" style="display: none;">
                            {{--???????????????--}}
                            <div class="left-source">
                                <table class="layui-table source-table" lay-skin="line">
                                    <tbody class="source-layui-table-header">
                                    <tr>
                                        <td class="operation-source">??????</td>
                                        <td>???????????????</td>
                                        <td>?????????????????????</td>
                                        <td>???????????????</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            {{--???????????????\???????????????--}}
                            <div class="right-source">
                                {{--???????????????--}}
                                <div id="ant-right-sql" class="ant-right-upload">
                                    <form class="layui-form layui-form-pane source-content source-sql" id="source-sql">
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">???????????????</label>
                                            <div class="layui-input-block">
                                                <input value="" type="text" name="db_host" class="layui-input source-name" placeholder="?????????">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">?????????</label>
                                            <div class="layui-input-block">
                                                <input value="" type="text" name="db_database" class="layui-input source-name" placeholder="???????????????">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">?????????</label>
                                            <div class="layui-input-block">
                                                <input value="" type="text" name="db_user" class="layui-input source-name" placeholder="?????????">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">??????</label>
                                            <div class="layui-input-block">
                                                <input value="" type="password" name="db_pwd" class="layui-input source-name" placeholder="??????">
                                            </div>
                                        </div>
                                        <div class="layui-form-item">
                                            <label class="layui-form-label">??????</label>
                                            <div class="layui-input-block">
                                                <input value="3306" type="text" name="db_port" class="layui-input source-name" placeholder="?????????">
                                            </div>
                                        </div>
                                    </form>
                                    <div class="source-button-test">
                                        <button class="btn btn-info" id="source-button-test" onclick="_SOURCE.commitSqlSource(0,1);">????????????</button>
                                    </div>
                                    <div class="source-button-connect">
                                        <button class="btn btn-success" id="source-button-connect" onclick="_SOURCE.commitSqlSource(0,4);">??????</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{--????????????--}}
            <div id="group-nav-each" style="display: none">
                <li id="view_g_view_id" data-id="g_view_id">
                    <div style="float:left; width:82%;" class="group-each" id="g_g_view_id">
                        <a id="group_g_view_id">g_group_title</a>
                    </div>
                    <div type="button" style="height: 48px;float:left;"  class="dropdown" id="group_g_view_id">
                        <span class="dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="glyphicon glyphicon-option-vertical text-style" ></i>
                        </span>
                        <ul class="dropdown-menu dropdown-menu-right" >
                            <li  style="height:30px;padding-left:0;"><a style="height:28px;color: black;"  onclick=" _GROUP.edit('g_view_id')";>??????</a></li>
                            <li  style="height:30px;padding-left:0;"><a style="height:28px;color: black;"  onclick=" _GROUP.del('g_view_id');">??????</a></li>
                        </ul>
                    </div>
                </li>
            </div>
            {{--????????????--}}
            <div id="tableList" style="display: none">
                <li id="t_TableId" class="link_content_list" >
                    <div class="source-table-name" data-id="TableId" data-name="TableName"><span class="fr">Description </span></div>
                    <div class="source-table-detail layui-icon layui-icon-about" data-id="TableId" data-name="TableName" data-remarks="Description" title="??????"></div>
                    <div class="source-table-choose layui-icon layui-icon-ok" data-id="TableId" data-name="TableName" title="??????"></div>
                </li>
            </div>
            {{--??????????????????--}}
            <div id="layer_add_group" style="display: none">
               <form id="title-form-header" onsubmit="return false;" class="form-horizontal" role="form" style="margin-top: 20px;">
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="title-header"><span class="red pr5">*</span>&ensp;?????????</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="title-header" id="title-header" placeholder="???????????????" value="">
                           </div>
                        </div>
                  </form>
            </div>
            {{--?????????????????????--}}
            <div id="layer-linked-edit" style="display: none">

                    <ul class="bomb-box" id="layer-edit">
                        <li data-code="1"><span class="left-img"></span>
                            <p>??????</p>
                        </li>
                        <li data-code="2" id="li-2" ><span class="inner-img" ></span>
                            <p>??????</p>
                        </li>
                        <li data-code="3"><span class="right-img"></span>
                            <p>??????</p>
                        </li>
                    </ul>
                    <div class="bomb-box-option">
                        <ul id="select_link">

                            <li>
                                <div class="bomb-box-select fl">
                                    <select id="table_l_l">
                                    </select>
                                </div>
                                <div class="bomb-box-equal fl" style="font-size: 0;">=</div>
                                <div class="bomb-box-select fl">
                                    <span id="table_rs" data-name="tableName-select">selectTable</span>
                                </div>
                            </li>

                            <div id="table_field_hidden">
                                <li >
                                    <div class="bomb-box-select fl">
                                        <select class="fields_l_l">
                                        </select>
                                    </div>
                                    <div class="bomb-box-equal fl">=</div>
                                    <div class="bomb-box-select fl">
                                        <select  class="fields_rr">
                                        </select>
                                    </div>

                                    <div class="bomb-box-delete fl">
                                    <i class="glyphicon glyphicon-trash"></i>
                                    </div>
                                </li>

                            </div>
                        </ul>
                        <p onclick="_POP.add_row()" style="cursor: pointer">????????????????????????</p>
                    </div>

            </div>
            {{--????????????--}}
            <div id="link_primarTabe" style="display: none">
                <div class="f1 right-cont-fl" id="li_parentId" data-parent="parentId">
                    <span class="fl" id="primarName">primarName</span>
                    <i class="glyphicon"></i>
                    <em class="fr"></em>
                </div>
            </div>
            {{--????????????div??????--}}
            <div id="append-div-ul" style="display:none;">
                <div style="left:lf_valpx;top:t_valpx" class="right-div">
                    <ul>
                        li_list
                    </ul>
                </div>
            </div>
            {{--??????????????????--}}
            <div id="link_table_div" style="display: none">
                <li class="li_uuId" id="li_uuId" data-parent="parentId">
                    <div class="fight-cont-fl-left"></div>
                    <div class="right-cont-fr-img fl">
                        <span class="join_class"  title="???????????????"></span>
                        <em></em>
                    </div>
                    <div class="right-cont-fr-text fl">
                        <span id="sublistNmae" >sublistNmae</span>
                        <i class="glyphicon" ></i>
                    </div>
                </li>
            </div>
            {{--????????????--}}
            <div id="link-layer-hidden" style="display: none">
                <li>
                    <div class="bomb-box-select fl">
                        <select class="fieldsLs">
                        </select>
                    </div>
                    <div class="bomb-box-equal fl">=</div>
                    <div class="bomb-box-select fl">
                        <select class="fieldsRs">
                        </select>
                    </div>
                    <div class="bomb-box-delete fl">
                    <i class="glyphicon glyphicon-trash" ></i>
                    </div>
                </li>
            </div>
            {{--???????????????--}}
            <div id="group-li-html" style="display: none;">
                <li id="view_$view_id" data-id="$view_id">
                    <div style="float:left; width:82%;" class="group-each" id="g_$view_id">
                        <a id="group_$view_id">$view_name</a>
                    </div>

                    <div type="button" style="height: 48px;float:left;"  class="dropdown"  id="group_$view_id">
                        <span class="dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                             <i class="glyphicon glyphicon-option-vertical text-style" ></i>
                        </span>
                        <ul class="dropdown-menu dropdown-menu-right" >
                            <li  style="height:30px;padding-left:0;"><a style="height:28px;color: black;" onclick=" _GROUP.edit('$view_id')";>??????</a></li>
                            <li  style="height:30px;padding-left:0;"><a style="height:28px;color: black;"  onclick="_GROUP.del('$view_id');">??????</a></li>
                        </ul>
                    </div>
                </li>
            </div>
            {{--excel???????????????tr--}}
            <div id="source-tr-html" style="display: none;">
                <table id="source-table-html">
                    <tr class="table-tr-$source_id">
                        <td class="operation">
                            <div class="layui-icon  layui-icon-ok" title="??????????????????" onclick="_SOURCE.selectSource('$source_id')"></div>
                            <div class="layui-icon layui-icon-table" data-toggle="modal" data-target="#myModal" title="????????????" onclick="_SOURCE.pop.tableList('$source_id')"></div>
                            <div class="layui-icon layui-icon-edit"  title="?????????????????????" onclick="_SOURCE.edit('$source_id')"></div>
                            <div class="layui-icon layui-icon-delete"  title="???????????????" onclick="_SOURCE.delete('$source_id')"></div>
                            <div class="layui-icon layui-icon-upload-circle" id="updata-$source_id" onclick="_SOURCE.update('$source_id')" data-id="$source_id" title="???????????????"></div>
                            <div class="layui-icon layui-icon-about"  title="??????????????????" onclick="_SOURCE.tableList('$source_id','$group_id')"></div>
                        </td>
                        <td class="source-name-$source_id">$source_name</td>
                        <td>$updated_at</td>
                        <td>$file_size <i class="layui-icon layui-icon-download-circle" style="padding-left: 3px;" title="?????????????????????" onclick="_SOURCE.download('$source_id')"></i></td>
                        <td>$line_num</td>
                    </tr>
                </table>
            </div>
            {{--sql???????????????tr--}}
            <div id="source-sql-html" style="display: none;">
                <table id="source-table-html">
                    <tr class="table-tr-$source_id">
                        <td class="operation">
                            <div class="layui-icon  layui-icon-ok" title="??????????????????" onclick="_SOURCE.selectSource('$source_id')"></div>
                            <div class="layui-icon layui-icon-edit"  title="?????????????????????" onclick="_SOURCE.edit('$source_id')"></div>
                            <div class="layui-icon layui-icon-delete"  title="???????????????" onclick="_SOURCE.delete('$source_id')"></div>
                            <div class="layui-icon layui-icon-upload-circle" id="updata-$source_id" onclick="_SOURCE.update('$source_id')" data-id="$source_id" data-name="$source_name" data-host="$db_host" data-database="$db_database" data-user="$db_user"  data-port="$db_port" title="???????????????"></div>
                            <div class="layui-icon layui-icon-about"  title="??????????????????" onclick="_SOURCE.tableList('$source_id','$group_id')"></div>
                        </td>
                        <td class="source-name-$source_id">$source_name</td>
                        <td>$db_host</td>
                        <td>$db_database</td>
                    </tr>
                </table>
            </div>
            {{--??????????????????--}}
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog" style="width: 800px;height: 480px;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                &times;
                            </button>
                            <h4 class="modal-title" id="myModalLabel">
                                {{--?????????--}}
                            </h4>
                        </div>
                        <div class="modal-body">
                            <div class="modal-table-box">
                                <table class="layer-table">
                                    {{--?????????--}}
                                </table>
                            </div>
                            <div class="modal-page">
                                <div id="page" class="source-table-page"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{--??????Excel???????????????--}}
            <div id="layer-excel-source-update" class="layer-excel-source-update" style="display: none;">
                <form>
                    <div class="upload-source-div" id="upload-source-div" style="width: 400px;height:200px;">
                        <div class="remind-box">
                            <div id="layer-upload-rate"></div>
                            <div class="upload-button">
                                <i class="layui-icon layui-icon-upload-drag"></i>
                            </div>
                            <p>????????????????????????????????????????????????</p>
                            <p class="text-one">?????????*.xls,*.xlsx??????????????????1M???</p>
                        </div>
                    </div>
                </form>
            </div>
            {{--??????SQL???????????????--}}
            <div id="layer-sql-source-update" class="layer-sql-source-update">
                <form class="layui-form layui-form-pane source-content" id="source-content-sql">
                    <div class="layui-form-item">
                        <label class="layui-form-label">???????????????</label>
                        <div class="layui-input-block">
                            <input type="text" name="db_host" value="" class="layui-input db_host" id="db_host" placeholder="?????????">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">?????????</label>
                        <div class="layui-input-block">
                            <input type="text" name="db_database" value="" class="layui-input db_database" id="db_database" placeholder="???????????????">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">?????????</label>
                        <div class="layui-input-block">
                            <input type="text" name="db_user" value="" class="layui-input db_user" id="db_user" placeholder="?????????">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">??????</label>
                        <div class="layui-input-block">
                            <input value="" type="password" name="db_pwd" class="layui-input source-name" id="db_pwd" placeholder="??????">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">??????</label>
                        <div class="layui-input-block">
                            <input type="text" name="db_port" value="" class="layui-input db_port" id="db_port" placeholder="?????????">
                        </div>
                    </div>
                    <div class="test-button" onclick="_SOURCE.commitSqlSource(0,3);">????????????</div>
                </form>
            </div>
            {{--?????????????????????????????????--}}
            <div id="layer-source-table" class="layer-source-table" style="display: none;">
                <div>
                    <table class="layer-source-table-box layui-table" lay-skin="line">
                    </table>
                </div>

                <div class="modal-page">
                    <div id="layer-page" class="source-table-page" style="text-align: center;"></div>
                </div>
            </div>
            {{--?????????????????????????????????tr--}}
            <div id="layer-source-table-tr">
                <table id="layer-source-tr-html">
                    <tr class="table-tr">
                        <td class="table-list-name">$table_name</td>
                        <td class="source-name-$source_id">$table_remarks</td>
                        <td><div class="operation layui-icon layui-icon-about" data-name="$table_name" data-remarks="$table_remarks" title="??????"></div></td>
                    </tr>
                </table>
            </div>
            {{--??????????????????????????????--}}
            <div id="layer-source-field" class="layer-source-field" style="display: none">
                <div class="layer-source-field-comment">
                    <div> <lable>????????????</lable><span class="field-table-name"></span> </div>
                    <div style="margin-top:10px;"><lable>????????????</lable><span class="field-table-remarks"></span></div>
                </div>
                <div>
                    <table class="layer-source-field-box layui-table" lay-skin="line">
                        <tr>
                            <td class="table-list-name">????????????</td>
                            <td>????????????</td>
                            <td>????????????</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script type="text/javascript" src="/libs/webuploader/webuploader.js"></script>
    <script>

        //????????????????????????????????????
        $(document).on('click','.bomb-box li',function (){//??????????????????
            $(this).addClass("clickli").siblings().removeClass("clickli");
        }).on('click','.source-name-remind',function (){//??????????????????????????????
            _SOURCE.showlayerSource();
        }).on('change','#data-source-option',function () { //select???????????????
            $('.source-name-remind').empty();//???????????????????????????

            $('#right-content').empty();//??????????????????????????????
            _GROUP.list();//????????????

            switch($(this).val()) {

                case '0': //?????????
                    _G.source_id = 0;
                    _SOURCE.hidelayerSource();//???????????????????????????
                    $('.linked-list').fadeOut(1500);//?????????????????????
                    break;

                case '1':   //??????????????????
                        _G.source_id = 1;
                        if($('#left-side-show').hasClass('layui-icon-shrink-right')){
                            _SOURCE.hidelayerSource();//???????????????????????????
                        }
                    break;

                default:   //Excel???SQL?????????
                    _SOURCE.showlayerSource();

            }

        }).on('blur','.pop-title',function () {//??????pop???????????????
            _SOURCE.pop.save_name($(this).attr('data-id'));
        }).on('blur','.fieldRemarks',function () {//??????pop?????????????????????
            var data ={};
            data.table_id = $(this).attr('data-table');
            data.field_name =  $(this).attr('data-id');
            data.field_remark = $(this).val();
            data.edit_field = 2;

            _SOURCE.pop.remarks(data);
        });

        $('#data-source-option').val('0');//????????????????????????

        $(document).ready(function () {

            _GROUP.list();//?????????????????????

            //??????????????????
            layer_add_group=$('#layer_add_group').html();
            layer_add_group = layer_add_group.replace(/title-form-header/g, "title-form");
            layer_add_group = layer_add_group.replace(/title-header/g, "title");


            //??????????????????
            layer_linked_edit=$("#layer-linked-edit").html();
            layer_linked_edit = layer_linked_edit.replace(/layer-edit/g, "edit-link");
            layer_linked_edit = layer_linked_edit.replace(/table_l_l/g, "table_l");
            layer_linked_edit = layer_linked_edit.replace(/fields_rr/g, "fields_r");
            layer_linked_edit = layer_linked_edit.replace(/fields_l_l/g, "fields_l");
            layer_linked_edit = layer_linked_edit.replace(/li-2/g, "li_select");
            layer_linked_edit = layer_linked_edit.replace(/table_field_hidden/g, "table-field");
            layer_linked_edit = layer_linked_edit.replace(/table_rs/g, "table_r");

        });
    </script>

    <script>

            /**
             *   _G  ??????????????????
             *   _GROUP  ??????????????????
             *   _POP  ????????????????????????
             *   G_handle  ????????????????????????
             */
            var _G = {},
                _GROUP = {},
                _POP = {},
                _SOURCE = {},
                G_handle = { };


            _G.globalJosn = {};  //???????????????????????????????????????
            _G.source_id = 1;   //SQL??? Excel?????????ID

            _POP.l_options = [];  //??????????????????????????????????????????????????????
            _POP.r_options = [];  //??????????????????????????????????????????????????????
            _POP.error_msg = "";  //????????????????????????????????????

            _SOURCE.pop = {};//?????????????????????

            var parent = document.getElementById("right-content");//?????????????????????--???????????????


            layui.use('upload', function () {
                var upload = layui.upload;

                //???????????????
                upload.render({
                    elem: '.upload-source',
                    accept: 'file',
                    acceptMime: 'xlsx|xls',
                    auto: false,
                    url: '/webi/views/upload?action=webi',
                    bindAction: '#source-button-upload',
                    choose: function(obj){
                        var $li = $( '#upload-rate'),
                            $percent = $li.find('.progress .progress-bar');

                        // ??????????????????
                        if ( !$percent.length ) {

                            $('<div class="progress progress-striped active">' +
                                '<div class="progress-bar" role="progressbar" style="width: 0%">' +
                                '<p>' +
                                '</p>' +
                                '</div>' +
                                '</div>').appendTo( $li ).find('.progress-bar');

                            obj.preview(function(index, file, result){
                                $li.find('p').text(file.name);
                            });

                        }

                    },
                    done: function(res, index, upload){ //??????????????????
                        if (res.code == 200) {
                            _SOURCE.commitExcelSource();
                        } else {
                            layer.msg(res.message,{icon:2,time:1500});
                        }
                    }
                });

            });


            //?????????????????????
            var updatePloader = WebUploader.create({
                auto: false,
                swf: '/libs/webuploader/Uploader.swf',
                server: '/webi/views/upload?action=webi',
                pick: '#upload-source-div',
                resize: false
            });
            updatePloader.on( 'fileQueued', function( file ) {
                var remind = $( '#layer-upload-rate');
                    remind.append( '<div id="' + file.id + '" class="item">' +
                    '<h4 class="info" style="color: green;">' + file.name + '</h4>' +
                    '<p class="state" style="color: green;">????????????...</p>' +
                    '</div>' );
            });
            updatePloader.on( 'uploadSuccess', function( file, res ) {
                $('#layer-upload-rate').find('.item').remove();//??????????????????
                if(res.code != 200){
                    layer.msg(res.message,{icon:2,time:3000});
                    return false;
                }
                layer.msg('????????????',{icon:1,time:1500});
                layer.closeAll();
                _SOURCE.sourceList();//????????????
            });


            //?????????????????????????????????
            _G.groupID = function(){
                var options=$("#data-source-option option:selected"); //??????????????????
                var group_id = options.val();//?????????????????????
                return group_id;
            };


            //????????????????????????????????????
            _SOURCE.showlayerSource = function(){
                $('#linked-content').fadeOut(100);
                $('#layer-source-box').fadeIn(1000);

                _SOURCE.sourceList(); //???????????????

                if( _G.groupID() == 2 ){ //Excel
                    $('#source-list-box #layer-source-excel').show().siblings().hide();//??????excel
                }else{     //SQL
                    $('#source-list-box #layer-source-sql').show().siblings().hide();  //??????sql
                }
            };
            //????????????????????????????????????
            _SOURCE.hidelayerSource = function(){
                $('#layer-source-box').fadeOut(500);
                $('#linked-content').fadeIn(1000);
                $('.source-layui-table-header').nextAll().empty();
            };
            //?????????????????????
            _SOURCE.sourceList = function(){
                var group_id = _G.groupID();//?????????????????????ID
                $('.source-layui-table-header').nextAll().empty();//?????????????????????
                $.ajax({
                    type: 'get',
                    data:{
                        group_id: group_id
                    },
                    url: '/webi/views/source/list',
                    success: function (res) {
                        if (res.code != 200) {
                            layer.msg(res.msg, {icon: 2, offset: '70px',time:1500});
                        } else {
                            var source_tr_html = _SOURCE.creatTr(res.data['source']);
                             $('.source-table').append(source_tr_html);
                        }
                    }
                });

            };
            //?????????????????????tr
            _SOURCE.creatTr = function(data){
                var tr_html="";
                if( _G.groupID() == 2){  //Excel
                    $.each(data, function (k, v) {

                        tr_html += $('#source-tr-html #source-table-html').html();

                        tr_html = tr_html.replace(/[\$]source_id/g,v._id);
                        tr_html = tr_html.replace(/[\$]group_id/g,v.group_id);
                        tr_html = tr_html.replace(/[\$]source_name/g,v.source_name);
                        tr_html = tr_html.replace(/[\$]file_size/g,v.file_size);
                        tr_html = tr_html.replace(/[\$]line_num/g,v.line_num);
                        tr_html = tr_html.replace(/[\$]updated_at/g,v.updated_at);

                    });
                }else{     //SQL

                    $.each(data, function (k, v) {

                        tr_html += $('#source-sql-html #source-table-html').html();

                        tr_html = tr_html.replace(/[\$]source_id/g,v._id);
                        tr_html = tr_html.replace(/[\$]group_id/g,v.group_id);
                        tr_html = tr_html.replace(/[\$]source_name/g,v.source_name);
                        tr_html = tr_html.replace(/[\$]db_host/g,v.db_host);
                        tr_html = tr_html.replace(/[\$]db_database/g,v.db_database);
                        tr_html = tr_html.replace(/[\$]db_user/g,v.db_user);
                        tr_html = tr_html.replace(/[\$]db_port/g,v.db_port);
                    });
                }

                return tr_html;
            };
            //???????????????excel
            _SOURCE.download = function(source_id){
                $.ajax({
                    type:'get',
                    data:{
                        source_id: source_id
                    },
                    url: '/webi/views/download/excel',
                    // ???????????????
                    success: function (obj) {
                        if ( obj.code == 200 ) {
                            window.location= obj.data.file_path;
                        } else {
                            layer.msg(obj.message,{icon:2,time:1500});
                        }
                    }
                });
            };
            //???????????????????????????
            _SOURCE.tableList = function(source_id,group_id,url_suffix){
                layer.open({
                    title:'?????????',
                    type: 1,
                    content: $('#layer-source-table'),
                    area:['700px','500px'],
                    success:function(){
                        _SOURCE.refreashTable(source_id,group_id,url_suffix);//????????????
                    }
                });
            };
            //????????????
            _SOURCE.refreashTable = function(source_id,group_id,url_suffix){
                var indexPage = layer.load();
                $('.layer-source-table-box').empty();//??????????????????

                var tr_thml = '<tr>';
                tr_thml += '<td class="table-list-name">??????</td>';
                tr_thml += '<td>??????</td>';
                tr_thml += '<td>??????</td>';
                tr_thml += ' </tr>';
                $('.layer-source-table-box').append(tr_thml);

                var page  = url_suffix || 1;
                $.ajax({
                    type:'get',
                    url: '/webi/design/views/table/search',
                    data: {
                        group_id: group_id,
                        source_id: source_id,
                        page: page,
                        limit: 10
                    },
                    success: function (res) {
                        layer.close(indexPage);

                        if( res.code==200 ){
                            if(!($.isEmptyObject(res.data))){

                                var source_tr_html = _SOURCE.tableCombine(res.data.data);//???????????????????????????
                                $('.layer-source-table-box').append(source_tr_html);

                                $.each($('.layer-source-table-box .operation'), function (k, v) {
                                    $(this).on('click',function(){
                                        _G.tableStructure(
                                            $(this).attr('data-name'),
                                            $(this).attr('data-remarks'),
                                            source_id
                                        );
                                    });
                                });

                                if(typeof  url_suffix == 'undefined'){//??????????????????
                                    _SOURCE.toPage(source_id,group_id,res.data.count);//??????
                                }
                            }
                        }
                    }
                });
            };
            //???????????????
            _SOURCE.toPage = function(source_id,group_id,count){
                var limit = 10;
                layui.use('laypage', function(){
                    var laypage = layui.laypage;
                    laypage.render({
                        elem: 'layer-page',
                        count: count,
                        curr: 1,
                        limit: limit,
                        theme:"#0099ff",
                        jump:function(obj, first) {//?????????????????????????????????
                            page = obj.curr;//?????????????????????
                            $("#currPage").val(page);
                            if(!first){ //?????????????????????????????????????????????????????? 
                                _SOURCE.refreashTable(source_id,group_id, page);
                            }
                        }
                    });
                });
            };
            //??????--???????????????????????????
            _SOURCE.tableCombine = function(data) {
                var tr_html = '';
                $.each(data, function (k, v) {

                    tr_html += $('#layer-source-table-tr #layer-source-tr-html').html();

                    tr_html = tr_html.replace(/[\$]source_id/g,v.source_id);
                    tr_html = tr_html.replace(/[\$]table_name/g,v.table_name);
                    tr_html = tr_html.replace(/[\$]table_remarks/g,v.description);

                });
                return tr_html;
            };
            //??????--?????????????????????
            _SOURCE.tableField = function(data) {
                var tr_demo_html = '<tr>';
                    tr_demo_html += '<td class="table-list-name">$column_name</td>';
                    tr_demo_html +='<td class="source-name-$source_id">$data_type</td>';
                    tr_demo_html +='<td class="source-name-$source_id">$column_comment</td>';
                    tr_demo_html +='</tr>';

                var tr_html = '';
                var column_name = "";
                var column_comment = "";

                $.each(data.field, function (k, v) {
                    if(_G.groupID() == 2){
                        column_name = v.field_name;
                        column_comment = v.field_remark;

                    }else if(_G.groupID() == 3){
                        column_name = v.column_name;
                        column_comment = v.column_comment;
                    }

                    tr_html += tr_demo_html;

                    tr_html = tr_html.replace(/[\$]column_name/g,column_name);
                    tr_html = tr_html.replace(/[\$]data_type/g,v.data_type);
                    tr_html = tr_html.replace(/[\$]column_comment/g,column_comment);

                });
                return tr_html;

            };
            //???????????????,?????????
            _SOURCE.selectSource = function(source_id){
                _G.source_id = source_id;//?????????ID,???????????????????????????
                $('.source-name-remind').html();//?????????
                $('.source-name-remind').attr('data-source',0);//?????????id
                $('.source-name-remind').html($('.source-name-'+source_id).eq(0).text());//????????????????????????
                $('.source-name-remind').attr('data-source',source_id);//?????????id

                _SOURCE.hidelayerSource();//???????????????????????????
                _GROUP.list(source_id);//
                //_G.showTable(source_id);//???????????????
            };
            //?????????????????????
            _SOURCE.pop.tableList = function(source_id,url_suffix){
                var url  = url_suffix || '&page=1&limit=10';
                $('.layer-table').empty();//????????????????????????
                if(typeof  url_suffix == 'undefined'){//??????????????????
                    $('#myModalLabel').empty();//????????????
                }

                $.ajax({
                    type: 'get',
                    url: '/webi/views/source/table/list?source_id='+source_id + url,
                    success: function (obj) {
                        if (obj.code != 200) {
                            layer.msg(obj.msg, {icon: 2, offset: '70px',time:1500});
                        } else {
                            var res = [];
                            res.name = obj.data['table_name'];
                            res.source_id = source_id;
                            res.table_id = obj.data['table_id'];

                            var count = obj.data['count'];//?????????
                            var tr_html = _SOURCE.pop.creatTable(obj.data['table'],res);//?????????tr

                            //??????
                            $('.layer-table').append( tr_html.html);
                            if(typeof  url_suffix == 'undefined'){//??????????????????
                                //??????
                                $('#myModalLabel').append( tr_html.name_html);
                                _SOURCE.pop.toPage(count,source_id);//??????
                            }
                        }


                    }
                });
            };
            //????????????
            _SOURCE.pop.creatTable = function(data,table){
                var  html = "";
                var tr_html = "";
                var th_html = "";
                var td_html = "";
                var th = "";
                var td = "";
                var table_th_html = '<th><div><input type="text" value="$source_remarks" class="fieldRemarks remarks-$field" data-id="$field" data-table="$table_id"></div></th>';
                var table_td_html = '<td>$parm</td>';

                $.each(data[0],function(dk,dv){//??????

                    th_html = table_th_html;

                    th_html = th_html.replace(/[\$]field/g, dk);
                    th_html = th_html.replace(/[\$]table_id/g, table['table_id']);
                    th += th_html.replace(/[\$]source_remarks/g, dv);

                });

                $.each(data[1], function (k, v) {
                    $.each(v, function (field, parm) {
                        td_html = table_td_html;

                        td_html = td_html.replace(/[\$]parm/g,parm);
                        td += td_html;
                    });
                    if( td != "" ){
                        tr_html += '<tr>'+ td + '</tr>';
                        td = '';
                    }
                });

                html = '<tr class="table-header">'+ th + '<tr>' + tr_html;

                var name_html = '<input type="text" value="'
                    + table['name'] +'" data-title="'+  table['name']
                    +'" class="pop-title" name="pop-title" data-table="'+ table['table_id'] +'" data-id="'+ table['source_id']+'" >';

                var data_array = {
                    'html':html,
                    'name_html':name_html
                };
                return data_array;
            };
            //????????????
            _SOURCE.pop.toPage = function(count,source_id){
                var limit = 10;
                layui.use('laypage', function(){

                    var laypage = layui.laypage;
                    laypage.render({
                        elem: 'page',
                        count: count,
                        curr: 1,
                        limit: limit,
                        theme:"#0099ff",
                        jump:function(obj, first) {//?????????????????????????????????
                            page = obj.curr;//?????????????????????
                            $("#currPage").val(page);
                            if(!first){ //??????????????????????????????????????????????????????
                                _SOURCE.pop.tableList(source_id,'&page='+ page+ '&limit='+ limit);
                            }
                        }
                    });
                });
            };
            //????????????????????????
            _SOURCE.pop.save_name = function (source_id) {

                if( $('.pop-title').val() == ""){
                    layer.msg('???????????????????????????',{icon:2,time:1500});
                    return false;
                }
                var table_name = $('.pop-title').val();
                $.ajax({
                    type:'post',
                    url: '/webi/views/source/table/save',
                    data: {
                        edit_field:1,
                        table_name: table_name,
                        table_id: $('.pop-title').attr('data-table')
                    },
                    dataType:'json',
                    // ???????????????
                    success: function (obj) {
                        layer.closeAll();
                        if ( obj.code == 200 ) {
                            $('.pop-title').val( table_name );
                            $('.pop-title').attr('data-title',table_name);
                        } else {
                            layer.msg(obj.message,{icon:2,time:1500});
                        }
                    }
                });
            };
            //??????????????????
            _SOURCE.pop.remarks = function (data) {

                $.ajax({
                    type:'post',
                    url: '/webi/views/source/table/save',
                    data: data,
                    dataType:'json',
                    // ???????????????
                    success: function (obj) {
                        if ( obj.code == 200 ) {
                            $('.remarks-'+ data.field_name).val(data.field_remark);
                        } else {
                            layer.msg(obj.message,{icon:2,time:1500});
                        }
                    }
                });
            };
            //?????????????????????
            _SOURCE.edit = function(source_id){ 
                var html = '<form id="pop_form" onsubmit="return false;" class="form-horizontal" role="form" style="margin-top: 15px">';
                html+='<div class="form-group">';
                html+='<label class="col-sm-3 control-label lable-name-option"><span class="red mr5">*&nbsp;</span>??????????????????</label>';
                html+='<div class="col-sm-8">';
                html+='<input type="text" class="form-control"  id="table-source-name" name="table_source_name"  placeholder="????????????????????????" value="">';
                html+='</div>';
                html+='</div>';
                html+='</form>';

                layer.open({
                    title: '?????????????????????',
                    type: 1,
                    offset:'100px',
                    area:['500px','180px'],
                    scrollbar: false,
                    content: html,
                    btn: ['??????', '??????'],
                    success:function(){
                        $('#table-source-name').val($('.source-name-'+ source_id).html());
                    },
                    yes:function(){
                        _SOURCE.save_name(source_id);
                    }
                });
            };
            //?????????????????????
            _SOURCE.save_name = function (source_id) {
                var data = E.getFormValues('pop_form');
                var message ='';
                if (E.isEmpty(data.table_source_name) ) {
                    message += '????????????????????????</br>';
                }
                if(message){  //????????????????????????????????????
                    layer.msg(message,{icon:2,time:1500});
                    return false;
                }

                layer.confirm('???????????????????????????????????????',{icon:3},function(){

                    E.ajax({
                        type:'post',
                        url: '/webi/views/source/save',
                        data: {
                            source_name: data.table_source_name,
                            source_id: source_id
                        },
                        dataType:'json',
                        // ???????????????
                        success: function (obj) {
                            layer.closeAll();
                            if ( obj.code == 200 ) {
                                layer.msg( obj.message, {icon:1,time:1500});
                               $('.source-name-'+ source_id).text(data.table_source_name);//????????????????????? ????????????
                            } else {
                                layer.msg(obj.message,{icon:2,time:1500});
                            }
                        }
                    });
                });
            };
            //???????????????
            _SOURCE.delete = function(source_id){
                layer.confirm('?????????????????????????????????',{icon:3},function(index){
                    layer.closeAll();
                    $.ajax({
                        type:'get',
                        data:{
                            source_id: source_id
                        },
                        url: '/webi/views/source/del',
                        // ???????????????
                        success: function (obj) {
                            if ( obj.code == 200 ) {
                               $('.table-tr-'+ source_id).parent().remove();//??????????????????
                            } else {
                                layer.msg(obj.message,{icon:2,time:1500});
                            }
                        }
                    });
                });
            };
            //SQL???????????????????????????
            _SOURCE.update = function(source_id){
                var group_id = _G.groupID();//???????????????????????????ID

                layer.open({
                    title: '???????????????',
                    type: 1,
                    offset:'100px',
                    area:['500px','auto'],
                    scrollbar: false,
                    content: group_id == 2 ? $('#layer-excel-source-update') : $('#layer-sql-source-update'),
                    btn: ['??????', '??????'],
                    success:function(){

                       if( group_id == 3 ){ //?????????input??????
                           document.getElementById("db_host").value = $('#updata-'+source_id).attr('data-host');
                           document.getElementById("db_database").value = $('#updata-'+source_id).attr('data-database');
                           document.getElementById("db_user").value = $('#updata-'+source_id).attr('data-user');
                           document.getElementById("db_pwd").value = '';
                           document.getElementById("db_port").value = $('#updata-'+source_id).attr('data-port');
                       }
                    },
                    yes:function(){
                        if( group_id == 3 ){
                            _SOURCE.commitSqlSource(source_id,5);
                        }else{
                            updatePloader.options.formData = {
                                source_id:source_id
                            };
                            updatePloader.upload();
                        }
                    },
                    btn2:function(){
                        if(group_id == 2){
                            $('#layer-upload-rate').find('.item').remove();
                        }
                    }
                });
            };
            //????????????Excel?????????
            _SOURCE.commitExcelSource = function(source_id){
                var sourceId = source_id || 0;

                var url = '/webi/views/import';

                if($('.table-name').html() == ''){
                    layer.msg('????????????????????????', {icon: 2, offset: '70px',time:1500});
                    return false;
                }
                $.ajax({
                    type: 'post',
                    url: url,
                    enctype: 'multipart/form-data',
                    data: {
                        source_id: sourceId,
                        source_name:$('#upload-rate p').text(),
                        action: 'webi'
                    },
                    success: function (res) {
                        if (res.code == 200) {
                            $('#upload-rate').find('.progress').remove();//???????????????
                            $('#source-name').val('');
                            $('#table-name p').empty();
                            _SOURCE.sourceList();//????????????
                        } else {
                            layer.msg(res.msg, {icon: 2, offset: '70px',time:1500});
                        }

                        $('#upload-rate').empty();
                    }
                });
            };
            //??????SQL?????????
            _SOURCE.commitSqlSource = function(source_id,type){
                var operation_type =  type || 0;
                var msg = '';

                if( type == 3 || type == 5){
                    var dt = E.getFormValues('source-content-sql');
                }else{
                    var dt = E.getFormValues('source-sql');
                }

                if( dt.db_host == ''){
                    msg += '??????????????????????????????<br/>';
                }
                if(dt.db_database == ''){
                    msg += '????????????????????????<br/>';
                }
                if(dt.db_user == ''){
                    msg += '??????????????????<br/>';
                }
                if(dt.db_pwd == ''){
                    msg += '???????????????<br/>';
                }
                if(dt.db_port == ''){
                    msg += '???????????????????????????<br/>';
                }
                if( msg != ''){
                    layer.msg(msg, {icon: 2, offset: '70px',time:2000});
                    return false;
                }

                $.ajax({
                    type: 'post',
                    url: '/webi/views/source/sql',
                    data: {
                        db_host:dt.db_host,
                        db_port:dt.db_port,
                        db_database:dt.db_database,
                        db_user:dt.db_user,
                        db_pwd:dt.db_pwd,
                        source_id:source_id,
                        operation_type:operation_type
                    },
                    success: function (res) {
                        if (res.code == 200) {
                            layer.msg('???????????????', {icon: 1, offset: '70px',time:1500});
                            if( operation_type > 3){ // ?????????  ????????????
                                $('#source-content-sql input').val('');
                                $('#source-sql input').val('');
                                _SOURCE.sourceList();//????????????
                                layer.closeAll();
                            }

                        } else {
                            layer.msg(res.message, {icon: 2, offset: '70px',time:1500});
                        }
                    }
                });
            };


            //???????????????????????????
            _GROUP.list = function(source_id){
                var source_id = source_id || 0;
               var group_id = _G.groupID();//?????????????????????ID

                $('#group-nav').empty();//??????????????????
                if( group_id != 1 &&source_id == 0){//Excel???SQL  ??????????????????
                    return false;
                }
                $.ajax({
                    type: 'get',
                    url: '/webi/design/views/group/list/'+ group_id +'/'+source_id,
                    success: function (res) {
                        if (res.code != 200) {
                            layer.msg(res.msg, {icon: 2, offset: '70px',time:1500});
                        } else {

                            if( res.data['view'] != '' ){
                                var group_html = _GROUP.creat(res.data['view']);//???????????????li
                                $('#group-nav').append(group_html);

                                //????????????????????????
                                $.each($('.group-each'), function (k, v) {
                                    $(this).on('click', function () {
                                        _G.search( $(this).attr('id').substring(2) );
                                    })
                                });
                            }
                        }
                    }
                });
            };
            //???????????????li
            _GROUP.creat = function(data) {

                var groupList_html="";
                $.each(data, function (k, v) {

                    groupList_html += $('#group-li-html').html();

                    groupList_html = groupList_html.replace(/[\$]view_name/g,v.view_name);
                    groupList_html = groupList_html.replace(/[\$]view_id/g,v._id);

                });
                return groupList_html;

            };
            //????????????
            _GROUP.add = function() {
                if(  _G.groupID() != 1 && _G.source_id == 1){
                    layer.msg( '???????????????????????????????????????' , { icon: 2,offset:'70px;',time:1500} ) ;
                    return false;
                }

                layer.open({
                    title: '???????????????',
                    offset: '70px',
                    type: 1,
                    area: ['540px', '170px'],
                    scrollbar: false,
                    closeBtn: 0,
                    content: layer_add_group,
                    btn: ['??????', '??????'],
                    yes: function () {
                            var dt = E.getFormValues('title-form');
                            dt.view_id = 0;

                            if(dt.title ==""){
                                layer.msg( '???????????????????????????' , { icon: 2,offset:'70px;',time:1500} ) ;
                                return false;
                            }

                            dt.source_id = _G.source_id;//?????????ID
                            dt.group_id = _G.groupID();//?????????????????????ID
                            E.ajax({
                                type: 'post',
                                url: '/webi/design/views/group/edit',
                                data: dt,
                                success: function (res) {
                                    if (res.code == 200) {

                                        $("#title").val("");

                                        //?????????????????????
                                        var nav_html=$("#group-nav-each").html();
                                        nav_html=nav_html.replace(/g_view_id/g,res.data.id);
                                        nav_html=nav_html.replace(/g_group_title/g,dt.title);
                                        $("#group-nav").append(nav_html);

                                        //??????????????????????????????
                                        $("#g_" + res.data.id).on('click',function(){
                                            _G.search(res.data.id);
                                        });

                                        layer.msg('????????????', {icon: 1, offset: '70px', time: 1500});

                                        $("#g_"+ res.data.id).trigger("click");//???????????????????????????

                                    } else {
                                        layer.msg(res.message, {icon: 2, offset: '70px',time:1500});
                                    }
                                }
                            });
                        }
                    });

            };
            //????????????
            _GROUP.edit = function(view_id) {

                var groupTitle=$("#group_"+view_id).text();
                layer.open({
                    title: '???????????????',
                    offset: '70px',
                    type: 1,
                    area: ['500px', '180px'],
                    scrollbar: false,
                    closeBtn: 0,
                    content: layer_add_group,
                    btn: ['??????','??????'],
                    success: function() {
                        $("#title").val(groupTitle);
                        $("#title").focus();
                    },
                    yes: function () {
                        var dt = E.getFormValues('title-form');
                        dt.view_id = view_id;

                        if(dt.title==""){
                            layer.msg("?????????????????????!", {icon: 2, offset: '70px',time:1500});
                            return false;
                        }
                        dt.group_id = _G.groupID();//?????????ID

                        E.ajax({
                            type: 'post',
                            url: '/webi/design/views/group/edit',
                            data:dt,
                            success: function (res) {
                                if (res.code == 200) {
                                    layer.closeAll();

                                    $("#group_"+view_id).html(dt.title);
                                    layer.msg('????????????', {icon: 1, offset: '70px', time: 1500});

                                } else {
                                    layer.msg(res.message, {icon: 2, offset: '70px',time:1500});
                                }
                            }
                        });
                    }
                });
            };
            //????????????
            _GROUP.del = function(view_id) {
                layer.confirm('?????????????????????????????????????????????????????????????????????????????????',{  icon: 3 ,offset: '100px'}, function (index) {
                    layer.close(index);

                    $.ajax({
                        type: 'get',
                        url: '/webi/design/views/group/del/' + view_id,
                        success: function (res) {
                            if (res.code == 200) {
                                layer.msg("????????????", {icon: 1, offset: '70px',time:1500});

                                if($("#view_"+view_id).hasClass("cur-list")){
                                    $('#right-content').empty();
                                }
                                $("#view_"+view_id).remove();
                            }else{
                                layer.msg(res.message, {icon: 2, offset: '70px',time:1500});
                            }
                        }
                    })
                })

            };


            //??????????????????
            _G.search = function(view_id){
                //??????????????????
                $("#g_"+view_id).eq($(this).index()).parent().addClass("cur-list").siblings().removeClass("cur-list");

                //????????????????????????
                $("#right-content").empty( );
                _G.globalJosn = {};//??????json_table??????

                $.ajax({
                    type: 'get',
                    data:{
                        view_id: view_id,
                        group_id: _G.groupID()
                    },
                    url: '/webi/design/views/table/linked/list',
                    success: function (res) {
                        if (res.code == 200) {

                            $("#groupId").val(view_id);//????????????????????????id

                            //??????????????????
                            if(!($.isEmptyObject(res.data['table']))){
                                _G.paste(res.data['table']);
                            }
                        }
                    }
                });

            };
            //??????????????????
            _G.paste = function(data){
                _G.globalJosn=data;//???????????????????????????????????????

                var i=0;

                var join_class = {
                    1:'left-img imgbox',
                    2:'inner-img imgbox',
                    3:'right-img imgbox'
                };

                //?????????????????????li
                var temp_link_html = $('#link_table_div').html();

                $.each(data, function (k, v) {
                    ++i;
                    if( i==1 ){

                        var primar_html=$('#link_primarTabe').html();
                        primar_html=primar_html.replace(/primarName/g,v.table_name);
                        primar_html=primar_html.replace(/parentId/g,k);

                        //????????????html
                        $('#right-content').append(primar_html);

                        //????????????????????????
                        $(".right-cont-fl" ).on('click','.glyphicon',function(){
                            _G.del(k);//??????uuid??????
                        });

                    }

                    var  link_li_html = "";  //????????????HTML

                    $.each(v.join, function (uid, join_table) {

                        var  temp_li_html = temp_link_html;
                        temp_li_html = temp_li_html.replace(/join_class/g,join_class[join_table[1]]);
                        temp_li_html = temp_li_html.replace(/uuId/g,uid);
                        temp_li_html = temp_li_html.replace(/sublistNmae/g,data[uid]['table_name']);
                        temp_li_html = temp_li_html.replace(/parentId/g,k);

                        link_li_html += temp_li_html;

                    });

                    //????????????????????????????????????????????????
                    if( i==1 && link_li_html== "" ){
                        $('.right-cont-w .fr').remove();
                    }

                    if(link_li_html!=""){
                        //??????????????????div????????????????????????
                        var p_left = document.getElementById(v.table_name).parentElement.offsetLeft;

                        var x_soller = !p_left ? 200 : p_left + 410;
                        var y_soller = document.getElementById(v.table_name).parentElement.parentElement.offsetTop + 71;

                        var sub_div = $('#append-div-ul').html().replace(/right-div/g,"right-cont-fr").replace(/li_list/g,link_li_html);
                        sub_div = sub_div.replace(/lf_val/g,( x_soller));
                        sub_div = sub_div.replace(/t_val/g,( y_soller));

                        $('#right-content').append( sub_div );
                    }


                });

                setTimeout(function(){
                    //??????????????????-???????????????
                    $.each($('.linked-content  li'), function (k, v) {

                        //??????
                        var id =$(this).attr('id').substring(3);
                        $(".li_"+ id +" .right-cont-fr-img").on('click','span',function(){
                            _POP.edit_show(id);
                        });

                        //??????
                        $(".li_"+ id +" .right-cont-fr-text").on('click','.glyphicon',function(){
                            _G.del(id);
                        });

                    });
                },500);

            };
            //????????????
            _G.del = function(uuid){
                layer.confirm('???????????????????????????????????????????????????', {icon: 3, offset: '100px'}, function (index) {
                    layer.close(index);

                    var viewId =$("#groupId").val();

                    var parent_id=$("#li_"+uuid).data('parent');//??????id
                    edit_link_contents=G_handle.del_node(uuid,parent_id);//???????????????????????????

                    $.ajax({
                        type:'post',
                        url: '/webi/design/views/group/link/del',
                        data:{
                            view_id:viewId,
                            table_json:edit_link_contents//??????json?????????????????????
                        },
                        dataType: 'JSON',
                        success: function (res) {
                            if (res.code == 200) {
                                //????????????????????????
                                $("#right-content").empty( );

                                layer.msg("????????????", {icon: 1, offset: '70px',time:1500});

                                //????????????HTML
                                setTimeout(_G.paste(res.data['table']),1500);

                                if($.isEmptyObject(edit_link_contents)){
                                    _G.globalJosn = {};  //???????????????????????????????????????
                                }
                            } else {
                                layer.msg(res.message, {icon: 2, offset: '70px',time:1500});
                            }
                        }
                    })
                })
            };
            //?????????????????????
            _G.showTableList = function(data){

                var tableList_html="";
                $.each(data, function (k, v) {

                    tableList_html+=$('#tableList').html();

                    tableList_html = tableList_html.replace(/Description/g,v.description);
                    tableList_html = tableList_html.replace(/TableId/g,v._id);
                    tableList_html = tableList_html.replace(/TableName/g,v.table_name);

                });
                return tableList_html;

            };
            //???????????????
            _G.hideTable = function(){

                <!--???????????????-->
                $('.linked-list').toggle(1800, '', false);

                $('#left-side-hide').toggle(1800);
                $('#left-side-show').toggle(1800);

//                $("#left-side-show").toggle(1000,function(){
//                        $('#left-side-show').removeClass('layui-icon-spread-left').addClass('layui-icon-shrink-right');
//                        $('#left-side-show').toggle(800, '', true);
//                },false);

                setTimeout(function(){
                    $("#right-content").removeClass("right-cont");
                },1300);//??????????????????

                //??????????????????
                $(".ul-list li").prop("onclick",null).off("click");//jQuery1.7+
                $(".ul-list li").attr('onclick','').unbind('click');//jQuery-1.7

              //  $('#left-side-show').attr('onclick','_G.showTable('+ $('.source-name-remind').data('source') +')');

            };
            //???????????????
            _G.showTable = function(source_id){
                var sourceID =$('.source-name-remind').data('source');
               
                source_id = sourceID || source_id;
          
                _G.tableList(source_id);

                $('.linked-list').toggle(1800, '', true);

                $('#left-side-hide').toggle(1800);
                $('#left-side-show').toggle(1800);

                $("#right-content").addClass("right-cont");

                //$('#left-side-show').attr('onclick','_G.hideTable()');

            };
            //?????????????????????
            _G.tableList = function(source_id){
                var source_id = source_id; //?????????id
                var group_id = _G.groupID();//?????????????????????ID

                if( group_id != 1 && source_id == 0){
                    return false;
                }//?????????????????????????????????

                $('.ul-list').empty();

                var page = layer.load();
                $.ajax({
                    type: 'GET',
                    data:{
                        group_id: group_id,
                        source_id: source_id
                    },
                    url: '/webi/design/views/table/search',
                    success: function (res) {
                        layer.close(page);
                        if( res.code==200 ){

                            if(!($.isEmptyObject(res.data))){

                                //?????????????????????
                                var html=  _G.showTableList(res.data.data);
                                $('.ul-list').append(html);

                                //??????????????????
                                $.each($('.ul-list li .source-table-choose'), function (k, v) {

                                    //???????????????
                                    $(this).on('click',function(){
                                        _G.chooseTableList($(this).attr('data-name'));
                                    });

                                });
                                $.each($('.ul-list li .source-table-detail'), function (k, v) {

                                    //?????????????????????
                                    $(this).on('click',function(){
                                        _G.tableStructure(
                                            $(this).attr('data-name'),
                                            $(this).attr('data-remarks'),
                                            source_id
                                        );
                                    });

                                });

                            }
                        }
                    }
                });
            };
            //???????????????
            _G.searchTableList = function(){
                var inputValue = document.getElementById("search").value;

                if(inputValue == ""){
                    layer.msg("????????????????????????", {icon: 2, offset: '70px',time:1500});
                    return false;
                }
                var group_id = _G.groupID();//?????????????????????ID

                $.ajax({
                    type: 'GET',
                    data:{
                        source_id: 0,
                        table_name: inputValue,
                        group_id: group_id
                    },
                    url: '/webi/design/views/table/search',
                    dataType: 'JSON',
                    success: function (res) {
                        $("#search").val("");
                        if( res.code==200 ){
                            if($.isEmptyObject(res.data)){

                                layer.msg("??????????????????", {icon: 1, offset: '70px',time:1500});
                                return false;

                            }else{
                                //????????????
                                $(".ul-list").empty();

                                //???????????????
                                var html=  _G.showTableList(res.data.data);
                                $('.ul-list').append(html);

                                //??????????????????
                                $.each($('.ul-list li .source-table-choose'), function (k, v) {

                                    //???????????????
                                    $(this).on('click',function(){
                                        _G.chooseTableList($(this).attr('data-name'));
                                    });

                                });
                                $.each($('.ul-list li .source-table-detail'), function (k, v) {

                                    //?????????????????????
                                    $(this).on('click',function(){
                                        _G.tableStructure(
                                            $(this).attr('data-name'),
                                            $(this).attr('data-remarks'),
                                            $('.source-name-remind').attr('data-source')
                                        );
                                    });

                                });

                            }
                        }else{
                            layer.msg(res.message, {icon: 2, offset: '70px',time:1500});
                            return false;
                        }

                    }
                });
            };
            //???????????????
            _G.chooseTableList = function(name){

                var table_name = name; //??????????????????
                var view_id = $('.cur-list').attr('data-id');

                if( typeof view_id == 'undefined' || view_id == ''){
                    layer.msg("??????????????????????????????~", {icon: 2, offset: '70px',time:2000});
                    return false;
                }

                var index  = layer.load() ;
                $.ajax({
                    type: 'get',
                    data:{
                        view_id: view_id,
                        group_id: _G.groupID(),
                        table_name: table_name,
                        source_id: _G.source_id
                    },
                    url: '/webi/design/views/make/table/structure',
                    success: function (res) {

                        layer.close(index);

                        if (res.code != 200) {//??????????????????????????????
                            layer.msg(res.msg, {icon: 2, offset: '70px',time:1500});
                            return false;
                        }

                        if ( $.isEmptyObject( res.data['tableL']['table']) ){  //???????????????????????????????????????
                            _G.addTable(view_id,res.data['tableR']['table'][0], res.data['tableR']['table'][1]);
                        } else {   //???????????????????????????

                            //???????????? ??????
                            _POP.l_options = res.data['tableL']['fields'];
                            _POP.r_options = res.data['tableR']['fields'];

                            layer_linked_edit = layer_linked_edit.replace(/tableName-select/g, res.data['tableR']['table'][0]);
                            layer_linked_edit = layer_linked_edit.replace(/selectTable/g, res.data['tableR']['table'][1]);

                           //???????????????
                            _POP.append_table(view_id,res.data);
                        }

                    }
                });
            };
            //???????????????????????????
            _G.tableStructure = function(name,remarks,source_id) {
                layer.open({
                    title: '?????????',
                    type: 1,
                    content: $('#layer-source-field'),
                    area: ['700px', '500px'],
                    success: function () {

                        var indexPage = layer.load();

                        $('.layer-source-field-box').empty();//??????????????????

                        var tr_thml = '<tr>';
                        tr_thml += '<td class="table-list-name">????????????</td>';
                        tr_thml += '<td>????????????</td>';
                        tr_thml += '<td>????????????</td>';
                        tr_thml += ' </tr>';
                        $('.layer-source-field-box').append(tr_thml);

                        $.ajax({
                            type:'get',
                            url: '/webi/views/table/field/list',
                            data: {
                                table_name: name,
                                source_id: source_id
                            },
                            success: function (res) {
                                layer.close(indexPage);

                                if( res.code==200 ){
                                    if(!($.isEmptyObject(res.data))){
                                        var source_tr_html = _SOURCE.tableField(res.data);//?????????????????????????????????
                                        $('.layer-source-field-comment .field-table-name').html(name);
                                        $('.layer-source-field-comment .field-table-remarks').html(remarks);
                                        $('.layer-source-field-box').append(source_tr_html);
                                    }
                                }
                            }
                        });
                    }
                });
            };
            //??????????????????table
            _G.addTable= function(view_id,table,table_name){

                //????????????table_json????????? ??????????????????????????????????????????????????????????????????,uuid
                G_handle.addJson( "", "", table, table_name, "","" );

                $.ajax({
                    type: 'post',
                    url: '/webi/design/views/group/link/add',
                    data: {
                        table_json : _G.globalJosn,
                        view_id : view_id
                    },
                    success: function (res) {
                        if (res.code == 200) {

                            //????????????????????????
                            $("#right-content").empty();

                            //????????????
                            setTimeout(function(){
                                _G.paste(res.data['table']);
                            },2000);


                        } else {
                            layer.msg(res.msg, {icon: 2, offset: '70px',time:1500});
                            return false;
                        }
                    }
                })
            };

            //??????????????????????????????
            _POP.append_table = function(view_id,data){
                layer.open({
                    title: '?????????????????????',
                    offset: '70px',
                    type: 1,
                    area: ['540px', '270px'],
                    scrollbar: false,
                    closeBtn: 0,
                    content: layer_linked_edit,
                    btn: ['??????', '??????'],
                    success:function(){
                        _POP.add_show(data['tableL'], data['tableR']);//?????????????????????
                        $('#li_select').addClass('clickli');//???????????????????????????
                    },
                    yes: function () {

                        var checkTableL = $("option:selected", $('#table_l')).val();
                        var checkTableR = data['tableR']['table'][0].toString();
                        var description= data['tableR']['table'][1].toString();
                        var innerType = $(".bomb-box .clickli").attr("data-code");

                        var leftField = "";
                        var rightField = "";
                        var field = [];
                        var error_msg = '';

                        $.each($('#table-field li'), function () {
                            leftField = $(this).find(".fields_l option:selected").val();
                            rightField = $(this).find(".fields_r option:selected").val();

                            if($.inArray(leftField + ":" + rightField , field) != "-1"){
                                $(this).addClass("li-lable");//???????????????
                                error_msg = "?????????????????????";
                                return false;
                            }
                            field.push(leftField + ":" + rightField);
                        });

                        if(error_msg != ""){
                            layer.msg(error_msg, {icon: 2, offset: '70px',time:1500});
                            return false;
                        }

                        //????????????table_json?????????   ??????????????????????????????????????????????????????????????????,uuid
                        G_handle.addJson(checkTableL,innerType,checkTableR,description,field,"");

                        _POP.submit( view_id, _G.globalJosn,1);//??????

                        //???????????????????????????
                        _POP.l_options = [];
                        _POP.r_options = [];
                    }
                });
            };
            //????????????????????????
            _POP.add_show = function(l_data,r_data){
                var table_l = "";
                var option_l = "";
                var option_r = "";
                var fieldL = [];
                var fieldR = [];

                //??????--??????
                $.each(l_data['table'], function (kt, vt) {
                    if( l_data['selected'] && kt== l_data['selected']){
                        table_l+='<option name="table_name_l" value="'+kt+'"  selected>'+vt+'</option>';
                    }else{
                        table_l+='<option name="table_name_l" value="'+kt+'">'+vt+'</option>';
                    }
                });

                if(r_data['innerType']){

                    $.each(l_data['fields'], function (k, v) {
                        fieldL.push(v['field_remark']);
                    })
                    $.each(r_data['fields'], function (kk, vk) {
                        fieldR.push(vk['field_remark']);
                    })

                    for (var i=0;i<l_data['fields'].length;i++) {

                        //??????-??????
                        $.each(l_data['allFields'], function (kl, vl) {
                            if( vl['field_name'] == l_data['fields'][i] ){
                                option_l = '<option name="fieldOne" class="left-table" value="' + vl['field_name'] + '" selected>' + vl['field_remark'] + '</option>';
                            }else{
                                option_l = '<option name="fieldOne" class="left-table" value="' + vl['field_name'] + '" >' + vl['field_remark'] + '</option>';
                            }
                            $('#table-field li:last-child .fields_l').append(option_l);
                        });

                        //??????--??????
                        $.each(r_data['allFields'], function (kf, vf) {
                            if( vf['field_name'] == r_data['fields'][i] ){
                                option_r = '<option name="fieldTwo" value="' + vf['field_name'] + '" selected>' +  vf['field_remark'] + '</option>';
                            }else{
                                option_r = '<option name="fieldTwo" value="' +vf['field_name'] + '">' +  vf['field_remark'] + '</option>';
                            }
                            $('#table-field li:last-child .fields_r').append(option_r);
                        });

                        //??????????????????
                        if(i+1<l_data['fields'].length){
                            var li = $('#link-layer-hidden').html();
                            li = li.replace(/fieldsLs/g, "fields_r");
                            li = li.replace(/fieldsRs/g, "fields_l");
                            $('#table-field').append(li);
                        }

                    }

                }else{
                    //??????-??????
                    $.each(l_data['fields'], function (k, v) {
                        option_l+= '<option name="fieldOne" class="left-table" value="'+v['field_name']+'">'+v['field_remark']+'</option>';
                    });

                    //??????--??????
                    $.each(r_data['fields'], function (kf, vf) {
                        option_r+= '<option name="fieldTwo" value="'+vf['field_name']+'">'+ vf['field_remark'] +'</option>';
                    });
                    $('.fields_l').append(option_l);
                    $('.fields_r').append(option_r);
                }

                $('#table_l').append(table_l);

                //?????????????????????????????????
                $('#table_l').on('change',function () {
                    _POP.select_change();
                });

                _POP.filed_changes();//?????????????????????????????????

            };
            //??????????????????????????????
            _POP.edit_show = function(uuid){
                var view_id = $("#groupId").val();//??????????????????id
                $.ajax({
                    type: 'get',
                    url: '/webi/design/views/group/search/' + view_id + '/' + uuid,
                    success: function (res) {
                        if (res.code == 200) {
                            if (res.data['tableL']){

                                _POP.l_options = res.data['tableL']['allFields'];//??????????????????
                                _POP.r_options = res.data['tableR']['allFields'];//??????????????????

                                var  layer_content = layer_linked_edit;

                                layer_content = layer_content.replace(/tableName-select/g, res.data['tableR']['table'][0]);
                                layer_content = layer_content.replace(/selectTable/g,res.data['tableR']['table'][1]);

                                layer.open({
                                    title: '?????????????????????',
                                    offset: '70px',
                                    type: 1,
                                    area: ['540px', '270px'],
                                    scrollbar: false,
                                    closeBtn: 0,
                                    content: layer_content,
                                    btn: ['??????', '??????'],
                                    yes: function () {

                                        var  checkTableL = $("option:selected", $('#table_l')).val();
                                        var checkTableR = res.data['tableR']['table'][1].toString();
                                        var innerType = $(".bomb-box .clickli").attr("data-code");

                                        var leftField = "";
                                        var rightField = "";
                                        var field = [];
                                        var msg = "";
                                        var pop_msg = "";

                                        $.each( $('#table-field li') , function(){
                                            leftField = $(this).find(".fields_l option:selected").val();
                                            rightField = $(this).find(".fields_r option:selected").val();

                                            if($.inArray(leftField + ":" + rightField , field) != "-1"){
                                                $(this).addClass("li-lable");//???????????????
                                                pop_msg = "?????????????????????";
                                                return false;
                                            }
                                            field.push(leftField + ":" + rightField);
                                        });
                                        if(pop_msg != ""){
                                            layer.msg( pop_msg, {icon: 2, offset: '70px',time:1500});
                                            return false;
                                        }

                                        //????????????table_json?????????   ??????????????????????????????????????????????????????????????????,uuid
                                        G_handle.addJson(checkTableL,innerType,checkTableR,"",field,uuid);

                                        if(msg == ""){
                                            _POP.submit( view_id, _G.globalJosn,2);//??????

                                            //???????????????????????????
                                            _POP.l_options = [];
                                            _POP.r_options = [];
                                        }

                                    }

                                })

                                _POP.add_show(res.data['tableL'], res.data['tableR']);//?????????????????????

                                if(res.data['tableR']['innerType']) {

                                    $.each($('#edit-link li'), function (k, v) {
                                        var inner=$(this).attr("data-code");
                                        if(inner==res.data['tableR']['innerType']){
                                            $(this).attr('class', 'clickli');
                                        }
                                    });

                                }

                            }else{
                                layer.msg(res.msg, {icon: 2, offset: '70px',time:1500});
                            }
                        }
                    }
                 })

            };
            //?????????????????????
            _POP.select_change = function(){
                var checkText = $("option:selected",$('#table_l')).val();
                var group_id = _G.groupID();//?????????????????????ID
                $.ajax({
                    type: 'get',
                    data:{
                        table_name: checkText,
                        group_id: group_id
                    },
                    url: '/webi/design/views/table/search/rule',
                    success: function (res) {
                        if( res.code == 200 ){

                            _POP.l_options = res.data['fields'];//???????????????????????????
                            _POP.select_options(res.data['fields']);//?????????????????????????????????option???

                        }
                    }
                })
            };
            //?????????????????????????????????
            _POP.filed_changes = function(){

                $('.fields_l').on('change',function () {
                    _POP.option_change();
                });

                $('.fields_r').on('change',function () {
                    _POP.option_change();
                });
            };
            //????????????????????????
            _POP.option_change = function(){

                var field = [];
                _POP.error_msg = "";
                $('#table-field li').removeClass("li-lable");

                $.each($('#table-field li'), function () {
                    leftField = $(this).find(".fields_l option:selected").val();
                    rightField = $(this).find(".fields_r option:selected").val();

                    if($.inArray(leftField + ":" + rightField , field) != "-1"){
                        $(this).addClass("li-lable");
                        _POP.error_msg = "?????????????????????";
                        layer.msg( _POP.error_msg, {icon: 2, offset: '70px',time:1500});
                        return false;
                    }
                    field.push(leftField + ":" + rightField);
                });


            };
            //?????????????????????????????????option???
            _POP.select_options = function(data){

                $('#table-field .fields_l').empty();
                var option_l="";
                $.each( data, function (k, v) {
                    option_l+='<option name="fieldOne" class="left-table" value="'+v['field_name']+'">'+v['field_remark']+'</option>';
                });
                $('#table-field .fields_l').append(option_l);

            };
            //????????????
            _POP.add_row = function(){

                var table_l = $("option:selected", $('#table_l')).val();
                var table_r = $('#table_r').data('name');
                var optionsR = "";
                var optionsL = "";

                var li = $('#link-layer-hidden').html();
                    li = li.replace(/fieldsLs/g, "fields_l");
                    li = li.replace(/fieldsRs/g, "fields_r");
                    $('#table-field').append(li);

                //????????????
                $.each( _POP.l_options, function (kl, vl) {
                    optionsL += '<option name="fieldOne" class="left-table" value="'+vl['field_name']+'">'+vl['field_remark']+'</option>';
                });
                $('#table-field li:last-child .fields_l').append(optionsL);

                //????????????
                $.each(_POP.r_options, function (kr, vr) {
                    optionsR += '<option name="fieldTwo" value="'+vr['field_name']+'" >'+vr['field_remark']+'</option>';
                });
                $('#table-field li:last-child .fields_r').append(optionsR);

                _POP.filed_changes();//?????????????????????????????????

                //???????????????option???
                $('.bomb-box-delete').on('click', function () {
                    $(this).closest('li').remove();
                })

            };
            //???????????????????????????JSON
            _POP.submit = function(view_id,table_json,type){

                <!--??????????????????????????????-->
                if( type == 1 ){
                    _G.hideTable();
                }

                $.ajax({
                    type: 'post',
                    url: '/webi/design/views/group/link/add',
                    data: {
                        view_id: view_id,
                        table_json:table_json,
                        type:type
                    },
                    success: function (result) {
                        if (result.code == 200) {
                            layer.closeAll();

                            //????????????????????????
                            $("#right-content").empty();

                            //????????????
                            setTimeout($("#g_" + view_id).trigger("click"),2000);


                        } else {
                            layer.msg(result.msg, {icon: 2, offset: '70px',time:1500});
                        }
                    }
                });

            };


            //????????????
            G_handle.del_node = function(del_uid,parent_uid) {

                if( typeof _G.globalJosn[del_uid] == 'undefined'){
                    alert('???????????????');
                    return false;
                }

                //??????????????????????????????????????????
                G_handle.diguiDel( _G.globalJosn[del_uid].join );

                delete _G.globalJosn[del_uid];

                if(!$.isEmptyObject(_G.globalJosn[parent_uid])){
                    delete _G.globalJosn[parent_uid]['join'][del_uid];
                }
                if( _G.globalJosn == {}){
                    _G.globalJosn = "";
                }

                return _G.globalJosn;
            };
            //???????????????????????????
            G_handle.diguiDel = function(obj) {
                if( obj.length == 0 ){
                    return false;
                }
                for( k in obj ){

                    delete _G.globalJosn[k];

                    if( obj[k].join.length > 0 ){
                        G_handle.diguiDel(obj[k].join);
                    }

                }
            };
            //?????????????????????????????????
            G_handle.addJson = function(checkTableL,innerType,checkTableR,description,field,uid) {

                    if( checkTableL == checkTableR ){
                        layer.msg("???????????????????????????", {icon: 2, offset: '70px',time:1500});
                        return false;
                    }

                    if( uid != "" ){//??????

                        for( k in _G.globalJosn ){

                            if(  _G.globalJosn[k].table == checkTableL ) {

                                delete _G.globalJosn[k]['join'][uid];//???????????????
                                _G.globalJosn[k]['join'][uid] = [ checkTableL,innerType,field];
                                _G.globalJosn[uid]['parent'] = k;//??????????????????uuid
                            }

                        }

                    }else{//??????

                        var uuid = BI.guid();//??????uuid

                        if( !$.isEmptyObject(_G.globalJosn) ){

                            for( k in  _G.globalJosn ){

                                //?????????????????????????????????????????????????????????
                                if( _G.globalJosn[k].table == checkTableL ){

                                    if (_G.globalJosn[k]['join'] == '[]') {
                                        _G.globalJosn[k]['join'] = {};
                                    }

                                    _G.globalJosn[k]['join'][uuid]=[checkTableR,innerType,field];

                                    //??????????????????????????????
                                    _G.globalJosn[uuid] = {
                                        uid : uuid,
                                        parent : k,
                                        table : checkTableR,
                                        table_name : description,//??????????????????
                                        join : {}
                                    }

                                    return false;
                                }
                            }
                        }else{

                            _G.globalJosn[uuid] = {
                                uid : uuid,
                                parent : "",
                                table : checkTableR,
                                table_name : description,
                                join : {}
                            };

                        }

                    }

                };

    </script>
@endsection

