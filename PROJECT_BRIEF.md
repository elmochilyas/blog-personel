# 📋 PROJECT BRIEF — Laravel Personal Blog

> **Client:** Former classmate launching a freelance career  
> **Mission:** Build a personal blog where he publishes technical articles, and visitors can read and filter them — no account needed for readers.  
> **Stack:** Laravel (Eloquent, Blade, Migrations, Named Routes)  
> **Duration:** 5 days — Mon 20/04 → Fri 24/04/2026 at 16:30  
> **Mode:** Solo project

---

## 🎯 Goal

A functional Laravel web application with:
- A **public-facing blog** (no login required)
- A **private dashboard** for the blogger to manage articles
- Full **authentication** (login/logout only — no registration page)
- **Category-based filtering** on the public page

---

## 👥 User Roles

| Role | Access | Created how |
|---|---|---|
| **Visitor** | Public blog only (read + filter) | No account needed |
| **Blogger** | Full dashboard (CRUD articles) | Only via DatabaseSeeder |

---

## 📖 User Stories

### 🌐 Public Blog (No login required)

#### US1 — Article List
- Visitors see all **published** articles (not drafts)
- Each article card shows: **title**, **category**, **publication date**, **content excerpt**
- Acceptance: drafts must NOT appear here

#### US2 — Article Detail
- Visitor clicks an article → sees full content + category
- Acceptance: clicking the title/card navigates to a detail page

#### US3 — Filter by Category
- Visitor can filter the list by selecting a category
- Acceptance: only articles from the selected category appear; all others are hidden

---

### 🔐 Authentication

#### US4 — Login / Logout
- Blogger logs in with **email + password**
- Blogger can log out from anywhere in the dashboard
- **No registration page** — account exists only via Seeder
- Acceptance: wrong credentials show a validation error; correct credentials redirect to dashboard

---

### 🛠️ Article Management (Login required)

#### US5 — Dashboard
- Blogger sees **ALL articles** (drafts + published) with their status
- Acceptance: drafts appear here but NOT on the public blog

#### US6 — Create Article
- Blogger fills: **title**, **content**, **category** (dropdown), **status** (draft or published)
- Acceptance: form validates all fields; article appears in dashboard immediately

#### US7 — Edit Article
- Blogger can modify: title, content, category, status (draft ↔ published)
- Acceptance: changes persist; toggling draft → published makes article appear on public blog

#### US8 — Delete Article
- Blogger can delete an article
- **Confirmation required** before deletion (JS confirm dialog or modal)
- Acceptance: deleted article disappears from both dashboard and public blog

---

## 🎁 Bonus (Pick ONE maximum)

| Option | Description |
|---|---|
| **Pagination** | `->paginate(6)` on the public article list |
| **Search** | Search bar filtering by article title on the public page |
| **Reading time** | Estimated reading time per article (word count ÷ 200) |

---

## 🗄️ Database Schema

### Table: `categories`
| Column | Type | Notes |
|---|---|---|
| id | bigIncrements | PK |
| name | string | e.g., "Laravel", "PHP", "DevOps" |
| timestamps | — | created_at, updated_at |

### Table: `users`
| Column | Type | Notes |
|---|---|---|
| id | bigIncrements | PK |
| name | string | Blogger's name |
| email | string | unique |
| password | string | bcrypt hashed |
| timestamps | — | — |

### Table: `articles`
| Column | Type | Notes |
|---|---|---|
| id | bigIncrements | PK |
| title | string | Required |
| content | text | Required |
| status | enum('draft','published') | Default: draft |
| category_id | foreignId | FK → categories.id |
| user_id | foreignId | FK → users.id |
| published_at | timestamp | nullable — set when status → published |
| timestamps | — | created_at, updated_at |

---

## 🔗 Eloquent Relations

```
Category  →  hasMany(Article)
Article   →  belongsTo(Category)
Article   →  belongsTo(User)
User      →  hasMany(Article)
```

---

## 🛣️ Routes Overview

### Public (no auth)
| Method | URI | Named Route | Description |
|---|---|---|---|
| GET | `/` | `blog.index` | List of published articles |
| GET | `/articles/{id}` | `blog.show` | Article detail |
| GET | `/?category={id}` | — | Filter by category (query param) |

