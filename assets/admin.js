// jQuery musí být globální dřív, než se načtou moduly z clown-core-assets
import jQuery from 'jquery';
window.jQuery = jQuery;
window.$ = jQuery;

import Nette from 'nette-forms';
window.Nette = Nette;

import '@contributte/datagrid/dist/datagrid-full.js';

import './admin/css/tailwind.css';

import AlertCloseButton from '@libormatejka/clown-core-assets/admin/js/AlertCloseButton';
import DataGridColChooser from '@libormatejka/clown-core-assets/admin/js/DataGridColChooser';
import GalleryManager from '@libormatejka/clown-gallery-assets/admin/js/GalleryManager';
import GenerateSlug from '@libormatejka/clown-core-assets/admin/js/GenerateSlug';
import MediaModal from '@libormatejka/clown-core-assets/admin/js/MediaModal';
import SidebarMenuButton from '@libormatejka/clown-core-assets/admin/js/SidebarMenuButton';
import StatusButtonLabelUpdater from '@libormatejka/clown-core-assets/admin/js/StatusButtonLabelUpdater';
import TableList from '@libormatejka/clown-core-assets/admin/js/TableList';
import UserMenuButton from '@libormatejka/clown-core-assets/admin/js/UserMenuButton';

DataGridColChooser.initOnLoad();
AlertCloseButton.initOnLoad();
SidebarMenuButton.initOnLoad();
UserMenuButton.initOnLoad();
GenerateSlug.initOnLoad();
StatusButtonLabelUpdater.initOnLoad();

document.addEventListener('DOMContentLoaded', () => {
	const tableListInstance = new TableList();
	window.tableListInstance = tableListInstance;
	tableListInstance.initMainCheckbox();
});

// Tinymce Init
import * as TinymceEditor from '@libormatejka/clown-core-assets/admin/js/TinymceEditor';
TinymceEditor.render();

// Initializing MediaModal if the element with id "mediaModalActive" exists in the DOM
document.addEventListener('DOMContentLoaded', () => {
	if (document.getElementById('mediaModalActive')) {
		new MediaModal();
	}
});

document.addEventListener('DOMContentLoaded', () => {
	// Inicializace GalleryManager pro Create Gallery
	const createGalleryForm = document.querySelector('#frm-createGallery-form');
	if (createGalleryForm) {
		new GalleryManager({
			token: 'clown-web',
			rootSelector: '#frm-createGallery-form',
			itemsPerPage: 50,
		});
	}

	// Inicializace GalleryManager pro Update Gallery
	const updateGalleryForm = document.querySelector('#frm-updateGallery-form');
	if (updateGalleryForm) {
		new GalleryManager({
			token: 'clown-web',
			rootSelector: '#frm-updateGallery-form',
			itemsPerPage: 50,
		});
	}
});
