/**
 * Demo Checkout — validates a simulated order, persists it to localStorage and
 * shows an in-page success state. No real payment. Reuses the lumaCart* helpers
 * from demo-cart.js (same concatenated bundle). No-ops off the checkout page.
 */

const CHECKOUT_STORAGE_KEY = 'luma_demo_orders';

const CHECKOUT_SELECTORS = {
  page:    '.js-checkout-page',
  main:    '.js-checkout-main',
  form:    '.js-checkout-form',
  summary: '.js-checkout-summary',
  success: '.js-checkout-success',
  empty:   '.js-checkout-empty',
};

const CHECKOUT_DELIVERY = {
  standard: { label: 'Standard Art Shipping',    price: 25 },
  premium:  { label: 'Premium Insured Shipping', price: 45 },
  pickup:   { label: 'Local Gallery Pickup',     price: 0 },
};

const CHECKOUT_REQUIRED = [ 'name', 'email', 'country', 'city', 'street', 'postal' ];

// ─── Helpers ──────────────────────────────────────────────────────────────────

function coShipping( form ) {
  const checked = form.querySelector( 'input[name="delivery"]:checked' );
  const key = checked ? checked.value : 'standard';
  return ( CHECKOUT_DELIVERY[ key ] || CHECKOUT_DELIVERY.standard ).price;
}

function coOrderNumber() {
  let orders = [];
  try { orders = JSON.parse( localStorage.getItem( CHECKOUT_STORAGE_KEY ) ) || []; } catch { orders = []; }
  const seq = String( orders.length + 1 ).padStart( 4, '0' );
  return { id: 'LUMA-' + new Date().getFullYear() + '-' + seq, orders };
}

// ─── Summary ──────────────────────────────────────────────────────────────────

function coRenderSummary( cart, shipping ) {
  const el = document.querySelector( CHECKOUT_SELECTORS.summary );
  if ( !el ) return;

  const subtotal = lumaCartSubtotal( cart );
  const total = subtotal + shipping;
  const cur = ( cart[0] && cart[0].currency ) || '€';

  const items = cart.map( ( it ) => `
    <li class="luma-checkout__sum-item">
      <span class="luma-checkout__sum-item-name">${ lumaCartEsc( it.title ) }<small>${ lumaCartEsc( it.artist ) }</small></span>
      <span class="luma-checkout__sum-item-price">${ lumaCartMoney( it.price, cur ) }</span>
    </li>` ).join( '' );

  el.innerHTML = `
    <div class="luma-checkout__summary-inner">
      <p class="luma-checkout__summary-eyebrow">Order Summary</p>
      <ul class="luma-checkout__sum-items">${ items }</ul>
      <dl class="luma-checkout__sum-rows">
        <div class="luma-checkout__sum-row"><dt>Subtotal</dt><dd>${ lumaCartMoney( subtotal, cur ) }</dd></div>
        <div class="luma-checkout__sum-row"><dt>Delivery</dt><dd>${ shipping ? lumaCartMoney( shipping, cur ) : 'Free' }</dd></div>
        <div class="luma-checkout__sum-row luma-checkout__sum-row--total"><dt>Total</dt><dd>${ lumaCartMoney( total, cur ) }</dd></div>
      </dl>
      <p class="luma-checkout__summary-note">Demo order — no real payment is processed.</p>
    </div>`;
}

// ─── Validation ───────────────────────────────────────────────────────────────

function coFieldError( form, name, message ) {
  const input = form.querySelector( `[name="${ name }"]` );
  if ( !input ) return;
  const field = input.closest( '.luma-checkout__field' ) || input.parentElement;
  field.classList.toggle( 'has-error', !!message );
  input.setAttribute( 'aria-invalid', message ? 'true' : 'false' );
  let err = field.querySelector( '.luma-checkout__error' );
  if ( message ) {
    if ( !err ) {
      err = document.createElement( 'p' );
      err.className = 'luma-checkout__error';
      field.appendChild( err );
    }
    err.textContent = message;
  } else if ( err ) {
    err.textContent = '';
  }
}

function coValidate( form ) {
  let firstInvalid = null;

  CHECKOUT_REQUIRED.forEach( ( name ) => {
    const input = form.querySelector( `[name="${ name }"]` );
    const value = input ? input.value.trim() : '';
    let msg = '';
    if ( !value ) {
      msg = 'This field is required.';
    } else if ( name === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test( value ) ) {
      msg = 'Enter a valid email address.';
    }
    coFieldError( form, name, msg );
    if ( msg && !firstInvalid ) firstInvalid = input;
  } );

  if ( !form.querySelector( 'input[name="delivery"]:checked' ) ) {
    const group = form.querySelector( '.js-delivery-group' );
    if ( group ) group.classList.add( 'has-error' );
    if ( !firstInvalid ) firstInvalid = form.querySelector( 'input[name="delivery"]' );
  }

  if ( firstInvalid ) {
    firstInvalid.focus();
    return false;
  }
  return true;
}

