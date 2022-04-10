<div class="content">

    <!--头部-->
    <div id="g_top_title" class="top-title over">
        <h6 class="fl">
            <a href="/webi/list/index" id="logo-href"><img src="/images/webi/webi_logo.jpg" alt="WeBI" style="margin-top: -6px;" height="59px;" width="179px;"></a>
        </h6>
        <div class="fr right-inner">
            <a href="javascript:void(0);" id="head_preview" class="preview" >预览</a>
            <a href="javascript:void(0);" id="upload_theme" class="glo_attribute" ><i class="layui-icon layui-icon-upload"></i>上传</a>
            <a href="/webi/design/views/list" class="top-btn overview" >我的数据集</a>
            <a href="javascript:void(0);" id="head_chooseBI" class="top-btn addBi" >添加BI</a>
            <a href="javascript:void(0);" id="head_We_Global_We_ATTR" class="top-btn global-attr">全局属性</a>
        </div>
    </div>

    <iframe frameborder="0" id="mainFrame" name="mainFrame" src="" width="100%" height="100%" style="display: none;"></iframe>

    <div class="main">

        <!--左侧数据集-->
        <div id="g_dts_main" class="left-list side-part slient">
            <div class="left-side-show side-show" id="left-side-show" style="opacity: 0;"><i class="layui-icon layui-icon-spread-left"></i></div>
            <div class="part-title over">
                <span class="fl">BI设计</span>
                <i id="dts_design_icon" class="fr icon side-icon layui-icon layui-icon-shrink-right"  data-type="left"></i>
            </div>
            <div class="list-cont">
                <div class="list-item">
                    <div class="title-box">
                        <div class="title">
                            <span class="fl">数据集</span>
                            <i class="fr icon down-arrow"></i>
                        </div>
                    </div>
                    <div id="selectBox" class="select-box hide">
                        <div class="dataSource-select">
                            <div class="data-title"><p>数据源</p></div>
                            <div class="select-dts">
                                <select id="selectSour" class="selectpicker source-group">
                                    <option value="0">系统数据源</option>
                                    <option value="1">微电汇数据源</option>
                                    <option value="2">Excel数据源</option>
                                    <option value="3">MYSQL数据源</option>
                                </select>
                            </div>
                        </div>
                        <div class="dataset-select">
                            <div class="data-title"><p>数据集</p></div>
                            <div class="select-dts data-mart">
                                <select id="selectDts" class="selectpicker">
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="list-item dts_sum_list_div">
                    <div class="title-box">
                        <div class="title">
                            <span class="fl">汇总</span>
                            <i class="fr add-show" data-id="1"><img src="/images/webi/biedit/icon-add.png" alt=""></i>
                        </div>
                    </div>
                    <div class="item-inner" id="item-inner-1">
                        <ul class="ul-1" id="sums">
                        </ul>
                    </div>
                </div>
                <div class="list-item dts_row_list_div">
                    <div class="title-box">
                        <div class="title">
                            <span class="fl">行设置</span>
                            <i class="fr add-show" data-id="2"><img src="/images/webi/biedit/icon-add.png" alt=""></i>
                        </div>
                    </div>
                    <div class="item-inner" id="item-inner-2">
                        <ul class="ul-2" id="rows">

                        </ul>
                    </div>
                </div>
                <div class="list-item dts_col_list_div">
                    <div class="title-box">
                        <div class="title">
                            <span class="fl">列设置</span>
                            <i class="fr add-show" data-id="3"><img src="/images/webi/biedit/icon-add.png" alt=""></i>
                        </div>
                    </div>
                    <div class="item-inner" id="item-inner-3">
                        <ul class="ul-3" id="cols">

                        </ul>
                    </div>
                </div>
                <div class="list-item dts_sort_list_div">
                    <div class="title-box">
                        <div class="title">
                            <span class="fl">排序设置</span>
                            <i class="fr add-show" data-id="4"><img src="/images/webi/biedit/icon-add.png" alt=""></i>
                        </div>
                    </div>
                    <div class="item-inner" id="item-inner-4">
                        <ul class="ul-4">

                        </ul>
                    </div>
                </div>
                <div class="list-item dts_where_list_div">
                    <div class="title-box">
                        <div class="title">
                            <span class="fl">筛选</span>
                            <i class="fr add-show" data-id="5"><img src="/images/webi/biedit/icon-add.png" alt=""></i>
                        </div>
                    </div>
                    <div class="item-inner" id="item-inner-5">
                        <ul class="ul-5">

                        </ul>
                    </div>
                </div>
            </div>

            <!--添加字段弹层-->
            <div class="field-info" id="field_info_pop" style="display: none;" >
                <div class="field-info-title">
                    <p>添加字段</p>
                    <i class="field-info-close">×</i>
                </div>
                <div id="field_info_inner" class="field-info-inner"></div>
                <div class="field-inf-btn">
                    <button>确定</button>
                </div>
            </div>

            <!--底部删除-->
            <div class="bottom-oper">
                <div class="refresh fr">
                   <input type="checkbox" id="refresh" value="0" style="vertical-align:middle;">
                    <label for="refresh"  style="vertical-align:middle;">&nbsp;自动刷新</label>
                </div>
                <div class="change fr">
                    <a href="javascript:void(0);">行列互换</a>
                </div>
                <div class="delete fr">
                    <a href="javascript:void(0);">清空所有设置</a>
                </div>
            </div>
        </div>

        <!--主内容-->
        <div class="main-cont parent pdleft pdright" id="parent_We_Main">
        </div>

        <!--右侧属性-->
        <div id="g_attr_main" class="right-set side-part slient">
            <div class="right-set-show side-show" id="right-set-show" style="opacity: 0;"><i class="layui-icon layui-icon-shrink-right"></i></div>
            <div class="part-title over">
                <span class="fl">属性</span>
                <i id="attr_design_icon" class="fr icon side-icon layui-icon layui-icon-spread-left" data-type="right"></i>
            </div>
            <div id="g_attr_setting" class="setting-cont">

                <div class="layui-collapse" style="border: none;" lay-filter="collapseFilter" lay-accordion >

                    <div class="layui-colla-item theme_attribute" id="theme" data-genre="theme">
                        <h2 class="layui-colla-title layer-module-title">模板库</h2>
                        <div class="layui-colla-content" id="parents_theme">
                            <div class="theme-region">
                                模板库<i class="layui-icon layui-icon-more theme-button"></i>
                            </div>
                        </div>
                    </div>

                    <div class="layui-colla-item general_attribute" id="general" data-genre="general">
                        <h2 class="layui-colla-title layer-module-title">常规</h2>
                        <div class="layui-colla-content" id="parents_general">
                            <div class="set-inner name general-title">
                                <p>名称</p>
                                <input type="text" class="attr-child" id="chart-title" name="title" data-attribute="general.title" data-style="">
                            </div>
                            <div class="set-inner">
                                <p>背景色</p>
                                <div class="color-pick">
                                    <input type="text" class="WeBIColor form-control attr-child" id="general_backgroundColor" value="" onchange="_We_ATTR.color_change"  name="backgroundColor"  data-attribute="general.backgroundColor" data-style="backgroundColor" readonly="readonly">
                                </div>
                            </div>
                            <div class="set-inner">
                                <p>背景图片</p>
                                <div class="upload-box">
                                    <div class="upload fl">
                                        <p>背景照片</p>
                                        <div class="bg-box">
                                            <div class="add-img layui-upload">
                                                <span class="b-img-add" id="b_img_upload" data-attribute="general.refresh_frequency">+</span>
                                                <p class="b-img-add">上传</p>
                                                <div class="layui-upload-list b_img" style="display:none;" >
                                                    <img id="b_img" class="layui-upload-img attr-child" data-attribute="general.backgroundImage" src="" style="width: 70px; height: 70px;">
                                                    <div class="img-del">
                                                        <i class="glyphicon glyphicon-trash"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="upload fr">
                                        <p>边框照片</p>
                                        <div class="bg-box">
                                            <div class="add-img layui-upload">
                                                <span class="border-add" id="border_upload" >+</span>
                                                <p class="border-add">上传</p>
                                                <div class="layui-upload-list border_img" style="display:none;">
                                                    <img id="border_img" class="layui-upload-img attr-child" data-attribute="general.border_image_source" src="" style="width: 70px; height: 70px;">
                                                    <div class="img-del">
                                                        <i class="glyphicon glyphicon-trash"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="set-inner">
                                <p>边框图片样式</p>
                                <div class="border-style">
                                    <div class="style">
                                        <p>粗细</p>
                                        <div class="icon-arrows">
                                            <div class="ant-input-number-handler-wrap">
                                                <span role="button" class="ant-input-number-handler-up"><i class="layui-icon layui-icon-up"></i></span>
                                                <span role="button" class="ant-input-number-handler-down "><i class="layui-icon layui-icon-down"></i></span>
                                            </div>
                                            <div>
                                                <input type="number" class="attr-child" name="border" id="bi_border_size" value="0" data-attribute="general.border" data-style="borderImageWidth">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="style">
                                        <p>偏移</p>
                                        <input type="number" class="attr-child" name="border_image_slice" id="bi_border_image_slice" value="0" data-attribute="general.border_image_slice" data-style="borderImageSlice">
                                    </div>
                                    <div class="style">
                                        <p>重复</p>
                                        <select name="bi_border_image_repeat" class="attr-child" id="bi_border_image_repeat" data-attribute="general.border_image_repeat" data-style="borderImageRepeat">
                                            <option value="stretch">拉伸</option>
                                            <option value="repeat">重复</option>
                                            <option value="round">环绕</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="set-inner general-font-way">
                                <p>字体样式</p>
                                <div class="color-pick font-color-general">
                                    <input class="WeBIColor form-control attr-child" value="" name="text-color" type="text" id="general_color" title="字体颜色"  onchange="_We_ATTR.color_change" readonly="readonly" data-attribute="general.color" data-style="color">
                                </div>
                                <div class="font-family-general">
                                    <select class="font-family table-font-family attr-child" id="general-font-family" title="字体" data-attribute="general.fontFamily" data-style="fontFamily">
                                    </select>
                                </div>
                                <div class="font-size-general">
                                    <input type="number" value="" name="general-font-size" class="font-weight attr-child" title="字体大小" min="1" step="1" data-attribute="general.fontSize" data-style="fontSize">
                                </div>
                                <div class="font-weight-general">
                                    <select class="general-fontWeight attr-child fontWeight" id="general-fontWeight" title="字体粗细" data-attribute="general.fontWeight" data-style="fontWeight">
                                        <option value="normal">normal</option>
                                        <option value="bold">bold</option>
                                    </select>
                                </div>
                                <div class="font-style-general">
                                    <select class="fontStyle attr-child" id="general-fontStyle" title="文本修饰" data-attribute="general.fontStyle" data-style="fontStyle">
                                        <option value="normal">normal</option>
                                        <option value="italic">italic</option>
                                        <option value="oblique">oblique</option>
                                    </select>
                                </div>
                            </div>
                            <div class="set-inner name sole-input" style="display: none">
                                <p>刷新频率（单位：分钟）</p>
                                <input type="number" class="refresh_frequency attr-child" name="refresh_frequency" id="refresh_frequency" data-attribute="general.refresh_frequency" data-style="refresh_frequency">
                            </div>
                            <div class="set-inner sole-input" style="display: none">
                                <div class="title-border">
                                    <p>边框样式</p>
                                    <button id="title-border-button" class="title-border-button short-setting" data-type="general">设置</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="layui-colla-item table_attribute" id="table" data-genre="table" style="display: none;">
                        <h2 class="layui-colla-title layer-module-title">表格</h2>
                        <div class="layui-colla-content" id="parents_table">
                            <div class="set-inner name">
                                <p>行高</p>
                                <input type="number" value=""  name="table_height" class="attr-child" min="1" step="1" data-attribute="table.height" data-style="height">
                            </div>
                            <div class="set-inner font-way">
                                <p>字体样式</p>
                                <div class="color-pick font-color-tab">
                                    <input class="WeBIColor form-control attr-child" value="" name="text-color" type="text" id="table_color" title="字体颜色"  onchange="_We_ATTR.color_change" readonly="readonly" data-attribute="table.color" data-style="color">
                                </div>
                                <div class="font-family-tab">
                                    <select class="font-family table-font-family attr-child" id="tab-font-family" title="字体" data-attribute="table.fontFamily" data-style="fontFamily">
                                    </select>
                                </div>
                                <div class="font-size-tab">
                                    <input type="number" value="" name="table-font-size" class="font-weight attr-child" title="字体大小" min="1" step="1" data-attribute="table.fontSize" data-style="fontSize">
                                </div>
                                <div class="font-weight-tab">
                                    <select class="tab-fontWeight attr-child fontWeight" id="tab-fontWeight" title="字体粗细" data-attribute="table.fontWeight" data-style="fontWeight">
                                        <option value="normal">normal</option>
                                        <option value="bold">bold</option>
                                    </select>
                                </div>
                                <div class="font-style-tab">
                                    <select class="fontStyle attr-child" id="tab-fontStyle" title="文本修饰" data-attribute="table.fontStyle" data-style="fontStyle">
                                        <option value="normal">normal</option>
                                        <option value="italic">italic</option>
                                        <option value="oblique">oblique</option>
                                    </select>
                                </div>
                            </div>

                            <div class="set-inner">
                                <p>背景色</p>
                                <div class="color-pick">
                                    <input class="WeBIColor form-control attr-child" value="" id="backgroundColor" onchange="_We_ATTR.color_change" name="table_backgroud_color"  type="text" readonly="readonly" data-attribute="table.backgroundColor"  data-style="backgroundColor">
                                </div>
                            </div>
                            <div class="set-inner">
                                <p>间隔色</p>
                                <div class="color-pick">
                                    <input class="WeBIColor form-control attr-child" value="" id="table_even_color" onchange="_We_ATTR.color_change"  name="table_even_color" type="text" readonly="readonly" data-attribute="table.even_color" data-style="evenColor">
                                </div>
                            </div>
                            <div class="set-inner">
                                <p>边框</p>
                                <div class="border">
                                    <p>样式</p>
                                    <div  class="border-style" id="border-style">
                                        <div class="table-border-style" id="table-border-style">
                                        </div>
                                    </div>
                                </div>
                                <div class="border">
                                    <p>粗细</p>
                                    <div class="border-thickness">
                                        <input type="number" value="" name="borderWidth" class="table-border" data-attribute="table.borderWidth"  data-style="borderWidth" >
                                    </div>
                                </div>
                                <div class="border">
                                    <p>颜色</p>
                                    <div  class="border-color">
                                        <input type="text" value="" name="borderColor" class="WeBIColor form-control attr-child table-border-color" id="borderColor" onchange="_We_ATTR.color_change" data-attribute="table.borderColor"  data-style="borderColor" readonly="readonly">
                                    </div>
                                </div>
                            </div>
                            <div class="set-inner">
                                <div class="col-width">
                                    <p>列宽度设置</p>
                                    <button id="row-wid-button" class="row-wid-button short-setting">设置</button>
                                </div>
                                <div class="col-legend">
                                    <p>列对齐方式</p>
                                    <button id="row-legend-button" class="row-legend-button short-setting">设置</button>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="layui-colla-item title_attribute" id="title" data-genre="title" style="display: none;">
                        <h2 class="layui-colla-title layer-module-title">标题</h2>
                        <form class="layui-form layui-switch">
                            <div class="layui-input-block">
                                <input type="checkbox" class="title-switch switch attr-child" name="title" lay-skin="switch" lay-filter="filter" data-attribute="title.switch">
                            </div>
                        </form>
                        <div class="layui-colla-content" id="parents_title">
                            <div class="set-inner name">
                                <p>名称</p>
                                <input type="text" class="title-value attr-child" name="title-value" id="title-value" value="" data-attribute="title.value">
                            </div>
                            <div class="set-inner name">
                                <p>链接</p>
                                <input type="text" class="link  attr-child" class="attr-child" name="link" data-attribute="title.link">
                            </div>
                            <div class="set-inner">
                                <p>打开方式</p>
                                <div class="target-way title-target-way attr-child" data-code="_self" data-attribute="title.target">当前页面</div>
                                <div class="target-way title-target-way attr-child" data-code="_blank" data-attribute="title.target">新窗口</div>
                            </div>
                            <div class="set-inner title-way">
                                <p>标题样式</p>
                                <div class="title-color">
                                    <input type="text" class="WeBIColor form-control attr-child" id="title-font-color" name="color" value="" title="字体颜色" onchange="_We_ATTR.color_change" data-attribute="title.color" readonly="readonly" data-style="color">
                                </div>
                                <div class="title-fontFamily">
                                    <select class="title-font-family font-family attr-child" id="fontFamily" title="字体" data-attribute="title.fontFamily">
                                    </select>
                                </div>
                                <div class="title-fontSize">
                                    <input type="number" value="14" class="attr-child"  name="font-size" min="1" step="1" title="字体大小" data-attribute="title.fontSize"  data-style="fontSize">
                                </div>
                                <div class="title-fontWeight">
                                    <select class="title_fontWeight font-weight attr-child" id="title_fontWeight" title="字体粗细" data-attribute="title.fontWeight" data-style="fontWeight">
                                        <option value="normal">normal</option>
                                        <option value="bold">bold</option>
                                    </select>
                                </div>
                                <div class="title-fontStyle">
                                    <select class="title_fontStyle font-style attr-child" id="title_fontStyle" title="文本修饰" data-attribute="title.fontStyle" data-style="fontStyle">
                                        <option value="normal">normal</option>
                                        <option value="italic">italic</option>
                                        <option value="oblique">oblique</option>
                                    </select>
                                </div>
                            </div>
                            <div class="set-inner">
                                <p>背景色</p>
                                <div class="color-pick">
                                    <input class="WeBIColor form-control attr-child" value="" onchange="_We_ATTR.color_change"  name="title_backgroud_color" data-attribute="title.backgroundColor" data-style="backgroundColor" id="title_backgroud_color" type="text">
                                </div>
                            </div>
                            <div class="set-inner">
                                <p>对齐方式</p>
                                <div class="title-legend legend-position attr-child" data-code="left" data-attribute="title.legend" data-style="legend" title="左对齐"><i class="layui-icon layui-icon-align-left"></i></div>
                                <div class="title-legend legend-position attr-child" data-code="center" data-attribute="title.legend" data-style="legend" title="居中对齐"><i class="layui-icon layui-icon-align-center"></i></div>
                                <div class="title-legend legend-position attr-child" data-code="right" data-attribute="title.legend" data-style="legend" title="右对齐"><i class="layui-icon layui-icon-align-right"></i></div>
                            </div>
                            <div class="set-inner">
                                <div class="title-border">
                                    <p>边框样式</p>
                                    <button id="title-border-button" class="title-border-button short-setting" data-type="title">设置</button>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="layui-colla-item text_attribute" id="text" data-genre="text" style="display: none;">
                        <h2 class="layui-colla-title layer-module-title">文本</h2>
                        <div class="layui-colla-content" id="parents_text">
                            <div class="set-inner general-font-way">
                                <p>内容字体样式</p>
                                <div class="color-pick font-color-general">
                                    <input class="WeBIColor form-control attr-child" value="" name="text-color" type="text" id="text_content_color" title="字体颜色"  onchange="_We_ATTR.color_change" readonly="readonly" data-attribute="text.content" data-style="color">
                                </div>
                                <div class="font-family-general">
                                    <select class="font-family table-font-family attr-child" id="content-font-family" title="字体" data-attribute="text.content" data-style="fontFamily" data-default="微软雅黑">
                                    </select>
                                </div>
                                <div class="font-size-general">
                                    <input type="number" value="" name="content-font-size" class="font-weight attr-child" title="字体大小" min="1" step="1" data-attribute="text.content" data-style="fontSize" data-default="14">
                                </div>
                                <div class="font-weight-general">
                                    <select class="general-fontWeight attr-child fontWeight" id="content-fontWeight" title="字体粗细" data-attribute="text.content" data-style="fontWeight" data-default="normal">
                                        <option value="normal">normal</option>
                                        <option value="bold">bold</option>
                                    </select>
                                </div>
                                <div class="font-style-general">
                                    <select class="fontStyle attr-child" id="content-fontStyle" title="文本修饰" data-attribute="text.content" data-style="fontStyle" data-default="normal">
                                        <option value="normal">normal</option>
                                        <option value="italic">italic</option>
                                        <option value="oblique">oblique</option>
                                    </select>
                                </div>
                            </div>
                            <div class="set-inner name">
                                <p>前缀</p>
                                <input type="text" class="prefix-value attr-child" name="prefix-value" id="prefix-value" value="" data-attribute="text.prefix" data-style="value">
                            </div>
                            <div class="set-inner general-font-way">
                                <p>前缀字体样式</p>
                                <div class="color-pick font-color-general">
                                    <input class="WeBIColor form-control attr-child" value="" name="text-color" type="text" id="text_prefix_color" title="字体颜色"  onchange="_We_ATTR.color_change" readonly="readonly" data-attribute="text.prefix" data-style="color">
                                </div>
                                <div class="font-family-general">
                                    <select class="font-family table-font-family attr-child" id="text-font-family" title="字体" data-attribute="text.prefix" data-style="fontFamily" data-default="微软雅黑">
                                    </select>
                                </div>
                                <div class="font-size-general">
                                    <input type="number" value="" name="text-font-size" class="font-weight attr-child" title="字体大小" min="1" step="1" data-attribute="text.prefix" data-style="fontSize" data-default="14">
                                </div>
                                <div class="font-weight-general">
                                    <select class="general-fontWeight attr-child fontWeight" id="text-fontWeight" title="字体粗细" data-attribute="text.prefix" data-style="fontWeight" data-default="normal">
                                        <option value="normal">normal</option>
                                        <option value="bold">bold</option>
                                    </select>
                                </div>
                                <div class="font-style-general">
                                    <select class="fontStyle attr-child" id="text-fontStyle" title="文本修饰" data-attribute="text.prefix" data-style="fontStyle" data-default="normal">
                                        <option value="normal">normal</option>
                                        <option value="italic">italic</option>
                                        <option value="oblique">oblique</option>
                                    </select>
                                </div>
                            </div>
                            <div class="set-inner name">
                                <p>后缀</p>
                                <input type="text" class="suffix-value attr-child" name="suffix-value" id="suffix-value" value="" data-attribute="text.suffix" data-style="value">
                            </div>
                            <div class="set-inner general-font-way">
                                <p>后缀字体样式</p>
                                <div class="color-pick font-color-general">
                                    <input class="WeBIColor form-control attr-child" value="" name="text-color" type="text" id="text_suffix_color" title="字体颜色"  onchange="_We_ATTR.color_change" readonly="readonly" data-attribute="text.suffix" data-style="color">
                                </div>
                                <div class="font-family-general">
                                    <select class="font-family table-font-family attr-child" id="text-font-family" title="字体" data-attribute="text.suffix" data-style="fontFamily" data-default="微软雅黑">
                                    </select>
                                </div>
                                <div class="font-size-general">
                                    <input type="number" value="" name="text-font-size" class="font-weight attr-child" title="字体大小" min="1" step="1" data-attribute="text.suffix" data-style="fontSize" data-default="14">
                                </div>
                                <div class="font-weight-general">
                                    <select class="general-fontWeight attr-child fontWeight" id="text-fontWeight" title="字体粗细" data-attribute="text.suffix" data-style="fontWeight" data-default="normal">
                                        <option value="normal">normal</option>
                                        <option value="bold">bold</option>
                                    </select>
                                </div>
                                <div class="font-style-general">
                                    <select class="fontStyle attr-child" id="text-fontStyle" title="文本修饰" data-attribute="text.suffix" data-style="fontStyle" data-default="normal">
                                        <option value="normal">normal</option>
                                        <option value="italic">italic</option>
                                        <option value="oblique">oblique</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="layui-colla-item chart_attribute" id="chart" data-genre="chart" style="display: none;">
                        <h2 class="layui-colla-title layer-module-title">图表</h2>
                        <div class="layui-colla-content" id="parents_theme">
                            <button id="chart-button" class="chart-button settings">设置</button>
                        </div>
                    </div>

                    <div class="layui-colla-item series_attribute" id="series" data-genre="series" style="display: none;">
                        <h2 class="layui-colla-title layer-module-title">序列</h2>
                        <div class="layui-colla-content" id="parents_series">
                            <div class="set-inner name">
                                <p>数据列</p>
                                <select name="col" id="series-col" class="form-control alias" data-attribute="series.col">
                                </select>
                            </div>
                            <div class="set-inner">
                                <p>值格式</p>
                                <div class="val-format legend-position attr-child" data-code="1" data-attribute="series.val_format" data-style="val_format" ><span>常规</span></div>
                                <div class="val-format legend-position attr-child" data-code="2" data-attribute="series.val_format" data-style="val_format" ><span>货币</span></div>
                                <div class="val-format legend-position attr-child" data-code="3" data-attribute="series.val_format" data-style="val_format"><span>百分比</span></div>
                            </div>
                            <div class="set-inner">
                                <p>单位格式</p>
                                <div class="unit-format legend-position attr-child" data-code="0" data-attribute="series.unit_format" data-style="unit_format">无</div>
                                <div class="unit-format legend-position attr-child" data-code="1" data-attribute="series.unit_format" data-style="unit_format">固定千</div>
                                <div class="unit-format legend-position attr-child" data-code="2" data-attribute="series.unit_format" data-style="unit_format">固定万</div>
                                <div class="unit-format legend-position attr-child" data-code="3" data-attribute="series.unit_format" data-style="unit_format">固定亿</div>
                                <div class="unit-format legend-position attr-child" data-code="4" data-attribute="series.unit_format" data-style="unit_format">自动千</div>
                                <div class="unit-format legend-position attr-child" data-code="5" data-attribute="series.unit_format" data-style="unit_format">自动万</div>
                                <div class="unit-format legend-position attr-child" data-code="6" data-attribute="series.unit_format" data-style="unit_format">自动亿</div>
                            </div>
                            <div class="set-inner name">
                                <p>小数位</p>
                                <input type="text" class="title-value attr-child" name="title-value" id="title-value" value="" data-attribute="series.decimal_num">
                            </div>
                            <div class="set-inner name" style="height:40px;">
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        {{--右侧图表设置弹层--}}
        <div id="chart-main" class="right-set side-part slient" style="display: none;">
            <div class="part-title over">
                <span class="fl">图表属性</span>
                <i id="attr_chart_icon" class="fr icon side-icon layui-icon layui-icon-spread-left" data-type="right"></i>
            </div>
            <div id="chart-setting" class="setting-cont setting-chart">
                <div class="layui-collapse" style="border: none;" lay-filter="collapseFilter" lay-accordion >

                    <div class="layui-colla-item chart_series_attribute" id="chart-series" data-genre="series">
                        <h2 class="layui-colla-title layer-chart-title">数据设置</h2>
                        <div class="layui-colla-content" id="chart_parents_series">
                            <div class="set-inner name">
                                <p>数据列</p>
                                <select name="col" id="series-alias" class="form-control alias" data-attribute="series.alias" data-parm="101">
                                </select>
                            </div>
                            <form class="layui-form">
                                <div class="set-inner">
                                    <div class="dataLine-title"><p>数据标注显示</p></div>
                                    <div class="dataLine-show makePoint">
                                        <input type="checkbox" class="switch-show attr-child" lay-skin="switch" lay-filter="chart-detial-filter" data-attribute="series.label" data-parm="101">
                                    </div>
                                </div>
                                <div class="set-inner">
                                    <div class="dataLine-title"><p>数据标线类型</p></div>
                                    <div class="chart-child makeLine-child">
                                        <div class="average">
                                            <div class="dataLine-title"><p>平均值</p></div>
                                            <div  class="dataLine-average">
                                                <input type="checkbox" class="attr-child" lay-skin="switch" lay-filter="chart-detial-filter" data-attribute="series.dataLine" data-parm="0">
                                            </div>
                                        </div>
                                        <div class="minV">
                                            <div class="dataLine-title"> <p>最小值</p></div>
                                            <div class="dataLine-min">
                                                <input type="checkbox" class="attr-child" lay-skin="switch" lay-filter="chart-detial-filter" data-attribute="series.dataLine"  data-parm="2">
                                            </div>
                                        </div>
                                        <div class="maxV">
                                            <div class="dataLine-title"> <p>最大值</p></div>
                                            <div  class="dataLine-max">
                                                <input type="checkbox" class="attr-child" lay-skin="switch" lay-filter="chart-detial-filter" data-attribute="series.dataLine" data-parm="1">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="set-inner name">
                                    <div class="dataLineColor-title"><p>数据标线颜色</p></div>
                                    <div class="dataLineColor-region">
                                        <input type="text" class="WeBIColor dataLineColor attr-child" id="chart-line" onchange="_We_CHART.color_change" data-attribute="series.dataLineColor" data-parm="101">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="layui-colla-item chart_legend_attribute" id="chart-legend" data-genre="legend">
                        <h2 class="layui-colla-title layer-chart-title">图例设置</h2>
                        <div class="layui-colla-content" id="chart_parents_legend">
                            <div class="set-inner">
                                <div class="title-show"><p>显示</p></div>
                                <div class="show-box layui-form">
                                    <input type="checkbox" class="switch-show attr-child" name="legend" lay-skin="switch" lay-filter="chart-detial-filter" data-attribute="legend.switch" data-parm="101">
                                </div>
                            </div>
                            <div class="set-inner align">
                                <p>X轴对齐方式</p>
                                <div class="legend-position attr-child chart-aligned" data-code="left" data-attribute="legend.x" title="左对齐" data-default="left"><i class="layui-icon layui-icon-align-left"></i></div>
                                <div class="legend-position attr-child chart-aligned" data-code="center" data-attribute="legend.x" title="居中对齐" data-default="left"><i class="layui-icon layui-icon-align-center"></i></div>
                                <div class="legend-position attr-child chart-aligned" data-code="right" data-attribute="legend.x"  title="右对齐" data-default="left"><i class="layui-icon layui-icon-align-right"></i></div>
                            </div>
                            <div class="set-inner align">
                                <p>Y轴对齐方式</p>
                                <div class="legend-position attr-child chart-aligned" data-code="top" data-attribute="legend.y" data-default="top" title="顶部对齐"><i class="glyphicon glyphicon-object-align-top"></i></div>
                                <div class="legend-position attr-child chart-aligned" data-code="center" data-attribute="legend.y" data-default="top" title="中部对齐"><i class="glyphicon glyphicon-object-align-horizontal"></i></div>
                                <div class="legend-position attr-child chart-aligned" data-code="bottom" data-attribute="legend.y" data-default="top"  title="底部对齐"><i class=" glyphicon glyphicon-object-align-bottom "></i></div>
                            </div>
                        </div>
                    </div>

                    <div class="layui-colla-item chart_title_attribute" id="chart-title" data-genre="title">
                        <h2 class="layui-colla-title layer-chart-title">标题设置</h2>
                        <div class="layui-colla-content" id="chart_parents_title">
                            <div class="set-inner">
                                <div class="title-show"><p>显示</p></div>
                                <div class="show-box layui-form">
                                    <input type="checkbox" class="switch-show attr-child" name="title" lay-skin="switch" lay-filter="chart-detial-filter" data-attribute="title.switch" data-parm="101">
                                </div>
                            </div>
                            <div class="set-inner name">
                                <p>主标题</p>
                                <input type="text" value=""  name="mainTitle" class="attr-child" min="1" step="1" data-attribute="title.mainTitle" data-style="" data-parm="101">
                            </div>
                            <div class="set-inner name">
                                <p>副标题</p>
                                <input type="text" value=""  name="subTitle" class="attr-child" min="1" step="1" data-attribute="title.subTitle" data-style="" data-parm="101">
                            </div>
                            <div class="set-inner">
                                <p>链接</p>
                                <div class="title-href">
                                    <input class="form-control attr-child" value="" id="linked" type="text" data-attribute="title.link" data-parm="101">
                                </div>
                                <div class="target-way  href-target-way attr-child chart-aligned" data-code="_self"  data-default="_blank" title="链接打开方式" data-attribute="title.target">当前页面</div>
                                <div class="target-way  href-target-way attr-child chart-aligned" data-code="_blank"  data-default="_blank" title="链接打开方式" data-attribute="title.target">新窗口</div>
                            </div>
                            <div class="set-inner font-way">
                                <p>样式</p>
                                <div class="color-pick font-color-tab">
                                    <input class="WeBIColor form-control attr-child" value="" name="text-color" id="chart-title-color" type="text" id="table_color" title="字体颜色"  onchange="_We_CHART.color_change" readonly="readonly" data-attribute="title.color" data-style="color" data-parm="101">
                                </div>
                                <div class="font-family-tab">
                                    <select class="font-family table-font-family attr-child" id="tab-font-family" title="字体"  data-default="微软雅黑" data-attribute="title.fontFamily" data-style="fontFamily" data-parm="101">
                                    </select>
                                </div>
                                <div class="font-size-tab">
                                    <input type="number" value="" name="table-font-size" class="font-weight attr-child" title="字体大小" data-default="14" min="1" step="1" data-attribute="title.fontSize" data-style="fontSize" data-parm="101">
                                </div>
                                <div class="font-weight-tab">
                                    <select class="tab-fontWeight attr-child fontWeight" id="tab-fontWeight" title="字体粗细" data-default="normal" data-attribute="title.fontWeight" data-style="fontWeight" data-parm="101">
                                        <option value="normal">normal</option>
                                        <option value="bold">bold</option>
                                    </select>
                                </div>
                                <div class="font-style-tab">
                                    <select class="fontStyle attr-child" id="tab-fontStyle" title="文本修饰" data-default="normal" data-attribute="title.fontStyle" data-parm="101">
                                        <option value="normal">normal</option>
                                        <option value="italic">italic</option>
                                        <option value="oblique">oblique</option>
                                    </select>
                                </div>
                            </div>
                            <div class="set-inner">
                                <p>X轴对齐方式</p>
                                <div class="align-legend legend-position attr-child chart-aligned" data-code="left" data-attribute="title.x" data-style="x" data-default="left" title="左对齐"><i class="layui-icon layui-icon-align-left"></i></div>
                                <div class="align-legend legend-position attr-child chart-aligned" data-code="center" data-attribute="title.x" data-style="x" data-default="left" title="居中对齐"><i class="layui-icon layui-icon-align-center"></i></div>
                                <div class="align-legend legend-position attr-child chart-aligned" data-code="right" data-attribute="title.x" data-style="x" data-default="left" title="右对齐"><i class="layui-icon layui-icon-align-right"></i></div>
                            </div>
                            <div class="set-inner" style="margin-bottom: 60px">
                                <p>Y轴对齐方式</p>
                                <div class="align-legend legend-position attr-child chart-aligned" data-code="top" data-attribute="title.y" data-default="top" title="顶部对齐"><i class="glyphicon glyphicon-object-align-top"></i></div>
                                <div class="align-legend legend-position attr-child chart-aligned" data-code="center" data-attribute="title.y" data-default="top" title="中部对齐"><i class="glyphicon glyphicon-object-align-horizontal"></i></div>
                                <div class="align-legend legend-position attr-child chart-aligned" data-code="bottom" data-attribute="title.y" data-default="top" title="底部对齐"><i class=" glyphicon glyphicon-object-align-bottom "></i></div>
                            </div>
                        </div>
                    </div>

                    <div class="layui-colla-item chart_axis_attribute" id="chart-axis" data-genre="axis">
                        <h2 class="layui-colla-title layer-chart-title">坐标轴设置</h2>
                        <div class="layui-colla-content" id="chart_parents_Axis">
                            <div class="set-inner name">
                                <div class="axis-title  title-x"><p class="button-checked-bottom" data-attribute="xAxis">X轴设置</p></div>
                                <div class="axis-title title-y"><p data-attribute="yAxis">Y轴设置</p></div>
                                <div class="chart-child">
                                    <div>
                                        <div class="axis-show-title"><p>显示</p></div>
                                        <div class="layui-form axis-show">
                                            <input type="checkbox" class="switch-show attr-child" name="show" lay-skin="switch" lay-filter="chart-detial-filter" data-attribute="axis.switch" data-parm="101">
                                        </div>
                                    </div>
                                    <div class="set-inner font-way">
                                        <p>样式</p>
                                        <div class="color-pick font-color-tab">
                                            <input class="WeBIColor form-control attr-child" value="" id="chart-axis-color" name="text-color" type="text" id="axis_color" title="字体颜色"  onchange="_We_CHART.color_change" readonly="readonly" data-attribute="axis.color" data-parm="101">
                                        </div>
                                        <div class="font-family-tab">
                                            <select class="font-family table-font-family attr-child" id="axis-font-family" title="字体" data-default="微软雅黑" data-attribute="axis.fontFamily" data-parm="101">
                                            </select>
                                        </div>
                                        <div class="font-size-tab">
                                            <input type="number" value="" name="axis-font-size" class="font-weight attr-child" title="字体大小" data-default="14" min="1" step="1" data-attribute="axis.fontSize" data-parm="101">
                                        </div>
                                        <div class="font-weight-tab">
                                            <select class="tab-fontWeight attr-child fontWeight" id="axis-fontWeight" title="字体粗细" data-default="normal" data-attribute="axis.fontWeight" data-parm="101">
                                                <option value="normal">normal</option>
                                                <option value="bold">bold</option>
                                            </select>
                                        </div>
                                        <div class="font-style-tab">
                                            <select class="fontStyle attr-child" id="axis-fontStyle" title="文本修饰" data-default="normal" data-attribute="axis.fontStyle" data-parm="101">
                                                <option value="normal">normal</option>
                                                <option value="italic">italic</option>
                                                <option value="oblique">oblique</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="set-inner font-way">
                                        <div class="title-show"><p>缩放</p></div>
                                        <div class="layui-form show-box">
                                            <input type="checkbox" class="attr-child" name="show" lay-skin="switch" lay-filter="chart-detial-filter" data-attribute="axis.scale" data-parm="0">
                                        </div>
                                        <div>
                                            <input type="number" class="scale attr-child" title="开始值" name="start" data-attribute="axis.scale" data-parm="1">
                                            <input type="number" class="scale attr-child" title="结束值" name="end" data-attribute="axis.scale" data-parm="2">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="layui-colla-item chart_tooltip_attribute" id="chart-tooltip" data-genre="tooltip">
                        <h2 class="layui-colla-title layer-chart-title">提示设置</h2>
                        <div class="layui-colla-content" id="chart_parents_tooltip">
                            <div class="set-inner name">
                                <div class="set-inner name">
                                    <div class="tooltip-show-title"><p>显示</p></div>
                                    <div class="layui-form tooltip-switch-button">
                                        <input type="checkbox" class="tooltip-switch attr-child" name="show" lay-skin="switch" lay-filter="chart-detial-filter" data-attribute="tooltip.switch" data-parm="101">
                                    </div>
                                </div>
                                <div class="set-inner name">
                                    <div class="tooltip-title"><p>背景色</p></div>
                                    <div class="edit-input-region">
                                        <input type="text" class="WeBIColor backgroundColor attr-child" name="backgroundColor" id="chart-tooltip-color" id="tooltip-backgroundcolor" onchange="_We_CHART.color_change" data-attribute="tooltip.backgroundColor" data-parm="1">
                                    </div>
                                    <div class="layui-form tooltip-switch-button">
                                        <input type="checkbox" class="attr-child" name="show" lay-skin="switch" lay-filter="chart-detial-filter" data-attribute="tooltip.backgroundColor" data-parm="0">
                                    </div>
                                </div>
                                <div class="set-inner name">
                                    <div class="tooltip-title"><p>边框宽度</p></div>
                                    <div class="edit-input-region">
                                        <input type="text" class="chart-tooltip-borderwidth attr-child" name="borderwidth" id="tooltip-borderwidth" data-attribute="tooltip.borderWidth.width" data-parm="1">
                                    </div>
                                    <div class="layui-form tooltip-switch-button">
                                        <input type="checkbox" name="show" class="attr-child" lay-skin="switch" lay-filter="chart-detial-filter" data-attribute="tooltip.borderWidth.switch" data-parm="0">
                                    </div>
                                </div>
                                <div class="set-inner name">
                                    <div class="tooltip-title"><p>边框颜色</p></div>
                                    <div class="edit-input-region">
                                        <input type="text" class="WeBIColor borderColor attr-child" name="borderColor" id="chart-tooltip-borderColor" onchange="_We_CHART.color_change" data-attribute="tooltip.borderColor" data-parm="1">
                                    </div>
                                    <div class="layui-form tooltip-switch-button">
                                        <input type="checkbox" class="attr-child" name="show" lay-skin="switch" lay-filter="chart-detial-filter" data-attribute="tooltip.borderColor.show" data-parm="0">
                                    </div>
                                </div>
                                <div class="set-inner font-way">
                                    <p>文字样式</p>
                                    <div class="font-family-tab">
                                        <select class="font-family table-font-family attr-child" id="tooltip-font-family" title="字体" data-default="微软雅黑" data-attribute="tooltip.fontFamily" data-parm="101">
                                        </select>
                                    </div>
                                    <div class="font-size-tab">
                                        <input type="number" value="" name="table-font-size" class="font-weight attr-child" id="tooltip-fontSize" title="字体大小" min="1" step="1" data-default="14" data-attribute="tooltip.fontSize" data-parm="101">
                                    </div>
                                    <div class="font-weight-tab">
                                        <select class="tab-fontWeight attr-child fontWeight" id="tooltip-fontWeight" title="字体粗细" data-default="normal" data-attribute="tooltip.fontWeight" data-parm="101">
                                            <option value="normal">normal</option>
                                            <option value="bold">bold</option>
                                        </select>
                                    </div>
                                    <div class="color-pick font-color-tab chart-color">
                                        <input class="WeBIColor form-control attr-child" value="" name="text-color" type="text" id="tooltip-color" title="字体颜色" id="chart-tooltip-clor"  onchange="_We_CHART.color_change" readonly="readonly" data-attribute="tooltip.color" data-parm="101">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="layui-colla-item chart_toolbox_attribute" id="chart-toolbox" data-genre="toolbox">
                        <h2 class="layui-colla-title layer-chart-title">工具栏设置</h2>
                        <div class="layui-colla-content" id="chart_parents_toolbox">
                            <div class="set-inner name">
                                <div class="set-inner name">
                                    <div class="toolbox-title"><p>数据视图</p></div>
                                    <div class="layui-form toolbox-switch-button">
                                        <input type="checkbox" class="attr-child" lay-skin="switch" lay-filter="chart-detial-filter" data-default="0" data-attribute="toolbox.dataView" data-parm="101">
                                    </div>
                                </div>
                                <div class="set-inner name">
                                    <div class="toolbox-title"><p>区域缩放</p></div>
                                    <div class="layui-form toolbox-switch-button">
                                        <input type="checkbox" class="attr-child" lay-skin="switch" lay-filter="chart-detial-filter" data-default="0" data-attribute="toolbox.dataZoom" data-parm="101">
                                    </div>
                                </div>
                                <div class="set-inner name">
                                    <div class="toolbox-title"><p>配置还原</p></div>
                                    <div class="layui-form toolbox-switch-button">
                                        <input type="checkbox" class="attr-child" lay-skin="switch" lay-filter="chart-detial-filter" data-default="0" data-attribute="toolbox.restore" data-parm="101">
                                    </div>
                                </div>
                                <div class="set-inner name">
                                    <div class="toolbox-title"><p>保存图片</p></div>
                                    <div class="layui-form toolbox-switch-button">
                                        <input type="checkbox" class="attr-child" lay-skin="switch" lay-filter="chart-detial-filter" data-default="0" data-attribute="toolbox.saveAsImage" data-parm="101">
                                    </div>
                                </div>
                                <div class="set-inner name">
                                    <div class="toolbox-title"><p>类型切换</p></div>
                                    <div class="layui-form toolbox-switch-button">
                                        <input type="checkbox" class="attr-child" lay-skin="switch" lay-filter="chart-detial-filter" data-default="0" data-attribute="toolbox.magicType" data-parm="101">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="layui-colla-item chart_linkage_attribute" id="linkage" data-genre="linkage">
                        <h2 class="layui-colla-title layer-chart-title">联动</h2>
                        <div class="layui-colla-content" id="chart_parents_linkage">
                            <button id="linkage-button" class="settings">设置</button>
                            <button class="settings set-del" style="display: none"><i class="layui-icon layui-icon-delete"></i></button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>

