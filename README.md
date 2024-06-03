
Introduction
This is a Blog System API built with Laravel, featuring user authentication, category management, and post management. All API endpoints are protected and require authentication. Admin policies control the deletion, posting, and updating of categories and posts. Errors are handled comprehensively.

Installation
Clone the repository.
Install dependencies with composer install and npm install.
Copy .env.example to .env and configure environment variables.
Generate an application key with php artisan key:generate.
Set up the database with php artisan migrate --seed.
Install Laravel Sanctum with php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" and php artisan migrate.
Running the Application
Start the application with:

sh
Copy code
php artisan serve
Access it at http://localhost:8000.

API Endpoints
Authentication Endpoints
Register: POST /api/register
Login: POST /api/login
Logout: POST /api/logout
Category Endpoints
Get All Categories: GET /api/categories
Create Category: POST /api/categories
Show Category: GET /api/categories/{id}
Update Category: PUT /api/categories/{id}
Delete Category: DELETE /api/categories/{id}
Post Endpoints
Get All Posts: GET /api/posts
Create Post: POST /api/posts
Show Post: GET /api/posts/{id}
Update Post: PUT /api/posts/{id}
Delete Post: DELETE /api/posts/{id}
Testing
Run tests with:
php artisan test

Technical Details
Authentication Library: Laravel Sanctum
Admin Policies: Implemented to restrict deletion, posting, and updating of categories and posts.
Resource Protection: All API endpoints require authentication.
Error Handling: Comprehensive error handling is implemented across all endpoints.
