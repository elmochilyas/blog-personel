# 🗂️ TASKS — Laravel Blog | Phase Breakdown

> Each section title = a **Jira Epic or Task title**.  
> Details below each title = implementation notes for you during coding.  
> Branch strategy: create the branch before starting each phase.

---

## ⚡ PRE-WORK (Before any code)

### TASK-00 — Project Setup & Git Init

**Jira title:** `[SETUP] Laravel installation and Git repository init`

**Steps:**
1. Install Laravel: `composer create-project laravel/laravel blog-app`
2. Create GitHub repository (public or private)
3. `git init`, add remote, push initial commit: `Initial Laravel installation`
4. Create `.env` from `.env.example`; configure DB (MySQL or SQLite)
5. Run `php artisan serve` → confirm it works at `localhost:8000`
6. Create branch `main` as your base

**Done when:** App runs locally, repo exists on GitHub with first commit.

---

## 📦 PHASE 1 — Database: Migrations & Models

> Branch: `feature/database`

---

### TASK-01 — Categories Migration & Model

**Jira title:** `[DB] Create categories migration and model`

**Steps:**
1. Run: `php artisan make:model Category -m`
2. In the migration file (`create_categories_table`), add:
   ```php
   $table->id();
   $table->string('name');
   $table->timestamps();
   ```
3. In `app/Models/Category.php`, add:
   ```php
   protected $fillable = ['name'];
   public function articles() { return $this->hasMany(Article::class); }
   ```
4. Commit: `Add categories migration and model`

**Done when:** Migration runs without error; `categories` table exists in DB.

---

### TASK-02 — Articles Migration & Model

**Jira title:** `[DB] Create articles migration and model`

**Steps:**
1. Run: `php artisan make:model Article -m`
2. In migration file (`create_articles_table`), add:
   ```php
   $table->id();
   $table->string('title');
   $table->text('content');
   $table->enum('status', ['draft', 'published'])->default('draft');
   $table->foreignId('category_id')->constrained()->onDelete('cascade');
   $table->foreignId('user_id')->constrained()->onDelete('cascade');
   $table->timestamp('published_at')->nullable();
   $table->timestamps();
   ```
3. In `app/Models/Article.php`, add:
   ```php
   protected $fillable = ['title', 'content', 'status', 'category_id', 'user_id', 'published_at'];
   public function category() { return $this->belongsTo(Category::class); }
   public function user() { return $this->belongsTo(User::class); }
   ```
4. In `app/Models/User.php`, add:
   ```php
   public function articles() { return $this->hasMany(Article::class); }
   ```
5. Commit: `Add articles migration and model with Eloquent relations`

**Done when:** `php artisan migrate` runs cleanly; all 3 tables exist.

---

### TASK-03 — DatabaseSeeder

**Jira title:** `[DB] Seed categories, blogger account, and articles`

**Steps:**
1. In `database/seeders/DatabaseSeeder.php`, write:
   - Create 4 categories: `Laravel`, `PHP`, `DevOps`, `JavaScript`
   - Create 1 user: `name`, `email`, `password` (use `bcrypt('password')`)
   - Create 6 articles: at least 2 drafts, 4 published, spread across categories
2. Run: `php artisan db:seed` to test
3. Run: `php artisan migrate:fresh --seed` → should fully reset and re-seed
4. Commit: `Add DatabaseSeeder with categories, blogger, and articles`

**Done when:** `migrate:fresh --seed` works; you can see data in the DB.

---

## 🔐 PHASE 2 — Authentication

> Branch: `feature/auth`

---

### TASK-04 — Login Routes & Controller

**Jira title:** `[AUTH] Create login and logout routes and controller`

**Steps:**
1. Run: `php artisan make:controller Auth/LoginController`
2. In `routes/web.php`, add named routes:
   ```php
   Route::get('/login', [LoginController::class, 'showForm'])->name('login');
   Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
   Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');
   ```
3. In `LoginController.php`:
   - `showForm()` → returns `auth.login` view
   - `login()` → use `Auth::attempt(['email' => $request->email, 'password' => $request->password])`
     - On success → redirect to `dashboard`
     - On failure → back with error
   - `logout()` → `Auth::logout()`, redirect to `blog.index`
4. Commit: `Add login and logout routes and controller`

**Done when:** You can navigate to `/login`, submit, and be redirected to `/dashboard`.

---

