<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {   
        $buku = Buku::whereNull('deleted_at')->with('category')->orderBy('created_at', 'desc')->get();  
        $user = User::all()->count();
        $book = Buku::all()->count();
        $category = Kategori::all()->count();
        return view('dashboard', compact('buku', 'user','book','category'));
    }
}
