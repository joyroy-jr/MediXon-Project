=======================================================
  MediXon — Medical Equipment & Medicine Sharing
  Version 2.0 | Bangladesh
=======================================================

SETUP INSTRUCTIONS
------------------
1. Copy the entire "medixon" folder to your XAMPP htdocs directory
   Path: C:\xampp\htdocs\medixon\

2. Open phpMyAdmin: http://localhost/phpmyadmin

3. Create a new database named: medixon_db

4. Import the database:
   Click "Import" → Choose file → Select "medixon_db.sql" → Go

5. Open browser: http://localhost/medixon/setup.php
   This creates the admin account automatically.
   ⚠️ DELETE setup.php after running it!

6. Open: http://localhost/medixon/index.html

=======================================================
LOGIN CREDENTIALS
-----------------
Admin:  admin@medixon.com  /  Admin@12345
        URL: http://localhost/medixon/admin-login.html

Users:  Register at: http://localhost/medixon/signup.html

=======================================================
PROJECT STRUCTURE
-----------------
HTML Pages (15):
  index.html          - Landing page
  about.html          - About MediXon
  feedback.html       - Public feedback form
  login.html          - User login
  signup.html         - User registration
  dashboard.html      - Main dashboard (Equipment + Medicine tabs)
  browse.html         - Browse equipment
  browseMedicine.html - Browse medicine
  uploadEquipment.html - Add new equipment
  uploadMedicine.html  - Add new medicine
  myRequests.html     - My sent requests (Equipment + Medicine tabs)
  ownerRequests.html  - Received requests (Equipment + Medicine tabs)
  chat.html           - Real-time chat with file attachments
  profile.html        - Edit profile & upload picture
  admin-login.html    - Admin login portal
  admin.php           - Admin panel (PHP-guarded)

PHP Backend (24 files):
  config.php          - Database class + helper functions
  models.php          - 7 OOP model classes (User, Equipment, Request,
                        Medicine, MedicineRequest, Message, Feedback)
  login.php / signup.php / LogoutServlet.php
  admin-login.php / admin-logout.php / setup.php
  UploadEquipmentServlet.php / UploadMedicineServlet.php
  requestEquipment.php / requestMedicine.php
  acceptRequest.php / rejectRequest.php
  acceptMedicineRequest.php / rejectMedicineRequest.php
  getRequests.php / getMyRequests.php
  getMedicineRequests.php / getMyMedicineRequests.php
  browseEquipment.php / browseMedicine.php
  dashboardData.php / adminData.php / adminDelete.php
  getChatUsers.php / loadMessages.php / sendMessage.php
  profileData.php / updateProfile.php / submitFeedback.php

JavaScript (3 files):
  script.js              - Dashboard + Browse + Request logic
  chat.js                - Real-time chat system
  myRequestsScripts.js   - My sent requests (equip + medicine)
  ownerRequestsScripts.js - Received requests (equip + medicine)

Database:
  medixon_db.sql      - 6 tables: users, equipment, requests,
                        medicines, medicine_requests, messages, feedback

=======================================================
FEATURES
--------
Equipment Module:
  - Donate / Swap / Rent / Low Price Sale modes
  - 7 categories + photo upload
  - Smart search + 3 filters
  - Request system with duplicate check
  - Auto-chat when request accepted

Medicine Module:
  - Donate / Swap / Low Price Sale modes
  - Dosage form, strength, generic name fields
  - Expiry date with visual warning
  - Sealed/Opened/Partial condition
  - Completely separate medicine_requests table

Chat System:
  - Real-time polling (2500ms)
  - File & image attachments
  - Profile picture avatars
  - Auto-seeded on request accept

Admin Panel:
  - Overview stats (6 counters)
  - Manage: Users, Equipment, Equipment Requests,
    Medicines, Medicine Requests, Messages, Feedback
  - Delete any record

=======================================================
Tech Stack: PHP 8.0 | MySQL 8.0 | HTML5 | CSS3 | JS
=======================================================
