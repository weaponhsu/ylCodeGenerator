<?php
/**
 * Created by GameHome.
 *
 *            ┏┓　　  ┏┓+ +
 *           ┏┛┻━━━━━┛┻┓ + +
 *           ┃         ┃ 　
 *           ┃   ━     ┃ ++ + + +
 *          ████━████  ┃+
 *           ┃　　　　　 ┃ +
 *           ┃　　　┻　　┃
 *           ┃　　　　　 ┃ + +
 *           ┗━┓　　　┏━┛
 *             ┃　　　┃　　　　
 *             ┃　　　┃ + + + +
 *             ┃　　　┃    Code is far away from bug with the alpaca protecting
 *             ┃　　　┃ + 　　　　        神兽保佑,代码无bug
 *             ┃　　　┃
 *             ┃　　　┃　　+
 *             ┃     ┗━━━┓ + +
 *             ┃         ┣┓
 *             ┃ 　　　　　┏┛
 *             ┗┓┓┏━━┳┓┏━┛ + + + +
 *              ┃┫┫  ┃┫┫
 *              ┗┻┛  ┗┻┛+ + + +
 * User: Huang Hsu
 * Date: 2016-07-07
 * Time: 下午 2:22
 */

date_default_timezone_set("PRC");

if (! file_exists(realpath(dirname(__FILE__)) . '/vendor/autoload.php'))
    throw new Exception("文件不存在");

require_once realpath(dirname(__FILE__)) . '/vendor/autoload.php';

use ylCodeGenerator\GenerateCrud;
use ylCodeGenerator\DbConfig;

define('BASEPATH', realpath(dirname(__FILE__)) . '/');

$allow_parameter_key = array('filter_column');

$allowed_operation_arr = array('generate');
$allowed_action_arr = array('crud', 'model', 'service', 'transformer', 'exception', 'controller');

if(empty($argv) || $argv[0] != $_SERVER['PHP_SELF'] || empty($argv[1])){
    exit('no parameters');
}

if(count($argv) < 3){
    exit("invalid parameters");
}

if(strpos($argv[1], ':') === false){
    exit("invalid separator");
}
$operation_arr = explode(':',  $argv[1]);
if($operation_arr[0] != 'yl'){
    exit("invalid company name");
}

if(!in_array($operation_arr[1], $allowed_operation_arr)){
    exit("invalid operation");
}

if(!in_array($operation_arr[2], $allowed_action_arr)){
    exit("invalid action");
}

$parameter_arr = [];
for($i = 3; $i < count($argv); $i++){
    $real_parameter = str_replace('--', '', $argv[$i]);
    $real_parameter_arr = explode('=', $real_parameter);
    if(in_array($real_parameter_arr[0], $allow_parameter_key)){
        $parameter_arr[$real_parameter_arr[0]] = strpos($real_parameter, ',') !== false ? explode(',', $real_parameter_arr[1]) : $real_parameter_arr[1];
//            $parameter_arr[] = [$real_parameter_arr[0] => $real_parameter_arr[1]];
    }
}

$table_name = $argv[2]/*$argv[count($argv)-1]*/;

try {

    DbConfig::getInstance("ylmall", "root", "123456");
    $generate = new GenerateCrud($table_name, $parameter_arr, $operation_arr[2]);

    //获取要生成的文件名及其路径
    $generate->getFilePath()->generateFile();

} catch (Exception $e) {
    echo $e->getMessage();
}
