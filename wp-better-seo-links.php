<?php
/*
Plugin Name: WP Better SEO Links
Plugin URI: https://wp.help/
Description: Better SEO links for: nofollow, sponsored, and ugc (user-generated content)
Version: 1.0
Author: WP Help
License: GPLv2

WordPress Better SEO Links
*/

/***********************
 * OPTIONS PAGE SECTION *
 ************************/

/* add plugin's options to white list / defaults */
function itswphelp_options_init() {
	register_setting( 'itswphelp_options_options', 'itswphelp_item', 'itswphelp_options_validate' );

	// if option doesn't exist, set defaults
	if ( ! get_option( 'itswphelp_item' ) ) {
		add_option( 'itswphelp_item', [
			'nofollow_comments' => 1,
			'nofollow_blogroll' => 0,
		], '', 'no' );
	}
}

/* add link to plugin's settings page under 'settings' on the admin menu */
function itswphelp_options_add_page() {
	add_options_page( 'Ultimate Nofollow Settings', 'Nofollow', 'manage_options', 'nofollow-sponsored', 'itswphelp_options_do_page' );
}

/* sanitize and validate input. 
accepts an array, returns a sanitized array. */
function itswphelp_options_validate( $input ) {
	$input['nofollow_comments'] = ( $input['nofollow_comments'] == 1 ? 1 : 0 ); // (checkbox) if 1 then 1, else 0
	$input['nofollow_blogroll'] = ( $input['nofollow_blogroll'] == 1 ? 1 : 0 ); // (checkbox) if 1 then 1, else 0
	//	$input[ 'test_text_1' ] =  wp_filter_nohtml_kses( $input[ 'test_text_1' ] ); // (textbox) safe text, no html
	return $input;
}

/* draw the settings page itself */
function itswphelp_options_do_page() {
	?>
    <div class="wrap">
        <div class="icon32" id="icon-options-general"><br/></div>
        <h2>Ultimate Nofollow Settings</h2>
        <form method="post" action="options.php">
			<?php settings_fields( 'itswphelp_options_options' ); // nonce settings page ?>
			<?php $options = get_option( 'itswphelp_item' ); // populate $options array from database ?>
            <table class="form-table">

                <!-- all comment links -->
                <tr>
                    <th scope="row">Nofollow all links in comments?</th>
                    <td><input name="itswphelp_item[nofollow_comments]"
                               type="checkbox"
                               value="1" <?php checked( $options['nofollow_comments'] ); ?> />
                    </td>
                </tr>

                <!-- all blogroll links -->
                <tr>
                    <th scope="row">Nofollow all blogroll links?</th>
                    <td><input name="itswphelp_item[nofollow_blogroll]"
                               type="checkbox"
                               value="1" <?php checked( $options['nofollow_blogroll'] ); ?> />
                        <span style="color:red; font-size:smaller">(warning: will override individual selections!)</span>
                    </td>
                </tr>

                <!-- <tr valign="top"><th scope="row">Text:</th>
					<td>
                    	UA-<input type="text" name="ssga_item[sometext1]" value="<?php // echo $options[ 'test_text_1']; ?>" style="width:90px;" maxlength="8" />
					</td>
				</tr> -->

            </table>
            <p class="submit">
                <input type="submit" class="button-primary"
                       value="<?php _e( 'Save Changes' ) ?>"/>
            </p>
        </form>
    </div>
	<?php
}

/* define additional plugin meta links */
function itswphelp_set_plugin_meta( $links, $file ) {
	$plugin = plugin_basename( __FILE__ ); // '/nofollow/nofollow.php' by default
	if ( $file == $plugin ) { // if called for THIS plugin then:
		$newlinks = [
			'<a href="options-general.php?page=nofollow-sponsored">Settings</a>',
		]; // array of links to add

		return array_merge( $links, $newlinks ); // merge new links into existing $links
	}

	return $links; // return the $links (merged or otherwise)
}

