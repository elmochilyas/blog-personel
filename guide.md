# 🚀 LARAVEL PERSONAL BLOG — Complete Learning Guide
> **For:** Ilyas (new to Laravel)  
> **Duration:** 5 days (Mon 20/04 → Fri 24/04/2026)  
> **Level:** Beginner → Intermediate

---

## 📚 Table of Contents
1. [Laravel Fundamentals (Concepts You Need)](#laravel-fundamentals)
2. [Project Architecture Overview](#project-architecture-overview)
3. [Day-by-Day Roadmap](#day-by-day-roadmap)
4. [Feature Implementation Guide](#feature-implementation-guide)
5. [Common Pitfalls & Solutions](#common-pitfalls--solutions)
6. [Quick Reference Commands](#quick-reference-commands)

---

## 🎓 Laravel Fundamentals

### What is Laravel?
Laravel is a **PHP web framework** that makes building modern web apps easier by:
- Handling routing (connecting URLs to code)
- Managing databases through Eloquent ORM
- Templating with Blade
- Built-in authentication & validation
- Migration system for database versioning

Think of it as a **structured toolkit** — instead of writing raw PHP, Laravel provides organized patterns (MVC).

---

### 1️⃣ MVC Architecture (You Already Know This!)

You've done MVC in school projects. Laravel follows the same pattern:

```
REQUEST
   ↓
ROUTE (routes/web.php) — "match URL to action"
   ↓
CONTROLLER (app/Http/Controllers/) — "business logic"
   ↓
MODEL (app/Models/) — "database queries via Eloquent"
   ↓
DATABASE (migrations + seeders)
   ↓
VIEW (resources/views/) — "render HTML with Blade"
   ↓
RESPONSE (sent to browser)
```

**Example Flow:**
```
GET /articles/3
   ↓
routes/web.php: Route::get('/articles/{id}', [ArticleController::class, 'show'])->name('blog.show');
   ↓
ArticleController::show($id) — finds article in database
   ↓
Article model: Article::findOrFail($id) — Eloquent query
   ↓
database: SELECT * FROM articles WHERE id = 3
   ↓
resources/views/blog/show.blade.php — renders HTML with article data
   ↓
Browser displays article
```

---

### 2️⃣ Routing (URL → Controller Mapping)

**File:** `routes/web.php`

```php
// Basic route
Route::get('/articles', [ArticleController::class, 'index'])->name('blog.index');

// Route with parameter
Route::get('/articles/{id}', [ArticleController::class, 'show'])->name('blog.show');

// Route group (all routes inside auth middleware)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/articles', [ArticleController::class, 'store'])->name('articles.store');
});

// Query parameters (no route parameter needed)
// URL: /articles?category=1
// Access in controller: $request->query('category')
```

**Key Concepts:**
- **Named routes** (`->name('blog.index')`) — use in views with `route('blog.index')`
- **Route groups** — share middleware for multiple routes
- **Route parameters** — `{id}` captured in controller method: `show($id)`

---

### 3️⃣ Controllers (Business Logic)

**File:** `app/Http/Controllers/`

Controller = class with methods (actions). Each method handles ONE request.

```php
<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    // Public blog — list all PUBLISHED articles
    public function index()
    {
        $articles = Article::where('status', 'published')->get();
        return view('blog.index', ['articles' => $articles]);
    }

    // Public blog — show ONE article
    public function show($id)
    {
        $article = Article::findOrFail($id); // 404 if not found
        return view('blog.show', ['article' => $article]);
    }

    // Dashboard — create form
    public function create()
    {
        $categories = Category::all();
        return view('dashboard.articles.create', ['categories' => $categories]);
    }

    // Dashboard — save article to database
    public function store(Request $request)
    {
        // Validate incoming data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,published',
        ]);

        // Add current user
        $validated['user_id'] = auth()->id();

        // Create & save
        Article::create($validated);

        return redirect()->route('dashboard')->with('success', 'Article created!');
    }
}
```

**Key Concepts:**
- Methods return either `view()` or `redirect()`
- `$request` object contains all form data
- `auth()` helper — current logged-in user
- `redirect()->route()` — go to named route with optional success message

---

### 4️⃣ Models & Eloquent (Database Queries)

**File:** `app/Models/Article.php`

Eloquent = Laravel's ORM (Object-Relational Mapper). Instead of raw SQL, write PHP objects.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    // Mass assignment — which columns can be filled via create()/update()
    protected $fillable = ['title', 'content', 'status', 'category_id', 'user_id', 'published_at'];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

**Using Eloquent in Controller:**

```php
// Get all articles
$articles = Article::all();

// Get one by ID
$article = Article::find(3);
$article = Article::findOrFail(3); // 404 if not found

// Filter by condition
$published = Article::where('status', 'published')->get();
$byCategory = Article::where('category_id', 1)->get();

// Chain conditions
$articles = Article::where('status', 'published')
                    ->where('category_id', 1)
                    ->get();

// Get one that matches condition
$article = Article::where('id', 3)->first();

// Count
$count = Article::where('status', 'published')->count();

// Create new
Article::create([
    'title' => 'My Article',
    'content' => '...',
    'category_id' => 1,
    'user_id' => 1,
    'status' => 'published',
]);

// Update
$article = Article::find(3);
$article->update(['status' => 'published']);

// Delete
$article->delete();
Article::destroy(3); // Delete by ID
```

**Relationships (Key for this project):**

```php
// Article belongs to Category
$article = Article::find(1);
$category = $article->category; // Access related category
echo $category->name; // Output category name

// Category has many Articles
$category = Category::find(1);
$articles = $category->articles; // Get all articles in category

// Filter by category
$articles = Article::where('category_id', $categoryId)->get();

// Using relationship for filtering
$articles = Article::whereHas('category', function ($q) use ($categoryId) {
    $q->where('id', $categoryId);
})->where('status', 'published')->get();
```

---

### 5️⃣ Blade Templating (Views)

**File:** `resources/views/`

Blade = Laravel's templating engine. Mix HTML with PHP logic in a clean way.

```blade
<!-- Conditions -->
@if ($user->email === 'admin@blog.com')
    <p>Welcome, admin!</p>
@else
    <p>Welcome, visitor!</p>
@endif

<!-- Loops -->
@foreach ($articles as $article)
    <div class="article-card">
        <h2>{{ $article->title }}</h2>
        <p>{{ $article->content }}</p>
        <span>{{ $article->category->name }}</span>
    </div>
@endforeach

<!-- Auth checks -->
@auth
    <p>You are logged in as {{ auth()->user()->name }}</p>
@endauth

@guest
    <p>You are not logged in. <a href="{{ route('login') }}">Login here</a></p>
@endguest

<!-- Variables (escapes HTML) -->
{{ $article->title }}

<!-- Raw output (no escaping) -->
{!! $article->content !!}

<!-- Links with named routes -->
<a href="{{ route('blog.show', $article->id) }}">Read more</a>
<a href="{{ route('articles.edit', $article->id) }}">Edit</a>

<!-- Forms (always include CSRF) -->
<form action="{{ route('articles.store') }}" method="POST">
    @csrf
    <input type="text" name="title" value="{{ old('title') }}">
    @error('title')
        <span style="color: red;">{{ $message }}</span>
    @enderror
    <button type="submit">Save</button>
</form>

<!-- Layout inheritance -->
@extends('layouts.app')

@section('content')
    <!-- Your content here -->
@endsection
```

**Key Concepts:**
- `{{ }}` = output escaped text
- `{!! !!}` = output raw HTML
- `@auth / @endauth` = block only for logged-in users
- `@guest / @endguest` = block only for visitors
- `@csrf` = security token (required on every form)
- `{{ old('field') }}` = repopulate form on validation error
- `{{ route('name') }}` = generate URL from named route
- `@extends() / @section()` = template inheritance

---

### 6️⃣ Migrations (Database Versioning)

**File:** `database/migrations/`

Migrations = version control for your database. Instead of writing SQL, define tables in PHP.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id(); // bigIncrements, PK
            $table->string('title');
            $table->text('content');
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('published_at')->nullable();
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
```

**Key Concepts:**
- `$table->id()` = bigIncrements (primary key)
- `$table->string('name')` = VARCHAR(255)
- `$table->text('content')` = LONGTEXT
- `$table->enum('status', [...])` = ENUM
- `$table->foreignId('category_id')->constrained()` = FK + foreign key constraint
- `$table->timestamp('published_at')->nullable()` = allows NULL
- `$table->timestamps()` = created_at + updated_at (automatic)
- `->onDelete('cascade')` = delete article if category deleted

**Running Migrations:**
```bash
php artisan migrate                  # Run pending migrations
php artisan migrate:fresh --seed     # Drop all tables & re-run (WITH seeder)
php artisan migrate:rollback         # Undo last migration batch
```

---

### 7️⃣ Seeders (Test Data)

**File:** `database/seeders/DatabaseSeeder.php`

Seeders = populate database with test data (categories, users, articles).

```php
<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create 4 categories
        $laravel = Category::create(['name' => 'Laravel']);
        $php = Category::create(['name' => 'PHP']);
        $devops = Category::create(['name' => 'DevOps']);
        $javascript = Category::create(['name' => 'JavaScript']);

        // Create 1 blogger account
        $user = User::create([
            'name' => 'Ahmed',
            'email' => 'ahmed@blog.com',
            'password' => Hash::make('password'), // Password: "password"
        ]);

        // Create 6 articles (mix of drafts + published)
        Article::create([
            'title' => 'Getting Started with Laravel',
            'content' => 'Lorem ipsum dolor sit amet...',
            'status' => 'published',
            'category_id' => $laravel->id,
            'user_id' => $user->id,
        ]);

        Article::create([
            'title' => 'Advanced Eloquent Queries',
            'content' => 'Lorem ipsum dolor sit amet...',
            'status' => 'draft',
            'category_id' => $laravel->id,
            'user_id' => $user->id,
        ]);

        // ... more articles
    }
}
```

**Running Seeder:**
```bash
php artisan migrate:fresh --seed  # Run migrations + seeders
php artisan db:seed               # Run seeders only
```

---

### 8️⃣ Authentication & Middleware

**Middleware** = filters/guards for routes. Auth middleware protects private routes.

```php
// routes/web.php

// Public routes (no middleware)
Route::get('/', [ArticleController::class, 'index'])->name('blog.index');

// Protected routes (require login)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
});
```

**Laravel's Auth Helper:**

```php
// In controller or view
auth()->user();        // Current logged-in user (User object)
auth()->id();          // Current user's ID
auth()->check();       // Is user logged in? (boolean)
auth()->logout();      // Log out

// In Blade view
@auth
    {{ auth()->user()->name }}
@endauth

@guest
    <a href="{{ route('login') }}">Login</a>
@endguest
```

**Checking Ownership (for edit/delete):**

```php
// Only let user edit their OWN article
public function edit($id)
{
    $article = Article::findOrFail($id);
    
    // Check if current user owns this article
    if ($article->user_id !== auth()->id()) {
        abort(403); // Forbidden
    }

    return view('dashboard.articles.edit', ['article' => $article]);
}
```

---

### 9️⃣ Validation (Form Data Safety)

**Validate on EVERY form submission:**

```php
public function store(Request $request)
{
    // Validate and get data
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string|min:10',
        'category_id' => 'required|exists:categories,id', // Must be valid category
        'status' => 'required|in:draft,published',
    ]);

    // If validation fails, Laravel automatically redirects back with $errors

    // If validation passes, create article
    Article::create($validated);
}
```

**Common Validation Rules:**
- `required` — field must be present
- `string` — must be text
- `max:255` — max 255 characters
- `min:10` — min 10 characters
- `email` — must be valid email
- `unique:users,email` — must not exist in table
- `exists:categories,id` — must exist in table
- `in:draft,published` — must be one of these values
- `confirmed` — must match `{field}_confirmation` field

**In Blade — Show Validation Errors:**

```blade
<form action="{{ route('articles.store') }}" method="POST">
    @csrf
    
    <input type="text" name="title" value="{{ old('title') }}">
    @error('title')
        <span style="color: red;">{{ $message }}</span>
    @enderror
    
    <button type="submit">Save</button>
