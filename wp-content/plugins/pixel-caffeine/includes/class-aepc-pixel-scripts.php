<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @class AEPC_Pixel_Scripts
 */
class AEPC_Pixel_Scripts {

	/**
	 * Hooks to include scripts.
	 */
	public static function init() {
		// The following are hook in wp_footer, to give ability to function helper to enqueue the code
		add_action( 'wp_footer', array( __CLASS__, 'track_standard_events' ), 5 );
		add_action( 'wp_footer', array( __CLASS__, 'track_advanced_events' ), 5 );
		add_action( 'wp_footer', array( __CLASS__, 'track_custom_fields' ), 5 );
		add_action( 'wp_footer', array( __CLASS__, 'track_conversions_events' ), 5 );

		add_action( 'wp_footer', array( __CLASS__, 'enqueue_scripts' ) );

		if ( 'head' == get_option( 'aepc_pixel_position', 'head' ) ) {
			add_action( 'wp_head', array( __CLASS__, 'pixel_init' ), 99 );
		} else {
			add_action( 'wp_footer', array( __CLASS__, 'pixel_init' ), 1 );
		}

		/**
		 * AMP Integration
		 */
		add_action( 'amp_post_template_footer', array( __CLASS__, 'track_on_amp_pages' ) );
	}

	/**
	 * Arguments to initialize the pixel
	 *
	 * @return array
	 */
	public static function pixel_init_arguments() {
		// All arguments to pass to js
		$args = array(
			'pixel_id' => PixelCaffeine()->get_pixel_id(),
			'user' => array(),
			'enable_advanced_events' => AEPC_Track::is_advanced_events_active() ? 'yes' : 'no',
			'fire_delay' => intval( AEPC_Track::detect_delay_firing( 'PageView' ) ),
		);

		// eCommerce parameters
		if ( AEPC_Addons_Support::are_detected_addons() ) {
			$args = array_merge( $args, array(
				'enable_viewcontent' => self::is_event_enabled( 'viewcontent' ) ? 'yes' : 'no',
				'enable_addtocart' => self::is_event_enabled( 'addtocart' ) ? 'yes' : 'no',
				'enable_addtowishlist' => self::is_event_enabled( 'addtowishlist' ) ? 'yes' : 'no',
				'enable_initiatecheckout' => self::is_event_enabled( 'initiatecheckout' ) ? 'yes' : 'no',
				'enable_addpaymentinfo' => self::is_event_enabled( 'addpaymentinfo' ) ? 'yes' : 'no',
				'enable_purchase' => self::is_event_enabled( 'purchase' ) ? 'yes' : 'no',
				'allowed_params' => array(
					'AddToCart' => AEPC_Track::get_allowed_standard_params( 'AddToCart' ),
					'AddToWishlist' => AEPC_Track::get_allowed_standard_params( 'AddToWishlist' ),
				)
			) );
		}

		// Logged in user information
		if ( 'yes' === get_option( 'aepc_enable_advanced_matching', 'yes' ) && is_user_logged_in() ) {
			$user = wp_get_current_user();

			$args['user'] = array(
				'em' => $user->user_email,
				'fn' => $user->first_name,
				'ln' => $user->last_name
			);

			// Add some extra information about the customer if an ecommerce addon is detected.
			$args['user'] = AEPC_Addons_Support::extend_customer_parameters( $args['user'] );

			$args['user'] = array_filter( $args['user'] );
			$args['user'] = array_map( 'strtolower', $args['user'] );  // It's required by facebook (https://developers.facebook.com/docs/facebook-pixel/pixel-with-ads/conversion-tracking#advanced_match).
		}

		foreach ( (array) $args as $key => $value ) {
			if ( !is_scalar($value) )
				continue;

			$args[$key] = html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8');
		}

		return $args;
	}

