<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $categories =  Category::all();
        // $categories =  Category::withAvg('books' , 'price')->get();
        $categories =  Category::with('books')->get();
       return ResponseHelper::success(' عرض جميع الأصناف',$categories);
    }

     public function show(Category $category)
    {
        $category->load('books');
       return ResponseHelper::success('بيانات الصنف',$category);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50|unique:categories',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // 2MB max
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->save();

         if ($request->hasFile('image')){
            $file = $request->file('image');
            $filename = Str::random(40) . '.' . $file->extension();
            Storage::putFileAs('categories-images', $file ,$filename );
            $category->image = $filename;
            $category->save();
        }

        return ResponseHelper::success("تمت إضافة الصنف" , $category);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => "required|max:50|unique:categories,name,{$category->id}",
            'image'=>'sometimes'
        ]);

        $category->name = $request->name;
        $category->save();

          if ($request->hasFile('image')) {
        // Delete old image
        if ($category->hasFile('image')) {
            Storage::delete('categories-images/' . $category->image);
        }

        // Save new image
         $file = $request->file('image');
         $filename = Str::random(40) . '.' . $file->extension();
         Storage::putFileAs('categories-images', $file ,$filename );
         $category->image = $filename;
        $category->save();
    }


        return ResponseHelper::success("تم تعديل الصنف" , $category);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if($category->books()->count() > 0)
        {
            return ResponseHelper::failed("لا يمكن حذف الصنف لارتباطه بالكتب", []);
        }
        else {

        if($category->image)
        Storage::delete('categories-images/' . $category->image);

        $category->delete();
        return ResponseHelper::success("تم حذف الصنف" , $category);

        }
    }
}
