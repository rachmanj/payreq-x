<form action="{{ route('cashier.approveds.auto_outgoing', $model->id) }}" method="POST">
  @csrf @method('PUT')
  <button type="submit" class="btn btn-xs btn-success {{ $model->outgoings->count() > 0 ? 'disabled' : '' }}" onclick="return confirm('Are You sure You want to pay this payreq?')">auto</button>
  <a href="{{ route('cashier.approveds.pay', $model->id) }}" class="btn btn-xs btn-info">pay</a>
</form>  

