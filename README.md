# Task Manager setup 

## Tech Stack

- **Laravel 9** 
- **MySQL** — migrations and seeders included
- **Blade** templates with **Bootstrap 5**
- **jQuery + AJAX** for status updates without page reload

## Feature Summary

| Feature | Detail |
|---|---|
| Create / Edit tasks | Title, description, due date, assigned user, priority |
| Task dashboard | Paginated table with status/user/date filters |
| Status updates | Complete or non-compliant — via AJAX (no full page reload) |
| Corrective action | Required when marking non-compliant; captured via modal |
| Role-based access | `manager` and `worker` roles; middleware on all routes |
| Activity log | Recorded on task creation and every status change |
| Overdue highlighting | Red row highlight |
| Due-today highlight | Yellow row highlight |

---

## Installation Guide


This project can be run either using Docker (recommended) or a traditional local PHP setup.

## Option 1: Run with Docker

Prerequisites
Docker installed
Docker Compose installed

### Steps

### 1. Clone the repository. `git clone <repo-url>`  `cd <project-folder>`

### 2.Navigate into the project root (where docker-compose.yml is located)

### 3.Copy environment file: `cp src/.env.example src/.env`

### 4. Update your .env file:
`DB_HOST=host.docker.internal`. This is required so the container can connect to your local database.

### 5. Build and start containers:
`docker-compose up --build`

### 6. Install dependencies inside the container:
`docker-compose exec app composer install`
`docker-compose exec app php artisan key:generate`
`docker-compose exec app php artisan migrate --seed`

### 7.Access the application:
`http://localhost:{PORT_NUMBER}` The port number set in the docker-compose.yml is 8182. Feel free to change to any port number you wish.

### To stop the app: `docker-compose down`


## Option 2: Run Without Docker

### Prerequisites
PHP >= 8.0
Composer
MySQL

### Steps

### 1. Navigate into the src folder:
`cd src`

### 2. Install dependencies:
`composer install`

### 3. Copy environment file:
`cp .env.example .env`

### 4. Configure your .env:
`DB_DATABASE=your_db`
`DB_USERNAME=your_user`
`DB_PASSWORD=your_password`

### 5. Generate app key:
`php artisan key:generate`

### 6. Run migrations and seeders:
`php artisan migrate --seed`

### 7. Start the development server:
`php artisan serve`

### 8. Access the application:
`http://127.0.0.1:8000`

## Notes
1. `RolesSeeder` — creates `manager` and `worker` roles
2. `UsersSeeder` — creates 2 managers + 3 workers
3. `TasksSeeder` — creates 8 sample tasks (mix of statuses, priorities, overdue)

If I had more time, I would have implemented the following features:

1. Workers should only see tasks assigned to them when they log in
2. Delete task by managers.

## Assumptions
* The system has two primary roles: Manager and Worker.
    * Managers can create and edit tasks.
    * Workers can view and update task status.
* Tasks are assigned to a single user (no multi-assignment).
* A task marked as non-compliant  cannot be updated.
* The application is intended for internal use, so authentication is simple (no registration flow).
* Pagination is server-side and limited to a reasonable default (15 tasks per page).
* Date comparisons (e.g., overdue, due today) are based on the server timezone.
* Activity logs are recorded for key actions (e.g., task creation and updates), but not for every minor interaction.

## AI USE
Claude was used to 
1. Generate blade templates, migrations, and seeders, 
2. Speed up scaffolding and boilerplate generation, 
3. Refine UX structure 
4. Improve code structure

All generated suggestions were:
* Reviewed and adapted to fit the project requirements
* Tested locally to ensure correctness
* Modified where necessary to maintain code quality and consistency