### TASK-05 — Login Blade View

**Jira title:** `[AUTH] Build login form view`

**Steps:**
1. Create `resources/views/auth/login.blade.php`
2. Extend `layouts/app.blade.php`
3. Include a form with:
   - `@csrf`
   - Email input
   - Password input
   - Submit button
   - Display `$errors` if present
4. Commit: `Add login form Blade view`

**Done when:** Login page displays; errors show on bad credentials; redirect works on success.

---

## 🌐 PHASE 3 — Public Blog

> Branch: `feature/public-blog`

---

### TASK-06 — Main Layout

**Jira title:** `[BLADE] Create main app layout with nav`

**Steps:**
1. Create `resources/views/layouts/app.blade.php`
2. Must include:
   - `<head>` with CSS (use Tailwind CDN or Bootstrap CDN — your choice)
   - `<nav>` with links to home (`blog.index`)
   - `@auth` block showing: "Dashboard" + Logout button
   - `@guest` block showing: Login link
   - `@yield('content')` in the body
3. Commit: `Add main Blade layout with auth-aware navigation`

**Done when:** Any page extending this layout shows the nav correctly for both guest and logged-in states.

---

### TASK-07 — Public Article List (US1 + US3)

**Jira title:** `[PUBLIC] Public article list with category filter`

**Steps:**
1. Run: `php artisan make:controller ArticleController`
2. Add `index()` method:
   ```php
   public function index(Request $request) {
       $query = Article::where('status', 'published')->with('category');
       if ($request->category) {
           $query->where('category_id', $request->category);
       }
       $articles = $query->latest()->get(); // or ->paginate(6) for bonus
       $categories = Category::all();
       return view('blog.index', compact('articles', 'categories'));
   }
   ```
3. Add route in `web.php`:
   ```php
   Route::get('/', [ArticleController::class, 'index'])->name('blog.index');
   ```
4. Create `resources/views/blog/index.blade.php`:
   - Show category filter buttons/links (pass `?category={id}` as query param)
   - Loop `$articles` → show title, category name, date, content excerpt (`Str::limit($article->content, 150)`)
   - Each article title links to `route('blog.show', $article->id)`
5. Commit: `Implement public article list with category filter`

**Done when:** Homepage shows published articles; clicking a category filters the list.

---

### TASK-08 — Public Article Detail (US2)

**Jira title:** `[PUBLIC] Public article detail page`

**Steps:**
1. Add `show($id)` method to `ArticleController`:
   ```php
   public function show($id) {
       $article = Article::with('category')->findOrFail($id);
       return view('blog.show', compact('article'));
   }
   ```
2. Add route:
   ```php
   Route::get('/articles/{id}', [ArticleController::class, 'show'])->name('blog.show');
   ```
3. Create `resources/views/blog/show.blade.php`:
   - Show: title, category badge, full content, published date
   - Back link to `blog.index`
4. Commit: `Add public article detail page`

**Done when:** Clicking an article on the list opens its full content.

---

## 🛠️ PHASE 4 — Dashboard & Article CRUD

> Branch: `feature/article-crud`

---

### TASK-09 — Auth Middleware Group & Dashboard Route

**Jira title:** `[DASHBOARD] Group dashboard routes under auth middleware`

**Steps:**
1. In `routes/web.php`, wrap all dashboard routes in:
   ```php
   Route::middleware('auth')->prefix('dashboard')->name('dashboard.')->group(function () {
       // all dashboard routes go here
   });
   ```
2. Create `DashboardController`: `php artisan make:controller DashboardController`
3. Add `index()` method → fetch ALL articles for logged-in user → pass to view
4. Add route inside the group: `Route::get('/', [DashboardController::class, 'index'])->name('index');`
   - Named: `dashboard.index`
5. Create `resources/views/dashboard/index.blade.php` → table showing all articles with status badge
6. Commit: `Add dashboard route and index view under auth middleware`

**Done when:** `/dashboard` redirects to login if not authenticated; shows article list if logged in.

---

### TASK-10 — Create Article (US6)

**Jira title:** `[CRUD] Implement create article feature`

