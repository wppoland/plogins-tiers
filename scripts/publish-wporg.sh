#!/usr/bin/env bash
# Publish Plogins Tiers to the WordPress.org plugin directory via SVN.
# You run this (it prompts for your SVN password on commit). SVN username: motylanogha.
#
#   bash scripts/publish-wporg.sh
#
# It builds a clean plugin from the current checkout, checks out the wp.org SVN
# repo, stages trunk + assets (banner/icon/screenshots), commits, and tags.
set -euo pipefail

SLUG="plogins-tiers"
SVN_URL="https://plugins.svn.wordpress.org/${SLUG}"
SVN_USER="motylanogha"
REPO_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
VER="$(grep -m1 'Stable tag:' "$REPO_ROOT/readme.txt" | sed 's/.*Stable tag:[[:space:]]*//' | tr -d '[:space:]')"
WORK="/tmp/svn-${SLUG}"

echo "==> Building a clean plugin (honours .distignore)"
bash "$REPO_ROOT/scripts/build-zip.sh"
BUILD_DIR="/tmp/tiers-build/tiers"        # produced by build-zip.sh
[ -d "$BUILD_DIR" ] || { echo "build dir $BUILD_DIR missing"; exit 1; }

echo "==> Checking out wp.org SVN ($SVN_URL)"
rm -rf "$WORK"
svn checkout "$SVN_URL" "$WORK"

echo "==> Staging trunk/"
rm -rf "$WORK/trunk"
mkdir -p "$WORK/trunk"
rsync -a "$BUILD_DIR/" "$WORK/trunk/"

echo "==> Staging assets/ (banner, icon, screenshots)"
mkdir -p "$WORK/assets"
cp "$REPO_ROOT/.wordpress-org/"banner-*.png "$WORK/assets/"
cp "$REPO_ROOT/.wordpress-org/"icon-*.png "$WORK/assets/"
cp "$REPO_ROOT/.wordpress-org/"screenshot-*.png "$WORK/assets/"

cd "$WORK"
svn add --force trunk assets --quiet
# Mark deletions (files removed since a prior release), if any.
svn status | awk '/^!/ {print $2}' | xargs -r svn delete --quiet

echo "==> Pending changes:"
svn status

echo
echo "==> Committing trunk + assets (you'll be prompted for your SVN password)"
svn commit -m "Plogins Tiers ${VER}: initial release" --username "$SVN_USER"

echo "==> Tagging ${VER}"
svn copy "^/trunk" "^/tags/${VER}" -m "Tag ${VER}" --username "$SVN_USER"

echo
echo "Done. Live shortly at https://wordpress.org/plugins/${SLUG}/"
