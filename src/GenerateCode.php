<?php


namespace ylCodeGenerator;

use PDO;
use Exception;


class GenerateCode
{
    protected $_table_fields = [];

    protected $_code_arr = [];

    protected $_file_head_note;

    protected $_column_type_input_type_arr = [
        'int' => 'text',
        'varchar' => 'text',
        'tinyint' => 'boolean',
        'text' => 'textarea',
        'date' => 'text',
    ];

    protected $_column_type_validator_type_arr = [
        'int' => 'string',
        'varchar' => 'string',
        'float' => 'float',
        'text' => 'string,'
    ];

    protected $_error_tips_operation = [
        'CREATE' => '创建',
        'EDIT' => '编辑',
        'DELETE' => '删除',
    ];

    protected $_error_tips_operation_result = [
        'SUCCESS' => '成功',
    ];

    /**
     * @param $file_head_note
     * @return $this
     */
    public function setFileHeadNote($file_head_note){
        $this->_file_head_note = $file_head_note;
        return $this;
    }

    /**
     * 生成代码头
     * @param string $start 代码头，必须是以<?php开头，调用该方法还会把文件注释头给加进去
     * @return string
     */
    public function genStartCode($start = ''){
        $code = '';
        if(!empty($start) && stripos('<?php', $start) === 0){
            $code = $start . "\r\n" . str_replace(['#date#', '#time#'], [date("Y-m-d", time()), date("H:i:s", time())], $this->_file_head_note) . "\r\n";
        }
        return $code;
    }

    /**
     * 生成namespace
     * @param string $namespace 命名空间
     * @return string
     */
    public function genNamespaceCode($namespace = ''){
        $code = '';
        if(!empty($namespace)){
            $code = 'namespace ' . $namespace . ";\r\n\r\n";
        }
        return $code;
    }

    /**
     * 生成use
     * @param mixed $use 可以是null，也可以是string，也可以是array 如果是null返回空，如果是数组就遍历返回字符串，如果是字符串直接返回use+字符串
     * @return string
     */
    public function genUseCode($use = null){
        $code = '';
        if($use !== null){
            if(is_array($use)){
                foreach($use as $use_file_namespace){
                    $code .= 'use ' . $use_file_namespace . ";\r\n";
                }
                $code .= "\r\n";
            }else{
                $code .= 'use ' . $use . "; \r\n";
            }
        }
        return $code;
    }

    /**
     * 生成类文件定义 class class_name extends base_class_name
     * @param string $class_name class #class_name#或者class #class_name# extends base_class_name
     * @param string $file_path 文件名 用以拆分得到class_name，并替换$class_name里的#class_name#
     * @param bool $is_controller
     * @return string
     */
    public function genClass($class_name = '', $file_path = '', $is_controller = false){
        $code = '';
        if(!empty($file_path) && !empty($class_name)){
            $real_class_name = substr($file_path, strrpos($file_path, '/')+1, (strrpos($file_path, '.') - 1 - strrpos($file_path, '/')));
            if($is_controller === true &&strpos($real_class_name, '_') !== false){
                $real_class_name_arr = explode('_', $real_class_name);
                foreach($real_class_name_arr as $k => $v){
                    $real_class_name_array[] = $k == 0 ? $v : ucfirst($v);
                }
                $real_class_name = implode('', $real_class_name_array);
            }
            $code = "\r\n" . str_replace('#class_name#', $real_class_name, $class_name) . "\r\n";
        }
        return $code;
    }

    public function genSwgAnnotation($table_name = ''){
        $code = '';
        if(! empty($table_name)){
            $table_fields = $this->_getTableConstruct(strtolower($table_name));
            if(!empty($table_fields)){
                $code = "*#/**";
                $code .= "*# * @SWG\\Swagger(";
                $code .= "*# *     @SWG\\Definition(";
                $code .= "*# *          type=\"object\",";
                $code .= "*# *          definition=\"" . strtolower($table_name) . "SingleData\",";
                $code .= "*# *          required={\"errno\", \"errmsg\", \"data\"},";
                $code .= "*# *          @SWG\\Property(property=\"errno\", type=\"integer\", format=\"int32\", description=\"编码\"),";
                $code .= "*# *          @SWG\\Property(property=\"errmsg\", type=\"string\", format=\"string\", description=\"错误提示信息\"),";
                $code .= "*# *          @SWG\\Property(property=\"result\", type=\"object\", ref=\"{*}/definitions/" . strtolower($table_name) . "SingleObj\")";
                $code .= "*# *     ),";
                $code .= "*# *     @SWG\\Definition(";
                $code .= "*# *          type=\"object\",";
                $code .= "*# *          definition=\"" . strtolower($table_name) . "SingleObj\",";
                $code .= "*# *          @SWG\\Property(property=\"data\", type=\"object\", ref=\"{*}/definitions/" . strtolower($table_name) . "Single\")";
                $code .= "*# *     ),";
                $code .= "*# *     @SWG\\Definition(";
                $code .= "*# *          type=\"object\",";
                $code .= "*# *          definition=\"" . strtolower($table_name) . "ListData\",";
                $code .= "*# *          required={\"errno\", \"errmsg\", \"result\"},";
                $code .= "*# *          @SWG\\Property(property=\"errno\", type=\"integer\", format=\"int32\", description=\"编码\"),";
                $code .= "*# *          @SWG\\Property(property=\"errmsg\", type=\"string\", format=\"string\", description=\"错误提示信息\"),";
                $code .= "*# *          @SWG\\Property(property=\"result\", ref=\"{*}/definitions/" . strtolower($table_name) . "ListObj\"),";
                $code .= "*# *     ),";
                $code .= "*# *     @SWG\\Definition(";
                $code .= "*# *          type=\"object\",";
                $code .= "*# *          definition=\"" . strtolower($table_name) . "ListObj\",";
                $code .= "*# *          @SWG\\Property(property=\"data\", type=\"object\", ref=\"{*}/definitions/" . strtolower($table_name) . "Data\"),";
                $code .= "*# *          @SWG\\Property(property=\"meta\", ref=\"{*}/definitions/meta\")";
                $code .= "*# *     ),";
                $code .= "*# *     @SWG\\Definition(";
                $code .= "*# *          type=\"array\",";
                $code .= "*# *          definition=\"" . strtolower($table_name) . "Data\",";
                $code .= "*# *          @SWG\\Items(";
                $code .= "*# *              title=\"data\", ref=\"{*}/definitions/" . strtolower($table_name) . "Single\"";
                $code .= "*# *          )";
                $code .= "*# *     ),";
                $code .= "*# *     @SWG\\Definition(";
                $code .= "*# *          type=\"object\",";
                $code .= "*# *          definition=\"" . strtolower($table_name) . "Single\",";
                foreach ($table_fields as $idx => $val){
                    switch ($val["Type"]){
                        case strpos($val["Type"], "int") !== false:
                            $code .= "*# *          @SWG\\Property(property=\"" . $val["Field"] . "\", type=\"integer\", format=\"int32\", ";
                            break;
                        default:
                            $code .= "*# *          @SWG\\Property(property=\"" . $val["Field"] . "\", type=\"string\", format=\"string\", ";
                            break;
                    }
                    $code .= " description=\"" . $val["Comment"] . "\"),";
                }
                $code .= "*# *     )";
                $code .= "*# * )";
                $code .= "*# */";
            }
        }

        return str_replace('*#', "\r\n", $code);
    }

