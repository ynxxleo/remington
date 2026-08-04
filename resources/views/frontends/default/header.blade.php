<div class="mein-menu">
    <nav class="navbar navbar-expand-lg navbar-dark ">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img style="max-height: 50px" src="{{getImage(imagePath()['logoIcon']['path'] .'/logo.png')}}" class="logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">Testimonials</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#faq">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/blog">Blog</a>
                    </li>
                    {{-- <li class="nav-item">
                        <div class="language-select">
                            <select class="select-bar">
                                <option value="">EN</option>
                                <option value="">IN</option>
                                <option value="">BN</option>
                            </select>
                        </div>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary text-white p-2" href="/login">Start Now !</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>
