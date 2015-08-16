<?php
/*
Plugin Name:    Woo Bulgarian Shipping Lite
Plugin URI:     
Description:    Save time to your visitors by putting EKONT shipping module. The Plugin contains all the offices of the company EKONT in Bulgaria.
Version:        1.0
Author:         V.Dafinov
License:        GPLv2
*/
define('WBSLURL', WP_PLUGIN_URL."/".dirname( plugin_basename( __FILE__ ) ) );

define('WBSLPATH', WP_PLUGIN_DIR."/".dirname( plugin_basename( __FILE__ ) ) );


//
// Disable default fields
add_filter( 'woocommerce_checkout_fields' , 'wbsl_custom_override_checkout_fields' );
function wbsl_custom_override_checkout_fields( $fields ) {

     unset($fields['billing']['billing_first_name']);
     unset($fields['billing']['billing_last_name']);
     unset($fields['billing']['billing_company']);
     unset($fields['billing']['billing_address_1']);
     unset($fields['billing']['billing_address_2']);
     unset($fields['billing']['billing_city']);
     unset($fields['billing']['billing_postcode']);
     unset($fields['billing']['billing_country']);
     unset($fields['billing']['billing_state']);
     unset($fields['billing']['billing_phone']);

     return $fields;

}


//
// Add custum fields
add_action( 'woocommerce_after_checkout_billing_form', 'wbsl_custom_checkout_field' );
 
function wbsl_custom_checkout_field( $checkout ) {

	  	woocommerce_form_field( 'name', array(
	        'type'          => 'text',
	        'class'         => array('my-field-class form-row-wide'),
	        'label'         => __('Име и фамилия'),
	        'placeholder'   => __(''),
	        ), $checkout->get_value( 'name' ));

		woocommerce_form_field( 'phone', array(
	        'type'          => 'text',
	        'class'         => array('my-field-class form-row-wide'),
	        'label'         => __('Телефон'),
	        'placeholder'   => __(''),
	        ), $checkout->get_value( 'phone' ));
 
    echo '<div id="to_address">';

	    woocommerce_form_field( 'custum_grad_address', array(
	        'type'          => 'text',
	        'class'         => array('hide_when_office'),
	        'label'         => __('Град'),
	        'placeholder'   => __(''),
	    ), $checkout->get_value( 'custum_grad_address' ));
	 
	    woocommerce_form_field( 'custum_address', array(
	        'type'          => 'text',
	        'class'         => array('hide_when_office'),
	        'label'         => __('Адрес'),
	        'placeholder'   => __(''),
	        ), $checkout->get_value( 'custum_address' ));

    echo '</div>';

    echo '<div id="to_office">';

	    woocommerce_form_field( 'custum_town', array(
	        'type'          => 'text',
	        'class'         => array('my-field-class form-row-wide'),
	        'label'         => __('Град'),
	        'placeholder'   => __(''),
	    ), $checkout->get_value( 'custum_town' ));

	    woocommerce_form_field( 'custum_office', array(
	        'type'          => 'select',
	        'class'         => array('my-field-class form-row-wide'),
	        'label'         => __('Офис'),
	        'placeholder'   => __(''),
	        'options' => array(''),
	    ), $checkout->get_value( 'custum_office' ));

    echo '</div>';
 
}


//
// Custum checkfield radio buttons /Shipping Mode/
add_action('woocommerce_before_checkout_billing_form', 'my_custom_checkout_field_process',1);

function my_custom_checkout_field_process() {
?>

<span>Доставка <i>/изберете</i>/</span>
<style>.wes_labels{  width: 100%;
border-radius: 3px;
border: 1px solid #D1D3D4;
}
.wes_float_left{
width: 100%;
float:left;
background-color: #ebebeb;
margin-bottom:10px;
padding: 10px 0;
}
#custum_office,#custum_office option{
height: 2em !important;
text-align:baseline !important;
}
#to_office,#to_address{
display: none;
}
#custum_town li,.ui-autocomplete li,.ui-autocomplete option,.ui-autocomplete ul{
background-color: #EBEBEB !important;
list-style-type: none;
max-width: 450px !important;
line-height: 2em;
}</style>
<br />
<div class="wes_float_left"><input type="radio" class="wes_inputs" name="shipping_mode" value="office" id="radio_office" />
<label for="radio_office" class="wes_labels">Офис на Еконт</label></div><br />
<div class="wes_float_left"><input type="radio" class="wes_inputs" name="shipping_mode" value="address" id="radio_address" />
<label for="radio_address" class="wes_labels">Личен адрес</label></div>
<br />
<?php
}

