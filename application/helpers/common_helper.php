<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('response_success'))
{
    function response_success($data,$status,$error)
    {
        return array('data'=>$data,'status'=>$status,'error'=>$error);
    }   
}

if ( ! function_exists('response_fail'))
{
    function response_fail($status,$error)
    {
        return array('status'=>$status,'error'=>$error);
    }   
}


if ( ! function_exists('get_table_name'))
{
    function get_table_name($tablename)
    {
        return 'stk_'.$tablename;
    }   
}