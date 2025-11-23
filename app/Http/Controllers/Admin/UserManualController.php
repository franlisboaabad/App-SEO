<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserManualController extends Controller
{
    /**
     * Mostrar manual de usuario
     */
    public function index()
    {
        return view('admin.user-manual.index');
    }
}
