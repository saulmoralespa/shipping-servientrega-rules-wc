<?php


class Shipping_Servientrega_Rules_WC_Plugin
{
    /**
     * Filepath of main plugin file.
     *
     * @var string
     */
    public $file;
    /**
     * Plugin version.
     *
     * @var string
     */
    public $version;
    /**
     * Absolute plugin path.
     *
     * @var string
     */
    public $plugin_path;
    /**
     * Absolute plugin URL.
     *
     * @var string
     */
    public $plugin_url;
    /**
     * assets plugin.
     *
     * @var string
     */
    public $assets;
    /**
     * Absolute path to plugin includes dir.
     *
     * @var string
     */
    public $includes_path;
    /**
     * @var bool
     */
    private $_bootstrapped = false;

    public function __construct($file, $version)
    {
        $this->file = $file;
        $this->version = $version;

        $this->plugin_path   = trailingslashit( plugin_dir_path( $this->file ) );
        $this->plugin_url    = trailingslashit( plugin_dir_url( $this->file ) );
        $this->assets = $this->plugin_url . trailingslashit('assets');
        $this->includes_path = $this->plugin_path . trailingslashit( 'includes' );
    }

    public function run_servientrega_rules_wc()
    {
        try{
            if ($this->_bootstrapped){
                throw new Exception( 'Servientrega shipping rules can only be called once');
            }
            $this->_run();
            $this->_bootstrapped = true;
        }catch (Exception $e){
            if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
                add_action('admin_notices', function() use($e) {
                    shipping_servientrega_rules_wc_srs_notices($e->getMessage());
                });
            }
        }
    }

    protected function _run()
    {
        require_once ($this->includes_path . 'class-shipping-servientrega-rules-wc.php');
        add_filter( 'woocommerce_shipping_methods', array( $this, 'shipping_servientrega_rules_wc_add_method') );
        add_filter('servientrega_dimensions_weight', array('Shipping_Servientrega_Rules_WC', 'servientrega_dimensions_weight_filter'), 10, 3);
        add_filter( 'servientrega_shipping_calculate_cost', array('Shipping_Servientrega_Rules_WC', 'servientrega_shipping_calculate_cost_filter'), 10, 4);
    }

    public function shipping_servientrega_rules_wc_add_method( $methods )
    {
        $methods['shipping_servientrega_rules_wc'] = 'Shipping_Servientrega_Rules_WC';
        return $methods;
    }
}