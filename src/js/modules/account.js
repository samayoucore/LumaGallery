/**
 * Account — demo collector dashboard. Renders profile, stats, demo order
 * history, saved artworks, followed artists and recently viewed entirely from
 * localStorage (luma_demo_orders, luma_favorites*, luma_recently_viewed,
 * luma_demo_cart). Reuses Favorites + lumaCart* helpers from the same bundle.
 * No-ops off the account page.
 */

const ACCOUNT_STORAGE_KEYS = {
  cart:   'luma_demo_cart',
  orders: 'luma_demo_orders',
  recent: 'luma_recently_viewed',
};

const ACCOUNT_SELECTORS = {
  page:    '.js-account-page',
  name:    '.js-account-name',
  email:   '.js-account-email',
  avatar:  '.js-account-avatar',
  since:   '.js-account-since',
  stats:   '.js-account-stats',
  orders:  '.js-account-orders',
  saved:   '.js-account-saved',
  artists: '.js-account-artists',
  recent:  '.js-account-recent',
};

const ACCOUNT_CONFIG = { currency: '€' };

// ─── Helpers ──────────────────────────────────────────────────────────────────

function acReadJSON( key ) {
  try { const d = JSON.parse( localStorage.getItem( key ) ); return Array.isArray( d ) ? d : []; }
  catch { return []; }
}

