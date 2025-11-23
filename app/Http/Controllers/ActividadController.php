<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Actividad;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ActividadController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:admin.actividad.index')->only('index'); // url internas php.route
        $this->middleware('can:admin.actividad.create')->only('create', 'store');
        $this->middleware('can:admin.actividad.edit')->only('edit', 'update');
        $this->middleware('can:admin.actividad.show')->only('show');
        $this->middleware('can:admin.actividad.destroy')->only('destroy');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $actividades = Actividad::get();

        return view('admin.actividades.index', compact('actividades'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $clientes = Cliente::get();
        $proyectos = Proyecto::get();

        return view('admin.actividades.create', compact('proyectos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validación
        $validator = Validator::make($request->all(), [
            'proyecto_id' => 'required',
            'actividad' => 'required',
            'fecha_facturacion' => 'required',
        ]);

        if ($validator->fails()) {
            // Si la validación falla, devuelve un estado HTTP 422 (Entidad No Procesable)
            // junto con los mensajes de error.
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // // Si la validación tiene éxito, crea la nueva actividad.
        $actividad = Actividad::create($request->all());


        // Devuelve una respuesta JSON con un mensaje de éxito.
        return response()->json([
            'status' => 'success',
            'message' => 'Actividad registrado correctamente.',
            'data' => $actividad // Opcionalmente, puedes enviar los datos de la actividad creada.
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Actividad  $actividad
     * @return \Illuminate\Http\Response
     */
    public function show(Actividad $actividad)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Actividad  $actividad
     * @return \Illuminate\Http\Response
     */
    public function edit(Actividad $actividad)
    {
        $proyectos = Proyecto::get();
        return view('admin.actividades.edit', compact('actividad', 'proyectos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Actividad  $actividad
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Actividad $actividad)
    {
        // Validación
        $validator = Validator::make($request->all(), [
            'proyecto_id' => 'required',
            'actividad' => 'required',
            'fecha_facturacion' => 'required',
        ]);

        if ($validator->fails()) {
            // Si la validación falla, devuelve un estado HTTP 422 (Entidad No Procesable)
            // junto con los mensajes de error.
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $actividad->update($request->all());

        // Devuelve una respuesta JSON con un mensaje de éxito.
        return response()->json([
            'status' => 'success',
            'message' => 'Actividad editado correctamente.',
            'data' => $actividad // Opcionalmente, puedes enviar los datos de la actividad creada.
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Actividad  $actividad
     * @return \Illuminate\Http\Response
     */
    public function destroy(Actividad $actividad)
    {
        $actividad->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Cliente eliminado correctamente',
            'data' => $actividad // Opcionalmente, puedes enviar los datos del cliente creado.
        ]);
    }
}
