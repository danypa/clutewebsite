<?php
/**
 *
 * Send Email Notification after Setting Order.
 *
 * @package Elex Request a Quote
 */

add_action( 'woocommerce_order_status_quote-requested', 'elex_raq_send_notification_email' );
/** Send adminstrator and customer email and chat notifications when quote is requested.
 *
 * @param var $order_id order id.
 */
function elex_raq_send_notification_email( $order_id ) {
	$all_settings = get_option( 'elex_request_a_quote_settings' );
	$order        = wc_get_order( $order_id );

	$recepients       = ! empty( $all_settings['raq_notification_email'] ) ? $all_settings['raq_notification_email'] : get_option( 'admin_email' );
	$recepients_array = explode( ',', $recepients );
	$subject          = __( 'Quote Request', 'ELEX_RAQ_PLUGIN_DOMAIN' );
	$heading          = __( 'You have received a new product quote from the customer', 'ELEX_RAQ_PLUGIN_DOMAIN' );

	$template_content = '<h3>Customer Details</h3>
	<p><strong>' . __( 'Username:', 'ELEX_RAQ_PLUGIN_DOMAIN' ) . '</strong>&nbsp;@billing_first_name @billing_last_name</p>
	<p><strong>' . __( 'Email:', 'ELEX_RAQ_PLUGIN_DOMAIN' ) . '</strong>&nbsp;@billing_email</p>
	<p><strong>' . __( 'Phone:', 'ELEX_RAQ_PLUGIN_DOMAIN' ) . '</strong>&nbsp;@billing_phone</p>
	<p><strong>' . __( 'Comments:', 'ELEX_RAQ_PLUGIN_DOMAIN' ) . '</strong>&nbsp;@customer_note</p>
	<h3>' . __( 'The customer has requested a quote for the following products:', 'ELEX_RAQ_PLUGIN_DOMAIN' ) . '</h3>'
	. '@order_items';

	$message = elex_dynamic_email_sms_template_generator( $order_id, 'quote_requested', $template_content, 'email' );

	// Administrator Email Notification.
	elex_custom_mailer( $recepients_array, $subject, $heading, $message );

	// Customer Email Notification.
	if ( ! empty( $all_settings['raq_customer_notification_order_statuses'] ) && in_array( 'quote_requested', $all_settings['raq_customer_notification_order_statuses'], true ) ) {
		elex_send_customer_notification_email( $order_id, 'quote_requested' );
	}
}

add_action( 'woocommerce_order_status_quote-approved', 'elex_raq_send_approved_notification_email' );
/** Gets called when quote is approved.
 *
 * @param var $order_id order id.
 */
function elex_raq_send_approved_notification_email( $order_id ) {
	$all_settings = get_option( 'elex_request_a_quote_settings' );
	if ( in_array( 'quote_approved', $all_settings['raq_customer_notification_order_statuses'], true ) ) {
		elex_send_customer_notification_email( $order_id, 'quote_approved' );
	}
}

add_action( 'woocommerce_order_status_quote-rejected', 'elex_raq_send_rejected_notification_email' );
/** Gets called when quote is rejected.
 *
 * @param var $order_id order id.
 */
function elex_raq_send_rejected_notification_email( $order_id ) {
	$all_settings = get_option( 'elex_request_a_quote_settings' );
	if ( in_array( 'quote_rejected', $all_settings['raq_customer_notification_order_statuses'], true ) ) {
		elex_send_customer_notification_email( $order_id, 'quote_rejected' );
	}
}

/** Send notification email to customer.
 *
 * @param var $order_id order id.
 * @param var $order_status order status.
 */
function elex_send_customer_notification_email( $order_id, $order_status ) {
	$order        = new WC_Order( $order_id );
	$all_settings = get_option( 'elex_request_a_quote_settings' );

	// Email Subject and Heading.
	$subject = '';
	$heading = '';
	if ( 'quote_requested' === $order_status ) {
		$subject = __( 'Quote Received', 'ELEX_RAQ_PLUGIN_DOMAIN' );
		$heading = __( 'Your quote request has been received', 'ELEX_RAQ_PLUGIN_DOMAIN' );
	} elseif ( 'quote_approved' === $order_status ) {
		$subject = __( 'Quote Approved', 'ELEX_RAQ_PLUGIN_DOMAIN' );
		$heading = __( 'Your quote request has been approved', 'ELEX_RAQ_PLUGIN_DOMAIN' );
	} elseif ( 'quote_rejected' === $order_status ) {
		$subject = __( 'Quote Rejected', 'ELEX_RAQ_PLUGIN_DOMAIN' );
		$heading = __( 'Your quote request has been rejected', 'ELEX_RAQ_PLUGIN_DOMAIN' );
	}

	// Email Content.
	$template_content = '';
	$message          = '';
	if ( 'quote_requested' === $order_status ) {
		$template_content = '<p>Hi @billing_first_name @billing_last_name,</p>
		<p>Your quote request is as follows:</p>
		@order_items';
		$message          = elex_dynamic_email_sms_template_generator( $order_id, 'quote_requested', $template_content, 'email' );
	} elseif ( 'quote_approved' === $order_status ) {
		$template_content = '<p>Hi @billing_first_name @billing_last_name,</p>
		<p>Your quote request has been approved, To pay for this order please use the following link:</p>
		@order_items
		<p><center>@payment_link</center></p>';
		$message          = elex_dynamic_email_sms_template_generator( $order_id, 'quote_approved', $template_content, 'email' );
	} elseif ( 'quote_rejected' === $order_status ) {
		$template_content = '<p>Hi @billing_first_name @billing_last_name,</p>
		<p>Your quote request for the following order has been rejected.</p>
		@order_items';
		$message          = elex_dynamic_email_sms_template_generator( $order_id, 'quote_rejected', $template_content, 'email' );
	}

	if ( ! empty( $order->get_billing_email() ) ) {
		$recepients       = $order->get_billing_email();
		$recepients_array = explode( ',', $recepients );
		elex_custom_mailer( $recepients_array, $subject, $heading, $message );
	}
}

