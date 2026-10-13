# BR-002: Divergence Calculation and Classification

## 1. Description
When multiple users answer the same questionnaire within the same Evaluation Round, their individual answers for a specific question might differ. This rule dictates how the system mathematically calculates the divergence (variance) among those answers and categorizes the severity of the disagreement.

## 2. Objective
To highlight questions where the team lacks consensus, guiding the manual or automated review process during the consolidation phase.

## 3. Mathematical Formulas

### 3.1 Variance Calculation (`variance`)
The system computes the population variance of the numeric scores assigned to the answers.
- **Formula:** `(1/N) * Σ(xi - mean)²`
- **Variables:**
  - `N`: Number of respondents.
  - `xi`: The score of an individual's answer.
  - `mean`: The average score for that question across all respondents.
- **Behavior on Edge Cases:**
  - If `N == 0` (no answers), variance is `0`.

### 3.2 Divergence Classification (`classify`)
The classification uses the **standard deviation** (σ = √variance), which is on the same 0–100 scale as the scores. Null scores ("Não se aplica") are ignored.
- **Rules (Decision Table):**
  | Standard deviation | Output Classification | Example |
  |--------------------|-----------------------|---------|
  | `<= 10`            | Low (baixa)           | 100 × 90 (σ = 5) |
  | `> 10` to `30`     | Medium (média)        | 100 × 50 (σ = 25) |
  | `> 30`             | High (alta)           | 100 × 0 (σ = 50) |

> Before 2026-10, the same thresholds were applied to the variance, so any disagreement between two evaluators was classified as "high".

## 4. Output
For any given question with multiple answers, the system outputs an array:
```json
{
  "variance": 625.0,
  "std_dev": 25.0,
  "classification": "medium"
}
```

## 5. Source Evidence
- **File:** `app/Services/DivergenceService.php`
- **Methods:** `variance()`, `classify()`, `forQuestion()`

---
**Confidence Level:** ★★★★★ (Extracted directly from active logic).
