@extends('templates.main')

@section('title_page')
  RABs
@endsection

@section('breadcrumb_title')
    rabs
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">RABs BUC Sync</h3>
      </div>
      <div class="form-horizontal">
        <div class="card-body">
            <p style="color: blue;">This action is to synchronize RAB belongs to dnc user on old Payreq-Support with new Payreq-X.</p>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label">RABs Payreq-Support count: </label>
            <div class="col-sm-6">
              <input type="text" class="form-control" value="{{ $rab_count }}" readonly>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label">RABs Payreq-X count: </label>
            <div class="col-sm-6">
              <input type="text" class="form-control" value="{{ $local_count }}" readonly>
            </div>
          </div>
        </div>
        <div class="card-footer text-center">
          <a href="{{ route('rabs.sync.sync_rabs') }}" type="submit" class="btn btn-info" onclick="return confirm('Are you sure you want to sync?')" style="width: 60%">Synchronize</a>
        </div>
      </div>
    </div> 
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">Sync RAB ID on Realization Details</h3>
      </div>
      <div class="card-body">
        <p style="color: blue;">This action is to synchronize RAB ID in realization_details table and RAB ID in Payreqs belongs to DNC user.</p>
      </div>
      <div class="card-footer text-center">
        <a href="{{ route('rabs.sync.update_rab') }}" type="submit" class="btn btn-info" onclick="return confirm('Are you sure you want to sync?')" style="width: 60%">Synchronize RAB ID</a>
      </div>
    </div> 
  </div>
</div>

@endsection