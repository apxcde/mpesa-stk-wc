<?php

/**
 * Plugin Name: MPesa STK For WooCommerce
 * Plugin URI: https://nineafrica.com/products
 * Description: This plugin extends WordPress and WooCommerce functionality to integrate MPesa stk push for making and receiving online payments.
 * Author: Nineafrica < packages@nineafrica.com >
 * Version: 1.0.1
 * Author URI: https://nineafrica.com/
 *
 *
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    exit('Please install WooCommerce for this extension to work');
}

define('MPESA_DIR', plugin_dir_path(__FILE__));
define('MPESA_INC_DIR', MPESA_DIR.'includes/');
define('WC_MPESA_VERSION', '1.0.0');

// Admin Menus
require_once(MPESA_INC_DIR.'menu.php');

//Payments Post Type
require_once(MPESA_INC_DIR.'payments.php');

//Payments Metaboxes
require_once(MPESA_INC_DIR.'metaboxes.php');

function get_post_id_by_meta_key_and_value($key, $value)
{
    global $wpdb;
    $meta = $wpdb->get_results("SELECT * FROM `".$wpdb->postmeta."` WHERE meta_key='".$key."' AND meta_value='".$value."'");
    if (is_array($meta) && !empty($meta) && isset($meta[0])) {
        $meta = $meta[0];
    }

    if (is_object($meta)) {
        return $meta->post_id;
    } else {
        return false;
    }
}

/**
 * Installation hook callback creates plugin settings
 */
register_activation_hook(__FILE__, 'wc_mpesa_install');
function wc_mpesa_install()
{
    update_option('wc_mpesa_version', WC_MPESA_VERSION);
    update_option('wc_mpesa_urls_reg', 0);
}

/**
 * Uninstall hook callback deletes plugin settings
 */
register_uninstall_hook(__FILE__, 'wc_mpesa_uninstall');
function wc_mpesa_uninstall()
{
    delete_option('wc_mpesa_version');
    delete_option('wc_mpesa_urls_reg');
}

function register_urls_notice()
{
    if (get_option('wc_mpesa_urls_reg', 0)) {
        echo '<div class="notification">You need to register your confirmation and validation endpoints to work.</div>';
    }
}

add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'mpesa_action_links');
function mpesa_action_links($links)
{
    return array_merge($links, [ '<a href="'.admin_url('admin.php?page=wc-settings&tab=checkout&section=mpesa').'">&nbsp;Preferences</a>' ]);
}

add_filter('plugin_row_meta', 'mpesa_row_meta', 10, 2);
function mpesa_row_meta($links, $file)
{
    $plugin = plugin_basename(__FILE__);

    if ($plugin == $file) {
        $row_meta = array(
            'github'    => '<a href="' . esc_url('https://github.com/mwamodo/mpesa-stk-wc/') . '" target="_blank" aria-label="' . esc_attr__('Contribute on Github', 'woocommerce') . '">' . esc_html__('Github', 'woocommerce') . '</a>',
            'apidocs' => '<a href="' . esc_url('https://developer.safaricom.co.ke/docs/') . '" target="_blank" aria-label="' . esc_attr__('MPesa API Docs ( Daraja )', 'woocommerce') . '">' . esc_html__('API docs', 'woocommerce') . '</a>',
            'pro' => '<a href="' . esc_url('https://nineafrica.com/products') . '" target="_blank" aria-label="' . esc_attr__('Get Pro Version', 'woocommerce') . '">' . esc_html__('Get pro', 'woocommerce') . '</a>'
         );

        return array_merge($links, $row_meta);
    }

    return ( array ) $links;
}

/**
 * Add Kenyan counties to list of woocommerce states
 */
