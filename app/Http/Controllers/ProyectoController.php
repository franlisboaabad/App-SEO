<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProyectoController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:admin.proyectos.index')->only('index');
        $this->middleware('can:admin.proyectos.create')->only('create', 'store');
        $this->middleware('can:admin.proyectos.edit')->only('edit', 'update');
        $this->middleware('can:admin.proyectos.show')->only('show');
        $this->middleware('can:admin.proyectos.destroy')->only('destroy');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $proyectos = Proyecto::get();
        // $usuarios = User::get()->where('user_id',);
        return view('admin.proyectos.index', compact('proyectos'));
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
        $usuarios = User::get();
        return view('admin.proyectos.create', compact('clientes','usuarios'));
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
            'cliente_id' => 'required',
            'nombre_proyecto' => 'required',
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
        $proyecto = Proyecto::create($request->all());

        $usuariosSeleccionados = $request->input('usuarios');
        $proyecto->usuarios()->attach($usuariosSeleccionados);

        // $usuariosIds = [1, 2, 3]; // IDs de los usuarios asignados
        // $proyecto->usuarios()->attach($usuariosIds);

        // Devuelve una respuesta JSON con un mensaje de éxito.
        return response()->json([
            'status' => 'success',
            'message' => 'Proyecto registrado correctamente.',
            'data' => $proyecto // Opcionalmente, puedes enviar los datos de la actividad creada.
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function show(Proyecto $proyecto)
    {
        $usuariosAsignados = $proyecto->usuarios;


        return view('admin.proyectos.show', compact('proyecto', 'usuariosAsignados'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function edit(Proyecto $proyecto)
    {
        $clientes = Cliente::get();
        return view('admin.proyectos.edit', compact('clientes', 'proyecto'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Proyecto $proyecto)
    {
        // Validación
        $validator = Validator::make($request->all(), [
            'cliente_id' => 'required',
            'nombre_proyecto' => 'required',
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
        $proyecto->update($request->all());


        // Devuelve una respuesta JSON con un mensaje de éxito.
        return response()->json([
            'status' => 'success',
            'message' => 'Proyecto editado correctamente.',
            'data' => $proyecto // Opcionalmente, puedes enviar los datos de la actividad creada.
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Proyecto  $proyecto
     * @return \Illuminate\Http\Response
     */
    public function destroy(Proyecto $proyecto)
    {
        //
    }
}
