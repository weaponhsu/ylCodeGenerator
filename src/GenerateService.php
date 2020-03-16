<?php


namespace ylCodeGenerator;


class GenerateService extends GenerateCode
{
    private $__column_arr = [];

    protected $_code_arr = [
        'start' => '<?php',
        'namespace' => 'models\\Service',
        'use' => [
            'Yaf\\Registry',
            'models\\DAO\\#table_name#Model',
            'models\\Exception\\DAO\\ModelDriverException;',
            'models\\Exception\\DAO\\ModelException;',
            'models\\Exception\\DAO\\ModelReflectionException;',
            'models\\Exception\\DAO\\ModelSqlException;',
            'models\\Exception\\Service\\ServiceException;',
            'models\\Exception\\Transformer\\TransformerException;',
            'models\\Transformer\\#table_name#Transformer;'
        ],
        'class' => 'class #class_name# {',
        'private_variable' => [
            'list_sorted_set_cache_name_prefix' => ['#table_name#:list', 'string', '列表缓存sortedSet'],
            'single_string_cache_name_prefix' => ['admin:#table_name#', 'string', '单条数据缓存string'],
        ],
        'public_variable' => [
            'instance' => ['null', 'static', '单例']
        ],
//        'construct' => '$this->error = 0;#        parent::setDao(#table_name#::getInstance(str_replace(\'Service\', \'\', get_class($this))));',
//        'construct' => '$this->error = 0;#        parent::setDao(new #table_name#());',
        'public_function' => [
            '__construct' => [
                'function' => '',
                'annotation' => '#table_name#Service constructor.'
            ],
            'getOne' => [
                'function' => '',
                'annotation' => '传入查询条件，条件可以为array(参考getList的condition入参)亦可为integer(主键编号)获取单条数据',
                'arr' => [
//                    ['#column#'],
                    ['', 'array', '查询条件', 'condition'],
                    ['false', 'boolean', '是否删除缓存', 'cache'],
                    ['', 'string', '缓存名称', 'cache_name'],
                    ['86400', 'integer', 'redis有效期', 'expire'],
                ],
                'return_annotation' => "@return array|mixed|null\r\n     * @throws ModelDriverException\r\n     * @throws ModelException\r\n     * @throws ModelReflectionException\r\n     * @throws ModelSqlException\r\n     * @throws ServiceException\r\n     * @throws TransformerException"
            ],
            'getList' => [
                'function' => '',
                'annotation' => '传入查询条件，当前页，每页显示条数，排序字段，排序方式，获取列表数据',
                'arr' => [
                    [1, 'integer', '查询条件', 'page'],
                    [20, 'integer', '每页显示条数', 'page_size'],
                    ['desc', 'string', '排序方式', 'sort'],
                    ['id', 'string', '排序字段', 'order'],
                    ['', 'array', '查询条件', 'condition'],
                    ['false', 'boolean', '是否删除缓存', 'cache'],
                    ['', 'string', '缓存名称', 'cache_name'],
                    ['86400', 'integer', 'redis有效期', 'expire'],
                ],
                'return_annotation' => "@return array|mixed\r\n     * @throws ModelDriverException\r\n     * @throws ModelException\r\n     * @throws ModelReflectionException\r\n     * @throws ModelSqlException\r\n     * @throws ServiceException\r\n     * @throws TransformerException"
            ],
            'create' => [
                'function' => '',
                'annotation' => "插入记录",
                'arr' => [
                    ['#column#'],
                    ['false', 'boolean', '是否删除缓存', 'cache'],
                    ['', 'string', '缓存名称', 'cache_name'],
                    ['86400', 'integer', 'redis有效期', 'expire'],
                ],
                'return_annotation' => "@return array\r\n     * @throws ModelDriverException\r\n     * @throws ModelReflectionException\r\n     * @throws ModelSqlException\r\n     * @throws ModelException\r\n     * @throws TransformerException"
            ],
            'update' => [
                'function' => '',
                'annotation' => '更新记录',
                'arr' => [
                    ['', 'array', '[\'column1\' => \'new_value1\', \'column2\' => \'new_value2\', ...]', 'update_column'],
                    ['', 'string', 'primary_key', 'primary_key'],
                    ['false', 'boolean', '是否删除缓存', 'cache'],
                    ['', 'string', '缓存名称', 'cache_name'],
                    ['86400', 'integer', 'redis有效期', 'expire'],
                ],
                'return_annotation' => "@return array|mixed|null\r\n     * @throws ModelDriverException\r\n     * @throws ModelException\r\n     * @throws ModelReflectionException\r\n     * @throws ModelSqlException\r\n     * @throws ServiceException\r\n     * @throws TransformerException"
            ],
            'delete' => [
                'function' => '',
                'annotation' => "删除记录",
                'arr' => [
                    ['', 'string', 'primary_key', 'primary_key'],
                    ['false', 'boolean', '是否删除缓存', 'cache'],
                    ['', 'string', '缓存名称', 'cache_name'],
                    ['86400', 'integer', 'redis有效期', 'expire'],
                ],
                'return_annotation' => "@return bool\r\n     * @throws ModelDriverException\r\n     * @throws ModelReflectionException\r\n     * @throws ModelSqlException\r\n     * @throws ServiceException\r\n     * @throws ModelException"
            ],
            'batchInsert' => [
                'function' => '',
                'annotation' => '批量插入',
                'arr' => [
                    ['', 'array', '传入的参数数组', 'arr']
                ],
                'return_annotation' => "@return array|mixed\r\n     * @throws ModelDriverException\r\n     * @throws ModelException\r\n     * @throws ModelSqlException\r\n     * @throws ServiceException"
            ],
            'batchUpdate' => [
                'function' => '',
                'annotation' => '批量删除',
                'arr' => [
                    ['', 'array', '666', 'update_column'],
                    ['false', 'boolean', '是否删除缓存', 'cache'],
                    ['86400', 'integer', 'redis有效期', 'expire'],
                ],
                'return_annotation' => "@return bool|string\r\n     * @throws ModelDriverException\r\n     * @throws ModelException\r\n     * @throws ModelSqlException\r\n     * @throws ModelReflectionException\r\n     * @throws ServiceException\r\n     * @throws TransformerException"
            ],
            'batchDelete' => [
                'function' => '',
                'annotation' => '批量删除',
                'arr' => [
                    ['', 'array', '要插入表的数组，一条记录一个数组，每个数组里，column为键，value为值', 'arr'],
                    ['false', 'boolean', '是否删除缓存', 'cache']
                ],
                'return_annotation' => "@return bool\r\n     * @throws ModelDriverException\r\n     * @throws ModelException\r\n     * @throws ModelSqlException\r\n     * @throws ServiceException"
            ]
        ],
        'protected_function' => [],
        'private_function' => [
        ]
    ];

