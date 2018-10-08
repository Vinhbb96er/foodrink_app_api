<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Device as DeviceResource;
use App\Models\Device;
use Exception;
use DB;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new DeviceResource(Device::all());
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
        DB::beginTransaction();

        try {
            $data = $request->only('device_secret');

            $device = Device::where('device_secret', $data['device_secret'])->first();

            if (!$device) {
                Device::create($data);
            }

            DB::commit();
            $code = 201;
        } catch (Exception $e) {
            DB::rollback();
            report($e);
            $code = 404;
        }
        
        return response()->json(['code' => $code]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($token)
    {
        DB::beginTransaction();

        try {
            $device = Device::where('device_secret', $token)->first();

            if (!$device) {
                throw new Exception("Error Processing Request", 1);
            }

            $device->delete();

            $code = 200;
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            abort($e);
            $code = 404;
        }

        return response()->json(['code' => $code]);
    }
}
