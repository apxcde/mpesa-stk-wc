<?php

/**
 * @package MPesa STK For WooCommerce
 * @subpackage Menus
 * @author Mwamodo <erick@mwamodo.com>
 * @since 0.18.01
 */

add_action('init', 'mpesa_payments_post_type', 0);
add_action('manage_posts_custom_column', 'mpesa_payments_table_column_content', 10, 2);

add_filter('manage_mpesaipn_posts_columns', 'filter_mpesa_payments_table_columns');
add_filter('manage_edit-mpesaipn_sortable_columns', 'mpesa_payments_columns_sortable');

// Register Custom Post - MPesa Payments
function mpesa_payments_post_type()
{
    $labels = array(
        'name'                  => _x('MPesa Payments', 'MPesa Payment General Name', 'woocommerce'),
        'singular_name'         => _x('MPesa Payment', 'MPesa Payment Singular Name', 'woocommerce'),
        'menu_name'             => __('MPesa Payments', 'woocommerce'),
        'name_admin_bar'        => __('MPesa Payment', 'woocommerce'),
        'archives'              => __('Payment Archives', 'woocommerce'),
        'attributes'            => __('Payment Attributes', 'woocommerce'),
        'parent_item_colon'     => __('Parent Payment:', 'woocommerce'),
        'all_items'             => __('MPesa Payments', 'woocommerce'),
        'add_new_item'          => __('Add New Payment', 'woocommerce'),
        'add_new'               => __('Add Payment', 'woocommerce'),
        'new_item'              => __('New Payment', 'woocommerce'),
        'edit_item'             => __('Edit Payment', 'woocommerce'),
        'update_item'           => __('Update Payment', 'woocommerce'),
        'view_item'             => __('View Payment', 'woocommerce'),
        'view_items'            => __('View Payments', 'woocommerce'),
        'search_items'          => __('Search Payments', 'woocommerce'),
        'not_found'             => __('Not found', 'woocommerce'),
        'not_found_in_trash'    => __('Not found in Trash', 'woocommerce'),
        'items_list'            => __('Payments list', 'woocommerce'),
        'items_list_navigation' => __('Payments list navigation', 'woocommerce'),
        'filter_items_list'     => __('Filter payments list', 'woocommerce'),
    );

    $env = get_option('woocommerce_mpesa_settings') ? get_option('woocommerce_mpesa_settings')["env"] : 'sandbox';
    $supports = ($env == 'live') ?
        array('revisions') :
        array('revisions', 'editor');

    $args = array(
        'label'                 => __('MPesa Payment', 'woocommerce'),
        'description'           => __('MPesa Payment Description', 'woocommerce'),
        'labels'                => $labels,
        'supports'              => $supports,
        'taxonomies'            => array(),
        'hierarchical'          => false,
        'public'                => false,
        'show_ui'               => true,
        'show_in_menu'          => false,
        'show_in_admin_bar'     => false,
        'show_in_nav_menus'     => false,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
        'capability_type'       => 'page',
        'rewrite'               => false,
    );

    register_post_type('mpesaipn', $args);
}

/**
 * A filter to add custom columns and remove built-in
 * columns from the edit.php screen.
 *
 * @access public
 * @param Array $columns The existing columns
 * @return Array $filtered_columns The filtered columns
 */

function filter_mpesa_payments_table_columns($columns)
{
    $columns['title'] = "Type";
    $columns['customer'] = "Customer";
    $columns['amount'] = "Amount";
    $columns['paid'] = "Paid";
    $columns['request'] = "Request";
    $columns['receipt'] = "Receipt";
    $columns['balance'] = "Balance";
    $columns['status'] = "Status";
    unset($columns['date']);
    return $columns;
}

/**
 * Render custom column content within edit.php table on event post types.
 *
 * @access public
 * @param String $column The name of the column being acted upon
 * @return void
 */

function mpesa_payments_table_column_content($column_id, $post_id)
{
    $order_id = get_post_meta($post_id, '_order_id', true);
    switch ($column_id) {
        case 'customer':
            echo ($value = get_post_meta($post_id, '_customer', true)) ? $value : "N/A";
            break;

        case 'amount':
            echo ($value = get_post_meta($post_id, '_amount', true)) ? $value : "0";
            break;

        case 'paid':
            echo ($value = get_post_meta($post_id, '_paid', true)) ? $value : "0";
            break;

        case 'request':
            echo ($value = get_post_meta($post_id, '_request_id', true)) ? $value : "N/A";
            break;

        case 'receipt':
            echo ($value = get_post_meta($post_id, '_receipt', true)) ? $value : "N/A";
            break;

        case 'balance':
            echo ($value = get_post_meta($post_id, '_balance', true)) ? $value : "0";
            break;

        case 'status':
            $statuses = array(
                "processing" => "This Order Is Processing",
                "on-hold" => "This Order Is On Hold",
                "complete" => "This Order Is Complete",
                "cancelled" => "This Order Is Cancelled",
                "refunded" => "This Order Is Refunded",
                "failed" => "This Order Failed"
            );

            echo ($value = get_post_meta($post_id, '_order_status', true)) ? '<a href="'.admin_url('post.php?post='.esc_attr(trim($order_id)).'&action=edit">'.esc_attr($statuses[$value]).'</a>') : '<a href="'.admin_url('post.php?post='.esc_attr(trim($order_id)).'&action=edit"').'>Set Status</a>';
            break;
    }
}

/**
 * Make custom columns sortable.
 *
 * @access public
 * @param Array $columns The original columns
 * @return Array $columns The filtered columns
 */

function mpesa_payments_columns_sortable($columns)
{
    $columns['title'] = "Type";
    $columns['customer'] = "Customer";
    $columns['paid'] = "Paid";
    $columns['receipt'] = "Receipt";
    $columns['status'] = "Status";
    return $columns;
}