add_filter('woocommerce_states', 'mpesa_ke_woocommerce_counties');
function mpesa_ke_woocommerce_counties($counties)
{
    $counties['KE'] = array(
        'BAR' => __('Baringo', 'woocommerce'),
        'BMT' => __('Bomet', 'woocommerce'),
        'BGM' => __('Bungoma', 'woocommerce'),
        'BSA' => __('Busia', 'woocommerce'),
        'EGM' => __('Elgeyo-Marakwet', 'woocommerce'),
        'EBU' => __('Embu', 'woocommerce'),
        'GSA' => __('Garissa', 'woocommerce'),
        'HMA' => __('Homa Bay', 'woocommerce'),
        'ISL' => __('Isiolo', 'woocommerce'),
        'KAJ' => __('Kajiado', 'woocommerce'),
        'KAK' => __('Kakamega', 'woocommerce'),
        'KCO' => __('Kericho', 'woocommerce'),
        'KBU' => __('Kiambu', 'woocommerce'),
        'KLF' => __('Kilifi', 'woocommerce'),
        'KIR' => __('Kirinyaga', 'woocommerce'),
        'KSI' => __('Kisii', 'woocommerce'),
        'KIS' => __('Kisumu', 'woocommerce'),
        'KTU' => __('Kitui', 'woocommerce'),
        'KLE' => __('Kwale', 'woocommerce'),
        'LKP' => __('Laikipia', 'woocommerce'),
        'LAU' => __('Lamu', 'woocommerce'),
        'MCS' => __('Machakos', 'woocommerce'),
        'MUE' => __('Makueni', 'woocommerce'),
        'MDA' => __('Mandera', 'woocommerce'),
        'MAR' => __('Marsabit', 'woocommerce'),
        'MRU' => __('Meru', 'woocommerce'),
        'MIG' => __('Migori', 'woocommerce'),
        'MBA' => __('Mombasa', 'woocommerce'),
        'MRA' => __('Muranga', 'woocommerce'),
        'NBO' => __('Nairobi', 'woocommerce'),
        'NKU' => __('Nakuru', 'woocommerce'),
        'NDI' => __('Nandi', 'woocommerce'),
        'NRK' => __('Narok', 'woocommerce'),
        'NYI' => __('Nyamira', 'woocommerce'),
        'NDR' => __('Nyandarua', 'woocommerce'),
        'NER' => __('Nyeri', 'woocommerce'),
        'SMB' => __('Samburu', 'woocommerce'),
        'SYA' => __('Siaya', 'woocommerce'),
        'TVT' => __('Taita Taveta', 'woocommerce'),
        'TAN' => __('Tana River', 'woocommerce'),
        'TNT' => __('Tharaka-Nithi', 'woocommerce'),
        'TRN' => __('Trans-Nzoia', 'woocommerce'),
        'TUR' => __('Turkana', 'woocommerce'),
        'USG' => __('Uasin Gishu', 'woocommerce'),
        'VHG' => __('Vihiga', 'woocommerce'),
        'WJR' => __('Wajir', 'woocommerce'),
        'PKT' => __('West Pokot', 'woocommerce')
     );

    return $counties;
}

/*
 * Register our gateway with woocommerce
 */
add_filter('woocommerce_payment_gateways', 'wc_mpesa_add_to_gateways');
function wc_mpesa_add_to_gateways($gateways)
{
    $gateways[] = 'WC_Gateway_MPESA';
    return $gateways;
}

