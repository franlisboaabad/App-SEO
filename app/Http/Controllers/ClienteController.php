<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:admin.clientes.index')->only('index'); // url internas php.route
        $this->middleware('can:admin.clientes.create')->only('create', 'store');
        $this->middleware('can:admin.clientes.edit')->only('edit', 'update');
        $this->middleware('can:admin.clientes.show')->only('show');
        $this->middleware('can:admin.clientes.destroy')->only('destroy');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clientes = Cliente::get();
        return view('admin.clientes.index', compact('clientes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.clientes.create');
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
            'nombre' => 'required',
            'apellidos' => 'required',
            'dni' => 'required|unique:clientes,dni',
            'celular' => 'required',
            'email' => 'required|email|unique:clientes,email',
            'direccion' => 'required',
            'nombre_tienda' => 'required',
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
        $cliente = Cliente::create($request->all());


        // Devuelve una respuesta JSON con un mensaje de éxito.
        return response()->json([
            'status' => 'success',
            'message' => 'Cliente registrado correctamente.',
            'data' => $cliente // Opcionalmente, puedes enviar los datos del cliente creado.
        ]);


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function show(Cliente $cliente)
    {
        return view('admin.clientes.show', compact('cliente'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function edit(Cliente $cliente)
    {
        //
        return view('admin.clientes.edit', compact('cliente'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cliente $cliente)
    {
         // Validación
         $validator = Validator::make($request->all(), [
            'nombre_empresa' => 'required',
            'nombre' => 'required',
            'apellidos' => 'required',
            'dni' => 'required|unique:clientes,dni,' . $cliente->id, // Verifica la unicidad del DNI excluyendo el cliente actual
            'celular' => 'required',
            'email' => 'required|email|unique:clientes,email,' . $cliente->id, // Corregido para email
            'direccion' => 'required',
        ]);

        if ($validator->fails()) {
            // Si la validación falla, devuelve un estado HTTP 422 (Entidad No Procesable)
            // junto con los mensajes de error.
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $cliente->update($request->all());

         // Devuelve una respuesta JSON con un mensaje de éxito.
         return response()->json([
            'status' => 'success',
            'message' => 'Cliente editado correctamente.',
            'data' => $cliente // Opcionalmente, puedes enviar los datos del cliente creado.
        ]);



    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cliente $cliente)
    {

        $cliente->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Cliente eliminado correctamente',
            'data' => $cliente // Opcionalmente, puedes enviar los datos del cliente creado.
        ]);

    }
}
