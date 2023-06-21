<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaginationRequest;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Blog;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PaginationRequest $request)
    {   
        //return message if not log in
        if( !Auth::check() ){
            return response()->json([
                'message' => 'Please log in to view this page',
            ], 401);
        }
        $page_number= $request->query('page', 2);
        $per_page= $request->query('per_page', 10);

        $query= Comment::query();

        $comments_view= $query-> paginate($per_page, ['*'], 'page', $page_number);
        return response()->json($comments_view, 200);
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request, $id)
    {
        // Find the corresponding Blog model by its ID
        $blog = Blog::find($id);

        $userId = Auth::id();
    
        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], 404);
        }
    
        // Create a new comment with the provided data and associate it with the blog post
        $comment = new Comment([
            'name' => $request->name,
            'comment' => $request->comment,
            'blog_id' => $id,
            'user_id' => $userId,
        ]);       
        $blog->comments()->save($comment);
    
        // Return the response with the created comment
        return response()->json($comment, 201);
    }    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $post_id, int $cmt_id)
    {
        // Find the comment by its ID
        $comment = Comment::find($cmt_id);
    
        // Check if the comment exists
        if (!$comment) return response()->json(['message' => 'Comment not found'], 404);
    
        // Check if the comment belongs to the specified blog post
        if ($comment->blog_id !== $post_id) {
            return response()->json(['message' => 'Comment in blog not found',
                                    'id' => $cmt_id,
                                    'blog'=> $post_id,
                                    'com of blog'=>$comment->blog_id], 404);
        }
        //get user auth
        $user = Auth::user();
        //check if user has permission to update comment
        if ($user->id !== $comment->user_id) {
            return response()->json(['message' => 'Unauthorized to update this comment'], 406);
        }
        //update comment
        $comment->update($request->all());
        return response()->json(['message' => 'Comment Updated!!!'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $post_id, int $cmt_id)
    {
        // Find the comment by its ID
        $comment = Comment::find($cmt_id);
    
        // Check if the comment exists
        if (!$comment) return response()->json(['message' => 'Comment not found'], 404);
        
    
        // Check if the comment belongs to the specified blog post
        if ($comment->blog_id !== $post_id) {
            return response()->json(['message' => 'Comment in blog not found'], 404);
        }
    
        // Get the authenticated user
        $user = Auth::user();
    
        // Check if the comment belongs to the authenticated user, if not return message
        if ($user->id !== $comment->user_id) {
            return response()->json(['message' => 'Unauthorized to delete this comment'], 406);
        }
    
        // Delete the comment
        $comment->delete();
    
        // Return a success message
        return response()->json(['message' => 'Comment deleted successfully'], 200);
    }
    
}


