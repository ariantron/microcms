# MicroCMS

A minimal Content Management System web service built with Laravel.

## Installation

### Prerequisites
- PHP 8.2+
- Composer
- Laravel
- PostgreSQL (recommended)

### Option 1: Local Installation

1. **Install dependencies**
   ```bash
   composer install
   ```

2. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database setup**
    - Configure database connection in `.env`
    - Run migrations and seeders
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

4. **Create storage link**
   ```bash
   php artisan storage:link
   ```

5. **Start server**
   ```bash
   php artisan serve
   ```

**Access the application:**
- Web Application: http://localhost:8000
   
### Option 2: Docker Installation

**Prerequisites:**
- Docker
- Docker Compose

**Setup:**
```bash
# 1. Create environment file from example
cp docker.env.example docker.env

# 2. Create Laravel environment file
cp docker.env .env

# 3. Generate application key
docker run --rm -v $(pwd):/app -w /app php:8.2-cli php artisan key:generate

# 4. Build and start containers
docker-compose up -d --build

# 5. Run migrations and seeders
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan db:seed --force

# 6. Create storage link
docker-compose exec app php artisan storage:link
```


**Access the application:**
- Web Application: http://localhost

## API Endpoints

- `POST /api/auth/login` - User authentication
- `GET /api/posts/{id}` - Get single post
- `GET /api/users/{id}/posts` - Get user posts
- `POST /api/profile/image` - Update profile image
- `DELETE /api/profile/image` - Delete profile image

All endpoints except login require JWT authentication.

### API Documentation

A Postman collection is included in the project for API testing and documentation:
- **File**: `postman.json`
- **Import**: Import this file into Postman to access all API endpoints

## Docker

This project is fully containerized with Docker and includes:

### Services
- **Laravel App**: PHP 8.2-FPM with all required extensions
- **PostgreSQL**: Database server (port 5432)
- **Nginx**: Web server with optimized configuration (port 80)

### Docker Commands

**Start all services:**
```bash
docker-compose up -d
```

**Stop all services:**
```bash
docker-compose down
```

**View logs:**
```bash
docker-compose logs -f
```

**Access application container:**
```bash
docker-compose exec app bash
```

**Run migrations:**
```bash
docker-compose exec app php artisan migrate
```

**Run tests:**
```bash
docker-compose exec app php artisan test
```

**Access database:**
```bash
docker-compose exec postgres psql -U cms_user -d cms_db
```

### Docker Configuration Files
- `Dockerfile` - Laravel application container
- `docker-compose.yml` - Multi-container orchestration
- `docker/nginx/default.conf` - Nginx configuration
- `docker.env.example` - Environment variables template (copy to `docker.env`)

## Testing

This project uses Pest PHP for testing. The test suite includes comprehensive API tests covering authentication, posts, and user operations.

### Running Tests

**Run all tests:**
```bash
php artisan test
```

**Run specific test files:**
```bash
# Authentication API tests
php artisan test --filter=AuthApiTest

# Post API tests
php artisan test --filter=PostApiTest

# User API tests
php artisan test --filter=UserApiTest
```

**Run multiple test files:**
```bash
php artisan test --filter="AuthApiTest|PostApiTest|UserApiTest"
```

### Test Coverage

The test suite includes:

- **Unit Tests (1 test)**
  - Basic unit test functionality

- **Feature Tests (19 tests)**
  - **Authentication API Tests (6 tests)**
    - Login with valid/invalid credentials
    - Validation for required fields
    - Mobile number and password format validation
  
  - **Post API Tests (5 tests)**
    - Post retrieval with valid UUID
    - 404 handling for non-existent posts
    - View count tracking and unique IP tracking
  
  - **User API Tests (7 tests)**
    - User posts retrieval
    - Profile image upload/delete operations
    - File validation (type, size)
    - Authentication requirements
  
  - **Example Feature Test (1 test)**
    - Application response validation

**Total: 20 tests with 91 assertions**

### Test Features

- ✅ JWT Authentication testing
- ✅ Request validation testing
- ✅ File upload testing with fake storage
- ✅ Database operations testing
- ✅ Error handling and edge cases
- ✅ Response structure validation
