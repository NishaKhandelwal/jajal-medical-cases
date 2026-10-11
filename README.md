# Medical Case Management

**Jajal Medical — Software Developer Intern Practical Assignment**

Medical Case Management is a Laravel web application for tracking medical-device implant cases from the initial draft through planning, review, approval, and completion. It provides a dashboard, case records, role-based access, a status-change audit log, and a REST API. The project uses fictional sample data only; it does not contain real patient information.

---

## Implementation Status

### Core requirements — implemented

| Requirement | Status | Location |
|---|---|---|
| Laravel + MySQL project with `users` and `cases` migrations | Done | `database/migrations/` |
| `created_by` foreign key, unique Case Number, and status index | Done | Cases migration |
| Seeder and factory for 2 users and at least 20 cases | Done (25 cases) | `database/seeders/`, `database/factories/` |
| Login/logout with invalid-credential handling | Done | Laravel Breeze, `routes/auth.php` |
| Authentication for case pages | Done | `routes/web.php` |
| Dashboard with total and status counts | Done | `DashboardController` |
| Paginated case list with search and status filtering | Done | `CaseController@index`, `MedicalCase::scopeFilter` |
| Role-based action visibility | Done | Blade `@can` checks |
| Shared create/edit form with field-level errors | Done | `cases/_form.blade.php` |
| Case details, creator, and timestamps | Done | `cases/show.blade.php` |
| Server-side authorization for deletion | Done | `MedicalCasePolicy`, `Gate::authorize` |
| REST API with 7 endpoints and Sanctum authentication | Done | `routes/api.php` |
| Consistent JSON responses and HTTP status codes | Done | `ApiResponse`, `bootstrap/app.php` |
| Form Request validation and unique-rule handling on edit | Done | `CaseRequest` |
| Policies, mass-assignment protection, and CSRF protection | Done | Policies, models, Blade views |
| `.env.example` without committed secrets | Done | `.env.example` |
| At least 6 feature tests | Done (more than 6) | `tests/Feature/CaseFeatureTest.php` |
| Database documentation, traceability, and Postman collection | Done | `docs/` |

### Bonus tasks

| Bonus task | Status | Notes |
|---|---|---|
| Priority filtering and column sorting | Implemented | Supported by both the web list and API; sort columns are whitelisted. |
| AWS deployment plan | Implemented as documentation | See `docs/deployment.md`. The application is not deployed to AWS. |
| Engineer role (create, edit, change status, no delete) | Not implemented | Implementation plan below. |
| Public-holiday lookup using Nager.Date | Not implemented | Implementation plan below. |
| Live deployment | Not implemented | No live URL is available. |

### Additional features

- **Status-change audit log:** Records the previous status, new status, user, and timestamp for changes made through the web interface and API. Unrelated edits do not create status-history entries.
- **Login rate limiting:** Applied to the API login route (`throttle:10,1`); web login uses Breeze's default rate limiting.
- **Interactive dashboard cards:** Each status card links to the corresponding filtered case list.

---

## What the Application Does

### Login and user roles

Users must sign in before accessing case pages. The application currently supports two roles:

| Action | Admin | Viewer |
|---|---|---|
| View cases and dashboard | Yes | Yes |
| Create a case | Yes | No |
| Edit a case or change its status | Yes | No |
| Delete a case | Yes | No |

Permissions are enforced on the server as well as in the interface. For example, a Viewer submitting an invalid create request receives `403 Forbidden`, because authorization is checked before validation.

### Dashboard and case management

The dashboard displays the total number of cases and counts for Planning, In Review, Approved, and Completed.

The case list displays 10 records per page and supports:

- Search by Case Number or Surgeon Name.
- Filtering by Status or Priority.
- Sorting supported columns in ascending or descending order.

The shared create/edit form handles validation and displays errors beside the relevant fields. Each case includes a unique Case Number, fictional Patient Reference, Surgeon Name, Implant Type, Status, Priority, and optional Surgery Date.

The application automatically records the logged-in user as the creator and maintains creation and update timestamps. The details page displays the full record and its status-change history.

### REST API

The API uses Laravel Sanctum for token-based authentication. It supports listing, viewing, creating, updating, and deleting cases, with the same Admin and Viewer permissions as the web interface. Case listings support pagination, search, filtering, and sorting.

---

## Technology Stack

- **Language and framework:** PHP 8.4, Laravel 12
- **Database:** MySQL
- **Frontend:** Laravel Blade, Tailwind CSS, Alpine.js
- **Web authentication:** Laravel Breeze
- **API authentication:** Laravel Sanctum
- **Automated testing:** PHPUnit
- **Development tools:** Composer, Node.js/npm, Git, Postman

---

## Run the Application Locally

### Requirements

- PHP 8.4 with the extensions required by Laravel and the installed dependencies.
- Composer.
- MySQL.
- Node.js and npm.
- PHP `pdo_sqlite` extension for the automated tests, which use in-memory SQLite.

### 1. Clone the repository and install dependencies

```bash
git clone https://github.com/NishaKhandelwal/jajal-medical-cases.git
cd jajal-medical-cases
composer install
npm install
npm run build
```

