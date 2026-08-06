<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mini Loan Management — The loan book your officers already know</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=Kalam:wght@400;700&display=swap" rel="stylesheet">
<style>
  :root{
    --forest:#16332B;
    --forest-2:#1E4038;
    --forest-3:#254C40;
    --cream:#FBF6E9;
    --cream-line:#E4D9BE;
    --marigold:#E2A33D;
    --marigold-dim:#B87E23;
    --rust:#B2472F;
    --sage:#A9C2B8;
    --ink:#1C2420;
    --slate:#5B6B62;
    --max:1160px;
  }
  *{box-sizing:border-box;margin:0;padding:0;}
  html{scroll-behavior:smooth;}
  body{
    background:var(--cream);
    color:var(--ink);
    font-family:'Inter',sans-serif;
    line-height:1.55;
    -webkit-font-smoothing:antialiased;
  }
  h1,h2,h3{font-family:'Space Grotesk',sans-serif;font-weight:600;letter-spacing:-0.01em;}
  .hand{font-family:'Kalam',cursive;}
  .wrap{max-width:var(--max);margin:0 auto;padding:0 32px;}
  a{color:inherit;text-decoration:none;}
  img,svg{display:block;}

  /* ---------- NAV ---------- */
  header{
    position:sticky;top:0;z-index:50;
    background:rgba(22,51,43,0.94);
    backdrop-filter:blur(8px);
  }
  nav{
    display:flex;align-items:center;justify-content:space-between;
    max-width:var(--max);margin:0 auto;padding:16px 32px;
  }
  .brand{display:flex;align-items:center;gap:9px;color:var(--cream);font-family:'Space Grotesk',sans-serif;font-weight:700;font-size:18px;}
  .brand-mark{width:20px;height:20px;flex:none;}
  .nav-links{display:flex;align-items:center;gap:28px;}
  .nav-links a{color:var(--sage);font-size:14px;font-weight:500;transition:color .2s ease;}
  .nav-links a:hover{color:var(--cream);}
  .btn{
    display:inline-flex;align-items:center;gap:8px;
    padding:11px 20px;border-radius:6px;
    font-size:14px;font-weight:600;
    border:1px solid transparent;cursor:pointer;
    transition:transform .15s ease, background .2s ease, border-color .2s ease;
  }
  .btn:active{transform:scale(0.97);}
  .btn-marigold{background:var(--marigold);color:var(--forest);}
  .btn-marigold:hover{background:#EEB458;}
  .btn-ghost-light{border-color:rgba(251,246,233,0.3);color:var(--cream);}
  .btn-ghost-light:hover{border-color:var(--marigold);color:var(--marigold);}
  .btn-outline-forest{border-color:rgba(22,51,43,0.3);color:var(--forest);}
  .btn-outline-forest:hover{border-color:var(--forest);}

  /* ---------- HERO ---------- */
  .hero{
    background:linear-gradient(180deg,var(--forest) 0%,var(--forest-2) 100%);
    color:var(--cream);
    padding:88px 0 64px;
    position:relative;overflow:hidden;
  }
  .hero-grid{display:grid;grid-template-columns:1.05fr 0.85fr;gap:56px;align-items:center;}
  .eyebrow{
    display:inline-flex;align-items:center;gap:8px;
    font-size:12.5px;letter-spacing:0.08em;color:var(--marigold);
    text-transform:uppercase;margin-bottom:18px;font-weight:600;
  }
  .eyebrow::before{content:"";width:16px;height:1px;background:var(--marigold);}
  h1.headline{font-size:clamp(34px,4.6vw,52px);line-height:1.1;color:var(--cream);margin-bottom:20px;}
  h1.headline em{color:var(--marigold);font-style:normal;}
  .hero p.lead{font-size:16.5px;color:var(--sage);max-width:460px;margin-bottom:32px;}
  .hero-cta{display:flex;gap:14px;flex-wrap:wrap;}
  .hero-note{margin-top:18px;font-size:13px;color:var(--sage);}

  /* ---------- COLLECTION BOOK WIDGET ---------- */
  .book{
    background:var(--cream);border-radius:10px;
    box-shadow:0 24px 60px rgba(0,0,0,0.32);
    transform:rotate(1.2deg);
    max-width:360px;margin-left:auto;
  }
  .book-top{
    display:flex;justify-content:space-between;align-items:center;
    padding:16px 20px;border-bottom:2px solid var(--forest);
  }
  .book-top .title{font-family:'Space Grotesk',sans-serif;font-weight:700;font-size:14px;color:var(--forest);}
  .book-top .date{font-size:12px;color:var(--slate);}
  .book-rows{padding:6px 20px;}
  .brow{
    display:flex;justify-content:space-between;align-items:center;gap:10px;
    padding:12px 0;border-bottom:1px solid var(--cream-line);
    opacity:0;transform:translateX(-8px);
    animation:rowIn .45s ease forwards;
  }
  .brow:last-child{border-bottom:none;}
  @keyframes rowIn{to{opacity:1;transform:translateX(0);}}
  .brow .who{font-size:13.5px;font-weight:500;color:var(--ink);}
  .brow .amt{font-size:13px;color:var(--slate);}
  .brow .tick{
    font-family:'Kalam',cursive;font-size:20px;color:var(--forest-3);
    opacity:0;animation:tickIn .3s ease forwards;
  }
  .brow .tick.miss{color:var(--rust);}
  .book-foot{
    padding:14px 20px 18px;display:flex;justify-content:space-between;align-items:center;
    border-top:2px dashed var(--cream-line);margin-top:4px;
  }
  .book-foot .lbl{font-size:12px;color:var(--slate);}
  .book-foot .tot{font-family:'Space Grotesk',sans-serif;font-weight:700;font-size:16px;color:var(--forest);}
  .book-foot .scribble{font-family:'Kalam',cursive;font-size:13px;color:var(--marigold-dim);transform:rotate(-2deg);}

  /* ---------- METRICS ---------- */
  .metrics{background:var(--forest-3);padding:22px 0;}
  .metrics .wrap{display:flex;justify-content:space-between;flex-wrap:wrap;gap:20px;}
  .metric{color:var(--cream);}
  .metric .num{font-family:'Space Grotesk',sans-serif;font-size:20px;font-weight:700;color:var(--marigold);}
  .metric .lbl{font-size:12.5px;color:var(--sage);margin-top:2px;}

  /* ---------- SECTION SHARED ---------- */
  section{padding:88px 0;}
  .section-head{max-width:560px;margin-bottom:52px;}
  .section-head .eyebrow-light{
    font-size:12.5px;letter-spacing:0.08em;color:var(--marigold-dim);
    text-transform:uppercase;margin-bottom:12px;display:block;font-weight:600;
  }
  .section-head h2{font-size:clamp(26px,3.2vw,34px);line-height:1.15;color:var(--ink);}
  .section-head p{color:var(--slate);margin-top:12px;font-size:15.5px;max-width:500px;}

  /* ---------- HOW IT WORKS ---------- */
  .flow{border-top:1px solid var(--cream-line);}
  .flow-list{display:flex;flex-direction:column;}
  .flow-item{
    display:grid;grid-template-columns:56px 1fr;gap:20px;
    padding:26px 0;border-top:1px solid var(--cream-line);align-items:start;
  }
  .flow-item:last-child{border-bottom:1px solid var(--cream-line);}
  .flow-num{font-family:'Space Grotesk',sans-serif;font-size:13px;color:var(--marigold-dim);font-weight:700;padding-top:3px;}
  .flow-item h3{font-size:19px;color:var(--ink);font-weight:600;margin-bottom:4px;}
  .flow-item p{color:var(--slate);font-size:14.5px;}

  /* ---------- FEATURES ---------- */
  .features{background:var(--forest);color:var(--cream);}
  .features .section-head h2{color:var(--cream);}
  .features .section-head p{color:var(--sage);}
  .features .eyebrow-light{color:var(--marigold);}
  .fgrid{display:grid;grid-template-columns:repeat(3,1fr);gap:1px;background:rgba(251,246,233,0.1);border:1px solid rgba(251,246,233,0.1);}
  .fcard{background:var(--forest);padding:30px 26px;transition:background .2s ease;}
  .fcard:hover{background:var(--forest-2);}
  .fcard .fmark{font-size:11px;color:var(--marigold);letter-spacing:0.06em;margin-bottom:14px;display:block;font-weight:600;}
  .fcard h3{font-size:17px;color:var(--cream);font-weight:600;margin-bottom:8px;}
  .fcard p{font-size:13.5px;color:var(--sage);}

  /* ---------- PRICING ---------- */
  .pricing{border-top:1px solid var(--cream-line);}
  .pgrid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;}
  .pcard{
    background:#fff;border:1px solid var(--cream-line);border-radius:10px;padding:28px 24px;
    display:flex;flex-direction:column;
  }
  .pcard.featured{border-color:var(--marigold);box-shadow:0 12px 30px rgba(226,163,61,0.18);}
  .pcard .ptag{font-size:12px;color:var(--marigold-dim);font-weight:700;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:8px;}
  .pcard h3{font-size:20px;margin-bottom:6px;}
  .pcard .price{font-family:'Space Grotesk',sans-serif;font-size:28px;font-weight:700;margin:10px 0 4px;}
  .pcard .price span{font-size:13px;font-weight:500;color:var(--slate);}
  .pcard .pdesc{font-size:13.5px;color:var(--slate);margin-bottom:18px;}
  .pcard ul{list-style:none;margin-bottom:22px;flex:1;}
  .pcard li{font-size:13.5px;color:var(--ink);padding:7px 0;border-top:1px solid var(--cream-line);}
  .pcard li:first-child{border-top:none;}

  /* ---------- QUOTE ---------- */
  .quote-section{border-top:1px solid var(--cream-line);}
  .quote-box{max-width:740px;margin:0 auto;border-left:3px solid var(--marigold);padding-left:30px;}
  .quote-box p.q{font-family:'Space Grotesk',sans-serif;font-weight:500;font-size:clamp(20px,2.4vw,26px);line-height:1.4;color:var(--ink);margin-bottom:20px;}
  .quote-attr{font-size:13px;color:var(--slate);}
  .quote-attr strong{color:var(--ink);}

  /* ---------- CTA ---------- */
  .cta{background:var(--forest-3);color:var(--cream);text-align:center;}
  .cta h2{font-size:clamp(26px,3.4vw,36px);margin-bottom:14px;color:var(--cream);}
  .cta p{color:var(--sage);max-width:460px;margin:0 auto 28px;font-size:15.5px;}
  .cta .hero-cta{justify-content:center;}

  /* ---------- FOOTER ---------- */
  footer{background:var(--forest);color:var(--sage);padding:44px 0 28px;}
  footer .wrap{display:flex;flex-direction:column;gap:28px;}
  footer .flex-row{display:flex;justify-content:space-between;flex-wrap:wrap;gap:22px;}
  footer .brand{margin-bottom:5px;}
  footer .fine{font-size:12px;color:#5F7B70;}
  footer .flinks{display:flex;gap:24px;flex-wrap:wrap;}
  footer .flinks a{font-size:13px;color:var(--sage);}
  footer .flinks a:hover{color:var(--marigold);}

  .reveal{opacity:0;transform:translateY(14px);transition:opacity .55s ease, transform .55s ease;}
  .reveal.in{opacity:1;transform:translateY(0);}

  @media (prefers-reduced-motion: reduce){
    *{animation-duration:0.01ms !important;transition-duration:0.01ms !important;}
    html{scroll-behavior:auto;}
  }

  @media (max-width:860px){
    .hero-grid{grid-template-columns:1fr;}
    .book{margin:0 auto;transform:rotate(0.6deg);}
    .fgrid{grid-template-columns:1fr;}
    .pgrid{grid-template-columns:1fr;}
    .metrics .wrap{flex-direction:column;gap:14px;}
    .nav-links{display:none;}
  }
</style>
</head>
<body>

<header>
  <nav>
    <div class="brand">
      <svg class="brand-mark" viewBox="0 0 20 20" fill="none">
        <rect x="1" y="2" width="14" height="17" rx="1.5" stroke="#E2A33D" stroke-width="1.4"/>
        <path d="M4 6H12M4 9.5H12M4 13H9" stroke="#E2A33D" stroke-width="1.4" stroke-linecap="round"/>
        <path d="M14.5 4.5L18 4.5V16L14.5 16" stroke="#E2A33D" stroke-width="1.2" stroke-linecap="round"/>
      </svg>
      Mini Loan
    </div>
    <div class="nav-links">
      <a href="#flow">How it works</a>
      <a href="#features">Features</a>
      <a href="#pricing">Pricing</a>
      <a href="#" class="btn btn-ghost-light" style="padding:8px 16px;">Log in</a>
      <a href="#cta" class="btn btn-marigold">Start free</a>
    </div>
  </nav>
</header>

<section class="hero">
  <div class="wrap hero-grid">
    <div>
      <span class="eyebrow">Built for small lending teams</span>
      <h1 class="headline">The loan book<br>your officers <em>already know.</em><br>Just faster.</h1>
      <p class="lead">Mini Loan Management replaces the paper collection book with something your team can actually keep accurate — without asking them to become spreadsheet people.</p>
      <div class="hero-cta">
        <a href="#cta" class="btn btn-marigold">Start free</a>
        <a href="#flow" class="btn btn-ghost-light">See how it works</a>
      </div>
      <div class="hero-note">No card needed &nbsp;·&nbsp; Set up your first officer in 10 minutes</div>
    </div>

    <div class="book">
      <div class="book-top">
        <span class="title">Today's collections</span>
        <span class="date">Fri, Aug 3</span>
      </div>
      <div class="book-rows">
        <div class="brow" style="animation-delay:.1s">
          <span class="who">Chidinma O.</span><span class="amt">₦8,000</span>
          <span class="tick hand" style="animation-delay:.5s">✓</span>
        </div>
        <div class="brow" style="animation-delay:.22s">
          <span class="who">Musa B.</span><span class="amt">₦5,500</span>
          <span class="tick hand" style="animation-delay:.6s">✓</span>
        </div>
        <div class="brow" style="animation-delay:.34s">
          <span class="who">Grace E.</span><span class="amt">₦12,000</span>
          <span class="tick hand miss" style="animation-delay:.7s">—</span>
        </div>
        <div class="brow" style="animation-delay:.46s">
          <span class="who">Tunde A.</span><span class="amt">₦6,200</span>
          <span class="tick hand" style="animation-delay:.8s">✓</span>
        </div>
      </div>
      <div class="book-foot">
        <div>
          <div class="lbl">Collected today</div>
          <div class="tot">₦19,700 / ₦31,700</div>
        </div>
        <span class="scribble hand">3 of 4 done</span>
      </div>
    </div>
  </div>
</section>

<div class="metrics">
  <div class="wrap">
    <div class="metric"><div class="num">1–15</div><div class="lbl">Officers per shop, typical range</div></div>
    <div class="metric"><div class="num">10 min</div><div class="lbl">To go from signup to first loan</div></div>
    <div class="metric"><div class="num">Offline-ready</div><div class="lbl">Officers can collect without signal</div></div>
    <div class="metric"><div class="num">₦0</div><div class="lbl">Setup cost</div></div>
  </div>
</div>

<section class="flow" id="flow">
  <div class="wrap">
    <div class="section-head reveal">
      <span class="eyebrow-light">How it works</span>
      <h2>Your officer's day, digitized.</h2>
      <p>Not a new process — the same four things your team already does, minus the notebook that gets lost or the totals that don't add up at close of day.</p>
    </div>
    <div class="flow-list">
      <div class="flow-item reveal">
        <div class="flow-num">01</div>
        <div><h3>Record</h3><p>An officer opens a new loan on their phone — borrower details, amount, and repayment plan, in under a minute.</p></div>
      </div>
      <div class="flow-item reveal">
        <div class="flow-num">02</div>
        <div><h3>Collect</h3><p>In the field, officers check off each repayment as it's made — even offline. It syncs the moment they're back online.</p></div>
      </div>
      <div class="flow-item reveal">
        <div class="flow-num">03</div>
        <div><h3>Reconcile</h3><p>At close of day, the shop owner sees what was collected, what's outstanding, and who missed a payment — automatically.</p></div>
      </div>
      <div class="flow-item reveal">
        <div class="flow-num">04</div>
        <div><h3>Report</h3><p>Weekly and monthly summaries per officer and per borrower, ready whenever you need to make a decision or show your books.</p></div>
      </div>
    </div>
  </div>
</section>

<section class="features" id="features">
  <div class="wrap">
    <div class="section-head reveal">
      <span class="eyebrow-light">Made for a small team</span>
      <h2>Not scaled down. Sized right.</h2>
      <p>Everything a two-to-fifteen-person lending shop needs, and nothing that only makes sense for a bank.</p>
    </div>
    <div class="fgrid">
      <div class="fcard reveal">
        <span class="fmark">OFFICER ACCOUNTS</span>
        <h3>One login per officer</h3>
        <p>Each officer sees only their own borrowers and collections — the owner sees everyone's.</p>
      </div>
      <div class="fcard reveal">
        <span class="fmark">FIELD-READY</span>
        <h3>Works without signal</h3>
        <p>Record a collection with no data connection. It saves locally and syncs when the phone reconnects.</p>
      </div>
      <div class="fcard reveal">
        <span class="fmark">REMINDERS</span>
        <h3>SMS before it's due</h3>
        <p>Borrowers get a text a day before a repayment is due — fewer missed payments, fewer awkward visits.</p>
      </div>
      <div class="fcard reveal">
        <span class="fmark">CLOSE OF DAY</span>
        <h3>One number that's right</h3>
        <p>The owner opens one screen at close of business and knows exactly what came in and what didn't.</p>
      </div>
      <div class="fcard reveal">
        <span class="fmark">SIMPLE PLANS</span>
        <h3>Daily, weekly, or monthly</h3>
        <p>Set the repayment rhythm per loan — no rigid template that doesn't fit how your borrowers actually pay.</p>
      </div>
      <div class="fcard reveal">
        <span class="fmark">EXPORTS</span>
        <h3>Take your data with you</h3>
        <p>Export any period to Excel whenever you need it for a partner, an auditor, or your own records.</p>
      </div>
    </div>
  </div>
</section>

<section class="pricing" id="pricing">
  <div class="wrap">
    <div class="section-head reveal">
      <span class="eyebrow-light">Pricing</span>
      <h2>Priced per officer, not per bank.</h2>
      <p>No setup fee. No contract. Add or remove officers as your shop grows or slows down.</p>
    </div>
    <div class="pgrid">
      <div class="pcard reveal">
        <div class="ptag">Solo</div>
        <h3>1 officer</h3>
        <div class="price">Free<span> / forever</span></div>
        <p class="pdesc">For a single lender managing their own book.</p>
        <ul>
          <li>Up to 30 active loans</li>
          <li>Field collection app</li>
          <li>Basic reminders</li>
        </ul>
        <a href="#cta" class="btn btn-outline-forest" style="width:100%;justify-content:center;">Start free</a>
      </div>
      <div class="pcard featured reveal">
        <div class="ptag">Shop</div>
        <h3>Up to 5 officers</h3>
        <div class="price">₦12,000<span> / month</span></div>
        <p class="pdesc">For a small shop with a handful of field officers.</p>
        <ul>
          <li>Unlimited active loans</li>
          <li>Offline collection sync</li>
          <li>SMS reminders included</li>
          <li>Close-of-day reconciliation</li>
        </ul>
        <a href="#cta" class="btn btn-marigold" style="width:100%;justify-content:center;">Start free</a>
      </div>
      <div class="pcard reveal">
        <div class="ptag">Growing</div>
        <h3>Up to 15 officers</h3>
        <div class="price">₦28,000<span> / month</span></div>
        <p class="pdesc">For a shop opening a second branch or team.</p>
        <ul>
          <li>Everything in Shop</li>
          <li>Multi-branch view</li>
          <li>Priority support</li>
        </ul>
        <a href="#cta" class="btn btn-outline-forest" style="width:100%;justify-content:center;">Start free</a>
      </div>
    </div>
  </div>
</section>

<section class="quote-section">
  <div class="wrap">
    <div class="quote-box reveal">
      <p class="q">"I used to close the day not knowing if the numbers in the book actually matched what was collected. Now I just open the app."</p>
      <p class="quote-attr"><strong>Adaeze N.</strong> — Owner, a 4-officer loan shop in Lagos</p>
    </div>
  </div>
</section>

<section class="cta" id="cta">
  <div class="wrap">
    <h2 class="reveal">Close today's book in one screen.</h2>
    <p class="reveal">Set up your first officer and your first loan in the next 10 minutes.</p>
    <div class="hero-cta reveal">
      <a href="#" class="btn btn-marigold">Start free</a>
      <a href="#" class="btn btn-ghost-light">Talk to us</a>
    </div>
  </div>
</section>

<footer>
  <div class="wrap">
    <div class="flex-row">
      <div>
        <div class="brand">Mini Loan</div>
        <div class="fine">The loan book your officers already know.</div>
      </div>
      <div class="flinks">
        <a href="#flow">How it works</a>
        <a href="#features">Features</a>
        <a href="#pricing">Pricing</a>
        <a href="#">Security</a>
        <a href="#">Contact</a>
      </div>
    </div>
    <div class="fine">© 2026 Mini Loan Management. Figures shown are illustrative.</div>
  </div>
</footer>

<script>
  const revealEls = document.querySelectorAll('.reveal');
  const io = new IntersectionObserver((entries) => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
  }, { threshold: 0.15 });
  revealEls.forEach(el => io.observe(el));
</script>

</body>
</html>