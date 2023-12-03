<?php 
class Superaddons_Contactform7_Cost_Calculator_Frontend{ 
    function __construct(){
        add_action("wp_enqueue_scripts",array($this,"add_lib"),1000);
    }
    function add_lib(){
        wp_enqueue_style("cf7_calculator",CT_7_COST_PLUGIN_URL."frontend/js/style.css",array());
        wp_enqueue_script("autoNumeric",CT_7_COST_PLUGIN_URL."frontend/js/autoNumeric-1.9.45.js",array("jquery"),"1.9.45");
        wp_enqueue_script("formula_evaluator",CT_7_COST_PLUGIN_URL."frontend/js/formula_evaluator-min.js",array("jquery"));
        wp_enqueue_script("cf7_calculator",CT_7_COST_PLUGIN_URL."frontend/js/cf7_calculator.js",array("jquery","autoNumeric","formula_evaluator"));
        wp_localize_script( "cf7_calculator", "cf7_calculator", array("pro"=>get_option( '_redmuber_item_20085516')) );
    }
}
new Superaddons_Contactform7_Cost_Calculator_Frontend;