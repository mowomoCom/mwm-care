<?php

declare(strict_types=1);

class mwm_updater
{
    private $file;
    private $plugin_data;
    private $basename;
	private $folder_name;
    private $active = false;
    private $github_response;

    public function __construct( $plugin )
    {
		$this->plugin = $plugin;
		$this->file = $plugin->get_info( 'file' );
		$this->basename = plugin_basename( $this->file );
		$this->folder_name = basename( dirname( $this->file ) );
    }

    public function init(): void
    {
		register_setting( $this->plugin->get_info( 'slug' ) . '-upda', $this->plugin->get_info( 'slug' ) . '-codi-lice' );
		register_setting( $this->plugin->get_info( 'slug' ) . '-upda', $this->plugin->get_info( 'slug' ) . '-lice-acti' );
		register_setting( $this->plugin->get_info( 'slug' ) . '-upda', $this->plugin->get_info( 'slug' ) . '-gith-auth-toke' );
		register_setting( $this->plugin->get_info( 'slug' ) . '-upda', $this->plugin->get_info( 'slug' ) . '-gith-repo' );
		
		if ( ! defined( 'MWM_GITH_REQU_URI' ) ) {
			define( 'MWM_GITH_REQU_URI', 'https://api.github.com/repos/%s/%s/releases' );
		}

		if ( ! defined( 'MWM_GITH_USERNAME' ) ) {
			define( 'MWM_GITH_USERNAME', 'mowomoCom' );
		}

        if(get_option($this->plugin->get_info( 'slug' ) . '-gith-auth-toke')){
            add_filter('pre_set_site_transient_update_plugins', array( $this, 'modify_transient'), 10, 1);
            add_filter('http_request_args', array( $this, 'set_header_token'), 10, 2);
            add_filter('plugins_api', array( $this, 'plugin_popup'), 10, 3);
            add_filter('upgrader_post_install', array( $this, 'after_install'), 10, 3);
        } else {
			if ( isset( $_GET['page'] ) && $_GET['page'] != 'mwm_dash-licenses' ) {
				add_action('all_admin_notices', array( $this, 'admin_notice' ));
			}
		}

    }

    public function modify_transient(object $transient)
    {
		update_option( 'mwm_debug_1', $transient );
        if (! property_exists($transient, 'checked')) {
            return $transient;
        }

        $this->get_repository_info();
        $this->get_plugin_data();

		if ( ! is_array( get_option( 'mwm_debug' ) ) ) {
			update_option( 'mwm_debug', array() );
		}
		update_option( 'mwm_debug', array_merge( get_option( 'mwm_debug' ) , array( $this ) ) );

        if (version_compare($this->github_response['tag_name'], $transient->checked[$this->basename], 'gt')) {
            $plugin = [
                'url' => $this->plugin_data['PluginURI'],
                'slug' => current(explode('/', $this->basename)),
                'package' => $this->github_response['zipball_url'],
                'new_version' => $this->github_response['tag_name'],
            ];

            $transient->response[$this->basename] = (object) $plugin;
        }

        return $transient;
    }

    public function plugin_popup(bool $result, string $action, object $args)
    {
		update_option( 'mwm_debug_2', array( $result, $action, $args ) );
        if ('plugin_information' !== $action || empty($args->slug)) {
            return false;
        }

        if ($args->slug == current(explode('/', $this->basename))) {
            $this->get_repository_info();
            $this->get_plugin_data();

            $plugin = [
                'name' => $this->plugin_data['Name'],
                'slug' => $this->basename,
                'requires' => $this->plugin_data['RequiresWP'],
                'tested' => $this->plugin_data['TestedUpTo'],
                'version' => $this->github_response['tag_name'],
                'author' => $this->plugin_data['AuthorName'],
                'author_profile' => $this->plugin_data['AuthorURI'],
                'last_updated' => $this->github_response['published_at'],
                'homepage' => $this->plugin_data['PluginURI'],
                'short_description' => $this->plugin_data['Description'],
                'sections' => [
                    'Description' => $this->plugin_data['Description'],
                    'Updates' => $this->github_response['body'],
                ],
                'download_link' => $this->github_response['zipball_url'],
            ];

            return (object) $plugin;
        }

        return $result;
    }

    public function after_install(bool $response, array $hook_extra, array $result)
    {
		global $wp_filesystem;

        $install_directory = plugin_dir_path($this->file);
        $wp_filesystem->move($result['destination'], $install_directory);
        $result['destination'] = $install_directory;

        if ($this->active) {
            activate_plugin($this->basename);
        }

        return $result;
    }

    public function set_header_token(array $parsed_args, string $url)
    {
		update_option( 'mwm_debug_3', array( $parsed_args, $url ) );
        $parsed_url = parse_url($url);

        if ('api.github.com' === ($parsed_url['host'] ?? null) && isset($parsed_url['query'])) {
            parse_str($parsed_url['query'], $query);

            if (isset($query['access_token'])) {
                $parsed_args['headers']['Authorization'] = 'token ' . $query['access_token'];

                $this->active = is_plugin_active($this->basename);
            }
        }

        return $parsed_args;
    }


    private function get_repository_info()
    {
        if (null !== $this->github_response) {
            return;
        }

        $args = [
            'method' => 'GET',
            'timeout' => 5,
            'redirection' => 5,
            'httpversion' => '1.0',
            'headers' => [
                'Authorization' => 'token ' . get_option( $this->plugin->get_info( 'slug' ) . '-gith-auth-toke' ),
            ],
            'sslverify' => true,
        ];
        $request_uri = sprintf(MWM_GITH_REQU_URI, MWM_GITH_USERNAME, get_option( $this->plugin->get_info( 'slug' ) . '-gith-repo' ));

        $request = wp_remote_get($request_uri, $args);
        if (is_array($request)) {
            $response = current($request);
            $response['tag_name'] = json_decode($request['body'])[0]->tag_name;
            $response['zipball_url'] = json_decode($request['body'])[0]->zipball_url;
            if ( get_option( $this->plugin->get_info( 'slug' ) . '-gith-auth-toke' ) ) {
                $response['zipball_url'] = $response['zipball_url']. '?access_token=' . get_option( $this->plugin->get_info( 'slug' ) . '-gith-auth-toke' );
            }
        }

        $this->github_response = $response;
    }

	/**
	 * Alerta al usuario de que no tiene activada la licencia
	 *
	 * @return void
	 */
	public function admin_notice() {
		ob_start(); ?>
			<div class="notice notice-warning is-dismissible">
				<p><?php echo __( 'Please activate the license of the "<strong>', 'mwm_dash' ) . $this->plugin->get_info('name') . __( '</strong>" plugin at ', 'mwm_dash' ) . '<a href="' . admin_url( 'admin.php?page=mwm_dash-licenses' ) . '">' . __( 'licence management', 'mwm_dash' ) . '</a>'; ?></p>
			</div>
		<?php $html = ob_get_clean();
		echo $html;
	}

    /* TODO CHECK THIS FUNCTION  */
    private function get_plugin_data()
    {
        
        if (null !== $this->plugin_data) {
            return;
        }

        $this->plugin_data = get_plugin_data($this->file);
    }

	/**
	 * Return if is activated or not
	 *
	 * @return boolean
	 */
	public function is_activated()
	{
		return get_option( $this->plugin->get_info( 'slug' ) . '-lice-acti' ) ? true : false;
	}
}