/* add hooks/filters */
// add meta links to plugin's section on 'plugins' page (10=priority, 2=num of args)
add_filter( 'plugin_row_meta', 'itswphelp_set_plugin_meta', 10, 2 );

// add plugin's options to white list on admin initialization
add_action( 'admin_init', 'itswphelp_options_init' );

// add link to plugin's settings page in 'settings' menu on admin menu initilization
add_action( 'admin_menu', 'itswphelp_options_add_page' );

/******************************
 * NOFOLLOW SHORTCODES SECTION *
 *******************************/

/* valid href starting substring? */
function itswphelp_valid_url( $href ) {
	$start_strs = [ // list of accepted url protocols
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
		'mms://',
	];

	foreach ( $start_strs as $start_str ) {
		if ( substr( $href, 0, strlen( $start_str ) ) == $start_str ) {
			return true;
		}
	}

	return false;
}

/* return nofollow link html or html error comment */
function itswphelp_nofollow_link( $atts, $content = null ) {
	extract(
		shortcode_atts(
			[
				'href'   => null,
				'title'  => null,
				'target' => null,
			],
			$atts
		)
	);

	// href
	if ( ! itswphelp_valid_url( $href ) ) {
		return '<!-- Ultimate Nofollow Plugin | shortcode insertion failed | given href resource not valid, href must begin with: ' . print_r( $start_strs, true ) . ' -->';
	} // if url doesn't starts with valid string
	else {
		$href_chunk = ' href="' . $href . '"';
	} // else add href=''

	// title
	if ( empty( $title ) ) {
		$title_chunk = null;
	} // if no $title, omit HTML
	else {
		$title_chunk = ' title="' . trim( htmlentities( strip_tags( $title ), ENT_QUOTES ) ) . '"';
	} // else add title=''

	// target
	if ( empty( $target ) ) {
		$target_chunk = null;
	} // if no $target, omit HTML
	else {
		$target_chunk = ' target="' . trim( htmlentities( strip_tags( $target ), ENT_QUOTES ) ) . '"';
	} // else add target=''

	// content
	if ( empty( $content ) ) {
		return '<!-- Ultimate Nofollow Plugin | shortcode insertion failed | no link text given -->';
	} // if url doesn't starts with valid string
	else {
		$content_chunk = trim( htmlentities( strip_tags( $content ), ENT_QUOTES ) );
	} // else add $content

	return '<a' . $href_chunk . $target_chunk . $title_chunk . ' rel="nofollow">' . $content_chunk . '</a>';
}

/* add hooks/filters */
// add shortcodes
$shortcodes = [
	'relnofollow',
	'nofollow',
	'nofol',
	'nofo',
	'nf',
];
foreach ( $shortcodes as $shortcode ) {
	add_shortcode( $shortcode, 'itswphelp_nofollow_link' );
}

/****************************
 * BLOGROLL NOFOLLOW SECTION *
 *****************************/

function itswphelp_blogroll_add_meta_box() {
	add_meta_box( 'itswphelp_blogroll_nofollow_div', 'Ultimate Nofollow', 'itswphelp_blogroll_inner_meta_box', 'link', 'side', 'high' );
}

