<?php

remove_action('wp_head', 'index_rel_link' );
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'start_post_rel_link', 10);
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
remove_action('wp_head', 'wp_shortlink_wp_head', 10);
remove_action('wp_head', 'parent_post_rel_link', 10);
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'feed_links_extra', 3 );
remove_action('wp_head', 'feed_links', 2 );
remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);
remove_action('wp_head', 'wp_oembed_add_host_js', 10);

function dw_remove_wp_ver_css_js( $src ) {
    if ( strpos( $src, 'ver=' ) )
        $src = remove_query_arg( 'ver', $src );
    return $src;
}
add_filter( 'style_loader_src', 'dw_remove_wp_ver_css_js', 9999 );
add_filter( 'script_loader_src', 'dw_remove_wp_ver_css_js', 9999 );

add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'wp_headers', 'disable_x_pingback' );
function disable_x_pingback( $headers ) {
  unset( $headers['X-Pingback'] );
return $headers;
}

function disable_emojis_tinymce( $plugins ) {
  if ( is_array( $plugins ) ) {
    return array_diff( $plugins, array( 'wpemoji' ) );
  } else {
    return array();
  }
}

function disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
	add_filter( 'emoji_svg_url', '__return_false' );
}
add_action( 'init', 'disable_emojis' );