add_action('plugins_loaded', 'wc_mpesa_gateway_init', 11);
function wc_mpesa_gateway_init()
{
    /**
     * @class WC_Gateway_MPesa
     * @extends WC_Payment_Gateway
     */
    class WC_Gateway_MPESA extends WC_Payment_Gateway
    {
        public $mpesa_name;
        public $mpesa_shortcode;
        public $mpesa_headoffice;
        public $mpesa_type;
        public $mpesa_key;
        public $mpesa_secret;
        public $mpesa_passkey;
        public $mpesa_callback_url;
        public $mpesa_timeout_url;
        public $mpesa_result_url;
        public $mpesa_confirmation_url;
        public $mpesa_validation_url;

        public $mpesa_env = 'sanbox';

        /**
         * Constructor for the gateway.
         */
        public function __construct()
        {
            $env = get_option('woocommerce_mpesa_settings')["env"];
            $reg_notice = '<li><a href="'.home_url('/?mpesa_ipn_register='.$env).'" target="_blank">Click here to register confirmation & validation URLs</a>. You only need to do this once for sandbox and once when you go live.</li>';
            $test_cred = ($env == 'sandbox') ? '<li>You can <a href="https://developer.safaricom.co.ke/test_credentials" target="_blank" >generate sandbox test credentials here</a>.</li>' : '';
            //$reg_notice = has_valid_licence() ? '' : $reg_notice;

            $this->id                 		= 'mpesa';
            $this->icon               		= apply_filters('woocommerce_mpesa_icon', plugins_url('mpesa.png', __FILE__));
            $this->method_title       		= __('Lipa Na MPesa', 'woocommerce');
            $this->method_description 		= __('<h4 style="color: red;">IMPORTANT!</h4><li>Please <a href="https://developer.safaricom.co.ke/" target="_blank" >create an app on Daraja</a> if you haven\'t. Fill in the app\'s consumer key and secret below.</li><li>For security purposes, and for the MPesa Instant Payment Notification to work, ensure your site is running over https(SSL).</li>'.$reg_notice.$test_cred).'<li>We offer test to production migration service at a flat fee of 6500 KES/$65. Email <a href="mailto:packages@nineafrica.com">packages@nineafrica.com</a> if you need help.</li>';
            $this->has_fields         		= false;

            // Load settings
            $this->init_form_fields();
            $this->init_settings();

            // Get settings
            $this->title              		= $this->get_option('title');
            $this->description        		= $this->get_option('description');
            $this->instructions       		= $this->get_option('instructions');
            $this->enable_for_methods 		= $this->get_option('enable_for_methods', array());
            $this->enable_for_virtual 		= $this->get_option('enable_for_virtual', 'yes') === 'yes' ? true : false;

            $this->mpesa_name 				= $this->get_option('business');
            $this->mpesa_shortcode 			= $this->get_option('shortcode');
            $this->mpesa_headoffice 		= $this->get_option('headoffice');
            $this->mpesa_type 				= $this->get_option('idtype');
            $this->mpesa_key 				= $this->get_option('key');
            $this->mpesa_secret 			= $this->get_option('secret');
            $this->mpesa_username 			= $this->get_option('username');
            $this->mpesa_password 			= $this->get_option('password');
            $this->mpesa_passkey 			= $this->get_option('passkey');
            $this->mpesa_callback_url 		= rtrim(home_url(), '/').':'.$_SERVER['SERVER_PORT'].'/?mpesa_ipn_listener=reconcile';
            $this->mpesa_timeout_url 		= rtrim(home_url(), '/').':'.$_SERVER['SERVER_PORT'].'/?mpesa_ipn_listener=timeout';
            $this->mpesa_result_url 		= rtrim(home_url(), '/').':'.$_SERVER['SERVER_PORT'].'/?mpesa_ipn_listener=reconcile';
            $this->mpesa_confirmation_url 	= rtrim(home_url(), '/').':'.$_SERVER['SERVER_PORT'].'/?mpesa_ipn_listener=confirm';
            $this->mpesa_validation_url 	= rtrim(home_url(), '/').':'.$_SERVER['SERVER_PORT'].'/?mpesa_ipn_listener=validate';

            $this->mpesa_env 			= $this->get_option('env');

            $this->mpesa_codes = array(
                0	=> 'Success',
                1	=> 'Insufficient Funds',
                2	=> 'Less Than Minimum Transaction Value',
                3	=> 'More Than Maximum Transaction Value',
                4	=> 'Would Exceed Daily Transfer Limit',
                5	=> 'Would Exceed Minimum Balance',
                6	=> 'Unresolved Primary Party',
                7	=> 'Unresolved Receiver Party',
                8	=> 'Would Exceed Maximum Balance',
                11	=> 'Debit Account Invalid',
                12	=> 'Credit Account Invalid',
                13	=> 'Unresolved Debit Account',
                14	=> 'Unresolved Credit Account',
                15	=> 'Duplicate Detected',
                17	=> 'Internal Failure',
                20	=> 'Unresolved Initiator',
                26	=> 'Traffic blocking condition in place'
            );

            add_action('woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ));
            add_filter('woocommerce_payment_complete_order_status', array( $this, 'change_payment_complete_order_status' ), 10, 3);

            // Customer Emails
            add_action('woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3);

            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ));
        }

        /**
         * Initialise Gateway Settings Form Fields.
         */
        public function init_form_fields()
        {
            $shipping_methods = array();

            foreach (WC()->shipping()->load_shipping_methods() as $method) {
                $shipping_methods[ $method->id ] = $method->get_method_title();
            }

            $this->form_fields = array(
                'enabled' => array(
                    'title'       => __('Enable/Disable', 'woocommerce'),
                    'label'       => __('Enable '.$this->method_title, 'woocommerce'),
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'no',
                 ),
                'title' => array(
                    'title'       => __('Method Title', 'woocommerce'),
                    'type'        => 'text',
                    'description' => __('Payment method name that the customer will see on your checkout.', 'woocommerce'),
                    'default'     => __('Lipa Na MPesa', 'woocommerce'),
                    'desc_tip'    => true,
                 ),
                'env' => array(
                    'title'       => __('Environment', 'woocommerce'),
                    'type'        => 'select',
                    'options' 		=> array(
                         'sandbox' 	=> __('Sandbox', 'woocommerce'),
                          'live' 		=> __('Live', 'woocommerce'),
                    ),
                    'description' => __('MPesa Environment', 'woocommerce'),
                    'desc_tip'    => true,
                 ),
                'idtype' => array(
                    'title'       => __('Identifier Type', 'woocommerce'),
                    'type'        => 'select',
                    'options' => array(
                          /**1 => __( 'MSISDN', 'woocommerce' ),*/
                          4 => __('Paybill Number', 'woocommerce'),
                         2 => __('Till Number', 'woocommerce')
                    ),
                    'description' => __('MPesa Identifier Type', 'woocommerce'),
                    'desc_tip'    => true,
                 ),
                'headoffice' => array(
                    'title'       => __('Head Office Number', 'woocommerce'),
                    'type'        => 'text',
                    'description' => __('HO (for Till) or Paybill Number. Use "Online Shortcode" in Sandbox', 'woocommerce'),
                    'default'     => __('174379', 'woocommerce'),
                    'desc_tip'    => true,
                 ),
                'shortcode' => array(
                    'title'       => __('Business Shortcode', 'woocommerce'),
                    'type'        => 'text',
                    'description' => __('Your MPesa Business Till/Paybill Number. Use "Online Shortcode" in Sandbox', 'woocommerce'),
                    'default'     => __('174379', 'woocommerce'),
                    'desc_tip'    => true,
                 ),
                'key' => array(
                    'title'       => __('App Consumer Key', 'woocommerce'),
                    'type'        => 'text',
                    'description' => __('Your App Consumer Key From Safaricom Daraja.', 'woocommerce'),
                    'default'     => __('9v38Dtu5u2BpsITPmLcXNWGMsjZRWSTG', 'woocommerce'),
                    'desc_tip'    => true,
                 ),
                'secret' => array(
                    'title'       => __('App Consumer Secret', 'woocommerce'),
                    'type'        => 'text',
                    'description' => __('Your App Consumer Secret From Safaricom Daraja.', 'woocommerce'),
                    'default'     => __('bclwIPkcRqw61yUt', 'woocommerce'),
                    'desc_tip'    => true,
                 ),
                'passkey' => array(
                    'title'       => __('Online Pass Key', 'woocommerce'),
                    'type'        => 'textarea',
                    'description' => __('Used to create a password for use when making a Lipa Na M-Pesa Online Payment API call.', 'woocommerce'),
                    'default'     => __('bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919', 'woocommerce'),
                    'desc_tip'    => true,
                 ),
                'description' => array(
                    'title'       => __('Method Description', 'woocommerce'),
                    'type'        => 'textarea',
                    'description' => __('Payment method description that the customer will see on your checkout.', 'woocommerce'),
                    'default'     => __('Cross-check your details above before pressing the button below.
Your phone number MUST be registered with MPesa( and ON ) for this to work.
You will get a pop-up on your phone asking you to confirm the payment.
Enter your service ( MPesa ) PIN to proceed.
You will receive a confirmation message shortly thereafter.', 'woocommerce'),
                    'desc_tip'    => true,
                 ),
                'instructions' => array(
                    'title'       => __('Instructions', 'woocommerce'),
                    'type'        => 'textarea',
                    'description' => __('Instructions that will be added to the thank you page.', 'woocommerce'),
                    'default'     => __('Thank you for buying from us. You will receive a confirmation message from MPesa shortly.', 'woocommerce'),
                    'desc_tip'    => true,
                 ),
                'enable_for_methods' => array(
                    'title'             => __('Enable for shipping methods', 'woocommerce'),
                    'type'              => 'multiselect',
                    'class'             => 'wc-enhanced-select',
                    'css'               => 'width: 400px;',
                    'default'           => '',
                    'description'       => __('If MPesa is only available for certain methods, set it up here. Leave blank to enable for all methods.', 'woocommerce'),
                    'options'           => $shipping_methods,
                    'desc_tip'          => true,
                    'custom_attributes' => array(
                        'data-placeholder' => __('Select shipping methods', 'woocommerce'),
                     ),
                 ),
                'enable_for_virtual' => array(
                    'title'             => __('Accept for virtual orders', 'woocommerce'),
                    'label'             => __('Accept MPesa if the order is virtual', 'woocommerce'),
                    'type'              => 'checkbox',
                    'default'           => 'yes',
                 ),
                'account' => array(
                    'title'       => __('Account Name', 'woocommerce'),
                    'type'        => 'text',
                    'description' => __('Account Name to show to customer in STK Push.', 'woocommerce'),
                    'default'     => __('Mpesa STK WC', 'woocommerce'),
                    'desc_tip'    => true,
                 ),
                'accountant' => array(
                    'title'       => __('Accountant', 'woocommerce'),
                    'type'        => 'text',
                    'description' => __('ID of WordPress user to assign authorship of payments generated by this plugin', 'woocommerce'),
                    'default'     => __('1', 'woocommerce'),
                    'desc_tip'    => true,
                 ),
                'completion' => array(
                    'title'       => __('Order Status on Payment', 'woocommerce'),
                    'type'        => 'select',
                    'options' => array(
                          'complete' => __('Mark order as complete', 'woocommerce'),
                         'processing' => __('Mark order as processing', 'woocommerce')
                    ),
                    'description' => __('What status to set the order after Mpesa payment has been received', 'woocommerce'),
                    'desc_tip'    => true,
                 )
             );
        }

        /**
         * Check If The Gateway Is Available For Use.
         *
         * @return bool
         */
        public function is_available()
        {
            $order          = null;
            $needs_shipping = false;

            // Test if shipping is needed first
            if (WC()->cart && WC()->cart->needs_shipping()) {
                $needs_shipping = true;
            } elseif (is_page(wc_get_page_id('checkout')) && 0 < get_query_var('order-pay')) {
                $order_id = absint(get_query_var('order-pay'));
                $order    = wc_get_order($order_id);

                // Test if order needs shipping.
                if (0 < sizeof($order->get_items())) {
                    foreach ($order->get_items() as $item) {
                        $_product = $item->get_product();
                        if ($_product && $_product->needs_shipping()) {
                            $needs_shipping = true;
                            break;
                        }
                    }
                }
            }

            $needs_shipping = apply_filters('woocommerce_cart_needs_shipping', $needs_shipping);

            // Virtual order, with virtual disabled
            if (! $this->enable_for_virtual && ! $needs_shipping) {
                return false;
            }

            // Only apply if all packages are being shipped via chosen method, or order is virtual.
            if (! empty($this->enable_for_methods) && $needs_shipping) {
                $chosen_shipping_methods = array();

                if (is_object($order)) {
                    $chosen_shipping_methods = array_unique(array_map('wc_get_string_before_colon', $order->get_shipping_methods()));
                } elseif ($chosen_shipping_methods_session = WC()->session->get('chosen_shipping_methods')) {
                    $chosen_shipping_methods = array_unique(array_map('wc_get_string_before_colon', $chosen_shipping_methods_session));
                }

                if (0 < count(array_diff($chosen_shipping_methods, $this->enable_for_methods))) {
                    return false;
                }
            }

            return parent::is_available();
        }

        /**
         * Allow transaction to proceed
         * @todo Get WC transaction ID
         */
        public function proceed($transID = 0)
        {
            return array(
              'ResponseCode'  => 0,
              'ResponseDesc'  => 'Success',
              'ThirdPartyTransID'	=> $transID
             );
        }

        public function reject($transID = 0)
        {
            return array(
              'ResponseCode'  		=> 1,
              'ResponseDesc'  		=> 'Failed',
              'ThirdPartyTransID'	=> $transID
             );
        }

        public function authenticate()
        {
            $endpoint = ($this->mpesa_env == 'live') ? 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials' : 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

            $credentials = base64_encode($this->mpesa_key.':'.$this->mpesa_secret);

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $endpoint);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array( 'Authorization: Basic '.$credentials ));
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $curl_response = curl_exec($curl);

            return json_decode($curl_response)->access_token;
        }

        /**
         * Register confirmation and validation endpoints
         */
        public function register_urls()
        {
            $token = $this->authenticate();

            $endpoint = ($this->mpesa_env == 'live') ? 'https://api.safaricom.co.ke/mpesa/c2b/v1/registerurl' : 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl';

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $endpoint);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array( 'Content-Type:application/json','Authorization:Bearer '.$token ));

            $curl_post_data = array(
                'ShortCode' 		=> $this->mpesa_shortcode,
                'ResponseType' 		=> 'Cancelled',
                'ConfirmationURL' 	=> $this->mpesa_confirmation_url,
                'ValidationURL' 	=> $this->mpesa_validation_url
            );

            $data_string = json_encode($curl_post_data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($curl, CURLOPT_HEADER, false);
            $content = curl_exec($curl);
            if ($content) {
                $msg = json_decode($content);
                $status = isset($msg->ResponseDescription) ? $msg->ResponseDescription : "Coud not register URLs";
            } else {
                $status = "Sorry could not connect to Daraja. Check your configuration and try again.";
            }
            return array( 'Registration status' => $status );
        }

        /**
         * Process the payment and return the result.
         *
         * @param int $order_id
         * @return array
         */
        public function process_payment($order_id)
        {
            $order = new WC_Order($order_id);

            $total = $order->get_total();
            $phone = $order->get_billing_phone();
            $first_name = $order->get_billing_first_name();
            $last_name = $order->get_billing_last_name();

            $phone = str_replace("+", "", $phone);
            $phone = preg_replace('/^0/', '254', $phone);

            $token = $this->authenticate();

            $endpoint = ($this->mpesa_env == 'live') ? 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest' : 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

            $timestamp = date('YmdHis');
            $password = base64_encode($this->mpesa_headoffice.$this->mpesa_passkey.$timestamp);
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $endpoint);
            curl_setopt(
                $curl,
                CURLOPT_HTTPHEADER,
                array(
                    'Content-Type:application/json',
                    'Authorization:Bearer '.$token
                )
            );

            $curl_post_data = array(
                'BusinessShortCode' => $this->mpesa_headoffice,
                'Password' 			=> $password,
                'Timestamp' 		=> $timestamp,
                'TransactionType' 	=> ($this->get_option('idtype') == 4) ? 'CustomerPayBillOnline' : 'CustomerBuyGoodsOnline',
                'Amount' 			=> round($total),
                'PartyA' 			=> $phone,
                'PartyB' 			=> $this->mpesa_shortcode,
                'PhoneNumber' 		=> $phone,
                'CallBackURL' 		=> $this->mpesa_callback_url,
                'AccountReference' 	=> ($this->get_option('account') == 'WC') ? 'WC'.$order_id : $this->get_option('account'),
                'TransactionDesc' 	=> 'WooCommerce Payment For '.$order_id,
                'Remark'			=> 'WooCommerce Payment Via MPesa'
            );

            $data_string = json_encode($curl_post_data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($curl, CURLOPT_HEADER, false);
            $content = curl_exec($curl);
            $result = json_decode($content);

            $request_id = $result->MerchantRequestID;

            if (! $content) {
                $error_message = 'Could not connect to MPesa to process payment. Please try again';
                $order->update_status('failed', __('Could not connect to MPesa to process payment.', 'woocommerce'));
                wc_add_notice(__('Failed! ', 'woocommerce') . $error_message, 'error');
                return array(
                    'result' 	=> 'fail',
                    'redirect'	=> ''
                );
            } elseif (isset($result->errorCode)) {
                $error_message = 'MPesa Error '.$result->errorCode.': '.$result->errorMessage;
                $order->update_status('failed', __($error_message, 'woocommerce'));
                wc_add_notice(__('Failed! ', 'woocommerce') . $error_message, 'error');
                return array(
                    'result' 	=> 'fail',
                    'redirect'	=> ''
                );
            } else {
                /**
                 * Temporarily set status as "on-hold", incase the MPesa API times out before processing our request
                 */
                $order->update_status('on-hold', __('Awaiting MPesa confirmation of payment from '.$phone.'.', 'woocommerce'));

                // Reduce stock levels
                wc_reduce_stock_levels($order_id);

                // Remove cart
                WC()->cart->empty_cart();

                $author = is_user_logged_in() ? get_current_user_id() : $this->get_option('accountant');

                // Insert the payment into the database
                $post_id = wp_insert_post(
                    array(
                        'post_title' 	=> 'Checkout',
                        'post_content'	=> "Response: ".$content."\nToken: ".$token,
                        'post_status'	=> 'publish',
                        'post_type'		=> 'mpesaipn',
                        'post_author'	=> $author,
                    )
                );

                update_post_meta($post_id, '_customer', "{$first_name} {$last_name}");
                update_post_meta($post_id, '_phone', $phone);
                update_post_meta($post_id, '_order_id', $order_id);
                update_post_meta($post_id, '_request_id', $request_id);
                update_post_meta($post_id, '_amount', $total);
                update_post_meta($post_id, '_paid', $total-$total);
                update_post_meta($post_id, '_balance', $total);
                update_post_meta($post_id, '_receipt', '');
                update_post_meta($post_id, '_order_status', 'on-hold');

                $this->instructions .= '<p>Awaiting MPesa confirmation of payment from '.$phone.' for request '.$request_id.'. Check your phone for the STK Prompt.</p>';

                // Return thankyou redirect
                return array(
                    'result' 	=> 'success',
                    'redirect'	=> $this->get_return_url($order),
                 );
            }
        }

        /**
         * Output for the order received page.
         */
        public function thankyou_page()
        {
            if ($this->instructions) {
                echo wpautop(wptexturize($this->instructions));
            }
        }

        /**
         * Change payment complete order status to completed for MPesa orders.
         *
         * @since  3.1.0
         * @param  string $status
         * @param  int $order_id
         * @param  WC_Order $order
         * @return string
         */
        public function change_payment_complete_order_status($status, $order_id = 0, $order = false)
        {
            if ($order && 'mpesa' === $order->get_payment_method()) {
                $status = 'completed';
            }
            return $status;
        }

        /**
         * Add content to the WC emails.
         *
         * @access public
         * @param WC_Order $order
         * @param bool $sent_to_admin
         * @param bool $plain_text
         */
        public function email_instructions($order, $sent_to_admin, $plain_text = false)
        {
            if ($this->instructions && ! $sent_to_admin && $this->id === $order->get_payment_method()) {
                echo wpautop(wptexturize($this->instructions)) . PHP_EOL;
            }
        }
    }
}

