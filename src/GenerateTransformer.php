<?php


namespace ylCodeGenerator;


class GenerateTransformer extends GenerateCode
{
    private $__column_arr;

    protected $_code_arr = [
        'start' => '<?php',
        'namespace' => 'models\\Transformer',
        'use' => [
            'Yaf\\Exception',
            'models\\DAO\\#table_name#',
            'models\\Exception\\DAO\\ModelException',
            'models\\Exception\\DAO\\ModelReflectionException',
            'models\\Exception\\Transformer\\TransformerException'
        ],
        'class' => 'class #class_name# extends BaseTransformer {',
        'public_variable' => [
            '#table_name#model' => ['', 'object', 'model对象']
        ],
        'private_variable' => [
        ],

        'construct' => [
            'function' => '',
            'annotation' => '',
//            '#table_name#model' => ['', 'object', '模型对象']
            'arr' => [
                ['', 'object', '模型对象', '#table_name#model']
            ]
        ],
        'public_function' => [
//            '__construct' => [
//                'function' => '',
//                '#table_name#_model' => ['', 'object', '模型对象']
//            ],
            'BackEndData' => [
                'function' => '',
                'annotation' => '获取后台data的json',
                'return_annotation' => "@return array\r\n     * @throws TransformerException"
            ],
            'SingleData' => [
                'function' => '',
                'annotation' => '获取后台单条记录的数据',
                'return_annotation' => "@return array\r\n     * @throws ModelReflectionException\r\n     * @throws TransformerException\r\n     * @throws ModelException"
            ],
        ]
    ];

    /**
     * @param string $file_path
     * @param string $operation
     * @return $this
     * @throws \Exception
     */
    public function setColumnArr($file_path = '', $operation = '')
    {
        if(!empty($file_path)){
            $table_name = strtolower(str_replace('Transformer', '', substr($file_path, strrpos($file_path, '/')+1, (strrpos($file_path, '.') - 1 - strrpos($file_path, '/')))));
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

    /**
     * 生成构造参数
     * @param string $file_path
     * @param array $parameters
     * @return string
     */
    private function __genConstruct($file_path = '', $parameters = []){
        $code = '';
        if(!empty($file_path)){
            $table_name = strtolower(str_replace("Transformer", "", substr($file_path, strrpos($file_path, '/')+1, strrpos($file_path, '.') - strrpos($file_path, '/') - 1)));
            $real_parameter = '';
            $parameter_arr = [];
            foreach ($parameters['arr'] as $index => $item){
                if(strpos($item[3], '#table_name#') !== false){
                    $real_parameter = $item[3] = str_replace("#table_name#", $table_name . "_", $item[3]);
                    $parameter_arr[] = $item;
                }
            }
            $function_main = "\$this->" . $table_name . "_model = \$" . $real_parameter . ";\r\n        parent::__construct();";
            $code .= $this->genConstruct($function_main, [$parameter_arr]);
        }
        return $code;
    }

    /**
     * @param $file_path
     * @param array $parameters
     * @return string
     */
    public function genBackEndDataFunctionMain($file_path, $parameters = []){
        $code = '';
        if(!empty($file_path)){
            $table_name = strtolower(str_replace("Transformer", "", substr($file_path, strrpos($file_path, '/')+1, strrpos($file_path, '.') - strrpos($file_path, '/') - 1)));
            $code = "    try {";
            $code .= "\r\n            if(! \$this->" . $table_name . "_model instanceof " . ucfirst($table_name) . "Model)";
            $code .= "\r\n                throw new TransformerException(TransformerException::INSTANCE_IS_INVALID, TransformerException::INSTANCE_IS_INVALID_NO);";
            $code .= "\r\n\r\n            \$return = [];";
            $code .= "\r\n            if(is_array(\$this->" . $table_name . "_model->data)){";
            $code .= "\r\n                foreach (\$this->" . $table_name . "_model->data as \$index => \$" . $table_name . "_model){";
            $code .= "\r\n                    \$return[\$index] = \$" . $table_name . "_model;";
            $code .= "\r\n                }";
            $code .= "\r\n            }";
            $code .= "\r\n\r\n            return [";
            $code .= "\r\n                'data' => \$return,";
            $code .= "\r\n                'meta' => (array)\$this->" . $table_name . "_model->meta";
            $code .= "\r\n            ];";
            $code .= "\r\n        } catch (TransformerException \$e) {";
            $code .= "\r\n            throw \$e;";
            $code .= "\r\n        }";
        }
        return $code;
    }

    /**
     * @param $file_path
     * @param array $parameters
     * @return string
     */
    public function genSingleDataFunctionMain($file_path, $parameters = []){
        $code = '';
        if(!empty($file_path)){
            $table_name = strtolower(str_replace("Transformer", "", substr($file_path, strrpos($file_path, '/')+1, strrpos($file_path, '.') - strrpos($file_path, '/') - 1)));
            $code = "    try {";
            $code .= "\r\n            return \$this->_getData(\$this->" . $table_name . "_model);";
            $code .= "\r\n        } catch (ModelException \$e) {";
            $code .= "\r\n            throw \$e;";
            $code .= "\r\n        } catch (TransformerException \$e) {";
            $code .= "\r\n        throw \$e;";
            $code .= "\r\n        } catch (ModelReflectionException \$e) {";
            $code .= "\r\n            throw \$e;";
            $code .= "\r\n        }";
        }
        return $code;
    }

    public function generatedTransformerCode($file_path = ''){
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
                                $table_name = ucfirst(str_replace("Transformer", "Model", substr($file_path, strrpos($file_path, '/')+1, (strrpos($file_path, '.') - 1 - strrpos($file_path, '/')))));
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
                                $code .= $this->genServiceVariable($column_variable_type, [], $file_path);
                            }else{
                                $function_name_arr = [];
                                if(strpos($variable_name, '#table_name#') !== false){
                                    $table_name = strtolower(str_replace('Transformer', '', substr($file_path, strrpos($file_path, '/')+1, (strrpos($file_path, '.') - 1 - strrpos($file_path, '/')))));
                                    $variable_name = str_replace("#table_name#", $table_name . '_', $variable_name);
                                    foreach (explode('_', $variable_name) as $index => $value){
                                        $function_name_arr[] = ucfirst($value);
                                    }
                                    if(!empty($function_name_arr)){
//                                        $function_name = 'get' . implode('', $function_name_arr);
                                        $function_main = "    return \$this->exception instanceof Exception ? \$this->exception : #        (\$this->error === 0 ? \$this->" . $variable_name . " : \$this->getResult('error'));";
                                    }
                                }
                                $code .= $this->genVariable(substr($key, 0, stripos($key, '_')), [$variable_name => $element_arr], false);
                            }
                        }
                        break;
                    case 'construct':
                        unset($element['function']);
                        unset($element['annotation']);
                        $code .= $this->__genConstruct($file_path, $element);
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
                        break;
                }
            }
        }

        return str_replace("#", "\r\n", $code) . '}';
    }

}
