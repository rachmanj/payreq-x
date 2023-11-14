<?php

namespace App\Http\Controllers;

use App\Models\ApprovalPlan;
use App\Models\Equipment;
use App\Models\Payreq;
use App\Models\Realization;
use App\Models\RealizationDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserRealizationController extends Controller
{
    public function index()
    {
        // get user's payreqs that has no realization
        $user_payreqs = Payreq::where('user_id', auth()->user()->id)
            ->where('status', 'paid')
            ->whereDoesntHave('realization')
            ->get();

        $realization_no = app(ToolController::class)->generateDraftRealizationNumber();

        return view('user-payreqs.realizations.index', compact('user_payreqs', 'realization_no'));
    }

    public function create()
    {
        // 
    }

    public function show($id)
    {
        $realization = Realization::findOrFail($id);
        $realization_details = $realization->realizationDetails;
        $approved_at = new Carbon($realization->approved_at);

        $approval_plans = ApprovalPlan::where('document_id', $id)
            ->where('document_type', 'realization')
            ->get();

        $submit_at = new Carbon($realization->submit_at);

        $approval_plan_status = app(ApprovalPlanController::class)->approvalStatus();

        return view('user-payreqs.realizations.show', compact([
            'realization',
            'realization_details',
            'approved_at',
            'approval_plans',
            'submit_at',
            'approval_plan_status'
        ]));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'payreq_id' => 'required'
        ]);

        $payreq = Payreq::findOrFail($request->payreq_id);

        $realization = Realization::create([
            'nomor' => $request->realization_no,
            'payreq_id' => $request->payreq_id,
            'user_id' => auth()->user()->id,
            'project' => $payreq->project,
            'department_id' => $payreq->department_id,
            'status' => 'draft',
            // 'status' => 'pre-draft', //pre-draft, utk ber-jaga2 jika pada saat add details gagal terhubung dgn ark-fleet server, maka pre-draft akan dihapus
        ]);

        return redirect()->route('user-payreqs.realizations.add_details', $realization->id);
    }

    public function submit_realization(Request $request)
    {
        $realization = Realization::findOrFail($request->realization_id);

        // create approval plan
        $approval_plan = app(ApprovalPlanController::class)->create_approval_plan('realization', $realization->id);

        if ($approval_plan) {
            $realization->update([
                'status' => 'submitted',
            ]);

            return redirect()->route('user-payreqs.realizations.index')->with('success', 'Realization submitted');
        } else {
            return redirect()->route('user-payreqs.realizations.index')->with('error', 'Realization failed to submit');
        }
    }

    public function print($id)
    {
        $realization = Realization::findOrFail($id);
        $realization_details = $realization->realizationDetails;
        $approved_at = new Carbon($realization->approved_at);
        $terbilang = app(ToolController::class)->terbilang($realization_details->sum('amount'));
        $approvers = app(ToolController::class)->getApproversName($id, 'realization');

        return view('user-payreqs.realizations.print_pdf', compact([
            'realization',
            'realization_details',
            'approved_at',
            'terbilang',
            'approvers'
        ]));
    }

    public function cancel($realization_id)
    {

        $realization = Realization::where('id', $realization_id)->first();

        // delete realization details
        $realization->realizationDetails()->delete();

        // delete realization
        $realization->delete();

        return redirect()->route('user-payreqs.realizations.index')->with('success', 'Realization deleted');
    }

    public function add_details($realization_id)
    {
        $realization = Realization::findOrFail($realization_id);
        $realization_details = $realization->realizationDetails;
        // $equipments = app(ToolController::class)->getEquipments($realization->project);
        // $equipments = Equipment::where('project', $realization->project)->get();

        $roles = app(ToolController::class)->getUserRoles();

        if (in_array('superadmin', $roles) || in_array('admin', $roles)) {
            $equipments = Equipment::orderBy('unit_code', 'asc')->get();
        } else {
            $equipments = Equipment::where('project', auth()->user()->project)->orderBy('unit_code', 'asc')->get();
        }

        return view('user-payreqs.realizations.add_details', compact([
            'realization',
            'realization_details',
            // 'project_equipment', 
            'equipments'
        ]));
    }

    public function store_detail(Request $request)
    {
        $this->validate($request, [
            'description' => 'required',
            'amount' => 'required|numeric',
        ]);

        $realization = Realization::findOrFail($request->realization_id);

        $realization->realizationDetails()->create([
            'description' => $request->description,
            'amount' => $request->amount,
            'project' => $realization->project,
            'department_id' => $realization->department_id,
            'unit_no' => $request->unit_no,
            'type' => $request->type,
            'qty' => $request->qty,
            'uom' => $request->uom,
            'km_position' => $request->km_position,
        ]);

        return redirect()->route('user-payreqs.realizations.add_details', $realization->id);
    }

    public function delete_detail($realization_detail_id)
    {
        $realization_detail = RealizationDetail::findOrFail($realization_detail_id);

        $realization_detail->delete();

        return redirect()->route('user-payreqs.realizations.add_details', $realization_detail->realization_id)->with('success', 'Realization Detail deleted');
    }

    public function data()
    {
        // get user's roles
        $userRoles = app(UserController::class)->getUserRoles();
        $status_include = ['approved', 'revise', 'submitted', 'draft'];

        if (in_array('superadmin', $userRoles) || in_array('admin', $userRoles)) {
            $realizations = Realization::whereIn('status', $status_include)
                ->get();
        } else {
            $realizations = Realization::whereIn('status', $status_include)
                ->where('user_id', auth()->user()->id)
                ->get();
        }

        return datatables()->of($realizations)
            ->editColumn('nomor', function ($realization) {
                return '<a href="' . route('user-payreqs.realizations.show', $realization->id) . '">' . $realization->nomor . '</a>';
            })
            ->addColumn('payreq_no', function ($realization) {
                return $realization->payreq->nomor;
            })
            ->addColumn('amount', function ($realization) {
                return number_format($realization->realizationDetails->sum('amount'), 2, ',', '.');
            })
            ->editColumn('created_at', function ($realization) {
                return $realization->created_at->addHours(8)->format('d-M-Y H:i') . ' wita';
            })
            ->addColumn('days', function ($realization) {
                $diff = Carbon::now()->diffInDays(Carbon::parse($realization->created_at));
                return $diff;
            })
            ->editColumn('status', function ($realization) {
                if ($realization->status === 'submitted') {
                    return 'Waiting Approval';
                } else {
                    return ucfirst($realization->status);
                }
            })
            ->addColumn('action', 'user-payreqs.realizations.action')
            ->rawColumns(['action', 'nomor'])
            ->addIndexColumn()
            ->toJson();
    }

    /*
    * check if realization amount is different from payreq amount
    * if realization amount > payreq amount, then create a new payreq
    * if realization amount < payreq amount, then create a new incomming payreq
    */
    public function check_realization_amount($realization_id)
    {
        $realization = Realization::findOrFail($realization_id);
        $realization_amount = $realization->realizationDetails->sum('amount');
        $payreq_amount = $realization->payreq->amount;

        if ($realization_amount > $payreq_amount) {
            // create new payreq
            $new_payreq = app(PayreqAdvanceController::class)->create_new_payreq($realization_id);

            if ($new_payreq) {
                return redirect()->route('user-payreqs.realizations.index')->with('success', 'Realization amount is different from Payreq amount. New Payreq created');
            } else {
                return redirect()->route('user-payreqs.realizations.index')->with('error', 'Realization amount is different from Payreq amount. Failed to create new Payreq');
            }
        } elseif ($realization_amount < $payreq_amount) {
            // create new incomming payreq
            $new_incomming_payreq = app(IncomingController::class)->create_new_incomming_realization($realization_id);

            if ($new_incomming_payreq) {
                return redirect()->route('user-payreqs.realizations.index')->with('success', 'Realization amount is different from Payreq amount. New Incomming Payreq created');
            } else {
                return redirect()->route('user-payreqs.realizations.index')->with('error', 'Realization amount is different from Payreq amount. Failed to create new Incomming Payreq');
            }
        } else {
            return redirect()->route('user-payreqs.realizations.index')->with('success', 'Realization amount is equal to Payreq amount');
        }
    }
}
