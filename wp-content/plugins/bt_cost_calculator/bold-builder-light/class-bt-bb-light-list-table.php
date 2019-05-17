<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class BT_BB_Light_List_Table extends WP_List_Table {
	
	private $post_type;
	private $edit_slug;
	private $found_items;
	private $shortcode;

	public static function define_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Title', 'bt-cost-calculator' ),
			'shortcode' => __( 'Shortcode', 'bt-cost-calculator' ),
			'author' => __( 'Author', 'bt-cost-calculator' ),
			'date' => __( 'Date', 'bt-cost-calculator' ),
		);

		return $columns;
	}

	function __construct( $post_type, $shortcode ) {
		parent::__construct( array(
			'singular' => 'post',
			'plural' => 'posts',
			'ajax' => false,
		) );
		$this->post_type = $post_type;
		$this->edit_slug = $post_type . '-edit';
		$this->shortcode = $shortcode;
	}

	function prepare_items() {
		$current_screen = get_current_screen();
		$post_type = $this->post_type;
		$per_page = $this->get_items_per_page( "edit_{$post_type}_per_page" );

		$this->_column_headers = $this->get_column_info();

		$args = array(
			'posts_per_page' => $per_page,
			'orderby' => 'title',
			'order' => 'ASC',
			'offset' => ( $this->get_pagenum() - 1 ) * $per_page,
		);

		if ( ! empty( $_REQUEST['s'] ) ) {
			$args['s'] = $_REQUEST['s'];
		}

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			if ( 'title' == $_REQUEST['orderby'] ) {
				$args['orderby'] = 'title';
			} elseif ( 'author' == $_REQUEST['orderby'] ) {
				$args['orderby'] = 'author';
			} elseif ( 'date' == $_REQUEST['orderby'] ) {
				$args['orderby'] = 'date';
			}
		}

		if ( ! empty( $_REQUEST['order'] ) ) {
			if ( 'asc' == strtolower( $_REQUEST['order'] ) ) {
				$args['order'] = 'ASC';
			} elseif ( 'desc' == strtolower( $_REQUEST['order'] ) ) {
				$args['order'] = 'DESC';
			}
		}

		$this->items = $this->find( $args );

		$total_items = $this->found_items;
		$total_pages = ceil( $total_items / $per_page );

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'total_pages' => $total_pages,
			'per_page' => $per_page,
		) );
	}

	function find( $args = '' ) {
		$defaults = array(
			'post_status' => 'any',
			'posts_per_page' => -1,
			'offset' => 0,
			'orderby' => 'ID',
			'order' => 'ASC',
		);

		$args = wp_parse_args( $args, $defaults );

		$args['post_type'] = $this->post_type;

		$q = new WP_Query();
		$posts = $q->query( $args );

		$this->found_items = $q->found_posts;

		$objs = array();

		foreach ( (array) $posts as $post ) {
			$objs[] = new BT_BB_Light_Item( $post, $this->shortcode );
		}

		return $objs;
	}

	function get_columns() {
		return get_column_headers( get_current_screen() );
	}

	function get_sortable_columns() {
		$columns = array(
			'title' => array( 'title', true ),
			'author' => array( 'author', false ),
			'date' => array( 'date', false ),
		);

		return $columns;
	}

	function get_bulk_actions() {
		$actions = array(
			'delete' => __( 'Delete', 'bt-cost-calculator' ),
		);

		return $actions;
	}

	function column_default( $item, $column_name ) {
		return '';
	}

	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			$this->_args['singular'],
			$item->id() );
	}

	function column_title( $item ) {
		$url = admin_url( 'admin.php?page=' . $this->edit_slug . '&post=' . absint( $item->id() ) );
		//$edit_link = add_query_arg( array( 'action' => 'edit' ), $url );
		$edit_link = $url;

		$output = sprintf(
			'<a class="row-title" href="%1$s" title="%2$s">%3$s</a>',
			esc_url( $edit_link ),
			esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;', 'bt-cost-calculator' ),
				$item->title() ) ),
			esc_html( $item->title() )
		);

		$output = sprintf( '<strong>%s</strong>', $output );

		$actions = array(
			'edit' => sprintf( '<a href="%1$s">%2$s</a>',
				esc_url( $edit_link ),
				esc_html__( 'Edit', 'bt-cost-calculator' ) ) );

		if ( current_user_can( 'edit_posts', $item->id() ) ) {
			$url = admin_url( 'admin.php?page=' . $this->post_type . '&post=' . absint( $item->id() ) );
			$delete_link = wp_nonce_url(
				add_query_arg( array( 'action' => 'delete' ), $url ),
				$this->post_type . '-copy-contact-form_' . absint( $item->id() ) );

			$actions = array_merge( $actions, array(
				'copy' => sprintf( '<a href="%1$s" onclick="if (confirm(\'' . esc_js( esc_html__( "You are about to delete this post.\n'Cancel' to stop, 'OK' to delete.", 'bt-cost-calculator' ) ) . '\')) {return true;} return false;">%2$s</a>',
					esc_url( $delete_link ),
					esc_html__( 'Delete', 'bt-cost-calculator' )
				),
			) );
		}

		$output .= $this->row_actions( $actions );

		return $output;
	}

	function column_author( $item ) {
		$post = get_post( $item->id() );

		if ( ! $post ) {
			return;
		}

		$author = get_userdata( $post->post_author );

		if ( false === $author ) {
			return;
		}

		return esc_html( $author->display_name );
	}

	function column_shortcode( $item ) {
		$shortcodes = array( $item->shortcode() );

		$output = '';

		foreach ( $shortcodes as $shortcode ) {
			$output .= "\n" . '<span class="shortcode"><input type="text"'
				. ' onfocus="this.select();" readonly="readonly"'
				. ' value="' . esc_attr( $shortcode ) . '"'
				. ' class="large-text code" /></span>';
		}

		return trim( $output );
	}

	function column_date( $item ) {
		$post = get_post( $item->id() );

		if ( ! $post ) {
			return;
		}

		$t_time = mysql2date( __( 'Y/m/d g:i:s A', 'bt-cost-calculator' ),
			$post->post_date, true );
		$m_time = $post->post_date;
		$time = mysql2date( 'G', $post->post_date )
			- get_option( 'gmt_offset' ) * 3600;

		$time_diff = time() - $time;

		if ( $time_diff > 0 && $time_diff < 24*60*60 ) {
			$h_time = sprintf(
				__( '%s ago', 'bt-cost-calculator' ), human_time_diff( $time ) );
		} else {
			$h_time = mysql2date( __( 'Y/m/d', 'bt-cost-calculator' ), $m_time );
		}

		return '<abbr title="' . $t_time . '">' . $h_time . '</abbr>';
	}
}
