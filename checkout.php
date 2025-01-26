<?php

/**
 * Plugin Name: upload fish
 * Description: upload fish dar safhe pardakht
 * Plugin URI: https://visapick.com
 * Author: pori
 * Version: 1.1
 */

// * Don't access this file directly
defined('ABSPATH') or die();

add_filter('woocommerce_cart_needs_payment', '__return_false');

add_action('wp_enqueue_scripts', 'pori_naati_script');

function pori_naati_script()
{
	wp_register_style('pori_naaticss', plugins_url('css/style.css', __FILE__));
	wp_enqueue_style('pori_naaticss');
	wp_enqueue_script('pori_naatijs', plugins_url('js/script.js', __FILE__), array('jquery'));
}

add_action('woocommerce_after_checkout_billing_form', 'pori_alonaati_upload_field');

function pori_alonaati_upload_field()
{
	?>
		<div class="form-row form-row-wide pori-upload">
		   <div class="sh-cart">
	    	<label class="lotfan">لطفا مجموع پیش پرداخت را به شماره کارت زیر واریز کنید</label>
			<img src="https://alonaatti.com/wp-content/uploads/2025/01/Card-Resalat.png" class="img-cart" >			
           </div>
			<div class="clipboard-shomare-cart">
				<input type="text" value="5041721205748164" id="PoriInput" readonly>
				<button onclick="myFunction(event)">کپی شماره </button>
		    </div><br>
			<div class="upload-fish">
			<label class="label-up">لطفا فیش واریزی خود را آپلود کنید </label>
			<label for="pori_alonaati" class="pori-alonaati"><a>آپلود فیش</a></label><br>
				<input type="file" id="pori_alonaati" name="pori_alonaati" />
				<input type="hidden" name="pori_alonaati_field" />
				</div>
				<div id="pori_list_upload">
			</div>
		</div>
		
        
		<script>
			function myFunction(event) {
				
				event.preventDefault();
	  
				
				var copyText = document.getElementById("PoriInput");
	  
				
				copyText.select();
				copyText.setSelectionRange(0, 99999); 
	  
				
				navigator.clipboard.writeText(copyText.value);
	  
				
				alert("شماره کارت کپی شد: " + copyText.value);
			}
		</script>
	<?php
}

function pori_alonaati_upload()
{
	$custom_upload_dir = WP_CONTENT_DIR . '/uploads/upload-fish';

	if (!file_exists($custom_upload_dir)) {
		wp_mkdir_p($custom_upload_dir);
	}

	if (isset($_FILES['pori_alonaati'])) {
		$file_name = $_FILES['pori_alonaati']['name'];
		$file_temp = $_FILES['pori_alonaati']['tmp_name'];

		$filetype = wp_check_filetype($file_name);

		if (in_array($filetype['ext'], ['jpg', 'jpeg', 'png', 'pdf'])) {
			$filename = time() . '-' . sanitize_file_name($file_name);
			$destination = $custom_upload_dir . '/' . $filename;

			if (move_uploaded_file($file_temp, $destination)) {
				echo $filename;
			} else {
				echo 'مشکلی در انتقال فایل رخ داده است.';
			}
		} else {
			echo 'فقط فیش با پسوند png , jpg , pdf مورد قبول می باشد.';
		}
	}
	die;
}

add_action('wp_ajax_porialonaatiupload', 'pori_alonaati_upload');
add_action('wp_ajax_nopriv_porialonaatiupload', 'pori_alonaati_upload');

add_action('woocommerce_checkout_update_order_meta', 'pori_naatisave_what_we_added');

function pori_naatisave_what_we_added($order_id)
{
	if (!empty($_POST['pori_alonaati_field'])) {
		update_post_meta($order_id, 'pori_alonaati_field', sanitize_text_field($_POST['pori_alonaati_field']));
	}
}

add_action('woocommerce_admin_order_data_after_order_details', 'pori_naatiorder_meta_general');

function pori_naatiorder_meta_general($order)
{
	$file_name = get_post_meta($order->get_id(), 'pori_alonaati_field', true);
	$custom_upload_dir_url = content_url('/uploads/upload-fish');

	if ($file_name) {
		$file_url = $custom_upload_dir_url . '/' . $file_name;

		echo '<p class="form-field form-field-wide wc-customer-user">';
		echo '<img src="' . esc_url($file_url) . '" alt="فیش واریزی" style="max-width: 300px; max-height: 300px;">';
		echo '</p>';

		echo '<p><a href="' . esc_url($file_url) . '" class="button" download>دانلود فیش</a></p>';
	}
}

add_action('woocommerce_checkout_process', 'customised_checkout_field_process');

function customised_checkout_field_process()
{
	if (empty($_POST['pori_alonaati_field'])) {
		wc_add_notice(__('آپلود فیش الزامی است.', 'woocommerce'), 'error');
	}
}

add_filter('woocommerce_payment_complete_order_status', 'set_order_status_to_on_hold', 10, 2);

function set_order_status_to_on_hold($status, $order_id)
{
	return 'on-hold';
}
