<?php

/**
 * @package MPesa STK For WooCommerce
 * @subpackage Menus
 * @author Nineafrica <erick@mwamodo.com>
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
            <p>This plugin is the work of <a href="https://nineafrica.com">nineafrica </a> developers to provide a simple
                plug-n-play implementation for integrating Mpesa payments into online stores built with WooCommerce and
                WordPress.</p>
        </article>

        <h3>Integration(Going Live)</h3>
        <article>
            <p>
                While we have made all efforts to ensure this plugin works out of the box - with minimum configuration required - the service provider requires that the user go through a certain process to migrate from sandbox(test) environment to production.
            </p>
        </article>

        <h3>Contact</h3>
        <h4>Get in touch with us <a href="https://nineafrica.com/">Nineafrica</a> either via email <a
                    href="mail-to:erick@mwamodo.com">erick@mwamodo.com</a></h4>
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
