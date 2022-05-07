<?php
/**
 * @package MPesa STK For WooCommerce
 * @subpackage Metaboxes
 * @author Nineafrica <erick@mwamodo.com>
 * @since 0.18.01
 */


add_action('add_meta_boxes', 'mpesa_mb_sm');
add_action('save_post', 'mpesa_ipn_save_meta');

function mpesa_mb_sm()
{
    add_meta_box('mpesa-ipn-customer_details', 'Customer Details', 'customer_details', 'mpesaipn', 'normal', 'high');
    add_meta_box('mpesa-ipn-order_details', 'Order Details', 'order_details', 'mpesaipn', 'normal', 'high');
    add_meta_box('mpesa-ipn-payment_details', 'Payment Details', 'payment_details', 'mpesaipn', 'side', 'high');
    //add_meta_box( 'mpesa-ipn-payment_status', 'Payment Status', 'mpesa_status', 'mpesaipn', 'side', 'low' );
    add_meta_box('woocommerce-order-notes', 'Payment Order Notes', 'order_notes', 'mpesaipn', 'normal', 'default');

    add_meta_box('mpesa-ipn-payment_create', 'Paid For Via MPesa?', 'mpesa_payment', 'shop_order', 'side', 'low');
}

function mpesa_payment($post)
{
    echo '<table class="form-table" >
		<tr valign="top" >
			<td>
				You can manually register a payment via MPesa after saving this order.
			</td>
		</tr>
		<tr valign="top" >
			<td>
				<a href="'.admin_url('post-new.php?post_type=mpesaipn&order='.$post->ID.'').'" class="page-title-action">Add New MPesa Payment</a>
			</td>
		</tr>
	</table>';
}

function mpesa_status($post)
{
    echo '<table class="form-table" >
		<tr valign="top" >
			<td>
				Incase MPesa timed out.
			</td>
		</tr>
		<tr valign="top" >
			<td>
				<button type="submit" name="mpesaipn_status" class="page-title-action">Check Payment Status</a>
			</td>
		</tr>
	</table>';
}

function customer_details($post)
{
    $customer = get_post_meta($post->ID, '_customer', true);
    $phone = get_post_meta($post->ID, '_phone', true);
    if (isset($_GET['order'])) {
        $order = new WC_Order($_GET['order']);
        $total = wc_format_decimal($order->get_total(), 2);
        $phone = $order->get_billing_phone();
        $first_name = $order->get_billing_first_name();
        $last_name = $order->get_billing_last_name();
        $customer = "{$first_name} {$last_name}";
    }

    // Remove the plus sign before the customer's phone number
    if (substr($phone, 0, 1) == "+") {
        $phone = str_replace("+", "", $phone);
    }

    echo '<style> #add_order_note { width: 100%; } </style><table class="form-table" >
		<tr valign="top" >
			<th scope="row" >Customer Full Names</th>
			<td><input type="text" name="customer" value="'. esc_attr($customer) .' " / > </td>
		</tr>
		<tr valign="top" >
			<th scope="row">Customer Phone Number</th>
			<td><input type="text" name="phone" value="'. esc_attr($phone) .' " / >
			<input type="hidden" name="ipnmb">
			<input type="hidden" name="post_title", value="Manual">
			</td>
		</tr>
	</table>';
}

