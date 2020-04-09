<?php


namespace ylCodeGenerator;


class GenerateModel extends GenerateCode
{

    public $obj;
    public $data;
    public $meta;
    public $page = 0;
    public $page_size = 0;
    protected $_code_arr = [
        'start' => '<?php',
        'namespace' => 'models\\DAO',
        'use' => [
            'Illuminate\Database\Eloquent\Model',
            'Illuminate\Database\Capsule\Manager as Capsule',
            'Yaf\Registry'
        ],
        'class' => 'class #class_name# extends Model{',
        'protected_variable' => [
            'table' => ['#table_name#', 'string', '']
        ],
        'public_function' => [
            'boot' => [
                'function' => '',
                'annotation' => '注册事件'
            ],
        ],
        'protected_function' => []
    ];

    /**
     * 生成函数注释
     * @param string $annotation 注释内容
     * @param string $file_path 表名
     * @return mixed|string
     */
    protected function _setFunctionAnnotation($annotation = '', $file_path = ''){
        $return = '';
        if (!empty($annotation) && !empty($file_path)) {
            $table_name = strtolower(substr($file_path, strrpos($file_path, '/') + 1, (strrpos($file_path, '.') - 1 - strrpos($file_path, '/'))));
            if (strpos($table_name, 'model') !== false) {
                $table_name = substr($table_name, 0, strpos($table_name, 'model')) . 'Model';
            }
            $return = $this->genAnnotation(str_replace(['#', 'table_name'], ['', $table_name], $annotation));
        }
        return $return;
    }

    /**
     * @param $type
     * @param array $parameters
     * @param string $file_path
     * @return mixed
     */
    public function genFindFunctionMain($type, $parameters = [], $file_path = ''){
        $real_parameters_arr = [];
        if(!empty($parameters) && is_array($parameters)){
            $real_parameters_arr = [];
            foreach ($parameters as $index => $item){
                $real_parameters_arr[] = strtolower($item[3]);
            }
        }
        return $this->genSelectSingleFunctionMain('find', implode(',', $real_parameters_arr), $file_path);

    }

    /**
     * @param string $function_name
     * @param string $parameters
     * @param string $file_path
     * @return string
     */
    public function genSelectSingleFunctionMain($function_name = '', $parameters = '', $file_path = '')
    {
        $function_name = !empty($function_name) && in_array($function_name, ["find", "findOneBy"]) ?
            $function_name : '';

        $code = '';
        if (!empty($function_name)) {
            $code = "    \$result = \$this->" . ($function_name == "find" ? "findByPrimaryKey(\$" . $parameters . ")" : "findRecordBy(\$" . $parameters . ")") . ";";
            $code .= "\r\n\r\n        \$this->setModelProperty(\$result);";
            $code .= "\r\n\r\n        return \$this;";
        }
        return $code;
    }

    /**
     * @param $type
     * @param array $parameters
     * @param string $file_path
     * @return string
     */
    public function genFindOneByFunctionMain($type, $parameters = [], $file_path = ''){
        $real_parameters_arr = [];
        if(!empty($parameters) && is_array($parameters)){
            $real_parameters_arr = [];
            foreach ($parameters as $index => $item){
                $real_parameters_arr[] = strtolower($item[3]);
            }
        }
        return $this->genSelectSingleFunctionMain('findOneBy', implode(',', $real_parameters_arr), $file_path);

    }

    /**
     * @param $type
     * @param array $parameters
     * @param string $file_path
     * @return mixed
     */
    public function genFindByFunctionMain($type, $parameters = [], $file_path = ''){
        $real_parameters_arr = [];
        if(!empty($parameters) && is_array($parameters)){
            $real_parameters_arr = [];
            foreach ($parameters as $index => $item){
                $real_parameters_arr[] = strtolower($item[3]);
            }
        }
        return $this->genSelectFunctionMain('findBy', implode(',', $real_parameters_arr), $file_path);
    }

    /**
     * @param string $function_name
     * @param string $parameters
     * @param string $file_path
     * @return string
     */
    public function genSelectFunctionMain($function_name = '', $parameters = '', $file_path = ''){
        $table_name = ucfirst(substr($file_path, strrpos($file_path, '/')+1, (strrpos($file_path, '.') - 1 - strrpos($file_path, 'Model'))));
        $object_name = strtolower($table_name) . '_obj';
        $function_name = !empty($function_name) && in_array($function_name, ["findAll", "findBy"]) ?
            $function_name : '';

        $code = $code_suffix = $code_prefix = '';
        if(!empty($function_name)){
            $code = "    \$count = \$this->getCount(\$condition);\r\n        \$total = \$count->fetchColumn();";
            $code .= "\r\n\r\n        \$this->data = [];";
            $code .= "\r\n        \$" . $object_name . " = \$this->findRecordBy(\$condition, \$this->page, \$this->page_size);";
            $code .= "\r\n\r\n        \$this->genMeta(\$total);";
            $code .= "\r\n        \$this->setModelProperty(\$" . $object_name . ", true);";
            $code .= "\r\n\r\n        return \$this;";
        }
        return $code;
    }

