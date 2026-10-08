# Medical Case Management (Jajal Medical – Intern Assignment)

A small Laravel application for managing medical-device implant cases from Draft to Completed.

**Dummy data only — no real patient information is used.**

## Technology Stack

- Laravel 11/12
- PHP 8.2+
- MySQL
- Blade + Tailwind CSS
- Laravel Breeze for authentication
- Laravel Sanctum for API authentication
- PHPUnit for automated testing

## Features

- Login and logout using Laravel Breeze
- Clear error message for invalid login credentials
- Dashboard with case counts:
  - Total Cases
  - Planning
  - In Review
  - Approved
  - Completed
- Case list with:
  - Pagination (10 cases per page)
  - Search by Case Number or Surgeon Name
  - Filter by Status
  - Filter by Priority
  - Column sorting
- Create and edit cases using a shared form
- Case details page
- Admin-only case deletion with server-side Policy authorization
- Admin and Viewer roles
- REST API secured with Laravel Sanctum tokens
- Consistent JSON API response format
- Form Request validation
- Automated feature tests

## User Roles

| Role | View Cases | Create/Edit | Change Status | Delete |
|---|---|---|---|---|
| Admin | Yes | Yes | Yes | Yes |
| Viewer | Yes | No | No | No |

## Setup

### 1. Clone the repository

```bash
git clone <your-repo-url>
cd jajal-medical-cases
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install frontend dependencies

```bash
npm install
npm run build
```

### 4. Configure the environment

Copy `.env.example` to `.env`.

On Linux/macOS:

```bash
cp .env.example .env
```

On Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Update the database settings in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jajal_medical_cases
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

Create the MySQL database if it does not already exist:

```sql
CREATE DATABASE jajal_medical_cases;
```

### 5. Run migrations and seed demo data

```bash
php artisan migrate --seed
```

The seeder creates demo Admin and Viewer users along with dummy medical cases.

### 6. Start the application

```bash
php artisan serve
```

The application will be available at:

```text
http://127.0.0.1:8000
```

## Test Login Credentials

The following are dummy credentials intended only for testing.

| Role | Email | Password |
|---|---|---|
| Admin | admin@example.com | Admin@12345 |
| Viewer | viewer@example.com | Viewer@12345 |

The credentials are configured through the `SEED_*` variables in `.env.example`.

## Running Tests

Run the complete automated test suite with:

```bash
php artisan test
```

The tests use an in-memory SQLite database configured in `phpunit.xml`.

The PHP `pdo_sqlite` extension must be enabled to run the test suite.

## REST API

The API base URL is:

```text
/api
```

For JSON requests, send:

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
| POST | `/api/login` | Get an API token | Anyone |
| POST | `/api/logout` | Revoke the current token | Logged in users |
| GET | `/api/cases` | Get paginated cases | Admin, Viewer |
| POST | `/api/cases` | Create a case | Admin |
| GET | `/api/cases/{id}` | Get a case | Admin, Viewer |
| PUT | `/api/cases/{id}` | Update a case | Admin |
| DELETE | `/api/cases/{id}` | Delete a case | Admin |

The case list API supports:

```text
?search=
?status=
?priority=
?sort=
?direction=
?page=
```

### API Response Format

API responses use a consistent structure:

```json
{
    "success": true,
    "data": {},
    "message": "..."
}
```

Validation errors use the following structure:

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

The API uses appropriate HTTP status codes including:

- `200` — Successful request
- `201` — Resource created
- `401` — Unauthenticated
- `403` — Unauthorized
- `404` — Resource not found
- `422` — Validation error

A Postman collection is available at:

```text
docs/postman_collection.json
```

## Documentation

Additional project documentation is available in the `docs` directory:

- `docs/database.md` — Database tables, columns, indexes and relationships
- `docs/traceability.md` — Requirement-to-code-to-test traceability matrix
- `docs/deployment.md` — AWS deployment outline (bonus)

## Database

The main application tables are:

```text
users
  |
  | 1-to-many
  |
  v
cases
```

Each case stores the user who created it through the `created_by` foreign key.

The `case_number` field has a unique database index, and `status` has a database index to support filtering.

See [`docs/database.md`](docs/database.md) for the complete database design.

## Validation and Security

The application includes the following security and validation measures:

- Laravel Breeze authentication
- Laravel Sanctum API authentication
- Policy-based authorization for Admin/Viewer permissions
- Form Request classes for backend validation
- `$fillable` protection against mass assignment
- `role` is not mass assignable
- CSRF protection for web forms
- Eloquent/query builder used for database access
- User-provided sorting columns are whitelisted
- Unique database constraint on Case Number
- Foreign key constraint on `created_by`
- `.env` is excluded from Git
- `.env.example` is provided for configuration

## Testing Coverage

The automated test suite covers authentication, authorization, case creation, validation and API behavior.

Important scenarios include:

- Valid login succeeds
- Invalid login is rejected
- Guests cannot access protected case pages/API endpoints
- Admin can create a case
- Admin can create a case through the API
- Duplicate Case Number is rejected
- A case can retain its own Case Number when edited
- Viewer cannot delete a case
- Viewer cannot create a case

## Assumptions and Known Limitations

- The model is named `MedicalCase` because `Case` is a reserved PHP word; the database table remains `cases`.
- The application uses only the two required roles: Admin and Viewer.
- Laravel Breeze's public registration route remains enabled. New registrations receive the Viewer role. In a production system, public registration would be disabled or restricted.
- No audit log for historical status changes is currently implemented.
- No Nager.Date public holiday integration is implemented.
- The application has not been deployed to a live hosting environment.

## Bonus Features Implemented

The following optional assignment features have been implemented:

- Priority filtering on the case list
- Column sorting on the case list
- AWS deployment documentation in `docs/deployment.md`

The following optional features were not implemented:

- Engineer role
- Nager.Date holiday integration
- Live deployment
- Status-change audit log

These can be added in a future iteration if required.

## Project Structure

Important application areas include:

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   └── ...
│   ├── Requests/
│   └── ...
├── Models/
├── Policies/
└── ...

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
└── web.php

tests/
├── Feature/
└── Unit/
```

## License

This project was created as a practical internship assignment and uses dummy data only.