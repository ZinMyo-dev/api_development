<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{

    protected function errorResponse($errorCode, $errorMessage){
        return response()->json([
            'statusCode' => $errorCode,
            'message' => $errorMessage,
        ], $errorCode);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // getting all blogs 
        $blogs = Blog::all();

        return response()->json([
            'statusCode' => 200,
            'message' => 'all blogs retrived successfully',
            'blogs' => BlogResource::collection($blogs),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => "required|string|max:255|unique:blogs,title",
            'body' => "required|string",
        ]);

        if($validator->fails()){
            return $this->errorResponse(400, $validator->errors());
        }

        $blog = Blog::create([
            'title' => $request['title'],
            'body' => $request['body'],
        ]);

        return response()->json([
            'statusCode' => 201,
            'blog' => $blog,
            'message' => 'a blog created successfully',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog)
    {
        return response()->json([
            'statusCode' => 200,
            'message' => 'blog retrived successfully',
            'blog' => new BlogResource($blog),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Blog $blog)
    {
        $validator = Validator::make($request->all(), [
            'title' => "required|string|max:255|unique:blogs,title,".$blog->id,
            'body' => "required|string"
        ]);

        if($validator->fails()){
            return $this->errorResponse(400, $validator->errors());
        }

        $blog->update([
            'title' => $request['title'],
            'body' => $request['body'],
        ]);

        return response()->json([
            'statusCode' => 200,
            'message' => 'blog was updated successfully',
            'blog' => new BlogResource($blog),
        ], 200 );
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
        $blog->delete();
        return response()->json([
            'statusCode' => 200,
            'message' => 'blog was deleted successfully',
        ], 200);
    }
}
