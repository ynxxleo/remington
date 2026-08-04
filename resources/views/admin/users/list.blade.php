@extends('layouts.app')
@section('content')
<div class="row" id="table-hover-row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Users</h4><div class="card-search"></div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover custom-data-bs-table">
                    <thead class="table-dark">
                        <tr>
                            <th>{{ __('locale.User')}}</th>
                            <th>{{ __('locale.Username')}}</th>
                            <th>{{ __('locale.Email')}}</th>
                            <th>{{ __('locale.Phone')}}</th>
                            <th>{{ __('locale.Joined At')}}</th>
                            <th>{{ __('locale.Action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            @if($user->id != 1)
                            <tr>
                                <td data-label="{{ __('locale.User')}}">
                                    <div class="row centerize">
                                        <div class="col-md-3 thumb">
                                            <img src="{{ $user->profile_photo_url ? $user->profile_photo_url : asset('images/portrait/small/avatar-s-11.jpg') }}"
                                                alt="{{ __('locale.image')}}">
                                        </div>
                                        <span class="col-md-9 name">{{$user->fullname}}</span>
                                    </div>
                                </td>
                                <td data-label="{{ __('locale.Username')}}"><a
                                        href="{{ route('admin.users.detail', $user->id) }}">{{ $user->username }}</a>
                                </td>
                                <td data-label="{{ __('locale.Email')}}">{{ $user->email }}</td>
                                <td data-label="{{ __('locale.Phone')}}">{{ $user->mobile }}</td>
                                <td data-label="{{ __('locale.Joined At')}}">{{ showDateTime($user->created_at) }}
                                </td>
                                <td data-label="{{ __('locale.Action')}}">

                                <a href="javascript:void(0)"
                                data-id="{{ $user->id }}"
                                class="btn btn-icon btn-danger btn-sm removeModalBtn" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="{{ __('locale.Remove')}}">
                                 <i class="bi bi-trash"></i>
                             </a>
                                    <a href="{{ route('admin.users.detail', $user->id) }}">
                                        <button class="btn btn-icon btn-warning btn-sm" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="{{ __('locale.Details')}}">
                                            <i class="bi bi-display"></i>
                                        </button>
                                    </a>
                                </td>
                            </tr>
                            @endif
                        @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($empty_message) }}</td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div><div class="mb-1">{{ paginateLinks($users) }}</div>
    </div>
</div>

{{-- Remove Subscriber MODAL --}}
<div id="removeModal" class="modal fade text-start" tabindex="-1" aria-hidden="true" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('locale.Are you sure want to remove?')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.users.remove') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <p><span class="fw-bold"></span> {{ __('locale.Bot will be removed.')}}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">{{ __('locale.Close')}}</button>
                    <button type="submit" class="btn btn-danger">{{ __('locale.Remove')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('script')
    <script>
        $(function(){
            "use strict";
            $('.removeModalBtn').on('click', function() {
                $('#removeModal').find('input[name=id]').val($(this).data('id'));
                $('#removeModal').modal('show');
            });
        });
    </script>
@endpush
