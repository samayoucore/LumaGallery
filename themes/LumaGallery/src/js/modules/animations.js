/**
 * Animations — global motion layer: scroll reveal + stagger, light parallax,
 * animated counters and magnetic buttons. All vanilla, RAF-throttled, and
 * fully guarded by prefers-reduced-motion / touch / element-presence checks.
 *
 * Markup hooks (opt-in):
 *   [data-reveal]            → element fades/slides in once on scroll
 *   [data-reveal-children]   → its direct children reveal with a stagger
 *   [data-parallax="0.15"]   → translateY by factor as it scrolls
 *   [data-count][data-suffix]→ number counts up from 0 when in view
 *   [data-magnetic] / .luma-button--lg → magnetic pull toward the pointer
 */

const ANIMATIONS_SELECTORS = {
  reveal:   '[data-reveal], [data-reveal-children]',
  parallax: '[data-parallax]',
  counter:  '[data-count]',
  magnet:   '[data-magnetic], .luma-button--lg',
  tilt:     '.luma-artwork-card, .luma-artist-card, .luma-exhibition-card, .luma-post-card, .luma-journal-featured',
};

const ANIMATIONS_CONFIG = {
  revealThreshold: 0.14,
  staggerStep:     90,    // ms between staggered children
  counterDuration: 1500,  // ms
  magnetStrength:  0.30,
  magnetRadius:    90,     // px beyond the button where pull begins
  tiltMax:          4.8,    // degrees
};

const ANIMATIONS_STATE = {
  reduced: false,
  parallaxItems: [],
  parallaxTicking: false,
  spotlightTicking: false,
  spotlightX: 0,
  spotlightY: 0,
};

// ─── Scroll reveal + stagger ──────────────────────────────────────────────────

function animReveal() {
  const items = document.querySelectorAll( ANIMATIONS_SELECTORS.reveal );
  if ( !items.length ) return;

  if ( !window.IntersectionObserver ) {
    items.forEach( ( el ) => el.classList.add( 'is-revealed' ) );
    return;
  }

  const io = new IntersectionObserver( ( entries ) => {
    entries.forEach( ( en ) => {
      if ( !en.isIntersecting ) return;
      const el = en.target;
      if ( el.hasAttribute( 'data-reveal-children' ) ) {
        const step = parseInt( el.dataset.revealChildren, 10 ) || ANIMATIONS_CONFIG.staggerStep;
        // Cap the index so large grids don't accrue multi-second delays.
        Array.from( el.children ).forEach( ( c, i ) => { c.style.animationDelay = ( Math.min( i, 10 ) * step ) + 'ms'; } );
      }
      el.classList.add( 'is-revealed' );
      io.unobserve( el );
    } );
  }, { threshold: ANIMATIONS_CONFIG.revealThreshold, rootMargin: '0px 0px -8% 0px' } );

  items.forEach( ( el ) => io.observe( el ) );
}

// ─── Animated counters ────────────────────────────────────────────────────────

function animCountUp( el ) {
  const target = parseFloat( el.dataset.count );
  if ( isNaN( target ) ) return;
  const suffix = el.dataset.suffix || '';
  const prefix = el.dataset.prefix || '';

  if ( ANIMATIONS_STATE.reduced ) {
    el.textContent = prefix + target + suffix;
    return;
  }

  const start = performance.now();
  const dur = ANIMATIONS_CONFIG.counterDuration;

  function step( now ) {
    const p = Math.min( 1, ( now - start ) / dur );
    const eased = 1 - Math.pow( 1 - p, 3 ); // easeOutCubic
    el.textContent = prefix + Math.round( target * eased ) + suffix;
    if ( p < 1 ) requestAnimationFrame( step );
  }
  requestAnimationFrame( step );
}

function animCounters() {
  const els = document.querySelectorAll( ANIMATIONS_SELECTORS.counter );
  if ( !els.length ) return;

  if ( !window.IntersectionObserver ) {
    els.forEach( animCountUp );
    return;
  }

  const io = new IntersectionObserver( ( entries ) => {
    entries.forEach( ( en ) => {
      if ( en.isIntersecting ) { animCountUp( en.target ); io.unobserve( en.target ); }
    } );
  }, { threshold: 0.6 } );

  els.forEach( ( el ) => io.observe( el ) );
}

// ─── Parallax ─────────────────────────────────────────────────────────────────

function animParallaxUpdate() {
  const vh = window.innerHeight;
  ANIMATIONS_STATE.parallaxItems.forEach( ( item ) => {
    const rect = item.el.getBoundingClientRect();
    if ( rect.bottom < -200 || rect.top > vh + 200 ) return; // offscreen
    const fromCenter = ( rect.top + rect.height / 2 ) - vh / 2;
    item.el.style.transform = `translate3d(0, ${ ( -fromCenter * item.factor ).toFixed( 2 ) }px, 0)`;
  } );
  ANIMATIONS_STATE.parallaxTicking = false;
}

