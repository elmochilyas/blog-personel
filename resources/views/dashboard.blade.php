<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Blog</title>
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
                    <a href="{{ route('dashboard') }}" class="text-white font-medium transition-colors">Dashboard</a>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('articles.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg transition-colors">
                        New Article
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-slate-400 hover:text-white transition-colors">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="py-12 px-4">
        <div class="max-w-7xl mx-auto">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-500/20 border border-green-500/30 text-green-300 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
                <div class="p-6 border-b border-white/10">
                    <h2 class="text-xl font-bold text-white">My Articles</h2>
                    <p class="text-slate-400 text-sm mt-1">Manage all your articles (drafts and published)</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-white/5">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-slate-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse($articles as $article)
                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-white font-medium">{{ $article->title }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-slate-400">{{ $article->category?->name ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($article->status === 'published')
                                            <span class="px-2.5 py-1 text-xs font-medium bg-green-500/20 text-green-400 rounded-full">
                                                Published
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-medium bg-yellow-500/20 text-yellow-400 rounded-full">
                                                Draft
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-400">
                                        {{ $article->published_at?->format('M d, Y') ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-3">
                                            <a href="{{ route('articles.edit', $article) }}" class="text-slate-400 hover:text-indigo-400 transition-colors" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <form method="POST" action="{{ route('articles.destroy', $article) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-slate-400 hover:text-red-400 transition-colors" title="Delete" onclick="return confirm('Are you sure you want to delete this article?')">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <svg class="w-12 h-12 text-white/20 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="text-slate-500 mb-4">No articles yet. Create your first article!</p>
                                        <a href="{{ route('articles.create') }}" class="inline-flex px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-lg transition-colors">
                                            Create Article
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>