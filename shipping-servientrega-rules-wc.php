<?php
/**
 * Plugin Name: Shipping Servientrega Rules Woocommerce
 * Description: Shipping Servientrega Rules Woocommerce for Shipping Servientrega Woocommerce
 * Version: 1.0.0
 * Author: Saul Morales Pacheco
 * Author URI: https://saulmoralespa.com
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * WC tested up to: 3.5
 * WC requires at least: 2.6
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if(!defined('SHIPPING_SERVIENTREGA_RULES_WC_SRS_VERSION')){
    define('SHIPPING_SERVIENTREGA_RULES_WC_SRS_VERSION', '1.0.0');
}

add_action( 'plugins_loaded', 'shipping_servientrega_rules_wc_srs_init');

function shipping_servientrega_rules_wc_srs_init(){
    if ( ! shipping_servientrega_rules_wc_srs_requirements() )
        return;
    shipping_servientrega_rules_wc_srs()->run_servientrega_rules_wc();

}

function shipping_servientrega_rules_wc_srs_notices( $notice ) {
    ?>
    <div class="error notice">
        <p><?php echo esc_html( $notice ); ?></p>
    </div>
    <?php
}

function shipping_servientrega_rules_wc_srs_requirements(){
    if ( !function_exists('shipping_servientrega_wc_ss') ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            add_action(
                'admin_notices',
                function() {
                    shipping_servientrega_rules_wc_srs_notices( 'Shipping Servientrega Rules Woocommerce: Requiere que se encuentre instalado y activo el plugin: Shipping Servientrega Woocommerce' );
                }
            );
        }
        return false;
    }

    return true;
}

function shipping_servientrega_rules_wc_srs(){
    static $plugin;
    if (!isset($plugin)){
        require_once('includes/class-shipping-servientrega-rules-wc-plugin.php');
        $plugin = new Shipping_Servientrega_Rules_WC_Plugin(__FILE__, SHIPPING_SERVIENTREGA_WC_SS_VERSION);
    }
    return $plugin;
}