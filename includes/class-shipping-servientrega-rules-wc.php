<?php


class Shipping_Servientrega_Rules_WC extends WC_Shipping_Method_Shipping_Servientrega_WC
{

    public function __construct($instance_id = 0)
    {
        parent::__construct($instance_id);
        //add_filter('servientrega_shipping_tabs', array($this, 'servientrega_shipping_tabs_add'));
        //add_filter('servientrega_shipping_calculate_cost', array($this, 'servientrega_shipping_calculate_cost_filter'), 10, 4);
    }

    public static function servientrega_dimensions_weight_filter($data, $items, $guide)
    {
        $data['total_valorization'] = 0;
        $data['high'] = 0;
        $data['length'] = 0;
        $data['width'] = 0;
        $data['weight'] = 0;
        $data['name_products'] = [];
        $total_min_shipping = 6000;

        foreach ( $items as $item => $values ) {
            $_product_id = $guide ? $values['product_id'] : $values['data']->get_id();
            $_product = wc_get_product( $_product_id );

            $data['name_products'][] = $_product->get_name();
            $custom_price_product = get_post_meta($_product_id, '_shipping_custom_price_product_smp', true);
            $data['total_valorization'] += $custom_price_product ? wc_format_decimal($custom_price_product, 0) : wc_format_decimal($_product->get_price(), 0);

            $quantity = $values['quantity'];
            $data['total_valorization'] = $data['total_valorization'] * $quantity;

            if ( !$_product->get_weight() || !$_product->get_length()
                || !$_product->get_width() || !$_product->get_height() )
                break;

            $data['high'] += $quantity > 1 ? $_product->get_height() * $quantity : $_product->get_height();
            $data['length'] += (int)$_product->get_length();
            $data['width'] += (int)$_product->get_width();
            $weight_volume = $_product->get_height() * $_product->get_width() * $_product->get_length() * 222 / 1000000;
            $weight_volume = round($weight_volume);
            $data['weight'] += $quantity > 1 ? $weight_volume * $quantity : $weight_volume;

        }

       /* $instance = new self();

        $data['weight'] = ceil($data['weight']);
        if ($instance->servientrega_product_type === '2' && $data['weight'] < 3)
            $data['weight'] = 3;*/

        if($data['weight'] == 0)
            $data['weight'] = 1;

        $data['total_valorization'] = $data['total_valorization'] < $total_min_shipping ? $total_min_shipping : $data['total_valorization'];

        return $data;
    }

    public static function servientrega_shipping_calculate_cost_filter($journeyCost, $matrix_data, $data_products, $package)
    {

        $instance = new self();
        $rates = $instance->rates_servientrega;
        $weight = $rates['weight'];
        $journey = $matrix_data['tipo_trayecto'];
        $freight = $rates['freight'];

        $total_weight_products = $data_products['weight'];

        $data_weight_key = [];

        foreach ($weight as $key => $value){

            $data_weight_key = [$key];

            if(($value === $total_weight_products) || ($value > $total_weight_products)){
                break;
            }elseif ($total_weight_products > $value) {
                $data_weight_key = [$key, $value];
            }
        }

        $rate_key_weight = $data_weight_key[0];

        $journeyCost = $rates[$journey][$rate_key_weight];

        //additionals kilos
        if (count($data_weight_key) === 2){
            $weight = $weight[$rate_key_weight];

            $remaining = $total_weight_products - $weight;

            $additionalCost = $rates['additional'][$journey];

            $additionalCost = $additionalCost * $remaining;

            $journeyCost += $additionalCost;

        }

        $percentage = 1;

        if ($data_products['total_valorization'] > 35000){
            $journeyCost += $data_products['total_valorization'] * ($percentage/100);
        }else{
            $journeyCost += $freight;
        }

        return $journeyCost;
    }

    /*public function servientrega_shipping_page_tabs($current = 'general')
    {
        $activation_check = get_option('dhl_activation_status');
        if(!empty($activation_check) && $activation_check === 'active')
        {
            $acivated_tab_html =  "<small style='color:green;font-size:xx-small;'>(Activated)</small>";

        }
        else
        {
            $acivated_tab_html =  "<small style='color:red;font-size:xx-small;'>(Activate)</small>";
        }
        $tabs = array(
            'general' => __("General"),
            'rates' => __("Tiempo de entrega, Liquidación y trayectos"),
            'packing' => __("Red operativa terrestre"),
            'rules' => __('reglas de precios')
            //'license' => __("License ".$acivated_tab_html, 'wf-shipping-dhl')
        );
        $html = '<h2 class="nav-tab-wrapper">';
        foreach ($tabs as $tab => $name) {
            $class = ($tab == $current) ? 'nav-tab-active' : '';
            $style = ($tab == $current) ? 'border-bottom: 1px solid transparent !important;' : '';
            $html .= '<a style="text-decoration:none !important;' . $style . '" class="nav-tab ' . $class . '" href="?page=wc-settings&tab=shipping&section=shipping_servientrega_wc&subtab=' . $tab . '">' . $name . '</a>';
        }
        $html .= '</h2>';
        return $html;
    }

    public function servientrega_shipping_tabs_add($tabs)
    {
        $tabs = array_merge(['rules'], $tabs);
        return $tabs;
    }

    public function calculate_cost($matrix_data, $data_products, $package)
    {
        $journey = $matrix_data['tipo_trayecto'];
        $journeyCost = 6000;

        if ($journey === 'especial')
            $journeyCost = 16000;

        return $journeyCost;
    }*/
}