function animParallax() {
  const els = document.querySelectorAll( ANIMATIONS_SELECTORS.parallax );
  if ( !els.length ) return;

  ANIMATIONS_STATE.parallaxItems = Array.from( els ).map( ( el ) => {
    el.style.willChange = 'transform';
    return { el, factor: parseFloat( el.dataset.parallax ) || 0.12 };
  } );

  const onScroll = () => {
    if ( !ANIMATIONS_STATE.parallaxTicking ) {
      ANIMATIONS_STATE.parallaxTicking = true;
      requestAnimationFrame( animParallaxUpdate );
    }
  };

  window.addEventListener( 'scroll', onScroll, { passive: true } );
  window.addEventListener( 'resize', onScroll, { passive: true } );
  animParallaxUpdate();
}

// ─── Magnetic buttons ─────────────────────────────────────────────────────────

function animMagnetic() {
  if ( window.matchMedia( '(hover: none), (pointer: coarse)' ).matches ) return;

  const els = document.querySelectorAll( ANIMATIONS_SELECTORS.magnet );
  els.forEach( ( el ) => {
    const r = ANIMATIONS_CONFIG.magnetRadius;

    function onMove( e ) {
      const rect = el.getBoundingClientRect();
      const cx = rect.left + rect.width / 2;
      const cy = rect.top + rect.height / 2;
      const dx = e.clientX - cx;
      const dy = e.clientY - cy;
      if ( Math.abs( dx ) > rect.width / 2 + r || Math.abs( dy ) > rect.height / 2 + r ) { reset(); return; }
      el.style.transform = `translate(${ dx * ANIMATIONS_CONFIG.magnetStrength }px, ${ dy * ANIMATIONS_CONFIG.magnetStrength }px)`;
    }
    function reset() { el.style.transform = ''; }

    el.addEventListener( 'mousemove', onMove );
    el.addEventListener( 'mouseleave', reset );
  } );
}

function animSpotlightUpdate() {
  if ( document.body ) {
    document.body.style.setProperty( '--luma-cursor-x', `${ ANIMATIONS_STATE.spotlightX }px` );
    document.body.style.setProperty( '--luma-cursor-y', `${ ANIMATIONS_STATE.spotlightY }px` );
  }
  ANIMATIONS_STATE.spotlightTicking = false;
}

function animSpotlight() {
  if ( window.matchMedia( '(hover: none), (pointer: coarse)' ).matches ) return;

  document.addEventListener( 'pointermove', ( e ) => {
    ANIMATIONS_STATE.spotlightX = e.clientX;
    ANIMATIONS_STATE.spotlightY = e.clientY;

    if ( !ANIMATIONS_STATE.spotlightTicking ) {
      ANIMATIONS_STATE.spotlightTicking = true;
      requestAnimationFrame( animSpotlightUpdate );
    }
  }, { passive: true } );
}

function animTiltCards() {
  if ( window.matchMedia( '(hover: none), (pointer: coarse)' ).matches ) return;

  const cards = document.querySelectorAll( ANIMATIONS_SELECTORS.tilt );
  if ( !cards.length ) return;

  cards.forEach( ( card ) => {
    function onMove( e ) {
      const rect = card.getBoundingClientRect();
      if ( !rect.width || !rect.height ) return;

      const relX = ( e.clientX - rect.left ) / rect.width - 0.5;
      const relY = ( e.clientY - rect.top ) / rect.height - 0.5;
      card.style.setProperty( '--tilt-x', `${ ( relX * ANIMATIONS_CONFIG.tiltMax ).toFixed( 2 ) }deg` );
      card.style.setProperty( '--tilt-y', `${ ( relY * -ANIMATIONS_CONFIG.tiltMax ).toFixed( 2 ) }deg` );
    }

    function reset() {
      card.style.setProperty( '--tilt-x', '0deg' );
      card.style.setProperty( '--tilt-y', '0deg' );
    }

    card.addEventListener( 'pointermove', onMove, { passive: true } );
    card.addEventListener( 'pointerleave', reset );
  } );
}

// ─── Boot ─────────────────────────────────────────────────────────────────────

export function initAnimations() {
  ANIMATIONS_STATE.reduced = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

  // Gate reveal/hover CSS on JS being present (progressive enhancement).
  document.documentElement.classList.add( 'js-anim' );

  animReveal();
  animCounters();

  if ( !ANIMATIONS_STATE.reduced ) {
    animSpotlight();
    animParallax();
    animMagnetic();
    animTiltCards();
  }
}
