<?php

namespace App\Http\Requests;

use Clickbar\Magellan\Data\Geometries\Point;
use Clickbar\Magellan\Http\Requests\TransformsGeojsonGeometry;
use Clickbar\Magellan\Rules\GeometryGeojsonRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClinicRequest extends FormRequest
{
    use TransformsGeojsonGeometry;

    public function geometries(): array
    {
        return ['location'];
    }

    /**
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', new GeometryGeojsonRule([Point::class])],
        ];
    }
}
