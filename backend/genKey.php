<?php
if (isset($_SERVER["REQUEST_METHOD"]) || isset($_SERVER["SERVER_PROTOCOL"])){
    http_response_code(404);
}else{
    $priPath = "key/pri";
    $pubPath = "key/pub";
    $config = [
        'private_key_bits' => 2048,
        'private_key_type' => OPENSSL_KEYTYPE_RSA
    ];
    $res = openssl_pkey_new($config);
    if (!$res) {
        $config["config"] = "D:\\Program Files\\PHP\\php-8.3.6-Win32-vs16-x64\\extras\\ssl\\openssl.cnf";
        $res = openssl_pkey_new($config);
    }
    openssl_pkey_export($res, $priKey, null, $config);
    file_put_contents($priPath, $priKey);
    $pubKey = openssl_pkey_get_details($res)["key"];
    file_put_contents($pubPath, $pubKey);
}