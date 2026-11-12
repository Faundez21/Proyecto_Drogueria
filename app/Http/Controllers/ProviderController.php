<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use App\Support\Rut;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;

class ProviderController extends Controller
{
    public function index(Request $request)
    {
        $query = Provider::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
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
            if ($sort == 'recent')
                $query->orderBy('created_at', 'desc');
            if ($sort == 'oldest')
                $query->orderBy('created_at', 'asc');
            if ($sort == 'az')
                $query->orderBy('name', 'asc');
            if ($sort == 'za')
                $query->orderBy('name', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $total = $query->count();
        $activos = (clone $query)->where('status', 'activo')->count();
        $porcentajeActivos = $total > 0 ? round(($activos / $total) * 100) : 0;

        $mesActual = Carbon::now()->month;
        $anioActual = Carbon::now()->year;
        $agregadosMes = Provider::whereMonth('created_at', $mesActual)
            ->whereYear('created_at', $anioActual)
            ->count();

        $mesesNombres = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];
        $nombreMesActual = $mesesNombres[$mesActual] . ' ' . $anioActual;

        $perPage = $request->input('per_page', 10);
        $providers = $query->paginate($perPage)->withQueryString();

        // Lista reducida para el autocompletado del buscador
        $allProviders = Provider::select('name', 'rut')->get();

        return view('providers.index', compact(
            'providers',
            'total',
            'activos',
            'porcentajeActivos',
            'agregadosMes',
            'nombreMesActual',
            'mesesNombres',
            'allProviders',
            'perPage'
        ));
    }

    /** Categorías de proveedor permitidas (las mismas del formulario). */
    private const CATEGORIAS = [
        'Fármacos y medicamentos',
        'Insumos clínicos',
        'Equipamiento mayor',
        'Dispositivos médicos',
    ];

    /**
     * Regla de validación del RUT: dígito verificador correcto y que no esté registrado
     * en otro proveedor, aunque esté escrito con otro formato (con o sin puntos).
     */
    private function reglaRut(?int $ignorarId = null): array
    {
        return ['required', 'string', 'max:20', function ($attribute, $value, $fail) use ($ignorarId) {
            if (! Rut::esValido($value)) {
                $fail('El RUT ingresado no es válido (revisa el dígito verificador).');

                return;
            }
            $limpio = Rut::limpiar($value);
            $duplicado = Provider::query()
                ->when($ignorarId, fn ($q) => $q->where('id', '!=', $ignorarId))
                ->get(['id', 'rut'])
                ->first(fn ($p) => Rut::limpiar($p->rut) === $limpio);
            if ($duplicado) {
                $fail('Este RUT ya está registrado en otro proveedor.');
            }
        }];
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rut' => $this->reglaRut(),
            'name' => 'required|string|max:255',
            'category' => ['required', 'string', Rule::in(self::CATEGORIAS)],
            'email' => 'required|email|max:255',
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9\s\-()]{8,20}$/'],
            'business_line' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
        ], [
            'rut.required' => 'El RUT es obligatorio.',
            'rut.unique' => 'Este RUT ya está registrado.',
            'rut.min' => 'El RUT debe tener al menos 9 caracteres.',
            'rut.max' => 'El RUT no puede superar los 20 caracteres.',
            'name.required' => 'El nombre del proveedor es obligatorio.',
            'category.required' => 'Debe seleccionar una categoría.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingrese una dirección de correo electrónico válida.',
            'category.in' => 'Seleccione una categoría válida de la lista.',
            'phone.regex' => 'El teléfono solo puede contener números, espacios, guiones y el signo +.',
        ]);

        $validated['rut'] = Rut::formatear($validated['rut']);

        Provider::create($validated);
        return response()->json(['success' => true]);
    }

    public function update(Request $request, Provider $provider)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rut' => $this->reglaRut($provider->id),
            'email' => 'required|email|max:255',
            'category' => ['required', 'string', Rule::in(self::CATEGORIAS)],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9\s\-()]{8,20}$/'],
            'business_line' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'status' => 'nullable|in:activo,inactivo',
        ], [
            'category.in' => 'Seleccione una categoría válida de la lista.',
            'phone.regex' => 'El teléfono solo puede contener números, espacios, guiones y el signo +.',
            'rut.required' => 'El RUT es obligatorio.',
            'rut.min' => 'El RUT debe tener al menos 9 caracteres.',
            'rut.unique' => 'Este RUT ya está registrado.',
            'name.required' => 'El nombre del proveedor es obligatorio.',
            'category.required' => 'Debe seleccionar una categoría.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingrese una dirección de correo electrónico válida.',
        ]);

        $validated['rut'] = Rut::formatear($validated['rut']);
        if (empty($validated['status'])) {
            unset($validated['status']);
        }

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
