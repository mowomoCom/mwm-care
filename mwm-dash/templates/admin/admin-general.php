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

// echop(mwm_dashboard()->get_plugins()[0]->get_info(array('slug', 'name', 'notifications')));

// $feed = new mwm_feed(MWM_RRSS_SLUG.'-feed-uno', 'https://www.mowomo.com', 'hola', 10, 1, false);
// echop($feed->get_info(''));


?>

<!-- Admin Page -->
<div id="mwm-wrap" class="wrap">

    <div class="mwm-panel-principal">

        <!-- Page Title -->
        <h2><?php echo $admin_title; ?></h2>

        <div class="mwm-content">
			<?php $no_pro_plugins = mwm_dashboard()->get_no_pro_plugins(); ?>
				<div class="mwm-card">
					<h3><?php echo __('Pro version', 'mwm_dash' ); ?></h3>
					<p><?php echo __('All plugins in this table can be updated to a pro version with new features', 'mwm_dash' ); ?></p>
				</div>
				<div class="mwm-table">
					<table >
						<tbody>
							<?php if(count($no_pro_plugins) > 0) : ?>
								<?php foreach ($no_pro_plugins as $no_pro_plugin) : ?>     
									<tr>
										<th>
											<span><?php echo $no_pro_plugin->get_info('name'); ?></span>
										</th>
										<td>
											<span><?php echo $no_pro_plugin->get_info('update_message'); ?></span>
										</td>
										<td>
											<a class="button-primary" href="<?php echo $no_pro_plugin->get_info('update_url'); ?>"><?php echo __('Get PRO', 'mwm_dash' ); ?></a>
										</td>
									</tr>
								<?php endforeach;?>
							<?php else: ?>
								<tr>
									<th>
										<span><?php echo __('Enhorabuena, todos tus plugins cuentan con la versión PRO.', 'mwm_dash' ) ?></span>
									</th>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>

			<?php $notifications = mwm_dashboard()->get_notifications(); ?>
				<div class="mwm-card">
					<h3><?php echo __('Plugin notifications', 'mwm_dash' ); ?></h3>
					<p><?php echo __('This table shows all mowomo notifications detected by the system.', 'mwm_dash' ); ?></p>
				</div>
				<div class="mwm-table">
					<table >
						<tbody>
							<?php if (count($notifications) > 0) : ?>
								<?php foreach ($notifications as $notification) : ?> 
									<?php 
										$notification_class = 'mwm-noti ';
										switch($notification->get_info('type')) {
											case 0: $notification_class .= 'mwm-noti-success'; break;
											case 1: $notification_class .= 'mwm-noti-info'; break;
											case 2: $notification_class .= 'mwm-noti-warning'; break;
											case 3: $notification_class .= 'mwm-noti-danger'; break;
										}
									?>
									<tr class="<?php echo $notification_class; ?>">
										<th>
											<span><?php echo $notification->get_info('name'); ?></span>
										</th>
										<td>
											<span><?php echo $notification->get_info('message'); ?></span>
										</td>
										<td>
											<a class="button-primary" href="<?php echo $notification->get_info('url'); ?>"><?php echo __('Solve', 'mwm_dash' ); ?></a>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<th>
										<span><?php echo __('Enhorabuena, has resuelto todas las notificaciones de mowomo.', 'mwm_dash' ) ?></span>
									</th>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			
			<?php /*
			<div class="mwm-card">
				<h3><?php echo __('Other interesting plugins', 'mwm_dash' ); ?></h3>
				<p><?php echo __('All these plugins can help you in many different ways, know them.', 'mwm_dash' ); ?></p>
			</div>
			*/ ?>

			<div class="mwm-card">
				<h3><?php echo __('New to mowomo?', 'mwm_dash' ); ?></h3>
				<p><?php echo __('mowomo is a company that makes custom developments for WordPress, both themes and plugins. It also offers other types of products.', 'mwm_dash' ); ?></p>
			</div>

			<div class="mwm-items">
				
			</div>
        </div>
    </div>
	<div class="mwm-panel-sidebar">

	</div>
    <?php settings_errors(); ?>
</div>