function itswphelp_blogroll_inner_meta_box( $post ) {
	$bookmark = get_bookmark( $post->link_id, 'ARRAY_A' );
	if ( strpos( $bookmark['link_rel'], 'nofollow' ) !== false ) {
		$checked = ' checked="checked"';
	} else {
		$checked = '';
	}

	$options = get_option( 'itswphelp_item' );
	if ( $options['nofollow_blogroll'] ) {
		$disabled = ' disabled="disabled"';
		$message  = '<br /><span style="color:red; font-size:smaller;">ALL blogroll links nofollowed on the <a href="options-general.php?page=nofollow-sponsored" target="_blank">options</a> page.</span>';
	} else {
		$disabled = '';
		$message  = '';
	}

	?>
    <label for="itswphelp_blogroll_nofollow_checkbox">Nofollow this
        link?</label>
    <input value="1" id="itswphelp_blogroll_nofollow_checkbox"
           name="itswphelp_blogroll_nofollow_checkbox"<?php echo $disabled; ?>
           type="checkbox"<?php echo $checked; ?> /> <?php echo $message; ?>

    <label for="itswphelp_blogroll_sponsored_checkbox">Sponsored this
        link?</label>
    <input value="1" id="itswphelp_blogroll_sponsored_checkbox"
           name="itswphelp_blogroll_sponsored_checkbox"<?php echo $disabled; ?>
           type="checkbox"<?php echo $checked; ?> /> <?php echo $message; ?>

    <label for="itswphelp_blogroll_ugc_checkbox">Nofollow this
        link?</label>
    <input value="1" id="itswphelp_blogroll_ugc_checkbox"
           name="itswphelp_blogroll_ugc_checkbox"<?php echo $disabled; ?>
           type="checkbox"<?php echo $checked; ?> /> <?php echo $message; ?>

	<?php
}

function itswphelp_blogroll_save_meta_box( $link_rel ) {
	$rel = trim( str_replace( 'nofollow', '', $link_rel ) );
	if ( $_POST['itswphelp_blogroll_nofollow_checkbox'] ) {
		$rel .= ' nofollow';
	}

	if ( $_POST['itswphelp_blogroll_sponsored_checkbox'] ) {
		$rel .= ' sponsored';
	}

	if ( $_POST['itswphelp_blogroll_ugc_checkbox'] ) {
		$rel .= ' ugc';
	}

	return trim( $rel );
}

function itswphelp_blogroll_nofollow_all( $links ) {
	foreach ( $links as $link ) {
		$rel            = trim( str_replace( 'nofollow', '', $link->link_rel ) );
		$link->link_rel = trim( $rel . ' nofollow' );
	}

	return $links;
}

/* add hooks/filters */
add_action( 'add_meta_boxes', 'itswphelp_blogroll_add_meta_box', 1 );
add_filter( 'pre_link_rel', 'itswphelp_blogroll_save_meta_box', 99998, 1 );

$itswphelp_options = get_option( 'itswphelp_item' ); // NOT IN FUNCTION
if ( $itswphelp_options['nofollow_blogroll'] ) {
	add_filter( 'get_bookmarks', 'itswphelp_blogroll_nofollow_all', 99999 );
}


/**********************************************
 * ADD LINK DIALOGUE NOFOLLOW CHECKBOX SECTION *
 ***********************************************/
function itswphelp_redo_wplink() {
	wp_deregister_script( 'wplink' );

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	wp_register_script( 'wplink', plugins_url( 'wplink' . $suffix . '.js', __FILE__ ), [
		'jquery',
		'wpdialogs',
	], false, 1 );

	wp_localize_script( 'wplink', 'wpLinkL10n', [
		'title'          => __( 'Insert/edit link' ),
		'update'         => __( 'Update' ),
		'save'           => __( 'Add Link' ),
		'noTitle'        => __( '(no title)' ),
		'noMatchesFound' => __( 'No matches found.' ),
	] );
}

add_action( 'admin_enqueue_scripts', 'itswphelp_redo_wplink', 999 );


/************************************
 * NOFOLLOW ON COMMENT LINKS SECTION *
 *************************************/

// add/remove nofollow from all comment links
function itswphelp_comment_links( $comment ) {
	$options = get_option( 'itswphelp_item' );
	if ( ! $options['nofollow_comments'] ) {
		$comment = str_replace( 'rel="nofollow"', '', $comment );
	} elseif ( ! strpos( $comment, 'rel="nofollow"' ) ) {
		$comment = str_replace( '<a ', '<a rel="nofollow"', $comment );
	}

	return $comment;
}

/* add hooks/filters */
// add/remove nofollow from comment links
add_filter( 'comment_text', 'itswphelp_comment_links', 10 );
?>
