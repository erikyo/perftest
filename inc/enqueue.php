<?php

if ( ! function_exists( 'perftest_performance_start' ) ) :

    /**
     * The performance.now() method returns a DOMHighResTimeStamp, measured in milliseconds.
     *
     * https://developer.mozilla.org/en-US/docs/web/api/performance/now
     *
     * @return void
     */
    function perftest_performance_start() {
        ?><script>const t0 = performance.now();</script><?php
    }

endif;

add_action( 'wp_head', 'perftest_performance_start',1 );


if ( ! function_exists( 'perftest_performance_end' ) ) :

    /**
     * The performance.now() method returns a DOMHighResTimeStamp, measured in milliseconds.
     *
     * https://developer.mozilla.org/en-US/docs/web/api/performance/now
     *
     * @return void
     */
    function perftest_performance_end() {
        ?><script>
        const t1 = performance.now();
        window.addEventListener(`load`, function () {
          const t2 = performance.now();
          const logWrapper = document.createElement(`p`);

          const log = `Load took ${Math.round((t1 - t0) * .1) / 100 } seconds, fully loaded in ${Math.round((t2 - t0) * .1) / 100} seconds.`;

          logWrapper.textContent = log;
          document.querySelector(`footer.wp-block-template-part > div `).appendChild(logWrapper);

          console.log(log);
        });
      </script><?php
    }

endif;

add_action( 'wp_footer', 'perftest_performance_end', 99 );



if ( ! function_exists( 'perftest_header_styles' ) ) :

    /**
     * Enqueue styles.
     *
     * @since PerfTest 1.0
     *
     * @return void
     */
    function perftest_header_styles() {
        // Register theme stylesheet.
        $theme_version = wp_get_theme()->get( 'Version' );

        $version_string = is_string( $theme_version ) ? $theme_version : false;
        wp_register_style( 'perftest-style', get_template_directory_uri() . '/style.css', array(), $version_string );

        // Add styles inline.
        wp_add_inline_style( 'perftest-style', perftest_get_font_face_styles() );

        // Enqueue theme stylesheet.
        wp_enqueue_style( 'perftest-style' );


        // Enqueue the wordpress.org styles
        wp_enqueue_style( 'dashicons-css', get_template_directory_uri() . '/assets/styles/dashicons.min.css', array(), $version_string );
        wp_enqueue_style( 'admin-bar-css', get_template_directory_uri() . '/assets/styles/admin-bar.min.css', array(), $version_string );
        wp_enqueue_style( 'wp-block-library-css', get_template_directory_uri() . '/assets/styles/style.css', array(), $version_string );
        wp_enqueue_style( 'mediaelement-css', get_template_directory_uri() . '/assets/styles/mediaelementplayer-legacy.min.css', array(), $version_string );
        wp_enqueue_style( 'wp-mediaelement-css', get_template_directory_uri() . '/assets/styles/wp-mediaelement.min.css', array(), $version_string );
        wp_enqueue_style( 'open-sans-css', get_template_directory_uri() . '/assets/styles/css2.css', array(), $version_string );
        wp_enqueue_style( 'wporg-style-css', get_template_directory_uri() . '/assets/styles/style(1).css', array(), $version_string );
        wp_enqueue_style( 'wporg-global-fonts-css', get_template_directory_uri() . '/assets/styles/style(2).css', array(), $version_string );
        wp_enqueue_style( 'wporg-global-header-footer-css', get_template_directory_uri() . '/assets/styles/style(3).css', array(), $version_string );
    }

endif;

add_action( 'wp_enqueue_scripts', 'perftest_header_styles' );


if ( ! function_exists( 'perftest_head_scripts' ) ) :

    /**
     * Enqueue header scripts.
     *
     * @since PerfTest 1.0
     *
     * @return void
     */
    function perftest_head_scripts() {
        // Register theme stylesheet.
        $theme_version = wp_get_theme()->get( 'Version' );

        $version_string = is_string( $theme_version ) ? $theme_version : false;

        wp_enqueue_script( 'wp-emoji', get_template_directory_uri() . '/assets/scripts/wp-emoji-release.min.js', array(), $version_string );
    }

endif;

add_action( 'wp_enqueue_scripts', 'perftest_head_scripts' );

