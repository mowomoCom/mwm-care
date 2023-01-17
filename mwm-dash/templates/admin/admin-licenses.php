<?php
/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt.
 */

// Load configuration
$admin_title = $admin_config['title'];
$page_slug = $admin_config['page_slug'];

$plugins = mwm_dashboard()->get_plugins();
$activated_plugins = array();
$not_activated_plugins = array();

if ( isset( $_POST['mwm-activate-licenses'] ) ) {
	if ( isset( $_POST['codi'] ) && !empty( $_POST['codi'] ) ) {
		$codigos_plugins = $_POST['codi'];
		foreach ( $codigos_plugins as $plugin_slug => $codigo ) {
			if ( $codigo ) {

				// TODO: Encapsular en función
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://pro.mowomo.com/wp-json/licencias/v1/check/?codi='. $codigo .'&slug='. $plugin_slug .'&url='.get_site_url(),
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => false,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
				));
				$response = curl_exec($curl);
				curl_close($curl);
			
				if( !str_contains( $response, 'Error' ) ){
					$response = json_decode( $response );
					update_option( $plugin_slug . '-gith-repo', $response[0] );
					update_option( $plugin_slug . '-gith-auth-toke', $response[1] );
					update_option( $plugin_slug . '-codi-lice',  $codigo );
					update_option( $plugin_slug . '-lice-acti', true );

					// TODO: Encapsular en función
					$curl = curl_init();
					curl_setopt_array($curl, array(
						CURLOPT_URL => 'https://pro.mowomo.com/wp-json/licencias/v1/use/?codi='. $codigo .'&slug='. $plugin_slug .'&url='.get_site_url(),
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING => '',
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 0,
						CURLOPT_FOLLOWLOCATION => false,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => 'POST',
					));
					$response = curl_exec($curl);
					curl_close($curl);
			
					ob_start(); ?>
						<div class="notice notice-success"> 
							<p><strong><?php echo __( 'Plugin license code "', 'mwm_dash' ) . $plugins[ $plugin_slug ]->get_info('name') . __( '" activated.', 'mwm_dash' ); ?></strong></p>
						</div>
					<?php $html = ob_get_clean();
					echo $html;
				} else {
					update_option( $plugin_slug . '-gith-repo', false );
					update_option( $plugin_slug . '-gith-auth-toke', false );
					update_option( $plugin_slug . '-codi-lice', false );
					update_option( $plugin_slug . '-lice-acti', false );
			
					ob_start(); ?>
						<div class="notice notice-error"> 
							<p><strong><?php echo __( 'Plugin license code "', 'mwm_dash' ) . $plugins[ $plugin_slug ]->get_info('name') . __( '" is not valid.', 'mwm_dash' ); ?></strong></p>
						</div>
					<?php $html = ob_get_clean();
					echo $html;
				}
			}
		}
	}
}

// echop( get_option( 'mwm_debug_1' ) );
foreach ( $plugins as $plugin_slug => $plugin ) {
	// Ver
	// echop( get_option( $plugin_slug . '-gith-repo' ) );
	// echop( get_option( $plugin_slug . '-gith-auth-toke' ) );
	// echop( get_option( $plugin_slug . '-codi-lice' ) );
	// echop( get_option( $plugin_slug . '-lice-acti' ) );

	// Borrar
	// update_option( $plugin_slug . '-gith-repo', false );
	// update_option( $plugin_slug . '-gith-auth-toke', false );
	// update_option( $plugin_slug . '-codi-lice', false );
	// update_option( $plugin_slug . '-lice-acti', false );
}
// echop( get_option( 'mwm_debug' ) );
// update_option( 'mwm_debug', array() );

// update_option( MWM_RRSS_SLUG . '-gith-repo', false );
// update_option( MWM_RRSS_SLUG . '-gith-auth-toke', false );
// update_option( MWM_RRSS_SLUG . '-codi-lice', false );
// update_option( MWM_RRSS_SLUG . '-lice-acti', false );

foreach ($plugins as $plugin) {
	if ( $plugin->is_activated() ) {
		$activated_plugins[] = $plugin;
	} else {
		$not_activated_plugins[] = $plugin;
	}
}

?>

<!-- Admin Page -->
<div id="mwm-wrap" class="wrap">

    <div class="mwm-panel-principal">

        <!-- Page Title -->
        <h2><?php echo $admin_title; ?></h2>

        <div class="mwm-content">
			<div class="mwm-card">
				<h3><?php _e('Activated licenses', 'mwm_dash' ); ?></h3>
				<p><?php _e('All plugins listed here will receive updates.', 'mwm_dash' ); ?></p>
			</div>
			<div class="mwm-table">
			<?php if(count($activated_plugins) > 0) : ?>
					<form action="" method="POST">
						<table>
							<thead>
								<tr>
									<th><span><?php _e( 'Plugin', 'mwm_dash' ); ?></span></th>
									<th><span><?php _e( 'Expiry date', 'mwm_dash' ); ?></span></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($activated_plugins as $plugin) : ?>     
									<tr>
										<td>
											<span><?php echo $plugin->get_info('name'); ?></span>
										</td>
										<td><span></span></td>
									</tr>
								<?php endforeach;?>
							</tbody>
						</table>
					</form>
				<?php else: ?>
					<table>
						<tbody>
							<tr>
								<th colspan="3">
									<span><?php echo __( 'None of your plugins have the licence activated.', 'mwm_dash' ) ?></span>
								</th>
							</tr>
						</tbody>
					</table>
				<?php endif; ?>
			</div>

			<div class="mwm-card">
				<h3><?php _e('Not activated licenses', 'mwm_dash' ); ?></h3>
				<p><?php _e('All plugins listed here will not receive updates until their license is activated.', 'mwm_dash' ); ?></p>
			</div>
			<div class="mwm-table">
				<?php if(count($not_activated_plugins) > 0) : ?>
					<form action="" method="POST">
						<table>
							<thead>
								<tr>
									<th><span><?php _e( 'Plugin', 'mwm_dash' ) ?></span></th>
									<th><span><?php _e( 'License code', 'mwm_dash' ) ?></span></th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($not_activated_plugins as $plugin) : ?>     
									<tr>
										<th>
											<span><?php echo $plugin->get_info('name'); ?></span>
										</th>
										<td>
											<span><input type="text" name="codi[<?php echo $plugin->get_info('slug'); ?>]" id="codi[<?php echo $plugin->get_info('slug'); ?>]" value="<?php echo get_option( $plugin->get_info( 'slug' ) . '-codi-lice' ); ?>"></span>
										</td>
										<td>
											<input type="submit" name="mwm-activate-licenses" class="button-primary" value="<?php _e( 'Activate', 'mwm_dash' ); ?>">
										</td>
									</tr>
								<?php endforeach;?>
							</tbody>
						</table>
					</form>
				<?php else: ?>
					<table>
						<tbody>
							<tr>
								<th colspan="3">
									<span><?php echo __( 'Congratulations, all your plugins are activated.', 'mwm_dash' ) ?></span>
								</th>
							</tr>
						</tbody>
					</table>
				<?php endif; ?>
			</div>
        </div>
    </div>
	<div class="mwm-panel-sidebar">

	</div>
    <?php settings_errors(); ?>
</div>