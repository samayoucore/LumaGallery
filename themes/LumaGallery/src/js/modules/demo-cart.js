/**
 * Demo Cart — localStorage-based commerce (no WooCommerce, no real payment).
 *
 * Owns: add-to-cart interception, the header count badge, and rendering the
 * /cart/ page. Shared helpers (lumaCart*) are reused by checkout.js (same
 * concatenated bundle). Safe on every page: bails when the relevant DOM/role
 * is absent and never throws.
 */

const DEMO_CART_STORAGE_KEY = 'luma_demo_cart';
const DEMO_CART_SHIPPING_ESTIMATE = 25; // default shipping shown on the cart page

const DEMO_CART_SELECTORS = {
  addBtn:      '.js-add-to-cart',
  count:       '.js-cart-count',
  page:        '.js-cart-page',
  layout:      '.js-cart-layout',
  items:       '.js-cart-items',
  summary:     '.js-cart-summary',
  empty:       '.js-cart-empty',
  remove:      '.js-cart-remove',
};

// ─── Shared storage helpers (also used by checkout.js) ────────────────────────

function lumaCartRead() {
  try {
    const data = JSON.parse( localStorage.getItem( DEMO_CART_STORAGE_KEY ) );
    return Array.isArray( data ) ? data : [];
  } catch { return []; }
}

function lumaCartWrite( cart ) {
  try { localStorage.setItem( DEMO_CART_STORAGE_KEY, JSON.stringify( cart ) ); } catch { /* full/disabled */ }
}

function lumaCartClear() {
  try { localStorage.removeItem( DEMO_CART_STORAGE_KEY ); } catch { /* noop */ }
  lumaCartUpdateBadges();
  document.dispatchEvent( new CustomEvent( 'luma:cart:changed' ) );
}

function lumaCartCount() { return lumaCartRead().length; } // unique originals

function lumaCartSubtotal( cart ) {
  return ( cart || lumaCartRead() ).reduce( ( sum, it ) => sum + ( parseFloat( it.price ) || 0 ), 0 );
}

function lumaCartMoney( n, currency ) {
  const cur = currency || ( window.lumaData && window.lumaData.currency ) || '€';
  const v = Math.round( ( parseFloat( n ) || 0 ) ).toString().replace( /\B(?=(\d{3})+(?!\d))/g, ' ' );
  return cur + ' ' + v;
}

function lumaCartUpdateBadges() {
  const count = lumaCartCount();
  document.querySelectorAll( DEMO_CART_SELECTORS.count ).forEach( ( el ) => {
    el.textContent = count;
    el.style.display = count > 0 ? '' : 'none';
  } );
}

