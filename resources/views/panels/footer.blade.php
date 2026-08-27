<!-- BEGIN: Footer-->
<footer class="footer footer-light {{($configData['footerType'] === 'footer-hidden') ? 'd-none':''}} {{$configData['footerType']}}">
    <div class="col d-flex justify-content-between">
            <span class="float-md-start d-none d-md-inline-block mt-25">COPYRIGHT &copy;
              <script>document.write(new Date().getFullYear())</script><a class="ms-25" target="_blank">{{ siteName() }}</a>,
              <span class="d-none d-sm-inline-block">All rights Reserved</span>
              <input type="hidden" id="curProfUserInnerAuthFoots" value="<?= auth()->user()->id ?>" />
            </span>
            <nav class="nova-app-footer-links d-none d-xl-flex" aria-label="Footer">
                <a href="{{ route('frontend.pages.about') }}">About</a>
                <a href="{{ route('contact') }}">Support</a>
                <a href="{{ route('frontend.pages.terms') }}">Terms</a>
                @if(Route::has('policy.show'))<a href="{{ route('policy.show') }}">Privacy</a>@endif
            </nav>
            <div class="float-md-end d-block d-md-inline-block ms-auto my-auto border-start px-1" id="txt"></div>
            <div class="dropdown dropdown-language my-auto border-end border-start px-1">
                <a id="dropdown-flag" href="#" data-bs-toggle="dropdown" aria-haspopup="true">
                  <i class="flag-icon flag-icon-us"></i>
                  <i class="bi bi-chevron-up text-dark"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-flag">
                <a class="dropdown-item" href="{{ url('lang/ar') }}" data-language="ar">
                    <i class="flag-icon flag-icon-iq"></i> Arabics
                    </a>
                  <a class="dropdown-item" href="{{ url('lang/en') }}" data-language="en">
                    <i class="flag-icon flag-icon-us"></i> English
                  </a>
                  <a class="dropdown-item" href="{{ url('lang/fr') }}" data-language="fr">
                    <i class="flag-icon flag-icon-fr"></i> French
                  </a>
                  <a class="dropdown-item" href="{{ url('lang/de') }}" data-language="de">
                    <i class="flag-icon flag-icon-de"></i> German
                  </a>
                  <a class="dropdown-item" href="{{ url('lang/pt') }}" data-language="pt">
                    <i class="flag-icon flag-icon-pt"></i> Portuguese
                  </a>
                  <a class="dropdown-item" href="{{ url('lang/vn') }}" data-language="vn">
                    <i class="flag-icon flag-icon-vn"></i> Vietnamese
                  </a>
                  <a class="dropdown-item" href="{{ url('lang/th') }}" data-language="th">
                    <i class="flag-icon flag-icon-th"></i> Thai
                  </a>
                </div>
            </div>
            <div class="d-none d-lg-block my-auto border-start">
                <a class="nav-link fs-4" style="padding-top:0!important;padding-bottom:0!important;">
                        <i id="toggleFullScreen" class="bi bi-aspect-ratio" onclick="toggleFullScreen();"></i>
                </a>
            </div>
            <div class="my-auto border-start border-end">
                <a class="nav-link  nav-link-style fs-4" style="padding-top:0!important;padding-bottom:0!important;">
                  <i class="bi bi-sun"></i>
                </a>
            </div>

    </div>
</footer>
 <script>
 
    var footerUserId = document.getElementById("curProfUserInnerAuthFoots").value;
 
    fetch(`https://nft-mini-api.herokuapp.com/api/v1/profile/profile-image/${footerUserId}`).then(data => {
        return data.text();
    }).then(response => {
        // console.log(response);
        const json = JSON.parse(response);
        if (json.profile) {
            const image = json.profile.image;
            if (image != undefined) {
                var elementExists = document.querySelector(".justifinedProfileSetImageCreated");
                if (elementExists) {
                    elementExists.src = `data:image/jpeg;base64,${image}`;
                }
                
                var elementExists2 = document.querySelector(".justifinedProfileSetImageCreated2");
                if (elementExists2) {
                    elementExists2.src = `data:image/jpeg;base64,${image}`;
                }
                // document.querySelector(".justifinedProfileSetImageCreated").src = `data:image/jpeg;base64,${image}`;
                // document.querySelector(".justifinedProfileSetImageCreated2").src = `data:image/jpeg;base64,${image}`;
            }
        }
        // window.location.reload();
    }).catch((e) => {
        console.log(e);
    });
    
    function toggleFullScreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
        $('#toggleFullScreen')
        .removeClass('bi-aspect-ratio')
        .addClass('bi-fullscreen-exit');
    } else {
      if (document.exitFullscreen) {
        document.exitFullscreen();
        $('#toggleFullScreen')
        .removeClass('bi-fullscreen-exit')
        .addClass('bi-aspect-ratio');
      }
    }
  }
    
    function startTime() {
        var today = new Date();
        var h = today.getHours();
        var m = today.getMinutes();
        var s = today.getSeconds();
        var d = today.getFullYear();
        var mm = today.getMonth() + 1;
        var dd = today.getDate();
        m = checkTime(m);
        s = checkTime(s);
        document.getElementById('txt').innerHTML =
        '<div class="d-block d-md-inline-block">' + '<i class="bi bi-clock" style="margin-right:5px"></i>' + d + '-' + mm + '-' + dd + '<i class="bi bi-chevron-right mx-1"></i>' + h + ':' + m + ':' + '<span class="text-danger">' + s + '</span>' + '</div>';
        var t = setTimeout(startTime, 500);
    }
    
    function checkTime(i) {
        if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
        return i;
    }
    
    console.log("ALl Pages");
    
</script>
<button class="btn scroll-top" style="z-index:10000;" type="button"><i class="bi bi-arrow-up-square-fill font-medium-5"></i></button>
<!-- END: Footer-->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/js/bootstrap.min.js" integrity="sha512-UR25UO94eTnCVwjbXozyeVd6ZqpaAE9naiEUBK/A+QDbfSTQFhPGj5lOR6d8tsgbBk84Ggb5A3EkjsOgPRPcKA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
