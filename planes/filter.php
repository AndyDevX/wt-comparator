<?php

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nations = isset($_POST['nations']) ? $_POST['nations'] : [];
    $classes = isset($_POST['classes']) ? $_POST['classes'] : [];
    $tiers = isset($_POST['tiers']) ? $_POST['tiers'] : [];

    $params = [
        "limit" => 200,
        "page" => 0,
        "country" => "",
        "type" => "",
        "era" => 0,
        "isPremium" => false,
        "isPack" => false,
        "isSquadronVehicle" => false,
        "isOnMarketplace" => false,
        "excludeKillstreak" => true,
        "excludeEventVehicles" => true,
    ];

    $datos = [];

    //? Iterar entre cada nación, cada clase y cada tier para guardar todos los resultados
    foreach ($nations as $nation) {
        foreach ($classes as $class) {
            foreach ($tiers as $tier) {
                $baseUrl = "https://www.wtvehiclesapi.sgambe.serv00.net/api/vehicles?";

                $params["country"] = $nation;
                $params["type"] = $class;
                $params["era"] = $tier;

                $urlParams = http_build_query($params);
                $url = $baseUrl . $urlParams;
                
                //? Solicitud
                $result = file_get_contents($url);
                $result = json_decode($result, true);

                //? Descomponer los datos
                $datos [$nation][$class][$tier] = $result;

            }
        }
    }

    $_SESSION['datos'] = $datos;
    header("Location: planes.php");

} else {
    echo "No POST data";
    header("Location: planes.php");
    exit();
}