function lumaCartEsc( str ) {
  return String( str == null ? '' : str )
    .replace( /&/g, '&amp;' ).replace( /</g, '&lt;' ).replace( />/g, '&gt;' )
    .replace( /"/g, '&quot;' ).replace( /'/g, '&#39;' );
}

// ─── Add / remove ─────────────────────────────────────────────────────────────

function dcInCart( id ) { return lumaCartRead().some( ( it ) => it.id === id ); }

function dcItemFromButton( btn ) {
  const d = btn.dataset;
  const card = btn.closest( '[data-artwork-id], .luma-artwork-card' );
  const id = d.cartId || d.artworkId || ( card && card.dataset.artworkId ) || ( 'art-' + Date.now() );
  return {
    id:        String( id ),
    title:     d.title     || ( card && card.dataset.title )   || 'Untitled',
    artist:    d.artist    || ( card && card.dataset.artist )  || '',
    price:     parseFloat( d.price ) || parseFloat( card && card.dataset.price ) || 0,
    currency:  d.currency  || ( window.lumaData && window.lumaData.currency ) || '€',
    image:     d.image     || '',
    gradient:  d.gradient  || '',
    url:       d.url       || '#',
    status:    d.status    || ( card && card.dataset.status ) || 'available',
    size:      d.size      || '',
    technique: d.technique || '',
    quantity:  1,
  };
}

function dcAdd( btn ) {
  const item = dcItemFromButton( btn );

  if ( item.status === 'sold' || item.status === 'reserved' ) {
    return 'unavailable';
  }
  if ( dcInCart( item.id ) ) {
    return 'exists';
  }

  const cart = lumaCartRead();
  cart.push( item );
  lumaCartWrite( cart );
  lumaCartUpdateBadges();
  document.dispatchEvent( new CustomEvent( 'luma:cart:changed' ) );
  return 'added';
}

function dcRemove( id ) {
  lumaCartWrite( lumaCartRead().filter( ( it ) => it.id !== id ) );
  lumaCartUpdateBadges();
  document.dispatchEvent( new CustomEvent( 'luma:cart:changed' ) );
}

// ─── Feedback (button label + aria-live toast) ────────────────────────────────

let dcToastEl = null;
let dcToastTimer = null;

function dcToast( message ) {
  if ( !dcToastEl ) {
    dcToastEl = document.createElement( 'div' );
    dcToastEl.className = 'luma-cart-toast';
    dcToastEl.setAttribute( 'role', 'status' );
    dcToastEl.setAttribute( 'aria-live', 'polite' );
    document.body.appendChild( dcToastEl );
  }
  dcToastEl.textContent = message;
  dcToastEl.classList.add( 'is-visible' );
  clearTimeout( dcToastTimer );
  dcToastTimer = setTimeout( () => dcToastEl.classList.remove( 'is-visible' ), 2200 );
}

function dcButtonFeedback( btn, label, ms ) {
  if ( btn.dataset.dcBusy ) return;
  btn.dataset.dcBusy = '1';
  const original = btn.dataset.dcLabel || btn.textContent;
  btn.dataset.dcLabel = original;
  btn.textContent = label;
  btn.classList.add( 'is-added' );
  setTimeout( () => {
    btn.textContent = btn.dataset.dcLabel;
    btn.classList.remove( 'is-added' );
    delete btn.dataset.dcBusy;
  }, ms || 1600 );
}

// ─── Cart page render ─────────────────────────────────────────────────────────

function dcRenderItem( it ) {
  const url = lumaCartEsc( it.url || '#' );
  const media = it.image
    ? `<img src="${ lumaCartEsc( it.image ) }" alt="${ lumaCartEsc( it.title ) }">`
    : `<span class="luma-cart__item-fallback" style="background:${ lumaCartEsc( it.gradient || '#26262a' ) };" aria-hidden="true"></span>`;
  const meta = [ it.technique, it.size ].filter( Boolean ).map( lumaCartEsc ).join( ' · ' );

  return `
    <article class="luma-cart__item" data-cart-id="${ lumaCartEsc( it.id ) }">
      <a class="luma-cart__item-media" href="${ url }" tabindex="-1">${ media }</a>
      <div class="luma-cart__item-info">
        ${ it.artist ? `<span class="luma-cart__item-artist">${ lumaCartEsc( it.artist ) }</span>` : '' }
        <h3 class="luma-cart__item-title"><a href="${ url }">${ lumaCartEsc( it.title ) }</a></h3>
        ${ meta ? `<p class="luma-cart__item-meta">${ meta }</p>` : '' }
        <span class="luma-cart__item-tag">Original artwork · Qty 1</span>
      </div>
      <div class="luma-cart__item-side">
        <span class="luma-cart__item-price">${ lumaCartMoney( it.price, it.currency ) }</span>
        <button type="button" class="luma-cart__remove js-cart-remove" data-cart-id="${ lumaCartEsc( it.id ) }" aria-label="Remove ${ lumaCartEsc( it.title ) } from cart">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          <span>Remove</span>
        </button>
      </div>
    </article>`;
}

function dcRenderSummary( cart ) {
  const subtotal = lumaCartSubtotal( cart );
  const shipping = DEMO_CART_SHIPPING_ESTIMATE;
  const total = subtotal + shipping;
  const cur = ( cart[0] && cart[0].currency ) || '€';
  const checkoutUrl = ( window.lumaData && window.lumaData.siteUrl ? window.lumaData.siteUrl : '' ) + '/checkout/';
  const galleryUrl  = ( window.lumaData && window.lumaData.siteUrl ? window.lumaData.siteUrl : '' ) + '/gallery/';

  return `
    <div class="luma-cart__summary-inner">
      <p class="luma-cart__summary-eyebrow">Order Summary</p>
      <h2 class="luma-cart__summary-title">${ cart.length } artwork${ cart.length !== 1 ? 's' : '' }</h2>
      <dl class="luma-cart__summary-rows">
        <div class="luma-cart__summary-row"><dt>Subtotal</dt><dd>${ lumaCartMoney( subtotal, cur ) }</dd></div>
        <div class="luma-cart__summary-row"><dt>Shipping <span>(est.)</span></dt><dd>${ lumaCartMoney( shipping, cur ) }</dd></div>
        <div class="luma-cart__summary-row luma-cart__summary-row--total"><dt>Total</dt><dd>${ lumaCartMoney( total, cur ) }</dd></div>
      </dl>
      <p class="luma-cart__summary-note">Shipping is estimated for demo purposes.</p>
      <a class="luma-button luma-button--accent luma-cart__summary-cta" href="${ lumaCartEsc( checkoutUrl ) }">Proceed to Checkout</a>
      <a class="luma-cart__summary-link" href="${ lumaCartEsc( galleryUrl ) }">Continue Exploring</a>
    </div>`;
}

function dcRenderPage() {
  const page = document.querySelector( DEMO_CART_SELECTORS.page );
  if ( !page ) return;

  const cart    = lumaCartRead();
  const layout  = page.querySelector( DEMO_CART_SELECTORS.layout );
  const itemsEl = page.querySelector( DEMO_CART_SELECTORS.items );
  const sumEl   = page.querySelector( DEMO_CART_SELECTORS.summary );
  const emptyEl = page.querySelector( DEMO_CART_SELECTORS.empty );

  if ( !cart.length ) {
    if ( layout ) layout.hidden = true;
    if ( emptyEl ) emptyEl.hidden = false;
    return;
  }

  if ( emptyEl ) emptyEl.hidden = true;
  if ( layout ) layout.hidden = false;
  if ( itemsEl ) itemsEl.innerHTML = cart.map( dcRenderItem ).join( '' );
  if ( sumEl ) sumEl.innerHTML = dcRenderSummary( cart );
}

// ─── Boot ─────────────────────────────────────────────────────────────────────

export function initDemoCart() {
  const lumaData = window.lumaData || {};
  const wooActive = lumaData.isWooActive === 'yes';

  lumaCartUpdateBadges();
  dcRenderPage();

  // Add-to-cart interception (demo mode only; let WooCommerce handle its own).
  if ( !wooActive ) {
    document.addEventListener( 'click', ( e ) => {
      const btn = e.target.closest( DEMO_CART_SELECTORS.addBtn );
      if ( !btn ) return;
      e.preventDefault();

      const result = dcAdd( btn );
      if ( result === 'added' ) {
        dcButtonFeedback( btn, 'Added' );
        dcToast( 'Added to cart' );
      } else if ( result === 'exists' ) {
        dcButtonFeedback( btn, 'Already in cart' );
        dcToast( 'Already in cart' );
      } else {
        dcToast( 'This artwork is not available' );
      }
    } );
  }

  // Remove from the cart page.
  document.addEventListener( 'click', ( e ) => {
    const btn = e.target.closest( DEMO_CART_SELECTORS.remove );
    if ( !btn ) return;
    e.preventDefault();
    const id = btn.dataset.cartId;
    const item = btn.closest( '.luma-cart__item' );
    if ( item ) {
      item.classList.add( 'is-removing' );
      setTimeout( () => { dcRemove( id ); dcRenderPage(); }, 220 );
    } else {
      dcRemove( id );
      dcRenderPage();
    }
  } );

  // Re-render the cart page whenever the cart changes elsewhere.
  document.addEventListener( 'luma:cart:changed', () => {
    if ( document.querySelector( DEMO_CART_SELECTORS.page ) ) dcRenderPage();
  } );
}

// Public exports for checkout.js (same bundle scope after concat).
export { lumaCartRead, lumaCartWrite, lumaCartClear, lumaCartCount, lumaCartSubtotal, lumaCartMoney, lumaCartUpdateBadges, lumaCartEsc };
