@extends('layouts.app')
<?php
header ("Access-Control-Allow-Origin: *");
header ("Access-Control-Expose-Headers: Content-Length, X-JSON");
header ("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header ("Access-Control-Allow-Headers: *");
?>
@section('content')
    <div id="market"></div>
@endsection

@section('page-script')
  <script>
    // Use Binance's market-data-only hosts. They avoid the authenticated API
    // gateway and alternate 9443 port, both of which are commonly blocked.
    (() => {
      const NativeWebSocket = window.WebSocket;
      const nativeOpen = window.XMLHttpRequest && window.XMLHttpRequest.prototype.open;

      if (nativeOpen) {
        window.XMLHttpRequest.prototype.open = function (method, url, ...args) {
          const requestedUrl = String(url);
          const endpoint = requestedUrl.includes('/v3/exchangeInfo')
            ? '{{ asset('market/json/exchangeInfo.json') }}'
            : requestedUrl.replace(
                'https://api.binance.com/api',
                'https://data-api.binance.vision/api'
              );

          return nativeOpen.call(this, method, endpoint, ...args);
        };
      }

      if (!NativeWebSocket) return;

      const TradingWebSocket = function (url, protocols) {
        const endpoint = String(url)
          .replace(
            'wss://stream.binance.com:9443',
            'wss://data-stream.binance.vision'
          )
          .replace('/ws/!ticker@arr', '/ws/!ticker_1d@arr');

        return protocols === undefined
          ? new NativeWebSocket(endpoint)
          : new NativeWebSocket(endpoint, protocols);
      };

      TradingWebSocket.prototype = NativeWebSocket.prototype;
      ['CONNECTING', 'OPEN', 'CLOSING', 'CLOSED'].forEach((state) => {
        TradingWebSocket[state] = NativeWebSocket[state];
      });

      window.WebSocket = TradingWebSocket;
    })();
  </script>
  <script async src="{{ asset(mix('js/market.js')) }}"></script>
@endsection
