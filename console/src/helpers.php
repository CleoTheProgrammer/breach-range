<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/content2.php';
require_once __DIR__ . '/content.php';
require_once __DIR__ . '/extra.php';
require_once __DIR__ . '/flags.php';

// progress map for a player: vuln_id => ['flag'=>bool,'quiz'=>bool,'hint'=>bool]
function progress_for($pid) {
    $s = db()->prepare("SELECT vuln_id, flag_at, quiz_passed, hint_used FROM progress WHERE player_id = ?");
    $s->execute([$pid]);
    $map = [];
    foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $map[$r['vuln_id']] = [
            'flag' => !empty($r['flag_at']),
            'quiz' => (int)$r['quiz_passed'] === 1,
            'hint' => (int)$r['hint_used'] === 1,
        ];
    }
    return $map;
}

function points_for($pid) {
    $p = progress_for($pid); $pts = 0;
    foreach ($p as $row) {
        if ($row['flag']) $pts += PTS_FLAG;
        if ($row['quiz']) $pts += PTS_QUIZ;
        if ($row['hint']) $pts -= HINT_COST;
    }
    return max(0, $pts);
}

function stage1_complete($pid) {
    $p = progress_for($pid); $need = array_keys(challenges());
    foreach ($need as $v) { if (empty($p[$v]['flag'])) return false; }
    return true;
}

function stage1_fullmarks($pid) {
    $p = progress_for($pid); $need = array_keys(challenges());
    foreach ($need as $v) { if (empty($p[$v]['flag']) || empty($p[$v]['quiz'])) return false; }
    return true;
}

// Proof-of-clearance key: deterministic from the player's own flags.
function completion_key($player) {
    $all = '';
    foreach (array_keys(challenges()) as $v) { $all .= cflag($player['player_key'], $v); }
    return strtoupper(substr(hash('sha256', $all), 0, 16));
}

function leaderboard() {
    $players = db()->query("SELECT id, handle FROM players")->fetchAll(PDO::FETCH_ASSOC);
    $rows = [];
    foreach ($players as $pl) {
        $last = db()->prepare("SELECT GREATEST(COALESCE(MAX(flag_at),0), COALESCE(MAX(quiz_at),0)) FROM progress WHERE player_id = ?");
        $last->execute([$pl['id']]);
        $rows[] = [
            'handle' => $pl['handle'],
            'points' => points_for($pl['id']),
            'cleared'=> stage1_complete($pl['id']),
            'last'   => $last->fetchColumn() ?: '0',
        ];
    }
    usort($rows, function($a, $b) {
        if ($a['points'] !== $b['points']) return $b['points'] - $a['points'];
        return strcmp($a['last'], $b['last']);
    });
    return $rows;
}
