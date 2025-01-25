<?php

/**
 * Plugin Name: upload fish
 * Description: upload fish dar safhe pardakht
 * Plugin URI: https://drpori.ir
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
			<img src="https://alonaatti.com/wp-content/uploads/2023/04/logo.png" class="img-cart" >			
			<input type="file" id="pori_alonaati" name="pori_alonaati" />
			<input type="hidden" name="pori_alonaati_field" />
			<label for="pori_alonaati" class="pori-alonaati"><a>لطفا فیش واریزی خود را اپلود کنید</a></label>
			<div id="pori_list_upload"></div>
		</div><br><br>
        <div class="clipboard-shomare-cart">
			<input type="text" value="603712342341234" id="PoriInput" readonly>
			<button onclick="myFunction(event)">کپی شماره </button>
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

add_action('woocommerce_checkout_process', 'customised_checkout_field_process');

function customised_checkout_field_process()
{
	if (!$_POST['pori_alonaati_field'])
		wc_add_notice(__('<strong>Billing Image</strong> is a required field.'), 'error');
}

add_action('wp_ajax_porialonaatiupload', 'pori_alonaati_upload');

add_action('wp_ajax_nopriv_porialonaatiupload', 'pori_alonaati_upload');

add_action('woocommerce_checkout_update_order_meta', 'pori_naatisave_what_we_added');

function pori_naatisave_what_we_added($order_id)
{
	if (!empty(intval($_POST['pori_alonaati_field']))) {
		update_post_meta($order_id, 'pori_alonaati_field', sanitize_text_field($_POST['pori_alonaati_field']));
	}
}

add_action('woocommerce_admin_order_data_after_order_details', 'pori_naatiorder_meta_general');

function pori_naatiorder_meta_general($order)
{
	$file = get_post_meta($order->get_id(), 'pori_alonaati_field', true);
	if ($file) {
		$file_url = wp_get_attachment_url($file);

		echo '<p class="form-field form-field-wide wc-customer-user">' . wp_get_attachment_image($file, 'medium') . '</p>';

		echo '<p><a href="' . esc_url($file_url) . '" class="button" download>دانلود فیش</a></p>';
	}
}
add_filter('woocommerce_payment_complete_order_status', 'set_order_status_to_on_hold', 10, 2);
function set_order_status_to_on_hold($status, $order_id) {
    return 'on-hold'; 
}
function pori_alonaati_upload()
{
	$upload_dir = wp_upload_dir();

	if (isset($_FILES['pori_alonaati'])) {
		$file_name = $_FILES['pori_alonaati']['name'];
		$file_temp = $_FILES['pori_alonaati']['tmp_name'];

		$filetype = wp_check_filetype($file_name);

		
		if (in_array($filetype['ext'], ['jpg', 'jpeg', 'png', 'gif', 'pdf'])) {
			$upload_dir = wp_upload_dir();
			$image_data = file_get_contents($file_temp);
			$filename = basename($file_name);
			$filename = time() . '.' . $filetype['ext'];

			if (wp_mkdir_p($upload_dir['path'])) {
				$file = $upload_dir['path'] . '/' . $filename;
			} else {
				$file = $upload_dir['basedir'] . '/' . $filename;
			}

			file_put_contents($file, $image_data);
			$wp_filetype = wp_check_filetype($filename, null);
			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => sanitize_file_name($filename),
				'post_content' => '',
				'post_status' => 'inherit'
			);

			$attach_id = wp_insert_attachment($attachment, $file);
			require_once (ABSPATH . 'wp-admin/includes/image.php');
			$attach_data = wp_generate_attachment_metadata($attach_id, $file);
			wp_update_attachment_metadata($attach_id, $attach_data);

			echo $attach_id;
		} else {
			echo 'فقط فیش با پسوند png , jpg , pdf مورد قبول می باشد.';
		}
	}
	die;
}
