/**
 * Hero parallax — tracks mouse and applies gentle offset to the sculpture
 * (±12 px) and decorative circles (±7 / ±4 px).
 *
 * The sculpture is bottom-anchored via CSS  bottom:0; transform:translateX(-50%).
 * JS preserves the X-centering while adding parallax on both axes.
 *
 * Respects prefers-reduced-motion.
 */

export function initCursorLight() {
  if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) return;
  if ( window.matchMedia( '(hover: none), (pointer: coarse)' ).matches ) return;

  const hero = document.querySelector( '.luma-hero' );
  if ( !hero ) return;

  const objectWrap = hero.querySelector( '.js-hero-object' );
  const circles    = Array.from( hero.querySelectorAll( '.js-hero-circle' ) );

  if ( !objectWrap && !circles.length ) return;

  let targetX = 0, targetY = 0;
  let currentX = 0, currentY = 0;
  let rafId = null;

  function lerp( a, b, t ) { return a + ( b - a ) * t; }

  function tick() {
    currentX = lerp( currentX, targetX, 0.055 );
    currentY = lerp( currentY, targetY, 0.055 );

    const ox = currentX * 12; // ±12 px on sculpture
    const oy = currentY *  8;

    // Sculpture: keep translateX(-50%) centering, add parallax offsets
    if ( objectWrap ) {
      objectWrap.style.transform =
        `translate( calc(-50% + ${ ox }px), ${ oy }px )`;
    }

    // Circles: lighter offset — feel "farther away"
    circles.forEach( ( c, i ) => {
      const f = i === 0 ? 0.55 : 0.35;
      c.style.transform = `translate( ${ ox * f }px, ${ oy * f }px )`;
    } );

    rafId = requestAnimationFrame( tick );
  }

  // Normalise cursor to −1…+1 on each axis
  hero.addEventListener( 'mousemove', ( e ) => {
    const r = hero.getBoundingClientRect();
    targetX = ( ( e.clientX - r.left  ) / r.width  - 0.5 ) * 2;
    targetY = ( ( e.clientY - r.top   ) / r.height - 0.5 ) * 2;
  } );

  hero.addEventListener( 'mouseleave', () => { targetX = 0; targetY = 0; } );

  // Only animate while hero is visible
  const io = new IntersectionObserver( ( entries ) => {
    entries.forEach( ( e ) => {
      if ( e.isIntersecting ) {
        if ( !rafId ) rafId = requestAnimationFrame( tick );
      } else {
        if ( rafId ) { cancelAnimationFrame( rafId ); rafId = null; }
      }
    } );
  }, { threshold: 0.1 } );

  io.observe( hero );
}
