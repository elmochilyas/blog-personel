<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public function index(Request $request) {
        $query = Article::where('status', 'published')->with('category');
        if ($request->category) {
            $query->where('category_id', $request->category);
        }
        $articles = $query->latest()->get(); 
        $categories = Category::all();
        return view('blog.index', compact('articles', 'categories'));
    }

    public function show(Article $article) {
        if ($article->status !== 'published') {
            abort(404);
        }
        return view('blog.show', compact('article'));
    }

    public function dashboard() {
        $articles = Article::where('user_id', Auth::id())->with('category')->latest()->get();
        return view('dashboard', compact('articles'));
    }

    public function create() {
        $categories = Category::all();
        return view('articles.create', compact('categories'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,published',
        ]);

        $validated['user_id'] = Auth::id();
        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        Article::create($validated);
        return redirect()->route('dashboard')->with('success', 'Article created successfully.');
    }

    public function edit(Article $article) {
        $this->authorizeArticle($article);
        $categories = Category::all();
        return view('articles.edit', compact('article', 'categories'));
    }

    public function update(Request $request, Article $article) {
        $this->authorizeArticle($article);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,published',
        ]);

        if ($validated['status'] === 'published' && $article->status !== 'published') {
            $validated['published_at'] = now();
        }

        $article->update($validated);
        return redirect()->route('dashboard')->with('success', 'Article updated successfully.');
    }

    public function destroy(Article $article) {
        $this->authorizeArticle($article);
        $article->delete();
        return redirect()->route('dashboard')->with('success', 'Article deleted successfully.');
    }

    private function authorizeArticle(Article $article) {
        if ($article->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
