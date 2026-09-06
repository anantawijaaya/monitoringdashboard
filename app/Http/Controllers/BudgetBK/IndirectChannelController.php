<?php

namespace App\Http\Controllers\BudgetBK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndirectChannelController extends Controller
{
    /**
     * Display a listing of the resource for Program Indirect Channel.
     */
    public function index()
    {
        return view('budget-bk.indirect-channel.index');
    }
}
