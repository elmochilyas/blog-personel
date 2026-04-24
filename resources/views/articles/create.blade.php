<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Article - Blog</title>
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
                    <a href="{{ route('dashboard') }}" class="text-slate-300 hover:text-white transition-colors">Dashboard</a>
                </div>
                <div class="flex items-center gap-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-slate-400 hover:text-white transition-colors">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Form Content -->
    <div class="py-12 px-4">
        <div class="max-w-3xl mx-auto">
            <div class="mb-8">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-slate-400 hover:text-white mb-4 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Dashboard
                </a>
                <h1 class="text-2xl font-bold text-white">Create New Article</h1>
                <p class="text-slate-400 mt-1">Write and publish a new article</p>
            </div>

            <div class="bg-white/5 border border-white/10 rounded-2xl p-8">
                <form method="POST" action="{{ route('articles.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="title" class="block text-sm font-medium text-slate-300 mb-2">Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="Enter article title">
                        @error('title')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-slate-300 mb-2">Category</label>
                        <select name="category_id" id="category_id" required
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="" class="text-slate-900">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }} class="text-slate-900">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="content" class="block text-sm font-medium text-slate-300 mb-2">Content</label>
                        <textarea name="content" id="content" rows="12" required
                            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="Write your article content here...">{{ old('content') }}</textarea>
                        @error('content')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-3">Status</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative flex items-center p-4 border border-white/10 rounded-xl cursor-pointer hover:border-indigo-500/50 transition-colors has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-500/10">
                                <input type="radio" name="status" value="draft" {{ old('status') == 'draft' ? 'checked' : 'checked' }} class="sr-only">
                                <div class="flex-1">
                                    <span class="block text-sm font-medium text-white">Draft</span>
                                    <span class="block text-xs text-slate-500">Save as draft for later</span>
                                </div>
                                <svg class="w-5 h-5 text-indigo-400 opacity-0 has-[:checked]:opacity-100" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </label>
                            <label class="relative flex items-center p-4 border border-white/10 rounded-xl cursor-pointer hover:border-indigo-500/50 transition-colors has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-500/10">
                                <input type="radio" name="status" value="published" {{ old('status') == 'published' ? 'checked' : '' }} class="sr-only">
                                <div class="flex-1">
                                    <span class="block text-sm font-medium text-white">Published</span>
                                    <span class="block text-xs text-slate-500">Publish immediately</span>
                                </div>
                                <svg class="w-5 h-5 text-indigo-400 opacity-0 has-[:checked]:opacity-100" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-4">
                        <a href="{{ route('dashboard') }}" class="px-6 py-3 text-slate-400 hover:text-white transition-colors">Cancel</a>
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-xl transition-colors">
                            Create Article
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>