<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class JetsonMiniGridWeatherDataController extends Controller
{

    public function index($miniGridId, $slug, $storageFolder, $file)
    {
        $fileName = $file;
        $companyId = $slug;
        try {
            $file = Storage::disk('local')->get("/public/$storageFolder/$miniGridId/public/$fileName");
            return json_decode($file, true);
        }catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}