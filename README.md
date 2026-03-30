# Iran News Project

Full-stack student project (FrontOffice + BackOffice) built with PHP and PostgreSQL.

## Quick Start

1. Copy environment file:

	cp .env.example .env

2. Build and run containers:

	docker compose up --build -d

3. Open application:

	- FrontOffice: http://localhost:8080
	- BackOffice login: http://localhost:8080/admin/login

## Default Admin Credentials

- Email: value from ADMIN_DEFAULT_EMAIL in .env
- Password: value from ADMIN_DEFAULT_PASSWORD in .env

## Project Goals

- Public content pages with clean URLs
- Admin CRUD for articles and categories
- SEO-ready pages (meta tags, semantic headings, image alt)
- Dockerized environment and PostgreSQL database