<!--弹层筛选条件设置-->
<div class="screen-layer" style="display: none;">
        <div class="screen-title">
            <p class="fl">筛选条件设置</p>
            <a href="javascript:;" class="fr layer-close"></a>
        </div>
        <div class="screen-inner">
            <div class="screen-tabel">
                <div class="table-head">
                    <table>
                        <thead>
                        <tr>
                            <th>字段</th>
                            <th>描述</th>
                            <th>关系</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <div class="table-body">
                    <table class="search-conditions">
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="screen-select">
                <p class="fl edit-now"></p>
                <div class="screen-select-box fl" style="width:45px;">
                    <select name="select_y_n" id="select_y_n" class="search-relation">
                        <option value="Y">是</option>
                        <option value="N">否</option>
                    </select>
                </div>
                <div class="screen-select-box fl">
                    <select name="select_relation" id="select_relation" class="search-relation">
                        <option value="1">等于</option>
                        <option value="2">大于</option>
                        <option value="3">大于等于</option>
                        <option value="4">小于</option>
                        <option value="5">小于等于</option>
                        <option value="6">包含</option>
                        <option value="7">排斥</option>
                        <option value="8">模糊匹配</option>
                        <option value="9">介于</option>
                    </select>
                </div>
                <div class="fl input-box text-in">
                    <input type="text" id="text_start">
                </div>
                <div class="fl include text-in text-include" style="margin-left: 5px;display: none"><span>~</span></div>
                <div class="fl input-box include text-in text-include" style="display: none">
                    <input type="text" id="text_end">
                </div>
                <div class="fl input-box time-in">
                    <input type="text" id="date_start">
                </div>
                <div class="fl include time-in time-include" style="margin-left: 5px;display: none"><span>~</span></div>
                <div class="fl input-box include time-in time-include" style="display: none">
                    <input type="text" id="date_end">
                </div>
            </div>
            <div class="screen-expres">
                <p>筛选表达式</p>
                <div class="screen-text">
                    <textarea id="show_where_sql"></textarea>
                </div>
            </div>
            <div class="screen-btn pull-right">
                <button type="button" class="btn btn-default" id="cancel">取消</button>
                <button type="button" class="btn btn-primary" id="confirm">确认</button>&nbsp;&nbsp;
            </div>
        </div>
