<?php

namespace App\Http\Middleware;

use App\Models\KYC;
use App\Models\Platform;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckKYC
{
    public function handle($request, Closure $next)
    {
        if(Platform::where('id',1)->exists()) {
            $platform = Platform::where('id',1)->first();
            if($platform->kyc == 1){
                if(KYC::where('userId',Auth::user()->id)->exists()){
                    $status = KYC::where('userId',Auth::user()->id)->first();
                    if ($status->status != 'approved') {
                        $notify[] = ['warning', 'Verify your identify first!'];
                        return $request->expectsJson()
                            ? abort(403, 'Your Identity is not verified.')
                            : redirect()->route('user.kyc')->withNotify($notify);
                    }
                } else {
                    $notify[] = ['warning', 'Verify your identify first!'];
                    return $request->expectsJson()
                        ? abort(403, 'Your Identity is not verified.')
                        : redirect()->route('user.kyc')->withNotify($notify);
                }
            }
        }

        return $next($request);
    }
}
