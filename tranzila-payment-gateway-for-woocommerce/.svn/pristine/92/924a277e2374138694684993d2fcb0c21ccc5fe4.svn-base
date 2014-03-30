<?php
/*
Plugin Name: WooCommerce Tranzila Gateway
Plugin URI: http://woothemes.com/woocommerce
Description: Extends WooCommerce with an Tranzila gateway.
Version: 1.0
Author: Dan Green
Author URI: http://tlvwebdevelopment.com
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

add_action('plugins_loaded', 'woocommerce_gateway_tranzila_init', 0);

function register_session() {
    if (!session_id())
        session_start();
}

add_action('init', 'register_session');

function woocommerce_gateway_tranzila_init() {

	if ( !class_exists( 'WC_Payment_Gateway' ) ) return;

	/**
 	 * Localisation
	 */
	load_plugin_textdomain('wc-gateway-tranzila', false, dirname( plugin_basename( __FILE__ ) ) . '/languages');
    
	/**
 	 * Gateway class
 	 */
	class WC_tranzila_Gateway extends WC_Payment_Gateway {
		
		public function __construct(){
			$this -> id = 'tranzila';
			$this -> medthod_title = 'tranzila';
			$this -> has_fields = false;
			
			$this -> init_form_fields();
			$this -> init_settings();
			
			$this -> title = $this -> get_option('title');
			$this -> description = $this -> get_option('description');
			$this -> merchant_id = $this -> get_option('merchant_id');
			$_SESSION['tranzila_terminal'] = $this->get_option('terminal_name');		
			$this->order_button_text = __( 'Proceed to Tranzila', 'woocommerce' );


			
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );			
		   
		}
		    
		function init_form_fields(){
		 
		       $this -> form_fields = array(
		                'enabled' => array(
		                    'title' => __('Enable/Disable', 'tlvwebdevelopment'),
		                    'type' => 'checkbox',
		                    'label' => __('Enable Tranzila Payment Module.', 'tlvwebdevelopment'),
		                    'default' => 'no'),
		                'title' => array(
		                    'title' => __('Title:', 'tlvwebdevelopment'),
		                    'type'=> 'text',
		                    'description' => __('This controls the title which the user sees during checkout.', 'tlvwebdevelopment'),
		                    'default' => __('Tranzila', 'tlvwebdevelopment')),
		                'description' => array(
		                    'title' => __('Description:', 'tlvwebdevelopment'),
		                    'type' => 'textarea',
		                    'description' => __('This controls the description which the user sees during checkout.', 'tlvwebdevelopment'),
		                    'default' => __('Pay securely with an Israeli credit card using Tranzila.', 'tlvwebdevelopment')),
                        'terminal_name' => array(
                            'title' => __('Terminal Name', 'tlvwebdevelopment'),
                            'type' => 'text',
                            'description' =>  __('The Tranzila Terminal Name', 'tlvwebdevelopment'),
                        )
		               
		            );
		    }
		 
		public function admin_options(){
		        echo '<h3>'.__('Tranzila Payment Gateway', 'tlvwebdevelopment').'</h3>';
		        echo '<p>'.__('This custom payment gateway was made by TLV Web Development').'</p>';
		        echo '<table class="form-table">';
		        // Generate the HTML For the settings form.
		        $this -> generate_settings_html();
		        echo '</table>';
		 
		    }
		   
		function payment_fields(){
				// Check if tranzila has been used
			echo $this->description;
		}
		
		
		
				 
		 /**
		 	 * Process the payment and return the result
		 	 *
		 	 * @access public
		 	 * @param int $order_id
		 	 * @return array
		 	 */
	 	function process_payment( $order_id ) {
	 		global $woocommerce;
	 		
	 		$order = new WC_Order( $order_id );
	 		$_SESSION['tranzila_token'] = mt_rand();
	 		$_SESSION['encryption-key'] = mt_rand();
	 		
			$tranzila_data = array(
				'sum' => $order -> get_order_total(),
				'pdesc' => $productinfo,
				'contact' => $order -> billing_first_name." ".$order -> billing_last_name,
				'company' => "Personal",
				'email' => $order -> billing_email,
				'phone' => $order -> billing_phone,
				'fax' => "",
				'address' => $order -> billing_address_1." ".$order -> billing_address_2,
				'city' => $order -> billing_city,
				'remarks'=> $order-> customer_orer_notes,
				'currency' => '1',
				'myid' => '',
				'TranzilaToken' => $_SESSION['tranzila_token'],
				'orderid' => $order_id,
				'cred_type' => '1'
				);
				 		
	 			
	 			return array(
	 				'result' 	=> 'success',
	 				'redirect'	=> "?tranzila=sendpayment&data=".serialize($tranzila_data));
	 
	 
	 	}
	 	
		 
	}
	
	/**
 	* Add the Gateway to WooCommerce
 	**/
	function woocommerce_add_gateway_tranzila($methods) {
		$methods[] = 'WC_tranzila_Gateway';
		return $methods;
	}
	
	add_filter('woocommerce_payment_gateways', 'woocommerce_add_gateway_tranzila' );
} 
function header_func(){
	
	
	
	
	/**
	 * Returns an encrypted & utf8-encoded
	 */
	function encrypt($pure_string) {
	    $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
	    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	    $encrypted_string = mcrypt_encrypt(MCRYPT_BLOWFISH, $_SESSION['encryption-key'], utf8_encode($pure_string), MCRYPT_MODE_ECB, $iv);
	    return base64_encode($encrypted_string);
	}

	function decrypt($encrypted_string) {
	    $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
	    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	    $decrypted_string = mcrypt_decrypt(MCRYPT_BLOWFISH, $_SESSION['encryption-key'],base64_decode($encrypted_string), MCRYPT_MODE_ECB, $iv);
	    return $decrypted_string;
	}
	
	/*
	 * Send encrypted token
	 */	
	if ($_GET['tranzila']=='sendpayment'){
		
		$tranzila_info = unserialize(stripslashes($_GET['data']));
		$tranzila_info['TranzilaToken'] = encrypt($tranzila_info['TranzilaToken']);
		$tranzila_info = json_encode($tranzila_info);
		?>
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
		<script type="text/javascript">(function(d){d.fn.redirect=function(a,b,c){void 0!==c?(c=c.toUpperCase(),"GET"!=c&&(c="POST")):c="POST";if(void 0===b||!1==b)b=d().parse_url(a),a=b.url,b=b.params;var e=d("<form></form");e.attr("method",c);e.attr("action",a);for(var f in b)a=d("<input />"),a.attr("type","hidden"),a.attr("name",f),a.attr("value",b[f]),a.appendTo(e);d("body").append(e);e.submit()};d.fn.parse_url=function(a){if(-1==a.indexOf("?"))return{url:a,params:{}};var b=a.split("?"),a=b[0],c={},b=b[1].split("&"),e={},d;for(d in b){var g= b[d].split("=");e[g[0]]=g[1]}c.url=a;c.params=e;return c}})(jQuery);</script>
		
			<script type='text/javascript'>
				<?php
				echo "jQuery().redirect('https://direct.tranzila.com/".	$_SESSION['tranzila_terminal']."/', $tranzila_info)";
				?>
			</script>
			<?php
				
				
	}
	
	
	/*
	 * Check Encrypted Token
	 */
	$decrypted = decrypt($_POST['TranzilaToken'])." ". $_SESSION['tranzila_token'];
	if ($_GET['tranzila']=='successful-payment'){
		if ($decrypted == $_SESSION['tranzila_token']){
			$order = new WC_Order($_POST['orderid']);
			
			// Mark as on-hold (we're awaiting the cheque)
			$order->update_status( 'processing', __( 'Paid with Tranzila', 'woocommerce' ) );
	
			// Reduce stock levels
			$order->reduce_order_stock();
	
			// Remove cart
			WC()->cart->empty_cart();
		}
		else{
			$to = get_option("admin_email");
			$subject = "Fraud attemp via Tranzilla - forward to site admin";
			ob_start();
			var_dump($_POST);
			$message = ob_get_clean();
			wp_mail( $to, $subject, $message);
		}
	}
	
	
}
add_action( 'wp_head', 'header_func' );


