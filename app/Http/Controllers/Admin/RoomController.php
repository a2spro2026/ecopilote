<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        return view('admin.rooms.active', [
            'salles' => $this->demoActiveRooms(),
        ]);
    }

    /**
     * Données de démonstration — à remplacer par le live métier.
     *
     * @return list<array<string, mixed>>
     */
    private function demoActiveRooms(): array
    {
        return [];
    }
}
