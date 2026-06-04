/**
 * Gallery Walk — fullscreen immersive artwork viewer.
 * Reads artworks from data attributes on .luma-gallery-walk-section__frame elements
 * or from a passed array. Renders them fullscreen with prev/next navigation,
 * spotlight effect, and thumbnail strip.
 */

import { openModal, closeModal } from './modals.js';

let artworks   = [];
let currentIdx = 0;
let overlay    = null;

function buildArtworkList() {
  const frames = document.querySelectorAll( '.luma-gallery-walk-section__frame' );
  const list = [];

  frames.forEach( ( frame ) => {
    const img    = frame.querySelector( 'img' );
    const label  = frame.querySelector( '.luma-gallery-walk-section__frame-label span' );
    const bg     = frame.querySelector( '.luma-gallery-walk-section__frame-inner' );
    list.push( {
      id:        frame.dataset.artworkId || '0',
      title:     label?.textContent?.trim() || '',
      artist:    frame.dataset.artist || '',
      price:     frame.dataset.price  || '',
      url:       frame.dataset.url    || '#',
      thumbnail: img?.src || '',
      gradient:  bg ? bg.style.background : '',
    } );
  } );

  return list;
}

function renderWall() {
  const wall = overlay.querySelector( '.js-gw-wall' );
  if ( !wall ) return;
  wall.innerHTML = '';

  artworks.forEach( ( art, i ) => {
    const frame = document.createElement( 'div' );
    frame.className = 'luma-gallery-walk__active-frame' + ( i === currentIdx ? ' is-active' : '' );
    frame.dataset.index = i;
    frame.style.display = i === currentIdx ? 'block' : 'none';

    if ( art.thumbnail ) {
      const img = document.createElement( 'img' );
      img.src = art.thumbnail;
      img.alt = art.title;
      frame.appendChild( img );
    } else {
      const placeholder = document.createElement( 'div' );
      placeholder.className = 'gw-placeholder';
      placeholder.style.background = art.gradient || 'linear-gradient(135deg,#2C2C2A,#4A4845)';
      frame.appendChild( placeholder );
    }

    wall.appendChild( frame );
  } );
}

function renderThumbs() {
  const strip = overlay.querySelector( '.js-gw-thumbs' );
  if ( !strip ) return;
  strip.innerHTML = '';

  artworks.forEach( ( art, i ) => {
    const thumb = document.createElement( 'div' );
    thumb.className = 'luma-gallery-walk__thumb' + ( i === currentIdx ? ' is-active' : '' );
    thumb.dataset.index = i;
    thumb.setAttribute( 'role', 'button' );
    thumb.setAttribute( 'aria-label', art.title || `Artwork ${ i + 1 }` );
    thumb.tabIndex = 0;

    if ( art.thumbnail ) {
      const img = document.createElement( 'img' );
      img.src = art.thumbnail;
      img.alt = art.title;
      thumb.appendChild( img );
    } else {
      thumb.style.background = art.gradient || 'linear-gradient(135deg,#2C2C2A,#4A4845)';
    }

    strip.appendChild( thumb );
  } );
}

function updateInfo() {
  const art = artworks[ currentIdx ];
  if ( !art ) return;

  const setEl = ( sel, val ) => {
    const el = overlay.querySelector( sel );
    if ( el ) el.textContent = val;
  };

  setEl( '.js-gw-title',   art.title  || '' );
  setEl( '.js-gw-artist',  art.artist || '' );
  setEl( '.js-gw-price',   art.price  || '' );
  setEl( '.js-gw-current', currentIdx + 1   );
  setEl( '.js-gw-total',   artworks.length  );

  const viewLink = overlay.querySelector( '.js-gw-view-link' );
  if ( viewLink ) viewLink.href = art.url || '#';

  // Favorite button
  const favBtn = overlay.querySelector( '.js-gw-favorite' );
  if ( favBtn ) {
    favBtn.dataset.artworkId = art.id;
    favBtn.dataset.type      = 'artwork';
  }

  // Update spotlight position (subtle random shift)
  const x = 40 + Math.random() * 20;
  const y = 35 + Math.random() * 15;
  overlay.style.setProperty( '--gw-spotlight-x', x + '%' );
  overlay.style.setProperty( '--gw-spotlight-y', y + '%' );
}

