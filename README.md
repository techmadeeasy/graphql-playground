# GraphQL Playground Project

A sample project demonstrating a GraphQL API using PHP, Lighthouse, Composer, JavaScript, and npm.

## Features

- GraphQL API for managing users and posts
- Pagination, filtering, and CRUD operations
- Custom scalars (e.g., DateTime)
- Laravel Eloquent relationships via Lighthouse directives

## Tech Stack

- PHP (Laravel recommended)
- Lighthouse (GraphQL server for Laravel)
- Composer (PHP dependencies)
- JavaScript (for frontend or tooling)
- npm (JavaScript dependencies)

## Getting Started

### Prerequisites

- PHP 8.x
- Composer
- Node.js & npm

### Installation

1. Clone the repository:
    ```sh
    git clone <your-repo-url>
    cd <project-directory>
    ```

2. Install PHP dependencies:
    ```sh
    composer install
    ```

3. Install JavaScript dependencies:
    ```sh
    npm install
    ```

4. Copy and configure your environment:
    ```sh
    cp .env.example .env
    # Edit .env as needed
    ```

5. Generate application key (if using Laravel):
    ```sh
    php artisan key:generate
    ```

6. Run database migrations:
    ```sh
    php artisan migrate
    ```

### Running the Project

- Start the development server:
    ```sh
    php artisan serve
    ```

- Access the GraphQL Playground at:
    ```
    http://localhost:8000/graphql-playground
    ```

### Example GraphQL Schema

See `graphql/schema.graphql` for the full schema definition.

## Scripts

- `npm run dev` — Start frontend development server (if applicable)
- `php artisan serve` — Start backend server

## License

MIT
