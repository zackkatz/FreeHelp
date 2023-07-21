<?php

namespace Modules\FreeHelp\Providers;
use Illuminate\Support\ServiceProvider;

class FreeHelpServiceProvider extends ServiceProvider
{
    private const MODULE_NAME = 'freehelp';

    public function boot()
    {
        \Eventy::addAction('javascript', function() {
            echo <<<JS


/**
* Remove the "target=_blank" attribute for all links.
* @see https://github.com/freescout-helpdesk/freescout/issues/2914
*/
$('[target="_blank"]').attr("target", null );

// In Reports and other AJAX-generated links.
$( document ).ajaxComplete(function() {
	$('[target="_blank"]').attr("target", null );
});

/**
* Convert custom field dropdowns to Select2.
*/
$( '#custom-fields-form select:not(.cf-multiselect)').each(function () {
	// Set a default option.
	$('option[value=""]', $( this ) ).html( 'Select an option&hellip;');
}).select2();

$( document ).on( 'keyup', function( e ) {
	
	// Skip inputs and editable areas.
	if (
		!e.target
		|| ( e.ctrlKey && key != 13 )
		|| e.altKey
		|| e.shiftKey
		|| e.metaKey
	) {
		if ( ! $('#conv-status.open:first').length ) {
			return;
		}
	}
	
	switch ( e.which ) {
		// Forward slash
		case 191:
			$( '#search-dt').not('[aria-expanded=true]').trigger('click'); // Click instead of toggle to run the .setTimeout() focus that FS uses.
			break;
		
		// Escape key
		case 27:
			// Close all open dropdowns.
			$( '#search-dt[aria-expanded=true]').dropdown('toggle');
			//$('#conv-status,#conv-assignee[aria-expanded="true"]').dropdown('close');
			
			// Stop editing a conversation subject.
			$( '.conv-subj-editing', '#conv-subject').removeClass('conv-subj-editing');
			
			// Stop editing a note.
			$( '.note-editor:visible .note-actions .glyphicon-trash' ).trigger( 'click' );
			break;
	}
} );


/**
* Based on https://github.com/iTeeLion/jquery.checkbox-shift-selector.js/
* @type {null}
*/
var chkboxShiftLastChecked = null;
$('.conv-cb label').on( 'click', function(e){
    
    var \$all_checkboxes = $('input.conv-checkbox');
	var this_input = $( this ).parent('td').find('input.conv-checkbox' )[0];
	
	// Remove the selection of text that happens.
	document.getSelection().removeAllRanges();
	
    if ( ! chkboxShiftLastChecked ) {
        chkboxShiftLastChecked = this_input;
        return;
    }

    if ( e.shiftKey ) {
        var start = \$all_checkboxes.index( this_input );
        var end = \$all_checkboxes.index(chkboxShiftLastChecked);

        \$all_checkboxes.slice(Math.min(start,end), Math.max(start,end)+ 1).prop('checked', chkboxShiftLastChecked.checked);
		
		// When removing the selected text using getSelection(), the last click gets nullified. Let's re-do it.
		$( this_input ).trigger('click');
    }
	
    chkboxShiftLastChecked = this_input;
});


JS;
        }, -1, 3);

	    \Eventy::addAction('layout.head', function() {
			echo <<<HTML
<style>
	#app { 
		font-family: "Aktiv Grotesk","Helvetica Neue", Helvetica, Arial, sans-serif; 
	}
	
	/**
	 * Global interface items
	 */
	 
	 #app .dropdown-menu {
	    box-shadow: 0 1px 7px 0 rgba(0, 0, 0, .08);
	    background-color: #fff;
	    background-clip: padding-box;
	    border: 1px solid #c5ced6;
	    border-radius: 3px;
	    font-size: 13px;
	    min-width: 181px;
	    padding: 5px 0;
	}
	
	/**
	 * Make Select2 dropdowns look more like HS. 
	 */
	body .select2-container--default .select2-results__option--highlighted[aria-selected] {
		background: #f1f3f5;
	    color:#07c
	}
	body .select2-container--default .select2-results__option[aria-selected=true] {
		background: #f1f3f5;
	}
	
	body .modal-backdrop.in {
		opacity:  1;
	}
	body .modal-backdrop {
		background:  rgba(3, 64, 119, 0.7) none repeat scroll 0 0 / auto padding-box border-box;
	}
	body .modal-dialog {
		margin-top: 25vh;
	}
	@media (min-width: 768px) {
		body .modal-content {
			box-shadow: none;
			border: none;
		}
	}
	
	/**
	 * Conversations table
	 */
	#app .table.table-conversations tbody .conv-active,
	#app .table-conversations thead,
	#app .conv-active .conv-date a {
		background-color: transparent;
	}
	
