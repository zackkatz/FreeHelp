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
	
	#app .sidebar-menu .folder-open i.glyphicon:before {
    	content: "✉";
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
