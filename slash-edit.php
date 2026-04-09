<?php
/**
 * Plugin Name: Slash Edit
 * Plugin URI: https://mediaron.com
 * Description: Edit your posts or pages with a simple "/edit" at the end.
 * Author: Ronald Huereca
 * Version: 1.3.0
 * Requires at least: 3.9.1
 * Author URI: https://mediaron.com
 * Contributors: ronalfy
 *
 * @package slash-edit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Slash Edit for WordPress
 *
 * @package slash-edit
 */
class Slash_Edit {
	/**
	 * Singleton instance
	 *
	 * @var Slash_Edit
	 */
	private static $instance = null;

	/**
	 * Endpoint
	 *
	 * @var string
	 */
	private $endpoint = 'edit';

	/**
	 * Last rewrite version update
	 *
	 * @var string
	 */
	private $last_rewrite_version_update = '1.2.2'; // Will increment any time I need to change rewrite rules.

	/**
	 * Get the singleton instance
	 *
	 * @return Slash_Edit
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	} //end get_instance

	/**
	 * Constructor
	 *
	 * @return void
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'init' ), 20 );
		add_action( 'template_redirect', array( 'Slash_Edit', 'maybe_redirect' ) );
		add_filter( 'rewrite_rules_array', array( 'Slash_Edit', 'add_rewrite_rules' ) );
		add_action( 'save_post', array( 'Slash_Edit', 'save_post' ) );
		$this->endpoint = sanitize_title( apply_filters( 'slash_edit_endpoint', 'edit' ) );
		add_action( 'admin_init', array( $this, 'maybe_redirect_from_admin' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
	} //end constructor

	/**
	 * Get the endpoint
	 *
	 * @return string
	 */
	public function get_endpoint() {
		return $this->endpoint;
	}

	/**
	 * Add query vars
	 *
	 * @param array $query_vars Query vars.
	 *
	 * @return array
	 */
	public function add_query_vars( $query_vars ) {
		$query_vars[] = 'author';
		$query_vars[] = 'theme';
		$query_vars[] = 'users';
		$query_vars[] = 'authors';
		return $query_vars;
	}

	/**
	 * Activate the plugin
	 *
	 * @return void
	 */
	public static function activate() {
		update_option( 'slash_edit_install', 'true' );
	}

