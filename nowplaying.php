<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-store');

$base = 'https://stream.radiobrazilweb.com:8757';

// Try ShoutCast /7.html first
$ctx = stream_context_create(['http'=>['timeout'=>5,'ignore_errors'=>true]]);
$raw = @file_get_contents($base.'/7.html', false, $ctx);

if ($raw !== false) {
    preg_match('/<body[^>]*>([\s\S]*?)<\/body>/i', $raw, $m);
    if (!empty($m[1])) {
        $parts = explode(',', strip_tags($m[1]));
        if (count($parts) >= 7) {
            $song = trim(implode(',', array_slice($parts, 6)));
            if ($song) { echo json_encode(['song'=>$song]); exit; }
        }
    }
}

// Fallback: status-json.xsl
$json = @file_get_contents($base.'/status-json.xsl', false, $ctx);
if ($json !== false) {
    $d = json_decode($json, true);
    $src = $d['icestats']['source'] ?? null;
    $song = is_array($src) ? ($src[0]['title'] ?? '') : ($src['title'] ?? '');
    if ($song) { echo json_encode(['song'=>$song]); exit; }
}

echo json_encode(['song'=>'']);