//
// Update the order meta with field value
add_action( 'woocommerce_checkout_update_order_meta', 'wbsl_custom_checkout_field_update_order_meta' );
 
function wbsl_custom_checkout_field_update_order_meta( $order_id ) {
    if ( ! empty( $_POST['name'] ) ) {
        update_post_meta( $order_id, 'name', sanitize_text_field( $_POST['name'] ) );
    }
    if ( ! empty( $_POST['phone'] ) ) {
        update_post_meta( $order_id, 'phone', sanitize_text_field( $_POST['phone'] ) );
    }
    if ( ! empty( $_POST['custum_address'] ) ) {
        update_post_meta( $order_id, 'custum_address', sanitize_text_field( $_POST['custum_address'] ) );
    }
    if ( ! empty( $_POST['custum_town'] ) ) {
        update_post_meta( $order_id, 'custum_town', sanitize_text_field( $_POST['custum_town'] ) );
    }
    if ( ! empty( $_POST['custum_office'] ) ) {
        update_post_meta( $order_id, 'custum_office', sanitize_text_field( $_POST['custum_office'] ) );
    }
    if ( ! empty( $_POST['custum_grad_address'] ) ) {
        update_post_meta( $order_id, 'custum_grad_address', sanitize_text_field( $_POST['custum_grad_address'] ) );
    }
    if( $_POST['shipping_mode'] != ''){
        update_post_meta( $order_id, 'shipping_mode',sanitize_text_field( $_POST['shipping_mode']) );
    }
}


//
// Add custum validation with errors
add_action( 'woocommerce_checkout_process', 'wbsl_woocommerce_add_error' );

function wbsl_woocommerce_add_error(  ) {
	if(   empty($_POST['shipping_mode'])) {
		wc_add_notice( __('Изберете начин на доставка','woocommerce'), 'error' );
	}
    if(   $_POST['shipping_mode'] == 'office') {
      if( empty( $_POST['name']) ){
            wc_add_notice( __('Попълнете име и фамилия','woocommerce'), 'error' );
        }
        if( empty( $_POST['custum_town']) ){
            wc_add_notice( __('Попълнете град','woocommerce'), 'error' );
        }
        if( empty($_POST['custum_office'])){
            wc_add_notice( __('Изберете Офис','woocommerce'),  'error' );
        }
        if( empty($_POST['phone'])){
            wc_add_notice( __('Попълнете телефон','woocommerce'),  'error' );
        }
    }
    if(   $_POST['shipping_mode'] == 'address') {
        if( empty( $_POST['phone']) ){
            wc_add_notice( __('Попълнете телефон','woocommerce'), 'error' );
        }
        if( empty($_POST['custum_address']) ){
            wc_add_notice( __('Попълнете адрес','woocommerce'),  'error' );
        }
        if( empty($_POST['custum_grad_address']) ){
            wc_add_notice( __('Попълнете град','woocommerce'),  'error' );
        }
    }
}


//
// Display order details on Admin page
add_action( 'woocommerce_admin_order_data_after_billing_address', 'wbsl_custom_checkout_field_display_admin_order_meta', 10, 1 );
add_action( 'woocommerce_order_details_after_customer_details', 'wbsl_custom_checkout_field_display_admin_order_meta', 10, 1 );

function wbsl_custom_checkout_field_display_admin_order_meta($order){
    if(get_post_meta( $order->id ,'shipping_mode',true) == 'office'){
	    echo '<p><strong>'.__('Доставка: Офис на ЕКОНТ').'</strong><p>';
	    echo '<p><strong>'.__('Име: ').':</strong> ' . get_post_meta( $order->id, 'name', true ) . '</p>';
	    echo '<p><strong>'.__('Телефон: ').':</strong> ' . get_post_meta( $order->id, 'phone', true ) . '</p>';
	    echo '<p><strong>'.__('Град: ').':</strong> ' . get_post_meta( $order->id, 'custum_town', true ) . '</p>';
	    echo '<p><strong>'.__('Офис: ').':</strong> ' . get_post_meta( $order->id, 'custum_office', true ) . '</p>';
	    echo '<p><strong>'.__('Бележка: ').':</strong> ' . $order->customer_note . '</p>';
    }
    if(get_post_meta( $order->id ,'shipping_mode',true) == 'address'){
	    echo '<p><strong>'.__('Доставка: личен адрес').'</strong><p>';
	    echo '<p><strong>'.__('Име: ').':</strong> ' . get_post_meta( $order->id, 'name', true ) . '</p>';
	    echo '<p><strong>'.__('Телефон: ').':</strong> ' . get_post_meta( $order->id, 'phone', true ) . '</p>';
	    echo '<p><strong>'.__('Град: ').':</strong> ' . get_post_meta( $order->id, 'custum_grad_address', true ) . '</p>';
	    echo '<p><strong>'.__('Адрес: ').':</strong> ' . get_post_meta( $order->id, 'custum_address', true ) . '</p>';
	    echo '<p><strong>'.__('Бележка: ').':</strong> ' . $order->customer_note .  '</p>';
    }
}


