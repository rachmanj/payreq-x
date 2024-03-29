<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Payreq;
use App\Models\Realization;
use App\Models\RealizationDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayreqReimburseController extends Controller
{
    public function create()
    {
        // $payreq_no = app(DocumentNumberController::class)->generate_draft_document_number(auth()->user()->project);
        $payreq_no = app(PayreqController::class)->generateDraftNumber();

        return view('user-payreqs.reimburse.create', compact('payreq_no'));
    }

    public function store(Request $request)
    {
        // $roles = app(ToolController::class)->getUserRoles();

        // if (in_array('superadmin', $roles) || in_array('admin', $roles)) {
        //     $equipments = Equipment::orderBy('unit_code', 'asc')->get();
        // } else {
        //     $equipments = Equipment::where('project', auth()->user()->project)->orderBy('unit_code', 'asc')->get();
        // }

        if (auth()->user()->project == '000H' || auth()->user()->project == 'APS' || auth()->user()->project == '001H') {
            $equipments = Equipment::orderBy('unit_code', 'asc')->get();
        } else {
            $equipments = Equipment::where('project', auth()->user()->project)->orderBy('unit_code', 'asc')->get();
        }

        // Create new Payreq with type 'reimburse'
        $payreq = Payreq::create([
            // 'nomor' => app(PayreqController::class)->generateDraftNumber(),
            'nomor' => app(DocumentNumberController::class)->generate_draft_document_number(auth()->user()->project),
            'type' => 'reimburse',
            'status' => 'draft',
            'remarks' => $request->remarks,
            'project' => auth()->user()->project,
            'department_id' => auth()->user()->department_id,
            'user_id' => auth()->user()->id,
        ]);

        // Create new Realization
        $realization = Realization::create([
            'payreq_id' => $payreq->id,
            'project' => $payreq->project,
            'department_id' => $payreq->department_id,
            'remarks' => $request->remarks,
            'user_id' => $payreq->user_id,
            // 'nomor' => app(ToolController::class)->generateDraftRealizationNumber(),
            'nomor' => app(DocumentNumberController::class)->generate_draft_document_number(auth()->user()->project),
            'status' => 'reimburse-draft',
        ]);

        return view('user-payreqs.reimburse.add_details', compact(['payreq', 'equipments', 'realization']));
    }

    public function edit($id)
    {
        $roles = app(ToolController::class)->getUserRoles();

        if (auth()->user()->project == '000H' || auth()->user()->project == 'APS' || auth()->user()->project == '001H') {
            $equipments = Equipment::orderBy('unit_code', 'asc')->get();
        } else {
            $equipments = Equipment::where('project', auth()->user()->project)->orderBy('unit_code', 'asc')->get();
        }

        $payreq = Payreq::findOrFail($id);
        $realization = Realization::where('payreq_id', $payreq->id)->first();

        return view('user-payreqs.reimburse.add_details', compact(['payreq', 'equipments', 'realization']));
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
            'nopol' => $request->nopol,
            'type' => $request->type,
            'qty' => $request->qty,
            'uom' => $request->uom,
            'km_position' => $request->km_position,
        ]);

        // update payreq amount is sum of realization details amount
        $payreq = Payreq::findOrFail($realization->payreq_id);
        $payreq->update([
            'amount' => $realization->realizationDetails()->sum('amount'),
        ]);

        return $this->edit($realization->payreq_id);
    }

    public function submit_payreq(Request $request)
    {
        $realization = Realization::findOrFail($request->realization_id);
        $payreq = Payreq::findOrFail($realization->payreq_id);

        // create approval plan
        $approval_plan = app(ApprovalPlanController::class)->create_approval_plan('payreq', $payreq->id);

        if ($approval_plan) {
            $payreq->update([
                'status' => 'submitted',
            ]);

            $realization->update([
                'status' => 'reimburse-submitted',
                'submit_at' => Carbon::now(),
                'editable' => 0,
                'deletable' => 0,
            ]);

            return redirect()->route('user-payreqs.index')->with('success', 'Payreq submitted successfully');
        } else {
            return redirect()->route('user-payreqs.index')->with('error', 'Payreq failed to submit');
        }
    }

    public function delete_detail(Request $request)
    {
        $realization = Realization::findOrFail($request->realization_id);

        $realization_detail = RealizationDetail::findOrFail($request->realization_detail_id);
        $realization_detail->delete();

        // update payreq amount is sum of realization details amount
        $payreq = Payreq::findOrFail($realization->payreq_id);
        $payreq->update([
            'amount' => $realization->realizationDetails()->sum('amount'),
        ]);

        return $this->edit($realization->payreq_id);
    }
}
