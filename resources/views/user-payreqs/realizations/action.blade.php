<a href="{{ route('user-payreqs.realizations.add_details', $model->id) }}" class="btn btn-xs btn-warning {{ $model->editable === 0 ? 'disabled' : '' }}">edit</a>

@if (!$model->realizationDetails->count() > 0 || $model->status === 'rejected')
<form action="{{ route('user-payreqs.realizations.destroy', $model->id) }}" method="POST" class="d-inline">
    @csrf @method('DELETE')
    <button type="submit" class="btn btn-xs btn-danger d-inline" onclick="return confirm('Are you sure you want delete this record? This action will also DELETE its realization details!!')" {{ $model->deletable === 0 ? 'disabled' : '' }}>delete</button>
</form>
@endif
<a href="{{ route('user-payreqs.realizations.print', $model->id) }}" class="btn btn-xs btn-info {{ $model->printable ? '' : 'disabled' }}" target="_blank">print</a>
