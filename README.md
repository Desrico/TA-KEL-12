# Campus Care

> Web-based student counseling service platform developed as a Digital Mental Health Intervention (DMHI) project for Institut Teknologi Del.

<p>
  <img src="https://img.shields.io/badge/PHP-8.2-4F5B93?style=flat-square&logo=php&logoColor=white" alt="PHP 8.2">
  <img src="https://img.shields.io/badge/Laravel-11-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel 11">
  <img src="https://img.shields.io/badge/MySQL-Primary%20Data-4479A1?style=flat-square&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/MongoDB-Student%20Monitoring-47A248?style=flat-square&logo=mongodb&logoColor=white" alt="MongoDB">
  <img src="https://img.shields.io/badge/Vite-Frontend%20Tooling-646CFF?style=flat-square&logo=vite&logoColor=white" alt="Vite">
</p>

## Overview

Campus Care is a web application that supports student counseling services in a more structured, accessible, and privacy-aware way. The system was designed to help students submit counseling requests, schedule sessions, communicate with counselors, access service history, and provide service feedback through a single platform.

Within the scope of the thesis project, Campus Care is positioned as a **Digital Mental Health Intervention (DMHI)** that focuses on improving access to campus counseling services, reducing barriers to help-seeking, and supporting service documentation without replacing professional clinical judgment.

## Why This Project Matters

Students often face academic, social, personal, and environmental pressures. In a residential campus setting, those pressures can become more complex due to adaptation demands, structured rules, and limited counseling access channels.

Campus Care addresses several practical service issues:

- Limited visibility of counseling service information
- Manual scheduling through forms and chat-based communication
- Scattered or non-centralized service documentation
- Reluctance to share personal issues openly
- Limited counselor capacity for administrative follow-up

## DMHI Perspective

From a DMHI perspective, this project focuses on:

- `Accessibility`: helping students reach counseling services through a digital entry point
- `Privacy`: providing features such as anonymous mode to reduce hesitation
- `Continuity of care`: supporting structured records, chat, and session history
- `Service efficiency`: helping counselors manage schedules, communication, and follow-up
- `Digital support, not diagnosis`: the system supports counseling workflows and monitoring, not medical diagnosis

## UCD and Iterative Development

The application was developed using an iterative **User-Centered Design (UCD)** approach within the broader ADDIE process. Feature planning and refinement were carried out incrementally so the product could stay aligned with the needs of students and counselors.

Core UCD principles reflected in the project:

- Focus on real user needs
- Design decisions informed by feedback
- Iterative improvement across sprints
- Evaluation of usability, security, and performance

## Feature Progress by Sprint

### Sprint 1

- `🔐` Registrasi dan login
- `🗓️` Penjadwalan konseling
- `🕶️` Mode anonim

### Sprint 2

- `💬` Chat konseling untuk mahasiswa dan konselor
- `📌` Melihat status jadwal konseling
- `🧾` Riwayat konseling

### Sprint 3

- `⭐` Evaluasi dan feedback layanan
- `🔔` Sistem notifikasi
- `ℹ️` Tentang layanan

## Current Application Scope

In its current implementation, Campus Care includes the following relevant capabilities:

- Student and counselor authentication flow
- Counseling session request and scheduling
- Anonymous identity mode for students
- One-to-one counseling chat
- Group chat support for guided anonymous discussion
- Counseling history and service records
- Feedback and service evaluation
- Web push notifications
- Educational mental health content
- Counselor/admin dashboard and service management
- AI-assisted summary support for counseling documentation

## User Roles

### `Mahasiswa`

Students use the system to:

- Log in to the platform
- Submit counseling requests
- Choose available schedules
- Use anonymous mode
- Chat with counselors
- Monitor counseling schedule status
- View counseling history
- Submit service feedback

### `Konselor / Admin`

Counselors use the system to:

- Review incoming counseling requests
- Manage schedules and session flow
- Communicate with students
- Access counseling records and reports
- Monitor service activity
- Manage educational content and notifications

## Technology Stack

- `Backend`: Laravel 11, PHP 8.2
- `Frontend`: Blade, Vite, Tailwind CSS, Alpine.js
- `Database`: MySQL and MongoDB
- `Realtime / Notification`: Laravel Reverb, Web Push
- `Media / Supporting Services`: Cloudinary integration

## Evaluation Focus

The project evaluation is centered on:

- `Usability`
- `Security`
- `Performance`
- `Blackbox testing`
- `User Acceptance Testing`

## Scope and Boundaries

This system is intended to support campus counseling workflows, not to function as a substitute for psychologists, psychiatrists, or formal medical services.

Project boundaries include:

- Focus on student counseling services in Institut Teknologi Del
- Web-based implementation
- Limited-scope deployment and testing environment
- No clinical diagnosis capability
- No full integration yet with all campus systems

## Getting Started

### Prerequisites

- PHP `^8.2`
- Composer
- Node.js and npm
- MySQL
- MongoDB

### Installation

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run build
php artisan serve
```

Adjust `.env` values for:

- application database
- MongoDB connection
- CIS / campus API configuration
- push notification configuration

## Project Goal

Campus Care aims to provide a professional, structured, and user-aware digital support system for student counseling services. The project combines counseling workflow support, privacy-conscious interaction, and iterative design improvement to better fit the real context of student mental health services on campus.
