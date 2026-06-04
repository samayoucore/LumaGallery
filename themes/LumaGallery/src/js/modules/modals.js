/**
 * Modal system — open/close any .js-modal by ID or data attribute.
 * Accessible: manages aria-hidden, focus trap, scroll lock, Escape key.
 */

let activeModal = null;
const scrollStack = [];

function lockScroll() {
  if ( scrollStack.length === 0 ) {
    const scrollY = window.scrollY;
    document.body.style.top    = `-${ scrollY }px`;
    document.body.style.position = 'fixed';
    document.body.style.width    = '100%';
    scrollStack.push( scrollY );
  }
}

function unlockScroll() {
  if ( scrollStack.length > 0 ) {
    const scrollY = scrollStack.pop();
    document.body.style.position = '';
    document.body.style.top      = '';
    document.body.style.width    = '';
    window.scrollTo( 0, scrollY );
  }
}

function getFocusable( el ) {
  return Array.from(
    el.querySelectorAll(
      'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
    )
  ).filter( ( node ) => !node.closest( '[aria-hidden="true"]' ) );
}

function trapFocus( modal, e ) {
  const focusable = getFocusable( modal );
  if ( focusable.length === 0 ) return;
  const first = focusable[0];
  const last  = focusable[ focusable.length - 1 ];

  if ( e.shiftKey ) {
    if ( document.activeElement === first ) { last.focus(); e.preventDefault(); }
  } else {
    if ( document.activeElement === last ) { first.focus(); e.preventDefault(); }
  }
}

export function openModal( modalEl ) {
  if ( !modalEl ) return;
  if ( activeModal ) closeModal( activeModal );

  activeModal = modalEl;
  modalEl.setAttribute( 'aria-hidden', 'false' );
  modalEl.classList.add( 'is-open' );
  lockScroll();

  // Focus first focusable element
  const focusable = getFocusable( modalEl );
  if ( focusable.length ) {
    setTimeout( () => focusable[0].focus(), 60 );
  }

  document.addEventListener( 'keydown', handleKeydown );
}

export function closeModal( modalEl ) {
  if ( !modalEl ) return;
  modalEl.setAttribute( 'aria-hidden', 'true' );
  modalEl.classList.remove( 'is-open' );
  if ( activeModal === modalEl ) activeModal = null;
  unlockScroll();
  document.removeEventListener( 'keydown', handleKeydown );

  document.dispatchEvent( new CustomEvent( 'luma:modal:closed', { detail: { modal: modalEl } } ) );
}

function handleKeydown( e ) {
  if ( e.key === 'Escape' && activeModal ) {
    closeModal( activeModal );
  }
  if ( e.key === 'Tab' && activeModal ) {
    trapFocus( activeModal, e );
  }
}

export function initModals() {
  // Close on backdrop / close button click
  document.addEventListener( 'click', ( e ) => {
    if ( e.target.closest( '.js-modal-close' ) ) {
      const modal = e.target.closest( '.js-modal' );
      if ( modal ) closeModal( modal );
      return;
    }

    // Click on backdrop (the element itself, not a child)
    if ( e.target.classList.contains( 'luma-modal__backdrop' ) ) {
      const modal = e.target.closest( '.js-modal' );
      if ( modal ) closeModal( modal );
    }
  } );

  // Open by data-modal-target attribute
  document.addEventListener( 'click', ( e ) => {
    const trigger = e.target.closest( '[data-modal-target]' );
    if ( !trigger ) return;
    const targetId = trigger.dataset.modalTarget;
    const modal = document.getElementById( targetId );
    if ( modal ) { e.preventDefault(); openModal( modal ); }
  } );
}
