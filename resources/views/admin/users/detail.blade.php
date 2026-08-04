<?php
    $info = json_decode(json_encode(getIpInfo()), true);
?>
@extends('layouts.app')
@section('vendor-style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap4-toggle/3.6.1/bootstrap4-toggle.min.css">
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-9 col-lg-7 col-md-7">
            <div class="card mt-50">
                <div class="card-body">
                    <h5 class="card-title mb-50 border-bottom pb-2">{{$user->fullname}} {{ __('locale.Information')}}</h5>

                    <form action="{{route('admin.users.update',[$user->id])}}" method="POST"
                          enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="col ">
                                    <label class="form-control-label h6 mt-1">{{ __('locale.First Name')}}<span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="firstname" value="{{$user->firstname}}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="col">
                                    <label class="form-control-label  h6 mt-1">{{ __('locale.Last Name')}} <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="lastname" value="{{$user->lastname}}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="col ">
                                    <label class="form-control-label h6 mt-1">{{ __('locale.Email')}} <span class="text-danger">*</span></label>
                                    <input class="form-control" type="email" name="email" value="{{$user->email}}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="col">
                                    <label class="form-control-label  h6 mt-1">{{ __('locale.Mobile Number')}} <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="mobile" value="{{$user->mobile}}">
                                </div>
                            </div>
                        </div>


                        <div class="row mt-2">
                            <div class="col-md-12">
                                <div class="col ">
                                    <label class="form-control-label h6 mt-1">{{ __('locale.Address')}} </label>
                                    <input class="form-control" type="text" name="address" value="{{@$user->home}}">
                                    <small class="form-text text-muted"><i class="bi bi-info-circle-circle"></i> {{ __('locale.House number, street address')}}
                                    </small>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="col">
                                    <label class="form-control-label h6 mt-1">{{ __('locale.City')}} </label>
                                    <input class="form-control" type="text" name="city" value="{{@$user->area}}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="col ">
                                    <label class="form-control-label h6 mt-1">{{ __('locale.State')}} </label>
                                    <input class="form-control" type="text" name="state" value="{{@$user->town}}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="col ">
                                    <label class="form-control-label h6 mt-1">{{ __('locale.Zip/Postal')}} </label>
                                    <input class="form-control" type="text" name="zip" value="{{@$user->zip}}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="col ">
                                    <label class="form-control-label h6 mt-1">{{ __('locale.Country')}} </label>
                                    <select id="country"
                                    name="country" placeholder="Country" aria-describedby="country"
                                    value="{{ old('country') }}" class="form-control"> @include('partials.country') </select>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col col-xl-4 col-md-6  col-sm-3 col-12">
                                <label class="form-control-label h6 mt-1">{{ __('locale.Status')}} </label>
                                <input type="checkbox" data-onstyle="success" data-offstyle="danger"
                                       data-bs-toggle="toggle" data-on="{{ __('locale.Active')}}" data-off="{{ __('locale.Banned')}}" data-width="100%"
                                       name="status"
                                       @if($user->status) checked @endif>
                            </div>

                            {{-- <div class="col  col-xl-4 col-md-6  col-sm-3 col-12">
                                <label class="form-control-label h6 mt-1">{{ __('locale.SMS Verification')}} </label>
                                <input class="form-check-input" data-width="100%" type="checkbox" data-bs-toggle="toggle" data-onstyle="success" data-offstyle="danger" data-on="{{ __('locale.Verified')}}" data-off="{{ __('locale.Unverified')}}" name="sv"
                                       @if($user->sv) checked @endif>

                            </div> --}}
                        </div>


                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="col">
                                    <button type="submit" class="btn btn-primary mt-2 btn-lg">{{ __('locale.Save Changes')}}
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-5 col-md-5">
            <div class="card rounded-2 overflow-hidden shadow">
                <div class="card-body p-0">
                    <div class="p-3 bg-white">
                        <div class="mb-2">
                            <img src="{{ Auth::user() ? Auth::user()->profile_photo_url : asset('images/portrait/small/avatar-s-11.jpg') }}" alt="{{ __('locale.Profile Image')}}" class="w-100">
                        </div>
                        <div class="mt-15">
                            <h4 class="">{{$user->fullname}}</h4>
                            <span class="text-small">{{ __('locale.Joined At')}} <strong>{{showDateTime($user->created_at,'d M, Y h:i A')}}</strong></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card rounded-2 overflow-hidden shadow">
                <div class="card-body">
                    <h5 class="text-muted">{{ __('locale.User information')}}</h5>
                    <ul class="list-group">

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ __('locale.Username')}}
                            <span class="h6 mt-1">{{$user->username}}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ __('locale.Status')}}
                            @switch($user->status)
                                @case(1)
                                <span class="badge rounded-pill bg-success">{{ __('locale.Active')}}</span>
                                @break
                                @case(0)
                                <span class="badge rounded-pill bg-danger">{{ __('locale.Banned')}}</span>
                                @break
                            @endswitch
                        </li>

                        @if($refer_by)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="float-start">{{ __('locale.Reference By')}}</span>
                                <span class="float-end text-muted">{{__($refer_by->username)}}</span>
                            </li>
                        @endif

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ __('locale.Balance')}}
                            <span class="h6 mt-1">
                                @if($wallet != 'null')
                                {{getAmount($wallet->balance)}}  {{__($general->cur_text)}}
                                @else
                                <form method="POST" action="{{ route('admin.wallet.create') }}">
                                    @csrf
                                    <input type="hidden" id="user_id" name="user_id" value="{{ $user->id }}">
                                    <input type="hidden" id="symbol" name="symbol" value="USDT">
                                    <button type="submit" class="btn btn-success btn-sm">Create Wallet</button></span>
                                </form>
                                @endif
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card rounded-2 overflow-hidden mt-2 shadow">
                <div class="card-body">
                    <h5 class="text-muted">{{ __('locale.User action')}}</h5>
                    <a data-bs-toggle="modal" href="#addSubModal" class="btn btn-success mt-2">
                        {{ __('locale.Add/Subtract Balance')}}
                    </a>
                    <a href="{{ route('admin.users.login.history.single', $user->id) }}"
                       class="btn btn-primary mt-2">
                        {{ __('locale.Login Logs')}}
                    </a>
                    <a href="{{route('admin.users.email.single',$user->id)}}"
                       class="btn btn-danger mt-2">
                        {{ __('locale.Send Email')}}
                    </a>
                     <a href="{{route('admin.users.referral.log',$user->id)}}"
                       class="btn btn-info mt-2">
                        {{ __('locale.Referral Log')}}
                    </a>

                    <a href="{{route('admin.users.commission.log',$user->id)}}"
                       class="btn btn-warning mt-2">
                        {{ __('locale.Commission Log')}}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div id="addSubModal" class="modal fade text-start" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('locale.Add / Subtract Balance')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{route('admin.users.addSubBalance', $user->id)}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col col-md-12">
                                <input class="form-check-input" data-width="100%" type="checkbox" data-bs-toggle="toggle" data-onstyle="success" data-offstyle="danger" data-on="{{ __('locale.Add Balance')}}" data-off="{{ __('locale.Subtract Balance')}}" name="act" checked>
                            </div>


                            <div class="col col-md-12 mt-1">
                                <label>{{ __('locale.Amount')}}<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" name="amount" class="form-control" placeholder="{{ __('locale.Please provide positive amount')}}">
                                        <div class="input-group-text">{{ __($general->cur_sym) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">{{ __('locale.Close')}}</button>
                        <button type="submit" class="btn btn-success">{{ __('locale.Submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('vendor-script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap4-toggle/3.6.1/bootstrap4-toggle.min.js"></script>

@endsection
@push('script')
    <script>
        "use strict";
        $('.toggle').bootstrapToggle({
            on: 'Y',
            off: 'N',
            width: '100%',
            size: 'small'
        });
        $("select[name=country]").val("{{ @$user->country }}");

        $('select[name=country_code]').change(function () {
            $('input[name=country]').val($('select[name=country_code] :selected').data('country'));
        }).change();
    </script>
@endpush

