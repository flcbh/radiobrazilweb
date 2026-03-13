<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-store, max-age=0');

function sc_fetch(string $url): string|false {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 4,
        CURLOPT_TIMEOUT        => 6,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT      => 'Mozilla/5.0',
    ]);
    $r = curl_exec($ch);
    curl_close($ch);
    return $r;
}

$base = 'https://stream.radiobrazilweb.com:8757';

// ShoutCast /7.html — CSV: Listeners,Status,Peak,Max,Unique,Bitrate,SongTitle
$raw = sc_fetch($base . '/7.html');
if ($raw !== false) {
    preg_match('/<body[^>]*>([\s\S]*?)<\/body>/i', $raw, $m);
    if (!empty($m[1])) {
        $parts = explode(',', strip_tags(trim($m[1])));
        if (count($parts) >= 7) {
            $song = trim(implode(',', array_slice($parts, 6)));
            if ($song) { echo json_encode(['song' => $song]); exit; }
        }
    }
}

// Fallback: status-json.xsl
$json = sc_fetch($base . '/status-json.xsl');
if ($json !== false) {
    $d = json_decode($json, true);
    $src  = $d['icestats']['source'] ?? null;
    $song = is_array($src) ? ($src[0]['title'] ?? '') : ($src['title'] ?? '');
    if ($song) { echo json_encode(['song' => $song]); exit; }
}

echo json_encode(['song' => '']);
