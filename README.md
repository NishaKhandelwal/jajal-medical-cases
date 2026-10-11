# Medical Case Management

**Jajal Medical — Software Developer Intern Practical Assignment**

Medical Case Management is a Laravel web application for keeping track of medical-device implant cases, from the initial draft through planning, review, approval, and completion. It provides a dashboard, case records, role-based access, and a REST API. The project uses fictional sample data only; it does not use real patient information.

## What the application does

### Login and user roles

Users sign in before opening case pages. The application has two roles:

| Action | Admin | Viewer |
|---|---|---|
| View cases and the dashboard | Yes | Yes |
| Create a case | Yes | No |
| Edit a case or change its status | Yes | No |
| Delete a case | Yes | No |

Permissions are checked on the server as well as in the interface, so a Viewer cannot perform a restricted action simply by sending a request directly.

### Dashboard and case list

The dashboard shows the total number of cases and counts for Planning, In Review, Approved, and Completed. The case list displays 10 records per page and lets users search by Case Number or Surgeon Name, filter by Status or Priority, and sort the available columns.

### Creating and reviewing a case

The same form is used to create and edit a case. It checks the submitted information and displays validation messages beside the relevant fields. Each case has a unique Case Number, a fictional Patient Reference, Surgeon Name, Implant Type, Status, Priority, and an optional Surgery Date. The application records the logged-in user who created the case, along with creation and update timestamps.

The case details page shows the full record and its history. When the status changes, the application records the previous status, the new status, the user associated with the change, and the time it happened. This history is recorded for changes made through both the web interface and the API. Editing an unrelated field does not create a status-change entry.

### REST API

The API uses Laravel Sanctum for token-based authentication. It supports listing, viewing, creating, updating, and deleting cases, with access controlled by the same Admin and Viewer roles. Case lists are paginated and support search, status and priority filters, and sorting. API validation errors include field-level messages.

## Technology

- **Language and framework:** PHP 8.4 and Laravel 12
- **Database:** MySQL
- **Web interface:** Laravel Blade and Tailwind CSS
- **Web login:** Laravel Breeze
- **API authentication:** Laravel Sanctum
- **Automated tests:** PHPUnit
- **Development tools:** Composer, Node.js/npm, Git, and Postman

## Run the application locally

### Requirements

Install PHP 8.2 or later with the extensions required by Laravel, Composer, MySQL, Node.js, and npm. To run the automated tests with the included in-memory SQLite configuration, the PHP `pdo_sqlite` extension must also be enabled.

### 1. Get the code and install dependencies

```bash
git clone https://github.com/NishaKhandelwal/jajal-medical-cases.git
cd jajal-medical-cases
composer install
npm install
npm run build
```

### 2. Create your environment file

Copy `.env.example` to `.env`.

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

In `.env`, configure your MySQL connection and the demo accounts. For the demo login details listed below, use these values before running the seeder:

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

Create the database if it does not already exist:

```sql
CREATE DATABASE jajal_medical_cases;
```

Keep `.env` on your own machine and do not commit it to Git. The values above are for local assignment testing only.

### 3. Create the tables and sample records

Run this once when setting up a new, empty database:

```bash
php artisan migrate --seed
```

The migrations create the application tables. The seeder creates the Admin and Viewer demo accounts and 25 fictional case records. The seeder uses predefined case numbers, so it should not be rerun against an already seeded database. Starting or restarting the Laravel server does not require seeding again.

### 4. Start the application

```bash
php artisan serve
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000) in your browser. Use `Ctrl+C` in the terminal to stop the local development server.

## Demo login details

Use these accounts after following the setup steps and setting the matching password values in `.env` before seeding.

| Role | Email | Password |
|---|---|---|
| Admin | `admin@example.com` | `Admin@12345` |
| Viewer | `viewer@example.com` | `Viewer@12345` |

These are fictional evaluation accounts, not accounts for real users. Passwords are hashed before they are saved to the database.

## Run the tests

Run the automated feature tests with:

```bash
php artisan test
```

The tests cover successful and unsuccessful login, guest access restrictions, case creation, duplicate Case Number validation, role permissions, and status-history recording through the web interface and API. They also check that changing unrelated case fields does not add a status-history record.

## REST API reference

The local API base URL is `http://127.0.0.1:8000/api`. Send `Accept: application/json` with JSON requests. Protected endpoints require a Sanctum token in the header:

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
| `GET` | `/api/cases/{id}` | View one case | Admin, Viewer |
| `PUT` | `/api/cases/{id}` | Update a case | Admin |
| `DELETE` | `/api/cases/{id}` | Delete a case | Admin |

The case-list endpoint accepts these query parameters:

| Parameter | Purpose |
|---|---|
| `search` | Search by Case Number or Surgeon Name |
| `status` | Filter by status |
| `priority` | Filter by priority |
| `sort` | Choose a supported column to sort by |
| `direction` | Choose ascending or descending order |
| `page` | Select a page of results |

Successful responses use a consistent JSON structure, with a `success` value, `data`, and a `message`. Validation failures return field-level errors. The API uses standard HTTP response codes such as `200`, `201`, `401`, `403`, `404`, and `422` where appropriate.

A Postman request collection is included at [`docs/postman_collection.json`](docs/postman_collection.json).

## Database overview

The application uses these main tables:

- **`users`** stores login details and each user's role.
- **`cases`** stores case details and the ID of the user who created each case.
- **`case_status_histories`** stores status changes, including the previous and new values, the associated user, and timestamps.

Each case points to its creator through `cases.created_by`. Case Numbers have a unique database constraint, and the status field is indexed for filtering. The Laravel migrations define the relationships and constraints.

See [`docs/database.md`](docs/database.md) for the table and relationship details.

## Validation and security

The application uses Laravel Form Request classes to validate case input, including the allowed statuses and priorities, valid optional dates, and unique Case Numbers. When editing a case, the uniqueness check allows that case to keep its existing Case Number. Database constraints provide an additional safeguard for unique Case Numbers and relationships.

Laravel authentication protects the web pages, Sanctum protects token-authenticated API routes, and server-side authorization restricts Admin-only actions. The application also uses mass-assignment protection, CSRF protection for web forms, and a whitelist of columns that can be used for sorting. Configuration and passwords belong in the local `.env` file, not in the repository.

## Project documentation

- [`docs/database.md`](docs/database.md) — tables, fields, indexes, and relationships
- [`docs/traceability.md`](docs/traceability.md) — requirements linked to the code and tests that verify them
- [`docs/postman_collection.json`](docs/postman_collection.json) — API requests for testing
- [`docs/deployment.md`](docs/deployment.md) — proposed AWS hosting approach

## Next steps

The required web screens, case management, role checks, API endpoints, database setup, and automated tests are implemented. The following optional improvements would be the next work to take on:

1. **Public-holiday information:** Use Laravel's HTTP client to call the Nager.Date public API and display relevant holidays near the Surgery Date field. Put the API URL in `.env`, set a request timeout, and show the form normally if the external service is unavailable.
2. **Live deployment:** Follow the plan in `docs/deployment.md` to host the Laravel application on AWS, use RDS for MySQL, configure production secrets and restricted network access, and enable HTTPS. The document describes the proposed approach rather than a completed deployment.

## Project structure

```text
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   └── Requests/
├── Models/
├── Policies/

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

---

This project was created for the Jajal Medical Software Developer Intern practical assignment. Use fictional sample records only.
