<?php


namespace ylCodeGenerator;


class GenerateException extends GenerateCode
{
    public $table_name = '';

    protected $_code_arr = [
        'start' => '<?php',
        'namespace' => 'models\Exception\Business',
        'use' => [
            'Exception'
        ],
        'class' => 'class #class_name# extends Exception {',
        'const_variable' => [
            '#table_name#_IS_NOT_EXISTS' => ['用户不存在', 'const', ''],
            '#table_name#_IS_NOT_EXISTS_NO' => ['400', 'const', ''],
            '#table_name#_CREATE_FAILURE' => ['用户创建失败', 'const', ''],
            '#table_name#_CREATE_FAILURE_NO' => ['422', 'const', ''],
            '#table_name#_EDIT_FAILURE' => ['用户编辑失败', 'const', ''],
            '#table_name#_EDIT_FAILURE_NO' => ['422', 'const', ''],
            '#table_name#_ID_IS_EMPTY' => ['用户编号不能为空', 'const', ''],
            '#table_name#_ID_IS_EMPTY_NO' => ['400', 'const', '']
        ]
    ];

    public function getTableIndex($file_path){
//        $table_info = $this->_getTablesInfo();
        $table_name_arr = [];
        array_walk($table_info, function($value, $idx) use(&$table_name_arr){
            array_push($table_name_arr, $value['TABLE_NAME']);
        });

        $file_name = strtolower(substr($file_path, strrpos($file_path, '/')+1, (strrpos($file_path, '.') - 1 - strrpos($file_path, '/'))));
        $table_name = str_replace('exception', '', $file_name);

        return array_search($table_name, $table_name_arr);
    }

    public function generatedExceptionCode($file_path = ''){
        $code = '';
        $this->table_name = strtolower(substr($file_path, strrpos($file_path, '/') + 1, strrpos($file_path, 'Exception') - strrpos($file_path, '/') - 1));

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
                                $table_name = ucfirst(str_replace("exception", "", substr($file_path, strrpos($file_path, '/')+1, (strrpos($file_path, '.') - 1 - strrpos($file_path, '/')))));
                                $element[$id] = str_replace('#table_name#', $table_name, $use_namespace);
                            }
                        }
                        $code .= $this->genUseCode($element);
                        break;
                    case 'class':
                        $code .= $this->genClass($element, $file_path);
                        break;
                    case 'const_variable':
                        foreach($element as $variable_name => $element_arr){
                            $code .= $this->genConstVariable($variable_name, $element_arr, $file_path);
                        }
                        break;
                }
            }
        }
        return str_replace("#", "\r\n", $code) . '}';
    }

    public function genConstVariable($variable_name = '', $parameters_arr = [], $file_path = ''){
        $variable_name = strtoupper(str_replace('#table_name#', $this->table_name, $variable_name));
        return $this->genVariable('const', [$variable_name => $parameters_arr], false);
    }

}
