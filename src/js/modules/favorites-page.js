/**
 * Favorites Page — renders saved artworks, artists and exhibitions on
 * /favorites/ entirely from the client-side meta store (see favorites.js).
 *
 * Removal is handled by the shared delegated handler in favorites.js (the
 * rendered cards carry .js-toggle-favorite); this module only listens for the
 * `luma:favorites:changed` event and re-paints the affected tab. Depends on the
 * global `Favorites` object exported by favorites.js (same concatenated bundle).
 */

const FP_HEART = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>';

function fpEsc( str ) {
  return String( str == null ? '' : str )
    .replace( /&/g, '&amp;' ).replace( /</g, '&lt;' ).replace( />/g, '&gt;' )
    .replace( /"/g, '&quot;' ).replace( /'/g, '&#39;' );
}

function fpBuildArtwork( it ) {
  const url   = fpEsc( it.url || '#' );
  const media = it.image
    ? `<img class="luma-artwork-card__image" src="${ fpEsc( it.image ) }" alt="${ fpEsc( it.title ) }">`
    : `<div class="luma-artwork-card__placeholder" style="background:${ fpEsc( it.gradient || '#2a2a2a' ) };" aria-label="${ fpEsc( it.title ) }"></div>`;
  return `
    <article class="luma-artwork-card luma-favorites-card" data-artwork-id="${ fpEsc( it.id ) }" data-status="${ fpEsc( it.status || 'available' ) }">
      <div class="luma-artwork-card__image-wrap">
        <a href="${ url }" class="luma-artwork-card__image-link">${ media }</a>
        <button class="luma-artwork-card__favorite js-toggle-favorite is-active" data-artwork-id="${ fpEsc( it.id ) }" data-type="artwork" aria-label="Remove from favorites" aria-pressed="true">${ FP_HEART }</button>
      </div>
      <div class="luma-artwork-card__body">
        ${ it.subtitle ? `<div class="luma-artwork-card__meta"><span class="luma-artwork-card__artist">${ fpEsc( it.subtitle ) }</span></div>` : '' }
        <h3 class="luma-artwork-card__title"><a href="${ url }">${ fpEsc( it.title || 'Untitled' ) }</a></h3>
        ${ it.price ? `<span class="luma-artwork-card__price">${ fpEsc( it.price ) }</span>` : '' }
      </div>
    </article>`;
}

function fpBuildArtist( it ) {
  const url   = fpEsc( it.url || '#' );
  const media = it.image
    ? `<img class="luma-artist-card__avatar" src="${ fpEsc( it.image ) }" alt="${ fpEsc( it.title ) }">`
    : `<div class="luma-artist-card__avatar-placeholder" style="background:${ fpEsc( it.gradient || '#2a2a2a' ) };"><span class="luma-artist-card__initial">${ fpEsc( it.initial || ( it.title || '?' ).charAt( 0 ) ) }</span></div>`;
  return `
    <article class="luma-artist-card luma-favorites-card" data-artist-id="${ fpEsc( it.id ) }">
      <a href="${ url }" class="luma-artist-card__image-link"><div class="luma-artist-card__image-wrap">${ media }</div></a>
      <div class="luma-artist-card__body">
        <h3 class="luma-artist-card__name"><a href="${ url }">${ fpEsc( it.title || 'Artist' ) }</a></h3>
        ${ it.subtitle ? `<span class="luma-artist-card__location">${ fpEsc( it.subtitle ) }</span>` : '' }
        <div class="luma-artist-card__footer">
          <button class="luma-button luma-button--ghost luma-button--xs js-toggle-favorite is-active" data-artist-id="${ fpEsc( it.id ) }" data-type="artist" aria-pressed="true" aria-label="Unfollow artist">Following</button>
        </div>
      </div>
    </article>`;
}

function fpBuildExhibition( it ) {
  const url   = fpEsc( it.url || '#' );
  const media = it.image
    ? `<img class="luma-exhibition-card__cover-img" src="${ fpEsc( it.image ) }" alt="${ fpEsc( it.title ) }">`
    : `<div class="luma-exhibition-card__cover-placeholder" style="background:${ fpEsc( it.gradient || '#2a2a2a' ) };"></div>`;
  return `
    <article class="luma-exhibition-card luma-favorites-card" data-exhibition-id="${ fpEsc( it.id ) }">
      <a href="${ url }" class="luma-exhibition-card__cover-link">
        <div class="luma-exhibition-card__cover">
          ${ media }
          <div class="luma-exhibition-card__cover-overlay" aria-hidden="true"></div>
          <div class="luma-exhibition-card__content">
            ${ it.subtitle ? `<span class="luma-exhibition-card__subtitle">${ fpEsc( it.subtitle ) }</span>` : '' }
            <h3 class="luma-exhibition-card__title"><a href="${ url }">${ fpEsc( it.title || 'Exhibition' ) }</a></h3>
            <div class="luma-exhibition-card__bottom">
              <span class="luma-exhibition-card__enter">Enter exhibition
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
              </span>
            </div>
          </div>
        </div>
      </a>
      <button class="luma-exhibition-card__favorite js-toggle-favorite is-active" data-exhibition-id="${ fpEsc( it.id ) }" data-type="exhibition" aria-pressed="true" aria-label="Remove from favorites">${ FP_HEART }</button>
    </article>`;
}

function fpBuild( typePlural, it ) {
  if ( typePlural === 'artists' )     return fpBuildArtist( it );
  if ( typePlural === 'exhibitions' ) return fpBuildExhibition( it );
  return fpBuildArtwork( it );
}

function fpRenderTab( typePlural ) {
  const panel = document.querySelector( `.js-fav-panel[data-fav-panel="${ typePlural }"]` );
  if ( !panel ) return 0;

  const grid        = panel.querySelector( '.js-fav-grid' );
  const empty       = panel.querySelector( '.js-fav-empty' );
  const recommended = panel.querySelector( '.js-fav-recommended' );
  const items       = ( typeof Favorites !== 'undefined' ) ? Favorites.getData( typePlural ) : [];

  // Per-tab count badges
  document.querySelectorAll( `.js-fav-count[data-fav-type="${ typePlural }"]` ).forEach( ( el ) => {
    el.textContent = items.length;
  } );

  if ( !items.length ) {
    if ( grid ) { grid.innerHTML = ''; grid.hidden = true; }
    if ( empty ) empty.hidden = false;
    if ( recommended ) recommended.hidden = false;
    return 0;
  }

  if ( grid ) {
    grid.innerHTML = items.map( ( it ) => fpBuild( typePlural, it ) ).join( '' );
    grid.hidden = false;
  }
  if ( empty ) empty.hidden = true;
  if ( recommended ) recommended.hidden = true;
  return items.length;
}

function fpRenderAll() {
  const total = [ 'artworks', 'artists', 'exhibitions' ].reduce( ( sum, t ) => sum + fpRenderTab( t ), 0 );
  document.querySelectorAll( '.js-fav-total' ).forEach( ( el ) => {
    el.textContent = total;
  } );
}

function fpInitTabs() {
  const tabs = Array.from( document.querySelectorAll( '.js-fav-tab' ) );
  if ( !tabs.length ) return;

  function activate( tab ) {
    const target = tab.dataset.favTab;
    tabs.forEach( ( t ) => {
      const on = t === tab;
      t.classList.toggle( 'is-active', on );
      t.setAttribute( 'aria-selected', on ? 'true' : 'false' );
      t.tabIndex = on ? 0 : -1;
    } );
    document.querySelectorAll( '.js-fav-panel' ).forEach( ( p ) => {
      p.hidden = p.dataset.favPanel !== target;
    } );
  }

  tabs.forEach( ( tab ) => {
    tab.addEventListener( 'click', () => activate( tab ) );
    tab.addEventListener( 'keydown', ( e ) => {
      if ( e.key !== 'ArrowRight' && e.key !== 'ArrowLeft' ) return;
      e.preventDefault();
      const i = tabs.indexOf( tab );
      const next = e.key === 'ArrowRight' ? ( i + 1 ) % tabs.length : ( i - 1 + tabs.length ) % tabs.length;
      tabs[ next ].focus();
      activate( tabs[ next ] );
    } );
  } );
}

export function initFavoritesPage() {
  if ( !document.querySelector( '.js-favorites-page' ) ) return;

  fpInitTabs();
  fpRenderAll();

  document.addEventListener( 'luma:favorites:changed', ( e ) => {
    const type = e.detail?.type;
    const plural = type ? type + 's' : null;
    if ( plural && document.querySelector( `.js-fav-panel[data-fav-panel="${ plural }"]` ) ) {
      fpRenderTab( plural );
      const total = [ 'artworks', 'artists', 'exhibitions' ]
        .reduce( ( sum, t ) => sum + ( ( typeof Favorites !== 'undefined' ) ? Favorites.getData( t ).length : 0 ), 0 );
      document.querySelectorAll( '.js-fav-total' ).forEach( ( el ) => { el.textContent = total; } );
    } else {
      fpRenderAll();
    }
  } );
}