</form>
```

---

### 🔟 CSRF Protection (Security)

CSRF = Cross-Site Request Forgery. Always include `@csrf` in forms to prevent attacks.

```blade
<form action="{{ route('articles.store') }}" method="POST">
    @csrf  <!-- Required! Laravel checks this token -->
    <input type="text" name="title">
    <button>Save</button>
</form>
```

---

## 🏗️ Project Architecture Overview

### Your Project Structure
```
laravel-blog/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── ArticleController.php        (public blog)
│   │       ├── Auth/LoginController.php     (login/logout)
│   │       ├── Dashboard/
│   │       │   ├── DashboardController.php  (dashboard index)
│   │       │   └── ArticleController.php    (CRUD articles)
│   │
│   ├── Models/
│   │   ├── User.php
│   │   ├── Article.php
│   │   └── Category.php
│
├── database/
│   ├── migrations/
│   │   ├── xxx_create_users_table.php
│   │   ├── xxx_create_categories_table.php
│   │   └── xxx_create_articles_table.php
│   │
│   └── seeders/
│       └── DatabaseSeeder.php
│
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php           (main layout)
│       ├── blog/
│       │   ├── index.blade.php         (public list)
│       │   └── show.blade.php          (article detail)
│       ├── dashboard/
│       │   ├── index.blade.php         (dashboard)
│       │   └── articles/
│       │       ├── create.blade.php
│       │       └── edit.blade.php
│       └── auth/
│           └── login.blade.php
│
└── routes/
    └── web.php
