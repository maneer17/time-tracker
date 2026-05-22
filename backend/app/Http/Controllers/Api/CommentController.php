<?php

namespace App\Http\Controllers\Api;

use App\Events\{NewCommentEvent, CommentDeletedByOwnerEvent};
use App\Http\Resources\CommentResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\{StoreCommentRequest, UpdateCommentRequest};
use App\Models\{Channel, Comment, SharedDay};
class CommentController extends Controller
{
    public function index(SharedDay $sharedDay)
    {
        $this->authorize('viewAny', [Comment::class, $sharedDay->channel]);

        return CommentResource::collection(
            $sharedDay->comments()
                ->with(['author', 'sharedDay.channel'])
                ->latest()
                ->paginate($this->paginate)
        );
    }

    public function store(StoreCommentRequest $request, SharedDay $sharedDay)
    {
        $this->authorize('create', [Comment::class, $sharedDay->channel]);

        $comment = $sharedDay->comments()->create([
            ...$request->validated(),
            'user_id' => auth()->id()
        ]);
        $comment = $comment->load(['author', 'sharedDay.channel']);
        event(new NewCommentEvent($comment));
        return new CommentResource($comment);
        
    }

    public function update(UpdateCommentRequest $request, SharedDay $sharedDay, Comment $comment)
    {
        $this->authorize('update', $comment);

        $comment->update($request->validated());

        return new CommentResource($comment->load(['author', 'sharedDay.channel']));
    }

    public function destroy(SharedDay $sharedDay, Comment $comment)
    {
        $this->authorize('delete', [$comment, $sharedDay->channel]);

        // ✅ load relations and fire event BEFORE deleting
        $comment->load(['author', 'sharedDay.channel']);

        if ($comment->sharedDay->channel->isOwner(auth()->user())) {
            event(new CommentDeletedByOwnerEvent($comment));
        }

        $comment->delete();
        return new CommentResource($comment);
    }

}
