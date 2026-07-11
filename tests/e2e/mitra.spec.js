// tests/e2e/mitra.spec.js
import { test, expect } from '@playwright/test';
import { execSync } from 'child_process';
import path from 'path';
import fs from 'fs';

const EVIDENCE_DIR = process.env.E2E_EVIDENCE_DIR 
  ? path.resolve(process.env.E2E_EVIDENCE_DIR)
  : path.join('C:', 'Users', 'wellington', '.gemini', 'antigravity-ide', 'brain', '94e4aa89-bbc9-40f9-8174-c0697d8320f2', 'evidences');

// Helper to seed the database with a specific consensus model
function seedDatabase(consensusModel) {
  console.log(`Seeding database with consensus model: ${consensusModel}...`);
  execSync(`php artisan db:seed --class=E2ETestSeeder`, {
    env: {
      ...process.env,
      E2E_CONSENSUS_MODEL: consensusModel,
    },
    stdio: 'inherit'
  });
}

// Ensure evidence directory exists
if (!fs.existsSync(EVIDENCE_DIR)) {
  fs.mkdirSync(EVIDENCE_DIR, { recursive: true });
}

// Credentials for our 5 E2E members
const USERS = [
  { email: 'e2e_owner@test.com', name: 'E2E Owner' },
  { email: 'e2e_eval1@test.com', name: 'E2E Evaluator 1' },
  { email: 'e2e_eval2@test.com', name: 'E2E Evaluator 2' },
  { email: 'e2e_eval3@test.com', name: 'E2E Evaluator 3' },
  { email: 'e2e_eval4@test.com', name: 'E2E Evaluator 4' },
];

async function fillQuestionnaire(browser, userEmail, userIndex, consensusModel) {
  console.log(`[User ${userIndex}] Starting process for ${userEmail} in ${consensusModel}...`);
  // Use a new clean browser context for each user session
  const context = await browser.newContext();
  const page = await context.newPage();
  
  page.on('console', msg => console.log(`[BROWSER LOG ${userEmail}]: ${msg.text()}`));
  page.on('pageerror', err => console.error(`[BROWSER EXCEPTION ${userEmail}]: ${err.message}`));
  
  // 1. Login
  console.log(`[User ${userIndex}] Navigating to /login...`);
  await page.goto('/login');
  console.log(`[User ${userIndex}] Logging in...`);
  await page.fill('input[type="email"]', userEmail);
  await page.fill('input[type="password"]', 'password123');
  await page.click('button[type="submit"]');
  console.log(`[User ${userIndex}] Waiting for redirect...`);
  await page.waitForURL(/\/(projects|dashboard)/);
  
  // 2. Select E2E Project
  console.log(`[User ${userIndex}] Selecting E2E Project...`);
  await page.click(`text=E2E Test Project (${consensusModel.charAt(0).toUpperCase() + consensusModel.slice(1)})`);
  
  // 3. Select E2E Round
  console.log(`[User ${userIndex}] Selecting E2E Round...`);
  await page.click('text=E2E Rodada de Teste E2E');
  
  // 4. Click to evaluate (view questionnaire)
  const userName = USERS[userIndex].name;
  console.log(`[User ${userIndex}] Finding inspection row for ${userName}...`);
  const row = page.locator('ul').first().locator('li', { hasText: userName });
  console.log(`[User ${userIndex}] Clicking View/Ver button...`);
  await row.locator('button:has-text("Ver"), button:has-text("View")').click();
  console.log(`[User ${userIndex}] Waiting for inspections page load...`);
  await page.waitForURL('**/inspections/*');
  
  // 5. Select first category to ensure UI renders responses visually
  console.log(`[User ${userIndex}] Clicking first category on sidebar navigation...`);
  await page.locator('nav button').first().click();
  await page.waitForTimeout(500);
  
  // Capture a screenshot of the filled questionnaire before closing
  console.log(`[User ${userIndex}] Taking screenshot of filled questionnaire...`);
  await page.screenshot({ path: path.join(EVIDENCE_DIR, `1_filled_questionnaire_${consensusModel}_user_${userIndex}.png`) });
  
  // 6. Close Inspection
  console.log(`[User ${userIndex}] Closing inspection...`);
  await page.locator('button', { hasText: /Finalizar|Close|Fechar/i }).click();
  console.log(`[User ${userIndex}] Confirming inspection closure...`);
  await page.locator('button', { hasText: /Sim|Yes/i }).click();
  
  // Wait for the redirect to results page
  console.log(`[User ${userIndex}] Waiting for results page redirect...`);
  await page.waitForURL('**/results');
  // Dismiss the "OK" success dialog
  await page.locator('button:has-text("OK")').click();
  console.log(`[User ${userIndex}] Closed inspection successfully!`);
  
  await context.close();
}

