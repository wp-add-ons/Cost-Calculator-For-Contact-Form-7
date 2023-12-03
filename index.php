<?php
/**
* Plugin Name: Contact Form 7 Cost Calculator - Price Calculation
* Plugin URI: https://add-ons.org/plugin/contact-form-7-cost-calculator/
* Description: Create forms with field values calculated based in other form field values for contact form 7
* Author: add-ons.org
* Version: 7.4.3
* Author URI: https://add-ons.org/
*/
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}
define( 'CT_7_COST_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CT_7_COST_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
include_once(ABSPATH.'wp-admin/includes/plugin.php');
/*
* Include lib
*/
if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' )  ) {
    include CT_7_COST_PLUGIN_PATH."backend/index.php";
    include CT_7_COST_PLUGIN_PATH."backend/checkbox.php";
    include CT_7_COST_PLUGIN_PATH."backend/select.php";
    include CT_7_COST_PLUGIN_PATH."backend/number_format.php";
    include CT_7_COST_PLUGIN_PATH."frontend/index.php";
    include CT_7_COST_PLUGIN_PATH."superaddons/check_purchase_code.php";
    new Superaddons_Check_Purchase_Code( 
        array(
            "plugin" => "cf7-cost-calculator-price-calculation/index.php",
            "id"=>"20085516",
            "pro"=>"https://add-ons.org/plugin/contact-form-7-cost-calculator/",
            "plugin_name"=> "Contact Form 7 Cost Calculator",
            "document"=>"https://add-ons.org/document-contact-form-7-cost-calculator/"
        )
    );
}
if(!class_exists('Superaddons_List_Addons')) {  
    include CT_7_COST_PLUGIN_PATH."add-ons.php"; 
}