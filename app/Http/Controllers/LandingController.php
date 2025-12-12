<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class LandingController extends Controller
{
    public function index() {
        Inertia::setRootView('layouts/app');

        return Inertia::render('Landing');
    }
}
