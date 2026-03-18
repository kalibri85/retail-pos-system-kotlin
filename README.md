# EasyScan POS System

## Overview
EasyScan POS is a stock management and point-of-sale system consisting of a backend web application and a mobile app. 

The system allows store employees to manage product inventory, scan items using a mobile device, and generate receipts in real time.

---

## Tech Stack
- Kotlin (Android)
- PHP (Backend)
- MySQL (Database)
- REST API (Volley)
- ZXing (Barcode Scanner)
- ESC/POS (Bluetooth Printer)
- Data Import: Excel file processing

---

## Features

### Backend (PHP, MySQL)
- Import product data from Excel files into database
- Manage product inventory (add, update, delete)
- Admin panel for stock control
- Automatic stock updates after sales

### Mobile Application (Kotlin)
- Barcode scanning for product identification
- API integration for retrieving product data
- Cart system with quantity management
- Payment flow (cash, card, gift card)
- Real-time total calculation
- Receipt generation with QR code
- Integration with thermal printer

---

## Architecture
The system consists of two main components:

1. Backend system (PHP + MySQL)  
   - Stores and manages product data  
   - Provides API for mobile application  

2. Mobile application (Kotlin)  
   - Scans products  
   - Communicates with backend via API  
   - Handles checkout and receipt generation  

---

## Key Highlights
- End-to-end system from inventory to checkout  
- API-driven communication between mobile and backend  
- Real-world business logic (pricing, discounts, stock updates)  
- Hardware integration (barcode scanner, thermal printer)  

---

## Future Improvements
- Extend stock management features  
- Improve UI/UX  
- Add advanced reporting system

- ## Setup

### Backend (PHP + MySQL)
- Create a MySQL database
- Import `easyStockPOS-schema.sql`
- Run project on local server (XAMPP)

### Mobile App (Android)
- Open project in Android Studio
- Update API base URL in `MainActivity.kt` to your local IP
- Ensure backend and mobile device are on the same network

### Optional (Printer)
- Configure Bluetooth printer MAC address in project files
- Requires ESC/POS compatible printer