    /**
     * @param string $construct_code
     * @param array $parameter_arr 构造函数所需的参数[parameter1 => [value1, type1, annotation1], parameter2 => [value2, type2, annotation2], ……]
     * @param string $annotation
     * @return string
     */
    public function genConstruct($construct_code = '', $parameter_arr = [], $annotation = ''){
        $code = '';
        if(!empty($construct_code)){
            if (empty($annotation)) {
                list($parameter, $annotation) = $this->genFunctionAnnotation($parameter_arr);
//                $annotation = $annotation . "\r\n";
            } else {
                $parameter = '';
            }
            $code .= $annotation . "    public function __construct(" . $parameter . "){\r\n        " . $construct_code . "\r\n" . "    }\r\n\r\n";
        }
        return $code;
    }


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
     * 当函数没有参数的时候
     * @param string $annotation
     * @return string
     */
    public function genAnnotation($annotation = ''){
        $code = '';
        if(!empty($annotation)){
            $code = "    /**\r\n" . "     * " . $annotation . "\r\n     */\r\n";
        }
        return $code;
    }

    /**
     * 生成function的注释
     * @param array $parameter_arr 构造函数所需的参数[parameter1 => [value1, type1, annotation1], parameter2 => [value2, type2, annotation2], ……]
     * @param string $function 函数名
     * @param string $function_annotation 函数说明
     * @return array
     */
    public function genFunctionAnnotation($parameter_arr = [], $function = 'constructor', $function_annotation = ''){
        $code_arr = ['', ''];
        if(!empty($parameter_arr)){
            $parameter_code_arr = $annotation_code_arr = [];
            foreach($parameter_arr as $parameter => $value_type_annotation_arr){
                if(is_array($value_type_annotation_arr)){
                    foreach ($value_type_annotation_arr as $index => $annotation_array){
                        if(method_exists($this, 'getColumnArr') && $annotation_array[0] == '#column#'){
//                            $annotation_code_arr[] = "     * @param $";
                            $column_annotation_arr = $this->getColumnArr();
//                            $column_annotation_arr = [];
                            foreach ($column_annotation_arr as $k => $column_info){
                                switch($column_info['Type']) {
                                    case strpos($column_info['Type'], 'int') !== false:
                                        if($function !== 'create'){
                                            if(($function === 'delete' || $function === 'update')){
                                                $parameter_code_arr[] = '$primary_key = 0';
                                                $type = 'int';
                                                $annotation_code_arr[] = "     * @param " . $type . " \$" . ($column_info['Extra'] === 'auto_increment' ? 'primary_key' : $column_info['Field']) . " " . $column_info['Comment'];
                                            }
                                        }else{
                                            if($column_info['Field'] != 'id'){
                                                $parameter_code_arr[] = '$' . $column_info['Field'] . ' = 0';
                                                $type = 'int';
                                                $annotation_code_arr[] = "     * @param " . $type . " \$" . $column_info['Field'] . " " . $column_info['Comment'];
                                            }
                                        }
                                        break;
                                    case /*strpos($column_info['Type'], 'timestamp') !== false ||*/
                                        strpos($column_info['Type'], 'varchar') !== false ||
                                        strpos($column_info['Type'], 'text') !== false:
                                        if($function != 'update' && $function != 'delete' && $function != 'getOne'){
                                            $parameter_code_arr[] = '$' . $column_info['Field'] . ' = ""';
                                            $type = 'string';
                                            $annotation_code_arr[] = "     * @param " . $type . " \$" . $column_info['Field'] . " " . $column_info['Comment'];
                                        }
                                        break;
                                    case strpos($column_info['Type'], 'decimal') !== false:
                                        if($function != 'update' && $function != 'delete' && $function != 'getOne'){
                                            $parameter_code_arr[] = '$' . $column_info['Field'] . ' = ' . $column_info['Default'] . '';
                                            $annotation_code_arr[] = "     * @param " . $type . " \$" . $column_info['Field'] . " " . $column_info['Comment'];
                                        }
                                        break;
                                    case strpos($column_info['Type'], 'timestamp') !== false:
                                        break;
                                    default:
                                        if($function !== 'update' && $function !== 'delete'){
                                            $parameter_code_arr[] = '$' . $column_info['Field'] . ' = ""';
                                            $type = 'string';
                                            $annotation_code_arr[] = "     * @param " . $type . " \$" . $column_info['Field'] . " " . $column_info['Comment'];
                                        }
                                        break;
                                }

                            }
                        }else{
                            $type = '';
                            switch($annotation_array[1]){
                                case 'array':
                                    $parameter_code_arr[] = '$' . $annotation_array[3] . ' = []';
                                    $type = 'array';
                                    break;
                                case 'boolean':
                                    $parameter_code_arr[] = '$' . $annotation_array[3] . ' = ' . (string)$annotation_array[0];
                                    $type = 'bool';
                                    break;
                                case 'integer':
                                    $parameter_code_arr[] = '$' . $annotation_array[3] . ' = ' . (integer)$annotation_array[0];
                                    $type = 'int';
                                    break;
                                case 'float':
                                    $parameter_code_arr[] = '$' . $annotation_array[3] . ' = ' . (float)$annotation_array[0];
                                    $type = 'float';
                                    break;
                                case 'object':
                                    $class_name = '';
                                    foreach (explode('_', $annotation_array[3]) as $idx => $word){
                                        $class_name .= ucfirst($word);
                                    }

                                    if(!empty($class_name)){
                                        $type = $class_name;
                                        $parameter_code_arr[] = $class_name . ' $' . $annotation_array[3];
                                    }
                                    break;
                                default:
                                    $parameter_code_arr[] = '$' . $annotation_array[3] . ' = ""';
                                    $type = 'string';
                                    break;
                            }
                            //拼接注释 * @param string $parameter1 参数1是个字符串，这是它的注释
                            if($type)
                                $annotation_code_arr[] = "     * @param " . $type . " $" . $annotation_array[3] . " " . $annotation_array[2];
                        }
                    }
                }else{
//                    var_dump($parameter_arr);
//                    var_dump($value_type_annotation_arr);

                    switch($value_type_annotation_arr[1]){
                        case 'array':
                            $parameter_code_arr[] = '$' . $parameter . ' = []';
                            break;
                        case 'boolean':
                            $parameter_code_arr[] = '$' . $parameter . ' = ' . (string)$value_type_annotation_arr[0];
                            break;
                        case 'integer':
                            $parameter_code_arr[] = '$' . $parameter . ' = ' . (integer)$value_type_annotation_arr[0];
                            break;
                        case 'float':
                            $parameter_code_arr[] = '$' . $parameter . ' = ' . (float)$value_type_annotation_arr[0];
                            break;
                        default:
                            $parameter_code_arr[] = '$' . $parameter . ' = ""';
                            break;
                    }
                    //拼接注释 * @param string $parameter1 参数1是个字符串，这是它的注释
                    $annotation_code_arr[] = "     * @param " . $value_type_annotation_arr[1] . " $" . $parameter . " " . $value_type_annotation_arr[2];
                }
                /*$parameter_code_arr[] = '$' . $parameter . ' = ' . ($value_type_annotation_arr[1] == 'array' ?
                        '[]' :
                        (
                            $value_type_annotation_arr[1] == 'boolean' ?
                                (string)$value_type_annotation_arr[0] :
                                '"' . $value_type_annotation_arr[0] . '"'
                        )
                    );*/
            }
            $code_arr[0] = implode(', ', $parameter_code_arr);
            $code_arr[1] = "    /**\r\n     * " . ($function == 'constructor' ? "class constructor" : $function).
                ". \r\n" . (!empty($function_annotation) ? "     * " . $function_annotation . "\r\n" : "") .
                implode("\r\n", $annotation_code_arr) . "\r\n     */\r\n";
        }
        return $code_arr;
    }

