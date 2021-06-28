<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/static/layuiadmin/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/layuiadmin/style/admin.css" media="all">
</head>
<body>

<div class="layui-fluid">
    <div class="layui-card">
        <!-- <div class="layui-form layui-card-header layuiadmin-card-header-auto">

                <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">文章ID</label>
                    <div class="layui-input-inline">
                        <input type="text" name="id" placeholder="请输入" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">文章标签</label>
                    <div class="layui-input-inline">
                        <select name="label">
                            <option value="">请选择标签</option>
                            <option value="0">美食</option>

                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn layuiadmin-btn-list" lay-submit lay-filter="LAY-app-contlist-search">
                        <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                    </button>
                </div>
            </div>




         </div>
 -->
        <div class="layui-card-body">
            <div style="padding-bottom: 10px;">
                <button class="layui-btn layuiadmin-btn-list" data-type="add">添加</button>
            </div>
            <table id="LAY-app-content-list" lay-filter="LAY-app-content-list"></table>

            <script type="text/html" id="table-content-list">
                <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit"><i
                            class="layui-icon layui-icon-edit"></i>编辑</a>
                <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i
                            class="layui-icon layui-icon-delete"></i>删除</a>
            </script>
        </div>
    </div>
</div>

<script src="/static/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/static/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'contlist', 'table'], function () {
        var table = layui.table
            , form = layui.form;

        //监听搜索
        form.on('submit(LAY-app-contlist-search)', function (data) {
            var field = data.field;

            //执行重载
            table.reload('LAY-app-content-list', {
                where: field
            });
        });


        table.render({
            elem: '#LAY-app-content-list'
            , url: 'index'
            , method: 'post'
            ,page: true //开启分页
            , cellMinWidth: 80 //全局定义常规单元格的最小宽度，layui 2.2.1 新增
            , cols: [[
            @table
                {width: 178, title: '操作', align: 'center', fixed: 'right', toolbar: '#table-content-list'}
            ]]
        });
        table.on('tool(LAY-app-content-list)', function (obj) { //注：tool 是工具条事件名，test 是 table 原始容器的属性 lay-filter="对应的值"
            var data = obj.data; //获得当前行数据
            var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）

            if (layEvent === 'del') { //删除
                layer.confirm('真的删除行么', function (index) {

                    $.ajax({
                        type: 'POST',
                        url: 'delete',
                        data: data,
                        success: function (i) {
                            if (i.code == 200) {
                                obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                                layer.close(index);
                                layer.msg('删除成功！', {
                                    offset: '15px'
                                    ,icon: 1
                                });
                            }else{
                                layer.msg('删除失败！', {
                                    offset: '15px'
                                    ,icon: 2
                                });
                            }

                        }
                    })


                    //向服务端发送删除指令
                });
            } else if (layEvent === 'edit') { //编辑
                layer.open({
                    type: 2
                    ,title: '修改'
                    ,content: 'update/id/'+data.id
                    ,maxmin: true
                    ,area: ['1000px', '800px']
                    ,btn: ['确定', '取消']
                    ,yes: function(index, layero){
                        var iframeWindow = window['layui-layer-iframe'+ index]
                            ,submitID = 'LAY-user-front-submit'
                            ,submit = layero.find('iframe').contents().find('#'+ submitID);

                        //监听提交
                        iframeWindow.layui.form.on('submit('+ submitID +')', function(data){
                            var field = data.field; //获取提交的字段

                            //提交 Ajax 成功后，静态更新表格中的数据
                            $.ajax({
                                type: 'POST',
                                url: 'update',
                                data: field,
                                success: function (i) {
                                    if (i.code == 200) {
                                        obj.update(field);
                                        layer.msg('修改成功！', {
                                            offset: '15px'
                                            ,icon: 1
                                        });
                                        table.reload('LAY-app-content-list');
                                        layer.close(index); //关闭弹层
                                    }else{
                                        layer.msg('失败失败！', {
                                            offset: '15px'
                                            ,icon: 2
                                        });
                                    }

                                }


                        })
//同步更新缓存对应的值

                        });

                        submit.trigger('click');
                    }
                });



            }
        });


        var $ = layui.$, active = {

            add: function () {
                layer.open({
                    type: 2
                    ,title: '添加'
                    ,content: 'save'
                    ,maxmin: true
                    ,area: ['1000px', '800px']
                    ,btn: ['确定', '取消']
                    ,yes: function(index, layero){
                        var iframeWindow = window['layui-layer-iframe'+ index]
                            ,submitID = 'LAY-user-front-submit'
                            ,submit = layero.find('iframe').contents().find('#'+ submitID);

                        //监听提交
                        iframeWindow.layui.form.on('submit('+ submitID +')', function(data){
                            var field = data.field; //获取提交的字段

                            //提交 Ajax 成功后，静态更新表格中的数据
                            $.ajax({
                                type: 'POST',
                                url: 'save',
                                data: field,
                                success: function (i) {
                                    if (i.code == 200) {

                                        layer.msg('添加成功！', {
                                            offset: '15px'
                                            ,icon: 1
                                        });
                                        table.reload('LAY-app-content-list');
                                        layer.close(index); //关闭弹层
                                    }else{
                                        layer.msg('添加失败！', {
                                            offset: '15px'
                                            ,icon: 2
                                        });
                                    }

                                }


                            })
//同步更新缓存对应的值

                        });

                        submit.trigger('click');
                    }
                });
            }
        };

        $('.layui-btn.layuiadmin-btn-list').on('click', function () {
            var type = $(this).data('type');
            active[type] ? active[type].call(this) : '';
        });

    });
</script>
</body>
</html>