    public function generatedServiceCode($file_path = ''){
        $code = '';
        $column_variable_type = 'private';
        $this->setColumnArr($file_path);
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
                                $table_name = ucfirst(str_replace("Service", "", substr($file_path, strrpos($file_path, '/')+1, (strrpos($file_path, '.') - 1 - strrpos($file_path, '/')))));
                                $element[$id] = str_replace('#table_name#', $table_name, $use_namespace);
                            }
                        }
                        $code .= $this->genUseCode($element);
                        break;
                    case 'class':
                        $code .= $this->genClass($element, $file_path);
                        break;
                    case 'public_variable':
                    case 'protected_variable':
                    case 'private_variable':
                        foreach($element as $variable_name => $element_arr){
                            if($variable_name == '#column#'){
                                $column_variable_type = substr($key, 0, stripos($key, '_'));
                                $code .= $this->genServiceVariable('public', [], $file_path);
                            }
                            elseif(in_array($variable_name, ['list_sorted_set_cache_name_prefix', 'single_string_cache_name_prefix'])){
                                $type = str_replace('_variable', '', $key);
                                $table_name = strtolower(str_replace('Service', '', substr($file_path, strrpos($file_path, '/')+1, (strrpos($file_path, '.') - 1 - strrpos($file_path, '/')))));
                                $element_arr[0] = str_replace('#table_name#', $table_name, $element_arr[0]);
                                $code .= $this->genVariable($type, [$variable_name => $element_arr], false);
                            }else{
                                $function_name = $function_main = '';
                                $function_name_arr = [];
                                $table_name = strtolower(str_replace('Service', '', substr($file_path, strrpos($file_path, '/')+1, (strrpos($file_path, '.') - 1 - strrpos($file_path, '/')))));
                                if(strpos($variable_name, '#table_name#') !== false){
                                    $variable_name = str_replace("#table_name#", $table_name . '_', $variable_name);
                                    foreach (explode('_', $variable_name) as $index => $value){
                                        $function_name_arr[] = ucfirst($value);
                                    }
                                    if(!empty($function_name_arr)){
                                        $function_name = 'get' . implode('', $function_name_arr);
                                        $function_main = "    return \$this->exception instanceof Exception ? \$this->exception : #        (\$this->error === 0 ? \$this->" . $variable_name . " : \$this->getResult('error'));";
                                    }
                                }
                                if($variable_name == 'instance'){
                                    $function_name = 'get' . ucfirst($variable_name);
                                    $function_main = "    if(is_null(self::\$instance)){\r\n            self::\$instance = new self();\r\n        }\r\n        return self::\$instance;";
                                }
                                $code .= $this->genVariable(substr($key, 0, stripos($key, '_')), [$variable_name => $element_arr], false);
                                if(!empty($function_name) && !empty($function_main)){
                                    if ($function_name === 'getInstance') {
                                        $code .= $this->genConstruct();
                                    }
                                    $code .= $this->genFunction(
                                        substr($key, 0, stripos($key, '_')),
                                        $function_name,
                                        strpos($variable_name, $table_name) !== false ? [] : [[$variable_name => $element_arr]],
                                        $function_main,
                                        "    /**\r\n     * " . ucfirst($table_name) . "Service constructor.\r\n     */\r\n"
                                    );
                                }
                            }
                        }
                        break;
                    case 'public_function':
                    case 'protected_function':
                    case 'private_function':
                        foreach ($element as $function_name => $parameter_function_arr) {
                            $table_name = strtolower(str_replace('Service', '', substr($file_path, strrpos($file_path, '/')+1, (strrpos($file_path, '.') - 1 - strrpos($file_path, '/')))));
                            // __construct方法
                            if ($function_name == '__construct') {
                                $function_main = "";
                                $code .= $this->genFunction(
                                    substr($key, 0, stripos($key, '_')),
                                    $function_name,
                                    [],
                                    $function_main,
                                    "    /**\r\n     * " . $table_name . "Service constructor.\r\n     */\r\n"
                                );
                            } else {
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
                                $function_main = $this->$method_name($file_path, substr($key, 0, stripos($key, '_')));

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
                        }
                        break;
                }
            }
        }
        return str_replace("#", "\r\n", $code) . '}';
