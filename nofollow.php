<?php

/*
Plugin Name: Ultimate Nofollow
Plugin URI: http://5fifty.co.uk
Description: A suite of tools that gives you complete control over the rel=nofollow tag on an individual link basis.
Version: 1.4.8
Author: 5fifty
Author URI: http://5fifty.co.uk
License: GPLv2
	Copyright 2017 5fifty (5fifty.co.uk)

This plugin contains several tools in one to significantly increase your control of the nofollow rel tag on every link on your blog, on both an individual and type basis. It is designed to give you fine-grained control of linking for SEO purposes.

Notice: This plugin changes WordPress functionality in a way that is not modular and may break with WP-Core updates.

*/

/***********************
* OPTIONS PAGE SECTION *
************************/

/* add plugin's options to white list / defaults */
function ultnofo_options_init() { 
	register_setting( 'ultnofo_options_options', 'ultnofo_item', 'ultnofo_options_validate' );

	// if option doesn't exist, set defaults
	if( !get_option( 'ultnofo_item' ) ) add_option( 'ultnofo_item', array( 'nofollow_comments' => 1, 'nofollow_blogroll' => 0 ), '', 'no' ); 
}

/* add link to plugin's settings page under 'settings' on the admin menu */
function ultnofo_options_add_page() { 
	add_options_page( 'Ultimate Nofollow Settings', 'Nofollow', 'manage_options', 'ultimate-nofollow', 'ultnofo_options_do_page' );
}

/* sanitize and validate input. 
accepts an array, returns a sanitized array. */
function ultnofo_options_validate( $input ) { 
	$input[ 'nofollow_comments' ] = ( $input[ 'nofollow_comments' ] == 1 ? 1 : 0 ); // (checkbox) if 1 then 1, else 0
	$input[ 'nofollow_blogroll' ] = ( $input[ 'nofollow_blogroll' ] == 1 ? 1 : 0 ); // (checkbox) if 1 then 1, else 0
	//	$input[ 'test_text_1' ] =  wp_filter_nohtml_kses( $input[ 'test_text_1' ] ); // (textbox) safe text, no html
	return $input;
}

/* draw the settings page itself */
function ultnofo_options_do_page() { 
	?>
	<div class="wrap">
    <div class="icon32" id="icon-options-general"><br /></div>
		<h2>Ultimate Nofollow Settings</h2>
		<form method="post" action="options.php">
			<?php settings_fields( 'ultnofo_options_options' ); // nonce settings page ?>
			<?php $options = get_option( 'ultnofo_item' ); // populate $options array from database ?>
			<table class="form-table">
				
                
<!-- all comment links -->
                <tr valign="top">
					<th scope="row">Nofollow all links in comments?</th>
					<td><input name="ultnofo_item[nofollow_comments]" type="checkbox" value="1" <?php checked( $options[ 'nofollow_comments' ] ); ?> />
					</td>
                </tr>
                
                
<!-- all blogroll links -->
                <tr valign="top">
					<th scope="row">Nofollow all blogroll links?</th>
					<td><input name="ultnofo_item[nofollow_blogroll]" type="checkbox" value="1" <?php checked( $options[ 'nofollow_blogroll' ] ); ?> />
					<span style="color:red; font-size:smaller">(warning: will override individual selections!)</span></td>
                </tr>
                
           		
           
				<!-- <tr valign="top"><th scope="row">Text:</th>
					<td>
                    	UA-<input type="text" name="ssga_item[sometext1]" value="<?php // echo $options[ 'test_text_1']; ?>" style="width:90px;" maxlength="8" />
					</td>
				</tr> -->
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
	<?php
}

/* define additional plugin meta links */
function set_plugin_meta_ultnofo( $links, $file ) { 
	$plugin = plugin_basename( __FILE__ ); // '/nofollow/nofollow.php' by default
    if ( $file == $plugin ) { // if called for THIS plugin then:
		$newlinks = array( 
			'<a href="options-general.php?page=ultimate-nofollow">Settings</a>',
			'<a href="http://5fifty.co.uk">Help Page</a>' 
		); // array of links to add
		return array_merge( $links, $newlinks ); // merge new links into existing $links
	}
return $links; // return the $links (merged or otherwise)
}

/* add hooks/filters */
// add meta links to plugin's section on 'plugins' page (10=priority, 2=num of args)
add_filter( 'plugin_row_meta', 'set_plugin_meta_ultnofo', 10, 2 ); 

// add plugin's options to white list on admin initialization
add_action('admin_init', 'ultnofo_options_init' ); 

// add link to plugin's settings page in 'settings' menu on admin menu initilization
add_action('admin_menu', 'ultnofo_options_add_page'); 

/******************************
* NOFOLLOW SHORTCODES SECTION *
*******************************/

/* valid href starting substring? */
function ultnofo_valid_url( $href ) {
	$start_strs = array( // list of accepted url protocols
		'/',
		'http://',
		'https://',
		'ftp://',
		'mailto:',
		'magnet:',
		'svn://',
		'irc:',
		'gopher://',
		'telnet://',
		'nntp://', 
		'worldwind://',
		'news:',
		'git://',
		'mms://'
	);
	
	foreach( $start_strs as $start_str )
		if( substr( $href, 0, strlen( $start_str ) ) == $start_str ) return TRUE;

	return FALSE;
}

