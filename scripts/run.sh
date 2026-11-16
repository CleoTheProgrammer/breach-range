#!/usr/bin/env bash
# Breach Range helper. Usage: ./scripts/run.sh [up|down|test]
# `up` prompts for host ports and the public IP so you can hand students one address.
# Non-interactive? Set TARGET_PORT / CONSOLE_PORT / PUBLIC_HOST in the environment.
set -euo pipefail
cd "$(dirname "$0")/.."

ask() {                 # $1=label  $2=default  $3=env var name
  local label="$1" def="$2" var="$3" val
  if [ -n "${!var:-}" ]; then echo "${!var}"; return; fi
  if [ -t 0 ]; then read -rp "$label [$def]: " val; echo "${val:-$def}"; else echo "$def"; fi
}

case "${1:-up}" in
  up)
    DEF_IP="$(hostname -I 2>/dev/null | awk '{print $1}')"; [ -n "$DEF_IP" ] || DEF_IP="127.0.0.1"
    TARGET_PORT="$(ask 'Target port'  8080 TARGET_PORT)"
    CONSOLE_PORT="$(ask 'Console port' 8081 CONSOLE_PORT)"
    PUBLIC_HOST="$(ask 'Public IP to give students' "$DEF_IP" PUBLIC_HOST)"
    export BIND_HOST="${BIND_HOST:-0.0.0.0}" TARGET_PORT CONSOLE_PORT PUBLIC_HOST
    docker compose up --build -d
    echo
    echo "=============================================================="
    echo " Give students this IP:   $PUBLIC_HOST"
    echo " They nmap it and find:   $TARGET_PORT (target), $CONSOLE_PORT (console)"
    echo " Target (attack this):    http://$PUBLIC_HOST:$TARGET_PORT"
    echo " Console (submit flags):  http://$PUBLIC_HOST:$CONSOLE_PORT"
    echo " Rebonametlholofa (you):  http://$PUBLIC_HOST:$CONSOLE_PORT/rebonametlholofa.php"
    echo "=============================================================="
    if command -v ufw >/dev/null 2>&1 && sudo -n ufw status 2>/dev/null | grep -q "Status: active"; then
      echo " NOTE: ufw is active. Allow the ports:"
      echo "   sudo ufw allow ${TARGET_PORT}/tcp && sudo ufw allow ${CONSOLE_PORT}/tcp"
    fi
    echo " Reminder: run this only on an isolated lab/classroom network."
    ;;
  down) docker compose down -v ;;
  test)
    TARGET_PORT="${TARGET_PORT:-8080}"; CONSOLE_PORT="${CONSOLE_PORT:-8081}"
    CONSOLE="http://127.0.0.1:${CONSOLE_PORT}"; TARGET="http://127.0.0.1:${TARGET_PORT}"
    echo "Waiting for services..."
    for i in $(seq 1 40); do curl -fsS "$CONSOLE/index.php" >/dev/null 2>&1 && curl -fsS "$TARGET/index.php" >/dev/null 2>&1 && break || sleep 2; done
    echo "[*] Console up:"; curl -fsS -o /dev/null -w "  %{http_code} $CONSOLE\n" "$CONSOLE/index.php"
    echo "[*] Target up:";  curl -fsS -o /dev/null -w "  %{http_code} $TARGET\n" "$TARGET/index.php"
    echo "[*] Legacy v0 API responds:"; curl -fsS "$TARGET/api/v0/" | grep -o '"flag":' && echo "  (flag field present)"
    echo "Smoke test done. Play from the console."
    ;;
  *) echo "usage: $0 [up|down|test]"; exit 1 ;;
esac
