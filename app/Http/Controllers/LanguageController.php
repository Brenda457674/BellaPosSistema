<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switch(string $locale)
    {
        $idiomasPermitidos = ['es', 'en', 'it'];

        if (!in_array($locale, $idiomasPermitidos, true)) {
            abort(404);
        }

        session(['locale' => $locale]);

        app()->setLocale($locale);

        return redirect()->back();
    }
}
