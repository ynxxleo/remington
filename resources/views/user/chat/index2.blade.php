<?php
$page_title = 'User Chat';
?>

@extends('layouts.app')

@section('content')

    <style>
        
        .chat-profiles {
            border: 1px solid yellow;
            padding: 12px;
        }
        
        .chat-screen {
            border: 1px solid green;
            padding: 12px;
            margin-left: 8px;
        }
        
    </style>
    
    <style>
        .activity-item {
          overflow: visible;
          position: relative;
          margin: 15px 0;
          border-top: 1px dashed #ccc;
          padding-top: 15px;
        }
        .activity-item:first-child {
          border-top: none;
        }
        .activity-item .avatar {
          -moz-border-radius: 2px;
          -webkit-border-radius: 2px;
          border-radius: 2px;
          width: 32px;
        }
        .activity-item > i {
          font-size: 18px;
          line-height: 1;
        }
        .activity-item .media-body {
          position: relative;
        }
        .activity-item .activity-title {
          margin-bottom: 0;
          line-height: 1.3;
        }
        .activity-item .activity-attachment {
          padding-top: 20px;
        }
        .activity-item .well {
          -moz-border-radius: 0;
          -webkit-border-radius: 0;
          border-radius: 0;
          -moz-box-shadow: none;
          -webkit-box-shadow: none;
          box-shadow: none;
          border: none;
          border-left: 2px solid #cfcfcf;
          background: #fff;
          margin-left: 20px;
          font-size: 0.85em;
        }
        .activity-item .thumbnail {
          display: inline;
          border: none;
          padding: 0;
        }
        .activity-item .thumbnail img {
          -moz-border-radius: 2px;
          -webkit-border-radius: 2px;
          border-radius: 2px;
          width: auto;
          margin: 0;
        }
        .activity-item .activity-actions {
          position: absolute;
          top: 15px;
          right: 0;
        }
        .activity-item .activity-actions .btn i {
          margin: 0;
        }
        .activity-item .activity-actions .dropdown-menu > li > a {
          font-size: 0.9em;
          padding: 3px 10px;
        }
        .activity-item + .btn {
          margin-bottom: 15px;
        }
    </style>
    
    <style>
        
        /* CSS talk bubble */
        .talk-bubble {
        	margin: 40px;
          display: inline-block;
          position: relative;
        	width: 200px;
        	height: auto;
        	background-color: lightyellow;
        }
        
        .border{
          border: 8px solid #666;
        }
        .round{
          border-radius: 30px;
        	-webkit-border-radius: 30px;
        	-moz-border-radius: 30px;
        
        }
        
        /* Right triangle placed top left flush. */
        .tri-right.border.left-top:before {
        	content: ' ';
        	position: absolute;
        	width: 0;
        	height: 0;
          left: -40px;
        	right: auto;
          top: -8px;
        	bottom: auto;
        	border: 32px solid;
        	border-color: #666 transparent transparent transparent;
        }
        .tri-right.left-top:after{
        	content: ' ';
        	position: absolute;
        	width: 0;
        	height: 0;
          left: -20px;
        	right: auto;
          top: 0px;
        	bottom: auto;
        	border: 22px solid;
        	border-color: lightyellow transparent transparent transparent;
        }
        
        /* Right triangle, left side slightly down */
        .tri-right.border.left-in:before {
        	content: ' ';
        	position: absolute;
        	width: 0;
        	height: 0;
          left: -40px;
        	right: auto;
          top: 30px;
        	bottom: auto;
        	border: 20px solid;
        	border-color: #666 #666 transparent transparent;
        }
        .tri-right.left-in:after{
        	content: ' ';
        	position: absolute;
        	width: 0;
        	height: 0;
          left: -20px;
        	right: auto;
          top: 38px;
        	bottom: auto;
        	border: 12px solid;
        	border-color: lightyellow lightyellow transparent transparent;
        }
        
        /*Right triangle, placed bottom left side slightly in*/
        .tri-right.border.btm-left:before {
        	content: ' ';
        	position: absolute;
        	width: 0;
        	height: 0;
        	left: -8px;
          right: auto;
          top: auto;
        	bottom: -40px;
        	border: 32px solid;
        	border-color: transparent transparent transparent #666;
        }
        .tri-right.btm-left:after{
        	content: ' ';
        	position: absolute;
        	width: 0;
        	height: 0;
        	left: 0px;
          right: auto;
          top: auto;
        	bottom: -20px;
        	border: 22px solid;
        	border-color: transparent transparent transparent lightyellow;
        }
        
        /*Right triangle, placed bottom left side slightly in*/
        .tri-right.border.btm-left-in:before {
        	content: ' ';
        	position: absolute;
        	width: 0;
        	height: 0;
        	left: 30px;
          right: auto;
          top: auto;
        	bottom: -40px;
        	border: 20px solid;
        	border-color: #666 transparent transparent #666;
        }
        .tri-right.btm-left-in:after{
        	content: ' ';
        	position: absolute;
        	width: 0;
        	height: 0;
        	left: 38px;
          right: auto;
          top: auto;
        	bottom: -20px;
        	border: 12px solid;
        	border-color: lightyellow transparent transparent lightyellow;
        }
        
        /*Right triangle, placed bottom right side slightly in*/
        .tri-right.border.btm-right-in:before {
        	content: ' ';
        	position: absolute;
        	width: 0;
        	height: 0;
          left: auto;
        	right: 30px;
        	bottom: -40px;
        	border: 20px solid;
        	border-color: #666 #666 transparent transparent;
        }
        .tri-right.btm-right-in:after{
        	content: ' ';
        	position: absolute;
        	width: 0;
        	height: 0;
          left: auto;
        	right: 38px;
        	bottom: -20px;
        	border: 12px solid;
        	border-color: lightyellow lightyellow transparent transparent;
        }
        /*
        	left: -8px;
          right: auto;
          top: auto;
        	bottom: -40px;
        	border: 32px solid;
        	border-color: transparent transparent transparent #666;
        	left: 0px;
          right: auto;
          top: auto;
        	bottom: -20px;
        	border: 22px solid;
        	border-color: transparent transparent transparent lightyellow;
        
        /*Right triangle, placed bottom right side slightly in*/
        .tri-right.border.btm-right:before {
        	content: ' ';
        	position: absolute;
        	width: 0;
        	height: 0;
          left: auto;
        	right: -8px;
        	bottom: -40px;
        	border: 20px solid;
        	border-color: #666 #666 transparent transparent;
        }
        .tri-right.btm-right:after{
        	content: ' ';
        	position: absolute;
        	width: 0;
        	height: 0;
          left: auto;
        	right: 0px;
        	bottom: -20px;
        	border: 12px solid;
        	border-color: lightyellow lightyellow transparent transparent;
        }
        
        /* Right triangle, right side slightly down*/
        .tri-right.border.right-in:before {
        	content: ' ';
        	position: absolute;
        	width: 0;
        	height: 0;
          left: auto;
        	right: -40px;
          top: 30px;
        	bottom: auto;
        	border: 20px solid;
        	border-color: #666 transparent transparent #666;
        }
        .tri-right.right-in:after{
        	content: ' ';
        	position: absolute;
        	width: 0;
        	height: 0;
          left: auto;
        	right: -20px;
          top: 38px;
        	bottom: auto;
        	border: 12px solid;
        	border-color: lightyellow transparent transparent lightyellow;
        }
        
        /* Right triangle placed top right flush. */
        .tri-right.border.right-top:before {
        	content: ' ';
        	position: absolute;
        	width: 0;
        	height: 0;
          left: auto;
        	right: -40px;
          top: -8px;
        	bottom: auto;
        	border: 32px solid;
        	border-color: #666 transparent transparent transparent;
        }
        .tri-right.right-top:after{
        	content: ' ';
        	position: absolute;
        	width: 0;
        	height: 0;
          left: auto;
        	right: -20px;
          top: 0px;
        	bottom: auto;
        	border: 20px solid;
        	border-color: lightyellow transparent transparent transparent;
        }
        
        /* talk bubble contents */
        .talktext{
          padding: 1em;
        	text-align: left;
          line-height: 1.5em;
        }
        .talktext p{
          /* remove webkit p margins */
          -webkit-margin-before: 0em;
          -webkit-margin-after: 0em;
        }
        
    </style>
    
    <div class="container-fluid">
        
        <div class="row">
            <div class="col-md-4 chat-profiles">
                <h2>Profiles to Chat</h2>
                <div>
                    <div class="media activity-item">
                        <a href="javascript:void(0);" class="pull-left">
                            <img src="http://bootdey.com/img/Content/avatar/avatar1.png" alt="Avatar" class="media-object avatar">
                        </a>
                        <div class="media-body pull-right">
                            <p class="activity-title"><a href="javascript:void(0);">Jphn Smith</a></p>
                        </div>
                    </div>
                    <div class="media activity-item">
                        <a href="javascript:void(0);" class="pull-left">
                            <img src="http://bootdey.com/img/Content/avatar/avatar1.png" alt="Avatar" class="media-object avatar">
                        </a>
                        <div class="media-body pull-right">
                            <p class="activity-title"><a href="javascript:void(0);">Jphn Smith</a></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-7 chat-screen">
                <h2>Chat Screen</h2>
                <div>
                    <div class="media activity-item">
                        <div class="media-body pull-right" style="float: right !important;">
                            <p class="activity-title">
                                <div class="talk-bubble tri-right btm-right">
                                    <div class="talktext">
                                        <p>Flush to the bottom right. Uses .btm-right only.</p>
                                    </div>
                                </div>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
