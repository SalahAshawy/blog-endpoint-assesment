<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;

class PostController extends Controller
{
    public function index()
    {
        return PostResource::collection(Post::with('user', 'category')->get());
    }

    public function store(Request $request)
    {
        try {
            $this->authorize('isAdmin', User::class);
            
            $request->validate([
                'title' => 'required|string|max:255',
                'body' => 'required|string',
                'category_id' => 'required|exists:categories,id',
            ]);

            $post = Post::create([
                'user_id' => Auth::id(),
                'category_id' => $request->category_id,
                'title' => $request->title,
                'body' => $request->body,
            ]);

            return new PostResource($post);
        } catch (AuthenticationException $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        } catch (AuthorizationException $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function show(Post $post)
    {
        return new PostResource($post->load('user', 'category'));
    }

    public function update(Request $request, Post $post)
    {
        try {
            $this->authorize('isAdmin', User::class);
            
            $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'body' => 'sometimes|required|string',
                'category_id' => 'sometimes|required|exists:categories,id',
            ]);

            $post->update($request->only('title', 'body', 'category_id'));

            return new PostResource($post);
        } catch (AuthenticationException $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        } catch (AuthorizationException $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function destroy(Post $post)
    {
        try {
            $this->authorize('isAdmin', User::class);
            $post->delete();

            return response()->json(null, 204);
        } catch (AuthenticationException $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        } catch (AuthorizationException $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }
    }
}
