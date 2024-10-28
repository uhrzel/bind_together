<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Sport;
use Illuminate\Http\Request;

class ProfileCompletionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return view('auth.profile-completion', ['sports' => Sport::all(), 'organizations' => Organization::class]);
    }
}
