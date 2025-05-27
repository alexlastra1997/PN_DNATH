<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProvinciaController extends Controller
{
     public function index()
    {
        

    return view('provincias.index');
    }
    public function show($id)
    {
        return view('provincias.show', ['id' => $id]);
    }
}
