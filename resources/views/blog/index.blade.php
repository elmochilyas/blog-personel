<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 min-h-screen">
    <!-- Navigation -->
    <nav class="border-b border-white/10 bg-white/5 backdrop-blur-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-8">
                    <a href="{{ route('blog.index') }}" class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <span class="text-white font-bold text-xl">Blog</span>
                    </a>
                    <a href="{{ route('blog.index') }}" class="text-slate-300 hover:text-white transition-colors">Home</a>
                </div>
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-slate-300 hover:text-white transition-colors">Dashboard</a>
                        <a href="{{ route('articles.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg transition-colors">
                            New Article
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-slate-400 hover:text-white transition-colors">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-slate-300 hover:text-white transition-colors">Login</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg transition-colors">
                            Register
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="py-16 px-4">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Welcome to Our Blog</h1>
            <p class="text-xl text-slate-400 max-w-2xl mx-auto">Discover the latest articles, insights, and stories from our community</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="flex flex-wrap items-center gap-4">
            <form method="GET" action="{{ route('blog.index') }}" class="flex items-center gap-2">
                <select id="category" name="category" onchange="this.form.submit()" 
                    class="bg-white/10 border border-white/10 text-white rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="" class="text-slate-900">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }} class="text-slate-900">
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <!-- Articles Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($articles as $article)
                <article class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden hover:border-indigo-500/50 transition-all group">
                    <div class="h-40 bg-gradient-to-br from-indigo-500/20 to-purple-500/20 flex items-center justify-center">
                        <svg class="w-16 h-16 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="px-2 py-1 text-xs font-medium bg-indigo-500/20 text-indigo-300 rounded-full">
                                {{ $article->category?->name ?? 'Uncategorized' }}
                            </span>
                            <span class="text-xs text-slate-500">
                                {{ $article->published_at?->format('M d, Y') }}
                            </span>
                        </div>
                        <h2 class="text-lg font-semibold text-white mb-2 group-hover:text-indigo-400 transition-colors line-clamp-2">
                            <a href="{{ route('blog.show', $article) }}">{{ $article->title }}</a>
                        </h2>
                        <p class="text-slate-400 text-sm mb-4 line-clamp-3">{{ Str::limit($article->content, 100) }}</p>
                        <a href="{{ route('blog.show', $article) }}" class="inline-flex items-center text-sm font-medium text-indigo-400 hover:text-indigo-300">
                            Read more
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </article>
            @empty
                <div class="col-span-full text-center py-16">
                    <svg class="w-16 h-16 text-white/20 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                    <p class="text-slate-500 text-lg">No articles found</p>
                </div>
            @endforelse
        </div>
    </div>
</body>
</html>