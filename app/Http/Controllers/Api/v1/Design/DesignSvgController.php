<?php

namespace App\Http\Controllers\Api\v1\Design;

use App\Http\Controllers\Controller;
use App\Models\Design;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class DesignSvgController extends Controller
{

    /**
     * @param string $filename
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws Exception
     */
    public function default(string $filename)
    {
        $path = public_path("vehicles_svg/default/{$filename}");

        if (!file_exists($path) || pathinfo($path, PATHINFO_EXTENSION) === '.svg') {
            throw new Exception("El archivo {$filename} no existe", Response::HTTP_NOT_FOUND);
        }

        return response(file_get_contents($path))->header('Content-Type', 'image/svg+xml');
    }
}
