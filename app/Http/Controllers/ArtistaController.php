<?php

namespace App\Http\Controllers;

use App\Models\Artista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArtistaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $artistas = Artista::get()->where('estado', 1);
        return view('admin.artistas.index', compact('artistas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        $validator = Validator::make($request->all(), ['nombre' => 'required', 'descripcion' => 'required']);

        if ($validator->fails()) {
            // Si la validación falla, devuelve un estado HTTP 422 (Entidad No Procesable)
            // junto con los mensajes de error.
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // // Si la validación tiene éxito, crea el artista
        $artista = Artista::create($request->all());

        if ($request->file('imagen')) {
            $artista->imagen = $request->file('imagen')->store('artistas', 'public');
            $artista->save();
        }


        // Devuelve una respuesta JSON con un mensaje de éxito.
        return response()->json([
            'status' => 'success',
            'message' => 'Artista registrado correctamente.',
            'data' => $artista // Opcionalmente, puedes enviar los datos del artista creada.
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Artista  $artista
     * @return \Illuminate\Http\Response
     */
    public function show(Artista $artista)
    {
        return view('admin.artistas.show', compact('artista'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Artista  $artista
     * @return \Illuminate\Http\Response
     */
    public function edit(Artista $artista)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Artista  $artista
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Artista $artista)
    {



        // // Validar los datos recibidos del formulario
        // $request->validate([
        //     'nombre' => 'required|string',
        //     'descripcion' => 'required|string',
        //     'imagen' => 'image|mimes:jpeg,png,jpg,gif|max:2048' // Ajusta según tus requisitos
        // ]);

        // // Buscar el artista por su ID
        // $artista = Artista::findOrFail($artista->id);

        // // Actualizar los campos del artista
        // $artista->nombre = $request->nombre;
        // $artista->descripcion = $request->descripcion;

        // // Si se cargó una nueva imagen, guardarla
        // if ($request->hasFile('imagen')) {
        //     // Eliminar la imagen anterior si existe
        //     // Asegúrate de tener un mecanismo para manejar el almacenamiento de archivos
        //     // y eliminar los archivos antiguos si se carga una nueva imagen
        //     // Ejemplo: Storage::delete($artista->imagen);

        //     // Guardar la nueva imagen
        //     $imagen = $request->file('imagen');
        //     $nombreImagen = time() . '.' . $imagen->getClientOriginalExtension();
        //     $imagen->move(public_path('imagenes'), $nombreImagen);
        //     $artista->imagen = $nombreImagen;
        // }

        // // Guardar los cambios en el artista
        // $artista->save();

        // // Devolver una respuesta
        // return response()->json(['mensaje' => 'Artista actualizado exitosamente'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Artista  $artista
     * @return \Illuminate\Http\Response
     */
    public function destroy(Artista $artista)
    {
        //
    }


    /***
     * Get id Artista
     */

    public function getArtistaData($id)
    {
        $artista = Artista::find($id);

        // Si el usuario existe, devuelve los datos en formato JSON
        if ($artista) {
            return response()->json([
                'success' => true,
                'artista' => $artista
            ]);
        } else {
            // Si el usuario no existe, devuelve un mensaje de error
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ]);
        }
    }
}
