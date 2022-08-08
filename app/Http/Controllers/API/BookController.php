<?php

namespace App\Http\Controllers\API;

use App\Helpers\DTO;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $books = Book::all();

        return DTO::ResponseDTO('Books List', null, $books, Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'desc' => 'required'
        ]);

        if ($validator->fails()) {
            return DTO::ResponseDTO($validator->errors(), null, null, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $book = Book::create([
                'title' => $request->title,
                'desc' => $request->desc
            ]);
        } catch (\Throwable $th) {
            return DTO::ResponseDTO('Failed Create Book', null, null, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return DTO::ResponseDTO('Book Created Successfully', null, $book, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $book = Book::find($id);
        if (is_null($book)) {
            return DTO::ResponseDTO('Book Not Found', null, null, Response::HTTP_NOT_FOUND);
        }

        return DTO::ResponseDTO('Show Book Successfully', null, $book, Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function edit(Book $book)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $book = Book::find($id);

        if (is_null($book)) {
            return DTO::ResponseDTO('Book Not Found', null, null, Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'desc' => 'required'
        ]);

        if ($validator->fails()) {
            return DTO::ResponseDTO($validator->errors(), null, null, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $book->update([
                'title' => $request->title,
                'desc' => $request->desc
            ]);
        } catch (\Throwable $th) {
            return DTO::ResponseDTO('Failed Update Book', null, null, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return DTO::ResponseDTO('Book Update Successfully', null, $book, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $book = Book::find($id);

        if (is_null($book)) {
            return DTO::ResponseDTO('Book Not Found', null, null, Response::HTTP_NOT_FOUND);
        }

        try {
            $book->delete();
        } catch (\Throwable $th) {
            return DTO::ResponseDTO('Delete Book Failed', null, null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return DTO::ResponseDTO('Delete Book Successfully', null, null, Response::HTTP_OK);
    }
}
