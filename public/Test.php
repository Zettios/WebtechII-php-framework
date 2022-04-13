<?php

echo "<pre>";
echo "=== Server ===\n";
var_dump($_SERVER);
var_dump($_SERVER['QUERY_STRING']);
echo "\n=== Request ===\n";
var_dump($_REQUEST);
echo "\n=== Post ===\n";
var_dump($_POST);
echo "\n=== GET ===\n";
var_dump($_GET);
echo "\n=== Cookie ===\n";
var_dump($_COOKIE);
//echo "\n=== Session ===\n";
//var_dump($_SESSION);
echo "\n=== Files ===\n";
var_dump($_FILES);


function detectRequestBody() {
    $rawInput = fopen('php://input', 'r');
    $tempStream = fopen('php://temp', 'r+');
    stream_copy_to_stream($rawInput, $tempStream);
    rewind($tempStream);
    echo gettype($rawInput);
    echo gettype($tempStream);

    return $tempStream;
}
echo "\n=== detectRequestBody() ===\n";
parse_str(fgets(detectRequestBody()), $output);
print_r($output);

echo "</pre>";