    /**
     * 生成function
     * @param string $type 函数类型，可选值public protected private
     * @param string $function_name 函数名
     * @param array $parameter_arr 参数数组 [[parameter1 => [default1, type1, annotation1], [parameter2 => [default2, type2, annotation2]], ……]]
     * @param string $function 函数体 #表示换行
     * @param string $annotation 注释 #表示换行
     * @param string $return_annotation
     * @return string
     */
    public function genFunction($type = 'public', $function_name = '', $parameter_arr = [], $function = '', $annotation = '', $return_annotation = ''){
        $code = '';
        if (! empty($function_name)) {
            $parameter_array = [];
            foreach($parameter_arr as $parameter_name => $default_type_annotation_arr){
                foreach($default_type_annotation_arr as $parameter_name => $value_arr){
                    $parameter_array[$parameter_name] = $value_arr;
                }
            }
            if (empty($annotation)) {
                list($parameter, $annotation) = $this->genFunctionAnnotation($parameter_array, $function_name, $return_annotation);
            } else {
                $parameter = '';
            };

            $code = $annotation . "    " . ($function_name == 'getInstance' ? 'static ' : '') . $type . " function ". ($type == 'private' ? '__' : ($type == 'protected' ? '_' : '')) .
                $function_name .
                "(" . ($function_name == 'getInstance' ? '' : $parameter) . ") {";
            $function_arr = '';
            if (!empty($function))
                $function_arr = explode('#', $function);
            if(is_array($function_arr)){
                $code .= "\r\n";
                foreach ($function_arr as $line => $string){
                    if(strpos($string,'$this->_created_at')  !==false || strpos($string,'$this->_updated_at')  !==false){
                        $code .= $this->__genTimeCode(key($parameter_array));
                        $code .= "else{\r\n       " .$string . "\r\n       }\r\n";

                    }elseif(strpos($string,'$this->_thumb') !==false || strpos($string,'$this->_avatar') !==false || (strpos($string,'$this->_album') !==false && !strpos($string,'$this->_album_id') !==false) && $function_name){
                        $code .= $this->__genSerializeCode(key($parameter_array),$function_name);
                        $code .= "else{\r\n       " .$string . "\r\n       }\r\n";
                    }else
                    {
                        $code .= "    " . $string . "\r\n";
                    }
                }
                $code .= "    ";
            }
            $code .= "}\r\n\r\n";
        }

        return $code;
    }

    /**
     * 生成变量注释代码
     * @param string $variable_annotation 变量注释字符串，若需换行，在每行前面必须加上#
     * @return string
     */
    public function genVariableAnnotation($variable_annotation = ''){
        $code = '';
        if(!empty($variable_annotation)){
            $code = "    /** ". "\r\n";
            if(strpos($variable_annotation , '#') !== ''){
                $variable_annotation_arr = explode('#', $variable_annotation);
                foreach($variable_annotation_arr as $line => $string){
                    $code .= "     * " . substr(str_replace("\r\n", "", $string), stripos(str_replace("\r\n", "", $string), '#')) . "\r\n";
                }
            }else{
                $code .= "     *" . $variable_annotation . "\r\n";
            }
            $code .= "     */" . "\r\n";
        }
        return $code;
    }

