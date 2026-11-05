<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->query('per_page', 9), 1), 24);

        $articles = Article::published()
            ->with('category:id,name,slug')
            ->select(['id', 'category_id', 'title', 'slug', 'excerpt', 'thumbnail', 'published_at'])
            ->when($request->query('category'), function ($query, $slug) {
                $query->whereHas('category', fn ($c) => $c->where('slug', $slug));
            })
            ->orderByDesc('published_at')
            ->paginate($perPage);

        return response()->json($articles);
    }

    public function show(string $slug): JsonResponse
    {
        $article = Article::published()
            ->with(['category:id,name,slug', 'author:id,name'])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($article);
    }
}