	/* Changing the background color means we need to change the fader color. */
	#app .table-conversations .conv-active .conv-fader {
		transition: opacity .2s;
	    background: -webkit-gradient(linear, left top, right top, from(rgba(255, 255, 255, 0)), to(white));
	    background: -o-linear-gradient(left, rgba(255, 255, 255, 0), #fff);
	    background: linear-gradient(to right, rgba(255, 255, 255, 0), #fff);
	}
	
	#app .table-conversations thead th span {
		font-weight: normal;
		color: #7a8da0;
	}
	
	#app .table-conversations tr {
		transition: background-color .2s;
	}
	
	#app .table-conversations tr:has(.conv-checkbox:checked) {
		transition: background-color .2s;
	    background-color: #fff7ea;
	    border-bottom-color:#d5dce1;
	    .conv-fader {
	    	opacity: 0;
			transition: opacity .2s;
	    }
	}
	
	/* To disable starred conversations, uncomment. */
	/* #app .sidebar-menu .star, */
	/* #app .conv-numnav .conv-star, */ 
	#app .table-conversations .conv-attachment .conv-star
	{
		display: none;
	}

	/** 
	 * Make checkboxes look better
	 * - Smaller
	 * - With a small inner shadow
	 */
	#app .magic-checkbox+label:before, 
	#app .magic-radio+label:before {
		width: 12px;
		height: 12px;
		box-shadow: inset 0 1px 1.5px 0 rgba(213,220,225,0.6);
	}
	#app .magic-checkbox:checked+label:before, 
	#app .magic-radio:checked+label:before {
		box-shadow: none;
	}
	#app .magic-checkbox+label:after {
		top: 1px;
		left: 4px;
		width: 4px;
		height: 8px;
		border-width: 1.75px;
	}
	#app .conv-cb label {
		top: -3px;
		left: 12px;
	}
	#app thead .conv-cb label {
		top: -4px;
		left: -1px;
	}
	
	/**
	 * Sidebar
	 */
	 
 	/** Use HS round-rect instead of tab-style rounded on the right */ 
	#app .sidebar-menu > li {
		min-height: 40px;
	}
	#app .sidebar-menu > li > a {
		border-radius: 4px;
	}
	
	/** Use HS background color for sidebar. Don't modify in modals. */ 
	@media (min-width:992px) {
		#app .sidebar-2col {
			width: 250px; /* HS is 250, FS is 260 */
			padding: 8px 5px 0;
			background-color: #f1f3f5;
		}
	}
	
	/* Instead of having a heavy drop-shadow, only have a thin line, like HS */
	#app .content-2col {
		box-shadow: -1px 0 0 #d5dce1,1px 0 0 #d5dce1,0 1px 0 #d5dce1;
	}
	
	#app .sidebar-menu .active a, 
	#app .sidebar-menu .active .glyphicon {
		background-color: #fff;
		color: #07c!important;
	}
	
	/** Don't show email used for the mailbox */
	#app .sidebar-title-email {
		display: none;
	}
	
	/** Position the title more like HS */
	#app .sidebar-title {
		padding: 8px 8px 8px 15px;
		margin: 0 0 8px;
		font-size: 18px;
	}
	
	#app .sidebar-buttons .glyphicon:before,
	#folders .glyphicon:before {
	    content: "";
	}
	
	#folders .glyphicon,
	#app .sidebar-buttons .glyphicon {
		display: block;
	    width: 20px;
	    height: 20px;
	    background-size: contain;
	    margin-top: -2px;
	    background-color:  transparent!important;
	    filter: invert(68%) sepia(14%) saturate(352%) hue-rotate(170deg) brightness(92%) contrast(84%);
	}
	#app .sidebar-buttons .glyphicon {
		display: inline-block;
		width: 18px;
		height: 18px;
	    margin-top: 0;
	}
	
	#app .glyphicon-hand-right {
		background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.85" stroke="black"><path stroke-linecap="round" stroke-linejoin="round" d="M10.05 4.575a1.575 1.575 0 10-3.15 0v3m3.15-3v-1.5a1.575 1.575 0 013.15 0v1.5m-3.15 0l.075 5.925m3.075.75V4.575m0 0a1.575 1.575 0 013.15 0V15M6.9 7.575a1.575 1.575 0 10-3.15 0v8.175a6.75 6.75 0 006.75 6.75h2.018a5.25 5.25 0 003.712-1.538l1.732-1.732a5.25 5.25 0 001.538-3.712l.003-2.024a.668.668 0 01.198-.471 1.575 1.575 0 10-2.228-2.228 3.818 3.818 0 00-1.12 2.687M6.9 7.575V12m6.27 4.318A4.49 4.49 0 0116.35 15m.002 0h-.002" /></svg>') left top no-repeat;
	}
	#app .glyphicon-star {
		background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.85" stroke="black"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" /></svg>') left top no-repeat;
	}
	#app .glyphicon-folder-open {
		background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.85" stroke="black"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 9v.906a2.25 2.25 0 01-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 001.183 1.981l6.478 3.488m8.839 2.51l-4.66-2.51m0 0l-1.023-.55a2.25 2.25 0 00-2.134 0l-1.022.55m0 0l-4.661 2.51m16.5 1.615a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V8.844a2.25 2.25 0 011.183-1.98l7.5-4.04a2.25 2.25 0 012.134 0l7.5 4.04a2.25 2.25 0 011.183 1.98V19.5z" /></svg>') left top no-repeat;
	}
	#app .glyphicon-duplicate {
		background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.85" stroke="black"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" /></svg>') left top no-repeat;
	}
	#app .glyphicon-user {
		background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.85" stroke="black"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>') left top no-repeat;
	}
	#app .glyphicon-lock {
		background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.85" stroke="black"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" /></svg>') left top no-repeat;
	}
	#app .glyphicon-trash {
		background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.85" stroke="black"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>') left top no-repeat;
	}
	#app .glyphicon-ban-circle {
		background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.85" stroke="black"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>') left top no-repeat;
	}
	#app .glyphicon-time {
		background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.85" stroke="black"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>') left top no-repeat;
	}
	#app .glyphicon-envelope {
		background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="black"><path d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z" /><path d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z" /></svg>') left top no-repeat;
	}
	#app .glyphicon-cog {
		background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="black"><path fill-rule="evenodd" d="M11.828 2.25c-.916 0-1.699.663-1.85 1.567l-.091.549a.798.798 0 01-.517.608 7.45 7.45 0 00-.478.198.798.798 0 01-.796-.064l-.453-.324a1.875 1.875 0 00-2.416.2l-.243.243a1.875 1.875 0 00-.2 2.416l.324.453a.798.798 0 01.064.796 7.448 7.448 0 00-.198.478.798.798 0 01-.608.517l-.55.092a1.875 1.875 0 00-1.566 1.849v.344c0 .916.663 1.699 1.567 1.85l.549.091c.281.047.508.25.608.517.06.162.127.321.198.478a.798.798 0 01-.064.796l-.324.453a1.875 1.875 0 00.2 2.416l.243.243c.648.648 1.67.733 2.416.2l.453-.324a.798.798 0 01.796-.064c.157.071.316.137.478.198.267.1.47.327.517.608l.092.55c.15.903.932 1.566 1.849 1.566h.344c.916 0 1.699-.663 1.85-1.567l.091-.549a.798.798 0 01.517-.608 7.52 7.52 0 00.478-.198.798.798 0 01.796.064l.453.324a1.875 1.875 0 002.416-.2l.243-.243c.648-.648.733-1.67.2-2.416l-.324-.453a.798.798 0 01-.064-.796c.071-.157.137-.316.198-.478.1-.267.327-.47.608-.517l.55-.091a1.875 1.875 0 001.566-1.85v-.344c0-.916-.663-1.699-1.567-1.85l-.549-.091a.798.798 0 01-.608-.517 7.507 7.507 0 00-.198-.478.798.798 0 01.064-.796l.324-.453a1.875 1.875 0 00-.2-2.416l-.243-.243a1.875 1.875 0 00-2.416-.2l-.453.324a.798.798 0 01-.796.064 7.462 7.462 0 00-.478-.198.798.798 0 01-.517-.608l-.091-.55a1.875 1.875 0 00-1.85-1.566h-.344zM12 15.75a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z" clip-rule="evenodd" /></svg>') left top no-repeat;
	}
	
	#folders li.active .glyphicon,
	#folders li.active:hover .glyphicon {
		filter: invert(49%) sepia(81%) saturate(3731%) hue-rotate(184deg) brightness(97%) contrast(91%);
	}
	#folders li:hover .glyphicon {
		filter: invert(56%) sepia(12%) saturate(555%) hue-rotate(169deg) brightness(88%) contrast(89%);
	}
	
	/**
	 * Header
	 */
	#app .navbar-static-top {
		background-color: #005ca4;
	}
	#app .navbar-default .navbar-nav > li > a {
		color: #a0d4ff;
	}
	#app .navbar-default .navbar-nav > li > a:hover {
		color: #fff;
	}
	#app {
		.navbar-default .navbar-nav > .active > a, .navbar-default .navbar-nav > li.active > a:hover, .navbar-default .navbar-nav > li.active > a:focus, .navbar-default .navbar-nav > li > a:hover {
			background-color: #005ca4;
			filter: none;
		}
	}
	
	/**
	 * Single conversations
	 */
	 
	 /* Look more like Help Scout: blue background => white. */
	 #app #conv-toolbar {
		border-bottom:  1px solid #e5e9ec;
		background-color: #fff;
		padding-right: 9px;
	}
	#app .conv-next-prev {
		padding-left: 9px;
	}
	
	/** Hide some unused buttons in text editor. */
	#app .note-btn-group.note-color,
	#app .note-btn-group.note-btn-underline,
	#app .note-btn[aria-label="Remove Formatting"] {
		display: none;
	}
	
	/** To look like HS, responses from support agents are distinguished by a grey border, not a blue background. */
	#app #conv-layout .thread-type-message {
	    background-color:transparent;
	    -webkit-box-shadow: inset 5px 0 0 0 #ffe19d;
	    box-shadow: inset 5px 0 0 0 #93a1b0;
	}
	
	/**
	 * Single conversation sidebar.
	 * Make look more like HS with grey BG, white panels, box shadow, etc. 
	 */
	
	#app .conv-sidebar-block {
		margin: 0 5px 4px 5px;
	}
	#app .panel-group {
		margin-bottom: 4px;
	}
	
	#app .conv-sidebar-block .panel-heading {
		background-color: #fff!important;
	}
	
	#app .conv-top-block {
	    background: #fbfbfb;
	    border: 1px solid #dae3e6;
	    margin:0 25px 25px;
	    padding: 15px 20px 12px 20px;
	    
	    .text-help {
		    color: #93a1b0;
		    font-size: 13px;
		    margin-bottom: 4px;
		    overflow: hidden;
		    -o-text-overflow: ellipsis;
		    text-overflow: ellipsis;
		    white-space: nowrap;
		    width:auto;
	    }
	}
	
	#app .panel-default > .panel-heading + .panel-collapse > .panel-body {
		background-color: #fff!important;
		border-radius: 0 0 4px 4px;
	}
	 
	#app .conv-sidebar-block .panel {
		box-shadow:  0 1px 3px 0 rgba(0,0,0,.1);
		box-sizing: border-box;
	}
	
	#app .conv-sidebar-block .accordion .panel-title a {
		font-size: 14px;
		color: #314351;
		font-weight: 500;
	}
	#app #conv-layout-customer {
		background-color: #f1f3f5;
	}
	
	/** Add padding and align links by content, not by icon. */
	#app .sidebar-block-list li {
		padding-left: 20px;
		margin-bottom: .4em;
		font-size: 13px;
		line-height: 18px;
		& a.help-link:hover {
			color: #556575!important;
		}
		.glyphicon {
			float: left;
			margin: 1px 1px 0 -20px;
			color:  #93a1b0;
		}
	}
	
</style>
HTML;
	    }, -2, 3);
    }
}