### 2. Configure the environment

Create your `.env` file.

**Windows PowerShell**

```powershell
Copy-Item .env.example .env
```

**Linux or macOS**

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Configure your MySQL connection and demo credentials in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jajal_medical_cases
DB_USERNAME=root
DB_PASSWORD=your_mysql_password

SEED_ADMIN_EMAIL=admin@example.com
SEED_ADMIN_PASSWORD=Admin@12345
SEED_VIEWER_EMAIL=viewer@example.com
SEED_VIEWER_PASSWORD=Viewer@12345
```

Create the database if it does not exist:

```sql
CREATE DATABASE jajal_medical_cases;
```

Keep `.env` on your local machine and never commit it to Git.

### 3. Run migrations and seed sample data

On a new, empty database, run:

```bash
php artisan migrate --seed
```

The seeder creates the Admin and Viewer accounts and 25 fictional cases, numbered `CASE-0001` through `CASE-0025`.

**Important:** The case numbers are fixed, so do not rerun the seeder against an already seeded database. To reset a development database and recreate its tables and sample data, run:

```bash
php artisan migrate:fresh --seed
```

This command deletes existing tables and their data. Use it only when you intend to reset the database.

### 4. Start the application

```bash
php artisan serve
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000) in your browser. Press `Ctrl+C` to stop the development server.

---

## Demo Login Details

These evaluation credentials work after you configure the matching values in `.env` and run the seeder.

| Role | Email | Password |
|---|---|---|
| Admin | `admin@example.com` | `Admin@12345` |
| Viewer | `viewer@example.com` | `Viewer@12345` |

These are fictional local demo accounts. Do not reuse these passwords for real accounts. Change the credentials and restrict access before any public deployment.

---

## Automated Tests

Run the test suite with:

```bash
php artisan test
```

The tests use an in-memory SQLite database and do not modify your MySQL data. Coverage includes:

1. Successful and unsuccessful login.
2. Guest restrictions on case pages and API access.
3. Admin case creation through the web interface and API.
4. Duplicate Case Number validation and updating a case without changing its existing number.
5. Viewer deletion restrictions (`403`).
6. Viewer creation restrictions, including invalid submissions returning `403` rather than `422`.
7. Status-change history through the web interface and API.
8. No history entry when unrelated case fields are edited.

Standard Laravel Breeze authentication and profile tests are included as well.

---

## REST API Reference

**Local base URL:** `http://127.0.0.1:8000/api`

Send the following header with JSON requests:

```http
Accept: application/json
```

Protected endpoints require a Sanctum token:

```http
Authorization: Bearer <token>
```

### Endpoints

| Method | Endpoint | Purpose | Access |
|---|---|---|---|
| `POST` | `/api/login` | Sign in and receive a token | Public |
| `POST` | `/api/logout` | Revoke the current token | Authenticated user |
| `GET` | `/api/cases` | List cases | Admin, Viewer |
| `POST` | `/api/cases` | Create a case | Admin |
| `GET` | `/api/cases/{id}` | View a case | Admin, Viewer |
| `PUT` | `/api/cases/{id}` | Update a case | Admin |
| `DELETE` | `/api/cases/{id}` | Delete a case | Admin |

### List query parameters

| Parameter | Purpose |
|---|---|
| `search` | Search by Case Number or Surgeon Name |
| `status` | Filter by status |
| `priority` | Filter by priority |
| `sort` | Sort by `case_number`, `surgeon_name`, `status`, `priority`, `surgery_date`, or `created_at` |
| `direction` | `asc` or `desc` |
| `page` | Page number; 10 records per page |

### Response format

Successful responses use a consistent JSON structure:

```json
{
  "success": true,
  "data": {},
  "message": "Case created."
}
```

A validation failure returns `422 Unprocessable Entity` with field-level errors:

```json
{
  "success": false,
  "data": null,
  "message": "Validation failed.",
  "errors": {
    "case_number": [
      "The case number has already been taken."
    ]
  }
}
```

| HTTP status | Meaning |
|---|---|
| `200` | Successful request |
| `201` | Resource created |
| `401` | Authentication required or invalid |
| `403` | Authenticated but not authorized |
| `404` | Resource not found |
| `422` | Validation failed |

The Postman collection is available at [`docs/postman_collection.json`](docs/postman_collection.json). Run **Login** first and store the returned token in the collection's `token` variable.

---

## Database Overview

| Table | Purpose |
|---|---|
| `users` | Login details and role (`admin` or `viewer`; default `viewer`) |
| `cases` | Case details, creator reference, and indexed fields |
| `case_status_histories` | Previous and new status, associated user, and timestamp |
| `personal_access_tokens` | Sanctum API authentication tokens |

The `cases.created_by` field references `users.id`. Case Numbers have a unique index, and the status field is indexed. Deleting a case removes its status history; deleting a user clears the history's user reference.

See [`docs/database.md`](docs/database.md) for the detailed schema and relationships.

---

## Validation and Security