</div>

<!-- 报表BI模块 -->
<div id="bi_content_html" style="display: none;">
    <div class="list-inner top-grid" id="grid_$uid">
        <div class="list-infor">
            <div class="list-oper" data-id="$uid" id="move_$uid">
                <span class="oper-touch chart-touch" id="touch_$uid" data-id="$uid"></span>
                <span class="oper-icon chart-operation"></span>
                <div class="oper-box" style="display: none;">
                    <a href="javascript:void(0);" class="chart-delete" data-id="$uid">删除</a>
                    <a href="javascript:void(0);" class="chart-copy" data-id="$uid">复制</a>
                    <a href="javascript:void(0);" class="chart-replace" data-id="$uid">更换</a>
                </div>
            </div>
            <div class="chart" id="chart_$uid"></div>
            <i class="icon-drag" id="icondrag_$uid" data-id="$uid"></i>
        </div>
    </div>
</div>

<!-- 公共pop设置项 -->
<div id="dts_pop_html" class="operation" style="display: none">
    <div class="operation-1 dts_pop_rename">
        <p class="m-o rename">重命名</p>
    </div>
    <div class="operation-1 dts_pop_edit">
        <p class="m-o edit">修改</p>
    </div>
    <div class="operation-1 dts_pop_delete">
        <p class="m-o delete" >删除</p>
    </div>
    <div class="operation-1 dts_pop_sort">
        <p class="oper-arr m-o">排序</p>
        <div class="operation-2 order order-show" style="display: none">
            <p class="check order-type dts_pop_asc order-asc" data-order="ASC">升序</p>
            <p class="order-type dts_pop_asc order-desc" data-order="DESC">降序</p>
        </div>
    </div>
    <div class="operation-1 dts_pop_limit">
        <p class="oper-arr m-o">
            显示
        </p>
        <div class="operation-2 appear-show" style="display: none">
            <p class="limit-num">1</p>
            <p class="limit-num">10</p>
            <p class="limit-num">30</p>
            <p>自定义</p>
            <div class="operation-3 dts_pop_selflimit" style="display: none">
                <input type="text" name="" id="">
                <div class="operation-3-btn">
                    <button class="limit-self">确定</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 单个字段选项li -->
