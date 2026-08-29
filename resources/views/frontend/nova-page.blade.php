<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <script>
            document.documentElement.dataset.tradingTheme = "dark";
            localStorage.removeItem("trading-theme");
        </script>
        <title>{{ $page_title }} — {{ siteName() }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap"
            rel="stylesheet"
        />
        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        />
        <link rel="stylesheet" href="{{ asset('css/nova-ui.css') }}" />
        <link rel="stylesheet" href="{{ asset('css/trading-ui.css') }}" />
    </head>
    <body class="nova-landing nova-info-page">
        @include('partials.nova-preloader')
        <header class="nova-nav">
            <a class="nova-brand" href="{{ route('home') }}" aria-label="{{ siteName() }} home">
                @include('partials.site-wordmark')
            </a>
            <nav class="nova-nav-links">
                <a href="{{ route('home') }}#platform">Platform</a
                ><a href="{{ route('home') }}#security">Security</a
                ><a href="{{ route('blogetc.index') }}">Market news</a>
            </nav>
            <div class="nova-nav-actions">
                <a class="nova-link-button" href="{{ route('login') }}"
                    >Sign in</a
                ><a
                    class="nova-button nova-button-sm"
                    href="{{ route('register') }}"
                    >Open account</a
                >
            </div>
        </header>
        <main class="nova-info-main">
            @if($pageType === 'about')
            <span class="nova-eyebrow">About {{ siteName() }}</span>
            <h1>Digital markets,<br /><span>designed for clarity.</span></h1>
            <p class="nova-info-lead">
                We bring trading, exchange, wallets and portfolio intelligence
                into a single secure experience that helps customers stay
                informed and in control.
            </p>
            <div class="nova-info-grid">
                <article>
                    <i class="bi bi-compass"></i>
                    <h2>Our mission</h2>
                    <p>
                        Make sophisticated market tools understandable,
                        responsive and accessible without compromising security.
                    </p>
                </article>
                <article>
                    <i class="bi bi-stars"></i>
                    <h2>Our approach</h2>
                    <p>
                        Thoughtful product design, transparent activity records
                        and a focused interface for every market decision.
                    </p>
                </article>
                <article>
                    <i class="bi bi-shield-check"></i>
                    <h2>Our commitment</h2>
                    <p>
                        Continuously improve account controls, operational
                        reliability and the quality of customer support.
                    </p>
                </article>
            </div>
            @elseif($pageType === 'terms')
            <span class="nova-eyebrow">Legal</span>
            <h1>Terms of service.</h1>
            <p class="nova-info-lead">
                These terms govern access to and use of {{ siteName() }}. By
                creating an account or using the platform, you agree to these
                terms and applicable policies.
            </p>
            <div class="nova-legal">
                <h2>1. Account responsibilities</h2>
                <p>
                    You must provide accurate registration information, protect
                    your login credentials and notify support promptly if you
                    suspect unauthorized activity.
                </p>
                <h2>2. Platform use</h2>
                <p>
                    You may use the platform only for lawful purposes and in
                    accordance with applicable financial, identity-verification
                    and transaction requirements.
                </p>
                <h2>3. Market risk</h2>
                <p>
                    Digital assets and trading products can be volatile. You are
                    responsible for evaluating each transaction and
                    understanding that past performance does not guarantee
                    future results.
                </p>
                <h2>4. Availability</h2>
                <p>
                    We work to maintain reliable access, but maintenance,
                    third-party services, market conditions or events outside
                    our control may affect availability.
                </p>
                <h2>5. Support</h2>
                <p>
                    Questions about these terms can be submitted through the
                    support page.
                </p>
            </div>
            @else
            <span class="nova-eyebrow">Customer support</span>
            <h1>How can we help?</h1>
            <p class="nova-info-lead">
                Tell us what you need and our support team will respond through
                the email address you provide.
            </p>
            @include('includes.partials.messages')
            <form
                class="nova-contact-form"
                action="{{ route('contact.send') }}"
                method="POST"
                enctype="multipart/form-data"
            >
                @csrf
                <div>
                    <label for="name">Name</label
                    ><input
                        id="name"
                        name="name"
                        value="{{ old('name', auth()->user()->name ?? '') }}"
                        required
                    />
                </div>
                <div>
                    <label for="email">Email</label
                    ><input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email', auth()->user()->email ?? '') }}"
                        required
                    />
                </div>
                <div class="full">
                    <label for="subject">Subject</label
                    ><input
                        id="subject"
                        name="subject"
                        value="{{ old('subject') }}"
                        required
                    />
                </div>
                <div class="full">
                    <label for="message">Message</label
                    ><textarea id="message" name="message" rows="6" required>
{{ old('message') }}</textarea
                    >
                </div>
                <div class="full">
                    <label for="attachments"
                        >Attachments
                        <small>Optional — JPG, PNG or PDF</small></label
                    ><input
                        id="attachments"
                        type="file"
                        name="attachments[]"
                        multiple
                        accept=".jpg,.jpeg,.png,.pdf"
                    />
                </div>
                <button class="nova-button" type="submit">
                    Send message <i class="bi bi-arrow-right"></i>
                </button>
            </form>
            @endif
        </main>
        <footer class="nova-footer">
            <a class="nova-brand" href="{{ route('home') }}" aria-label="{{ siteName() }} home">
                @include('partials.site-wordmark')
            </a>
            <p>Digital markets, thoughtfully designed.</p>
            <div>
                <a href="{{ route('frontend.pages.about') }}">About</a
                ><a href="{{ route('contact') }}">Support</a
                ><a href="{{ route('frontend.pages.terms') }}">Terms</a
                >@if(Route::has('policy.show'))<a
                    href="{{ route('policy.show') }}"
                    >Privacy</a
                >@endif
            </div>
            <small
                >© {{ date('Y') }} {{ siteName() }}. All rights reserved.</small
            >
        </footer>
    </body>
</html>
