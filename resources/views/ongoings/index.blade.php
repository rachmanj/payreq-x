@extends('templates.main')

@section('title_page')
    Approved Payment Request
@endsection

@section('breadcrumb_title')
    approved
@endsection

@section('content')
<div class="row">
  <div class="col-12">

    <div class="card">
      <div class="card-header">
        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#new-payreq">
          <i class="fas fa-plus"></i> New Payreq
        </button>
        {{-- <a href="{{ route('ongoings.create_advance') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Payreq Advance</a>
        <a href="{{ route('ongoings.create_other') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Payreq Other</a> --}}
      </div>
      <!-- /.card-header -->
      <div class="card-body">
        <table id="ongoings" class="table table-bordered table-striped">
          <thead>
          <tr>
            <th>#</th>
            <th>Payreq No</th>
            <th>Type</th>
            <th>Status</th>
            <th>Created at</th>
            <th>IDR</th>
            {{-- <th>Days</th> --}}
            <th></th>
          </tr>
          </thead>
        </table>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
  <!-- /.col -->
</div>
<!-- /.row -->

{{-- MODAL NEW PAYREQ --}}
<div class="modal fade" id="new-payreq">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Select Payment Request Type</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body justify-content-between">
          <a href="{{ route('ongoings.create_advance') }}" class="btn btn-outline-success btn-lg btn-block">Advance</a>
          <a href="{{ route('ongoings.create_other') }}" class="btn btn-outline-primary btn-lg btn-block">Other</a>
      </div>
    </div> <!-- /.modal-content -->
  </div> <!-- /.modal-dialog -->
</div> <!-- /.modal -->

@endsection

@section('styles')
    <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('adminlte/plugins/datatables/css/datatables.min.css') }}"/>
@endsection

@section('scripts')
    <!-- DataTables  & Plugins -->
<script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables/datatables.min.js') }}"></script>

<script>
  $(function () {
    $("#ongoings").DataTable({
      processing: true,
      serverSide: true,
      ajax: '{{ route('ongoings.data') }}',
      columns: [
        {data: 'DT_RowIndex', orderable: false, searchable: false},
        {data: 'payreq_no'},
        {data: 'type'},
        {data: 'status'},
        {data: 'created_at'},
        {data: 'amount'},
        // {data: 'days'},
        // {data: 'action', orderable: false, searchable: false},
      ],
      fixedHeader: true,
      columnDefs: [
              {
                "targets": [5],
                "className": "text-right"
              },
            ]
    })
  });
</script>
<script>
  $(function () {
    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })
  }) 
</script>
@endsection