<div id="dts_field_li_html" style="display: none">
        <li class="sub-item">
            <span class='sub_field' data-uid="$uid" data-type="$type" data-table="$table" data-field="$field" data-alias="$alias" data-sort="$sort">$alias
            </span><i class="fr">...</i>
        </li>
</div>

<!-- 字体选项option -->
<div id="side_option_html" style="display: none">
    <option value="$font_family">$font_family</option>
</div>

 <!-- 宽度设置单个字段选项li -->
 <div id="attr_width_li_html" style="display: none">
     <li class="row-li">
         <span class="row-field-name">$width_name</span>
         <input type="number" min="0.1" step="0.1" max="100" value="$widthPercent" data-type="$widthType">
         <span class="row-field-percent">%</span>
     </li>
 </div>

 <!-- 列文本对齐方式设置单个div -->
 <div id="attr_legend_div_html" style="display: none">

         <div class="col-legend-field" id="col-legend-field">
             <span class="row-field-name">$legend_name</span>
             <select class="col-legend length-$num" id="col-legend" data-type="$legendType">
                 <option value="center">center</option>
                 <option value="left">left</option>
                 <option value="right">right</option>
             </select>
         </div>

 </div>

 <!--宽度设置弹层-->
 <div class="row-info width-html" id="row_info_pop" style="display: none;;z-index: 9999">
     <div class="row-info-title">
         <p>列宽度设置</p>
         <i class="row-info-close">×</i>
     </div>
     <div id="row_info_inner" class="row-info-inner">
         <ul class="row-field" id="attr-row-field">
         </ul>
     </div>
     <div class="row-inf-btn">
         <button>确定</button>
     </div>
 </div>

 <!--列对齐方式设置弹层-->
 <div class="row-info col-html" id="col_info_pop" style="display: none;;z-index: 4">
     <div class="row-info-title">
         <p>列文本对齐方式设置</p>
         <i class="row-legend-close">×</i>
     </div>
     <div id="legend-region" class="row-info-inner">
     </div>
     <div class="row-inf-btn">
         <button>确定</button>
     </div>
 </div>

 <!-- 表格边框下拉框 div -->
 <div class="row-info" id="table_border_select_html" style="display: none;z-index: 9999">
     <div class="table_border_select">
         <ul data-style="borderStyle" data-attribute="table.borderStyle">
             <li data-classify="none" title="None" class="border-none" ><div>None</div></li>
             <li data-classify="solid" title="实线" class="border-solid"></li>
             <li data-classify="dotted" title="点状边框" class="border-dotted"></li>
             <li data-classify="double" title="双线" class="border-double"></li>
             <li data-classify="dashed" title="虚线" class="border-dashed"></li>
             <li data-classify="groove" title="3D凹槽边框" class="border-groove"></li>
             <li data-classify="ridge" title="3D垄状边框"  class="border-ridge"></li>
             <li data-classify="inset" title="3D嵌入边框"  class="border-inset"></li>
             <li data-classify="outset" title="3D突出边框" class="border-outset"></li>
         </ul>
     </div>
 </div>

 <!-- 四周边框设置弹层 div -->
