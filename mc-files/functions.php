<?php
defined('__COMMON_DIR__') or die('Restricted access!');
/**
 * 用户登录
 * @param $username
 * @param $password
 *
 */
function token()
{
    $args = func_get_args();
    $args[] = $_SERVER['HTTP_USER_AGENT'];

    return crypt_code(serialize($args), 'ENCODE');
}

/**
 * 检查授权令牌
 * @param $username
 * @param $password
 * @param $authcode
 *
 */
function check_token($username, $password, $authcode)
{
    $args = array_slice(func_get_args(), 0, func_num_args() - 1);
    $args[] = $_SERVER['HTTP_USER_AGENT'];
    if (@unserialize(crypt_code($authcode, 'DECODE')) == $args) {
        return 1;
    } else {
        return 0;
    }
}

/**
 * 设置cookie
 *
 */
function set_auth($username, $password, $expire = null)
{
    setcookie('auth_token', token($username, $password), $expire);
}

/**
 * 获取cookie中令牌
 *
 * @return token
 */
function get_auth()
{
    return isset($_COOKIE['auth_token']) ? $_COOKIE['auth_token'] : null;
}

/**
 * 加密密码
 *
 * @return encode password
 */
function encode_pass($password)
{
    return crypt_code($password, 'ENCODE', 'safe');
}

/**
 * 加密密码
 *
 * @return decode password
 */
function decode_pass($password)
{
    return crypt_code($password, 'DECODE', 'safe');
}


/**
 *
 * @param string $string 原文或者密文
 * @param string $operation 操作(ENCODE | DECODE), 默认为 DECODE
 * @param string $key 密钥
 * @param int $expiry 密文有效期, 加密时候有效， 单位 秒，0 为永久有效
 * @return string 处理后的 原文或者 经过 base64_encode 处理后的密文
 * @example
 *   $a = authcode('abc', 'ENCODE', 'key');
 *   $b = authcode($a, 'DECODE', 'key');  // $b(abc)
 *
 *   $a = authcode('abc', 'ENCODE', 'key', 3600);
 *   $b = authcode('abc', 'DECODE', 'key'); // 在一个小时内，$b(abc)，否则 $b 为空
 */
function crypt_code($string, $operation = 'DECODE', $key = '', $expiry = 0)
{
    $string = trim($string);
    $ckey_length = 4;

    $key = md5($key ? $key : "safe-token-cms");
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if ($operation == 'DECODE') {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}
