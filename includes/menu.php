<?php

/**
 * @package MPesa STK For WooCommerce
 * @subpackage Menus
 * @author Nineafrica < packages@nineafrica.com >
 * @since 0.18.01
 */

add_action('admin_menu', 'mpesa_transactions_menu');

function mpesa_transactions_menu()
{
    // create custom top-level menu
    add_menu_page(
        'MPesa Payments',
        'MPesa',
        'manage_options',
        'mpesa',
        'mpesa_transactions_menu_transactions',
        'dashicons-money',
        58
    );

    add_submenu_page(
        'mpesa',
        'About this Plugin',
        'About',
        'manage_options',
        'mpesa_about',
        'mpesa_transactions_menu_about'
    );

    // add_submenu_page(
    //     'mpesa',
    //     'MPesa APIs Utility',
    //     'Utility',
    //     'manage_options',
    //     'mpesa_utility',
    //     'mpesa_transactions_menu_utility'
    // );

    // add_submenu_page(
    //     'mpesa',
    //     'MPesa Payments Analytics',
    //     'Analytics',
    //     'manage_options',
    //     'mpesa_analytics',
    //     'mpesa_transactions_menu_analytics'
    // );

    add_submenu_page(
        'mpesa',
        'MPesa Preferences',
        'Configure',
        'manage_options',
        'mpesa_preferences',
        'mpesa_transactions_menu_pref'
    );
}

function mpesa_transactions_menu_about()
{ ?>
    <div class="wrap">
        <h1>About MPesa STK for WooCommerce</h1>

        <h3>The Plugin</h3>
        <article>
            <p>This plugin builds on the work of <a href="https://github.com/moshthepitt/woocommerce-lipa-na-mpesa">Kelvin Jayanoris</a>, the <a href="https://nineafrica.com">Nineafrica </a> developers and others to provide a simple plug-n-play implementation for integrating MPesa Payments into online stores built with WooCommerce and WordPress.</p>
        </article>

        <h3>Integration(Going Live)</h3>
        <article>
            <p>
                While we have made all efforts to ensure this plugin works out of the box - with minimum configuration required - the service provider requires that the user go through a certain process to migrate from sandbox(test) environment to production. We have a team ready on call to assist you in this are, at a flat fee of 6500KES/$65 one-off.
            </p>
        </article>

        <h3>Development</h3>
        <article>
            <p>To help improve and support our effort to make such solutions as this one, you can start by contributing here:</p>
            <li><a href="https://github.com/pesakite/mpesa-stk-wc">This Plugin's Github Repo</a></li>
        </article>

        <h3>Contact</h3>
        <h4>Get in touch with us ( <a href="https://nineafrica.com/">Nineafrica</a> ) either via email ( <a href="mail-to:packages@nineafrica.com">packages@nineafrica.com</a> )</h4>
    </div><?php
}

function mpesa_transactions_menu_transactions()
{
    wp_redirect(admin_url('edit.php?post_type=mpesaipn'));
}

function mpesa_transactions_menu_pref()
{
    wp_redirect(admin_url('admin.php?page=wc-settings&tab=checkout&section=mpesa'));
}
