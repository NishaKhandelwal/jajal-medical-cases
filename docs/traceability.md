# Traceability matrix

| ID | Requirement (input) | Implementation (output) | Verification |
|---|---|---|---|
| REQ-001 | Admin shall be able to create a case | `CaseController@store` + `cases/create.blade.php` (+ `Api\CaseController@store`), `CaseRequest` | `tests/Feature/CaseFeatureTest.php`: `test_admin_can_create_a_case`, `test_admin_can_create_a_case_via_api` |
| REQ-002 | Case Number shall be unique | Unique DB index (`create_cases_table` migration) + `Rule::unique()->ignore()` in `CaseRequest` | `tests/Feature/CaseFeatureTest.php`: `test_duplicate_case_number_fails_with_422`, `test_update_may_keep_its_own_case_number` |
| REQ-003 | Only Admin shall delete cases | `MedicalCasePolicy@delete` + `Gate::authorize` in both `destroy()` methods | `tests/Feature/CaseFeatureTest.php`: `test_viewer_cannot_delete_a_case` (403) |
| REQ-004 (extra) | Every status change shall be recorded with previous status, new status, user and time | `MedicalCase::booted()` updated hook, `CaseStatusHistory`, `case_status_histories` migration | `tests/Feature/CaseFeatureTest.php`: `test_status_change_is_recorded_with_previous_and_new_status`, `test_status_change_via_api_is_recorded`, `test_updating_other_fields_does_not_create_status_history` |
