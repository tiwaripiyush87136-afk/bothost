<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$db_file = 'verifications.json';

// --- DATA SAVE (From Website) ---
if (isset($_GET['zombieCookie']) && isset($_GET['chatid'])) {
    $zombieId = $_GET['zombieCookie'];
    $chatId = $_GET['chatid'];

    $data = file_exists($db_file) ? json_decode(file_get_contents($db_file), true) : ["fingerprints" => [], "verified_users" => []];

    // Anti-cheat: Duplicate device check
    if (isset($data['fingerprints'][$zombieId]) && $data['fingerprints'][$zombieId] != $chatId) {
        echo json_encode(["status" => "error", "message" => "Duplicate Device Detected"]);
        exit;
    }

    $data['fingerprints'][$zombieId] = $chatId;
    $data['verified_users'][$chatId] = true;
    file_put_contents($db_file, json_encode($data));
    echo json_encode(["status" => "success"]);
    exit;
}

// --- DATA CHECK (From Bot) ---
if (isset($_GET['check_id'])) {
    $checkId = $_GET['check_id'];
    if (file_exists($db_file)) {
        $data = json_decode(file_get_contents($db_file), true);
        if (isset($data['verified_users'][$checkId])) {
            echo json_encode(["status" => "verified"]);
            exit;
        }
    }
    echo json_encode(["status" => "pending"]);
}
?>