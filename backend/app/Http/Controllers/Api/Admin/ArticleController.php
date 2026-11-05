<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $articles = Article::with('category:id,name,slug')
            ->select(['id', 'category_id', 'title', 'slug', 'excerpt', 'thumbnail', 'status', 'published_at', 'created_at'])
            ->when($request->query('status'), function ($query, $status) {
                if (in_array($status, ['draft', 'published'], true)) {
                    $query->where('status', $status);
                }
            })
            ->when($request->query('q'), fn ($query, $q) => $query->where('title', 'like', '%'.$q.'%'))
            ->latest()
            ->paginate(10);

        return response()->json($articles);
    }

    public function store(StoreArticleRequest $request): JsonResponse
    {
        $data = $request->validated();
        unset($data['thumbnail']);

        $data['user_id'] = $request->user()->id;
        $data['slug'] = $data['slug'] ?? $this->generateSlug($data['title']);

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('articles', 'public');
        }

        $article = Article::create($data);

        return response()->json($article->load('category:id,name,slug'), 201);
    }

    public function show(Article $article): JsonResponse
    {
        return response()->json($article->load('category:id,name,slug'));
    }

    public function update(UpdateArticleRequest $request, Article $article): JsonResponse
    {
        $data = $request->validated();
        unset($data['thumbnail'], $data['remove_thumbnail']);

        if (empty($data['slug'])) {
            unset($data['slug']);
        }

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = $article->published_at ?? now();
        }

        if ($request->hasFile('thumbnail')) {
            $this->deleteThumbnail($article);
            $data['thumbnail'] = $request->file('thumbnail')->store('articles', 'public');
        } elseif ($request->boolean('remove_thumbnail')) {
            $this->deleteThumbnail($article);
            $data['thumbnail'] = null;
        }

        $article->update($data);

        return response()->json($article->fresh()->load('category:id,name,slug'));
    }

    public function destroy(Article $article): Response
    {
        $this->deleteThumbnail($article);
        $article->delete();

        return response()->noContent();
    }

    private function deleteThumbnail(Article $article): void
    {
        if ($article->thumbnail) {
            Storage::disk('public')->delete($article->thumbnail);
        }
    }

    private function generateSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'artikel';
        $slug = $base;
        $i = 2;

        while (Article::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
