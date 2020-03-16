<?php


namespace ylCodeGenerator;


class GenerateController extends GenerateCode
{

    public $table_name = '';
    private $__table_construct = [];
    private $__field_arr = [];

    protected $_code_arr = [
        'start' => '<?php',
        'namespace' => '',
        'use' => [
            'Yaf\\Session',
            'Yaf\\Registry'
        ],
        'class' => 'class #class_name# extends ApiBaseController{',
        'private_variable' => [],
        'construct' => [
            'function' => ''
        ],
        'public_variable' => [
        ],
        'protected_variable' => [
        ],
        'public_function' => [
            'createAction' => [
                'function' => ''
            ],
            'listAction' => [
                'function' => ''
            ],
            'editAction' => [
                'function' => ''
            ],
            'deleteAction' => [
                'function' => ''
            ]
        ],
        'protected_function' => [],
        'private_function' => []
    ];

    public function generatedControllersCode($file_path = ''){

        $code = '';
        $column_variable_type = 'private';
        $this->table_name = ucfirst(substr($file_path, strrpos($file_path, '/')+1, (strrpos($file_path, 'Controller') - strrpos($file_path, '/') - 1)));
        if(!empty($this->_code_arr) && !empty($file_path)){
            foreach($this->_code_arr as $key => $element){
                switch ($key){
                    case 'start':
                        $code .= $this->genStartCode($element);
                        break;
                    case 'namespace':
                        $code .= $this->genNamespaceCode($element);
                        break;
                    case 'use':
                        foreach($element as $id => $use_namespace){
                            if(strpos($use_namespace, '#table_name#') !== false){
                                $this->__table_construct = $this->_getTableConstruct($this->table_name);
                                foreach ($this->__table_construct as $column) {
                                    $this->__field_arr[] = $column['Field'];
                                }
                                $element[$id] = str_replace('#table_name#', $this->table_name, $use_namespace);
                                if(strpos($use_namespace, '#table_prefix#') !== false && $this->table_name){
                                    $element[$id] = str_replace("#table_prefix#", strtoupper(substr($this->table_name, 0, 1)), $element[$id]);
                                }
                            }
                        }
                        if(in_array('album_id',$this->__field_arr)){
                            $element[] = 'models\Service\Business\AlbumTrait';
                        }
                        $code .= $this->genUseCode($element);
                        break;
                    case 'class':
                        $code .= $this->genClass($element, $file_path, true);
                        break;
                    case 'public_function':
                    case 'protected_function':
                    case 'private_function':
                        foreach($element as $function_name => $parameter_function_arr){
                            $parameter_arr = [];
                            $function = $annotation = '';
                            if (isset($parameter_function_arr['function'])) {
                                $function = $parameter_function_arr['function'];
                            }

                            if (isset($parameter_function_arr['annotation'])) {
                                $annotation = $parameter_arr['annotation'];
                            }

                            if (empty($annotation)) {
                                $annotation = $this->genSwgAnnotation4AdminController($this->table_name, $function_name);
                            }

                            //生成函数主体
                            $function_main = '';
                            if(empty($function)){
                                $method_name = 'gen' . ucfirst($function_name) . 'FunctionMain';
                                if (method_exists($this, $method_name)) {
                                    $function_main = $this->$method_name($file_path, $column_variable_type);
                                }
                            }
                            //合并备注，当函数存在参数且存在函数备注时，将两个备注合并起来
                            //当函数只有参数备注时，只显示参数备注
                            //当函数只有函数备注时，只显示函数备注
                            $code .= !empty($annotation) ?
                                (
                                !empty($parameter_arr) ?
                                    str_replace('/**', str_replace(["*/\r\n", "    /**"], ['*', '/**'], $annotation), $this->genFunction(substr($key, 0, stripos($key, '_')), $function_name, $parameter_arr, $function_main)) :
                                    $annotation . $this->genFunction(substr($key, 0, stripos($key, '_')), $function_name, $parameter_arr, $function_main)) :
                                $this->genFunction(substr($key, 0, stripos($key, '_')), $function_name, $parameter_arr, $function_main);
                        }
                        break;
                }
            }
        }
        return $code . '}';
//        return str_replace("#", "\r\n", $code) . '}';
    }

    /**
     * 生成controller的createAction
     * @param string $file_path controller所在路径，用以获取表名，生成与之对应的form的变量名
     * @param string $type 与表名对应的变量名的属性，可选值public protected private
     * @return mixed
     */
    public function genCreateActionFunctionMain($file_path = '', $type = 'public'){
        $code = '';
        if(!empty($file_path) && in_array($type, ['public', 'protected', 'private'])){
            $code .= "    \$parameters = Registry::get('parameters');";
            $code .= "\r\n        return \$this->_responseJson(['data' => \$parameters]);";
        }
        return str_replace('%', '$', $code);
    }

    /**
     * 生成controller的createAction
     * @param string $file_path controller所在路径，用以获取表名，生成与之对应的form的变量名
     * @param string $type 与表名对应的变量名的属性，可选值public protected private
     * @return mixed
     */
    public function genDeleteActionFunctionMain($file_path = '', $type = 'public'){
        $code = '';
        if(!empty($file_path) && in_array($type, ['public', 'protected', 'private'])){
            $code .= "    \$parameters = Registry::get('parameters');";
            $code .= "\r\n        return \$this->_responseJson(['data' => \$parameters]);";
        }
        return str_replace('%', '$', $code);
    }
    /**
     * 生成controller的createAction
     * @param string $file_path controller所在路径，用以获取表名，生成与之对应的form的变量名
     * @param string $type 与表名对应的变量名的属性，可选值public protected private
     * @return mixed
     */
    public function genEditActionFunctionMain($file_path = '', $type = 'public'){
        $code = '';
        if(!empty($file_path) && in_array($type, ['public', 'protected', 'private'])){
            $code .= "    \$parameters = Registry::get('parameters');";
            $code .= "\r\n        return \$this->_responseJson(['data' => \$parameters]);";
        }
        return str_replace('%', '$', $code);
    }
    /**
     * 生成controller的createAction
     * @param string $file_path controller所在路径，用以获取表名，生成与之对应的form的变量名
     * @param string $type 与表名对应的变量名的属性，可选值public protected private
     * @return mixed
     */
    public function genListActionFunctionMain($file_path = '', $type = 'public'){
        $code = '';
        if(!empty($file_path) && in_array($type, ['public', 'protected', 'private'])){
            $code .= "    \$parameters = Registry::get('parameters');";
            $code .= "\r\n        return \$this->_responseJson(['data' => \$parameters]);";
        }
        return str_replace('%', '$', $code);
    }
}