    /**
     * 生成插入方法代码
     * @return string
     */
    public function genInsertFunctionMain(){
        return $this->_genCrudMethod('insert');
    }

    /**
     * 生成更新方法代码
     * @return string
     */
    public function genUpdateFunctionMain(){
        return $this->_genCrudMethod('update');
    }

    /**
     * 生成删除方法代码
     * @return string
     */
    public function genDeleteFunctionMain(){
        return $this->_genCrudMethod('delete');
    }

    /**
     * 生成批量插入代码
     * @param string $type
     * @param array $parameters
     * @param string $file_path
     * @return string
     */
    public function genBatchInsertFunctionMain($type = "public", $parameters = [], $file_path = ""){
        $code = "";
        $code .= "    return \$this->batchInsertRecord();";
        return $code;
    }

    /**
     * @param string $type
     * @param array $parameters
     * @param string $file_path
     * @return string
     */
    public function genBatchDeleteFunctionMain($type = 'public', $parameters = [], $file_path = ''){
        $real_parameters_arr = [];
        if(!empty($parameters) && is_array($parameters)){
            $real_parameters_arr = [];
            foreach ($parameters as $index => $item){
                $real_parameters_arr[] = strtolower($item[3]);
            }
        }
        return $this->_genCrudMethod('batchDelete');
    }

    /**
     * @param string $function_name
     * @param string $parameters
     * @param string $file_path
     * @return string
     */
    public function genGenBatchUpdateSqlFunctionMain($function_name = '', $parameters = '', $file_path = ''){
        $code = '';
        if(! empty($file_path)){
//            $table_name = ucfirst(str_replace('Model.php', '', substr($file_path, strrpos($file_path, '/')+1)));
            $code .= "    return \$this->_genBatchUpdateSql();";
        }
        return $code;
    }

    /**
     * 生成新增，删除，更新代码
     * @param string $method
     * @return string
     */
    protected function _genCrudMethod($method = ''){
        $method = !empty($method) && in_array($method, ['insert', 'update', 'delete', 'batchDelete']) ?
            $method : '';
        $code = '';
        if(!empty($method)){
            if(strpos($method, 'batch') !== false){
                if (strpos($method, 'Delete') !== false) {
                    $code = "    \$this->batch" .
                        ucfirst(strpos($method, 'batch') == 0 ?
                            strtolower(substr($method, strpos($method, 'batch') + 5)) :
                            strtolower(substr($method, 0, (strlen($method) - strpos($method, 'batch'))))
                        ) . "Record();";
                } else {
                    $code = "    return \$this->batch" .
                        ucfirst(strpos($method, 'batch') == 0 ?
                            strtolower(substr($method, strpos($method, 'batch') + 5)) :
                            strtolower(substr($method, 0, (strlen($method) - strpos($method, 'batch'))))
                        ) . "Record();";
                }
            }else{
                $code = "    return \$this->" . strtolower($method) . "Record();";
            }
        }
        return $code;
    }

    protected function genModelTable($file_path) {
        $model_name = strtolower(substr($file_path, strrpos($file_path, '/') + 1, (strrpos($file_path, '.') - 1 - strrpos($file_path, '/'))));
        $table_name = str_replace('model', '', $model_name);
        return "\r\n    protected \$table = '" . $table_name . "';\r\n";
    }

    protected function genBootFunctionMain($type = 'public', $parameters = [], $file_path = '') {
        $code = "    parent::boot();";
        $code .= "\r\n\r\n        static::created(function (\$model) {";
        $code .= "\r\n            // 日志记录";
        $code .= "\r\n            Registry::get('db_log')->info(get_class(\$model) . ' - created - ' . json_encode(Capsule::connection()->getQueryLog()[0]));";
        $code .= "\r\n        });";
        $code .= "\r\n\r\n        static::updated(function (\$model) {";
        $code .= "\r\n            // 日志记录";
        $code .= "\r\n            Registry::get('db_log')->info(get_class(\$model) . ' - updated - ' . json_encode(Capsule::connection()->getQueryLog()[0]));";
        $code .= "\r\n        });";
        $code .= "\r\n\r\n        static::deleted(function (\$model) {";
        $code .= "\r\n            // 日志记录";
        $code .= "\r\n            Registry::get('db_log')->info(get_class(\$model) . ' - deleted - ' . json_encode(Capsule::connection()->getQueryLog()[0]));";
        $code .= "\r\n        });";

        return $code;
    }


