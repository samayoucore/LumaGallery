/**
 * Favorites — localStorage-based favorites for artworks, artists, exhibitions.
 *
 * Two stores are kept in sync:
 *   luma_favorites       → { artworks:[id], artists:[id], exhibitions:[id] }  (source of truth)
 *   luma_favorites_meta  → { "type:id": {title,url,gradient,…} }              (render snapshots)
 *
 * The meta store lets the /favorites/ page rebuild rich cards entirely
 * client-side without a backend round-trip. Capturing happens at favorite-time,
 * when the originating card is still in the DOM.
 */

const STORAGE_KEY = 'luma_favorites';
const META_KEY    = 'luma_favorites_meta';

function load() {
  try {
    return JSON.parse( localStorage.getItem( STORAGE_KEY ) ) || { artworks: [], artists: [], exhibitions: [] };
  } catch {
    return { artworks: [], artists: [], exhibitions: [] };
  }
}

function save( data ) {
  try {
    localStorage.setItem( STORAGE_KEY, JSON.stringify( data ) );
  } catch { /* storage full or disabled */ }
}

function loadMeta() {
  try {
    return JSON.parse( localStorage.getItem( META_KEY ) ) || {};
  } catch {
    return {};
  }
}

function saveMeta( data ) {
  try {
    localStorage.setItem( META_KEY, JSON.stringify( data ) );
  } catch { /* storage full or disabled */ }
}

function getList( type ) {
  return load()[ type ] || [];
}

function toggle( type, id ) {
  const data = load();
  const key = type + 's'; // artwork → artworks
  if ( !data[ key ] ) data[ key ] = [];
  const idx = data[ key ].indexOf( String( id ) );
  if ( idx === -1 ) {
    data[ key ].push( String( id ) );
  } else {
    data[ key ].splice( idx, 1 );
  }
  save( data );
  return idx === -1; // true = added
}

function isInFavorites( type, id ) {
  return getList( type + 's' ).includes( String( id ) );
}

function getCount() {
  const data = load();
  return ( data.artworks?.length || 0 ) + ( data.artists?.length || 0 ) + ( data.exhibitions?.length || 0 );
}

// ─── Snapshot capture (DOM → meta store) ──────────────────────────────────────

function favText( el ) { return el ? el.textContent.trim() : ''; }
function favAttr( el, name ) { return el ? ( el.getAttribute( name ) || '' ) : ''; }
function favBg( el ) { return el && el.style ? el.style.background : ''; }

function metaKeyFor( type, id ) { return type + ':' + String( id ); }

function captureCardMeta( type, id, btn ) {
  const meta = loadMeta();
  const snap = { type, id: String( id ), addedAt: Date.now() };

  if ( type === 'artwork' ) {
    const card = btn.closest( '.luma-artwork-card' );
    if ( card ) {
      snap.title    = favText( card.querySelector( '.luma-artwork-card__title' ) );
      snap.subtitle = card.dataset.artist || favText( card.querySelector( '.luma-artwork-card__artist' ) );
      snap.price    = favText( card.querySelector( '.luma-artwork-card__price' ) );
      snap.url      = favAttr( card.querySelector( '.luma-artwork-card__title a' ), 'href' ) || favAttr( card.querySelector( '.luma-artwork-card__image-link' ), 'href' );
      snap.gradient = favBg( card.querySelector( '.luma-artwork-card__placeholder' ) );
      snap.image    = favAttr( card.querySelector( '.luma-artwork-card__image' ), 'src' );
      snap.status   = card.dataset.status || 'available';
    }
  } else if ( type === 'artist' ) {
    const card = btn.closest( '.luma-artist-card' );
    if ( card ) {
      snap.title    = favText( card.querySelector( '.luma-artist-card__name' ) );
      snap.subtitle = favText( card.querySelector( '.luma-artist-card__location' ) );
      snap.url      = favAttr( card.querySelector( '.luma-artist-card__name a' ), 'href' ) || favAttr( card.querySelector( '.luma-artist-card__image-link' ), 'href' );
      snap.gradient = favBg( card.querySelector( '.luma-artist-card__avatar-placeholder' ) );
      snap.image    = favAttr( card.querySelector( '.luma-artist-card__avatar' ), 'src' );
      snap.initial  = favText( card.querySelector( '.luma-artist-card__initial' ) );
    }
  } else if ( type === 'exhibition' ) {
    const card = btn.closest( '.luma-exhibition-card' );
    if ( card ) {
      snap.title    = favText( card.querySelector( '.luma-exhibition-card__title' ) );
      snap.subtitle = favText( card.querySelector( '.luma-exhibition-card__subtitle' ) );
      snap.url      = favAttr( card.querySelector( '.luma-exhibition-card__title a' ), 'href' ) || favAttr( card.querySelector( '.luma-exhibition-card__cover-link' ), 'href' );
      snap.gradient = favBg( card.querySelector( '.luma-exhibition-card__cover-placeholder' ) );
      snap.image    = favAttr( card.querySelector( '.luma-exhibition-card__cover-img' ), 'src' );
    }
  }

  meta[ metaKeyFor( type, id ) ] = snap;
  saveMeta( meta );
}

