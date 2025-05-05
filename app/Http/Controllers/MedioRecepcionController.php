<?php

namespace App\Http\Controllers;

use App\Models\MedioRecepcion;
use Illuminate\Http\Request;

class MedioRecepcionController extends Controller
{
    public function index() { return MedioRecepcion::all(); }

    public function store(Request $request) {
        return MedioRecepcion::create($request->validate(['nombre' => 'required|string|max:100']));
    }

    public function update(Request $request, MedioRecepcion $medio) {
        $medio->update($request->validate(['nombre' => 'required|string|max:100']));
        return $medio;
    }

    public function destroy(MedioRecepcion $medio) { $medio->delete(); return response()->noContent(); }
}