/**
 * Register Validation and Confirmation URLs
 * Outputs registration status
 */
add_action('init', 'wc_mpesa_register');
function wc_mpesa_register()
{
    header("Access-Control-Allow-Origin: *");
    header('Content-Type:Application/json');
    if (! isset($_GET['mpesa_ipn_register'])) {
        return;
    }

    $mpesa = new WC_Gateway_MPESA();
    wp_send_json($mpesa->register_urls());
}

/**
 *
 */
add_action('init', 'wc_mpesa_confirm');
function wc_mpesa_confirm()
{
    if (! isset($_GET['mpesa_ipn_listener'])) {
        return;
    }
    if ($_GET['mpesa_ipn_listener'] !== 'confirm') {
        return;
    }

    $response = json_decode(file_get_contents('php://input'), true);

    if (! isset($response['Body'])) {
        return;
    }
    header("Access-Control-Allow-Origin: *");
    header('Content-Type:Application/json');

    $mpesa = new WC_Gateway_MPESA();
    wp_send_json($mpesa->proceed());
}

/**
 *
 */
add_action('init', 'wc_mpesa_validate');
function wc_mpesa_validate()
{
    if (! isset($_GET['mpesa_ipn_listener'])) {
        return;
    }
    if ($_GET['mpesa_ipn_listener'] !== 'validate') {
        return;
    }

    $response = json_decode(file_get_contents('php://input'), true);

    if (! isset($response['Body'])) {
        return;
    }
    header("Access-Control-Allow-Origin: *");
    header('Content-Type:Application/json');

    $mpesa = new WC_Gateway_MPESA();
    wp_send_json($mpesa->proceed());
}

