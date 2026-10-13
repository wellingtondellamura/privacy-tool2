# BR-001: Scoring and Aggregation Formulas

## 1. Description
This rule defines how individual question scores are aggregated upwards to calculate Category scores, Section scores, Global scores, and how these numerical values are translated into gamified Medals.

## 2. Objective
To provide a normalized 0-100 score at every hierarchical level of the questionnaire, ensuring that sections with more questions do not disproportionately outweigh sections with fewer questions (since section scores are averages of category scores, not sums of question scores).

## 3. Mathematical Formulas & Pseudocode

### 3.1 Category Score (`categoryScore`)
Calculates the score for a specific category based on the applicable answered questions.
- **Answer values:** high = 100, medium = 50, low = 0, other ("Não se aplica") = null.
- **Formula:** `Round( (Sum of applicable scores) / (Applicable answered questions * 100) * 100 )`
- "Não se aplica" answers and unanswered questions are left out of the denominator (MANUAL §9.4).
- A category without any applicable answer has a `null` score and is ignored by the section average.
- **Pseudocode:**
  ```text
  applicable = scores WHERE score IS NOT NULL
  IF count(applicable) == 0 THEN RETURN null
  RETURN ROUND((SUM(applicable) / (count(applicable) * 100)) * 100)
  ```

### 3.2 Category Percentage (`categoryPercentage`)
Calculates the completion percentage of a category.
- **Formula:** `(Answered Questions / Total Questions) * 100`

### 3.3 Section Score (`sectionScore`)
Calculates the score for a section. Crucially, this is the average of **Category Scores**, not a direct average of all questions in the section.
- **Formula:** `Round( Sum of Category Scores / Total Categories )`, ignoring categories with a `null` score (returns `null` when none is left).

### 3.4 Global Score
Calculates the overall compliance score of an Inspection or Evaluation Round.
- **Formula:** `Round( Sum of Section Scores / Total Sections )`, ignoring sections with a `null` score (0 when none is left).

### 3.5 Medal Assignment (`medalForScore`)
Translates a 0-100 score (usually at the Section or Global level) into a tier.
- **Rules (Decision Table):**
  | Condition (Score) | Output Medal |
  |-------------------|--------------|
  | 91 to 100         | Gold         |
  | 61 to 90          | Silver       |
  | 41 to 60          | Bronze       |
  | 0 to 40           | Incipient    |
  | null (no data)    | no_data      |

## 4. Source Evidence
- **File:** `app/Services/AggregationService.php`
- **Methods:** `categoryScore()`, `sectionScore()`, `medalForScore()`
- **File:** `app/Actions/CloseRoundAction.php` (Line 150-155 for Global Score calculation).

---
**Confidence Level:** ★★★★★ (Extracted directly from active calculation services).
