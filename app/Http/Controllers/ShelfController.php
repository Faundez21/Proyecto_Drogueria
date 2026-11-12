<?php

namespace App\Http\Controllers;

use App\Services\InventoryService;

use Illuminate\Http\Request;
use App\Models\Aisle;
use App\Models\Level;
use App\Models\Position;
use App\Models\Shelf;

class ShelfController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shelves = Shelf::with('aisle')
            ->withCount('levels')
            ->orderBy('id')
            ->get();

        // traer todos los pasillos para el select del formulario
        $aisles = Aisle::orderBy('name')->get();

        $counts = [
            'aisles' => $aisles->count(),
            'shelves' => $shelves->count(),
            'levels' => Level::count(),
            'positions' => Position::count(),
        ];

        return view('distribution.maintainers.shelf.index', compact('shelves', 'aisles', 'counts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'aisle_id' => 'required|exists:aisles,id',
            'capacidad' => 'nullable|integer|min:0|max:1000000',
        ]);

        if (Shelf::whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($request->name))])->exists()) {
            return redirect()->back()->withInput()->with('error', "Ya existe una estantería llamada «" . trim($request->name) . "». Cada estantería debe tener un nombre único.");
        }

        $datos = $request->only(['name', 'description', 'aisle_id', 'capacidad']);
        $datos['capacidad'] = (int) ($datos['capacidad'] ?? 0);

        Shelf::create($datos);

        return redirect()->route('shelf.index')->with('success', 'Estantería creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $shelf = Shelf::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'aisle_id' => 'required|exists:aisles,id',
            'capacidad' => 'nullable|integer|min:0|max:1000000',
        ]);

        if (Shelf::where('id', '!=', $shelf->id)->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($request->name))])->exists()) {
            return redirect()->back()->withInput()->with('error', "Ya existe otra estantería llamada «" . trim($request->name) . "». Cada estantería debe tener un nombre único.");
        }

        $datos = $request->only(['name', 'description', 'aisle_id', 'capacidad']);
        $datos['capacidad'] = (int) ($datos['capacidad'] ?? 0);

        $shelf->update($datos);

        $shelf->inventories()->update([
            'estante' => $shelf->name,
            'aisle_id' => $shelf->aisle_id,
        ]);

        return redirect()->route('shelf.index')->with('success', 'Estantería actualizada exitosamente.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $shelf = Shelf::findOrFail($id);

        $lotes = InventoryService::lotesEnUbicacion('estante', $shelf);
        if ($lotes > 0) {
            return redirect()->route('shelf.index')->with('error', "No se puede eliminar la estantería «{$shelf->name}» porque todavía tiene {$lotes} lote(s) con stock. Primero reubica esos productos.");
        }

        $shelf->delete();

        return redirect()->route('shelf.index')->with('success', 'Estantería eliminada correctamente');
    }
    public function updatePosition(Request $request, $id)
    {
        $request->validate([
            'pos_x' => 'required|numeric',
            'pos_z' => 'required|numeric',
        ]);

        $shelf = Shelf::findOrFail($id);
        $posX = (float) $request->pos_x;
        $posZ = (float) $request->pos_z;

        // Una estantería mide poco más de 4 unidades de ancho, así que si otra está más
                // cerca que eso en el plano se estarían pisando
        $minSeparacion = 4.5;
        $conflicto = Shelf::where('id', '!=', $shelf->id)
            ->whereNotNull('pos_x')
            ->whereNotNull('pos_z')
            ->get()
            ->first(function ($otro) use ($posX, $posZ, $minSeparacion) {
                return abs(((float) $otro->pos_x) - $posX) < $minSeparacion
                    && abs(((float) $otro->pos_z) - $posZ) < $minSeparacion;
            });

        if ($conflicto) {
            return response()->json([
                'success' => false,
                'message' => "No se puede colocar la estantería {$shelf->name} encima de {$conflicto->name}. Seleccione un espacio libre.",
            ], 422);
        }

        $shelf->update([
            'pos_x' => $posX,
            'pos_z' => $posZ,
        ]);

        return response()->json(['success' => true, 'message' => 'Posición actualizada correctamente']);
    }
}