if ( ! function_exists( 'perftest_footer_scripts' ) ) :

    /**
     * Enqueue header scripts.
     *
     * @since PerfTest 1.0
     *
     * @return void
     */
    function perftest_footer_scripts() {
        // Register theme stylesheet.
        $theme_version = wp_get_theme()->get( 'Version' );

        $version_string = is_string( $theme_version ) ? $theme_version : false;

        wp_enqueue_script( 'load-scripts', get_template_directory_uri() . '/assets/scripts/load-scripts.js', array(), $version_string );
        wp_enqueue_script( 'skip-link-focus-fix', get_template_directory_uri() . '/assets/scripts/skip-link-focus-fix.min.js', array(), $version_string );
        wp_enqueue_script( 'e-202214', get_template_directory_uri() . '/assets/scripts/e-202214.js', array(), $version_string );
    }

endif;

add_action( 'wp_enqueue_scripts', 'perftest_footer_scripts' );





if ( ! function_exists( 'perftest_editor_styles' ) ) :

    /**
     * Enqueue editor styles.
     *
     * @since PerfTest 1.0
     *
     * @return void
     */
    function perftest_editor_styles() {

        // Add styles inline.
        wp_add_inline_style( 'wp-block-library', perftest_get_font_face_styles() );

    }

endif;

add_action( 'admin_init', 'perftest_editor_styles' );


if ( ! function_exists( 'perftest_get_font_face_styles' ) ) :

    /**
     * Get font face styles.
     * Called by functions perftest_styles() and perftest_editor_styles() above.
     *
     * @since PerfTest 1.0
     *
     * @return string
     */
    function perftest_get_font_face_styles() {

        return "
		@font-face{
			font-family: 'Source Serif Pro';
			font-weight: 200 900;
			font-style: normal;
			font-stretch: normal;
			font-display: swap;
			src: url('" . get_theme_file_uri( 'assets/fonts/SourceSerif4Variable-Roman.ttf.woff2' ) . "') format('woff2');
		}

		@font-face{
			font-family: 'Source Serif Pro';
			font-weight: 200 900;
			font-style: italic;
			font-stretch: normal;
			font-display: swap;
			src: url('" . get_theme_file_uri( 'assets/fonts/SourceSerif4Variable-Italic.ttf.woff2' ) . "') format('woff2');
		}
		";

    }

endif;

if ( ! function_exists( 'perftest_preload_webfonts' ) ) :

    /**
     * Preloads the main web font to improve performance.
     *
     * Only the main web font (font-style: normal) is preloaded here since that font is always relevant (it is used
     * on every heading, for example). The other font is only needed if there is any applicable content in italic style,
     * and therefore preloading it would in most cases regress performance when that font would otherwise not be loaded
     * at all.
     *
     * @since PerfTest 1.0
     *
     * @return void
     */
    function perftest_preload_webfonts() {
        ?>
        <link rel="preload" href="<?php echo esc_url( get_theme_file_uri( 'assets/fonts/SourceSerif4Variable-Roman.ttf.woff2' ) ); ?>" as="font" type="font/woff2" crossorigin>
        <?php
    }

endif;

add_action( 'wp_head', 'perftest_preload_webfonts' );

