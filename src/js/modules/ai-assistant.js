/**
 * AI Artist Assistant — rule-based mock generation.
 * No external API. Templates are selected by tone + context.
 */

import { openModal } from './modals.js';

const AI_ASSISTANT_AI_ASSISTANT_MODAL_ID = 'modal-ai-assistant';

// ─── Template bank ────────────────────────────────────────────────────────────

const TEMPLATES = {
  description: {
    poetic: [
      'This work exists in the space between memory and perception — where form dissolves and light becomes its own language. Painted with {technique}, it carries a quiet insistence that asks the viewer to slow down, to look again.',
      'A meditation rendered in {technique}. The composition breathes with a rhythm that cannot be hurried, inviting the eye to rest in the intervals between mark and silence.',
      'In this work, {technique} becomes a vessel for the inexpressible — a landscape of feeling mapped onto a surface that holds both restraint and release in careful tension.',
    ],
    gallery: [
      'An original work in {technique}, this piece demonstrates the artist\'s mastery of tonal balance and compositional restraint. The work is presented unframed, with a certificate of authenticity.',
      'Executed in {technique} on archival support, this work is part of a limited series exploring the interplay of light and shadow. Museum-quality archival materials throughout.',
      'A significant example of the artist\'s mature practice — {technique} handled with precision and intention. Suitable for both private and institutional collections.',
    ],
    minimal: [
      '{technique}. Original. Unique. Available.',
      'One work. One moment. {technique} on fine support.',
      'Form studied. Silence kept. {technique}.',
    ],
    commercial: [
      'A stunning original artwork in {technique} — the perfect centrepiece for any thoughtfully curated interior. Ready to ship with a certificate of authenticity.',
      'This beautiful original in {technique} will bring character and depth to your space. Loved by collectors and interior designers alike.',
      'Invest in original art. This {technique} work comes with full provenance documentation and is ready to hang immediately.',
    ],
    'social-media': [
      'New work just finished 🎨 {technique} on canvas — this one took weeks to get right. Available through Luma Gallery, link in bio.',
      'There\'s something about working in {technique} that forces you to commit to every mark. Sharing this piece for the first time — thoughts?',
      'Studio to gallery ✨ My latest in {technique} is now live on Luma Gallery. Each piece is original and ships worldwide.',
    ],
  },

  tags: {
    _any: [
      [ 'original', 'one-of-a-kind', 'contemporary', 'handmade', 'signed' ],
      [ 'texture', 'layered', 'meditative', 'minimal', 'editorial' ],
      [ 'collector', 'wall art', 'interior design', 'fine art', 'gallery quality' ],
    ],
  },

  story: {
    poetic: [
      'This work began not with a sketch but with a feeling — a particular quality of afternoon light that seemed to refuse capture. Over several sessions, the surface was built up slowly, each layer a negotiation between what was seen and what was felt.',
      'The painting arrived in fragments. First a line, then a tone, then a silence between the two that demanded attention. What you see now is the residue of that conversation — a record of looking.',
    ],
    gallery: [
      'This work was developed over an extended period as part of an ongoing investigation into the relationship between material and perception. The artist begins with close observation of a chosen subject before allowing the composition to evolve intuitively across multiple sessions.',
      'The work emerged from a sustained engagement with the studio environment — specifically the quality of northern light at different hours. The surface records this process directly, with earlier layers remaining visible beneath subsequent applications.',
    ],
    minimal: [
      'Made slowly. In silence.',
      'A single subject. Many returns.',
    ],
    commercial: [
      'This piece was created during an especially productive studio period and represents one of the artist\'s most refined works to date. The subject was chosen for its timeless quality — it will look as relevant in twenty years as it does today.',
      'Behind this work is months of development. The artist explored dozens of approaches before finding the composition you see here. The result is a piece that rewards long looking and suits a variety of interior contexts.',
    ],
    'social-media': [
      'I started this one wanting to capture a specific feeling I couldn\'t quite name. Three months later, I think I got close. Swipe to see some process shots.',
      'Every painting teaches you something. This one taught me patience. Full story in the Luma Gallery listing.',
    ],
  },

  social: {
    _any: [
      'New work available on Luma Gallery — original {technique}, signed and ready to ship. The kind of piece that changes how a room feels.',
      'Just listed: a new original on Luma Gallery. This {technique} work has been with me for months — now it\'s ready to find its home.',
      'An original artwork, hand-finished and signed. If you\'ve been looking for something quiet and considered for your space, this might be the one.',
    ],
  },

  seo: {
    _any: [
      'Original {technique} artwork by a contemporary artist. Available for sale on Luma Gallery with a certificate of authenticity and worldwide shipping.',
      'Buy original contemporary art. This {technique} work is a unique, signed original — part of a curated collection of paintings by independent artists on Luma Gallery.',
    ],
  },
};

function pick( arr ) {
  return arr[ Math.floor( Math.random() * arr.length ) ];
}

function getTemplate( type, tone ) {
  const bank = TEMPLATES[ type ];
  if ( !bank ) return null;
  const toneBank = bank[ tone ] || bank['gallery'] || bank['_any'];
  if ( !toneBank ) return null;
  const tmpl = Array.isArray( toneBank[0] ) ? pick( toneBank ) : toneBank;
  return pick( tmpl );
}

