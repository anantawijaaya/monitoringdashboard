<?php

namespace App\Http\Controllers\BudgetBK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DirectSalesController extends Controller
{
    /**
     * Display a listing of the resource for Program Direct Sales.
     */
    public function index()
    {
        return view('budget-bk.direct-sales.index');
    }
}