function order_details($post)
{
    $order = ($value = get_post_meta($post->ID, '_order_id', true)) ? $value : $post->ID;
    $order = isset($_GET['order']) ? $_GET['order'] : $order;
    $amount = get_post_meta($post->ID, '_amount', true);
    $paid = get_post_meta($post->ID, '_paid', true);
    $balance = get_post_meta($post->ID, '_balance', true);

    if (isset($_GET['order'])) {
        $order_details = new WC_Order($_GET['order']);
        $amount = wc_format_decimal($order_details->get_total(), 2);
        $phone = $order_details->get_billing_phone();
        $first_name = $order_details->get_billing_first_name();
        $last_name = $order_details->get_billing_last_name();
        $customer = "{$first_name} {$last_name}";
    }

    $new = wc_get_order($order) ? '' : ' <a href="'.admin_url('post-new.php?post_type=shop_order').'" class="page-title-action">Add New Manual Order</a>';

    echo '<table class="form-table" >
		<tr valign="top" >
			<th scope="row" >Order ID</th>
			<td>
				<input type="text" name="order_id" value="'. esc_attr($order) .' " / >'.$new.'
			</td>
		</tr>
		<tr valign="top" >
			<th scope="row">Order Amount</th>
			<td><input type="text" name="amount" value="'. esc_attr($amount) .' " / > </td>
		</tr>
		<tr valign="top" >
			<th scope="row">Amount Paid</th>
			<td><input type="text" name="paid" value="'. esc_attr($paid) .' " / > </td>
		</tr>
	</table>';
}

function order_notes($post)
{
    echo '<table class="form-table" >
		<tr valign="top" >
			<th scope="row" >Add Order Note</th>
			<td>
				<textarea id="add_order_note" name="order_note"></textarea>
			</td>
		</tr>
	</table>';
}

function payment_details($post)
{
    $status = ($value = get_post_meta($post->ID, '_order_status', true)) ? $value : 'complete';
    $request = get_post_meta($post->ID, '_request_id', true);
    $receipt = get_post_meta($post->ID, '_receipt', true);

    $statuses = array(
        "processing" => "This Order Is Processing",
        "on-hold" => "This Order Is On Hold",
        "complete" => "This Order Is Complete",
        "cancelled" => "This Order Is Cancelled",
        "refunded" => "This Order Is Refunded",
        "failed" => "This Order Failed"
    ); ?>
	<h4>Request ID: <?php echo $request; ?></h4>
	Add here the MPesa confirmation code received and set the appropriate order status.
	<?php echo '<p>MPesa Receipt Number <input type="text" name="receipt" value="'. esc_attr($receipt) .' " /></p>'; ?>
	<p>Set Order(Payment) Status
		<select name="status">
			<option class="postbox" value="<?php echo esc_attr($status); ?>"><?php echo esc_attr($statuses[$status]); ?></option>
			<?php  unset($statuses[$status]);
    foreach ($statuses as $ostatus => $label): ?>
				<option class="postbox" value="<?php echo esc_attr($ostatus); ?>"><?php echo esc_attr($label); ?></option>
			<?php endforeach; ?>
		</select>
		<input type="hidden" name="save_meta">
	</p><?php
}

function mpesa_ipn_save_meta($post_id)
{
    if (isset($_POST['save_meta'])) {
        $customer = trim($_POST['customer']);
        $phone = trim($_POST['phone']);
        $order_id = trim($_POST['order_id']);
        $order_status = trim($_POST['status']);
        $order_note = trim($_POST['order_note']);

        $amount = trim($_POST['amount']);
        $paid = trim($_POST['paid']);

        $receipt = trim($_POST['receipt']);

        update_post_meta($post_id, '_customer', strip_tags($customer));
        update_post_meta($post_id, '_phone', strip_tags($phone));
        update_post_meta($post_id, '_order_id', strip_tags($order_id));
        update_post_meta($post_id, '_amount', strip_tags($amount));
        update_post_meta($post_id, '_paid', strip_tags($paid));
        update_post_meta($post_id, '_balance', strip_tags($amount-$paid));
        update_post_meta($post_id, '_receipt', strip_tags($receipt));
        update_post_meta($post_id, '_order_status', strip_tags($order_status));

        if (wc_get_order($order_id !== false)) {
            $order = new WC_Order($order_id);
            $order->update_status(strip_tags($order_status));

            if ($order_note !== "") {
                $order->add_order_note(__(strip_tags($order_note)));
            }
        }
    }
}
