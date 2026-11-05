# 🧺 T.A.M Laundry - Management Information System

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/MariaDB-003545?style=for-the-badge&logo=mariadb&logoColor=white" />
  <img src="https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white" />
</p>

A comprehensive, production-grade web-based management information system designed to solve operational bottlenecks in micro-scale laundry businesses. Built with the **Laravel** framework, this system natively implements the **M/M/s Queuing Theory Algorithm** to mathematically calculate Estimated Completion Times (EWP) based on real-time machine workloads.

## ✨ Core Features & Technical Highlights

- **🧠 Algorithmic Queue Management (M/M/s):** Dynamically calculates and sets order pickup schedules to prevent bottlenecking at the washing stations, strictly bound to the store's operational hours.
- **💳 Dynamic Digital Payment:** Integrated with the **Duitku API** to generate real-time QRIS Snap Tokens and asynchronously handle webhook callbacks for automated status updates.
- **📍 Geospatial Logistics Tracking:** Utilizes **Nominatim API** (OpenStreetMap) and **Leaflet.js** to map customer coordinates, facilitating accurate pickup and delivery navigation.
- **📱 Automated WhatsApp Notifications:** Leverages the **Fonnte API** to send automated, real-time status updates directly to customers' WhatsApp numbers.
- **🔐 Secure Role-Based Access Control:** Strict segregation of duties among Customers (self-service ordering), Admins (verification & operational updates), and Couriers (logistics management).

## 🏗️ System Architecture
The system adopts a robust **Client-Server architecture** implementing the **MVC (Model-View-Controller)** pattern. Data exchanges with third-party APIs (Duitku, Nominatim, Fonnte) are handled asynchronously using JSON payloads.

<img width="1396" height="734" alt="System Architecture" src="https://github.com/user-attachments/assets/69e9619c-75ef-493b-98b4-7175ddc307e8" />

## 📸 Application Previews

| Admin Dashboard & Order Validation | QRIS Payment Gateway Generation |
| :---: | :---: |
| <img width="2559" height="1394" alt="Admin Dashboard" src="https://github.com/user-attachments/assets/2725094e-d8bc-469d-9de4-586b39e86fda" /> | <img width="2554" height="1467" alt="QRIS Payment" src="https://github.com/user-attachments/assets/e72e5988-d2f9-43d6-a2fd-09abe086e0c5" /> |

## 🚀 Installation & Setup (Local Environment)

1. Clone the repository:
   ```bash
   git clone [https://github.com/HappyFifa/tam-laundry-management-system.git](https://github.com/HappyFifa/tam-laundry-management-system.git)
2. Install dependencies:
   ```bash
   composer install
   npm install && npm run build
3. Environment Setup:
   ```Copy .env.example to .env.
   Configure your local DB_DATABASE, DB_USERNAME, and API Keys (Duitku & Fonnte).
4. Generate application key and run migrations:
   ```Bash
   php artisan key:generate
   php artisan migrate
5. Serve the application:
   ```Bash
   php artisan serve
Developed by Fikri Defi Wulanto as a comprehensive thesis project and academic journal publication.
