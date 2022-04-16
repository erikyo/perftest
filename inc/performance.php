<?php

add_filter( 'upload_mimes', 'perf_mime_types' );
function perf_mime_types( $mime_types ) {
    $mime_types['avif']  = 'image/avif';
    $mime_types['heic']  = 'image/heic';
    $mime_types['heif']  = 'image/heif';
    $mime_types['avifs'] = 'image/avif-sequence';
    $mime_types['heics'] = 'image/heic-sequence';
    $mime_types['heifs'] = 'image/heif-sequence';

    return $mime_types;
}

add_filter( 'wp_check_filetype_and_ext', 'perf_add_custom_mime', 10, 4 );
function perf_add_custom_mime( $types, $file, $filename, $mimes ) {
    if ( false !== strpos( $filename, '.avif' ) ) {
        $types['ext']  = 'avif';
        $types['type'] = 'image/avif';
    }

    return $types;
}

add_filter( 'webp_uploads_upload_image_mime_transforms', 'my_custom_mime_transforms' );
function my_custom_mime_transforms( $default_transforms ) {
    return array_merge( $default_transforms, array(
        'application/pdf' => array( 'image/webp' ),
        'image/jpeg'      => array( 'image/jpeg', 'image/avif', 'image/webp' ),
        'image/png'       => array( 'image/png', 'image/webp', 'image/avif' ),
        'image/webp'      => array( 'image/webp', 'image/jpeg', 'image/avif' ),
        'image/avif'      => array( 'image/avif', 'image/jpeg', 'image/webp' )
    ) );
}

add_filter( 'webp_uploads_content_image_mimes', 'webp_uploads_content_image_mimes', 10, 3 );
function webp_uploads_content_image_mimes( $target_mimes, $attachment_id, $context ) {
    $target_mimes = array_merge( $target_mimes, array( 'application/pdf', 'image/png', 'image/avif', 'image/gif' ) );

    return $target_mimes;
}




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