@extends('layouts.app')

@section('content')
<div class="dashboard-section">
    <div class="card col-lg-7 mx-auto">
        <div class="card-header"><h5 class="card-title mb-0">Bank Withdrawal</h5></div>
        <div class="card-body">
            <p class="text-muted">Enter your bank details. Your request will remain pending while it is reviewed.</p>
            <form action="{{ route('user.withdraw.money') }}" method="POST">
                @csrf
                <input type="hidden" name="address" value="{{ $address }}">
                <input type="hidden" name="method_code" value="{{ $method->id }}">
                <input type="hidden" name="currency" value="{{ $method->currency }}">
                <div class="mb-3"><label>Withdrawal Amount</label><input class="form-control" type="number" name="amount" min="{{ $method->min_limit }}" max="{{ $method->max_limit }}" step="0.01" required></div>
                @foreach($method->user_data ?? [] as $key => $field)
                    <div class="mb-3">
                        <label>{{ $field->field_level }} @if($field->validation === 'required')<span class="text-danger">*</span>@endif</label>
                        @if($field->type === 'textarea')
                            <textarea class="form-control" name="{{ $key }}" rows="3" @if($field->validation === 'required') required @endif></textarea>
                        @else
                            <input class="form-control" type="text" name="{{ $key }}" @if($field->validation === 'required') required @endif>
                        @endif
                    </div>
                @endforeach
                <button class="btn btn-success" type="submit">Continue to Review</button>
                <a class="btn btn-link" href="{{ route('user.withdraw', $address) }}">Other withdrawal methods</a>
            </form>
        </div>
    </div>
</div>
@endsection
