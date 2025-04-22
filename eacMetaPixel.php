<?php
namespace EarthAsylumConsulting;

/**
 * Add {eac}MetaPixel extension to {eac}Doojigger
 *
 * @category	WordPress Plugin
 * @package 	{eac}MetaPixel\{eac}Doojigger Extensions
 * @author		Kevin Burkholder <KBurkholder@EarthAsylum.com>
 * @copyright	Copyright (c) 2025 EarthAsylum Consulting <www.earthasylum.com>
 * @link		https://eacDoojigger.earthasylum.com/
 *
 * @wordpress-plugin
 * Plugin Name:			{eac}MetaPixel
 * Description:			{eac}MetaPixel installs the Facebook/Meta Pixel to enable tracking of PageView, ViewContent, AddToCart, InitiateCheckout and Purchase events.
 * Version:				1.0.7
 * Requires at least:	5.8
 * Tested up to:		6.8
 * Requires PHP:		7.4
 * Plugin URI:          https://eacdoojigger.earthasylum.com/eacmetapixel/
 * Author:				EarthAsylum Consulting
 * Author URI:			http://www.earthasylum.com
 * License: 			GPLv3 or later
 * License URI: 		https://www.gnu.org/licenses/gpl.html
 */

if (!defined('EACDOOJIGGER_VERSION'))
{
	\add_action( 'all_admin_notices', function()
		{
			echo '<div class="notice notice-error is-dismissible"><p>{eac}MetaPixel requires installation & activation of '.
				 '<a href="https://eacdoojigger.earthasylum.com/eacdoojigger" target="_blank">{eac}Doojigger</a>.</p></div>';
		}
	);
	return;
}

class eacMetaPixel
{
	/**
	 * constructor method
	 *
	 * @return	void
	 */
	public function __construct()
	{
		/**
		 * {pluginname}_load_extensions - get the extensions directory to load
		 *
		 * @param 	array	$extensionDirectories - array of [plugin_slug => plugin_directory]
		 * @return	array	updated $extensionDirectories
		 */
		add_filter( 'eacDoojigger_load_extensions',	function($extensionDirectories)
			{
				/*
    			 * Enable update notice (self hosted or wp hosted)
    			 */
				eacDoojigger::loadPluginUpdater(__FILE__,'wp');

				/*
    			 * Add links on plugins page
    			 */
				add_filter( (is_network_admin() ? 'network_admin_' : '').'plugin_action_links_' . plugin_basename( __FILE__ ),
					function($pluginLinks, $pluginFile, $pluginData) {
						return array_merge(
							[
								'settings'		=> eacDoojigger::getSettingsLink($pluginData,'tracking'),
								'documentation'	=> eacDoojigger::getDocumentationLink($pluginData),
								'support'		=> eacDoojigger::getSupportLink($pluginData),
							],
							$pluginLinks
						);
					},20,3
				);

				/*
    			 * Add our extension to load
    			 */
				$extensionDirectories[ plugin_basename( __FILE__ ) ] = [plugin_dir_path( __FILE__ )];
				return $extensionDirectories;
			}
		);
	}
}
new \EarthAsylumConsulting\eacMetaPixel();
?>
