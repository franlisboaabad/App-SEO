<?php

namespace App\Http\Controllers;

use App\Models\Invitado;
use App\Models\Registro;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Dompdf\Dompdf;
use Hamcrest\Core\AllOf;
use Illuminate\Support\Facades\View;


class InvitadoController extends Controller
{


    public function __construct()
    {
        $this->middleware('can:admin.invitados.index')->only('index');
        $this->middleware('can:admin.invitados.create')->only('create','store');
        $this->middleware('can:admin.invitados.edit')->only('edit','update');
        $this->middleware('can:admin.invitados.show')->only('show');
        $this->middleware('can:admin.invitados.destroy')->only('destroy');

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invitados = Invitado::get();
        return view('admin.invitados.index', compact('invitados'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Invitado  $invitado
     * @return \Illuminate\Http\Response
     */
    public function show(Invitado $invitado)
    {
      
        
    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Invitado  $invitado
     * @return \Illuminate\Http\Response
     */
    public function edit(Invitado $invitado)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invitado  $invitado
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invitado $invitado)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invitado  $invitado
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invitado $invitado)
    {
        //
    }


    public function registrar($invitadoId)
    {
        $invitado = Invitado::findOrFail($invitadoId);
        
        Registro::create(
            [
                'invitado_id' => $invitado->id,
                'codigo_qr' => $invitado->dni,
                'validado' => 1
            ]
        );

        return back()->with('success', 'Se registro al invitado correctamente');
    }

    public function validarQR($codigo)
    {
        $invitado = Invitado::find($codigo);
        return view('invitado', compact('invitado'));
    }


    public function generarPDF(Invitado $invitado)
    {
        $nombres = $invitado->nombre . ' ' . $invitado->apellidos;
        $invitadoId = $invitado->id; // Obtén el ID del invitado
        $urlRegistro = route('invitados.validar-qr', $invitadoId); // Genera la URL de registro utilizando el nombre de la ruta y el ID del invitado
        $qrCode = QrCode::size(400)->generate($urlRegistro);

        $data = [
            'titulo' => $nombres,
            'qrCode' => 'data:image/png;base64,' . base64_encode($qrCode),
        ];

        $pdf = new Dompdf();
        $pdf->loadHtml(View::make('pdf', $data)->render());
        $pdf->setPaper('A4');
        $pdf->render();
        $pdf->stream('ejemplo.pdf');
    }
}
