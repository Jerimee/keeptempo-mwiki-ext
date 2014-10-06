<?php
/**
 * Hooks for Example extension
 *
 * @file
 * @ingroup Extensions
 */

class ExampleHooks {
	/**
	 * Add welcome module to the load queue of all pages
	 */
	public static function onBeforePageDisplay( OutputPage &$out, Skin &$skin ) {
		global $wgExampleEnableWelcome;

		if ( $wgExampleEnableWelcome ) {
			$src = $dir . '/css/tempostyles.css';
			$out->addStyle( $src );
		}

		// Always return true, indicating that parser initialization should
		// continue normally.
		return true;
	}
}
