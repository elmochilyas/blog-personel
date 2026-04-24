<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $article->title }} - Blog</title>
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

    <!-- Article Header -->
    <div class="py-12 px-4">
        <div class="max-w-4xl mx-auto">
            <a href="{{ route('blog.index') }}" class="inline-flex items-center text-slate-400 hover:text-white mb-6 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Blog
            </a>
            
            <span class="inline-block px-3 py-1 text-sm font-medium bg-indigo-500/20 text-indigo-300 rounded-full mb-4">
                {{ $article->category?->name ?? 'Uncategorized' }}
            </span>
            
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">{{ $article->title }}</h1>
            
            <div class="flex items-center gap-4 text-slate-400">
                <span>{{ $article->published_at?->format('F d, Y') }}</span>
                <span>&bull;</span>
                <span>{{ Str::wordCount($article->content) }} words</span>
            </div>
        </div>
    </div>

    <!-- Article Content -->
    <div class="max-w-4xl mx-auto px-4 pb-16">
        <div class="bg-white/5 border border-white/10 rounded-2xl p-8 md:p-12">
            <div class="prose prose-invert max-w-none">
                <p class="text-slate-300 leading-relaxed whitespace-pre-wrap">{{ $article->content }}</p>
            </div>
        </div>

        <!-- Back Link -->
        <div class="mt-8 text-center">
            <a href="{{ route('blog.index') }}" class="inline-flex items-center text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to all articles
            </a>
        </div>
    </div>
</body>
</html>