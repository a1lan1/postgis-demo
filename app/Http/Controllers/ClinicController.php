<?php

namespace App\Http\Controllers;

use App\Contracts\ClinicServiceInterface;
use App\Http\Requests\IndexClinicRequest;
use App\Http\Requests\StoreClinicRequest;
use App\Models\Clinic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClinicController extends Controller
{
    public function __construct(private readonly ClinicServiceInterface $clinicService) {}

    public function index(IndexClinicRequest $request): Response
    {
        $clinics = [];

        if (! empty($request->validated('lat')) && ! empty($request->validated('lng'))) {
            $clinics = $this->clinicService->getNearbyClinics(
                $request->validated('lat'),
                $request->validated('lng'),
                $request->validated('radius', 5000),
            );
        }

        return Inertia::render('clinic/Index', [
            'clinics' => $clinics,
        ]);
    }

    public function store(StoreClinicRequest $request): JsonResponse
    {
        $clinic = Clinic::create(
            $request->validated()
        );

        return response()->json($clinic);
    }

    public function autocomplete(Request $request): JsonResponse
    {
        return response()->json(
            $this->clinicService->autocomplete(
                $request->get('query')
            )
        );
    }
}
