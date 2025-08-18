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
		const VERSION			= '25.0818.1';

		/**
		 * @var string extension tab name
		 */
		const TAB_NAME 			= 'Tracking';

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
		 * @var string facebook CAPI url
		 */
		private $capi_url;


		/**
		 * constructor method
		 *
		 * @param 	object	$plugin main plugin object
		 * @return 	void
		 */
		public function __construct($plugin)
		{
			parent::__construct($plugin, self::DEFAULT_DISABLED);

			$this->registerExtension( 'Facebook_(Meta)_Pixel' );
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
							'default'	=> 	"Facebook/Meta Pixel Events are triggered via JavaScript in the browser. ".
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
								["<abbr title='PageView events may occur on all pages.'>Page Views</abbr>"				=> 'PageView'],
								["<abbr title='Search result pages.'>Site Search</abbr>"								=> 'Search'],
								["<abbr title='Archive (category and tag) pages.'>Content View (archives)</abbr>"		=> 'ViewArchive'],
								["<abbr title='Product pages.'>Content View (products)</abbr>"							=> 'ViewProduct'],
								["<abbr title='Product category, tag, and shop pages.'>Content View (commerce)</abbr>"	=> 'ViewCommerce'],
								["<abbr title='The shopping cart page.'>Content View (cart)</abbr>"						=> 'ViewCart'],
								["<abbr title='Buttons and links that add an item to the cart.'>Add To Cart</abbr>"		=> 'AddToCart'],
								["<abbr title='The checkout page.'>Initiate Checkout</abbr>"							=> 'Checkout'],
								["<abbr title='Billing information on checkout page.'>Add Payment Info</abbr>"			=> 'Payment'],
								["<abbr title='Purchase confirmation page.'>Purchase Completed</abbr>"					=> 'Purchase'],
							],
							'style'		=> 'display:block;',
							'default'	=> 	['PageView','Search','ViewArchive'],
						),
					'FacebookPageViews'	=> array(
							'type'		=> 	'radio',
							'title'		=>	"Should PageView events be triggered on every page or ".
											"only on pages that don't trigger other events.",
							'label'		=> 	'Page View Option',
							'options'	=>	[
												['On every page'			=> 'All'],
												['Only on non-event pages'	=> 'Only'],
											],
							'default'	=> 	'All',
						),
					'_Facebook_server'	=> array(
							'type'		=> 	'display',
							'label'		=> 	'<span class="dashicons dashicons-info-outline"></span>',
							'default'	=> 	"Server-based Conversion API (CAPI) events may be used for all events and ".
											"provide more detail than the JavaScript Pixel event.",
							'info'		=>	'See <a href="https://developers.facebook.com/docs/marketing-api/conversions-api" target="_blank">Conversions API</a>.  '.
											'CAPI events require a Meta Business Manager. ',
						),
					'FacebookToken'		=> array(
							'type'		=> 	'textarea',
							'title'		=>	'Required for Conversion API events. '.
											'If empty, only JavaScript Pixel events will be used.',
							'label'		=> 	'Server Access Token',
							'info'		=> 	'Go to <a href="https://business.facebook.com/events_manager2" target="_blank">Events Manager</a> '.
											'&rarr; Data Sources &rarr; Your Pixel &rarr; Settings &rarr; Conversions API.',
						),
					'FacebookTestId'	=> array(
							'type'		=> 	'text',
							'title'		=> 	'Use when testing Conversion API events.',
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
				PageView, Search, ViewContent, AddToCart, InitiateCheckout, AddPaymentInfo, and Purchase events when using WooCommerce.
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

			$this->capi_url = esc_url(
				sprintf("https://graph.facebook.com/v23.0/%s/events?access_token=%s",$this->pixelId,$this->conversionToken)
			);
		}


		/**
		 * Add filters and actions - called from main plugin
		 *
		 * @return	void
		 */
    	public function addActionsAndFilters()
    	{
			/* Add domain verification */
			if (!empty($this->metaTag))
			{
				add_action("wp_head",					function() {
					if (is_front_page()) echo "<meta name=\"facebook-domain-verification\" content=\"".esc_attr($this->metaTag)."\" />\n";
				});
			}

			/* add meta pixel js, maybe trigger CAPI */
			if (!empty($this->pixelId))
			{
			//	add_action("wp_enqueue_scripts",		array($this, 'addPixelScript'));
				add_action("wp_print_footer_scripts",	array($this, 'addPixelScript'), 100);
			}

			/* Woo AddToCart */
			if ( $this->is_option('FacebookOptions','AddToCart') )
			{
				add_action( 'woocommerce_add_to_cart',	array($this, 'wooAddToCart'), 10, 6);
			}

			/**
			 * filter {pluginName}_meta_pixel_event_code -
			 * returns the script for the given event (to use with DOM event).
			 *
			 * @param string $eventType
			 * @param array $eventData
			 * @param string $$eventID
			 * @return string script code
			 */
			$this->add_filter('meta_pixel_event_code', 	array($this, 'fb_track'), 10, 3);

			/**
			 * action {pluginName}_meta_pixel_add_event -
			 * adds a custom event (pixel & capi) to the page.
			 *
			 * @param string $eventType
			 * @param array $eventData
			 * @param string $$eventID
			 * @return array $eventData
			 */
			$this->add_action('meta_pixel_add_event', 	array($this, 'addCustomEvent'), 10, 3);
		}


		/**
		 * woocommerce_add_to_cart tracking capi event (ajax event, no js pixel)
		 *
		 * @param string $cart_item_key
		 * @param int $product_id
		 * @param int $quantity
		 * @param int $variation_id
		 * @param object $variation
		 * @param array $cart_item_data
		 * @return void
		 */
		public function wooAddToCart($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data)
    	{
			$id = $variation_id ?: $product_id;
			$product = wc_get_product($id);

			$data = [
				'content_name'	=> 'AddToCart',
				"content_ids"	=> [$product->get_sku()],
				"content_type"	=> "product",
				"num_items"		=> $quantity,
				"value"			=> (float) $product->get_price() * $quantity,
				"currency"		=> get_woocommerce_currency(),
			];

			$this->fb_track('wooAddToCart', $data);
		}


		/**
		 * Add custom event.
		 *
		 * @param string $eventType fb track event name
		 * @param array $eventData data passed
		 * @param string $$eventID optional 4th arg to fbq
		 * @return	void|string
		 */
    	public function addCustomEvent($eventType=null, $eventData=[], $eventID=null)
    	{
    		static $customEvents = '';
			if (func_num_args() == 0) {
				return $customEvents;
			}
			$this->customEvents .= $this->fb_track($eventType,$eventData,$eventID);
		}


		/**
		 * Add facebook tracking script.
		 * @return	void
		 */
    	public function addPixelScript()
    	{
			global $wp;
			$script_code = '';

			/*
				WooCommerce pages
			*/
			if ( class_exists('woocommerce') )
			{
				/*
					Purchase/Subscribe
				*/
				if ( is_order_received_page() && $this->is_option('FacebookOptions','Purchase') )
				{
					$order_id = intval( $wp->query_vars['order-received'] );
					if (! ($order = wc_get_order($order_id)) ) return;

					$eventType = ( (function_exists('\wcs_order_contains_subscription') && \wcs_order_contains_subscription($order_id)) ||
							       (function_exists('\sumo_order_contains_subscription') && \sumo_order_contains_subscription($order_id)) )
						? 'Subscribe'
						: 'Purchase';

					$content 	=  [];
					foreach ($order->get_items() as $item) $content[] = $item->get_product()->get_sku();
					$value 		= (float) $order->get_subtotal();
					$data = [
						'content_name'	=> 'Purchase',
						'content_ids' 	=> $content,
						'content_type' 	=> 'product',
						"num_items"		=> count($content),
						'value'			=> $value,
						'order_id'		=> $order_id,
					];

					$script_code .= $this->fb_track($eventType, $data, $order_id);
				}

				/*
					InitiateCheckout
				*/
				else if ( is_checkout() && $this->is_option('FacebookOptions','Checkout') )
				{
					$content = []; $value = 0;
					if (is_object(WC()->cart)) {
						foreach (WC()->cart->get_cart_contents() as $item) $content[] = $item['data']->get_sku();
						$count = WC()->cart->get_cart_contents_count();
						$value = WC()->cart->get_subtotal();
					}
					$data = [
						'content_name'	=> 'Checkout',
						'content_ids' 	=> $content,
						'content_type' 	=> 'product',
						'num_items'		=> $count,
						'value'			=> $value,
					];
					$script_code .= $this->fb_track('InitiateCheckout',$data);
				}

				/*
					ViewContent (cart)
				*/
				else if ( is_cart() && $this->is_option('FacebookOptions','ViewCart') )
				{
					$content = []; $value = 0;
					if (is_object(WC()->cart)) {
						foreach (WC()->cart->get_cart_contents() as $item) $content[] = $item['data']->get_sku();
						$value = WC()->cart->get_subtotal();
					}
					$data = [
						'content_name'	=> 'Cart',
						'content_ids' 	=> $content,
						'content_type' 	=> 'product',
						'value'			=> $value,
					];
					$script_code .= $this->fb_track('ViewContent',$data);
				}

				/*
					ViewContent (product)
				*/
				else if ( is_product() && $this->is_option('FacebookOptions','ViewProduct') )
				{
					$id = get_the_ID();
					$product = wc_get_product($id);
					$data = [
						'content_name'	=> $product->get_slug(),
						'content_ids' 	=> [ $product->get_sku() ],
						'content_type' 	=> 'product',
					];
					$script_code .= $this->fb_track('ViewContent',$data);
				}

				/*
					ViewContent (commerce)
				*/
				else if ($this->is_option('FacebookOptions','ViewCommerce'))
				{
					if ( is_product_category() || is_product_tag() || is_shop() ) {
						$content = strip_tags(html_entity_decode( get_the_archive_title() ));
						$slug = [];
						if ($current = get_queried_object()) {
							$slug[] = $current->slug ?? $current->labels->archives;
						}
						$data = [
							'content_name'	=> $content,
							'content_ids' 	=> $slug,
							'content_type' 	=> 'product_group',
						];
						$script_code .= $this->fb_track('ViewContent',$data);
					}
				}
			}

			/*
				no WooCommerce tracking
			*/
			if (empty($script_code))
			{
				/*
					Search
				*/
				if ( is_search() && $this->is_option('FacebookOptions','Search') )
				{
					$data = [
						'content_name'	=> 'Search',
						'search_string' => get_search_query(),
					];
					$script_code .= $this->fb_track('Search',$data);
				}

				/*
					ViewContent (archives)
				*/
				else if ($this->is_option('FacebookOptions','ViewArchive'))
				{
					if ( is_category() || is_tag() || is_archive() ) {
						$content = strip_tags(html_entity_decode( get_the_archive_title() ));
						$data = [
							'content_name'	=> $content,
						];
						$script_code .= $this->fb_track('ViewContent',$data);
					}
				}
			}

			/*
				Custom Events
			*/
			$script_code .= $this->addCustomEvent();

			/*
				PageView
			*/
			if ( $this->is_option('FacebookOptions','PageView') && ($this->is_option('FacebookPageViews','All') || empty($script_code)) )
			{
				$script_code = $this->fb_track('PageView') . $script_code;
			}

			/*
				AddToCart/AddPaymentInfo JavaScript click events
			*/
			if ( class_exists('woocommerce') )
			{
				/*
					AddToCart
				*/
				if ( $this->is_option('FacebookOptions','AddToCart') )
				{
					/* AddToCart button on product page */
					if ( is_product() )
					{
						$id = get_the_ID();
						$product = wc_get_product($id);
						$data = [
							'content_name'	=> 'AddToCart',
							'content_ids' 	=> [$product->get_sku()],
							'content_type' 	=> 'product',
						];
						$script_code .= "document.querySelectorAll('.single_add_to_cart_button').forEach(element => {".
 							"element.addEventListener('click',()=>{" . $this->fb_track('AddToCart',$data) . "})})\n";
					}
					/* AddToCart link anywhere else */
					$data = [
						'content_name'	=> 'AddToCart',
						'content_ids' 	=> ['SKU'],
						'content_type' 	=> 'product'
					];
					$sku = "element.getAttribute('data-product_sku')||element.getAttribute('data-product_id')";
					$script_code .= "document.querySelectorAll('a[href*=\"?add-to-cart\"]').forEach(element => {".
 							"element.addEventListener('click',()=>{let sku = $sku;" .
 							str_replace(["'SKU'","'ATC-SKU'"],['sku',"'ATC-'+sku"],$this->fb_track('AddToCart',$data)) . "})})\n";
				}

				/*
					AddPaymentInfo
				*/
				if ( is_checkout() && $this->is_option('FacebookOptions','Payment') )
				{
					$content = []; $value = 0;
					if (is_object(WC()->cart)) {
						foreach (WC()->cart->get_cart_contents() as $item) $content[] = $item['data']->get_sku();
						$value = WC()->cart->get_subtotal();
					}
					$data = [
						'content_name'	=> 'Payment',
						'content_ids' 	=> $content,
						'content_type' 	=> 'product',
						'value'			=> $value,
					];
					$script_code .= "document.querySelectorAll('#billing_city').forEach(element => {".
 							"element.addEventListener('change',()=>{" . $this->fb_track('AddPaymentInfo',$data) . "})})\n";
				}
			}

			if (empty($script_code)) return;

			$javascript = "/* Facebook Meta Pixel Code */
				!function(f,b,e,v,n,t,s)
				{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
				n.callMethod.apply(n,arguments):n.queue.push(arguments)};
				if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
				n.queue=[];t=b.createElement(e);t.async=!0;
				t.src=v;s=b.getElementsByTagName(e)[0];
				s.parentNode.insertBefore(t,s)}(window,document,'script',
				'https://connect.facebook.net/en_US/fbevents.js');
				fbq('init', '".esc_js($this->pixelId)."');\n";
			$javascript .= "const gcv = (n,d)=>(document.cookie.match('(^|;)\\\s*'+n+'\\\s*=\\\s*([^;]+)')?.pop()||d);\n";
			$javascript .= $script_code;

			$scriptId = sanitize_key( 'meta-pixel-'.$this->getVersion() );
		//	wp_print_inline_script_tag( $this->minifyString($javascript), ['id'=>"{$scriptId}-js"] );
			wp_print_inline_script_tag( $javascript, ['id'=>"{$scriptId}-js"] );
		}


		/**
		 * build tracking js string and maybe trigger CAPI call
		 *
		 * @param string $eventType fb track event name
		 * @param array $eventData data passed
		 * @param string $$eventID optional 4th arg to fbq
		 * @return 	string script output
		 */
		private function fb_track($eventType, $eventData=[], $eventID=null)
    	{
			$script = '';
			if (isset($eventData['value']) && function_exists('get_woocommerce_currency')) {
				$eventData['currency'] = get_woocommerce_currency();
			}

			/**
			 * filter {pluginName}_meta_pixel_eventdata -
			 * modify the event-specific data sent with the pixel and capi.
			 *
			 * @param array $eventData
			 * @param string $eventType
			 * @return array $eventData
			 */
			$eventData = $this->apply_filters('meta_pixel_eventdata', $eventData, $eventType);

			foreach ($eventData as $name=>$value) {
				$value = str_replace('"',"'",json_encode($value));
				$script .= "'{$name}': {$value},";
			}

			if (is_null($eventID)) $eventID = uniqid();

			/**
			 * filter {pluginName}_meta_pixel_eventid -
			 * modify the event ID sent with the pixel and capi.
			 *
			 * @param string $eventID
			 * @param array $eventData
			 * @param string $eventType
			 * @return string $eventID
			 */
			$eventID = $this->apply_filters('meta_pixel_eventid', (string)$eventID, $eventData, $eventType);

			$capi = '';
			switch ($eventType) {
				case 'AddPaymentInfo':
					break;				// click event, no capi
				case 'AddToCart':
					$eventID 			= 'ATC-'.implode('-',$eventData['content_ids']);
					break; 				// click event, no capi
				case 'wooAddToCart':
					$eventType			= 'AddToCart';
					$eventID 			= 'ATC-'.implode('-',$eventData['content_ids']);
					// no break;
				default:
					if (strpos(current_filter(),'meta_pixel_event_code') === false) {
						$capi = $this->fb_capi($eventType,$eventData,['event_id'=>$eventID]);
					}
			}

			$eventID 					= "{'eventID': '{$eventID}'}";

			// since the php page could be cached, we default to the _cookie value
			// but also try to get the cookie value (gcv) from the browse.
			$fbc = $this->get_fbc();
			$script .= "'fbc': gcv('_fbc','{$fbc}'),";

			$fbp = $this->get_fbp();
			$script .= "'fbp': gcv('_fbp','{$fbp}'),";

			$script  = "fbq('track', '{$eventType}', {".rtrim($script,',')."}, {$eventID});\n";

			/**
			 * filter {pluginName}_meta_pixel_console -
			 * enable console.info logging.
			 *
			 * Enable console logging.
			 * @param bool false
			 * @return bool
			 */
			$console = ($this->apply_filters('meta_pixel_console',false))
				? "console.info(\"".trim($script)."\");\n" . $capi : '';

			return $script . $console;
		}


		/**
		 * post tracking CAPI event
		 *
		 * @param string $eventType fb track event name
		 * @param array $eventData data to post
		 * @param array $apiData event id array
		 * @param array $userData data to post
		 * @return 	string console output
		 */
		private function fb_capi($eventType,$eventData=[],$apiData=[],$userData=[])
    	{
			if (!$this->conversionToken) return '';

			$source = ($this->plugin->doing_ajax()) ? $_SERVER['HTTP_REFERER'] : $this->plugin->currentURL();
			$source = explode('?',$source);
			$source = esc_url($source[0]);

			if ( isset($eventData['order_id']) && ($order = wc_get_order($eventData['order_id'])) )
			{
				$userData = array_merge($userData,[
					'fn' 			=> $this->normalize( $order->get_billing_first_name() ),
					'ln' 			=> $this->normalize( $order->get_billing_last_name() ),
					"em"			=> $this->normalize( $order->get_billing_email() ),
					"ph"			=> $this->normalize( $order->get_billing_phone() ),
					"ct"			=> $this->normalize( $order->get_billing_city() ),
					"st"			=> $this->normalize( $order->get_billing_state() ),
					"zp"			=> $this->normalize( $order->get_billing_postcode() ),
					"country"		=> $this->normalize( $order->get_billing_country() ),
				]);
			}

			$userData = array_merge($userData,[
				'client_ip_address' 		=> $this->plugin->getVisitorIP(),
				'client_user_agent' 		=> $this->plugin->varServer('HTTP_USER_AGENT'),
			]);

			$fbc = $this->get_fbc();
			if (!empty($fbc)) $userData['fbc'] = $fbc;

			$fbp = $this->get_fbp();
			if (!empty($fbp)) $userData['fbp'] = $fbp;

			/**
			 * filter {pluginName}_meta_pixel_userdata -
			 * modify the user_data sent with capi.
			 *
			 * @param array $userData
			 * @param array $eventData
			 * @param string $eventType
			 * @return array $userData
			 */
			$userData = $this->apply_filters('meta_pixel_userdata', $userData, $eventData, $eventType);

			$apiData = (object) array(
				"data" => array(
					(object) array_merge([
						"event_name"		=> $eventType,
						"event_time"		=> time(),
						"event_source_url"	=> $source,
						"action_source"		=> "website",
						"custom_data"		=> (object) $eventData,
						"user_data"			=> (object) $userData,
					],$apiData)
				)
			);

			if ($origin = $this->varServer('HTTP_REFERER')) {
				$apiData->data[0]->referrer_url = $origin;
			}

			if ($this->testEvent) {
				$apiData->test_event_code = esc_attr($this->testEvent);
			}

			$post = wp_remote_post(
				$this->capi_url,
				[
					'blocking' 	=> false, // asynchronous request
					'headers'   => ['Content-Type' => 'application/json'],
					'body'      => wp_json_encode($apiData),
				]
			);
		//	$this->plugin->logDebug($apiData,__METHOD__);
			return "console.info(\"fbcapi('{$eventType}', ".str_replace('"',"'",wp_json_encode($eventData)).")\");\n";
		}


		/**
		 * get the fbc (Meta ClickID) value
		 *
		 * @return string
		 */
		private function get_fbc()
    	{
    		static $fbc = null;
    		if (is_null($fbc))
    		{
				if ($fbc = $this->plugin->varRequest('fbclid')) {
					$fbc = sprintf("%s.%s.%s.%s",'fb','1',time(),$fbc);
					$this->set_fbc($fbc);
				} else {
					$fbc = $this->plugin->get_cookie('_fbc');
				}
    		}
    		return $fbc;
		}


		/**
		 * set the fbc (Meta ClickID) cookie
		 *
		 * @param string fbc value
		 */
		private function set_fbc($fbc)
    	{
			/**
			 * filter {pluginName}_meta_pixel_cookie
			 * Enable fbc cookie.
			 * @param bool false
			 * @return bool
			 */
			if ($this->apply_filters('meta_pixel_cookie',false))
			{
				$this->plugin->set_cookie('_fbc', $fbc, "90 Days", [/* default options */],
					[
						'category' => 'marketing',
						'function' => '%s sets this cookie to retain the Facebook/Meta Ad click ID (fbclid).'
					]
				);
			}
		}


		/**
		 * get the fbp (unique id) value
		 *
		 * @return string
		 */
		private function get_fbp()
    	{
    		static $fbp = null;
    		if (is_null($fbp))
    		{
				$fbp = $this->plugin->get_cookie('_fbp');
    		}
    		return $fbp;
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
