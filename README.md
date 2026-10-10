# Medical Case Management
### Jajal Medical — Software Developer Internship Assignment

A Laravel-based web application for managing medical-device implant cases throughout their lifecycle, from Draft to Completed.

**Dummy data only — no real patient information is used.**

## Technology Stack

- **Backend:** PHP 8.4, Laravel 12
- **Database:** MySQL
- **Frontend:** Blade, Tailwind CSS
- **Authentication:** Laravel Breeze
- **API Authentication:** Laravel Sanctum
- **Testing:** PHPUnit
- **Tools:** Composer, npm, Git, Postman

## Features

### Authentication and Authorization
- Login and logout using Laravel Breeze
- Clear validation errors for invalid login credentials
- Role-based access control for Admin and Viewer
- Server-side authorization for protected operations

### Dashboard
- Total case count
- Cases grouped by status:
  - Planning
  - In Review
  - Approved
  - Completed

### Case Management
- Create, view, and edit medical cases
- Admin-only case deletion
- Case details page with creator and record timestamps
- Search by Case Number or Surgeon Name
- Filter by Status and Priority
- Column sorting
- Pagination with 10 cases per page
- Form Request validation
- Unique Case Number enforcement

### Status-Change Audit Log
- Records previous and new status when a case's status changes
- Stores the associated case, authenticated user ID, and timestamp
- Captures changes made through both the web interface and REST API
- Does not create a status-change entry when unrelated fields are updated
- Displays status history on the case details page

### REST API
- Token-based authentication using Laravel Sanctum
- Consistent JSON response structure
- Paginated case listing
- Search, filtering, and sorting
- Request validation and JSON error responses

## User Roles

| Capability | Admin | Viewer |
|---|---|---|
| View cases | Yes | Yes |
| Create cases | Yes | No |
| Edit cases | Yes | No |
| Change status | Yes | No |
| Delete cases | Yes | No |

## Setup Instructions

### Prerequisites

Install the following:

- PHP 8.2 or later, with the required Laravel extensions
- Composer
- MySQL
- Node.js and npm

### 1. Clone the repository

```bash
git clone https://github.com/NishaKhandelwal/jajal-medical-cases.git
cd jajal-medical-cases
```

### 2. Install dependencies

```bash
composer install
npm install
npm run build
```

### 3. Configure the environment

Create a local `.env` file from `.env.example`.

**Windows PowerShell**

```powershell
Copy-Item .env.example .env
```

**Linux/macOS**

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Configure the database connection in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jajal_medical_cases
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

Configure the demo account settings in `.env` as well:

```env
SEED_ADMIN_EMAIL=admin@example.com
SEED_ADMIN_PASSWORD=your_admin_password
SEED_VIEWER_EMAIL=viewer@example.com
SEED_VIEWER_PASSWORD=your_viewer_password
```

Use strong local passwords and keep `.env` out of version control.

Create the database in MySQL if it does not exist:

```sql
CREATE DATABASE jajal_medical_cases;
```

### 4. Run migrations and seed demo data

```bash
php artisan migrate --seed
```

This creates the database tables and seeds the initial demo accounts and sample medical cases.

**Important:** Seed the database during initial setup. You do not need to seed it every time you start Laravel. The database stores accounts and cases between application restarts.

The current seeder creates demo users and 25 cases using predefined case numbers. Avoid repeatedly running the seeder against an existing database, as it uses record creation rather than updating existing accounts.

If you already have a database with records, back it up before making changes to the database or running seeders again.

### 5. Start the application

```bash
php artisan serve
```

Open:

```text
http://127.0.0.1:8000
```

To stop the server, press `Ctrl+C`. Restarting the development server should not erase your MySQL records.

## Demo Login Credentials

The seeded accounts use the email addresses configured through the `SEED_*` environment variables.

| Role | Default email |
|---|---|
| Admin | `admin@example.com` |
| Viewer | `viewer@example.com` |

The passwords are determined by `SEED_ADMIN_PASSWORD` and `SEED_VIEWER_PASSWORD` in your local `.env` file. Use the configured values when logging in.

The seeder hashes passwords before storing them in the database. Passwords are not stored as plain text.

## Running Tests

