# Audiogram API

This project is a Laravel-based API designed to be a part of bigger project, and dream.

---

## **Features**
- **Health Check Endpoint**
  Simple endpoint to check API availability.

- **Authentication:**
  - Passwordless authentication (send and verify token).
  - Social login (supports Google and GitHub).
  - Traditional email/password login and registration.

- **Audiogram Management:**
  - Store, retrieve, and display audiograms to adjust audio settings based on user needs.

- **User Profile:**
  - Update user profile details.

---

## **API Endpoints**

### **Health Check**
| Method | Endpoint   | Description               |
|--------|------------|---------------------------|
| GET    | `/health`  | Returns API health status.|

---

### **Authentication**

#### Guest Routes
| Method | Endpoint                              | Description                                  |
|--------|---------------------------------------|----------------------------------------------|
| POST   | `/auth/providers/{provider}/callback` | Handle social login callback (Google/GitHub).|
| POST   | `/auth/passwordless/token`           | Send passwordless authentication token.      |
| POST   | `/auth/passwordless/verify`          | Verify passwordless token.                   |
| POST   | `/auth/login`                        | Traditional email/password login.            |
| POST   | `/auth/register`                     | Register a new user.                         |

**Note:** Replace `{provider}` with either `google` or `github`.

#### Protected Routes (Require Authentication)
| Method | Endpoint   | Description            |
|--------|------------|------------------------|
| PUT    | `/profile` | Update user profile.   |

---

### **Audiograms**

| Method | Endpoint      | Description                           |
|--------|---------------|---------------------------------------|
| GET    | `/audiograms` | List all audiograms for the user.     |
| POST   | `/audiograms` | Create a new audiogram.               |
| GET    | `/audiograms/{id}` | Retrieve details for a specific audiogram.|

---

## **Setup Instructions**

### 1. Clone the Repository
```bash
git clone https://github.com/Nabbra/nabbra-api
cd nabbra-api
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Set Up Environment

Copy .env.example to .env

```bash
cp .env.example .env
```

Configure your .env file with the appropriate settings (database, etc.).

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Run Migrations

```bash
php artisan migrate
```

### 6. Serve the Application

On your own! but hint:

```bash
php artisan serve
```

## Testing

```bash
php artisan test
```
