<?php

class MySQL
{
    private $conn;

    public function __construct()
    {
        $mysql_config = parse_ini_file("mysql.ini");
        try {
            $conn = @mysqli_connect(
                $mysql_config["hostname"] ?? "hostname",
                $mysql_config["username"] ?? "root",
                $mysql_config["password"] ?? "123456",
                $mysql_config["database"] ?? "root",
                $mysql_config["port"] ?? 3306
            );
            if ($conn) {
                $this->conn = $conn;
            } else {
                result(500, "数据库连接失败：" . mysqli_connect_error());
            }
        }catch (Exception$e){
            result(500, "数据库连接失败：" . $e->getMessage());
        }

    }
    public function query($query)
    {
        try {
            $result = mysqli_query($this->conn, $query);
            if ($result) {
                return $result;
            } else {
                result(500, "数据库执行失败：" . mysqli_error($this->conn));
            }
        }catch (Exception$e){
            result(500, "数据库执行失败：".$e->getMessage());
        }
    }
}