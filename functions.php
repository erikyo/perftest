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
    global $post;

    // return if is front page or backend
    // or if the page doesn't start for "type-"

    $page_data = isset($post->post_name) ? explode("-", $post->post_name) : '';

    if ( is_admin() || is_front_page() || empty( $page_data[0] ) || ! in_array( $page_data[0], array( 'type', 'image' ) ) ) {

        return $content;

    } else {

        if ($page_data[0] == 'type') {

            // extract filetype and quality (if available from page slug)
            list( $slug, $filetype, $quality ) = array_pad( $page_data, 3, false );

            // if the page doesn't start for "type-"
            if ( $quality ) {
                $format_path = get_template_directory_uri() . "/formats/$quality/$filetype";
            } else {
                $format_path = get_template_directory_uri() . "/formats/$filetype";
            }

            // the title
            $content = str_replace( '%%TITLE%%', $filetype, $content );

            //monkey patching mozjpeg extension
            if ( $filetype === 'mozjpeg' ) {
                $filetype = 'jpg';
            }

            // replace %%PATH%% with the path to images folder
            $content = str_replace( '%%PATH%%', $format_path, $content );


            // don't change the extension if the page is the "original"
            return $filetype === 'original' ? $content : str_replace( array( '.jpg', '.png', '.gif' ), "." . strtolower( $filetype ), $content );
        } else {
            return $content;
        }

    }
}
add_filter('render_block', 'perf_image_format_replace', 1  );



function save_img_copy( $format, $quality, $filename, $dest ) {
    $image = new Imagick($filename);
    $image->setImageFormat($format);
    $image->setImageCompressionQuality($quality);
    $image->writeImage($dest.$format);
}
// save_img_copy( 'avif', 24, wp_upload_dir()['basedir'] .'/2022/04/bandh-test-target.jpg', wp_upload_dir()['basedir'] .'/2022/04/bandh.' );
