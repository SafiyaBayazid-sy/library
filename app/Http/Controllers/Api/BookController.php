<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $book=Book::with('authors', 'category')->get();
        // return ResponseHelper::success('جميع الكتب', $book);
        // }

        // $book = Book::all();

        /** using map to return custom fields */
        // $books = Book::select("ISBN" ,"title" ,  "price" ,"mortgage" ,"cover")
        // ->get()
        // ->map(function($book){
        //     return [
        //         "ISBN" => $book->ISBN ,
        //         "title" => $book->title ,
        //         "price" => $book->price ,
        //         "mortgage" => $book->mortgage ,
        //         "cover" =>  asset('storage/book-images/' . ($book->cover ?? 'no-image.jpeg')) ,
        //     ];
        // });
        // return ResponseHelper::success(' جميع الكتب', $books);

        // if ($title){}, we use when() method instead of if condition

        // $title = $request->has('title');
        $title = $request->title;
        $books = Book::select("id" ,"ISBN" ,"title" ,  "price" ,"mortgage" ,"cover" , "category_id")
        ->when($title , function($q ) use ($title) {
            return $q->where('title' , 'like' , "%$title%");
        })
        ->with(['authors', 'category'])
        ->orderBy('id' )
        ->get();

        /** Using resource */
        return ResponseHelper::success(trans('library.all-categories'), BookResource::collection($books));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {
        $book = Book::create($request->all());

        $book->authors()->attach($request->author_ids);


        if ($request->hasFile('cover')){
            $file = $request->file('cover');
            $filename = "$book->ISBN." . $file->extension();
            Storage::putFileAs('book-images', $file ,$filename );
            $book->cover = $filename;

            $book->save();
        }

        $book->load('authors','category');
        return ResponseHelper::success("تمت إضافة الكتاب", $book);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        // $book->load('authors', 'category');
        // return ResponseHelper::success("تفاصيل الكتاب", $book);
        $book = $book->load(['authors:name', 'category']);

        return ResponseHelper::success("تم إعادة الكتاب بنجاح", new BookResource($book));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        $book->update($request->all());

         if ($request->has('author_ids')) {
        $book->authors()->sync($request->author_ids);
    }

        if ($request->hasFile('cover')) {
        // Delete old cover
        if ($book->cover) {
            Storage::delete('book-images/' . $book->cover);
        }

        // Save new cover
         $file = $request->file('cover');
        $filename = "$book->ISBN." . $file->extension();

        Storage::putFileAs('book-images', $file, $filename);
        $book->cover = $filename;
        $book->save();
    }


    $book->load('authors', 'category');

        return ResponseHelper::success("تم تعديل الكتاب", $book);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
            if($book->cover)
            Storage::delete('book-images/' . $book->cover);
            $book->delete();

            return ResponseHelper::success("تم حذف الكتاب", $book);

    }
}
