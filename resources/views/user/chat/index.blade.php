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
    
</style>

<?php
    
    function check_match ($obj, $copyObj) {
        foreach ($copyObj as $key => $oj) {
            if($obj->id == $oj["master"] || $obj->id == $oj["copier"]) return true;
        }
        return false;
    }
    
    function search($masterObj, $copiedMaster) {
        foreach($copiedMaster as $key => $obj) {
            if($masterObj->id == $obj["master"]) return true;
        }
        return false;
    }
    
?>

<div class="row" id="table-hover-row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Chat</h4><div class="card-search"></div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover custom-data-bs-table">
                    <thead>
                            <!--<th>{{ __('locale.ID')}}</th>-->
                            <th>{{ __('locale.First Name')}}</th>
                            <th>{{ __('locale.Last Name')}}</th>
                            <th>{{ __('locale.Username')}}</th>
                            <!--<th>{{ __('locale.Date')}}</th>-->
                            <th>{{ __('locale.Action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($referrals as $referral)
                            <?php if( check_match($referral, $copiedMasterUsers) ): ?>
                            <tr>
                                <!--<td data-label="{{ __('locale.ID')}}">{{$loop->iteration}}</td>-->
                                <td data-label="{{ __('locale.First Name')}}">{{__($referral->firstname)}}</td>
                                <td data-label="{{ __('locale.Last Name')}}">{{__($referral->lastname)}}</td>
                                <td data-label="{{ __('locale.Username')}}"> {{__($referral->username)}}</td>
                                <!--<td data-label="{{ __('locale.Date')}}">{{ showDateTime($referral->created_at) }}</td>-->
                                <td data-label="{{ __('locale.Action')}}">
                                    <button class="btn btn-warning" onclick="sendReceiveChat('{{__($referral->email)}}', '{{__($referral->lastname)}}', '{{__($referral->firstname)}}')">
                                        Chat
                                    </button>
                                </td>
                            </tr>
                            <?php endif; ?>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/js/bootstrap.min.js" integrity="sha512-UR25UO94eTnCVwjbXozyeVd6ZqpaAE9naiEUBK/A+QDbfSTQFhPGj5lOR6d8tsgbBk84Ggb5A3EkjsOgPRPcKA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>

    var mainEmail = "";
    var fullName = "";

    function copyMasterTrader(index, email, lastname) {
        $(`.actionText${index}`).hide(200);
        $(`.actionLoader${index}`).show(200);
        var routingUrl = document.getElementById("routingUrl").value;
        fetch(`${routingUrl}?email=${email}`).then(data => {
            return data.text();
        }).then(response => {
            $(`.actionText${index}`).show(200);
            $(`.actionLoader${index}`).hide(200);
            var res = JSON.parse(response);
            $(".openSnackings").click();
            $("#snackbar").html(res.message);
            // console.log(res.code)
            if(res.code == 200 || res.code == 201) {
                $(".snackbar-main").addClass("success-snack");
                window.location.reload();
            }
            else {
                $(".snackbar-main").addClass("error-snack");
            }
        }).catch((e) => {
            console.log(e);
            $(`.actionText${index}`).show(200);
            $(`.actionLoader${index}`).hide(200);
        });
    }
    
    function myChats(email) {
        $(".actionLoader4").show(300);
        var routingUrl = document.getElementById("chatUsersRoutingUrl").value;
        fetch(`${routingUrl}?email=${email}`).then(data => {
            return data.text();
        }).then(response => {
            console.log(response);
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



