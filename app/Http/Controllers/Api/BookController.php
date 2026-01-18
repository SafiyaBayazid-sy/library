<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\ResponseHelper;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $book=Book::with('authors', 'category')->get();
        return ResponseHelper::success('جميع الكتب', $book);
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
            $filename = "$request->ISBN." . $file->extension();
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
        $book->load('authors', 'category');
        return ResponseHelper::success("تفاصيل الكتاب", $book);
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
        $filename = "$request->ISBN." . $file->extension();

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
