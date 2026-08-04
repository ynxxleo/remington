@extends('layouts.app')
@section('page-style')

  <link rel="stylesheet" href="{{ asset(mix('css/kyc/style.css'))}}">
@endsection
@section('content')
<div class="row match-height">
    <div class="@if ($gnl->referal_status == 1)col-lg-9 @else col-lg-12 @endif col-md-12 col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">{{ __('FxPairs')}}</h4>
                <div class="card-search"></div>
            </div>
            <div class="table-responsive" style="max-height:280px;overflow-y:auto;">
                <table class="table custom-data-bs-table">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">{{ __('Pairs')}}</th>
                            <th scope="col">{{ __('locale.Low')}}</th>
                            <th scope="col">{{ __('locale.High')}}</th>
                            <th scope="col">{{ __('Spread')}}</th>
                            <th scope="col">{{ __('locale.Status')}}</th>
                            <th scope="col">{{ __('locale.Action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fxpairs as $pair)
                            <tr>
                                <td data-label="{{ __('locale.Pairs')}}">
                                    <div class="fw-bold fs-4">{{ $pair->symbol }}</div>
                                    <small class="text-warning">({{ $pair->symbol }}/{{ $pair->pair }})</small>
                                </td>
                                <td data-label="{{ __('locale.Low')}}">
                                <!--<div class="fw-bold fs-4">{{ $pair->low }}</div>-->
                                <div id="pair_fad_{{$pair->id}}">{{$pair->low}}</div>
                                
                                
                                </td>
                                <td data-label="{{ __('locale.High')}}">
                                <div class="fw-bold fs-4" id="pair_high{{$pair->id}}">{{ $pair->high }}</div>
                                <script>
                                                setInterval(function pLow_{{$pair->id}}() {
                                                    min_{{$pair->id}} = {{ $pair->low - (($pair->low * 50) / 100) }};
                                                    max_{{$pair->id}} = {{ $pair->high}};
                                                    max_high_{{$pair->id}} = {{$pair->high + (($pair->high * 40)/100)}};
                                                    pair_d_{{$pair->id}} = document.getElementById('pair_fad_{{$pair->id}}');
                                                    pair_c_{{$pair->id}} = (Math.random() * (max_{{$pair->id}} - min_{{$pair->id}})) + min_{{$pair->id}};
                                                    pair_a_{{$pair->id}} = "{{ $pair->low }}";
                                                    pair_high_element_{{$pair->id}} = document.getElementById('pair_high{{$pair->id}}');
                                                    pair_high_element_{{$pair->id}}.innerHTML = ((Math.random() * (max_high_{{$pair->id}} - max_{{$pair->id}})) + max_{{$pair->id}}).toFixed(5);
                                                    
                                                    if (pair_c_{{$pair->id}} > pair_a_{{$pair->id}}){
                                                        pair_d_{{$pair->id}}.innerHTML = (pair_c_{{$pair->id}}).toFixed(5) + '<i class="bi bi-arrow-up"></i>';
                                                        pair_d_{{$pair->id}}.style.color = 'rgb(14,203,129)';
                                                    } else if (pair_c_{{$pair->id}} < pair_a_{{$pair->id}}){
                                                        pair_d_{{$pair->id}}.innerHTML = pair_c_{{$pair->id}}.toFixed(5) + '<i class="bi bi-arrow-down"></i>';
                                                        pair_d_{{$pair->id}}.style.color = 'rgb(246,70,93';
                                                    } else {
                                                        pair_d_{{$pair->id}}.innerHTML = pair_c_{{$pair->id}};
                                                        pair_d_{{$pair->id}}.style.color = 'dark';
                                                    }
                                                }, 3000);
                                            </script>
                                </td>
                                <td data-label="{{ __('locale.Spread')}}">
                                <div class="fw-bold fs-4">{{ $pair->spread }}</div>
                                </td>
                                <td data-label="{{ __('locale.Status')}}">
                                    @if($pair->status == 1)
                                    <span class="badge bg-success">{{ __('locale.Active')}}</span>
                                    @elseif($pair->status != 1)
                                    <span class="badge bg-warning">{{ __('locale.In Active')}}</span>
                                    @endif
                                </td>
                                <td data-label="{{ __('locale.FxBot')}}">
                                    @if($pair->status == 1)
                                    <a href="{{route('user.fxbot.fxnow',['symbol'=>$pair->symbol,'pair'=>$pair->pair])}}"
                                        class="btn btn-icon btn-info btn-sm" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="{{ __('Purchase FxBot')}}">
                                            <i class="bi bi-box-arrow-up-right"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                        <tr>
                            <td class="text-muted text-center" colspan="100%">{{ __($empty_message) }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @if ($gnl->referal_status == 1)
    <div class="col-lg-3 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-body text-center">
                <i class="bi bi-gift text-warning font-large-2 mb-1"></i>
                <h5 class="card-title">{{ __('locale.Refer & Earn')}}</h5>
                <p class="card-text">
                    {{ __('locale.Refer your friends & Earn for 5% of every customer that complete 1 deposit in the platform.')}}
                </p>
                <!-- modal trigger button -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#referEarnModal">
                    {{ __('locale.Invite')}}
                </button>
            </div>
        </div>
        @include('user/partials/refer-earn')
    </div>
    @endif
</div>
@endsection

@push('script')
    <script>
        "use strict";
        function myFunction() {
            var copyText = document.getElementById("referralURL");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            /*For mobile devices*/
            document.execCommand("copy");
            iziToast.success({message: "Referral Url Copied: " + copyText.value, position: "topRight"});
        }
    </script>
@endpush