/**
 *
 */
add_action('init', 'wc_mpesa_reconcile');
function wc_mpesa_reconcile()
{
    if (! isset($_GET['mpesa_ipn_listener'])) {
        return;
    }
    if ($_GET['mpesa_ipn_listener'] !== 'reconcile') {
        return;
    }

    $response = json_decode(file_get_contents('php://input'), true);

    if (! isset($response['Body'])) {
        return;
    }

    $resultCode 						= $response['Body']['stkCallback']['ResultCode'];
    $resultDesc 						= $response['Body']['stkCallback']['ResultDesc'];
    $merchantRequestID 					= $response['Body']['stkCallback']['MerchantRequestID'];
    $checkoutRequestID 					= $response['Body']['stkCallback']['CheckoutRequestID'];

    $post = get_post_id_by_meta_key_and_value('_request_id', $merchantRequestID);
    wp_update_post([ 'post_content' => file_get_contents('php://input'), 'ID' => $post ]);

    $order_id 							= get_post_meta($post, '_order_id', true);
    $amount_due 						=  get_post_meta($post, '_amount', true);
    $before_ipn_paid 					= get_post_meta($post, '_paid', true);

    if (wc_get_order($order_id)) {
        $order 							= new WC_Order($order_id);
        $first_name 					= $order->get_billing_first_name();
        $last_name 						= $order->get_billing_last_name();
        $customer 						= "{$first_name} {$last_name}";
    } else {
        $customer 						= "MPesa Customer";
    }

    if (isset($response['Body']['stkCallback']['CallbackMetadata'])) {
        $amount 						= $response['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'];
        $mpesaReceiptNumber 			= $response['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value'];
        $balance 						= $response['Body']['stkCallback']['CallbackMetadata']['Item'][2]['Value'];
        $transactionDate 				= $response['Body']['stkCallback']['CallbackMetadata']['Item'][3]['Value'];
        $phone 							= $response['Body']['stkCallback']['CallbackMetadata']['Item'][4]['Value'];

        $after_ipn_paid = round($before_ipn_paid)+round($amount);
        $ipn_balance = $after_ipn_paid-$amount_due;

        if (wc_get_order($order_id)) {
            $order = new WC_Order($order_id);

            if ($ipn_balance == 0) {
                $mpesa = new WC_Gateway_MPESA();
                $order->update_status($mpesa->get_option('completion'));
                $order->add_order_note(__("Full MPesa Payment Received From {$phone}. Receipt Number {$mpesaReceiptNumber}"));
                update_post_meta($post, '_order_status', 'complete');
            } elseif ($ipn_balance < 0) {
                $currency = get_woocommerce_currency();
                $order->payment_complete();
                $order->add_order_note(__("{$phone} has overpayed by {$currency} {$balance}. Receipt Number {$mpesaReceiptNumber}"));
                update_post_meta($post, '_order_status', 'complete');
            } else {
                $order->update_status('on-hold');
                $order->add_order_note(__("MPesa Payment from {$phone} Incomplete"));
                update_post_meta($post, '_order_status', 'on-hold');
            }
        }

        update_post_meta($post, '_paid', $after_ipn_paid);
        update_post_meta($post, '_amount', $amount_due);
        update_post_meta($post, '_balance', $balance);
        update_post_meta($post, '_phone', $phone);
        update_post_meta($post, '_customer', $customer);
        update_post_meta($post, '_order_id', $order_id);
        update_post_meta($post, '_receipt', $mpesaReceiptNumber);
    } else {
        if (wc_get_order($order_id)) {
            $order = new WC_Order($order_id);
            $order->update_status('on-hold');
            $order->add_order_note(__("MPesa Error {$resultCode}: {$resultDesc}"));
        }
    }
}

