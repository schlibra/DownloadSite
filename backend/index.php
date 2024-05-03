<?php
//echo phpversion();
date_default_timezone_set("PRC");
include("vendor/autoload.php");
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
include("Mail.php");
include("MySQL.php");
include("Utils.php");
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (str_starts_with($_SERVER["PATH_INFO"], "/download/") OR str_starts_with($_SERVER["PATH_INFO"], "//download/")) {
        $code = str_replace("/","",str_replace("/download/","",$_SERVER["PATH_INFO"]));
        echo $code;
        $mysql = new MySQL();
        $password = @$_GET["password"];
        $result = $mysql->query("SELECT * FROM `list` WHERE `link`='$code';");
        while ($row = $result->fetch_assoc()) {
            if (!empty($row["password"])){
                if (empty($password)){
                    result(403, "请传入提取码");
                }
                if (@string_decode($row["password"], $password, true) !== $password){
                    result(401, "提取码错误");
                }
            }
            $mysql->query("UPDATE `list` SET `downloadCount`=`downloadCount`+1 WHERE `link`='$code';");
            $filename = "upload/" . $row["hash"];
            header("Content-Type: application/octet-stream");
            header("Accept-Ranges: bytes");
            header("Accept-Length: ".$row["filesize"]);
            header("Content-Disposition: attachment; filename=" . $row["filename"]);
//            result(200, "OK", ["hash"=>$row["hash"]]);
            $f = fopen($filename, "rb");
            echo fread($f, $row["filesize"]);
            die();
//            print_r($row);
        }
        result(404, "文件不存在");
        echo $code;
    }else {
        readfile("main.html");
    }
}else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_GET["action"];
    $mysql = new MySQL();
    $pri = file_get_contents("key/pri");
    $pub = file_get_contents("key/pub");
    $pubKey = new Key($pub, "RS256");
    switch ($action) {
        case "login":
            $json = json_decode(file_get_contents("php://input"),true);
            $username = $json["username"];
            $password = $json["password"];
            if (empty($username)||empty($password)){
                result(401, "用户名和密码不能为空");
            }
            $result = $mysql->query("SELECT * FROM `user` WHERE `username`='$username' OR `email`='$username'");
            while ($row = mysqli_fetch_assoc($result)){
                if (@string_decode($row["password"], $password, true) == $password){
                    if ($row["ban"]){
                        result(401, "用户已被封禁");
                    }else{
                        $payload = [
                            "iss" => "https://dl.schhz.cn",
                            "aud" => "https://dl.schhz.cn",
                            "iat" => time(),
                            "nbf" => time(),
                            "exp" => time()+6*60*60,
                            "username" => $row["username"],
                            "password" => $password,
                            "nickname" => $row["nickname"]
                        ];
                        $token = JWT::encode($payload, $pri, "RS256");
                        result(200, "登录成功", ["admin"=>1, "token" => $token]);
                    }
                }else{
                    result(401, "密码错误");
                }
            }
            result(401, "用户不存在");
            break;
        case "getInfo":
            $token = @getallheaders()["Authorization"];
            if (str_starts_with($token, "Bearer ")){
                $token = str_replace("Bearer ", "", $token);
                try {
                    $obj = (array)JWT::decode($token, $pubKey);
                    $username = $obj["username"];
                    $password = $obj["password"];
                    $nickname = $obj["nickname"];
                    $result = $mysql->query("SELECT * FROM `user` WHERE `username`='$username' AND `nickname`='$nickname';");
                    while ($row = mysqli_fetch_assoc($result)) {
                        if ($row["ban"]) {
                            result(401, "用户已被封禁");
                        }
                        if (string_decode($row["password"], $password, true) === $password) {
                            $data = [
                                "username" => $obj["username"],
                                "nickname" => $obj["nickname"],
                                "admin" => $row["admin"],
                                "email" => $row["email"],
                                "sex" => $row["sex"],
                                "hash" => hash("sha256", $row["email"])
                            ];
                            $result2 = $mysql->query("SELECT count(*) FROM `list` WHERE `username`='$username';");
                            $row2 = mysqli_fetch_assoc($result2);
                            $downloadCount = $row2["count(*)"];
                            $data["downloadCount"] = $downloadCount;
                            result(200, "操作成功", ["data" => $data]);
                        } else {
                            result(401, "登录已过期");
                        }
                    }
                    result(401, "登录已过期");
                }catch (Exception$e){
                    result(401, "登录已过期");
                }
            }else{
                result(401, "认证失败");
            }
            break;
        case "sendMail":
            $json = json_decode(file_get_contents("php://input"),true);
            $mail = $json["mail"];
            $code = rand(111111,999999);
            Mail::send($mail, "注册下载站验证码：$code", "<h2>感谢您注册下载站</h2><p>这是您的注册验证码：<mark>$code</mark>，请不要泄露该验证码给他人，验证码在15分钟内有效，重新发送后本验证码将失效。</p>", "注册下载站验证码：$code");
            $payload = [
                "iss"  => "https://dl.schhz.cn",
                "aud"  => "https://dl.schhz.cn",
                "iat"  => time(),
                "nbf"  => time(),
                "exp"  => time()+15*60,
                "mail" => $mail,
                "code" => $code
            ];
            $token = JWT::encode($payload, $pri, "RS256");
            result(200, "发送成功", ["token"=>$token]);
            break;
        case "register":
            $json = json_decode(file_get_contents("php://input"),true);
            $token = getallheaders()["Authorization"];
            $username = $json["username"];
            $nickname = $json["nickname"];
            $password = $json["password"];
            $confirm = $json["confirm"];
            $email = $json["email"];
            $code = $json["code"];
            $sex = $json["sex"];
            if (empty($username))result(401, "用户名不能为空");
            if (empty($nickname))result(401, "昵称不能为空");
            if (empty($password))result(401, "密码不能为空");
            if ($password != $confirm) result(401, "两次密码不一致");
            if (empty($email)) result(401, "邮箱不能为空");
            if (empty($code)) result(401, "验证码不能为空");
            if (empty($sex)) result(401, "性别不能为空");
            if (str_starts_with($token, "Bearer ")){
                $token = str_replace("Bearer ", "", $token);
                try {
                    $obj = (array) JWT::decode($token, $pubKey);
                    if ($email != $obj["mail"]) result(401, "邮箱错误");
                    if ($code != $obj["code"]) result(401, "验证码错误");
                    $result = $mysql->query("SELECT * FROM `user` WHERE `username`='$username' OR `email`='$email';");
                    while ($row = mysqli_fetch_assoc($result)){
                        result(401, "用户名或邮箱已存在");
                    }
                    $ori_password = $password;
                    $password = string_encode($password, $password, true);
                    $mysql->query("INSERT INTO `user` (`username`, `password`, `nickname`, `sex`, `admin`, `ban`, `email`) VALUES ('$username', '$password', '$nickname', '$sex', '0', '0', '$email');");
                    $payload = [
                        "iss" => "https://dl.schhz.cn",
                        "aud" => "https://dl.schhz.cn",
                        "iat" => time(),
                        "nbf" => time(),
                        "exp" => time()+6*60*60,
                        "username" => $username,
                        "password" => $ori_password,
                        "nickname" => $nickname
                    ];
                    $token = JWT::encode($payload, $pri, "RS256");
                    result(200, "注册成功", ["token"=>$token]);
                }catch (Exception $e){
                    result(401, "邮箱验证失败：".$e->getMessage());
                }

            }else{
                result(401, "请重新发送验证码");
            }
            break;
        case "decodeToken":
            $token = $_POST["token"];
//            JWT::$leeway+=15;
            try {
                $obj = (array) JWT::decode($token, $pubKey);
                print_r($obj);
            }catch (Exception $e){
                print_r($e->getMessage());
            }
            break;
        case "fileList":
            $list = [];
            $result = $mysql->query("SELECT * FROM `list`;");
            while ($row = mysqli_fetch_assoc($result)){
                $size = $row["filesize"];
                $prefix_list = ["B", "KB", "MB", "GB", "TB"];
                $prefix_count = 0;
                while ($size>1024){
                    $size/=1024;
                    $prefix_count+=1;
                }
                $row["filesize"] = round($size, 2) . $prefix_list[$prefix_count];
                $row["hasPassword"] = empty($row["password"]) ? 0 : 1;
                unset($row["password"]);
                $list[] = $row;
            }
            result(200, "操作成功", ["data"=>$list]);
            break;
        case "fileInfo":
            $json = json_decode(file_get_contents("php://input"),true);
            $link = $json["id"];
            $data = [];
            $result = $mysql->query("SELECT * FROM `list` WHERE `link`='$link';");
            while ($row = mysqli_fetch_assoc($result)){
                if (!empty($row["password"])){
                    $data["link"] = $row["link"];
                    $data["hasPassword"] = 1;
                    goto fileInfoSkip;
                }
                $data = $row;
                $size = $data["filesize"];
                $data["pan123_pwd"] = $data["123_pwd"];
                unset($data["123_pwd"]);
                $prefix_list = ["B", "KB", "MB", "GB", "TB"];
                $prefix_count = 0;
                while ($size>1024){
                    $size/=1024;
                    $prefix_count+=1;
                }
                $data["filesize"] = round($size, 2) . $prefix_list[$prefix_count];
                $username = $row["username"];
                $result2 = $mysql->query("SELECT * FROM `user` WHERE `username`='$username';");
                while ($row2 = mysqli_fetch_assoc($result2)){
                    $data["nickname"] = $row2["nickname"];
                    goto fileInfoSkip;
                }
                $data["nickname"] = "匿名用户";
                fileInfoSkip:
                result(200, "操作成功", ["data"=>$data]);
            }
            result(404, "文件不存在");
            break;
        case "getFile":
            $json = json_decode(file_get_contents("php://input"), true);
            $id = $json["id"];
            $password = $json["password"];
            $result = $mysql->query("SELECT * FROM `list` WHERE `link`='$id';");
            while ($row = mysqli_fetch_assoc($result)){
                if (@string_decode($row["password"], $password, true) == $password) {
                    $data = $row;
                    unset($data["password"]);
                    $size = $data["filesize"];
                    $prefix_list = ["B", "KB", "MB", "GB", "TB"];
                    $prefix_count = 0;
                    while ($size > 1024) {
                        $size/=1024;
                        $prefix_count+=1;
                    }
                    $data["filesize"] = round($size, 2) . $prefix_list[$prefix_count];
                    $username = $row["username"];
                    $result2 = $mysql->query("SELECT * FROM `user` WHERE `username`='$username';");
                    $data["pan123_pwd"] = $data["123_pwd"];
                    unset($data["123_pwd"]);
                    while ($row2 = mysqli_fetch_assoc($result2)){
                        $data["nickname"] = $row2["nickname"];
                        goto fileInfoSkip2;
                    }
                    $data["nickname"] = "匿名用户";
                    fileInfoSkip2:
                    result(200, "操作成功", ["data"=>$data]);
                }else{
                    result(401, "提取码错误");
                }
            }
            result(404, "文件不存在");
            break;
        case "upload":
            $file = $_FILES["file"];
//            print_r($file);
            $hash = hash_file("sha256", $file["tmp_name"]);
            move_uploaded_file($file["tmp_name"], "temp/$hash");
            result(200, "文件上传成功", ["hash"=>$hash]);
            break;
        case "submitFile":
            $json = json_decode(file_get_contents("php://input"), true);
            $token = getallheaders()["Authorization"];
            if (str_starts_with($token, "Bearer ")){
                $code = rand(111111,999999);
                $token = substr($token, 7);
                try {
                    $obj = (array)JWT::decode($token, $pubKey);
                    $username = $obj["username"];
                    $tmpFilename = "temp/$username." . time() . "." . $code;
                    touch($tmpFilename);
                    $f = fopen($tmpFilename, "ab");
                    foreach ($json["blob"] as $item) {
                        $tmpPiece = "temp/$item";
                        if (file_exists($tmpPiece)) {
                            $rf = fopen($tmpPiece, "rb");
                            fwrite($f, fread($rf, filesize($tmpPiece)));
                            unlink($tmpPiece);
                            fclose($rf);
                        } else {
                            result(502, "分片文件丢失");
                        }
                    }
                    fclose($f);
                    $hash = hash_file("sha256", $tmpFilename);
                    rename($tmpFilename, "upload/$hash");
                    $password = $json["password"];
                    if (!empty($password)) {
                        $password = string_encode($password, $password, true);
                    }
                    $filesize = $json["filesize"];
                    $filename = $json["filename"];
                    $description = $json["description"];
                    $tsinbei = $json["tsinbei"];
                    $tsinbei_pwd = $json["tsinbei_pwd"];
                    $pan123 = $json["pan123"];
                    $pan123_pwd = $json["pan123_pwd"];
                    $lanzou = $json["lanzou"];
                    $lanzou_pwd = $json["lanzou_pwd"];
                    try {
                        $result = $mysql->query("INSERT INTO `list` (`link`, `filename`, `filesize`, `hash`, `tsinbei`, `123`, `lanzou`, `username`, `createTime`, `downloadCount`, `password`, `description`, `tsinbei_pwd`, `lanzou_pwd`, `123_pwd`) VALUES ('$code', '$filename', '$filesize', '$hash', '$tsinbei', '$pan123', '$lanzou', '$username', now(), 0, '$password', '$description', '$tsinbei_pwd', '$lanzou_pwd', '$pan123_pwd');");
                        result(200, "文件提交成功！");
                    } catch (Exception $e) {
                        result(500, "数据库执行失败：" . $e->getMessage());
                    }
                }catch (Exception$e){
                    result(401, "认证失败：" . $e->getMessage());
                }
            }else{
                result(401, "认证信息无效");
            }
            break;
    }
    die();
}