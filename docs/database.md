# Database design (MySQL)

```
users                          cases
-----                          -----
id (PK)              1 ──── *  id (PK)
name                           case_number   UNIQUE
email  UNIQUE                  patient_reference
password (hashed)              surgeon_name
role  (admin|viewer)           implant_type
remember_token                 status        INDEX (Draft…Cancelled)
timestamps                     priority      (Low|Medium|High)
                               surgery_date  NULLABLE
                               created_by    FK -> users.id (RESTRICT)
                               timestamps
```

**Relationship:** one user creates many cases (`cases.created_by -> users.id`).
A user who owns cases cannot be deleted (`restrictOnDelete`), which protects the audit trail.

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

## users (extra column)
| Column | Type | Notes |
|---|---|---|
| role | varchar(20) | default `viewer`; `admin` or `viewer`. Not mass-assignable. |

Status/priority are validated against allowed lists in `App\Models\MedicalCase` (constants) via the Form Request.
`personal_access_tokens` (Sanctum) stores API tokens.
