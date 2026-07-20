# BR-001: Scoring and Aggregation Formulas

## 1. Description
This rule defines how individual question scores are aggregated upwards to calculate Category scores, Section scores, Global scores, and how these numerical values are translated into gamified Medals.

## 2. Objective
To provide a normalized 0-100 score at every hierarchical level of the questionnaire, ensuring that sections with more questions do not disproportionately outweigh sections with fewer questions (since section scores are averages of category scores, not sums of question scores).

## 3. Mathematical Formulas & Pseudocode

### 3.1 Category Score (`categoryScore`)
Calculates the score for a specific category based on the questions answered within it.
- **Formula:** `Round( (Sum of Question Scores) / (Total Questions * 100) * 100 )`
- **Simplified Formula:** Because question scores are out of 100, the formula mathematically reduces to the average of question scores.
- **Pseudocode:**
  ```text
  IF totalQuestions == 0 THEN RETURN 0
  sum = SUM(scores)
  RETURN ROUND((sum / (totalQuestions * 100)) * 100)
  ```

### 3.2 Category Percentage (`categoryPercentage`)
Calculates the completion percentage of a category.
- **Formula:** `(Answered Questions / Total Questions) * 100`

### 3.3 Section Score (`sectionScore`)
Calculates the score for a section. Crucially, this is the average of **Category Scores**, not a direct average of all questions in the section.
- **Formula:** `Round( Sum of Category Scores / Total Categories )`

### 3.4 Global Score
Calculates the overall compliance score of an Inspection or Evaluation Round.
- **Formula:** `Round( Sum of Section Scores / Total Sections )`

### 3.5 Medal Assignment (`medalForScore`)
Translates a 0-100 score (usually at the Section or Global level) into a tier.
- **Rules (Decision Table):**
  | Condition (Score) | Output Medal |
  |-------------------|--------------|
  | 91 to 100         | Gold         |
  | 61 to 90          | Silver       |
  | 41 to 60          | Bronze       |
  | 0 to 40           | Incipient    |

## 4. Source Evidence
- **File:** `app/Services/AggregationService.php`
- **Methods:** `categoryScore()`, `sectionScore()`, `medalForScore()`
- **File:** `app/Actions/CloseRoundAction.php` (Line 150-155 for Global Score calculation).

---
**Confidence Level:** ★★★★★ (Extracted directly from active calculation services).
