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

function challenges_stage($n) {
    $out = [];
    foreach (challenges() as $id => $c) { if ((int)($c['stage'] ?? 1) === (int)$n) $out[$id] = $c; }
    return $out;
}

function stage1_complete($pid) {
    $p = progress_for($pid); $need = array_keys(challenges_stage(1));
    foreach ($need as $v) { if (empty($p[$v]['flag'])) return false; }
    return true;
}

function stage1_fullmarks($pid) {
    $p = progress_for($pid); $need = array_keys(challenges_stage(1));
    foreach ($need as $v) { if (empty($p[$v]['flag']) || empty($p[$v]['quiz'])) return false; }
    return true;
}

function stage2_complete($pid) {
    $p = progress_for($pid); $need = array_keys(challenges_stage(2));
    if (!$need) return false;
    foreach ($need as $v) { if (empty($p[$v]['flag'])) return false; }
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
        $prog = progress_for($pl['id']);
        $flags = 0; foreach ($prog as $r) { if ($r['flag']) $flags++; }
        $lt = db()->prepare("SELECT MAX(GREATEST(COALESCE(flag_at,'1970-01-01 00:00:00'), COALESCE(quiz_at,'1970-01-01 00:00:00'))) FROM progress WHERE player_id = ?");
        $lt->execute([$pl['id']]);
        $last = $lt->fetchColumn();
        $rows[] = [
            'handle'  => $pl['handle'],
            'flags'   => $flags,
            'points'  => points_for($pl['id']),
            'cleared' => stage1_complete($pl['id']),
            'last'    => ($last && $last > '1971') ? $last : '',
        ];
    }
    // Rank: most flags first; ties to whoever reached it earliest (fastest); then points.
    usort($rows, function($a, $b) {
        if ($a['flags'] !== $b['flags']) return $b['flags'] - $a['flags'];
        $al = $a['last'] !== '' ? $a['last'] : '9999';
        $bl = $b['last'] !== '' ? $b['last'] : '9999';
        if ($al !== $bl) return strcmp($al, $bl);
        return $b['points'] - $a['points'];
    });
    return $rows;
}

// Instructor-controlled challenge visibility. Shared runtime state (one console
// container), polled by students so a toggle reaches them within seconds.
function state_path() { return sys_get_temp_dir() . '/br_challenges_visible'; }
function challenges_visible() {
    $p = state_path();
    if (!is_file($p)) return true;                 // default: shown
    return trim((string)@file_get_contents($p)) !== '0';
}
function set_challenges_visible($on) { @file_put_contents(state_path(), $on ? '1' : '0'); }

// ---- Non-repudiation audit log (append-only, HMAC hash chain) ----------------
define('AUDIT_GENESIS', str_repeat('0', 64));
function audit_secret() { return getenv('AUDIT_SECRET') ?: 'change-me-audit-secret'; }
function audit($action, $detail = '', $player_id = null, $handle = null) {
    try {
        if ($player_id === null && !empty($_SESSION['pid'])) $player_id = (int)$_SESSION['pid'];
        if ($handle === null) { $p = (!empty($_SESSION['pid'])) ? current_player() : null; $handle = $p['handle'] ?? ''; }
        $detail = substr((string)$detail, 0, 255);
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $ts = date('Y-m-d H:i:s');
        $prev = db()->query("SELECT row_hash FROM audit ORDER BY id DESC LIMIT 1")->fetchColumn();
        if (!$prev) $prev = AUDIT_GENESIS;
        $canon = $prev.'|'.$ts.'|'.(int)$player_id.'|'.$handle.'|'.$action.'|'.$detail.'|'.$ip;
        $row_hash = hash_hmac('sha256', $canon, audit_secret());
        db()->prepare("INSERT INTO audit (ts,player_id,handle,action,detail,ip,prev_hash,row_hash) VALUES (?,?,?,?,?,?,?,?)")
            ->execute([$ts, $player_id ?: null, $handle, $action, $detail, $ip, $prev, $row_hash]);
    } catch (Throwable $e) { /* never break the app over audit logging */ }
}
function audit_verify() {
    $rows = db()->query("SELECT * FROM audit ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
    $prev = AUDIT_GENESIS;
    foreach ($rows as $r) {
        $canon = $prev.'|'.$r['ts'].'|'.(int)$r['player_id'].'|'.$r['handle'].'|'.$r['action'].'|'.$r['detail'].'|'.(string)$r['ip'];
        $calc = hash_hmac('sha256', $canon, audit_secret());
        if (!hash_equals((string)$r['prev_hash'], $prev) || !hash_equals((string)$r['row_hash'], $calc)) {
            return ['ok' => false, 'bad' => (int)$r['id'], 'count' => count($rows)];
        }
        $prev = $r['row_hash'];
    }
    return ['ok' => true, 'bad' => null, 'count' => count($rows)];
}