	/**
	 * Add rewrite rules
	 *
	 * @param array $rules Rewrite rules.
	 *
	 * @global WP_Rewrite $wp_rewrite
	 * @return array
	 */
	public static function add_rewrite_rules( $rules ) {
		/**
		 * WP_Rewrite $wp_rewrite WordPress rewrite object.
		 *
		 * This is not always set on init.
		 */
		global $wp_rewrite;

		if ( ! is_object( $wp_rewrite ) ) {
			return $rules;
		}
		// Get taxonomies.
		$taxonomies  = get_taxonomies();
		$blog_prefix = '';
		$endpoint    = self::get_instance()->get_endpoint();
		if ( is_multisite() && ! is_subdomain_install() && is_main_site() ) { /* stolen from /wp-admin/options-permalink.php */
			$blog_prefix = 'blog/';
		}
		$exclude = array(
			'category',
			'post_tag',
			'nav_menu',
			'link_category',
			'post_format',
		);
		foreach ( $taxonomies as $key => $taxonomy ) {
			if ( in_array( $key, $exclude, true ) ) {
				continue;
			}
			// $key may have a front, so check the rewrite structure for a front.
			$taxonomy_rewrite = get_taxonomy( $key )->rewrite;
			$taxonomy_slug    = $key;
			if ( isset( $taxonomy_rewrite['slug'] ) ) {
				$taxonomy_slug = $taxonomy_rewrite['slug'];
			}
			$rule_structure           = "{$blog_prefix}{$taxonomy_slug}(?:/([^/]+)/)+{$endpoint}(/(.*))?/?$";
			$endpoint_structure       = 'index.php?' . $key . '=$matches[1]&' . $endpoint . '=$matches[3]';
			$rules[ $rule_structure ] = $endpoint_structure;
		}

		// Add post type archive rules. This allows things like /faqs/edit, and will take  you to the post type edit screen.
		$post_types    = get_post_types( array( 'has_archive' => true ) );
		$archive_rules = array();
		foreach ( $post_types as $post_type ) {
			$post_type_obj = get_post_type_object( $post_type );

			// Determine the archive slug.
			$cpt_archive_slugs_to_add = array();
			if ( is_string( $post_type_obj->has_archive ) ) {
				$cpt_archive_slugs_to_add[] = sanitize_title( $post_type_obj->has_archive );
			}
			// This is the singular slug, but should act as an archive too if post name is ommitted.
			if ( isset( $post_type_obj->rewrite['slug'] ) ) {
				$cpt_archive_slugs_to_add[] = sanitize_title( $post_type_obj->rewrite['slug'] );
			} else {
				$cpt_archive_slugs_to_add[] = sanitize_title( $post_type_obj->name );
			}

			// Check if we need to add front.
			$has_front = false;
			if ( isset( $post_type_obj->rewrite['with_front'] ) && true === $post_type_obj->rewrite['with_front'] && true === $post_type_obj->rewrite['with_front'] ) {
				$has_front = true;
			}

			if ( $has_front && ! empty( $wp_rewrite->front ) ) {
				foreach ( $cpt_archive_slugs_to_add as $key => $archive_slug ) {
					$cpt_archive_slugs_to_add[ $key ] = substr( $wp_rewrite->front, 1 ) . $archive_slug;
				}
			}

			// Add the archive edit rules.
			foreach ( $cpt_archive_slugs_to_add as $archive_slug ) {
				$rule_structure                   = "{$blog_prefix}{$archive_slug}/{$endpoint}/?$";
				$endpoint_structure               = 'index.php?post_type=' . $post_type . '&' . $endpoint . '=1';
				$archive_rules[ $rule_structure ] = $endpoint_structure;
			}
		}

		// Add /author/edit to rewrites.
		$archive_rules[ "author/{$endpoint}/?$" ]  = 'index.php?author=$matches[1]&' . $endpoint . '=1';
		$archive_rules[ "users/{$endpoint}/?$" ]   = 'index.php?users=$matches[1]&' . $endpoint . '=1';
		$archive_rules[ "authors/{$endpoint}/?$" ] = 'index.php?authors=$matches[1]&' . $endpoint . '=1';

		// Add /theme/edit to rewrites.
		$archive_rules[ "theme/{$endpoint}/?$" ] = 'index.php?theme=$matches[1]&' . $endpoint . '=1';

		// Merge archive rules at the beginning for precedence.
		$rules                    = $archive_rules + $rules;
		$add_frontpage_edit_rules = false;
		if ( ! get_page_by_path( $endpoint ) ) {
			$add_frontpage_edit_rules = true;
		} else {
			$page = get_page_by_path( $endpoint );
			if ( is_a( $page, 'WP_Post' ) && 'publish' === $page->post_status ) {
				$add_frontpage_edit_rules = true;
			}
		}
		if ( $add_frontpage_edit_rules ) {
			$edit_array_rule = array( "{$endpoint}/?$" => 'index.php?' . $endpoint . '=frontpage' );
			$rules           = $edit_array_rule + $rules;
		}
		return $rules;
	}

	/**
	 * Deactivate the plugin
	 *
	 * @return void
	 */
	public static function deactivate() {
	}

	/**
	 * Initialize the plugin
	 *
	 * @return void
	 */
	public function init() {
		global $wp_rewrite;
		$endpoint = self::get_instance()->get_endpoint();

		// Determine if we need to flush rules for a new version of the plugin.
		$version = get_option( 'slash_edit_version', '1.0.0' );
		if ( version_compare( $this->last_rewrite_version_update, $version, 'gt' ) ) {
			update_option( 'slash_edit_version', $this->last_rewrite_version_update );
			flush_rewrite_rules( true );
		}

		// Delete rewrite rules if plugin is deactivated.
		if ( isset( $_GET['action'] ) && 'deactive' === sanitize_text_field( wp_unslash( $_GET['action'] ) ) ) {
			$plugin_basename = plugin_basename( __FILE__ );

			// Let's see if we're being deactivated.
			if ( isset( $_GET['plugin'] ) && sanitize_text_field( wp_unslash( $_GET['plugin'] ) ) === $plugin_basename ) {
				$nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) : '';
				if ( wp_verify_nonce( $nonce, 'deactivate-plugin_' . $plugin_basename ) ) {
						add_rewrite_endpoint( $endpoint, EP_NONE );
						flush_rewrite_rules( false );
				}
			}
		}

