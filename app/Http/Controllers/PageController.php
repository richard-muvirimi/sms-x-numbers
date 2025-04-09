<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class PageController extends Controller
{
    public function privacy()
    {
        return Inertia::render('Privacy');
    }

    public function developers()
    {
        return Inertia::render('Developers');
    }

    public function howItWorks()
    {
        return Inertia::render('HowItWorks');
    }
}
