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
            'models\Exception\DAO\ModelDriverException',
            'models\Exception\DAO\ModelException',
            'models\Exception\DAO\ModelReflectionException',
            'models\Exception\DAO\ModelSqlException'
        ],
        'class' => 'class #class_name# extends BaseModel{',
        'protected_variable' => [
            '#column#' => ['null', 'string', '字段']
        ],
        'public_variable' => [
            'obj' => ['', 'string', 'DAO对象'],
            'data' => ['', 'array', 'DAO对象数组'],
            'meta' => ['null', 'object', '翻页对象'],
            'page' => ['1', 'integer', '当前页'],
            'page_size' => ['10', 'integer', '每页显示条数'],
            'primary_key_arr' => ['', 'array', '主键数组'],
            'auto_increment_key_arr' => ['', 'array', '自增字段数组'],
            'unique_key_arr' => ['', 'string', '唯一字段'],
            'instance' => ['null', 'static', '单例实例']
        ],
        'private_variable' => [],
        'public_function' => [
            '__set' => [
                'function' => '',
                'annotation' => 'set属性',
                'arr' => [
                    ['', 'string', '参数名', 'name'],
                    ['', 'string', '参数值', 'value'],
                ],
                'return_annotation' => '@return $this'
            ],
            '__get' => [
                'function' => '',
                'annotation' => 'get属性',
                'arr' => [
                    ['', 'string', '参数名', 'name']
                ],
                'return_annotation' => '@return mixed'
            ],
            'insert' => [
                'function' => '',
                'annotation' => '创建#table_name#',
                'return_annotation' => "\r\n     * @return mixed\r\n     * @throws ModelDriverException\r\n     * @throws ModelReflectionException\r\n     * @throws ModelSqlException\r\n     * @throws ModelException"
            ],
            'update' => [
                'function' => '',
                'annotation' => '编辑#table_name#',
                'return_annotation' => "\r\n     * @return mixed\r\n     * @throws ModelDriverException\r\n     * @throws ModelReflectionException\r\n     * @throws ModelSqlException\r\n     * @throws ModelException"
            ],
            'delete' => [
                'function' => '',
                'annotation' => '删除#table_name#，需先指定主键',
                'return_annotation' => "\r\n     * @return mixed\r\n     * @throws ModelDriverException\r\n     * @throws ModelReflectionException\r\n     * @throws ModelSqlException\r\n     * @throws ModelException"
            ],
            'find' => [
                'function' => '',
                'annotation' => '根据主键查询#table_name#，获取单条记录',
                'arr' => [
                    ['0', 'integer', '主键编号', 'primary_key']
                ],
                'return_annotation' => "@return \$this\r\n     * @throws ModelException\r\n     * @throws ModelReflectionException"
            ],
            'findOneBy' => [
                'function' => '',
                'annotation' => '根据条件数组查询#table_name#，获取多条记录',
                'arr' => [
                    ['', 'array', '查询条件', 'condition']
                ],
                'return_annotation' => "@return \$this\r\n     * @throws ModelDriverException\r\n     * @throws ModelException\r\n     * @throws ModelReflectionException\r\n     * @throws ModelSqlException"
            ],
            'findBy' => [
                'function' => '',
                'annotation' => '根据条件数组查询#table_name#，获取多条记录',
                'arr' => [
                    ['', 'array', '查询条件', 'condition']
                ],
                'return_annotation' => "@return \$this\r\n     * @throws ModelDriverException\r\n     * @throws ModelException\r\n     * @throws ModelReflectionException\r\n     * @throws ModelSqlException"
            ],
            'batchInsert' => [
                'function' => '',
                'annotation' => '批量插入#table_name#',
                'return_annotation' => "\r\n     * @return mixed\r\n     * @throws ModelDriverException\r\n     * @throws ModelException\r\n     * @throws ModelSqlException"
            ],
            'batchDelete' => [
                'function' => '',
                'annotation' => '批量删除#table_name#',
                'return_annotation' => "\r\n     * @throws ModelDriverException\r\n     * @throws ModelException\r\n     * @throws ModelSqlException"
            ],
            'genBatchUpdateSql' => [
                'function' => '',
                'annotation' => '生成批量生成update的sql',
                'return_annotation' => "\r\n     * @return bool|string\r\n     * @throws ModelDriverException\r\n     * @throws ModelException\r\n     * @throws ModelSqlException"
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
                            if($variable_name == '#column#'){
                                $code .= $this->genModelVariable('public', [], $file_path);
                            }else{
                                if($variable_name == '#table_name#'){
                                    $variable_name = strtolower(substr($file_path, strrpos($file_path, '/')+1, (strrpos($file_path, '.') - 1 - strrpos($file_path, '/'))));
                                }
                                $code .= $this->genVariable(substr($key, 0, stripos($key, '_')),[$variable_name => empty($element_arr) ? ['', 'string', ''] : $element_arr]);
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