```

### Data Flow Example: "Edit Article"

```
User clicks "Edit Article #3"
    ↓
GET /dashboard/articles/3/edit
    ↓
routes/web.php matches → Dashboard/ArticleController@edit(3)
    ↓
ArticleController checks: auth()->id() === article->user_id
    ↓
If YES: return view with pre-filled form
    If NO: abort(403)
    ↓
Form submitted: PATCH /dashboard/articles/3
    ↓
ArticleController@update($id, $request)
    ↓
validate() → Article::findOrFail($id)->update(...)
    ↓
redirect() with success message
    ↓
Browser goes to dashboard
```

---

## 📅 Day-by-Day Roadmap

### **DAY 1 (Monday) — Setup + Database + Models**
**Goal:** Database & models ready; nothing yet in browser

#### 1.1 Create Laravel Project
```bash
composer create-project laravel/laravel laravel-blog
cd laravel-blog
```

#### 1.2 Create Migrations
```bash
php artisan make:migration create_categories_table
php artisan make:migration create_users_table
php artisan make:migration create_articles_table
```

**In migrations — define tables as shown in "Migrations" section above**

#### 1.3 Create Models
```bash
php artisan make:model Category
php artisan make:model Article
# User model exists by default
```

**In models — define relationships as shown in "Relationships" section**

```php
// app/Models/Article.php
public function category() {
    return $this->belongsTo(Category::class);
}

