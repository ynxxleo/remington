@extends('layouts.app')
@section('content')
<div class="row mb-none-30">
    <div class="col-lg-6 col-md-6">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.setting.logo_icon_update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6 mb-1">
                            <div id="logoPreviewLight" class="img-fluid logoPrev"
                                style="height:80px;background-size: cover;background-image: url({{ getImage(imagePath()['logoIcon']['path'].'/logo.png', '?'.time()) }})">
                                <button type="button" class="btn-icon btn-danger rounded"><i
                                        class="bi bi-x"></i></button>
                            </div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div id="logoPreviewDark" class="img-fluid bg-dark logoPrev"
                                style="height:80px;background-size: cover;background-image: url({{ getImage(imagePath()['logoIcon']['path'].'/logo.png', '?'.time()) }})">
                                <button type="button" class="btn-icon btn-danger rounded"><i
                                        class="bi bi-x"></i></button>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group input-group-merge">
                                <input class="form-control" type="file" id="profilePicUpload1"
                                    accept=".png, .jpg, .jpeg" name="logo">
                                <label class="input-group-text"
                                    for="profilePicUpload1">{{ __('locale.Select Logo')}}</label>
                            </div>
                        </div>
                        <small class="ms-1 text-danger"><code>350px x 75px</code></small>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6 mb-1">
                            <div id="faviconPreviewLight" class="img-fluid iconPrev"
                                style="height:80px;width:80px;background-size: cover;background-image: url({{ getImage(imagePath()['logoIcon']['path'] .'/favicon.png', '?'.time()) }})">
                                <button type="button" class="btn-icon btn-danger rounded"><i
                                        class="bi bi-x"></i></button>
                            </div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div id="faviconPreviewDark" class="img-fluid bg-dark iconPrev"
                                style="height:80px;width:80px;background-size: cover;background-image: url({{ getImage(imagePath()['logoIcon']['path'] .'/favicon.png', '?'.time()) }})">
                                <button type="button" class="btn-icon btn-danger rounded"><i
                                        class="bi bi-x"></i></button>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group input-group-merge">
                                <input class="form-control" type="file" id="profilePicUpload2" accept=".png"
                                    name="favicon">
                                <label for="profilePicUpload2"
                                    class="input-group-text">{{ __('locale.Select Favicon')}}</label>
                            </div>
                        </div>
                        <small class="ms-1 text-danger"><code>64px x 64px</code></small>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2 btn-lg">{{ __('locale.Update')}}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style type="text/css">
    .logoPrev{
        background-size: 100%;
    }
    .iconPrev{
        background-size: 100%;
    }
</style>
@endpush

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function preview(inputId, targets) {
        var input = document.getElementById(inputId);
        if (!input) return;
        input.addEventListener('change', function () {
            var file = this.files && this.files[0];
            if (!file) return;
            var url = URL.createObjectURL(file);
            targets.forEach(function (id) {
                var target = document.getElementById(id);
                if (target) target.style.backgroundImage = 'url("' + url + '")';
            });
        });
    }
    preview('profilePicUpload1', ['logoPreviewLight', 'logoPreviewDark']);
    preview('profilePicUpload2', ['faviconPreviewLight', 'faviconPreviewDark']);
});
</script>
@endpush
