# Laravel Practical Assignment - Backend Developer

## Overview
This project is a secure and optimized Laravel web application demonstrating:
- Multi-Authentication (Admin & Customer)
- Real-time updates via WebSockets and Pusher
- Large-scale product import using Queues and Batch Processing

## Logic & Features
### 1. Multi-Authentication
- **Guards:** `admin`, `web` (customer).
- **Admins:** Manage products and imports. `Admin` model.
- **Customers:** Login/Register. `User` model.
- **Middleware:** `auth:admin` and `auth` are used to protect routes.

### 2. Product Management (Admin)
- CRUD operations for products.
- Fields: Name, Description, Price, Stock, Category, Image.

### 3. Bulk Import
- **Feature**: Upload CSV with up to 100k products.
- **Optimization**: Uses `ImportProducts` job with `fgetcsv` for memory-efficient chunked reading (1000 rows/batch).
- **Validation**: Basic validation executed per row.
- **Queue**: Processed via `database` driver to prevent timeouts.

### 4. Real-Time Updates (WebSockets)
- **Presence**: Shows real-time Online/Offline status of users in Admin Dashboard.
- **Live Products**: New products added by other admins appear instantly in the dashboard table without refresh.
- **Tech**: `laravel-websockets` (self-hosted replacement for Pusher) + `pusher-js`.
- **Event**: `UserStatusChanged`, `ProductCreated`.
- **Channel**: `presence-online`, `products`.

## Setup Instructions
1. **Clone Repo:**
   ```bash
   git clone <repo-url>
   cd laravel-practical
   ```
2. **Install Dependencies:**
   ```bash
   composer install
   npm install && npm run build
   ```
3. **Configure Environment:**
   - Copy `.env.example` to `.env`
   - Set Database Credentials (`DB_DATABASE=laravel_practical`)
   - Run `php artisan key:generate`
   - Set Queue Driver: `QUEUE_CONNECTION=database`
   - Set Broadcast Driver: `BROADCAST_DRIVER=pusher`
   - Set Pusher Keys (Local):
     ```ini
     PUSHER_APP_ID=local
     PUSHER_APP_KEY=local
     PUSHER_APP_SECRET=local
     PUSHER_HOST=127.0.0.1
     PUSHER_PORT=6001
     PUSHER_SCHEME=http
     PUSHER_APP_CLUSTER=mt1
     VITE_PUSHER_HOST=127.0.0.1
     VITE_PUSHER_PORT=6001
     ```
4. **Setup Database & Migrations:**
   ```bash
   php artisan migrate
   ```
5. **Run WebSocket Server:**
   ```bash
   php artisan websockets:serve
   ```
6. **Run Queue Worker:**
   ```bash
   php artisan queue:work
   ```
7. **Serve App:**
   ```bash
   php artisan serve
   ```

## Testing
Run the test suite:
```bash
php artisan test
```
**Tests Covered:**
- `ProductImportTest`: Verifies file upload and Job dispatch.
- `ProductCreationTest`: Verifies Admin product creation flow and Auth protection.

## Architecture Notes
- **Import Optimization**: Reading large CSVs into memory causes crashes. I used `fopen` and `fgetcsv` to stream the file line-by-line and insert in chunks of 1000.
- **Queues**: Import is dispatched to a background queue to ensure the Admin UI remains responsive.
- **WebSockets**: Used a Presence Channel (`online`) to track active connections securely.

## Deliverables
- [x] Codebase
- [x] Sample CSV (`products_sample_import.csv`)
- [x] README.md
- [x] Tests
