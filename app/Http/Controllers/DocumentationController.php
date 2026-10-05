<?php

namespace App\Http\Controllers;

use App\Support\Documentation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class DocumentationController extends Controller
{
    public function page(string $nom): View
    {
        return Documentation::vue($nom);
    }

    public function ancienneAdresse(string $nom): RedirectResponse
    {
        return redirect()->route($nom, status: 301);
    }
}
