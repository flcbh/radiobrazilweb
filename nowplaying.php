<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-store, max-age=0');

function sc_get(string $url): string|false {
    if (!function_exists('curl_init')) return false;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 4,
        CURLOPT_TIMEOUT        => 6,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_USERAGENT      => 'Mozilla/5.0',
    ]);
    $r = curl_exec($ch);
    $err = curl_errno($ch);
    curl_close($ch);
    return ($err === 0 && $r !== false) ? $r : false;
}

$base = 'https://stream.radiobrazilweb.com:8757';

// 1. ShoutCast /7.html — CSV format
$raw = sc_get($base . '/7.html');
if ($raw !== false) {
    preg_match('/<body[^>]*>([\s\S]*?)<\/body>/i', $raw, $m);
    if (!empty($m[1])) {
        $parts = explode(',', strip_tags(trim($m[1])));
        if (count($parts) >= 7) {
            $song = trim(implode(',', array_slice($parts, 6)));
            if ($song && $song !== 'undefined') {
                echo json_encode(['song' => $song]); exit;
            }
        }
    }
}

// 2. ShoutCast /currentsong
$raw2 = sc_get($base . '/currentsong');
if ($raw2 !== false && strlen(trim($raw2)) > 0) {
    $song = trim($raw2);
    if ($song) { echo json_encode(['song' => $song]); exit; }
}

// 3. status-json.xsl (IceCast compatible)
$json = sc_get($base . '/status-json.xsl');
if ($json !== false) {
    $d = json_decode($json, true);
    if ($d) {
        $src  = $d['icestats']['source'] ?? null;
        $song = is_array($src) ? ($src[0]['title'] ?? '') : ($src['title'] ?? '');
        if ($song) { echo json_encode(['song' => $song]); exit; }
    }
}

// 4. Try HTTP fallback
$base2 = 'http://stream.radiobrazilweb.com:8757';
$raw3 = sc_get($base2 . '/7.html');
if ($raw3 !== false) {
    preg_match('/<body[^>]*>([\s\S]*?)<\/body>/i', $raw3, $m);
    if (!empty($m[1])) {
        $parts = explode(',', strip_tags(trim($m[1])));
        if (count($parts) >= 7) {
            $song = trim(implode(',', array_slice($parts, 6)));
            if ($song) { echo json_encode(['song' => $song]); exit; }
        }
    }
}

echo json_encode(['song' => '']);