/**
 *
 */
add_action('init', 'wc_mpesa_timeout');
function wc_mpesa_timeout()
{
    if (! isset($_GET['mpesa_ipn_listener'])) {
        return;
    }
    if ($_GET['mpesa_ipn_listener'] !== 'timeout') {
        return;
    }

    $response = json_decode(file_get_contents('php://input'), true);

    if (! isset($response['Body'])) {
        return;
    }

    $resultCode 					= $response['Body']['stkCallback']['ResultCode'];
    $resultDesc 					= $response['Body']['stkCallback']['ResultDesc'];
    $merchantRequestID 				= $response['Body']['stkCallback']['MerchantRequestID'];
    $checkoutRequestID 				= $response['Body']['stkCallback']['CheckoutRequestID'];

    $post = get_post_id_by_meta_key_and_value('_request_id', $merchantRequestID);
    wp_update_post([ 'post_content' => file_get_contents('php://input'), 'ID' => $post ]);
    update_post_meta($post, '_order_status', 'pending');

    $order_id = get_post_meta($post, '_order_id', true);
    if (wc_get_order($order_id)) {
        $order = new WC_Order($order_id);

        $order->update_status('pending');
        $order->add_order_note(__("MPesa Payment Timed Out", 'woocommerce'));
    }
}