**Steps:**
1. Run: `php artisan make:controller Dashboard/ArticleController`
2. Add `create()` and `store()` methods:
   ```php
   public function create() {
       $categories = Category::all();
       return view('dashboard.articles.create', compact('categories'));
   }

   public function store(Request $request) {
       $validated = $request->validate([
           'title' => 'required|string|max:255',
           'content' => 'required|string',
           'category_id' => 'required|exists:categories,id',
           'status' => 'required|in:draft,published',
       ]);
       $validated['user_id'] = auth()->id();
       if ($validated['status'] === 'published') {
           $validated['published_at'] = now();
       }
       Article::create($validated);
       return redirect()->route('dashboard.index')->with('success', 'Article created!');
   }
   ```
3. Add routes in the auth group:
   ```php
   Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create');
   Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
   ```
4. Create `resources/views/dashboard/articles/create.blade.php`:
   - Form with `@csrf`, title input, content textarea, category dropdown, status select
   - Submit button
   - Display validation errors with `@error` directives
5. Commit: `Implement create article with validation and auth protection`

**Done when:** Blogger can create articles from the dashboard; drafts/published work correctly.

---

### TASK-11 — Edit Article (US7)

**Jira title:** `[CRUD] Implement edit article feature`

**Steps:**
1. Add `edit($id)` and `update(Request $request, $id)` methods:
   ```php
   public function edit($id) {
       $article = Article::findOrFail($id);
       $categories = Category::all();
       return view('dashboard.articles.edit', compact('article', 'categories'));
   }

   public function update(Request $request, $id) {
       $article = Article::findOrFail($id);
       $validated = $request->validate([
           'title' => 'required|string|max:255',
           'content' => 'required|string',
           'category_id' => 'required|exists:categories,id',
           'status' => 'required|in:draft,published',
       ]);
       if ($validated['status'] === 'published' && $article->status === 'draft') {
           $validated['published_at'] = now();
       }
       $article->update($validated);
       return redirect()->route('dashboard.index')->with('success', 'Article updated!');
   }
   ```
2. Add routes in the auth group:
   ```php
   Route::get('/articles/{id}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
   Route::put('/articles/{id}', [ArticleController::class, 'update'])->name('articles.update');
   ```
3. Create `resources/views/dashboard/articles/edit.blade.php`:
   - Same form as create, but pre-filled with `$article` data
   - Form method: `POST` with `@method('PUT')`
4. Commit: `Implement edit article with status toggle`

**Done when:** Edit form opens pre-filled; saving updates the article; draft→published makes it appear on the blog.

---

### TASK-12 — Delete Article (US8)

**Jira title:** `[CRUD] Implement delete article with confirmation`

**Steps:**
1. Add `destroy($id)` method:
   ```php
   public function destroy($id) {
       $article = Article::findOrFail($id);
       $article->delete();
       return redirect()->route('dashboard.index')->with('success', 'Article deleted!');
   }
   ```
2. Add route:
   ```php
   Route::delete('/articles/{id}', [ArticleController::class, 'destroy'])->name('articles.destroy');
   ```
3. In dashboard index view, add a delete button per article:
   ```html
   <form action="{{ route('dashboard.articles.destroy', $article->id) }}" method="POST"
         onsubmit="return confirm('Are you sure you want to delete this article?')">
       @csrf
       @method('DELETE')
       <button type="submit">Delete</button>
   </form>
   ```
4. Commit: `Add delete article with JS confirmation`

**Done when:** Delete button shows confirm dialog; confirmed deletion removes article from both dashboard and public blog.

---

## 🎁 PHASE 5 — Bonus (Pick ONE)

> Stay on branch `feature/public-blog` or create `feature/bonus`

---

### TASK-13 (Option A) — Pagination

**Jira title:** `[BONUS] Add pagination to public article list`

**Steps:**
1. In `ArticleController@index`, replace `.get()` with `.paginate(6)`
2. In `blog/index.blade.php`, add at the bottom: `{{ $articles->links() }}`
3. If using Tailwind: run `php artisan vendor:publish --tag=laravel-pagination`
4. Commit: `Add pagination to public article list`

---

### TASK-13 (Option B) — Reading Time

**Jira title:** `[BONUS] Add estimated reading time to articles`

**Steps:**
1. In `Article.php` model, add an accessor:
   ```php
   public function getReadingTimeAttribute() {
       $wordCount = str_word_count(strip_tags($this->content));
       return max(1, ceil($wordCount / 200));
   }
   ```
2. In `blog/index.blade.php` and `blog/show.blade.php`, display:
   ```blade
   {{ $article->reading_time }} min read
   ```
3. Commit: `Add reading time estimator to articles`

