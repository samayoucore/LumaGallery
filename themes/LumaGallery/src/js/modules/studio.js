/**
 * Artist Studio (Phase 6C) — a front-end demo of an artist workspace.
 *
 * Everything lives in localStorage (luma_studio_artworks, luma_studio_posts and
 * a small luma_studio_ai_count counter). No real auth, no WordPress CPT writes,
 * no external/AI APIs — the assistant is a rule-based text generator. The module
 * no-ops on every page except the /studio/ template (.js-studio-page) and never
 * throws: storage reads fall back to [], DOM access is guarded throughout.
 *
 * Top-level names are STUDIO_* / studio*-prefixed on purpose: Gulp concatenates
 * every module into one scope, so generic identifiers (qs, cap, CONFIG…) would
 * collide with the other bundles.
 */

const STUDIO_STORAGE_KEYS = {
  artworks: 'luma_studio_artworks',
  posts:    'luma_studio_posts',
  aiCount:  'luma_studio_ai_count',
};

const STUDIO_CONFIG = {
  currency:    '€',
  maxArtworks: 24,
  maxPosts:    24,
  aiDelay:     550, // ms — simulated "thinking" so the loading state is visible
};

const STUDIO_SELECTORS = {
  page:             '.js-studio-page',
  tab:              '.js-studio-tab',
  panel:            '.js-studio-panel',
  go:               '.js-studio-go',
  stat:             '[data-studio-stat]',
  artworkForm:      '.js-studio-artwork-form',
  artworks:         '.js-studio-artworks',
  artworksFeedback: '.js-studio-artworks-feedback',
  overviewArtworks: '.js-studio-overview-artworks',
  preview:          '.js-studio-preview',
  postForm:         '.js-studio-post-form',
  posts:            '.js-studio-posts',
  postsFeedback:    '.js-studio-posts-feedback',
  overviewPosts:    '.js-studio-overview-posts',
  postArtwork:      '.js-studio-post-artwork',
  aiForm:           '.js-studio-ai-form',
  aiOutput:         '.js-studio-ai-output',
};

// Deterministic, editorial gradients used as the "image" for demo artworks.
const STUDIO_GRADIENTS = {
  calm:        'linear-gradient(150deg, #283039 0%, #46555f 55%, #1b2127 100%)',
  dark:        'linear-gradient(150deg, #17171b 0%, #2c2c34 55%, #0c0c0f 100%)',
  romantic:    'linear-gradient(150deg, #3a2230 0%, #6b3a4d 55%, #241019 100%)',
  minimal:     'linear-gradient(150deg, #2b2b2e 0%, #4a4a4f 55%, #1d1d20 100%)',
  bright:      'linear-gradient(150deg, #3a3320 0%, #7a6a32 55%, #241f10 100%)',
  melancholic: 'linear-gradient(150deg, #20262e 0%, #3c4654 55%, #14181e 100%)',
  atmospheric: 'linear-gradient(150deg, #1e2a2a 0%, #3a4a4a 55%, #141e1e 100%)',
};

const STUDIO_COLOR_GRADIENTS = {
  light:   'linear-gradient(150deg, #d9d6cf 0%, #f0eee8 55%, #c7c3ba 100%)',
  dark:    'linear-gradient(150deg, #161618 0%, #2b2b30 55%, #0c0c0e 100%)',
  warm:    'linear-gradient(150deg, #3a2a1c 0%, #7a5634 55%, #241810 100%)',
  cold:    'linear-gradient(150deg, #1c2a3a 0%, #345a7a 55%, #101824 100%)',
  neutral: 'linear-gradient(150deg, #2a2a2c 0%, #4c4c50 55%, #1c1c1e 100%)',
  accent:  'linear-gradient(150deg, #3a1410 0%, #fb472f 75%, #7a1a10 100%)',
};

// Rule-based phrase banks for the mock AI assistant.
const STUDIO_AI = {
  mood: {
    calm:        [ 'quiet balance', 'a soft visual rhythm', 'a meditative stillness' ],
    dark:        [ 'deep shadow', 'a charged contrast', 'a nocturnal atmosphere' ],
    romantic:    [ 'a tender warmth', 'an intimate glow', 'a gentle longing' ],
    minimal:     [ 'a calm clarity', 'breathing space', 'essential restraint' ],
    bright:      [ 'an open light', 'a luminous energy', 'an optimistic openness' ],
    melancholic: [ 'a wistful quiet', 'fading light', 'a tender sadness' ],
    atmospheric: [ 'a drifting haze', 'a sense of depth', 'an enveloping mood' ],
  },
  style: {
    abstract:   [ 'gestural abstraction', 'non-literal form', 'pure shape and field' ],
    modern:     [ 'a modern sensibility', 'confident contemporary lines', 'a current visual language' ],
    minimal:    [ 'a reduced composition', 'a clear visual language', 'essential form' ],
    figurative: [ 'a grounded figurative presence', 'recognisable form', 'the human trace' ],
    landscape:  [ 'an expansive horizon', 'a strong sense of place', 'open terrain' ],
    surreal:    [ 'a dreamlike logic', 'an uncanny stillness', 'a shifted reality' ],
  },
  technique: {
    oil:           [ 'worked in oil', 'built from layered oil glazes', 'rich in oil surfaces' ],
    acrylic:       [ 'rendered in acrylic', 'set in crisp acrylic planes', 'painted in fast acrylic passages' ],
    watercolor:    [ 'washed in watercolour', 'made of translucent washes', 'carried by fluid pigment' ],
    'mixed media': [ 'built in mixed media', 'assembled from layered material', 'composed of collaged surfaces' ],
    digital:       [ 'composed digitally', 'shaped with precise digital tooling', 'finished screen-native' ],
    ink:           [ 'drawn in ink', 'cut with decisive ink lines', 'alive with fluid ink marks' ],
  },
};

const STUDIO_TOPIC_LABELS = {
  'artist-stories': 'Artist Stories',
  'exhibitions':    'Exhibitions',
  'collecting-art': 'Collecting Art',
  'studio-notes':   'Studio Notes',
};

const STUDIO_AI_TYPE_LABELS = {
  description: 'Artwork Description',
  story:       'Story Behind the Artwork',
  tags:        'Tags',
  social:      'Social Post',
  seo:         'SEO Description',
};

// Module state.
let studioLastAi = null;   // { type, text, tags }
let studioAiTimer = null;

// ─── Tiny utilities ─────────────────────────────────────────────────────────

function studioQs( sel )            { return document.querySelector( sel ); }
function studioCap( s )             { s = String( s || '' ); return s ? s.charAt( 0 ).toUpperCase() + s.slice( 1 ) : s; }
function studioVal( form, name )    { return form && form.elements[ name ] ? String( form.elements[ name ].value ).trim() : ''; }
function studioRand( arr )          { return arr[ Math.floor( Math.random() * arr.length ) ]; }

