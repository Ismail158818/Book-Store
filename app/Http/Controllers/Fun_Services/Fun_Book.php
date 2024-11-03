<?php
namespace  App\Http\Controllers\Fun_Services;

use App\Models\Type;
use App\Models\Book;

class Fun_Book
{
    public function add_book_services($validated)
    {
        $a = Book::where('name', $validated['name'])->get();
        if(!$a->isEmpty())
        {
            return 'false';
        }
        else{
            $book = Book::create([
                'name' => $validated['name'],
                'writer' => $validated['writer'],
                'image' => $validated['image'],
                'book_price' => $validated['book_price'],
                'type_id' => $validated['type_id'],
                'description' => $validated['description']
            ]);

            return 'true';
        }

    }
    public function show_all_book_services()
    {
        $book=Book::all();
        return $book;
    }
  
    public function delete_book_services($request)
    {
        $book=Book::where('name',$request->name);
        if($book)
        {
            $book->delete();
            return 'true';
        }
        return 'fulse';
    }
    public function edit_book_services($validated)
    {
        $book = Book::find($validated['id']);
        if (!$book) {
            return 'false';
        }

        $status=$book->update(array_filter([
            'name' => $validated['name'] ?? $book->name,
            'writer' => $validated['writer'] ?? $book->writer,
            'image' => $validated['image'] ?? $book->image,
            'book_price' => $validated['book_price'] ?? $book->book_price,
            'type_id' => $validated['type_id'] ?? $book->type_name,
            'description' => $validated['description'] ?? $book->description
        ]));
        if ($status)
        {
            return 'true';
        }
        return 'false';

    }





}
