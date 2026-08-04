@extends('layouts.app')
@section('page-style')

  <link rel="stylesheet" href="{{ asset(mix('css/kyc/style.css'))}}">
@endsection
@section('content')
<div class="row match-height">
    <div class="@if ($gnl->referal_status == 1)col-lg-9 @else col-lg-12 @endif col-md-12 col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">{{ __('Stocks')}}</h4>
                <div class="card-search"></div>
            </div>
            <div class="table-responsive" style="max-height:280px;overflow-y:auto;">
                <table class="table custom-data-bs-table">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">{{ __('Company')}}</th>
                            <th scope="col">{{ __('Last')}}</th>
                            <th scope="col">{{ __('Change')}}</th>
                            <th scope="col">{{ __('Change Percent')}}</th>
                            <th scope="col">{{ __('locale.Status')}}</th>
                            <th scope="col">{{ __('locale.Action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stocks as $stock)
                            <tr>
                                <td data-label="{{ __('locale.Pairs')}}">
                                    <div class="fw-bold fs-4">{{ $stock->symbol }}</div>
                                    <small class="text-warning">({{ $stock->company }})</small>
                                </td>
                                <td data-label="{{ __('Last')}}">
                                
                                <div id="com_fad_{{$stock->id}}">{{$stock->last}}</div>
                                
    <div class="modal fade custom--modal" id="buyStock{{$stock->id}}">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Purchase {{$stock->symbol}} Stock</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <form class="stock-form" action="{{route('user.stbot.stock.buy')}}" method="POST">
                    @csrf
                    <input name="shareprice" value="{{$stock->last}}" type="hidden">
                    <input name="symbol" value="{{$stock->symbol}}" type="hidden">
                    <input type="number" value="1" class="form-control" name="amount">  <div class="modal-body">
                        <p>Are you sure you want to buy this stock?</p>
                    </div>
    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm text--white btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm text--white btn-success">Buy</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

                                 <script>
                                                setInterval(function sLast_{{$stock->id}}() {
                                                    min_{{$stock->id}} = {{ $stock->last - (($stock->last * 10) / 100) }};
                                                    max_{{$stock->id}} = {{ $stock->last + (($stock->last * 10) / 100)}}  ;
                                                    pair_d_{{$stock->id}} = document.getElementById('com_fad_{{$stock->id}}');
                                                    pair_chg_{{$stock->id}} = document.getElementById('com_chg_{{$stock->id}}');
                                                    pair_per_{{$stock->id}} = document.getElementById('com_per_{{$stock->id}}');
                                                    pair_c_{{$stock->id}} = (Math.random() * (max_{{$stock->id}} - min_{{$stock->id}})) + min_{{$stock->id}};
                                                    pair_a_{{$stock->id}} = "{{ $stock->last }}";
                                                    
                                                    if (pair_c_{{$stock->id}} > pair_a_{{$stock->id}}){
                                                        pair_d_{{$stock->id}}.innerHTML = pair_c_{{$stock->id}}.toFixed(2) + '<i class="bi bi-arrow-up"></i>';
                                                        pair_d_{{$stock->id}}.style.color = 'rgb(14,203,129)';
                                                        profit = (pair_c_{{$stock->id}} -  pair_a_{{$stock->id}})
                                                        pair_chg_{{$stock->id}}.innerHTML = profit.toFixed(2);
                                                         pair_per_{{$stock->id}}.innerHTML = (((profit)/  pair_a_{{$stock->id}})*100).toFixed(2);
                                                        pair_per_{{$stock->id}}.style.color = 'rgb(14,203,129)';
                                                        
                                                    } else if (pair_c_{{$stock->id}} < pair_a_{{$stock->id}}){
                                                        pair_d_{{$stock->id}}.innerHTML = pair_c_{{$stock->id}}.toFixed(2) + '<i class="bi bi-arrow-down"></i>';
                                                        loss = (pair_c_{{$stock->id}} -  pair_a_{{$stock->id}});
                                                         pair_chg_{{$stock->id}}.innerHTML = loss.toFixed(2);
                                                         pair_per_{{$stock->id}}.innerHTML = ((loss/  pair_a_{{$stock->id}})*100).toFixed(2);
                                                         
                                                        pair_d_{{$stock->id}}.style.color = 'rgb(246,70,93)';
                                                        pair_per_{{$stock->id}}.style.color = 'rgb(246,70,93)';
                                                    } else {
                                                        pair_d_{{$stock->id}}.innerHTML = pair_c_{{$stock->id}};
                                                        pair_d_{{$stock->id}}.style.color = 'dark';
                                                    }
                                                }, 3000);
                                            </script>
                               
                                </td>
                                <td data-label="{{ __('Change')}}">
                                <div class="fw-bold fs-4" id="com_chg_{{$stock->id}}">{{ $stock->change }}</div>
                                </td>
                                <td data-label="{{ __('Change Percent')}}">
                                <div class="fw-bold fs-4" id="com_per_{{$stock->id}}">{{ $stock->changepercent }}%</div>
                                </td>
                                <td data-label="{{ __('locale.Status')}}">
                                    @if($stock->status == 1)
                                    <span class="badge bg-success">{{ __('locale.Active')}}</span>
                                    @elseif($stock->status != 1)
                                    <span class="badge bg-warning">{{ __('locale.In Active')}}</span>
                                    @endif
                                </td>
                                <td data-label="{{ __('locale.FxBot')}}">
                                    @if($stock->status == 1)
                                    <a href="{{route('user.fxbot.fxnow',['symbol'=>$stock->symbol,'pair'=>'stock'])}}"
                                        class="btn btn-icon btn-info btn-sm" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="{{ __('Purchase Stock')}}">
                                            <i class="bi bi-box-arrow-up-right"></i>
                                        </a>
                                        <button type="button" class="w-100 btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#buyStock{{$stock->id}}" id="buyStockModal">{{ __('Buy')}}</button>
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