    public function generatedModelCode($file_path = '') {
        $code = '';
        $column_variable_type = 'private';
        if(!empty($this->_code_arr) && !empty($file_path)){
            foreach($this->_code_arr as $key => $element){
                $table_name = substr($file_path, strrpos($file_path, '/')+1, (strrpos($file_path, 'Model.php') - 1 - strrpos($file_path, '/')));
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
                                $table_name = ucfirst(substr($file_path, strrpos($file_path, '/')+1, (strrpos($file_path, '.') - 1 - strrpos($file_path, '/'))));
                                $table_name = str_replace('Model', '', $table_name);
                                $element[$id] = str_replace('#table_name#', $table_name, $use_namespace);
                            }
                        }
                        $code .= $this->genUseCode($element);
                        break;
                    case 'class':
                        $code .= $this->genSwgAnnotation(strtolower($table_name));
                        $code .= $this->genClass($element, $file_path);
                        break;
                    case 'public_variable':
                    case 'protected_variable':
                    case 'private_variable':
                        foreach($element as $variable_name => $element_arr){
                            if ($variable_name == 'table') {
                                $code .= $this->genModelTable($file_path);
                            }

                            if($variable_name == 'instance'){
                                //getInstance方法
                                $function_name = 'get' . ucfirst($variable_name);
                                $model_name = strtolower(substr($file_path, strrpos($file_path, '/')+1, (strrpos($file_path, '.') - 1 - strrpos($file_path, '/'))));
                                $function_main = '    if(is_null(self::$instance)){#        self::$instance = new self();'.
                                    '#    }#    return self::$instance;';
//                                $function_main = '#    if(is_null(self::$instance)){#        self::$instance = new self();#    }##    return self::$instance;';
                                $code .= $this->genFunction(
                                    substr($key, 0, stripos($key, '_')),
                                    $function_name,
                                    [[$variable_name => $element_arr]],
                                    $function_main,
                                    '    /**#     * @return ' . $table_name . 'Model|null#     * @throws ModelSqlException#     */#'
                                );

                                // __construct方法
                                $function_name = '__construct';
                                $function_main = "    parent::__construct(str_replace(\"Model\", \"\", substr(get_class(\$this), strrpos(get_class(\$this), \"\\\\\")+1)));\r\n        \$this->meta = new \\stdClass();";
                                $code .= $this->genFunction(
                                    substr($key, 0, stripos($key, '_')),
                                    $function_name,
                                    [],
                                    $function_main,
                                    "    /**\r\n     * " . $table_name . "Model constructor.\r\n     * @throws ModelSqlException\r\n     */\r\n"
                                );

                                //clone方法
                                $function_name = '__clone';
                                $function_main = '    throw new ModelException(#        str_replace(\'%s\', get_class($this), ModelException::INSTANCE_NOT_ALLOW_TO_CLONE), ModelException::INSTANCE_NOT_ALLOW_TO_CLONE_NO);';
                                $code .= $this->genFunction('public', $function_name, [], $function_main, '    /**#     * @throws ModelException#     */#');

                                //__destruct方法
                                $function_name = '__destruct';
                                $function_main = '    self::$instance = null;';
                                $code .= $this->genFunction('public', $function_name, [], $function_main);
                            }
                        }
                        break;
                    case 'construct':
                        $code .= $this->genConstruct($element, '', '    /**#     * ' . $table_name . 'Model|null constructor.#     * @throws ModelSqlException#     */');
                        break;
                    case 'public_function':
                    case 'protected_function':
                    case 'private_function':
                        foreach($element as $function_name => $parameter_function_arr){
                            $parameter_arr = [];
                            $function = $annotation = '';
                            if (isset($parameter_function_arr['function']) && !empty($parameter_function_arr['function'])) {
                                $function_name = $parameter_function_arr['function'];
                            }

                            if (isset($parameter_function_arr['annotation'])) {
                                $annotation = $this->_setFunctionAnnotation($parameter_function_arr['annotation'], $file_path);
                            }

                            if (isset($parameter_function_arr['arr'])) {
                                $parameter_arr = $parameter_function_arr['arr'];
                            }

                            $method_name = 'gen' . (stripos($function_name, '__') !== false ?
                                    ucfirst(substr($function_name, stripos($function_name, '__') + 2)) :
                                    ucfirst($function_name)) . 'FunctionMain';
                            $function_main = $this->$method_name(substr($key, 0, stripos($key, '_')), $parameter_arr, $file_path);

                            //合并备注，当函数存在参数且存在函数备注时，将两个备注合并起来
                            //当函数只有参数备注时，只显示参数备注
                            //当函数只有函数备注时，只显示函数备注
                            $function_code = '';
                            if (!empty($annotation)) {
                                if (!empty($parameter_arr)) {
                                    $function_code .= str_replace(
                                        '/**',
                                        str_replace(
                                            ["*/\r\n", "    /**"],
                                            ['*', '/**'],
                                            $annotation
                                        ),
                                        $this->genFunction(
                                            substr($key, 0, stripos($key, '_')),
                                            $function_name,
                                            [[$parameter_arr]],
                                            $function_main
                                        )
                                    );
                                } else {
                                    $function_code = $annotation . $this->genFunction(substr($key, 0, stripos($key, '_')), $function_name, $parameter_arr, $function_main);
                                }
                            } else {
                                $function_code = $this->genFunction(substr($key, 0, stripos($key, '_')), $function_name, $parameter_arr, $function_main);
                            }

                            if (isset($parameter_function_arr['return_annotation']))
                                $function_code = str_replace('*/', "* " . $parameter_function_arr['return_annotation'] . "\r\n     */", $function_code);

                            $code .= $function_code;
                        }
                        break;
                }
            }
        }

        return str_replace("#", "\r\n", $code) . '}';

    }


}
