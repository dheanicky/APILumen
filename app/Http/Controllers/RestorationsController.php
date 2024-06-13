<?php

namespace App\Http\Controllers;
use App\Helpers\ApiFormatter;
use App\Models\Restoration;
use Illuminate\Http\Request;
use App\Models\Lending;
use App\Models\StuffStock;


class RestorationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
                'user_id' => 'required',
                'lending_id' => 'required',
                'date_time' => 'required',
                'total_good_stuff' => 'required',
                'total_defec_stuff' => 'required',
            ]);

            $getLending = Lending::where('id', $request->lending_id)->first(); //get data sesuai dengan pengenbalian

            $totalStuff = $request->total_good_stuff + $request->total_defec_stuff; 

            if ($getLending['total_stuff'] != $totalStuff) { //pengecekan apakah barang yang dipinjam jumlahnya sesuai atau tidak
                return ApiFormatter::sendResponse(400, false, 'The Amount Of item returned doest match the amount');
            } else {
                $getStuffStock = StuffStock::where('stuff_id', $getLending['stuff_id'])->first();
            }

            $createRestoration = Restoration::create([
                'user_id' => $request->user_id,
                'lending_id' => $request->lending_id,
                'date_time' => $request->date_time,
                'total_good_stuff' => $request->total_good_stuff,
                'total_defec_stuff' => $request->total_defec_stuff,
            ]);

            $updateStock = $getStuffStock->update([
                'total_avaliable' => $getStuffStock['total_avaliable'] + $request->total_good_stuff,
                'total_defec' => $getStuffStock['total_defec'] + $request->total_defec_stuff,
            ]);

            if ($createRestoration && $updateStock) {
                return ApiFormatter::sendResponse(200, 'Sucessfully create A Restoration Data', $createRestoration);
            }
        }catch (\Exception $e) {
            return ApiFormatter::sendResponse(400, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Restorations  $restorations
     * @return \Illuminate\Http\Response
     */
    public function show(Restorations $restorations)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Restorations  $restorations
     * @return \Illuminate\Http\Response
     */
    public function edit(Restorations $restorations)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Restorations  $restorations
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Restorations $restorations)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Restorations  $restorations
     * @return \Illuminate\Http\Response
     */
    public function destroy(Restorations $restorations)
    {
        //
    }

    public function __construct()
    {
        $this->middleware('auth:api');
    }
}
