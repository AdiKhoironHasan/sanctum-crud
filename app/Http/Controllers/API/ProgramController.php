<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\DTO;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

use App\Models\Program;
use App\Http\Resources\ProgramResource;

class ProgramController extends Controller
{
    public function index()
    {
        $data = Program::all();

        return DTO::ResponseDTO('Program List', null, $data, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required:string:max:255',
            'desc' => 'required'
        ]);

        if($validator->fails()){
            return DTO::ResponseDTO($validator->errors(), null, null, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $program = Program::create([
                'name' => $request->name,
                'desc' => $request->desc,
            ]);
        } catch (\Throwable $th) {
            return DTO::ResponseDTO('Failed Create Program', null, null, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return DTO::ResponseDTO('Program Create Successfully', null, $program, Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $program = Program::find($id);
        if(is_null($program)){
            return DTO::ResponseDTO('Data Not Found', null, null, Response::HTTP_NOT_FOUND);
        }
        return DTO::ResponseDTO('Show Program Successfully', null, $program, Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $program = Program::find($id);

        if(is_null($program)){
            return DTO::ResponseDTO('Data Not Found', null, null, Response::HTTP_NOT_FOUND);
        }
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'desc' => 'required'
        ]);

        if ($validator->fails()) {
            return DTO::ResponseDTO($validator->errors(), null, null, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $program->update([
                'name' => $request->name,
                'desc' => $request->desc,
            ]);
        } catch (\Throwable $th) {
            return DTO::ResponseDTO('Failed Update Program', null, null, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return DTO::ResponseDTO('Program Update Successfully', null, $program, Response::HTTP_CREATED);
    }

    public function destroy($id)
    {
        $program = Program::find($id);

        if(is_null($program)){
            return DTO::ResponseDTO('Data Not Found', null, null, Response::HTTP_NOT_FOUND);
        }
        try {
            $program->delete();
        } catch (\Exception $error) {
            return DTO::ResponseDTO('Delete Program Failed', null, null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return DTO::ResponseDTO('Delete Program Successfully', null, null, Response::HTTP_OK);
    }

}
