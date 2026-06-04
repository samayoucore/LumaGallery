/**
 * View in Room modal — injects the current artwork image into the CSS room scene,
 * handles room type switching, and opens/closes the modal.
 */

import { openModal, closeModal } from './modals.js';

const VIEW_IN_ROOM_MODAL_ID = 'modal-view-in-room';

function setArtwork( src, title, size, gradient ) {
  const modal = document.getElementById( VIEW_IN_ROOM_MODAL_ID );
  if ( !modal ) return;

  const imageEl = modal.querySelector( '.js-room-artwork-image' );
  const titleEl = modal.querySelector( '.js-room-info-title' );
  const sizeEl  = modal.querySelector( '.js-room-info-size' );
  const viewLink = modal.querySelector( '.js-room-view-link' );

  if ( imageEl ) {
    imageEl.innerHTML = '';
    if ( src ) {
      const img = document.createElement( 'img' );
      img.src = src;
      img.alt = title || '';
      img.style.cssText = 'width:100%;height:100%;object-fit:cover;';
      imageEl.appendChild( img );
    } else {
      imageEl.style.background = gradient || 'linear-gradient(135deg,#2C2C2A,#4A4845)';
    }
  }

  if ( titleEl ) titleEl.textContent = title || '';
  if ( sizeEl  ) sizeEl.textContent  = size  || '';
}

function switchRoom( roomSlug ) {
  const modal = document.getElementById( VIEW_IN_ROOM_MODAL_ID );
  if ( !modal ) return;

  // Toggle furniture
  modal.querySelectorAll( '.js-room-furniture' ).forEach( ( el ) => {
    el.style.display = el.dataset.room === roomSlug ? '' : 'none';
  } );

  // Update buttons
  modal.querySelectorAll( '.js-room-switch' ).forEach( ( btn ) => {
    btn.classList.toggle( 'is-active', btn.dataset.room === roomSlug );
  } );
}

export function openViewInRoom( { src = '', title = '', size = '', url = '#', gradient = '' } = {} ) {
  const modal = document.getElementById( VIEW_IN_ROOM_MODAL_ID );
  if ( !modal ) return;

  setArtwork( src, title, size, gradient );

  const viewLink = modal.querySelector( '.js-room-view-link' );
  if ( viewLink ) viewLink.href = url;

  openModal( modal );
}

export function initViewInRoom() {
  // Room switcher
  document.addEventListener( 'click', ( e ) => {
    const btn = e.target.closest( '.js-room-switch' );
    if ( !btn ) return;
    const room = btn.dataset.room;
    if ( room ) switchRoom( room );
  } );

  // Trigger from artwork pages/cards
  document.addEventListener( 'click', ( e ) => {
    const trigger = e.target.closest( '.js-view-in-room' );
    if ( !trigger ) return;
    e.preventDefault();
    openViewInRoom( {
      src:   trigger.dataset.artworkSrc   || '',
      title: trigger.dataset.artworkTitle || '',
      size:  trigger.dataset.artworkSize  || '',
      url:   trigger.dataset.artworkUrl   || trigger.href || '#',
      gradient: trigger.dataset.artworkGradient || document.querySelector( '.luma-artwork-hero__placeholder' )?.style.background || '',
    } );
  } );
}