//
// Add jQuery-Autocomplete library
add_action('wp_enqueue_scripts', 'wbsl_scripts_method');

function my_library_method() {
   wp_register_script('library_script','https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js');
   wp_enqueue_script('library_script');
}
add_action('wp_enqueue_scripts', 'my_library_method');

function wbsl_scripts_method() {
  wp_register_script('custom_script', WBSLURL.'/js/js-auto.js', array( 'jquery' ), false, true );
  wp_enqueue_script('custom_script');
}


//
// Thank you page custumisation
remove_action( 'woocommerce_thankyou', 'woocommerce_order_details_table', 10 );

add_action('woocommerce_thankyou','wbsl_custum_thank_you_fields',10);
$cutum_notes = $order->customer_note;
function wbsl_custum_thank_you_fields($order){
  if(get_post_meta( $order ,'shipping_mode',true) == 'office'){
	    echo '<p><strong>'.__('Доставка: Офис на ЕКОНТ').'</strong><p>';
	    echo '<p><strong>'.__('Име: ').':</strong> ' . get_post_meta( $order, 'name', true ) . '</p>';
	    echo '<p><strong>'.__('Телефон: ').':</strong> ' . get_post_meta( $order, 'phone', true ) . '</p>';
	    echo '<p><strong>'.__('Град: ').':</strong> ' . get_post_meta( $order, 'custum_town', true ) . '</p>';
	    echo '<p><strong>'.__('Офис: ').':</strong> ' . get_post_meta( $order, 'custum_office', true ) . '</p>';
    }
    if(get_post_meta( $order ,'shipping_mode',true) == 'address'){
	    echo '<p><strong>'.__('Доставка: личен адрес').'</strong><p>';
	    echo '<p><strong>'.__('Име: ').':</strong> ' . get_post_meta( $order, 'name', true ) . '</p>';
	    echo '<p><strong>'.__('Телефон: ').':</strong> ' . get_post_meta( $order, 'phone', true ) . '</p>';
	    echo '<p><strong>'.__('Град: ').':</strong> ' . get_post_meta( $order, 'custum_grad_address', true ) . '</p>';
	    echo '<p><strong>'.__('Адрес: ').':</strong> ' . $customer_notes . '</p>';
    }
}


//
// Email custumization
add_action('woocommerce_email_customer_details','wbsl_custum_email');

function wbsl_custum_email($order){
    if(get_post_meta( $order->id ,'shipping_mode',true) == 'office'){
      echo '<p><strong>'.__('Доставка: Офис на ЕКОНТ').'</strong><p>';
      echo '<p><strong>'.__('Име: ').':</strong> ' . get_post_meta( $order->id, 'name', true ) . '</p>';
      echo '<p><strong>'.__('Телефон: ').':</strong> ' . get_post_meta( $order->id, 'phone', true ) . '</p>';
      echo '<p><strong>'.__('Град: ').':</strong> ' . get_post_meta( $order->id, 'custum_town', true ) . '</p>';
      echo '<p><strong>'.__('Офис: ').':</strong> ' . get_post_meta( $order->id, 'custum_office', true ) . '</p>';
      echo '<p><strong>'.__('Бележка: ').':</strong> ' . $order->customer_note . '</p>';
    }
    if(get_post_meta( $order->id ,'shipping_mode',true) == 'address'){
      echo '<p><strong>'.__('Доставка: личен адрес').'</strong><p>';
      echo '<p><strong>'.__('Име: ').':</strong> ' . get_post_meta( $order->id, 'name', true ) . '</p>';
      echo '<p><strong>'.__('Телефон: ').':</strong> ' . get_post_meta( $order->id, 'phone', true ) . '</p>';
      echo '<p><strong>'.__('Град: ').':</strong> ' . get_post_meta( $order->id, 'custum_grad_address', true ) . '</p>';
      echo '<p><strong>'.__('Адрес: ').':</strong> ' . get_post_meta( $order->id, 'custum_address', true ) . '</p>';
      echo '<p><strong>'.__('Бележка: ').':</strong> ' . $order->customer_note  . '</p>';
    }
}
?>
