<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaginationRequest;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreUserRequest;
 
class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PaginationRequest $request)
    {
        $page_number = $request->query('page' , 1) ;
        $per_page = $request->query('per_page' , 10);
        $query = Blog::query();
        //search query
        $search = $request->query('search');
        
        
        if ($search){
            $query = $query->where('name' , 'like', '%' . $search . '%');
        }
        $posts_view = $query->paginate($per_page, ['*'] , 'page' , $page_number);
        return response()->json($posts_view, 200);
    }
    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreUserRequest $request)
    {       
        Blog::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => Auth::user()->id 
        ]);       
        // Return response json with message
        return response()->json(['message' => 'blog created successfully', ] , 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {   
        $blog = Blog::find($id);
        //message if id not found   
        if(!$blog){
            return response()->json([
                'message' => 'blog not found',
            ], 404);
        }
        //return blog by id
        return $blog;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request , int $id)
    {
        //find blog by blog's id
        $blog = Blog::find($id);
        //check if user is post's owner
        if(Auth::id() != $blog->user_id) {
            return response()->json(['message'=> 'You are not authorized to edit this post'], 406);
        }
        //update blog from request
        $blog->update($request->all());
        //return response json with message
        return response()->json([
            'message' => 'blog updated successfully',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        //find blog by id
        $blog = Blog::find($id);
        // Return error message if blog is not found
        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], 404);
        }
        //return message error if dont have auth to delete
        if(Auth::id() != $blog->user_id){
            return response()->json(['message'=> 'You are not authorized to delete this post',], 401);
        };
        //return delete blog only if it match with user_id
        Blog::where('id', $id)->where('user_id', Auth::id())->delete();
         //create response json with message
            return response()->json([
                'message' => 'blog deleted successfully',
            ], 200);
    }

    
}
