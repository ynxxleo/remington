@extends('layouts.app')

@section('content')
<div class="dashboard-section">
    <div class="card col-lg-7 mx-auto">
        <div class="card-header"><h5 class="card-title mb-0">Bank Withdrawal</h5></div>
        <div class="card-body">
            <p class="text-muted">Enter your bank details. Your request will remain pending while it is reviewed.</p>
            <form action="{{ route('user.withdraw.money') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="address" value="{{ $address }}">
                <input type="hidden" name="method_code" value="{{ $method->id }}">
                <input type="hidden" name="currency" value="{{ $method->currency }}">
                <div class="mb-3"><label>Withdrawal Amount</label><input class="form-control" type="number" name="amount" min="{{ $method->min_limit }}" max="{{ $method->max_limit }}" step="0.01" required></div>
                @foreach($method->user_data ?? [] as $key => $field)
                    <div class="mb-3">
                        @php
                            $field = is_object($field) ? $field : (object) $field;
                            $fieldName = $field->field_name ?? $key;
                            $fieldLabel = $field->field_level ?? ucwords(str_replace('_', ' ', $fieldName));
                        @endphp
                        <label>{{ $fieldLabel }} @if(($field->validation ?? 'nullable') === 'required')<span class="text-danger">*</span>@endif</label>
                        @if(($field->type ?? 'text') === 'textarea')
                            <textarea class="form-control" name="{{ $key }}" rows="3" @if(($field->validation ?? 'nullable') === 'required') required @endif></textarea>
                        @elseif(($field->type ?? 'text') === 'file')
                            <input class="form-control" type="file" name="{{ $key }}" @if(($field->validation ?? 'nullable') === 'required') required @endif>
                        @else
                            <input class="form-control" type="text" name="{{ $key }}" @if(($field->validation ?? 'nullable') === 'required') required @endif>
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