<div class="border-round" id="rim-info-html" style="display:none;z-index: 9998;background-color: #ffa970">

     <div class="border-inner-title">
         <p>边框设置</p>
         <i class="border-style-close">×</i>
     </div>

     <div class="border-region-box">

         <div class="border-box">
             <div class="border-top"><div class="top-box round-boder" data-position="top"></div></div>
             <div class="border-left"><div class="left-box round-boder" data-position="left"></div></div>
             <div class="border-right"><div class="right-box round-boder" data-position="right"></div></div>
             <div class="border-bottom"><div class="bottom-box round-boder" data-position="bottom"></div></div>
         </div>
         <div class="set-box">
             <div class="border overall-border" style="height: 47px;">
                 <p >显示</p>
                 <div class="set-border">
                     <form class="layui-form" >
                         <div class="layui-form-item">
                             <div class="layui-input-block">
                                 <input type="checkbox" name="show" title="是">
                             </div>
                         </div>
                     </form>
                 </div>
             </div>

             <div class="border overall-border">
                 <p>粗细</p>
                 <div class="border-thickness">
                     <input type="number" value="" name="borderWidth" class="table-border" data-attribute="borderWidth"  data-style="borderWidth" >
                 </div>
             </div>
             <div class="border overall-border">
                 <p>颜色</p>
                 <div  class="border-color">
                     <input type="text" value="" name="borderColor" class="WeBIColor form-control attr-child table-border-color" id="globalBorderColor" onchange="_We_ATTR.borderColor_change" data-attribute="borderColor"  data-style="borderColor" readonly="readonly">
                 </div>
             </div>
             <div class="border overall-border">
                 <p>样式</p>
                 <div  class="border-style" id="border-style">
                     <div class="table-border-style second-border-style" id="border-style">
                     </div>
                 </div>
             </div>
         </div>

     </div>

     <div class="border-inner-btn">
         <button>确定</button>
     </div>

 </div>
 <!--模板库模板弹窗 -->