//        exit();
    }

    /**
     * @param string $file_path
     * @param string $operation
     * @return $this
     * @throws \Exception
     */
    public function setColumnArr($file_path = '', $operation = '')
    {
        if(!empty($file_path)){
            $table_name = strtolower(substr($file_path, strrpos($file_path, '/')+1, (strrpos($file_path, '.') - 1 - strrpos($file_path, '/'))));
            $this->__column_arr = $this->_getTableConstruct($table_name);
            if(/*$operation !== 'delete'*/$operation === 'create'){
                foreach($this->__column_arr as $index => $value_arr){
                    if($value_arr['Key'] == 'PRI' && $value_arr['Extra'] == 'auto_increment'){
                        unset($this->__column_arr[$index]);
                    }
                }
            }
        }
        return $this;
    }

    public function getColumnArr(){
        return $this->__column_arr;
    }

    /**
     * 生成insert方法的代码
     * @param string $file_path 要生成service的文件名，既表名
     * @param string $type 字段属性的类型，默认值是public
     * @return mixed
     */
    public function genCreateFunctionMain($file_path = '', $type = 'public'){
        $code = $unique_index = '';
        $code_supplement = [];
        if(!empty($file_path)){
            $table_name = strtolower(str_replace('Service', '', substr($file_path, strrpos($file_path, '/')+1, (strrpos($file_path, '.') - 1 - strrpos($file_path, '/')))));
            $code = "    \$is_into_redis = \$delete_list_sorted_set = false;";
            $code .= "\r\n\r\n        \$" . $table_name . "_model = " . ucfirst($table_name) .  "Model::getInstance();";
            $code .= "\r\n        if (! method_exists(\$" . $table_name . "_model, '__set'))";
            $code .= "\r\n            throw new ModelReflectionException(ModelException::THERE_IS_NO_SET_METHOD, ModelException::THERE_IS_NO_SET_METHOD_NO);";
            $code .= "\r\n        if (! property_exists(\$" . $table_name . "_model, 'primary_key_arr'))";
            $code .= "\r\n            throw new ModelReflectionException(ModelException::THERE_IS_NO_PRIMARY_KEY_ARR, ModelException::THERE_IS_NO_PRIMARY_KEY_ARR_NO);";
            $code .= "\r\n        // 若primary_key是自增字段，则清空model的primary_key属性的值";
            $code .= "\r\n        \$primary_key = null;";
            $code .= "\r\n        foreach (\$" . $table_name . "_model->primary_key_arr as \$primary_key) {";
            $code .= "\r\n            if (property_exists($" . $table_name . "_model, 'auto_increment_key_arr') && in_array(\$primary_key, \$" . $table_name . "_model->auto_increment_key_arr))";
            $code .= "\r\n                \$" . $table_name . "_model->__set(\$primary_key, null);";
            $code .= "\r\n        }";
            $code .= "\r\n\r\n        // 插入数据库";
            $code .= "\r\n        \$new_" . $table_name . "_primary_key = \$" . $table_name . "_model";
            foreach ($this->getColumnArr() as $column_arr) {
                if ($column_arr['Key'] != 'PRI'){
                    if($column_arr['Default'] == 'CURRENT_TIMESTAMP' || $column_arr['Extra'] == ' on update CURRENT_TIMESTAMP')
                        $code_supplement[] = "->__set('". $column_arr['Field'] ."', \$datetime_now)";
                    else
                        $code .= "\r\n            ->__set('" . strtolower($column_arr['Field']) . "', \$" . strtolower($column_arr['Field']) . ")";

                    if($column_arr['Key'] == 'UNI' && empty($unique_index))
                        $unique_index = $column_arr['Field'];
                }
            }
            $code .= "\r\n            ->insert();";
            $code .= "\r\n\r\n        if (! is_null(\$primary_key))";
            $code .= "\r\n            \$" . $table_name . "_model->__set(\$primary_key, \$new_" . $table_name . "_primary_key);";

            if (!empty($code_supplement)) {
                $code .=  "\r\n\r\n        // 插入成功，生成创建时间，更新时间，登录次数 生成返回用数据\r\n        \$datetime_now = date('Y-m-d H:i:s', time());";
                foreach ($code_supplement as $val) {
                    $code .= "\r\n        \$" . $table_name . "_model" . $val . ";";
                }
            }

            $code .= "\r\n        \$transformer = new " . ucfirst($table_name) . "Transformer(" . ucfirst($table_name) . "Model::getInstance());";
            $code .= "\r\n        \$data_from_storage = \$transformer->SingleData();";
            $code .= "\r\n\r\n        // 需要使用缓存 is_into_redis为true delete_list_sorted_set为true";
            $code .= "\r\n        if(\$cache === true){";
            $code .= "\r\n            \$cache_name = \$this->__single_string_cache_name_prefix . ':' . \$new_" . $table_name . "_primary_key;";
            $code .= "\r\n            \$is_into_redis = \$delete_list_sorted_set = true;";
            $code .= "\r\n        }";
            $code .= "\r\n\r\n        // is_into_redis为true 新增的会员数据写入redis";
            $code .= "\r\n        if(\$is_into_redis === true)";
            $code .= "\r\n            Registry::get('redis_string')->genString(\$cache_name, json_encode(\$data_from_storage), \$expire);";
            $code .= "\r\n        // 需要删除列表的sortedSet的相关数据";
            $code .= "\r\n        if(\$delete_list_sorted_set === true) {";
            $code .= "\r\n            // 删除缓存中的列表SortedSet数据";
            $code .= "\r\n            Registry::get('redis_sorted_set')->deleteSortedSet(\$this->__list_sorted_set_cache_name_prefix . ':*');";
            $code .= "\r\n\r\n            // 删除缓存中的列表meta数据";
            $code .= "\r\n            \$res = Registry::get('redis_string')->getKeysByPattern(\$this->__list_sorted_set_cache_name_prefix . ':*');";
            $code .= "\r\n            if(is_array(\$res) && !empty(\$res))";
            $code .= "\r\n                Registry::get('redis_string')->deleteString(\$res);";
            $code .= "\r\n        }";
            $code .= "\r\n\r\n        return \$data_from_storage;";
        }
        return $code;
    }

    /**
     * 生成update方法的代码
     * @param string $file_path 要生成service的文件名，既表名
     * @param string $type 字段属性的类型，默认值是public
     * @return mixed
     */
    public function genUpdateFunctionMain($file_path = '', $type = 'public'){
        $code = '';
        if(!empty($file_path)){
            $table_name = strtolower(str_replace('Service', '', substr($file_path, strrpos($file_path, '/')+1, (strrpos($file_path, '.') - 1 - strrpos($file_path, '/')))));
            $code .= "    if(empty(\$primary_key))";
            $code .= "\r\n            throw new ServiceException(ServiceException::SERVICE_UPDATE_ID_EMPTY, ServiceException::SERVICE_UPDATE_ID_EMPTY_NO);";
            $code .= "\r\n        if(empty(\$update_column) || ! is_array(\$update_column))";
            $code .= "\r\n            throw new ServiceException(ServiceException::SERVICE_UPDATE_INVALID_COLUMN_ARR, ServiceException::SERVICE_UPDATE_INVALID_COLUMN_ARR_NO);";
            $code .= "\r\n\r\n        // is_into_redis 写入redis 需要使用redis但redis不存在时会为true，反之为false";
            $code .= "\r\n        // update_redis 更新redis 需要使用redis且redis存在时为true，反之为false";
            $code .= "\r\n        // delete_list_sorted_set 删除列表sortedSet与列表meta的string 需要使用redis且redis存在时为true，反之为false";
            $code .= "\r\n        // select_db 查询数据库";
            $code .= "\r\n        \$is_into_redis = \$update_redis = \$select_db = \$delete_list_sorted_set = false;";
            $code .= "\r\n        \$redis_is_exists = null;";
            $code .= "\r\n\r\n        // 需要使用缓存 但没有传入缓存名";
            $code .= "\r\n        if(\$cache === true && empty(\$cache_name))";
            $code .= "\r\n             throw new ServiceException(ServiceException::SERVICE_CACHE_NAME_IS_EMPTY, ServiceException::SERVICE_CACHE_NAME_IS_EMPTY_NO);";
            $code .= "\r\n\r\n         // 需要使用缓存";
            $code .= "\r\n        if (\$cache === true && \$cache_name) {";
            $code .= "\r\n            \$cache_name = \$this->__single_string_cache_name_prefix . ':' . \$cache_name;";
            $code .= "\r\n            \$redis_is_exists = Registry::get('redis_string')->getString(\$cache_name);";
            $code .= "\r\n        }";
            $code .= "\r\n        // 不使用缓存";
            $code .= "\r\n        else \$select_db = true;";
            $code .= "\r\n\r\n        // 缓存不存在 查询数据库为true 写入redis为true";
            $code .= "\r\n        if (\$redis_is_exists === null)";
            $code .= "\r\n            \$select_db = \$is_into_redis = true;";
            $code .= "\r\n        // 缓存存在 直接json_decode缓存中的数据库 更新redis为true";
            $code .= "\r\n        else {";
            $code .= "\r\n            \$redis_is_exists = json_decode(\$redis_is_exists, true);";
            $code .= "\r\n            \$update_redis = \$delete_list_sorted_set = true;";
            $code .= "\r\n        }";
            $code .= "\r\n\r\n        // 实例化 " . $table_name . "_model";
            $code .= "\r\n        $" . $table_name . "_model = " . ucfirst($table_name) . "Model::getInstance();";
            $code .= "\r\n        foreach (\$update_column as \$column_name => \$value) {";
            $code .= "\r\n            \$" . $table_name . "_model->__set(\$column_name, \$value);";
            $code .= "\r\n            if (\$update_redis === true)";
            $code .= "\r\n                \$redis_is_exists[\$column_name] = \$value;";
            $code .= "\r\n        }";
            $code .= "\r\n        // 修改数据库";
            $code .= "\r\n        \$" . $table_name . "_model->__set(implode('', \$" . $table_name . "_model->primary_key_arr), \$primary_key)->update();";
            $code .= "\r\n\r\n        // 不需要使用缓存 或缓存不存在时 读取数据库";
            $code .= "\r\n        // 之所以再从数据库中获取一边，是为了返回给调用者一个" . $table_name . "";
            $code .= "\r\n        if (\$select_db === true) {";
            $code .= "\r\n            // 直接使用主键查询";
            $code .= "\r\n            \$data_from_db = \$" . $table_name . "_model->find(\$primary_key);";
            $code .= "\r\n\r\n            \$is_into_redis = \$cache;";
            $code .= "\r\n\r\n            \$" . $table_name . "_transformer = new " . ucfirst($table_name) . "Transformer(\$data_from_db);";
            $code .= "\r\n            \$redis_is_exists = \$" . $table_name . "_transformer->SingleData();";
            $code .= "\r\n        }";
            $code .= "\r\n        // 需要删除列表的sortedSet的相关数据";
            $code .= "\r\n        if(\$delete_list_sorted_set === true) {";
            $code .= "\r\n            // 删除缓存中的列表SortedSet数据";
            $code .= "\r\n            Registry::get('redis_sorted_set')->deleteSortedSet(\$this->__list_sorted_set_cache_name_prefix . ':*');";
            $code .= "\r\n\r\n            // 删除缓存中的列表meta数据";
            $code .= "\r\n            \$res = Registry::get('redis_string')->getKeysByPattern(\$this->__list_sorted_set_cache_name_prefix . ':*');";
            $code .= "\r\n            if(is_array(\$res) && !empty(\$res))";
            $code .= "\r\n                Registry::get('redis_string')->deleteString(\$res);";
            $code .= "\r\n        }";
            $code .= "\r\n\r\n        if (\$is_into_redis === true)";
            $code .= "\r\n            Registry::get('redis_string')->genString(\$cache_name, json_encode(\$redis_is_exists), \$expire);";
            $code .= "\r\n\r\n        if (\$update_redis === true)";
            $code .= "\r\n            Registry::get('redis_string')->updateString(\$cache_name, json_encode(\$redis_is_exists), \$expire);";
            $code .= "\r\n\r\n        if (\$update_redis === true || \$is_into_redis === true)";
            $code .= "\r\n            \$redis_is_exists = Registry::get('redis_string')->getString(\$cache_name);";
            $code .= "\r\n\r\n        return \$redis_is_exists;";
        }
        return $code;
    }

    /**
     * 生成delete方法
     * @param string $file_path 要生成service的文件名，既表名
     * @param string $type 字段属性的类型，默认值是public
     * @return mixed
     */
    public function genDeleteFunctionMain($file_path = '', $type = 'public'){
        $code = '';
        if(!empty($file_path)){
            $table_name = strtolower(str_replace("Service", "", substr($file_path, strrpos($file_path, '/')+1, (strrpos($file_path, '.') - 1 - strrpos($file_path, '/')))));
            $code .= "    if(empty(\$primary_key))";
            $code .= "\r\n            throw new ServiceException(ServiceException::SERVICE_DELETE_ID_EMPTY, ServiceException::SERVICE_DELETE_ID_EMPTY_NO);";
            $code .= "\r\n\r\n        // 删除缓存的结果 缓存不存在或不删除缓存为null 缓存删除成功为1 缓存删除失败为0";
            $code .= "\r\n        \$delete_from_db = null;";
            $code .= "\r\n        // 需要使用缓存 但没有传入缓存名";
            $code .= "\r\n        if (\$cache === true && empty(\$cache_name))";
            $code .= "\r\n            throw new ServiceException(ServiceException::SERVICE_CACHE_NAME_IS_EMPTY, ServiceException::SERVICE_CACHE_NAME_IS_EMPTY_NO);";
            $code .= "\r\n        // 需要使用缓存";
            $code .= "\r\n        if (\$cache === true && \$cache_name) {";
            $code .= "\r\n            \$cache_name = \$this->__single_string_cache_name_prefix . ':' . \$cache_name;";
            $code .= "\r\n\r\n            // 从缓存中读取要删除的单条数据";
            $code .= "\r\n            Registry::get('redis_string')->deleteString(\$cache_name);";
            $code .= "\r\n\r\n            // 删除缓存中的列表SortedSet数据";
            $code .= "\r\n            Registry::get('redis_sorted_set')->deleteSortedSet(\$this->__list_sorted_set_cache_name_prefix . ':*');";
            $code .= "\r\n\r\n            // 删除缓存中的列表meta数据";
            $code .= "\r\n            \$res = Registry::get('redis_string')->getKeysByPattern(\$this->__list_sorted_set_cache_name_prefix . ':*');";
            $code .= "\r\n            if (is_array(\$res) && !empty(\$res))";
            $code .= "\r\n                Registry::get('redis_string')->deleteString(\$res);";
            $code .= "\r\n\r\n            \$delete_from_db = true;";
            $code .= "\r\n        } else";
            $code .= "\r\n            \$delete_from_db = true;";
            $code .= "\r\n\r\n        // 缓存删除成功 或不使用缓存 或缓存不存在";
            $code .= "\r\n        if (\$delete_from_db === true)";
            $code .= "\r\n            // 删除数据库";
            $code .= "\r\n            " . ucfirst($table_name) . "Model::getInstance()->__set(" . ucfirst($table_name) . "Model::getInstance()->primary_key_arr[0], \$primary_key)->delete();";
            $code .= "\r\n\r\n        return true;";
        }
        return $code;
    }

    /**
     * @param string $file_path
     * @param string $type
     * @return string
     */
    public function genBatchInsertFunctionMain($file_path = '', $type = 'public'){
        $code = "";
        if(!empty($file_path)){
            $table_name = strtolower(str_replace("Service", "", substr($file_path, strrpos($file_path, '/') + 1, (strrpos($file_path, '.') - 1 - strrpos($file_path, '/')))));

            $code .= "    \$array = !empty(\$arr) && is_array(\$arr) ? \$arr : '';";
            $code .= "\r\n        if(empty(\$array))";
            $code .= "\r\n            throw new ServiceException(ServiceException::SERVICE_BATCH_INSERT_RETURN_FALSE,";
            $code .= "\r\n                ServiceException::SERVICE_BATCH_INSERT_RETURN_FALSE_NO);";
            $code .= "\r\n\r\n        \$" . $table_name . "_model = " . ucfirst($table_name) . "Model::getInstance();";
            $code .= "\r\n\r\n        \$" . $table_name . "_model->__set('data', \$arr)->batchInsert();";

            $code .= "\r\n        // 获取列表缓存名";
            $code .= "\r\n        \$list_cache_name = \$this->__list_sorted_set_cache_name_prefix . ':*';";
            $code .= "\r\n        \$list_cache_name_arr = Registry::get('redis_sorted_set')->getKeysByPattern(\$list_cache_name);";
            $code .= "\r\n\r\n        // 删除列表缓存";
            $code .= "\r\n        if (! empty(\$list_cache_name_arr)) {";
            $code .= "\r\n            Registry::get('redis_sorted_set')->deleteSortedSet(\$list_cache_name);";
            $code .= "\r\n            Registry::get('redis_string')->deleteString(\$list_cache_name_arr);";
            $code .= "\r\n        }";
            $code .= "\r\n        return true;";

            /*$code .= "    \$array = !empty(\$arr) && is_array(\$arr) ? \$arr : '';";
            $code .= "\r\n        if(empty(\$array))";
            $code .= "\r\n            throw new ServiceException(ServiceException::SERVICE_BATCH_INSERT_RETURN_FALSE,";
            $code .= "\r\n                ServiceException::SERVICE_BATCH_INSERT_RETURN_FALSE_NO);";
            $code .= "\r\n\r\n        \$" . $table_name . "_model = " . ucfirst($table_name) . "Model::getInstance();";
            $code .= "\r\n        if(! property_exists(\$" . $table_name . "_model, 'unique_key_arr') || empty(\$" . $table_name . "_model->unique_key_arr))";
            $code .= "\r\n            throw new ModelException(";
            $code .= "\r\n                str_replace('%class_name%', 'unique_key_arr', ModelException::THERE_IS_NO_PROPERTY),";
            $code .= "\r\n                    ModelException::THERE_IS_NO_PROPERTY_NO);";
            $code .= "\r\n\r\n        \$" . $table_name . "_model->__set('data', \$arr)->batchInsert();";
            $code .= "\r\n\r\n        \$rules = ['field' => \$" . $table_name . "_model->unique_key_arr[0], 'op' => 'in',";
            $code .= "\r\n            'data' => implode(',', array_column(\$array, \$" . $table_name . "_model->unique_key_arr[0]))];";
            $code .= "\r\n        \$condition = [";
            $code .= "\r\n            'groupOp' => 'AND',";
            $code .= "\r\n            'rules' => [\$rules]";
            $code .= "\r\n        ];";
            $code .= "\r\n\r\n        \$cache_name = '';";
            $code .= "\r\n        if (\$cache === true) {";
            $code .= "\r\n            \$cache_name = '" . $table_name . ":list:' . http_build_query([\$" . $table_name . "_model->unique_key_arr[0] => array_column(\$array, \$" . $table_name . "_model->unique_key_arr[0])]) . ':1:' . count(array_column(\$array, \$" . $table_name . "_model->unique_key_arr[0]));";
            $code .= "\r\n            \$data_from_storage = \$this->getList(1, count(array_column(\$array, \$" . $table_name . "_model->unique_key_arr[0])), 'desc', 'id', \$condition, \$cache, \$cache_name);";
            $code .= "\r\n        } else";
            $code .= "\r\n            \$data_from_storage = \$this->getList(1, count(array_column(\$array, \$" .  $table_name . "_model->unique_key_arr[0])), 'desc', 'id', \$condition, \$cache, \$cache_name);";
            $code .= "\r\n\r\n        \$data_into_redis = \$cache_name_arr = [];";
            $code .= "\r\n        if (\$cache === true && !empty(\$data_from_storage['data'])) {";
            $code .= "\r\n            array_reduce(array_keys(\$data_from_storage['data']), function (\$carry, \$key) use(&\$cache_name_arr, &\$data_from_storage, &\$data_into_redis, &\$" . $table_name . "_model) {";
            $code .= "\r\n                array_push(\$cache_name_arr, \$this->__single_string_cache_name_prefix . ':' . \$data_from_storage['data'][\$key][\$" . $table_name . "_model->primary_key_arr[0]]);";
            $code .= "\r\n                    array_push(\$data_into_redis, json_encode(\$data_from_storage['data'][\$key]));";
            $code .= "\r\n            });";
            $code .= "\r\n        }";
            $code .= "\r\n\r\n        if (! empty(\$data_into_redis) && ! empty(\$cache_name_arr))";
            $code .= "\r\n            Registry::get('redis_string')->genString(\$cache_name_arr, \$data_into_redis, \$expire);";
            $code .= "\r\n        return \$data_from_storage;";*/
        }
        return $code;
    }

    /**
     * @param string $file_path
     * @param string $type
     * @return string
     */
    public function genBatchUpdateFunctionMain($file_path = '', $type = 'public'){
        $code = "";
        if(!empty($file_path)){
            $table_name = strtolower(str_replace("Service", "", substr($file_path, strrpos($file_path, '/') + 1, (strrpos($file_path, '.') - 1 - strrpos($file_path, '/')))));
            $code .= "    if (empty(\$update_column) || ! is_array(\$update_column))";
            $code .= "\r\n            throw new ServiceException(ServiceException::SERVICE_BATCH_UPDATE_DATA_EMPTY, ServiceException::SERVICE_BATCH_UPDATE_DATA_EMPTY_NO);";
            $code .= "\r\n        \$cache_name_arr = \$redis_is_exists = \$update_redis_data = [];";
            $code .= "\r\n        \$into_redis = false;";
            $code .= "\r\n\r\n        \$" . $table_name . "_model = " . ucfirst($table_name) . "Model::getInstance();";
            $code .= "\r\n        // 更新数据库";
            $code .= "\r\n        \$" . $table_name . "_model->__set('data', \$update_column)->genBatchUpdateSql();";
            $code .= "\r\n\r\n        // 需要缓存";
            $code .= "\r\n        if (\$cache === true) {";
            $code .= "\r\n            \$cache_name = \$this->__single_string_cache_name_prefix . ':*';";
            $code .= "\r\n            \$cache_name_arr = Registry::get('redis_string')->getKeysByPattern(\$cache_name);";
            $code .= "\r\n        }";
            $code .= "\r\n\r\n        // 缓存名读取成功";
            $code .= "\r\n        if (! empty(\$cache_name_arr)) {";
            $code .= "\r\n            \$redis_is_exists = Registry::get('redis_string')->getString(\$cache_name_arr);";
            $code .= "\r\n            if(is_array(\$redis_is_exists))";
            $code .= "\r\n            array_walk(\$redis_is_exists, function (\$json_encode_string, \$idx) use(&\$redis_is_exists){";
            $code .= "\r\n                \$redis_is_exists[\$idx] = json_decode(\$json_encode_string, true);";
            $code .= "\r\n            });";
            $code .= "\r\n        }";
            $code .= "\r\n        // 缓存不存在 或不需要读取缓存";
            $code .= "\r\n        else { ";
            $code .= "\r\n            \$redis_is_exists = \$this->getList(1, count(\$update_column), 'desc', \$rule_model->primary_key_arr[0], [";
            $code .= "\r\n                'groupOp' => 'AND', 'rules' => [";
            $code .= "\r\n                    ['field' => \$" . $table_name . "_model->primary_key_arr[0], 'op' => 'in', 'data' => implode(',', array_column(\$update_column, \$" . $table_name . "_model->primary_key_arr[0]))]]";
            $code .= "\r\n            ]);";
            $code .= "\r\n            \$into_redis = \$cache;";
            $code .= "\r\n            \$redis_is_exists = \$redis_is_exists['data'];";
            $code .= "\r\n        }";
            $code .= "\r\n\r\n        // 缓存读取成功，修改对应的值";
            $code .= "\r\n        if (\$redis_is_exists && \$cache === true) {";
            $code .= "\r\n            \$match_update_column = array_column(\$update_column, \$" . $table_name . "_model->primary_key_arr[0]);";
            $code .= "\r\n            foreach (\$redis_is_exists as \$idx => \$record) {";
            $code .= "\r\n                if (false !== \$key = array_search(\$record[\$" . $table_name . "_model->primary_key_arr[0]], \$match_update_column)) {";
            $code .= "\r\n                    foreach (\$update_column[\$key] as \$column => \$new_value)";
            $code .= "\r\n                        \$redis_is_exists[\$idx][\$column] = \$new_value;";
            $code .= "\r\n                        \$update_redis_data[\$idx] = json_encode(\$redis_is_exists[\$idx]);";
            $code .= "\r\n\r\n                        if (\$into_redis === true)";
            $code .= "\r\n                            array_push(\$cache_name_arr, \$this->__single_string_cache_name_prefix . ':' . \$record[\$" . $table_name . "_model->primary_key_arr[0]]);";
            $code .= "\r\n                }";
            $code .= "\r\n            }";
            $code .= "\r\n        }";
            $code .= "\r\n\r\n        // 更新缓存";
            $code .= "\r\n        if (! empty(\$update_redis_data) && ! empty(\$cache_name_arr))";
            $code .= "\r\n            Registry::get('redis_string')->updateString(\$cache_name_arr, \$update_redis_data, \$expire);";
            $code .= "\r\n\r\n        return \$redis_is_exists;";
        }
        return $code;
    }

    /**
     * @param string $file_path
     * @param string $type
     * @return string
     */
    public function genBatchDeleteFunctionMain($file_path = '', $type = 'public'){
        $code = "";
        if(!empty($file_path)){
            $table_name = strtolower(str_replace("Service", "", substr($file_path, strrpos($file_path, '/') + 1, (strrpos($file_path, '.') - 1 - strrpos($file_path, '/')))));
            $code = "    \$array = !empty(\$arr) && is_array(\$arr) ? \$arr : '';";
            $code .= "\r\n        if(empty(\$array))";
            $code .= "\r\n            throw new ServiceException(ServiceException::SERVICE_BATCH_DELETE_DATA_EMPTY, ServiceException::SERVICE_BATCH_DELETE_DATA_EMPTY_NO);";
            $code .= "\r\n\r\n        \$" . $table_name . "_model = " . ucfirst($table_name) . "Model::getInstance();";
            $code .= "\r\n        \$" . $table_name . "_model->__set(\"data\", \$array)->batchDelete();";
            $code .= "\r\n\r\n        \$list_cache_name_arr = \$cache_name_arr = [];";
            $code .= "\r\n        \$list_cache_name = [];";
            $code .= "\r\n        // 需要缓存";
            $code .= "\r\n        if (\$cache === true) {";
            $code .= "\r\n            \$cache_name = \$this->__single_string_cache_name_prefix . ':*';";
            $code .= "\r\n            \$cache_name_arr = Registry::get('redis_string')->getKeysByPattern(\$cache_name);";
            $code .= "\r\n            \$list_cache_name = \$this->__list_sorted_set_cache_name_prefix . ':*';";
            $code .= "\r\n            \$list_cache_name_arr = Registry::get('redis_sorted_set')->getKeysByPattern(\$list_cache_name);";
            $code .= "\r\n        }";
            $code .= "\r\n\r\n        // 删除列表缓存";
            $code .= "\r\n        if (! empty(\$list_cache_name_arr)) {";
            $code .= "\r\n            Registry::get('redis_sorted_set')->deleteSortedSet(\$list_cache_name);";
            $code .= "\r\n            Registry::get('redis_string')->deleteString(\$list_cache_name_arr);";
            $code .= "\r\n        }";
            $code .= "\r\n\r\n        if (! empty(\$cache_name_arr)) {";
            $code .= "\r\n            Registry::get('redis_string')->deleteString(\$cache_name_arr);";
            $code .= "\r\n        }";
            $code .= "\r\n        return true;";
        }
        return $code;
    }

    /**
     * @param string $file_path
     * @param array $parameters_arr
     * @param bool $is_one
     * @return string
     */
    public function genSelectFunctionMain($file_path = '', $parameters_arr =[], $is_one = true){
        return $is_one === true ? $this->__genGetOneFunctionMain($file_path, $parameters_arr) :
            $this->genGetListFunctionMain($file_path, $parameters_arr);
    }

    /**
     * @param string $file_path
     * @param array $parameters_arr
     * @return string
     */
    public function genGetListFunctionMain($file_path = '', $parameters_arr = []){
        $code = '';
        if (!empty($file_path)) {
            $table_name = strtolower(str_replace("Service", "", substr($file_path, strrpos($file_path, '/') + 1, (strrpos($file_path, '.') - 1 - strrpos($file_path, '/')))));
            $code = "    \$select_db = \$is_into_redis = false;";
            $code .= "\r\n        \$cache_name = \$cache_name === '' ? false : \$cache_name;";
            $code .= "\r\n        \$redis_is_exists = \$full_data = [];";
            $code .= "\r\n        \$result_from_db = false;";
            $code .= "\r\n\r\n        // 需要使用缓存 但没有传入缓存名";
            $code .= "\r\n        if (\$cache === true && empty(\$cache_name))";
            $code .= "\r\n            throw new ServiceException(ServiceException::SERVICE_CACHE_NAME_IS_EMPTY, ServiceException::SERVICE_CACHE_NAME_IS_EMPTY_NO);";
            $code .= "\r\n\r\n        // 使用缓存，且缓存名存在，读取缓存";
            $code .= "\r\n        if (\$cache === true && is_string(\$cache_name)) {";
            $code .= "\r\n            \$redis_is_exists = Registry::get('redis_sorted_set')->getSortedSet(\$cache_name);";
            $code .= "\r\n            \$string_redis_key_arr = [];";
            $code .= "\r\n            array_walk(\$redis_is_exists, function (\$val, \$idx) use (&\$string_redis_key_arr) {";
            $code .= "\r\n                array_push(\$string_redis_key_arr, \$this->__single_string_cache_name_prefix . ':' . \$val);";
            $code .= "\r\n            });";
            $code .= "\r\n            if (!empty(\$string_redis_key_arr)) {";
            $code .= "\r\n                \$full_data = [";
            $code .= "\r\n                    'data' => Registry::get('redis_string')->getString(\$string_redis_key_arr),";
            $code .= "\r\n                    'meta' => json_decode(Registry::get('redis_string')->getString(\$cache_name), true)";
            $code .= "\r\n                ];";
            $code .= "\r\n\r\n                // 缓存数据读取失败";
            $code .= "\r\n                if (\$full_data['data'] === false || \$full_data['meta'] === false)";
            $code .= "\r\n                    \$redis_is_exists = [];";
            $code .= "\r\n            }";
            $code .= "\r\n        }";
            $code .= "\r\n        // 不使用缓存或缓存不存在 select_db为true";
            $code .= "\r\n        if (empty(\$redis_is_exists))";
            $code .= "\r\n            \$select_db = true;";
            $code .= "\r\n        // 读取数据库数据";
            $code .= "\r\n        if (\$select_db === true)";
            $code .= "\r\n            \$result_from_db = " . ucfirst($table_name) . "Model::getInstance()->__set('page', \$page)->__set('page_size', \$page_size)";
            $code .= "\r\n                ->setOrderBy(['order' => \$order, 'sort' => \$sort])->findBy(\$condition);";
            $code .= "\r\n        // 数据库查无结果 或 select_db === false";
            $code .= "\r\n        // 从数据库中查询出结果了";
            $code .= "\r\n        if (\$result_from_db instanceof " . ucfirst($table_name) . "Model) {";
            $code .= "\r\n            \$transformer = new " . $table_name . "Transformer(\$result_from_db);";
            $code .= "\r\n            // 根据是否使用缓存来获取数据";
            $code .= "\r\n            \$full_data = \$transformer->BackEndData();";
            $code .= "\r\n            if (\$cache === true)";
            $code .= "\r\n                \$redis_is_exists = ['data' => array_column(\$full_data['data'], 'id')];";
            $code .= "\r\n            \$is_into_redis = \$cache;";
            $code .= "\r\n        }";
            $code .= "\r\n\r\n        // 有正确数据，且需要写入redis的sortedSet";
            $code .= "\r\n        if (\$is_into_redis === true && \$redis_is_exists && isset(\$full_data['meta'])) {";
            $code .= "\r\n            Registry::get('redis_sorted_set')->genSortedSet(\$cache_name, \$redis_is_exists['data'], \$expire);";
            $code .= "\r\n            Registry::get('redis_string')->genString(\$cache_name, json_encode(\$full_data['meta']), \$expire);";
            $code .= "\r\n        }";
            $code .= "\r\n\r\n        // 读取缓存";
            $code .= "\r\n        // 并按照sortedSet的顺序，将mget的结果一个一个写进redis_is_exists['data']里";
            $code .= "\r\n        // 并删除掉多余无用的redis_is_exists的键";
            $code .= "\r\n        if(\$cache){";
            $code .= "\r\n            \$batch_into_redis_key = \$batch_into_redis_value = [];";
            $code .= "\r\n\r\n            // 遍历单条数据 若单条数据缓存不存在 则调用getOne方法获取数据并复制给\$detail_in_redis";
            $code .= "\r\n            foreach (\$full_data['data'] as \$idx => \$single_data) {";
            $code .= "\r\n                // 单条数据不存在";
            $code .= "\r\n                if (\$single_data === false) {";
            $code .= "\r\n                    if (! isset(\$redis_is_exists[\$idx]))";
            $code .= "\r\n                        throw new ServiceException(ServiceException::SERVICE_GET_LIST_WITH_REDIS_IDX_NOT_EXISTS, ServiceException::SERVICE_GET_LIST_WITH_REDIS_IDX_NOT_EXISTS_NO);";
            $code .= "\r\n                    \$redis_is_exists['data'][\$idx] = \$this->getOne(\$redis_is_exists[\$idx], true, (int)\$redis_is_exists[\$idx]);";
            $code .= "\r\n                } else if (is_array(\$single_data)) {";
            $code .= "\r\n                    \$redis_is_exists['data'][\$idx] = \$single_data;";
            $code .= "\r\n                    array_push(\$batch_into_redis_key, strpos(\$this->__single_string_cache_name_prefix, \$cache_name) === false ? ";
            $code .= "\r\n                    \$this->__single_string_cache_name_prefix . ':' . \$single_data['id'] : \$single_data['id']);";
            $code .= "\r\n                    array_push(\$batch_into_redis_value, json_encode(\$single_data));";
            $code .= "\r\n                } else { ";
            $code .= "\r\n                    \$redis_is_exists['data'][\$idx] = json_decode(\$single_data, true);";
            $code .= "\r\n                }";
            $code .= "\r\n                unset(\$redis_is_exists[\$idx]);";
            $code .= "\r\n            }";
            $code .= "\r\n            \$redis_is_exists['meta'] = \$full_data['meta'];";
            $code .= "\r\n            // array_multisort(array_column(\$redis_is_exists['data'], 'index'), SORT_ASC, \$redis_is_exists['data']);";
            $code .= "\r\n            if (!empty(\$batch_into_redis_key) && !empty(\$batch_into_redis_value))";
            $code .= "\r\n                Registry::get(\"redis_string\")->genString(\$batch_into_redis_key, \$batch_into_redis_value);";
            $code .= "\r\n        } else";
            $code .= "\r\n            \$redis_is_exists = \$full_data;";
            $code .= "\r\n\r\n        return \$redis_is_exists;";
        }
        return $code;
    }

    /**
     * @param string $file_path
     * @param array $parameters_arr
     * @return string
     */
    public function genGetOneFunctionMain($file_path = '', $parameters_arr = []){
        $code = '';
        if (!empty($file_path)) {
            $table_name = strtolower(str_replace("Service", "", substr($file_path, strrpos($file_path, '/') + 1, (strrpos($file_path, '.') - 1 - strrpos($file_path, '/')))));
            $code .= "    // 查询数据库，写入缓存变量 false表示不执行，true表示要执行";
            $code .= "\r\n        \$select_db = \$is_into_redis = false;";
            $code .= "\r\n        \$cache_name = \$cache_name === '' ? false : \$cache_name;";
            $code .= "\r\n        \$redis_is_exists = false;";
            $code .= "\r\n\r\n        // 使用缓存，且缓存名存在，检查缓存";
            $code .= "\r\n        if(\$cache === true && ! is_string(\$cache_name)) {";
            $code .= "\r\n            \$cache_name = strpos(\$this->__single_string_cache_name_prefix, (string)\$cache_name) === false ? ";
            $code .= "\r\n            \$this->__single_string_cache_name_prefix . ':' . \$cache_name : \$cache_name;";
            $code .= "\r\n            \$redis_is_exists = Registry::get('redis_string')->getString(\$cache_name);";
            $code .= "\r\n        }";
            $code .= "\r\n\r\n        if (\$cache === true && empty(\$cache_name))";
            $code .= "\r\n            throw new ServiceException(ServiceException::SERVICE_CACHE_NAME_IS_EMPTY, ServiceException::SERVICE_CACHE_NAME_IS_EMPTY_NO);";
            $code .= "\r\n\r\n        // 缓存不存在 查询数据库为true";
            $code .= "\r\n        if (\$redis_is_exists === false) \$select_db = true;";
            $code .= "\r\n        // 缓存存在 直接json_decode缓存中的数据库";
            $code .= "\r\n        else \$redis_is_exists = json_decode(\$redis_is_exists, true);";
            $code .= "\r\n\r\n        // 需要查询数据库";
            $code .= "\r\n        if(\$select_db === true){";
            $code .= "\r\n            // \$condition是数组 多条件查询 反之 直接使用主键查询";
            $code .= "\r\n            \$data_from_db = is_array(\$condition) ? " . ucfirst($table_name) . "Model::getInstance()->findOneBy(\$condition) : ";
            $code .= "\r\n                " . ucfirst($table_name) . "Model::getInstance()->find(\$condition);";
            $code .= "\r\n\r\n            \$is_into_redis = \$cache;";
            $code .= "\r\n\r\n            \$" . $table_name . "_transformer = new " . ucfirst($table_name) . "Transformer(\$data_from_db);";
            $code .= "\r\n            \$redis_is_exists = \$" . $table_name . "_transformer->SingleData();";
            $code .= "\r\n        }";
            $code .= "\r\n\r\n        if(\$is_into_redis === true)";
            $code .= "\r\n            Registry::get('redis_string')->genString(\$cache_name, json_encode(\$redis_is_exists), \$expire);";
            $code .= "\r\n\r\n        return \$redis_is_exists;";
        }
        return $code;
    }
}
