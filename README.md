# 🚀 LeetMatcher — Academic & Professional Matching Platform

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="200" alt="Laravel Logo">
</p>

<p align="center">
  <strong>Connecting students and companies through intelligent matching and real-time collaboration.</strong>
</p>

---

## 📌 Project Overview

**LeetMatcher** is a dynamic web platform designed to bridge the gap between the **academic world (students)** and the **professional world (companies)**.

It leverages a **skills-based matching algorithm** to connect the right candidates with the right opportunities, improving recruitment efficiency and collaboration.

---

## 🎯 Project Scope

### ✅ Included Features

- Responsive web platform (desktop & mobile)
- User management (Students, Recruiters, Admins)
- Profile management & CV upload
- Project/job posting system
- Application management system
- Skills-based matching algorithm
- Real-time internal messaging
- Interview scheduling & video conferencing
- Feedback & rating system
- Blog (career advice & articles)
- Email notifications

### ❌ Excluded Features

- Native mobile apps (Android / iOS)
- Payment systems
- Advanced AI (Deep Learning in production)
- External API integrations (LinkedIn, etc.)
- Production cloud deployment

---

## 🏗️ Technical Architecture

### 🧰 Technologies Used

| Layer        | Technologies |
|-------------|-------------|
| Backend      | PHP 8+, Laravel |
| Frontend     | Vue.js, Tailwind CSS, JavaScript |
| Database     | MySQL / PostgreSQL |
| Architecture | MVC (Model-View-Controller) |
| Versioning   | Git & GitHub |
| Auth         | Laravel Auth (Breeze / Jetstream) |

---

### 🧠 Architecture Design

- MVC Architecture
- Service Layer for:
  - Matching logic
  - Notifications
- Internal APIs for:
  - Messaging
  - Recommendations

---

## 👥 User Roles

| Role | Description |
|------|------------|
| **Admin** | Manages users, moderates content, monitors system |
| **Recruiter** | Posts projects, evaluates candidates, manages interviews |
| **Student** | Creates profile, uploads CV, applies, receives recommendations |

---

## ✨ Key Features

### 🧾 Profiles & CV
- CV upload & management  
- (Bonus) CV parsing for skill extraction  
- Profile with skills, education, portfolio  
- CV scoring & improvement suggestions  

---

### 🧠 Matching System
- Automatic matching based on skills  
- Matching score between candidate & project  
- Peer recommendation between students  

---

### 🎥 Recruitment & Interviews
- Interview scheduling system  
- (Bonus) Dedicated interview room  
- Candidate evaluation system (scoring grid)  
- Calendar synchronization  
- Interview feedback  

---

### 💬 Communication & Engagement
- Real-time chat (WebSockets)  
- Feedback & review system  
- Blog with career advice  

---

## 🔄 System Logic & Automation

- ⏰ Scheduled tasks (Cron Jobs):
  - Interview reminders  
  - Weekly job recommendations  
- 🔔 Notifications:
  - Application updates  
  - Unread messages  
  - Pending actions  

---

## 🗄️ Database Structure (Simplified)

Main entities:

- **Users** → id, email, password, role  
- **Profiles** → bio, cv_path, score_cv  
- **Projects** → title, description, required_skills  
- **Applications** → status (pending / accepted / rejected)  
- **Interviews** → date, score, notes, meeting link  
- **Messages** → sender, receiver, content  

---

## 📂 Application Structure

### 🌐 Public Pages
- Home  
- About  
- Blog  
- Contact  
- Login / Register  

---

### 🎓 Student Dashboard
- Profile & CV  
- Projects list  
- Applications  
- Interviews  
- Messaging  
- Notifications  

---

### 🏢 Recruiter Dashboard
- Project management  
- Candidate list  
- Interviews  
- Evaluation & scoring  
- Messaging  

---

### ⚙️ Admin Dashboard
- User management  
- Blog moderation  
- System statistics  
- Logs  

---

## 🧩 UML & Data Model

### Relationships

```
User (1) — (1) Profile  
User (1) — (N) Projects  
User (1) — (N) Applications  
Project (1) — (N) Applications  
Application (1) — (0..1) Interview  
User (1) — (N) Messages  
```

---

## 🎨 UI/UX Design

### 🎨 Design System
- Colors: Blue, White, Light Gray  
- Typography: Roboto / Inter  
- Icons: Heroicons / FontAwesome  
- Style: Minimalist (LinkedIn-inspired)

### 📱 User Experience
- Sidebar + Topbar navigation  
- Dashboard with visual indicators  
- Alerts, badges, and progress feedback  
- Fully responsive design  

---

## ⚙️ Installation

```bash
# Clone the repo
git clone https://github.com/mo24ali/leetmatcher.git

cd leetmatcher

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database & migrate
php artisan migrate

# Run project
php artisan serve
npm run dev
```

---

## 🚀 Development Roadmap

### 📅 Phases

| Phase | Description | Duration |
|------|------------|--------|
| Analysis | Requirements & specs | 1 week |
| Design | UML & architecture | 1 week |
| Backend | Models, APIs | 2 weeks |
| Frontend | UI integration | 1 week |
| Matching System | Algorithm implementation | 3 days |
| Testing | Validation | 1 week |
| Documentation | Final delivery | 1 week |

---

### 🔄 Methodology
- Agile (light Scrum)
- Weekly sprints
- Version control with Git/GitHub

---

## 🔮 Future Improvements

- 🤖 Advanced AI matching  
- 📊 Analytics dashboard  
- 📱 Mobile version  
- 🧾 Advanced CV parsing  
- 🔔 Smart notifications  

---

## 🤝 Contributing

Contributions are welcome!

1. Fork the project  
2. Create a feature branch  
3. Commit your changes  
4. Open a Pull Request  

---

## 📄 License

This project is licensed under the **MIT License**.

---

## 👨‍💻 Author

**Mohamed Ali**  
Full-Stack Developer | Passionate about scalable platforms  

---

## ⭐ Support

If you like this project, don’t forget to ⭐ the repository!