### Auth
| Method | URI | Named Route | Description |
|---|---|---|---|
| GET | `/login` | `login` | Login form |
| POST | `/login` | `login.submit` | Process login |
| POST | `/logout` | `logout` | Logout |

### Dashboard (middleware: auth)
| Method | URI | Named Route | Description |
|---|---|---|---|
| GET | `/dashboard` | `dashboard` | All articles list |
| GET | `/dashboard/articles/create` | `articles.create` | Create form |
| POST | `/dashboard/articles` | `articles.store` | Store new article |
| GET | `/dashboard/articles/{id}/edit` | `articles.edit` | Edit form |
| PUT/PATCH | `/dashboard/articles/{id}` | `articles.update` | Update article |
| DELETE | `/dashboard/articles/{id}` | `articles.destroy` | Delete article |

---

## 🏗️ Laravel Architecture Rules

### Controllers
- `ArticleController` — public blog actions (index, show)
- `DashboardController` — dashboard index
- `Auth/LoginController` — login/logout
- `Dashboard/ArticleController` — CRUD actions (under auth middleware)

### Views (Blade)
```
resources/views/
├── layouts/
│   └── app.blade.php         ← Main layout (required)
├── blog/
│   ├── index.blade.php       ← Public article list
│   └── show.blade.php        ← Article detail
├── dashboard/
│   ├── index.blade.php       ← Blogger dashboard
│   └── articles/
│       ├── create.blade.php
│       └── edit.blade.php
└── auth/
    └── login.blade.php
```

### Models
```
app/Models/
├── User.php       ← $fillable, hasMany(Article)
├── Article.php    ← $fillable, belongsTo(Category), belongsTo(User)
└── Category.php   ← $fillable, hasMany(Article)
```

---

## ✅ Mandatory Technical Constraints

| Constraint | Requirement |
|---|---|
| **Routing** | All routes must be named; show up in `php artisan route:list` |
| **Middleware** | All `/dashboard/*` routes grouped under `auth` middleware |
| **Migrations** | Every table created via Laravel migration — zero manual SQL |
| **Eloquent** | Relations defined on models; used in controllers |
| **Blade** | Single `layouts/app.blade.php` layout; use `@auth` / `@guest` |
| **Validation** | `$request->validate([...])` on every form submit |
| **CSRF** | `@csrf` on every form |
| **Fillable** | `$fillable` array defined on every model |
| **Security** | Unauthenticated access to `/dashboard/*` redirects to `/login` |

---

## 📦 Seeder Requirements

`php artisan migrate:fresh --seed` must produce:

- **4 categories**: e.g., Laravel, PHP, DevOps, JavaScript
- **1 blogger account**: email + hashed password
- **6 articles**: mix of drafts and published, across different categories

---

## 📁 Git Requirements

- **Minimum 15 commits** with clear messages
- **One branch per feature module:**
  - `feature/auth`
  - `feature/article-crud`
  - `feature/public-blog`
- Commit message format examples:
  - `Add Article migration and model`
  - `Implement category filter on public index`
  - `Protect article routes with auth middleware`

---

## 🏆 Grading Criteria

| Area | Weight |
|---|---|
| Laravel Architecture (routes, Eloquent, migrations, MVC separation) | 40% |
| Features (auth, CRUD, draft/publish, category filter, route protection) | 35% |
| Code Quality (validation, @csrf, $fillable, naming, 15+ commits) | 25% |

---

## 🎤 Oral Evaluation — What to Prepare

1. **Live demo flow:** visitor → filter → read → login → dashboard → create draft → publish → edit → delete → logout
2. **Code trace:** explain `GET /articles/3` step by step: route → controller → model → view
3. **Eloquent Q&A:** show the relation between `Article` and `Category`; show how to display category name in Blade
4. **Middleware Q&A:** show where `auth` middleware is applied; what happens when a visitor hits `/dashboard/articles/create` directly
5. **Live coding:** add a new migration (e.g., `excerpt` column) and display it in the view