    /**
     * 生成变量代码
     * @param string $type 变量类型，可选值public protected private
     * @param array $variable 变量数组 [变量名1 => [默认值1, 类型1, 注释1]]
     * @param bool $is_field 是否是生成表单时所需的变量数组，默认false
     * @param string $file_path 若是生成表单时所需的变量数组，则必须传入要生成的文件名，以此判断到底要获取哪张表的表结构
     * @return string
     * @throws Exception
     */
    public function genVariable($type = 'public', $variable = [], $is_field = false, $file_path = ''){
        $code = '';
        $default_type_annotation_arr[1] = 'string';
        if (empty($variable))
            throw new Exception("无效变量");

        if(!$this->_table_fields && !empty($file_path)){
            $table_name = substr($file_path, strrpos($file_path, '/')+1, (strrpos($file_path, 'Form.php') - 1 - strrpos($file_path, '/')));
            $this->_table_fields = $this->_getTableConstruct($table_name);
        }
        //生成普通变量
        foreach($variable as $variable_name => $default_type_annotation_arr){
            if(strpos($variable_name, 'primary_key_arr') !== false ||
                strpos($variable_name, 'auto_increment_key_arr') !== false ||
                strpos($variable_name, 'unique_key_arr') !== false
            ){
                $default_value = [];
                foreach ($this->_table_fields as $field => $value){
                    if ($variable_name == 'primary_key_arr' && $value['Key'] == 'PRI') {
                        $default_value[] = $value['Field'];
                    } else if ($variable_name == 'auto_increment_key_arr' && $value['Extra'] == 'auto_increment') {
                        $default_value[] = $value['Field'];
                    } else if ($variable_name == 'unique_key_arr' && $value['Key'] == 'UNI') {
                        $default_value[] = $value['Field'];
                    }
                }
                if(!empty($default_value)){
                    if(!empty($default_type_annotation_arr[0])){
                        if(preg_match_all('/"(.*)"/Usi', $default_type_annotation_arr[0], $preg_match_arr)){
                            $default_type_annotation_arr[0] = implode(',', array_merge($preg_match_arr[1], $default_value));
                        }
                    }else{
                        $default_type_annotation_arr[0] = implode('","', $default_value);
                    }

                }
            }

            $code = $this->genVariableAnnotation($default_type_annotation_arr[2]);
            if($type == 'const'){
                $code .= '    const ' . $variable_name . ' = ';
            }else{
                $code .= '    ' . ($variable_name == 'instance' ? 'static ' : '') . $type . ' $' . ($type == 'protected' ? '_' : ($type == 'private' ? '__' : '')) . $variable_name . ' = ';
            }

            if(isset($default_type_annotation_arr[0]) && $is_field === false){
                switch ($default_type_annotation_arr[1]){
                    case 'array':
                        $value = empty($default_type_annotation_arr[0]) ? '[]' : '["' . (strpos($default_type_annotation_arr[0], ',') !== false ? str_replace(',', '","', $default_type_annotation_arr[0]) : $default_type_annotation_arr[0]) . '"]';
                        break;
                    case 'static':
                    case 'object':
                        $value = empty($default_type_annotation_arr[0]) ? 'null' : $default_type_annotation_arr[0];
                        break;
                    case 'integer':
                        $value = empty($default_type_annotation_arr[0]) ? 0 : intval($default_type_annotation_arr[0]);
                        break;
                    case 'string' || 'const':
                        $value = "'$default_type_annotation_arr[0]'";
                        break;
                    default:
                        $value = '""';
                        break;
                }
            }else{
                $value = '""';
            }
            $code .= isset($default_type_annotation_arr[0]) && $is_field === false ?
                $value . ';':
                '""';
        }
        //生成表单生成用变量
        if($is_field == true && !empty($file_path)){
            $code_arr = $foreign_relationship_sql = $foreign_relationship_result_arr = $foreign_relationship_match_key_value = [];
            $table_name = substr($file_path, strrpos($file_path, '/')+1, (strrpos($file_path, 'Form') - 1 - strrpos($file_path, '/')));

            //查询外键
            $foreign_relationship = $this->_getForeignRelationship($table_name);
            foreach($foreign_relationship as $index => $value){
                if(preg_match_all('<"(.*)":"(.*)">', $value['comment'], $foreign_table_column_arr) !== false){
                    $foreign_relationship_sql[$value['column']] = "select `" . $value['foreign_column'] . "`, `" . $foreign_table_column_arr[2][0] .
                        "`" . ($value['column'] == "parent_id" ? ", concat(`path`, '', `id`) as `t_path`" : "") . " from `" . $foreign_table_column_arr[1][0] . "` order by `" . $foreign_table_column_arr[2][0] . "` asc";
                    $foreign_relationship_match_key_value[$value['column']] = $foreign_table_column_arr[2][0];
                }
            }

//                $table_fields = $this->_getTableConstruct(strtolower($table_name));
//                foreach($table_fields as $key => $val){
            foreach($this->_table_fields as $key => $val){
                $type = strpos($val['Type'], "(") !== false ? substr($val['Type'], 0, stripos($val['Type'], '(')) : $val['Type'];
                $length = strpos($val['Type'], "(") !== false ? substr($val['Type'], strrpos($val['Type'], "(") + 1, (strrpos($val['Type'], ")") - strrpos($val['Type'], "(")) - 1) : 0;
                /*$real_comment = substr(
                    $val['Comment'],
                    0,
                    (strpos($val['Comment'], '.<') !== false ?
                        (stripos($val['Comment'], '.<') - 2) :
                        (strpos($val['Comment'], '.{') !== false ?
                            (stripos($val['Comment'], '.{') - 2) :
                            strlen($val['Comment'])
                        )
                    )
                );*/
                $label_name = (stripos($val['Comment'], '(') !== false && stripos($val['Comment'], ')') !== false && (strpos($val['Type'],'tinyint') !== false || strpos($val['Comment'],'resource') !== false) ? strstr($val['Comment'], "(",true) : substr(
                    $val['Comment'],
                    0,
                    (stripos($val['Comment'], '.<') !== false ?
                        stripos($val['Comment'], '.<') :
                        (stripos($val['Comment'], '.{<') !== false ? stripos($val['Comment'], '.{<') : strlen($val['Comment']))
                    )
                ));
                $real_comment = preg_replace('/<|>|"|\'/', '', $val['Comment']);
                /*$value_array = [];
                preg_match_all('/<(.*):(.*)>/Usi', str_replace([' "', '\'', '"'], '"', $val['Comment']), $preg_foreign);
                if(count($preg_foreign[1]) == 1 && count($preg_foreign[2]) == 1 && $preg_foreign[0][0] == substr($val['Comment'], stripos($val['Comment'], $preg_foreign[0][0]))){
                    $column = str_replace(['"', '\''], '`', $preg_foreign[2][0]);
                    $sql = "select " . $column . ", `name` from " . str_replace(['"', '\''], '`', $preg_foreign[1][0]);
                    $result = $this->_getResult($sql);
                    foreach ($result as $index => $v){
                        $value_array[] = '"' . $v[str_replace(['\'', '"'], '', $preg_foreign[2][0])] . '"=>"' . $v['name'] . '"';
                    }
                }*/
                $value_string = '""';
//                    $value_array = $real_value_array = [];
//                    if(isset($foreign_relationship_sql[$val['Field']])){
//                        $value_array[$val['Field']] = "'" . $foreign_relationship_sql[$val['Field']] . "'";
//                        $foreign_relationship_result = $this->_getResult($foreign_relationship_sql[$val['Field']]);
//                        foreach ($foreign_relationship_result as $k => $v){
////                            $value_array[$v[$foreign_relationship_match_key_value[$val['Field']]]] = $v['name'];
//                            $path_prefix = '';
//                            if(isset($v['t_path'])){
//                                $path_prefix = count(explode('-', $v['t_path'])) > 2 ? '|-' : '';
//                                for ($i = 2; $i < count(explode('-', $v['t_path'])); $i++){
//                                    $path_prefix .= '-';
//                                }
//                            }
//                            $real_value_array[] = $v[$foreign_relationship_match_key_value[$val['Field']]] . " => \"" . $path_prefix .  $v['0']/*$v['name']*/ . "\"";
//                        }
//                        if(!empty($real_value_array)){
//                            $value_string = "[" . implode(",", $real_value_array) . "]";
//                        }
//                    }
                if(isset($foreign_relationship_sql[$val['Field']])){
                    $table_st = stripos($foreign_relationship_sql[$val['Field']],"from");
                    $table_ed = stripos($foreign_relationship_sql[$val['Field']],"order");
                    if(!($table_st == false||$table_ed == false) || !$table_st >= $table_ed)
                        $foreign_table_name = substr($foreign_relationship_sql[$val['Field']],($table_st + 6),($table_ed - $table_st - 8));
                    $field_st = stripos($foreign_relationship_sql[$val['Field']],"select");
                    $field_ed = stripos($foreign_relationship_sql[$val['Field']],"from");
                    if(!($field_st == false||$field_ed == false) || !$field_st >= $field_ed)
                        $field_name = substr($foreign_relationship_sql[$val['Field']],($field_st + 7),($field_ed - $field_st - 8));
                    $value_string = "[\"SELECT ".$field_name." FROM `".$foreign_table_name."`\"]";
                }

                if($val['Field'] != 'created_at' /*&& $val['Extra'] != 'auto_increment'*/ && $val['Field'] != 'updated_at'){
                    $code_arr[$val['Field']] = "#        '" . $val['Field'] . "' => #            [#".
                        "            'label' => '" . $label_name . "',".
                        "#            'name' => '" . $val['Field'] . "',".
                        "#            'type' => '" . /*(!empty($value_array) ? 'select' : */
                        /*($val['Extra'] != 'auto_increment' ?
                            (
                                $value_string !== '""' ? 'select' :
                                    (isset($this->_column_type_input_type_arr[$type]) ?
                                        $this->_column_type_input_type_arr[$type] :
                                        (strpos($val['Comment'], 'album') !== false || strpos($val['Comment'], 'thumb') !== false ? $val['Field'] : 'text')
                                    )
                            ) :
                            'hidden'
                        )*/
                        ($val['Extra'] != 'auto_increment' ?
                            ($val['Field'] == 'path' ?
                                'hidden' :
                                (strpos($val['Comment'], 'resource') !== false ? 'resource' :
                                    (
                                    $value_string !== '""' && strpos($val['Comment'], 'album') === false ? 'select' :
                                        (
                                        strpos($val['Comment'], 'album') !== false || strpos($val['Comment'], 'thumb') !== false ?
                                            $val['Field'] :

                                            (
                                            isset($this->_column_type_input_type_arr[$type]) ?
                                                $this->_column_type_input_type_arr[$type] :
                                                'text'
                                            )
                                        )
                                    )
                                )
                            )
                            :
                            'hidden'
                        )
                        //
                        . "',".
                        "#            'attr' => [".
                        "#                'required' => " . ($val['Null'] == 'YES' ? 'false' : 'true') . ",".
                        "#                'placeholder' => '请输入" . $real_comment . "',".
                        "#                'class' => 'form-control',".
                        "#                'data-error' => 'data-error'".
                        (strpos($val['Comment'], 'max_file_num') !== false ? ",#                'max_file_num' => " . substr($val['Comment'], stripos($val['Comment'], ':') + 1, stripos($val['Comment'], '.') - strpos($val['Comment'], ':') - 1) : "") .
                        (strpos($val['Comment'], 'resource') !== false ? (strpos($val['Comment'], 'multiple') !== false ? ",#                'multiple' => true " : ",#                'multiple' => false ") : " ") .                            "#            ],".
                        "#            'validator' => [".
                        "#                'type' => '" . (isset($this->_column_type_validator_type_arr[$type]) ? $this->_column_type_validator_type_arr[$type] : 'string') . "',".
                        "#            ],".
                        "#            'value' => " . (!empty($value_string) ? $value_string : '""') .",".
                        "#            'error' => ''".
                        "#        ],";
                    switch($val['Field']){
                        case 'mobile':
                            str_replace(
                                ",],\r\n'value' => '',\r\n",
                                "'min' => '" . $length . "',\r\n,'max' => '" . $length . "',\r\n, 'customer' => ['mobile'],\r\n],\r\n'value' => '',\r\n",
                                $code_arr[$val['Field']]
                            );
                            break;
                        case 'password':
                            str_replace(
                                ",],\r\n'value' => '',\r\n",
                                "'min' => '6',\r\n,'max' => '16',\r\n, 'customer' => ['password'],\r\n],\r\n'value' => '',\r\n",
                                $code_arr[$val['Field']]
                            );
                            $code_arr['repeat_password'] = $code_arr[$val['Field']];
                            str_replace(
                                ["'label' => '密码'", "'placeholder' => '请输入密码'", "'customer' => ['password']"],
                                ["'label' => '重复密码'", "'placeholder' => '请重复输入密码'", "'customer' => ['password',\r\n,'repeat']"],
                                $code_arr['repeat_password']
                            );
                            break;
                    }
                }
            }

            $code_arr['button'] = "#        'button' => [".
                "#            'submit' => [".
                "#                'label' => '提交',".
                "#                'name' => 'submit',".
                "#                'type' => 'submit',".
                "#                'attr' => [".
                "#                    'class' => 'btn btn-sm btn-primary'".
                "#                ]".
                "#            ],".
                "#            'reset' => [".
                "#                'label' => '重置',".
                "#                'name' => 'reset',".
                "#                'type' => 'button',".
                "#                'attr' => [".
                "#                    'class' => 'btn btn-sm btn-default'".
                "#                ]".
                "#            ]".
                "#        ]".
                "#    ]".
                "#\r\n";

            $code = substr($code, 0, strrpos($code, '""')) . '[' . substr(
                    str_replace(
                        "\r\n\r\n",
                        "\r\n",
                        implode("                    ", $code_arr)
                    ),
                    0,
                    strrpos(
                        str_replace(
                            "\r\n\r\n", "\r\n",
                            implode("                    ", $code_arr)
                        ),
                        "\r\n"
                    )
                ) . ($default_type_annotation_arr[1] == 'array' ? ';' :'";') . "\r\n";
//                $code = '[' . substr($code, 1, mb_strlen($code)-1) . ']';
        }

        return $code . "##";
    }

