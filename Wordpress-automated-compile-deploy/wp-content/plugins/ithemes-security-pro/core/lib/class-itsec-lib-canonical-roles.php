<?php

final class ITSEC_Lib_Canonical_Roles {

	/**
	 * Check if a given role is at least as or equally as powerful as a given role.
	 *
	 * @param string $min_role
	 * @param string $role
	 *
	 * @return bool
	 */
	public static function is_canonical_role_at_least( $min_role, $role ) {
		$roles = array(
			'super-admin'   => 6,
			'administrator' => 5,
			'editor'        => 4,
			'author'        => 3,
			'contributor'   => 2,
			'subscriber'    => 1,
			''              => 0,
		);

		if ( ! isset( $roles[$role] ) || ! isset( $roles[$min_role] ) ) {
			return false;
		}

		if ( $roles[$role] >= $roles[$min_role] ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if a user's role is at least as or equally as powerful as a given role.
	 *
	 * @param string                   $role
	 * @param int|string|WP_User|false $user
	 *
	 * @return bool
	 */
	public static function is_user_at_least( $role, $user = false ) {
		$roles = array(
			'super-admin'   => 6,
			'administrator' => 5,
			'editor'        => 4,
			'author'        => 3,
			'contributor'   => 2,
			'subscriber'    => 1,
			''              => 0,
		);

		if ( ! isset( $roles[$role] ) ) {
			return false;
		}

		$user_role = self::get_user_role( $user );

		if ( $roles[$user_role] >= $roles[$role] ) {
			return true;
		}

		return false;
	}

	/**
	 * Retrieve the default WordPress role that would be associated with the given capabilities list.
	 *
	 * This checks the caps list for containing at least one of the unique capabilities from each default role.
	 *
	 * @param string[] $caps
	 *
	 * @return int|string
	 */
	public static function get_role_from_caps( $caps ) {
		if ( is_string( $caps ) ) {
			$caps = array( $caps );
		}

		$canonical_caps = self::get_unique_capabilities();

		foreach ( $canonical_caps as $role => $role_caps ) {
			$shared_caps = array_intersect( $caps, $role_caps );

			if ( ! empty( $shared_caps ) ) {
				return $role;
			}
		}

		return '';
	}

	/**
	 * Retrieve a user's equivalent default WordPress role from their capabilities.
	 *
	 * @param bool $user
	 *
	 * @return int|string
	 */
	public static function get_user_role( $user = false ) {
		$user = ITSEC_Lib::get_user( $user );

		if ( false === $user ) {
			return '';
		}

		if ( is_multisite() && is_super_admin( $user->ID ) ) {
			return 'super-admin';
		}

		$canonical_caps = self::get_unique_capabilities();

		foreach ( $canonical_caps as $role => $caps ) {
			foreach ( $caps as $cap ) {
				if ( $user->has_cap( $cap ) ) {
					return $role;
				}
			}
		}

		return '';
	}

	/**
	 * Get a list of all of the capabilities that are unique to each role.
	 *
	 * @return array
	 */
	public static function get_unique_capabilities() {
		return array(
			'administrator' => array(
				'activate_plugins',
				'create_users',
				'delete_plugins',
				'delete_themes',
				'delete_users',
				'edit_dashboard',
				'edit_files',
				'edit_plugins',
				'edit_theme_options',
				'edit_themes',
				'edit_users',
				'export',
				'import',
				'install_plugins',
				'install_themes',
				'level_8',
				'level_9',
				'level_10',
				'list_users',
				'manage_options',
				'promote_users',
				'remove_users',
				'switch_themes',
				'unfiltered_upload',
				'update_core',
				'update_plugins',
				'update_themes',
			),
			'editor' => array(
				'delete_others_pages',
				'delete_others_posts',
				'delete_pages',
				'delete_private_pages',
				'delete_private_posts',
				'delete_published_pages',
				'edit_others_pages',
				'edit_others_posts',
				'edit_pages',
				'edit_private_pages',
				'edit_private_posts',
				'edit_published_pages',
				'level_3',
				'level_4',
				'level_5',
				'level_6',
				'level_7',
				'manage_categories',
				'manage_links',
				'moderate_comments',
				'publish_pages',
				'read_private_pages',
				'read_private_posts',
				'unfiltered_html',
			),
			'author' => array(
				'delete_published_posts',
				'edit_published_posts',
				'level_2',
				'publish_posts',
				'upload_files',
			),
			'contributor' => array(
				'delete_posts',
				'edit_posts',
				'level_1',
			),
			'subscriber' => array(
				'level_0',
				'read',
			),
		);
	}

	/**
	 * Get a list of all of the capabilities each default WordPress role has.
	 *
	 * @return array
	 */
	public static function get_capabilities() {
		return array(
			'administrator' => array(
				'activate_plugins',
				'create_users',
				'delete_others_pages',
				'delete_others_posts',
				'delete_pages',
				'delete_plugins',
				'delete_posts',
				'delete_private_pages',
				'delete_private_posts',
				'delete_published_pages',
				'delete_published_posts',
				'delete_themes',
				'delete_users',
				'edit_dashboard',
				'edit_files',
				'edit_others_pages',
				'edit_others_posts',
				'edit_pages',
				'edit_plugins',
				'edit_posts',
				'edit_private_pages',
				'edit_private_posts',
				'edit_published_pages',
				'edit_published_posts',
				'edit_theme_options',
				'edit_themes',
				'edit_users',
				'export',
				'import',
				'install_plugins',
				'install_themes',
				'level_0',
				'level_1',
				'level_2',
				'level_3',
				'level_4',
				'level_5',
				'level_6',
				'level_7',
				'level_8',
				'level_9',
				'level_10',
				'list_users',
				'manage_categories',
				'manage_links',
				'manage_options',
				'moderate_comments',
				'promote_users',
				'publish_pages',
				'publish_posts',
				'read',
				'read_private_pages',
				'read_private_posts',
				'remove_users',
				'switch_themes',
				'unfiltered_html',
				'unfiltered_upload',
				'update_core',
				'update_plugins',
				'update_themes',
				'upload_files',
			),
			'editor' => array(
				'delete_others_pages',
				'delete_others_posts',
				'delete_pages',
				'delete_posts',
				'delete_private_pages',
				'delete_private_posts',
				'delete_published_pages',
				'delete_published_posts',
				'edit_others_pages',
				'edit_others_posts',
				'edit_pages',
				'edit_posts',
				'edit_private_pages',
				'edit_private_posts',
				'edit_published_pages',
				'edit_published_posts',
				'level_0',
				'level_1',
				'level_2',
				'level_3',
				'level_4',
				'level_5',
				'level_6',
				'level_7',
				'manage_categories',
				'manage_links',
				'moderate_comments',
				'publish_pages',
				'publish_posts',
				'read',
				'read_private_pages',
				'read_private_posts',
				'unfiltered_html',
				'upload_files',
			),
			'author' => array(
				'delete_posts',
				'delete_published_posts',
				'edit_posts',
				'edit_published_posts',
				'level_0',
				'level_1',
				'level_2',
				'publish_posts',
				'read',
				'upload_files',
			),
			'contributor' => array(
				'delete_posts',
				'edit_posts',
				'level_0',
				'level_1',
				'read',
			),
			'subscriber' => array(
				'level_0',
				'read',
			),
		);
	}
}
