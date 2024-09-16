<?php
error_reporting(0);
$list_query = array_filter(@explode("\n", str_replace(array("\r", " "), "", @file_get_contents(readline("[?] List Query       ")))));
echo "[*] Total Query : ".count($list_query)."\n";
for ($i = 0; $i < count($list_query); $i++) {
    $c = $i + 1;
    echo "\n[$c]\n";
    $auth = $list_query[$i];
    $task = get_task($auth);
    echo "[*] Get Task : ";
    if($task){
        echo "success\n\n";
        for ($a = 0; $a < count($task); $a++) {
            $ex = explode("|", $task[$a]);
            echo "[-] ".$ex[1]." => ".solve_task($ex[0], $auth)."\n";
        }
        for ($b = 0; $b < 4; $b++) {
            $d = $b + 1;
            echo "[-] Airdrop Step $d => ".solve_quest($auth)."\n";
        }
        echo "\n[*] Total : ".reward($auth)."\n";
    }
    else{
        echo "failed\n\n";
    }
}



function get_task($auth){
    $curl = curl("getTasks", $auth, "{}")['result']['data'];
    for ($i = 0; $i < count($curl); $i++) {
        $list[] = $curl[$i]['type']."|".$curl[$i]['title'];
    }
    return $list;
}

function solve_task($type, $auth){
    $curl = curl("completeTask", $auth, "{\"type\":\"$type\"}")['result']['reward'];
    return $curl;
}

function solve_quest($auth){
    $curl = curl("completeQuest", $auth, "{}")['result']['balance'];
    return $curl;
}

function reward($auth){
    $curl = curl("getMe", $auth, "{}")['result']['balance'];
    return $curl;
}

function curl($path, $auth, $body = false){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.agent301.org/'.$path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if($body){
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }
    $headers = array();
    $headers[] = 'Accept: application/json, text/plain, */*';
    $headers[] = 'Accept-Language: en-US,en;q=0.9';
    $headers[] = 'Authorization: '.$auth;
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Origin: https://telegram.agent301.org';
    $headers[] = 'Referer: https://telegram.agent301.org/';
    $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36 Edg/128.0.0.0';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    $decode = json_decode($result, true);
    return $decode;
}