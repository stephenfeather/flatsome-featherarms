<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_filter('woocommerce_checkout_posted_data', 'fa_custom_woocommerce_checkout_posted_data');
function fa_custom_woocommerce_checkout_posted_data($data){
  // The data posted by the user comes from the $data param. 
  
  // Format Shipping Information

  if($data['shipping_first_name']){
    $data['shipping_first_name'] = strtoupper($data['shipping_first_name']);
  }

  if($data['shipping_last_name']){
    $data['shipping_last_name'] = strtoupper($data['shipping_last_name']);
  }

  if($data['shipping_address_1']){
    $data['shipping_address_1'] = strtoupper($data['shipping_address_1']);
  }

  if($data['shipping_address_2']){
    $data['shipping_address_2'] = strtoupper($data['shipping_address_2']);
  }

  if($data['shipping_city']){
    $data['shipping_city'] = strtoupper($data['shipping_city']);
  }

  if($data['billing_state']){
    $data['billing_state'] = strtoupper($data['billing_state']);
  }

  // Format Billing Information

  if($data['billing_first_name']){
    $data['billing_first_name'] = strtoupper($data['billing_first_name']);
  }

  if($data['billing_last_name']){
    $data['billing_last_name'] = strtoupper($data['billing_last_name']);
  }

  if($data['billing_address_1']){
    $data['billing_address_1'] = strtoupper($data['billing_address_1']);
  }

  if($data['billing_address_2']){
    $data['billing_address_2'] = strtoupper($data['billing_address_2']);
  }

  if($data['billing_city']){
    $data['billing_city'] = strtoupper($data['billing_city']);
  }

  if($data['billing_state']){
    $data['billing_state'] = strtoupper($data['billing_state']);
  }

  
  return $data;
}

// Rewrite certain customer data to standard formats during checkout and update from account page
//add_filter( 'woocommerce_process_checkout_field_billing_first_name', 'trim_and_uppercase', 10, 1 );
//add_filter( 'woocommerce_process_myaccount_field_billing_first_name', 'trim_and_uppercase', 10, 1 );
//add_filter( 'woocommerce_process_checkout_field_billing_last_name', 'trim_and_uppercase', 10, 1 );
//add_filter( 'woocommerce_process_myaccount_field_billing_last_name', 'trim_and_uppercase', 10, 1 );
//add_filter( 'woocommerce_process_checkout_field_billing_company', 'trim_and_uppercase', 10, 1 );
//add_filter( 'woocommerce_process_myaccount_field_billing_company', 'trim_and_uppercase', 10, 1 );
//  add_filter( 'woocommerce_process_checkout_field_billing_vat', 'format_tax', 10, 1 );
//  add_filter( 'woocommerce_process_myaccount_field_billing_vat', 'format_tax', 10, 1 );
//add_filter( 'woocommerce_process_checkout_field_billing_address_1', 'format_place', 10, 1 );
//add_filter( 'woocommerce_process_myaccount_field_billing_address_1', 'format_place', 10, 1 );
//add_filter( 'woocommerce_process_checkout_field_billing_postcode', 'format_zipcode', 10, 1 );
//add_filter( 'woocommerce_process_myaccount_field_billing_postcode', 'format_zipcode', 10, 1 );
//add_filter( 'woocommerce_process_checkout_field_billing_city', 'format_city', 10, 1 );
//add_filter( 'woocommerce_process_myaccount_field_billing_city', 'format_city', 10, 1 );
//  add_filter( 'woocommerce_process_checkout_field_billing_phone', 'format_phone_number', 10, 1 );
//  add_filter( 'woocommerce_process_myaccount_field_billing_phone', 'format_phone_number', 10, 1 );
//add_filter( 'woocommerce_process_checkout_field_billing_email', 'format_mail', 10, 1 );
//add_filter( 'woocommerce_process_myaccount_field_billing_email', 'format_mail', 10, 1 );
//add_filter( 'woocommerce_process_checkout_field_billing_birthday', 'format_date', 10, 1 );
//add_filter( 'woocommerce_process_checkout_field_shipping_first_name', 'trim_and_uppercase', 10, 1 );
//add_filter( 'woocommerce_process_myaccount_field_shipping_first_name', 'trim_and_uppercase', 10, 1 );
//add_filter( 'woocommerce_process_checkout_field_shipping_last_name', 'trim_and_uppercase', 10, 1 );
//add_filter( 'woocommerce_process_myaccount_field_shipping_last_name', 'trim_and_uppercase', 10, 1 );
//add_filter( 'woocommerce_process_checkout_field_shipping_address_1', 'format_place', 10, 1 );
//add_filter( 'woocommerce_process_myaccount_field_shipping_address_1', 'format_place', 10, 1 );
//add_filter( 'woocommerce_process_checkout_field_shipping_postcode', 'format_zipcode', 10, 1 );
//add_filter( 'woocommerce_process_myaccount_field_shipping_postcode', 'format_zipcode', 10, 1 );
//add_filter( 'woocommerce_process_checkout_field_shipping_city', 'format_city', 10, 1 );
//add_filter( 'woocommerce_process_myaccount_field_shipping_city', 'format_city', 10, 1 );

function trim_and_uppercase( $value ) {
add_custom_tracer("fa_trim_and_uppercase");
  add_custom_tracer("fa_trim_and_uppercase");
  return str_replace( 'Oww ', 'OWW ', implode( '.', array_map( 'ucwords', explode( '.', implode( '(', array_map( 'ucwords', explode( '(', implode( '-', array_map( 'ucwords', explode( '-', mb_strtolower( trim($value) ) ) ) ) ) ) ) ) ) ) );
}

function format_place( $value ) {
  add_custom_tracer("format_place");
  return trim_and_uppercase( $value );
}

function format_zipcode( $value ) {
  add_custom_tracer("format_zipcode");
  return trim( $value );
}

function format_city( $value ) {
  add_custom_tracer("format_city");
  return trim_and_uppercase( $value );
}

function format_mail( $value ) {
  add_custom_tracer("format_mail");
  return mb_strtolower( trim($value) );
}

function format_headquarter( $value ) {
  add_custom_tracer("format_headquater");
  return trim_and_uppercase( $value );
}
