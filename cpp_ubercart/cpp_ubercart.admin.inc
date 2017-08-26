<?php
/**
 * This module provides all the CPP Ubercart core functions
 *
 * This file implements the end-points call-back methods that were set in .module file.

 * The development and maintenance of this module is made by ThinkBinario: thinkbinario.com
 * @package    Customer Product Predictor
 * @author     Jose Ortiz <jose@thinkbinario.com>
 */
function cpp_ubercart_form($form,&$form_state)
{
    return $form;
}

/**
 * Call back method executed when the admin access to admin/config/customer-product-predictor/cpp-ubercart/settings
 * Contains cpp_uc general settings and configurations.
 * @param $form
 * @param $form_state
 * @return mixed
 */
function cpp_ubercart_settings_form($form, $form_state)
{

    $form['cpp_uc_general'] = array(
        '#type' => 'fieldset',
        '#title' => t('CPP General Settings'),
        '#weight' => 0,
    );
    $form['cpp_uc_general']['train'] = array(
        '#title' => t('Train CPP model with my data (highly recommended)'),
        '#type' => 'checkbox',
        '#description' => "",
        '#default_value' => variable_get('train', TRUE)
    );
    $form['cpp_uc_general']['email_activation'] = array(
        '#title' => t('Activate Email in Custom Hooks'),
        '#type' => 'checkbox',
        '#description' => "",
        '#default_value' => variable_get('email_activation', FALSE),
    );
    $form['cpp_uc_general']['owner_email'] = array(
        '#type' => 'textfield',
        '#title' => t('Site Owner or Administrator Email'),
        '#description' => "",
        '#size' => 100,
        '#maxlength' => 255,
        '#default_value' => variable_get('owner_email',""),
        '#attributes' => array('placeholder' => t('Example myemail@mail.com')),
        '#disabled' => (variable_get('email_activation') ? FALSE : TRUE)

    );
    $form['cpp_uc_general']['completed_orders'] = array(
        '#title' => t('Show completed orders, if any, in CPP Ubercart Reviews table'),
        '#type' => 'checkbox',
        '#description' => "",
        '#default_value' => variable_get('completed_orders', TRUE)
    );
    $form['cpp_uc_general']['pending_orders'] = array(
        '#title' => t('Show pending orders, if any, in CPP Ubercart Reviews table'),
        '#type' => 'checkbox',
        '#description' => "",
        '#default_value' => variable_get('pending_orders', TRUE)
    );
    $form['submit'] = array(
        '#type' => 'submit',
        '#value' => t('Save')
    );
    return $form;
}

/**
 * Call-back method executed when the admin access to admin/config/customer-product-predictor/cpp-ubercart/reviews
 * Creates a drupal administration table with information about the new order comment evaluation.
 * Note that this table only shows order information for new orders purchased or pending by the time this module was
 * installed, old orders are not shown on the table since their order comments evaluation are used to train the data
 * and may or may not be as accurate as the evaluation predicted on the new orders.
 * @param $form
 * @param $form_state
 * @return mixed
 */
function cpp_ubercart_reviews_form($form, &$form_state)
{
    $parameters = drupal_get_query_parameters();
    $order_by = (isset($parameters['order_by']) ? $parameters['order_by'] : 'comment_id'); // parameters to order by
    // headers
    $header = array(
        'order_id' => t('Order #'),
        'comment_id' => l(t('Comment #'),'/admin/config/customer-product-predictor/cpp-ubercart/reviews', array('query' => array('order_by' => 'comment_id'))),
        'review' => t('Customer Comment'),
        'user' => t('User'),
        'email' => t('email'),
        'roles' => t('Roles'),
        'order_status' => t('Order Status'),
        'prediction' => l(t('Automatic Evaluation'),'/admin/config/customer-product-predictor/cpp-ubercart/reviews', array('query' => array('order_by' => 'prediction'))),
    );
    $predictions = cpp_ubercart_get_product_review_predictions_with_header($header, $order_by);
    // rows
    $rows = array();
    $counter = 0;
    while ($prediction  = $predictions->fetchAssoc()) {
        if (intval(trim($prediction['is_in_training'])) == 0 && intval(trim($prediction['comment_id'])) != 0) {
            $order = cpp_ubercart_get_order_information($prediction['comment_id']);
            $user = user_load($order['uid']);
            $rows[$prediction['prid']] = array(
                'order_id' => $order['order_id'],
                'comment_id' => $prediction['comment_id'],
                'review' => $order['review'],
                'user' => ($order['uid'] ? $user->name : $order['primary_name']),
                'email' => ($order['uid'] ? l($user->mail, $user->mail, array('absolute' => TRUE)) : l($order['primary_email'], $order['primary_email'], array('absolute' => TRUE))),
                'roles' => ($order['uid'] ? implode(', ', $user->roles) : 'Anonymous'),
                'order_status' => $order['order_status'],
                'prediction' => $prediction['prediction'],
            );
            $counter++;
        }
    }
    // creates table
    $form['table'] = array(
        '#type' => 'tableselect',
        '#header' => $header,
        '#options' => $rows,
        '#empty' => t('There are no reviews predictions'),
    );
    $form['pager'] = array('#markup' => theme('pager')); // create pager theme

    return $form;
}

/**
 * Implements hook_form_validate
 * Validates if the owner or administrator email entered on the administration has the correct email format.
 * @param $form
 * @param $form_state
 */
function cpp_ubercart_settings_form_validate($form, &$form_state) {
    // Validates email.
    if (!preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*+[a-z]{2}/is', $form_state['values']['owner_email']) &&
        $form_state['values']['email_activation'] && // check if the email api is active
        !$form['cpp_uc_general']['owner_email']['#disabled']) // make sure that the email texfield is not disabled.
        form_set_error("owner_email","Please enter a valid email address.");
}

/**
 * Implements hook_form_submit for cpp_ubercart settings administration page.
 * @param $form
 * @param $form_state
 */
function cpp_ubercart_settings_form_submit($form, &$form_state)
{
    $message = "Configuration Successfully Saved";
    variable_set('train', $form_state['values']['train']);
    variable_set('email_activation', $form_state['values']['email_activation']);
    variable_set('owner_email', $form_state['values']['owner_email']);
    variable_set('completed_orders', $form_state['values']['completed_orders']);
    variable_set('pending_orders', $form_state['values']['pending_orders']);
    drupal_set_message($message);
}