public function user() {
    return $this->belongsTo(User::class);
}

// app/Models/Category.php
public function articles() {
    return $this->hasMany(Article::class);
}

// app/Models/User.php (add this)
public function articles() {
    return $this->hasMany(Article::class);
}
```

#### 1.4 Create Seeder
```bash
php artisan make:seeder DatabaseSeeder
```

**In seeder — create 4 categories, 1 user, 6 articles**

#### 1.5 Run Migrations + Seeders
```bash
php artisan migrate:fresh --seed
```

**Check database:** should have categories, users, articles

#### 1.6 Test in Tinker (PHP CLI for Laravel)
```bash
php artisan tinker
>>> Article::all(); // Should show 6 articles
>>> Article::first()->category->name; // Should show category name
>>> exit
```

**Commits:**
- `Add Category, Article, User migrations`
- `Define Eloquent relationships on models`
- `Create DatabaseSeeder with test data`

---

### **DAY 2 (Tuesday) — Public Blog Routes & Views**
**Goal:** Visitors can view articles without logging in

#### 2.1 Create Routes
```php
// routes/web.php
use App\Http\Controllers\ArticleController;

Route::get('/', [ArticleController::class, 'index'])->name('blog.index');
Route::get('/articles/{id}', [ArticleController::class, 'show'])->name('blog.show');
```

#### 2.2 Create ArticleController (Public)
```bash
php artisan make:controller ArticleController
```

```php
// app/Http/Controllers/ArticleController.php
public function index(Request $request)
{
    $query = Article::where('status', 'published');

    // Filter by category if provided
    if ($request->has('category')) {
        $query->where('category_id', $request->query('category'));
    }

    $articles = $query->get();
    $categories = Category::all();

    return view('blog.index', [
        'articles' => $articles,
        'categories' => $categories,
        'selectedCategory' => $request->query('category'),
    ]);
}

