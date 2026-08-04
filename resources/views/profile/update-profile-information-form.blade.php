<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/css/bootstrap.min.css" integrity="sha512-T584yQ/tdRR5QwOpfvDfVQUidzfgc2339Lc8uBDtcp/wYu80d7jwBgAxbyMh0a9YM9F8N3tdErpFI8iaGx6x5g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
    .actionLoader, .actionLoader2 {
        display: none;
    }
</style>

<x-jet-form-section submit="updateProfileInformation">
    
    <x-slot name="title">
        @if (auth()->user()->role_id != 1)
            
            <input type="hidden" id="routingUrl" value="{{ route('user.becomemaster') }}" />
            
            @if ($this->user->isMaster == 0)
                <button type="button" class="btn btn-warning w-100 activate-listen-master" style="float: right !important;">Request Master Trader</button>
            @endif
            
            @if ($this->user->isMaster == 1)
                <span class="text-warning">You're Request has been recieved and will be updated shortly</span><br/>
            @endif
            
            @if ($this->user->isMaster == 2)
                <span class="text-success">You are a Master Trader</span><br/>
            @endif
        
        @endif
        
        {{ __('Profile Information') }}
        
  </x-slot>
  
  <x-slot name="description">
    {{ __('Update your account\'s profile information and email address.') }}
  </x-slot>

  <x-slot name="form">

    <x-jet-action-message on="saved">
      {{ __('Saved.') }}
    </x-jet-action-message>

    <!-- Profile Photo -->
    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
      <div class="mb-1" x-data="{photoName: null, photoPreview: null}">
        <!-- Profile Photo File Input -->
        <!--<input type="file" hidden wire:model="photo" x-ref="photo" x-on:change=" photoName = $refs.photo.files[0].name; const reader = new FileReader(); reader.onload = (e) => { photoPreview = e.target.result;}; reader.readAsDataURL($refs.photo.files[0]);" />-->
        <input type="file" hidden x-ref="photo" class="slurpyImage" x-on:change=" photoName = $refs.photo.files[0].name; const reader = new FileReader(); reader.onload = (e) => { photoPreview = e.target.result;}; reader.readAsDataURL($refs.photo.files[0]);" />

        <!-- Current Profile Photo -->
        <div class="mt-2" x-show="! photoPreview">
          <!--<img src="{{ $this->user->profile_photo_url }}" class="rounded-circle justifinedProfileSetImageCreated" height="80px" width="80px">-->
          <img src="{{ $this->user->profile_photo_url }}" class="rounded-circle justifinedProfileSetImageCreated" height="80px" width="80px">
        </div>
        
        <!-- New Profile Photo Preview -->
        <div class="mt-2" x-show="photoPreview">
          <img x-bind:src="photoPreview" class="rounded-circle" width="80px" height="80px">
        </div>
        
        <input type="hidden" id="curProfUserInnerAuth" value="<?= auth()->user()->id ?>" />

        <x-jet-secondary-button class="mt-2 me-2" type="button" x-on:click.prevent="$refs.photo.click()">
          {{ __('Select A New Photo') }}
        </x-jet-secondary-button>

        @if ($this->user->profile_photo_path)
          <!--<button type="button" class="btn btn-danger text-uppercase mt-2" wire:click="deleteProfilePhoto">-->
          <!--  {{ __('Remove Photo') }}-->
          <!--</button>-->
        @endif

        <!--<x-jet-input-error for="photo" class="mt-2" />-->
      </div>
    @endif

    <div class="row">
    <div class="col-md-4 col-sm-6 col-lg-4">
<!-- Firstname -->
    <div class="mb-1">
        <x-jet-label class="form-label" for="firstname" value="{{ __('Firstname') }}" />
        <x-jet-input id="firstname" type="text" class="{{ $errors->has('firstname') ? 'is-invalid' : '' }}"
          wire:model.defer="state.firstname" autocomplete="firstname" />
        <x-jet-input-error for="name" />
      </div>
    </div>
    <div class="col-md-4 col-sm-6 col-lg-4">
