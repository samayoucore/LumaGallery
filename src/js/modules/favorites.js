/**
 * Favorites — localStorage-based favorites for artworks, artists, exhibitions.
 * Structure: { artworks: [id,...], artists: [id,...], exhibitions: [id,...] }
 */

const STORAGE_KEY = 'luma_favorites';

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

function updateCountBadge() {
  const badges = document.querySelectorAll( '.js-favorites-count' );
  const count = getCount();
  badges.forEach( ( el ) => {
    el.textContent = count;
    el.style.display = count > 0 ? '' : 'none';
  } );
}

function syncButtonStates() {
  document.querySelectorAll( '.js-toggle-favorite' ).forEach( ( btn ) => {
    const artworkId = btn.dataset.artworkId;
    const artistId  = btn.dataset.artistId;
    const type      = btn.dataset.type;
    let active = false;

    if ( type === 'artwork' && artworkId ) {
      active = isInFavorites( 'artwork', artworkId );
    } else if ( type === 'artist' && artistId ) {
      active = isInFavorites( 'artist', artistId );
    }

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

    const type      = btn.dataset.type || 'artwork';
    const artworkId = btn.dataset.artworkId;
    const artistId  = btn.dataset.artistId;
    const id        = artworkId || artistId;

    if ( !id ) return;

    const added = toggle( type, id );
    btn.classList.toggle( 'is-active', added );
    btn.setAttribute( 'aria-pressed', added ? 'true' : 'false' );

    // Micro-animation
    btn.classList.add( 'is-animating' );
    btn.addEventListener( 'animationend', () => btn.classList.remove( 'is-animating' ), { once: true } );

    updateCountBadge();

    // Dispatch custom event for other modules to react
    document.dispatchEvent( new CustomEvent( 'luma:favorites:changed', {
      detail: { type, id, added }
    } ) );
  } );
}

// Public API
export const Favorites = { getList, toggle, isInFavorites, getCount, load };
