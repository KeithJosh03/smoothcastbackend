<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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

            $uploaded[] = [
                'originIndex' => $originIndex,
                'url' => Storage::url($path),
            ];
        }

        return response()->json([
            'status' => true,
            'files' => $uploaded
        ]);
    }
}