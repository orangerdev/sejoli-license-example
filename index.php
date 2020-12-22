<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              Ridwan Arifandi
 * @since             1.0.0
 * @package           Sejoli_Donation
 *
 * @wordpress-plugin
 * Plugin Name:       Sejoli - Contoh Integrasi Lisensi
 * Plugin URI:        https://sejoli.co.id
 * Description:       Contoh integrasi lisensi dengan SEJOLI
 * Version:           1.0.0
 * Author:            Ridwan Arifandi
 * Author URI:        https://ridwan-arifandi.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sejoli-license
 * Domain Path:       /languages
 */

global $sejolicense;

$sejolicense = false;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Ganti http://localhost dengan url website anda
define('SEJOLI_REGISTER_LICENSE_URL', 'http://member.andidigitalonline.com/sejoli/sejoli-license'); 			// URL untuk pendaftaran lisensi
define('SEJOLI_VALIDATE_LICENSE_URL', 'http://member.andidigitalonline.com/sejoli/sejoli-validate-license');	// URL untuk pengecekan lisensi


// Class to check license
require_once 'license.php';

add_action('admin_init', 'sejolicense_check_form', 1);

/**
 * Check license form data
 * Hooked via action admin_init, priority 1
 * @since 	1.0.0
 * @return 	void
 */
function sejolicense_check_form() {

	global $sejolicense;

	$post_data = wp_parse_args($_POST, array(
					'sejoli-license-nonce' => NULL,
					'email'                => NULL,
					'password'             => NULL,
					'license'              => NULL,
					'string'               => NULL
				 ));

	if(!is_null($post_data['sejoli-license-nonce'])) :

		if(wp_verify_nonce($post_data['sejoli-license-nonce'], 'sejoli-register-license')) :

			$sejolicense = SEJOLICENSE::set_url(SEJOLI_REGISTER_LICENSE_URL)
							->set_email($post_data['email'])
							->set_password($post_data['password'])
							->set_license($post_data['license'])
							->set_string($post_data['string'])
							->register();

		elseif(wp_verify_nonce($post_data['sejoli-license-nonce'], 'sejoli-check-license')) :

			$sejolicense = SEJOLICENSE::set_url(SEJOLI_VALIDATE_LICENSE_URL)
							->set_license($post_data['license'])
							->set_string($post_data['string'])
							->check();

		endif;

	endif;
}

add_action('admin_notices', 'sejolicense_display_message');

function sejolicense_display_message() {

	global $sejolicense, $pagenow;

	if(false !== $sejolicense && array_key_exists('valid', $sejolicense)) :

		$class = (false !== $sejolicense['valid']) ? 'success' : 'error';

		?>
		<div class="notice notice-<?php echo $class; ?>">
	        <p><?php echo implode('<br />', $sejolicense['messages']); ?></p>
	    </div>
		<?php
	endif;

	if(
		'options-general.php' === $pagenow &&
		isset($_GET['page']) &&
		'sejoli-license' === $_GET['page']
	) :

		?>
		<div class="notice notice-warning">
			<h2>PERHATIAN</h2>
			<p>
				<?php _e('PASTIKAN anda sudah mengubah target link di file index.php line 38 dan 39', 'sejoli'); ?>.<br />
				define('SEJOLI_REGISTER_LICENSE_URL', 'http://localhost/sejoli/sejoli-license');<br />
				define('SEJOLI_VALIDATE_LICENSE_URL', 'http://localhost/sejoli/sejoli-validate-license');
			</p>
		</div>
		<?php

	endif;

}


add_action('admin_menu', 'sejolicense_register_option_page', 1);

/**
 * Register option page to simulate license integration
 * Hooked via action admin_menu, priority 1
 * @since   1.0.0
 * @return  void
 */
function sejolicense_register_option_page() {

    add_options_page(
        __('Sejoli - Contoh Integrasi License', 'ttom'),
        __('Sejoli License', 'ttom'),
        'manage_options',
        'sejoli-license',
        'sejolicense_display_license_page'
    );
}

