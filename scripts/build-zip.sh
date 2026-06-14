#!/usr/bin/env bash
# Build a clean, installable tiers.zip for local testing, honouring .distignore.
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
OUT_DIR="${1:-/tmp/tiers-build}"
STAGE="${OUT_DIR}/tiers"

rm -rf "${OUT_DIR}"
mkdir -p "${STAGE}"

rsync -a --exclude-from="${ROOT_DIR}/.distignore" \
    --exclude '.git' --exclude 'node_modules' --exclude 'vendor' \
    --exclude '.DS_Store' \
    "${ROOT_DIR}/" "${STAGE}/"

find "${STAGE}" -name '.DS_Store' -delete

( cd "${OUT_DIR}" && zip -rqX /tmp/tiers.zip tiers -x '*.DS_Store' )
echo "✓ Built /tmp/tiers.zip from ${STAGE}"
