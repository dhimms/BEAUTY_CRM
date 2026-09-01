#!/bin/bash
# ─────────────────────────────────────────────────────────────
# BEAUTY_CRM - Docker Setup Script
# Jalankan sekali untuk setup awal: bash docker/setup.sh
# ─────────────────────────────────────────────────────────────

set -e

echo ""
echo "╔═══════════════════════════════════════╗"
echo "║     BEAUTY_CRM Docker Setup           ║"
echo "╚═══════════════════════════════════════╝"
echo ""

# 1. Salin .env.docker ke .env jika belum ada
if [ ! -f .env ]; then
    echo "📋 [1/4] Menyalin .env.docker → .env ..."
    cp .env.docker .env
fi

# 2. Build & jalankan container (App + Nginx + MinIO)
echo "🐳 [2/4] Build & jalankan container..."
docker-compose up -d --build

# 3. Jalankan migration ke MySQL host (Laragon)
echo "🗃️  [3/4] Menjalankan migrate..."
docker-compose exec app php artisan migrate --force

# 4. Generate storage link & optimize
echo "⚡ [4/4] Finishing up..."
docker-compose exec app php artisan storage:link
docker-compose exec app php artisan optimize:clear

echo ""
echo "✅ Setup selesai!"
echo ""
echo "🌐 App     → http://localhost:8080"
echo "🗄️  MinIO   → http://localhost:9011  (Username: minioadmin | Password: minioadmin)"
echo "🔌 MySQL   → MySQL Laragon Lokal (Port 3306)"
echo ""