<!-- Lastname -->
      <div class="mb-1">
          <x-jet-label class="form-label" for="lastname" value="{{ __('Lastname') }}" />
          <x-jet-input id="lastname" type="text" class="{{ $errors->has('lastname') ? 'is-invalid' : '' }}"
            wire:model.defer="state.lastname" autocomplete="lastname" />
          <x-jet-input-error for="lastname" />
        </div>
    </div>
    <div class="col-md-4 col-sm-6 col-lg-4">
            <!-- Email -->
      <div class="mb-1">
          <x-jet-label class="form-label" for="email" value="{{ __('Email') }}" />
          <x-jet-input id="email" type="email" class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
            wire:model.defer="state.email" />
          <x-jet-input-error for="email" />
        </div>
    </div>
    </div>

    <div class="row">
        <div class="col-md-4 col-sm-6 col-lg-4">
      <!-- Address -->
    <div class="mb-1">
        <x-jet-label class="form-label" for="address" value="{{ __('Address') }}" />
        <x-jet-input id="address" type="text" class="{{ $errors->has('address') ? 'is-invalid' : '' }}"
          wire:model.defer="state.address" autocomplete="address" />
        <x-jet-input-error for="address" />
      </div>
    </div>
    <div class="col-md-4 col-sm-6 col-lg-4">
      <!-- Town -->
    <div class="mb-1">
        <x-jet-label class="form-label" for="town" value="{{ __('Town') }}" />
        <x-jet-input id="town" type="text" class="{{ $errors->has('town') ? 'is-invalid' : '' }}"
          wire:model.defer="state.town" autocomplete="town" />
        <x-jet-input-error for="town" />
      </div>
    </div>
    <div class="col-md-4 col-sm-6 col-lg-4">
      <!-- City -->
    <div class="mb-1">
        <x-jet-label class="form-label" for="city" value="{{ __('City') }}" />
        <x-jet-input id="city" type="text" class="{{ $errors->has('city') ? 'is-invalid' : '' }}"
          wire:model.defer="state.city" autocomplete="city" />
        <x-jet-input-error for="city" />
      </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4 col-sm-6 col-lg-4">
      <!-- Twitter -->
    <div class="mb-1">
        <x-jet-label class="form-label" for="twitter" value="{{ __('Twitter') }}" />
        <x-jet-input id="twitter" type="text" class="border-twitter {{ $errors->has('twitter') ? 'is-invalid' : '' }}"
          wire:model.defer="state.twitter" autocomplete="twitter" placeholder="https://twitter.com/[username]" />
        <x-jet-input-error for="twitter" />
      </div>
    </div>
    <div class="col-md-4 col-sm-6 col-lg-4">
      <!-- Facebook -->
    <div class="mb-1">
        <x-jet-label class="form-label" for="facebook" value="{{ __('Facebook') }}" />
        <x-jet-input id="facebook" type="text" class="border-facebook {{ $errors->has('facebook') ? 'is-invalid' : '' }}"
          wire:model.defer="state.facebook" autocomplete="facebook" placeholder="https://facebook.com/[username]"/>
        <x-jet-input-error for="facebook" />
      </div>
    </div>
    <div class="col-md-4 col-sm-6 col-lg-4">
      <!-- instagram -->
    <div class="mb-1">
        <x-jet-label class="form-label" for="instagram" value="{{ __('Instagram') }}" />
        <x-jet-input id="instagram" type="text" class="border-instagram {{ $errors->has('instagram') ? 'is-invalid' : '' }}"
          wire:model.defer="state.instagram" autocomplete="instagram" placeholder="https://instagram.com/[username]" />
        <x-jet-input-error for="instagram" />
      </div>
    </div>
</div>
  </x-slot>

  @if (auth()->user()->role_id != 3)
  <x-slot name="actions">
    <div class="d-flex align-items-baseline">
      <x-jet-button>
        {{ __('Save') }}
      </x-jet-button>
    </div>
  </x-slot>
  @endif
