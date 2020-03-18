<?php


namespace ylCodeGenerator;


class DbConfig
{
    static $instance = null;

    public $db_name = 'develop';
    public $db_user = "root";
    public $db_password = "123456";

    static public function getInstance($db_name = "", $db_user = "", $db_password = "") {
        if (is_null(self::$instance))
            self::$instance = new self($db_name, $db_user, $db_password);

        return self::$instance;
    }

    private function __construct($db_name = "", $db_user = "", $db_password = "")
    {
        $this->db_name = $db_name ? $db_name : $this->db_name;
        $this->db_user = $db_name ? $db_user : $this->db_user;
        $this->db_password = $db_password ? $db_password : $this->db_password;
    }

}
