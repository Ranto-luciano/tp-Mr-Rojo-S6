#!/usr/bin/env sh
set -eu

mkdir -p storage/backups

TIMESTAMP="$(date +%Y%m%d_%H%M%S)"
OUT_FILE="storage/backups/db_${TIMESTAMP}.sql"

docker compose exec -T db pg_dump -U "${DB_USER:-postgres}" "${DB_NAME:-iran_news}" > "${OUT_FILE}"

echo "Backup created at ${OUT_FILE}"
