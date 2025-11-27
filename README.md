# e-MEDICARE - Setup Guide

Emergency medication delivery system connecting patients with pharmacies in Rwanda.

## Quick Setup

### 1. Install XAMPP
- Download from https://www.apachefriends.org/download.html
- Install to default location
- Open XAMPP Control Panel
- Start Apache and MySQL (both should show green)

### 2. Copy Project Files
- Copy the project folder to:
  - Windows: `C:\xampp\htdocs\emedcare`
  - Mac: `/Applications/XAMPP/htdocs/emedcare`

### 3. Create Database
- Open browser, go to: `http://localhost/phpmyadmin`
- Click "New" in sidebar
- Database name: `emedcare`
- Collation: `utf8mb4_general_ci`
- Click "Create"

### 4. Create Tables
- Click on `emedcare` database
- Click "SQL" tab
- Copy and paste this code, then click "Go":

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pharmacies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    distance DECIMAL(5,2) DEFAULT 0.5,
    status VARCHAR(50) DEFAULT 'Open now',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    medication VARCHAR(255),
    quantity INT,
    image_path VARCHAR(255),
    upload_date DATETIME,
    status ENUM('pending', 'processed', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    medicine VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    address TEXT NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE medicines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2),
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 5. Add Sample Data
- In the same SQL tab, paste and click "Go":

```sql
INSERT INTO pharmacies (name, address, phone, distance, status) VALUES
('City Pharmacy', 'KN 4 Ave, Kigali', '+250788111111', 0.5, 'Open now'),
('Health Plus Pharmacy', 'KG 9 Ave, Kigali', '+250788222222', 1.2, 'Open now'),
('MediCare Pharmacy', 'KN 3 Rd, Kigali', '+250788333333', 2.0, 'Open now'),
('Central Pharmacy', 'Avenue de la Paix', '+250788444444', 2.5, 'Open now'),
('Unity Pharmacy', 'KG 11 Ave, Kigali', '+250788555555', 3.0, 'Open now'),
('Remera Pharmacy', 'Remera, Kigali', '+250788666666', 4.5, 'Open now');
```

### 6. Create Upload Folder

**Windows:**
- Navigate to `C:\xampp\htdocs\emedcare`
- Create folder: `uploads`, inside it create: `prescriptions`
- Right-click `uploads` > Properties > Security > Edit > Add "Everyone" with Full Control

**Mac/Linux:**
```bash
cd /Applications/XAMPP/htdocs/emedcare
mkdir -p uploads/prescriptions
chmod -R 777 uploads
```

### 7. Run Application
- Open browser: `http://localhost/emedcare/index.html`
- You should see the e-MEDICARE splash screen

## Testing the App

### Test 1: Register User
1. Click "Sign Up"
2. Enter: Name: `Test User`, Phone: `0788123456`, Email: `test@example.com`, Password: `test123`
3. Should show "Registration successful"

### Test 2: Login
1. Login with Phone: `0788123456`, Password: `test123`
2. Should see home dashboard with "Welcome Test User"

### Test 3: Order Medicine
1. Click "Order Medicine" > "Fill Information"
2. Enter: Medicine: `Paracetamol`, Dosage: `500mg`, Quantity: `20`
3. Click "Continue" > "Looks Good - Find Pharmacy"
4. Should see map and 6 pharmacies listed

### Test 4: Complete Order
1. Click any pharmacy to select it
2. Click "Continue"
3. Select payment method, enter address: `KG 123 Ave, Kigali`
4. Click "Confirm Order"
5. Should show "Order placed successfully"

### Test 5: View Orders
1. Click "My Orders" from home
2. Should see your recent order

### Test 6: Camera Feature
1. "Order Medicine" > "Snap Prescription" > click camera icon
2. Allow camera permissions
3. Take photo > "Continue with Photo"
4. Image saves to `uploads/prescriptions/`

### Test 7: Language Switch
- Click "FR" button - text changes to French
- Click "RW" button - text changes to Kinyarwanda
- Click "EN" - back to English

## Features

**Patient App:**
- User registration and login
- Trilingual (English, French, Kinyarwanda)
- Camera prescription scanning
- Manual medicine entry
- Google Maps pharmacy locator
- Distance filtering
- Order placement and tracking
- Emergency hotline (0788490000)

**Technical:**
- Frontend: HTML, CSS, JavaScript
- Backend: PHP
- Database: MySQL
- Server: Apache (XAMPP)

## Troubleshooting

**"Connection failed"**
- Check MySQL is running in XAMPP
- Verify `db.php` has: username=`root`, password=`''`, database=`emedcare`

**"404 Not Found"**
- Check files are in `htdocs/emedcare/`
- Restart Apache in XAMPP

**"Failed to save file"**
- Check `uploads/prescriptions` folder exists
- Set folder permissions to 777

**No pharmacies showing**
- Run the INSERT pharmacies SQL again
- Check `pharmacies` table has data in phpMyAdmin

**Camera not working**
- Use Chrome browser
- Allow camera permissions when prompted
- Use "Fill Information" as alternative

## File Structure

```
emedcare/
├── index.html
├── db.php
├── register.php
├── login.php
├── prescription_save.php
├── get_pharmacies.php
├── get_orders.php
├── order.php
├── uploads/
│   └── prescriptions/
└── README.md
