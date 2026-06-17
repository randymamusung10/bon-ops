<?php

namespace App\Http\Controllers\Business\Finance\GeneralJournal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GeneralJournalController extends Controller
{
    public function index()
    {
        return view('pages.business.finance.journal.index');
    }
}
