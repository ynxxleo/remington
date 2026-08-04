@extends('layouts.app')
@section('content')


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/css/bootstrap.min.css" integrity="sha512-T584yQ/tdRR5QwOpfvDfVQUidzfgc2339Lc8uBDtcp/wYu80d7jwBgAxbyMh0a9YM9F8N3tdErpFI8iaGx6x5g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
    
    /* The snackbar - position it at the bottom and in the middle of the screen */
    #snackbar {
        visibility: hidden; /* Hidden by default. Visible on click */
        min-width: 250px; /* Set a default minimum width */
        margin-left: -125px; /* Divide value of min-width by 2 */
        background-color: #333; /* Black background color */
        color: #fff; /* White text color */
        text-align: center; /* Centered text */
        border-radius: 2px; /* Rounded borders */
        padding: 16px; /* Padding */
        position: fixed; /* Sit on top of the screen */
        z-index: 1; /* Add a z-index if needed */
        left: 50%; /* Center the snackbar */
        bottom: 30px; /* 30px from the bottom */
    }
    
    /* Show the snackbar when clicking on a button (class added with JavaScript) */
    #snackbar.show {
        visibility: visible; /* Show the snackbar */
        /* Add animation: Take 0.5 seconds to fade in and out the snackbar. However, delay the fade out process for 2.5 seconds */
        -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
        animation: fadein 0.5s, fadeout 0.5s 2.5s;
    }
    
    /* Animations to fade the snackbar in and out */
    @-webkit-keyframes fadein {
        from {bottom: 0; opacity: 0;}
        to {bottom: 30px; opacity: 1;}
    }
    
    @keyframes fadein {
        from {bottom: 0; opacity: 0;}
        to {bottom: 30px; opacity: 1;}
    }
    
    @-webkit-keyframes fadeout {
        from {bottom: 30px; opacity: 1;}
        to {bottom: 0; opacity: 0;}
    }
    
    @keyframes fadeout {
        from {bottom: 30px; opacity: 1;}
        to {bottom: 0; opacity: 0;}
    }
    
    .actionLoader, .actionLoader2 {
        display: none;
    }
    
    .json-response {
        padding: 12px;
        color: white;
        border: none;
        border-radius: 10px;
    }
    
    .error-snack {
        color: whitesmoke;
        background-color: #EE4B2B;
    }
    
    .success-snack {
        color: grey;
        background-color: #00A36C;
    }
    
    .base_messaging {
        background-color: #00A36C;
        color: white;
        padding: 15px;
        border-radius: 15px;
        margin: 10px;
    }
    
    #custom-button {
      padding: 10px;
      color: white;
      background-color: #009578;
      border: 1px solid #000;
      border-radius: 5px;
      cursor: pointer;
    }
    
    #custom-button:hover {
      background-color: #00b28f;
    }
    
    #custom-text {
      margin-left: 10px;
      font-family: sans-serif;
      color: #aaa;
    }
    
    /*.full-width {*/
    /*    display: block;*/
    /*    width: 100%;*/
    /*}*/
    
    .error-response {
        background-color: #800020;
    }
    
    .success-response {
        background-color: #097969;
    }
    
    .plain-response {
        display: none;
        padding: 20px;
        border: none;
        border-radius: 12px;
        color: white;
        text-align: center;
    }
    
    .plain-response2 {
        display: none;
        padding: 20px;
        border: none;
        border-radius: 12px;
        color: white;
        text-align: center;
    }
    
</style>

<?php
    function callApiGET($url)
    {
        $ch = curl_init($url);
        $options = [
            CURLOPT_POST => false,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
        ];
        curl_setopt_array($ch, $options);
        $data = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return array("response" => $data, "code" => $httpcode);
    }
?>

