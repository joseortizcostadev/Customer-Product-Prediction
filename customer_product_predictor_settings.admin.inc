<?php
/**
 * File: customer_product_predictor_settings.admin.inc
 * This file contains call-back functions that fire when certains end-points from this module are accessed
 * @package    Customer Product Predictor
 * @author     Jose Ortiz <jose@thinkbinario.com>
 */

//----------------------------------------------------------------------------------------------------------------------
//                                    hook_form with page call-backs
//----------------------------------------------------------------------------------------------------------------------

/**
 * Page call-back that renders a form containing the links to all the CPP settings and features
 * @param $form
 * @param $form_state
 * @return mixed
 */
function customer_product_predictor_settings_form($form, &$form_state)
{
    $form['cpp_configuration_links'] = array(
        '#type' => 'fieldset',
        '#title' => t('CPP Configuration Pages and Modules'),
        '#weight' => 2,
    );
    $form['cpp_configuration_links']['cpp_ubercart_settings'] = array(
        '#title' => t("CPP Ubercart"),
        '#type' => 'link',
        '#href' => 'admin/config/customer-product-predictor/cpp-ubercart',
        '#prefix' => '<div class="cpp_ubercart">',
        '#suffix' => '</div>',
    );

    return $form;
}

/**
 * Page call-back that renders a forms which handles the CPP general settings
 * @param $form
 * @param $form_state
 * @return mixed
 */
function customer_product_predictor_general_settings_form($form, &$form_state)
{
    return $form;
}

/**
 * Implements hook_form_submit
 * @param $form
 * @param $form_state
 */
function customer_product_predictor_general_settings_form_submit($form, $form_state)
{

}











