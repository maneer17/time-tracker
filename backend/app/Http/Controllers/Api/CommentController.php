<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CommentResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\{StoreCommentRequest, UpdateCommentRequest};
use App\Models\{Channel, Comment, SharedDay};

class CommentController extends Controller
{
    public function index(Channel $channel, SharedDay $sharedDay)
    {
        $this->authorize('viewAny', [Comment::class, $channel]);

        return CommentResource::collection(
            $sharedDay->comments()
                ->with(['author', 'sharedDay.channel'])
                ->latest()
                ->paginate(1)
        );
    }

    public function store(StoreCommentRequest $request, Channel $channel, SharedDay $sharedDay)
    {
        $this->authorize('create', [Comment::class, $channel]);

        $comment = $sharedDay->comments()->create([
            ...$request->validated(),
            'user_id' => auth()->id()
        ]);

        return new CommentResource($comment->load(['author', 'sharedDay.channel']));
    }

    public function update(UpdateCommentRequest $request, Channel $channel, SharedDay $sharedDay, Comment $comment)
    {
        $this->authorize('update', $comment);

        $comment->update($request->validated());

        return new CommentResource($comment->load(['author', 'sharedDay.channel']));
    }

    public function destroy(Channel $channel, SharedDay $sharedDay, Comment $comment)
    {
        $this->authorize('delete', [$comment, $channel]);

        $comment->delete();

        return new CommentResource($comment);
    }
}
