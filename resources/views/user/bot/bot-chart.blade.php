@extends('layouts.app')
@php
use \mobiledetect\mobiledetectlib\Detection;
$detect = new Mobile_Detect;
@endphp
@section('vendor-style')
  <!-- vendor css files -->
  <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/spinner/jquery.bootstrap-touchspin.css'))}}">
    <style>
        .market-chart-shell {
            width: 100%;
            height: clamp(650px, 76vh, 840px);
            min-height: 620px;
            overflow: hidden;
            border: 1px solid rgba(148, 163, 184, .16);
            border-radius: 22px;
            background: linear-gradient(180deg, #0a1411 0%, #070d0b 100%);
            box-shadow: 0 24px 70px rgba(0, 0, 0, .22);
        }
        .native-chart { height: 100%; display: flex; flex-direction: column; color: #e8f0ec; }
        .native-chart__topbar { min-height: 68px; padding: 12px 18px; display: flex; align-items: center; justify-content: space-between; gap: 16px; border-bottom: 1px solid rgba(148,163,184,.14); background: rgba(255,255,255,.025); }
        .native-chart__identity { display: flex; align-items: center; gap: 12px; min-width: 0; }
        .native-chart__mark { width: 38px; height: 38px; display: grid; place-items: center; border-radius: 12px; color: #07100d; background: #20e19a; font-weight: 800; box-shadow: 0 0 0 5px rgba(32,225,154,.08); }
        .native-chart__symbol { font-size: 1.05rem; font-weight: 750; letter-spacing: .025em; white-space: nowrap; }
        .native-chart__price { color: #91a099; font-size: .78rem; margin-top: 2px; }
        .native-chart__price strong { color: #edf6f1; font-weight: 650; }
        .native-chart__intervals { display: flex; gap: 5px; padding: 4px; border-radius: 13px; background: #111c18; }
        .native-chart__interval { border: 0; border-radius: 9px; padding: 8px 10px; color: #87968f; background: transparent; font-size: .78rem; font-weight: 650; transition: .18s ease; }
        .native-chart__interval:hover { color: #e9f2ed; background: rgba(255,255,255,.05); }
        .native-chart__interval.is-active { color: #07100d; background: #20e19a; }
        .native-chart__body { position: relative; flex: 1; min-height: 0; padding: 8px 8px 0; }
        .native-chart__main { height: 68%; min-height: 330px; }
        .native-chart__indicator { height: 30%; min-height: 150px; border-top: 1px solid rgba(148,163,184,.12); }
        .native-chart__status { position: absolute; inset: 0; z-index: 4; display: flex; align-items: center; justify-content: center; padding: 24px; text-align: center; background: rgba(7,13,11,.88); backdrop-filter: blur(8px); }
        .native-chart__status[hidden] { display: none; }
        .native-chart__loader { width: 34px; height: 34px; margin: 0 auto 12px; border: 3px solid rgba(32,225,154,.18); border-top-color: #20e19a; border-radius: 50%; animation: chart-spin .8s linear infinite; }
        .native-chart__retry { margin-top: 12px; border: 1px solid rgba(32,225,154,.4); border-radius: 10px; padding: 8px 14px; color: #20e19a; background: transparent; }
        .native-chart__footer { min-height: 42px; padding: 8px 18px; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(148,163,184,.12); color: #75847d; font-size: .72rem; }
        @keyframes chart-spin { to { transform: rotate(360deg); } }
        .market-chart-column { min-width: 0; }
        .market-trade-actions { min-width: 0; color: #e8f0ec; }
        .market-trade-actions .card {
            color: #e8f0ec;
            border-color: rgba(148, 163, 184, .18);
            background: #0c1512;
        }
        .market-trade-actions .form-label,
        .market-trade-actions small,
        .market-trade-actions .text-dark { color: #aebbb5 !important; }
        .market-trade-actions .input-group { width: 100% !important; flex-wrap: nowrap; }
        .market-trade-actions .form-control {
            min-width: 0;
            color: #111916 !important;
            background: #f4f7f5 !important;
        }
        .market-trade-actions .input-group-text {
            flex: 0 0 auto;
            color: #111916 !important;
            background: #e8eeeb !important;
        }
        .market-trade-actions .btn { white-space: normal; }
        .market-trade-actions #selectBot {
            min-height: 54px;
            color: #fff !important;
            border-color: #7c3aed !important;
            background: linear-gradient(135deg, #7c3aed, #9b5cff) !important;
            box-shadow: 0 12px 28px rgba(124, 58, 237, .24);
        }
        .market-trade-actions #selectBot:hover,
        .market-trade-actions #selectBot:focus {
            border-color: #a978ff !important;
            background: linear-gradient(135deg, #8b4df0, #ab72ff) !important;
            transform: translateY(-1px);
        }
        .market-trade-actions #botcontract > div:last-child > .btn-success,
        .market-trade-actions #botcontract button[type="submit"].btn-success {
            min-height: 58px;
            color: #04110c !important;
            border-color: #20e19a !important;
            background: linear-gradient(135deg, #20e19a, #55efb5) !important;
            box-shadow: 0 14px 32px rgba(32, 225, 154, .22);
            font-weight: 750;
        }
        .market-trade-actions #botcontract button[type="submit"].btn-success i {
            color: #08764f !important;
        }
        .market-trade-actions #botcontract button[type="submit"].btn-success:hover,
        .market-trade-actions #botcontract button[type="submit"].btn-success:focus {
            border-color: #66f2be !important;
            background: linear-gradient(135deg, #32e5a4, #70f4c4) !important;
            transform: translateY(-1px);
        }
        #botTypeModal .modal-content {
            color: #e9f2ed;
            border: 1px solid rgba(148, 163, 184, .2);
            background: #0a1310 !important;
            box-shadow: 0 28px 80px rgba(0, 0, 0, .55);
        }
        #botTypeModal .modal-header {
            min-height: 72px;
            border-bottom-color: rgba(148, 163, 184, .16);
        }
        #botTypeModal .btn-close {
            opacity: 1;
            filter: invert(1) grayscale(1);
            background-color: rgba(255, 255, 255, .08);
            border-radius: 14px;
        }
        #botTypeModal .bg-wallet {
            color: #dce8e2 !important;
            border: 1px solid rgba(148, 163, 184, .16);
            background: #101b17 !important;
            transition: border-color .2s ease, background-color .2s ease, transform .2s ease;
        }
        #botTypeModal .bg-wallet:hover {
            transform: translateY(-2px);
            border-color: rgba(32, 225, 154, .48);
            background: #14231d !important;
        }
        #botTypeModal .bg-wallet-active {
            border-color: #20e19a !important;
            background: rgba(32, 225, 154, .1) !important;
        }
        #botTypeModal .bg-wallet .text-white,
        #botTypeModal .bg-wallet .fw-bold { color: #f5faf7 !important; }
        #botTypeModal .bg-wallet .text-warning { color: #ffad55 !important; }
        #botTypeModal .bg-wallet .text-success { color: #20e19a !important; }
        #botTypeModal .bg-wallet img {
            width: 100%;
            max-width: 96px;
            aspect-ratio: 1;
            object-fit: contain;
            border-radius: 18px;
            background: rgba(255, 255, 255, .06);
        }
        @media (max-width: 767px) {
            .market-chart-shell {
                height: clamp(350px, calc(100svh - 315px), 560px);
                min-height: 350px;
                border-radius: 16px;
            }
            .native-chart__topbar { align-items: flex-start; padding: 12px; }
            .native-chart__identity { padding-top: 4px; }
            .native-chart__mark { width: 34px; height: 34px; }
            .native-chart__intervals { max-width: 58%; overflow-x: auto; }
            .native-chart__interval { padding: 7px 9px; }
            .native-chart__body { padding-inline: 0; }
            .native-chart__main { min-height: 0; height: 68%; }
            .native-chart__indicator { min-height: 88px; height: 30%; }
            body.bot-trader-page { overflow: hidden !important; }
            body.bot-trader-page .fin-subpage-head { display: none !important; }
            body.bot-trader-page .app-content { height: 100svh; overflow: hidden !important; padding-bottom: 0 !important; }
            body.bot-trader-page .content-wrapper { padding: .5rem .75rem 0 !important; }
            body.bot-trader-page .content-body > .row { width: min(100%, 560px); margin: 0 auto !important; }
            body.bot-trader-page .footer { display: none !important; }
            .bot-mobile-actions {
                position: relative !important; inset: auto !important;
                width: min(100%, 560px) !important; margin: .55rem auto 0 !important;
            }
            .bot-mobile-actions #botcontract { margin-top: 0 !important; }
            .bot-mobile-actions #botcontract .mx-1 { margin-inline: 0 !important; }
            .bot-mobile-actions #botcontract .mb-1 { margin-bottom: .5rem !important; }
            .bot-mobile-actions #selectBot,
            .bot-mobile-actions #botTimed { min-height: 44px; }
            .bot-mobile-actions .input-group .form-control,
            .bot-mobile-actions .input-group .input-group-text { min-height: 46px !important; }
            .bot-mobile-actions #botcontract button[type="submit"].btn-success { min-height: 48px; }
        }
    </style>
    @if( $detect->isMobile() && !$detect->isTablet() )
    <style>
        @media
        only screen
        and (min-device-width               : 1px)
        and (max-device-width               : 897px)
        and (orientation                    : landscape) {
        .responsive-portrait {
            display : flex!important;
        }
        body > footer { display: none; }
            div.ads {
                display: none;
            }
        }
    </style>
    @endif
@endsection
@section('content')
<script>document.body.classList.add('bot-trader-page');</script>
<div class="se-pre-con">
    <div class="se-pre-con2 spinner-border text-primary" role="status">
        <span class="sr-only"></span>
    </div>
</div>
@if( $detect->isMobile() && !$detect->isTablet() )
<div class="responsive-portrait ov-nblr">
    <div>
        <div class="brand-text"><img src="{{getImage(imagePath()['logoIcon']['path'] .'/logo.png')}}"
                alt="{{ __('locale.image')}}"></div>
        <span>{{ __('locale.Please turn your device in portrait mode.')}}</span>
    </div>
</div>
<div class="row me-1">
    <div class="col-12 market-chart-column">
        <div class="market-chart-shell js-market-chart"></div>
    </div>
</div>
<div class="bot-mobile-actions market-trade-actions mb-1 w-100">
    <form class="mt-1 text-center" id="botcontract" action="{{ route('user.bot.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="bot_id" name="bot_id">
        <input type="hidden" id="pair" name="pair" value="{{ $pair }}">
        <input type="hidden" id="symbol" name="symbol" value="{{ $symbol }}">
        <input type="hidden" id="type" name="type">
        <input type="hidden" id="botTime" name="botTime">
        <div class="mx-1 mb-1">
            <div class="row">
                <div class="col">
                    @if($wallet == 'null')
                    <div class="text-center my-1">
                        <form method="POST" action="{{ route('user.wallet.create') }}">
                            @csrf
                            <input type="hidden" id="id" name="id" value="{{ $pair }}">
                            <button type="submit" class="btn btn-success btn-sm">{{ __('locale.Create Wallet')}}</button></span>
                        </form>
                    </div>
                    @else

                    <button type="button" class="w-100 btn btn-primary" data-bs-toggle="modal" data-bs-target="#botTypeModal" id="selectBot">{{ __('locale.Select Bot')}}</button>
                    @endif
                </div>
                <div class="col">
                    <div class="dropdown">
                        <button class="w-100 btn btn-outline-warning dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false" id="botTimed" name="botTimed">
                            {{ __('locale.Duration')}}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @foreach ($bot_timing as $timing)
                            <li><a class="dropdown-item" onclick="$('#botTimed').text($(this).text());$('#botcontract').find('input[name=botTime]').val($(this).data('time'));$('#botcontract').find('input[name=type]').val($(this).data('type'));" data-time="{{ $timing->duration }}" data-type="{{ $timing->type }}">{{ $timing->duration }} {{ $timing->type }}s</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @if($wallet != 'null')
        <div class="input-group mb-1 mx-1 w-auto">
            <input type="number" class="form-control" min=""
                max="" step=""
                onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" required="" id="amount"
                name="amount" placeholder="Amount">
            <span class="input-group-text text-light" id="amount">{{ $general->cur_text }}</span>
        </div>
        @endif

        <div class="mx-1">
            <button
                class="w-100 btn btn-success d-flex align-items-center justify-content-between"
                type="submit"><i class="bi bi-battery-charging fs-3"></i><span> {{ __('locale.Start Bot')}}</span>
            </button>
        </div>
    </form>
</div>
@else
<div class="row me-1">
    <div class="flex-start col-9 col-xll-9 col-xl-9 col-lg-9 col-md-8 col-sm-12 market-chart-column">
        <div class="market-chart-shell js-market-chart"></div>
    </div>
    <div class="flex-end col-3 col-xll-3 col-xl-3 col-lg-3 col-md-4 col-sm-12 market-trade-actions">
        <div class="mt-5">
            <div class="card mb-1">
                @if($wallet == 'null')
                <div class="text-center my-1">
                    <form method="POST" action="{{ route('user.wallet.create') }}">
                        @csrf
                        <input type="hidden" id="id" name="id" value="{{ $walletpair }}">
                        <button type="submit" class="btn btn-success btn-sm">{{ __('locale.Create Wallet')}}</button></span>
                    </form>
                </div>
                @else

                <div class="row my-1 mx-1">
                    <button type="button" class="w-100 btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#botTypeModal" id="selectBot">{{ __('locale.Select Bot')}}</button>
                </div>
                @endif
            </div>
            <div class="card mb-1">
                <form class="mt-1 text-center" id="botcontract" action="{{ route('user.bot.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="bot_id" name="bot_id">
                    <input type="hidden" id="pair" name="pair" value="{{ $pair }}">
                    <input type="hidden" id="symbol" name="symbol" value="{{ $symbol }}">
                    <input type="hidden" id="type" name="type">
                    <input type="hidden" id="botTime" name="botTime">
                    <input type="hidden" id="walletpair" name="walletpair" value="{{$walletpair}}">
                    
                    <div class="mx-1 mb-1">
                        <div class="dropdown">
                            <button class="w-100 btn btn-outline-warning dropdown-toggle" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false" id="botTimed" name="botTimed">
                                {{ __('locale.Duration')}}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @foreach ($bot_timing as $timing)
                                <li><a class="dropdown-item" onclick="$('#botTimed').text($(this).text());$('#botcontract').find('input[name=botTime]').val($(this).data('time'));$('#botcontract').find('input[name=type]').val($(this).data('type'));" data-time="{{ $timing->duration }}" data-type="{{ $timing->type }}">{{ $timing->duration }} {{ $timing->type }}s</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @if($wallet != 'null')
                    <div class="input-group mb-1 mx-1 w-auto">
                        <input type="number" class="form-control" min=""
                            max="" step="" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')"
                            required="" id="amount" name="amount" placeholder="Amount">
                        <span class="input-group-text text-light">{{ $walletpair }}</span>
                    </div>
                    @endif

                    <div class="mx-1 mb-1">
                        <button
                            class="w-100 btn btn-success d-flex align-items-center justify-content-between"
                            type="submit"><i class="bi bi-battery-charging fs-3"></i><span> {{ __('locale.Start Bot')}}</span>
                        </button>
                    </div>
                </form>
            </div>
                @if ($runningBot != "null")
                
                <a href="{{$route }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('locale.View Contract')}}">
                    <div id="speedtest">
                    <svg id="gauge" version="1.1" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100"
                        style="enable-background:new 0 0 100 100;" xml:space="preserve">
                        <path style="fill: #e31212;"
                            d="M12.9,75.5c0.3,0.5,0.7,0.9,1,1.4l7.6-5.7c-0.3-0.4-0.5-0.7-0.8-1.1L12.9,75.5z" />
                        <path style="fill: #ea1f39; display: none;"
                            d="M9.3,69.3c0.3,0.5,0.5,1,0.8,1.5l8.4-4.4c-0.2-0.4-0.4-0.8-0.6-1.2L9.3,69.3z" />
                        <path style="fill: #f12d60; display: none;"
                            d="M6.7,62.4C6.9,62.9,7,63.5,7.2,64l9.1-3c-0.1-0.4-0.3-0.8-0.4-1.2L6.7,62.4z" />
                        <path style="fill: #f83c85; display: none;"
                            d="M5.3,55.2c0,0.6,0.1,1.1,0.2,1.7l9.4-1.5c-0.1-0.4-0.1-0.9-0.2-1.3L5.3,55.2z" />
                        <path style="fill: #ff4ba8; display: none;"
                            d="M5,47.9c0,0.6,0,1.1,0,1.7l9.5,0.1c0-0.5,0-0.9,0-1.4L5,47.9z" />
                        <path style="fill: #fb3eb2; display: none;"
                            d="M5.9,40.7c-0.1,0.6-0.2,1.1-0.3,1.7L15,44c0.1-0.4,0.2-0.9,0.2-1.3L5.9,40.7z" />
                        <path style="fill: #f732bf; display: none;"
                            d="M8,33.7c-0.2,0.5-0.4,1.1-0.6,1.6l9,3.1c0.1-0.4,0.3-0.9,0.5-1.3L8,33.7z" />
                        <path style="fill: #f226cd; display: none;"
                            d="M11.2,27.1c-0.3,0.5-0.5,1-0.8,1.5l8.3,4.6c0.2-0.4,0.4-0.8,0.7-1.2L11.2,27.1z" />
                        <path style="fill: #ee1adc; display: none;"
                            d="M15.5,21.2c-0.4,0.4-0.8,0.9-1.1,1.3l7.5,5.8c0.3-0.4,0.6-0.7,0.9-1.1L15.5,21.2z" />
                        <path style="fill: #ae19de; display: none;"
                            d="M20.6,16c-0.5,0.3-0.9,0.7-1.3,1.1l6.5,7c0.3-0.3,0.6-0.6,1-0.9L20.6,16z" />
                        <path style="fill: #6618cd; display: none;"
                            d="M26.4,11.7c-0.5,0.3-0.9,0.6-1.4,0.9l5.3,7.9c0.3-0.2,0.7-0.5,1-0.7L26.4,11.7z" />
                        <path style="fill: #2716bd; display: none;"
                            d="M33,8.3c-0.6,0.2-1.1,0.5-1.6,0.7l3.9,8.7c0.4-0.2,0.9-0.4,1.3-0.6L33,8.3z" />
                        <path style="fill: #1537ac; display: none;"
                            d="M39.9,6.1c-0.5,0.1-1.1,0.3-1.6,0.4l2.5,9.2c0.4-0.1,0.8-0.2,1.2-0.3L39.9,6.1z" />
                        <path style="fill: #2061c1; display: none;"
                            d="M47.2,5.2c-0.6,0-1.1,0-1.7,0.1l0.9,9.4c0.4,0,0.9-0.1,1.3-0.1L47.2,5.2z" />
                        <path style="fill: #2c90d5; display: none;"
                            d="M54.5,5.2c-0.6,0-1.1-0.1-1.7-0.1l-0.6,9.5c0.4,0,0.9,0.1,1.3,0.1L54.5,5.2z" />
                        <path style="fill: #3bc2ea; display: none;"
                            d="M61.6,6.5c-0.5-0.1-1.1-0.3-1.6-0.4l-2.2,9.3c0.4,0.1,0.9,0.2,1.3,0.3L61.6,6.5z" />
                        <path style="fill: #4bf7ff; display: none;"
                            d="M68.6,9C68,8.7,67.5,8.5,67,8.3l-3.6,8.8c0.4,0.2,0.8,0.3,1.2,0.5L68.6,9z" />
                        <path style="fill: #39f6d2; display: none;"
                            d="M74.9,12.5c-0.4-0.3-0.9-0.6-1.4-0.9l-5,8.1c0.4,0.2,0.7,0.5,1.1,0.7L74.9,12.5z" />
                        <path style="fill: #28ed99; display: none;"
                            d="M80.6,17c-0.5-0.4-0.9-0.7-1.3-1.1l-6.2,7.2c0.3,0.3,0.7,0.6,1,0.9L80.6,17z" />
                        <path style="fill: #19e55d; display: none;"
                            d="M85.7,22.4c-0.4-0.4-0.7-0.9-1.1-1.3l-7.4,6.1c0.3,0.3,0.5,0.7,0.8,1L85.7,22.4z" />
                        <path style="fill: #0adc1e; display: none;"
                            d="M89.6,28.6c-0.2-0.5-0.5-1-0.8-1.5L80.6,32c0.2,0.4,0.4,0.7,0.6,1.1L89.6,28.6z" />
                        <path style="fill: #0adc1e; display: none;"
                            d="M92.5,35.3c-0.2-0.5-0.4-1.1-0.6-1.6l-8.8,3.4c0.2,0.4,0.3,0.8,0.5,1.2L92.5,35.3z" />
                        <path style="fill: #0adc1e; display: none;"
                            d="M94.4,42.2c-0.1-0.5-0.2-1.1-0.3-1.6l-9.4,2c0.1,0.4,0.2,0.9,0.2,1.3L94.4,42.2z" />
                        <path style="fill: #0adc1e; display: none;"
                            d="M95,47.8l-9.5,0.5c0,0.4,0,0.9,0,1.3l9.5-0.1C95,48.9,95,48.4,95,47.8z" />
                        <path style="fill: #0adc1e; display: none;"
                            d="M94.5,56.8c0.1-0.5,0.1-1.1,0.2-1.7l-9.4-1c-0.1,0.4-0.1,0.9-0.2,1.3L94.5,56.8z" />
                        <path style="fill: #0adc1e; display: none;"
                            d="M92.8,63.9c0.2-0.5,0.3-1,0.5-1.7l-9.1-2.6c-0.1,0.4-0.3,0.9-0.4,1.3L92.8,63.9z" />
                        <path style="fill: #0adc1e; display: none;"
                            d="M90,70.7c0.2-0.5,0.5-1,0.8-1.5l-8.7-4.1c-0.2,0.4-0.4,0.8-0.6,1.2L90,70.7z" />
                        <path style="fill: #0adc1e; display: none;"
                            d="M89.9,80.1c0.5-0.7,1-1.3,1.4-1.9l-12-8.2c-0.3,0.4-0.6,0.9-0.9,1.3L89.9,80.1z" />
                    </svg>
                    <div id="gauge-label">0</div>
                </div></a>
                @endif
        </div>
    </div>
</div>
@endif
<div class="modal fade" id="botTypeModal" tabindex="-1" aria-labelledby="botType" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-3 px-sm-3">
                @foreach ($bot_type as $bot)
                <a onclick="$('#botcontract').find('input[name=bot_id]').val($(this).data('id'));$('#selectBot').text($(this).data('bot'));$('#botcontract').find('input[name=unit]').val($(this).data('unit'));$('#botTypeModal').modal('hide');$('#botcontract').find('input[name=amount]').attr('min',$(this).data('min'));$('#botcontract').find('input[name=amount]').attr('max',$(this).data('max'));$('#botcontract').find('input[name=amount]').attr('step',$(this).data('min'));"
                    style="stretched-link" data-id="{{ $bot->id }}"
                    data-min="{{ json_decode($bot->limits)->min_bot_amount }}"
                    data-max="{{ json_decode($bot->limits)->max_bot_amount }}" data-bot="{{ $bot->title }}"
                    data-unit="Day">
                    <div class="row bg-wallet p-1 rounded mb-1 @if($bot->id == 1) bg-wallet-active @endif">
                        <div class="col-3">
                            <img src="{{ getImage('assets/images/bot/'.$bot->image)}}">
                        </div>
                        <div class="col-9">
                            <div class="d-flex justify-content-between">
                                <div class="fw-bold fs-4 text-white">{{ $bot->title }} @if ($bot->is_new == 1)<span
                                        class="fs-6 badge bg-success text-white">{{ __('locale.New')}}</span>@endif
                                </div>
                                <div class="fs-6 text-white d-none d-md-block"><i class="bi bi-app-indicator"></i>
                                    {{ number_format($bot->fake) }}</div>
                            </div>
                            <div class="row">
                                <small class="fs-6 text-warning">{{ $bot->desc }}</small>
                                <div>{{ __('locale.Highest APR Today')}}: <span
                                        class="text-success">{{ number_format($bot->perc) }}%</span></div>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>


@endsection
@section('page-script')
<script src="{{ asset('vendors/js/charts/apexcharts.min.js') }}"></script>
@endsection
@push('script')
@php
    $chartAsset = $crypto ? 'crypto' : (strtolower($pair) === 'stock' ? 'stock' : 'forex');
@endphp
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.js-market-chart').forEach(function (container) {
            mountNativeMarketChart(container);
        });
        $('.se-pre-con').fadeOut('slow');
    });

    function mountNativeMarketChart(container) {
        var symbol = @json(strtoupper($symbol));
        var pair = @json(strtoupper($pair));
        var asset = @json($chartAsset);
        var endpoint = @json(route('user.market.candles'));
        var candleChart = null, macdChart = null;

        container.innerHTML = '<div class="native-chart">' +
            '<div class="native-chart__topbar"><div class="native-chart__identity"><span class="native-chart__mark">' + symbol.charAt(0) + '</span><div><div class="native-chart__symbol">' + symbol + (asset === 'stock' ? '' : ' / ' + pair) + '</div><div class="native-chart__price">Loading live market data…</div></div></div>' +
            '<div class="native-chart__intervals" aria-label="Chart interval"><button type="button" class="native-chart__interval" data-interval="1min">1m</button><button type="button" class="native-chart__interval" data-interval="5min">5m</button><button type="button" class="native-chart__interval is-active" data-interval="15min">15m</button><button type="button" class="native-chart__interval" data-interval="1h">1h</button><button type="button" class="native-chart__interval" data-interval="1day">1D</button></div></div>' +
            '<div class="native-chart__body"><div class="native-chart__status"><div><div class="native-chart__loader"></div><span>Loading live candles…</span></div></div><div class="native-chart__main"></div><div class="native-chart__indicator"></div></div>' +
            '<div class="native-chart__footer"><span>MACD 12 · 26 · 9</span><span>UTC · Live market data</span></div></div>';

        var status = container.querySelector('.native-chart__status');
        var price = container.querySelector('.native-chart__price');
        container.querySelectorAll('.native-chart__interval').forEach(function (button) {
            button.addEventListener('click', function () {
                container.querySelectorAll('.native-chart__interval').forEach(function (item) { item.classList.remove('is-active'); });
                button.classList.add('is-active'); load(button.dataset.interval);
            });
        });

        function setStatus(message, retry) {
            status.hidden = !message;
            if (!message) return;
            status.innerHTML = retry ? '<div><div>' + message + '</div><button type="button" class="native-chart__retry">Retry</button></div>' : '<div><div class="native-chart__loader"></div><span>' + message + '</span></div>';
            var retryButton = status.querySelector('.native-chart__retry');
            if (retryButton) retryButton.addEventListener('click', function () { load(container.querySelector('.is-active').dataset.interval); });
        }
        function ema(values, period) {
            var multiplier = 2 / (period + 1), result = [], previous = values[0] || 0;
            values.forEach(function (value, index) { previous = index ? (value - previous) * multiplier + previous : value; result.push(previous); });
            return result;
        }
        function macdSeries(candles) {
            var closes = candles.map(function (bar) { return bar.close; }), fast = ema(closes, 12), slow = ema(closes, 26);
            var line = closes.map(function (_, index) { return fast[index] - slow[index]; }), signal = ema(line, 9);
            return {
                line: candles.map(function (bar, index) { return [bar.time, line[index]]; }),
                signal: candles.map(function (bar, index) { return [bar.time, signal[index]]; }),
                histogram: candles.map(function (bar, index) { return { x: bar.time, y: line[index] - signal[index], fillColor: line[index] >= signal[index] ? '#20e19a' : '#ff5263' }; })
            };
        }
        function render(payload) {
            var candles = payload.candles || [];
            if (!candles.length) throw new Error('No candle data was returned.');
            if (candleChart) candleChart.destroy(); if (macdChart) macdChart.destroy();
            var baseChart = { foreColor: '#819089', fontFamily: 'inherit', animations: { enabled: false }, toolbar: { show: false }, zoom: { enabled: true, type: 'x', autoScaleYaxis: true }, background: 'transparent' };
            candleChart = new ApexCharts(container.querySelector('.native-chart__main'), {
                chart: Object.assign({ type: 'candlestick', height: '100%' }, baseChart),
                series: [{ name: 'Price', type: 'candlestick', data: candles.map(function (bar) { return { x: bar.time, y: [bar.open, bar.high, bar.low, bar.close] }; }) }, { name: 'Volume', type: 'bar', data: candles.map(function (bar) { return { x: bar.time, y: bar.volume, fillColor: bar.close >= bar.open ? 'rgba(32,225,154,.22)' : 'rgba(255,82,99,.22)' }; }) }],
                plotOptions: { candlestick: { colors: { upward: '#20e19a', downward: '#ff5263' }, wick: { useFillColor: true } }, bar: { columnWidth: '78%' } }, stroke: { width: [1, 0] },
                grid: { borderColor: 'rgba(148,163,184,.09)', strokeDashArray: 2 }, xaxis: { type: 'datetime', labels: { datetimeUTC: true }, axisBorder: { color: 'rgba(148,163,184,.16)' } },
                yaxis: [{ opposite: true, decimalsInFloat: 5, tooltip: { enabled: true } }, { show: false, seriesName: 'Volume' }], tooltip: { theme: 'dark', shared: true }, legend: { show: false }
            });
            var macd = macdSeries(candles);
            macdChart = new ApexCharts(container.querySelector('.native-chart__indicator'), {
                chart: Object.assign({ type: 'line', height: '100%' }, baseChart), series: [{ name: 'MACD', type: 'line', data: macd.line }, { name: 'Signal', type: 'line', data: macd.signal }, { name: 'Histogram', type: 'bar', data: macd.histogram }],
                colors: ['#42a5ff', '#ff9f43', '#20e19a'], stroke: { width: [2, 2, 0], curve: 'smooth' }, plotOptions: { bar: { columnWidth: '76%' } },
                grid: { borderColor: 'rgba(148,163,184,.08)', strokeDashArray: 2 }, xaxis: { type: 'datetime', labels: { datetimeUTC: true }, axisBorder: { color: 'rgba(148,163,184,.16)' } }, yaxis: { opposite: true, decimalsInFloat: 5 }, tooltip: { theme: 'dark', shared: true }, legend: { show: false }
            });
            Promise.all([candleChart.render(), macdChart.render()]).then(function () { setStatus(null); });
            var last = candles[candles.length - 1], change = last.close - last.open;
            price.innerHTML = '<strong>' + last.close.toLocaleString(undefined, { maximumFractionDigits: 6 }) + '</strong> <span style="color:' + (change >= 0 ? '#20e19a' : '#ff5263') + '">' + (change >= 0 ? '+' : '') + change.toFixed(6) + '</span> · ' + payload.provider;
        }
        function load(interval) {
            setStatus('Loading live candles…', false);
            var query = new URLSearchParams({ symbol: symbol, pair: pair, asset: asset, interval: interval });
            fetch(endpoint + '?' + query.toString(), { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
                .then(function (response) { return response.json().then(function (body) { if (!response.ok) throw new Error(body.message || 'Market data request failed.'); return body; }); })
                .then(render).catch(function (error) { setStatus(error.message, true); price.textContent = 'Market data unavailable'; });
        }
        load('15min');
    }
</script>
{{-- Legacy Binance socket and Devexperts chart code removed: those browser-direct
     services are region-blocked or retired and left the production chart blank. --}}
{{--
    <script>
        "use strict";
        $(document).ready(function () {
            var i = 0,
                delay = 5000,
                value = 0,
                valueStore = 0,
                tick = 1,
                tickStore = 1,
                tickDiff = 0,
                tickDiffValue = 0;

            function valBetween(v, min, max) {
                return (Math.min(max, Math.max(min, v)));
            }
            (function loop() {
                value = Math.ceil(Math.random() * 10);
                tick = valBetween(Math.ceil((value / 10) * 28), 1, 28);
                tickDiff = Math.abs(tick - tickStore);
                tickDiffValue = Math.abs(value - valueStore) / tickDiff;
                var counter = 0,
                    valueStoreTemp = valueStore,
                    tickStoreTemp = tickStore;
                if (value > valueStore) {
                    for (i = tickStoreTemp; i <= tick; i++) {
                        (function (i) {
                            setTimeout(function () {
                                $('#gauge').css('box-shadow', '0 0 32px rgba(21, 55, 172, 0.25), inset 0 -192px 192px -240px ' + $('#gauge path:nth-child(' + i + ')')[0].style.fill + ', inset 0 0 2px -1px ' + $('#gauge path:nth-child(' + i + ')')[0].style.fill);
                                $('#gauge path:nth-child(' + i + ')').show();
                                $('#gauge-label')
                                    .css('color', $('#gauge path:nth-child(' + i + ')')[0].style.fill)
                                    .text(Math.ceil(valueStoreTemp + (tickDiffValue * Math.abs(tickStoreTemp - i))));
                                if (i == tick) {
                                    $('#gauge-label').text(value);
                                }
                            }, 50 * counter);
                            counter++;
                        }(i));
                    }
                } else if (value < valueStore) {
                    for (i = tickStoreTemp; i >= tick; i--) {
                        (function (i) {
                            setTimeout(function () {
                                $('#gauge').css('box-shadow', '0 0 32px rgba(21, 55, 172, 0.25), inset 0 -192px 192px -240px ' + $('#gauge path:nth-child(' + i + ')')[0].style.fill + ', inset 0 0 2px -1px ' + $('#gauge path:nth-child(' + i + ')')[0].style.fill);
                                $('#gauge path:nth-child(' + i + ')').hide();
                                $('#gauge-label')
                                    .css('color', $('#gauge path:nth-child(' + i + ')')[0].style.fill)
                                    .text(Math.floor(valueStoreTemp - (tickDiffValue * Math.abs(tickStoreTemp - i))));
                                if (i == tick) {
                                    $('#gauge-label').text(value);
                                }
                            }, 50 * counter);
                            counter++;
                        }(i));
                    }
                }
                valueStore = value;
                tickStore = tick;
                window.setTimeout(loop, delay);
            })();
        });
        window.onload = prepareButton;
        let ws_i = new WebSocket('wss://stream.binance.com:9443/ws');
        async function prepareButton() {
            $(".se-pre-con").fadeOut("slow");
            var timesClicked = 0;
            if (ws_i.readyState == WebSocket.OPEN) {
                ws_i.send(JSON.stringify({
                    'method': 'UNSUBSCRIBE',
                    'params': ['{{strtolower($symbol)}}{{strtolower($pair)}}@depth10@100ms'],
                    'id': 1
                }))
                ws_i.send(JSON.stringify({
                    'method': 'UNSUBSCRIBE',
                    'params': ['{{strtolower($symbol)}}{{strtolower($pair)}}@depth20@100ms'],
                    'id': 2
                }))
                ws_i.send(JSON.stringify({
                    'method': 'UNSUBSCRIBE',
                    'params': ['{{strtolower($symbol)}}{{strtolower($pair)}}@ticker'],
                    'id': 3
                }))
                ws_i.send(JSON.stringify({
                    'method': 'UNSUBSCRIBE',
                    'params': ['{{strtolower($symbol)}}{{strtolower($pair)}}@miniTicker'],
                    'id': 4
                }))
            }
            document.getElementById('toggleInfo').onclick = function () {
                timesClicked++;
                if (timesClicked%2 == 0) {
                    ws_i.send(JSON.stringify({
                        'method': 'UNSUBSCRIBE',
                        'params': ['{{strtolower($symbol)}}{{strtolower($pair)}}@ticker'],
                        'id': 3
                    }))
                } else {
                    /* Binance */
                    let el_b = document.getElementById('show_b');
                    let last_b = null;
                    let el_P = document.getElementById('show_P');
                    let el_p = document.getElementById('show_p');
                    let slider = document.getElementById("myRange");
                    let sliderColor = document.getElementById("myRangeColor");
                    let el_l = document.getElementById('show_l');
                    let el_h = document.getElementById('show_h');
                    let min = document.getElementById("myRange").getAttribute('aria-valuemin');
                    let max = document.getElementById("myRange").getAttribute('aria-valuemax');
                    let now = document.getElementById("myRange").getAttribute('aria-valuenow');
                    let el_v = document.getElementById('show_v');
                    let el_mc = document.getElementById('show_mc');
                    let el_ts = document.getElementById('show_ts');
                    ws_i.send(JSON.stringify({
                        'method': 'SUBSCRIBE',
                        'params': ['{{strtolower($symbol)}}{{strtolower($pair)}}@ticker'],
                        'id': 3
                    }))
                    ws_i.onmessage = function (event1) {
                        let data = JSON.parse(event1.data);
                        let b = parseFloat(data.b).toFixed(2);
                        let P = parseFloat(data.P).toFixed(2);
                        let p = parseFloat(data.p).toFixed(2);
                        let o = parseFloat(data.o).toFixed(2);
                        let h = parseFloat(data.h).toFixed(2);
                        let l = parseFloat(data.l).toFixed(2);
                        let v = parseFloat(data.v).toFixed(2);
                        let q = parseFloat(data.q).toFixed(2);
                        let c = parseFloat(data.c).toFixed(2);
                        el_b.innerText = b;
                        if (b < last_b) {
                            el_b.innerHTML = b + '<i class="bi bi-arrow-down"></i>';
                            el_b.style.color = 'rgb(246,70,93';
                        } else if (b > last_b) {
                            el_b.innerHTML = b + '<i class="bi bi-arrow-up"></i>';
                            el_b.style.color = 'rgb(14,203,129)';
                        } else {
                            el_b.innerText = b;
                            el_b.style.color = 'dark';
                        }
                        last_b = b;
                        el_P.innerText = P;
                        el_p.innerText = p;
                        if (P < 0) {
                            el_P.innerHTML = '<i class="bi bi-arrow-down"></i>' + P + '%';
                            el_P.style.color = 'rgb(246,70,93';
                        } else if (P > 0) {
                            el_P.innerHTML = '<i class="bi bi-arrow-up"></i>' + P + '%';
                            el_P.style.color = 'rgb(14,203,129)';
                        } else {
                            el_P.innerHTML = '(' + P + ')';
                            el_P.style.color = 'dark';
                        }
                        el_p.innerText = p;

                        if (p < 0) {
                            el_p.innerText = p;
                            el_p.style.color = 'rgb(246,70,93';
                        } else if (p > 0) {
                            el_p.innerText = p;
                            el_p.style.color = 'rgb(14,203,129)';
                        } else {
                            el_p.innerText = p;
                            el_p.style.color = 'dark';
                        }
                        el_l.innerText = l;
                        el_h.innerText = h;
                        min = l;
                        max = h;
                        now = b;
                        let siz = 100 * ((now - min) / (max - min));
                        slider.style = 'width: ' + siz + '%';
                        if (siz > 66) {
                            sliderColor.classList.add('progress-bar-success')
                            sliderColor.classList.remove('progress-bar-warning')
                            sliderColor.classList.remove('progress-bar-danger')
                        } else if (siz > 33 && siz < 66) {
                            sliderColor.classList.add('progress-bar-warning')
                            sliderColor.classList.remove('progress-bar-success')
                            sliderColor.classList.remove('progress-bar-danger')
                        } else if (siz < 33) {
                            sliderColor.classList.add('progress-bar-danger')
                            sliderColor.classList.remove('progress-bar-success')
                            sliderColor.classList.remove('progress-bar-warning')
                        }
                        el_v.innerText = v;
                        let mc = ((q * c) / 100000000000).toFixed(2);
                        let ts = (q / 100000000).toFixed(2);
                        el_mc.innerText = mc + 'B';
                        el_ts.innerText = ts + 'M';
                    };
                }
            }
            document.getElementById('toggleDepth').onclick = function () {
                timesClicked++;
                if (timesClicked%2 == 0) {
                    ws_i.send(JSON.stringify({
                            'method': 'UNSUBSCRIBE',
                            'params': ['{{strtolower($symbol)}}{{strtolower($pair)}}@depth20@100ms'],
                            'id': 2
                        }))
                    } else {
                        ws_i.send(JSON.stringify({
                                'method': 'SUBSCRIBE',
                                'params': ['{{strtolower($symbol)}}{{strtolower($pair)}}@depth20@100ms'],
                                'id': 2
                            }))
                am5.ready(function () {
                    var root = am5.Root.new("chartdiv");
                    root.setThemes([
                        am5themes_Animated.new(root),
                        am5themes_Dark.new(root),
                    ]);
                    var chart = root.container.children.push(
                        am5xy.XYChart.new(root, {
                            focusable: true,
                            panX: false,
                            panY: false,
                            wheelX: "none",
                            wheelY: "none"
                        })
                    );

                    var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
                        categoryField: "value",
                        renderer: am5xy.AxisRendererX.new(root, {
                            minGridDistance: 70
                        }),
                        tooltip: am5.Tooltip.new(root, {})
                    }));

                    xAxis.get("renderer").labels.template.adapters.add("text", function (text, target) {
                        if (target.dataItem) {
                            return root.numberFormatter.format(Number(target.dataItem.get("category")), "#.####");
                        }
                        return text;
                    });

                    var yAxis = chart.yAxes.push(
                        am5xy.ValueAxis.new(root, {
                            maxDeviation: 0.1,
                            renderer: am5xy.AxisRendererY.new(root, {})
                        })
                    );

                    var bidsTotalVolume = chart.series.push(am5xy.StepLineSeries.new(root, {
                        minBulletDistance: 10,
                        xAxis: xAxis,
                        yAxis: yAxis,
                        valueYField: "bidstotalvolume",
                        categoryXField: "value",
                        stroke: am5.color(0x00ff00),
                        fill: am5.color(0x00ff00),
                        tooltip: am5.Tooltip.new(root, {
                            pointerOrientation: "horizontal",
                            labelText: "[width: 120px]Ask:[/][bold]{categoryX}[/]\n[width: 120px]Total volume:[/][bold]{valueY}[/]\n[width: 120px]Volume:[/][bold]{bidsvolume}[/]"
                        })
                    }));
                    bidsTotalVolume.strokes.template.set("strokeWidth", 2)
                    bidsTotalVolume.fills.template.setAll({
                        visible: true,
                        fillOpacity: 0.2
                    });

                    var asksTotalVolume = chart.series.push(am5xy.StepLineSeries.new(root, {
                        minBulletDistance: 10,
                        xAxis: xAxis,
                        yAxis: yAxis,
                        valueYField: "askstotalvolume",
                        categoryXField: "value",
                        stroke: am5.color(0xf00f00),
                        fill: am5.color(0xff0000),
                        tooltip: am5.Tooltip.new(root, {
                            pointerOrientation: "horizontal",
                            labelText: "[width: 120px]Ask:[/][bold]{categoryX}[/]\n[width: 120px]Total volume:[/][bold]{valueY}[/]\n[width: 120px]Volume:[/][bold]{asksvolume}[/]"
                        })
                    }));
                    asksTotalVolume.strokes.template.set("strokeWidth", 2)
                    asksTotalVolume.fills.template.setAll({
                        visible: true,
                        fillOpacity: 0.2
                    });

                    var bidVolume = chart.series.push(am5xy.ColumnSeries.new(root, {
                        minBulletDistance: 10,
                        xAxis: xAxis,
                        yAxis: yAxis,
                        valueYField: "bidsvolume",
                        categoryXField: "value",
                        fill: am5.color(0x000000)
                    }));
                    bidVolume.columns.template.set("fillOpacity", 0.2);

                    var asksVolume = chart.series.push(am5xy.ColumnSeries.new(root, {
                        minBulletDistance: 10,
                        xAxis: xAxis,
                        yAxis: yAxis,
                        valueYField: "asksvolume",
                        categoryXField: "value",
                        fill: am5.color(0x000000)
                    }));
                    asksVolume.columns.template.set("fillOpacity", 0.2);

                    var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                        xAxis: xAxis
                    }));
                    cursor.lineY.set("visible", false);

                        function loadData() {
                            ws_i.onmessage = function (event2) {
                                var data = JSON.parse(event2.data);
                                var res = [];
                                processData(data.bids, "bids", true, res);
                                processData(data.asks, "asks", false, res);
                                xAxis.data.setAll(res);
                                bidsTotalVolume.data.setAll(res);
                                asksTotalVolume.data.setAll(res);
                                bidVolume.data.setAll(res);
                                asksVolume.data.setAll(res);
                            };
                        }

                        loadData();

                        function processData(list, type, desc, res) {

                            for (var i = 0; i < list.length; i++) {
                                list[i] = {
                                    value: Number(list[i][0]),
                                    volume: Number(list[i][1]),
                                }
                            }

                            list.sort(function (a, b) {
                                if (a.value > b.value) {
                                    return 1;
                                } else if (a.value < b.value) {
                                    return -1;
                                } else {
                                    return 0;
                                }
                            });

                            if (desc) {
                                for (var i = list.length - 1; i >= 0; i--) {
                                    if (i < (list.length - 1)) {
                                        list[i].totalvolume = list[i + 1].totalvolume + list[i].volume;
                                    } else {
                                        list[i].totalvolume = list[i].volume;
                                    }
                                    var dp = {};
                                    dp["value"] = list[i].value;
                                    dp[type + "volume"] = list[i].volume;
                                    dp[type + "totalvolume"] = list[i].totalvolume;
                                    res.unshift(dp);
                                }
                            } else {
                                for (var i = 0; i < list.length; i++) {
                                    if (i > 0) {
                                        list[i].totalvolume = list[i - 1].totalvolume + list[i].volume;
                                    } else {
                                        list[i].totalvolume = list[i].volume;
                                    }
                                    var dp = {};
                                    dp["value"] = list[i].value;
                                    dp[type + "volume"] = list[i].volume;
                                    dp[type + "totalvolume"] = list[i].totalvolume;
                                    res.push(dp);
                                }
                            }
                        }

                });
            }

            }
            document.getElementById('toggleOrders').onclick = function () {
                timesClicked++;
                if (timesClicked%2 == 0) {
                    ws_i.send(JSON.stringify({
                        'method': 'UNSUBSCRIBE',
                        'params': ['{{strtolower($symbol)}}{{strtolower($pair)}}@depth10@100ms'],
                        'id': 1
                    }))
                } else {
                    var computBarWidth = {
                        width: 250,
                        sortDepth: {
                            sort: function (e) {
                                return e.sort(function (e, t) {
                                    return e[1] - t[1]
                                }), e
                            },
                            median: function (e) {
                                var t = Math.floor(e.length / 3 * 2);
                                return e[t][1] < 1 ? 1 : e[t][1]
                            },
                            medianUnit: function (e, t, n) {
                                var r = new Array(e);
                                r = r[0];
                                var o = new Array(t);
                                o = o[0], r = r.concat(o);
                                var i = this.median(this.sort(r)) / n;
                                return o = r = null, i
                            },
                            width: function (e, t) {
                                if (0 == t) return 1;
                                var n = Math.round(Number(e) / t);
                                return n <= 0 ? 1 : 160 < n ? 160 : n
                            }
                        },
                        init: function (e, t) {
                            var n = [],
                                r = [];
                            e.forEach(function (e) {
                                n.push(e);
                            }), t.forEach(function (e) {
                                r.push(e)
                            });
                            var o = this.sortDepth.medianUnit(n, r, 48);
                            e.forEach(function (e) {
                                e.push({
                                    width: computBarWidth.sortDepth.width(e[1], o) * computBarWidth.width / 100
                                })
                            }), t.forEach(function (e) {
                                e.push({
                                    width: computBarWidth.sortDepth.width(e[1], o) * computBarWidth.width / 100
                                })
                            })
                        }
                    }

                    function number_format(number, decimals, decPoint, thousandsSep) {
                        number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
                        var n = !isFinite(+number) ? 0 : +number
                        var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
                        var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
                        var dec = (typeof decPoint === 'undefined') ? '.' : decPoint
                        var s = ''

                        var toFixedFix = function (n, prec) {
                            var k = Math.pow(10, prec)
                            return '' + (Math.round(n * k) / k)
                                .toFixed(prec)
                        }

                        // @todo: for IE parseFloat(0.55).toFixed(0) = 0;
                        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.')
                        if (s[0].length > 3) {
                            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
                        }
                        if ((s[1] || '').length < prec) {
                            s[1] = s[1] || ''
                            s[1] += new Array(prec - s[1].length + 1).join('0')
                        }

                        return s.join(dec)
                    }
                    ws_i.send(JSON.stringify({
                        'method': 'SUBSCRIBE',
                        'params': ['{{strtolower($symbol)}}{{strtolower($pair)}}@depth10@100ms'],
                        'id': 1
                    }))
                    ws_i.onmessage = function (event3) {
                        let data = JSON.parse(event3.data);

                        computBarWidth.init(data.bids, data.asks);

                        $('.asks,.bids').empty();

                        $.each(data.asks, function (index, item) {
                            let row = $('<tr>')
                                .append($('<td>').css("color", "rgb(246,70,93)").addClass("price").append($("<span>").text(Number(item[0]).toFixed(2))))
                                .append($('<td>').addClass("quantity").append($("<span>").text(Number(item[1]).toFixed(6))))
                                .append($('<td>').addClass("btc").append($("<span>").text(number_format((item[0] * item[1]), 2, ",")))
                                    .append($("<div>").addClass("percent").css("width", item[2].width + "px")));

                            if (index > 10) row.hide();

                            $('.asks').prepend(row);
                        });
                        $.each(data.bids, function (index, item) {
                            let row = $('<tr>')
                                .append($('<td>').css("color", "rgb(14,203,129)").addClass("price").append($("<span>").text(Number(item[0]).toFixed(2))))
                                .append($('<td>').addClass("quantity").append($("<span>").text(Number(item[1]).toFixed(6))))
                                .append($('<td>').addClass("btc").append($("<span>").text(number_format((item[0] * item[1]), 2, ",")))
                                    .append($("<div>").addClass("percent").css("width", item[2].width + "px")));

                            if (index > 10) row.hide();

                            $('.bids').prepend(row);
                        });
                    }
                }
            }
        }
    </script>
--}}
@endpush
