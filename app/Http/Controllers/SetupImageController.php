<?php

namespace App\Http\Controllers;

use App\Models\SetupImage;
use Illuminate\Http\Request;

class SetupImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        $setupimage = SetupImage::all();
        return response()->json([
            'status' => true,
            'images' => $setupimage
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(SetupImage $setupImage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SetupImage $setupImage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SetupImage $setupImage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SetupImage $setupImage)
    {
        //
    }
}
