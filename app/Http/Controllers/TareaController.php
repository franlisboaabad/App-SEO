<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use App\Models\Actividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TareaController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:admin.tareas.index')->only('index'); // url internas php.route
        $this->middleware('can:admin.tareas.create')->only('create', 'store');
        $this->middleware('can:admin.tareas.edit')->only('edit', 'update');
        $this->middleware('can:admin.tareas.show')->only('show');
        $this->middleware('can:admin.tareas.destroy')->only('destroy');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tareas = Tarea::get();
        return view('admin.tareas.index', compact('tareas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $actividades = Actividad::get();

        return view('admin.tareas.create', compact('actividades'));
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
            'actividad_id' => 'required',
            'nombre_tarea' => 'required',
            'fecha_inicio' => 'required',
            'fecha_presentacion' => 'required',
            'responsable' => 'required',
            'estado_de_tarea' => 'required|in:no iniciado,en proceso,en revisión,culminado'
        ]);

        if ($validator->fails()) {
            // Si la validación falla, devuelve un estado HTTP 422 (Entidad No Procesable)
            // junto con los mensajes de error.
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Si la validación tiene éxito, crea el nuevo cliente.
        $tarea = Tarea::create($request->all());


        // Devuelve una respuesta JSON con un mensaje de éxito.
        return response()->json([
            'status' => 'success',
            'message' => 'Tarea registrado correctamente.',
            'data' => $tarea // Opcionalmente, puedes enviar los datos del cliente creado.
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tarea  $tarea
     * @return \Illuminate\Http\Response
     */
    public function show(Tarea $tarea)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tarea  $tarea
     * @return \Illuminate\Http\Response
     */
    public function edit(Tarea $tarea)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tarea  $tarea
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tarea $tarea)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tarea  $tarea
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tarea $tarea)
    {
        //
    }
}
