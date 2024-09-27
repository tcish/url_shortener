# URL Shortener web & api

## Introduction

This project is a simple URL shortener that converts long web addresses into short, easy-to-share links. The shortened URLs still redirect users to the original destinations.

---

## Prerequisites

Before starting, ensure the following software is installed on your system:

- [PHP >= 8.2](https://www.php.net/downloads)
- [Composer](https://getcomposer.org/download/)
- [Node.js & npm](https://nodejs.org/en/)
- [Git](https://git-scm.com/)
- [MySQL (or any supported database)](https://www.mysql.com/downloads/)

---

## Installation

**Clone the Repository**

First, clone the project repository from GitHub using the following command:

```bash
git clone https://github.com/tcish/url_shortener.git
```

**Navigate into the project directory:**
```bash
cd url_shortener
```

**Install dependencies:**
```bash
composer i
npm i
```

**Copy the .env.example file to create your .env configuration file or run below command**
```bash
cp .env.example .env
```

**Open the .env file and add database name**
- DB_DATABASE=your_database_name

**Generate an application key:**
```bash
php artisan key:generate
```

**Migrate the database:**
```bash
php artisan migrate
```
**Build local frontend assets:**
```bash
npm run dev
```

**Start the Laravel development server:**
```bash
php artisan serve
```

---

## Features

**User Registration and Authentication:**
- Implemented user registration and login functionality using Laravel’s built-in authentication ([Breeze](https://laravel.com/docs/11.x/starter-kits#laravel-breeze)).

**URL shorting & insights**
- A web form to input long URLs.
- Automatically generate short URLs for inputted long URLs.
- Display both the original URL and its corresponding short URL.
- Short URLs redirect users to their original long URLs.
- Track the number of times each short URL is accessed (click count).
- User can also view ip addresses & clicked time in insights.

**Permission**
- Registered users can manage their own shortened URLs, including viewing and deleting links and accessing details insights.

**API**
**hit the below apli link to get shorted url**
- development
```bash
http://127.0.0.1:8000/api/short-url/{url}
or
http://localhost:8000/api/short-url/{url}
```

- production
```bash
https://short.000.pe/api/short-url/{url}
```

---

## Tech Stack

**Client:** HTML, CSS, TainwindCSS, JavaScript, Tippy.js

**Server:** PHP, Laravel

**Database:** MySQL
