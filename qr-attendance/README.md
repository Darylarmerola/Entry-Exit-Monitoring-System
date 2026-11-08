# QR Attendance System — Laravel 12

This is a Laravel 12 application source package (application files, not `vendor/` or installed dependencies).

## Requirements
- PHP 8.2+
- Composer
- MySQL / MariaDB (XAMPP or Laragon)
- Node.js and npm (optional; this package uses CDN Bootstrap and QR libraries, so the basic UI works without compiling assets)
- [ngrok](https://ngrok.com/download) (for securely accessing the app and phone camera over the internet)

## Installation (Windows)
1. Install XAMPP or Laragon and start Apache and MySQL.
2. Open PowerShell in the folder where you want the project:
   ```powershell
   composer create-project laravel/laravel qr-attendance
   ```
3. Open the newly created `qr-attendance` folder.
4. Extract this ZIP and copy its `app`, `database`, `resources`, `routes`, and `bootstrap` folders into `qr-attendance`, allowing files to merge/replace. Copy the other project files as needed, but keep your existing `.env` if the project is already configured.
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
   npm install
   npm run build
   php artisan serve --host=127.0.0.1 --port=8000
   ```
8. Visit http://127.0.0.1:8000 on your laptop.

## Access the system on an Android phone using ngrok (HTTPS)
Use this when the phone is the student scanner. Mobile browsers generally require HTTPS to grant camera access. Do not use the laptop's plain `http://192.168...` address for camera scanning.

### One-time setup
1. Create an ngrok account and install ngrok on your Windows laptop: https://ngrok.com/download
2. Follow ngrok's setup instructions to add your account's authtoken. You normally do this once:
   ```powershell
   ngrok config add-authtoken YOUR_NGROK_AUTHTOKEN
   ```
   Keep the authtoken private; do not put it in this project's `.env` or share it.

### Every time you want to use the phone
1. On your laptop, open PowerShell in the Laravel project folder and start Laravel:
   ```powershell
   php artisan serve --host=127.0.0.1 --port=8000
   ```
   Leave this terminal running.
2. Open a **second** PowerShell window and start the tunnel:
   ```powershell
   ngrok http http://127.0.0.1:8000
   ```
   Leave this terminal running too. Ngrok will display a forwarding address similar to:
   ```text
   Forwarding  https://your-name.ngrok-free.app -> http://127.0.0.1:8000
   ```
3. Copy the **HTTPS** address shown by ngrok (not the `http://` address).
4. On your Android phone, open Chrome and visit that HTTPS address. The phone needs an internet connection; it does not have to be connected to the same Wi-Fi as the laptop.
5. Log in to the QR Attendance System, open **Scan QR**, and allow Chrome to use the camera when prompted. If permission was previously denied, open Chrome's site settings for the ngrok address and allow Camera, then reload the page.
6. Keep both the Laravel and ngrok terminal windows open while using the system. Stop them with `Ctrl+C` when finished.

### If your ngrok URL changes
A free ngrok URL may change when you restart the tunnel. Always use the current HTTPS forwarding URL printed in the ngrok terminal. If the application has an `APP_URL` value set to an old ngrok URL and generated links or redirects point to the wrong address, update `APP_URL` in `.env` to the current HTTPS URL, then run:
```powershell
php artisan optimize:clear
```
For example:
```env
APP_URL=https://your-current-name.ngrok-free.app
```
Do not include a trailing slash. When the URL changes again, update it again if the app relies on `APP_URL` for generated absolute links.

### ngrok and phone troubleshooting
- **`ERR_NGROK_8012` / upstream connection error:** Make sure `php artisan serve` is still running on port `8000`, then restart `ngrok http http://127.0.0.1:8000`.
- **`ERR_NGROK_334` / endpoint already online:** Stop the other ngrok process using `Ctrl+C`, then start the tunnel again. Do not run two tunnels with the same reserved endpoint unless you intentionally configure pooling.
- **Camera permission does not appear:** Confirm you opened the `https://` ngrok URL in Chrome, not an HTTP URL. Check Chrome site settings and allow Camera.
- **Laravel shows a 419 or redirects to an old URL:** Update `APP_URL` to the current HTTPS ngrok address and run `php artisan optimize:clear`. Close old browser tabs and open the current URL again.
- **The page cannot be reached:** Check that both terminals are still running and test `http://127.0.0.1:8000` on the laptop first.
- **Ngrok warning/interstitial page:** If ngrok shows its free-tier browser warning, continue through it to reach the application.

## Initial administrator accounts
- Angela / `olasiman`
- Nicole / `Vacal`
- Daryl / `Armerola`

These are initial passwords only. Change them after first login. Passwords are stored as Laravel hashes.

## How to use
1. Register students and teachers from the homepage. Public registration does not allow creating administrators.
2. Log in using the account role.
3. Teacher: create an attendance session with course, subject, and date. Open the generated QR display.
4. Student: open **Scan QR**, allow camera access, and scan the teacher's QR. The first successful scan records time-in and adds the student to the teacher's attendance list.
5. After time-in, the student uses the **Time Out** button in the student interface to record time-out. Scanning the QR again is not the time-out action.
6. Teacher: open the session attendee list to view attendance, remove an attendee, or export a CSV file that opens in Microsoft Excel.

## Notes / important behavior
- QR attendance uses a random session token. A scan is accepted only for an active session. A student's first successful scan records time-in; time-out is submitted separately using the student's **Time Out** button.
- Attendance timestamps use the `Asia/Manila` timezone (Philippine Time, UTC+08:00) on the Laravel server. The server, not the phone's clock, determines the recorded time.
- The teacher controls session activation. Close a session when attendance taking is finished.
- Camera scanning requires HTTPS or localhost. `http://127.0.0.1:8000` is acceptable for local development on the laptop; use the HTTPS ngrok URL on the phone.
- The export is CSV (Excel-compatible). Open it in Excel and use **Save As → .xlsx** if a native XLSX workbook is required.
- This is a development starter. Before deployment, use strong passwords, HTTPS, backups, rate limiting, and review your institution's privacy requirements. Do not expose the seeded administrator credentials publicly.
- The default course field is free text. You can later replace it with a managed courses/subjects table.


## Automatically generated student and teacher IDs

- Student accounts receive IDs such as `STU-000001`, `STU-000002`, and so on.
- Teacher accounts receive IDs such as `TEA-00001`, `TEA-00002`, and so on.
- The student and teacher counters are independent and are generated on the server during account creation. Users do not type their own IDs.
- The ID is shown on the student's dashboard, teacher's dashboard, and admin account list.
- After updating an existing installation, run `php artisan migrate` so Laravel creates the ID counters. This migration normalizes existing student IDs and teacher IDs into the new formats, ordered by account creation/database ID. Attendance records remain linked to the same user records and are not detached. If your teachers' current `school_id` values are official external IDs that must be retained, back up the database and contact me before running this migration; the migration replaces those values with the new generated `TEA-` IDs.

## Recent fixes

- Restored the Vite CSS/JS entry-point reference in the shared Blade layout and retained the existing navy/blue Bootstrap dashboard styling.
- Student IDs are generated automatically in the `STU-000001` format, and teacher IDs are generated automatically in the `TEA-00001` format. Each role has its own independent sequence; neither role needs to enter an ID manually.
- QR scanning supports explicit start/stop and camera switching, prefers a rear camera on phones, and reports the HTTPS/permission requirement clearly.
- Teachers can remove an attendance record from their own session; the route verifies session ownership and attendance membership.
- Forwarded proxy headers are trusted so HTTPS URLs and secure-context camera access work correctly behind an HTTPS reverse proxy such as ngrok.
- Attendance time-in and time-out are intended to use Philippine time (`Asia/Manila`).
- Logout returns to the login page.

### Environment setup

Copy `.env.example` to `.env`, set your database credentials, then run `php artisan key:generate`, `php artisan migrate`, and `npm install && npm run build`. Set `APP_URL` to the current public HTTPS URL when using ngrok if generated links or redirects need the public host. Do not commit or share `.env`.
