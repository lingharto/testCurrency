<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index(Request $request)
    {
        return Currency::paginate();
    }

    public function show($id)
    {
        $currency = Currency::find($id);

        if(!$currency) {
            abort(response()->json(['error' => 'Not found'], 404));
        }

        return $currency;
    }
}