/**
 * Display license page to
 * @return [type] [description]
 */
function sejolicense_display_license_page() {
    ?>
    <div class="wrap">
	    <h1 class="wp-heading-inline">
	        <?php _e('Pengecekan License', 'sejoli'); ?>
		</h1>

		<!-- Form Pendaftaran Lisensi -->
		<form action="" method="post">

			<h2><?php _e('Pendaftaran Lisensi', 'sejoli'); ?></h2>

			<p>
				<?php _e('Form ini berguna untuk mendaftarkan lisensi dengan stringnya ke membership sejoli', 'sejoli'); ?>.<br />
				<?php _e('Hasil dari form ini nantinya akan menyimpan string ke lisensi yang dituju', 'sejoli'); ?>.<br />
				<?php _e('Gunakan fungsi pada form ini untuk pendaftarn lisensi di awal', 'sejoli'); ?>
			</p>

			<?php wp_nonce_field('sejoli-register-license', 'sejoli-license-nonce'); ?>

			<table class='form-table' role="presentation">
				<tbody>
					<tr>
						<th scope='row'><?php _e('Alamat Email', 'sejoli'); ?></th>
						<td><input class='regular-text' type="email" name='email' value='' /></td>
					</tr>
					<tr>
						<th scope='row'><?php _e('Password', 'sejoli'); ?></th>
						<td><input class='regular-text' type="text" name='password' value='' /></td>
					</tr>
					<tr>
						<th scope='row'><?php _e('Lisensi', 'sejoli'); ?></th>
						<td><input class='regular-text' type="text" name='license' value='' /></td>
					</tr>
					<tr>
						<th scope='row'><?php _e('String', 'sejoli'); ?></th>
						<td>
							<input class='regular-text' type="text" name='string' value='' />
							<p class='description'>
								<?php _e('Contoh string bisa nama domain, mac ID handphone, dll', 'sejoli'); ?>.<br />
								<?php _e('Pastikan string ini unik', 'sejoli'); ?>
							</p>
						</td>
					</tr>
				</tbody>
			</table>

			<p clas='submit'>
				<button type="submit" class='button button-primary' name="submit"><?php _e('Daftar Lisensi', 'sejoli'); ?></button>
			</p>

		</form>

		<hr />

		<!-- Form Pengecekan Lisensi -->
		<form action="" method="post">

			<h2><?php _e('Pengecekan Lisensi', 'sejoli'); ?></h2>

			<p>
				<?php _e('Form ini berguna untuk mengecek lisensi dengan stringnya ke membership sejoli', 'sejoli'); ?>.<br />
				<?php _e('Hasil dari pengecekan ini adalah apakah string sesuai dengan lisensi yang dicek', 'sejoli'); ?>.<br />
				<?php _e('Gunakan fungsi form ini berjalan pada background sistem, contohnya melalui CRON JOB, mengecek lisensi secara berkala', 'sejoli'); ?>.
			</p>

			<?php wp_nonce_field('sejoli-check-license', 'sejoli-license-nonce'); ?>

			<table class='form-table' role="presentation">
				<tbody>
					<tr>
						<th scope='row'><?php _e('Lisensi', 'sejoli'); ?></th>
						<td><input class='regular-text' type="text" name='license' value='' /></td>
					</tr>
					<tr>
						<th scope='row'><?php _e('String', 'sejoli'); ?></th>
						<td>
							<input class='regular-text' type="text" name='string' value='' />
							<p class='description'>
								<?php _e('Contoh string bisa nama domain, mac ID handphone, dll', 'sejoli'); ?>.<br />
								<?php _e('Pastikan string ini unik', 'sejoli'); ?>
							</p>
						</td>
					</tr>
				</tbody>
			</table>

			<p clas='submit'>
				<button type="submit" class='button button-primary' name="submit"><?php _e('Cek Lisensi', 'sejoli'); ?></button>
			</p>

		</form>
    </div>
    <?php
}