function custom_excerpt_length( $length ) {
	return 20;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

add_filter('wp_mail_from', 'dw_fromemail');
function dw_fromemail($email) {
	$wpfrom = get_option('admin_email');
    return $wpfrom;
}
add_filter('wp_mail_from_name', 'dw_fromname');
function dw_fromname($email){
   	$wpfrom = get_option('blogname');
    return $wpfrom;
}

add_filter( 'embed_oembed_html', 'dw_oembed_filter', 10, 4 ) ;
function dw_oembed_filter($html, $url, $attr, $post_ID) {
    $return = '<div class="video-container">'.$html.'</div>';
	$return = str_replace('frameborder="0" allowfullscreen', 'style="border:none"', $return);
    return $return;
}

add_filter( 'embed_oembed_html', 'dw_embed_oembed_html' );
function dw_embed_oembed_html( $html ) {
    return preg_replace( '@src="https?:@', 'src="', $html );
}


add_action( 'admin_menu', 'daw_is_online_add_admin_menu' );
add_action( 'admin_init', 'daw_is_online_settings_init' );

function daw_is_online_add_admin_menu(  ) {
	add_options_page( 'DW Status', 'DAW Online', 'manage_options', 'daw_is_online', 'daw_is_online_options_page' );
}

function daw_is_online_settings_init(  ) {
	register_setting( 'dwstatus', 'daw_is_online_settings' );
	add_settings_section('daw_is_online_pluginPage_section', __( 'DAW Online', 'wordpress' ), 'daw_is_online_settings_section_callback', 'dwstatus');
	add_settings_field( 'daw_is_online_select_field_0', __( 'Am I Online ?', 'wordpress' ), 'daw_is_online_select_field_0_render', 'dwstatus', 'daw_is_online_pluginPage_section' );
}

function daw_is_online_select_field_0_render(  ) {
	$options = get_option( 'daw_is_online_settings' ); ?>
	<select name='daw_is_online_settings[daw_is_online_select_field_0]'>
		<option value='1' <?php selected( $options['daw_is_online_select_field_0'], 1 ); ?>>Yes</option>
		<option value='2' <?php selected( $options['daw_is_online_select_field_0'], 2 ); ?>>No</option>
	</select>
<?php }

function daw_is_online_settings_section_callback(  ) {
	echo __( 'This sets my online status', 'wordpress' );
}

function daw_is_online_options_page(  ) { ?>
	<form action='options.php' method='post'>
		<?php settings_fields( 'dwstatus' );
		do_settings_sections( 'dwstatus' );
		submit_button(); ?>
	</form>
<?php }

function dw_online_get_status() {
	$dw_status = get_option('daw_is_online_settings');
	if( $dw_status['daw_is_online_select_field_0'] == '1' ) {
	wp_enqueue_style( 'message', get_template_directory_uri() . '/css/messenger.css');
?>
<script type="text/javascript">function txt_dave(){$.ajax({url:"https://davidawindham.com/wha/phony/sms.php",dataType:"json",success:function(){}})}$(function(){var a;a=0,Messenger.options={extraClasses:"messenger-fixed messenger-on-bottom messenger-on-left",theme:"flat"};var b;b=Messenger().post({message:"I'm Currently Online",type:'success',actions:{cancel:{label:"Connect",delay:60,action:function(){txt_dave();var c;c=Messenger().run({errorMessage:"Please wait a moment while I get connected with you.",successMessage:"Connecting",action:function(c){return b.hide(),++a<2?c.error({status:500,readyState:0,responseText:0}):(window.location.href="http://chat.davidawindham.com",b.update({message:"Redirect to Chat",hideAfter:20,type:"success",actions:!1}))}})}}},retry:{label:"No Thanks",action:function(){return b.update({message:"Maybe Next Time",hideAfter:1,type:"error",actions:!1})}}})});</script>
<?php }
}

function dw_online_get_chat() {
	$dw_status = get_option('daw_is_online_settings');
	if( $dw_status['daw_is_online_select_field_0'] == '1' ) { ?>
		<script type="text/javascript">
		Messenger.options = {extraClasses: "messenger-fixed messenger-on-bottom messenger-on-left",theme: "flat"};var i;i = 0;
		$(function(){
			var msg_return;
			msg_return = Messenger().run({
				id: 'one',
				errorMessage: 'Looking for David...',
				action: function(opts) {if (++i < 2) {get_dave();return opts.error({status: 500,readyState: 0,responseText: 0});}}});});
		function get_dave() {$(function poll() {var x = 0; var countTimer = setInterval(function () {if(x > 5){clearInterval(countTimer)}else if(x == 5){dave_not_available()}else{var URLchatAPI = "http://code.davidawindham.com:8080/status";var request = $.ajax({url: URLchatAPI,dataType: 'json',cache: false, success: function (data) {online = data.online;if(online=='yes') {$('.chat').modal('show');x = x+5;};if (online=='no') {dave_connecting();};},error: function ( xhr, tStatus, err ) {dave_error();x = x+5;}});x++;}}, 5000);});}
		function dave_connecting(){var y;y = 0;var msg_waiting; msg_waiting = Messenger().run({id: 'one',hideAfter: 4,errorMessage: 'Waiting on David...',action: function(opts) {if (++y < 2) {get_dave();return opts.error({status: 500,readyState: 0,responseText: 0});}}});}
		function dave_not_available(){var msg_error; msg_error = Messenger().post({message: 'Sorry, but I am busy.',type: 'error',id: 'one',showCloseButton: true,actions:{cancel: {label: 'Leave me a message',action: function(){window.location.href = '../';}}}});}
		function dave_error(){var msg_error; msg_error = Messenger().post({message: 'Oops. Something has gone wrong.',type: 'error',id: 'one',showCloseButton: true,actions:{cancel: {label: 'Leave me a message',action: function(){window.location.href = '../';}}}});}
		$(function() {if (window.location.hash.indexOf("chat") !== -1) {$('.chat').modal('show');}});
		</script>
	<?php }
}

function dw_deliver_form_mail() {
	if ( isset( $_POST['dw-contact'] ) ) {
		$fname = sanitize_text_field( $_POST["dw-fname"] );
		$lname = sanitize_text_field( $_POST["dw-lname"] );
		$email = sanitize_email( $_POST["dw-email"] );
		$subject = sanitize_text_field( $_POST["dw-org"] );
		$message = esc_textarea( $_POST["dw-message"] );
		$to = get_option( 'admin_email' );
		$headers = "From: $fname $lname <$email>" . "\r\n";

		if ( wp_mail( $to, $subject, $message, $headers ) ) {
			echo '<div class="alert alert-success" role="alert">';
			echo '<p>Thank you. I will be in touch.</p>';
			echo '</div>';
		}
		else {
			echo '<div class="alert alert-danger" role="alert">';
			echo '<p>Error, please try again</p>';
			echo '</div>';
		}
	}
}

add_shortcode('dwsearch', 'get_search_form');

function doctype_opengraph($output) {
    return $output . '
    xmlns:og="http://opengraphprotocol.org/schema/"
    xmlns:fb="http://www.facebook.com/2008/fbml"';
}
add_filter('language_attributes', 'doctype_opengraph');

function dw_opengraph() {
  global $post;
  if (is_single()) {
    if (has_post_thumbnail($post->ID)) {
      $img_src = wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ), 'full'); $img_src = $img_src[0];
    } elseif ( metadata_exists( 'post', get_the_ID(), 'featured_image_url' ) ){
      $img_src = get_post_meta( get_the_ID(), 'featured_image_url', true );
    } else {
      $img_src = get_stylesheet_directory_uri() . '/img/opengraph_image.jpg';
    }
    if($excerpt = $post->post_excerpt) {
      $excerpt = strip_tags($post->post_excerpt);
      $excerpt = str_replace("", "'", $excerpt);
    } else {
      $excerpt = get_bloginfo('description');
    }
?>
  <meta property="og:title" content="<?php echo the_title(); ?>"/>
  <meta property="og:description" content="<?php echo $excerpt; ?>"/>
  <meta property="og:type" content="article"/>
  <meta property="og:url" content="<?php echo the_permalink(); ?>"/>
  <meta property="og:site_name" content="<?php echo get_bloginfo(); ?>"/>
  <meta property="og:image" content="<?php echo $img_src; ?>"/>
  <meta property="fb:app_id" content="203136806559589" />
  <meta name="twitter:site" content="@windhamdavid">
  <meta name="twitter:creator" content="@windhamdavid">
  <meta name="twitter:title" content="<?php echo the_title(); ?>">
  <meta name="twitter:description" content="<?php echo $excerpt; ?>">
  <meta name="twitter:image" content="<?php echo $img_src; ?>">
<?php
    } else {
      return;
    }
	 $content = do_shortcode( apply_filters( 'the_content', $post->post_content ) );
	 $media_url = get_post_meta( get_the_ID(), 'media', true );
	 $media = get_media_embedded_in_content( $content );
	 if( !empty($media) ) {
	         $video_url = $media[0];
	?>
	<meta property="og:video" content="<?php echo $media_url; ?>" />
	<meta property="og:video:secure_url" content="<?php echo $media_url; ?>" />
	<meta property="og:video:width" content="1280" />
	<meta property="og:video:height" content="720" />
	<meta property="og:video:type" content="video/mp4" />
	<meta name="twitter:card" content="player">
	<meta name="twitter:player" content="<?php echo get_permalink();?>container/" />
	<meta name="twitter:player:width" content="1280" />
	<meta name="twitter:player:height" content="720" />
	<meta name="twitter:player:stream" content="<?php echo $media_url; ?>" />
	<meta name="twitter:player:stream:content_type" content="video/mp4" />
<?php
    } else {
?>
  <meta name="twitter:card" content="summary_large_image">
<?php
    }
}
add_action('wp_head', 'dw_opengraph', 5);


function dw_read_container_endpoint(){
	add_rewrite_endpoint( 'container', EP_PERMALINK);
}
add_action( 'init', 'dw_read_container_endpoint' );

function dw_read_container_template( $template = '' ) {
    global $wp_query;
    if( ! array_key_exists( 'container', $wp_query->query_vars ) ) return $template;
    $template = locate_template( 'single-container.php' );
    return $template;
}
add_filter( 'single_template', 'dw_read_container_template' );

function dw_video_embed( $attr, $content='' ) {
  if ( ! isset( $attr['poster'] ) && has_post_thumbnail() ) {
	$poster = get_post_meta( get_the_ID(), 'media-poster', true );
    $attr['poster'] = $poster;
  }
  return wp_video_shortcode( $attr, $content );
}
add_shortcode( 'video', 'dw_video_embed' );


?>
