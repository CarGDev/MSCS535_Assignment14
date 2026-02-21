# Secure Application

A secure web application with PHP backend and JavaScript frontend featuring authentication, data management, and modern UI.

## Features

- User registration and login with secure password handling
- CSRF protection
- SQL injection prevention (PDO prepared statements)
- XSS protection
- Real-time form validation
- Password strength requirements
- Show/hide password toggle
- Responsive dark theme UI

## Tech Stack

- **Backend**: PHP 8+ with PostgreSQL
- **Frontend**: Vanilla JavaScript, HTML, CSS
- **Database**: PostgreSQL

## Requirements

- PHP 8.0+
- PostgreSQL
- Node.js (for formatting/linting)

## Setup

1. **Install dependencies**:
   ```bash
   npm install
   composer install
   ```

2. **Configure database**:
   Copy `.env.example` to `.env` and update with your database credentials:
   ```
   DB_HOST=localhost
   DB_NAME=securecode
   DB_USER=your_username
   DB_PASSWORD=your_password
   DB_PORT=5432
   ```

3. **Create database**:
   ```bash
   psql -h localhost -U your_username -d postgres -c "CREATE DATABASE securecode;"
   ```

4. **Run migrations**:
   ```bash
   psql -h localhost -U your_username -d securecode -f config/schema.sql
   ```

5. **Start development server**:
   ```bash
   php -S localhost:8000 -t public
   ```

6. **Access the app**: http://localhost:8000

## Development

- **Format code**: `npm run format`
- **Lint code**: `npm run lint`

## Project Structure

```
├── config/
│   ├── database.php      # Database connection & helpers
│   └── schema.sql       # Database schema
├── api/
│   └── index.php       # API endpoints
├── public/
│   ├── index.php       # Router
│   ├── views/          # Page templates
│   ├── js/             # JavaScript files
│   └── styles/         # CSS files
└── .env.example        # Environment template
```

## Password Requirements

- Minimum 8 characters
- At least one uppercase letter
- At least one lowercase letter
- At least one number
- At least one special character
