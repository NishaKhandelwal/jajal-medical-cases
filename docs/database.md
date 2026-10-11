# Database design (MySQL)

```
users                     cases                          case_status_histories
-----                     -----                          ---------------------
id (PK)          1 ── *   id (PK)               1 ── *   id (PK)
name                      case_number  UNIQUE            medical_case_id  FK -> cases.id (CASCADE)
email  UNIQUE             patient_reference              old_status       NULLABLE
password (hashed)         surgeon_name                   new_status
role (admin|viewer)       implant_type                   changed_by       FK -> users.id (SET NULL)
remember_token            status       INDEX             created_at / updated_at
timestamps                priority
                          surgery_date NULLABLE
                          created_by   FK -> users.id (RESTRICT)
                          timestamps
```

**Relationships**
- One user creates many cases (`cases.created_by -> users.id`, `restrictOnDelete`). A user who owns cases cannot be deleted.
- One case has many status-history rows (`case_status_histories.medical_case_id -> cases.id`, `cascadeOnDelete`). Deleting a case deletes its history.
- One user can change status on many cases (`case_status_histories.changed_by -> users.id`, `nullOnDelete`). Deleting a user keeps the history and clears the user reference.

## users (extra column)
| Column | Type | Notes |
|---|---|---|
| role | varchar(20) | default `viewer`; `admin` or `viewer`. Not mass-assignable. |

## cases
| Column | Type | Notes |
|---|---|---|
| id | bigint unsigned PK | auto-increment |
| case_number | varchar(50) | **unique index**, e.g. CASE-0001 |
| patient_reference | varchar(100) | dummy code only |
| surgeon_name | varchar(150) | |
| implant_type | varchar(150) | |
| status | varchar(20) | **indexed**; Draft, Planning, In Review, Approved, Completed, Cancelled |
| priority | varchar(20) | Low, Medium, High |
| surgery_date | date, nullable | |
| created_by | bigint unsigned FK | set from the logged-in user, never from input |
| created_at / updated_at | timestamp | Laravel timestamps |

## case_status_histories
| Column | Type | Notes |
|---|---|---|
| id | bigint unsigned PK | |
| medical_case_id | bigint unsigned FK | cascade on delete |
| old_status | varchar(20), nullable | status before the change |
| new_status | varchar(20) | status after the change |
| changed_by | bigint unsigned FK, nullable | user who made the change; set null on user delete |
| created_at / updated_at | timestamp | when the change happened |

Index on `(medical_case_id, created_at)`. A row is written only when `status` actually changes (web or API).

Status and priority are validated against the allowed lists in `App\Models\MedicalCase` (constants) via the Form Request.
`personal_access_tokens` (Sanctum) stores API tokens.