<div id="theme-box" style="display: none;">
    <input type="hidden" value="" id="group-type-id">
    <div class="modal-body">
        <div class="theme-modal-body">
            <div class="dashboard-filter">
                <div class="radio-group">
                    <div class="radio-button-wrapper radio-button-1" data-index="1"><span>云端</span></div>
                    <div class="radio-button-wrapper radio-button-2" data-index="2"><span>本地</span></div>
                </div>
                <div class="select-theme-box">
                    <div class="form-group">
                        <select class="form-control search-option" id="search-option">
                            <option value="1">下载量</option>
                            <option value="2">收藏量</option>
                        </select>
                    </div>
                </div>

                <div class="fr input-box search-theme">
                    <input type="text" id="theme-search" class="form-control" placeholder="请输入模板名称" />
                    <i class="search"></i>
                </div>

            </div>
            <div class="main-list theme-ul">
                <ul class="theme-content" id="theme-content">
                </ul>
            </div>

        </div>

    </div>
    <div id="page" class="theme-page"></div>
</div>
 <!--生成模板库模板（li）-->
<div id="template-li" style="display: none;">
    <li class="template-box template-$id" data-id="$id">
        <div class="template-img"><img src="#"></div>
        <div class="template-title">template_title</div>
    </li>
</div>
 <!--上传模板库弹窗 -->
