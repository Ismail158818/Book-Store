<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Fun_Services\Fun_Book;
use App\Http\Requests\BookRequest;
use Illuminate\Http\Request;

class BookApiController extends Controller
{
    public function add_book(BookRequest $request)
    {
        if (!auth()->check()) {
            return response()->json(['status' => 'error',
                'message' => 'User not authenticated']
                , 401);
        }
         $add=new Fun_Book();
         $data=$add->add_book_services($request);
         if($data=='true')
         {
             return response()->json('success');
         }
         return response()->json('fail');
    }
    
    public function delete_book(Request $request)
    {
        $add = new Fun_Book();
        $data = $add->delete_book_services($request);

        if ($data) {
            return response()->json([
                'status' => "success delete book {$request->name}"
            ]);
        } else {
            return response()->json([
                'status' => 'error deleting book'
            ]);
        }
    }

    public function show_all_book()
    {
        $add = new Fun_Book();
        $data = $add->show_all_book_services();
        if ($data) {
            return response()->json([
                'status' => 'success',
                'data' => $data
            ], 200);
        }
        return response()->json(['status' => 'fail'], 400);
    }
      public function edit_book(BookRequest $request)
      {
          $add = new Fun_Book();
          $data = $add->edit_book_services($request);
          if ($data)
          {
              return response()->json('success');
          }
          return response()->json('fail');
      }
}