	/**
	 * Pass extra parameters to JS in order to add them in all pixels
	 *
	 * @return array
	 */
	public static function pixel_advanced_parameters() {
		$params = array();

		if ( 'yes' === get_option( 'aepc_enable_utm_tags', 'yes' ) ) {
			foreach ( $_GET as $key => $value ) {
				if ( strpos( $key, 'utm_' ) !== false ) {
					$params[ $key ] = urldecode( $value );
				}
			}
		}

		return apply_filters( 'aepc_pixel_extra_args', $params );
	}

	/**
	 * Add the Facebook Pixel snippet
	 */
	public static function pixel_init() {
		?>
		<!-- Facebook Pixel Code -->
		<script>
			<?php if ( ! PixelCaffeine()->is_debug_mode() ) : ?>
			!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
				n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
				n.push=n;n.loaded=!0;n.version='2.0';n.agent='dvpixelcaffeinewordpress';n.queue=[];t=b.createElement(e);t.async=!0;
				t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
				document,'script','https://connect.facebook.net/en_US/fbevents.js');
			<?php else : ?>
			var fbq_calls = [],
				fbq = function() {
					console.log( 'fbq: ', arguments[0], arguments[1], arguments[2] );
					fbq_calls.push( arguments );
				};
			<?php endif; ?>

			var aepc_pixel = <?php echo wp_json_encode( self::pixel_init_arguments() ) ?>,
				aepc_pixel_args = <?php echo wp_json_encode( self::pixel_advanced_parameters() ) ?>,
				aepc_extend_args = function( args ) {
					if ( typeof args === 'undefined' ) {
						args = {};
					}

					for(var key in aepc_pixel_args)
						args[key] = aepc_pixel_args[key];

					return args;
				};

			// Extend args
			if ( 'yes' === aepc_pixel.enable_advanced_events ) {
				aepc_pixel_args.userAgent = navigator.userAgent;
				aepc_pixel_args.language = navigator.language;

				if ( document.referrer.indexOf( document.domain ) < 0 ) {
					aepc_pixel_args.referrer = document.referrer;
				}
			}

			fbq('init', aepc_pixel.pixel_id, aepc_pixel.user);

