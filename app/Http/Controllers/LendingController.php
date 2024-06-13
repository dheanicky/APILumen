<?php

namespace App\Http\Controllers;

use App\Models\Lending;
use App\Models\StuffStock;
use Illuminate\Http\Request;
use App\Helpers\ApiFormatter;
use Illuminate\support\Facades\App;
use App\Models\Restoration;


class LendingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $getLending = Lending::with('stuff', 'user')->get();

            return ApiFormatter::sendResponse(200, 'Successfully Get All Lending Data', $getLending);
        } catch(\Exception $e) {
            return ApiFormatter::sendResponse(400, $e -> getMessage());
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
        try{
            $this->validate($request, [
                'stuff_id' => 'required',
                'date_time' => 'required',
                'name' => 'required',
                'user_id' => 'required',
                'notes' => 'required',
                'total_stuff' => 'required',
            ]);

            $createLending = Lending::create([
                'stuff_id' => $request->stuff_id,
                'date_time' => $request->date_time,
                'name' => $request->name,
                'user_id' => $request->user_id,
                'notes' => $request->notes,
                'total_stuff' => $request->total_stuff,
            ]);

            $getStuffStock = StuffStock::where('stuff_id', $request -> stuff_id)->first();
            $updateStock = $getStuffStock -> update([
                'total_available' => $getStuffStock['total_available'] - $request -> total_stuff,
            ]);

            return ApiFormatter::sendResponse(200, 'Successfully Create A Lending Data', $createLending);
        } catch (\Exception $e) {
            return ApiFormatter::sendResponse(400, $e -> getMessage());

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Lending  $lending
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $getLending = Lending::where('id', $id) -> with('stuff', 'user', 'restoration')->first();

            if (!$getLending) {
                return ApiFormatter::sendResponse(404, 'Data Lending Not Found');
            } else {
                return ApiFormatter::sendResponse(200, 'Successfully Get A Lending Data', $getLending);
            }
        } catch (\Exception $e) {
            return ApiFormatter::sendResponse(400, $e -> getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Lending  $lending
     * @return \Illuminate\Http\Response
     */
    public function edit(Lending $lending)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Lending  $lending
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Lending $lending, $id)
    {
        try {
            $getLending = Lending::find($id);

            if($getLending) {
                $this->validate($request, [
                    'stuff_id' => 'required',
                    'date_time' => 'required',
                    'name' => 'required',
                    'user_id' => 'required',
                    'notes' => 'required',
                    'total_stuff' => 'required',
                ]);

                $getStuffStock = StuffStock::where('stuff_id', $request->stuff_id)->first();
                $getCurrentStock = StuffStock::where('stuff_id', $getLending['stuff_id'])->first();
            
                if ($request->stuff_id == $getCurrentStock['stuff_id']) {
                    $updateStock = $getCurrentStock->update([
                        'total_avaliable' => $getCurrentStock['total_avaliable'] + $getLending['total_stuff'] - $request->total_stuff,
                    ]);
                } else {
                    $updateStock = $getStuffStock->update([
                        'total_avaliable' => $getCurrentStock['total_avaliable'] + $getLending['total_stuff'],
                    ]);

                    $updateStock = $getStuffStock->update([
                        'total_avaliable' => $getCurrentStock['total_avaliable'] - $request['total_stuff'],
                    ]);
                }

                $updateLending = $getLending->update([
                    'stuff_id' => $request->stuff_id,
                    'date_time' => $request->date_time,
                    'name' => $request->name,
                    'user_id' => $request->user_id,
                    'notes' => $request->notes,
                    'total_stuff' => $request->total_stuff,
                ]);

                $getUpdateLending = Lending::where('id', $id)->with('stuff', 'user', 'restoration')->first();

                return ApiFormatter::sendResponse(200, 'Successfully Updare A Lending Data', $getUpdateLending);
            }
        } catch (\Exception $e) {
            return ApiFormatter::sendResponse(400, $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Lending  $lending
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lending $lending, $id)
    {
        try {
            // Find the lending record
            $lending = Lending::find($id);
        
            // Check for restoration (already returned)
            if ($lending->restoration) {
                return ApiFormatter::sendResponse(400, 'bad requet', 'Data peminjaman sudah memiliki data pengembalian');
            }
        
            // Delete the lending record
            $lending->delete();
        
            $stuffStock = StuffStock::where('stuff_id', $lending->stuff_id)->first();
        
            if ($stuffStock) {
                $stuffStock->total_available += $lending->total_stuff;
                $stuffStock->save();
            } 
    
            return ApiFormatter::sendResponse(200, 'success', 'Data Lending berhasil dihapus ');
        } catch (\Exception $err) {
            return ApiFormatter::sendResponse(400, false, $err->getMessage());
        }    

    }

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function recycleBin()
    {
        try {

            $lendingDeleted = Lending::onlyTrashed()->get();

            if (!$lendingDeleted) {
                return ApiFormatter::sendResponse(404, 'Deletd Data Lending Doesnt Exists');
            } else {
                return ApiFormatter::sendResponse(200, 'Successfully Get Delete All Lending Data', $lendingDeleted);
            }
        } catch (\Exception $e) {
            return ApiFormatter::sendResponse(400, $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {

            $getLending = Lending::onlyTrashed()->where('id', $id);

            if (!$getLending) {
                return ApiFormatter::sendResponse(404, 'Restored Data Lending Doesnt Exists');
            } else {
                $restoreLending = $getLending->restore();

                if ($restoreLending) {
                    $getRestore = Lending::find($id);
                    $addStock = StuffStock::where('stuff_id', $getRestore['stuff_id'])->first();
                    $updateStock = $addStock->update([
                        'total_available' => $addStock['total_available'] - $getRestore['total_stuff'],
                    ]);

                    return ApiFormatter::sendResponse(200, 'Successfully Restore A Deleted Lending Data', $getRestore);
                }
            }
        } catch (\Exception $e) {
            return ApiFormatter::sendResponse(400, $e->getMessage());
        }
    }

    public function forceDestroy($id)
    {
        try {

            $getLending = Lending::onlyTrashed()->where('id', $id);

            if (!$getLending) {
                return ApiFormatter::sendResponse(404, 'Data Lending for Permanent Delete Doesnt Exists');
            } else {
                $forceStuff = $getLending->forceDelete();

                if ($forceStuff) {
                    return ApiFormatter::sendResponse(200, 'Successfully Permanent Delete A Lending Data');
                }
            }
        } catch (\Exception $e) {
            return ApiFormatter::sendResponse(400, $e->getMessage());
        }
    }

}
