<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ProviderController extends Controller
{
    public function index(Request $request)
    {
        $query = Provider::query();

        // 1. Filtros de búsqueda
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('rut', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        if ($request->filled('sort')) {
            $sort = $request->sort;
            if ($sort == 'recent') $query->orderBy('created_at', 'desc');
            if ($sort == 'oldest') $query->orderBy('created_at', 'asc');
            if ($sort == 'az') $query->orderBy('name', 'asc');
            if ($sort == 'za') $query->orderBy('name', 'desc');
        } else {
            $query->orderBy('created_at', 'desc'); 
        }

        // 2. Cálculo de KPIs
        $total = $query->count();
        $activos = (clone $query)->where('status', 'activo')->count();
        $porcentajeActivos = $total > 0 ? round(($activos / $total) * 100) : 0;
        
        $mesActual = Carbon::now()->month;
        $anioActual = Carbon::now()->year;
        $agregadosMes = Provider::whereMonth('created_at', $mesActual)
                                ->whereYear('created_at', $anioActual)
                                ->count();

        $mesesNombres = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        $nombreMesActual = $mesesNombres[$mesActual] . ' ' . $anioActual;

        // 3. Paginación y registros por página (10 por defecto)
        $perPage = $request->input('per_page', 10);
        $providers = $query->paginate($perPage)->withQueryString();

        // 4. Lista ligera para Autocomplete
        $allProviders = Provider::select('name', 'rut')->get();

        return view('providers.index', compact(
            'providers', 'total', 'activos', 'porcentajeActivos', 
            'agregadosMes', 'nombreMesActual', 'mesesNombres', 'allProviders', 'perPage'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rut'            => 'required|string|max:20|unique:providers',
            'name'           => 'required|string|max:255',
            'category'       => 'required|string',
            'email'          => 'required|email|max:255',
            'phone'          => 'nullable|string|max:20',
        ]);

        Provider::create($validated);
        return response()->json(['success' => true]);
    }

    public function update(Request $request, Provider $provider)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rut' => 'required|string|max:20|unique:providers,rut,' . $provider->id,
            'email' => 'required|email|max:255',
            'category' => 'required|string',
            'phone' => 'nullable|string|max:20',
        ]);

        $provider->update($validated);
        return response()->json(['success' => true]);
    }

    public function toggleStatus(Provider $provider)
    {
        $provider->status = $provider->status === 'activo' ? 'inactivo' : 'activo';
        $provider->save();
        return response()->json(['success' => true, 'new_status' => $provider->status]);
    }
}