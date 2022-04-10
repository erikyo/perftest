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
return array_merge($default_transforms, array(
'application/pdf' => array( 'image/webp' ),
'image/jpeg' => array( 'image/jpeg', 'image/avif', 'image/webp' ),
'image/png'  => array( 'image/png', 'image/webp', 'image/avif' ),
'image/webp' => array( 'image/webp', 'image/jpeg', 'image/avif' ),
'image/avif' => array( 'image/avif', 'image/jpeg', 'image/webp' )
) );
}

add_filter( 'webp_uploads_content_image_mimes', 'webp_uploads_content_image_mimes', 10, 3 );
function webp_uploads_content_image_mimes( $target_mimes, $attachment_id, $context ) {
$target_mimes = array_merge( $target_mimes, array( 'application/pdf', 'image/png', 'image/avif', 'image/gif' ) );
return $target_mimes;
}