function generate( type, tone, context = {} ) {
  let text = getTemplate( type, tone );
  if ( !text ) return 'No template available for this combination.';

  // Replace tokens
  text = text.replace( /\{technique\}/g, context.technique || 'oil' );
  text = text.replace( /\{style\}/g,     context.style     || 'contemporary' );
  text = text.replace( /\{mood\}/g,      context.mood      || 'calm' );
  text = text.replace( /\{title\}/g,     context.title     || 'Untitled' );
  text = text.replace( /\{artist\}/g,    context.artist    || 'the artist' );

  if ( type === 'tags' ) {
    // text is an array for tags
    const arr = Array.isArray( text ) ? text : text.split( ',' ).map( s => s.trim() );
    return arr.map( t => `#${ t }` ).join( '  ' );
  }

  return text;
}

// ─── UI logic ─────────────────────────────────────────────────────────────────

let currentType = null;
let currentTone = 'gallery';

function showState( modal, state ) {
  modal.querySelectorAll( '.js-ai-state-empty, .js-ai-state-loading, .js-ai-state-generated' )
    .forEach( ( el ) => { el.style.display = 'none'; } );

  const target = modal.querySelector( `.js-ai-state-${ state }` );
  if ( target ) target.style.display = '';
}

function setGeneratedText( modal, type, text ) {
  const typeEl = modal.querySelector( '.js-ai-generated-type' );
  const textEl = modal.querySelector( '.js-ai-generated-text' );

  const labels = {
    description: 'Generated Description',
    tags:        'Suggested Tags',
    story:       'Story Behind the Artwork',
    social:      'Social Media Post',
    seo:         'SEO Description',
  };

  if ( typeEl ) typeEl.textContent = labels[ type ] || type;
  if ( textEl ) textEl.textContent = text;

  showState( modal, 'generated' );
}

function triggerGenerate( modal, type ) {
  currentType = type;
  showState( modal, 'loading' );

  const loaderText = modal.querySelector( '.js-ai-loader-text' );
  const messages = [
    'Analysing composition…',
    'Selecting tone…',
    'Writing for you…',
    'Almost there…',
  ];

  let step = 0;
  const interval = setInterval( () => {
    if ( loaderText && step < messages.length ) {
      loaderText.textContent = messages[ step++ ];
    }
  }, 400 );

  const delay = 1400 + Math.random() * 600;

  setTimeout( () => {
    clearInterval( interval );
    const context = {
      technique: document.querySelector( '[name="artwork_technique"]' )?.value || 'oil',
      style:     document.querySelector( '[name="artwork_style"]' )?.value     || 'contemporary',
      mood:      document.querySelector( '[name="artwork_mood"]' )?.value      || 'calm',
      title:     document.querySelector( '[name="post_title"]' )?.value        || 'Untitled',
    };
    const text = generate( type, currentTone, context );
    setGeneratedText( modal, type, text );
  }, delay );
}

export function initAiAssistant() {
  const modal = document.getElementById( AI_ASSISTANT_MODAL_ID );
  if ( !modal ) return;

  // Tone selector
  modal.addEventListener( 'click', ( e ) => {
    const toneBtn = e.target.closest( '.js-ai-tone' );
    if ( toneBtn ) {
      modal.querySelectorAll( '.js-ai-tone' ).forEach( b => b.classList.remove( 'is-active' ) );
      toneBtn.classList.add( 'is-active' );
      currentTone = toneBtn.dataset.tone || 'gallery';
    }

    // Generate
    const genBtn = e.target.closest( '.js-ai-generate' );
    if ( genBtn ) {
      triggerGenerate( modal, genBtn.dataset.type );
    }

    // Regenerate
    if ( e.target.closest( '.js-ai-regenerate' ) && currentType ) {
      triggerGenerate( modal, currentType );
    }

    // Copy
    if ( e.target.closest( '.js-ai-copy' ) ) {
      const textEl = modal.querySelector( '.js-ai-generated-text' );
      if ( textEl?.textContent ) {
        navigator.clipboard?.writeText( textEl.textContent ).catch( () => {} );
        const btn = e.target.closest( '.js-ai-copy' );
        const orig = btn.textContent;
        btn.textContent = 'Copied!';
        setTimeout( () => { btn.textContent = orig; }, 2000 );
      }
    }

    // Edit manually
    if ( e.target.closest( '.js-ai-edit' ) ) {
      const textEl = modal.querySelector( '.js-ai-generated-text' );
      if ( textEl ) {
        const isEditable = textEl.contentEditable === 'true';
        textEl.contentEditable = isEditable ? 'false' : 'true';
        const btn = e.target.closest( '.js-ai-edit' );
        btn.textContent = isEditable ? 'Edit Manually' : 'Stop Editing';
        if ( !isEditable ) textEl.focus();
      }
    }
  } );

  // Open modal via global trigger
  document.addEventListener( 'click', ( e ) => {
    if ( e.target.closest( '.js-open-ai-assistant' ) ) {
      e.preventDefault();
      showState( modal, 'empty' );
      openModal( modal );
    }
  } );
}

// Public so other modules can open AI panel directly
export function openAiAssistant() {
  const modal = document.getElementById( AI_ASSISTANT_MODAL_ID );
  if ( modal ) openModal( modal );
}
