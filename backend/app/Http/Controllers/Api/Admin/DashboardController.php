<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Portfolio;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        return response()->json([
            'articles_total'     => Article::count(),
            'articles_published' => Article::where('status', 'published')->count(),
            'articles_draft'     => Article::where('status', 'draft')->count(),
            'categories_total'   => Category::count(),
            'portfolios_total'   => Portfolio::count(),
            'latest_articles'    => Article::with('category:id,name')
                ->latest()
                ->take(5)
                ->get(['id', 'category_id', 'title', 'slug', 'status', 'thumbnail', 'created_at']),
        ]);
    }
}
