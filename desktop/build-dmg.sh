#!/usr/bin/env bash
# God Statue Billing - macOS DMG Installer Builder
set -e

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
cd "$DIR"

echo "============================================="
echo "   COMPILING MACOS DMG INSTALLER PACKAGE     "
echo "============================================="

# 1. Install dependencies
npm install

# 2. Download and prepare PHP binaries
node scripts/download-mac-php.js
chmod +x bin/mac/php bin/mac/*/php 2>/dev/null || true

# 3. Build DMG
echo "Building DMG for macOS..."
npx electron-builder --mac dmg zip

echo ""
echo "============================================="
echo "SUCCESS: DMG created in desktop/dist/"
echo "============================================="
ls -lh dist/*.dmg 2>/dev/null || true
