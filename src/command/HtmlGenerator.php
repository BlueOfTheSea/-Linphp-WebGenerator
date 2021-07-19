<?php

namespace Linphp\WebGenerator\command;


use think\facade\Db;

/**
 * Class HtmlGenerator
 * @package Linphp\Generator\command
 */
class HtmlGenerator
{
    private $htmlDir;
    private $table;
    private $controller;
    private $modular;


    /**
     * 首页数据渲染
     * @author Administrator
     */
    public function index()
    {
        $HtmlFile = $this->htmlDir . "index.tpl";
        $file = @file_get_contents($HtmlFile);
        $cols = '';
        foreach ($this->table as $v) {

            if (strstr($v['column_comment'], '图片')) {
                $cols .= "{field: '" . $v['column_name'] . "',align: 'center', title: '" . $v['column_comment'] . "',templet:function (val){return '<img src='+val.img+'>'}},\n";
            } else if (strstr($v['column_comment'], '富文本')) {

            } else {
                $cols .= "{field: '" . $v['column_name'] . "',align: 'center', title: '" . $v['column_comment'] . "'},\n";
            }

        }
        $html = preg_replace("/@table/", $cols, $file);
        $dir = app_path() . $this->modular . '/view/' . $this->cc_format($this->controller) . '/index.html';
        $this->fileSave($dir, $html);
    }

    /**
     * 修改页面渲染
     * @author Administrator
     */
    public function update()
    {
        $HtmlFile = $this->htmlDir . "update.tpl";
        $file = @file_get_contents($HtmlFile);


        $cols = '';
        $column_comment_str = [];
        foreach ($this->table as $v) {

            array_push($column_comment_str, $v['column_comment']);
            if ($v['column_name'] == 'id') {
                $cols .= ' <input type="hidden" value="{$info.id}"  name="id" lay-verify="required" >';
            } else {

                if (strstr($v['column_comment'], '图片')) {
                    $cols .= '<div class="layui-form-item">
                              <label class="layui-form-label">' . $v['column_comment'] . '</label>
                                <div class="layui-input-block">
                                       <div class="layui-upload">
                                        <button type="button" class="layui-btn ueditor" >上传图片</button>
                                        <div class="layui-upload-list">
                                            <img class="layui-upload-img" src="{$info.' . $v['column_name'] . '}">
                                            <p></p>
                                            <input type="hidden" value="{$info.' . $v['column_name'] . '}" name="' . $v['column_name'] . '" >
                                        </div>
                                    </div>           
                                </div>
                          </div>' . "\n";
                } else if (strstr($v['column_comment'], '富文本')) {

                    $cols .= '<div class="layui-form-item">
                            <label class="layui-form-label">' . $v['column_comment'] . '</label>
                                <div class="layui-input-block">
                                <script id="editor" name="' . $v['column_name'] . '" type="text/plain" style="height:400px;">{:htmlspecialchars_decode($info.' . $v['column_name'] . ')}</script>
                               </div>
                          </div>' . "\n";
                } else {
                    $cols .= '<div class="layui-form-item">
                            <label class="layui-form-label">' . $v['column_comment'] . '</label>
                                <div class="layui-input-block">
                                    <input type="text" value="{$info.' . $v['column_name'] . '}"  name="' . $v['column_name'] . '" lay-verify="required" placeholder="请输入' . $v['column_comment'] . '" autocomplete="off" class="layui-input">
                                </div>
                          </div>' . "\n";
                }
            }
        }


        //图片展示
        $img_upload = @file_get_contents($this->htmlDir . "/configure/ordinary.tpl");
        foreach ($column_comment_str as $v_comment) {
            if (strstr($v_comment, '图片')) {
                $img_upload  = @file_get_contents($this->htmlDir . "/configure/upload.tpl");
                break;
            }

        }
        $ueditor = '';
        foreach ($column_comment_str as $v_comment) {
            if (strstr($v_comment, '富文本')) {
                $ueditor =  @file_get_contents($this->htmlDir . "/configure/ueditor.tpl");
                break;
            }
        }
        $string = $file;
        $patterns = array();
        $patterns[0] = '/@form/';
        $patterns[1] = '/@ueditor/';
        $patterns[2] = '/@upload/';
        $replacements = array();
        $replacements[0] = $cols;
        $replacements[1] = $ueditor;
        $replacements[2] = $img_upload;
        $html = preg_replace($patterns, $replacements, $string);
        $dir = app_path() . $this->modular . '/view/' . $this->cc_format($this->controller) . '/update.html';
        $this->fileSave($dir, $html);

    }

