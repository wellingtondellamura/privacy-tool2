#!/bin/bash

# run_tests.sh - Mitra E2E Test Suite Executor

# 1. Check if the evidence directory parameter is provided
if [ -z "$1" ]; then
    echo "Erro: Caminho para a pasta de evidências não especificado."
    echo "Uso: ./run_tests.sh <caminho_da_pasta_de_evidencias>"
    echo "Exemplo: ./run_tests.sh ./minhas-evidencias"
    exit 1
fi

TARGET_DIR="$1"

# 2. Create the target directory if it doesn't exist
if [ ! -d "$TARGET_DIR" ]; then
    echo "Criando pasta de evidências: $TARGET_DIR..."
    mkdir -p "$TARGET_DIR"
fi

# Convert to absolute path to prevent resolve issues in Playwright
ABS_TARGET_DIR=$(cd "$TARGET_DIR" && pwd)

echo "Pasta de evidências configurada: $ABS_TARGET_DIR"

# 3. Compile assets to ensure the latest changes are active
echo "Compilando assets do frontend (npm run build)..."
npm run build
if [ $? -ne 0 ]; then
    echo "Erro ao compilar assets. Abortando execução de testes."
    exit 1
fi

# 4. Run Playwright E2E tests passing the E2E_EVIDENCE_DIR environment variable
echo "Iniciando execução da suíte de testes E2E com Playwright..."
E2E_EVIDENCE_DIR="$ABS_TARGET_DIR" npx playwright test

TEST_STATUS=$?

if [ $TEST_STATUS -eq 0 ]; then
    echo "--------------------------------------------------------"
    echo "Sucesso! Todos os testes E2E passaram."
    echo "Evidências salvas com sucesso em: $ABS_TARGET_DIR"
    echo "Arquivos gerados:"
    ls -l "$ABS_TARGET_DIR"
    echo "--------------------------------------------------------"
else
    echo "--------------------------------------------------------"
    echo "Erro: Alguns testes E2E falharam (código de saída: $TEST_STATUS)."
    echo "Verifique os logs detalhados e as capturas parciais em: $ABS_TARGET_DIR"
    echo "--------------------------------------------------------"
fi

exit $TEST_STATUS
