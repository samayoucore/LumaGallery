/**
 * AI Curator — rule-based artwork recommender (demo, no external AI API).
 *
 * Powers the full /ai-curator/ page: parses the free-text prompt for keywords,
 * merges them with the explicit selects, then curates the artwork grid —
 * hiding non-matches, surfacing a recommendation summary and per-card
 * explanation chips. Runs only when a .js-curator-form is present, so the
 * homepage teaser (which has none) is never affected.
 */

const EXPLANATION_CHIPS = {
  mood:   'matches mood',
  budget: 'within budget',
  color:  'soft palette',
  room:   'good for the room',
  size:   'right scale',
};

function parseBudget( val ) {
  const map = {
    '150':      [0,   150 ],
    '300':      [0,   300 ],
    '500':      [0,   500 ],
    'premium':  [500, Infinity],
    'any':      [0,   Infinity],
    // legacy range keys (kept for safety)
    '0-200':    [0,   200 ],
    '200-500':  [200, 500 ],
    '500-1000': [500, 1000],
    '1000+':    [1000, Infinity],
  };
  return map[ val ] || [0, Infinity];
}

/** Loose keyword parsing of the free-text prompt → filter hints. */
function parsePrompt( text ) {
  const t = ( text || '' ).toLowerCase();
  const hints = {};

  const moods = { calm: 'calm', dark: 'dark', moody: 'dark', bright: 'bright', romantic: 'romantic', minimal: 'minimal', minimalist: 'minimal', melanchol: 'melancholic', atmospheric: 'atmospheric' };
  for ( const k in moods ) { if ( t.includes( k ) ) { hints.mood = moods[ k ]; break; } }

  const rooms = { bedroom: 'bedroom', office: 'office', living: 'living', studio: 'studio' };
  for ( const k in rooms ) { if ( t.includes( k ) ) { hints.room = rooms[ k ]; break; } }

  const colors = { light: 'light', warm: 'warm', cold: 'cold', cool: 'cold', neutral: 'neutral', accent: 'accent', colourful: 'accent', colorful: 'accent' };
  for ( const k in colors ) { if ( t.includes( k ) ) { hints.color = colors[ k ]; break; } }

  const sizes = { small: 'small', large: 'large', big: 'large', medium: 'medium' };
  for ( const k in sizes ) { if ( t.includes( k ) ) { hints.size = sizes[ k ]; break; } }

  const under = t.match( /under\s*€?\s*(\d{2,5})/ ) || t.match( /€\s*(\d{2,5})/ ) || t.match( /\b(\d{2,5})\s*(?:eur|euro|€)/ );
  if ( under ) {
    const n = parseInt( under[ 1 ], 10 );
    hints.budget = n <= 150 ? '150' : n <= 300 ? '300' : n <= 500 ? '500' : 'premium';
  }

  return hints;
}

function readFilters( form ) {
  const filters = {
    mood:   form.querySelector( '[name="curator_mood"]' )?.value   || 'any',
    budget: form.querySelector( '[name="curator_budget"]' )?.value || 'any',
    room:   form.querySelector( '[name="curator_room"]' )?.value   || 'any',
    color:  form.querySelector( '[name="curator_color"]' )?.value  || 'any',
    size:   form.querySelector( '[name="curator_size"]' )?.value   || 'any',
  };

  // Prompt hints only fill gaps — an explicit select always wins.
  const hints = parsePrompt( form.querySelector( '[name="curator_prompt"]' )?.value || '' );
  for ( const key in hints ) {
    if ( !filters[ key ] || filters[ key ] === 'any' ) filters[ key ] = hints[ key ];
  }
  return filters;
}