/* return nofollow link html or html error comment */
function ultnofo_nofollow_link( $atts, $content = NULL ) {
	extract( 
		shortcode_atts( 
			array( 
				'href' => NULL, 
				'title' => NULL,
				'target' => NULL 
			), 
			$atts
		)
	);
	
	// href
	if( !ultnofo_valid_url( $href ) ) return '<!-- Ultimate Nofollow Plugin | shortcode insertion failed | given href resource not valid, href must begin with: ' . print_r( $start_strs, TRUE ) . ' -->'; // if url doesn't starts with valid string
	else $href_chunk = ' href="' . $href . '"'; // else add href=''
	
	// title
	if( empty( $title ) ) $title_chunk = NULL; // if no $title, omit HTML
	else $title_chunk = ' title="' . trim( htmlentities( strip_tags( $title ), ENT_QUOTES ) ) . '"'; // else add title='' 

	// target
	if( empty( $target ) ) $target_chunk = NULL; // if no $target, omit HTML
	else $target_chunk = ' target="' . trim( htmlentities( strip_tags( $target ), ENT_QUOTES ) ) . '"'; // else add target='' 
	
	// content
	if( empty( $content ) ) return '<!-- Ultimate Nofollow Plugin | shortcode insertion failed | no link text given -->'; // if url doesn't starts with valid string
	else $content_chunk = trim( htmlentities( strip_tags( $content ), ENT_QUOTES ) ); // else add $content
	
	return '<a' . $href_chunk . $target_chunk . $title_chunk . ' rel="nofollow">' . $content_chunk . '</a>';
}

/* add hooks/filters */
// add shortcodes
$shortcodes = array(
	'relnofollow',
	'nofollow',
	'nofol',
	'nofo',
	'nf'
);
foreach( $shortcodes as $shortcode ) add_shortcode( $shortcode, 'ultnofo_nofollow_link' );

/****************************
* BLOGROLL NOFOLLOW SECTION *
*****************************/

function ultnofo_blogroll_add_meta_box() {
	add_meta_box( 'ultnofo_blogroll_nofollow_div', 'Ultimate Nofollow', 'ultnofo_blogroll_inner_meta_box', 'link', 'side','high' );	
}

function ultnofo_blogroll_inner_meta_box ( $post ) {
	$bookmark = get_bookmark( $post->link_id, 'ARRAY_A' );
	if( strpos( $bookmark['link_rel'], 'nofollow' ) !== FALSE ) $checked = ' checked="checked"';
	else $checked = '';

	$options = get_option( 'ultnofo_item' );
	if( $options['nofollow_blogroll'] ) { 
		$disabled=' disabled="disabled"';
		$message='<br /><span style="color:red; font-size:smaller;">ALL blogroll links nofollowed on the <a href="options-general.php?page=ultimate-nofollow" target="_blank">options</a> page.</span>';
	}
	else { 
		$disabled = '';	
		$message = '';
	}

	?>
<label for="ultnofo_blogroll_nofollow_checkbox">Nofollow this link?</label>
<input value="1" id="ultnofo_blogroll_nofollow_checkbox" name="ultnofo_blogroll_nofollow_checkbox"<?php echo $disabled; ?> type="checkbox"<?php echo $checked; ?> /> <?php echo $message; ?>
<?php
}

function ultnofo_blogroll_save_meta_box( $link_rel ) {
	$rel = trim( str_replace( 'nofollow', '', $link_rel ) );
	if( $_POST['ultnofo_blogroll_nofollow_checkbox'] ) $rel .= ' nofollow';
	return trim( $rel );
}

function ultnofo_blogroll_nofollow_all( $links ) {
	foreach( $links as $link ) {
		$rel = trim( str_replace('nofollow', '', $link->link_rel ) );
		$link->link_rel = trim( $rel . ' nofollow' );
	}
	return $links;
}

/* add hooks/filters */
add_action( 'add_meta_boxes', 'ultnofo_blogroll_add_meta_box', 1 );
add_filter( 'pre_link_rel', 'ultnofo_blogroll_save_meta_box', 99998, 1);

$ultnofo_options = get_option( 'ultnofo_item' ); // NOT IN FUNCTION
if( $ultnofo_options['nofollow_blogroll'] ) add_filter( 'get_bookmarks', 'ultnofo_blogroll_nofollow_all', 99999);




/**********************************************
* ADD LINK DIALOGUE NOFOLLOW CHECKBOX SECTION *
***********************************************/
function nofollow_redo_wplink() {
	wp_deregister_script( 'wplink' );
	
	$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
	
	wp_register_script( 'wplink', plugins_url( 'wplink' . $suffix . '.js', __FILE__), array( 'jquery', 'wpdialogs' ), false, 1 );
	
	wp_localize_script( 'wplink', 'wpLinkL10n', array(
		'title' => __('Insert/edit link'),
		'update' => __('Update'),
		'save' => __('Add Link'),
		'noTitle' => __('(no title)'),
		'noMatchesFound' => __('No matches found.')
	) );
}
add_action( 'admin_enqueue_scripts', 'nofollow_redo_wplink', 999 );


/************************************
* NOFOLLOW ON COMMENT LINKS SECTION *
*************************************/

// add/remove nofollow from all comment links
function ultnofo_comment_links( $comment ) {
	$options = get_option( 'ultnofo_item' );
	if( !$options[ 'nofollow_comments' ] )
		$comment = str_replace( 'rel="nofollow"', '', $comment );
	elseif( !strpos( $comment, 'rel="nofollow"' ) )
		$comment = str_replace( '<a ', '<a rel="nofollow"', $comment ); 
	return $comment;	
}

/* add hooks/filters */
// add/remove nofollow from comment links
add_filter('comment_text', 'ultnofo_comment_links', 10);
?>