			<?php /* Trigger the event when delay is passed and where there are all fbq calls that need to wait */ ?>
			setTimeout( function() {
				fbq('track', "PageView", aepc_pixel_args);
			}, aepc_pixel.fire_delay * 1000 );
		</script>
		<?php if ( ! PixelCaffeine()->is_debug_mode() ) : ?>
		<noscript><img height="1" width="1" style="display:none"
		               src="https://www.facebook.com/tr?id=<?php echo esc_attr( PixelCaffeine()->get_pixel_id() ) ?>&ev=PageView&noscript=1"
			/></noscript>
		<?php endif; ?>
		<!-- End Facebook Pixel Code -->
		<?php
	}

	/**
	 * Include the necessary scripts file
	 */
	public static function enqueue_scripts() {
		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'aepc-pixel-events', PixelCaffeine()->build_url( 'frontend.js' ), array( 'jquery' ), PixelCaffeine()->version, true );
		wp_enqueue_script( 'aepc-pixel-events' );

		wp_localize_script( 'aepc-pixel-events', 'aepc_pixel_events', array_filter( array(
			'standard_events' => AEPC_Track::get_standard_events(),
			'custom_events'   => AEPC_Track::get_custom_events(),
			'css_events'      => self::track_css_events(),
			'link_clicks'     => self::track_link_click_events()
		) ) );
	}

	/**
	 * Check if the event is enabled by option
	 *
	 * @param $event
	 * @return bool
	 */
	protected static function is_event_enabled( $event ) {
		return call_user_func( 'AEPC_Track::is_' . strtolower( $event ) . '_active' );
	}

	/**
	 * Standard Events trackable at load page
	 */
	public static function track_standard_events() {

		if ( is_search() ) {
			AEPC_Track::track( 'Search', array( 'search_string' => get_search_query( false ) ) );
		}

		// Fire the dynamic ads events caught by ecommerce addons supported.
		foreach ( AEPC_Addons_Support::get_detected_addons() as $addon ) {
			foreach ( $addon->get_event_supported() as $event_name ) {
				if ( self::is_event_enabled( $event_name ) && $addon->supports_event( $event_name ) && $addon->can_fire( $event_name ) ) {
					$params = AEPC_Track::check_event_parameters( $event_name, $addon->get_parameters_for( $event_name ) );
					AEPC_Track::track( $event_name, $params );
				}
			}
		}
	}

	/**
	 * Track advanced events
	 */
	public static function track_advanced_events() {

		// Exit if the general option is disabled.
		if ( ! AEPC_Track::is_advanced_events_active() ) {
			return;
		}

		// Start!
		$params = array();
		$enable_tax = AEPC_Track::is_taxonomy_events_active();

		// Track login status
		$params['login_status'] = is_user_logged_in() ? 'logged_in' : 'not_logged_in';

		// Set parameters for each page type
		if ( is_singular() ) {
			$params['post_type'] = get_post_type();
			$params['object_id'] = get_the_ID();
			$params['object_type'] = is_page() ? 'page' : 'single';

			if ( $enable_tax ) {
				global $post;

				foreach ( get_the_taxonomies( $post ) as $taxonomy => $taxonomy_name ) {
					$terms = wp_list_pluck( (array) get_the_terms( $post, $taxonomy ), 'name' );
					if ( ! empty( $terms ) ) {
						$params[ 'tax_' . $taxonomy ] = $terms;
					}
				}
			}
		}

		elseif ( is_author() ) {
			$params['object_id'] = get_the_author_meta( 'ID' );
			$params['object_type'] = 'author_archive';
		}

		elseif ( is_date() ) {
			$params['object_type'] = 'date_archive';
		}

		elseif ( is_category() || is_tag() || is_tax() ) {
			if ( $enable_tax ) {
				$params[ 'tax_' . get_queried_object()->taxonomy ] = get_queried_object()->name;
			}

			$params['object_id'] = get_queried_object()->term_id;

			if ( is_category() ) {
				$params['object_type'] = 'cat_archive';
			} else if ( is_tag() ) {
				$params['object_type'] = 'tag_archive';
			} else {
				$params['object_type'] = 'tax_archive';
			}
		}

		elseif ( is_post_type_archive() ) {
			$params['post_type'] = get_post_type();
			$params['object_type'] = 'post' == get_post_type() ? 'blog' : 'cpt_archive';
		}

		// Set object_id and object_type for Home & Blog pages
		if ( is_front_page() && is_home() ) {  // Home blog posts
			$params['object_type'] = 'home';

		} elseif ( is_front_page() ) { // Static home page
			$params['object_id'] = get_option( 'page_on_front' );
			$params['object_type'] = 'home';

		} elseif ( is_home() ) { // Static blog page
			$params['object_id'] = get_option( 'page_for_posts' );
			$params['object_type'] = 'blog';
		}

		// Track events if any
		if ( $params ) {
			AEPC_Track::track( 'AdvancedEvents', $params );
		}
	}

	/**
	 * Track custom fields
	 */
	public static function track_custom_fields() {
		$params = array();

		if ( is_page() || is_single() ) {
			global $post;

			// Get custom fields choose by user
			$fields_to_track = AEPC_Track::get_custom_fields_to_track();
			foreach ( $fields_to_track as $field ) {
				 if ( $meta = get_post_meta( $post->ID, $field, true ) ) {
					 $params[ $field ] = $meta;
				 }
			}
		}

		if ( $params ) {
			AEPC_Track::track( 'CustomFields', $params );
		}
	}

	/**
	 * Track all conversion events defined by user, for all events contains a specific URL
	 */
	public static function track_conversions_events() {
		$conversions = AEPC_Track::get_conversions_events();

		foreach ( $conversions as $track ) {
			if ( 'page_visit' != $track['trigger'] ) {
				continue;
			}

			$current_rel_uri = trailingslashit( home_url( add_query_arg( NULL, NULL ) ) );
			$url = addcslashes( str_replace( '*', '[^/]+', $track['url'] ), '/' );

			if ( '*' == $track['url'] || preg_match( "/{$url}/", $current_rel_uri ) ) {
				AEPC_Track::track(
					$track['event'],
					$track['params'],
					$track['custom_params'],
					isset( $track['delay'] ) && '' !== $track['delay'] ? $track['delay'] : false
				);
			}
		}
	}

	/**
	 * Get all conversion events defined by user, for all events handled by css selector
	 *
	 * @return array
	 */
	public static function track_css_events() {
		$conversions = AEPC_Track::get_conversions_events();
		$css_selectors = array();

		foreach ( $conversions as $track ) {
			if ( 'css_selector' != $track['trigger'] ) {
				continue;
			}

			if ( ! isset( $css_selectors[ $track['css'] ] ) ) {
				$css_selectors[ $track['css'] ] = array();
			}

			$css_selectors[ $track['css'] ][] = array(
				'trackType' => AEPC_Track::get_track_type( $track['event'] ),
				'trackName' => $track['event'],
				'trackParams' => AEPC_Track::sanitize_fields( array_merge( $track['params'], $track['custom_params'] ) )
			);
		}

		return $css_selectors;
	}

	/**
	 * Get all conversion events defined by user, for all events handled by link click, containing an URL
	 *
	 * @return array
	 */
	public static function track_link_click_events() {
		$conversions = AEPC_Track::get_conversions_events();
		$links = array();

		foreach ( $conversions as $track ) {
			if ( 'link_click' != $track['trigger'] ) {
				continue;
			}

			if ( ! isset( $links[ $track['url'] ] ) ) {
				$links[ $track['url'] ] = array();
			}

			$links[ $track['url'] ][] = array(
				'trackType' => AEPC_Track::get_track_type( $track['event'] ),
				'trackName' => $track['event'],
				'trackParams' => AEPC_Track::sanitize_fields( array_merge( $track['params'], $track['custom_params'] ) )
			);
		}

		return $links;
	}

	/**
	 * Return a formatted list of categories as facebook expects
	 *
	 * @param $object_id
	 * @param string $tax
	 *
	 * @return string
	 */
	public static function content_category_list( $object_id, $tax = 'product_cat' ) {
		$terms = wp_get_object_terms( $object_id, $tax );

		foreach ( $terms as &$term ) {
			if ( $term->parent != 0 ) {
				$parent_term = $term;

				while( ! empty( $parent_term->parent ) ) {
					$parent_term = get_term( $parent_term->parent, $tax );
					$term->name = $parent_term->name . ' > ' . $term->name;
				};
			}
		}

		return array_map('html_entity_decode', wp_list_pluck( $terms, 'name' ) );
	}

	// INTEGRATIONS

	/**
	 * Add Facebook Pixel tracking also on AMP pages, if there is the AMP plugin activated
	 */
	public static function track_on_amp_pages( $amp_template ) {

		// Track the pixel events
		self::track_standard_events();
		self::track_advanced_events();
		self::track_custom_fields();
		self::track_conversions_events();

		?><amp-pixel src="<?php echo esc_url( 'https://www.facebook.com/tr?id=' . esc_attr( PixelCaffeine()->get_pixel_id() ) . '&ev=PageView&noscript=1' ); ?>&ord=RANDOM"></amp-pixel>
		<?php

		foreach ( AEPC_Track::get_standard_events() as $track_name => $args ) {
			?><amp-pixel src="<?php echo esc_url( AEPC_Track::get_track_url( $track_name ) ); ?>&ord=RANDOM"></amp-pixel>
			<?php
		}

		foreach ( AEPC_Track::get_custom_events() as $track_name => $args ) {
			?><amp-pixel src="<?php echo esc_url( AEPC_Track::get_track_url( $track_name ) ); ?>&ord=RANDOM"></amp-pixel>
			<?php
		}
	}
}
