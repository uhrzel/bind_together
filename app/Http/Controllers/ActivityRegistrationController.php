<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityRegistrationRequest;
use App\Models\Activity;
use App\Models\ActivityRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\HtmlString;

class ActivityRegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('student.activity.index', [
            'activities' => Activity::with('sport')->where('status', 1)->where('is_deleted', 0)->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreActivityRegistrationRequest $request)
    {
        $data = [
            'activity_id' => $request->activity_id,
            'height' => $request->height,
            'weight' => $request->weight,
            'contact_person' => $request->contact_person,
            'emergency_contact' => $request->emergency_contact,
            'relationship' => $request->relationship,
            'user_id' => Auth::id(),
        ];

        $fileFields = ['certificate_of_registration', 'parent_consent', 'other_file', 'photo_copy_id'];

        // Handle file uploads
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $filePath = $request->file($field)->store('activity_files', 'public');
                $data[$field] = $filePath;
            }
        }

        ActivityRegistration::create($data);

        alert()->success('Activity registration created successfully.');

        return redirect()->route('activity-registration.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(ActivityRegistration $activityRegistration)
    {
        $activityRegistration->load('sport');

        return response()->json($activityRegistration);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ActivityRegistration $activityRegistration)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $activityRegistrationId)
    {
        $act = ActivityRegistration::with(['user', 'activity'])->find($activityRegistrationId);
        $act->update(['status' => $request->status]);

        if ((int)$request->status === 1) {
            $user = Auth::user();
            Mail::send([], [], function ($message) use($act, $user) {
                $htmlContent = '
                <p>DEAR '.$act["user"]["firstname"] .' '. $act["user"]["lastname"] .',</p>
                <p>WE ARE PLEASED TO INFORM YOU THAT YOUR REGISTRATION FOR '.$act["activity"]["title"].' HAS BEEN APPROVED! WE ARE EXCITED TO HAVE YOU ON BOARD AND LOOK FORWARD TO SEEING YOU PARTICIPATE.</p>
                <p>PLEASE STAY TUNED FOR FURTHER UPDATES AND INFORMATION.</p>
                <p>BEST REGARDS,<br>
                '.$user["firstname"] .' '. $user["lastname"] .'<br>
                ADMIN</p>';
        
                $message->to($act["user"]["email"])
                        ->subject('REGISTRATION APPROVED - WELCOME TO '.$act["activity"]["title"].'!')
                        ->html($htmlContent); 
            });

        }

        if ((int)$request->status === 2) {
            $user = Auth::user();
            Mail::send([], [], function ($message) use($act, $user) {
                $htmlContent = '
                <p>THANK YOU FOR YOUR INTEREST IN PARTICIPATING IN '.$act["activity"]["title"].'.</p>
                <p>UNFORTUNATELY, WE REGRET TO INFORM YOU THAT YOUR REGISTRATION HAS NOT BEEN APPROVED AT THIS TIME.</p>
                <p><strong>REASON FOR DECLINING:</strong><br>
                WE OBSERVE THAT YOUR HEIGHT AND WEIGHT DID NOT MEET OUR CRITERIA TO JOIN THE COMPETITION.</p>
                <p>WE APPRECIATE YOUR ENTHUSIASM AND ENCOURAGE YOU TO APPLY FOR FUTURE ACTIVITIES. IF YOU HAVE ANY QUESTIONS OR WOULD LIKE FEEDBACK, PLEASE FEEL FREE TO REACH OUT.</p>
                <p>BEST REGARDS,<br>
                '.$user["firstname"].' '.$user["lastname"].'<br>
                ADMIN</p>';
                
                $message->to($act["user"]["email"])
                        ->subject('REGISTRATION STATUS - WELCOME TO '.$act["activity"]["title"].'!')
                        ->html($htmlContent); 
            });

        }
        
        alert()->success('Updated successfully');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ActivityRegistration $activityRegistration)
    {
        //
    }
}
