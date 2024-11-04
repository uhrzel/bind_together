<?php

namespace App\Http\Controllers;

use App\Enums\ActivityType;
use App\Models\ActivityRegistration;
use App\Models\User;
use App\Enums\UserTypeEnum;
use Illuminate\Http\Request;

class AuditionListController extends Controller
{
    /**
 * Handle the incoming request.
     */
    public function __invoke(Request $request)
    { {

            $coaches = User::where('user_type', UserTypeEnum::COACH)->get();

            return view('coaches.index', compact('coaches'));
        }
    }
}
