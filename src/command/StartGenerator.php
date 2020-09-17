<?php

namespace Linphp\Generator\command;

use Linphp\Generator\Msgee;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\Db;

class StartGenerator extends Command
{
    /**
     * @author Administrator
     */
    protected function configure()
    {
        $this->setName('webGen')
            ->addArgument('name', Argument::OPTIONAL)
            ->setDescription('Auto generate file command');
    }

    /**
     * @param Input $input
     * @param Output $output
     * @return int|void|null
     * @author Administrator
     */
    protected function execute(Input $input, Output $output)
    {
        $this->start($input->getArgument('name'));
        echo '格式化文件代码中...温馨提示php think gen 控制器名@类文件名 或 控制器名 如果文件存在是不会覆盖的亲~。';
        exec('composer fix-style');
        echo 'SUCCESS';
    }

    /**
     * @param $name
     * @author Administrator
     */
    public function start($name)
    {
        if (!$name) {
            $this->deldir(app_path().'model\\entity');
            (new EntityModelGenerator())->command();
            return;
        }

        $ServiceGenerator    = new ServiceGenerator();
        $ModelGenerator      = new ModelGenerator();
        $ControllerGenerator = new ControllerGenerator();
        $HtmlGenerator       = new HtmlGenerator();
        $class_name          = explode('@', $name);

        #初始化实体模型
        (new EntityModelGenerator())->command();
        if (count($class_name) > 1) {
            #生成模型
            $ModelGenerator->command($class_name[0], $class_name[1]);
            #生成Service
            $ServiceGenerator->command($class_name[0], $class_name[1]);
            #生成控制器
            $ControllerGenerator->command($class_name[0], $class_name[1]);
        } else {
            $result = Db::query('show tables');
            foreach ($result as $k => $v) {
                $tableName    = $v['Tables_in_' . config('database.connections.mysql.database')];
                $prefix       = config('database.connections.mysql.prefix');
                $prefixSum    = strlen($prefix); #字符串长度,准备裁剪数据表结构前缀
                $str          = substr($tableName, $prefixSum);
                $str_array    = explode('_', $str);
                $tableNameVal = '';
                for ($a = 0; $a < count($str_array); $a++) {
                    $tableNameVal .= ucfirst($str_array[$a]);
                }
                #生成模型
                $ModelGenerator->command($class_name[0], $tableNameVal, $tableName);
                #生成Service
                $ServiceGenerator->command($class_name[0], $tableNameVal, $tableName);
                #生成控制器
                $ControllerGenerator->command($class_name[0], $tableNameVal, $tableName);
                #生成页面

                $HtmlGenerator->command($class_name[0], $tableNameVal);
            }
        }
    }

    /**
     * 删除model/entity实体模型
     * @author Administrator
     * @param $dir
     * @return bool
     */
    public function deldir($dir) {
        //先删除目录下的文件：
        $dh=opendir($dir);
        while ($file=readdir($dh)) {
            if($file!="." && $file!="..") {
                $fullpath=$dir."/".$file;
                if(!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    deldir($fullpath);
                }
            }
        }

        closedir($dh);
        //删除当前文件夹：
        if(rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }
}