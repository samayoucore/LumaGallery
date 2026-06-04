/**
 * Gallery filters — client-side filter/sort for the artwork grid.
 * Works with or without AJAX: filters visible .luma-artwork-card elements by
 * their data attributes (data-mood, data-style, data-technique, data-status, data-price).
 * priceMax is handled numerically via data-price (raw number).
 */

const FILTER_SKIP_KEYS = new Set( ['priceMax'] );

function getActiveFilters( form ) {
  const data = {};
  new FormData( form ).forEach( ( val, key ) => {
    if ( FILTER_SKIP_KEYS.has( key ) ) return;
    if ( val && val !== 'any' && val !== '' ) {
      data[ key ] = val;
    }
  } );
  return data;
}

function cardPasses( card, filters ) {
  for ( const [key, val] of Object.entries( filters ) ) {
    const cardVal = card.dataset[ key ] || '';
    // Multi-value check: card may have comma-separated values
    const cardParts = cardVal.split( ',' ).map( s => s.trim() );
    if ( !cardParts.some( p => p.toLowerCase().includes( val.toLowerCase() ) ) ) {
      return false;
    }
  }
  return true;
}

function getPrice( card ) {
  return parseFloat( card.dataset.price || '0' );
}

function sortCards( cards, sortVal ) {
  return [...cards].sort( ( a, b ) => {
    switch ( sortVal ) {
      case 'price-asc':  return getPrice( a ) - getPrice( b );
      case 'price-desc': return getPrice( b ) - getPrice( a );
      case 'newest':     return parseInt( b.dataset.date || '0', 10 ) - parseInt( a.dataset.date || '0', 10 );
      default:           return 0;
    }
  } );
}

function applyFilters( grid, filterForm ) {
  if ( !grid ) return;

  const filters  = getActiveFilters( filterForm );
  const priceMax = parseFloat( filterForm.querySelector( '[name="priceMax"]' )?.value || '999999' );
  const sortEl   = document.querySelector( '.js-sort-select' );
  const sortVal  = sortEl?.value || '';
  const cards    = Array.from( grid.querySelectorAll( '.luma-artwork-card' ) );

  // Sort first
  const sorted = sortVal ? sortCards( cards, sortVal ) : cards;

  // Re-append in sorted order (and show/hide)
  sorted.forEach( ( card ) => {
    const passesText  = cardPasses( card, filters );
    const cardPrice   = getPrice( card );
    const passesPrice = cardPrice === 0 || cardPrice <= priceMax;
    const passes      = passesText && passesPrice;
    card.style.display  = passes ? '' : 'none';
    card.style.opacity  = passes ? '1' : '0';
    grid.appendChild( card );
  } );

  // Update count
  const visibleCount = sorted.filter( c => c.style.display !== 'none' ).length;
  const countEl = document.querySelector( '.js-gallery-count' );
  if ( countEl ) {
    countEl.textContent = `${ visibleCount } artwork${ visibleCount !== 1 ? 's' : '' }`;
  }

  // Empty state
  const empty = document.querySelector( '.js-gallery-empty' );
  if ( empty ) empty.style.display = visibleCount === 0 ? '' : 'none';
}

function initViewToggle() {
  const toggleBtns = document.querySelectorAll( '.js-view-toggle' );
  const grid       = document.querySelector( '.js-artwork-grid' );
  if ( !toggleBtns.length || !grid ) return;

  toggleBtns.forEach( ( btn ) => {
    btn.addEventListener( 'click', () => {
      toggleBtns.forEach( b => b.classList.remove( 'is-active' ) );
      btn.classList.add( 'is-active' );
      const view = btn.dataset.view;
      grid.dataset.view = view;
      grid.className = grid.className.replace( /luma-artwork-grid--\S+/g, '' ).trim();
      if ( view === 'wall' ) {
        grid.classList.add( 'luma-artwork-grid--wall' );
      } else {
        grid.classList.add( 'luma-artwork-grid--4col' );
      }
    } );
  } );
}

export function initFilters() {
  const filterForm = document.querySelector( '.js-filter-form' );
  const grid       = document.querySelector( '.js-artwork-grid' );

  if ( !filterForm || !grid ) {
    initViewToggle();
    return;
  }

  filterForm.addEventListener( 'change', () => applyFilters( grid, filterForm ) );
  filterForm.addEventListener( 'reset',  () => setTimeout( () => applyFilters( grid, filterForm ), 0 ) );

  const sortEl = document.querySelector( '.js-sort-select' );
  if ( sortEl ) sortEl.addEventListener( 'change', () => applyFilters( grid, filterForm ) );

  // Price range live update
  const priceRange = filterForm.querySelector( '[name="priceMax"]' );
  if ( priceRange ) {
    priceRange.addEventListener( 'input', () => {
      const display = filterForm.querySelector( '.js-price-display' );
      if ( display ) {
        const val = parseInt( priceRange.value, 10 );
        display.textContent = val >= 10000 ? '€10 000+' : '€' + val.toLocaleString( 'fr-FR' );
      }
      applyFilters( grid, filterForm );
    } );
  }

  // Filter toggle on mobile
  const filterToggle = document.querySelector( '.js-filter-toggle' );
  const filterPanel  = document.querySelector( '.js-filter-panel' );
  if ( filterToggle && filterPanel ) {
    filterToggle.addEventListener( 'click', () => {
      const open = filterPanel.classList.toggle( 'is-open' );
      filterToggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
    } );
  }

  initViewToggle();
}
