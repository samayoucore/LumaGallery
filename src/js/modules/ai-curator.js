/**
 * AI Curator — rule-based artwork recommender.
 * Filters artwork cards in the DOM by mood, budget, room, color, size.
 * On the /ai-curator/ page, also generates a recommendation summary.
 */

const EXPLANATION_CHIPS = {
  mood:   'matches mood',
  budget: 'within budget',
  color:  'soft palette',
  room:   'fits the room',
  size:   'right scale',
};

function parseBudget( val ) {
  const map = {
    '0-200':    [0,   200 ],
    '200-500':  [200, 500 ],
    '500-1000': [500, 1000],
    '1000+':    [1000, Infinity],
    'any':      [0,   Infinity],
  };
  return map[ val ] || [0, Infinity];
}

function cardMatchesFilters( card, filters ) {
  let matched = 0;
  const reasons = [];

  const cardMood    = card.dataset.mood    || '';
  const cardPrice   = parseFloat( card.dataset.price   || '0' );
  const cardRoom    = card.dataset.room    || '';
  const cardColor   = card.dataset.color   || '';
  const cardSize    = card.dataset.size    || '';

  if ( filters.mood && filters.mood !== 'any' ) {
    if ( cardMood.includes( filters.mood ) ) { matched++; reasons.push( 'mood' ); }
  }

  if ( filters.budget && filters.budget !== 'any' ) {
    const [min, max] = parseBudget( filters.budget );
    if ( cardPrice >= min && cardPrice <= max ) { matched++; reasons.push( 'budget' ); }
  }

  if ( filters.room && filters.room !== 'any' ) {
    if ( !cardRoom || cardRoom.includes( filters.room ) ) { matched++; reasons.push( 'room' ); }
  }

  if ( filters.color && filters.color !== 'any' ) {
    if ( !cardColor || cardColor.includes( filters.color ) ) { matched++; reasons.push( 'color' ); }
  }

  if ( filters.size && filters.size !== 'any' ) {
    if ( !cardSize || cardSize.includes( filters.size ) ) { matched++; reasons.push( 'size' ); }
  }

  // Show card if at least one filter matches, or no filters active
  const activeFilters = Object.values( filters ).filter( v => v && v !== 'any' ).length;
  return { show: activeFilters === 0 || matched > 0, reasons };
}

function buildSummary( form, count ) {
  const mood   = form.querySelector( '[name="curator_mood"]' )?.value   || '';
  const budget = form.querySelector( '[name="curator_budget"]' )?.value || '';
  const room   = form.querySelector( '[name="curator_room"]' )?.value   || '';

  let parts = [];
  if ( mood   && mood   !== 'any' ) parts.push( mood );
  if ( budget && budget !== 'any' ) parts.push( `under ${ budget.replace( '-', '–' ) }` );
  if ( room   && room   !== 'any' ) parts.push( `for a ${ room } room` );

  return `Based on your preferences, we found ${ count } artwork${ count !== 1 ? 's' : '' }${ parts.length ? ' — ' + parts.join( ', ' ) : '' }.`;
}

function renderChips( container, reasons ) {
  container.innerHTML = '';
  reasons.forEach( ( key ) => {
    const chip = document.createElement( 'span' );
    chip.className = 'luma-chip luma-curator-chip';
    chip.textContent = EXPLANATION_CHIPS[ key ] || key;
    container.appendChild( chip );
  } );
}

function runCurator( form ) {
  const filters = {
    mood:   form.querySelector( '[name="curator_mood"]' )?.value   || 'any',
    budget: form.querySelector( '[name="curator_budget"]' )?.value || 'any',
    room:   form.querySelector( '[name="curator_room"]' )?.value   || 'any',
    color:  form.querySelector( '[name="curator_color"]' )?.value  || 'any',
    size:   form.querySelector( '[name="curator_size"]' )?.value   || 'any',
  };

  const cards   = document.querySelectorAll( '.luma-artwork-card[data-artwork-id]' );
  const results = document.querySelector( '.js-curator-results' );
  const summary = document.querySelector( '.js-curator-summary' );

  let visibleCount = 0;

  cards.forEach( ( card ) => {
    const { show, reasons } = cardMatchesFilters( card, filters );

    card.style.transition = 'opacity .3s, transform .3s';
    if ( show ) {
      card.style.opacity   = '1';
      card.style.transform = 'none';
      card.hidden          = false;
      visibleCount++;

      // Attach explanation chips
      let chipWrap = card.querySelector( '.luma-curator-chips' );
      if ( !chipWrap ) {
        chipWrap = document.createElement( 'div' );
        chipWrap.className = 'luma-curator-chips';
        card.querySelector( '.luma-artwork-card__body' )?.appendChild( chipWrap );
      }
      renderChips( chipWrap, reasons );
    } else {
      card.style.opacity   = '0.2';
      card.style.transform = 'scale(0.97)';
      card.hidden          = false; // keep in grid but dim
    }
  } );

  if ( summary ) {
    summary.textContent = buildSummary( form, visibleCount );
    summary.style.display = '';
  }

  if ( results ) {
    results.style.display = '';
    results.scrollIntoView( { behavior: 'smooth', block: 'start' } );
  }
}

export function initAiCurator() {
  const form = document.querySelector( '.js-curator-form' );
  if ( !form ) return;

  // Simulated async generation
  form.addEventListener( 'submit', ( e ) => {
    e.preventDefault();

    const submitBtn = form.querySelector( '[type="submit"]' );
    const loader    = document.querySelector( '.js-curator-loader' );

    if ( submitBtn ) { submitBtn.disabled = true; submitBtn.classList.add( 'is-loading' ); }
    if ( loader    ) loader.style.display = '';

    setTimeout( () => {
      runCurator( form );
      if ( submitBtn ) { submitBtn.disabled = false; submitBtn.classList.remove( 'is-loading' ); }
      if ( loader    ) loader.style.display = 'none';
    }, 1200 );
  } );

  // Live re-filter when selects change (optional UX)
  form.querySelectorAll( 'select, input[type="range"]' ).forEach( ( input ) => {
    input.addEventListener( 'change', () => {} ); // intentionally no auto-run
  } );
}
