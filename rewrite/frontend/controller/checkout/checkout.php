<?php

class ControllerTenfCheckoutCheckout extends ControllerCheckoutCheckout
{
    public function index()
    {
        // 1. shopping cart not empty
        // 2. have address
        // 3. default address
        // 4. has shipping ?
        // 5. payment method
        // 6. shipping method
        // 7. refresh total amount

        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $this->response->redirect($this->url->link('checkout/cart'));
        }

        // prev if not create address, if not need shipping, set default address
        // 1. get products and total, expect for shipping price
        // 2. if need shipping , select shipping method and get shipping quote
        // 3. select shipping method, refresh total amount
        // 4. select payment method
        // 5. create order and go to payment
        $products = $this->getCartProducts();
        $address  = $this->getAddress();
        $hasShipping = $this->hasShipping();


        $total = $this->getTotals();
        /*********************************************
         * payment method
         *********************************************/
        // Payment Methods
        $paymentMethods = $this->getPaymentMethods($address->getData(), $total['totals'][1]);
        /*********************************************
         * shipping method
         *********************************************/
        // Shipping Methods
        if ($hasShipping) {
            $shippingMethods = $this->getShippingMethods($address->getData());
        } else {
            $shippingMethods = [];
        }

        /*********************************************
        /* Validate minimum quantity requirements.
         *********************************************/
        // @TODO
//        foreach ($products as $product) {
//            $product_total = 0;
//
//            foreach ($products as $product_2) {
//                if ($product_2['product_id'] == $product['product_id']) {
//                    $product_total += $product_2['quantity'];
//                }
//            }
//
//            if ($product['minimum'] > $product_total) {
//                $this->response->redirect($this->url->link('checkout/cart'));
//            }
//        }

        $this->load->language('checkout/checkout');
        $this->document->addStyle('catalog/view/javascript/vue.js');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = array();

        $data = $this->initData();
        $data['payment_methods'] = $paymentMethods;
        $data['shipping_methods'] = $shippingMethods;
        $data['totals'] = $total;