function acEsc( str ) {
  return String( str == null ? '' : str )
    .replace( /&/g, '&amp;' ).replace( /</g, '&lt;' ).replace( />/g, '&gt;' )
    .replace( /"/g, '&quot;' ).replace( /'/g, '&#39;' );
}

function acMoney( n, cur ) {
  if ( typeof lumaCartMoney === 'function' ) return lumaCartMoney( n, cur );
  const c = cur || ACCOUNT_CONFIG.currency;
  return c + ' ' + Math.round( parseFloat( n ) || 0 ).toString().replace( /\B(?=(\d{3})+(?!\d))/g, ' ' );
}

function acFav( typePlural ) {
  return ( typeof Favorites !== 'undefined' && Favorites.getData ) ? Favorites.getData( typePlural ) : [];
}

function acOrders()   { return acReadJSON( ACCOUNT_STORAGE_KEYS.orders ); }
function acRecent()   { return acReadJSON( ACCOUNT_STORAGE_KEYS.recent ); }

function acFmtDate( iso ) {
  try { return new Date( iso ).toLocaleDateString( undefined, { year: 'numeric', month: 'short', day: 'numeric' } ); }
  catch { return ''; }
}

// ─── Profile ──────────────────────────────────────────────────────────────────

function acRenderProfile() {
  const orders = acOrders();
  const latest = orders[ orders.length - 1 ];
  const name = ( latest && latest.customer && latest.customer.name ) ? latest.customer.name : 'Guest Collector';
  const email = ( latest && latest.customer && latest.customer.email ) ? latest.customer.email : 'collector@luma.demo';

  let since = '';
  if ( orders.length ) {
    const first = orders.reduce( ( a, b ) => ( new Date( a.createdAt ) <= new Date( b.createdAt ) ? a : b ) );
    since = new Date( first.createdAt ).getFullYear();
  } else {
    since = new Date().getFullYear();
  }

  const set = ( sel, val ) => { const e = document.querySelector( sel ); if ( e ) e.textContent = val; };
  set( ACCOUNT_SELECTORS.name, name );
  set( ACCOUNT_SELECTORS.email, email );
  set( ACCOUNT_SELECTORS.since, since );
  const avatar = document.querySelector( ACCOUNT_SELECTORS.avatar );
  if ( avatar ) avatar.textContent = ( name.charAt( 0 ) || 'G' ).toUpperCase();
}

// ─── Stats ────────────────────────────────────────────────────────────────────

function acRenderStats() {
  const el = document.querySelector( ACCOUNT_SELECTORS.stats );
  if ( !el ) return;

  const orders = acOrders();
  const spent = orders.reduce( ( s, o ) => s + ( ( o.totals && o.totals.total ) || 0 ), 0 );
  const cur = ( orders[0] && orders[0].items && orders[0].items[0] && orders[0].items[0].currency ) || ACCOUNT_CONFIG.currency;

  const stats = [
    { label: 'Demo Orders',    value: orders.length },
    { label: 'Saved Artworks', value: acFav( 'artworks' ).length },
    { label: 'Following',      value: acFav( 'artists' ).length },
    { label: 'Total Spent',    value: acMoney( spent, cur ) },
  ];

  el.innerHTML = stats.map( ( s, i ) => `
    <div class="luma-account__stat" style="--i:${ i }">
      <span class="luma-account__stat-value">${ acEsc( s.value ) }</span>
      <span class="luma-account__stat-label">${ acEsc( s.label ) }</span>
    </div>` ).join( '' );
}

// ─── Orders ───────────────────────────────────────────────────────────────────

function acBuildOrder( order ) {
  const cur = ( order.items[0] && order.items[0].currency ) || ACCOUNT_CONFIG.currency;
  const items = ( order.items || [] ).map( ( it ) => `
    <li class="luma-account__order-item">
      <span>${ acEsc( it.title ) }<small>${ acEsc( it.artist ) }</small></span>
      <span>${ acMoney( it.price, cur ) }</span>
    </li>` ).join( '' );
  const delivery = order.shipping && order.shipping.deliveryMethod ? order.shipping.deliveryMethod.replace( /(^|\s)\S/g, ( c ) => c.toUpperCase() ) : '';

  return `
    <article class="luma-account__order" data-order-id="${ acEsc( order.id ) }">
      <button type="button" class="luma-account__order-head js-order-toggle" aria-expanded="false">
        <span class="luma-account__order-main">
          <span class="luma-account__order-number">${ acEsc( order.id ) }</span>
          <span class="luma-account__order-date">${ acEsc( acFmtDate( order.createdAt ) ) }</span>
        </span>
        <span class="luma-account__order-aside">
          <span class="luma-account__order-status">Demo confirmed</span>
          <span class="luma-account__order-count">${ order.items.length } item${ order.items.length !== 1 ? 's' : '' }</span>
          <span class="luma-account__order-total">${ acMoney( order.totals && order.totals.total, cur ) }</span>
          <svg class="luma-account__order-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
        </span>
      </button>
      <div class="luma-account__order-detail" hidden>
        <ul class="luma-account__order-items">${ items }</ul>
        <dl class="luma-account__order-info">
          <div><dt>Delivery</dt><dd>${ acEsc( delivery ) }</dd></div>
          <div><dt>Ship to</dt><dd>${ acEsc( [ order.shipping.city, order.shipping.country ].filter( Boolean ).join( ', ' ) ) }</dd></div>
          <div><dt>Subtotal</dt><dd>${ acMoney( order.totals && order.totals.subtotal, cur ) }</dd></div>
          <div><dt>Shipping</dt><dd>${ order.totals && order.totals.shipping ? acMoney( order.totals.shipping, cur ) : 'Free' }</dd></div>
        </dl>
      </div>
    </article>`;
}

function acRenderOrders() {
  const el = document.querySelector( ACCOUNT_SELECTORS.orders );
  if ( !el ) return;
  const orders = acOrders().slice().reverse(); // newest first
  el.innerHTML = orders.length
    ? orders.map( acBuildOrder ).join( '' )
    : acEmpty( 'No demo orders yet.', 'Complete a demo checkout to see your order history here.', '/gallery/', 'Explore Gallery' );
}

// ─── Card builders (reuse art-direction card classes) ────────────────────────

function acMedia( it, cls ) {
  return it.image
    ? `<img class="${ cls }__image" src="${ acEsc( it.image ) }" alt="${ acEsc( it.title ) }">`
    : `<span class="${ cls }__placeholder" style="background:${ acEsc( it.gradient || '#26262a' ) };" aria-hidden="true"></span>`;
}

function acBuildArtwork( it, withFav ) {
  const url = acEsc( it.url || '#' );
  const fav = withFav ? `
    <button type="button" class="luma-artwork-card__favorite js-toggle-favorite is-active" data-artwork-id="${ acEsc( it.id ) }" data-type="artwork" aria-pressed="true" aria-label="Remove from favorites">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
    </button>` : '';
  return `
    <article class="luma-artwork-card" data-artwork-id="${ acEsc( it.id ) }">
      <div class="luma-artwork-card__image-wrap">
        <a class="luma-artwork-card__image-link" href="${ url }" tabindex="-1">${ acMedia( it, 'luma-artwork-card' ) }</a>
        ${ fav }
      </div>
      <div class="luma-artwork-card__body">
        ${ it.subtitle || it.artist ? `<div class="luma-artwork-card__meta"><span class="luma-artwork-card__artist">${ acEsc( it.subtitle || it.artist ) }</span></div>` : '' }
        <h3 class="luma-artwork-card__title"><a href="${ url }">${ acEsc( it.title || 'Untitled' ) }</a></h3>
        ${ it.price ? `<div class="luma-artwork-card__footer"><span class="luma-artwork-card__price">${ typeof it.price === 'number' ? acMoney( it.price, it.currency ) : acEsc( it.price ) }</span></div>` : '' }
      </div>
    </article>`;
}

function acBuildArtist( it ) {
  const url = acEsc( it.url || '#' );
  const media = it.image
    ? `<img class="luma-artist-card__avatar" src="${ acEsc( it.image ) }" alt="${ acEsc( it.title ) }">`
    : `<span class="luma-artist-card__avatar-placeholder" style="background:${ acEsc( it.gradient || '#26262a' ) };"><span class="luma-artist-card__initial">${ acEsc( it.initial || ( it.title || '?' ).charAt( 0 ) ) }</span></span>`;
  return `
    <article class="luma-artist-card" data-artist-id="${ acEsc( it.id ) }">
      <a class="luma-artist-card__image-link" href="${ url }" tabindex="-1"><span class="luma-artist-card__image-wrap">${ media }</span></a>
      <div class="luma-artist-card__body">
        <div class="luma-artist-card__head">
          <h3 class="luma-artist-card__name"><a href="${ url }">${ acEsc( it.title || 'Artist' ) }</a></h3>
          ${ it.subtitle ? `<span class="luma-artist-card__location">${ acEsc( it.subtitle ) }</span>` : '' }
        </div>
        <div class="luma-artist-card__footer">
          <button type="button" class="luma-artist-card__follow js-toggle-favorite is-active" data-artist-id="${ acEsc( it.id ) }" data-type="artist" aria-pressed="true" aria-label="Unfollow artist">
            <span class="luma-artist-card__follow-icon" aria-hidden="true">+</span>
            <span class="luma-artist-card__follow-text">Following</span>
          </button>
        </div>
      </div>
    </article>`;
}

function acEmpty( title, text, url, cta ) {
  const href = ( window.lumaData && window.lumaData.siteUrl ? window.lumaData.siteUrl : '' ) + url;
  return `
    <div class="luma-account__empty">
      <p class="luma-account__empty-title">${ acEsc( title ) }</p>
      <p class="luma-account__empty-text">${ acEsc( text ) }</p>
      <a class="luma-button luma-button--ghost" href="${ acEsc( href ) }">${ acEsc( cta ) }</a>
    </div>`;
}

function acRenderGrid( sel, items, builder, empty ) {
  const el = document.querySelector( sel );
  if ( !el ) return;
  el.classList.toggle( 'is-empty', !items.length );
  el.innerHTML = items.length ? items.map( builder ).join( '' ) : empty;
}

// ─── Render all ───────────────────────────────────────────────────────────────

function acRenderAll() {
  acRenderProfile();
  acRenderStats();
  acRenderOrders();
  acRenderGrid( ACCOUNT_SELECTORS.saved,   acFav( 'artworks' ), ( it ) => acBuildArtwork( it, true ),
    acEmpty( 'No saved artworks yet.', 'Tap the heart on any artwork to save it here.', '/gallery/', 'Explore Gallery' ) );
  acRenderGrid( ACCOUNT_SELECTORS.artists, acFav( 'artists' ), acBuildArtist,
    acEmpty( 'You are not following any artists yet.', 'Follow artists to keep their new work close.', '/artists/', 'Meet Artists' ) );
  acRenderGrid( ACCOUNT_SELECTORS.recent,  acRecent(), ( it ) => acBuildArtwork( it, false ),
    acEmpty( 'Nothing viewed yet.', 'Open an artwork and it will appear here.', '/gallery/', 'Explore Gallery' ) );
}

// ─── Boot ─────────────────────────────────────────────────────────────────────

export function initAccount() {
  if ( !document.querySelector( ACCOUNT_SELECTORS.page ) ) return;

  acRenderAll();

  // Expand / collapse order details.
  document.addEventListener( 'click', ( e ) => {
    const head = e.target.closest( '.js-order-toggle' );
    if ( !head ) return;
    const order = head.closest( '.luma-account__order' );
    const detail = order && order.querySelector( '.luma-account__order-detail' );
    if ( !detail ) return;
    const open = detail.hidden;
    detail.hidden = !open;
    head.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
    order.classList.toggle( 'is-open', open );
  } );

  // Live updates when favorites / cart change elsewhere on the page.
  document.addEventListener( 'luma:favorites:changed', () => {
    acRenderStats();
    acRenderGrid( ACCOUNT_SELECTORS.saved,   acFav( 'artworks' ), ( it ) => acBuildArtwork( it, true ),
      acEmpty( 'No saved artworks yet.', 'Tap the heart on any artwork to save it here.', '/gallery/', 'Explore Gallery' ) );
    acRenderGrid( ACCOUNT_SELECTORS.artists, acFav( 'artists' ), acBuildArtist,
      acEmpty( 'You are not following any artists yet.', 'Follow artists to keep their new work close.', '/artists/', 'Meet Artists' ) );
  } );
}
