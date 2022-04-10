<?php
/**
 * PerfTest functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Two
 * @since PerfTest 1.0
 */


if ( ! function_exists( 'perftest_support' ) ) :

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * @since PerfTest 1.0
	 *
	 * @return void
	 */
	function perftest_support() {

		// Add support for block styles.
		add_theme_support( 'wp-block-styles' );

		// Enqueue editor styles.
		add_editor_style( 'style.css' );

	}

endif;

add_action( 'after_setup_theme', 'perftest_support' );


include 'inc/block-patterns.php';
include 'inc/enqueue.php';
include 'inc/performance.php';



function perf_image_format_replace( $content ) {

    if (is_admin() || is_front_page()) return $content;

    global $post;
    $post_slug   = $post->post_name;
    $format_path = get_template_directory_uri() . '/formats/' . $post_slug;

    //monkey patch for mozjpeg
    if ($post_slug === 'mozjpeg') $post_slug = 'jpg';

    $content = str_replace( '%%PATH%%', $format_path, $content );
    $content = ($post_slug !== 'original') ? str_replace( array('.jpg', '.png', '.gif'), ".". strtolower($post_slug) , $content ) : $content;
    return $content;
}
add_filter('render_block', 'perf_image_format_replace', 1  );



function save_img_copy( $format, $quality, $filename, $dest ) {
    $image = new Imagick($filename);
    $image->setImageFormat($format);
    $image->setImageCompressionQuality($quality);
    $image->writeImage($dest.$format);
}
// save_img_copy( 'avif', 24, wp_upload_dir()['basedir'] .'/2022/04/bandh-test-target.jpg', wp_upload_dir()['basedir'] .'/2022/04/bandh.' );
