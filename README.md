# Laravel Booking System API Assignment

Welcome to the **Nabooki** Software Engineering Laravel tech test.

This repository contains a clean **Laravel 12** project template, designed as a take-home assignment. It requires at least **PHP 8.2** and **Composer** installed. An in-memory **SQLite database** is already configured for testing.

## Setup Instructions

Follow these steps to set up the project for development:

### 1. Fork and Clone this repository

First, **fork this repository on GitHub** to your own account. Then, clone your forked repository to your local machine:

```bash
git clone <repository-url>
cd tech-test
```

### 2. Install dependencies
```bash
composer install
```

### 3. Configure environment
```bash
cp .env.example .env
```

### 4. Generate application key
```bash
php artisan key:generate
```

### 5. Set up database and seed with test data
```bash
php artisan migrate --seed
```

This command will:
- Create the SQLite database file (`database/database.sqlite`)
- Run all migrations to create the necessary tables
- Populate the database with test data including:
  - 1 test user (test@example.com, password: password)
  - 15 sample customers
  - 10 sample services
  - 30 sample bookings with random statuses

**Note:** If you wish to spin up an environment to suit your workflow during development, you are completely free to do so. However, please keep in mind that solutions will be evaluated on the tests you write, and no additional setup should be required for `php artisan test` to be executed beyond the initial steps provided here.

You should not require any additional composer packages to complete this task. If you do decide to add any, please explain your reasoning for doing so in your submission.

## Authentication for Testing

This project uses **Laravel Sanctum** for API authentication. To test your API endpoint, you will need to generate a token for your user.

## Assignment Task: Booking System API Enhancement

We are mindful of your time, and this task is designed to accommodate various valid solutions. Your solution will be primarily evaluated on your use of tests as well as your implementation and understanding of Laravel conventions.

This test should not take hours from your time; we encourage you to provide a solution that meets the requirements effectively, without over-engineering.

You do not need to "impress" us with everything you know or coding "clever" solutions. Work with the provided structure and feel free to get in touch if there are any gaps.

If you feel you would approach any of these tasks differently given more time, please provide the details as part of your submission.

#### Task: Expose a New API Endpoint to List All Bookings

Implement a new **internal API endpoint** `GET /api/bookings` to list all bookings in the system. This endpoint will exist only for **authenticated users** and will be consumed by an internal SPA frontend.

As part of exposing these details, we only want to provide the following data for each booking:

- Booking ID
- Customer Full Name
- Service Name
- Starts At (ISO datetime format)
- Ends At (ISO datetime format)
- Status
- Total Price:
  - This value must be only visible in the response for **confirmed** bookings
  - This value is stored as cents in the database, this must be displayed in human readable dollar format

The following must also be observed:

- The data returned should be paginated for scalability.
- The oldest bookings must be at the top of the list.
- The endpoint should accept optional filters: service_id (integer), status (e.g., pending, confirmed, cancelled), date_from (YYYY-MM-DD), and date_to (YYYY-MM-DD).

**NOTE:** You are not required to implement any additional auth features/tests, and you can assume any/all auth associated tests are already done. You are also not required to build out the frontend as part of this task.

## Evaluation Criteria

Your solution will be evaluated primarily based on your:

1. **Testing Approach:** The quality and coverage of your tests.
2. **Code Quality:** Clean, readable, and well-organized code following Laravel and PHP best practices.
3. **Laravel Conventions:** Proper and idiomatic use of Laravel features (Eloquent, controllers, resources, routing, middleware).
4. **API Design:** Adherence to RESTful principles, clear query parameters, and appropriate HTTP status codes.
5. **Problem-Solving:** Your ability to meet the requirements efficiently and robustly.
6. **Git Workflow:** Clear and descriptive Git commit messages

## Submission

When you're ready to submit your solution:

1. Ensure all your changes are committed with clear, descriptive commit messages
2. Push your changes to your forked repository
3. Open a Pull Request from your branch to the `main` branch
4. Include a brief description of your implementation approach in the PR description

We encourage you to leave comments through your Pull Request to explain your thoughts if you feel so.

Good luck with your implementation!
