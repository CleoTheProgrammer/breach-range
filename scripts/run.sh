#!/usr/bin/env bash
# Breach Range helper. Usage: ./scripts/run.sh [up|down|test]
# `up` prompts for the host ports (defaults 8080/8081) so you can dodge clashes.
# Non-interactive? Set TARGET_PORT / CONSOLE_PORT in the environment (or a .env file)
# and they are used without prompting.
set -euo pipefail
cd "$(dirname "$0")/.."

ask_port() {            # $1=label  $2=default  $3=env var name
  local label="$1" def="$2" var="$3" val
  if [ -n "${!var:-}" ]; then echo "${!var}"; return; fi   # already set, respect it
  if [ -t 0 ]; then
    read -rp "$label port [$def]: " val
    echo "${val:-$def}"
  else
    echo "$def"
  fi
}

case "${1:-up}" in
  up)
    TARGET_PORT="$(ask_port 'Target (vulnerable app)' 8080 TARGET_PORT)"
    CONSOLE_PORT="$(ask_port 'Console (scoreboard)' 8081 CONSOLE_PORT)"
    export TARGET_PORT CONSOLE_PORT
    docker compose up --build -d
    echo
    echo "Console: http://127.0.0.1:${CONSOLE_PORT}  (start here)"
    echo "Target:  http://127.0.0.1:${TARGET_PORT}"
    ;;
  down) docker compose down -v ;;
  test)
    TARGET_PORT="${TARGET_PORT:-8080}"; CONSOLE_PORT="${CONSOLE_PORT:-8081}"
    CONSOLE="http://127.0.0.1:${CONSOLE_PORT}"; TARGET="http://127.0.0.1:${TARGET_PORT}"
    echo "Waiting for services..."
    for i in $(seq 1 40); do curl -fsS "$CONSOLE/index.php" >/dev/null 2>&1 && curl -fsS "$TARGET/index.php" >/dev/null 2>&1 && break || sleep 2; done
    echo "[*] Console up:"; curl -fsS -o /dev/null -w "  %{http_code} $CONSOLE\n" "$CONSOLE/index.php"
    echo "[*] Target up:";  curl -fsS -o /dev/null -w "  %{http_code} $TARGET/index.php\n" "$TARGET/index.php"
    echo "[*] Legacy v0 API responds:"; curl -fsS "$TARGET/api/v0/" | grep -o '"flag":' && echo "  (flag field present)"
    echo "[*] SQLi auth bypass accepted:"; curl -fsS -d "username=admin' -- " -d "password=x" "$TARGET/login.php" -o /dev/null -w "  HTTP %{http_code}\n"
    echo "Smoke test done. Play from the console."
    ;;
  *) echo "usage: $0 [up|down|test]"; exit 1 ;;
esac
