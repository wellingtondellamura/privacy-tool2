# BR-003: Evaluation Round Closure Flow

## 1. Description
This rule governs the logic executed when an Evaluation Round transitions to the `closed` state. It details how multiple individual inspection results are aggregated into a single, definitive `RoundSnapshot`.

## 2. Pre-conditions
- The Evaluation Round must have at least one Inspection in the `closed` state.
- Only closed inspections with valid `resultSnapshots` are considered.

## 3. Post-conditions
- A `RoundSnapshot` is created containing the merged data payload.
- The `EvaluationRound` status is updated to `closed`.
- `closed_at` timestamp is set to the current time.

## 4. Flow & Logic

1. **Fetch Inspections:** Retrieve all closed inspections for the round. If none exist, abort (return `false`).
2. **Template Extraction:** Extract the JSON payload structure from the first inspection to use as a template for Categories, Sections, and Questions.
3. **Consensus Strategy Resolution:** 
   - Check the `ConsensusModel` defined in the Project settings.
   - Resolve the consensus answers for all questions using the `strategy()->resolve()` method. This returns an array mapping `question_id` to the final agreed-upon `AnswerLevel`.
4. **Question Merging Loop:** 
   - For every question in the template, gather the scores from all individual inspection payloads.
   - Calculate divergence using `DivergenceService::forQuestion()`.
   - **Score Assignment:**
     - **IF** a manual consensus was reached (exists in the resolved answers map), use the score of the resolved `AnswerLevel`.
     - **ELSE**, calculate the average score of all individual answers and infer the `AnswerLevel` dynamically based on the average score (>=91: high, >=41: medium, <41: low).
5. **Recalculation:**
   - With the finalized question scores, dynamically recalculate Category scores using `AggregationService::categoryScore()`.
   - Recalculate Section scores using `AggregationService::sectionScore()`.
   - Recalculate the Global score as the average of the finalized Section scores.
6. **Payload Construction:** 
   - Assemble the final JSON payload containing `global_score`, `medal`, nested `sections`, divergence data, and metadata (`inspection_count`, `user_count_total`).
7. **Database Transaction:** Save the `RoundSnapshot` and update the `EvaluationRound` within a single DB transaction to ensure data integrity.

## 5. Exceptions / Edge Cases
- If all inspections are empty or have no payload, it returns a zeroed structure `['global_score' => 0, 'medal' => ['name' => 'incipient'], 'sections' => []]`.

## 6. Source Evidence
- **File:** `app/Actions/CloseRoundAction.php`
- **Method:** `execute()`, `calculateRoundPayload()`

---
**Confidence Level:** ★★★★★ (Extracted directly from the Action class handling this use case).