    public function save()
    {
        $HtmlFile = $this->htmlDir . "save.tpl";
        $file = @file_get_contents($HtmlFile);
        $cols = '';
        $column_comment_str = [];
        foreach ($this->table as $v) {
            if ($v['column_name'] != 'id') {
                array_push($column_comment_str, $v['column_comment']);
                if (strstr($v['column_comment'], '图片')) {
                    $cols .= '<div class="layui-form-item">
                              <label class="layui-form-label">' . $v['column_comment'] . '</label>
                                <div class="layui-input-block">
                                       <div class="layui-upload">
                                        <button type="button" class="layui-btn ueditor" >上传图片</button>
                                        <div class="layui-upload-list">
                                            <img class="layui-upload-img">
                                            <p></p>
                                            <input type="hidden" name="' . $v['column_name'] . '" >
                                        </div>
                                    </div>           
                                </div>
                          </div>' . "\n";
                } else if (strstr($v['column_comment'], '富文本')) {

                    $cols .= '<div class="layui-form-item">
                            <label class="layui-form-label">' . $v['column_comment'] . '</label>
                                <div class="layui-input-block">
                                <script id="editor" name="' . $v['column_name'] . '" type="text/plain" style="height:400px;"></script>
                               </div>
                          </div>' . "\n";
                } else {
                    $cols .= '<div class="layui-form-item">
                            <label class="layui-form-label">' . $v['column_comment'] . '</label>
                                <div class="layui-input-block">
                                    <input type="text" value="" name="' . $v['column_name'] . '" lay-verify="required" placeholder="请输入' . $v['column_comment'] . '" autocomplete="off" class="layui-input">
                                </div>
                          </div>' . "\n";
                }

            }
        }

        //图片展示
        $img_upload =  @file_get_contents($this->htmlDir . "/configure/ordinary.tpl");
        foreach ($column_comment_str as $v_comment) {
            if (strstr($v_comment, '图片')) {
                $img_upload = @file_get_contents($this->htmlDir . "/configure/upload.tpl");
                break;
            }

        }
        $ueditor = '';
        foreach ($column_comment_str as $v_comment) {
            if (strstr($v_comment, '富文本')) {
                $ueditor =  @file_get_contents($this->htmlDir . "/configure/ueditor.tpl");
                break;
            }
        }
        $string = $file;
        $patterns = array();
        $patterns[0] = '/@form/';
        $patterns[1] = '/@ueditor/';
        $patterns[2] = '/@upload/';
        $replacements = array();
        $replacements[0] = $cols;
        $replacements[1] = $ueditor;
        $replacements[2] = $img_upload;
        $html = preg_replace($patterns, $replacements, $string);
        $dir = app_path() . $this->modular . '/view/' . $this->cc_format($this->controller) . '/save.html';
        $this->fileSave($dir, $html);

    }
    
    public function cc_format($name)
    {
        $temp_array = array();
        for ($i = 0; $i < strlen($name); $i++) {
            $ascii_code = ord($name[$i]);
            if ($ascii_code >= 65 && $ascii_code <= 90) {
                if ($i == 0) {
                    $temp_array[] = chr($ascii_code + 32);
                } else {
                    $temp_array[] = '_' . chr($ascii_code + 32);
                }
            } else {
                $temp_array[] = $name[$i];
            }
        }
        return implode('', $temp_array);
    }
    
    /**
     * 启动
     * @author Administrator
     */
    public function command($modular = '', $controller = '')
    {

        $this->controller = $controller;

        $this->modular = $modular;
        $dir = app_path() . $modular . '/view/'.$this->cc_format($controller);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $this->htmlDir = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR
            . "html" . DIRECTORY_SEPARATOR;
        $mysql         = config('database.connections.mysql');

        $class_name = $this->controller;
        $cc_format  = $mysql['prefix'] . lcfirst((new ModelGenerator())->cc_format($class_name));
        $column     = "select column_name,column_comment from information_schema.columns where table_name = '" . $cc_format . "'AND table_schema= '" . $mysql['database'] . "'";

        $table       = Db::query($column);
        $this->table = $table;
        $this->index();
        $this->update();
        $this->save();
    }


    public function fileSave($dir, $html)
    {
        if (!file_exists($dir)) {
            echo '生成模块HTML层   ' . $dir . "\n";
            @file_put_contents($dir, $html);
        }
    }

}
