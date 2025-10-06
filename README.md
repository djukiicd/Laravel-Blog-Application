# Laravel Blog Application

A simple blog application built with Laravel featuring user authentication, post management, and commenting system.

## Installation & Setup

You can run the application in two ways:  
1. On your local machine (recommended for development)  
2. Inside Docker containers 

---

### Running on Your Local Machine

#### Prerequisites

Before you begin, make sure you have the following installed:

- PHP 8.4 or higher  
- Composer  
- Node.js & NPM (for asset compilation)

---

#### Quick Start

1. **Clone the repository**

First, clone this repository and navigate into the project directory:

   ```bash
   git clone <repository-url>
   cd blog-app
   ```

2. **Install PHP dependencies**
   Next, we need to install the PHP dependencies using Composer:
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
Now, copy the example environment file and generate a new application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**
 Run the migrations and seeders to set up your database:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Build assets**
Next, compile and build your frontend assets:
   ```bash
   npm run build
   ```

7. **Start the development server**
Finally, start the Laravel development server:
   ```bash
   php artisan serve
   ```

8. **Visit the application**
   Open your browser and go to `http://localhost:8000`


## Docker Setup

The application includes complete Docker containerization for easy deployment:

### Quick Docker Start
```bash
# Make the setup script executable and run it
chmod +x docker-setup.sh
./docker-setup.sh
```

### Manual Docker Setup
```bash
# Build and start containers
docker-compose up -d

# Run Laravel setup commands
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan db:seed --force
```

### Docker Services
- **Application**: `http://localhost:8000`
- **phpMyAdmin**: `http://localhost:8080`
- **Database**: MySQL 8.0 on port 3306


### Sample Data

The application comes with sample data including:
- users (including admin@example.com)
- sample blog posts
- Multiple comments on each post

**Default login credentials:**
- Email: `admin@example.com`
- Password: `password`

Or create your own account using the registration form.


## Features

### Core Features

1. User Registration and Authentication
2. Database Setup
3. CRUD for Blog Posts
4. Comments System
5. Authorization & Middleware
6. Frontend

### Additional Features
1. Docker Support
2. Role-Based Access Control: Admin role with elevated permissions
3. Comprehensive Testing


### Bonus Features

1. Sample Data: Comprehensive seeders with realistic content
2. User Experience: Intuitive navigation and clear feedback messages
3. Tag System: Add tags to posts for better organization and categorization
4. Search Functionality: Search posts by tags and keywords for easy content discovery
5. Admin Panel: Dedicated admin interface for managing users, posts, and comments


## API Endpoints

| Method | URI | Description | Auth Required |
|--------|-----|-------------|---------------|
| GET | `/` | Redirects to posts index | No |
| GET | `/posts` | List all posts | No |
| GET | `/posts/create` | Show create post form | Yes |
| POST | `/posts` | Store new post | Yes |
| GET | `/posts/{post}` | Show single post | No |
| GET | `/posts/{post}/edit` | Show edit form | Yes (Owner) |
| PUT | `/posts/{post}` | Update post | Yes (Owner) |
| DELETE | `/posts/{post}` | Delete post | Yes (Owner) |
| POST | `/posts/{post}/comments` | Add comment | Yes |
| DELETE | `/comments/{comment}` | Delete comment | Yes (Owner/Post Owner) |



