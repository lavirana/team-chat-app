# 💬 Team Chat App

A real-time team chat application inspired by Slack — built with **Laravel 12**, **Vue.js 3**, **PostgreSQL**, and **Laravel Reverb**.

![Laravel](https://img.shields.io/badge/Laravel-12-red?style=flat-square&logo=laravel)
![Vue.js](https://img.shields.io/badge/Vue.js-3-green?style=flat-square&logo=vue.js)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15-blue?style=flat-square&logo=postgresql)
![Laravel Reverb](https://img.shields.io/badge/Laravel_Reverb-WebSocket-purple?style=flat-square)

---

## ✨ Features

- 🔐 User registration and login (Laravel Sanctum)
- 🏢 Create and manage workspaces
- 📢 Public and private channels
- 💬 Real-time messaging (Laravel Reverb WebSocket)
- 📩 Direct messages between users
- 📎 File and image sharing
- 😀 Message reactions (emoji)
- 🔔 Live notification bell with unread count
- 🟢 Online / offline user status
- ✅ Message read receipts
- 🔍 Search messages
- 👥 Role-based access (Admin / Member)

---

## 🛠️ Tech Stack

### Backend
| Technology | Purpose |
|-----------|---------|
| Laravel 12 | API and business logic |
| Laravel Reverb | Real-time WebSocket server (self-hosted) |
| Laravel Sanctum | Token-based API authentication |
| Laravel Queue | Background jobs and notifications |
| Spatie Permission | Roles and permissions |
| PostgreSQL 15 | Main database |

### Frontend
| Technology | Purpose |
|-----------|---------|
| Vue.js 3 (Composition API) | UI framework |
| Vue Router 4 | Client-side routing |
| Pinia | State management |
| Laravel Echo + Pusher JS | WebSocket client |
| Axios | API calls |
| Chart.js | Dashboard charts |

---

## 📸 Screenshots

> Coming soon

---

## ⚙️ Requirements

- PHP 8.2+
- Composer
- Node.js 18+
- PostgreSQL 15+
- Laravel 12

---

## 🚀 Installation

### 1. Clone the repository

```bash
git clone https://github.com/your-username/team-chat-app.git
cd team-chat-app
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Set up environment

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure database in `.env`

```bash
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=chat_app
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

### 5. Run migrations

```bash
php artisan migrate
```

### 6. Install frontend dependencies

```bash
cd chat-frontend
npm install
```

### 7. Configure frontend `.env`

```bash
cp .env.example .env
# Update VITE_REVERB_* values to match your backend .env
```

---

## ▶️ Running the Project

You need 4 terminals running at the same time:

```bash
# Terminal 1 — Laravel API
php artisan serve

# Terminal 2 — Laravel Reverb (WebSocket server)
php artisan reverb:start --debug

# Terminal 3 — Queue worker
php artisan queue:work

# Terminal 4 — Vue frontend
cd chat-frontend
npm run dev
```

Then open: **http://localhost:5173**

---

## 📁 Project Structure

```
chat-app/                   ← Laravel backend
├── app/
│   ├── Http/Controllers/
│   ├── Models/
│   ├── Events/
│   └── Jobs/
├── routes/
│   ├── api.php
│   └── channels.php
└── ...

chat-frontend/              ← Vue.js frontend
├── src/
│   ├── views/
│   ├── components/
│   ├── stores/
│   ├── composables/
│   └── router/
└── ...
```

---

## 🗺️ Roadmap

See [ROADMAP.md](./ROADMAP.md) for the full development plan.

---