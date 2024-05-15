<?php
function debug_to_console($data)
{
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

function dd($value)
{
    echo "<pre>";
    foreach (func_get_args() as $arg) {
        var_dump($arg);
      }
    echo "</pre>";
    die();
}
;

function aws_Config(): array
{

    $region = 'us-east-1';
    $key = 'ASIA4MTWJWNBIP2K45U7';
    $secret = 'JKFvoI6KL8sBU0ZLHSYhhPu+5m9Fl9Hbjk+nU1rj';
    $token = "IQoJb3JpZ2luX2VjEDMaCXVzLXdlc3QtMiJGMEQCIHgFYSHPAKSJ4X8vqweUHxwH6M1f9YBiXn5gjAByAhhiAiA1Cdj5hAnirE9b81uvkjlr9u+DPLHemDtOGDv/DYwaoyq/Agic//////////8BEAAaDDg1MTcyNTM2NjA4MiIMN+qHMc+FWyxIbCoBKpMCjZu/Ed6WEXjTmwiOqUpgo0haSB3JoBEurBP1/cIazcX5nar8hONBytDhVdv9o3sg1zPU342Boph+bKIfptYhK5cDROdqLbEvEuMIp6BSCuMS9mJfMYam22YYhxSKCXsx4KpwE7LCJbph0NFqbw3XQwYwEUL7Y8rB4UynhPfAR9Fl3cPDePY8Z2dnmxu8ndKCTOrvg8XSmDrkYxnk8x5okSvwpsj1p75Ca5PAtKjsEk2xAudeUDPMSQ0Q6kyPE0GIiYifZyk9NU9MptSd4OoFd/JmOHftm9JEL3o+RxUcmhXsBU+RdN/Jp7khbG5theZFULeAEvbgd7nP+LkalTarU8RQiugS0WiT7NpO1NOHFhqVY74wldGQsgY6ngGAKTlxmaGLDO+mCV06nkU9PtcakINxtb7OT3Rix/fLDLBtpaIf01bu/MgM9bVeFVuwUKSGZr0Oj7uyKvZXaWe6GDARoLFVSAXPg4fnoe8Ub3iBg+L2PJ9eXQvDN1Xn+IPBUPX13FNKmSs4aI77kNBaK0lDgDs0+uU2o+dNb82Ky+JnW39Dsncdq2iSorfUuyjHFppaoKi3sKt+7n4H6g==";
    $region = 'us-east-1';
    $config = [
        'region' => $region,
        'version' => 'latest',
        'credentials' => new \Aws\Credentials\Credentials($key, $secret, $token)

    ];

    return $config;
}