</x-jet-form-section>

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
                            
                            <div class="row">
                                <div class="col-md-12 card">
                                    <input type="file" id="real-file" hidden="hidden" accept="image/*" multiple />
                                    <button type="button" id="custom-button">CHOOSE FILES</button>
                                    <span id="custom-text">Upload Screenshots of past trades.</span>
                                </div>
                            </div>
                            <p>Enter social media account</p>
                            <div class="form-group">
                                <label>
                                    Facebook
                                    <input type="text" id="realSocialMedia" class="form-control" placeholder="Enter social media account" />
                                </label>
                            </div>
                            <div class="form-group">
                                <label>
                                    Twitter
                                    <input type="text" id="realSocialMedia" class="form-control" placeholder="Enter social media account" />
                                </label>
                            </div>
                            <div class="form-group">
                                <label>
                                    Linkedin
                                    <input type="text" id="realSocialMedia" class="form-control" placeholder="Enter social media account" />
                                </label>
                            </div>
                            
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

    const realFileBtn = document.getElementById("real-file");
    const customBtn = document.getElementById("custom-button");
    const customTxt = document.getElementById("custom-text");
    
    if (customBtn) {
        customBtn.addEventListener("click", function() {
          realFileBtn.click();
        });
    }
    
    if (realFileBtn) {
        realFileBtn.addEventListener("change", function() {
          if (realFileBtn.value) {
              const files = document.querySelector('#real-file').files;
            //   customTxt.innerHTML = realFileBtn.value.match(/[\/\\]([\w\d\s\.\-\(\)]+)$/)[1];
              customTxt.innerHTML = `${files.length} File(s) Selected`;
          } else {
            customTxt.innerHTML = "No file chosen, yet.";
          }
        });
    }
    
    var activatorListener = document.querySelector(".activate-listen-master");
    
    if( activatorListener != undefined || activatorListener != null ) {
        
        activatorListener.addEventListener("click", function(e) {
            $(".openModalMyModal2").click();
        });
        
    }
    
    function startCopying() {
        
        $(`.actionTextindexm`).hide(200);
        $(`.actionLoaderindexm`).show(200);
        
        const userId = document.getElementById("curProfUserInnerAuth").value;
        
        var form_data = new FormData();
        form_data.append('user', userId);
        
        const files = document.querySelector('#real-file').files;
        const realSocialMedia = document.getElementById("realSocialMedia").value;
        
        form_data.append('social_media', realSocialMedia);
        
        if (files.length > 0 && realSocialMedia != "") {
            
            for (var i = 0; i <= files.length; i++) {
                var file = files[i];
                if (file != undefined) {
                    form_data.append('images', file);
                }
            }
            
            $.ajax({
                url: 'https://nft-mini-api.herokuapp.com/api/v1/oth/become-master-trader', 
                type: 'POST',
                data: form_data,
                dataType: 'json',
                contentType: false,
                processData: false,
                success: function (response) {
                    console.log(response);
                    var routingUrl = document.getElementById("routingUrl").value;
                    fetch(routingUrl).then(data => {
                        return data.text();
                    }).then(response => {
                        console.log(response);
                        window.location.reload();
                        $(`.actionTextindexm`).show(200);
                        $(`.actionLoaderindexm`).hide(200);
                    }).catch((e) => {
                        console.log(e);
                    });
                },
                error: function (err) {
                    console.log(err);
                    $(`.actionTextindexm`).show(200);
                    $(`.actionLoaderindexm`).hide(200);
                }
            });
            
        }
        
    }
    
    const realFileBtnV = document.querySelector(".slurpyImage");
    
    const toBase64 = file => new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result);
        reader.onerror = error => reject(error);
    });
    
    realFileBtnV.addEventListener("change", function() {
        if (realFileBtnV.value) {
            
            const file = document.querySelector(".slurpyImage").files[0];
            const userId = document.getElementById("curProfUserInnerAuth").value;
            
            var form_data = new FormData();
            form_data.append('image', file);
            form_data.append('user', userId);
            
            fetch('https://nft-mini-api.herokuapp.com/api/v1/profile/add-profile', {
                method: 'POST',
                body: form_data
            }).then(data => {
                return data.text();
            }).then(response => {
                console.log(response);
                window.location.reload();
            }).catch((e) => {
                console.log(e);
            });
        
      } else {}
      
    });
    
</script>
