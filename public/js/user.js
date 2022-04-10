// 设置全局的tableURL，这种方式主用于base.js里表格查询的参数设置
var bootstrap_table_ajax_url  = '/eoa/user/search';

//加载表格
$('#table').bootstrapTable({
    classes: 'table table-hover', //bootstrap的表格样式
    sidePagination: 'server', //获取数据方式【从服务器获取数据】
    pagination: true, //分页
    height: $(window).height() - 200, //表格高度
    pageNumber: 1, //页码【第X页】
    pageSize: 10, //每页显示多少条数据
    queryParamsType: 'limit',
    queryParams: function (params) {
        var dt = E.getFormValues('search-form');
        $.extend(params, dt);
        return params;
    },

    url:bootstrap_table_ajax_url ,//ajax链接
    sortName: 'uuid', //排序字段
    sortOrder: 'DESC',//排序方式
    columns: [ //字段
        { title: '操作', field: 'operation', align: 'center' },
        { title: '姓名', field: 'trueName', align: 'center'  },
        { title: '部门',  field: 'departmentName', align: 'center' },
        { title: 'email', field: 'email', align: 'left' },
        { title: '手机', field: 'mobile', align: 'center' }
    ]

});

var User ={

    top_departments : top_departments,

    all_department_data: all_department_data,

    errmsg:'',

//   添加和修改员工
    edit: function (uuid) {
        var data = E.getFormValues('department-form');

        if(data.departmentID){
            var name;
            $.each(User.all_department_data,function(i,n){
                if(data.departmentID == n.departmentID)
                    name = n.departmentName;
            })
        }

        var html = '<form id="group-form" onsubmit="return false;" class="form-horizontal" role="form">';
        html += '<input type="hidden" name="uuid" id="uuid" value="0">';

        html += '<div class="form-group">';
        html += '<label class="col-sm-3 control-label" for="userID"><span class="red pr5">*</span> 用户名：</label>';
        html += '<div class="col-sm-9">';
        html += '<input class="form-control" type="text" id="userID" name="userID" maxlength="20" value="" placeholder="请输入用户名">';
        html += '</div>';
        html += '</div>';

        html += '<div class="form-group">';
        html += '<label class="col-sm-3 control-label" for="trueName"><span class="red pr5">*</span> 真实姓名：</label>';
        html += '<div class="col-sm-9">';
        html += '<input class="form-control" type="text" id="trueName" name="trueName" maxlength="20" value="" placeholder="请输入真实姓名">';
        html += '</div>';
        html += '</div>';

        html += '<div class="form-group">';
        html += '<label class="col-sm-3 control-label" for="userPwd"><span class="red pr5">*</span> 密码：</label>';
        html += '<div class="col-sm-9">';
        html += '<input class="form-control" type="password" id="userPwd" name="userPwd" maxlength="20" value="" placeholder="请输入密码">';
        html += '</div>';
        html += '</div>';

        html += '<div class="user_father"><div class="form-group">';
        html += '<label class="col-sm-3 control-label" for="departmentID"><span class="red pr5">*</span> <span>所属部门</span>：</label>';
        html += '<div class="col-sm-9">';
        html += '<select class="form-control" id="departmentID" name="departmentID" data-parentID="0" onchange="User.sub_department_show($(this))" action="user">';

        if(uuid == 0){
            if(data.departmentID){
                html += '<option value="'+ data.departmentID + '"  data_department_name="'+ name +'" selected readonly>'+ name + '</option>';
            }else if(!data.departmentID){
                html += '<option value="">请选择部门</option>';
                $.each( top_departments , function(i,n){
                    html += '<option value="'+ n.departmentID + '"  data_department_name="'+ n.departmentName +'">'+ n.departmentName + '</option>';
                });
            }
        }

        html += '</select></div>';
        html += '</div></div>';

        html += '<div class="form-group">';
        html += '<label class="col-sm-3 control-label" for="email"><span class="red pr5">*</span> email：</label>';
        html += '<div class="col-sm-9">';
        html += '<input class="form-control" type="text" id="email" name="email" maxlength="20" value="" placeholder="请输入email">';
        html += '</div>';
        html += '</div>';

        html += '<div class="form-group">';
        html += '<label class="col-sm-3 control-label" for="mobile"><span class="red pr5">*</span> 手机号：</label>';
        html += '<div class="col-sm-9">';
        html += '<input class="form-control" type="text" id="mobile" name="mobile" maxlength="20" value="" placeholder="请输入手机号">';
        html += '</div>';
        html += '</div>';

        html += '<div class="form-group">';
        html += '<label class="col-sm-3 control-label" for="roleId"><span class="red pr5">*</span> <span>用户角色</span>：</label>';
        html += '<div class="col-sm-9">';
        html += '<select class="form-control" id="roleId" name="roleId">';
        html += '<option value="">请选择员工角色</option>';
        $.each(roles,function(i,n){
            html += '<option value="'+ n.roleID + '"> ' + n.roleName + '</option>';
        })
        html += '</select>';
        html += '</div></div>';

        html += '</form>';

        layer.open({
            title: uuid == 0 ? '新增员工' : '修改员工信息',
            type: 1,
            offset: '50px',
            area: ['450px', '460px'],
            scrollbar: false,
            content: html,
            btn: ['保存', '取消'],
            yes: function (index) {

                var dt = E.getFormValues('group-form');
                dt.departmentName = $('#group-form').find('select').find('option:selected').attr('data_department_name');

                //验证单
                if (dt.userID == '') {
                    layer.alert('请输入用户名', {icon: 2, offset: '70px'});
                    return false;
                }
                if (dt.trueName == '') {
                    layer.alert('请选择真实姓名', {icon: 2, offset: '70px'});
                    return false;
                }
                if (dt.userPwd == '') {
                    layer.alert('请输入密码', {icon: 2, offset: '70px'});
                    return false;
                }
                if (dt.departmentID == '') {
                    layer.alert('请输入所属部门', {icon: 2, offset: '70px'});
                    return false;
                }
                if (dt.email == '') {
                    layer.alert('请输入企业邮件', {icon: 2, offset: '70px'});
                    return false;
                }
                var reg =  /^[a-zA-Z0-9]+@(([a-zA-Z0-9])+\.)+([a-zA-Z0-9]{2,4})+$/;
                if(!reg.test(dt.email)){
                    layer.alert('邮箱格式不正确', {icon: 2, offset: '70px'});
                    return false;
                }
                if (dt.mobile == '') {
                    layer.alert('请输入手机号码', {icon: 2, offset: '70px'});
                    return false;
                }
                reg = /^[0-9]{11}$/;
                if(!reg.test(dt.mobile)){
                    layer.alert('手机号码必须为11位有效数字', {icon: 2, offset: '70px'});
                    return false;
                }

                if(dt.role == ''){
                    layer.alert('请选择用户角色', {icon: 2, offset: '70px'});
                    return false;
                }

                E.ajax({
                    type: 'POST',
                    url: '/eoa/user/store',
                    data: dt,
                    success: function (res) {
                        if (res.code == 200) {
                            layer.alert('员工信息保存成功', {icon: 1, offset: '70px', time: 1500});
                            if (dt.uuid) {
                                layer.close(index);
                            } else {
                                $('#userID').val('');
                                $('#userName').val('');
                                $('#userPwd').val('');
                                if($.isEmptyObject(data)){   // 为特定部门添加，不清除
                                    $('#departmentID').val('');
                                }
                                $('#email').val('');
                                $('#mobile').val('');
                                $('#role').val('');
                            }
                            $('#table').bootstrapTable('refresh');
                        } else {
                            layer.alert(res.message, {icon: 2, offset: '70px'});
                        }
                    }
                });

            }
        });

        $('#userID').focus();

        if (uuid) {
            E.ajax({
                type: 'get',
                url: '/eoa/user/detail/' + uuid,
                success: function (res) {
                    if (res.code == 200) {
                        $('#userID').val(res.data.userID);
                        $('#uuid').val(res.data.uuid);
                        $('#trueName').val(res.data.trueName);
                        $('#userPwd').val(res.data.userPwd);
                        $('#group-form').find('.user_father').html(res.html);
                        $('#email').val(res.data.email);
                        $('#mobile').val(res.data.mobile);
                        $('#roleId').val(res.data.roleId);
                    }else {
                        layer.alert(res.message, {icon: 2, offset: '70px'});
                    }
                }
            });
        }

    },

    get_user_list:function(obj){
        var name = obj.attr('data_department_name');
        var id = obj.attr('data_department_id');
        var h1 = $('#department-form').find('[type="hidden"]');
        var h2 = $('#search-form').find('[type="hidden"]');
        var i = '<input type="hidden" class="form-control" name="departmentID" value="'+ id + '">';
        //没找到就插入
        if( h1.length == 0){
            $('#department-form').append(i);
        }else{   //  找到替换
            h1.val(id);
        }

        if( h2.length == 0){
            $('#search-form').append(i);
        }else {
            h2.val(id);
        }
//      替换数据后模拟查询点击，提交参数
        $('#search-form').find('[name="search"]').click();
    },


    //删除用户
    del:function(uuid){
        layer.confirm('您确认要删除该员工吗？',
            {  icon: 3 ,
                offset: '100px'
            }, function (index) {

                layer.close(index);

                E.ajax({
                    type: 'get',
                    url: '/eoa/user/delete/' + uuid,
                    success: function (res) {
                        if (res.code == 200) {
                            layer.alert('员工删除成功', {icon: 1, offset: '70px', time: 1000});
                            $('#table').bootstrapTable('refresh');
                        } else {
                            layer.msg(res.message, {icon: 2, offset: '70px'});
                        }
                    }
                });

            });

    },


//  部门多级分类折叠动作

//    用户和部门选择所属部门时，有下属部门就显示
    sub_department_show:function(obj){
        // 先移除所有的select
        obj.parents('.form-group').eq(0).nextAll('.form-group').remove();
        //  再判断添加
        var action = obj.attr('action');
        var id= $.trim(obj.val());                   //       去除ID两边的空白
        //     不选择下属部门，返出
        if( id == obj.attr('data-parentID')){
            return ;
        }

        var name = obj.find('option:selected').attr('data_department_name');
        // 为部门添加上级部门为parentID,为用户添加为departmentID
        var className = action == 'department'? 'parentID' : 'departmentID' ;
        $.ajax({
             type:'get',
             url:'/eoa/department/sub/'+ id,
             dataType:'json',
             success:function(res){

                 if(res.code == 200){
                     //处理完数据
                     if($.isEmptyObject(res.data)) {
                         return;
                     }
                         str = '<div class="form-group"><label class="col-sm-3 control-label" style="font-size:15px;letter-spacing:0.1rem;"></label><div class="col-sm-9">';
                         str += '<select data-parentID=' + id + '  name="' + className + '" class="form-control" onchange="User.sub_department_show($(this))" action="'+ action + '"><option value="'+ id +'" selected>不选择下属部门</option>';
                         $.each(res.data,function(i,n){
                             str += ' <option value="' + n.departmentID + '" data_department_name="'+ n.departmentName + '">' + n.departmentName + '</option>';
                         })
                         str += '</select></div></div>';
                         //把得到的数据插进所选择部门后面
                         $('.' + action + '_father').append(str);
                         //为最后一个select元素添加name字段
                         $('.' + action + '_father').find('select').removeAttr('name').last().attr('name',className);

                 }else {
                     layer.alert(res.message, {icon: 2, offset: '70px'});
                 }
             }
        });
    },

}


