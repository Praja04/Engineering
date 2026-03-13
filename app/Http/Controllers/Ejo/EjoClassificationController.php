<?php

namespace App\Http\Controllers\Ejo;

use App\Http\Controllers\Controller;
use App\Models\Ejo\EjoClassification;
use App\Models\Ejo\EjoType;

class EjoClassificationController extends Controller
{
    /**
     * List semua klasifikasi (untuk dropdown create form)
     * GET /api/ejo/classifications
     */
    public function index()
    {
        $classifications = EjoClassification::with('type')
            ->get()
            ->map(fn ($c) => [
                'id'        => $c->id,
                'name'      => $c->name,
                'type_id'   => $c->type_id,
                'type_name' => $c->type?->name,
            ]);

        return response()->json($classifications);
    }

    /**
     * Struktur menu sidebar: Type → Classification → jumlah EJO
     * GET /api/ejo/menu
     *
     * Response:
     * [
     *   {
     *     "id": 1,
     *     "name": "Project",
     *     "classifications": [
     *       { "id": 1, "name": "Sipil", "count": 12, "url": "/ejo?classification=1" },
     *       { "id": 2, "name": "Mekanik", "count": 8, "url": "/ejo?classification=2" }
     *     ]
     *   },
     *   ...
     * ]
     */
    public function menu()
    {
        $types = EjoType::with(['classifications' => function ($q) {
            $q->withCount('tickets');
        }])->get();

        $menu = $types->map(fn ($type) => [
            'id'              => $type->id,
            'name'            => $type->name,
            'classifications' => $type->classifications->map(fn ($c) => [
                'id'    => $c->id,
                'name'  => $c->name,
                'count' => $c->tickets_count,
                'url'   => '/ejo?classification=' . $c->id,
            ]),
        ]);

        return response()->json($menu);
    }
}
