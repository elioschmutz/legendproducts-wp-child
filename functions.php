<?php

/**
 * Child Theme Function
 *
 */

add_action( 'after_setup_theme', 'mantis_child_theme_setup' );
add_action( 'wp_enqueue_scripts', 'mantis_child_enqueue_styles', 20);

if( !function_exists('mantis_child_enqueue_styles') ) {
    function mantis_child_enqueue_styles() {
        wp_enqueue_style( 'mantis-child-style',
            get_stylesheet_directory_uri() . '/style.css',
            array( 'mantis-theme' ),
            wp_get_theme()->get('Version')
        );

    }
}

if( !function_exists('mantis_child_theme_setup') ) {
    function mantis_child_theme_setup() {
        load_child_theme_textdomain( 'mantis-child', get_stylesheet_directory() . '/languages' );
    }
}

function extend_mimetypes($mime_types){
    $mime_types['woff'] = 'application/x-font-woff';
    return $mime_types;
}

function show_only_free_shipping_if_available( $rates )
{
    $free = array();
     foreach ( $rates as $rate_id => $rate )
    {
         if ( 'free_shipping' === $rate->method_id )
        {
              $free[ $rate_id ] = $rate;
              break;
        }
   }
   return ! empty( $free ) ? $free : $rates;
}

function delivery_time() {
  echo '<tr><th>' . esc_html__( 'Delivery time', 'mantis_child' ) . '</th><td>3-5 ' . esc_html__( 'days', 'mantis_child' )  . '</td></tr>';
}

add_action( 'woocommerce_review_order_before_submit', 'add_checkout_privacy_policy', 9 );
   
function add_checkout_privacy_policy() {
  
woocommerce_form_field( 'age_validation', array(
    'type'          => 'checkbox',
    'class'         => array('form-row privacy'),
    'label_class'   => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
    'input_class'   => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
    'required'      => true,
    'label'         => __('I agree, that I am at least 18 years old', 'mantis_child'),
)); 
 
woocommerce_form_field( 'newsletter', array(
    'type'          => 'checkbox',
    'class'         => array('form-row newsletter'),
    'label_class'   => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
    'input_class'   => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
    'required'      => false,
    'label'         => __('I want to subscribe to the newsletter', 'mantis_child'),
)); 
}
  
// Show notice if customer does not tick
   
add_action( 'woocommerce_checkout_process', 'not_approved_privacy' );
  
function not_approved_privacy() {
    if ( ! (int) isset( $_POST['age_validation'] ) ) {
        wc_add_notice( __( 'Please validate your age', 'mantis_child' ), 'error' );
    }
}

add_action( 'woocommerce_checkout_order_processed', 'send_on_newsletter_subscription', 10, 3 );

function send_on_newsletter_subscription( $order_id, $posted_data, $order ) {
    if ($_POST['newsletter'] != '1') {
        return;
    }
	
    $to = get_option('woocommerce_email_from_address');
    $subject = 'Newsletteranmeldung über eine Bestellung';
    $body = $posted_data['billing_first_name'] . ' ' . $posted_data['billing_last_name'] . ' hat sich mit der E-Mail: ' . $posted_data['billing_email'] . ' für den Newsletter angemeldet.' . '<br><br><a href="' . get_option('siteurl') . '/wp-admin/post.php?post=' . $order_id . '&action=edit">Bestellung #' .$order_id . ' ansehen</a>';
    $headers = array('Content-Type: text/html; charset=UTF-8');
 
    wp_mail( $to, $subject, $body, $headers );
}

add_action( 'woocommerce_cart_totals_after_shipping', 'delivery_time', 90);
add_action( 'woocommerce_review_order_after_shipping', 'delivery_time', 90);
add_filter( 'woocommerce_package_rates', 'show_only_free_shipping_if_available', 90 );

add_filter('upload_mimes', 'extend_mimetypes', 1, 1);
