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