    /**
     * @param string $type 变量类型，可选值public protected private
     * @param array $validator 变量数组 [变量名1 => [默认值1, 类型1, 注释1]], 当生成字段变量时该参数为空
     * @param string $file_path 当生成字段变量时该参数则必须传入要生成的文件名，以此判断到底要获取哪张表的表结构
     * @return string
     * @throws Exception
     */
    public function genServiceVariable($type = 'protected', $validator = [], $file_path = ''){
        $code = '';
        if(empty($validator) && !empty($file_path)){
            $code_arr = [];
            $table_name = substr($file_path, strrpos($file_path, '/')+1, (strrpos($file_path, '.') - 1 - strrpos($file_path, '/')));

            $table_fields = $this->_getTableConstruct(strtolower($table_name));
            foreach($table_fields as $key => $val){
                $code_arr[] = $this->genVariableAnnotation($val['Comment']) . "    " .
//                    $type . " " . ($type == 'protected' ? '$_' : ($type == 'private' ? '$__' : '$')) .
                    $type . " \$_"  .
                    $val['Field'] . ' = ' . ($val['Null'] == 'NO' ? 'null' : '""') . ';' . "\r\n";
                ;
                $function_name_set = $this->__genFunctionName($val['Field'], 'set');
                $function_name_get = $this->__genFunctionName($val['Field'], 'get');
                $parameter_arr = [[
                    $val['Field'] => [
                        ($val['Null'] == 'NO' ? 'null' : ($val['Default'] ? $val['Default'] : '')),
                        ($val['Null'] == 'NO' ? 'boolean' : (isset($this->_column_type_validator_type_arr[$val['Type']]) ? $this->_column_type_validator_type_arr[$val['Type']] : 'string')),
                        $val['Comment']
                    ]
                ]];
                $function_main = $this->__genFunctionMain('protected', $val['Field'], (in_array($val['Field'], ['thumb', 'avatar', 'album']) ? '_' . $val['Field'] : ''), 'set', $table_name);
                $code_arr[] = $this->genFunction('public', $function_name_set, $parameter_arr, $function_main);
//                $code_arr[] = $this->genFunction('public', $function_name_get, $parameter_arr, $function_main);
                $code_arr[] = $this->genGetFunctionMain('public', ["_" . $val['Field']], $function_name_get);
            }

            //查找外键关系，并将关联字段赋值给对应的model
            $table_name = substr(strtolower($table_name), 0, strpos(strtolower($table_name), 'service'));
            $sql = "SELECT * FROM `foreign_relationship` WHERE `table` = '" . $table_name . "'";
            $foreign_relationship = $this->_getResult($sql);
            $foreign_relationship_table_prefix = [];
            $foreign_relationship_table_prefix_match_arr = [];
            $i = $j = 1;
            $table_prefix = substr($table_name, 0, 1);
            foreach ($foreign_relationship as $row => $value){
                if(preg_match_all('/<"(.*)":"(.*)">/Usi', $value['comment'], $comment_arr)){
                    $comment = $value['table'] .'表的' . $value['column'] . '字段关联' . $comment_arr[1][0] . '表的' . $comment_arr[2][0] . '字段';
                    if(!isset($foreign_relationship_table_prefix[$value['column']])){
//                        $foreign_relationship_table_prefix[$value['column']] = $comment_arr[1][0] != $table_name ? substr($comment_arr[1][0], 0, 1) :
//                            substr($comment_arr[1][0], 0, 1) . '_1';
                        // 自己连自己
                        if($comment_arr[1][0] == $table_name){
                            $foreign_relationship_table_prefix[$value['column']] = substr($comment_arr[1][0], 0, 1) . '_' . $i++;
                        }else{
                            // 表前缀一致
                            if(substr($comment_arr[1][0], 0, 1) == $table_prefix){
                                $foreign_relationship_table_prefix[$value['column']] = substr($comment_arr[1][0], 0, 1) . '_' . $j++;
                            }else{
                                $foreign_relationship_table_prefix[$value['column']] = substr($comment_arr[1][0], 0, 1);
                            }
                        }
                        $foreign_relationship_table_prefix_match_arr[$comment_arr[1][0]][] = $value['column'];
                    }else{
                        if(!in_array($value['column'], $foreign_relationship_table_prefix_match_arr[$comment_arr[1][0]])){
                            $foreign_relationship_table_prefix_match_arr[$comment_arr[1][0]][] = $value['column'];
                        }
                        $foreign_relationship_table_prefix[$value['column']] =
                            substr($comment_arr[1][0], 0, 1) .
                            (count($foreign_relationship_table_prefix_match_arr[$comment_arr[1][0]]) - 1 > 0 ?
                                '_' . count($foreign_relationship_table_prefix_match_arr[$comment_arr[1][0]]) - 1 :
                                ''
                            );
                    }
                    $code_arr[] = $this->genVariableAnnotation($comment) . "    protected \$_" .
                        $foreign_relationship_table_prefix[$value['column']] . '_' . $value['foreign_column'] . '_' . ' = ' .
                        ('null') . ';' . "\r\n";

                    $function_name_set = $this->__genFunctionName('_'.$foreign_relationship_table_prefix[$value['column']] . '_' . $value['foreign_column'], 'set');
                    $function_name_get = $this->__genFunctionName('_'.$foreign_relationship_table_prefix[$value['column']] . '_' . $value['foreign_column'], 'get');

                    $function_main = $this->__genFunctionMain('protected', $val['Field'], (in_array($val['Field'], ['thumb', 'avatar', 'album']) ? '_' . $val['Field'] : ''), 'set', $table_name);
//                    $code_arr[] = $this->genFunction('public', $function_name_set, $parameter_arr, $function_main);
                    $code_arr[] = $this->genSetFunctionMain('public', ['_'.$foreign_relationship_table_prefix[$value['column']] . '_' . $value['foreign_column'] . '_'], $function_name_set);
//                    $code_arr[] = $this->genFunction('public', $function_name_get, $parameter_arr, $function_main);
                    $code_arr[] = $this->genGetFunctionMain('public', ['_'.$foreign_relationship_table_prefix[$value['column']] . '_' . $value['foreign_column'] . '_'], $function_name_get);
                }

            }

            $code = implode("\r\n", $code_arr);
        }
        return $code;
    }

