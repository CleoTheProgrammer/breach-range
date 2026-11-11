#!/usr/bin/env bash
# Breach Range helper. Usage: ./scripts/run.sh [up|down|test]
set -euo pipefail
cd "$(dirname "$0")/.."
CONSOLE="http://127.0.0.1:8081"; TARGET="http://127.0.0.1:8080"
case "${1:-up}" in
  up)   docker compose up --build -d; echo "Console: $CONSOLE (start here)"; echo "Target:  $TARGET" ;;
  down) docker compose down -v ;;
  test)
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
