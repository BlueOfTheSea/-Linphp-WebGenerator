<style>
    .layui-upload-img{width: 600px; height: 300px; margin: 0 10px 10px 0;}
</style>
<script>
    //上传配置
    layui.config({
        base: '/static/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index','upload','form'], function () {
        var $ = layui.$
            , upload = layui.upload;
        var loading = '';
        //普通图片上传
        var uploadInst = upload.render({
            elem: '.ueditor'
            , url: '/admin/upload/index'
            , before: function (obj) {
                loading = layer.msg('正在上传', {icon: 16, shade: 0.3, time: 0});
                var item = this.item;
                //预读本地文件示例，不支持ie8
                obj.preview(function (index, file, result) {
                    item.nextAll('div').find('img').attr('src',result);
                });
            }
            , done: function (res) {
                //如果上传失败
                if (res.code > 0) {
                    return layer.msg('上传失败');
                }
                layer.msg('上传成功');
                var item = this.item;
                item.nextAll('div').find('input').val(res.data.src)
                layer.close(loading);
                //上传成功
            }
            , error: function () {
                layer.msg('上传失败');
                layer.close(loading);
                //演示失败状态，并实现重传
                var demoText = $('#test-upload-demoText');
                demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-mini demo-reload">重试</a>');
                demoText.find('.demo-reload').on('click', function () {
                    uploadInst.upload();
                });
            }
        });
    })
</script>