public function show($id)
{
    $article = Article::findOrFail($id);
    return view('blog.show', ['article' => $article]);
}
```

#### 2.3 Create Views

**Main Layout:**
```blade
<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>My Blog</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        nav { background: #333; color: white; padding: 10px; margin-bottom: 20px; }
        nav a { color: white; margin: 0 15px; text-decoration: none; }
        .article-card { border: 1px solid #ccc; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .btn { background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 3px; }
    </style>
</head>
<body>
    <nav>
        <a href="{{ route('blog.index') }}">Blog</a>
        @auth
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" style="background: none; color: white; border: none; cursor: pointer;">Logout</button>
            </form>
        @endauth
        @guest
            <a href="{{ route('login') }}">Login</a>
        @endguest
    </nav>

    @yield('content')
</body>
</html>
```

**Public Article List:**
```blade
<!-- resources/views/blog/index.blade.php -->
@extends('layouts.app')

@section('content')
    <h1>Blog</h1>

    <!-- Category Filter -->
    <div style="margin-bottom: 20px;">
        <a href="{{ route('blog.index') }}" class="btn">All Articles</a>
        @foreach ($categories as $category)
            <a href="{{ route('blog.index', ['category' => $category->id]) }}" 
               class="btn"
               style="background: {{ $selectedCategory == $category->id ? '#28a745' : '#007bff' }};">
                {{ $category->name }}
            </a>
        @endforeach
    </div>

    <!-- Articles -->
    @forelse ($articles as $article)
        <div class="article-card">
            <h2><a href="{{ route('blog.show', $article->id) }}">{{ $article->title }}</a></h2>
            <p><small>{{ $article->category->name }} — {{ $article->created_at->format('M d, Y') }}</small></p>
            <p>{{ Str::limit($article->content, 150) }}</p>
            <a href="{{ route('blog.show', $article->id) }}">Read more →</a>
        </div>
    @empty
        <p>No articles found.</p>
    @endforelse
@endsection
```

**Article Detail:**
```blade
<!-- resources/views/blog/show.blade.php -->
@extends('layouts.app')

@section('content')
    <h1>{{ $article->title }}</h1>
    <p><small>{{ $article->category->name }} — {{ $article->created_at->format('M d, Y') }}</small></p>
    <div>{!! $article->content !!}</div>
    <a href="{{ route('blog.index') }}">← Back to blog</a>
@endsection
```

#### 2.4 Test
```bash
php artisan serve  # Start local server on localhost:8000
# Visit: http://localhost:8000
# Should see articles, click to view detail, filter by category
```

**Commits:**
- `Add public blog routes`
- `Create ArticleController with index and show methods`
- `Create main layout and blog views`
- `Implement category filtering on public blog`

---

### **DAY 3 (Wednesday) — Authentication**
**Goal:** Login/Logout working

#### 3.1 Create Login Controller
```bash
php artisan make:controller Auth/LoginController
```

```php
// app/Http/Controllers/Auth/LoginController.php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Show login form
    public function show()
    {
        return view('auth.login');
    }

    // Process login
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Try to authenticate
        if (Auth::attempt($credentials)) {
            return redirect()->route('dashboard');
        }

        // Failed — return with error
        return back()->withErrors([
            'email' => 'Invalid credentials',
        ]);
    }

    // Logout
    public function destroy()
    {
        Auth::logout();
        return redirect()->route('blog.index');
    }
}
```

#### 3.2 Create Routes
```php
// routes/web.php (add to existing)
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
```

#### 3.3 Create Login View
```blade
<!-- resources/views/auth/login.blade.php -->
@extends('layouts.app')

@section('content')
    <div style="max-width: 400px; margin: 50px auto;">
        <h1>Login</h1>

        @if ($errors->any())
            <div style="background: #ffcccc; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
                @foreach ($errors->all() as $error)
                    <p style="margin: 5px 0;">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf

            <div style="margin-bottom: 15px;">
                <label>Email:</label>
                <input type="email" name="email" value="{{ old('email') }}" required style="width: 100%; padding: 8px;">
                @error('email')
                    <span style="color: red;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 15px;">
                <label>Password:</label>
                <input type="password" name="password" required style="width: 100%; padding: 8px;">
                @error('password')
                    <span style="color: red;">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn" style="width: 100%;">Login</button>
        </form>

        <p style="margin-top: 15px; text-align: center;">
            <small>Test account — Email: <code>ahmed@blog.com</code> | Password: <code>password</code></small>
        </p>
    </div>
