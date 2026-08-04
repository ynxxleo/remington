@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12 mb-3">
        <form action="{{ route('admin.platform.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card">
                <h4 class="card-header">Platform Features</h4>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-xl-3 col-lg-4 col-sm-6">
                            <div class="border-primary rounded p-1">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="me-1" for="binary">{{ __('locale.Binary Trading')}}</label>
                                    <input class="form-check-input" type="checkbox" data-bs-toggle="toggle"
                                        data-onstyle="success" data-offstyle="danger" data-on="{{ __('locale.Active')}}"
                                        data-off="{{ __('locale.Inactive')}}" name="binary" id="binary"
                                        @if($platform->binary) checked @endif>
                                </div>
                            </div>
                        </div>
                        @if ($bot->installed == 1)
                        <div class="col-xl-3 col-lg-4 col-sm-6">
                            <div class="border-primary rounded p-1">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="me-1" for="bottrader">{{ __('locale.Bot Trader')}}</label>
                                    <input class="form-check-input" type="checkbox" data-bs-toggle="toggle"
                                        data-onstyle="success" data-offstyle="danger" data-on="{{ __('locale.Active')}}"
                                        data-off="{{ __('locale.Inactive')}}" name="bottrader" id="bottrader"
                                        @if($bot->status) checked @endif>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-sm-6">
                            <div class="border-primary rounded p-1">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="me-1"
                                        for="bot_fake">{{ __('locale.Bot Trader Fake/Real Data')}}</label>
                                    <input class="form-check-input" type="checkbox" data-bs-toggle="toggle"
                                        data-onstyle="success" data-offstyle="danger" data-on="{{ __('locale.Active')}}"
                                        data-off="{{ __('locale.Inactive')}}" name="bot_fake" id="bot_fake"
                                        @if($platform->bot_fake) checked @endif>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if ($ico->installed == 1)
                        <div class="col-xl-3 col-lg-4 col-sm-6">
                            <div class="border-primary rounded p-1">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="me-1" for="ico">{{ __('locale.Token ICO')}}</label>
                                    <input class="form-check-input" type="checkbox" data-bs-toggle="toggle"
                                        data-onstyle="success" data-offstyle="danger" data-on="{{ __('locale.Active')}}"
                                        data-off="{{ __('locale.Inactive')}}" name="ico" id="ico" @if($ico->status)
                                    checked @endif>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if ($mlm->installed == 1)
                        <div class="col-xl-3 col-lg-4 col-sm-6">
                            <div class="border-primary rounded p-1">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="me-1" for="mlm">{{ __('locale.MLM')}}</label>
                                    <input class="form-check-input" type="checkbox" data-bs-toggle="toggle"
                                        data-onstyle="success" data-offstyle="danger" data-on="{{ __('locale.Active')}}"
                                        data-off="{{ __('locale.Inactive')}}" name="mlm" id="mlm" @if($mlm->status)
                                    checked @endif>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="col-xl-3 col-lg-4 col-sm-6">
                            <div class="border-primary rounded p-1">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="me-1" for="kyc">{{ __('locale.KYC')}}</label>
                                    <input class="form-check-input" type="checkbox" data-bs-toggle="toggle"
                                        data-onstyle="success" data-offstyle="danger" data-on="{{ __('locale.Active')}}"
                                        data-off="{{ __('locale.Inactive')}}" name="kyc" id="kyc" @if($platform->kyc)
                                    checked @endif>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-sm-6">
                            <div class="border-primary rounded p-1">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="me-1"
                                        for="subdomain">{{ __('locale.Use Platform on Subdomain')}}</label>
                                    <input class="form-check-input" type="checkbox" data-bs-toggle="toggle"
                                        data-onstyle="success" data-offstyle="danger" data-on="{{ __('locale.Active')}}"
                                        data-off="{{ __('locale.Inactive')}}" name="subdomain" id="subdomain"
                                        @if($platform->subdomain) checked @endif>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-sm-6">
                            <div class="border-primary rounded p-1">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="me-1" for="wallet_address">{{ __('locale.Wallet Address')}}</label>
                                    <input class="form-check-input" type="checkbox" data-bs-toggle="toggle"
                                        data-onstyle="success" data-offstyle="danger" data-on="{{ __('locale.Active')}}"
                                        data-off="{{ __('locale.Inactive')}}" name="wallet_address" id="wallet_address"
                                        @if($platform->wallet_address) checked @endif>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <div class="col">
                        <button type="submit" class="btn btn-primary">{{ __('locale.Update')}}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

