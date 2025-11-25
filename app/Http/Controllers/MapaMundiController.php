<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MapaMundiController extends Controller
{
    public function index()
    {
        return view('mapa.mundi');
    }
}