/** Generate order table.
 *
 * @param var $order_id order id.
 */
function elex_raq_generate_order_table( $order_id ) {
	$order  = new WC_Order( $order_id );
	$date   = gmdate( 'M d, Y' );
	$table  = '';
	$table .= "<h2 style='color:#557da1;display:block;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:18px;font-weight:bold;line-height:130%;margin:16px 0 8px;text-align:left'>
				Order # $order_id ( $date )
			</h2>";
	if ( count( $order->get_items() ) > 0 ) {
		$table .= "<table cellspacing='0' cellpadding='6' style='width:100%;border:1px solid #eee' border='1'>
						<thead>
							<tr>
								<th scope='col' style='text-align:left;border:1px solid #eee;padding:12px'>Product</th>
								<th scope='col' style='text-align:left;border:1px solid #eee;padding:12px'>Quantity</th>
								<th scope='col' style='text-align:left;border:1px solid #eee;padding:12px'>Price</th>
							</tr>
						</thead>
						<tbody>";
		$table .= wc_get_email_order_items( $order );
		$table .= '</tbody><tfoot>';
		$totals = $order->get_order_item_totals();
		if ( $totals ) {
			$i = 0;
			foreach ( $totals as $total ) {
				$i++;
				$label  = $total['label'];
				$value  = $total['value'];
				$table .= "<tr>
							<th scope='row' colspan='2' style='text-align:left; border: 1px solid #eee;'>$label</th>
							<td style='text-align:left; border: 1px solid #eee;'>$value</td>
						</tr>";
			}
		}
		$table .= '</tfoot>
				</table>';
	}
	return $table;
}

/** Custom order note.
 *
 * @param var $order_id order id.
 */
function elex_get_order_notes( $order_id ) {
	remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ) );
	$comments = get_comments(
		array(
			'post_id' => $order_id,
			'orderby' => 'comment_ID',
			'order'   => 'DESC',
			'approve' => 'approve',
			'type'    => 'order_note',
		)
	);
	$notes    = wp_list_pluck( $comments, 'comment_content' );
	add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ) );
	return end( $notes );
}

/** Custom mailer.
 *
 * @param var $recepients recepients.
 * @param var $subject subject.
 * @param var $heading heading.
 * @param var $message message.
 */
function elex_custom_mailer( $recepients, $subject, $heading, $message ) {

	$html_email_headers = array( 'Content-Type: text/html; charset=UTF-8' );
	$all_settings = get_option( 'elex_request_a_quote_settings' );
	$mailer       = WC()->mailer();
	$wc_email     = new WC_Email();
	$content      = $wc_email->style_inline( $mailer->wrap_message( $heading, $message ) );
	$mailer->send( $recepients, $subject, $content, $html_email_headers );
	if ( ! empty( $all_settings['raq_notification_debug_log'] ) && 'yes' === $all_settings['raq_notification_debug_log'] ) {
		// Save data in woocommerce logs.
		$log      = wc_get_logger();
		$head     = "<------------------- Request a Quote Email Log ------------------->\n";
		$body     = array(
			'recepients' => $recepients,
			'subject'    => $subject,
			'heading'    => $heading,
			'message'    => $message,
		);
		$log_text = $head . print_r( (object) $body, true );
		$context  = array( 'source' => 'elex_request_a_quote_email_log' );
		$log->log( 'debug', $log_text, $context );
	}
}

/** Email SMS Template generator.
 *
 * @param var $order_id order id.
 * @param var $order_status order status.
 * @param var $template_content template content.
 * @param var $content_type content type.
 */
function elex_dynamic_email_sms_template_generator( $order_id, $order_status, $template_content, $content_type ) {
	$all_settings  = get_option( 'elex_request_a_quote_settings' );
	$order         = wc_get_order( $order_id );
	$customer_note = ! empty( elex_get_order_notes( $order_id ) ) ? elex_get_order_notes( $order_id ) : '';

	$billing_first_name = ! empty( $order->get_billing_first_name() ) ? $order->get_billing_first_name() : '';
	$billing_last_name  = ! empty( $order->get_billing_last_name() ) ? $order->get_billing_last_name() : '';
	$billing_phone      = ! empty( $order->get_billing_phone() ) ? $order->get_billing_phone() : '';
	$billing_email      = ! empty( $order->get_billing_email() ) ? $order->get_billing_email() : '';
	if ( 'email' === $content_type ) {
		$order_items = elex_raq_generate_order_table( $order_id );
	}

	$template_literals = array(
		'@billing_first_name',
		'@billing_last_name',
		'@billing_phone',
		'@billing_email',
		'@order_items',
		'@customer_note',
	);
	$template_values   = array(
		$billing_first_name,
		$billing_last_name,
		$billing_phone,
		$billing_email,
		$order_items,
		$customer_note,
	);

	if ( 'quote_approved' === $order_status ) {
		$payment_link = "<a href={$order->get_checkout_payment_url()}>" . __( 'PAY NOW', 'ELEX_RAQ_PLUGIN_DOMAIN' ) . '</a>';
		array_push( $template_literals, '@payment_link' );
		array_push( $template_values, $payment_link );
	}

	$message = str_replace( $template_literals, $template_values, $template_content );

	return $message;
}
