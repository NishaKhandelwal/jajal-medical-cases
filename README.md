# Medical Case Management (Jajal Medical – Intern Assignment)

A small Laravel app to track medical-device implant cases from Draft to Completed.
Dummy data only – no real patient information.

**Stack:** Laravel 11/12, PHP 8.2+, MySQL, Blade + Tailwind (Laravel Breeze), Laravel Sanctum (API tokens), PHPUnit.

## Features
- Login / logout (Breeze), wrong credentials show an error message
- Dashboard counts: Total, Planning, In Review, Approved, Completed
- Case list: pagination (10/page), search (case number / surgeon), filter by status and priority, column sorting
- Create / edit (one shared form), details page, delete (Admin only – enforced on the server by a Policy)
- JSON REST API with Sanctum tokens and one consistent response shape
- Roles: **Admin** (everything) and **Viewer** (read only)

## Setup
```bash
git clone <your-repo-url> && cd <repo>
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
# edit .env: DB_CONNECTION=mysql, DB_DATABASE=jajal_cases, DB_USERNAME, DB_PASSWORD
# create the empty database first:  mysql -u root -p -e "CREATE DATABASE jajal_cases"
php artisan migrate --seed
php artisan serve        # http://127.0.0.1:8000
```

## Test login credentials (dummy)
| Role | Email | Password |
|---|---|---|
| Admin | admin@example.com | Admin@12345 |
| Viewer | viewer@example.com | Viewer@12345 |

(They are read from `SEED_*` variables in `.env.example`; change them there if you like.)

## Running tests
```bash
php artisan test
```
Tests use an in-memory SQLite database (see `phpunit.xml`), so the `pdo_sqlite` PHP extension must be enabled.

## API
Base URL `/api`. Send `Accept: application/json`; for protected routes send `Authorization: Bearer <token>`.

| Method | Endpoint | Purpose | Who |
|---|---|---|---|
| POST | /api/login | Get token (`email`, `password`) | Anyone |
| POST | /api/logout | Revoke current token | Logged in |
| GET | /api/cases | Paginated list (`?search=&status=&priority=&sort=&direction=&page=`) | Admin, Viewer |
| POST | /api/cases | Create | Admin |
| GET | /api/cases/{id} | Show one | Admin, Viewer |
| PUT | /api/cases/{id} | Update | Admin |
| DELETE | /api/cases/{id} | Delete | Admin |

Response shape: `{ "success": true|false, "data": ..., "message": "...", "errors": {field: [msgs]} }`
(`errors` only on 422). Status codes: 200, 201, 401, 403, 404, 422.
A Postman collection is in `docs/postman_collection.json`.

## Docs
- `docs/database.md` – tables and relationships
- `docs/traceability.md` – requirement → code → test
- `docs/deployment.md` – AWS hosting outline (bonus)

## Security notes
Policy-based authorization (web + API), Form Request validation, `$fillable` on models (`role` is not fillable),
Eloquent only (no raw SQL with user input), CSRF on web forms, sort columns whitelisted, `.env` not committed.

## Assumptions & known limitations
- Model is named `MedicalCase` because `Case` is a reserved PHP word; table is `cases`.
- Breeze's public `/register` route still exists; new users get the `viewer` role. In production I would remove it.
- Only two roles are implemented. Next step for the Engineer bonus role: add `ROLE_ENGINEER`, allow it in `MedicalCasePolicy@create/update`, keep `delete` Admin-only.
- Not done: Nager.Date holiday lookup, live deployment. For the holiday feature I would use `Http::timeout(3)->get(config('services.holidays.url'))` inside try/catch and show nothing on failure.
- No audit log of status changes yet.
