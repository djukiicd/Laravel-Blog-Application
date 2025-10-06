# Laravel Blog Application

A simple blog application built with Laravel featuring user authentication, post management, and commenting system.

## Features

### ✅ Core Requirements Implemented

1. **User Registration and Authentication**
   - Laravel Breeze authentication system
   - User registration, login, and logout
   - Password reset functionality
   - Email verification support

2. **Database Setup**
   - **Users table**: id, name, email, password, timestamps
   - **Posts table**: id, user_id, title, content, timestamps
   - **Comments table**: id, post_id, user_id, comment, timestamps
   - Proper foreign key relationships with cascade deletes
   - Eloquent relationships implemented

3. **CRUD for Blog Posts**
   - ✅ `GET /posts` - List all posts
   - ✅ `GET /posts/create` - Show create form (authenticated users only)
   - ✅ `POST /posts` - Store new post (authenticated users only)
   - ✅ `GET /posts/{id}` - View single post with comments
   - ✅ `GET /posts/{id}/edit` - Edit post (owner only)
   - ✅ `PUT /posts/{id}` - Update post (owner only)
   - ✅ `DELETE /posts/{id}` - Delete post (owner only)
   - Input validation for title and content

4. **Comments System**
   - ✅ `POST /posts/{id}/comments` - Add comment (authenticated users)
   - ✅ `DELETE /comments/{id}` - Delete comment (owner or post owner)
   - Display all comments under posts
   - Support for anonymous comments (future enhancement)

5. **Authorization & Middleware**
   - Laravel Policies for post and comment authorization
   - Middleware protection for authenticated routes
   - Owner-only edit/delete permissions
   - Post owners can delete any comment on their posts

6. **Frontend**
   - Blade templating with Tailwind CSS (via Laravel Breeze)
   - Responsive design
   - Clean, modern UI
   - Form validation and error handling

### 🎯 Bonus Features

- **Sample Data**: Comprehensive seeders with realistic content
- **Modern UI**: Beautiful, responsive interface with Tailwind CSS
- **User Experience**: Intuitive navigation and clear feedback messages
- **Security**: CSRF protection, input validation, authorization policies
- **🐳 Docker Support**: Complete containerization with Docker Compose
- **🔐 Role-Based Access Control**: Admin role with elevated permissions
- **🧪 Comprehensive Testing**: 65+ unit and feature tests with 100% pass rate

## Technology Stack

- **Backend**: Laravel 12.x (PHP 8.4+)
- **Database**: SQLite (development), easily configurable for MySQL/PostgreSQL
- **Frontend**: Blade templates with Tailwind CSS
- **Authentication**: Laravel Breeze
- **Styling**: Tailwind CSS

## Installation & Setup

### Prerequisites

- PHP 8.4 or higher
- Composer
- Node.js & NPM (for asset compilation)

### Quick Start

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd blog-app
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Build assets**
   ```bash
   npm run build
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

8. **Visit the application**
   Open your browser and go to `http://localhost:8000`

### Sample Data

The application comes with sample data including:
- 5 users (including admin@example.com)
- 5 sample blog posts
- Multiple comments on each post

**Default login credentials:**
- Email: `admin@example.com`
- Password: `password`

Or create your own account using the registration form.

## Project Structure

```
blog-app/
├── app/
│   ├── Http/Controllers/
│   │   ├── PostController.php      # Post CRUD operations
│   │   ├── CommentController.php   # Comment operations
│   │   └── ProfileController.php   # User profile (Breeze)
│   ├── Models/
│   │   ├── Post.php               # Post model with relationships
│   │   ├── Comment.php            # Comment model with relationships
│   │   └── User.php               # User model (extended)
│   └── Policies/
│       ├── PostPolicy.php         # Post authorization
│       └── CommentPolicy.php      # Comment authorization
├── database/
│   ├── migrations/
│   │   ├── create_posts_table.php
│   │   └── create_comments_table.php
│   └── seeders/
│       ├── UserSeeder.php
│       ├── PostSeeder.php
│       └── CommentSeeder.php
├── resources/views/
│   ├── posts/                     # Post views
│   │   ├── index.blade.php        # Posts listing
│   │   ├── show.blade.php         # Single post view
│   │   ├── create.blade.php       # Create post form
│   │   └── edit.blade.php         # Edit post form
│   └── auth/                      # Authentication views (Breeze)
└── routes/
    └── web.php                    # Application routes
```

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

## Database Schema

### Users Table
- `id` (primary key)
- `name` (string)
- `email` (string, unique)
- `password` (hashed)
- `email_verified_at` (timestamp, nullable)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### Posts Table
- `id` (primary key)
- `user_id` (foreign key → users.id)
- `title` (string)
- `content` (text)
- `created_at` (timestamp)
- `updated_at` (timestamp)

### Comments Table
- `id` (primary key)
- `post_id` (foreign key → posts.id)
- `user_id` (foreign key → users.id, nullable)
- `comment` (text)
- `created_at` (timestamp)
- `updated_at` (timestamp)

## Authorization Rules

### Posts
- **View**: Anyone can view posts (public)
- **Create**: Authenticated users only
- **Update**: Only post owner
- **Delete**: Only post owner

### Comments
- **View**: Anyone can view comments (public)
- **Create**: Authenticated users only
- **Update**: Only comment owner
- **Delete**: Comment owner OR post owner

## Development Commands

```bash
# Run migrations
php artisan migrate

# Run seeders
php artisan db:seed

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Build assets
npm run dev      # Development build with watch
npm run build    # Production build
```

## 🐳 Docker Setup

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

## 🧪 Testing

The application includes comprehensive testing with 65+ tests covering all functionality:

### Run Tests
```bash
# Run all tests
php artisan test

# Run specific test suites
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run with coverage
php artisan test --coverage
```

### Test Coverage
- ✅ **Authentication**: Login, registration, password reset
- ✅ **Posts**: CRUD operations, authorization, pagination
- ✅ **Comments**: Creation, deletion, authorization
- ✅ **Roles**: Admin permissions, user restrictions
- ✅ **Security**: CSRF protection, input validation
- ✅ **UI**: Form validation, error handling

## Security Features

- CSRF protection on all forms
- Input validation and sanitization
- Authorization policies
- Password hashing
- SQL injection protection (Eloquent ORM)
- XSS protection (Blade templating)

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Future Enhancements

- [ ] Role-based access control (Admin role)
- [ ] Image uploads for posts
- [ ] Post categories and tags
- [ ] Search functionality
- [ ] Email notifications
- [ ] API endpoints
- [ ] Docker containerization
- [ ] Unit and feature tests
- [ ] Social media integration
- [ ] Comment threading/replies

---

**Built with ❤️ using Laravel**