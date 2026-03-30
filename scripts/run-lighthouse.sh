#!/usr/bin/env sh
set -eu

TARGET_URL="${1:-http://localhost:8080}"

echo "Run Lighthouse manually if not installed:"
echo "lighthouse ${TARGET_URL} --only-categories=performance,seo --preset=desktop"
echo "lighthouse ${TARGET_URL} --only-categories=performance,seo --preset=mobile"
