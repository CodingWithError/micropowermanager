<?php

namespace App\Http\Controllers;

use App\Models\Cluster;
use App\Models\MiniGrid;
use App\Models\City;
use App\Models\Country;
use App\Http\Requests\CityRequest;
use App\Http\Resources\ApiResource;
use App\Services\CityService;
use App\Services\ClusterService;
use App\Services\MiniGridService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CityController extends Controller
{
    public function __construct(
        private CityService $cityService
    ) {
    }

    public function index(Request $request): ApiResource
    {
        $limit = $request->get('limit');

        return ApiResource::make($this->cityService->getAll($limit));
    }

    public function show($cityId, Request $request): ApiResource
    {
        $relation = $request->get('relation');

        if ($relation) {
            return ApiResource::make($this->cityService->getByIdWithRelation($cityId, ['location', 'country']));
        }

        return ApiResource::make($this->cityService->getById($cityId));
    }


    public function update($cityId, CityRequest $request): ApiResource
    {
        $city = $this->cityService->getById($cityId);
        $cityData = $request->only(['name', 'mini_grid_id', 'cluster_id','country_id']);

        return ApiResource::make($this->cityService->update($city, $cityData));
    }

    public function store(CityRequest $request): ApiResource
    {
        $city = $this->cityService->create($request->getCity());
        return ApiResource::make($city);
    }
}