@endsection
```

#### 3.4 Update Layout with Auth
Already done above — check `layouts/app.blade.php` has `@auth` / `@guest` blocks

#### 3.5 Test
```bash
# Login page: http://localhost:8000/login
# Credentials: ahmed@blog.com / password
# Should redirect to dashboard (which doesn't exist yet)
```

**Commits:**
- `Create LoginController with authentication logic`
- `Add auth routes (login, logout)`
- `Create login view`

---

### **DAY 4 (Thursday) — Dashboard & CRUD**
**Goal:** Blogger can create, edit, delete articles

#### 4.1 Create Dashboard Routes
```php
// routes/web.php (add)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Article CRUD
    Route::get('/dashboard/articles/create', [ArticleController::class, 'create'])->name('articles.create');
    Route::post('/dashboard/articles', [ArticleController::class, 'store'])->name('articles.store');
    Route::get('/dashboard/articles/{id}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
    Route::put('/dashboard/articles/{id}', [ArticleController::class, 'update'])->name('articles.update');
    Route::delete('/dashboard/articles/{id}', [ArticleController::class, 'destroy'])->name('articles.destroy');
});
```

#### 4.2 Create Dashboard Controller
```bash
php artisan make:controller DashboardController
```

```php
// app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use App\Models\Article;

class DashboardController extends Controller
{
    public function index()
    {
        $articles = Article::where('user_id', auth()->id())->get();
        return view('dashboard.index', ['articles' => $articles]);
    }
}
```

#### 4.3 Update ArticleController with CRUD
Add to your existing `ArticleController`:

```php
use App\Models\Category;

// Show create form
public function create()
{
    $categories = Category::all();
    return view('dashboard.articles.create', ['categories' => $categories]);
}

// Store article
public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'category_id' => 'required|exists:categories,id',
        'status' => 'required|in:draft,published',
    ]);

    Article::create([
        ...$validated,
        'user_id' => auth()->id(),
    ]);

    return redirect()->route('dashboard')->with('success', 'Article created!');
}

// Show edit form
public function edit($id)
{
    $article = Article::findOrFail($id);
    
    // Only owner can edit
    if ($article->user_id !== auth()->id()) {
        abort(403);
    }

    $categories = Category::all();
    return view('dashboard.articles.edit', [
        'article' => $article,
        'categories' => $categories,
    ]);
}

// Update article
public function update($id, Request $request)
{
    $article = Article::findOrFail($id);

    if ($article->user_id !== auth()->id()) {
        abort(403);
    }

    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'category_id' => 'required|exists:categories,id',
        'status' => 'required|in:draft,published',
    ]);

    $article->update($validated);

    return redirect()->route('dashboard')->with('success', 'Article updated!');
}

// Delete article
public function destroy($id)
{
    $article = Article::findOrFail($id);

    if ($article->user_id !== auth()->id()) {
        abort(403);
    }

    $article->delete();

    return redirect()->route('dashboard')->with('success', 'Article deleted!');
}
```

#### 4.4 Create Dashboard Views

**Dashboard Index:**
```blade
<!-- resources/views/dashboard/index.blade.php -->
@extends('layouts.app')

@section('content')
    <h1>My Articles</h1>
    
    @if (session('success'))
        <div style="background: #d4edda; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('articles.create') }}" class="btn" style="margin-bottom: 20px;">+ New Article</a>

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f5f5f5;">
                <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Title</th>
                <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Category</th>
                <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Status</th>
                <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($articles as $article)
                <tr style="border: 1px solid #ddd;">
                    <td style="padding: 10px;">{{ $article->title }}</td>
                    <td style="padding: 10px;">{{ $article->category->name }}</td>
                    <td style="padding: 10px;">
                        <span style="background: {{ $article->status === 'published' ? '#d4edda' : '#fff3cd' }}; padding: 5px 10px; border-radius: 3px;">
                            {{ ucfirst($article->status) }}
                        </span>
                    </td>
                    <td style="padding: 10px;">
                        <a href="{{ route('articles.edit', $article->id) }}" class="btn" style="background: #007bff;">Edit</a>
                        <form action="{{ route('articles.destroy', $article->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn" style="background: #dc3545;" onclick="return confirm('Delete this article?');">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="padding: 20px; text-align: center;">No articles yet. <a href="{{ route('articles.create') }}">Create one</a></td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