<div id="ulpoadTheme" class="layui-form" style="display: none;">
    <div class="theme-upload">
        <div class="theme-input theme-upload-name">
            <label class="layui-form-label"><span class="red">*</span>模板名称：</label>
            <div class="template-input-block">
                 <input type="text" class="layui-input theme-template-title" placeholder="请输入模板名称" value="">
            </div>
        </div>
        <div class="theme-input  theme-upload-group">
            <label class="layui-form-label"><span class="red">*</span>模板分组：</label>
            <div class="template-input-block">
                <input type="checkbox" class="theme-template-group" name="group" value="2" title="本地" checked>
                <input type="checkbox" class="theme-template-group" name="group" value="1" title="云端" disabled>
            </div>
        </div>
        <div class="theme-input theme-upload-icon">
            <label class="layui-form-label"><span class="red">*</span>模板展示图：</label>
            <div class="template-input-block">
                <button class="btn btn-success" id="upload-template-pic">上传模板展示图</button>
                 <input type="hidden" class="layui-input theme-template-pic" value="">
            </div>
        </div>
    </div>
</div>
{{--联动设置弹出层--}}
<div id="linkage-box" class="layer-linkage" style="display: none;">

    {{--左侧--}}
    <div class="item-container-left">
        <div class="add-container">
            <span class="item-title">选择被关联维度字段</span>
            <i class="layui-icon layui-icon-add-circle icon-add"></i>
        </div>
        <div class="dimen-item">

        </div>
    </div>

    {{--右侧--}}
    <div class="item-container-right">
        <div class="layui-tab" lay-filter="linkage">
            <ul class="layui-tab-title">
                <li class="layui-this" lay-id="1">同数据集</li>
                <li lay-id="2">非同数据集</li>
            </ul>
            <div class="layui-tab-content">
                {{--同数据集--}}
                <div class="layui-tab-item layui-show">
                    <div class="layui-form">
                        <p class="chart-num">同数据集(已关联<span class="same-chart-num">0</span>个图表，共<span class="same-chart-total">0</span>个图表)</p>
                        <div class="star-inserted">
                            <input type="checkbox" id="same_select_all" title="全选" lay-skin="primary" lay-filter="same_select_all">
                        </div>
                        <ul class="layui-row same-chart">
                        </ul>
                    </div>
                </div>
                {{--END--}}

                {{--非同数据集--}}
                <div class="layui-tab-item">

                    <div class="layui-form">
                        <p class="chart-num">非同数据集(已关联<span class="diff-chart-num">0</span> 个图表，共<span class="diff-chart-total">0</span>个图表）</p>
                        <div class="star-inserted">
                            <input type="checkbox" id="diff_select_all" title="全选" lay-skin="primary" lay-filter="diff_select_all">
                        </div>
                        <ul class="layui-row diff-chart">
                        </ul>
                    </div>

                </div>
                {{--END--}}
            </div>
        </div>
    </div>

</div>
{{--联动设置左侧维度select--}}
<div class="chart-link-select" style="display: none">
    <div class="item-content">
        <div class="selecter-container">
            <div class="selected-item">
                <select class="selected-subset">

                </select>
            </div>
            <div class="icon-delete">
                <i class="layui-icon layui-icon-delete icon-del"></i>
            </div>
        </div>
    </div>
</div>
{{--联动设置右侧维度---同数据集li--}}
<div class="chart-link-same-li" style="display: none">
    <li class="layui-col-md6">
        <input type="checkbox" name="chart" value="$uuid" title="$title" lay-skin="primary" lay-filter="c_one">
    </li>
</div>
{{--联动设置右侧维度---非同数据集li--}}
<div class="chart-link-different-li" style="display: none">
    <li class="layui-col-md12">
        {{--组件名称--}}
        <input type="checkbox" name="diff-chart" value="$uuid" title="$title" lay-skin="primary" lay-filter="d_one">

        <div class="cube-box element_$uuid" style="display: none">
            {{--数据集名称--}}
            <div class="cube-label">
                <i class="layui-icon layui-icon-component"></i>
                <span data-chart="$charts">$ds</span>
            </div>
            {{--数据集字段--}}
            <div class="layui-form cube-column-item">
                <select multiple lay-search data-id="$uuid" data-table="$charts" lay-filter="multi">
                    <option value="" disabled selected>选择被关联维度字段</option>
                </select>
            </div>
        </div>
    </li>
</div>
