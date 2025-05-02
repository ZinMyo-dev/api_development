<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Api\HttpResponseTrait;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    use HttpResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = Category::when($request->q, function($query) use($request) {
            $query->where("name", "like", "%".$request->q."%");
        })->paginate(2);    
        return $this->successResponse(200, "categories retrived successfully",  ['categories' => CategoryResource::collection($categories)]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required|string|max:255|unique:categories,name",
        ]);

        if($validator->fails()){
            return $this->errorResponse(400, $validator->errors());
        }

        $category = Category::create([
            'name' => $request->name,
        ]);
        return $this->successResponse( 201, "a category was created successfully", ["category" => new CategoryResource($category)]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return $this->successResponse(200, "a category was retrived successfylly", ['category' => new CategoryResource($category)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required|string|max:255|unique:categories,name,".$category->id,
        ]);

        if($validator->fails()){
            return $this->errorResponse(400, $validator->errors());
        }

        $category->update([
            'name' => $request->name,
        ]);

        return $this->successResponse(200, "category was updated successfully", new CategoryResource($category));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return $this->successResponse(200, "a category was deleted successfully");
    }
}
