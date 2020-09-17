Lin PHP WebGenerator  
===================



Installation
------------
```
composer require linphp/webgenerator
```


webgenerator   --thinkphp v6.0.3 插件
-----

```php 
    view层管理系统是layuiadmin后台管理系统
    每次执行命令除了app/model会初始化外,别的文件夹不用担心会初始化,已有文件不会初始化。
    如果在linux上操作权限一定要给足,如果无法创建文件请检查用户权限
    遇到问题,如有宝贵的建议邮箱联系我哦。
    email： ymdhis@163.com
    
命令1.  php think webGen index  #根据数据表生成所有controller,service,model,view的文件。
命令2.  php think webGen index@user #生成 index模块下的 user控制器文件 userService文件,model文件，view文件
(如果指定的模块跟数据表对应会生成view对应的一套增删改查页面)
命令3.  php think webGen  #如果有修改数据表操作请执行此命令,重构app/model映射文件夹,保证映射表关系。
提示:
    1.执行命令前请配置好数据库,保证能正确连接。
    2.生成文件说明
        |--app
            |--index
                 |--controller #自动生成的控制器文件
                 |--model #自动生成的模型,sql语句可以在这里写。
                 |--service #自动生成的逻辑层代码,
                 |--view #自动生成的页面模块简单的增删改查,
            |--model  #此文件夹禁止修改文件
                 |--entity #自动生成的映射模型,此文件夹下文件是对应所有数据表中的映射模型,禁止写入代码,每次使用php think gen index或index@user会重构结构表   



```


