<form action="{{ route('cash-journals.in.add_to_cart') }}" method="POST">
    @csrf
    <input type="hidden" name="incoming_id" value="{{ $model->id }}">
    <button type="submit" class="btn btn-xs btn-primary" title="move to cart"><i class="fas fa-arrow-down"></i></button>
</form>