Run the full automated test suite:

```bash
php artisan test
```

The tests use the SQLite in-memory database configured in `phpunit.xml`. The PHP `pdo_sqlite` extension must be enabled.

The feature tests cover authentication, authorization, case operations, API behavior, validation, and status-change auditing.

## REST API

The API base URL is:

```text
/api
```

For JSON requests, include:

```http
Accept: application/json
```

Protected endpoints require a valid Sanctum token:

```http
Authorization: Bearer <token>
```

### Endpoints

| Method | Endpoint | Purpose |
|---|---|---|
| POST | `/api/login` | Authenticate and obtain an API token |
| POST | `/api/logout` | Revoke the current token |
| GET | `/api/cases` | Retrieve paginated cases |
| POST | `/api/cases` | Create a case |
| GET | `/api/cases/{id}` | Retrieve a case |
| PUT | `/api/cases/{id}` | Update a case |
| DELETE | `/api/cases/{id}` | Delete a case |

Protected endpoints require authentication, and operations are subject to the application's authorization rules.

The case listing supports these query parameters:

```text
?search=
?status=
?priority=
?sort=
?direction=
?page=
```

### API Response Format

Successful responses follow a consistent structure:

```json
{
  "success": true,
  "data": {},
  "message": "..."
}
```

Validation errors follow this structure:

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

Common HTTP status codes include:

- `200` — Successful request
- `201` — Resource created
- `401` — Unauthenticated
- `403` — Forbidden
- `404` — Resource not found
- `422` — Validation error

A Postman collection is available at `docs/postman_collection.json`.

## Database Design

The main tables are:

- `users` — Application accounts and roles
- `cases` — Medical-device case records
- `case_status_histories` — Recorded status transitions

Each case references its creator through the `created_by` foreign key. Status history records the previous and new status, the case reference, the user ID associated with the change, and timestamps.

The `case_number` column has a unique constraint, while the `status` column is indexed for filtering.

See [`docs/database.md`](docs/database.md) for the database design.

## Validation and Security

The application includes:

- Laravel Breeze authentication
- Laravel Sanctum API authentication
- Server-side authorization
- Form Request validation
- Eloquent mass-assignment protection
- CSRF protection for web forms
- Whitelisted sorting columns
- Database uniqueness and foreign-key constraints
- Environment-based configuration
- `.env` excluded from version control
- Status-change history recording

The application uses dummy data and is intended for assignment evaluation, not direct use with real patient information.

## Testing Coverage

The automated feature tests cover scenarios including:

- Valid login succeeds
- Invalid login is rejected
- Guests cannot access protected case pages or API endpoints
- Admin can create a case through the web interface and API
- Duplicate Case Numbers are rejected
- A case can retain its existing Case Number during an update
- Viewer permissions are enforced for restricted operations
- Web status changes are recorded
- API status changes are recorded
- Updating unrelated fields does not create a status-history entry

Run `php artisan test` to execute the suite.

## Documentation

Additional project documentation is available in the `docs` directory:

- [`docs/database.md`](docs/database.md) — Database tables, columns, indexes, and relationships
- [`docs/traceability.md`](docs/traceability.md) — Requirement-to-code-to-test traceability
- [`docs/deployment.md`](docs/deployment.md) — Proposed AWS deployment architecture
- [`docs/postman_collection.json`](docs/postman_collection.json) — API request collection

The AWS document describes a proposed deployment plan; it does not imply that the application is deployed.

## Assumptions and Limitations

- The model is named `MedicalCase` because `Case` is a reserved PHP keyword; the database table is named `cases`.
- The application implements the Admin and Viewer roles.
- Public registration remains enabled, with new registrations assigned the Viewer role. A production deployment should review and restrict registration as appropriate.
- The status-history table currently deletes associated history when a case is deleted.
- Nager.Date public holiday integration has not been implemented.
- The application has not been deployed to a live hosting environment.
- Only dummy medical data should be used.

## Bonus Features Implemented

- Priority filtering
- Column sorting
- Status-change audit log
- Proposed AWS deployment documentation

## Project Structure

```text
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   └── Requests/
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
└── Feature/
```

## License

This project was created as a practical software developer internship assignment and uses dummy data only.
