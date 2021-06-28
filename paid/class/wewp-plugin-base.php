<?php

if ( ! class_exists( 'Wewp_Plugin_Base' ) ) {
	class Wewp_Plugin_Base {
		protected $required_plugins = [];

		function have_required_plugins() {
			if ( empty( $this->required_plugins ) ) {
				return true;
			}
			$active_plugins = (array) get_option( 'active_plugins', array() );
			if ( is_multisite() ) {
				$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
			}
			foreach ( $this->required_plugins as $key => $required ) {
				$required = ( ! is_numeric( $key ) ) ? "{$key}/{$required}.php" : "{$required}/{$required}.php";
				if ( ! in_array( $required, $active_plugins ) && ! array_key_exists( $required, $active_plugins ) ) {
					return false;
				}
			}

			return true;
		}
	}
}

