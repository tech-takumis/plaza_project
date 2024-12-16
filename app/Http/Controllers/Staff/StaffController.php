<?php

namespace App\Http\Controllers\Staff;

use App\Events\DeleteOfficialEvent;
use App\Models\User;
use App\Models\Staff;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Events\NewOfficialNotification;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    public function show_officials(){
        $staff = Auth::guard('staff')->user();

        return  DB::select('select * from get_filtered_staff(?)',[$staff->id]);

    }

    public function destroy_staff($id)
    {
        $staff = Staff::findOrFail($id);

        Broadcast(new DeleteOfficialEvent($staff));

        $staff->delete();

        return response()->noContent();
    }
}
