<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Api\HttpResponseTrait;

class BlogController extends Controller
{
    use HttpResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // getting all blogs 
        $blogs = Blog::with('category')->get();

        return $this->successResponse(200, "blogs was retrived successfully", BlogResource::collection($blogs));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "category_id" => "required|exists:categories,id",
            'title' => "required|string|max:255|unique:blogs,title",
            'body' => "required|string",
        ]);

        if($validator->fails()){
            return $this->errorResponse(400, $validator->errors());
        }

        return $validator->validated();
        $blog = Blog::create($validator->validated());

        return $this->successResponse(201, "a blog was created successfully", new BlogResource($blog));
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog)
    {
        return $this->successResponse(200, "blog was retrived successfully", new BlogResource($blog));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Blog $blog)
    {
        $validator = Validator::make($request->all(), [
            "category_id" => "required|exists:categories,id",
            'title' => "required|string|max:255|unique:blogs,title,".$blog->id,
            'body' => "required|string"
        ]);

        if($validator->fails()){
            return $this->errorResponse(400, $validator->errors());
        }

        $blog->update($validator->validated());

        return $this->successResponse(200, "blog was updated successfully", new BlogResource($blog));
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
        $blog->delete();
        return $this->successResponse(200, "blog was deleted successfully", );
    }
}
