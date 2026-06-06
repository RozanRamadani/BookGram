<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Buku;
use Illuminate\Http\Request;

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
        $totalKategori = Kategori::count();
        $totalBuku = Buku::count();
        $recentBukus = Buku::with('kategori')->latest()->take(5)->get();

        return view('home', compact('totalKategori', 'totalBuku', 'recentBukus'));
    }
}