        $this->response->setOutput($this->load->view('checkout/checkout', $data));
    }


    protected function getAddress()
    {
        // if address not set and hasShipping is true
        // set default address
        // country_id, zone_id
        $hasInfo = false;
        if ($this->customer->isLogged()) {
            if ($this->customer->getAddressId()) {
                // get address info
                $address = $this->load->getSingletonModel('customer/address')->load($this->customer->getAddressId());
                $hasInfo = true;
            }
        }

        if (!$hasInfo) {
            $address = new BaseObject([
                'country_id' => $this->config->get('config_country_id'),
                'zone_id' => $this->config->get('config_zone_id')
            ]);
        }

        return $address;
    }

    protected function getShippingMethods($address)
    {
        // Shipping Methods
        $method_data = array();
        if (isset($address)) {

            $this->load->model('setting/extension');

            $results = $this->model_setting_extension->getExtensions('shipping');

            foreach ($results as $result) {
                if ($this->config->get('shipping_' . $result['code'] . '_status')) {
                    $this->load->model('extension/shipping/' . $result['code']);

                    $quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($address);

                    if ($quote) {
                        $method_data[$result['code']] = array(
                            'title'      => $quote['title'],
                            'quote'      => $quote['quote'],
                            'sort_order' => $quote['sort_order'],
                            'error'      => $quote['error']
                        );
                    }
                }
            }

            $sort_order = array();

            foreach ($method_data as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $method_data);

            $this->session->data['shipping_methods'] = $method_data;
        }
        return $method_data;
/**
        if (empty($this->session->data['shipping_methods'])) {
            $data['error_warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['shipping_methods'])) {
            $data['shipping_methods'] = $this->session->data['shipping_methods'];
        } else {
            $data['shipping_methods'] = array();
        }

        if (isset($this->session->data['shipping_method']['code'])) {
            $data['code'] = $this->session->data['shipping_method']['code'];
        } else {
            $data['code'] = '';
        }

        if (isset($this->session->data['comment'])) {
            $data['comment'] = $this->session->data['comment'];
        } else {
            $data['comment'] = '';
        }

        return $data;
//*/
    }

    protected function getPaymentMethods($address, $total)
    {
        // Payment Methods
        $method_data = array();

        $this->load->model('setting/extension');

        $results = $this->model_setting_extension->getExtensions('payment');

        $recurring = $this->cart->hasRecurringProducts();

        foreach ($results as $result) {
            if ($this->config->get('payment_' . $result['code'] . '_status')) {
                $this->load->model('extension/payment/' . $result['code']);

                $method = $this->{'model_extension_payment_' . $result['code']}->getMethod($address, $total);

                if ($method) {
                    if ($recurring) {
                        if (property_exists($this->{'model_extension_payment_' . $result['code']}, 'recurringPayments') && $this->{'model_extension_payment_' . $result['code']}->recurringPayments()) {
                            $method_data[$result['code']] = $method;
                        }
                    } else {
                        $method_data[$result['code']] = $method;
                    }
                }
            }
        }

        $sort_order = array();

        foreach ($method_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $method_data);

        return $method_data;
/**
        $this->session->data['payment_methods'] = $method_data;

        if (empty($this->session->data['payment_methods'])) {
            $data['error_warning'] = sprintf($this->language->get('error_no_payment'), $this->url->link('information/contact'));
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['payment_methods'])) {
            $data['payment_methods'] = $this->session->data['payment_methods'];
        } else {
            $data['payment_methods'] = array();
        }

        if (isset($this->session->data['comment'])) {
            $data['comment'] = $this->session->data['comment'];
        } else {
            $data['comment'] = '';
        }

        if ($this->config->get('config_checkout_id')) {
            $this->load->model('catalog/information');

            $information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));

            if ($information_info) {
                $data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . $this->config->get('config_checkout_id'), true), $information_info['title'], $information_info['title']);
            } else {
                $data['text_agree'] = '';
            }
        } else {
            $data['text_agree'] = '';
        }

        return $data;
//*/
    }


    protected function getCartProducts()
    {
        $data = array();

        foreach ($this->cart->getProducts() as $product) {
            $option_data = array();

            foreach ($product['option'] as $option) {
                if ($option['type'] != 'file') {
                    $value = $option['value'];
                } else {
                    $upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

                    if ($upload_info) {
                        $value = $upload_info['name'];
                    } else {
                        $value = '';
                    }
                }

                $option_data[] = array(
                    'name'  => $option['name'],
                    'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
                );
            }

            $recurring = '';

            if ($product['recurring']) {
                $frequencies = array(
                    'day'        => $this->language->get('text_day'),
                    'week'       => $this->language->get('text_week'),
                    'semi_month' => $this->language->get('text_semi_month'),
                    'month'      => $this->language->get('text_month'),
                    'year'       => $this->language->get('text_year'),
                );

                if ($product['recurring']['trial']) {
                    $recurring = sprintf($this->language->get('text_trial_description'), $this->currency->format($this->tax->calculate($product['recurring']['trial_price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['trial_cycle'], $frequencies[$product['recurring']['trial_frequency']], $product['recurring']['trial_duration']) . ' ';
                }

                if ($product['recurring']['duration']) {
                    $recurring .= sprintf($this->language->get('text_payment_description'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
                } else {
                    $recurring .= sprintf($this->language->get('text_payment_cancel'), $this->currency->format($this->tax->calculate($product['recurring']['price'] * $product['quantity'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']), $product['recurring']['cycle'], $frequencies[$product['recurring']['frequency']], $product['recurring']['duration']);
                }
            }

            $data[] = array(
                'cart_id'    => $product['cart_id'],
                'product_id' => $product['product_id'],
                'name'       => $product['name'],
                'model'      => $product['model'],
                'option'     => $option_data,
                'recurring'  => $recurring,
                'quantity'   => $product['quantity'],
                'subtract'   => $product['subtract'],
                'price'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']),
                'total'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'], $this->session->data['currency']),
                'href'       => $this->url->link('product/product', 'product_id=' . $product['product_id'])
            );
        }
        return $data;
    }

    protected function getTotals()
    {
        $totals = array();
        $taxes = $this->cart->getTaxes();
        $total = 0;

        // Because __call can not keep var references so we put them into an array.
        $total_data = array(
            'totals' => &$totals,
            'taxes'  => &$taxes,
            'total'  => &$total
        );

        $this->load->model('setting/extension');

        $sort_order = array();

        $results = $this->model_setting_extension->getExtensions('total');

        foreach ($results as $key => $value) {
            $sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
        }

        array_multisort($sort_order, SORT_ASC, $results);

        foreach ($results as $result) {
            if ($this->config->get('total_' . $result['code'] . '_status')) {
                $this->load->model('extension/total/' . $result['code']);

                // We have to put the totals in an array so that they pass by reference.
                $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
            }
        }

        $sort_order = array();

        foreach ($totals as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $totals);

        $data = array();
        foreach ($totals as $total) {
            if (!$this->hasShipping()) {
                if ($total['code'] != 'total') {
                    continue;
                }
            }
            $data[] = array(
                'title' => $total['title'],
                'text'  => $this->currency->format($total['value'], $this->session->data['currency'])
            );
        }

        return $data;
    }

    protected function hasShipping()
    {
        return $this->cart->hasShipping();
    }


    protected function initData()
    {

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_cart'),
            'href' => $this->url->link('checkout/cart')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('checkout/checkout', '', true)
        );
/**
        $data['text_checkout_option'] = sprintf($this->language->get('text_checkout_option'), 1);
        $data['text_checkout_account'] = sprintf($this->language->get('text_checkout_account'), 2);
        $data['text_checkout_payment_address'] = sprintf($this->language->get('text_checkout_payment_address'), 2);
        $data['text_checkout_shipping_address'] = sprintf($this->language->get('text_checkout_shipping_address'), 3);
        $data['text_checkout_shipping_method'] = sprintf($this->language->get('text_checkout_shipping_method'), 4);

        if ($this->cart->hasShipping()) {
            $data['text_checkout_payment_method'] = sprintf($this->language->get('text_checkout_payment_method'), 5);
            $data['text_checkout_confirm'] = sprintf($this->language->get('text_checkout_confirm'), 6);
        } else {
            $data['text_checkout_payment_method'] = sprintf($this->language->get('text_checkout_payment_method'), 3);
            $data['text_checkout_confirm'] = sprintf($this->language->get('text_checkout_confirm'), 4);
        }

        if (isset($this->session->data['error'])) {
            $data['error_warning'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } else {
            $data['error_warning'] = '';
        }

        $data['logged'] = $this->customer->isLogged();

        if (isset($this->session->data['account'])) {
            $data['account'] = $this->session->data['account'];
        } else {
            $data['account'] = '';
        }
 */
        $data['shipping_required'] = $this->hasShipping();

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        return $data;
    }

    public function save()
    {
        $this->load->language('checkout/checkout');
        $json = array();


        /**************************************************
         * validate start
         **************************************************/
        // Validate if customer is logged in.
        if (!$this->customer->isLogged()) {
            $json['redirect'] = $this->url->link('checkout/checkout', '', true);
        }

        // Validate cart has products and has stock.
        if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $json['redirect'] = $this->url->link('checkout/cart');
        }

        // Validate minimum quantity requirements.
        $products = $this->cart->getProducts();

        foreach ($products as $product) {
            $product_total = 0;

            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $json['redirect'] = $this->url->link('checkout/cart');

                break;
            }
        }
        /**************************************************
         * validate end
         **************************************************/

        $this->hasShipping();

        $this->renderJson($json);
    }

    protected function renderJson($data)
    {
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($data));
    }
}