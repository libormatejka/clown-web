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
import GenerateSlug from '@libormatejka/clown-core-assets/admin/js/GenerateSlug';
import MediaModal from '@libormatejka/clown-core-assets/admin/js/MediaModal';
import MediaUploader from '@libormatejka/clown-core-assets/admin/js/MediaUploader';
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

// Closing the user menu when clicking anywhere outside of it (or its button)
document.addEventListener('DOMContentLoaded', () => {
	document.addEventListener('click', (event) => {
		const userMenuButton = document.getElementById('user-menu-button');
		const userMenu = document.querySelector('[aria-labelledby="user-menu-button"]');

		if (!userMenuButton || !userMenu || userMenuButton.getAttribute('aria-expanded') !== 'true') {
			return;
		}

		if (userMenuButton.contains(event.target) || userMenu.contains(event.target)) {
			return;
		}

		userMenu.classList.add('hidden');
		userMenuButton.setAttribute('aria-expanded', 'false');
	});
});

// Initializing MediaUploader if the drop zone exists (MediaLibrary bulk upload)
document.addEventListener('DOMContentLoaded', () => {
	if (document.getElementById('mediaDropZone')) {
		const uploadEndpoint = document.getElementById('mediaUploadEndpoint')?.value ?? '/admin/media-library/upload-file';
		const getSessionEndpoint = document.getElementById('mediaGetSessionEndpoint')?.value ?? '/admin/media-library/get-session-files';
		const removeEndpoint = document.getElementById('mediaRemoveEndpoint')?.value ?? '/admin/media-library/remove-file';

		window.mediaUploader = new MediaUploader({
			dropZoneId: 'mediaDropZone',
			fileInputId: 'mediaFileInput',
			uploadListId: 'mediaUploadList',
			uploadEndpoint,
			getSessionEndpoint,
			removeEndpoint,
			maxFileSize: 50 * 1024 * 1024,
			maxFiles: 50,
			maxConcurrentUploads: 3,
			allowedTypes: [
				'image/jpeg',
				'image/png',
				'image/gif',
				'image/webp',
				'image/avif',
				'application/pdf',
				'application/zip',
				'application/x-zip-compressed',
				'application/x-rar-compressed',
				'application/x-rar',
				'application/vnd.rar',
			],
			debug: false,
		});
	}
});
