<?php

namespace App\Http\Controllers\Api\v1\Page;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;

class PageController extends ApiController
{
    public function __construct()
    {
    }

    public function index(): JsonResponse
    {
        $pages = json_decode(file_get_contents(public_path('/data/pages.json')), true);

        return $this->successResponse($pages);
    }
}