//  阻止冒泡事件
$('.list-group-item a').click(function(){
    event.stopPropagation();
    $('.list-group-item').css('background','transparent');
    $(this).parents('.list-group-item').eq(0).css('background','lightgrey');
})

var Department = {

    toggleFold:function(obj){
        var listGroup = obj.siblings('.list-group');

        if( listGroup.css('display')=='block'){
            listGroup.removeClass('showAction').addClass('hiddenArea');
            obj.find('.glyphicon-play').css('transform','rotateZ(90deg)');  // 旋转90度（弧度）
        }else{
            listGroup.removeClass('hiddenArea').addClass('showAction');
            obj.find('.glyphicon-play').css('transform','rotateZ(0deg)');
        }
    },


    search:function(){
        var dt = E.getFormValues('department-search');
        $.ajax({
            type: 'POST',
            url: '/eoa/department/search',
            data: dt,
            success: function (res) {
                if(res.code == 200){
                    $('#main_department').html(res.data);
                }else {
                    layer.alert(res.message, {icon: 2, offset: '70px'});
                }
            }
        });
    },

    edit:function(mark){
        //  拿到表单数据
        var data = E.getFormValues('department-form');
        if( mark!=0 && $.isEmptyObject(data) ){
            return;
        }
        data.mark = mark;
        var html = '<form id="group-form" onsubmit="return false;" class="form-horizontal" role="form">';
        html += '<input type="hidden" name="departmentID" id="departmentID" value="0">';

        html += '<div class="form-group">';
        html += '<label class="col-sm-3 control-label" for="departmentCode"><span class="red pr5">*</span> 部门编号：</label>';
        html += '<div class="col-sm-9">';
        html += '<input class="form-control" type="text" id="departmentCode" name="departmentCode" maxlength="20" value="" placeholder="请输入部门编号">';
        html += '</div>';
        html += '</div>';

        html += '<div class="form-group">';
        html += '<label class="col-sm-3 control-label" for="departmentName"><span class="red pr5">*</span> 部门名称：</label>';
        html += '<div class="col-sm-9">';
        html += '<input class="form-control" type="text" id="departmentName" name="departmentName" maxlength="20" value="" placeholder="请输入部门名称">';
        html += '</div>';
        html += '</div>';


        if(mark == 0){
            html += '<div class="department_father"><div class="form-group">';
            html += '<label class="col-sm-3 control-label" for="parentID"><span class="red pr5">*</span> <span>上级部门</span>：</label>';
            html += '<div class="col-sm-9">';
            html += '<select class="form-control" id="parentID" name="parentID" data-parentID="0" onchange="User.sub_department_show($(this))" action="department">';

            html += '</select>';
            html += '</div></div>';
            html += '</div>';
        }else{
            html += '<div class="form-group">';
            html += '<label class="col-sm-3 control-label" for="parentName"><span class="red pr5">*</span> 上级部门：</label>';
            html += '<div class="col-sm-9">';
            html += '<input class="form-control" type="text" id="parentName" name="parentName" maxlength="20" value="" placeholder="请输入上级部门" readonly>';
            html += '</div>';
            html += '</div>';
        }

        html += '<div class="form-group">';
        html += '<label class="col-sm-3 control-label" for="sort"><span class="red pr5">*</span> 排序：</label>';
        html += '<div class="col-sm-9">';
        html += '<input class="form-control" type="text" id="sort" name="sort" maxlength="20" value="" placeholder="请输入排序">';
        html += '</div>';
        html += '</div>';

        html += '</form>';

        var layer_index = 0,

        layer_index = layer.open({
            title: mark == 1 ? '编辑部门信息' : '添加部门',
            type: 1,
            offset: '50px',
            area: '450px',
            // scrollbar: false,
            content: html,
            btn: ['保存', '取消'],
            yes: function (layer_index) {

                var dt = E.getFormValues('group-form');
                dt.mark = mark;

                //验证单
                if (dt.departmentCode == '') {
                    layer.alert('请输入部门编码', {icon: 2, offset: '70px'});
                    return false;
                }
                if (dt.departmentName == '') {
                    layer.alert('请输入部门名称', {icon: 2, offset: '70px'});
                    return false;
                }
                if (dt.sort == '') {
                    layer.alert('请输入排序', {icon: 2, offset: '70px'});
                    return false;
                }

                E.ajax({
                    type: 'POST',
                    url: '/eoa/department/store',
                    data: dt,
                    success: function (res) {
                        if (res.code == 200) {
                            layer.alert('部门保存成功', {icon: 1, offset: '70px', time: 1500});
                            $('#main_department').html(res.data.html);
                            if (dt.mark == 1) {
                                layer.close(layer_index);
                            } else {
                                $('#departmentCode').val('');
                                $('#departmentName').val('');
                                $('#sort').val('');
                            }
                            if(mark == 1){
                                $('#table').bootstrapTable('refresh');
                            }
                            //更新部门数据，供给页面静态调用
                            User.all_department_data =  res.data.department;
                        } else {
                            layer.alert(res.message, {icon: 2, offset: '70px'});
                        }
                    }
                });
            }
        });
        $('#departmentCode').focus();

        E.ajax({
            type: 'post',
            url: '/eoa/department/edit',
            data:data,
            success: function (res) {
                if(mark != 0){
                    if (res.code == 200) {
                        $('#departmentCode').val(res.data.departmentCode);
                        $('#departmentName').val(res.data.departmentName);
                        $('#parentName').val(res.data.parentName);
                        $('#sort').val(res.data.sort);
                        if(mark == 1){
                            $('#group-form').append('<input type="hidden" name="departmentID" value="'+ res.data.departmentID +'"/>');
                        }
                        $('#group-form').append('<input type="hidden" name="parentID" value="'+ res.data.parentID +'"/>');
                    }else {
                        layer.alert(res.message, {icon: 2, offset: '70px'});
                    }
                }else{
                    $('#group-form').find('.department_father select').html(res.data);
                }
            }
        });

    },

    del:function(){
       var departmentID =  $.trim($('#department-form').find('[type="hidden"]').val());
        //  没有隐藏域
        if(!departmentID){
            return ;
        }
        layer.confirm('您确定删除该部门及该部门下所有部门吗？',
            {  icon: 3 ,
                offset: '100px'
            }, function (index) {

                layer.close(index);

                E.ajax({
                    type: 'get',
                    url: '/eoa/department/delete/' + departmentID,
                    success: function (res) {
                        if (res.code == 200) {
                            layer.alert('部门删除成功', {icon: 1, offset: '70px', time: 1000});
                            $('#main_department').html(res.data);
                            $('#department-form').find('[type="hidden"]').remove();
                            $('#table').bootstrapTable('refresh');
                        } else {
                            layer.msg(res.message, {icon: 2, offset: '70px'});
                        }
                    }
                });

            });

    }



}