    /**
     * 生成时间戳代码
     * @param string $column_name 字段的field
     * @return string
     */
    private function __genTimeCode($column_name = ''){
        $code = '';
        if(!empty($column_name)){
            $code = "       if(strpos(\$$column_name, \"_at\") !== false){".
                "#           \$this->_$column_name = \$$column_name  == null ? date(\"Y-m-d H:i:s\", time()) : (strpos(\$$column_name, \":\") ? ".
                "#           \$$column_name : date(\"Y-m-d H:i:s\", \$$column_name));".
                "#       }";
        }
        return $code;
    }

    /**
     * 将图片、头像、相册序列化、反序列化
     * @param string $column_name 字段的field
     * @param string $function_name get/set方法名
     * @return string
     */
    private function __genSerializeCode($column_name = '',$function_name){
        $code = '';
        if(!empty($column_name) && strpos($function_name,'set') !==false){
            if(strpos($column_name,'album') !== false){
                $code .= "       if(\$this->isSerialized(\$$column_name) === false){".
                    "#           \$$column_name"."_arr = array_filter(array_unique(explode(',',\$$column_name)));".
                    "#           foreach (\$$column_name"."_arr as \$index => \$$column_name"."_url){".
                    "#              if(strpos(\$$column_name"."_url, 'http://') !== false || strpos(\$$column_name"."_url, 'https://') !== false && \$this->isQiNiuUrl(substr(\$$column_name"."_url, 0, stripos(\$$column_name"."_url, '.com/')+5))){".
                    "#                 \$$column_name"."_arr[\$index] = substr(\$$column_name"."_url, strrpos(\$$column_name"."_url, '.com/')+5);".
                    "#              }else{".
                    "#                 \$$column_name"."_arr[\$index] = \$$column_name"."_url;".
                    "#              }".
                    "#           }".
                    "#              ".
                    "#         \$this->_$column_name = !empty(\$$column_name"."_arr) ?  serialize(\$$column_name"."_arr) : serialize('');".
                    "#      }";
            }else{
                $code .= "       if(\$this->isSerialized(\$$column_name) === false){".
                    "#           \$this->_$column_name = \$$column_name  == null ? serialize(\$$column_name) : ((strpos(\$$column_name, \"http://\") !== false || strpos(\$$column_name, \"https://\") !== false) && \$this->isQiNiuUrl(substr(\$$column_name, 0, stripos(\$$column_name, '.com/')+5)) ? ".
                    "#           serialize(substr(\$$column_name,strpos(\$$column_name,'.com/')+5)) : serialize(\$$column_name));".
                    "#       }";
                /* $code .= "       if(strpos(\$$column_name, \"http://\") !== false){".
                     "#           \$this->_$column_name = \$$column_name  == null ? serialize(\$$column_name) : (strpos(\$$column_name, \"http://\") !== false ? ".
                     "#           serialize(substr(\$$column_name,strpos(\$$column_name,'.com/')+5)) : serialize(substr(\$$column_name,strpos(\$$column_name,'.com/')+5)));".
                     "#       }";*/
            }

        }else{
            $code .= "       if(strpos(\$$column_name, \":\") !== false){".
                "#           \$this->_$column_name = \$$column_name  == null ? unserialize(\$$column_name) : (strpos(\$$column_name, \"http://\") !== false ? ".
                "#          
                 \$$column_name : unserialize(\$$column_name));".
                "#       }";
        }
        return $code;
    }

    /**
     * @param string $type 变量类型，可选值public protected private
     * @param array $validator 变量数组 [变量名1 => [默认值1, 类型1, 注释1]], 当生成字段变量时该参数为空
     * @param string $file_path 当生成字段变量时该参数则必须传入要生成的文件名，以此判断到底要获取哪张表的表结构
     * @return string
     */
    public function genSetFunctionMain($type = 'protected', $validator = [], $file_path = ''){
        $code = '';
        if(!empty($validator) && !empty($file_path)){
            if(strpos($file_path, 'Model.php') === false){
                $code = "\r\n    " . $type . " function " . $file_path . "(";
                foreach ($validator as $real_validator){
                    $parameter_code[] = "\$" . $real_validator . " = null";
                    $main_code[] = "    \$this->" . $real_validator . " = \$" . $real_validator . ";";
                }
                $code .= implode(",", $parameter_code) . "){\r\n    " . implode("\r\n", $main_code) . "\r\n    ";
                $code .= "#        return \$this;\r\n    }";
            } else {
                $code .="    \$this->\$name = \$value;#    return \$this;";
            }
        }
        return $code;
    }

    public function genGetFunctionMain($type = 'protected', $validator = [], $file_path = ''){
        $code = '';
        if(!empty($validator) && !empty($file_path)){
            if(strpos($file_path, 'Model.php') === false){
                $code = "\r\n    " . $type . " function " . $file_path . "(){\r\n";
                foreach ($validator as $real_validator){
                    if(strpos($real_validator,'_thumb') !==false || strpos($real_validator,'_avatar') !==false || (strpos($real_validator,'_album') !==false && strpos($real_validator,'_album_id') ===false)){
                        $code .= "        return unserialize(\$this->" . $real_validator . ");";
                    }else{
                        $code .= "        return \$this->" . $real_validator . ";";
                    }
                }
                $code .= "\r\n    }";
            }else{
                $code .= "    return  \$this->\$name ;";
            }
        }
        return $code;
    }

    /**
     * 生成Model的属性
     * @param string $type
     * @param array $validator
     * @param string $file_path
     * @return string
     * @throws Exception
     */
    public function genModelVariable($type = 'public', $validator = [], $file_path = ''){
        $code = '';
        if(empty($validator) && !empty($file_path)){
            $table_name = substr($file_path, strrpos($file_path, '/')+1, (strrpos($file_path, '.') - 1 - strrpos($file_path, '/')));
            if(empty($this->_table_fields)){
                $this->_table_fields = $this->_getTableConstruct(strtolower($table_name));
            }
            foreach($this->_table_fields as $key => $val) {
                $code_arr[] = $this->genVariableAnnotation($val['Comment']) . "    " . $type . " \$" .
//                    $type . " " . ($type == 'protected' ? '$_' : ($type == 'private' ? '$__' : '$')) .
                    $val['Field'] . ' = ' . ($val['Null'] == 'NO' ? 'null' : '""') . ';' . "\r\n";
            }
        }
        /*
        //查找外键关系，并将关联字段赋值给对应的model
        $table_name = substr(strtolower($table_name), 0, strpos(strtolower($table_name), 'model'));
        $sql = "SELECT * FROM `foreign_relationship` WHERE `table` = '" . $table_name . "'";
        $foreign_relationship = $this->_getResult($sql);
        $foreign_relationship_table_prefix = $foreign_relationship_table_prefix_match_arr = [];
        $i = $j = 1;
        $table_prefix = substr($table_name, 0, 1);
        foreach ($foreign_relationship as $row => $value){
            if(preg_match_all('/<"(.*)":"(.*)">/Usi', $value['comment'], $comment_arr)){
                $comment = $value['table'] .'表的' . $value['column'] . '字段关联' . $comment_arr[1][0] . '表的' . $comment_arr[2][0] . '字段';
                if(!isset($foreign_relationship_table_prefix[$value['column']])){
                    // 自己连自己
                    if($comment_arr[1][0] == $table_name){
                        $foreign_relationship_table_prefix[$value['column']] = substr($comment_arr[1][0], 0, 1) . '_' . $i++;
                    }else{
                        // 表前缀一致
                        if(substr($comment_arr[1][0], 0, 1) == $table_prefix){
                            $foreign_relationship_table_prefix[$value['column']] = substr($comment_arr[1][0], 0, 1) . '_' . $j++;
                        }else{
                            $foreign_relationship_table_prefix[$value['column']] = substr($comment_arr[1][0], 0, 1);
                        }
                    }
//                    $foreign_relationship_table_prefix[$value['column']] = $comment_arr[1][0] != $table_name ? substr($comment_arr[1][0], 0, 1) :
//                        substr($comment_arr[1][0], 0, 1) . '_1';
                    $foreign_relationship_table_prefix_match_arr[$comment_arr[1][0]][] = $value['column'];
                }else{
                    if(!in_array($value['column'], $foreign_relationship_table_prefix_match_arr[$comment_arr[1][0]])){
                        $foreign_relationship_table_prefix_match_arr[$comment_arr[1][0]][] = $value['column'];
                    }
                    $foreign_relationship_table_prefix[$value['column']] =
                        substr($comment_arr[1][0], 0, 1) .
                        (count($foreign_relationship_table_prefix_match_arr[$comment_arr[1][0]]) - 1 > 0 ?
                            '_' . count($foreign_relationship_table_prefix_match_arr[$comment_arr[1][0]]) - 1 :
                            ''
                        );
                }
                $code_arr[] = $this->genVariableAnnotation($comment) . "    public \$_" .
                    $foreign_relationship_table_prefix[$value['column']] . '_' . $value['foreign_column'] . '_' . ' = ' .
                    ($val['Null'] == 'NO' ? 'null' : '""') . ';' . "\r\n";
            }
        }
        */
//        var_dump($foreign_relationship_table_prefix);
        $code .= implode('#', $code_arr);
        return $code;

    }

    /**
     * 生成函数名
     * @param string $function_name 函数名必须是以"_"间隔多个单词，例：created_at，最后生成的函数名是setCreatedAt或者getCreatedAt
     * @param string $type 只能是set 或者get
     * @return string
     */
    private function __genFunctionName($function_name = '', $type = 'set'){
        $real_function_name = '';
        if(!empty($function_name) && in_array($type, ['set', 'get'])){
            $column_name_arr = explode('_', $function_name);
            $real_function_name = $type;
            foreach($column_name_arr as $key => $letter){
                $real_function_name .= ucfirst($letter);
            };
        }else if(in_array($type, ['__set', '__get'])){
            $real_function_name = $type;
        }
        return $real_function_name;
    }

    /**
     * @param string $variable_type 只能是public protected private
     * @param string $function_name $this->的参数，必须是以"_"间隔多个单词，例：created_at
     * @param string $real_function_name 真实函数名，如果为空则条用__genFunctionName生成，反之则必须传入由__genFunctionName生成的函数名，如function_name传入created_at则此处应传入setCreatedAt
     * @param string $type 只能是set或者get
     * @return string
     * @throws Exception
     */
    private function __genFunctionMain($variable_type = 'public', $function_name = '', $real_function_name = '', $type = 'set', $service_name = ''){
        $code = '';
        if(!empty($function_name) && in_array($type, ['set', 'get']) && in_array($variable_type, ['public', 'protected', 'private'])){
//            if(!empty($real_function_name)){
//                $real_func_name = '__gen' . ucfirst((stripos($real_function_name, '_') !== false ? substr($real_function_name, stripos($real_function_name, '_')+1) : $real_function_name )) . 'FunctionMain';
//                $code = $this->$real_func_name($variable_type, $real_function_name);
//            }else{
            $prefix = $variable_type == 'private' ? '__' : ($variable_type == 'protected' ? '_' : '');
            if(!empty($service_name) && strpos($function_name, 'path') !== false){
                $table_name = strtolower(str_replace("Service", "", $service_name));
                $foreign_relationship = $this->_getForeignRelationship($table_name);
                $foreign_relationship_column = $service_name = $path_suffix = null;
                foreach ($foreign_relationship as $index => $foreign_relationship_arr){
                    $service_name = $foreign_relationship_arr['table'] . 'Service()';
                    $foreign_relationship_column = $foreign_relationship_arr['column'];
                    if(preg_match_all('"<(.*):(.*)>"', str_replace('"', '', $foreign_relationship_arr['comment']), $master_table_id)){
                        $path_suffix = $master_table_id[2][0];
                    }

                }

//                 if($service_name && $foreign_relationship_column && $path_suffix){
//                        $code .= "    if(\$this->_parent_id !== null){".
//                            "#        \$parent_service = new " . $service_name . ";".
//                            "#        \$parent_is_exists = \$parent_service->getOne(".
//                            "#            ['groupOp' => \"AND\",\"rule\" => [['field' => '" . $foreign_relationship_column . "', 'op' => 'eq', 'data' => \$this->_". $foreign_relationship_column ."]]]".
//                            "#        );".
//                            "#        if(! \$parent_is_exists instanceof Exception)".
//                            "#            \$path = \$parent_is_exists->dao->__get(\"_". strtolower(str_replace("set", "", $function_name)) ."\") . \$parent_is_exists->dao->__get(\"_". $path_suffix ."\") . \"-\";".
//                            "#        else \$path = null;".
//                            "#    }#";
//                    }
                if($service_name && $foreign_relationship_column && $path_suffix){
                    $model_name = ucfirst(strstr($service_name,'Service',true)).'Model';
                    $code .= "    if(\$this->_". $foreign_relationship_column ." !== null){".
                        "#        \$parent_model = new " . $model_name . ";".
                        "#        \$parent_is_exists = \$parent_model->find(\$this->_". $foreign_relationship_column .");".
                        "#        if(!\$parent_is_exists instanceof Exception)".
                        "#            \$path = \$parent_is_exists->__get(\"_". strtolower(str_replace("set", "", $function_name)) ."\") . \$parent_is_exists->__get(\"_". $path_suffix ."\") . \"-\";".
                        "#        else \$path = null;".
                        "#       }#";
                }
//                    var_dump($service_name, $function_name);
            }
//                $foreign_relationship = $this->_getForeignRelationship()
            $code .= '    $this->' . $prefix . ''. $function_name .' = $' . $function_name . ';#    return $this;';
        }
//        }
        return $code;
    }

    /**
     * 生成缩略图set方法，将域名去掉
     * @param string $variable_type
     * @param string $column_name
     * @return string
     */
    private function __genAvatarFunctionMain($variable_type = 'public', $column_name = ''){
        $code = '';
        if(!empty($column_name)){
            $real_column_name = substr($column_name, stripos($column_name, '_')+1);
            $code = "    \$this->" . $column_name . " = strpos(\$" . $real_column_name . ", 'http://') !== false ?";
            $code .= "#        substr(\$" . $real_column_name . ", strrpos(\$" . $real_column_name . ", '/')+1) :";
            $code .= "#        (strpos(\$" . $real_column_name . ", 'https://') !== false ? substr(\$" . $real_column_name . ", strpos(\$". $real_column_name .", '/') + 1) : \$". $real_column_name .");";
            $code .= "#    return \$this;";
        }
        return $code;
    }

    /**
     * 生成缩略图set方法，将域名去掉
     * @param string $variable_type
     * @param string $column_name
     * @return string
     */
    private function __genThumbFunctionMain($variable_type = 'public', $column_name = ''){
        $code = '';
        if(!empty($column_name)){
            $real_column_name = substr($column_name, stripos($column_name, '_')+1);
            $code = "    \$this->" . $column_name . " = strpos(\$" . $real_column_name . ", 'http://') !== false ?";
            $code .= "#        substr(\$" . $real_column_name . ", strrpos(\$" . $real_column_name . ", '/')+1) :";
            $code .= "#        (strpos(\$" . $real_column_name . ", 'https://') !== false ? substr(\$" . $real_column_name . ", strpos(\$". $real_column_name .", '/') + 1) : \$". $real_column_name .");";
            $code .= "#    return \$this;";
        }
        return $code;
    }

    /**
     * 生成set相册方法，将域名去掉
     * @param string $variable_type
     * @param string $column_name
     * @return string
     */
    private function __genAlbumFunctionMain($variable_type = 'public', $column_name = ''){
        $code = '';
        if(!empty($column_name)){
            $real_column_name = substr($column_name, stripos($column_name, '_')+1);
            $code = "    \$" . $real_column_name . "_arr = array_filter(array_unique(explode(',', \$" . $real_column_name . ")));";
            $code .= "#    foreach (\$" . $real_column_name . "_arr as \$index => \$album_url){";
            $code .= "#        if(strpos(\$album_url, 'http://') !== false || strpos(\$album_url, 'https://') !== false){";
            $code .= "#            \$" . $real_column_name . "_arr[\$index] = substr(\$album_url, strrpos(\$album_url, '/')+1);";
            $code .= "#        }";
            $code .= "#    }";
            $code .= "#    \$this->" . $column_name . " = !empty(\$" . $real_column_name . "_arr) ? \$" . $real_column_name . "_arr : \$" . $real_column_name . ";";
//            $code = "    \$". $real_column_name ."_arr = [];";
//            $code .= "#    if(strpos(\$" . $real_column_name . ", 'http://') !== false){";
//            $code .= "#        \$" . $real_column_name . "_arr = explode(',', \$" . $real_column_name . ");";
//            $code .= "#    }else if(strpos(\$" . $real_column_name . ", 'https://') !== false){";
//            $code .= "#        \$" . $real_column_name . "_arr = explode(',', \$" . $real_column_name . ");";
//            $code .= "#    }";
//            $code .= "#    if(!empty(\$". $real_column_name ."_arr)){";
//            $code .= "#        \$" . $real_column_name . "_arr = array_filter(array_unique(\$" . $real_column_name . "_arr));";
//            $code .= "#        foreach (\$" . $real_column_name . "_arr as \$index => \$url){";
//            $code .= "#            \$" . $real_column_name . "_arr[\$index] = substr(\$url, strrpos(\$url, '/')+1);";
//            $code .= "#        }";
//            $code .= "#    }";
//            $code .= "#    \$this->" . $column_name . " = !empty(\$" . $real_column_name . "_arr) ? implode(',', \$" . $real_column_name . "_arr) : \$" . $real_column_name . ";";
            $code .= "#    return \$this;";
        }
        return $code;
    }

    /**
     * 实例化数据库链接 传入表名 读取完整表结构
     * @param string $table_name
     * @return array
     * @throws Exception
     */
    public function _getTableConstruct($table_name = ''){
        return GeneratePdo::getInstance()->_getTableConstruct($table_name);
    }

    /**
     * @param string $sql
     * @return array|bool
     * @throws Exception
     */
    protected function _getResult($sql = ''){
        return GeneratePdo::getInstance()->getResult($sql);
    }

    /**
     * @param string $table_name
     * @return array
     */
    protected function _getTableComment($table_name = ''){
        return GeneratePdo::getInstance()->getTableComment($table_name);
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function _getTablesInfo(){
        return GeneratePdo::getInstance()->getTablesInfo();
    }

    /**
     * @param string $table_name
     * @return array
     * @throws Exception
     */
    protected function _getForeignRelationship($table_name = ''){
        return GeneratePdo::getInstance()->getForeignRelationship($table_name);
    }

    protected function _genErrorTips($old_content_arr = [], $table_name = ''){
        $result_arr = $key_arr = $table_comment = [];
        if(!empty($table_name)){
            $table_comment = $this->_getTableComment($table_name);
            $table_name_comment = substr($table_comment[0][1], strrpos($table_comment[0][1], "COMMENT='") + 9, (strrpos($table_comment[0][1], "'") - strrpos($table_comment[0][1], "COMMENT='")));

            if(!empty($table_name_comment)){
                $result_arr[0] = '    const ' . strtoupper($table_name . '_NOT_EXISTS') . ' = "' . $table_name_comment . '不存在";';
                $key_arr[0] = '    const ' . strtoupper($table_name . '_NOT_EXISTS');
                foreach($this->_error_tips_operation as $key => $value){
                    foreach($this->_error_tips_operation_result as $k => $result){
                        $result_arr[] = '    const ' . strtoupper($table_name . '_' . $key . '_' . $k) . ' = "' . $table_name_comment . $value . $result . '";';
                        $result_arr[] = '    const ' . strtoupper($table_name . '_' . $key . '_' . $k . '_DESCRIPTION') . ' = "' . $table_name_comment . $value . $result . '，正在为你跳转' . '";' . "\r\n";
                        $key_arr[] = '    const ' . strtoupper($table_name . '_' . $key . '_' . $k);
                        $key_arr[] = '    const ' . strtoupper($table_name . '_' . $key . '_' . $k . "_DESCRIPTION");
                    }
                }
            }

            if(!empty($old_content_arr) && !empty($key_arr)){
                foreach ($key_arr as $k => $v){
                    foreach($old_content_arr as $index => $content){
                        if(strpos($content, 'const')){
                            $old_key_arr = explode(' = ', $content);
                            if($v == $old_key_arr[0]){
                                unset($result_arr[$k]);
                                unset($old_content_arr[$index]);
                            }
                        }
                    }
                }
            }
        }
        return empty($result_arr) && !empty($key_arr) ? true : $result_arr;
    }

}
