<?php

namespace App\Http\Controllers\Api\TopAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TopAdmin\Post\StorePostRequest;
use App\Http\Requests\Api\TopAdmin\Post\UpdatePostRequest;
use App\Models\Post;
use App\Traits\UploadsFile;

class PostController extends Controller
{
    use UploadsFile;

    public function index()
    {
        $postsBySection = Post::query()
            ->with('user:id,full_name,email')
            ->latest()
            ->get()
            ->groupBy(fn (Post $post) => $post->section ?? 'uncategorized');

        return response()->json([
            'status' => true,
            'message' => 'Posts fetched successfully',
            'data' => $postsBySection,
        ]);
    }

    public function show(string $id)
    {
        $post = Post::query()
            ->with('user:id,full_name,email')
            ->findOrFail($id);

        return response()->json([
            'status' => true,
            'message' => 'Post fetched successfully',
            'data' => $post,
        ]);
    }

    public function store(StorePostRequest $request)
    {
        $data = $request->safe()->except('file_path');
        $data['user_id'] = $request->user()->id;

        if ($request->hasFile('file_path')) {
            $uploaded = $this->storeUploadedFile($request->file('file_path'), 'uploads/posts');
            $data['file_path'] = $uploaded['path'];
            $data['file_type'] = $uploaded['mime_type'];
        }

        $post = Post::query()->create($data);

        return response()->json([
            'status' => true,
            'message' => 'Post created successfully',
            'data' => $post,
        ], 201);
    }

    public function update(UpdatePostRequest $request, string $id)
    {
        $post = Post::query()->findOrFail($id);
        $data = $request->safe()->except('file_path');

        if ($request->hasFile('file_path')) {
            $uploaded = $this->replaceUploadedFile(
                $request->file('file_path'),
                $post->file_path,
                'uploads/posts'
            );
            $data['file_path'] = $uploaded['path'];
            $data['file_type'] = $uploaded['mime_type'];
        }

        if ($data !== []) {
            $post->update($data);
        }

        $post->refresh();

        return response()->json([
            'status' => true,
            'message' => 'Post updated successfully',
            'data' => $post,
        ]);
    }

    public function destroy(string $id)
    {
        $post = Post::query()->findOrFail($id);
        $this->deleteStoredFile($post->file_path);
        $post->delete();

        return response()->json([
            'status' => true,
            'message' => 'Post deleted successfully',
        ]);
    }
}
