#!/usr/bin/env sh
set -eu

echo "Waiting for PostgreSQL..."
until docker compose exec -T db pg_isready -U "${DB_USER:-postgres}" >/dev/null 2>&1; do
	sleep 1
done

echo "Database is ready."
