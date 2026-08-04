@extends('layouts.app')
@section('content')
<div class="row" id="table-hover-row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">{{ __('locale.Exchanges')}}</h4><div class="card-search"></div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover custom-data-bs-table">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">{{ __('locale.ID')}}</th>
                            <th scope="col">{{ __('locale.User')}}</th>
                            <th scope="col">{{ __('locale.Pair')}}</th>
                            <th scope="col">{{ __('locale.Amount')}}</th>
                            <th scope="col">{{ __('locale.Type')}}</th>
                            <th scope="col">{{ __('locale.Status')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($exchanges as $exchange)
                        <tr>
                            <td data-label="{{ __('locale.ID')}}">{{__($loop->iteration)}}</td>
                            <td data-label="{{ __('locale.User')}}"><a class="badge bg-info" href="{{ route('admin.users.detail', $exchange->user_id) }}">{{ $user->where('id',$exchange->user_id)->first()->username; }}</a></td>
                            <td data-label="{{ __('locale.Pair')}}">
                                @if($exchange->type == 1)
                                    <span class="fw-bold">{{$exchange->to}}/{{$exchange->from}}</span>
                                @elseif($exchange->type == 2)
                                    <span class="fw-bold">{{$exchange->from}}/{{$exchange->to}}</span>
                                @endif
                            </td>
                            <td data-label="{{ __('locale.Amount')}}">
                                <div> {{ __('locale.Amount')}}: <span class="fw-bold">{{ttz($exchange->amount_from)}}
                                    @if($exchange->type == 1){{$exchange->to}}@elseif($exchange->type == 2){{$exchange->from}}@endif
                                </span></div>
                                <div> {{ __('locale.Price Was')}}: <span class="fw-bold">{{ttz($exchange->price_was)}} USD</span></div>
                            </td>
                            <td data-label="{{ __('locale.Type')}}">
                                @if($exchange->type == 1)
                                    <span class="badge bg-success">{{ __('locale.Buy')}}</span>
                                @elseif($exchange->type == 2)
                                    <span class="badge bg-danger">{{ __('locale.Sell')}}</span>
                                @endif
                            </td>
                            <td data-label="{{ __('locale.Status')}}">
                                @if($exchange->status == 0)
                                    <span class="badge bg-primary">{{ __('locale.Pending')}}</span>
                                @elseif($exchange->status == 1)
                                    <span class="badge bg-success">{{ __('locale.Completed')}}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{__($empty_message) }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
        </div>
        <div class="mb-1">{{paginateLinks($exchanges) }}</div>
    </div>
</div>
@endsection