if ( ! function_exists( 'perftest_inline_head_style' ) ) :

    /**
     * adds the inline style in head
     *
     * @since PerfTest 1.0
     *
     * @return void
     */
    function perftest_inline_head_style() {
        ?>
        <style id="global-styles-for-classic-themes">
          body{--wp--preset--color--black: #000000;--wp--preset--color--cyan-bluish-gray: #abb8c3;--wp--preset--color--white: #ffffff;--wp--preset--color--pale-pink: #f78da7;--wp--preset--color--vivid-red: #cf2e2e;--wp--preset--color--luminous-vivid-orange: #ff6900;--wp--preset--color--luminous-vivid-amber: #fcb900;--wp--preset--color--light-green-cyan: #7bdcb5;--wp--preset--color--vivid-green-cyan: #00d084;--wp--preset--color--pale-cyan-blue: #8ed1fc;--wp--preset--color--vivid-cyan-blue: #0693e3;--wp--preset--color--vivid-purple: #9b51e0;--wp--preset--color--darker-grey: #1c2024;--wp--preset--color--dark-grey: #23282d;--wp--preset--color--dark-strokes-grey: #2f363d;--wp--preset--color--light-grey: #d9d9d9;--wp--preset--color--beige: #e4ddd4;--wp--preset--color--beige-2: #d3ccc3;--wp--preset--color--blue-1: #3e58e1;--wp--preset--color--blue-2: #213fd4;--wp--preset--color--blue-3: #7b90ff;--wp--preset--color--blue-5: #2541D6;--wp--preset--color--off-white: #f8f3ec;--wp--preset--color--off-white-2: #eff2ff;--wp--preset--color--light-salmon: #ffe9de;--wp--preset--color--green: #72d1a7;--wp--preset--color--success-green: #00a32a;--wp--preset--color--coral-red: #f86368;--wp--preset--gradient--vivid-cyan-blue-to-vivid-purple: linear-gradient(135deg,rgba(6,147,227,1) 0%,rgb(155,81,224) 100%);--wp--preset--gradient--light-green-cyan-to-vivid-green-cyan: linear-gradient(135deg,rgb(122,220,180) 0%,rgb(0,208,130) 100%);--wp--preset--gradient--luminous-vivid-amber-to-luminous-vivid-orange: linear-gradient(135deg,rgba(252,185,0,1) 0%,rgba(255,105,0,1) 100%);--wp--preset--gradient--luminous-vivid-orange-to-vivid-red: linear-gradient(135deg,rgba(255,105,0,1) 0%,rgb(207,46,46) 100%);--wp--preset--gradient--very-light-gray-to-cyan-bluish-gray: linear-gradient(135deg,rgb(238,238,238) 0%,rgb(169,184,195) 100%);--wp--preset--gradient--cool-to-warm-spectrum: linear-gradient(135deg,rgb(74,234,220) 0%,rgb(151,120,209) 20%,rgb(207,42,186) 40%,rgb(238,44,130) 60%,rgb(251,105,98) 80%,rgb(254,248,76) 100%);--wp--preset--gradient--blush-light-purple: linear-gradient(135deg,rgb(255,206,236) 0%,rgb(152,150,240) 100%);--wp--preset--gradient--blush-bordeaux: linear-gradient(135deg,rgb(254,205,165) 0%,rgb(254,45,45) 50%,rgb(107,0,62) 100%);--wp--preset--gradient--luminous-dusk: linear-gradient(135deg,rgb(255,203,112) 0%,rgb(199,81,192) 50%,rgb(65,88,208) 100%);--wp--preset--gradient--pale-ocean: linear-gradient(135deg,rgb(255,245,203) 0%,rgb(182,227,212) 50%,rgb(51,167,181) 100%);--wp--preset--gradient--electric-grass: linear-gradient(135deg,rgb(202,248,128) 0%,rgb(113,206,126) 100%);--wp--preset--gradient--midnight: linear-gradient(135deg,rgb(2,3,129) 0%,rgb(40,116,252) 100%);--wp--preset--duotone--dark-grayscale: url('#wp-duotone-dark-grayscale');--wp--preset--duotone--grayscale: url('#wp-duotone-grayscale');--wp--preset--duotone--purple-yellow: url('#wp-duotone-purple-yellow');--wp--preset--duotone--blue-red: url('#wp-duotone-blue-red');--wp--preset--duotone--midnight: url('#wp-duotone-midnight');--wp--preset--duotone--magenta-yellow: url('#wp-duotone-magenta-yellow');--wp--preset--duotone--purple-green: url('#wp-duotone-purple-green');--wp--preset--duotone--blue-orange: url('#wp-duotone-blue-orange');--wp--preset--font-size--small: 14px;--wp--preset--font-size--medium: 20px;--wp--preset--font-size--large: 20px;--wp--preset--font-size--x-large: 42px;--wp--preset--font-size--tiny: 12px;--wp--preset--font-size--normal: 16px;--wp--preset--font-size--extra-large: 24px;--wp--preset--font-size--huge: 32px;--wp--preset--font-family--eb-garamond: 'EB Garamond', serif;--wp--preset--font-family--inter: 'Inter', sans-serif;--wp--custom--alignment--aligned-max-width: 50%;--wp--custom--alignment--edge-spacing: clamp( 24px, calc( 100vw / 18 ), 80px );--wp--custom--button--color--background: var(--wp--preset--color--blue-1);--wp--custom--button--color--text: var(--wp--preset--color--white);--wp--custom--button--border--color: var(--wp--preset--color--blue-1);--wp--custom--button--border--radius: 2px;--wp--custom--button--border--style: solid;--wp--custom--button--border--width: 2px;--wp--custom--button--hover--color--background: var(--wp--preset--color--blue-2);--wp--custom--button--hover--color--text: var(--wp--preset--color--white);--wp--custom--button--hover--border--color: var(--wp--preset--color--blue-2);--wp--custom--button--active--color--background: var(--wp--preset--color--black);--wp--custom--button--active--color--text: var(--wp--preset--color--white);--wp--custom--button--active--border--color: var(--wp--preset--color--black);--wp--custom--button--focus--color--background: var(--wp--preset--color--dark-grey);--wp--custom--button--focus--color--text: var(--wp--preset--color--white);--wp--custom--button--focus--border--color: var(--wp--preset--color--dark-grey);--wp--custom--button--spacing--padding--top: 0.2em;--wp--custom--button--spacing--padding--bottom: var(--wp--custom--button--spacing--padding--top);--wp--custom--button--spacing--padding--left: 1em;--wp--custom--button--spacing--padding--right: var(--wp--custom--button--spacing--padding--left);--wp--custom--button--typography--font-size: var(--wp--preset--font-size--normal);--wp--custom--button--typography--font-weight: normal;--wp--custom--button--typography--line-height: 2;--wp--custom--code--typography--font-family: monospace;--wp--custom--form--padding: calc( 0.5 * var(--wp--custom--margin--horizontal) );--wp--custom--form--border--color: #EFEFEF;--wp--custom--form--border--radius: 0;--wp--custom--form--border--style: solid;--wp--custom--form--border--width: 2px;--wp--custom--form--checkbox--checked--content: "\2715";--wp--custom--form--checkbox--checked--font-size: 14px;--wp--custom--form--checkbox--checked--position--left: 3px;--wp--custom--form--checkbox--checked--position--top: 4px;--wp--custom--form--checkbox--checked--sizing--height: 12px;--wp--custom--form--checkbox--checked--sizing--width: 12px;--wp--custom--form--checkbox--unchecked--content: "";--wp--custom--form--checkbox--unchecked--position--left: 0;--wp--custom--form--checkbox--unchecked--position--top: 0.2em;--wp--custom--form--checkbox--unchecked--sizing--height: 16px;--wp--custom--form--checkbox--unchecked--sizing--width: 16px;--wp--custom--form--color--background: transparent;--wp--custom--form--color--box-shadow: none;--wp--custom--form--label--typography--font-size: var(--wp--preset--font-size--tiny);--wp--custom--gallery--caption--font-size: var(--wp--preset--font-size--small);--wp--custom--body--typography--line-height: 1.9;--wp--custom--heading--typography--font-family: var(--wp--preset--font-family--eb-garamond);--wp--custom--heading--typography--font-weight: 400;--wp--custom--heading--typography--line-height: 1.3;--wp--custom--h-1--breakpoint--mobile--typography--font-size: 70px;--wp--custom--h-1--breakpoint--mobile--typography--line-height: 1.1;--wp--custom--h-1--typography--font-size: 38px;--wp--custom--h-1--typography--line-height: var(--wp--custom--heading--typography--line-height);--wp--custom--h-2--breakpoint--mobile--typography--font-size: clamp(26px, 4vw, 50px);--wp--custom--h-2--typography--font-size: 26px;--wp--custom--h-2--typography--line-height: var(--wp--custom--heading--typography--line-height);--wp--custom--h-3--breakpoint--mobile--typography--font-size: clamp(26px, 4vw, 36px);--wp--custom--h-3--breakpoint--mobile--typography--line-height: 1.3;--wp--custom--h-3--typography--font-size: 26px;--wp--custom--h-3--typography--line-height: var(--wp--custom--heading--typography--line-height);--wp--custom--h-4--breakpoint--mobile--typography--font-size: 30px;--wp--custom--h-4--typography--font-size: 20px;--wp--custom--h-4--typography--line-height: var(--wp--custom--heading--typography--line-height);--wp--custom--h-5--breakpoint--mobile--typography--font-size: 26px;--wp--custom--h-5--breakpoint--mobile--typography--line-height: 1.35;--wp--custom--h-5--typography--font-size: 20px;--wp--custom--h-5--typography--line-height: var(--wp--custom--heading--typography--line-height);--wp--custom--h-6--typography--font-size: var(--wp--preset--font-size--normal);--wp--custom--h-6--typography--line-height: var(--wp--custom--heading--typography--line-height);--wp--custom--layout--content-size: 680px;--wp--custom--layout--wide-size: 1070px;--wp--custom--layout--content-meta-size: calc( var(--wp--custom--layout--wide-size) - var(--wp--custom--layout--content-size) );--wp--custom--list--spacing--padding--left: var(--wp--custom--margin--horizontal);--wp--custom--margin--baseline: 10px;--wp--custom--margin--horizontal: 30px;--wp--custom--margin--vertical: 30px;--wp--custom--paragraph--dropcap--margin: .1em .1em 0 0;--wp--custom--paragraph--dropcap--typography--font-size: 110px;--wp--custom--paragraph--dropcap--typography--font-weight: 400;--wp--custom--paragraph--padding--horizontal: var(--wp--custom--alignment--edge-spacing);--wp--custom--paragraph--padding--vertical: var(--wp--custom--alignment--edge-spacing);--wp--custom--post-comment--typography--font-size: var(--wp--preset--font-size--normal);--wp--custom--post-comment--typography--line-height: var(--wp--custom--body--typography--line-height);--wp--custom--pullquote--citation--typography--font-size: 30px;--wp--custom--pullquote--citation--typography--font-family: inherit;--wp--custom--pullquote--citation--typography--font-style: italic;--wp--custom--pullquote--citation--spacing--margin--top: var(--wp--custom--margin--vertical);--wp--custom--pullquote--typography--text-align: left;--wp--custom--quote--citation--typography--font-size: 20px;--wp--custom--quote--citation--typography--font-family: inherit;--wp--custom--quote--citation--typography--font-style: normal;--wp--custom--quote--typography--text-align: left;--wp--custom--separator--opacity: 1;--wp--custom--separator--margin: var(--wp--custom--margin--vertical) auto;--wp--custom--separator--width: 150px;--wp--custom--table--figcaption--typography--font-size: var(--wp--preset--font-size--tiny);}.has-black-color{color: var(--wp--preset--color--black) !important;}.has-cyan-bluish-gray-color{color: var(--wp--preset--color--cyan-bluish-gray) !important;}.has-white-color{color: var(--wp--preset--color--white) !important;}.has-pale-pink-color{color: var(--wp--preset--color--pale-pink) !important;}.has-vivid-red-color{color: var(--wp--preset--color--vivid-red) !important;}.has-luminous-vivid-orange-color{color: var(--wp--preset--color--luminous-vivid-orange) !important;}.has-luminous-vivid-amber-color{color: var(--wp--preset--color--luminous-vivid-amber) !important;}.has-light-green-cyan-color{color: var(--wp--preset--color--light-green-cyan) !important;}.has-vivid-green-cyan-color{color: var(--wp--preset--color--vivid-green-cyan) !important;}.has-pale-cyan-blue-color{color: var(--wp--preset--color--pale-cyan-blue) !important;}.has-vivid-cyan-blue-color{color: var(--wp--preset--color--vivid-cyan-blue) !important;}.has-vivid-purple-color{color: var(--wp--preset--color--vivid-purple) !important;}.has-darker-grey-color{color: var(--wp--preset--color--darker-grey) !important;}.has-dark-grey-color{color: var(--wp--preset--color--dark-grey) !important;}.has-dark-strokes-grey-color{color: var(--wp--preset--color--dark-strokes-grey) !important;}.has-light-grey-color{color: var(--wp--preset--color--light-grey) !important;}.has-beige-color{color: var(--wp--preset--color--beige) !important;}.has-beige-2-color{color: var(--wp--preset--color--beige-2) !important;}.has-blue-1-color{color: var(--wp--preset--color--blue-1) !important;}.has-blue-2-color{color: var(--wp--preset--color--blue-2) !important;}.has-blue-3-color{color: var(--wp--preset--color--blue-3) !important;}.has-blue-5-color{color: var(--wp--preset--color--blue-5) !important;}.has-off-white-color{color: var(--wp--preset--color--off-white) !important;}.has-off-white-2-color{color: var(--wp--preset--color--off-white-2) !important;}.has-light-salmon-color{color: var(--wp--preset--color--light-salmon) !important;}.has-green-color{color: var(--wp--preset--color--green) !important;}.has-success-green-color{color: var(--wp--preset--color--success-green) !important;}.has-coral-red-color{color: var(--wp--preset--color--coral-red) !important;}.has-black-background-color{background-color: var(--wp--preset--color--black) !important;}.has-cyan-bluish-gray-background-color{background-color: var(--wp--preset--color--cyan-bluish-gray) !important;}.has-white-background-color{background-color: var(--wp--preset--color--white) !important;}.has-pale-pink-background-color{background-color: var(--wp--preset--color--pale-pink) !important;}.has-vivid-red-background-color{background-color: var(--wp--preset--color--vivid-red) !important;}.has-luminous-vivid-orange-background-color{background-color: var(--wp--preset--color--luminous-vivid-orange) !important;}.has-luminous-vivid-amber-background-color{background-color: var(--wp--preset--color--luminous-vivid-amber) !important;}.has-light-green-cyan-background-color{background-color: var(--wp--preset--color--light-green-cyan) !important;}.has-vivid-green-cyan-background-color{background-color: var(--wp--preset--color--vivid-green-cyan) !important;}.has-pale-cyan-blue-background-color{background-color: var(--wp--preset--color--pale-cyan-blue) !important;}.has-vivid-cyan-blue-background-color{background-color: var(--wp--preset--color--vivid-cyan-blue) !important;}.has-vivid-purple-background-color{background-color: var(--wp--preset--color--vivid-purple) !important;}.has-darker-grey-background-color{background-color: var(--wp--preset--color--darker-grey) !important;}.has-dark-grey-background-color{background-color: var(--wp--preset--color--dark-grey) !important;}.has-dark-strokes-grey-background-color{background-color: var(--wp--preset--color--dark-strokes-grey) !important;}.has-light-grey-background-color{background-color: var(--wp--preset--color--light-grey) !important;}.has-beige-background-color{background-color: var(--wp--preset--color--beige) !important;}.has-beige-2-background-color{background-color: var(--wp--preset--color--beige-2) !important;}.has-blue-1-background-color{background-color: var(--wp--preset--color--blue-1) !important;}.has-blue-2-background-color{background-color: var(--wp--preset--color--blue-2) !important;}.has-blue-3-background-color{background-color: var(--wp--preset--color--blue-3) !important;}.has-blue-5-background-color{background-color: var(--wp--preset--color--blue-5) !important;}.has-off-white-background-color{background-color: var(--wp--preset--color--off-white) !important;}.has-off-white-2-background-color{background-color: var(--wp--preset--color--off-white-2) !important;}.has-light-salmon-background-color{background-color: var(--wp--preset--color--light-salmon) !important;}.has-green-background-color{background-color: var(--wp--preset--color--green) !important;}.has-success-green-background-color{background-color: var(--wp--preset--color--success-green) !important;}.has-coral-red-background-color{background-color: var(--wp--preset--color--coral-red) !important;}.has-black-border-color{border-color: var(--wp--preset--color--black) !important;}.has-cyan-bluish-gray-border-color{border-color: var(--wp--preset--color--cyan-bluish-gray) !important;}.has-white-border-color{border-color: var(--wp--preset--color--white) !important;}.has-pale-pink-border-color{border-color: var(--wp--preset--color--pale-pink) !important;}.has-vivid-red-border-color{border-color: var(--wp--preset--color--vivid-red) !important;}.has-luminous-vivid-orange-border-color{border-color: var(--wp--preset--color--luminous-vivid-orange) !important;}.has-luminous-vivid-amber-border-color{border-color: var(--wp--preset--color--luminous-vivid-amber) !important;}.has-light-green-cyan-border-color{border-color: var(--wp--preset--color--light-green-cyan) !important;}.has-vivid-green-cyan-border-color{border-color: var(--wp--preset--color--vivid-green-cyan) !important;}.has-pale-cyan-blue-border-color{border-color: var(--wp--preset--color--pale-cyan-blue) !important;}.has-vivid-cyan-blue-border-color{border-color: var(--wp--preset--color--vivid-cyan-blue) !important;}.has-vivid-purple-border-color{border-color: var(--wp--preset--color--vivid-purple) !important;}.has-darker-grey-border-color{border-color: var(--wp--preset--color--darker-grey) !important;}.has-dark-grey-border-color{border-color: var(--wp--preset--color--dark-grey) !important;}.has-dark-strokes-grey-border-color{border-color: var(--wp--preset--color--dark-strokes-grey) !important;}.has-light-grey-border-color{border-color: var(--wp--preset--color--light-grey) !important;}.has-beige-border-color{border-color: var(--wp--preset--color--beige) !important;}.has-beige-2-border-color{border-color: var(--wp--preset--color--beige-2) !important;}.has-blue-1-border-color{border-color: var(--wp--preset--color--blue-1) !important;}.has-blue-2-border-color{border-color: var(--wp--preset--color--blue-2) !important;}.has-blue-3-border-color{border-color: var(--wp--preset--color--blue-3) !important;}.has-blue-5-border-color{border-color: var(--wp--preset--color--blue-5) !important;}.has-off-white-border-color{border-color: var(--wp--preset--color--off-white) !important;}.has-off-white-2-border-color{border-color: var(--wp--preset--color--off-white-2) !important;}.has-light-salmon-border-color{border-color: var(--wp--preset--color--light-salmon) !important;}.has-green-border-color{border-color: var(--wp--preset--color--green) !important;}.has-success-green-border-color{border-color: var(--wp--preset--color--success-green) !important;}.has-coral-red-border-color{border-color: var(--wp--preset--color--coral-red) !important;}.has-vivid-cyan-blue-to-vivid-purple-gradient-background{background: var(--wp--preset--gradient--vivid-cyan-blue-to-vivid-purple) !important;}.has-light-green-cyan-to-vivid-green-cyan-gradient-background{background: var(--wp--preset--gradient--light-green-cyan-to-vivid-green-cyan) !important;}.has-luminous-vivid-amber-to-luminous-vivid-orange-gradient-background{background: var(--wp--preset--gradient--luminous-vivid-amber-to-luminous-vivid-orange) !important;}.has-luminous-vivid-orange-to-vivid-red-gradient-background{background: var(--wp--preset--gradient--luminous-vivid-orange-to-vivid-red) !important;}.has-very-light-gray-to-cyan-bluish-gray-gradient-background{background: var(--wp--preset--gradient--very-light-gray-to-cyan-bluish-gray) !important;}.has-cool-to-warm-spectrum-gradient-background{background: var(--wp--preset--gradient--cool-to-warm-spectrum) !important;}.has-blush-light-purple-gradient-background{background: var(--wp--preset--gradient--blush-light-purple) !important;}.has-blush-bordeaux-gradient-background{background: var(--wp--preset--gradient--blush-bordeaux) !important;}.has-luminous-dusk-gradient-background{background: var(--wp--preset--gradient--luminous-dusk) !important;}.has-pale-ocean-gradient-background{background: var(--wp--preset--gradient--pale-ocean) !important;}.has-electric-grass-gradient-background{background: var(--wp--preset--gradient--electric-grass) !important;}.has-midnight-gradient-background{background: var(--wp--preset--gradient--midnight) !important;}.has-small-font-size{font-size: var(--wp--preset--font-size--small) !important;}.has-medium-font-size{font-size: var(--wp--preset--font-size--medium) !important;}.has-large-font-size{font-size: var(--wp--preset--font-size--large) !important;}.has-x-large-font-size{font-size: var(--wp--preset--font-size--x-large) !important;}.has-tiny-font-size{font-size: var(--wp--preset--font-size--tiny) !important;}.has-normal-font-size{font-size: var(--wp--preset--font-size--normal) !important;}.has-extra-large-font-size{font-size: var(--wp--preset--font-size--extra-large) !important;}.has-huge-font-size{font-size: var(--wp--preset--font-size--huge) !important;}.has-eb-garamond-font-family{font-family: var(--wp--preset--font-family--eb-garamond) !important;}.has-inter-font-family{font-family: var(--wp--preset--font-family--inter) !important;}body { --wp--style--block-gap: 24px; }		</style>
        <style type="text/css">
          img.wp-smiley,
          img.emoji {
            display: inline !important;
            border: none !important;
            box-shadow: none !important;
            height: 1em !important;
            width: 1em !important;
            margin: 0 0.07em !important;
            vertical-align: -0.1em !important;
            background: none !important;
            padding: 0 !important;
          }
        </style>
        <style id="admin-bar-inline-css" type="text/css">

          .admin-bar {
            position: inherit !important;
            top: auto !important;
          }
          .admin-bar .goog-te-banner-frame {
            top: 32px !important
          }
          @media screen and (max-width: 782px) {
            .admin-bar .goog-te-banner-frame {
              top: 46px !important;
            }
          }
          @media screen and (max-width: 480px) {
            .admin-bar .goog-te-banner-frame {
              position: absolute;
            }
          }

        </style>
        <style id="wp-block-library-inline-css" type="text/css">
          .has-text-align-justify{text-align:justify;}
        </style>
        <?php
    }

endif;
add_action( 'wp_head', 'perftest_inline_head_style' );


if ( ! function_exists( 'perftest_inline_footer_style' ) ) :

    /**
     * Enqueue footer inline styles.
     *
     * @since PerfTest 1.0
     *
     * @return void
     */
    function perftest_inline_footer_style() {
        ?>
      <style>.wp-container-2 {display: flex;gap: 0.5em;flex-wrap: wrap;align-items: center;}.wp-container-2 > * { margin: 0; }</style>
      <style>.wp-container-4 {display: flex;gap: 0.5em;flex-wrap: wrap;align-items: center;}.wp-container-4 > * { margin: 0; }</style>
      <style>.wp-container-5 .alignleft { float: left; margin-right: 2em; margin-left: 0; }.wp-container-5 .alignright { float: right; margin-left: 2em; margin-right: 0; }</style>
      <style>.wp-container-6 .alignleft { float: left; margin-right: 2em; margin-left: 0; }.wp-container-6 .alignright { float: right; margin-left: 2em; margin-right: 0; }</style>
      <style>.wp-container-8 {display: flex;gap: 0.5em;flex-wrap: wrap;align-items: center;}.wp-container-8 > * { margin: 0; }</style>
      <style>.wp-container-10 {display: flex;gap: 0.5em;flex-wrap: wrap;align-items: center;}.wp-container-10 > * { margin: 0; }</style>
      <style>.wp-container-12 {display: flex;gap: 0.5em;flex-wrap: wrap;align-items: center;}.wp-container-12 > * { margin: 0; }</style>
      <style>.wp-container-14 {display: flex;gap: 0.5em;flex-wrap: wrap;align-items: center;}.wp-container-14 > * { margin: 0; }</style>
      <style>.wp-container-16 {display: flex;gap: 0.5em;flex-wrap: wrap;align-items: center;}.wp-container-16 > * { margin: 0; }</style>
      <style>.wp-container-17 .alignleft { float: left; margin-right: 2em; margin-left: 0; }.wp-container-17 .alignright { float: right; margin-left: 2em; margin-right: 0; }</style>
      <style>.wp-container-18 {display: flex;gap: 0.5em;flex-wrap: wrap;align-items: center;justify-content: flex-start;}.wp-container-18 > * { margin: 0; }</style>
      <style>.wp-container-19 {display: flex;gap: 0.5em;flex-wrap: wrap;align-items: center;}.wp-container-19 > * { margin: 0; }</style>
      <style>.wp-container-20 .alignleft { float: left; margin-right: 2em; margin-left: 0; }.wp-container-20 .alignright { float: right; margin-left: 2em; margin-right: 0; }</style>
      <style>.wp-container-21 .alignleft { float: left; margin-right: 2em; margin-left: 0; }.wp-container-21 .alignright { float: right; margin-left: 2em; margin-right: 0; }</style>
        <?php
    }

endif;

add_action( 'wp_footer', 'perftest_inline_footer_style' );