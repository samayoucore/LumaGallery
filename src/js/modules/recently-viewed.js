/**
 * Recently Viewed — records artworks the visitor opens (image link, title link
 * or quick-view) into localStorage so the /account/ dashboard can show them.
 * Newest first, de-duplicated, capped. No-ops when no artwork cards exist.
 */

const RECENTLY_VIEWED_STORAGE_KEY = 'luma_recently_viewed';
const RECENTLY_VIEWED_CONFIG = { max: 12 };
const RECENTLY_VIEWED_SELECTORS = {
  card:    '.luma-artwork-card',
  trigger: '.luma-artwork-card__image-link, .luma-artwork-card__title a, .js-quick-view',
};

function lumaRecentlyViewedRead() {
  try {
    const data = JSON.parse( localStorage.getItem( RECENTLY_VIEWED_STORAGE_KEY ) );
    return Array.isArray( data ) ? data : [];
  } catch { return []; }
}

function rvBg( el ) { return el && el.style ? el.style.background : ''; }
function rvText( el ) { return el ? el.textContent.trim() : ''; }
function rvAttr( el, name ) { return el ? ( el.getAttribute( name ) || '' ) : ''; }

function rvFromCard( card ) {
  const id = card.dataset.artworkId || '';
  if ( !id ) return null;
  return {
    id:       String( id ),
    title:    rvText( card.querySelector( '.luma-artwork-card__title' ) ) || card.dataset.title || 'Untitled',
    artist:   card.dataset.artist || rvText( card.querySelector( '.luma-artwork-card__artist' ) ) || '',
    price:    parseFloat( card.dataset.price ) || 0,
    currency: ( window.lumaData && window.lumaData.currency ) || '€',
    image:    rvAttr( card.querySelector( '.luma-artwork-card__image' ), 'src' ),
    gradient: rvBg( card.querySelector( '.luma-artwork-card__placeholder' ) ),
    url:      rvAttr( card.querySelector( '.luma-artwork-card__image-link' ), 'href' ) || rvAttr( card.querySelector( '.luma-artwork-card__title a' ), 'href' ) || '#',
    status:   card.dataset.status || 'available',
    viewedAt: new Date().toISOString(),
  };
}

function rvRecord( item ) {
  if ( !item || !item.id ) return;
  let list = lumaRecentlyViewedRead().filter( ( x ) => x.id !== item.id );
  list.unshift( item );
  if ( list.length > RECENTLY_VIEWED_CONFIG.max ) list = list.slice( 0, RECENTLY_VIEWED_CONFIG.max );
  try {
    localStorage.setItem( RECENTLY_VIEWED_STORAGE_KEY, JSON.stringify( list ) );
  } catch { /* full / disabled */ }
}

export function initRecentlyViewed() {
  // Record a view without blocking the click (synchronous localStorage write
  // completes before any navigation).
  document.addEventListener( 'click', ( e ) => {
    const trigger = e.target.closest( RECENTLY_VIEWED_SELECTORS.trigger );
    if ( !trigger ) return;
    const card = trigger.closest( RECENTLY_VIEWED_SELECTORS.card );
    if ( !card ) return;
    rvRecord( rvFromCard( card ) );
  }, true );

  // Single artwork pages can opt in with a .js-recently-viewed-track element.
  const single = document.querySelector( '.js-recently-viewed-track' );
  if ( single ) {
    const d = single.dataset;
    if ( d.id ) {
      rvRecord( {
        id: String( d.id ), title: d.title || 'Untitled', artist: d.artist || '',
        price: parseFloat( d.price ) || 0, currency: d.currency || '€',
        image: d.image || '', gradient: d.gradient || '', url: d.url || '#',
        status: d.status || 'available', viewedAt: new Date().toISOString(),
      } );
    }
  }
}

export { lumaRecentlyViewedRead };
