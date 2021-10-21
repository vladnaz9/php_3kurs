<?php

if (isset($_REQUEST["string"])) {
    $string = $_POST['string'];
    $fio = $_POST['FIO'];


    $url = "https://reqbin.com/echo/post/json";
//    $data = explode(PHP_EOL, $string);
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $arr = ["esse" => $string, 'fio' => $fio];

    $data = json_encode($arr);
//    var_dump($data);
    $headers = array(
        "Accept: application/json",
        "Content-Type: application/json",
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

//for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $resp = curl_exec($curl);
    curl_close($curl);
    $decodedResp = json_decode($resp);
    if ($decodedResp == null) {
        echo " ошибка подключения ";
    } else {
        $decodedResp = json_decode($resp);
        foreach ($decodedResp as $key => $value) {
            echo $key . ' = ' . $value;
        }
    }


}

