/**
 * Distorted Text — the signature "broken type" interaction.
 *
 * Splits any `.js-distort-text` element into per-letter spans; as the pointer
 * approaches, letters are pushed away / rotated, then ease back when it leaves.
 * One shared pointer + one RAF loop drive every target on the page.
 *
 * Safe by design: bails on touch devices and prefers-reduced-motion, no-ops
 * when no target exists, pauses targets that scroll out of view, and reads
 * cached layout offsets (no per-frame thrashing).
 */

const DISTORTED_TEXT_SELECTORS = { target: '.js-distort-text' };

const DISTORTED_TEXT_CONFIG = {
  radius:    140,   // px — influence area around the pointer
  maxShift:  22,    // px — peak letter displacement
  maxRotate: 16,    // deg — peak letter rotation
  ease:      0.16,  // 0–1 — return/approach smoothing
};

const DISTORTED_TEXT_STATE = {
  letters: [],            // { el, bx, by, x, y, r, host }
  hosts:   [],            // { el, visible }
  pointer: { x: -99999, y: -99999 },
  raf:     null,
};

function dtSplit( el ) {
  const text = el.textContent;
  el.dataset.distortOriginal = text;
  el.textContent = '';
  el.setAttribute( 'aria-hidden', 'true' );

  const host = { el, visible: true };
  DISTORTED_TEXT_STATE.hosts.push( host );

  for ( const ch of text ) {
    const span = document.createElement( 'span' );
    span.className = 'luma-distort__char';
    span.textContent = ch === ' ' ? ' ' : ch;
    el.appendChild( span );
    DISTORTED_TEXT_STATE.letters.push( {
      el: span, host,
      bx: span.offsetLeft + span.offsetWidth / 2,
      by: span.offsetTop + span.offsetHeight / 2,
      x: 0, y: 0, r: 0,
    } );
  }

  if ( window.IntersectionObserver ) {
    new IntersectionObserver( ( entries ) => {
      entries.forEach( ( en ) => { host.visible = en.isIntersecting; } );
    }, { threshold: 0 } ).observe( el );
  }
}

function dtRecalc() {
  DISTORTED_TEXT_STATE.letters.forEach( ( l ) => {
    l.bx = l.el.offsetLeft + l.el.offsetWidth / 2;
    l.by = l.el.offsetTop + l.el.offsetHeight / 2;
  } );
}

function dtTick() {
  const { letters, hosts, pointer } = DISTORTED_TEXT_STATE;
  const cfg = DISTORTED_TEXT_CONFIG;
  let moving = false;

  // One rect read per host (cheap, transform-independent), reused by its letters.
  const rects = new Map();
  hosts.forEach( ( h ) => { if ( h.visible ) rects.set( h, h.el.getBoundingClientRect() ); } );

  letters.forEach( ( l ) => {
    let tx = 0, ty = 0, tr = 0;
    const rect = rects.get( l.host );

    if ( rect ) {
      const cx = rect.left + l.bx;
      const cy = rect.top + l.by;
      const dx = cx - pointer.x;
      const dy = cy - pointer.y;
      const dist = Math.hypot( dx, dy );

      if ( dist < cfg.radius ) {
        const force = 1 - dist / cfg.radius;
        const ang = Math.atan2( dy, dx );
        tx = Math.cos( ang ) * force * cfg.maxShift;
        ty = Math.sin( ang ) * force * cfg.maxShift;
        tr = ( dx > 0 ? 1 : -1 ) * force * cfg.maxRotate;
      }
    }

    l.x += ( tx - l.x ) * cfg.ease;
    l.y += ( ty - l.y ) * cfg.ease;
    l.r += ( tr - l.r ) * cfg.ease;

    if ( Math.abs( l.x ) > 0.05 || Math.abs( l.y ) > 0.05 || Math.abs( l.r ) > 0.05 ) {
      l.el.style.transform = `translate(${ l.x.toFixed( 2 ) }px, ${ l.y.toFixed( 2 ) }px) rotate(${ l.r.toFixed( 2 ) }deg)`;
      moving = true;
    } else if ( l.el.style.transform ) {
      l.el.style.transform = '';
    }
  } );

  DISTORTED_TEXT_STATE.raf = moving ? requestAnimationFrame( dtTick ) : null;
}

function dtOnMove( e ) {
  DISTORTED_TEXT_STATE.pointer.x = e.clientX;
  DISTORTED_TEXT_STATE.pointer.y = e.clientY;
  if ( !DISTORTED_TEXT_STATE.raf ) DISTORTED_TEXT_STATE.raf = requestAnimationFrame( dtTick );
}

export function initDistortedText() {
  if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) return;
  if ( window.matchMedia( '(hover: none), (pointer: coarse)' ).matches ) return;

  const targets = document.querySelectorAll( DISTORTED_TEXT_SELECTORS.target );
  if ( !targets.length ) return;

  targets.forEach( dtSplit );

  window.addEventListener( 'mousemove', dtOnMove, { passive: true } );

  let resizeTimer = null;
  window.addEventListener( 'resize', () => {
    clearTimeout( resizeTimer );
    resizeTimer = setTimeout( dtRecalc, 150 );
  }, { passive: true } );
}
