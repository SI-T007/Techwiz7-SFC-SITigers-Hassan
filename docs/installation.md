# Fan Hub Plus — Comprehensive Installation & Setup Guide

This guide provides step-by-step instructions for deploying and running **Fan Hub Plus** in both standard PHP/Composer environments and local XAMPP setups.

---

## 1. System & Server Requirements

| Component | Minimum Version | Recommended Version |
|---|---|---|
| **PHP** | 8.2+ | 8.2 or 8.3 |
| **Laravel** | 12.x | 12.69+ |
| **MySQL / MariaDB** | MySQL 8.0+ / MariaDB 10.4+ | MariaDB 10.4.32+ |
| **Composer** | 2.x | 2.7+ |
| **Web Server** | Apache 2.4+ or Nginx 1.20+ or `php artisan serve` | PHP Built-in / Apache (XAMPP) |

### Required PHP Extensions
The following standard PHP extensions must be enabled in `php.ini`:
- `pdo` & `pdo_mysql`
- `mbstring`
- `openssl`
- `tokenizer`
- `xml` & `dom`
- `ctype`
- `json`
- `fileinfo`
- `curl`

---

## 2. Standard Development Installation (Recommended)

### Step 1: Clone or Extract the Repository
```bash
cd /path/to/webserver
# Extract the project directory into "Fan Hub Plus"
cd "Fan Hub Plus"
```

### Step 2: Install Composer Dependencies
```bash
composer install --optimize-autoloader
```

### Step 3: Configure Environment
Copy the `.env.example` file to create your active `.env`:
```bash
# On Linux / macOS / PowerShell:
cp .env.example .env

# On Windows Command Prompt:
copy .env.example .env
```

### Step 4: Generate Application Encryption Key
```bash
php artisan key:generate
```

### Step 5: Configure Database Connection
Edit the `.env` file with your MySQL database parameters:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fanhubplus
DB_USERNAME=root
DB_PASSWORD=
```
*(Ensure the database `fanhubplus` is created in MySQL: `CREATE DATABASE fanhubplus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`)*

### Step 6: Run Database Migrations & Seeders
Execute the complete schema migration and seed demo data:
```bash
php artisan migrate --seed
```

### Step 7: Create Public Storage Symlink
Link the private storage disk to the public directory for uploads (avatars, character art, submissions):
```bash
php artisan storage:link
```

### Step 8: (Optional) Configure AI Assistant
If you wish to enable the optional Gemini AI completion features, supply your Google Gemini API key:
```env
AI_PROVIDER=gemini
AI_API_KEY=your_gemini_api_key_here
AI_MODEL=gemini-1.5-flash
```
*Note: If `AI_API_KEY` is left blank, Fan Hub Plus automatically functions in high-performance fallback mode, providing intelligent answers directly from its internal Knowledge Base and MySQL database.*

### Step 9: Launch Application Server
```bash
php artisan serve
```
Access the application in your browser at: **`http://localhost:8000`**

---

## 3. Alternative XAMPP Installation Guide

If developing or evaluating on **XAMPP for Windows**:

1. **Locate XAMPP Web Root**:
   Place the project folder in `C:\xampp\htdocs\Fan Hub Plus`.
2. **Start Services**:
   Open **XAMPP Control Panel** and start **Apache** and **MySQL**.
3. **Create Database in phpMyAdmin**:
   Navigate to `http://localhost/phpmyadmin`, create a new database named `fanhubplus`.
4. **Configure `.env`**:
   Ensure `.env` contains:
   ```env
   APP_URL=http://localhost/Fan%20Hub%20Plus/public
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=fanhubplus
   DB_USERNAME=root
   DB_PASSWORD=
   ```
5. **Run Migrations**:
   Open terminal or PowerShell in `C:\xampp\htdocs\Fan Hub Plus`:
   ```powershell
   & "C:\xampp\php\php.exe" artisan migrate --seed
   & "C:\xampp\php\php.exe" artisan storage:link
   ```
6. **Access Application**:
   Navigate to `http://localhost/Fan%20Hub%20Plus/public` or start `php artisan serve` to browse on `http://127.0.0.1:8000`.

---

## 4. Development & Demo Credentials

The seeded development database includes pre-configured accounts:

| User Type | Email Address | Password | Clearances & Privileges |
|---|---|---|---|
| **Visitor** | *(No login required)* | N/A | Public browsing, content discovery, read lore/events/media |
| **Registered User** | `user@fanhub.test` | `Password@123` | Bookmarking, private notes, 5-star ratings, fan submissions, AI chatbot history, user profile |
| **Administrator** | `admin@fanhub.test` | `Password@123` | Full Admin Control Center (12 modules), content CRUD, moderation queue, analytics, user roles |

*(Note: The above are development and academic demonstration credentials only).*

---

## 5. Troubleshooting & FAQ

- **Issue:** `Storage link already exists or permission denied`  
  **Fix:** Run `php artisan storage:unlink` followed by `php artisan storage:link`.
- **Issue:** `SQLSTATE[HY000] [1049] Unknown database 'fanhubplus'`  
  **Fix:** Create the database `fanhubplus` in MySQL/phpMyAdmin prior to running `php artisan migrate`.
- **Issue:** `Target class [SetSecurityHeaders] does not exist`  
  **Fix:** Run `composer dump-autoload` to regenerate autoload mapping.
