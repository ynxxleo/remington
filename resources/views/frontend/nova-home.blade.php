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
        <meta
            name="description"
            content="Trade digital assets with a secure, intelligent platform built for clarity and control."
        />
        <title>{{ siteName() }} — Modern digital asset trading</title>
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
    <body class="nova-landing">
        @include('partials.nova-preloader')
        <header class="nova-nav" id="top">
            <a
                class="nova-brand"
                href="{{ route('home') }}"
                aria-label="{{ siteName() }} home"
                >@include('partials.site-wordmark')</a
            >
            <div class="nova-mobile-tools">
                <button
                    class="nova-menu-toggle"
                    type="button"
                    aria-label="Toggle menu"
                    aria-expanded="false"
                >
                    <i class="bi bi-list"></i>
                </button>
            </div>
            <nav class="nova-nav-links">
                <a href="#platform">Platform</a><a href="#markets">Markets</a
                ><a href="#security">Security</a
                ><a href="#how-it-works">How it works</a>
            </nav>
            <div class="nova-nav-actions">
                @auth<a class="nova-link-button" href="{{ route('user.home') }}"
                    >Dashboard</a
                >@else<a class="nova-link-button" href="{{ route('login') }}"
                    >Sign in</a
                ><a
                    class="nova-button nova-button-sm"
                    href="{{ route('register') }}"
                    >Open account <i class="bi bi-arrow-up-right"></i></a
                >@endauth
            </div>
        </header>
        <main>
            <section class="nova-hero">
                <div class="nova-hero-glow"></div>
                <div class="nova-hero-copy">
                    <span class="nova-eyebrow"
                        ><i class="bi bi-stars"></i> A clearer way to
                        trade</span
                    >
                    <h1>Own your next <span>market move.</span></h1>
                    <p>
                        One secure workspace for trading, exchanging and
                        managing digital assets—with the context you need to act
                        with confidence.
                    </p>
                    <div class="nova-hero-actions">
                        <a class="nova-button" href="{{ route('register') }}"
                            >Start trading <i class="bi bi-arrow-right"></i></a
                        ><a
                            class="nova-button nova-button-ghost"
                            href="#platform"
                            ><i class="bi bi-play-fill"></i> Explore platform</a
                        >
                    </div>
                    <div class="nova-trust-row">
                        <span
                            ><i class="bi bi-shield-check"></i> Account
                            protection</span
                        ><span
                            ><i class="bi bi-lightning-charge"></i> Fast
                            execution</span
                        ><span
                            ><i class="bi bi-headset"></i> Dedicated
                            support</span
                        >
                    </div>
                </div>
                <div class="nova-hero-visual" aria-hidden="true">
                    <img
                        class="nova-hero-image-dark"
                        src="{{ asset('images/nova/hero-orbit.png') }}"
                        alt=""
                    />
                    <div class="nova-float-card nova-market-card">
                        <div>
                            <span class="nova-coin">B</span
                            ><span>Bitcoin<small>BTC / USD · Live</small></span>
                        </div>
                        <strong
                            ><span data-bitcoin-price
                                >{{ $bitcoin && is_numeric($bitcoin->price) ?
                                '$'.number_format($bitcoin->price, 2) :
                                'Loading…' }}</span
                            >
                            <small
                                data-bitcoin-change
                                class="{{ $bitcoin && $bitcoin->twenty_four < 0 ? 'is-negative' : '' }}"
                                >{{ $bitcoin &&
                                is_numeric($bitcoin->twenty_four) ?
                                (($bitcoin->twenty_four >= 0 ? '+' :
                                '').number_format($bitcoin->twenty_four, 2).'%')
                                : '' }}</small
                            ></strong
                        ><svg viewBox="0 0 180 42">
                            <path
                                d="M1 34 C24 38,30 12,51 22 S80 36,96 17 S130 6,143 14 S163 9,179 3"
                            />
                        </svg>
                    </div>
                    <div class="nova-float-card nova-balance-card">
                        <small>Portfolio balance</small
                        ><strong>$126,640.08</strong
                        ><span
                            ><i class="bi bi-graph-up-arrow"></i> 5.14% this
                            month</span
                        >
                    </div>
                </div>
            </section>
            <section class="nova-ticker">
                <span>Market snapshot</span>
                <div>
                    <b>BTC</b>
                    <span data-bitcoin-price
                        >{{ $bitcoin && is_numeric($bitcoin->price) ?
                        '$'.number_format($bitcoin->price, 2) : 'Loading…'
                        }}</span
                    >
                    <em
                        data-bitcoin-change
                        class="{{ $bitcoin && $bitcoin->twenty_four < 0 ? 'is-negative' : '' }}"
                        >{{ $bitcoin && is_numeric($bitcoin->twenty_four) ?
                        (($bitcoin->twenty_four >= 0 ? '+' :
                        '').number_format($bitcoin->twenty_four, 2).'%') : ''
                        }}</em
                    >
                </div>
                <div><b>ETH</b> $3,471 <em>+2.84%</em></div>
                <div><b>SOL</b> $174.82 <em>+6.12%</em></div>
                <div><b>USDT</b> $1.00 <em>+0.01%</em></div>
            </section>
            <section class="nova-proof" aria-label="Platform highlights">
                <article>
                    <strong>24/7</strong><span>Market access</span>
                </article>
                <article>
                    <strong>99.9%</strong
                    ><span>Platform availability target</span>
                </article>
                <article>
                    <strong>2FA</strong><span>Account protection</span>
                </article>
                <article>
                    <strong>One</strong><span>Unified trading workspace</span>
                </article>
            </section>
            <section class="nova-section" id="platform">
                <div class="nova-section-heading">
                    <span class="nova-eyebrow">Everything connected</span>
                    <h2>A complete market toolkit.<br />One calm interface.</h2>
                    <p>
                        Move from insight to execution without stitching
                        together multiple platforms.
                    </p>
                </div>
                <div class="nova-feature-grid">
                    <article class="nova-feature nova-feature-wide">
                        <div class="nova-icon">
                            <i class="bi bi-bar-chart-line"></i>
                        </div>
                        <h3>Professional trading</h3>
                        <p>
                            Responsive charts, live market context, flexible
                            contracts and fast execution in one focused
                            workspace.
                        </p>
                        <div class="nova-mini-chart">
                            <span></span><span></span><span></span><span></span
                            ><span></span><span></span><span></span>
                        </div>
                    </article>
                    <article class="nova-feature">
                        <div class="nova-icon">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <h3>Unified wallets</h3>
                        <p>
                            Track balances, send, receive and exchange supported
                            assets without losing the thread.
                        </p>
                    </article>
                    <article class="nova-feature">
                        <div class="nova-icon"><i class="bi bi-robot"></i></div>
                        <h3>Automated strategies</h3>
                        <p>
                            Use intelligent trading tools and monitor strategy
                            performance from your dashboard.
                        </p>
                    </article>
                    <article class="nova-feature">
                        <div class="nova-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <h3>Copy trading</h3>
                        <p>
                            Discover experienced market participants and follow
                            strategies aligned with your goals.
                        </p>
                    </article>
                    <article class="nova-feature nova-feature-accent">
                        <div class="nova-icon">
                            <i class="bi bi-arrow-left-right"></i>
                        </div>
                        <h3>Instant exchange</h3>
                        <p>
                            Convert assets with transparent previews and an
                            auditable history.
                        </p>
                        <a href="{{ route('register') }}"
                            >Explore exchange <i class="bi bi-arrow-right"></i
                        ></a>
                    </article>
                </div>
            </section>
            <section class="nova-section nova-product-story" id="solutions">
                <div>
                    <span class="nova-eyebrow">Made for every move</span>
                    <h2>One account.<br />Multiple ways to participate.</h2>
                    <p>
                        Build a workflow that matches how you approach digital
                        markets—from hands-on execution to assisted strategies
                        and portfolio oversight.
                    </p>
                    <a class="nova-text-link" href="{{ route('register') }}"
                        >Create your workspace <i class="bi bi-arrow-right"></i
                    ></a>
                </div>
                <div class="nova-product-stack">
                    <article>
                        <i class="bi bi-lightning-charge"></i>
                        <div>
                            <h3>Execute with confidence</h3>
                            <p>
                                Preview key details, act quickly, and keep a
                                clear record of every trade.
                            </p>
                        </div>
                    </article>
                    <article>
                        <i class="bi bi-pie-chart"></i>
                        <div>
                            <h3>Understand your portfolio</h3>
                            <p>
                                See balances and account activity together
                                instead of switching between tools.
                            </p>
                        </div>
                    </article>
                    <article>
                        <i class="bi bi-cpu"></i>
                        <div>
                            <h3>Explore automation</h3>
                            <p>
                                Start and monitor supported strategies from the
                                same secure dashboard.
                            </p>
                        </div>
                    </article>
                </div>
            </section>
            <section class="nova-section nova-security" id="security">
                <div class="nova-security-visual">
                    <div class="nova-orbit">
                        <i class="bi bi-shield-lock-fill"></i><span></span
                        ><span></span><span></span>
                    </div>
                </div>
                <div>
                    <span class="nova-eyebrow">Security by design</span>
                    <h2>Your assets deserve more than a password.</h2>
                    <p>
                        Layered account controls, identity verification and
                        continuous monitoring help protect every action.
                    </p>
                    <ul>
                        <li>
                            <i class="bi bi-check2"></i
                            ><span
                                ><b>Two-factor authentication</b
                                ><small
                                    >Add a second layer of protection to account
                                    access.</small
                                ></span
                            >
                        </li>
                        <li>
                            <i class="bi bi-check2"></i
                            ><span
                                ><b>Verified transactions</b
                                ><small
                                    >Review critical details before funds
                                    move.</small
                                ></span
                            >
                        </li>
                        <li>
                            <i class="bi bi-check2"></i
                            ><span
                                ><b>Activity visibility</b
                                ><small
                                    >Keep a clear record of deposits,
                                    withdrawals and trades.</small
                                ></span
                            >
                        </li>
                    </ul>
                </div>
            </section>
            <section class="nova-section" id="how-it-works">
                <div class="nova-section-heading nova-centered">
                    <span class="nova-eyebrow">Built for momentum</span>
                    <h2>From account to market in three steps.</h2>
                </div>
                <div class="nova-steps">
                    <article>
                        <span>01</span><i class="bi bi-person-plus"></i>
                        <h3>Create your account</h3>
                        <p>
                            Register with your details and secure your sign-in.
                        </p>
                    </article>
                    <article>
                        <span>02</span><i class="bi bi-patch-check"></i>
                        <h3>Complete verification</h3>
                        <p>Verify your profile to unlock protected features.</p>
                    </article>
                    <article>
                        <span>03</span><i class="bi bi-candlestick"></i>
                        <h3>Fund and trade</h3>
                        <p>Choose your market, manage your wallet and trade.</p>
                    </article>
                </div>
            </section>
            <section class="nova-section nova-stories" id="stories">
                <div class="nova-section-heading">
                    <span class="nova-eyebrow">Built around clarity</span>
                    <h2>A calmer way to navigate digital markets.</h2>
                    <p>
                        A focused experience for people who value visibility,
                        control, and responsive tools.
                    </p>
                </div>
                <div class="nova-story-grid">
                    <blockquote>
                        <i class="bi bi-quote"></i>
                        <p>
                            Everything I need is visible without the usual
                            clutter. Funding, activity and positions finally
                            feel connected.
                        </p>
                        <footer>
                            <span>AM</span>
                            <div>
                                <b>Alex M.</b><small>Active trader</small>
                            </div>
                        </footer>
                    </blockquote>
                    <blockquote>
                        <i class="bi bi-quote"></i>
                        <p>
                            The portfolio view makes it much easier to
                            understand what is happening before I make the next
                            move.
                        </p>
                        <footer>
                            <span>JK</span>
                            <div>
                                <b>Jordan K.</b
                                ><small>Digital asset investor</small>
                            </div>
                        </footer>
                    </blockquote>
                    <blockquote>
                        <i class="bi bi-quote"></i>
                        <p>
                            I can move from market research to execution without
                            jumping across several disconnected products.
                        </p>
                        <footer>
                            <span>SR</span>
                            <div><b>Sam R.</b><small>Strategy user</small></div>
                        </footer>
                    </blockquote>
                </div>
            </section>
            <section class="nova-section nova-faq" id="faq">
                <div>
                    <span class="nova-eyebrow">Frequently asked</span>
                    <h2>Answers before you get started.</h2>
                    <p>
                        Need something else? Our support team is ready to help.
                    </p>
                    <a class="nova-text-link" href="{{ route('contact') }}"
                        >Contact support <i class="bi bi-arrow-right"></i
                    ></a>
                </div>
                <div class="nova-faq-list">
                    <details open>
                        <summary>
                            What can I manage from the platform?<i
                                class="bi bi-plus"
                            ></i>
                        </summary>
                        <p>
                            You can access supported trading, exchange, wallet,
                            contract, automation and account activity features
                            from one workspace.
                        </p>
                    </details>
                    <details>
                        <summary>
                            How is my account protected?<i
                                class="bi bi-plus"
                            ></i>
                        </summary>
                        <p>
                            The platform supports identity controls, two-factor
                            authentication, activity visibility and verification
                            around important actions.
                        </p>
                    </details>
                    <details>
                        <summary>
                            Can I explore before using real funds?<i
                                class="bi bi-plus"
                            ></i>
                        </summary>
                        <p>
                            Where enabled, practice contracts let you become
                            familiar with the workflow before moving to live
                            activity.
                        </p>
                    </details>
                    <details>
                        <summary>
                            Where can I get assistance?<i
                                class="bi bi-plus"
                            ></i>
                        </summary>
                        <p>
                            Use the support page from the website or your
                            dashboard to contact the team and follow your
                            requests.
                        </p>
                    </details>
                </div>
            </section>
            <section class="nova-cta" id="markets">
                <span class="nova-eyebrow">Your next move starts here</span>
                <h2>Trade with more clarity.</h2>
                <p>
                    Join a modern digital asset experience built around control,
                    speed and confidence.
                </p>
                <a class="nova-button" href="{{ route('register') }}"
                    >Create free account <i class="bi bi-arrow-up-right"></i
                ></a>
            </section>
        </main>
        <footer class="nova-footer nova-footer-pro">
            <div class="nova-footer-intro">
                <a class="nova-brand" href="#top" aria-label="{{ siteName() }} home">
                    @include('partials.site-wordmark')
                </a>
                <p>
                    A secure, focused workspace for trading and managing digital
                    assets.
                </p>
                <span
                    ><i class="bi bi-circle-fill"></i> Platform
                    operational</span
                >
            </div>
            <div class="nova-footer-column">
                <b>Product</b><a href="#platform">Trading platform</a
                ><a href="#solutions">Solutions</a
                ><a href="#security">Security</a
                ><a href="#how-it-works">How it works</a>
            </div>
            <div class="nova-footer-column">
                <b>Resources</b
                ><a href="{{ route('blogetc.index') }}">Market news</a
                ><a href="#faq">Help center</a
                ><a href="{{ route('contact') }}">Contact support</a
                ><a href="#stories">Customer stories</a>
            </div>
            <div class="nova-footer-column">
                <b>Company</b
                ><a href="{{ route('frontend.pages.about') }}">About us</a
                ><a href="{{ route('frontend.pages.about') }}#mission"
                    >Our mission</a
                ><a href="{{ route('contact') }}">Partnerships</a
                ><a href="{{ route('contact') }}">Careers</a>
            </div>
            <div class="nova-footer-column">
                <b>Account</b>@auth<a href="{{ route('user.home') }}"
                    >Dashboard</a
                >@else<a href="{{ route('login') }}">Sign in</a
                ><a href="{{ route('register') }}">Open account</a>@endauth<a
                    href="{{ route('contact') }}"
                    >Support</a
                >
            </div>
            <div class="nova-footer-bottom">
                <small
                    >© {{ date('Y') }} {{ siteName() }}. All rights
                    reserved.</small
                >
                <nav>
                    <a href="{{ route('frontend.pages.terms') }}">Terms</a
                    >@if(Route::has('policy.show'))<a
                        href="{{ route('policy.show') }}"
                        >Privacy</a
                    >@endif<a href="{{ route('contact') }}"
                        >Cookie preferences</a
                    >
                </nav>
                <a href="#top">Back to top <i class="bi bi-arrow-up"></i></a>
            </div>
        </footer>
        <script>
            const toggle=document.querySelector('.nova-menu-toggle');
            toggle?.addEventListener('click',()=>{const open=document.body.classList.toggle('nova-menu-open');toggle.setAttribute('aria-expanded',open?'true':'false')});
            (function(){
                const endpoint=@json(route('market-data.bitcoin'));
                const price=new Intl.NumberFormat('en-US',{style:'currency',currency:'USD',minimumFractionDigits:2,maximumFractionDigits:2});
                const refresh=async()=>{
                    try {
                        const response=await fetch(endpoint,{headers:{Accept:'application/json'},cache:'no-store'});
                        if(!response.ok)return;
                        const market=await response.json();
                        if(!market.available)return;
                        document.querySelectorAll('[data-bitcoin-price]').forEach(node=>node.textContent=price.format(market.price));
                        document.querySelectorAll('[data-bitcoin-change]').forEach(node=>{
                            if(market.change_24h===null){node.textContent='';return;}
                            node.textContent=(market.change_24h>=0?'+':'')+market.change_24h.toFixed(2)+'%';
                            node.classList.toggle('is-negative',market.change_24h<0);
                        });
                    } catch (error) {}
                };
                refresh();
                window.setInterval(refresh,60000);
            })();
        </script>
    </body>
</html>