function cardMatchesFilters( card, filters ) {
  const reasons = [];
  const cardMood  = ( card.dataset.mood  || '' ).toLowerCase();
  const cardColor = ( card.dataset.color || '' ).toLowerCase();
  const cardRoom  = ( card.dataset.room  || '' ).toLowerCase();
  const cardSize  = ( card.dataset.size  || '' ).toLowerCase();
  const cardPrice = parseFloat( card.dataset.price || '0' );

  let hard = true; // mood + budget are exclusionary; the rest only add reasons

  if ( filters.mood && filters.mood !== 'any' ) {
    if ( cardMood.includes( filters.mood ) ) reasons.push( 'mood' );
    else hard = false;
  }
  if ( filters.budget && filters.budget !== 'any' ) {
    const [ min, max ] = parseBudget( filters.budget );
    if ( cardPrice >= min && cardPrice <= max ) reasons.push( 'budget' );
    else hard = false;
  }
  if ( filters.color && filters.color !== 'any' && cardColor.includes( filters.color ) ) reasons.push( 'color' );
  if ( filters.room  && filters.room  !== 'any' && ( !cardRoom || cardRoom.includes( filters.room ) ) ) reasons.push( 'room' );
  if ( filters.size  && filters.size  !== 'any' && ( !cardSize || cardSize.includes( filters.size ) ) ) reasons.push( 'size' );

  return { show: hard, reasons };
}

function buildSummary( filters, count ) {
  const mood   = filters.mood  !== 'any' ? filters.mood : '';
  const color  = filters.color !== 'any' ? `${ filters.color }-toned` : '';
  const budget = { '150': 'under €150', '300': 'under €300', '500': 'under €500', premium: 'premium' }[ filters.budget ] || '';
  const room   = { living: 'a living room', bedroom: 'a bright bedroom', office: 'an office', studio: 'a studio' }[ filters.room ] || '';

  const descriptors = [ mood, color ].filter( Boolean ).join( ', ' );
  let s = `Based on your request, we found ${ count } ${ descriptors ? descriptors + ' ' : '' }artwork${ count !== 1 ? 's' : '' }`;
  if ( budget ) s += ' ' + budget;
  if ( room )   s += `, well suited to ${ room }`;
  return s + '.';
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
  const filters = readFilters( form );
  const cards   = document.querySelectorAll( '.js-curator-results .luma-artwork-card' );
  const results = document.querySelector( '.js-curator-results' );
  const summary = document.querySelector( '.js-curator-summary' );
  const empty   = document.querySelector( '.js-curator-empty' );

  let visibleCount = 0;

  cards.forEach( ( card ) => {
    const { show, reasons } = cardMatchesFilters( card, filters );

    if ( show ) {
      card.hidden = false;
      card.classList.add( 'is-curated' );
      visibleCount++;

      let chipWrap = card.querySelector( '.luma-curator-chips' );
      if ( !chipWrap ) {
        chipWrap = document.createElement( 'div' );
        chipWrap.className = 'luma-curator-chips';
        card.querySelector( '.luma-artwork-card__body' )?.appendChild( chipWrap );
      }
      renderChips( chipWrap, reasons );
    } else {
      card.hidden = true;
      card.classList.remove( 'is-curated' );
    }
  } );

  if ( summary ) {
    summary.textContent = buildSummary( filters, visibleCount );
    summary.hidden = false;
  }
  if ( empty ) empty.hidden = visibleCount !== 0;
  if ( results ) results.hidden = false;
}

function resetCurator( form ) {
  form.reset();
  document.querySelectorAll( '.js-curator-results .luma-artwork-card' ).forEach( ( card ) => {
    card.hidden = false;
    card.classList.remove( 'is-curated' );
    card.querySelector( '.luma-curator-chips' )?.remove();
  } );
  const summary = document.querySelector( '.js-curator-summary' );
  const empty   = document.querySelector( '.js-curator-empty' );
  if ( summary ) summary.hidden = true;
  if ( empty )   empty.hidden = true;
}

export function initAiCurator() {
  const form = document.querySelector( '.js-curator-form' );
  if ( !form ) return; // teaser / other pages have no curator form

  form.addEventListener( 'submit', ( e ) => {
    e.preventDefault();

    const submitBtn = form.querySelector( '[type="submit"]' );
    const loader    = document.querySelector( '.js-curator-loader' );

    if ( submitBtn ) { submitBtn.disabled = true; submitBtn.classList.add( 'is-loading' ); }
    if ( loader )    loader.hidden = false;

    // Simulated async curation
    setTimeout( () => {
      runCurator( form );
      if ( submitBtn ) { submitBtn.disabled = false; submitBtn.classList.remove( 'is-loading' ); }
      if ( loader )    loader.hidden = true;
    }, 1100 );
  } );

  // Reset button(s)
  document.querySelectorAll( '.js-curator-reset' ).forEach( ( btn ) => {
    btn.addEventListener( 'click', ( e ) => {
      e.preventDefault();
      resetCurator( form );
    } );
  } );
}