---

## 🧹 PHASE 6 — Polish & Delivery

> Branch: `main` (merge all features)

---

### TASK-14 — Route Audit

**Jira title:** `[QA] Verify all named routes with artisan route:list`

**Steps:**
1. Run: `php artisan route:list`
2. Confirm every route has a name
3. Confirm all `/dashboard/*` routes show `auth` middleware
4. Fix any unnamed or unprotected routes
5. Commit: `Audit and fix all named routes`

---

### TASK-15 — Security & Validation Audit

**Jira title:** `[QA] Audit CSRF, validation, and middleware protection`

**Steps:**
1. Check every Blade form has `@csrf`
2. Check every controller POST/PUT method has `$request->validate([...])`
3. Check every model has `$fillable` defined
4. Manually test: open `/dashboard/articles/create` in incognito → should redirect to `/login`
5. Commit: `Security audit — CSRF, validation, and middleware verified`

---

### TASK-16 — README.md

**Jira title:** `[DOCS] Write README with setup instructions`

**Content to include:**
```markdown
# Blog App — Laravel

## Description
Personal blog for a freelance developer. Public visitors can browse and filter articles by category. The blogger authenticates to manage articles (create, edit, delete, draft/publish).

## Tech Stack
- Laravel 10+
- MySQL / SQLite
- Blade templates

## Installation
1. Clone the repo
2. `composer install`
3. Copy `.env.example` → `.env` and configure DB
4. `php artisan key:generate`
5. `php artisan migrate:fresh --seed`
6. `php artisan serve`
7. Login: `blogger@example.com` / `password`

## Features
- Public article list with category filter
- Article detail page
- Blogger authentication (login/logout)
- Dashboard with full CRUD
- Draft/Published status system
- Route protection via auth middleware
```
5. Commit: `Add README with project description and setup instructions`

---

### TASK-17 — Final Git Check & Merge

**Jira title:** `[GIT] Final commit count check and branch merge`

**Steps:**
1. Run: `git log --oneline | wc -l` → must be ≥ 15
2. Merge all feature branches into `main`
3. Run `php artisan migrate:fresh --seed` one final time on a clean DB
4. Do a full manual walkthrough of the demo flow:
   - Visitor: list → filter → detail
   - Blogger: login → dashboard → create → edit → delete → logout
5. Final commit: `Final review — app complete and demo-ready`

---

## 📊 Summary Table (for Jira import)

| # | Jira Title | Phase | Priority |
|---|---|---|---|
| TASK-00 | [SETUP] Laravel installation and Git repository init | Setup | 🔴 Critical |
| TASK-01 | [DB] Create categories migration and model | Phase 1 | 🔴 Critical |
| TASK-02 | [DB] Create articles migration and model | Phase 1 | 🔴 Critical |
| TASK-03 | [DB] Seed categories, blogger account, and articles | Phase 1 | 🔴 Critical |
| TASK-04 | [AUTH] Create login and logout routes and controller | Phase 2 | 🔴 Critical |
| TASK-05 | [AUTH] Build login form view | Phase 2 | 🔴 Critical |
| TASK-06 | [BLADE] Create main app layout with nav | Phase 3 | 🔴 Critical |
| TASK-07 | [PUBLIC] Public article list with category filter | Phase 3 | 🔴 Critical |
| TASK-08 | [PUBLIC] Public article detail page | Phase 3 | 🔴 Critical |
| TASK-09 | [DASHBOARD] Group dashboard routes under auth middleware | Phase 4 | 🔴 Critical |
| TASK-10 | [CRUD] Implement create article feature | Phase 4 | 🔴 Critical |
| TASK-11 | [CRUD] Implement edit article feature | Phase 4 | 🔴 Critical |
| TASK-12 | [CRUD] Implement delete article with confirmation | Phase 4 | 🔴 Critical |
| TASK-13 | [BONUS] Add pagination OR reading time (pick one) | Phase 5 | 🟡 Optional |
| TASK-14 | [QA] Verify all named routes with artisan route:list | Phase 6 | 🟠 High |
| TASK-15 | [QA] Audit CSRF, validation, and middleware protection | Phase 6 | 🟠 High |
| TASK-16 | [DOCS] Write README with setup instructions | Phase 6 | 🟠 High |
| TASK-17 | [GIT] Final commit count check and branch merge | Phase 6 | 🟠 High |