function studioEsc( str ) {
  return String( str == null ? '' : str )
    .replace( /&/g, '&amp;' ).replace( /</g, '&lt;' ).replace( />/g, '&gt;' )
    .replace( /"/g, '&quot;' ).replace( /'/g, '&#39;' );
}

function studioMoney( n, cur ) {
  if ( typeof lumaCartMoney === 'function' ) return lumaCartMoney( n, cur );
  const c = cur || STUDIO_CONFIG.currency;
  return c + ' ' + Math.round( parseFloat( n ) || 0 ).toString().replace( /\B(?=(\d{3})+(?!\d))/g, ' ' );
}

function studioId( prefix ) {
  return prefix + '-' + Date.now().toString( 36 ) + '-' + Math.random().toString( 36 ).slice( 2, 7 );
}

function studioFmtDate( iso ) {
  try { return new Date( iso ).toLocaleDateString( undefined, { year: 'numeric', month: 'short', day: 'numeric' } ); }
  catch { return ''; }
}

function studioClampText( str, n ) {
  const s = String( str || '' ).trim();
  return s.length > n ? s.slice( 0, n - 1 ).trim() + '…' : s;
}

function studioTagsToArray( str ) {
  return String( str || '' ).split( ',' ).map( ( s ) => s.trim() ).filter( Boolean ).slice( 0, 12 );
}

function studioTagsToString( arr ) {
  return Array.isArray( arr ) ? arr.join( ', ' ) : String( arr || '' );
}

function studioStatusLabel( s ) { return studioCap( s || 'draft' ); }
function studioTopicLabel( slug ) { return STUDIO_TOPIC_LABELS[ slug ] || 'Studio Notes'; }
function studioAiTypeLabel( t ) { return STUDIO_AI_TYPE_LABELS[ t ] || 'Result'; }

function studioGradient( mood, color ) {
  if ( STUDIO_GRADIENTS[ mood ] ) return STUDIO_GRADIENTS[ mood ];
  if ( STUDIO_COLOR_GRADIENTS[ color ] ) return STUDIO_COLOR_GRADIENTS[ color ];
  return 'linear-gradient(150deg, #2a2a2e 0%, #4a4a4f 55%, #1c1c1f 100%)';
}

function studioFeedback( el, msg, type ) {
  if ( !el ) return;
  el.textContent = msg || '';
  el.classList.remove( 'is-success', 'is-error' );
  if ( msg && type === 'success' ) el.classList.add( 'is-success' );
  if ( msg && type === 'error' ) el.classList.add( 'is-error' );
}

// ─── Storage ────────────────────────────────────────────────────────────────

function studioReadJSON( key ) {
  try { const d = JSON.parse( localStorage.getItem( key ) ); return Array.isArray( d ) ? d : []; }
  catch { return []; }
}

function studioReadArtworks() { return studioReadJSON( STUDIO_STORAGE_KEYS.artworks ); }
function studioWriteArtworks( list ) {
  try { localStorage.setItem( STUDIO_STORAGE_KEYS.artworks, JSON.stringify( Array.isArray( list ) ? list : [] ) ); }
  catch { /* full / disabled */ }
}

function studioReadPosts() { return studioReadJSON( STUDIO_STORAGE_KEYS.posts ); }
function studioWritePosts( list ) {
  try { localStorage.setItem( STUDIO_STORAGE_KEYS.posts, JSON.stringify( Array.isArray( list ) ? list : [] ) ); }
  catch { /* full / disabled */ }
}

function studioReadAiCount() {
  try { const n = parseInt( localStorage.getItem( STUDIO_STORAGE_KEYS.aiCount ), 10 ); return isNaN( n ) ? 0 : n; }
  catch { return 0; }
}
function studioBumpAiCount() {
  try { localStorage.setItem( STUDIO_STORAGE_KEYS.aiCount, String( studioReadAiCount() + 1 ) ); }
  catch { /* noop */ }
  studioRenderStats();
}

function studioArtworkTitle( id ) {
  const a = studioReadArtworks().find( ( x ) => x.id === id );
  return a ? ( a.title || 'Untitled' ) : '';
}

// ─── Card / list builders ─────────────────────────────────────────────────────

function studioEmpty( title, text, go, cta ) {
  return `
    <div class="luma-studio__empty">
      <p class="luma-studio__empty-title">${ studioEsc( title ) }</p>
      <p class="luma-studio__empty-text">${ studioEsc( text ) }</p>
      ${ cta ? `<button type="button" class="luma-button luma-button--ghost js-studio-go" data-studio-go="${ studioEsc( go ) }">${ studioEsc( cta ) }</button>` : '' }
    </div>`;
}

function studioArtworkCard( a ) {
  const tags  = Array.isArray( a.tags ) ? a.tags : [];
  const grad  = a.imageGradient || studioGradient( a.mood, a.color );
  const meta  = [ a.mood, a.technique ].filter( Boolean ).map( studioCap ).join( ' · ' );
  const price = ( a.price || a.price === 0 ) ? studioMoney( a.price, a.currency ) : '';
  const isPub = a.status === 'published';
  const status = a.status || 'draft';
  return `
    <article class="luma-studio__card" data-studio-id="${ studioEsc( a.id ) }">
      <div class="luma-studio__card-media" style="background:${ studioEsc( grad ) };" aria-hidden="true">
        <span class="luma-studio__card-badge luma-studio__card-badge--${ studioEsc( status ) }">${ studioEsc( studioStatusLabel( status ) ) }</span>
      </div>
      <div class="luma-studio__card-body">
        ${ meta ? `<p class="luma-studio__card-meta">${ studioEsc( meta ) }</p>` : '' }
        <h4 class="luma-studio__card-title">${ studioEsc( a.title || 'Untitled' ) }</h4>
        ${ price ? `<p class="luma-studio__card-price">${ studioEsc( price ) }</p>` : '' }
        ${ tags.length ? `<div class="luma-studio__card-tags">${ tags.slice( 0, 4 ).map( ( t ) => `<span class="luma-studio__card-tag">${ studioEsc( t ) }</span>` ).join( '' ) }</div>` : '' }
        <p class="luma-studio__card-date">${ studioEsc( studioFmtDate( a.createdAt ) ) }</p>
        <div class="luma-studio__card-actions">
          <button type="button" class="luma-studio__card-btn js-studio-art-edit" data-studio-id="${ studioEsc( a.id ) }">Edit</button>
          <button type="button" class="luma-studio__card-btn js-studio-art-duplicate" data-studio-id="${ studioEsc( a.id ) }">Duplicate</button>
          <button type="button" class="luma-studio__card-btn js-studio-art-toggle" data-studio-id="${ studioEsc( a.id ) }">${ isPub ? 'Unpublish' : 'Publish' }</button>
          <button type="button" class="luma-studio__card-btn luma-studio__card-btn--danger js-studio-art-delete" data-studio-id="${ studioEsc( a.id ) }">Delete</button>
        </div>
      </div>
    </article>`;
}

function studioPostCard( p ) {
  const isPub   = p.status === 'published';
  const topic   = studioTopicLabel( p.topic );
  const linked  = p.linkedArtworkId ? studioArtworkTitle( p.linkedArtworkId ) : '';
  const excerpt = p.excerpt || studioClampText( p.content, 140 );
  return `
    <article class="luma-studio__post" data-studio-id="${ studioEsc( p.id ) }">
      <div class="luma-studio__post-head">
        <span class="luma-studio__post-topic">${ studioEsc( topic ) }</span>
        <span class="luma-studio__post-status luma-studio__post-status--${ isPub ? 'published' : 'draft' }">${ studioEsc( studioStatusLabel( p.status ) ) }</span>
      </div>
      <h4 class="luma-studio__post-title">${ studioEsc( p.title || 'Untitled post' ) }</h4>
      ${ excerpt ? `<p class="luma-studio__post-excerpt">${ studioEsc( excerpt ) }</p>` : '' }
      ${ linked ? `<p class="luma-studio__post-linked"><span>Linked artwork</span> ${ studioEsc( linked ) }</p>` : '' }
      <p class="luma-studio__post-date">${ studioEsc( studioFmtDate( p.createdAt ) ) }</p>
      <div class="luma-studio__card-actions">
        <button type="button" class="luma-studio__card-btn js-studio-post-duplicate" data-studio-id="${ studioEsc( p.id ) }">Duplicate</button>
        <button type="button" class="luma-studio__card-btn js-studio-post-toggle" data-studio-id="${ studioEsc( p.id ) }">${ isPub ? 'Unpublish' : 'Publish' }</button>
        <button type="button" class="luma-studio__card-btn luma-studio__card-btn--danger js-studio-post-delete" data-studio-id="${ studioEsc( p.id ) }">Delete</button>
      </div>
    </article>`;
}

// ─── Renderers ────────────────────────────────────────────────────────────────

function studioRenderStats() {
  const arts  = studioReadArtworks();
  const posts = studioReadPosts();
  const counts = {
    artworks:  arts.length,
    drafts:    arts.filter( ( a ) => a.status === 'draft' ).length,
    published: arts.filter( ( a ) => a.status === 'published' ).length,
    posts:     posts.length,
    ai:        studioReadAiCount(),
  };
  document.querySelectorAll( STUDIO_SELECTORS.stat ).forEach( ( el ) => {
    const key = el.getAttribute( 'data-studio-stat' );
    if ( Object.prototype.hasOwnProperty.call( counts, key ) ) el.textContent = counts[ key ];
  } );
}

function studioRenderArtworks() {
  const el = studioQs( STUDIO_SELECTORS.artworks );
  if ( !el ) return;
  const list = studioReadArtworks().slice().reverse(); // newest first
  el.classList.toggle( 'is-empty', !list.length );
  el.innerHTML = list.length
    ? list.map( studioArtworkCard ).join( '' )
    : studioEmpty( 'No studio artworks yet', 'Create your first demo artwork and preview how it could appear in Luma Gallery.', 'add-artwork', 'Add Artwork' );
}

function studioRenderPosts() {
  const el = studioQs( STUDIO_SELECTORS.posts );
  if ( !el ) return;
  const list = studioReadPosts().slice().reverse();
  el.classList.toggle( 'is-empty', !list.length );
  el.innerHTML = list.length
    ? list.map( studioPostCard ).join( '' )
    : studioEmpty( 'No studio posts yet', 'Share a note from the studio using the form. It stays local to your browser in this demo.', '', '' );
}

function studioRenderOverview() {
  const aEl = studioQs( STUDIO_SELECTORS.overviewArtworks );
  if ( aEl ) {
    const list = studioReadArtworks().slice().reverse().slice( 0, 3 );
    aEl.classList.toggle( 'is-empty', !list.length );
    aEl.innerHTML = list.length
      ? list.map( studioArtworkCard ).join( '' )
      : studioEmpty( 'No artworks yet', 'Add your first demo artwork to get started.', 'add-artwork', 'Add Artwork' );
  }
  const pEl = studioQs( STUDIO_SELECTORS.overviewPosts );
  if ( pEl ) {
    const list = studioReadPosts().slice().reverse().slice( 0, 2 );
    pEl.classList.toggle( 'is-empty', !list.length );
    pEl.innerHTML = list.length
      ? list.map( studioPostCard ).join( '' )
      : studioEmpty( 'No posts yet', 'Write your first studio post to share your process.', 'posts', 'Write Studio Post' );
  }
}

function studioPopulatePostArtworkSelect() {
  const sel = studioQs( STUDIO_SELECTORS.postArtwork );
  if ( !sel ) return;
  const current = sel.value;
  const arts = studioReadArtworks();
  sel.innerHTML = [ '<option value="">None</option>' ].concat(
    arts.map( ( a ) => `<option value="${ studioEsc( a.id ) }">${ studioEsc( a.title || 'Untitled' ) }</option>` )
  ).join( '' );
  if ( current && arts.some( ( a ) => a.id === current ) ) sel.value = current;
}

function studioUpdatePreview() {
  const el   = studioQs( STUDIO_SELECTORS.preview );
  const form = studioQs( STUDIO_SELECTORS.artworkForm );
  if ( !el || !form ) return;

  const get      = ( n ) => studioVal( form, n );
  const title    = get( 'title' ) || 'Untitled study';
  const mood     = get( 'mood' );
  const tech     = get( 'technique' );
  const desc     = get( 'description' );
  const priceRaw = get( 'price' );
  const currency = get( 'currency' ) || STUDIO_CONFIG.currency;
  const tags     = studioTagsToArray( get( 'tags' ) );
  const grad     = studioGradient( mood, get( 'color' ) );
  const meta     = [ mood, tech ].filter( Boolean ).map( studioCap ).join( ' · ' );
  const price    = priceRaw !== '' ? studioMoney( priceRaw, currency ) : '';

  el.innerHTML = `
    <article class="luma-studio__preview-card">
      <div class="luma-studio__preview-media" style="background:${ studioEsc( grad ) };" aria-hidden="true">
        <span class="luma-studio__preview-index" aria-hidden="true">N01</span>
      </div>
      <div class="luma-studio__preview-body">
        ${ meta ? `<p class="luma-studio__preview-meta">${ studioEsc( meta ) }</p>` : '' }
        <h4 class="luma-studio__preview-title">${ studioEsc( title ) }</h4>
        ${ price ? `<p class="luma-studio__preview-price">${ studioEsc( price ) }</p>` : '' }
        ${ desc ? `<p class="luma-studio__preview-desc">${ studioEsc( studioClampText( desc, 180 ) ) }</p>` : '' }
        ${ tags.length ? `<div class="luma-studio__preview-tags">${ tags.slice( 0, 5 ).map( ( t ) => `<span class="luma-studio__preview-tag">${ studioEsc( t ) }</span>` ).join( '' ) }</div>` : '' }
      </div>
    </article>`;
}

// ─── Validation + field errors ────────────────────────────────────────────────

function studioFieldError( form, name, message ) {
  const input = form.elements[ name ];
  if ( !input ) return;
  const field = input.closest( '.luma-studio__field' ) || input.parentElement;
  if ( !field ) return;
  field.classList.toggle( 'has-error', !!message );
  input.setAttribute( 'aria-invalid', message ? 'true' : 'false' );
  let err = field.querySelector( '.luma-studio__error' );
  if ( message ) {
    if ( !err ) {
      err = document.createElement( 'p' );
      err.className = 'luma-studio__error';
      field.appendChild( err );
    }
    err.textContent = message;
  } else if ( err ) {
    err.textContent = '';
  }
}

function studioClearErrors( form ) {
  if ( !form ) return;
  form.querySelectorAll( '.has-error' ).forEach( ( f ) => f.classList.remove( 'has-error' ) );
  form.querySelectorAll( '.luma-studio__error' ).forEach( ( e ) => { e.textContent = ''; } );
}

function studioValidateArtwork( form ) {
  const required = [
    [ 'title',  'Title is required.' ],
    [ 'price',  'Price is required.' ],
    [ 'status', 'Choose a status.' ],
    [ 'mood',   'Choose a mood.' ],
    [ 'style',  'Choose a style.' ],
  ];
  let firstInvalid = null;
  required.forEach( ( [ name, msg ] ) => {
    const input = form.elements[ name ];
    const value = input ? String( input.value ).trim() : '';
    let m = '';
    if ( !value ) m = msg;
    else if ( name === 'price' && ( isNaN( parseFloat( value ) ) || parseFloat( value ) < 0 ) ) m = 'Enter a valid price.';
    studioFieldError( form, name, m );
    if ( m && !firstInvalid ) firstInvalid = input;
  } );
  if ( firstInvalid ) { firstInvalid.focus(); return false; }
  return true;
}

function studioValidatePost( form ) {
  const required = [
    [ 'title',   'Title is required.' ],
    [ 'content', 'Content is required.' ],
  ];
  let firstInvalid = null;
  required.forEach( ( [ name, msg ] ) => {
    const input = form.elements[ name ];
    const value = input ? String( input.value ).trim() : '';
    const m = value ? '' : msg;
    studioFieldError( form, name, m );
    if ( m && !firstInvalid ) firstInvalid = input;
  } );
  if ( firstInvalid ) { firstInvalid.focus(); return false; }
  return true;
}

// ─── Artwork CRUD ───────────────────────────────────────────────────────────

function studioAfterArtworkChange() {
  studioRenderStats();
  studioRenderArtworks();
  studioRenderOverview();
  studioPopulatePostArtworkSelect();
}

function studioClearArtworkForm() {
  const form = studioQs( STUDIO_SELECTORS.artworkForm );
  if ( !form ) return;
  const defaults = { currency: '€', status: 'draft' };
  Array.from( form.elements ).forEach( ( el ) => {
    if ( !el.name ) return;
    if ( el.tagName === 'SELECT' ) el.value = Object.prototype.hasOwnProperty.call( defaults, el.name ) ? defaults[ el.name ] : '';
    else el.value = '';
  } );
  studioClearErrors( form );
  const submit = form.querySelector( '.js-studio-artwork-submit' );
  if ( submit ) submit.textContent = 'Save Artwork';
  const title = studioQs( '.js-studio-form-title' );
  if ( title ) title.textContent = 'Add Artwork';
  studioUpdatePreview();
}

function studioSaveArtwork( form ) {
  const feedback = studioQs( '.js-studio-artwork-feedback' );
  if ( !studioValidateArtwork( form ) ) {
    studioFeedback( feedback, 'Please fix the highlighted fields.', 'error' );
    return;
  }

  const get  = ( n ) => studioVal( form, n );
  const now  = new Date().toISOString();
  const data = {
    title:       get( 'title' ),
    price:       parseFloat( get( 'price' ) ) || 0,
    currency:    get( 'currency' ) || STUDIO_CONFIG.currency,
    status:      get( 'status' ) || 'draft',
    mood:        get( 'mood' ),
    style:       get( 'style' ),
    technique:   get( 'technique' ),
    material:    get( 'material' ),
    size:        get( 'size' ),
    year:        get( 'year' ),
    color:       get( 'color' ),
    room:        get( 'room' ),
    description: get( 'description' ),
    story:       get( 'story' ),
    tags:        studioTagsToArray( get( 'tags' ) ),
  };
  data.imageGradient = studioGradient( data.mood, data.color );

  const list      = studioReadArtworks();
  const editingId = get( 'editingId' );

  // Update an existing record.
  if ( editingId ) {
    const idx = list.findIndex( ( a ) => a.id === editingId );
    if ( idx !== -1 ) {
      list[ idx ] = Object.assign( {}, list[ idx ], data, { updatedAt: now } );
      studioWriteArtworks( list );
      studioAfterArtworkChange();
      studioClearArtworkForm();
      studioSwitchTab( 'artworks' );
      studioFeedback( studioQs( STUDIO_SELECTORS.artworksFeedback ), 'Artwork updated.', 'success' );
      return;
    }
  }

  // Create a new record (respect the cap).
  if ( list.length >= STUDIO_CONFIG.maxArtworks ) {
    studioFeedback( feedback, `Limit reached — you can store up to ${ STUDIO_CONFIG.maxArtworks } demo artworks. Delete one to add more.`, 'error' );
    return;
  }
  const artwork = Object.assign( { id: studioId( 'studio-artwork' ) }, data, { createdAt: now, updatedAt: now } );
  list.push( artwork );
  studioWriteArtworks( list );
  studioAfterArtworkChange();
  studioClearArtworkForm();
  studioFeedback( feedback, `“${ artwork.title }” saved as ${ artwork.status }.`, 'success' );
}

function studioEditArtwork( id ) {
  const a    = studioReadArtworks().find( ( x ) => x.id === id );
  const form = studioQs( STUDIO_SELECTORS.artworkForm );
  if ( !a || !form ) return;

  const set = ( n, v ) => { const el = form.elements[ n ]; if ( el ) el.value = v == null ? '' : v; };
  set( 'editingId', a.id );
  set( 'title', a.title );
  set( 'price', a.price );
  set( 'currency', a.currency || '€' );
  set( 'status', a.status || 'draft' );
  set( 'mood', a.mood );
  set( 'style', a.style );
  set( 'technique', a.technique );
  set( 'material', a.material );
  set( 'size', a.size );
  set( 'year', a.year );
  set( 'color', a.color );
  set( 'room', a.room );
  set( 'description', a.description );
  set( 'story', a.story );
  set( 'tags', studioTagsToString( a.tags ) );

  studioClearErrors( form );
  studioFeedback( studioQs( '.js-studio-artwork-feedback' ), '', '' );
  const submit = form.querySelector( '.js-studio-artwork-submit' );
  if ( submit ) submit.textContent = 'Update Artwork';
  const title = studioQs( '.js-studio-form-title' );
  if ( title ) title.textContent = 'Edit Artwork';

  studioSwitchTab( 'add-artwork' );
  studioUpdatePreview();
  if ( form.elements.title ) form.elements.title.focus();
}

function studioDuplicateArtwork( id ) {
  const list = studioReadArtworks();
  const a = list.find( ( x ) => x.id === id );
  if ( !a ) return;
  if ( list.length >= STUDIO_CONFIG.maxArtworks ) {
    studioFeedback( studioQs( STUDIO_SELECTORS.artworksFeedback ), `Limit reached — up to ${ STUDIO_CONFIG.maxArtworks } demo artworks. Delete one to duplicate.`, 'error' );
    return;
  }
  const now = new Date().toISOString();
  list.push( Object.assign( {}, a, {
    id:        studioId( 'studio-artwork' ),
    title:     ( a.title || 'Untitled' ) + ' (Copy)',
    status:    'draft',
    createdAt: now,
    updatedAt: now,
  } ) );
  studioWriteArtworks( list );
  studioAfterArtworkChange();
  studioFeedback( studioQs( STUDIO_SELECTORS.artworksFeedback ), 'Artwork duplicated as a draft.', 'success' );
}

function studioToggleArtworkStatus( id ) {
  const list = studioReadArtworks();
  const a = list.find( ( x ) => x.id === id );
  if ( !a ) return;
  a.status = a.status === 'published' ? 'draft' : 'published';
  a.updatedAt = new Date().toISOString();
  studioWriteArtworks( list );
  studioAfterArtworkChange();
  studioFeedback( studioQs( STUDIO_SELECTORS.artworksFeedback ),
    a.status === 'published' ? 'Artwork published (demo).' : 'Artwork moved to draft.', 'success' );
}

function studioDeleteArtwork( id ) {
  studioWriteArtworks( studioReadArtworks().filter( ( a ) => a.id !== id ) );
  studioAfterArtworkChange();
  studioFeedback( studioQs( STUDIO_SELECTORS.artworksFeedback ), 'Artwork deleted.', 'success' );
}

// ─── Post CRUD ──────────────────────────────────────────────────────────────

function studioAfterPostChange() {
  studioRenderStats();
  studioRenderPosts();
  studioRenderOverview();
}

function studioClearPostForm() {
  const form = studioQs( STUDIO_SELECTORS.postForm );
  if ( !form ) return;
  const defaults = { topic: 'studio-notes', status: 'draft' };
  Array.from( form.elements ).forEach( ( el ) => {
    if ( !el.name ) return;
    if ( el.tagName === 'SELECT' ) el.value = Object.prototype.hasOwnProperty.call( defaults, el.name ) ? defaults[ el.name ] : '';
    else el.value = '';
  } );
  studioClearErrors( form );
  const submit = form.querySelector( '.js-studio-post-submit' );
  if ( submit ) submit.textContent = 'Save Post';
}

function studioSavePost( form ) {
  const feedback = studioQs( '.js-studio-post-feedback' );
  if ( !studioValidatePost( form ) ) {
    studioFeedback( feedback, 'Please fix the highlighted fields.', 'error' );
    return;
  }

  const get  = ( n ) => studioVal( form, n );
  const now  = new Date().toISOString();
  const content = get( 'content' );
  const data = {
    title:           get( 'title' ),
    topic:           get( 'topic' ) || 'studio-notes',
    excerpt:         get( 'excerpt' ) || studioClampText( content, 140 ),
    content,
    linkedArtworkId: get( 'linkedArtworkId' ),
    status:          get( 'status' ) || 'draft',
  };

  const list      = studioReadPosts();
  const editingId = get( 'editingId' );

  if ( editingId ) {
    const idx = list.findIndex( ( p ) => p.id === editingId );
    if ( idx !== -1 ) {
      list[ idx ] = Object.assign( {}, list[ idx ], data, { updatedAt: now } );
      studioWritePosts( list );
      studioAfterPostChange();
      studioClearPostForm();
      studioFeedback( feedback, 'Post updated.', 'success' );
      return;
    }
  }

  if ( list.length >= STUDIO_CONFIG.maxPosts ) {
    studioFeedback( feedback, `Limit reached — you can store up to ${ STUDIO_CONFIG.maxPosts } demo posts. Delete one to add more.`, 'error' );
    return;
  }
  const post = Object.assign( { id: studioId( 'studio-post' ) }, data, { createdAt: now, updatedAt: now } );
  list.push( post );
  studioWritePosts( list );
  studioAfterPostChange();
  studioClearPostForm();
  studioFeedback( feedback, `“${ post.title }” saved as ${ post.status }.`, 'success' );
}

function studioDuplicatePost( id ) {
  const list = studioReadPosts();
  const p = list.find( ( x ) => x.id === id );
  if ( !p ) return;
  if ( list.length >= STUDIO_CONFIG.maxPosts ) {
    studioFeedback( studioQs( STUDIO_SELECTORS.postsFeedback ), `Limit reached — up to ${ STUDIO_CONFIG.maxPosts } demo posts.`, 'error' );
    return;
  }
  const now = new Date().toISOString();
  list.push( Object.assign( {}, p, {
    id:        studioId( 'studio-post' ),
    title:     ( p.title || 'Untitled post' ) + ' (Copy)',
    status:    'draft',
    createdAt: now,
    updatedAt: now,
  } ) );
  studioWritePosts( list );
  studioAfterPostChange();
  studioFeedback( studioQs( STUDIO_SELECTORS.postsFeedback ), 'Post duplicated as a draft.', 'success' );
}

function studioTogglePostStatus( id ) {
  const list = studioReadPosts();
  const p = list.find( ( x ) => x.id === id );
  if ( !p ) return;
  p.status = p.status === 'published' ? 'draft' : 'published';
  p.updatedAt = new Date().toISOString();
  studioWritePosts( list );
  studioAfterPostChange();
  studioFeedback( studioQs( STUDIO_SELECTORS.postsFeedback ),
    p.status === 'published' ? 'Post published (demo).' : 'Post moved to draft.', 'success' );
}

function studioDeletePost( id ) {
  studioWritePosts( studioReadPosts().filter( ( p ) => p.id !== id ) );
  studioAfterPostChange();
  studioFeedback( studioQs( STUDIO_SELECTORS.postsFeedback ), 'Post deleted.', 'success' );
}

// ─── Two-step inline confirm (no confirm() dialogs) ───────────────────────────

function studioConfirmDelete( btn, onConfirm ) {
  if ( btn.dataset.confirm === '1' ) {
    window.clearTimeout( Number( btn.dataset.confirmTimer ) );
    onConfirm();
    return;
  }
  btn.dataset.confirm = '1';
  btn.dataset.label = btn.textContent;
  btn.textContent = 'Confirm?';
  btn.classList.add( 'is-confirm' );
  btn.dataset.confirmTimer = String( window.setTimeout( () => {
    btn.dataset.confirm = '';
    btn.textContent = btn.dataset.label || 'Delete';
    btn.classList.remove( 'is-confirm' );
  }, 3000 ) );
}

// ─── Mock AI assistant ────────────────────────────────────────────────────────

function studioPhrase( group, key ) {
  const bank = STUDIO_AI[ group ] && STUDIO_AI[ group ][ key ];
  return ( bank && bank.length ) ? studioRand( bank ) : '';
}

function studioGenDescription( d ) {
  const t     = d.title || 'This piece';
  const mood  = studioPhrase( 'mood', d.mood );
  const style = studioPhrase( 'style', d.style );
  const tech  = studioPhrase( 'technique', d.technique );
  const tone  = d.tone || 'gallery';

  if ( tone === 'poetic' ) {
    let s = `${ t } holds ${ mood || 'a quiet presence' }`;
    if ( style ) s += `, unfolding through ${ style }`;
    if ( tech ) s += `, ${ tech }`;
    return s + '. Light settles slowly, and the surface keeps something back for the patient viewer.';
  }
  if ( tone === 'minimal' ) {
    const bits = [ style, tech, mood ].filter( Boolean ).map( studioCap );
    return `${ t }.` + ( bits.length ? ' ' + bits.join( '. ' ) + '.' : '' );
  }
  if ( tone === 'commercial' ) {
    let s = `${ t } is an original work defined by ${ style || 'a distinctive hand' }`;
    if ( tech ) s += `, ${ tech }`;
    s += '.';
    if ( mood ) s += ` It brings ${ mood } into any room.`;
    return s + ' A statement piece ready to anchor a collection.';
  }
  if ( tone === 'social' ) {
    let s = `${ t } ✨`;
    if ( mood ) s += ` ${ studioCap( mood ) }`;
    const extra = [ style, tech ].filter( Boolean );
    return extra.length ? s + ' · ' + extra.join( ' · ' ) : s;
  }
  // gallery (default)
  let s = `${ t }`;
  if ( style ) s += ` is built on ${ style }`;
  if ( tech ) s += `, ${ tech }`;
  s += '.';
  if ( mood ) s += ` The composition carries ${ mood }.`;
  return s + ' Presented as an original studio work.';
}

function studioGenStory( d ) {
  const t    = d.title || 'this piece';
  const mood = studioPhrase( 'mood', d.mood );
  const tech = studioPhrase( 'technique', d.technique );
  const tone = d.tone || 'gallery';
  let s = `I began “${ t }” chasing ${ mood || 'a feeling I could not quite name' }. `;
  s += `The studio was quiet, and the work was ${ tech || 'made slowly, by hand' }. `;
  if ( tone === 'poetic' ) s += 'Some mornings it resisted me, and I learned to let it. ';
  else if ( tone === 'commercial' ) s += 'It became one of the pieces I am proudest to release. ';
  else if ( tone === 'social' ) s += 'Swipe to watch it come together 👀 ';
  else if ( tone === 'minimal' ) s += 'No rush — layer by layer. ';
  return s + `What stayed, in the end, was the ${ d.mood || 'mood' } I kept returning to.`;
}

function studioGenTags( d ) {
  const moodExtra = {
    calm: [ 'soft light', 'stillness' ], dark: [ 'shadow', 'contrast' ], romantic: [ 'warmth', 'intimate' ],
    minimal: [ 'negative space', 'restraint' ], bright: [ 'luminous', 'open air' ], melancholic: [ 'wistful', 'quiet' ],
    atmospheric: [ 'depth', 'haze' ],
  }[ d.mood ] || [];
  const styleExtra = {
    abstract: [ 'gesture' ], modern: [ 'contemporary' ], minimal: [ 'essential form' ],
    figurative: [ 'the figure' ], landscape: [ 'horizon' ], surreal: [ 'dreamlike' ],
  }[ d.style ] || [];
  const all = [ d.mood, d.style, d.technique ].concat( moodExtra, styleExtra, [ 'original art', 'studio work', 'collectible' ] );
  const seen = {};
  const out = [];
  all.forEach( ( x ) => {
    const v = String( x || '' ).toLowerCase().trim();
    if ( v && !seen[ v ] ) { seen[ v ] = 1; out.push( v ); }
  } );
  return out.slice( 0, 8 );
}

function studioGenSocial( d ) {
  const t    = d.title || 'New work';
  const mood = studioPhrase( 'mood', d.mood );
  const tone = d.tone || 'social';
  let line = `${ t } — ${ mood || 'fresh from the studio' }.`;
  if ( tone === 'commercial' ) line += ' Available now — DM to collect.';
  else if ( tone === 'poetic' ) line += ' A small, paused world.';
  else if ( tone === 'gallery' ) line += ' Now showing in the studio.';
  else if ( tone === 'minimal' ) line += ' New work.';
  else line += ' New in the studio.';
  const tags = studioGenTags( d ).slice( 0, 5 ).map( ( s ) => '#' + s.replace( /\s+/g, '' ) );
  return line + '\n\n' + tags.join( ' ' );
}

function studioGenSeo( d ) {
  const parts = [ d.title || 'Original artwork' ];
  if ( d.style ) parts.push( studioCap( d.style ) + ' style' );
  if ( d.technique ) parts.push( d.technique );
  if ( d.mood ) parts.push( d.mood + ' mood' );
  return studioClampText( parts.join( ' · ' ) + '. Original studio artwork at Luma Gallery.', 160 );
}

function studioGenerate( type, d ) {
  switch ( type ) {
    case 'story':  return { text: studioGenStory( d ), tags: null };
    case 'tags':   { const tags = studioGenTags( d ); return { text: tags.join( ', ' ), tags }; }
    case 'social': return { text: studioGenSocial( d ), tags: null };
    case 'seo':    return { text: studioGenSeo( d ), tags: null };
    case 'description':
    default:       return { text: studioGenDescription( d ), tags: null };
  }
}

function studioCurrentAiText() {
  const ta = studioQs( '.js-studio-ai-text' );
  if ( ta ) return ta.value;
  return studioLastAi ? studioLastAi.text : '';
}

function studioRenderAiOutput( state ) {
  const el = studioQs( STUDIO_SELECTORS.aiOutput );
  if ( !el ) return;

  if ( !state ) {
    el.innerHTML = '<p class="luma-studio__ai-placeholder">Pick what to generate and press <strong>Generate</strong>. Your draft appears here.</p>';
    return;
  }
  if ( state.loading ) {
    el.innerHTML = '<p class="luma-studio__ai-loading">Generating studio draft…</p>';
    return;
  }

  const { type, text, tags } = state;
  const tagChips = tags
    ? `<div class="luma-studio__ai-tags">${ tags.map( ( t ) => `<span class="luma-studio__ai-chip">${ studioEsc( t ) }</span>` ).join( '' ) }</div>`
    : '';
  const applyLabel = type === 'social' ? 'Apply to Post' : 'Apply to Artwork';
  const applyBtn = type === 'seo'
    ? ''
    : `<button type="button" class="luma-button luma-button--ghost luma-button--sm js-studio-ai-apply">${ applyLabel }</button>`;

  el.innerHTML = `
    <div class="luma-studio__ai-result">
      <span class="luma-studio__ai-result-type">${ studioEsc( studioAiTypeLabel( type ) ) }</span>
      <textarea class="luma-textarea js-studio-ai-text" rows="6" aria-label="Generated ${ studioEsc( studioAiTypeLabel( type ) ) }">${ studioEsc( text ) }</textarea>
      ${ tagChips }
      <div class="luma-studio__ai-actions">
        <button type="button" class="luma-button luma-button--ghost luma-button--sm js-studio-ai-copy">Copy</button>
        ${ applyBtn }
        <button type="button" class="luma-button luma-button--ghost luma-button--sm js-studio-ai-regenerate">Regenerate</button>
      </div>
      <p class="luma-studio__ai-feedback js-studio-ai-feedback" role="status" aria-live="polite"></p>
    </div>`;
}

function studioRunAi( form ) {
  if ( !form ) return;
  const d = {
    type:      studioVal( form, 'type' ) || 'description',
    title:     studioVal( form, 'title' ),
    mood:      studioVal( form, 'mood' ),
    style:     studioVal( form, 'style' ),
    technique: studioVal( form, 'technique' ),
    tone:      studioVal( form, 'tone' ) || 'gallery',
  };
  studioRenderAiOutput( { loading: true } );
  window.clearTimeout( studioAiTimer );
  studioAiTimer = window.setTimeout( () => {
    const res = studioGenerate( d.type, d );
    studioLastAi = { type: d.type, text: res.text, tags: res.tags };
    studioRenderAiOutput( studioLastAi );
    studioBumpAiCount();
  }, STUDIO_CONFIG.aiDelay );
}

function studioFallbackCopy( text, done ) {
  try {
    const ta = document.createElement( 'textarea' );
    ta.value = text;
    ta.setAttribute( 'readonly', '' );
    ta.style.position = 'fixed';
    ta.style.opacity = '0';
    document.body.appendChild( ta );
    ta.select();
    const ok = document.execCommand && document.execCommand( 'copy' );
    document.body.removeChild( ta );
    done( !!ok );
  } catch { done( false ); }
}

function studioCopyAi() {
  const text = studioCurrentAiText();
  const fb = studioQs( '.js-studio-ai-feedback' );
  const done = ( ok ) => studioFeedback( fb, ok ? 'Copied to clipboard.' : 'Copy is not available — select the text manually.', ok ? 'success' : 'error' );
  if ( !text ) { done( false ); return; }
  if ( navigator.clipboard && navigator.clipboard.writeText ) {
    navigator.clipboard.writeText( text ).then( () => done( true ) ).catch( () => studioFallbackCopy( text, done ) );
  } else {
    studioFallbackCopy( text, done );
  }
}

function studioApplyAi() {
  if ( !studioLastAi ) return;
  const text = studioCurrentAiText();
  const type = studioLastAi.type;

  if ( type === 'social' ) {
    const form = studioQs( STUDIO_SELECTORS.postForm );
    if ( form && form.elements.content ) form.elements.content.value = text;
    studioSwitchTab( 'posts' );
    studioFeedback( studioQs( '.js-studio-post-feedback' ), 'Applied generated text to the post content.', 'success' );
    return;
  }

  const fieldMap = { description: 'description', story: 'story', tags: 'tags' };
  const field = fieldMap[ type ];
  if ( !field ) return;
  const form = studioQs( STUDIO_SELECTORS.artworkForm );
  if ( form && form.elements[ field ] ) form.elements[ field ].value = text;
  studioSwitchTab( 'add-artwork' );
  studioUpdatePreview();
  studioFeedback( studioQs( '.js-studio-artwork-feedback' ), 'Applied generated text to the artwork form.', 'success' );
}

// Prefill the AI form from the artwork / post forms, then jump to the AI tab.
function studioArtworkToAi() {
  const art = studioQs( STUDIO_SELECTORS.artworkForm );
  const ai  = studioQs( STUDIO_SELECTORS.aiForm );
  if ( art && ai ) {
    const copy = ( n ) => { if ( ai.elements[ n ] && art.elements[ n ] ) ai.elements[ n ].value = art.elements[ n ].value; };
    copy( 'title' ); copy( 'mood' ); copy( 'style' ); copy( 'technique' );
    if ( ai.elements.type && !ai.elements.type.value ) ai.elements.type.value = 'description';
  }
  studioSwitchTab( 'ai' );
}

function studioPostToAi() {
  const ai = studioQs( STUDIO_SELECTORS.aiForm );
  const post = studioQs( STUDIO_SELECTORS.postForm );
  if ( ai ) {
    if ( ai.elements.type ) ai.elements.type.value = 'social';
    if ( ai.elements.title && post && post.elements.title ) ai.elements.title.value = post.elements.title.value;
  }
  studioSwitchTab( 'ai' );
}

// ─── Tabs ─────────────────────────────────────────────────────────────────────

function studioSwitchTab( key, focusTab ) {
  const page = studioQs( STUDIO_SELECTORS.page );
  if ( !page ) return;
  page.querySelectorAll( STUDIO_SELECTORS.tab ).forEach( ( t ) => {
    const isActive = t.getAttribute( 'data-studio-tab' ) === key;
    t.setAttribute( 'aria-selected', isActive ? 'true' : 'false' );
    t.setAttribute( 'tabindex', isActive ? '0' : '-1' );
    t.classList.toggle( 'is-active', isActive );
    if ( isActive && focusTab ) t.focus();
  } );
  page.querySelectorAll( STUDIO_SELECTORS.panel ).forEach( ( p ) => {
    p.hidden = p.id !== ( 'studio-panel-' + key );
  } );
}

function studioTabKeydown( e ) {
  const page = studioQs( STUDIO_SELECTORS.page );
  if ( !page ) return;
  const tabs = Array.from( page.querySelectorAll( STUDIO_SELECTORS.tab ) );
  const i = tabs.indexOf( e.target.closest( STUDIO_SELECTORS.tab ) );
  if ( i === -1 ) return;
  let next = -1;
  if ( e.key === 'ArrowRight' || e.key === 'ArrowDown' ) next = ( i + 1 ) % tabs.length;
  else if ( e.key === 'ArrowLeft' || e.key === 'ArrowUp' ) next = ( i - 1 + tabs.length ) % tabs.length;
  else if ( e.key === 'Home' ) next = 0;
  else if ( e.key === 'End' ) next = tabs.length - 1;
  if ( next === -1 ) return;
  e.preventDefault();
  studioSwitchTab( tabs[ next ].getAttribute( 'data-studio-tab' ), true );
}

// ─── Delegated click handling ──────────────────────────────────────────────────

function studioOnClick( e ) {
  const tab = e.target.closest( STUDIO_SELECTORS.tab );
  if ( tab ) { studioSwitchTab( tab.getAttribute( 'data-studio-tab' ) ); return; }

  const go = e.target.closest( STUDIO_SELECTORS.go );
  if ( go ) { studioSwitchTab( go.getAttribute( 'data-studio-go' ) ); return; }

  let b;
  if ( ( b = e.target.closest( '.js-studio-art-edit' ) ) )      { studioEditArtwork( b.getAttribute( 'data-studio-id' ) ); return; }
  if ( ( b = e.target.closest( '.js-studio-art-duplicate' ) ) ) { studioDuplicateArtwork( b.getAttribute( 'data-studio-id' ) ); return; }
  if ( ( b = e.target.closest( '.js-studio-art-toggle' ) ) )    { studioToggleArtworkStatus( b.getAttribute( 'data-studio-id' ) ); return; }
  if ( ( b = e.target.closest( '.js-studio-art-delete' ) ) )    { studioConfirmDelete( b, () => studioDeleteArtwork( b.getAttribute( 'data-studio-id' ) ) ); return; }

  if ( ( b = e.target.closest( '.js-studio-post-duplicate' ) ) ) { studioDuplicatePost( b.getAttribute( 'data-studio-id' ) ); return; }
  if ( ( b = e.target.closest( '.js-studio-post-toggle' ) ) )    { studioTogglePostStatus( b.getAttribute( 'data-studio-id' ) ); return; }
  if ( ( b = e.target.closest( '.js-studio-post-delete' ) ) )    { studioConfirmDelete( b, () => studioDeletePost( b.getAttribute( 'data-studio-id' ) ) ); return; }

  if ( e.target.closest( '.js-studio-artwork-ai' ) ) { studioArtworkToAi(); return; }
  if ( e.target.closest( '.js-studio-post-ai' ) )    { studioPostToAi(); return; }

  if ( e.target.closest( '.js-studio-ai-copy' ) )       { studioCopyAi(); return; }
  if ( e.target.closest( '.js-studio-ai-apply' ) )      { studioApplyAi(); return; }
  if ( e.target.closest( '.js-studio-ai-regenerate' ) ) { studioRunAi( studioQs( STUDIO_SELECTORS.aiForm ) ); return; }
}

// ─── Boot ─────────────────────────────────────────────────────────────────────

export function studioInit() {
  const page = studioQs( STUDIO_SELECTORS.page );
  if ( !page ) return;

  // Initial render from localStorage.
  studioRenderStats();
  studioRenderArtworks();
  studioRenderPosts();
  studioRenderOverview();
  studioPopulatePostArtworkSelect();
  studioUpdatePreview();
  studioRenderAiOutput( null );

  // Bind listeners once.
  if ( page.dataset.studioBound === '1' ) return;
  page.dataset.studioBound = '1';

  page.addEventListener( 'click', studioOnClick );
  page.addEventListener( 'keydown', ( e ) => {
    if ( e.target.closest( STUDIO_SELECTORS.tab ) ) studioTabKeydown( e );
  } );

  const artForm = studioQs( STUDIO_SELECTORS.artworkForm );
  if ( artForm ) {
    artForm.addEventListener( 'submit', ( e ) => { e.preventDefault(); studioSaveArtwork( artForm ); } );
    artForm.addEventListener( 'reset', ( e ) => { e.preventDefault(); studioClearArtworkForm(); studioFeedback( studioQs( '.js-studio-artwork-feedback' ), '', '' ); } );
    artForm.addEventListener( 'input', ( e ) => {
      const field = e.target.closest && e.target.closest( '.luma-studio__field' );
      if ( field && field.classList.contains( 'has-error' ) ) studioFieldError( artForm, e.target.name, '' );
      studioUpdatePreview();
    } );
  }

  const postForm = studioQs( STUDIO_SELECTORS.postForm );
  if ( postForm ) {
    postForm.addEventListener( 'submit', ( e ) => { e.preventDefault(); studioSavePost( postForm ); } );
    postForm.addEventListener( 'reset', ( e ) => { e.preventDefault(); studioClearPostForm(); studioFeedback( studioQs( '.js-studio-post-feedback' ), '', '' ); } );
    postForm.addEventListener( 'input', ( e ) => {
      const field = e.target.closest && e.target.closest( '.luma-studio__field' );
      if ( field && field.classList.contains( 'has-error' ) ) studioFieldError( postForm, e.target.name, '' );
    } );
  }

  const aiForm = studioQs( STUDIO_SELECTORS.aiForm );
  if ( aiForm ) {
    aiForm.addEventListener( 'submit', ( e ) => { e.preventDefault(); studioRunAi( aiForm ); } );
  }
}
