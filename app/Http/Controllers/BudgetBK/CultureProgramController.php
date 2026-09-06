<?php

namespace App\Http\Controllers\BudgetBK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CultureProgramController extends Controller
{
    /**
     * Display a listing of the resource for Culture Program.
     */
    public function index()
    {
        return view('budget-bk.culture-program.index');
    }
}