- **Input validation:** `CaseRequest` validates required fields, unique Case Numbers, optional dates, and allowed Status and Priority values. When editing, the unique rule ignores the current case.
- **Authorization:** `MedicalCasePolicy` enforces role permissions in both web and API controllers.
- **Mass-assignment protection:** `$fillable` restricts assignable model fields. The role is not fillable, and `created_by` is derived from the authenticated session rather than user input.
- **Database integrity:** Unique constraints and foreign keys provide additional safeguards.
- **Query safety:** Eloquent is used for database queries, and sortable columns are checked against a whitelist.
- **Authentication and request protection:** Web forms use CSRF protection; protected API routes use Sanctum tokens.
- **Credential management:** Passwords are hashed, and secrets belong in the git-ignored `.env` file.

---

## Assumptions and Known Limitations

### Assumptions

- Only fictional sample data is used; Patient Reference is a dummy identifier.
- The current implementation supports two roles: Admin and Viewer.
- Changing a case's status is part of editing and is therefore Admin-only.

### Known limitations

- **Public registration is enabled.** Breeze's `/register` route remains available. New users receive the Viewer role, but a production deployment should disable registration or require invitations.
- **Email verification is not enforced.** Local mail uses the `log` driver, so password-reset messages are written to logs rather than sent.
- **Creation is not recorded in the status history.** The audit log records status changes after a case is created.
- **No live deployment.** `docs/deployment.md` describes a proposed AWS deployment, not a deployed application.
- **No Engineer role or public-holiday lookup.** See the implementation plans below.
- **Search scalability:** Search uses `LIKE '%term%'`, which is suitable for small datasets but may require full-text indexing as data grows.
- **Responsive UI testing:** The interface is responsive but has not been tested on every device.

---

## How I Would Implement the Remaining Items

The following are proposed improvements, not completed features.

### 1. Engineer role

The existing `role` column is a `varchar(20)`, so a schema migration is not required.

1. Add `ROLE_ENGINEER = 'engineer'` to `User` and a helper method:

   ```php
   public function canEditCases(): bool
   {
       return in_array(
           $this->role,
           [self::ROLE_ADMIN, self::ROLE_ENGINEER],
           true
       );
   }
   ```

2. Update `MedicalCasePolicy` so `create` and `update` use `$user->canEditCases()`, while `delete` remains Admin-only.
3. Update the role table and verify the existing Blade views and API controllers enforce the new permissions.
4. Add a demo Engineer account using `SEED_ENGINEER_EMAIL` and `SEED_ENGINEER_PASSWORD`.
5. Add tests confirming that Engineers can create and update cases, including status changes, but receive `403` when attempting deletion.
6. Update `docs/traceability.md` to document the new role and tests.

### 2. Public-holiday lookup using Nager.Date

1. Add the following configuration to `.env` and `.env.example`:

   ```env
   NAGER_API_URL=https://date.nager.at/api/v3
   NAGER_COUNTRY=IN
   ```

   Expose these values through `config/services.php` under a `nager` configuration key.

2. Create `App\Services\HolidayService` using Laravel's HTTP client. Call the `/PublicHolidays/{year}/{country}` endpoint with a three-second timeout, cache successful results for 24 hours, and handle exceptions and unsuccessful responses gracefully.
3. Add an authenticated route such as `GET /holidays/{year}` that returns the service result as JSON.
4. In `cases/_form.blade.php`, use Alpine.js to check the selected Surgery Date and display a note if it falls on a public holiday. If the service fails, the form should remain usable without displaying an error.
5. Test successful responses and simulated failures using `Http::fake()`.

### 3. Live deployment

1. Choose a host that supports PHP and MySQL, such as Render, Railway, or a suitable VPS, and verify its current pricing and hosting requirements.
2. Configure production environment variables, including `APP_ENV=production`, `APP_DEBUG=false`, a secure `APP_KEY`, database credentials, and `APP_URL`.
3. Install production dependencies and build frontend assets:

   ```bash
   composer install --no-dev --optimize-autoloader
   npm ci
   npm run build
   ```

4. Run migrations with `php artisan migrate --force`. Seed only when necessary, using new demo credentials.
5. Disable public registration, configure HTTPS, verify production access controls, and add the live URL to this README after successful deployment.

The AWS deployment proposal covering EC2, RDS, S3, security groups, secrets management, and HTTPS is documented in [`docs/deployment.md`](docs/deployment.md).

---

## Project Documentation

- [`docs/database.md`](docs/database.md) — tables, fields, indexes, and relationships.
- [`docs/traceability.md`](docs/traceability.md) — requirements mapped to implementation and tests.
- [`docs/postman_collection.json`](docs/postman_collection.json) — API requests for testing.
- [`docs/deployment.md`](docs/deployment.md) — proposed AWS hosting plan.

## Project Structure

```text
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   └── Requests/
├── Models/
├── Policies/
└── Traits/

database/
├── factories/
├── migrations/
└── seeders/

docs/
├── database.md
├── deployment.md
├── postman_collection.json
└── traceability.md

resources/
└── views/

routes/
├── api.php
├── auth.php
└── web.php

tests/
└── Feature/
```

---

This project was created for the Jajal Medical Software Developer Intern practical assignment. Use fictional sample records only.