function goTo( index ) {
  const prev = overlay.querySelectorAll( '.luma-gallery-walk__active-frame' );
  prev.forEach( ( el ) => { el.style.display = 'none'; } );

  const thumbs = overlay.querySelectorAll( '.luma-gallery-walk__thumb' );
  thumbs.forEach( ( t ) => t.classList.remove( 'is-active' ) );

  currentIdx = ( index + artworks.length ) % artworks.length;

  const nextFrame = overlay.querySelector( `[data-index="${ currentIdx }"]` );
  if ( nextFrame ) nextFrame.style.display = 'block';

  const activeThumb = overlay.querySelector( `.luma-gallery-walk__thumb[data-index="${ currentIdx }"]` );
  if ( activeThumb ) {
    activeThumb.classList.add( 'is-active' );
    activeThumb.scrollIntoView( { inline: 'center', behavior: 'smooth', block: 'nearest' } );
  }

  updateInfo();
}

function handleKeydown( e ) {
  if ( !overlay || overlay.getAttribute( 'aria-hidden' ) !== 'false' ) return;
  if ( e.key === 'ArrowRight' ) { e.preventDefault(); goTo( currentIdx + 1 ); }
  if ( e.key === 'ArrowLeft'  ) { e.preventDefault(); goTo( currentIdx - 1 ); }
  if ( e.key === 'Escape'     ) { e.preventDefault(); close(); }
}

function open( startIndex = 0 ) {
  artworks   = buildArtworkList();
  currentIdx = startIndex;

  if ( artworks.length === 0 ) return;

  overlay = document.querySelector( '.js-gallery-walk' );
  if ( !overlay ) return;

  renderWall();
  renderThumbs();
  updateInfo();

  overlay.setAttribute( 'aria-hidden', 'false' );
  overlay.classList.add( 'is-open' );
  document.body.style.overflow = 'hidden';
  document.addEventListener( 'keydown', handleKeydown );

  // Focus close button
  const closeBtn = overlay.querySelector( '.js-gw-close' );
  if ( closeBtn ) setTimeout( () => closeBtn.focus(), 60 );
}

function close() {
  if ( !overlay ) return;
  overlay.setAttribute( 'aria-hidden', 'true' );
  overlay.classList.remove( 'is-open' );
  document.body.style.overflow = '';
  document.removeEventListener( 'keydown', handleKeydown );
}

export function initGalleryWalk() {
  // Open on "Start Gallery Walk" button
  document.addEventListener( 'click', ( e ) => {
    const btn = e.target.closest( '.js-start-gallery-walk' );
    if ( btn ) { e.preventDefault(); open( 0 ); }
  } );

  // Open on frame click
  document.addEventListener( 'click', ( e ) => {
    const frame = e.target.closest( '.luma-gallery-walk-section__frame' );
    if ( frame ) {
      const idx = parseInt( frame.dataset.index, 10 ) || 0;
      open( idx );
    }
  } );

  // Delegated events inside overlay (bound after overlay is queried)
  document.addEventListener( 'click', ( e ) => {
    // Prev / Next
    if ( e.target.closest( '.js-gw-prev' ) ) { goTo( currentIdx - 1 ); return; }
    if ( e.target.closest( '.js-gw-next' ) ) { goTo( currentIdx + 1 ); return; }

    // Close
    if ( e.target.closest( '.js-gw-close' ) ) { close(); return; }

    // Thumbnail click
    const thumb = e.target.closest( '.luma-gallery-walk__thumb' );
    if ( thumb ) {
      const idx = parseInt( thumb.dataset.index, 10 );
      if ( !isNaN( idx ) ) goTo( idx );
    }
  } );

  // Drag-scroll the homepage strip
  const strip = document.querySelector( '.js-gallery-strip' );
  if ( strip ) {
    let isDown = false;
    let startX = 0;
    let scrollLeft = 0;

    strip.addEventListener( 'mousedown',  ( e ) => {
      isDown = true;
      strip.classList.add( 'is-dragging' );
      startX = e.pageX - strip.offsetLeft;
      scrollLeft = strip.scrollLeft;
    } );

    document.addEventListener( 'mouseup',   () => { isDown = false; strip.classList.remove( 'is-dragging' ); } );
    document.addEventListener( 'mousemove', ( e ) => {
      if ( !isDown ) return;
      e.preventDefault();
      const x    = e.pageX - strip.offsetLeft;
      const walk = ( x - startX ) * 1.6;
      strip.scrollLeft = scrollLeft - walk;
    } );
  }

  // Keyboard nav on thumbs
  document.addEventListener( 'keydown', ( e ) => {
    if ( e.key !== 'Enter' && e.key !== ' ' ) return;
    const thumb = document.activeElement?.closest( '.luma-gallery-walk__thumb' );
    if ( thumb ) {
      e.preventDefault();
      const idx = parseInt( thumb.dataset.index, 10 );
      if ( !isNaN( idx ) ) goTo( idx );
    }
  } );
}
