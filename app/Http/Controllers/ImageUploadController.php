<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'image|mimes:jpg,jpeg,png,gif,bmp,webp|max:5120',
            'originIndex' => 'required|array',
        ]);
        
        $uploaded = [];
        $files = $request->file('files');
        $originIndexes = $request->input('originIndex', []);

        foreach ($files as $key => $file) {
            // Safely fetch originIndex matching the specific file key
            $originIndex = isset($originIndexes[$key]) ? (int) $originIndexes[$key] : (int) $key;

            $name = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads', $name, 'public');

            // Force a clean relative path instead of full domain URL from Storage::url()
            $uploaded[] = [
                'originIndex' => $originIndex,
                'url' => '/storage/' . $path,
            ];
        }

        return response()->json([
            'status' => true,
            'files' => $uploaded
        ]);
    }
}