test.describe('E2E Test Mitra — 5 Members Round & Consensus Models', () => {

  // Test Case 1: Consensus Model - Owner Decides (Dono Decide)
  test('Consenso: owner_decides - Dono resolve divergências manualmente', async ({ browser }) => {
    const consensusModel = 'owner_decides';
    seedDatabase(consensusModel);
    
    // 1. All 5 users fill out their active inspections
    console.log(`[${consensusModel}] Filling out inspections for all 5 users...`);
    for (let i = 0; i < USERS.length; i++) {
      await fillQuestionnaire(browser, USERS[i].email, i, consensusModel);
    }
    
    // 2. Owner logs in, goes to review round, and resolves the divergence
    console.log(`[${consensusModel}] Owner logging in to review and close round...`);
    const context = await browser.newContext();
    const page = await context.newPage();
    
    page.on('console', msg => console.log(`[BROWSER LOG Owner]: ${msg.text()}`));
    page.on('pageerror', err => console.error(`[BROWSER EXCEPTION Owner]: ${err.message}`));
    
    await page.goto('/login');
    await page.fill('input[type="email"]', USERS[0].email);
    await page.fill('input[type="password"]', 'password123');
    await page.click('button[type="submit"]');
    await page.waitForURL(/\/(projects|dashboard)/);
    
    await page.click(`text=E2E Test Project (Owner_decides)`);
    await page.click('text=E2E Rodada de Teste E2E');
    
    // Enter Review Phase
    await page.locator('button', { hasText: /Revisão|Review/i }).click({ force: true });
    // Dismiss the success popup modal
    await page.locator('button:has-text("OK")').click({ force: true });
    await page.waitForTimeout(500);
    
    // Continue to Review URL
    await page.locator('button', { hasText: /Revisar|Review/i }).click({ force: true });
    await page.waitForURL('**/rounds/*/review');
    
    // Evidence: Review Dashboard showing divergence list
    await page.screenshot({ path: path.join(EVIDENCE_DIR, `2_review_dashboard_divergence_${consensusModel}.png`) });
    
    // Select the divergent Question 1 card on the list and click "Alto" (high) to resolve it
    console.log(`[${consensusModel}] Clicking resolve decision button...`);
    await page.getByRole('button', { name: /100 pts Alto|100 pts High/i }).click({ force: true });
    console.log(`[${consensusModel}] Verifying reset button visibility...`);
    await expect(page.getByRole('button', { name: /Desfazer Consolidação|Reset/i })).toBeVisible();
    
    // Dismiss the success popup modal
    console.log(`[${consensusModel}] Dismissing success popup modal...`);
    await page.locator('button:has-text("OK")').click({ force: true });
    await page.waitForTimeout(500);
    
    // Evidence: Resolved consensus screenshot
    await page.screenshot({ path: path.join(EVIDENCE_DIR, `3_resolved_divergence_${consensusModel}.png`) });
    
    // Submit closing details
    console.log(`[${consensusModel}] Filling diagnosis...`);
    await page.locator('textarea').last().fill('Diagnóstico de teste E2E: Concluímos que os controles são satisfatórios após consenso.');
    
    // Check "Publish immediately"
    console.log(`[${consensusModel}] Checking publish immediately checkbox...`);
    await page.locator('label', { hasText: /Publicar/i }).click({ force: true });
    
    // Consolidate & Close Round
    console.log(`[${consensusModel}] Clicking consolidar button...`);
    await page.getByRole('button', { name: /Consolidar e Fechar|Consolidate/i }).click({ force: true });
    console.log(`[${consensusModel}] Waiting for rounds redirect...`);
    await page.waitForURL('**/rounds/*');
    
    // Evidence: Closed Round details page
    await page.screenshot({ path: path.join(EVIDENCE_DIR, `4_closed_round_dashboard_${consensusModel}.png`) });
    
    // Go to public directory to verify
    await page.goto('/tools');
    await expect(page.locator('text=E2E Test Project (Owner_decides)')).toBeVisible();
    await page.screenshot({ path: path.join(EVIDENCE_DIR, `5_public_directory_${consensusModel}.png`) });
    
    await context.close();
  });

  // Test Case 2: Consensus Model - Majority Vote (Voto Majoritário)
  test('Consenso: majority_vote - Sistema resolve por voto da maioria com desempate conservador', async ({ browser }) => {
    const consensusModel = 'majority_vote';
    seedDatabase(consensusModel);
    
    // 1. All 5 users fill out their active inspections
    console.log(`[${consensusModel}] Filling out inspections for all 5 users...`);
    for (let i = 0; i < USERS.length; i++) {
      await fillQuestionnaire(browser, USERS[i].email, i, consensusModel);
    }
    
    // 2. Owner logs in, puts round in review, and checks the majority vote result
    console.log(`[${consensusModel}] Owner logging in to check majority resolution...`);
    const context = await browser.newContext();
    const page = await context.newPage();
    
    await page.goto('/login');
    await page.fill('input[type="email"]', USERS[0].email);
    await page.fill('input[type="password"]', 'password123');
    await page.click('button[type="submit"]');
    
    await page.click(`text=E2E Test Project (Majority_vote)`);
    await page.click('text=E2E Rodada de Teste E2E');
    
    // Enter Review Phase
    await page.locator('button', { hasText: /Revisão|Review/i }).click({ force: true });
    // Dismiss the success popup modal
    await page.locator('button:has-text("OK")').click({ force: true });
    await page.waitForTimeout(500);
    
    // Continue to Review URL
    await page.locator('button', { hasText: /Revisar|Review/i }).click({ force: true });
    await page.waitForURL('**/rounds/*/review');
    
    // Evidence: Review Dashboard showing Majority resolved option
    // Since: User 0 (owner) -> medium, User 1 -> medium, User 2 -> low, User 3 -> low, User 4 -> high.
    // Votes: medium = 2, low = 2, high = 1.
    // Tie is resolved to lowest (low).
    await page.screenshot({ path: path.join(EVIDENCE_DIR, `6_review_majority_tie_${consensusModel}.png`) });
    
    // Close the round immediately (System will apply majority calculation on executing CloseRoundAction)
    console.log(`[${consensusModel}] Filling diagnosis...`);
    await page.locator('textarea').last().fill('Diagnóstico voto majoritário: Empates resolvidos de forma conservadora.');
    console.log(`[${consensusModel}] Clicking consolidar button...`);
    await page.getByRole('button', { name: /Consolidar e Fechar|Consolidate/i }).click({ force: true });
    console.log(`[${consensusModel}] Waiting for rounds redirect...`);
    await page.waitForURL('**/rounds/*');
    
    // Dismiss the success popup modal
    await page.locator('button:has-text("OK")').click({ force: true });
    await page.waitForTimeout(500);
    
    // Evidence: Closed Round showing final consolidated results
    await page.screenshot({ path: path.join(EVIDENCE_DIR, `7_closed_round_majority_${consensusModel}.png`) });
    
    // Generate official badge
    await page.locator('button:has-text("Selo"), button:has-text("Badge")').click({ force: true });
    await page.waitForTimeout(1000);
    await page.screenshot({ path: path.join(EVIDENCE_DIR, `7_badge_generated_${consensusModel}.png`) });
    
    await context.close();
  });

  // Test Case 3: Consensus Model - Evaluator Convergence (Convergência dos Avaliadores)
  test('Consenso: evaluator_convergence - Membros discutem em chat para alinhar notas', async ({ browser }) => {
    const consensusModel = 'evaluator_convergence';
    seedDatabase(consensusModel);
    
    // 1. All 5 users fill out their active inspections
    console.log(`[${consensusModel}] Filling out inspections for all 5 users...`);
    for (let i = 0; i < USERS.length; i++) {
      await fillQuestionnaire(browser, USERS[i].email, i, consensusModel);
    }
    
    // 2. Owner logs in, puts round in review, and posts a discussion thread comment
    console.log(`[${consensusModel}] Owner logging in to discuss divergence...`);
    const context = await browser.newContext();
    const page = await context.newPage();
    
    await page.goto('/login');
    await page.fill('input[type="email"]', USERS[0].email);
    await page.fill('input[type="password"]', 'password123');
    await page.click('button[type="submit"]');
    await page.waitForURL(/\/(projects|dashboard)/);
    
    await page.click(`text=E2E Test Project (Evaluator_convergence)`);
    await page.click('text=E2E Rodada de Teste E2E');
    
    // Enter Review Phase
    await page.locator('button', { hasText: /Revisão|Review/i }).click({ force: true });
    // Dismiss the success popup modal
    await page.locator('button:has-text("OK")').click({ force: true });
    await page.waitForTimeout(500);
    
    // Continue to Review URL
    await page.locator('button', { hasText: /Revisar|Review/i }).click({ force: true });
    await page.waitForURL('**/rounds/*/review');
    
    // Post a discussion comment
    const commentBox = page.locator('textarea').first();
    await commentBox.fill('Atenção equipe: Divergimos na Questão 1. Por favor verifiquem se o controle existe no código.');
    await page.click('form button[type="submit"]', { force: true });
    await expect(page.locator('text=Atenção equipe: Divergimos na Questão 1')).toBeVisible();
    
    // Dismiss the success popup modal
    console.log(`[${consensusModel}] Dismissing comment success popup modal...`);
    await page.locator('button:has-text("OK")').click({ force: true });
    await page.waitForTimeout(500);
    
    // Evidence: Chat discussion screenshot showing the posted message
    await page.screenshot({ path: path.join(EVIDENCE_DIR, `8_review_discussion_chat_${consensusModel}.png`) });
    
    // Close the round
    console.log(`[${consensusModel}] Filling diagnosis...`);
    await page.locator('textarea').last().fill('Diagnóstico de convergência: Rodada fechada após debate da equipe.');
    console.log(`[${consensusModel}] Clicking consolidar button...`);
    await page.getByRole('button', { name: /Consolidar e Fechar|Consolidate/i }).click({ force: true });
    console.log(`[${consensusModel}] Waiting for rounds redirect...`);
    await page.waitForURL('**/rounds/*');
    
    // Evidence: Final page
    await page.screenshot({ path: path.join(EVIDENCE_DIR, `9_closed_round_convergence_${consensusModel}.png`) });
    
    await context.close();
  });

});
