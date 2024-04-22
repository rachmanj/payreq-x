<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Payreq;
use App\Models\Realization;
use App\Models\RealizationDetail;
use App\Models\User;
use Illuminate\Http\Request;

class MigrasiController extends Controller
{
    public function index()
    {
        $migrasi_data = $this->migrasiData();

        return response()->json([
            'migrasi_data' => $migrasi_data
        ]);
    }

    public function departments()
    {
        $departments = Department::all();

        return $departments;
    }

    public function migrasiData()
    {
        return [
            'payreqs' => $this->payreqs(),
            'realizations' => $this->realizations(),
            'realization_details' => $this->realizationDetails(),
        ];
    }

    public function getUserIds() // only users that have payreqs
    {
        // only users that have payreqs
        $users = User::select('id', 'name', 'username', 'project', 'department_id')
            ->withCount('payreqs')
            ->whereHas('payreqs')
            ->get();

        $usersIds = $users->pluck('id')->toArray();

        return $usersIds;
    }

    public function payreqs()
    {
        // only payreqs that belong to users with id in $usersIds
        $userIds = $this->getUserIds();
        $payreqs = Payreq::whereIn('user_id', $userIds)
            ->get();

        return $payreqs;
    }

    public function realizations()
    {
        $payreqIds = $this->payreqs()->pluck('id')->toArray();
        $realizations = Realization::whereIn('payreq_id', $payreqIds)
            ->get();

        return $realizations;
    }

    public function realizationDetails()
    {
        $realizations = $this->realizations();
        $realizationIds = $realizations->pluck('id')->toArray();
        $realizationDetails = RealizationDetail::whereIn('realization_id', $realizationIds)
            ->get();

        return $realizationDetails;
    }
}
