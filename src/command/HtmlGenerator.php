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
        $file     = @file_get_contents($HtmlFile);


        $cols = '';
        foreach ($this->table as $v) {
            $cols .= "{field: '" . $v['column_name'] . "',align: 'center', title: '" . $v['column_comment'] . "'},\n";
        }
        $html = preg_replace("/@table/", $cols, $file);
        $dir  = app_path() . $this->modular . '\\view\\'.$this->controller.'\index.html';
        $this->fileSave($dir, $html);
    }

    /**
     * 修改页面渲染
     * @author Administrator
     */
    public function update()
    {
        $HtmlFile = $this->htmlDir . "update.tpl";
        $file     = @file_get_contents($HtmlFile);


        $cols = '';
        foreach ($this->table as $v) {
            if($v['column_name']=='id')
            {
                $cols.=' <input type="hidden" value="{$info.id}"  name="id" lay-verify="required" >';
            }else{
                $cols .= '<div class="layui-form-item">
                            <label class="layui-form-label">'.$v['column_comment'].'</label>
                                <div class="layui-input-inline">
                                    <input type="text" value="{$info.'.$v['column_name'].'}" name="'.$v['column_name'].'" lay-verify="required" placeholder="请输入'.$v['column_comment'].'" autocomplete="off" class="layui-input">
                                </div>
                          </div>'."\n";
            }
        }


        $html = preg_replace("/@form/", $cols, $file);
        $dir  = app_path() . $this->modular . '\\view\\'.$this->controller.'\update.html';
        $this->fileSave($dir, $html);
    }
    public function save()
    {
        $HtmlFile = $this->htmlDir . "save.tpl";
        $file     = @file_get_contents($HtmlFile);


        $cols = '';
        foreach ($this->table as $v) {
            if($v['column_name']!='id') {
                $cols .= '<div class="layui-form-item">
                            <label class="layui-form-label">' . $v['column_comment'] . '</label>
                                <div class="layui-input-inline">
                                    <input type="text" value="" name="' . $v['column_name'] . '" lay-verify="required" placeholder="请输入' . $v['column_comment'] . '" autocomplete="off" class="layui-input">
                                </div>
                          </div>' . "\n";
            }
        }


        $html = preg_replace("/@form/", $cols, $file);
        $dir  = app_path() . $this->modular . '\\view\\'.$this->controller.'\save.html';
        $this->fileSave($dir, $html);
    }
    /**
     * 启动
     * @author Administrator
     */
    public function command($modular = '', $controller = '')
    {

        $this->controller = $controller;

        $this->modular = $modular;
        $dir = app_path() . $modular . '\\view\\'.lcfirst($controller);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $this->htmlDir = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR
            . "html" . DIRECTORY_SEPARATOR;
        $mysql         = config('database.connections.mysql');

        $class_name = $this->controller;
        $cc_format  = $mysql['prefix'] . lcfirst((new ModelGenerator())->cc_format($class_name));
        $column     = "select column_name,column_comment from information_schema.columns where   table_name = '" . $cc_format . "' ;";

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