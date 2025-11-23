<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Cliente;
use App\Models\TipoEquipo;
use App\Models\ImagenEquipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EquipoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $equipos = Equipo::get();
        return view('admin.equipos.index', compact('equipos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $clientes = Cliente::get();
        $tipo_equipos = TipoEquipo::get();

        return view('admin.equipos.create', compact('clientes', 'tipo_equipos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         Equipo::create($request->all());
         return redirect()->route('equipos.index')->with('success', 'Equipo registrado correctamente');

        // $equipo = Equipo::create($request->all());
        // $lastInsertedId = $equipo->id;

        // // if ($lastInsertedId) {
        // //     if ($request->file('file')) {
        // //         $path = $request->file('file')->store('equipos', 'public');

        // //         $equipmentPicture = ImagenEquipo::create([
        // //             'equipo_id' => $lastInsertedId,
        // //             'file' => $path
        // //         ]);
        // //     }
        // // }

        // // return redirect()->route('equipos.index')->with('success', 'Equipo registrado correctamente');

        // $request->validate([
        //     // Validaciones para otros campos
        //     'imagenes.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        // ]);

        // if ($request->hasfile('imagenes')) {
        //     foreach ($request->file('imagenes') as $file) {
        //         $path = $file->store('equipos', 'public');
        //         // Aquí, guarda el $path junto con cualquier otra información relevante en tu base de datos

        //         $equipmentPicture = ImagenEquipo::create([
        //             'equipo_id' => $lastInsertedId,
        //             'file' => $path
        //         ]);
        //     }
        // }

        // return redirect()->route('equipos.index')->with('success', 'Equipo registrado correctamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Equipo  $equipo
     * @return \Illuminate\Http\Response
     */
    public function show(Equipo $equipo)
    {
        return view('admin.equipos.show', compact('equipo'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Equipo  $equipo
     * @return \Illuminate\Http\Response
     */
    public function edit(Equipo $equipo)
    {
        $clientes = Cliente::get();
        $tipo_equipos = TipoEquipo::get();
        return view('admin.equipos.edit', compact('equipo', 'clientes', 'tipo_equipos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Equipo  $equipo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Equipo $equipo)
    {
        $equipo->update($request->all());
        return redirect()->route('equipos.index')->with('success', 'Equipo editado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Equipo  $equipo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Equipo $equipo)
    {
        //
    }


    /**
     *
     * Imagen Equipo
     */

    public function deleteImagen($equipo, $id)
    {
        $equipo = Equipo::findOrFail($equipo);
        $imagen = $equipo->imagenes()->findOrFail($id);


        // Eliminar la imagen
        Storage::delete($imagen->file); // Elimina el archivo de imagen del disco
        $imagen->delete(); // Elimina el registro de la base de datos

        return back()->with('success', 'Imagen eliminada correctamente.');

    }
}
