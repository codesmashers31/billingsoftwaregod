#!/usr/bin/env bash
# God Statue Billing - macOS Standalone Desktop Launcher
set -e

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
cd "$DIR"

echo "============================================="
echo "   SASHTA SASTI - GOD STATUE BILLING ERP     "
echo "        macOS Standalone Application         "
echo "============================================="

# 1. Install dependencies if needed
if [ ! -d "node_modules" ]; then
    echo "[1/3] Installing dependencies..."
    npm install
fi

# 2. Ensure macOS PHP binary is present and executable
if [ ! -f "bin/mac/php" ]; then
    echo "[2/3] Fetching standalone macOS PHP runtimes..."
    node scripts/download-mac-php.js
fi

chmod +x bin/mac/php bin/mac/*/php 2>/dev/null || true

# 3. Launch Electron
echo "[3/3] Launching application..."
npm start
