#!/bin/bash

# Este script executa as principais otimizações do Laravel, Filament e Vite para o ambiente de produção.

echo "🚀 Iniciando otimização da aplicação..."

# 1. Dependências do PHP (Otimização do Autoloader)
echo "📦 Instalando dependências do Composer (Production)..."
composer install --optimize-autoloader --no-dev --no-interaction

# 2. Otimização do Laravel (Caches de Configuração e Rotas)
echo "⚙️  Otimizando configurações, rotas e views..."
php artisan optimize
php artisan view:cache
php artisan event:cache

# 3. Otimização do Filament & Icons
echo "🎨 Otimizando Filament e Ícones..."
php artisan filament:cache-components
php artisan icons:cache
php artisan filament:upgrade --ansi

# 4. Frontend (Vite Build)
echo "🌐 Compilando assets do frontend (Vite)..."
npm install --legacy-peer-deps
npm run build

echo "✅ Aplicação otimizada com sucesso!"