add_filter('manage_edit-shop_order_columns', 'wcmpesa_new_order_column');
function wcmpesa_new_order_column($columns)
{
    $columns['mpesa'] = 'Reinitiate Mpesa';
    return $columns;
}

add_filter('woocommerce_email_attachments', 'woocommerce_emails_attach_downloadables', 10, 3);
function woocommerce_emails_attach_downloadables($attachments, $status, $order)
{
    if (! is_object($order) || ! isset($status)) {
        return $attachments;
    }
    if (empty($order)) {
        return $attachments;
    }
    if (! $order->has_downloadable_item()) {
        return $attachments;
    }
    $allowed_statuses = array( 'customer_invoice', 'customer_completed_order' );
    if (isset($status) && in_array($status, $allowed_statuses)) {
        foreach ($order->get_items() as $item_id => $item) {
            foreach ($order->get_item_downloads($item) as $download) {
                $attachments[] = str_replace(content_url(), WP_CONTENT_DIR, $download['file']);
            }
        }
    }
    return $attachments;
}

add_action('woocommerce_email_order_details', 'uiwc_email_order_details_products', 1, 4);
function uiwc_email_order_details_products($order, $admin, $plain, $email)
{
    $post = get_post_id_by_meta_key_and_value('_order_id', $merchantRequestID);
    $receipt = get_post_meta($post, '_receipt', true);
    echo __('<strong>MPESA RECEIPT NUMBER:</strong> '.$receipt, 'uiwc');
}

add_action('woocommerce_before_email_order', 'add_order_instruction_email', 10, 2);
function add_order_instruction_email($order, $sent_to_admin)
{
    if (! $sent_to_admin) {
        if ('mpesa' == $order->payment_method) {
            echo wpautop(wptexturize($instructions)) . PHP_EOL;
        }
    }
}