// ─── Success ──────────────────────────────────────────────────────────────────

function coShowSuccess( order ) {
  const main = document.querySelector( CHECKOUT_SELECTORS.main );
  const success = document.querySelector( CHECKOUT_SELECTORS.success );
  if ( main ) main.hidden = true;
  if ( !success ) return;

  const cur = ( order.items[0] && order.items[0].currency ) || '€';
  const date = new Date( order.createdAt ).toLocaleDateString( undefined, { year: 'numeric', month: 'long', day: 'numeric' } );
  const deliveryLabel = ( CHECKOUT_DELIVERY[ order.shipping.deliveryMethod ] || {} ).label || order.shipping.deliveryMethod;

  const set = ( sel, val ) => { const e = success.querySelector( sel ); if ( e ) e.textContent = val; };
  set( '.js-success-number', order.id );
  set( '.js-success-date', date );
  set( '.js-success-name', order.customer.name );
  set( '.js-success-email', order.customer.email );
  set( '.js-success-delivery', deliveryLabel );
  set( '.js-success-total', lumaCartMoney( order.totals.total, cur ) );

  success.hidden = false;
  requestAnimationFrame( () => success.classList.add( 'is-visible' ) );
  success.setAttribute( 'tabindex', '-1' );
  success.focus();
  window.scrollTo( { top: 0, behavior: 'smooth' } );
}

// ─── Order creation ───────────────────────────────────────────────────────────

function coCreateOrder( form, cart ) {
  const shipping = coShipping( form );
  const subtotal = lumaCartSubtotal( cart );
  const deliveryKey = ( form.querySelector( 'input[name="delivery"]:checked' ) || {} ).value || 'standard';
  const get = ( n ) => { const i = form.querySelector( `[name="${ n }"]` ); return i ? i.value.trim() : ''; };
  const { id, orders } = coOrderNumber();

  const order = {
    id,
    createdAt: new Date().toISOString(),
    customer: { name: get( 'name' ), email: get( 'email' ), phone: get( 'phone' ) },
    shipping: { country: get( 'country' ), city: get( 'city' ), street: get( 'street' ), postalCode: get( 'postal' ), deliveryMethod: deliveryKey },
    items: cart,
    totals: { subtotal, shipping, total: subtotal + shipping },
    status: 'demo-confirmed',
  };

  orders.push( order );
  try { localStorage.setItem( CHECKOUT_STORAGE_KEY, JSON.stringify( orders ) ); } catch { /* noop */ }

  lumaCartClear(); // empties luma_demo_cart + updates badges
  return order;
}

// ─── Boot ─────────────────────────────────────────────────────────────────────

export function initCheckout() {
  const page = document.querySelector( CHECKOUT_SELECTORS.page );
  if ( !page ) return;

  const cart = lumaCartRead();
  const main = page.querySelector( CHECKOUT_SELECTORS.main );
  const empty = page.querySelector( CHECKOUT_SELECTORS.empty );
  const form = page.querySelector( CHECKOUT_SELECTORS.form );

  if ( !cart.length ) {
    if ( main ) main.hidden = true;
    if ( empty ) empty.hidden = false;
    return;
  }

  coRenderSummary( cart, coShipping( form ) );

  // Live total on delivery change.
  page.querySelectorAll( 'input[name="delivery"]' ).forEach( ( input ) => {
    input.addEventListener( 'change', () => {
      const group = page.querySelector( '.js-delivery-group' );
      if ( group ) group.classList.remove( 'has-error' );
      coRenderSummary( lumaCartRead(), coShipping( form ) );
    } );
  } );

  // Clear field errors as the user types.
  if ( form ) {
    form.querySelectorAll( 'input, textarea, select' ).forEach( ( input ) => {
      input.addEventListener( 'input', () => {
        const field = input.closest( '.luma-checkout__field' );
        if ( field && field.classList.contains( 'has-error' ) ) coFieldError( form, input.name, '' );
      } );
    } );

    form.addEventListener( 'submit', ( e ) => {
      e.preventDefault();
      if ( !coValidate( form ) ) return;
      const current = lumaCartRead();
      if ( !current.length ) return;
      const order = coCreateOrder( form, current );
      coShowSuccess( order );
    } );
  }
}
