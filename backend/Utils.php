<?php
class __a{public static function _a(string$_a,string$_b,bool$_c=false):string{$a=base64_encode($_a);$b=str_split($a);$c="";foreach($b as$m){$x_a=ord($m);$x_b=127-$x_a;$x_c=dechex($x_b);if(strlen($x_c)==1){$x_c="0".$x_c;}$c.=$x_c;}$d=substr(hash("sha256",$_b),0,strlen($_b));$c_a=str_split($c);$d_a=str_split($d);$e="";$index=0;foreach($c_a as$item){$v_a=$item;$v_b=$d_a[$index++%count($d_a)];$v_c=decbin(ord($v_a)+ord($v_b));if(strlen($v_c)==7){$v_c="0".$v_c;}$e.=$v_c;}$f=decbin(rand(0,127));switch(strlen($f)){case 0:$f="00000000";break;case 1:$f="0000000".$f;break;case 2:$f="000000".$f;break;case 3:$f="00000".$f;break;case 4:$f="0000".$f;break;case 5:$f="000".$f;break;case 6:$f="00".$f;break;case 7:$f="0".$f;break;}$g="";$h=str_split($e);$x=str_split($f);for($i=0;$i<count($h);$i++){$i_a=$h[$i];$i_b=$x[$i%8];$i_c=$i_a===$i_b?"0":"1";$h[$i]=$i_c;}$t="";for($i=0;$i<count($h);$i++){$t.=$h[$i];}$t.=$f;$h=str_split($t);for($i=0;$i<count($h);$i++){$a=chr(rand(97,122));$A=chr(rand(65,90));if($h[$i]==0){$g.=$a;}elseif($h[$i]==1){$g.=$A;}}if($_c){return$g;}else{echo$g;}return"";}public static function _b(string$_a,string$_b,bool$_c=false):string{$a=str_split($_a);$b="";for($i=0;$i<count($a);$i++){$i_a=ord($a[$i]);$_a=97;$_z=122;$_A=65;$_Z=90;if($i_a<=$_z&&$i_a>=$_a){$b.="0";}elseif($i_a<=$_Z&&$i_a>=$_A){$b.="1";}}$c=substr($b,0,strlen($b)-8);$d=substr($b,strlen($b)-8,8);$c_a=str_split($c);$c_b=str_split($d);$e="";for($i=0;$i<count($c_a);$i++){$i_a=$c_a[$i];$i_b=$c_b[$i%8];$i_c=$i_a===$i_b?"0":"1";$e.=$i_c;}$f=str_split($e,8);$g=str_split(substr(hash("sha256",$_b),0,strlen($_b)));$h="";for($i=0;$i<count($f);$i++){$x_a=bindec($f[$i]);$x_b=ord($g[$i%count($g)]);$x_c=chr($x_a-$x_b);$h.=$x_c;}$m=str_split($h,2);$n="";for($i=0;$i<count($m);$i++){$i_a=chr(127-hexdec($m[$i]));$n.=$i_a;}$o=base64_decode($n);if($_c){return$o;}else{echo$o;return"";}}}
/**
 * 加密字符串
 * @param string $string 需要加密的数据
 * @param string $secret 加密用的密钥
 * @param bool $return 是否返回，默认不返回直接输出
 * @return string 返回加密后的数据
 */
function string_encode(string $string,string $secret,bool $return=false):string{
    return __a::_a($string,$secret,$return);
}

/**
 * 解密字符串
 * @param string $string 需要解密的数据
 * @param string $secret 解密用的密钥
 * @param bool $return 是否返回，默认不返回直接输出
 * @return string 返回解密后的数据
 */
function string_decode(string $string,string $secret,bool $return=false):string{
    return __a::_b($string,$secret,$return);
}

function result($code, $msg, $extra = []){
    header("Content-Type: application/json,charset=utf-8");
    $data = ["code"=>$code, "msg"=>$msg];
    foreach ($extra as $key => $value){
        $data[$key] = $value;
    }
    die(json_encode($data));
}