<?php

namespace App\Http\Controllers;

use App\Models\Stuff;
use App\Helpers\ApiFormatter;
use Illuminate\Http\Request;

class StuffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // ambil data yang mau di tampilkan
            $data = Stuff::with('stuffStock', 'inboundStuffs', 'Lendings')->get();

            return ApiFormatter::sendResponse(200, 'success', $data);
        } catch(\Exception $err){
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
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
        try {
            $this->validate($request, [
                'name' => 'required',
                'category' => 'required',
            ]);

            $data = Stuff::create([
                'name' => $request->name,
                'category' => $request->category,

            ]);
            return ApiFormatter::sendResponse(200,'success',$data);
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400,'bad request' , $err->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Stuff  $stuff
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $data = Stuff::where('id', $id)->with('stuffStock', 'inboundStuffs', 'Lendings')->first();

            if(is_null($data)) {
                return ApiFormatter::sendResponse(400, 'bad request', 'Data not found!');
            }else {
                return ApiFormatter::sendResponse(200, 'success', $data);
            }
            
        }catch(\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Stuff  $stuff
     * @return \Illuminate\Http\Response
     */
    public function edit(Stuff $stuff)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Stuff  $stuff
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    try {
        $this->validate($request, [
            'name' => 'required',
            'category' => 'required'
        ]);

        $checkProses = Stuff::where('id', $id)->update([
            'name' => $request->name,
            'category' => $request->category   
        ]);

        if($checkProses) {
            $data = Stuff::find($id);
            return ApiFormatter::sendResponse(200, 'succes', $data);
        }else {
            return ApiFormatter::sendResponse(400, 'bad request ',  'failed to update data!');
            
        }
    } catch(\Exception $err) {
        return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
    }
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Stuff  $stuff
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $getStuff = Stuff::where('id' ,$id)->delete();
            
            return ApiFormatter::sendResponse(200, 'success', 'Data stuff berhasil di hapus!');
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request' , $err->getMessage());
        }
    }

    public function trash()
    {
        try {
            $data = Stuff::onlyTrashed()->get();

            return ApiFormatter::sendResponse(200, 'success', $data);
        }catch(\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
    }
}

    public function restore($id)
    {
        try {
         
            $checkProses = Stuff::onlyTrashed()->where('id', $id)->restore();

        if($checkProses){
            $data = Stuff::find($id);
            return ApiFormatter::sendResponse(200, 'success', $data);
        }else {
            return ApiFormatter::sendResponse(400, 'bad request', 'failed to restore data!');
        }

        }catch(\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
    }
    }

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function deletePermanent($id)
    {
        try {
            $checkProses = Stuff::onlyTrashed() -> where('id', $id)-> forceDelete();

            return ApiFormatter::sendResponse(200, 'success', 'Berhasil menghapus permanen data stuff!');
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, 'bad request', $err->getMessage());
        }
    }
}