function removeCardMeta( type, id ) {
  const meta = loadMeta();
  delete meta[ metaKeyFor( type, id ) ];
  saveMeta( meta );
}

/**
 * Return rich, render-ready data for a favorites type, newest first.
 * @param {string} typePlural  'artworks' | 'artists' | 'exhibitions'
 */
function getData( typePlural ) {
  const singular = typePlural.replace( /s$/, '' );
  const meta = loadMeta();
  return getList( typePlural )
    .map( ( id ) => meta[ metaKeyFor( singular, id ) ] || { type: singular, id: String( id ), title: '', url: '#' } )
    .sort( ( a, b ) => ( b.addedAt || 0 ) - ( a.addedAt || 0 ) );
}

/** Programmatic removal that keeps both stores + badges in sync. */
function removeFav( type, id ) {
  const data = load();
  const key  = type + 's';
  if ( data[ key ] ) {
    const idx = data[ key ].indexOf( String( id ) );
    if ( idx !== -1 ) data[ key ].splice( idx, 1 );
  }
  save( data );
  removeCardMeta( type, id );
  updateCountBadge();
  document.dispatchEvent( new CustomEvent( 'luma:favorites:changed', {
    detail: { type, id: String( id ), added: false }
  } ) );
}

// ─── Header badge + button sync ───────────────────────────────────────────────

function updateCountBadge() {
  const badges = document.querySelectorAll( '.js-favorites-count' );
  const count = getCount();
  badges.forEach( ( el ) => {
    el.textContent = count;
    el.style.display = count > 0 ? '' : 'none';
  } );
}

function favButtonId( btn ) {
  return btn.dataset.artworkId || btn.dataset.artistId || btn.dataset.exhibitionId || '';
}

function syncButtonStates() {
  document.querySelectorAll( '.js-toggle-favorite' ).forEach( ( btn ) => {
    const type = btn.dataset.type || 'artwork';
    const id   = favButtonId( btn );
    const active = id ? isInFavorites( type, id ) : false;
    btn.classList.toggle( 'is-active', active );
    btn.setAttribute( 'aria-pressed', active ? 'true' : 'false' );
  } );
}

export function initFavorites() {
  updateCountBadge();
  syncButtonStates();

  document.addEventListener( 'click', ( e ) => {
    const btn = e.target.closest( '.js-toggle-favorite' );
    if ( !btn ) return;
    e.preventDefault();
    e.stopPropagation();

    const type = btn.dataset.type || 'artwork';
    const id   = favButtonId( btn );
    if ( !id ) return;

    const added = toggle( type, id );

    if ( added ) {
      captureCardMeta( type, id, btn );
    } else {
      removeCardMeta( type, id );
    }

    btn.classList.toggle( 'is-active', added );
    btn.setAttribute( 'aria-pressed', added ? 'true' : 'false' );

    // Micro-animation
    btn.classList.add( 'is-animating' );
    btn.addEventListener( 'animationend', () => btn.classList.remove( 'is-animating' ), { once: true } );

    updateCountBadge();

    // Dispatch custom event for other modules (e.g. the favorites page) to react
    document.dispatchEvent( new CustomEvent( 'luma:favorites:changed', {
      detail: { type, id, added }
    } ) );

    // Re-sync every toggle so duplicate references to the same item (e.g. a
    // recommended card and its saved copy) stay in agreement.
    syncButtonStates();
  } );
}

// Public API
export const Favorites = {
  getList, toggle, isInFavorites, getCount, load,
  getData, remove: removeFav, getMeta: loadMeta,
};
