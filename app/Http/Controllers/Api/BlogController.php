<?php

namespace App\Http\Controllers\Api;

use App\Api\HttpResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    use HttpResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // getting all blogs 
        $blogs = Blog::when($request->q, function($query) use($request) {
            $query->where("title", "like", "%".$request->q."%");
        } )->get();

        return $this->successResponse(200, "blogs was retrived successfully", ["blogs" => BlogResource::collection($blogs)] );
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

        return $this->successResponse(201, "blog was created successfully", ["blog" => new BlogResource($blog)]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog)
    {
        return $this->successResponse(200, "blog was retrived successfully", ["blog" => new BlogResource($blog)]);
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

        return $this->successResponse(200, "blog was updated successfully", ["blog" => new BlogResource($blog)]);
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
        $blog->delete();
        return $this->successResponse(200, "blog was deleted successfully");
    }
}
