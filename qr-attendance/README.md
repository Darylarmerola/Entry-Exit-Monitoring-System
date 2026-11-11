# QR Attendance System — Laravel 12

This is a Laravel 12 application source package (application files, not `vendor/` or installed dependencies).

## Requirements
- PHP 8.2+
- Composer
- MySQL / MariaDB (XAMPP or Laragon)
- Node.js and npm (optional; this package uses CDN Bootstrap and QR libraries, so the basic UI works without compiling assets)

## Installation (Windows)
1. Install XAMPP or Laragon and start Apache and MySQL.
2. Open PowerShell in the folder where you want the project:
   ```powershell
   composer create-project laravel/laravel qr-attendance
   ```
3. Open the newly created `qr-attendance` folder.
4. Extract this ZIP and copy its `app`, `database`, `resources`, `routes`, and `bootstrap` folders into `qr-attendance`, allowing files to merge/replace.
5. Create a MySQL database named `qr_attendance` in phpMyAdmin.
6. Copy `.env.example` to `.env` if `.env` does not exist, then set:
   ```env
   APP_NAME="QR Attendance"
   APP_URL=http://127.0.0.1:8000
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=qr_attendance
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   Adjust the database username/password for your installation.
7. Run:
   ```powershell
   composer install
   php artisan key:generate
   php artisan migrate --seed
   php artisan serve
   ```
8. Visit http://127.0.0.1:8000

## Initial administrator accounts
- Angela / `olasiman`
- Nicole / `Vacal`
- Daryl / `Armerola`

These are initial passwords only. Change them after first login. Passwords are stored as Laravel hashes.

## How to use
1. Register students and teachers from the homepage. Public registration does not allow creating administrators.
2. Log in using the account role.
3. Teacher: create an attendance session with course, subject, and date. Open the generated QR display.
4. Student: open **Scan QR**, allow camera access, and scan the teacher's QR. The first successful scan records time-in; the next scan records time-out.
5. Teacher: open the session attendee list to view attendance, remove an attendee, or export a CSV file that opens in Microsoft Excel.

## Notes / important behavior
- QR attendance uses a random session token. A scan is accepted only for an active session and only once for time-in and once for time-out per student.
- The teacher controls session activation. Close a session when attendance taking is finished.
- Camera scanning requires HTTPS or localhost. `http://127.0.0.1:8000` is acceptable for local development.
- The export is CSV (Excel-compatible). Open it in Excel and use **Save As → .xlsx** if a native XLSX workbook is required.
- This is a development starter. Before deployment, use strong passwords, HTTPS, backups, rate limiting, and review your institution's privacy requirements. Do not expose the seeded administrator credentials publicly.
- The default course field is free text. You can later replace it with a managed courses/subjects table.

## Recent fixes

- Restored the Vite CSS/JS entry-point reference in the shared Blade layout and retained the existing navy/blue Bootstrap dashboard styling.
- Student IDs are generated automatically after a student account is created (`STU-` plus the new user record number). Teachers still enter their school-issued ID.
- QR scanning now supports explicit start/stop and camera switching, prefers a rear camera on phones, and reports the HTTPS/permission requirement clearly.
- Teachers can remove an attendance record from their own session; the route verifies session ownership and attendance membership.
- Forwarded proxy headers are trusted so HTTPS URLs and secure-context camera access work correctly behind an HTTPS reverse proxy such as ngrok. Open the HTTPS ngrok URL on the phone; an HTTP LAN address will not permit camera access on most mobile browsers.
- Logout returns to the login page.

### Environment setup

Copy `.env.example` to `.env`, set your database credentials, then run `php artisan key:generate`, `php artisan migrate`, and `npm install && npm run build`. Set `APP_URL` to the public HTTPS URL when deploying behind a fixed HTTPS domain. Do not commit or share `.env`.
