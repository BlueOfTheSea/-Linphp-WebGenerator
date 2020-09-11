<?php

namespace Linphp\Generator\notice;

/**
 * Class MSG
 * @package Linphp\ServiceController\command
 */
class Msg
{
    public static function JSON($code = '', $msg = '', $data = '',$count='')
    {

        $msg_data['code'] = $code;
        $msg_data['msg']  = $msg;
        $msg_data['data'] = $data;
        $msg_data['count'] = $count;

        return json($msg_data);
    }
}