<?php
namespace EarthAsylumConsulting\Extensions;

if (! class_exists(__NAMESPACE__.'\metapixel_extension', false) )
{
	/**
 	* Extension: metapixel_extension - facebook conversion - {eac}Doojigger for WordPress
 	*
 	* @category		WordPress Plugin
 	* @package		{eac}Doojigger\Extensions
 	* @author		Kevin Burkholder <KBurkholder@EarthAsylum.com>
 	* @copyright	Copyright (c) 2025 EarthAsylum Consulting <www.EarthAsylum.com>
 	* @link			https://eacDoojigger.earthasylum.com/
 	*/

	/*
		Facebook/Meta Events

		Specifications for Facebook Pixel Standard Events - https://business.facebook.com/business/help/402791146561655?id=1205376682832142
		Pixel reference: https://developers.facebook.com/docs/meta-pixel/reference
		Conversion API : https://developers.facebook.com/docs/marketing-api/conversions-api
 	*/

	class metapixel_extension extends \EarthAsylumConsulting\abstract_extension
	{
		/**
		 * @var string extension version
		 */
		const VERSION	= '25.0419.1';

		/**
		 * @var string facebook pixel
		 */
		private $pixelId;

		/**
		 * @var string facebook meta tag content
		 */
		private $metaTag;

		/**
		 * @var string facebook conversion token
		 */
		private $conversionToken;

		/**
		 * @var string facebook test event id
		 */
		private $testEvent;


		/**
		 * constructor method
		 *
		 * @param 	object	$plugin main plugin object
		 * @return 	void
		 */
		public function __construct($plugin)
		{
			parent::__construct($plugin, self::DEFAULT_DISABLED);

			$this->registerExtension( ['Facebook_(Meta)_Pixel','Tracking'] );
			// Register plugin options when needed
			$this->add_action( "options_settings_page", array($this, 'admin_options_settings') );
			// Add contextual help
			$this->add_action( 'options_settings_help', array($this, 'admin_options_help') );
		}


		/**
		 * register options on options_settings_page
		 *
		 * @access public
		 * @return void
		 */
		public function admin_options_settings()
		{
			$this->registerExtensionOptions( 'Facebook_(Meta)_Pixel',
				[
					'_Facebook_browser'	=> array(
											'type'		=> 	'display',
											'label'		=> 	'<span class="dashicons dashicons-info-outline"></span>',
											'default'	=> 	"Facebook/Meta pixel events are triggered via JavaScript in the browser. ".
															"All events require the pixel ID.",
											'info'		=>	'See <a href="https://developers.facebook.com/docs/meta-pixel/reference" target="_blank">Meta Pixel Reference</a>',
										),
					'FacebookPixel'		=> array(
				    						'type'		=> 	'text',
											'label'		=> 	'Facebook Pixel ID',
											'info'		=> 	'Go to <a href="https://business.facebook.com/events_manager2" target="_blank">Events Manager</a> '.
															'&rarr; Data Sources &rarr; Your Pixel &rarr; Settings &rarr; Pixel ID.',
										),
					'FacebookOptions'	=> array(
											'type'		=> 	'checkbox',
											'label'		=> 	'Conversions Tracked',
											'options'	=>	[
																['Page Views'				=> 'PageView'],
																['Site Search'				=> 'Search'],
																['Content View (archives)'	=> 'ViewArchive'],
																['Content View (products)'	=> 'ViewProduct'],
																['Content View (commerce)'	=> 'ViewCommerce'],
																['Content View (cart)'		=> 'ViewCart'],
																['Add To Cart'				=> 'AddToCart'],
																['Initiate Checkout'		=> 'Checkout'],
																['Purchase Completed'		=> 'Purchase'],
															],
											'style'		=> 'display:block;',
											'default'	=> 	['PageView','Search','ViewArchive'],
										),
					'FacebookPageViews'	=> array(
											'type'		=> 	'radio',
											'title'		=>	"Should PageView events be triggered on every page or ".
															"only on pages that don't trigger another event.",
											'label'		=> 	'Page View Option',
											'options'	=>	[
																['On every pages'			=> 'All'],
																['Only on non-event pages'	=> 'Only'],
															],
											'default'	=> 	'All',
										),
					'_Facebook_server'	=> array(
											'type'		=> 	'display',
											'label'		=> 	'<span class="dashicons dashicons-info-outline"></span>',
											'default'	=> 	"Server events may be used for purchase conversions and ".
															"provide more details from the order than the standard browser event.",
											'info'		=>	'See <a href="https://developers.facebook.com/docs/marketing-api/conversions-api" target="_blank">Conversions API</a>.  '.
															'Server events require a Meta Business Manager. ',
										),
					'FacebookToken'		=> array(
				    						'type'		=> 	'textarea',
											'title'		=>	'Required for purchase conversion server event. '.
															'If empty, a JavaScript browser event will be used.',
											'label'		=> 	'Server Access Token',
											'info'		=> 	'Go to <a href="https://business.facebook.com/events_manager2" target="_blank">Events Manager</a> '.
															'&rarr; Data Sources &rarr; Your Pixel &rarr; Settings &rarr; Conversions API.',
										),
					'FacebookTestId'	=> array(
				    						'type'		=> 	'text',
											'title'		=> 	'Use when testing purchase conversion server events.',
											'label'		=> 	'Test Event Code',
											'info'		=> 	'Go to <a href="https://business.facebook.com/events_manager2" target="_blank">Events Manager</a> '.
															'&rarr; Data Sources &rarr; Your Pixel &rarr; Test Events &rarr; Test Server Events.',
										),
					'_Facebook_metatag'	=> array(
											'type'		=> 	'display',
											'label'		=> 	'<span class="dashicons dashicons-info-outline"></span>',
											'default'	=>	"Optionally, add your Domain Verification Meta Tag to the &lt;head&gt; section of your home page.",
										),
 					'FacebookMetaTag'	=> array(
				    						'type'		=> 	'text',
											'label'		=> 	'Domain Verification',
											'info'		=> 	'Go to <a href="https://business.facebook.com/settings/" target="_blank">Business Settings</a> '.
															"&rarr; Brand Safety &rarr; Domains &rarr; Your Domain &rarr; Add a meta-tag ".
															"and copy just the 'content=' string. (i.e. content='<strong>xyzzy1ndu84mmhaifl5gawo9ntafn8</strong>')",
										),
				]
			);
		}


		/**
		 * Add help tab on admin page
		 *
		 * @return	void
		 */
		public function admin_options_help()
		{
			if (!$this->plugin->isSettingsPage('Tracking')) return;

			ob_start();
			?>
				The {eac}MetaPixel extension installs the Facebook/Meta Pixel and enables tracking of
				PageView, Search, ViewContent, AddToCart, InitiateCheckout and Purchase events when using WooCommerce.
			<?php
			$content = ob_get_clean();

			$this->addPluginHelpTab('Tracking',$content,['Facebook (Meta) Pixel','open']);

			$this->addPluginSidebarLink(
				"<span class='dashicons dashicons-facebook-alt'></span>{eac}MetaPixel",
				"https://eacdoojigger.earthasylum.com/eacMetaPixel/",
				"{eac}MetaPixel Extension Plugin"
			);
		}


		/**
		 * initialize method - called from main plugin
		 *
		 * @return 	void
		 */
		public function initialize()
		{
			if ( ! parent::initialize() ) return; // disabled

			$fb = $this->get_option('FacebookPixel');
			$this->pixelId = trim($fb);

			$fb = $this->get_option('FacebookMetaTag');
			$this->metaTag = trim($fb);

			$fb = $this->get_option('FacebookToken');
			$this->conversionToken = trim($fb);

			$fb = $this->get_option('FacebookTestId');
			$this->testEvent = trim($fb);
		}


		/**
		 * Add filters and actions - called from main plugin
		 *
		 * @return	void
		 */
    	public function addActionsAndFilters()
    	{
			if (!empty($this->metaTag))
			{
				add_action("wp_head",	function() {
					if (is_front_page()) echo "<meta name=\"facebook-domain-verification\" content=\"".esc_attr($this->metaTag)."\" />\n";
				});
			}

			if (!empty($this->pixelId))
			{
				add_action("wp_enqueue_scripts",	array($this, 'addPixelScript'));
			}
		}


		/**
		 * Add facebook tracking script
		 *
		 * @return	void
		 */
    	public function addPixelScript()
    	{
			global $wp;

			$insert = '';

			if ( class_exists('woocommerce') )
			{
				/* Purchase/Subscribe/StartTrial */
				if ( is_order_received_page() && $this->is_option('FacebookOptions','Purchase') )
				{
					$order_id = intval( $wp->query_vars['order-received'] );
					if (! ($order = wc_get_order($order_id)) ) return;
					if (function_exists('\wcs_order_contains_subscription') && \wcs_order_contains_subscription($order_id)) {
						$eventType = 'Subscribe';
						foreach (wcs_get_subscriptions_for_order($order_id) as $sub) {
							if ($sub->get_date('schedule_trial_end')) {
								$eventType = 'StartTrial';
								break;
							}
						}
					} else {
						$eventType = 'Purchase';
					}
					if ($this->conversionToken) {
						$this->fb_purchase_tracking($order,$eventType);
					} else {
						$content =  '';
						foreach ($order->get_items() as $item) $content .= $item->get_id().',';
						$count = $order->get_item_count();
						$value = (float) $order->get_subtotal();
						$insert = $this->fb_track($eventType,null,"[".rtrim($content,',')."]",$count,$value, "{'eventID': '{$order_id}'}");
					}
				}

				/* InitiateCheckout */
				else if ( is_checkout() && $this->is_option('FacebookOptions','Checkout') )
				{
					$content =  ''; $count = 0; $value = 0;
					if (is_object(WC()->cart)) {
						foreach (WC()->cart->get_cart_contents() as $item) $content .= $item['data']->get_id().',';
						$count = WC()->cart->get_cart_contents_count();
						$value = WC()->cart->get_subtotal();
					}
					$insert = $this->fb_track('InitiateCheckout',null,"[".rtrim($content,',')."]",$count,$value);
				}

				/* ViewContent (cart) */
				else if ( is_cart() && $this->is_option('FacebookOptions','ViewCart') )
				{
					$content =  ''; $count = 0; $value = 0;
					if (is_object(WC()->cart)) {
						foreach (WC()->cart->get_cart_contents() as $item) $content .= $item['data']->get_id().',';
						$count = WC()->cart->get_cart_contents_count();
						$value = WC()->cart->get_subtotal();
					}
					$insert = $this->fb_track('ViewContent',"'Cart'","[".rtrim($content,',')."]",$count,$value);
				}

				/* ViewContent (products) */
				else if ( is_product() && $this->is_option('FacebookOptions','ViewProduct') )
				{
					$id = get_the_ID();
					$product = wc_get_product($id);
					$content = 'Product: '.$product->get_sku();
					$insert = $this->fb_track('ViewContent',"'{$content}'","[{$id}]");
				}

				/* ViewContent (commerce) */
				else if ($this->is_option('FacebookOptions','ViewCommerce'))
				{
					if ( is_product_category() || is_product_tag() || is_shop() ) {
						$content = strip_tags(html_entity_decode( get_the_archive_title() ));
						$insert = $this->fb_track('ViewContent',"'{$content}'");
					}
				}
			}

			/* no WooCommerce tracking */
			if (empty($insert))
			{
				/* Search */
				if ( is_search() && $this->is_option('FacebookOptions','Search') )
				{
					$search = get_search_query();
					$insert = $this->fb_track('Search',"'{$search}'");
				}

				/* ViewContent (archives) */
				else if ($this->is_option('FacebookOptions','ViewArchive'))
				{
					if ( is_category() || is_tag() || is_archive() ) {
						$content = strip_tags(html_entity_decode( get_the_archive_title() ));
						$insert = $this->fb_track('ViewContent',"'{$content}'");
					}
				}
			}

			/* PageView */
			if ( $this->is_option('FacebookOptions','PageView') && ($this->is_option('FacebookPageViews','All') || empty($insert)) )
			{
				$insert = $this->fb_track('PageView') . $insert;
			}

			/* include AddToCart JavaScript events */
			if ( class_exists('woocommerce') )
			{
				/* AddToCart */
				if ( $this->is_option('FacebookOptions','AddToCart') )
				{
					/* AddToCart on product page */
					if ( is_product() )
					{
						$id = get_the_ID();
						$product = wc_get_product($id);
						$content = $product->get_sku();
						$insert .= "jQuery( function($) {\$('.single_add_to_cart_button').click( function( event ) {".
							$this->fb_track('AddToCart',
								"'{$content}'",
								"[ $('input.variation_id').val() || $('input.product_id').val() || $(this).val() ]",
								"$('input.qty').val() || $('input.quantity').val() || 1").
							"});});";
					}
					/* AddToCart anywhere else */
					$insert .= "jQuery( function($) {\$('a[href*=\"?add-to-cart\"]').click( function( event ) {".
							$this->fb_track('AddToCart',
								"$(this).data('product_sku')||'no sku'",
								"[ $(this).data('product_id')||'no id' ]",
								"$(this).data('quantity')||1").
							"});});";
				}
			}

			if (empty($insert)) return;

			// jquery required
			wp_enqueue_script('jquery');

			$javascript = "/* Facebook Meta Pixel Code */
					!function(f,b,e,v,n,t,s)
					{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
					n.callMethod.apply(n,arguments):n.queue.push(arguments)};
					if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
					n.queue=[];t=b.createElement(e);t.async=!0;
					t.src=v;s=b.getElementsByTagName(e)[0];
					s.parentNode.insertBefore(t,s)}(window,document,'script',
					'https://connect.facebook.net/en_US/fbevents.js');
 					fbq('init', '".esc_js($this->pixelId)."');
					{$insert}
			";
			/* testing
				$javascript = "
						function fbq(v1,v2, v3) {alert(v1 +' '+  v2  +', '+ JSON.stringify(v3));}
						{$insert}
				";
				echo "<script>console.info(\"".esc_attr($this->className).": ".esc_js($insert)."\");</script>\n";
			*/
			$scriptId = sanitize_key( 'meta-pixel-'.$this->getVersion() );
			wp_register_script( $scriptId, false, ['jquery'] );
			wp_enqueue_script( $scriptId );
			wp_add_inline_script( $scriptId, $this->minifyString($javascript) );
		}


		/**
		 * build tracking js string
		 *
		 * @param string $event fb track event name
		 * @param string $name content_name (or search_string)
		 * @param string $ids content_ids
		 * @param int $count num_items
		 * @param float $value price/value
		 * @param string $extra optional 4th arg to fbq (event_id)
		 * @return 	string
		 */
		private function fb_track($event, $name=null, $ids=null, $count=null, $value=null, $extra=null)
    	{
			$str = '';
			if (!is_null($name)) {
				$str .= ($event=='Search') ? "'search_string': {$name}," : "'content_name': {$name},";
			}
			if (!is_null($ids))   	$str .= "'content_ids': {$ids},'content_type': 'product',";
			if (!is_null($count))   $str .= "'num_items': {$count},";
			if (!is_null($value)) 	$str .= "'value': {$value},'currency': '".get_woocommerce_currency()."',";

			$str = "fbq('track', '{$event}', {".rtrim($str,',')."}";
			return $str . ( (!is_null($extra)) ? ",{$extra});" : ");\n" );
		}


		/**
		 * purchase tracking server event
		 *
		 * @param object $order wc_order
		 * @param string $eventType fb track event name (Purchase/Subscribe/StartTrial)
		 * @return 	void
		 */
		private function fb_purchase_tracking($order,$eventType)
    	{
			if (!$order) return new WP_Error(400,"order not found");

			$apiUrl = esc_url("https://graph.facebook.com/v14.0/{$this->pixelId}/events?access_token={$this->conversionToken}");

			$items = $ids = array();
			foreach ($order->get_items() as $key => $item) {
				$product = $item->get_product();
				$items[] = (object) [
					"id"			=> $product->get_sku(),
					"quantity"		=> $item->get_quantity(),
					"item_price"	=> (float) $product->get_price(),
				];
				$ids[] = $item->get_id();
			}

			$source = explode('?',$_SERVER['REQUEST_URI']);
			$source = sanitize_url( (is_ssl() ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $source[0] );

			$apiData = (object) array(
				"data" => array(
					(object) [
						"event_name"		=> $eventType,
						"event_id"			=> $order->get_id(),
						"event_time"		=> time(),
						"event_source_url"	=> $source,
						"action_source"		=> "website",
						"user_data"			=> (object) [
							'fn' 			=> $this->normalize( $order->get_billing_first_name() ),
							'ln' 			=> $this->normalize( $order->get_billing_last_name() ),
							"em"			=> $this->normalize( $order->get_billing_email() ),
							"ph"			=> $this->normalize( $order->get_billing_phone() ),
							"ct"			=> $this->normalize( $order->get_billing_city() ),
							"st"			=> $this->normalize( $order->get_billing_state() ),
							"zp"			=> $this->normalize( $order->get_billing_postcode() ),
							"country"		=> $this->normalize( $order->get_billing_country() ),
							"client_ip_address"	=> $order->get_customer_ip_address(),
							"client_user_agent"	=> $order->get_customer_user_agent(),
						],
						"custom_data"		=> (object) [
							"currency"		=> get_woocommerce_currency(),
							"value"			=> (float) $order->get_subtotal(),
							"order_id"		=> $order->get_id(),
							"content_ids"	=> $ids,
							"contents"		=> $items,
							"content_type"	=> "product",
						]
					]
				)
			);

			$fbp = $this->plugin->_COOKIE('_fbp'); // sanitizes $_COOKIE value
			if (!empty($fbp)) $apiData->data[0]->user_data->fbp = $fbp;
			$fbc = $this->plugin->_COOKIE('_fbc') ?? $this->plugin->_GET('fbclid');
			if (!empty($fbc)) $apiData->data[0]->user_data->fbc = $fbc;

			if ($this->testEvent) {
				$apiData->test_event_code = esc_attr($this->testEvent);
			}

			$post = wp_remote_post(
				$apiUrl,
				array(
					'headers'     	=> array(
						'Content-Type'		=> 'application/json',
					),
					'body'        	=> wp_json_encode($apiData),
				)
			);
			$this->plugin->logDebug([$apiData,$post],__METHOD__);
		}


		/**
		 * normalize & hash a value
		 *
		 * @param 	string	$value value to be hashed
		 * @return 	string	hashed value
		 */
		private function normalize($value)
    	{
			return hash("sha256", strtolower($value));
		}
	}
}
/**
 * return a new instance of this class
 */
return new metapixel_extension($this);
?>
