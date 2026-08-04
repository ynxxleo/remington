@extends('layouts.app')
@section('content')
@if($mlm->installed == 1)
<div class="card">
    <div class="card-body">
        <div class="card-title">
            MLM Table Optimizer
        </div>
        <a href="{{ route('admin.mlm.regenerate') }}" class="btn btn-primary">Regenerate MLM Rows For Old Users</a>
    </div>
</div>
@endif
@endsection