```

**Create Article Form:**
```blade
<!-- resources/views/dashboard/articles/create.blade.php -->
@extends('layouts.app')

@section('content')
    <div style="max-width: 600px;">
        <h1>Create Article</h1>

        @if ($errors->any())
            <div style="background: #ffcccc; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
                <strong>Errors:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('articles.store') }}" method="POST">
            @csrf

            <div style="margin-bottom: 15px;">
                <label><strong>Title</strong></label>
                <input type="text" name="title" value="{{ old('title') }}" required style="width: 100%; padding: 8px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label><strong>Category</strong></label>
                <select name="category_id" required style="width: 100%; padding: 8px;">
                    <option value="">Select Category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label><strong>Content</strong></label>
                <textarea name="content" required style="width: 100%; padding: 8px; height: 200px;">{{ old('content') }}</textarea>
            </div>

            <div style="margin-bottom: 15px;">
                <label><strong>Status</strong></label>
                <select name="status" required style="width: 100%; padding: 8px;">
                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                </select>
            </div>

            <button type="submit" class="btn">Create Article</button>
            <a href="{{ route('dashboard') }}" style="margin-left: 10px;">Cancel</a>
        </form>
    </div>
@endsection
```

**Edit Article Form:**
```blade
<!-- resources/views/dashboard/articles/edit.blade.php -->
@extends('layouts.app')

