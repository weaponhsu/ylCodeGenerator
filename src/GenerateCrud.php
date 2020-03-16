<?php


namespace ylCodeGenerator;


use Exception;

class GenerateCrud
{
    private $__table_name = null;
    private $__file_arr = null;

    protected $_filter_parameter_arr = null;

    protected $_allowed_file = [
        'model' => BASEPATH . 'application/models/DAO/#table_name#Model.php',
        'service' => BASEPATH.'application/models/Service/#table_name#Service.php',
        'transformer' => BASEPATH . 'application/models/Transformer/#table_name#Transformer.php',
    ];

    /**
     * @var array
     * 允许生成的代码类型
     */
    protected $_code_arr = [
        'Model',
        'Service',
        'Transformer'
    ];


    public function __construct($table_name = '', $filter_parameter_arr = [], $action_name = 'crud'){
        $this->__table_name = !empty($table_name) ? strtolower(trim($table_name)) : '';
        $this->_filter_parameter_arr = !empty($filter_parameter_arr) && is_array($filter_parameter_arr) ? $filter_parameter_arr : [];
        if(empty($this->__table_name)) {
            throw new Exception("表名不能为空");
        }
        if($action_name !== 'crud'){
            foreach ($this->_allowed_file as $action => $file_path){
                if($action_name != $action){
                    unset($this->_allowed_file[$action]);
                }
            }
        }
    }

    public function  getFilePath(){
        $path_arr = $path_array = $write_file_arr = [];
        foreach($this->_allowed_file as $type => $path){
            if($type === 'phtml'){
                foreach($this->_action_arr as $action_name => $file_path){
//                    if($action_name != 'data'){
                    if(!in_array($action_name, ['data', 'delete', 'update', 'store'])){
                        $real_file_path = str_replace("#action_name#", $action_name, $path);
                        $path_arr[$file_path][] = str_replace("#table_name#", $this->__table_name, $real_file_path);
                    }
                }
            }else{
                $path_arr[$type][] = str_replace("#table_name#", ucfirst($this->__table_name), $path);
            }
        }

        foreach($path_arr as $type => $val){
            foreach($val as $real_path){
                if(strpos($real_path, '#') === false){
                    $this->__file_arr[] = $real_path;
                }
            }
        }

        return $this;
    }


    /**
     * 检测目录与文件是否存在
     */
    public function generateFile(){
        foreach($this->__file_arr as $dir){
            $real_dir = substr($dir, 0, strrpos($dir, '/'));
            if(strpos($real_dir, 'views') != false && strpos($real_dir, '_') !== false){
                $real_last_dir_array = [];
                $real_last_dir = substr($real_dir, strrpos($real_dir, '/') + 1);
                $real_last_dir_arr = explode('_', $real_last_dir);
                foreach($real_last_dir_arr as $k => $v){
                    $real_last_dir_array[] = $k == 0 ? $v : ucfirst($v);
                }
                $real_dir = str_replace($real_last_dir, implode('', $real_last_dir_array), $real_dir);
            }
            //当前文件所在目录已经存在，检测文件是否存在，如果存在，备份原文件，创建新文件。
            //当前文件所在目录不存在，创建目录，再创建文件
            if(!is_dir($real_dir)){
                mkdir($real_dir, 0777);
            }
            $this->__chkFile($dir);
        }
    }

    /**
     * 传入文件绝对路径，检测文件是否存在，存在则移动并创建一个新的，反之创建新文件
     * @param string $file_path
     * @return bool
     * @throws Exception
     */
    private function __chkFile($file_path = ''){
        if (empty($file_path))
            throw new Exception("文件名不能为空");

        $fh = null;
        $real_file_path = '';
        //如果生成控制器，且控制器文件名中包含下划线，则切换为驼峰
        if(strpos($file_path, '_') !== false){
            $file_name_arr = $file_name_array = [];
            if(strpos($file_path, 'Controllers') !== false){
                $file_name_arr = explode('_', $file_path);
            } else if(strpos($file_path, 'views') !== false){
                $file_name_arr = explode('_', $file_path);
            }
            foreach($file_name_arr as $k => $v){
                $file_name_array[] = strpos($file_path, 'Controllers') !== false ? ucfirst($v) : ($k == 0 ? $v : ucfirst($v));

            }
            $real_file_path = implode('', $file_name_array);
        } else
            $real_file_path = $file_path;

        if(!empty($real_file_path)){
            if(file_exists($real_file_path)){
                $copy_file_path = 'code_backup' . substr($real_file_path, stripos($real_file_path, '/')) . '_' . date("Y-m-d", time()) . '.php';
                $this->__chkDir($copy_file_path);
//                    @rename($real_file_path, substr($real_file_path, 0, strrpos($real_file_path, '.')) . '_' . date("Y-m-d",  time()) . '.php');
                @rename($real_file_path, substr($copy_file_path, 0, strrpos($copy_file_path, '.')));
                $fh = fopen($real_file_path, 'wr');
            } else {
                $this->__chkDir($file_path);
                $fh = fopen($file_path, 'x');
            }
        }else{
            $this->__chkDir($file_path);
            $fh = fopen($file_path, 'x');
        }
        if($fh){
            $code = $this->__generateCode($file_path);
            fwrite($fh, $code);
            fclose($fh);
        }
        $code = $this->__generateCode($file_path);
//            echo $code;exit;
        return true;
    }

    private function __chkDir($file_path = ''){
        $file_path = !empty($file_path) && is_string($file_path) && strpos($file_path, '/') !== false ? trim($file_path) : '';
        if(!empty($file_path)){
            $file_array = explode('/', $file_path);
            $real_file_dir = '';
            foreach ($file_array as $index => $value){
                if($index + 1 < count($file_array)){
                    $real_file_dir .= $value . '/';
                    if(!is_dir($real_file_dir)){
                        mkdir($real_file_dir, 0777);
//                        chown($real_file_dir, 'momo');
                    }
                }
            }
        }

        return true;
    }


    /**
     * 生成代码
     * @param string $file_path 要生成代码的文件的绝对路径
     * @return string|string[]
     * @throws Exception
     */
    private function __generateCode($file_path = ''){
        if (empty($file_path))
            throw new Exception("生成文件路径不能为空");

        $model = new GenerateModel();
        $service = new GenerateService();
        $transformer = new GenerateTransformer();

        $code = '';
        foreach($this->_code_arr as $type){
            $file_path_arr = explode('/', substr($file_path, 0, strrpos($file_path, '.')));
            $file_name = $file_path_arr[count($file_path_arr) - 1];
            if (strpos($file_name, $type) !== false) {
                switch ($type) {
                    case 'Service':
                        $code = $service->generatedServiceCode($file_path);
                        break;
                    case 'Transformer':
                        $code = $transformer->generatedTransformerCode($file_path);
                        break;
                    default:
                        $code = $model->generatedModelCode($file_path);
                        break;
                }
            }
        }

        if(strpos($file_path, 'views') !== false){
            return $code;
        }else{
            return str_replace("{*}", "#", str_replace("#", "\r\n", $code));
        }
    }

}
