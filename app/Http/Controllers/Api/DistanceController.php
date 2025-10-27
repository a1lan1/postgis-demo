<?php

namespace App\Http\Controllers\Api;

use App\Contracts\DistanceServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\CalculateDistanceRequest;
use Illuminate\Http\JsonResponse;

class DistanceController extends Controller
{
    public function __construct(
        protected DistanceServiceInterface $distanceService
    ) {}

    public function calculate(CalculateDistanceRequest $request): JsonResponse
    {
        $distanceInKm = $this->distanceService->calculate(
            $request->validated('lat1'),
            $request->validated('lng1'),
            $request->validated('lat2'),
            $request->validated('lng2'),
        );

        return response()->json([
            'distance' => $distanceInKm,
            'unit' => 'km',
        ]);
    }
}
