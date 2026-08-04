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
    
    function search($masterObj, $copiedMaster) {
        foreach($copiedMaster as $key => $obj) {
            if($masterObj->id == $obj["master"]) return true;
        }
        return false;
    }
    
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
                <h4 class="card-title">Master Users</h4><div class="card-search"></div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover custom-data-bs-table">
                    <thead>
                            <th>{{ __('locale.ID')}}</th>
                            <th>{{ __('Profile Photo')}}</th>
                            <th>{{ __('locale.First Name')}}</th>
                            <th>{{ __('locale.Last Name')}}</th>
                            <th>{{ __('locale.Username')}}</th>
                            <!--<th>{{ __('locale.Date')}}</th>-->
                            <th>{{ __('locale.Action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($referrals as $referral)
                            <?php #if ($actualMainUserId != $referral->id): ?>
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
                                            <td data-label="{{ __('Profile Photo')}}">
                                                <img src="data:image/jpeg;base64,{{__($image)}}" class="rounded-circle" height="80px" width="80px">
                                            </td>
                                <?php
                                        }
                                        else {
                                ?>
                                            <td data-label="{{ __('Profile Photo')}}">No Profile Yet</td>
                                <?php
                                        }
                                    }
                                    else {
                                ?>
                                        <td data-label="{{ __('Profile Photo')}}">No Profile Yet</td>
                                <?php
                                    }
                                ?>
                                
                                <td data-label="{{ __('locale.First Name')}}">{{__($referral->firstname)}}</td>
                                <td data-label="{{ __('locale.Last Name')}}">{{__($referral->lastname)}}</td>
                                <td data-label="{{ __('locale.Username')}}"> {{__($referral->username)}}</td>
                                <!--<td data-label="{{ __('locale.Date')}}">{{ showDateTime($referral->created_at) }}</td>-->
                                <td data-label="{{ __('locale.Action')}}">
                                    <?php if(!search($referral, $copiedMasterUsers)): ?>
                                    <button class="btn btn-dark" onclick="copyMasterTrader('{{__($referral->id)}}', '{{__($referral->email)}}', '{{__($referral->lastname)}}', '{{__($referral->firstname)}}', '{{__($referral->copy_gain)}}', '{{__($referral->copy_copier1)}}', '{{__($referral->copy_copier2)}}', '{{__($referral->copy_profit)}}', '{{__($referral->copy_loss)}}', '{{__($referral->copy_floating_profit)}}', '{{__($referral->copy_equity)}}', '{{__($referral->copy_master_trader_bonus)}}', '{{__($referral->copy_leverage)}}', '{{__($referral->copy_fee)}}', '{{__($referral->copy_min_amt)}}')">
                                        <span class="actionText actionText{{__($referral->id)}}">Copy Trade</span>
                                        <div class="spinner-border actionLoader actionLoader{{__($referral->id)}}" role="status">
                                            <span class="sr-only"></span>
                                        </div>
                                    </button>
                                    <?php else: ?>
                                    <br/>
                                    <div class="plain-response2"></div>
                                    <br/>
                                    <button class="btn btn-danger" onclick="stopCopyingMasterTrader('{{__($referral->id)}}', '{{__($referral->email)}}', '{{__($referral->lastname)}}', '{{__($referral->firstname)}}')">
                                        <span class="actionText actionText{{__($referral->id)}}-stop">Stop Copy</span>
                                        <div class="spinner-border actionLoader actionLoader{{__($referral->id)}}-stop" role="status">
                                            <span class="sr-only"></span>
                                        </div>
                                    </button>
                                    
                                    <button class="btn btn-warning" onclick="sendReceiveChat('{{__($referral->email)}}', '{{__($referral->lastname)}}', '{{__($referral->firstname)}}')">
                                        Chat
                                    </button>
                                    <!--<span class="text-success">Already Copied</span>-->
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php #endif; ?>
                        @empty
                            <tr>
                                <td colspan="100%">{{ __($empty_message) }}</td>
                            </tr>
                        @endforelse
                        <input type="hidden" id="routingUrl" value="{{ route('user.copymaster') }}" />
                        <input type="hidden" id="chatUsersRoutingUrl" value="{{ route('user.mychatusers') }}" />
                        <input type="hidden" id="sendMyChatRoutingUrl" value="{{ route('user.sendmychat') }}" />
                    </tbody>
                </table>
            </div>

        </div>
        <div class="mb-1">{{paginateLinks($referrals) }}</div>
    </div>
    
    <!-- Use a button to open the snackbar -->
    <button onclick="showSnacks()" class="openSnackings" style="display: none;">Show Snackbar</button>
    <!-- The actual snackbar -->
    <div id="snackbar" class="snackbar-main">Some text some message..</div>
    
    <!-- Trigger the modal with a button -->
    <button type="button" class="btn btn-info btn-lg openModalMyModal" data-toggle="modal" data-target="#myModal" style="display: none;">Open Modal</button>
    <button type="button" class="btn btn-info btn-lg openModalMyModal2" data-toggle="modal" data-target="#myModal2" style="display: none;">Open Modal</button>
    
    <!-- Modal -->
    <div id="myModal2" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close closeChatModal" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        Copy Master Trader
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        
                        <div class="row">
                            <div class="col-md-12 card">
                                
                                <p class="displayMasterTraderName"></p>
                                
                                <div>
                                    <h6 style="padding: 12px;">
                                        Social Media Account: 
                                        <strong>
                                            <span id="showSocialAccount"></span>
                                        </strong>
                                    </h6>
                                    <br/>
                                    <div class="plain-response"></div>
                                    <br/>
                                    <button class="btn btn-warning" onclick="startCopying()">
                                        <span class="actionText actionTextindexm">SETUP COPYING</span>
                                        <center>
                                            <div class="spinner-border actionLoader actionLoaderindexm" role="status">
                                                <span class="sr-only"></span>
                                            </div>
                                        </center>
                                    </button>
                                </div>
                                
                            </div>
                        </div>
                        
                        <div class="row meshedImage">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 card">
                                <p>Performance</p>
                            </div>
                            <div class="col-md-4 card">
                                <p>GAIN</p>
                                <span class="copy_gain">93.03%</span>
                            </div>
                            <div class="col-md-4 card">
                                <p>COPIERS</p>
                                <span class="copy_copier1">46</span>
                                <!--<span></span>-->
                                <span class="copy_copier2"> | 23</span>
                            </div>
                            <div class="col-md-4 card">
                                <p>PROFIT AND LOSS</p>
                                <span class="copy_profit">$381</span>
                                <!--<span></span>-->
                                <span class="copy_loss">$5.95</span>
                                <hr/>
                            </div>
                        </div>
                        
                        <br/>
                        
                        <div class="row">
                            <div class="col-md-12 card">
                                <p>Account Details</p>
                            </div>
                            <div class="col-md-6 card">
                                <p>FLOATING PROFIT</p>
                                <span class="copy_floating_profit">$0.00</span>
                            </div>
                            <div class="col-md-6 card">
                                <p>EQUITY</p>
                                <span class="copy_equity">$100,000.08</span>
                            </div>
                            <div class="col-md-6 card">
                                <p>MASTER TRADER BONUS</p>
                                <span class="copy_master_trader_bonus">$0.00</span>
                            </div>
                            <div class="col-md-6 card">
                                <p>LEVERAGE</p>
                                <span class="copy_leverage">1:500</span>
                            </div>
                            <div class="col-md-12 card">
                                <p>Copy Fee</p>
                                <span class="copy_fee">1:500</span>
                            </div>
                            <div class="col-md-12 card">
                                <p>Minimum Investment Capital</p>
                                <span class="copy_min_amt">1:500</span>
                            </div>
                        </div>
                        
                        <div class="row">
                            
                        </div>
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <input type="hidden" id="routingUrl3" value="{{ route('user.copycheck') }}" />
    
    <!-- Modal -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close closeChatModal" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        Chat <span class="fullNameChatWith"></span>
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="panel panel-default">
                        <div class="panel-heading top-bar">
                        </div>
                        <div class="spinner-border actionLoader actionLoader4" role="status">
                            <span class="sr-only"></span>
                        </div>
                        <div class="panel-body msg_container_base msg_display_html">
                            
                        </div>
                        <div class="panel-footer">
                            <div class="input-group">
                                <input id="btn-input" type="text" class="form-control input-sm chat_input enterChatMsag" placeholder="Write your message here..." />
                                <span class="input-group-btn">
                                    <button class="btn btn-primary btn-sm sendYourMessage" id="btn-chat">
                                        <span class="actionText2">Send Message</span>
                                        <div class="spinner-border actionLoader2" role="status">
                                            <span class="sr-only"></span>
                                        </div>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</div>

<input type="hidden" id="curProfUserInnerAuth" value="<?= auth()->user()->id ?>" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/js/bootstrap.min.js" integrity="sha512-UR25UO94eTnCVwjbXozyeVd6ZqpaAE9naiEUBK/A+QDbfSTQFhPGj5lOR6d8tsgbBk84Ggb5A3EkjsOgPRPcKA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>

    var mainEmail = "";
    var fullName = "";
    
    var currentSelectedIndex = 0;
    var currentSelectedEmail = "";
    var currentSelectedLastName = "";
    
    var currentSelectedGain = "";
    var currentSelectedCopier1 = "";
    var currentSelectedCopier2 = "";
    var currentSelectedProfit = "";
    var currentSelectedLoss = "";
    var currentSelectedFloatingProfit = "";
    var currentSelectedEquity = "";
    var currentSelectedMasterTraderBonus = "";
    var currentSelectedLeverage = "";
    var currentSelectedFee = "";
    var currentSelectedMinFee = "";
    
    function create_blob(file, callback) {
        var reader = new FileReader();
        reader.onload = function() { callback(reader.result) };
        reader.readAsDataURL(file);
    }
    
    function startCopying() {
        
        var routingUrl = document.getElementById("routingUrl").value;
        const userId = document.getElementById("curProfUserInnerAuth").value;
        
        // var form_data = new FormData();
        // form_data.append('copier', userId);
        // form_data.append('master', currentSelectedIndex);
        
        var form_data = {
            'copier': userId,
            'master': currentSelectedIndex
        };
        
        $(`.actionTextindexm`).hide(200);
        $(`.actionLoaderindexm`).show(200);
        
        $(`.plain-response`).hide(500);
        $(".plain-response").removeClass("success-response");
        $(".plain-response").removeClass("error-response");
        $(".plain-response").html("");
        
        var routingUrl = document.getElementById("routingUrl3").value;
        fetch(`${routingUrl}?user=${userId}&fee=${currentSelectedFee}&master=${currentSelectedIndex}`).then(data => {
            return data.text();
        }).then(response => {
            console.log(response);
            
            if(response != "completed") {
                $(`.actionTextindexm`).show(200);
                $(`.actionLoaderindexm`).hide(200);
                $(".openSnackings").click();
                $("#snackbar").html(response);
                
                $(`.plain-response`).show(500);
                $(".plain-response").removeClass("success-response");
                $(".plain-response").addClass("error-response");
                $(".plain-response").html(response);
                
                return;
            }
            else {
                fetch(`https://spotalert-api.herokuapp.com/api/v1/oth/copy-trade/${userId}/${currentSelectedIndex}`, {
                    method: "POST",
                    body: JSON.stringify(form_data)
                }).then(data => {
                    return data.text();
                }).then(response => {
                    // console.log(response);
                    $(`.actionTextindexm`).show(200);
                    $(`.actionLoaderindexm`).hide(200);
                    
                    var res = JSON.parse(response);
                    $(".openSnackings").click();
                    $("#snackbar").html(res.message);
                    if(res.status == "success") {
                        $(".snackbar-main").addClass("success-snack");
                        window.location.reload();
                    }
                    else {
                        $(".snackbar-main").addClass("error-snack");
                    }
                }).catch((e) => {
                    console.log(e);
                    $(`.actionTextindexm`).show(200);
                    $(`.actionLoaderindexm`).hide(200);
                });
            }
            
        }).catch((e) => {
            console.log(e);
            $(`.actionTextindexm`).hide(200);
            $(`.actionLoaderindexm`).show(200);
        });
    }

    function copyMasterTrader(index, email, lastname, firstname, copy_gain, copy_copier1, copy_copier2, copy_profit, copy_loss, copy_floating_profit, copy_equity, copy_master_trader_bonus, copy_leverage, copy_fee, copy_min_amt) {
        // console.log("THE RESTERS");
        currentSelectedIndex = index;
        currentSelectedEmail = email;
        currentSelectedLastName = lastname;
        
        currentSelectedGain = (copy_gain == "") ? "No Gain Value" : `${copy_gain}%`;
        currentSelectedCopier1 = (copy_copier1 == "") ? "No Copy Value 1" : copy_copier1;
        currentSelectedCopier2 = (copy_copier2 == "") ? "No Copy Value 2" : copy_copier2;
        currentSelectedProfit = (copy_profit == "") ? "No Profit Value" : `$${copy_profit}`;
        currentSelectedLoss = (copy_loss == "") ? "No Loss Value" : `$${copy_loss}`;
        currentSelectedFloatingProfit = (copy_floating_profit == "") ? "No Floating Profit Value" : `$${copy_floating_profit}`;
        currentSelectedEquity = (copy_equity == "") ? "No Equity Value" : `$${copy_equity}`;
        currentSelectedMasterTraderBonus = (copy_master_trader_bonus == "") ? "No Master Trader Bonus" : `$${copy_master_trader_bonus}`;
        currentSelectedLeverage = (copy_leverage == "") ? "No Leverage Value" : copy_leverage;
        currentSelectedFee = (copy_fee == "") ? "No Copy Fee Value" : copy_fee;
        currentSelectedMinFee = (copy_min_amt == "") ? "No Minimum Fee Value" : copy_min_amt;
        
        $(".copy_gain").html(currentSelectedGain);
        $(".copy_copier1").html(currentSelectedCopier1);
        $(".copy_copier2").html(currentSelectedCopier2);
        $(".copy_profit").html(currentSelectedProfit);
        $(".copy_loss").html(currentSelectedLoss);
        $(".copy_floating_profit").html(currentSelectedFloatingProfit);
        $(".copy_equity").html(currentSelectedEquity);
        $(".copy_master_trader_bonus").html(currentSelectedMasterTraderBonus);
        $(".copy_leverage").html(currentSelectedLeverage);
        $(".copy_fee").html(currentSelectedFee);
        $(".copy_min_amt").html(currentSelectedMinFee);
        
        $(".meshedImage").html("");
        
        $(".displayMasterTraderName").html(`${lastname.toUpperCase()} ${firstname.toUpperCase()}`);
        $(".openModalMyModal2").click();
        
        fetch(`https://spotalert-api.herokuapp.com/api/v1/oth/get-master-trader/${currentSelectedIndex}`).then(data => {
            return data.text();
        }).then(response => {
            var json = JSON.parse(response);
            if (json.checkMaster) {
                const socialAccount = json.checkMaster.social_media;
                $("#showSocialAccount").html(socialAccount);
                const images = json.checkMaster.copyImages;
                if (images) {
                    images.map((image) => {
                        $(".meshedImage").append(`<div class="col-md-6 card">
                            <img src="data:image/jpeg;base64,${image}" class="rounded-circle" height="80px" width="80px">
                        </div>`);
                    })
                }
            }
        }).catch((e) => {
            console.log(e);
        });
        
    }
    
    function stopCopyingMasterTrader(index, email, lastname, firstname) {
        
        const userId = document.getElementById("curProfUserInnerAuth").value;
        
        var form_data = {
            'copier': userId,
            'master': index
        };
        
        $(`.actionText${index}-stop`).hide(200);
        $(`.actionLoader${index}-stop`).show(200);
        
        $(`.plain-response2`).hide(500);
        $(".plain-response2").removeClass("success-response");
        $(".plain-response2").removeClass("error-response");
        $(".plain-response2").html("");
        
        fetch(`https://spotalert-api.herokuapp.com/api/v1/oth/stop/copy-trade/${userId}/${index}`, {
            method: "POST",
            body: JSON.stringify(form_data)
        }).then(data => {
            return data.text();
        }).then(response => {
            // console.log(response);
            $(`.actionText${index}-stop`).show(200);
            $(`.actionLoader${index}-stop`).hide(200);
            var res = JSON.parse(response);
            $(".openSnackings").click();
            $("#snackbar").html(res.message);
            if(res.status == "success") {
                $(".snackbar-main").addClass("success-snack");
                window.location.reload();
            }
            else {
                $(".snackbar-main").addClass("error-snack");
                
                $(`.plain-response2`).show(500);
                $(".plain-response2").removeClass("success-response");
                $(".plain-response2").addClass("error-response");
                $(".plain-response2").html(res.message);
                
            }
        }).catch((e) => {
            console.log(e);
            $(`.actionText${index}-stop`).show(200);
            $(`.actionLoader${index}-stop`).hide(200);
        });
        
    }
    
    function myChats(email) {
        $(".actionLoader4").show(300);
        var routingUrl = document.getElementById("chatUsersRoutingUrl").value;
        fetch(`${routingUrl}?email=${email}`).then(data => {
            return data.text();
        }).then(response => {
            // console.log(response);
            var json = JSON.parse(response);
            // console.log(json);
            var output = "";
            json.map((msg) => {
                output += `<div class="row msg_container base_receive base_messaging">
                                <div class="col-md-10 col-xs-10">
                                    <div class="messages msg_receive">
                                        <p>${msg.message}</p>
                                        <time datetime="${msg.time_sent}">${msg.from_user} • 51 min</time>
                                    </div>
                                </div>
                            </div>`;
            })
            $(".msg_display_html").html(output);
            $(".actionLoader4").hide(300);
        }).catch((e) => {
            console.log(e);
        });
    }
    
    function sendReceiveChat(email, lastname, firstname) {
        mainEmail = email;
        var fname = `${lastname} ${firstname}`;
        fullName = fname;
        $(".fullNameChatWith").html(`With ${fname}`);
        myChats(email);
        $(".openModalMyModal").click();
    }
    
    $(".closeChatModal").on("click", function(e) {
        $(".msg_display_html").html("<p> </p>");
    });
    
    $(".sendYourMessage").on("click", function(e) {
        
        $(`.actionText2`).hide(200);
        $(`.actionLoader2`).show(200);
        
        var messageText = $(".enterChatMsag").val();
        
        var routingUrl = document.getElementById("sendMyChatRoutingUrl").value;
        fetch(`${routingUrl}?email=${mainEmail}&chat_msg=${messageText}`).then(data => {
            return data.text();
        }).then(response => {
            $(`.actionText2`).show(200);
            $(`.actionLoader2`).hide(200);
            
            var result = JSON.parse(response);
            $(".openSnackings").click();
            $("#snackbar").html(result.message);
            // $(".closeChatModal").click();
            
            $(".enterChatMsag").val("");
            
            myChats(mainEmail);
            
        }).catch((e) => {
            console.log(e);
            $(`.actionText2`).show(200);
            $(`.actionLoader2`).hide(200);
        });
        
        
    });
    
    function showSnacks() {
        // Get the snackbar DIV
        var x = document.getElementById("snackbar");
        // Add the "show" class to DIV
        x.className = "show";
        // After 3 seconds, remove the show class from DIV
        setTimeout(function(){ x.className = x.className.replace("show", ""); }, 4000);
    }
    
</script>

@endsection



