'use strict';

const fs = require('fs');
const path = require('path');
const data = require('../src/data/site-data');

const outDir = path.resolve(__dirname, '../static');
const year = new Date().getFullYear();

const pages = {
  home: 'index.html',
  gallery: 'gallery.html',
  artists: 'artists.html',
  exhibitions: 'exhibitions.html',
  journal: 'journal.html',
  favorites: 'favorites.html',
  curator: 'ai-curator.html',
  studio: 'studio.html',
  account: 'account.html',
  cart: 'cart.html',
  checkout: 'checkout.html',
  about: 'about.html',
  search: 'search.html'
};

const esc = (value = '') => String(value)
  .replace(/&/g, '&amp;')
  .replace(/</g, '&lt;')
  .replace(/>/g, '&gt;')
  .replace(/"/g, '&quot;')
  .replace(/'/g, '&#039;');

const money = (value, currency = '€') => `${currency} ${Number(value || 0).toLocaleString('fr-FR')}`;
const words = (text, limit = 24) => {
  const parts = String(text || '').split(/\s+/).filter(Boolean);
  return parts.length > limit ? `${parts.slice(0, limit).join(' ')}...` : parts.join(' ');
};

const byId = (list, id) => list.find((item) => item.id === id);
const artworkUrl = (art) => `artwork-${art.id}.html`;
const artistUrl = (artist) => `artist-${artist.id}.html`;
const exhibitionUrl = (exhibition) => `exhibition-${exhibition.id}.html`;
const journalUrl = (post) => `journal-${post.id}.html`;

function svg(name, attrs = '') {
  const map = {
    search: '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>',
    heart: '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>',
    bag: '<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>',
    user: '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
    close: '<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>',
    arrow: '<path d="m9 18 6-6-6-6"/>',
    eye: '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
    info: '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>',
    grid: '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
    wall: '<rect x="3" y="3" width="4" height="11" rx="1"/><rect x="10" y="3" width="11" height="5" rx="1"/><rect x="10" y="11" width="11" height="10" rx="1"/><rect x="3" y="17" width="4" height="4" rx="1"/>',
    pin: '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>',
    sparkle: '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>'
  };
  return `<svg ${attrs || 'width="20" height="20"'} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">${map[name] || ''}</svg>`;
}

function navList(className) {
  const nav = [
    ['Gallery', pages.gallery],
    ['Artists', pages.artists],
    ['Exhibitions', pages.exhibitions],
    ['Journal', pages.journal],
    ['AI Curator', pages.curator],
    ['Studio', pages.studio],
    ['About', pages.about]
  ];
  return `<ul class="${className}">${nav.map(([label, href]) => `<li class="menu-item"><a href="${href}">${label}</a></li>`).join('')}</ul>`;
}

function header() {
  return `
<header class="luma-header js-header" role="banner">
  <div class="luma-header__inner">
    <a href="${pages.home}" class="luma-header__logo" aria-label="Luma Gallery">
      <span class="luma-header__logo-text"><span class="luma-header__logo-luma">Luma</span><span class="luma-header__logo-gallery">Gallery</span></span>
    </a>
    <nav class="luma-header__nav" aria-label="Primary navigation">${navList('luma-header__nav-list')}</nav>
    <div class="luma-header__actions">
      <button class="luma-header__action-btn js-search-toggle" aria-label="Open search">${svg('search')}</button>
      <a href="${pages.favorites}" class="luma-header__action-btn luma-header__action-btn--favorites" aria-label="Favorites">${svg('heart')}<span class="luma-header__action-count js-favorites-count" style="display:none;">0</span></a>
      <a href="${pages.cart}" class="luma-header__action-btn luma-header__action-btn--cart" aria-label="Cart">${svg('bag')}<span class="luma-header__action-count js-cart-count" style="display:none;">0</span></a>
      <a href="${pages.account}" class="luma-header__action-btn" aria-label="Account">${svg('user')}</a>
      <a href="${pages.studio}" class="luma-button luma-button--ghost luma-header__cta">Become an Artist</a>
      <button class="luma-header__burger js-menu-toggle" aria-label="Toggle menu" aria-expanded="false"><span></span><span></span><span></span></button>
    </div>
  </div>
  <div class="luma-header__mobile-menu js-mobile-menu" aria-hidden="true">
    <nav class="luma-header__mobile-nav">
      ${navList('luma-header__mobile-nav-list')}
      <a href="${pages.studio}" class="luma-button luma-button--primary luma-header__mobile-cta">Become an Artist</a>
    </nav>
  </div>
  <div class="luma-header__search js-search-overlay" aria-hidden="true">
    <div class="luma-header__search-inner">
      <form class="luma-header__search-form" role="search" method="get" action="${pages.search}">
        <input class="luma-header__search-input" type="search" name="s" placeholder="Search artworks, artists..." autocomplete="off">
        <button type="submit" class="luma-header__search-submit" aria-label="Search">${svg('search')}</button>
      </form>
      <button class="luma-header__search-close js-search-close" aria-label="Close search">${svg('close', 'width="22" height="22"')}</button>
    </div>
  </div>
</header>`;
}

function footer() {
  return `
<footer class="luma-footer" role="contentinfo">
  <div class="luma-footer__inner">
    <div class="luma-footer__brand">
      <a href="${pages.home}" class="luma-footer__logo"><span class="luma-footer__logo-luma">Luma</span><span class="luma-footer__logo-gallery">Gallery</span></a>
      <p class="luma-footer__tagline">A digital gallery for artworks, artists, and immersive discovery.</p>
      <div class="luma-footer__social">
        <a href="#" class="luma-footer__social-link" aria-label="Instagram">${svg('sparkle', 'width="18" height="18"')}</a>
        <a href="#" class="luma-footer__social-link" aria-label="Pinterest">${svg('heart', 'width="18" height="18"')}</a>
        <a href="#" class="luma-footer__social-link" aria-label="Twitter / X">${svg('close', 'width="18" height="18"')}</a>
      </div>
    </div>
    <div class="luma-footer__nav-col">
      <h4 class="luma-footer__nav-title">Explore</h4>
      <ul class="luma-footer__nav-list">
        <li><a href="${pages.gallery}">Gallery</a></li><li><a href="${pages.artists}">Artists</a></li><li><a href="${pages.exhibitions}">Exhibitions</a></li><li><a href="${pages.journal}">Journal</a></li><li><a href="${pages.curator}">AI Curator</a></li>
      </ul>
    </div>
    <div class="luma-footer__nav-col">
      <h4 class="luma-footer__nav-title">Artists</h4>
      <ul class="luma-footer__nav-list">
        <li><a href="${pages.studio}">Artist Studio</a></li><li><a href="${pages.artists}">Meet the Artists</a></li><li><a href="${pages.about}">About Luma</a></li>
      </ul>
    </div>
    <div class="luma-footer__nav-col">
      <h4 class="luma-footer__nav-title">Newsletter</h4>
      <p class="luma-footer__newsletter-text">Get curated picks and new exhibitions delivered to your inbox.</p>
      <form class="luma-footer__newsletter-form js-newsletter-form">
        <input type="email" class="luma-footer__newsletter-input" placeholder="your@email.com" required>
        <button type="submit" class="luma-button luma-button--primary luma-footer__newsletter-btn">Subscribe</button>
      </form>
    </div>
  </div>
  <div class="luma-footer__wordmark-row"><span class="luma-footer__wordmark js-distort-text" aria-hidden="true">LUMA GALLERY</span></div>
  <div class="luma-footer__bottom">
    <div class="luma-footer__bottom-inner">
      <p class="luma-footer__copyright">&copy; ${year} Luma Gallery. All rights reserved.</p>
      <p class="luma-footer__disclaimer">Static demo project. No real payments are processed. AI features are rule-based simulations.</p>
      <nav class="luma-footer__legal" aria-label="Legal navigation"><ul class="luma-footer__legal-list"><li><a href="${pages.about}">About</a></li></ul></nav>
    </div>
  </div>
</footer>`;
}

function layout(title, content, bodyClass = '') {
  const lumaData = {
    siteUrl: '',
    themeUrl: 'assets',
    isWooActive: 'no',
    cartUrl: pages.cart,
    currency: '€',
    routes: pages
  };
  return `<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Luma Gallery static demo for GitHub Pages">
  <title>${esc(title)} | Luma Gallery</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400&family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/main.min.css">
  <script>window.lumaData=${JSON.stringify(lumaData)};</script>
</head>
<body class="${esc(bodyClass)}">
${header()}
${content}
${footer()}
${modalViewInRoom()}
${modalAiAssistant()}
<script src="assets/js/main.min.js"></script>
</body>
</html>`;
}

function artworkCard(art, index = 0, opts = {}) {
  const href = artworkUrl(art);
  const price = opts.price || money(opts.priceRaw ?? art.priceRaw);
  const priceRaw = opts.priceRaw ?? art.priceRaw;
  const id = art.id;
  const statusLabel = { available: 'Available', reserved: 'Reserved', sold: 'Sold' }[art.status] || 'Available';
  const featured = opts.featured ? ' luma-artwork-card--featured' : '';
  return `
<article class="luma-artwork-card${featured}" data-artwork-id="${esc(id)}" data-index="${index}" data-title="${esc(`${art.title} ${art.artist}`)}" data-artist="${esc(art.artist)}" data-price="${esc(priceRaw)}" data-style="${esc(art.style || '')}" data-mood="${esc(art.mood || '')}" data-technique="${esc(art.technique || '')}" data-color="${esc(art.color || '')}" data-room="${esc(art.room || '')}" data-size="${esc(art.size || '')}" data-status="${esc(art.status || 'available')}" data-date="${esc(art.date || 0)}">
  <div class="luma-artwork-card__image-wrap">
    <span class="luma-artwork-card__index" aria-hidden="true">N${String(index + 1).padStart(2, '0')}</span>
    <a href="${href}" class="luma-artwork-card__image-link" tabindex="-1">
      <div class="luma-artwork-card__placeholder" style="background:${esc(art.gradient)};" aria-label="${esc(art.title)}"></div>
      <span class="luma-artwork-card__scrim" aria-hidden="true"></span>
    </a>
    ${art.status !== 'available' ? `<span class="luma-badge luma-badge--${esc(art.status)} luma-artwork-card__status-badge">${statusLabel}</span>` : ''}
    <div class="luma-artwork-card__overlay" aria-hidden="true">
      <div class="luma-artwork-card__overlay-actions">
        <a href="${href}" class="luma-button luma-button--accent luma-button--sm">View Artwork</a>
        ${art.status === 'available' ? `<button type="button" class="luma-button luma-button--ghost-light luma-button--sm js-add-to-cart" data-cart-id="${esc(id)}" data-title="${esc(art.title)}" data-artist="${esc(art.artist)}" data-price="${esc(priceRaw)}" data-currency="€" data-image="" data-gradient="${esc(art.gradient)}" data-url="${href}" data-status="${esc(art.status)}" data-size="${esc(art.size)}" data-technique="${esc(art.technique)}">Add to Cart</button>` : ''}
        <button class="luma-artwork-card__quick-view js-quick-view js-view-in-room" data-artwork-id="${esc(id)}" data-artwork-title="${esc(art.title)}" data-artwork-size="${esc(art.size)}" data-artwork-url="${href}" data-artwork-gradient="${esc(art.gradient)}" aria-label="Quick view">${svg('eye', 'width="16" height="16"')}</button>
      </div>
    </div>
    <button class="luma-artwork-card__favorite js-toggle-favorite" data-artwork-id="${esc(id)}" data-type="artwork" aria-label="Add to favorites" aria-pressed="false">${svg('heart', 'width="18" height="18"')}</button>
  </div>
  <div class="luma-artwork-card__body">
    <div class="luma-artwork-card__meta"><span class="luma-artwork-card__artist">${esc(art.artist)}</span></div>
    <h3 class="luma-artwork-card__title"><a href="${href}">${esc(art.title)}</a></h3>
    <div class="luma-artwork-card__footer">
      <span class="luma-artwork-card__price">${esc(price)}</span>
      <span class="luma-artwork-card__status luma-artwork-card__status--${esc(art.status)}">${statusLabel}</span>
    </div>
  </div>
</article>`;
}

function artistCard(artist, index = 0) {
  return `
<article class="luma-artist-card" data-artist-id="${esc(artist.id)}" data-index="${index}" data-style="${esc(artist.style || '')}">
  <a href="${artistUrl(artist)}" class="luma-artist-card__image-link" tabindex="-1">
    <div class="luma-artist-card__image-wrap">
      <div class="luma-artist-card__avatar-placeholder" style="background:${esc(artist.gradient)};"><span class="luma-artist-card__initial">${esc(artist.name.charAt(0))}</span></div>
      <span class="luma-artist-card__scrim" aria-hidden="true"></span>
      <span class="luma-artist-card__index" aria-hidden="true">N${String(index + 1).padStart(2, '0')}</span>
      <span class="luma-artist-card__view" aria-hidden="true">View profile &rarr;</span>
    </div>
  </a>
  <div class="luma-artist-card__body">
    <div class="luma-artist-card__head">
      <h3 class="luma-artist-card__name"><a href="${artistUrl(artist)}">${esc(artist.name)}</a></h3>
      <span class="luma-artist-card__location">${svg('pin', 'width="11" height="11"')} ${esc(artist.location)}</span>
    </div>
    <p class="luma-artist-card__bio">${esc(words(artist.bio, 16))}</p>
    <div class="luma-artist-card__footer">
      <span class="luma-artist-card__count">${artist.artworks} works</span>
      <button class="luma-artist-card__follow js-toggle-favorite" data-artist-id="${esc(artist.id)}" data-type="artist" aria-label="Follow artist" aria-pressed="false"><span class="luma-artist-card__follow-icon" aria-hidden="true">+</span><span class="luma-artist-card__follow-text">Follow</span></button>
    </div>
  </div>
</article>`;
}

function exhibitionCard(exhibition, index = 0) {
  return `
<article class="luma-exhibition-card" data-exhibition-id="${esc(exhibition.id)}" data-index="${index}" data-moods="${esc(exhibition.moodTags.join(' '))}">
  <a href="${exhibitionUrl(exhibition)}" class="luma-exhibition-card__cover-link">
    <div class="luma-exhibition-card__cover">
      <div class="luma-exhibition-card__cover-placeholder" style="background:${esc(exhibition.gradient)};"></div>
      <span class="luma-exhibition-card__index" aria-hidden="true">EXH&middot;${String(index + 1).padStart(2, '0')}</span>
      <div class="luma-exhibition-card__cover-overlay" aria-hidden="true"></div>
      <div class="luma-exhibition-card__content">
        <div class="luma-exhibition-card__mood-tags">${exhibition.moodTags.slice(0, 3).map((tag) => `<span class="luma-exhibition-card__mood">${esc(tag)}</span>`).join('')}</div>
        <span class="luma-exhibition-card__subtitle">${esc(exhibition.subtitle)}</span>
        <h3 class="luma-exhibition-card__title"><a href="${exhibitionUrl(exhibition)}">${esc(exhibition.title)}</a></h3>
        <p class="luma-exhibition-card__curator">${esc(words(exhibition.curator, 16))}</p>
        <div class="luma-exhibition-card__bottom"><span class="luma-exhibition-card__count">${exhibition.worksCount} works</span><span class="luma-exhibition-card__enter">Enter exhibition ${svg('arrow', 'width="14" height="14"')}</span></div>
      </div>
    </div>
  </a>
  <button class="luma-exhibition-card__favorite js-toggle-favorite" data-exhibition-id="${esc(exhibition.id)}" data-type="exhibition" aria-label="Save exhibition" aria-pressed="false">${svg('heart', 'width="18" height="18"')}</button>
</article>`;
}

function journalCard(post, index = 0) {
  return `
<article class="luma-post-card luma-journal-card js-journal-card" data-topic="${esc(post.topicSlug)}">
  <a href="${journalUrl(post)}" class="luma-post-card__image-link luma-journal-card__media">
    <span class="luma-journal-card__placeholder" style="background:${esc(post.gradient)};" aria-hidden="true"></span>
    <span class="luma-journal-card__topic">${esc(post.topic)}</span>
  </a>
  <div class="luma-post-card__body">
    <div class="luma-journal-card__meta"><span class="luma-journal-card__date">${esc(post.date)}</span><span class="luma-journal-card__artist">${esc(post.artist)}</span></div>
    <h3 class="luma-post-card__title"><a href="${journalUrl(post)}">${esc(post.title)}</a></h3>
    <p class="luma-post-card__excerpt">${esc(post.excerpt)}</p>
    <a href="${journalUrl(post)}" class="luma-journal-card__cta">Read story ${svg('arrow', 'width="14" height="14"')}</a>
  </div>
</article>`;
}

function pageHero(kind, title, subtitle, word = title.toUpperCase()) {
  return `
<section class="luma-page-hero luma-page-hero--${kind}" aria-label="${esc(title)}">
  <div class="luma-container"><div class="luma-page-hero__inner"><p class="luma-page-hero__eyebrow">Luma Gallery</p><h1 class="luma-page-hero__title">${esc(title)}</h1><p class="luma-page-hero__subtitle">${esc(subtitle)}</p></div></div>
  <div class="luma-page-hero__word js-distort-text" aria-hidden="true">${esc(word)}</div>
</section>`;
}

function galleryWalkFrames(list = data.artworks.slice(0, 8), hidden = false) {
  return `
<div class="luma-gallery-walk-section__strip js-gallery-strip"${hidden ? ' style="display:none;"' : ''}>
  <div class="luma-gallery-walk-section__track">
    ${list.map((art, i) => `<div class="luma-gallery-walk-section__frame" data-artwork-id="${esc(art.id)}" data-index="${i}" data-artist="${esc(art.artist)}" data-price="${esc(money(art.priceRaw))}" data-url="${artworkUrl(art)}"><div class="luma-gallery-walk-section__frame-inner" style="background:${esc(art.gradient)};"></div><div class="luma-gallery-walk-section__frame-label"><span>${esc(art.title)}</span></div></div>`).join('')}
  </div>
</div>`;
}

function galleryWalkOverlay() {
  return `
<div class="luma-gallery-walk js-gallery-walk" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Gallery Walk">
  <div class="luma-gallery-walk__wall js-gw-wall"></div>
  <div class="luma-gallery-walk__spotlight js-gw-spotlight" aria-hidden="true"></div>
  <div class="luma-gallery-walk__controls">
    <button class="luma-gallery-walk__btn luma-gallery-walk__btn--prev js-gw-prev" aria-label="Previous"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="m15 18-6-6 6-6"/></svg></button>
    <div class="luma-gallery-walk__progress"><span class="js-gw-current">1</span><span class="luma-gallery-walk__sep">/</span><span class="js-gw-total">8</span></div>
    <button class="luma-gallery-walk__btn luma-gallery-walk__btn--next js-gw-next" aria-label="Next">${svg('arrow', 'width="22" height="22"')}</button>
  </div>
  <div class="luma-gallery-walk__info js-gw-info">
    <h3 class="luma-gallery-walk__info-title js-gw-title"></h3><p class="luma-gallery-walk__info-artist js-gw-artist"></p><p class="luma-gallery-walk__info-price js-gw-price"></p>
    <div class="luma-gallery-walk__info-actions"><a href="#" class="luma-button luma-button--primary luma-button--sm js-gw-view-link">View Details</a><button class="luma-button luma-button--ghost luma-button--sm js-gw-favorite js-toggle-favorite" data-type="artwork">Add to Favorites</button></div>
  </div>
  <div class="luma-gallery-walk__thumbs js-gw-thumbs"></div>
  <button class="luma-gallery-walk__close js-gw-close" aria-label="Close Gallery Walk">${svg('close', 'width="22" height="22"')}</button>
</div>`;
}

function homePage() {
  const featured = data.exhibitions[0];
  return layout('Home', `
<main class="luma-main luma-main--home" id="main-content">
  <section class="luma-hero" id="hero" aria-label="Hero">
    <div class="luma-hero__bg" aria-hidden="true"></div>
    <div class="luma-hero__object-wrap js-hero-object"><div class="luma-hero__object"><img src="assets/images/hero/hero-item.png" alt="Featured artwork" loading="eager" decoding="async" draggable="false"></div></div>
    <div class="luma-hero__glass" aria-hidden="true"></div><div class="luma-hero__circle luma-hero__circle--lg js-hero-circle" aria-hidden="true"></div><div class="luma-hero__circle luma-hero__circle--sm js-hero-circle" aria-hidden="true"></div>
    <div class="luma-hero__content-wrap">
      <div class="luma-hero__word luma-hero__word--top" aria-hidden="true">DISCOVER</div><div class="luma-hero__word luma-hero__word--bottom" aria-hidden="true">GALLERY</div>
      <div class="luma-hero__intro"><div class="luma-hero__intro-line" aria-hidden="true"></div><p class="luma-hero__intro-label">Luma Gallery - Original Works</p><p class="luma-hero__intro-text">Explore immersive exhibitions, follow independent artists, and collect original artworks from a curated digital gallery.</p><a href="${pages.gallery}" class="luma-hero__intro-cta">Enter the Gallery &rarr;</a><div class="luma-hero__intro-stats" aria-label="Gallery statistics"><span><strong data-count="120" data-suffix="+">120+</strong> Artworks</span><span class="luma-hero__intro-sep" aria-hidden="true">/</span><span><strong data-count="36">36</strong> Artists</span><span class="luma-hero__intro-sep" aria-hidden="true">/</span><span><strong data-count="8">8</strong> Exhibitions</span></div></div>
    </div>
  </section>
  <section class="luma-section luma-gallery-walk-section" id="gallery-walk"><div class="luma-container"><div class="luma-section__header luma-section__header--split"><div><span class="luma-section__label">Immersive Experience</span><h2 class="luma-heading luma-heading--section">Walk Through the Gallery</h2></div><button class="luma-button luma-button--primary js-start-gallery-walk" data-collection="featured">Start Gallery Walk ${svg('arrow', 'width="16" height="16"')}</button></div></div>${galleryWalkFrames()}<div class="luma-container"><p class="luma-gallery-walk-section__hint">Scroll horizontally or press Start to explore in fullscreen.</p></div></section>
  ${galleryWalkOverlay()}
  <section class="luma-section luma-featured-exhibition" id="featured-exhibition"><div class="luma-container"><div class="luma-section__header"><span class="luma-section__label">Exhibition of the Week</span></div><div class="luma-featured-exhibition__inner"><a href="${exhibitionUrl(featured)}" class="luma-featured-exhibition__cover-link"><div class="luma-featured-exhibition__cover"><div class="luma-featured-exhibition__cover-placeholder" style="background:${esc(featured.gradient)};"></div><div class="luma-featured-exhibition__cover-overlay"></div><div class="luma-featured-exhibition__tags">${featured.moodTags.map((tag) => `<span class="luma-badge luma-badge--mood">${esc(tag)}</span>`).join('')}</div></div></a><div class="luma-featured-exhibition__info"><span class="luma-featured-exhibition__subtitle">Exhibition of the Week</span><h2 class="luma-heading luma-heading--section luma-featured-exhibition__title">${esc(featured.title)}</h2><p class="luma-featured-exhibition__curator">${esc(featured.curator)}</p><div class="luma-featured-exhibition__meta"><span>${featured.worksCount} artworks</span></div><div class="luma-featured-exhibition__actions"><a href="${exhibitionUrl(featured)}" class="luma-button luma-button--primary">Enter Exhibition</a><a href="${pages.exhibitions}" class="luma-button luma-button--ghost">All Exhibitions</a></div></div></div></div></section>
  <section class="luma-section luma-popular-artworks" id="popular-artworks"><div class="luma-container"><div class="luma-section__header luma-section__header--split"><div><span class="luma-section__label">Most Collected</span><h2 class="luma-heading luma-heading--section">Popular Artworks</h2></div><a href="${pages.gallery}" class="luma-button luma-button--ghost">View All</a></div><div class="luma-artwork-grid luma-artwork-grid--4col">${data.artworks.slice(0, 8).map(artworkCard).join('')}</div></div></section>
  <section class="luma-section luma-mood-picker" id="mood-picker"><div class="luma-container"><div class="luma-section__header"><span class="luma-section__label">Find Your Art</span><h2 class="luma-heading luma-heading--section">Browse by Mood</h2><p class="luma-section__sub">How do you want to feel? Choose a mood and discover artworks that match your state of mind.</p></div><div class="luma-mood-picker__grid">${data.moods.map(([slug, label, icon, desc]) => `<a href="${pages.gallery}?mood=${slug}" class="luma-mood-picker__item js-mood-item" data-mood="${slug}"><span class="luma-mood-picker__icon" aria-hidden="true">${esc(icon)}</span><span class="luma-mood-picker__label">${esc(label)}</span><span class="luma-mood-picker__desc">${esc(desc)}</span></a>`).join('')}</div></div></section>
  <section class="luma-section luma-new-artists" id="new-artists"><div class="luma-container"><div class="luma-section__header luma-section__header--split"><div><span class="luma-section__label">Recently Joined</span><h2 class="luma-heading luma-heading--section">New Artists</h2></div><a href="${pages.artists}" class="luma-button luma-button--ghost">All Artists</a></div><div class="luma-artist-grid luma-artist-grid--3col">${data.artists.map(artistCard).join('')}</div><div class="luma-new-artists__cta"><p class="luma-new-artists__cta-text">Are you an artist? Join Luma Gallery and reach new collectors.</p><a href="${pages.studio}" class="luma-button luma-button--primary">Become an Artist</a></div></div></section>
  <section class="luma-distort" aria-label="Curated selection"><span class="luma-distort__ghost" data-parallax="-0.07" aria-hidden="true">LUMA</span><div class="luma-container"><div class="luma-distort__inner"><p class="luma-distort__eyebrow">Hand-picked</p><div class="luma-distort__word js-distort-text">CURATED</div><p class="luma-distort__sub">Every work is chosen with intent. Move your cursor across the type like light shifting over a canvas.</p></div></div></section>
  <section class="luma-section luma-how-it-works" id="how-it-works"><div class="luma-container"><div class="luma-section__header"><span class="luma-section__label">The Process</span><h2 class="luma-heading luma-heading--section">How Luma Works</h2></div><div class="luma-how-it-works__steps" data-reveal-children="120">${[['01','Discover','Browse curated exhibitions, explore by mood, or let the AI Curator suggest artworks tailored to your space and taste.'],['02','Connect','Follow artists, read their stories, explore studio posts, and understand the inspiration behind each work.'],['03','Collect','Save to your favorites, view art in a virtual room, and make a demo purchase to explore the full collector experience.']].map(([num,title,text]) => `<div class="luma-how-it-works__step"><span class="luma-how-it-works__num">${num}</span><h3 class="luma-how-it-works__title">${title}</h3><p class="luma-how-it-works__text">${text}</p></div>`).join('')}</div></div></section>
  <section class="luma-section luma-ai-curator-teaser luma-section--dark" id="ai-curator-teaser"><div class="luma-container"><div class="luma-ai-curator-teaser__inner"><div class="luma-ai-curator-teaser__copy"><span class="luma-section__label luma-section__label--light">Intelligent Curation</span><h2 class="luma-heading luma-heading--section">Let the AI Curator find art for you.</h2><p class="luma-ai-curator-teaser__desc">Describe your mood, budget, or room. The Luma Curator will suggest artworks tailored to your space and sensibility.</p><div class="luma-ai-curator-teaser__chips" aria-hidden="true"><span class="luma-chip">calm atmosphere</span><span class="luma-chip">under €500</span><span class="luma-chip">living room</span><span class="luma-chip">soft palette</span></div><div class="luma-ai-curator-teaser__disclaimer">${svg('info', 'width="14" height="14"')} Demo AI. Rule-based suggestions only.</div><a href="${pages.curator}" class="luma-button luma-button--primary luma-button--lg">Try the AI Curator ${svg('arrow', 'width="16" height="16"')}</a></div><div class="luma-ai-curator-teaser__form-preview" aria-hidden="true"><div class="luma-ai-curator-teaser__panel"><div class="luma-ai-curator-teaser__panel-header"><span class="luma-ai-curator-teaser__panel-dot"></span><span class="luma-ai-curator-teaser__panel-dot"></span><span class="luma-ai-curator-teaser__panel-dot"></span><span class="luma-ai-curator-teaser__panel-title">AI Curator</span></div><div class="luma-ai-curator-teaser__panel-body"><div class="luma-ai-curator-teaser__field"><label>Mood</label><div class="luma-ai-curator-teaser__select-mock">calm</div></div><div class="luma-ai-curator-teaser__loader"><div class="luma-ai-curator-teaser__loader-bar"></div><span>Finding artworks...</span></div><div class="luma-ai-curator-teaser__result-row">${data.artworks.slice(0,3).map((art) => `<div class="luma-ai-curator-teaser__result-frame" style="background:${esc(art.gradient)};"></div>`).join('')}</div><p class="luma-ai-curator-teaser__result-summary">Found 6 calm artworks under €800.</p></div></div></div></div></div></section>
  <section class="luma-section luma-cta luma-cta--dark" id="cta"><div class="luma-container"><div class="luma-cta__inner"><h2 class="luma-heading luma-heading--display luma-cta__heading">Ready to discover your next favorite artwork?</h2><p class="luma-cta__subtext">Original works by independent artists. Curated exhibitions. Immersive viewing. No pressure, just art.</p><div class="luma-cta__actions" data-reveal-children="100"><a href="${pages.gallery}" class="luma-button luma-button--primary luma-button--lg">Enter the Gallery</a><a href="${pages.artists}" class="luma-button luma-button--ghost luma-button--lg">Meet the Artists</a></div></div></div></section>
</main>`, 'luma-home');
}

function galleryPage() {
  const styles = ['Abstract', 'Figurative', 'Landscape', 'Minimalist', 'Expressionist'];
  const moods = ['Calm', 'Melancholic', 'Energetic', 'Dark', 'Romantic', 'Bright', 'Minimal'];
  const techniques = ['Oil', 'Watercolor', 'Acrylic', 'Ink', 'Mixed Media', 'Pastel'];
  const colorSwatches = { Black: '#1d1d1f', White: '#f5f5f5', Red: '#c0392b', Blue: '#2980b9', Green: '#27ae60', Yellow: '#f1c40f', 'Earth Tones': '#8B6914' };
  return layout('Gallery', `
<main class="luma-main luma-page luma-page--gallery" id="main-content">
${pageHero('gallery', 'Gallery', 'Browse curated artworks by mood, style, technique, and artist')}
<section class="luma-gallery-body"><div class="luma-container">
  <div class="luma-gallery-topbar"><div class="luma-gallery-topbar__left"><button class="luma-button luma-button--ghost luma-button--sm js-filter-toggle" aria-expanded="false" aria-controls="gallery-filters">Filters</button><span class="luma-gallery-topbar__count js-gallery-count">${data.artworks.length} artworks</span></div><div class="luma-gallery-topbar__right"><select class="luma-select js-sort-select" aria-label="Sort artworks"><option value="">Newest first</option><option value="price-asc">Price: Low to High</option><option value="price-desc">Price: High to Low</option></select><div class="luma-view-toggle" role="group" aria-label="View mode"><button class="luma-view-toggle__btn is-active js-view-toggle" data-view="grid" aria-label="Grid view">${svg('grid', 'width="14" height="14" fill="currentColor" stroke="none"')}</button><button class="luma-view-toggle__btn js-view-toggle" data-view="wall" aria-label="Gallery wall view">${svg('wall', 'width="14" height="14" fill="currentColor" stroke="none"')}</button></div></div></div>
  <div class="luma-gallery-layout">
    <aside class="luma-gallery-sidebar js-filter-panel" id="gallery-filters" aria-label="Filter artworks"><form class="luma-filter-form js-filter-form" novalidate>
      <div class="luma-filter-group"><label class="luma-filter-group__label" for="filter-search">Search</label><input type="search" id="filter-search" name="title" class="luma-input" placeholder="Title or artist..." autocomplete="off"></div>
      <div class="luma-filter-group"><label class="luma-filter-group__label" for="filter-status">Availability</label><select id="filter-status" name="status" class="luma-select luma-select--full"><option value="">All</option><option value="available">Available</option><option value="reserved">Reserved</option><option value="sold">Sold</option></select></div>
      <div class="luma-filter-group"><label class="luma-filter-group__label" for="filter-price">Max Price</label><input type="range" id="filter-price" name="priceMax" class="luma-range js-price-range" min="0" max="10000" step="100" value="10000"><div class="luma-filter-range__labels"><span>€0</span><span class="js-price-display">€10 000+</span></div></div>
      <div class="luma-filter-group"><label class="luma-filter-group__label" for="filter-style">Style</label><select id="filter-style" name="style" class="luma-select luma-select--full"><option value="">All styles</option>${styles.map((x) => `<option value="${x}">${x}</option>`).join('')}</select></div>
      <div class="luma-filter-group"><label class="luma-filter-group__label" for="filter-mood">Mood</label><select id="filter-mood" name="mood" class="luma-select luma-select--full"><option value="">Any mood</option>${moods.map((x) => `<option value="${x}">${x}</option>`).join('')}</select></div>
      <div class="luma-filter-group"><label class="luma-filter-group__label" for="filter-technique">Technique</label><select id="filter-technique" name="technique" class="luma-select luma-select--full"><option value="">Any technique</option>${techniques.map((x) => `<option value="${x}">${x}</option>`).join('')}</select></div>
      <div class="luma-filter-group"><span class="luma-filter-group__label">Color</span><div class="luma-color-filters" role="radiogroup" aria-label="Color filter">${Object.entries(colorSwatches).map(([label, hex]) => `<label class="luma-color-filter" title="${label}"><input type="radio" name="color" value="${label}" class="luma-color-filter__input" aria-label="${label}"><span class="luma-color-filter__swatch" style="background:${hex};"></span></label>`).join('')}<label class="luma-color-filter" title="Any color"><input type="radio" name="color" value="" checked class="luma-color-filter__input" aria-label="Any color"><span class="luma-color-filter__swatch luma-color-filter__swatch--any">x</span></label></div></div>
      <div class="luma-filter-group"><label class="luma-filter-group__label" for="filter-artist">Artist</label><select id="filter-artist" name="artist" class="luma-select luma-select--full"><option value="">All artists</option>${data.artists.map((a) => `<option value="${esc(a.name)}">${esc(a.name)}</option>`).join('')}</select></div>
      <button type="reset" class="luma-button luma-button--ghost luma-button--sm luma-filter-reset">Reset all filters</button>
    </form></aside>
    <div class="luma-gallery-main"><div class="luma-gallery-empty js-gallery-empty" style="display:none;" aria-live="polite" role="status"><h3>No artworks found</h3><p>Try adjusting your filters to discover more works.</p></div><div class="luma-artwork-grid luma-artwork-grid--4col js-artwork-grid" data-view="grid">${data.artworks.map(artworkCard).join('')}</div></div>
  </div>
</div></section>
</main>`);
}

function artistsPage() {
  const featured = data.artists[0];
  const filters = ['Abstract', 'Figurative', 'Landscape', 'Minimalist', 'Expressionist', 'Mixed Media'];
  return layout('Artists', `
<main class="luma-main" id="main-content">
${pageHero('artists', 'Artists', 'Meet the artists behind the works')}
<section class="luma-artists-featured" aria-label="Featured artist"><div class="luma-artists-featured__inner"><div class="luma-artists-featured__text"><p class="luma-artists-featured__label">Featured Artist</p><h2 class="luma-artists-featured__name">${esc(featured.name)}</h2><p class="luma-artists-featured__location">${svg('pin', 'width="12" height="12"')} ${esc(featured.location)}</p><p class="luma-artists-featured__bio">${esc(words(featured.bio, 30))}</p><div class="luma-artists-featured__actions"><a href="${artistUrl(featured)}" class="luma-button luma-button--ghost-light">View Profile</a><button class="luma-button luma-button--ghost-light js-toggle-favorite" data-artist-id="${esc(featured.id)}" data-type="artist" aria-pressed="false">Follow</button></div></div><div class="luma-artists-featured__image"><div style="background:${esc(featured.gradient)};width:100%;height:100%;min-height:400px;display:flex;align-items:center;justify-content:center;"><span style="font-family:var(--luma-font-heading,Georgia,serif);font-size:clamp(60px,12vw,140px);font-weight:300;color:rgba(255,255,255,.18);">${esc(featured.name.charAt(0))}</span></div></div></div></section>
<div class="luma-artists-toolbar"><div class="luma-artists-toolbar__inner"><div class="luma-artists-search">${svg('search', 'class="luma-artists-search__icon" width="16" height="16"')}<input type="search" class="luma-input luma-artists-search__input js-artist-search" placeholder="Search artists..." autocomplete="off" aria-label="Search artists"></div><div class="luma-style-filter" role="group" aria-label="Filter by style"><span class="luma-style-filter__label">Style:</span><button class="luma-style-chip is-active js-style-chip" data-style="">All</button>${filters.map((x) => `<button class="luma-style-chip js-style-chip" data-style="${x}">${x}</button>`).join('')}</div></div></div>
<section class="luma-artists-body"><div class="luma-artists-header"><h2 class="luma-artists-header__title">All Artists</h2><span class="luma-artists-header__count js-artists-count">${data.artists.length} artists</span></div><div class="luma-artists-grid js-artists-grid" data-reveal-children="55">${data.artists.map(artistCard).join('')}</div></section>
<section class="luma-artists-cta"><div class="luma-artists-cta__inner"><p class="luma-artists-cta__eyebrow">Join Luma Gallery</p><h2 class="luma-artists-cta__title">Become an Artist</h2><p class="luma-artists-cta__text">Share your work with a curated audience of collectors and art lovers.</p><a href="${pages.studio}" class="luma-button luma-button--primary luma-button--lg">Open Studio</a></div></section>
</main>`);
}

function exhibitionsPage() {
  const featured = data.exhibitions[0];
  const moodFilters = ['Calm', 'Dark', 'Melancholic', 'Romantic', 'Bright', 'Abstract', 'Atmospheric', 'Minimal'];
  return layout('Exhibitions', `
<main class="luma-main" id="main-content">
${pageHero('exhibitions', 'Curated Exhibitions', 'Explore artworks through mood, theme, and visual storytelling', 'EXHIBITIONS')}
<section class="luma-exhibitions-featured" aria-label="Featured exhibition"><div style="position:absolute;inset:0;background:${esc(featured.gradient)};opacity:.35;"></div><div class="luma-exhibitions-featured__gradient" aria-hidden="true"></div><div class="luma-exhibitions-featured__content"><p class="luma-exhibitions-featured__eyebrow">Now Showing</p><h2 class="luma-exhibitions-featured__title">${esc(featured.title)}</h2><p class="luma-exhibitions-featured__subtitle">${esc(featured.subtitle)}</p><p class="luma-exhibitions-featured__curator">${esc(words(featured.curator, 28))}</p><div class="luma-exhibitions-featured__meta"><span class="luma-badge luma-badge--tag">${featured.worksCount} works</span>${featured.moodTags.map((tag) => `<span class="luma-badge luma-badge--mood">${esc(tag)}</span>`).join('')}<a href="${exhibitionUrl(featured)}" class="luma-button luma-button--ghost-light">Enter Exhibition</a></div></div></section>
<nav class="luma-exhibitions-filter" aria-label="Filter by mood"><div class="luma-exhibitions-filter__inner"><span class="luma-exhibitions-filter__label">Mood:</span><button class="luma-mood-chip is-active js-mood-chip" data-mood="">All</button>${moodFilters.map((mood) => `<button class="luma-mood-chip js-mood-chip" data-mood="${mood.toLowerCase()}">${mood}</button>`).join('')}</div></nav>
<section class="luma-exhibitions-body"><div class="luma-exhibitions-body__header"><h2 class="luma-exhibitions-body__title">All Exhibitions</h2><span class="luma-exhibitions-body__count js-exhibitions-count">${data.exhibitions.length} exhibitions</span></div><div class="luma-exhibitions-grid js-exhibitions-grid">${data.exhibitions.map(exhibitionCard).join('')}</div></section>
${galleryWalkFrames(data.artworks.slice(0, 8), true)}
${galleryWalkOverlay()}
<section class="luma-exhibitions-cta"><div class="luma-exhibitions-cta__inner"><p class="luma-exhibitions-cta__eyebrow">Immersive Experience</p><h2 class="luma-exhibitions-cta__title">Start Gallery Walk</h2><p class="luma-exhibitions-cta__text">Step into a curated sequence of artworks, guided by mood and movement.</p><button class="luma-button luma-button--ghost-light luma-button--lg js-start-gallery-walk">Begin Gallery Walk</button></div></section>
</main>`);
}

function journalPage() {
  const featured = data.journal[0];
  const topics = [...new Map(data.journal.map((post) => [post.topicSlug, post.topic])).entries()];
  return layout('Journal', `
<main class="luma-main luma-page luma-page--journal" id="main-content">
<section class="luma-journal-hero"><div class="luma-container"><p class="luma-journal-hero__eyebrow">The Luma Journal</p><h1 class="luma-journal-hero__title">Journal</h1><p class="luma-journal-hero__subtitle">Stories from artists, exhibitions, and the world of contemporary art.</p></div><div class="luma-journal-hero__word js-distort-text" aria-hidden="true">JOURNAL</div></section>
<section class="luma-journal-featured-wrap"><div class="luma-container"><article class="luma-journal-featured" data-reveal><a href="${journalUrl(featured)}" class="luma-journal-featured__media"><span class="luma-journal-featured__placeholder" style="background:${esc(featured.gradient)};" aria-hidden="true"></span><span class="luma-journal-featured__topic">${esc(featured.topic)}</span></a><div class="luma-journal-featured__body"><p class="luma-journal-featured__label">Featured story</p><h2 class="luma-journal-featured__title"><a href="${journalUrl(featured)}">${esc(featured.title)}</a></h2><p class="luma-journal-featured__excerpt">${esc(featured.excerpt)}</p><div class="luma-journal-featured__meta"><span class="luma-journal-featured__date">${esc(featured.date)}</span><span class="luma-journal-featured__artist">${esc(featured.artist)}</span></div><a href="${journalUrl(featured)}" class="luma-button luma-button--primary luma-journal-featured__cta">Read story</a></div></article></div></section>
<section class="luma-journal-filterbar"><div class="luma-container"><div class="luma-journal-filters" role="group" aria-label="Filter stories by topic"><button class="luma-mood-chip js-journal-filter is-active" data-topic="all" aria-pressed="true">All</button>${topics.map(([slug, label]) => `<button class="luma-mood-chip js-journal-filter" data-topic="${esc(slug)}" aria-pressed="false">${esc(label)}</button>`).join('')}</div></div></section>
<section class="luma-journal-body"><div class="luma-container"><div class="luma-post-grid js-journal-grid" data-reveal-children="60">${data.journal.slice(1).map(journalCard).join('')}</div><div class="luma-empty-state js-journal-empty" hidden><p class="luma-empty-state__text">No stories in this topic yet.</p><a href="${pages.journal}" class="luma-button luma-button--ghost">View all stories</a></div></div></section>
</main>`);
}

function favoritesPage() {
  const tabs = [
    ['artworks', 'Artworks', 'You have not saved any artworks yet.', 'Explore Gallery', pages.gallery],
    ['artists', 'Artists', 'You have not followed any artists yet.', 'Meet Artists', pages.artists],
    ['exhibitions', 'Exhibitions', 'You have not saved any exhibitions yet.', 'Explore Exhibitions', pages.exhibitions]
  ];
  return layout('Saved Collection', `
<main class="luma-main luma-page luma-page--favorites" id="main-content">
<section class="luma-favorites-hero" aria-label="Saved Collection"><div class="luma-container"><p class="luma-favorites-hero__eyebrow">Your Collection</p><h1 class="luma-favorites-hero__title">Saved Collection</h1><p class="luma-favorites-hero__subtitle">Your personal selection of artworks, artists, and exhibitions to revisit.</p><p class="luma-favorites-hero__note">Save artworks, follow artists, and build your own private gallery inside Luma.</p><p class="luma-favorites-hero__total"><span class="js-fav-total">0</span> items saved</p></div></section>
<section class="luma-favorites-body js-favorites-page"><div class="luma-container"><div class="luma-favorites-tabs" role="tablist" aria-label="Saved items">${tabs.map(([key, label], i) => `<button class="luma-favorites-tab js-fav-tab${i === 0 ? ' is-active' : ''}" role="tab" id="fav-tab-${key}" aria-controls="fav-panel-${key}" aria-selected="${i === 0}" tabindex="${i === 0 ? '0' : '-1'}" data-fav-tab="${key}"><span class="luma-favorites-tab__label">${label}</span><span class="luma-favorites-tab__count js-fav-count" data-fav-type="${key}">0</span></button>`).join('')}</div>${tabs.map(([key, label, empty, cta, href], i) => { const grid = key === 'artworks' ? 'luma-artwork-grid luma-artwork-grid--4col' : key === 'artists' ? 'luma-artist-grid luma-artist-grid--3col' : 'luma-favorites-grid--exhibitions'; return `<div class="luma-favorites-panel js-fav-panel" id="fav-panel-${key}" role="tabpanel" aria-labelledby="fav-tab-${key}" data-fav-panel="${key}" ${i ? 'hidden' : ''}><div class="${grid} js-fav-grid" hidden></div><div class="luma-favorites-empty js-fav-empty">${svg('heart', 'width="40" height="40"')}<p class="luma-favorites-empty__text">${empty}</p><a href="${href}" class="luma-button luma-button--primary">${cta}</a></div>${key === 'artworks' ? `<div class="luma-favorites-recommended js-fav-recommended"><div class="luma-favorites-recommended__header"><h2 class="luma-favorites-recommended__title">Start with these artworks</h2><p class="luma-favorites-recommended__sub">A few pieces our collectors keep coming back to.</p></div><div class="luma-artwork-grid luma-artwork-grid--4col">${data.artworks.slice(0, 4).map(artworkCard).join('')}</div></div>` : ''}</div>`; }).join('')}</div></section>
</main>`);
}

function aiCuratorPage() {
  const option = (value, label) => `<option value="${value}">${label}</option>`;
  const curatorArtworks = data.artworks.map((art, i) => ({ ...art, priceRaw: [280, 240, 480, 150, 300, 260, 190, 410, 560, 520, 140, 220][i] || art.priceRaw }));
  return layout('AI Curator', `
<main class="luma-main luma-page luma-page--curator js-curator-page" id="main-content">
<section class="luma-curator-hero"><div class="luma-container"><p class="luma-curator-hero__eyebrow"><span class="luma-ai-icon" aria-hidden="true">${svg('sparkle', 'width="14" height="14"')}</span> Intelligent Curation</p><h1 class="luma-curator-hero__title">AI Curator</h1><p class="luma-curator-hero__subtitle">Describe your mood, room, or budget. Luma will suggest artworks.</p><p class="luma-curator-hero__disclaimer">${svg('info', 'width="14" height="14"')} Demo AI Curator. No external AI API is used.</p></div></section>
<section class="luma-curator-body"><div class="luma-container"><div class="luma-curator-layout"><aside class="luma-curator-panel"><form class="luma-curator-form js-curator-form" novalidate><div class="luma-curator-field"><label class="luma-curator-field__label" for="curator-prompt">What are you looking for?</label><textarea class="luma-textarea" id="curator-prompt" name="curator_prompt" rows="3" placeholder="I want a calm abstract artwork for a bright bedroom under €300"></textarea></div><div class="luma-curator-fields">${[
    ['curator_mood', 'curator-mood', 'Mood', [['any','Any mood'],['calm','Calm'],['dark','Dark'],['romantic','Romantic'],['minimal','Minimal'],['bright','Bright'],['melancholic','Melancholic'],['atmospheric','Atmospheric']]],
    ['curator_budget', 'curator-budget', 'Budget', [['any','Any budget'],['150','Under €150'],['300','Under €300'],['500','Under €500'],['premium','Premium']]],
    ['curator_room', 'curator-room', 'Room', [['any','Any room'],['living','Living room'],['bedroom','Bedroom'],['office','Office'],['studio','Studio']]],
    ['curator_color', 'curator-color', 'Color palette', [['any','Any color'],['light','Light'],['dark','Dark'],['warm','Warm'],['cold','Cold'],['neutral','Neutral'],['accent','Accent']]],
    ['curator_size', 'curator-size', 'Size', [['any','Any size'],['small','Small'],['medium','Medium'],['large','Large']]]
  ].map(([name, id, label, opts]) => `<div class="luma-curator-field"><label class="luma-curator-field__label" for="${id}">${label}</label><select class="luma-select luma-select--full" id="${id}" name="${name}">${opts.map(([v,l]) => option(v,l)).join('')}</select></div>`).join('')}</div><button type="submit" class="luma-button luma-button--accent luma-button--lg luma-curator-submit">Find Artworks</button><p class="luma-curator-form__hint">Tip: try "calm minimal piece for an office under €300".</p></form></aside><div class="luma-curator-results-wrap"><div class="luma-curator-loader js-curator-loader" role="status" aria-live="polite" hidden><span class="luma-curator-loader__dots"><span></span><span></span><span></span></span><span class="luma-curator-loader__text">Curating your selection...</span></div><p class="luma-curator-summary js-curator-summary" aria-live="polite" hidden></p><div class="luma-curator-empty js-curator-empty" hidden><h2 class="luma-curator-empty__title">No perfect matches yet</h2><p class="luma-curator-empty__text">Try changing your mood, budget, or color palette.</p><button type="button" class="luma-button luma-button--ghost js-curator-reset">Reset Curator</button></div><div class="luma-artwork-grid luma-artwork-grid--3col js-curator-results">${curatorArtworks.map((art, i) => artworkCard(art, i, { priceRaw: art.priceRaw })).join('')}</div></div></div></div></section>
<section class="luma-curator-how"><div class="luma-container"><div class="luma-curator-how__header"><p class="luma-curator-how__eyebrow">Behind the curation</p><h2 class="luma-curator-how__title">How Luma Curator works</h2></div><div class="luma-curator-how__grid">${[['01','Mood','We read the feeling you want and match the emotional tone of each work.'],['02','Space','Room and scale shape the suggestion so the piece sits naturally.'],['03','Budget','Your price range filters the selection from accessible originals to premium statement pieces.']].map(([n,t,txt]) => `<div class="luma-curator-how__col"><span class="luma-curator-how__num">${n}</span><h3 class="luma-curator-how__col-title">${t}</h3><p class="luma-curator-how__col-text">${txt}</p></div>`).join('')}</div><p class="luma-curator-how__note">This demo uses rule-based recommendations without external AI APIs.</p></div></section>
</main>`);
}

function cartPage() {
  return layout('Cart', `
<main class="luma-cart js-cart-page" id="main-content">
<section class="luma-cart__hero"><span class="luma-cart__word" aria-hidden="true">COLLECT</span><div class="luma-container"><p class="luma-cart__eyebrow">Demo Cart</p><h1 class="luma-cart__title">Your Cart</h1><p class="luma-cart__subtitle">Review selected artworks before the demo checkout.</p><p class="luma-cart__disclaimer">${svg('info', 'width="13" height="13"')} Demo cart. No real payment will be processed.</p></div></section>
<section class="luma-cart__body"><div class="luma-container"><div class="luma-cart__layout js-cart-layout" hidden><div class="luma-cart__items js-cart-items" aria-label="Cart items"></div><aside class="luma-cart__summary js-cart-summary" aria-label="Order summary"></aside></div><div class="luma-cart__empty js-cart-empty"><div class="luma-cart__empty-inner"><h2 class="luma-cart__empty-title">Your cart is empty</h2><p class="luma-cart__empty-text">Start collecting artworks from the gallery.</p><a class="luma-button luma-button--accent" href="${pages.gallery}">Explore Gallery</a></div><div class="luma-cart__recommended"><p class="luma-cart__recommended-eyebrow">Start with these</p><div class="luma-artwork-grid luma-artwork-grid--4col">${data.artworks.slice(0,4).map(artworkCard).join('')}</div></div></div></div></section>
</main>`);
}

function checkoutPage() {
  const delivery = [
    ['standard', 'Standard Art Shipping', 'Tracked, 5-9 business days', 25, true],
    ['premium', 'Premium Insured Shipping', 'Climate-controlled, fully insured', 45, false],
    ['pickup', 'Local Gallery Pickup', 'Collect in person at the gallery', 0, false]
  ];
  return layout('Demo Checkout', `
<main class="luma-checkout js-checkout-page" id="main-content">
<section class="luma-checkout__hero"><span class="luma-checkout__word" aria-hidden="true">ORDER</span><div class="luma-container"><p class="luma-checkout__eyebrow">Demo Checkout</p><h1 class="luma-checkout__title">Demo Checkout</h1><p class="luma-checkout__subtitle">Complete a simulated order for your selected artworks.</p><p class="luma-checkout__disclaimer">${svg('info', 'width="13" height="13"')} No real payment will be processed.</p></div></section>
<section class="luma-checkout__body"><div class="luma-container"><div class="luma-checkout__empty js-checkout-empty" hidden><h2 class="luma-checkout__empty-title">Your cart is empty</h2><p class="luma-checkout__empty-text">Add an artwork before starting the demo checkout.</p><a class="luma-button luma-button--accent" href="${pages.gallery}">Return to Gallery</a></div><div class="luma-checkout__layout js-checkout-main"><form class="luma-checkout__form js-checkout-form" novalidate>
${checkoutFieldset('01', 'Contact Information', [['co-name','name','Full name','text','name',true],['co-email','email','Email','email','email',true],['co-phone','phone','Phone','tel','tel',false]])}
${checkoutFieldset('02', 'Shipping Address', [['co-country','country','Country','text','country-name',true],['co-city','city','City','text','address-level2',true],['co-street','street','Street address','text','address-line1',true,'wide'],['co-postal','postal','Postal code','text','postal-code',true]])}
<fieldset class="luma-checkout__section"><legend class="luma-checkout__section-title"><span>03</span>Delivery Method</legend><div class="luma-checkout__delivery-group js-delivery-group" role="radiogroup" aria-label="Delivery method">${delivery.map(([value,label,desc,price,checked]) => `<label class="luma-checkout__delivery"><input type="radio" name="delivery" value="${value}" data-price="${price}" ${checked ? 'checked' : ''}><span class="luma-checkout__delivery-body"><span class="luma-checkout__delivery-label">${label}</span><span class="luma-checkout__delivery-desc">${desc}</span></span><span class="luma-checkout__delivery-price">${price ? `€ ${price}` : 'Free'}</span></label>`).join('')}</div></fieldset>
<fieldset class="luma-checkout__section"><legend class="luma-checkout__section-title"><span>04</span>Demo Payment</legend><div class="luma-checkout__payment-demo"><div class="luma-checkout__card" aria-hidden="true"><div class="luma-checkout__card-top"><span class="luma-checkout__card-brand">Luma Demo Payment</span><span class="luma-checkout__card-chip"></span></div><div class="luma-checkout__card-number">4242 4242 4242 4242</div><div class="luma-checkout__card-foot"><span>12 / 30</span><span>CVC 123</span></div></div><div class="luma-checkout__payment-fields"><div class="luma-checkout__field"><label for="co-card-name">Cardholder name</label><input class="luma-input" type="text" id="co-card-name" name="cardName" placeholder="Optional - demo only"></div><div class="luma-checkout__field"><label for="co-card-number">Card number</label><input class="luma-input" type="text" id="co-card-number" value="4242 4242 4242 4242" disabled aria-disabled="true"></div><div class="luma-checkout__field luma-checkout__field--half"><label for="co-card-exp">Expiry</label><input class="luma-input" type="text" id="co-card-exp" value="12 / 30" disabled aria-disabled="true"></div><div class="luma-checkout__field luma-checkout__field--half"><label for="co-card-cvc">CVC</label><input class="luma-input" type="text" id="co-card-cvc" value="123" disabled aria-disabled="true"></div></div><p class="luma-checkout__payment-note">This payment method is simulated for portfolio demonstration. No card is charged.</p></div></fieldset>
<button type="submit" class="luma-button luma-button--accent luma-button--lg luma-checkout__submit">Place Demo Order</button></form><aside class="luma-checkout__summary js-checkout-summary" aria-label="Order summary"></aside></div><div class="luma-checkout__success js-checkout-success" hidden><div class="luma-checkout__success-inner"><span class="luma-checkout__success-mark" aria-hidden="true"><svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg></span><p class="luma-checkout__success-eyebrow">Confirmation</p><h2 class="luma-checkout__success-title">Demo order confirmed</h2><p class="luma-checkout__success-text">Your simulated order has been created successfully. No payment was processed.</p><dl class="luma-checkout__success-details">${[['Order number','number'],['Date','date'],['Name','name'],['Email','email'],['Delivery','delivery'],['Total','total']].map(([label, key]) => `<div><dt>${label}</dt><dd class="js-success-${key}">-</dd></div>`).join('')}</dl><ol class="luma-order-timeline"><li class="luma-order-timeline__step is-done">Order created</li><li class="luma-order-timeline__step is-done">Demo payment confirmed</li><li class="luma-order-timeline__step">Preparing artwork</li><li class="luma-order-timeline__step">Ready for shipping</li></ol><div class="luma-checkout__success-actions"><a class="luma-button luma-button--accent" href="${pages.gallery}">Explore Gallery</a><a class="luma-button luma-button--ghost" href="${pages.favorites}">View Saved Collection</a></div></div></div></div></section>
</main>`);
}

function checkoutFieldset(num, title, fields) {
  return `<fieldset class="luma-checkout__section"><legend class="luma-checkout__section-title"><span>${num}</span>${title}</legend><div class="luma-checkout__grid">${fields.map(([id,name,label,type,autocomplete,required,wide]) => `<div class="luma-checkout__field ${wide ? 'luma-checkout__field--wide' : ''}"><label for="${id}">${label}${required ? '' : ' <span class="luma-checkout__optional">(optional)</span>'}</label><input class="luma-input" type="${type}" id="${id}" name="${name}" autocomplete="${autocomplete}" ${required ? 'required' : ''}></div>`).join('')}</div></fieldset>`;
}

function accountPage() {
  const sections = [
    ['saved', 'js-account-saved', 'luma-artwork-grid luma-artwork-grid--4col', 'Collection', 'Saved Artworks', pages.favorites, 'All favorites'],
    ['artists', 'js-account-artists', 'luma-artist-grid luma-artist-grid--3col', 'People', 'Following', pages.artists, 'Meet artists'],
    ['recent', 'js-account-recent', 'luma-artwork-grid luma-artwork-grid--4col', 'History', 'Recently Viewed', pages.gallery, 'Explore gallery']
  ];
  return layout('Account', `
<main class="luma-account js-account-page" id="main-content"><section class="luma-account__hero"><span class="luma-account__word" aria-hidden="true">ARCHIVE</span><div class="luma-container"><div class="luma-account__profile"><span class="luma-account__avatar js-account-avatar" aria-hidden="true">G</span><div class="luma-account__profile-info"><p class="luma-account__eyebrow">Demo Account</p><h1 class="luma-account__name js-account-name">Guest Collector</h1><p class="luma-account__email js-account-email">collector@luma.demo</p><p class="luma-account__since">Collecting since <span class="js-account-since">${year}</span></p></div></div><nav class="luma-account__links" aria-label="Account shortcuts"><a class="luma-account__link" href="${pages.gallery}">Gallery</a><a class="luma-account__link" href="${pages.favorites}">Favorites</a><a class="luma-account__link" href="${pages.curator}">AI Curator</a><a class="luma-account__link" href="${pages.cart}">Cart</a></nav></div></section><section class="luma-account__body"><div class="luma-container"><div class="luma-account__stats js-account-stats" aria-label="Account statistics"></div><section class="luma-account__section"><div class="luma-account__section-head"><p class="luma-account__section-eyebrow">Purchases</p><h2 class="luma-account__section-title">Demo Order History</h2></div><div class="luma-account__orders js-account-orders"></div></section>${sections.map((s) => `<section class="luma-account__section"><div class="luma-account__section-head"><div><p class="luma-account__section-eyebrow">${s[3]}</p><h2 class="luma-account__section-title">${s[4]}</h2></div><a class="luma-account__section-link" href="${s[5]}">${s[6]} &rarr;</a></div><div class="${s[2]} ${s[1]}"></div></section>`).join('')}</div></section></main>`);
}

function studioPage() {
  const opt = (value, label = value) => `<option value="${esc(value)}">${esc(label)}</option>`;
  const moods = ['calm','dark','romantic','minimal','bright','melancholic','atmospheric'];
  const styles = ['abstract','modern','minimal','figurative','landscape','surreal'];
  const techniques = ['oil','acrylic','watercolor','mixed media','digital','ink'];
  const colors = ['light','dark','warm','cold','neutral','accent'];
  const rooms = ['living room','bedroom','office','studio'];
  return layout('Artist Studio', `
<main class="luma-studio js-studio-page" id="main-content"><section class="luma-studio__hero"><span class="luma-studio__word" aria-hidden="true">STUDIO</span><div class="luma-container"><p class="luma-studio__eyebrow">Demo Studio</p><h1 class="luma-studio__title">Artist Studio</h1><p class="luma-studio__subtitle">Create demo artworks, generate gallery-ready descriptions, and manage your artist presence.</p><p class="luma-studio__disclaimer">${svg('info', 'width="14" height="14"')} Demo studio. Content is stored locally in your browser.</p><nav class="luma-studio__links" aria-label="Studio shortcuts"><a class="luma-studio__link" href="${pages.gallery}">Gallery</a><a class="luma-studio__link" href="${pages.journal}">Journal</a><a class="luma-studio__link" href="${pages.account}">Account</a><a class="luma-studio__link" href="${pages.favorites}">Favorites</a></nav></div></section>
<section class="luma-studio__body"><div class="luma-container"><div class="luma-studio__profile"><span class="luma-studio__profile-avatar" aria-hidden="true">DA</span><div class="luma-studio__profile-info"><p class="luma-studio__profile-label">Local demo profile</p><h2 class="luma-studio__profile-name">Demo Artist</h2><ul class="luma-studio__profile-meta"><li><span>Role</span>Independent artist</li><li><span>Location</span>Digital Studio</li></ul><p class="luma-studio__profile-note">This studio simulates the artist workflow without registration or external APIs.</p></div></div><div class="luma-studio__stats" aria-label="Studio statistics">${[['artworks','Artworks'],['drafts','Drafts'],['published','Published Demo Works'],['posts','Artist Posts'],['ai','AI Generations']].map(([k,l]) => `<div class="luma-studio__stat"><span class="luma-studio__stat-value" data-studio-stat="${k}">0</span><span class="luma-studio__stat-label">${l}</span></div>`).join('')}</div><div class="luma-studio__tabs" role="tablist" aria-label="Studio sections">${[['overview','Overview'],['artworks','Artworks'],['add-artwork','Add Artwork'],['posts','Posts'],['ai','AI Assistant']].map(([k,l],i) => `<button type="button" class="luma-studio__tab js-studio-tab" id="studio-tab-${k}" role="tab" data-studio-tab="${k}" aria-controls="studio-panel-${k}" aria-selected="${i === 0}" tabindex="${i === 0 ? '0' : '-1'}">${l}</button>`).join('')}</div>
<section class="luma-studio__panel js-studio-panel" id="studio-panel-overview" role="tabpanel" aria-labelledby="studio-tab-overview" tabindex="0"><div class="luma-studio__overview-head"><p class="luma-studio__panel-eyebrow">Dashboard</p><h3 class="luma-studio__panel-title">Studio Overview</h3></div><div class="luma-studio__quick-actions"><button type="button" class="luma-button luma-button--accent js-studio-go" data-studio-go="add-artwork">Add Artwork</button><button type="button" class="luma-button luma-button--ghost js-studio-go" data-studio-go="posts">Write Studio Post</button><button type="button" class="luma-button luma-button--ghost js-studio-go" data-studio-go="ai">Generate Description</button></div><div class="luma-studio__overview-grid"><div class="luma-studio__overview-col"><div class="luma-studio__subhead"><h4 class="luma-studio__subhead-title">Recent Artworks</h4><button type="button" class="luma-studio__subhead-link js-studio-go" data-studio-go="artworks">All artworks &rarr;</button></div><div class="luma-studio__grid luma-studio__grid--3 js-studio-overview-artworks"></div></div><div class="luma-studio__overview-col"><div class="luma-studio__subhead"><h4 class="luma-studio__subhead-title">Recent Posts</h4><button type="button" class="luma-studio__subhead-link js-studio-go" data-studio-go="posts">All posts &rarr;</button></div><div class="luma-studio__post-list js-studio-overview-posts"></div></div></div><p class="luma-studio__note">In production, these actions would save data to WordPress CPTs with user roles and moderation.</p></section>
<section class="luma-studio__panel js-studio-panel" id="studio-panel-artworks" role="tabpanel" aria-labelledby="studio-tab-artworks" tabindex="0" hidden><div class="luma-studio__panel-head"><div><p class="luma-studio__panel-eyebrow">Catalogue</p><h3 class="luma-studio__panel-title">Your Demo Artworks</h3></div><button type="button" class="luma-button luma-button--accent js-studio-go" data-studio-go="add-artwork">Add Artwork</button></div><p class="luma-studio__feedback js-studio-artworks-feedback" role="status" aria-live="polite"></p><div class="luma-studio__grid luma-studio__grid--4 js-studio-artworks"></div></section>
<section class="luma-studio__panel js-studio-panel" id="studio-panel-add-artwork" role="tabpanel" aria-labelledby="studio-tab-add-artwork" tabindex="0" hidden><div class="luma-studio__panel-head"><div><p class="luma-studio__panel-eyebrow">Compose</p><h3 class="luma-studio__panel-title js-studio-form-title">Add Artwork</h3></div></div><div class="luma-studio__split"><form class="luma-studio__form js-studio-artwork-form" novalidate><input type="hidden" name="editingId" value=""><div class="luma-studio__form-grid">${studioInput('studio-art-title','title','Title','text','wide',true)}${studioInput('studio-art-price','price','Price','number','',true)}<div class="luma-studio__field"><label for="studio-art-currency">Currency</label><select class="luma-select" id="studio-art-currency" name="currency">${['€','$','£'].map((x) => opt(x)).join('')}</select></div>${studioSelect('studio-art-status','status','Status',['draft','published','archived'],true)}${studioSelect('studio-art-mood','mood','Mood',moods,true)}${studioSelect('studio-art-style','style','Style',styles,true)}${studioSelect('studio-art-technique','technique','Technique',techniques)}${studioInput('studio-art-material','material','Material','text')} ${studioInput('studio-art-size','size','Size','text')} ${studioInput('studio-art-year','year','Year','number')} ${studioSelect('studio-art-color','color','Color palette',colors)}${studioSelect('studio-art-room','room','Room type',rooms)}${studioTextarea('studio-art-description','description','Description')}${studioTextarea('studio-art-story','story','Story behind the artwork')}${studioInput('studio-art-tags','tags','Tags','text','wide')}</div><p class="luma-studio__feedback js-studio-artwork-feedback" role="status" aria-live="polite"></p><div class="luma-studio__form-actions"><button type="submit" class="luma-button luma-button--accent js-studio-artwork-submit">Save Artwork</button><button type="reset" class="luma-button luma-button--ghost js-studio-artwork-reset">Reset Form</button><button type="button" class="luma-button luma-button--ghost js-studio-artwork-ai">Generate with AI</button></div></form><aside class="luma-studio__preview-wrap" aria-label="Live preview"><p class="luma-studio__preview-eyebrow">Live Preview</p><div class="luma-studio__preview js-studio-preview"></div><p class="luma-studio__preview-note">Preview updates as you type. Visuals use a generated gradient.</p></aside></div></section>
<section class="luma-studio__panel js-studio-panel" id="studio-panel-posts" role="tabpanel" aria-labelledby="studio-tab-posts" tabindex="0" hidden><div class="luma-studio__panel-head"><div><p class="luma-studio__panel-eyebrow">Journal</p><h3 class="luma-studio__panel-title">Studio Posts</h3></div></div><div class="luma-studio__split luma-studio__split--posts"><form class="luma-studio__form js-studio-post-form" novalidate><input type="hidden" name="editingId" value=""><div class="luma-studio__form-grid">${studioInput('studio-post-title','title','Title','text','wide',true)}${studioSelect('studio-post-topic','topic','Topic',['artist-stories','exhibitions','collecting-art','studio-notes'])}<div class="luma-studio__field"><label for="studio-post-artwork">Linked artwork</label><select class="luma-select js-studio-post-artwork" id="studio-post-artwork" name="linkedArtworkId"><option value="">None</option></select></div>${studioTextarea('studio-post-excerpt','excerpt','Excerpt',2)}${studioTextarea('studio-post-content','content','Content',5,true)}${studioSelect('studio-post-status','status','Status',['draft','published'])}</div><p class="luma-studio__feedback js-studio-post-feedback" role="status" aria-live="polite"></p><div class="luma-studio__form-actions"><button type="submit" class="luma-button luma-button--accent js-studio-post-submit">Save Post</button><button type="reset" class="luma-button luma-button--ghost js-studio-post-reset">Reset</button><button type="button" class="luma-button luma-button--ghost js-studio-post-ai">Generate Post with AI</button></div><p class="luma-studio__note">Studio posts are stored locally.</p></form><div class="luma-studio__post-list-wrap"><div class="luma-studio__subhead"><h4 class="luma-studio__subhead-title">Your Posts</h4></div><p class="luma-studio__feedback js-studio-posts-feedback" role="status" aria-live="polite"></p><div class="luma-studio__post-list js-studio-posts"></div></div></div></section>
<section class="luma-studio__panel js-studio-panel" id="studio-panel-ai" role="tabpanel" aria-labelledby="studio-tab-ai" tabindex="0" hidden><div class="luma-studio__panel-head"><div><p class="luma-studio__panel-eyebrow">Assistant</p><h3 class="luma-studio__panel-title">Mock AI Assistant</h3></div></div><div class="luma-studio__split luma-studio__split--ai"><form class="luma-studio__form luma-studio__ai js-studio-ai-form" novalidate><div class="luma-studio__form-grid">${studioSelect('studio-ai-type','type','Generate',['description','story','tags','social','seo'])}${studioInput('studio-ai-title','title','Artwork title','text','wide')}${studioSelect('studio-ai-mood','mood','Mood',moods)}${studioSelect('studio-ai-style','style','Style',styles)}${studioSelect('studio-ai-technique','technique','Technique',techniques)}${studioSelect('studio-ai-tone','tone','Tone',['poetic','gallery','minimal','commercial','social'])}</div><div class="luma-studio__form-actions"><button type="submit" class="luma-button luma-button--accent js-studio-ai-generate">Generate</button></div><p class="luma-studio__disclaimer luma-studio__disclaimer--ink">${svg('info', 'width="14" height="14"')} Demo AI Assistant. No external AI API is used.</p></form><div class="luma-studio__ai-output js-studio-ai-output" role="status" aria-live="polite"></div></div></section>
</div></section></main>`);
}

function studioInput(id, name, label, type = 'text', wide = '', req = false) {
  return `<div class="luma-studio__field ${wide ? 'luma-studio__field--wide' : ''}"><label for="${id}">${label}${req ? ' <span class="luma-studio__req">*</span>' : ''}</label><input class="luma-input" type="${type}" id="${id}" name="${name}" autocomplete="off"></div>`;
}
function studioTextarea(id, name, label, rows = 4, req = false) {
  return `<div class="luma-studio__field luma-studio__field--wide"><label for="${id}">${label}${req ? ' <span class="luma-studio__req">*</span>' : ''}</label><textarea class="luma-textarea" id="${id}" name="${name}" rows="${rows}"></textarea></div>`;
}
function studioSelect(id, name, label, opts, req = false) {
  return `<div class="luma-studio__field"><label for="${id}">${label}${req ? ' <span class="luma-studio__req">*</span>' : ''}</label><select class="luma-select" id="${id}" name="${name}"><option value="">-</option>${opts.map((x) => `<option value="${esc(x)}">${esc(x.replace(/-/g, ' ').replace(/\b\w/g, (m) => m.toUpperCase()))}</option>`).join('')}</select></div>`;
}

function aboutPage() {
  return layout('About', `<main class="luma-about" id="main-content"><section class="luma-about__hero"><span class="luma-about__word" aria-hidden="true">ABOUT</span><div class="luma-container"><p class="luma-about__eyebrow">Digital Art Experience</p><h1 class="luma-about__title">About Luma Gallery</h1><p class="luma-about__subtitle">A digital gallery built around artists, stories, and immersive discovery.</p></div></section><div class="luma-about__body"><div class="luma-container">${[
['01','Mission','A gallery beyond the white wall','Luma Gallery combines curated artworks, artist stories, immersive viewing modes, and demo collecting tools into one digital gallery experience.'],
['02','Curation','Curated by mood, space, and story','Browse by feeling, follow artists, enter exhibitions, and use the rule-based AI Curator to translate a sentence about your space into a focused selection.'],
['03','Transparency','Built as a static portfolio demo','No real payment is ever processed. Favorites, cart, checkout, account, and studio data run entirely in your browser via localStorage.']
].map(([n,e,t,txt]) => `<section class="luma-about__section"><div class="luma-about__section-head"><span class="luma-about__section-num" aria-hidden="true">${n}</span><p class="luma-about__section-eyebrow">${e}</p></div><h2 class="luma-about__section-title">${t}</h2><p class="luma-about__section-text">${txt}</p></section>`).join('')}<section class="luma-about__section luma-about__split"><div class="luma-about__split-aside"><span class="luma-about__section-num" aria-hidden="true">04</span><p class="luma-about__section-eyebrow">For collectors</p><h2 class="luma-about__section-title">For collectors</h2><a class="luma-button luma-button--accent" href="${pages.gallery}">Explore Gallery</a></div><ul class="luma-about__list" role="list"><li>Browse a curated catalogue of original artworks</li><li>Save favorites and revisit them any time</li><li>Ask the AI Curator for a tailored selection</li><li>Run a full demo checkout, start to finish</li></ul></section><section class="luma-about__section luma-about__split luma-about__split--reverse"><div class="luma-about__split-aside"><span class="luma-about__section-num" aria-hidden="true">05</span><p class="luma-about__section-eyebrow">For artists</p><h2 class="luma-about__section-title">For artists</h2><a class="luma-button luma-button--accent" href="${pages.studio}">Open Artist Studio</a></div><ul class="luma-about__list" role="list"><li>Present a dedicated artist profile</li><li>Manage demo artworks in the Artist Studio</li><li>Draft copy with the mock AI Assistant</li><li>Preview how a work appears in the gallery</li></ul></section></div></div><section class="luma-about__cta"><span class="luma-about__word luma-about__word--cta" aria-hidden="true">LUMA</span><div class="luma-container"><h2 class="luma-about__cta-title">Start exploring Luma</h2><div class="luma-about__cta-actions"><a class="luma-button luma-button--accent luma-button--lg" href="${pages.gallery}">Enter Gallery</a><a class="luma-button luma-button--ghost-light luma-button--lg" href="${pages.curator}">Ask AI Curator</a><a class="luma-button luma-button--ghost-light luma-button--lg" href="${pages.journal}">View Journal</a></div></div></section></main>`);
}

function artworkPage(art) {
  const artist = byId(data.artists, art.artistId);
  const similar = data.artworks.filter((item) => item.id !== art.id && (item.artistId === art.artistId || item.mood === art.mood)).slice(0, 4);
  const rv = `class="js-recently-viewed-track" data-id="${esc(art.id)}" data-title="${esc(art.title)}" data-artist="${esc(art.artist)}" data-price="${esc(art.priceRaw)}" data-currency="€" data-gradient="${esc(art.gradient)}" data-url="${artworkUrl(art)}" data-status="${esc(art.status)}"`;
  return layout(art.title, `<main class="luma-main" id="main-content"><span ${rv}></span><div class="luma-artwork-hero"><div class="luma-artwork-hero__image-panel"><div class="luma-artwork-hero__placeholder" style="background:${esc(art.gradient)};"></div><button class="luma-artwork-hero__zoom" aria-label="Zoom image">${svg('search', 'width="16" height="16"')}</button></div><div class="luma-artwork-hero__info"><p class="luma-artwork-hero__eyebrow">Original Artwork</p><h1 class="luma-artwork-hero__title">${esc(art.title)}</h1><p class="luma-artwork-hero__artist">by <a href="${artist ? artistUrl(artist) : pages.artists}">${esc(art.artist)}</a></p><p class="luma-artwork-hero__price">${money(art.priceRaw)}</p><div class="luma-artwork-hero__status"><span class="luma-badge luma-badge--${esc(art.status)}">${esc(art.status)}</span></div><div class="luma-artwork-hero__actions">${art.status === 'available' ? `<button type="button" class="luma-button luma-button--accent js-add-to-cart" data-cart-id="${esc(art.id)}" data-title="${esc(art.title)}" data-artist="${esc(art.artist)}" data-price="${esc(art.priceRaw)}" data-currency="€" data-gradient="${esc(art.gradient)}" data-url="${artworkUrl(art)}" data-status="${esc(art.status)}" data-size="${esc(art.size)}" data-technique="${esc(art.technique)}">Add to Cart</button><button type="button" class="luma-button luma-button--primary js-add-to-cart" data-cart-id="${esc(art.id)}" data-title="${esc(art.title)}" data-artist="${esc(art.artist)}" data-price="${esc(art.priceRaw)}" data-currency="€" data-gradient="${esc(art.gradient)}" data-url="${artworkUrl(art)}" data-status="${esc(art.status)}" data-size="${esc(art.size)}" data-technique="${esc(art.technique)}">Buy Now</button>` : ''}<button class="luma-button luma-button--ghost js-toggle-favorite" data-artwork-id="${esc(art.id)}" data-type="artwork" aria-pressed="false">${svg('heart', 'width="15" height="15"')} Favorite</button><button class="luma-button luma-button--ghost js-view-in-room" data-artwork-title="${esc(art.title)}" data-artwork-size="${esc(art.size)}" data-artwork-url="${artworkUrl(art)}">${svg('grid', 'width="15" height="15"')} View in Room</button></div><dl class="luma-artwork-specs">${[['Size',art.size],['Technique',art.technique],['Material',art.material],['Year',art.year]].map(([k,v]) => `<div class="luma-artwork-specs__item"><dt>${k}</dt><dd>${esc(v)}</dd></div>`).join('')}</dl></div></div><div class="luma-artwork-body"><div class="luma-artwork-body__main"><div class="luma-artwork-section"><h2 class="luma-artwork-section__title">Description</h2><div class="luma-artwork-section__text"><p>${esc(art.description)}</p></div></div><div class="luma-artwork-section"><h2 class="luma-artwork-section__title">Story Behind the Artwork</h2><div class="luma-artwork-section__text"><p>${esc(art.story)}</p></div></div><div class="luma-artwork-section"><h2 class="luma-artwork-section__title">Details</h2><dl class="luma-artwork-details">${[['Artist',art.artist],['Dimensions',art.size],['Status',art.status],['Mood',art.mood],['Style',art.style]].map(([k,v]) => `<div class="luma-artwork-details__row"><dt>${k}</dt><dd>${esc(v)}</dd></div>`).join('')}</dl></div><div class="luma-artwork-section"><h2 class="luma-artwork-section__title">Tags</h2><div class="luma-artwork-tags">${[art.style, art.mood, art.technique, art.color].map((tag) => `<span class="luma-tag">${esc(tag)}</span>`).join('')}</div></div></div><div class="luma-artwork-body__aside">${artist ? `<div class="luma-artwork-artist-preview"><p class="luma-artwork-artist-preview__label">About the Artist</p><div class="luma-artwork-artist-preview__inner"><div class="luma-artwork-artist-preview__avatar">${esc(artist.name.charAt(0))}</div><div><h3 class="luma-artwork-artist-preview__name"><a href="${artistUrl(artist)}">${esc(artist.name)}</a></h3><p class="luma-artwork-artist-preview__location">${esc(artist.location)}</p><p class="luma-artwork-artist-preview__bio">${esc(words(artist.bio, 20))}</p><a href="${artistUrl(artist)}" class="luma-button luma-button--ghost luma-button--sm">View Profile</a></div></div></div>` : ''}</div></div><section class="luma-artwork-similar"><div class="luma-artwork-similar__header"><h2 class="luma-artwork-similar__title">Similar Artworks</h2><a href="${pages.gallery}" class="luma-button luma-button--ghost luma-button--sm">View all</a></div><div class="luma-artwork-similar__grid">${similar.map(artworkCard).join('')}</div></section><section class="luma-recently-viewed" style="display:none;" id="recently-viewed"><div class="luma-recently-viewed__header"><h2 class="luma-recently-viewed__title">Recently Viewed</h2></div><div class="luma-recently-viewed__grid js-recently-viewed-grid"></div></section></main>`);
}

function artistPage(artist) {
  const works = data.artworks.filter((art) => art.artistId === artist.id);
  const similar = data.artists.filter((item) => item.id !== artist.id).slice(0, 3);
  return layout(artist.name, `<main class="luma-main luma-artist-page" id="main-content"><section class="luma-artist-hero" aria-label="${esc(artist.name)}"><div class="luma-artist-hero__cover-placeholder" style="background:${esc(artist.gradient)};position:absolute;inset:0;opacity:.3;"></div><div class="luma-artist-hero__gradient" aria-hidden="true"></div><div class="luma-artist-hero__content"><div class="luma-artist-hero__avatar-placeholder" style="background:${esc(artist.gradient)};"><span>${esc(artist.name.charAt(0))}</span></div><div class="luma-artist-hero__meta"><h1 class="luma-artist-hero__name">${esc(artist.name)}</h1><p class="luma-artist-hero__location">${svg('pin', 'width="13" height="13"')} ${esc(artist.location)}</p><p class="luma-artist-hero__bio">${esc(words(artist.bio, 24))}</p><div class="luma-artist-hero__stats"><div class="luma-artist-hero__stat"><strong>${artist.artworks}</strong><span>Works</span></div><div class="luma-artist-hero__stat"><strong>${artist.followers.toLocaleString('en-US')}</strong><span>Followers</span></div><div class="luma-artist-hero__stat"><strong>${artist.sold}</strong><span>Sold</span></div></div><div class="luma-artist-hero__actions"><button class="luma-button luma-button--ghost-light js-toggle-favorite" data-artist-id="${esc(artist.id)}" data-type="artist" aria-pressed="false">Follow Artist</button><a href="${artist.website}" class="luma-button luma-button--ghost-light" target="_blank" rel="noopener noreferrer">Contact</a></div></div></div></section><div class="luma-artist-body"><div class="luma-artist-main"><div class="luma-artist-section"><h2 class="luma-artist-section__title">Biography</h2><p>${esc(artist.bio)}</p></div><div class="luma-artist-section"><h2 class="luma-artist-section__title">Artist Statement</h2><blockquote><p>${esc(artist.statement)}</p></blockquote></div></div><div class="luma-artist-aside"></div></div><section class="luma-artist-artworks"><div class="luma-artist-artworks__header"><h2 class="luma-artist-artworks__title">Artworks by ${esc(artist.name)}</h2><span class="luma-artist-artworks__count">${works.length}</span></div><div class="luma-artist-artworks__grid">${works.map(artworkCard).join('')}</div></section><section class="luma-artist-similar"><div class="luma-artist-similar__header"><h2 class="luma-artist-similar__title">Similar Artists</h2></div><div class="luma-artist-similar__grid">${similar.map(artistCard).join('')}</div></section></main>`);
}

function exhibitionPage(exhibition) {
  const works = exhibition.artworkIds.map((id) => byId(data.artworks, id)).filter(Boolean);
  return layout(exhibition.title, `<main class="luma-main" id="main-content"><section class="luma-exhibition-hero" aria-label="${esc(exhibition.title)}"><div class="luma-exhibition-hero__cover-placeholder" style="background:${esc(exhibition.gradient)};position:absolute;inset:0;opacity:.3;"></div><div class="luma-exhibition-hero__gradient" aria-hidden="true"></div><div class="luma-exhibition-hero__content"><p class="luma-exhibition-hero__eyebrow">${esc(exhibition.subtitle)}</p><h1 class="luma-exhibition-hero__title">${esc(exhibition.title)}</h1><p class="luma-exhibition-hero__subtitle">${esc(exhibition.subtitle)}</p><blockquote class="luma-exhibition-hero__curator">${esc(words(exhibition.curator, 30))}</blockquote><div class="luma-exhibition-hero__meta"><span class="luma-badge luma-badge--tag">${works.length} works</span>${exhibition.moodTags.map((tag) => `<span class="luma-badge luma-badge--mood">${esc(tag)}</span>`).join('')}<button class="luma-button luma-button--ghost-light luma-button--sm js-start-gallery-walk">Start Gallery Walk</button></div></div></section><div class="luma-exhibition-intro"><p>${esc(exhibition.curator)}</p></div><section class="luma-exhibition-artworks"><div class="luma-exhibition-artworks__header"><h2 class="luma-exhibition-artworks__title">Works in this Exhibition</h2><span class="luma-exhibition-artworks__count">${works.length} works</span></div><div class="luma-exhibition-artworks__grid">${works.map(artworkCard).join('')}</div></section><section class="luma-exhibition-commentary" aria-label="Curator commentary">${works.slice(0,3).map((art, i) => `<div class="luma-commentary-block"><div><p class="luma-commentary-block__artwork-label">${['Opening Work','Midpoint','Closing'][i]}</p><h3 class="luma-commentary-block__artwork-title">${esc(art.title)}</h3><p class="luma-commentary-block__artwork-artist">${esc(art.artist)}</p></div><div class="luma-commentary-block__text"><p>${esc(['The exhibition opens with a work that sets the emotional key for everything that follows.','At the heart of the exhibition, the works enter into dialogue. Color becomes architecture; silence becomes narrative.','The final work resolves without resolution, asking the visitor to carry something unnamed back into the world.'][i])}</p></div></div>`).join('')}</section>${galleryWalkFrames(works, true)}${galleryWalkOverlay()}<section class="luma-exhibition-cta"><div class="luma-exhibition-cta__inner"><p class="luma-exhibition-cta__eyebrow">Immersive Mode</p><h2 class="luma-exhibition-cta__title">Start Gallery Walk</h2><p class="luma-exhibition-cta__text">Experience this exhibition in full-screen mode.</p><button class="luma-button luma-button--ghost-light luma-button--lg js-start-gallery-walk">Begin Gallery Walk</button></div></section></main>`);
}

function journalPostPage(post) {
  const related = data.artworks.slice(0, 3);
  const more = data.journal.filter((item) => item.id !== post.id).slice(0, 3);
  return layout(post.title, `<main class="luma-main luma-page luma-page--journal-single" id="main-content"><article class="luma-post"><header class="luma-post-hero"><div class="luma-container luma-container--narrow"><span class="luma-post-hero__topic">${esc(post.topic)}</span><h1 class="luma-post-hero__title">${esc(post.title)}</h1><p class="luma-post-hero__excerpt">${esc(post.excerpt)}</p><div class="luma-post-hero__meta"><span class="luma-post-hero__date">${esc(post.date)}</span><span class="luma-post-hero__dot" aria-hidden="true">·</span><span class="luma-post-hero__author">${esc(post.artist)}</span></div></div><div class="luma-post-hero__media"><span class="luma-post-hero__placeholder" style="background:${esc(post.gradient)};" aria-hidden="true"></span></div></header><div class="luma-post-content luma-container luma-container--narrow">${post.content.map((p) => `<p>${esc(p)}</p>`).join('')}</div><aside class="luma-post-artist"><div class="luma-container luma-container--narrow"><div class="luma-post-artist__inner"><div class="luma-post-artist__avatar" style="background:${esc(post.gradient)};" aria-hidden="true"><span class="luma-post-artist__initial">${esc(post.artist.charAt(0) || 'L')}</span></div><div class="luma-post-artist__detail"><p class="luma-post-artist__eyebrow">About the artist</p><h2 class="luma-post-artist__name">${esc(post.artist)}</h2><p class="luma-post-artist__bio">An artist featured in Luma Gallery, exploring mood, material and quiet through original work.</p><a href="${post.artistId ? artistUrl(byId(data.artists, post.artistId)) : pages.artists}" class="luma-button luma-button--ghost luma-button--sm">View Artist</a></div></div></div></aside></article><section class="luma-post-related"><div class="luma-container"><div class="luma-post-related__header"><h2 class="luma-post-related__title">Related artworks</h2><a href="${pages.gallery}" class="luma-post-related__link">View all</a></div><div class="luma-artwork-grid luma-artwork-grid--3col">${related.map(artworkCard).join('')}</div></div></section><section class="luma-post-more"><div class="luma-container"><h2 class="luma-post-more__title">More stories</h2><div class="luma-post-grid">${more.map(journalCard).join('')}</div></div></section><section class="luma-post-cta"><div class="luma-container"><div class="luma-post-cta__inner"><h2 class="luma-post-cta__title">Discover original artworks</h2><p class="luma-post-cta__text">Browse the full collection of one-of-one works by contemporary artists.</p><a href="${pages.gallery}" class="luma-button luma-button--accent luma-button--lg">Explore the Gallery</a></div></div></section></main>`);
}

function searchPage() {
  const results = [
    ...data.artworks.map((item) => ({ type: 'Artwork', title: item.title, excerpt: `${item.artist} - ${item.style}, ${item.mood}`, url: artworkUrl(item), gradient: item.gradient })),
    ...data.artists.map((item) => ({ type: 'Artist', title: item.name, excerpt: item.bio, url: artistUrl(item), gradient: item.gradient })),
    ...data.exhibitions.map((item) => ({ type: 'Exhibition', title: item.title, excerpt: item.curator, url: exhibitionUrl(item), gradient: item.gradient })),
    ...data.journal.map((item) => ({ type: 'Journal', title: item.title, excerpt: item.excerpt, url: journalUrl(item), gradient: item.gradient }))
  ];
  return layout('Search', `<main class="luma-search" id="main-content"><section class="luma-search__hero"><span class="luma-search__word" aria-hidden="true">SEARCH</span><div class="luma-container"><p class="luma-search__eyebrow">Search Luma</p><h1 class="luma-search__title js-search-title">Search the gallery</h1><p class="luma-search__subtitle">Find artworks, artists, exhibitions, and journal stories.</p><form class="luma-search__form" role="search" method="get" action="${pages.search}"><label class="luma-search__label" for="luma-search-field">Search the gallery</label><input class="luma-input luma-search__field js-static-search-input" type="search" id="luma-search-field" name="s" placeholder="Search artworks, artists, journal..." autocomplete="off"><button type="submit" class="luma-button luma-button--accent luma-search__submit">Search</button></form></div></section><section class="luma-search__body"><div class="luma-container"><p class="luma-search__count js-static-search-count" role="status">${results.length} results available</p><div class="luma-search__results js-static-search-results">${results.map((item, i) => `<article class="luma-search__item" data-search-text="${esc(`${item.type} ${item.title} ${item.excerpt}`.toLowerCase())}"><a class="luma-search__item-media" href="${item.url}" tabindex="-1" aria-hidden="true"><span class="luma-search__item-fallback" style="background:${esc(item.gradient)};"></span></a><div class="luma-search__item-body"><span class="luma-search__item-type">${esc(item.type)}</span><h2 class="luma-search__item-title"><a href="${item.url}">${esc(item.title)}</a></h2><p class="luma-search__item-excerpt">${esc(words(item.excerpt, 24))}</p><div class="luma-search__item-foot"><a class="luma-search__item-cta" href="${item.url}">Open &rarr;</a></div></div></article>`).join('')}</div><div class="luma-search__empty js-static-search-empty" hidden><p class="luma-search__empty-eyebrow">No results found</p><h2 class="luma-search__empty-title">Nothing found</h2><p class="luma-search__empty-text">Try a different keyword or explore curated sections instead.</p><div class="luma-search__empty-actions"><a class="luma-button luma-button--accent" href="${pages.gallery}">Explore Gallery</a><a class="luma-button luma-button--ghost" href="${pages.artists}">Meet Artists</a><a class="luma-button luma-button--ghost" href="${pages.curator}">AI Curator</a></div></div></div></section><script>
document.addEventListener('DOMContentLoaded',function(){var p=new URLSearchParams(location.search),q=(p.get('s')||p.get('q')||'').trim().toLowerCase(),input=document.querySelector('.js-static-search-input'),title=document.querySelector('.js-search-title'),count=document.querySelector('.js-static-search-count'),empty=document.querySelector('.js-static-search-empty'),items=[].slice.call(document.querySelectorAll('.luma-search__item'));if(input)input.value=q;if(title)title.textContent=q?'Results for "'+q+'"':'Search the gallery';function run(term){var n=0;items.forEach(function(item){var ok=!term||item.dataset.searchText.indexOf(term)>-1;item.hidden=!ok;if(ok)n++;});if(count)count.textContent=n+' result'+(n===1?'':'s')+' found';if(empty)empty.hidden=n!==0;}run(q);if(input)input.addEventListener('input',function(){run(input.value.trim().toLowerCase());});});
</script></main>`);
}

function errorPage() {
  return layout('404', `<main class="luma-error" id="main-content"><span class="luma-error__word" aria-hidden="true">LOST</span><div class="luma-container luma-error__inner"><div class="luma-error__visual" aria-hidden="true"><div class="luma-error__frame luma-error__frame--a"></div><div class="luma-error__frame luma-error__frame--b"><span class="luma-error__frame-tag">No. 404</span></div><div class="luma-error__frame luma-error__frame--c"></div><span class="luma-error__hook"></span></div><div class="luma-error__content"><p class="luma-error__number" aria-hidden="true">404</p><p class="luma-error__eyebrow">Error 404</p><h1 class="luma-error__title">This artwork is missing</h1><p class="luma-error__subtitle">The page you are looking for may have been moved, archived, or never existed.</p><form class="luma-error__search" role="search" method="get" action="${pages.search}"><label class="luma-visually-hidden" for="luma-404-search">Search the gallery</label><input class="luma-input luma-error__search-field" type="search" id="luma-404-search" name="s" placeholder="Search the gallery..." autocomplete="off"><button type="submit" class="luma-button luma-button--accent">Search</button></form><div class="luma-error__actions"><a class="luma-button luma-button--ghost-light" href="${pages.home}">Return Home</a><a class="luma-button luma-button--ghost-light" href="${pages.gallery}">Explore Gallery</a><a class="luma-button luma-button--ghost-light" href="${pages.curator}">Ask AI Curator</a></div><nav class="luma-error__links" aria-label="Suggested pages"><a href="${pages.gallery}">Gallery</a><a href="${pages.artists}">Artists</a><a href="${pages.exhibitions}">Exhibitions</a><a href="${pages.journal}">Journal</a><a href="${pages.favorites}">Favorites</a></nav></div></div></main>`);
}

function modalViewInRoom() {
  return `<div class="luma-modal luma-modal--room js-modal" id="modal-view-in-room" role="dialog" aria-modal="true" aria-label="View artwork in a room" aria-hidden="true"><div class="luma-modal__backdrop js-modal-close" tabindex="-1"></div><div class="luma-modal__panel luma-modal__panel--room"><div class="luma-modal__header"><h2 class="luma-modal__title">View in Room</h2><div class="luma-room-switcher">${[['living','Living Room'],['bedroom','Bedroom'],['office','Office']].map(([slug,label],i) => `<button class="luma-room-switcher__btn js-room-switch${i === 0 ? ' is-active' : ''}" data-room="${slug}">${label}</button>`).join('')}</div><button class="luma-modal__close js-modal-close" aria-label="Close">${svg('close')}</button></div><div class="luma-room js-room" data-active-room="living"><div class="luma-room__wall"><div class="luma-room__light" aria-hidden="true"></div><div class="luma-room__light-cone" aria-hidden="true"></div><div class="luma-room__frame js-room-frame"><div class="luma-room__frame-border"><div class="luma-room__frame-mat"><div class="luma-room__frame-image js-room-artwork-image"></div></div></div><div class="luma-room__frame-shadow" aria-hidden="true"></div></div><div class="luma-room__wainscot" aria-hidden="true"></div></div><div class="luma-room__floor" aria-hidden="true"><div class="luma-room__floor-planks"></div></div><div class="luma-room__furniture luma-room__furniture--living js-room-furniture" data-room="living"><div class="luma-room__sofa"><div class="luma-room__sofa-back"></div><div class="luma-room__sofa-seat"></div><div class="luma-room__sofa-cushion luma-room__sofa-cushion--1"></div><div class="luma-room__sofa-cushion luma-room__sofa-cushion--2"></div></div><div class="luma-room__side-table"><div class="luma-room__side-table-top"></div><div class="luma-room__side-table-leg"></div></div><div class="luma-room__plant"><div class="luma-room__plant-pot"></div><div class="luma-room__plant-leaves"></div></div></div><div class="luma-room__furniture luma-room__furniture--bedroom js-room-furniture" data-room="bedroom" style="display:none;"><div class="luma-room__bed"><div class="luma-room__bed-frame"></div><div class="luma-room__bed-mattress"></div><div class="luma-room__bed-pillow luma-room__bed-pillow--1"></div><div class="luma-room__bed-pillow luma-room__bed-pillow--2"></div><div class="luma-room__bed-blanket"></div></div><div class="luma-room__nightstand"><div class="luma-room__nightstand-top"></div><div class="luma-room__nightstand-lamp"><div class="luma-room__lamp-shade"></div><div class="luma-room__lamp-base"></div></div></div></div><div class="luma-room__furniture luma-room__furniture--office js-room-furniture" data-room="office" style="display:none;"><div class="luma-room__desk"><div class="luma-room__desk-top"></div><div class="luma-room__desk-monitor"><div class="luma-room__monitor-screen"></div><div class="luma-room__monitor-stand"></div></div><div class="luma-room__desk-leg luma-room__desk-leg--1"></div><div class="luma-room__desk-leg luma-room__desk-leg--2"></div></div><div class="luma-room__office-chair"><div class="luma-room__chair-back"></div><div class="luma-room__chair-seat"></div></div></div></div><div class="luma-modal__room-info"><div class="luma-modal__room-info-text"><span class="luma-modal__room-info-title js-room-info-title"></span><span class="luma-modal__room-info-size js-room-info-size"></span></div><a href="#" class="luma-button luma-button--primary luma-button--sm js-room-view-link">View Artwork</a></div></div></div>`;
}

function modalAiAssistant() {
  const tones = [['poetic','Poetic'],['gallery','Gallery'],['minimal','Minimal'],['commercial','Commercial'],['social-media','Social Media']];
  const actions = [['description','Generate Description'],['tags','Suggest Tags'],['story','Write Story'],['social','Social Post'],['seo','SEO Description']];
  return `<div class="luma-modal luma-modal--ai js-modal" id="modal-ai-assistant" role="dialog" aria-modal="true" aria-label="AI Artist Assistant" aria-hidden="true"><div class="luma-modal__backdrop js-modal-close" tabindex="-1"></div><div class="luma-modal__panel luma-modal__panel--ai"><div class="luma-modal__header"><div class="luma-modal__header-brand"><div class="luma-ai-icon" aria-hidden="true">${svg('sparkle', 'width="18" height="18"')}</div><h2 class="luma-modal__title">AI Artist Assistant</h2></div><button class="luma-modal__close js-modal-close" aria-label="Close">${svg('close')}</button></div><div class="luma-ai-panel__tones"><span class="luma-ai-panel__tones-label">Tone:</span>${tones.map(([slug,label]) => `<button class="luma-ai-panel__tone-btn js-ai-tone${slug === 'gallery' ? ' is-active' : ''}" data-tone="${slug}">${label}</button>`).join('')}</div><div class="luma-ai-panel__actions">${actions.map(([type,label]) => `<button class="luma-button luma-button--ghost luma-button--sm js-ai-generate" data-type="${type}">${label}</button>`).join('')}</div><div class="luma-ai-panel__output"><div class="luma-ai-panel__state luma-ai-panel__state--empty js-ai-state-empty"><div class="luma-ai-panel__empty-icon" aria-hidden="true">${svg('sparkle', 'width="36" height="36"')}</div><p>Choose a generation type above and the assistant will write for you.</p></div><div class="luma-ai-panel__state luma-ai-panel__state--loading js-ai-state-loading" style="display:none;" aria-live="polite"><div class="luma-ai-panel__loader"><div class="luma-ai-panel__loader-dots"><span></span><span></span><span></span></div><p class="luma-ai-panel__loader-text js-ai-loader-text">Generating...</p></div></div><div class="luma-ai-panel__state luma-ai-panel__state--generated js-ai-state-generated" style="display:none;" aria-live="polite"><div class="luma-ai-panel__generated-type js-ai-generated-type"></div><div class="luma-ai-panel__generated-text js-ai-generated-text" contenteditable="false"></div><div class="luma-ai-panel__generated-actions"><button class="luma-button luma-button--primary luma-button--sm js-ai-apply">Apply</button><button class="luma-button luma-button--ghost luma-button--sm js-ai-copy">Copy</button><button class="luma-button luma-button--ghost luma-button--sm js-ai-regenerate">Regenerate</button><button class="luma-button luma-button--ghost luma-button--sm js-ai-edit">Edit Manually</button></div></div></div><p class="luma-ai-panel__disclaimer">${svg('info', 'width="12" height="12"')} Demo AI Assistant. All results are rule-based simulations.</p></div></div>`;
}

function write(name, html) {
  fs.writeFileSync(path.join(outDir, name), html, 'utf8');
}

fs.mkdirSync(outDir, { recursive: true });

write(pages.home, homePage());
write(pages.gallery, galleryPage());
write(pages.artists, artistsPage());
write(pages.exhibitions, exhibitionsPage());
write(pages.journal, journalPage());
write(pages.favorites, favoritesPage());
write(pages.curator, aiCuratorPage());
write(pages.cart, cartPage());
write(pages.checkout, checkoutPage());
write(pages.account, accountPage());
write(pages.studio, studioPage());
write(pages.about, aboutPage());
write(pages.search, searchPage());
write('404.html', errorPage());

data.artworks.forEach((art) => write(artworkUrl(art), artworkPage(art)));
data.artists.forEach((artist) => write(artistUrl(artist), artistPage(artist)));
data.exhibitions.forEach((exhibition) => write(exhibitionUrl(exhibition), exhibitionPage(exhibition)));
data.journal.forEach((post) => write(journalUrl(post), journalPostPage(post)));

console.log(`Generated ${fs.readdirSync(outDir).filter((file) => file.endsWith('.html')).length} HTML files in ${outDir}`);
