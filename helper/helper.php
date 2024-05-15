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
    $key = '';
    $secret = '+5m9Fl9Hbjk+nU1rj';
    $token = "";
    $region = 'us-east-1';
    $config = [
        'region' => $region,
        'version' => 'latest',
        'credentials' => new \Aws\Credentials\Credentials($key, $secret, $token)

    ];

    return $config;
}
