-- Console (scoreboard / LMS) schema. Secure side of the range.
USE console;

CREATE TABLE players (
  id INT AUTO_INCREMENT PRIMARY KEY,
  handle VARCHAR(40) UNIQUE,            -- display name on the leaderboard
  pass_hash VARCHAR(255),               -- bcrypt, the console is NOT a target
  player_key CHAR(32),                  -- per-player secret used to derive flags
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE progress (
  id INT AUTO_INCREMENT PRIMARY KEY,
  player_id INT,
  vuln_id VARCHAR(40),                  -- matches the content keys (e.g. 'sqli','idor')
  flag_at TIMESTAMP NULL,               -- when the correct flag was submitted
  quiz_passed TINYINT DEFAULT 0,
  quiz_at TIMESTAMP NULL,
  hint_used TINYINT DEFAULT 0,
  UNIQUE KEY uniq_player_vuln (player_id, vuln_id)
);
