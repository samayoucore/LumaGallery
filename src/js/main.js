/**
 * Luma Gallery — main.js
 * Entry point compiled by Gulp into assets/js/main.min.js.
 * Initialises all modules.
 */

import { initCursorLight }  from './modules/cursor-light.js';
import { initFavorites }    from './modules/favorites.js';
import { initModals }       from './modules/modals.js';
import { initGalleryWalk }  from './modules/gallery-walk.js';
import { initViewInRoom }   from './modules/view-in-room.js';
import { initAiAssistant }  from './modules/ai-assistant.js';
import { initAiCurator }    from './modules/ai-curator.js';
import { initFilters }      from './modules/filters.js';

// ─── Header scroll behaviour ──────────────────────────────────────────────────

function initHeader() {
  const header = document.querySelector( '.js-header' );
  if ( !header ) return;

  let ticking = false;

  function onScroll() {
    if ( !ticking ) {
      requestAnimationFrame( () => {
        header.classList.toggle( 'is-solid', window.scrollY > 40 );
        ticking = false;
      } );
      ticking = true;
    }
  }

  window.addEventListener( 'scroll', onScroll, { passive: true } );
  onScroll(); // run once on load
}

// ─── Mobile menu ──────────────────────────────────────────────────────────────

function initMobileMenu() {
  const toggle = document.querySelector( '.js-menu-toggle' );
  const menu   = document.querySelector( '.js-mobile-menu' );
  if ( !toggle || !menu ) return;

  toggle.addEventListener( 'click', () => {
    const open = menu.classList.toggle( 'is-open' );
    menu.setAttribute( 'aria-hidden', open ? 'false' : 'true' );
    toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
    document.body.style.overflow = open ? 'hidden' : '';
  } );

  // Close on nav link click
  menu.querySelectorAll( 'a' ).forEach( link => {
    link.addEventListener( 'click', () => {
      menu.classList.remove( 'is-open' );
      menu.setAttribute( 'aria-hidden', 'true' );
      toggle.setAttribute( 'aria-expanded', 'false' );
      document.body.style.overflow = '';
    } );
  } );
}

// ─── Search overlay ───────────────────────────────────────────────────────────

function initSearch() {
  const toggleBtn  = document.querySelector( '.js-search-toggle' );
  const closeBtn   = document.querySelector( '.js-search-close' );
  const overlay    = document.querySelector( '.js-search-overlay' );
  const input      = overlay?.querySelector( '.luma-header__search-input' );
  if ( !toggleBtn || !overlay ) return;

  function open() {
    overlay.classList.add( 'is-open' );
    overlay.setAttribute( 'aria-hidden', 'false' );
    setTimeout( () => input?.focus(), 80 );
  }

  function close() {
    overlay.classList.remove( 'is-open' );
    overlay.setAttribute( 'aria-hidden', 'true' );
  }

  toggleBtn.addEventListener( 'click', open );
  closeBtn?.addEventListener(  'click', close );
  document.addEventListener(   'keydown', e => { if ( e.key === 'Escape' ) close(); } );
}

// ─── Scroll hint ──────────────────────────────────────────────────────────────

function initScrollHint() {
  const hint = document.querySelector( '.js-scroll-hint' );
  if ( !hint ) return;
  hint.addEventListener( 'click', () => {
    window.scrollBy( { top: window.innerHeight * 0.85, behavior: 'smooth' } );
  } );
}

// ─── Reveal on scroll ─────────────────────────────────────────────────────────

function initReveal() {
  const els = document.querySelectorAll( '.luma-section' );
  if ( !els.length || !window.IntersectionObserver ) return;

  const io = new IntersectionObserver( ( entries ) => {
    entries.forEach( entry => {
      if ( entry.isIntersecting ) {
        entry.target.classList.add( 'is-visible' );
        io.unobserve( entry.target );
      }
    } );
  }, { threshold: 0.08 } );

  els.forEach( el => io.observe( el ) );
}

// ─── Cart count (demo mode without WooCommerce) ───────────────────────────────

function initDemoCart() {
  const lumaData = window.lumaData || {};
  if ( lumaData.isWooActive === 'yes' ) return;

  try {
    const cart = JSON.parse( localStorage.getItem( 'luma_demo_cart' ) ) || [];
    const badges = document.querySelectorAll( '.js-cart-count' );
    const count  = cart.reduce( ( sum, item ) => sum + ( item.qty || 1 ), 0 );
    badges.forEach( el => {
      el.textContent = count;
      el.style.display = count > 0 ? '' : 'none';
    } );
  } catch { /* storage unavailable */ }
}

function initAddToCartDemo() {
  const lumaData = window.lumaData || {};
  if ( lumaData.isWooActive === 'yes' ) return;

  document.addEventListener( 'click', e => {
    const btn = e.target.closest( '.js-add-to-cart' );
    if ( !btn ) return;
    e.preventDefault();

    const id    = btn.dataset.artworkId;
    const price = btn.dataset.price;
    const title = btn.closest( '.luma-artwork-card' )?.querySelector( '.luma-artwork-card__title' )?.textContent?.trim() || '';

    try {
      let cart = JSON.parse( localStorage.getItem( 'luma_demo_cart' ) ) || [];
      const exists = cart.find( item => item.id === id );
      if ( !exists ) {
        cart.push( { id, price, title, qty: 1 } );
        localStorage.setItem( 'luma_demo_cart', JSON.stringify( cart ) );
        initDemoCart();

        // Visual feedback
        btn.textContent = 'Added!';
        btn.disabled = true;
        setTimeout( () => {
          btn.textContent = 'In Cart';
          btn.disabled = false;
        }, 2000 );
      }
    } catch { /* storage unavailable */ }
  } );
}

// ─── Newsletter mock submit ───────────────────────────────────────────────────

function initNewsletter() {
  document.querySelectorAll( '.js-newsletter-form' ).forEach( form => {
    form.addEventListener( 'submit', e => {
      e.preventDefault();
      const input = form.querySelector( 'input[type="email"]' );
      const btn   = form.querySelector( 'button[type="submit"]' );
      if ( !input?.value ) return;

      if ( btn ) { btn.textContent = 'Subscribed!'; btn.disabled = true; }
      input.value = '';
      setTimeout( () => {
        if ( btn ) { btn.textContent = 'Subscribe'; btn.disabled = false; }
      }, 4000 );
    } );
  } );
}

// ─── Boot ─────────────────────────────────────────────────────────────────────

document.addEventListener( 'DOMContentLoaded', () => {
  initHeader();
  initMobileMenu();
  initSearch();
  initScrollHint();
  initReveal();
  initModals();
  initFavorites();
  initGalleryWalk();
  initViewInRoom();
  initAiAssistant();
  initAiCurator();
  initFilters();
  initDemoCart();
  initAddToCartDemo();
  initNewsletter();
  initCursorLight();
} );
