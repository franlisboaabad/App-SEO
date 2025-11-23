<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Equipo;
use App\Models\OrdenDeServicio;
use Illuminate\Http\Request;

class OrdenDeServicioController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:admin.ordenes.index')->only('index');
        $this->middleware('can:admin.ordenes.create')->only('create', 'store');
        $this->middleware('can:admin.ordenes.edit')->only('edit', 'update');
        $this->middleware('can:admin.ordenes.show')->only('show');
        $this->middleware('can:admin.ordenes.destroy')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ordenes = OrdenDeServicio::get();
        return view('admin.ordenes.index', compact('ordenes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $clientes = Cliente::get();
        $equipos = Equipo::get();

        return view('admin.ordenes.create', compact('clientes', 'equipos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        OrdenDeServicio::create($request->all());
        return redirect()->route('ordenes-de-servicio.index')->with('success', 'Orden registrado correctamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OrdenDeServicio  $ordenDeServicio
     * @return \Illuminate\Http\Response
     */
    public function show(OrdenDeServicio $ordenDeServicio)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OrdenDeServicio  $ordenDeServicio
     * @return \Illuminate\Http\Response
     */
    public function edit(OrdenDeServicio $orden)
    {

        // dd($orden);

        $clientes = Cliente::get();
        $equipos = Equipo::get();
        return view('admin.ordenes.edit', compact('clientes','equipos','orden'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OrdenDeServicio  $ordenDeServicio
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OrdenDeServicio $orden)
    {

        // dd($request->all());
        $orden->update($request->all());
        return redirect()->route('ordenes-de-servicio.index')->with('success', 'Orden editado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OrdenDeServicio  $ordenDeServicio
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrdenDeServicio $orden)
    {
        // dd($orden);
        $orden->delete();
        return back()->with('success', 'Orden eliminada correctamente.');
    }
}
