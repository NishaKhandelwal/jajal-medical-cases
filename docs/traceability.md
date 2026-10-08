# Traceability matrix

| ID | Requirement (input) | Implementation (output) | Verification |
|---|---|---|---|
| REQ-001 | Admin shall be able to create a case | `CaseController@store` + `cases/create.blade.php` (+ `Api\CaseController@store`), `CaseRequest` | Feature tests: `test_admin_can_create_a_case`, `test_admin_can_create_a_case_via_api` |
| REQ-002 | Case Number shall be unique | Unique DB index (`create_cases_table` migration) + `Rule::unique()->ignore()` in `CaseRequest` | Feature test: `test_duplicate_case_number_fails_with_422` (+ `test_update_may_keep_its_own_case_number`) |
| REQ-003 | Only Admin shall delete cases | `MedicalCasePolicy@delete` + `Gate::authorize` in both `destroy()` methods | Feature test: `test_viewer_cannot_delete_a_case` (403) |
