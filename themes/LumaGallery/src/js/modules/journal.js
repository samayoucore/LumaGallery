/**
 * Journal — client-side topic filtering for the /journal/ archive.
 * Filters the already-rendered cards by their data-topic slug; works for both
 * real posts and demo cards. No-ops unless the journal grid is present.
 */

export function initJournal() {
  const grid = document.querySelector( '.js-journal-grid' );
  const tabs = Array.from( document.querySelectorAll( '.js-journal-filter' ) );
  if ( !grid || !tabs.length ) return;

  const empty = document.querySelector( '.js-journal-empty' );

  function apply( topic ) {
    grid.querySelectorAll( '.js-journal-card' ).forEach( ( card ) => {
      const match = topic === 'all' || ( card.dataset.topic || '' ) === topic;
      card.hidden = !match;
    } );
    if ( empty ) {
      const anyVisible = Array.from( grid.querySelectorAll( '.js-journal-card' ) ).some( ( c ) => !c.hidden );
      empty.hidden = anyVisible;
    }
  }

  tabs.forEach( ( tab ) => {
    tab.addEventListener( 'click', () => {
      const topic = tab.dataset.topic || 'all';
      tabs.forEach( ( t ) => {
        const on = t === tab;
        t.classList.toggle( 'is-active', on );
        t.setAttribute( 'aria-pressed', on ? 'true' : 'false' );
      } );
      apply( topic );
    } );
  } );
}