		// Refresh rewrite rules if plugin is activated.
		add_rewrite_endpoint( $endpoint, EP_PERMALINK | EP_PAGES | EP_CATEGORIES | EP_TAGS | EP_AUTHORS | EP_ALL_ARCHIVES ); // todo - adding EP_ATTACHMENT messes up EP_PERMALINK and EP_PAGES.
		if ( get_option( 'slash_edit_install', 'false' ) === 'true' ) {
			flush_rewrite_rules( false );
			delete_option( 'slash_edit_install' );
		}
	} //end init

	/**
	 * Maybe redirect from admin.
	 *
	 * @return void
	 */
	public static function maybe_redirect_from_admin() {
		if ( ! is_admin() ) {
			return;
		}
		$action_name  = 'slash_edit_redirect';
		$maybe_action = sanitize_text_field( wp_unslash( filter_input( INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS ) ) );
		if ( $action_name !== $maybe_action ) {
			return;
		}

		$token = sanitize_key( wp_unslash( filter_input( INPUT_GET, 'token', FILTER_SANITIZE_SPECIAL_CHARS ) ) );
		if ( ! $token ) {
			return;
		}
		$edit_url = get_transient( 'slash_edit_token_' . $token );
		if ( ! $edit_url ) {
			wp_safe_redirect( esc_url_raw( site_url() ) );
			exit;
		}
		delete_transient( 'slash_edit_token_' . $token );

		/**
		 * Filter the capability check.
		 *
		 * @param string $capability_check The capability check.
		 * @param string $edit_url The edit URL.
		 * @return string
		 */
		$capability_check = apply_filters( 'slash_edit_capability_check', 'edit_others_posts', $edit_url );

		/**
		 * Filter Can Edit override. If true, the user can edit the item regardless of the capability check.
		 *
		 * @param bool $can_edit Whether the user can override the capability check and edit the item.
		 * @param string $edit_url The edit URL.
		 * @return bool
		 */
		$can_edit = apply_filters( 'slash_edit_can_edit', false, $edit_url );

		if ( current_user_can( $capability_check ) || $can_edit ) {
			wp_safe_redirect( esc_url_raw( $edit_url ) );
		} else {
			wp_die( __( 'You are not authorized to edit this item.', 'slash-edit' ) );
		}
		exit;
	}

	/**
	 * Maybe redirect.
	 *
	 * @return void
	 */
	public static function maybe_redirect() {
		global $wp_query;
		$endpoint = self::get_instance()->get_endpoint();
		if ( ! isset( $wp_query->query_vars[ $endpoint ] ) ) {
			return;
		}

		$edit_url = false;
		/**
		 * Post, page, attachment, or CPTs.
		 */
		if ( is_attachment() || is_single() || is_page() || is_singular() ) {
			// Get the post, page, or cpt id.
			$post    = get_queried_object();
			$post_id = isset( $post->ID ) ? $post->ID : false;
			if ( false === $post_id ) {
				return;
			}

			// Build the url.
			$edit_url = add_query_arg(
				array(
					'post'   => absint( $post_id ),
					'action' => 'edit',
				),
				admin_url( 'post.php' )
			);
		} elseif ( is_author() ) { /* Author Page */
			$user_data = get_queried_object();
			if ( is_a( $user_data, 'WP_User' ) ) {
				$user_id = $user_data->ID;
				// Build the url.
				$edit_url = add_query_arg(
					array(
						'user_id' => absint( $user_id ),
						'action'  => 'edit',
					),
					admin_url( 'user-edit.php' )
				);
			}
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$tax_data = get_queried_object();
			if ( is_object( $tax_data ) && isset( $tax_data->term_id ) ) {
				$term_id  = $tax_data->term_id;
				$taxonomy = $tax_data->taxonomy;

				// Get taxonomy post types.
				$post_types = get_post_types( array( 'taxonomies' => array( $taxonomy ) ) );
				$post_type  = current( array_keys( $post_types ) );
				if ( count( $post_types ) > 1 ) {
					$post_type = '';
				}

				// Build the url.
				$edit_url = add_query_arg(
					array(
						'tag_ID'    => absint( $term_id ),
						'taxonomy'  => $taxonomy,
						'action'    => 'edit',
						'post_type' => $post_type,
					),
					admin_url( 'edit-tags.php' )
				);
			}
		} elseif ( is_post_type_archive() ) {
			$post_type     = sanitize_key( get_query_var( 'post_type' ) );
			$post_type_obj = get_post_type_object( $post_type );
			if ( null !== $post_type_obj ) {
				$edit_url = add_query_arg(
					array(
						'post_type' => $post_type,
					),
					admin_url( 'edit.php' )
				);
			}
		}
		// Fail safe for home_url/edit/.
		if ( false === $edit_url && 'page' === get_option( 'show_on_front' ) && get_option( 'page_on_front' ) && 'frontpage' === get_query_var( 'edit' ) ) {
			// Build the url.
			$edit_url = add_query_arg(
				array(
					'post'   => get_option( 'page_on_front' ),
					'action' => 'edit',
				),
				admin_url( 'post.php' )
			);
		} elseif ( 'frontpage' === get_query_var( 'edit' ) ) {
			// No front page set - so redirect back to homepage.
			$edit_url = home_url();
		} elseif ( ( isset( $wp_query->query['author'] ) || isset( $wp_query->query['users'] ) || isset( $wp_query->query['authors'] ) ) && get_query_var( 'edit' ) ) {
			$edit_url = admin_url( 'users.php' );
		} elseif ( isset( $wp_query->query['theme'] ) && get_query_var( 'edit' ) ) {
			if ( wp_is_block_theme() ) {
				$edit_url = admin_url( 'site-editor.php' );
			} else {
				$edit_url = admin_url( 'customize.php' );
			}
		} elseif ( is_home() ) {
			$edit_url = admin_url( 'options-reading.php' );
		}

		// Filter to rule them all.
		$edit_url = apply_filters( 'slash_edit_url', $edit_url ); // Return false for no redirect.

		// Return if nothing to redirect to.
		if ( false === $edit_url ) {
			return;
		}

		// Create token. Lasts 5 minutes.
		$token = sanitize_key( wp_generate_password( 20, false ) );
		set_transient( 'slash_edit_token_' . $token, esc_url_raw( $edit_url ), 5 * MINUTE_IN_SECONDS );

		$redirect_url = add_query_arg(
			array(
				'token'  => $token,
				'action' => 'slash_edit_redirect',
			),
			admin_url()
		);

		// Redirect to admin with token.
		wp_safe_redirect( esc_url_raw( $redirect_url ) );
		exit;
	}

	/**
	 * Update rewrite rules if a parent page with slug 'edit' is edited and/or update - This way if there is a page with path www.domain.com/edit/, the page has priority.
	 *
	 * @param int $post_id The post ID.
	 * @return void
	 */
	public static function save_post( $post_id = 0 ) {
		$endpoint = self::get_instance()->get_endpoint();
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		global $post;
		if ( ! is_object( $post ) ) {
			$maybe_post = get_post( $post_id );
		}
		if ( 0 === $maybe_post->post_parent && $endpoint === $maybe_post->post_name && 'page' === $post->post_type ) {
			flush_rewrite_rules( false );
		}
	}
} //end class Slash_Edit

add_action( 'plugins_loaded', 'slash_edit_instantiate' );
/**
 * Instantiate the Slash_Edit class
 *
 * @return void
 */
function slash_edit_instantiate() { // phpcs:ignore 
	Slash_Edit::get_instance();
}

register_activation_hook( __FILE__, array( 'Slash_Edit', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Slash_Edit', 'deactivate' ) );