<div class="row" id="table-hover-row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Become Master Users</h4><div class="card-search"></div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover custom-data-bs-table">
                    <thead>
                            <th>{{ __('locale.ID')}}</th>
                            <th>{{ __('Profile Image')}}</th>
                            <th>{{ __('locale.First Name')}}</th>
                            <th>{{ __('locale.Last Name')}}</th>
                            <th>{{ __('locale.Username')}}</th>
                            <th>{{ __('locale.Date')}}</th>
                            <th>{{ __('locale.Action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($referrals as $referral)
                            <tr>
                                <td data-label="{{ __('locale.ID')}}">{{$loop->iteration}}</td>
                                <?php
                                    
                                    $result = callApiGET('https://spotalert-api.herokuapp.com/api/v1/profile/profile-image/'.$referral->id);
                                    $responseCode = $result["code"];
                                    $result = $result["response"];
                                    $result = json_decode($result, true);
                                    $profile = $result["profile"];
                                    
                                    if ($profile) {
                                        $image = $profile["image"];
                                        if ($image != "") {
                                ?>
                                            <td data-label="{{ __('Profile Image')}}">
                                                <img src="data:image/jpeg;base64,{{__($image)}}" class="rounded-circle" height="80px" width="80px">
                                            </td>
                                <?php
                                        }
                                        else {
                                ?>
                                            <td data-label="{{ __('Profile Image')}}">No Profile Yet</td>
                                <?php
                                        }
                                    }
                                    else {
                                ?>
                                        <td data-label="{{ __('Profile Image')}}">No Profile Yet</td>
                                <?php
                                    }
                                ?>
                                <td data-label="{{ __('locale.First Name')}}">{{__($referral->firstname)}}</td>
                                <td data-label="{{ __('locale.Last Name')}}">{{__($referral->lastname)}}</td>
                                <td data-label="{{ __('locale.Username')}}"> {{__($referral->username)}}</td>
                                <td data-label="{{ __('locale.Date')}}">{{ showDateTime($referral->created_at) }}</td>
                                <td data-label="{{ __('locale.Action')}}">
                                    <?php if ($referral->isMaster == 1): ?>
                                    <button class="btn btn-danger" onclick="approveRef('{{__($referral->email)}}')">Start Approval</button>
                                    <?php endif; ?>
                                    <?php if ($referral->isMaster == 2): ?>
                                    <button class="btn btn-danger" onclick="masterProfile('{{__($referral->email)}}', '{{__($referral->copy_gain)}}', '{{__($referral->copy_copier1)}}', '{{__($referral->copy_copier2)}}', '{{__($referral->copy_profit)}}', '{{__($referral->copy_loss)}}', '{{__($referral->copy_floating_profit)}}', '{{__($referral->copy_equity)}}', '{{__($referral->copy_master_trader_bonus)}}', '{{__($referral->copy_leverage)}}', '{{__($referral->copy_fee)}}', '{{__($referral->copy_min_amt)}}')">Update Masters Profile</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%">{{ __($empty_message) }}</td>
                            </tr>
                        @endforelse
                        <input type="hidden" id="routingUrl" value="{{ route('user.becomemasterfinal') }}" />
                        <input type="hidden" id="routingUrl2" value="{{ route('user.becomemasterfinally') }}" />
                    </tbody>
                </table>
            </div>

        </div>
        <div class="mb-1">{{paginateLinks($referrals) }}</div>
    </div>
</div>

<!-- Use a button to open the snackbar -->
<button onclick="showSnacks()" class="openSnackings" style="display: none;">Show Snackbar</button>
<!-- The actual snackbar -->
<div id="snackbar" class="snackbar-main">Some text some message..</div>

<button type="button" class="btn btn-info btn-lg openModalMyModal2" data-toggle="modal" data-target="#myModal2" style="display: none;">Open Modal</button>
<!-- Modal -->
<div id="myModal2" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close closeChatModal" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    Approve Master Trader
                </h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="form-group">
                            <label for="copy_gain">Gain:</label>
                            <input type="text" class="form-control" placeholder="Enter Gain" id="copy_gain">
                        </div>
                        <br/>
                        
                        <div class="form-group">
                            <label for="copy_copier1">Copier Value 1:</label>
                            <input type="text" class="form-control" placeholder="Enter Copier Value 1" id="copy_copier1">
                        </div>
                        <br/>
                        
                        <div class="form-group">
                            <label for="copy_copier2">Copier Value 2:</label>
                            <input type="text" class="form-control" placeholder="Enter Copier Value 1" id="copy_copier2">
                        </div>
                        <br/>
                        
                        <div class="form-group">
                            <label for="copy_profit">Profit:</label>
                            <input type="text" class="form-control" placeholder="Enter Profit" id="copy_profit">
                        </div>
                        <br/>
                        
                        <div class="form-group">
                            <label for="copy_loss">Loss:</label>
                            <input type="text" class="form-control" placeholder="Enter Loss" id="copy_loss">
                        </div>
                        <br/>
                        
                        <div class="form-group">
                            <label for="copy_floating_profit">Floating Profit:</label>
                            <input type="text" class="form-control" placeholder="Enter Floating Profit" id="copy_floating_profit">
                        </div>
                        <br/>
                        
                        <div class="form-group">
                            <label for="copy_equity">Equity:</label>
                            <input type="text" class="form-control" placeholder="Enter Equity" id="copy_equity">
                        </div>
                        <br/>
                        
                        <div class="form-group">
                            <label for="copy_master_trader_bonus">Master Trader Bonus:</label>
                            <input type="text" class="form-control" placeholder="Enter Master Trader Bonus" id="copy_master_trader_bonus">
                        </div>
                        <br/>
                        
                        <div class="form-group">
                            <label for="copy_leverage">Leverage:</label>
                            <input type="text" class="form-control" placeholder="Enter Leverage" id="copy_leverage">
                        </div>
                        <br/>
                        
                        <div class="form-group">
                            <label for="copy_fee">Copy Fee:</label>
                            <input type="text" class="form-control" placeholder="Enter Copy Fee" id="copy_fee">
                        </div>
                        <br/>
                        
                        <div class="form-group">
                            <label for="copy_min_amt2">Minimum Fee:</label>
                            <input type="text" class="form-control" placeholder="Enter Minimum Fee" id="copy_min_amt">
                        </div>
                        
                        <div class="form-group">
                            
                            <br/>
                            <div class="plain-response"></div>
                            <br/>
                            
                            <button class="btn btn-success" onclick="finalApprove()">
                                <span class="actionText">Approve</span>
                                <div class="spinner-border actionLoader" role="status">
                                    <span class="sr-only"></span>
                                </div>
                            </button>
                            
                        </div>
                    
                    </div>
                    
                    <br/>
                
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            
        </div>
        
    </div>
</div>

<button type="button" class="btn btn-info btn-lg openModalMyModal3" data-toggle="modal" data-target="#myModal3" style="display: none;">Open Modal</button>
<!-- Modal -->
<div id="myModal3" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close closeChatModal" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    Update Master Trader
                </h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="form-group">
                            <label for="copy_gain2">Gain:</label>
                            <input type="text" class="form-control" placeholder="Enter Gain" id="copy_gain2">
                        </div>
                        <br/>
                        
                        <div class="form-group">
                            <label for="copy_copier12">Copier Value 1:</label>
                            <input type="text" class="form-control" placeholder="Enter Copier Value 1" id="copy_copier12">
                        </div>
                        <br/>
                        
                        <div class="form-group">
                            <label for="copy_copier22">Copier Value 2:</label>
                            <input type="text" class="form-control" placeholder="Enter Copier Value 1" id="copy_copier22">
                        </div>
                        <br/>
                        
                        <div class="form-group">
                            <label for="copy_profit2">Profit:</label>
                            <input type="text" class="form-control" placeholder="Enter Profit" id="copy_profit2">
                        </div>
                        <br/>
                        
                        <div class="form-group">
                            <label for="copy_loss2">Loss:</label>
                            <input type="text" class="form-control" placeholder="Enter Loss" id="copy_loss2">
                        </div>
                        <br/>
                        
                        <div class="form-group">
                            <label for="copy_floating_profit2">Floating Profit:</label>
                            <input type="text" class="form-control" placeholder="Enter Floating Profit" id="copy_floating_profit2">
                        </div>
                        <br/>
                        
                        <div class="form-group">
                            <label for="copy_equity2">Equity:</label>
                            <input type="text" class="form-control" placeholder="Enter Equity" id="copy_equity2">
                        </div>
                        <br/>
                        
                        <div class="form-group">
                            <label for="copy_master_trader_bonus2">Master Trader Bonus:</label>
                            <input type="text" class="form-control" placeholder="Enter Master Trader Bonus" id="copy_master_trader_bonus2">
                        </div>
                        <br/>
                        
                        <div class="form-group">
                            <label for="copy_leverage2">Leverage:</label>
                            <input type="text" class="form-control" placeholder="Enter Leverage" id="copy_leverage2">
                        </div>
                        <br/>
                        
                        <div class="form-group">
                            <label for="copy_fee2">Copy Fee:</label>
                            <input type="text" class="form-control" placeholder="Enter Copy Fee" id="copy_fee2">
                        </div>
                        <br/>
                        
                        <div class="form-group">
                            <label for="copy_min_amt2">Minimum Fee:</label>
                            <input type="text" class="form-control" placeholder="Enter Minimum Fee" id="copy_min_amt2">
                        </div>
                        
                        <div class="form-group">
                            
                            <br/>
                            <div class="plain-response2"></div>
                            <br/>
                            
                            <button class="btn btn-success" onclick="finalMasterUpdate()">
                                <span class="actionText actionText2">Update Copy Profile</span>
                                <div class="spinner-border actionLoader actionLoader2" role="status">
                                    <span class="sr-only"></span>
                                </div>
                            </button>
                            
                        </div>
                    
                    </div>
                    
                    <br/>
                
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            
        </div>
        
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/js/bootstrap.min.js" integrity="sha512-UR25UO94eTnCVwjbXozyeVd6ZqpaAE9naiEUBK/A+QDbfSTQFhPGj5lOR6d8tsgbBk84Ggb5A3EkjsOgPRPcKA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>

    let readerEmail = "";
    
    function approveRef(data) {
        $(".openModalMyModal2").click();
        readerEmail = data;
    }
    
    function masterProfile(data, copy_gain, copy_copier1, copy_copier2, copy_profit, copy_loss, copy_floating_profit, copy_equity, copy_master_trader_bonus, copy_leverage, copy_fee, copy_min_amt) {
        $(".openModalMyModal3").click();
        readerEmail = data;
        
        document.getElementById("copy_gain2").value = copy_gain;
        document.getElementById("copy_copier12").value = copy_copier1;
        document.getElementById("copy_copier22").value = copy_copier2;
        document.getElementById("copy_profit2").value = copy_profit;
        document.getElementById("copy_loss2").value = copy_loss;
        document.getElementById("copy_floating_profit2").value = copy_floating_profit;
        document.getElementById("copy_equity2").value = copy_equity;
        document.getElementById("copy_master_trader_bonus2").value = copy_master_trader_bonus;
        document.getElementById("copy_leverage2").value = copy_leverage;
        document.getElementById("copy_fee2").value = copy_fee;
        document.getElementById("copy_min_amt2").value = copy_min_amt;
        
    }
    
    function finalApprove() {
        
        $(`.actionText`).hide(200);
        $(`.actionLoader`).show(200);
        
        $(`.plain-response`).hide(500);
        $(".plain-response").removeClass("success-response");
        $(".plain-response").removeClass("error-response");
        $(".plain-response").html("");
        
        const copy_gain = document.getElementById("copy_gain").value;
        const copy_copier1 = document.getElementById("copy_copier1").value;
        const copy_copier2 = document.getElementById("copy_copier2").value;
        const copy_profit = document.getElementById("copy_profit").value;
        const copy_loss = document.getElementById("copy_loss").value;
        const copy_floating_profit = document.getElementById("copy_floating_profit").value;
        const copy_equity = document.getElementById("copy_equity").value;
        const copy_master_trader_bonus = document.getElementById("copy_master_trader_bonus").value;
        const copy_leverage = document.getElementById("copy_leverage").value;
        const copy_fee = document.getElementById("copy_fee").value;
        const copy_min_amt = document.getElementById("copy_min_amt").value;
        
        if (copy_gain == "" || copy_copier1 == "" || copy_copier2 == "" || copy_profit == "" || copy_loss == ""  || copy_floating_profit == "" || copy_equity == "" || copy_master_trader_bonus == "" || copy_leverage == "" || copy_fee == "" || copy_min_amt == "") {
            $(`.actionText`).show(200);
            $(`.actionLoader`).hide(200);
            
            $(`.plain-response`).show(500);
            $(".plain-response").removeClass("success-response");
            $(".plain-response").addClass("error-response");
            $(".plain-response").html("Please fill fields to continue");
            return;
        }
        
        var api = `copy_gain=${copy_gain}&copy_copier1=${copy_copier1}&copy_copier2=${copy_copier2}&copy_profit=${copy_profit}&copy_loss=${copy_loss}&copy_floating_profit=${copy_floating_profit}&copy_equity=${copy_equity}&copy_master_trader_bonus=${copy_master_trader_bonus}&copy_leverage=${copy_leverage}&copy_fee=${copy_fee}&copy_min_amt=${copy_min_amt}`;
        
        var routingUrl = document.getElementById("routingUrl").value;
        fetch(`${routingUrl}?email=${readerEmail}&${api}`).then(data => {
            return data.text();
        }).then(response => {
            console.log(response);
            $(`.actionText`).show(200);
            $(`.actionLoader`).hide(200);
            window.setTimeout((time) => {
                window.location.reload();
            }, 1500);
        }).catch((e) => {
            console.log(e);
            $(`.actionText`).show(200);
            $(`.actionLoader`).hide(200);
        });
        
    }
    
    function finalMasterUpdate() {
        
        $(`.actionText2`).hide(200);
        $(`.actionLoader2`).show(200);
        
        $(`.plain-response2`).hide(500);
        $(".plain-response2").removeClass("success-response");
        $(".plain-response2").removeClass("error-response");
        $(".plain-response2").html("");
        
        const copy_gain = document.getElementById("copy_gain2").value;
        const copy_copier1 = document.getElementById("copy_copier12").value;
        const copy_copier2 = document.getElementById("copy_copier22").value;
        const copy_profit = document.getElementById("copy_profit2").value;
        const copy_loss = document.getElementById("copy_loss2").value;
        const copy_floating_profit = document.getElementById("copy_floating_profit2").value;
        const copy_equity = document.getElementById("copy_equity2").value;
        const copy_master_trader_bonus = document.getElementById("copy_master_trader_bonus2").value;
        const copy_leverage = document.getElementById("copy_leverage2").value;
        const copy_fee = document.getElementById("copy_fee2").value;
        const copy_min_amt = document.getElementById("copy_min_amt2").value;
        
        if (copy_gain == "" || copy_copier1 == "" || copy_copier2 == "" || copy_profit == "" || copy_loss == ""  || copy_floating_profit == "" || copy_equity == "" || copy_master_trader_bonus == "" || copy_leverage == "" || copy_fee == "" || copy_min_amt == "") {
            $(`.actionText2`).show(200);
            $(`.actionLoader2`).hide(200);
            
            $(`.plain-response2`).show(500);
            $(".plain-response2").removeClass("success-response");
            $(".plain-response2").addClass("error-response");
            $(".plain-response2").html("Please fill fields to update this profile");
            return;
        }
        
        var api = `copy_gain=${copy_gain}&copy_copier1=${copy_copier1}&copy_copier2=${copy_copier2}&copy_profit=${copy_profit}&copy_loss=${copy_loss}&copy_floating_profit=${copy_floating_profit}&copy_equity=${copy_equity}&copy_master_trader_bonus=${copy_master_trader_bonus}&copy_leverage=${copy_leverage}&copy_fee=${copy_fee}&copy_min_amt=${copy_min_amt}`;
        
        var routingUrl = document.getElementById("routingUrl2").value;
        fetch(`${routingUrl}?email=${readerEmail}&${api}`).then(data => {
            return data.text();
        }).then(response => {
            console.log(response);
            $(`.actionText2`).show(200);
            $(`.actionLoader2`).hide(200);
            window.setTimeout((time) => {
                window.location.reload();
            }, 1500);
        }).catch((e) => {
            console.log(e);
            $(`.actionText2`).show(200);
            $(`.actionLoader2`).hide(200);
        });
        
    }
    
</script>

@endsection