@section('content')
    <div style="max-width: 600px;">
        <h1>Edit Article</h1>

        <form action="{{ route('articles.update', $article->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 15px;">
                <label><strong>Title</strong></label>
                <input type="text" name="title" value="{{ old('title', $article->title) }}" required style="width: 100%; padding: 8px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label><strong>Category</strong></label>
                <select name="category_id" required style="width: 100%; padding: 8px;">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $article->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label><strong>Content</strong></label>
                <textarea name="content" required style="width: 100%; padding: 8px; height: 200px;">{{ old('content', $article->content) }}</textarea>
            </div>

            <div style="margin-bottom: 15px;">
                <label><strong>Status</strong></label>
                <select name="status" required style="width: 100%; padding: 8px;">
                    <option value="draft" {{ old('status', $article->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status', $article->status) === 'published' ? 'selected' : '' }}>Published</option>
                </select>
            </div>

            <button type="submit" class="btn">Update Article</button>
            <a href="{{ route('dashboard') }}" style="margin-left: 10px;">Cancel</a>
        </form>
    </div>
@endsection
```

#### 4.5 Test
```bash
# Login
# Go to dashboard: http://localhost:8000/dashboard
# Create article → should appear
# Edit → should update
# Delete → should remove
# Check that drafts don't appear on public blog
# Check that published articles do appear
```

**Commits:**
- `Create DashboardController`
- `Add article CRUD routes and actions`
- `Create dashboard and article management views`

---

### **DAY 5 (Friday) — Polish + Bonus + Git**
**Goal:** Finish everything, add bonus feature, clean up commits

#### 5.1 Choose ONE Bonus Feature

**Option 1: Pagination**
```php
// In ArticleController@index
public function index(Request $request)
{
    $query = Article::where('status', 'published');

    if ($request->has('category')) {
        $query->where('category_id', $request->query('category'));
    }

    $articles = $query->paginate(6); // Returns paginated collection
    $categories = Category::all();

    return view('blog.index', [
        'articles' => $articles,
        'categories' => $categories,
    ]);
}
```

```blade
<!-- In blog/index.blade.php, after articles loop -->
{{ $articles->links() }}
```

**Option 2: Search**
```php
public function index(Request $request)
{
    $query = Article::where('status', 'published');

    if ($request->has('search')) {
        $query->where('title', 'like', '%' . $request->query('search') . '%');
    }

    if ($request->has('category')) {
        $query->where('category_id', $request->query('category'));
    }

    $articles = $query->get();
    $categories = Category::all();

    return view('blog.index', [
        'articles' => $articles,
        'categories' => $categories,
    ]);
}
```

```blade
<form action="{{ route('blog.index') }}" method="GET" style="margin-bottom: 20px;">
    <input type="text" name="search" placeholder="Search articles..." value="{{ request()->query('search') }}">
    <button type="submit">Search</button>
</form>
```

**Option 3: Reading Time**
```php
// In Article model
public function getReadingTimeAttribute()
{
    $wordCount = str_word_count(strip_tags($this->content));
    $minutesRead = ceil($wordCount / 200);
    return $minutesRead;
}
```

```blade
<!-- In blog views -->
{{ $article->reading_time }} min read
```

#### 5.2 Fix Any Issues
- Test all flows
- Check validation messages
- Verify ownership checks
- Test draft/published logic

#### 5.3 Git Commits
Ensure you have **at least 15 commits** across 3 branches:

```bash
git checkout -b feature/auth
# (commit auth work)

git checkout -b feature/public-blog
# (commit blog work)

git checkout -b feature/article-crud
# (commit dashboard & CRUD work)

git checkout main
git merge feature/auth
git merge feature/public-blog
git merge feature/article-crud
```

**Example commits:**
```
1. Initial project setup with migrations
2. Add Category, Article, User models
3. Create DatabaseSeeder with test data
4. Add public blog routes (index, show)
5. Create ArticleController for public views
6. Create blog index and detail views
7. Implement category filtering
8. Create auth routes and LoginController
9. Add login view and form
10. Implement authentication logic
11. Create DashboardController
12. Add article create action and view
13. Add article edit action and view
14. Add article delete action with confirmation
15. Create dashboard view with article table
16. Add pagination to article list
```

---

## 🐛 Common Pitfalls & Solutions

### Pitfall 1: Forgetting `@csrf` on Forms
**Problem:** Form submission fails with 419 error
**Solution:** Always add `@csrf` inside every form

### Pitfall 2: Models Not Using `$fillable`
**Problem:** `Article::create([...])` doesn't work; data ignored
**Solution:** Define `protected $fillable = [...]` on every model

### Pitfall 3: Forgetting to Define Relationships
**Problem:** Calling `$article->category` throws error
**Solution:** Define `belongsTo()` / `hasMany()` on models

### Pitfall 4: Returning Wrong View
**Problem:** View not found error
**Solution:** Check path matches exactly — `view('blog.index')` looks for `resources/views/blog/index.blade.php`

### Pitfall 5: Forgetting `@method('DELETE')` on Delete Form
**Problem:** DELETE route not triggered
**Solution:** Add `@method('DELETE')` hidden input for non-POST requests

### Pitfall 6: Not Checking Ownership Before Edit/Delete
**Problem:** User can edit/delete other users' articles
**Solution:** Always check `$article->user_id === auth()->id()` before modifying

### Pitfall 7: Allowing Drafts to Show on Public Blog
**Problem:** Drafts visible to everyone
**Solution:** Always filter with `->where('status', 'published')` on public routes

---

## ⚡ Quick Reference Commands

```bash
# Start development server
php artisan serve

# Run migrations
php artisan migrate
php artisan migrate:fresh --seed

# Create files
php artisan make:controller ControllerName
php artisan make:model ModelName
php artisan make:migration create_table_name
php artisan make:seeder SeederName

# Database
php artisan tinker                    # PHP REPL
php artisan db:seed DatabaseSeeder    # Run seeders only

# Git
git init
git add .
git commit -m "message"
git branch feature/name
git checkout feature/name
git merge feature/name
```

---

## 📝 Final Checklist

Before submitting:

- [ ] 15+ commits across 3 feature branches
- [ ] All routes use `->name()`
- [ ] All forms have `@csrf`
- [ ] All models have `$fillable`
- [ ] All relationships defined
- [ ] Drafts don't appear on public blog
- [ ] Ownership checks (can't edit/delete others' articles)
- [ ] Validation on every form
- [ ] Success/error messages
- [ ] Database seeded with test data
- [ ] Test login: ahmed@blog.com / password
- [ ] Test create, edit, delete articles
- [ ] Test category filter
- [ ] Test logout

Good luck! 🚀