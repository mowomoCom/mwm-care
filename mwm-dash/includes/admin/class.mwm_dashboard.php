<?php
/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt.
 */

/**
 * Check if exists the class 'mwm_dashboard'.
 *
 * @since 1.0.0
 */
if (!class_exists('mwm_dashboard')) {
    /**
     * Implements the mwm_dashboards class.
     * 
     * This is the class that controls the entire plugin.
     *
     * @since 1.0.0
     */
    class mwm_dashboard
    {
        /**
         * Single instance of the class.
         *
         * @var \mwm_dashboard
         *
         * @since 1.0.0
         */
        protected static $instance;

        /**
         * Returns single instance of the class.
         *
         * @since 1.0.0
         * 
         * @return \mwm_dashboard
         */
        public static function get_instance()
        {
            if (is_null(self::$instance)) :
                self::$instance = new self();
            endif;

            return self::$instance;
        }

        /**
         * Variable
         * 
         * mowomo plugins
         *
         * @since 1.0.0
         * 
         * @return array
         */
        protected $plugins = array();

        /**
         * Variable
         * 
         * mowomo notifications
         *
         * @since 1.0.0
         * 
         * @return array
         */
        protected $notifications = array();

        /**
         * Constructor.
         *
         * Initialice plugin and registers actions and filters to be used.
         *
         * @since 1.0.0
         * 
         * @return \mwm_dashboard
         */
        private function __construct()
        {
            // Ading scripts
            add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));

            // Initializing plugin information
            add_action('admin_init', array($this, 'init') );

            // Showing admin page
            add_action('admin_menu', array($this, 'add_menu_to_admin'));
            add_action('admin_menu', array($this, 'add_license_page_to_admin'), 100);
            add_filter('admin_footer_text', array($this, 'admin_footer'));

            // Showing admin bar
            add_action( 'wp_before_admin_bar_render', array($this, 'show_admin_bar'));
        }

        /**
         * Enqueue scripts and styles.
         *
         * @since 1.0.0
         * 
         * @return void
         */
        public function enqueue_scripts()
        {
            // Enqueuing scripts in the admin page
            wp_register_script( MWM_FRA_SLUG.'_admin_jquery_ui', 'https://code.jquery.com/ui/1.12.1/jquery-ui.min.js', array(), MWM_FRA_VERSION, true );
            wp_register_script( MWM_FRA_SLUG.'_admin_scripts', MWM_FRA_ASS.'js/admin_scripts.js', array('jquery'), '1.0.0', true);
            wp_register_style( MWM_FRA_SLUG.'_admin_jquery_ui_css', 'https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css', array(), MWM_FRA_VERSION );
            wp_register_style( MWM_FRA_SLUG.'_admin_styles', MWM_FRA_ASS.'css/admin_styles.min.css', array());

			// Adding info to scripts
            wp_localize_script( MWM_FRA_SLUG.'_admin_scripts', 'admin_data', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
				'move_icon' => '<span class="dashicons dashicons-move"></span>',
            ));

            wp_enqueue_script( MWM_FRA_SLUG.'_admin_jquery_ui' );
            wp_enqueue_script( MWM_FRA_SLUG.'_admin_scripts');
            wp_enqueue_style( MWM_FRA_SLUG.'_admin_jquery_ui_css' );
            wp_enqueue_style( MWM_FRA_SLUG.'_admin_styles');

            
        }

        /**
         * Register settings for the plugin.
         *
         * @since 1.0.0
         * 
         * @return void
         */
        public  function init()
        {

        }

        /**
         * Add the plugin menu and plugin menu page to the dashboard
         *
         * @since 1.0.0
         * 
         * @return void
         */
        public function add_menu_to_admin()
        {
			add_menu_page( 'mowomo', __('mowomo', 'mwm_dash'), 'manage_options', MWM_FRA_SLUG, array($this, 'get_main_admin_page'), $this->logo_base64_uri() );
            add_submenu_page( MWM_FRA_SLUG, 'mowomo', __('Panel General', 'mwm_dash' ), 'manage_options',  MWM_FRA_SLUG, array($this, 'get_main_admin_page'));
        }

        /**
         * Add the plugin menu and plugin menu page to the dashboard
         *
         * @since 1.0.0
         * 
         * @return void
         */
        public  function add_license_page_to_admin()
        {
            add_submenu_page( MWM_FRA_SLUG, 'mowomo', __('Gestión de licencias', 'mwm_dash' ), 'manage_options', MWM_FRA_SLUG.'-licenses', array($this, 'get_licenses_page'));
        }

        /**
         * Load the administration page template.
         *
         * @since 1.0.0
         * 
         * @return void
         */
        public function get_main_admin_page()
        {
            // Configure the administrator page
            $admin_config = array(
                'title' => __('mowomo General panel', 'mwm_dash'),
                'page_slug' => MWM_FRA_SLUG,
            );

            set_query_var( 'admin_config', $admin_config );

            // Load the base template
            mwm_dashboard_template('admin/admin', 'general', array());
        }

        /**
         * Load the administration page template.
         *
         * @since 1.0.0
         * 
         * @return void
         */
        public function get_licenses_page()
        {
            // Configure the administrator page
            $admin_config = array(
                'title' => __('License management', 'mwm_dash'),
                'page_slug' => MWM_FRA_SLUG,
            );

            set_query_var( 'admin_config', $admin_config );

            // Load the base template
            mwm_dashboard_template('admin/admin', 'licenses', array());
        }

        /**
         * Modify the footer text
         *
         * @since 1.0.0
         * 
         * @return string
         *
        */
        public function admin_footer($footer_text)
        {
            if ( isset($_GET['page']) && ($_GET['page'] == MWM_FRA_SLUG || $_GET['page'] == MWM_FRA_SLUG.'-licenses') ) :
                $footer_text = __( 'Thanks for using mowomo Social Share PRO, plugin made by ', 'mwm_dash') . '<a href="https://mowomo.com" target="_blank" rel="nofollow">' . __('mowomo team', 'mwm_dash'). '</a>.';
            endif;
            return $footer_text;
        }

        /**
         * Show the admin bar
         *
         * @since 1.0.0
         * 
         * @return void
         *
        */
        public function show_admin_bar()
        {
            global $wp_admin_bar;

			$logo_data = array(
				'style' => 'width: auto; height: 70%; padding-top: 20%;',
				'color' => '#FFFFFF',
			);

            // General Menu
            $wp_admin_bar->add_menu( array(
                'parent' => false,
                'id' => MWM_FRA_SLUG,
                'title' => $this->logo_mowomo_svg( $logo_data ),
                'href' => admin_url('admin.php?page=mwm_dash'),
                'meta' => false
            ));

            // $wp_admin_bar->add_menu( array(
            //     'parent' => MWM_FRA_SLUG,
            //     'id' => MWM_FRA_SLUG.'-general',
            //     'title' => __('Avisos generales', 'mwm_dash' ),
            //     'href' => admin_url('admin.php?page=mwm_dash'),
            //     'meta' => false
            // ));

            // Plugin notifications menu
            foreach ($this->plugins as $p_key => $p_value) {
                if (count($p_value->get_info('notifications')) > 0) {
                    $wp_admin_bar->add_menu( array(
                        'parent' => MWM_FRA_SLUG,
                        'id' => $p_value->get_info('slug'),
                        'title' => $p_value->get_info('name'),
                        'href' => admin_url('admin.php?page='.$p_value->get_info('slug')),
                        'meta' => false
                    ));
                    
                    foreach ($p_value->get_info('notifications') as $n_key => $v_value) {
                        $wp_admin_bar->add_menu( array(
                            'parent' => $p_value->get_info('slug'),
                            'id' => $v_value->get_info('code'),
                            'title' => __('Aviso:', 'mwm_dash').' '.$v_value->get_info('name'),
                            'href' => admin_url($v_value->get_info('url')),
                            'meta' => false
                        ));
                    }
                }
            }

            // $wp_admin_bar->add_menu( array(
            //     'parent' => MWM_FRA_SLUG,
            //     'id' => MWM_FRA_SLUG.'-offers',
            //     'title' => __('Ofertas y promociones', 'mwm_dash' ),
            //     'href' => admin_url('admin.php?page=mwm_dash'),
            //     'meta' => false
            // ));
            
        }

		/**
		 * Undocumented function
		 *
		 * @param [Array] $data
		 * @return String
		 */
		public function logo_mowomo_svg( $data )
		{
			$style = isset( $data['style'] ) ? $data['style'] : '';
			$color = isset( $data['color'] ) ? $data['color'] : 'rgb(15, 76, 129)';
			
			$logo = '<svg width="414" height="403" viewBox="0 0 414 403" fill="none" xmlns="http://www.w3.org/2000/svg" style="'. $style .'">';
			$logo .= '<path d="M96.6646 197.532C94.9752 189.837 94.8407 182.252 96.259 174.78C97.6774 167.305 100.36 160.49 104.308 154.329C108.257 148.167 113.305 142.866 119.444 138.428C125.588 133.992 132.529 130.913 140.27 129.194C147.194 127.658 154.107 127.395 161.015 128.408C167.923 129.417 174.485 131.689 180.702 135.207C184.779 129.404 189.713 124.582 195.511 120.749C201.309 116.914 207.715 114.221 214.725 112.666C222.468 110.949 230.027 110.804 237.411 112.239C244.796 113.671 251.565 116.351 257.725 120.268C263.884 124.184 269.171 129.231 273.583 135.411C277.996 141.588 281.042 148.527 282.732 156.222L303.293 249.948L265.998 258.227L245.437 164.501C244.873 161.935 243.866 159.635 242.42 157.603C240.97 155.569 239.219 153.892 237.16 152.574C235.103 151.25 232.856 150.36 230.412 149.893C227.969 149.428 225.475 149.477 222.923 150.043C220.373 150.609 218.065 151.626 215.997 153.092C213.934 154.56 212.221 156.333 210.86 158.413C209.505 160.49 208.589 162.758 208.128 165.217C207.665 167.675 207.715 170.184 208.276 172.752L228.838 266.478L191.679 274.727L171.118 181.001C170.554 178.435 169.528 176.139 168.034 174.117C166.538 172.093 164.764 170.423 162.707 169.101C160.651 167.778 158.402 166.887 155.959 166.42C153.518 165.955 151.021 166.005 148.47 166.571C145.92 167.136 143.611 168.153 141.544 169.62C139.479 171.088 137.789 172.855 136.476 174.925C135.164 176.992 134.254 179.26 133.744 181.729C133.236 184.196 133.261 186.711 133.825 189.278L154.386 283.004L117.226 291.253L96.6646 197.532Z" fill="'. $color .'"/>';
			$logo .= '<path d="M399.057 165.647C378.78 139.583 364.754 110.718 356.981 79.0948C349.229 47.4504 328.359 31.0759 294.416 30.012C260.493 28.9287 228.176 21.427 197.486 7.5245C166.795 -6.39745 140.434 -1.0002 118.382 23.7143C96.3521 48.4501 70.0759 67.9522 39.5733 82.2398C9.0727 96.5293 -2.94706 119.629 3.49591 151.538C9.95897 183.449 9.5148 215.257 2.16548 246.983C-5.18585 278.73 6.17876 302.116 36.2774 317.182C66.3761 332.247 92.0836 352.405 113.416 377.691C134.771 402.978 160.963 409.05 192.034 395.925C223.105 382.801 255.612 376.137 289.577 375.952C323.542 375.727 344.832 359.926 353.494 328.486C362.134 297.065 376.961 268.57 397.978 243.037C418.975 217.509 419.334 191.711 399.057 165.647ZM378.442 238.851C359.579 261.788 346.273 287.381 338.52 315.632C330.747 343.863 311.631 358.071 281.13 358.253C250.65 358.438 221.459 364.427 193.577 376.223C165.675 388.019 142.144 382.559 122.986 359.829C103.85 337.118 80.7411 318.985 53.7253 305.452C26.7095 291.919 16.5084 270.904 23.1182 242.387C29.7078 213.87 30.1098 185.291 24.3019 156.611C18.5141 127.95 29.3079 107.201 56.7036 94.3645C84.0792 81.5259 107.673 64.0078 127.444 41.7867C147.235 19.5657 170.915 14.7208 198.457 27.2327C226.001 39.7231 255.023 46.4682 285.481 47.429C315.962 48.3898 334.676 63.0878 341.645 91.523C348.615 119.958 361.205 145.878 379.413 169.305C397.642 192.713 397.305 215.895 378.442 238.851Z" fill="'. $color .'"/>';
			$logo .= '</svg>';

			return $logo;
		}

		/**
		 * Undocumented function
		 *
		 * @return String
		 */
		public function logo_base64_uri()
		{
			return 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDE0IiBoZWlnaHQ9IjQwMyIgdmlld0JveD0iMCAwIDQxNCA0MDMiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxwYXRoIGQ9Ik05Ni42NjQ2IDE5Ny41MzJDOTQuOTc1MiAxODkuODM3IDk0Ljg0MDcgMTgyLjI1MiA5Ni4yNTkgMTc0Ljc4Qzk3LjY3NzQgMTY3LjMwNSAxMDAuMzYgMTYwLjQ5IDEwNC4zMDggMTU0LjMyOUMxMDguMjU3IDE0OC4xNjcgMTEzLjMwNSAxNDIuODY2IDExOS40NDQgMTM4LjQyOEMxMjUuNTg4IDEzMy45OTIgMTMyLjUyOSAxMzAuOTEzIDE0MC4yNyAxMjkuMTk0QzE0Ny4xOTQgMTI3LjY1OCAxNTQuMTA3IDEyNy4zOTUgMTYxLjAxNSAxMjguNDA4QzE2Ny45MjMgMTI5LjQxNyAxNzQuNDg1IDEzMS42ODkgMTgwLjcwMiAxMzUuMjA3QzE4NC43NzkgMTI5LjQwNCAxODkuNzEzIDEyNC41ODIgMTk1LjUxMSAxMjAuNzQ5QzIwMS4zMDkgMTE2LjkxNCAyMDcuNzE1IDExNC4yMjEgMjE0LjcyNSAxMTIuNjY2QzIyMi40NjggMTEwLjk0OSAyMzAuMDI3IDExMC44MDQgMjM3LjQxMSAxMTIuMjM5QzI0NC43OTYgMTEzLjY3MSAyNTEuNTY1IDExNi4zNTEgMjU3LjcyNSAxMjAuMjY4QzI2My44ODQgMTI0LjE4NCAyNjkuMTcxIDEyOS4yMzEgMjczLjU4MyAxMzUuNDExQzI3Ny45OTYgMTQxLjU4OCAyODEuMDQyIDE0OC41MjcgMjgyLjczMiAxNTYuMjIyTDMwMy4yOTMgMjQ5Ljk0OEwyNjUuOTk4IDI1OC4yMjdMMjQ1LjQzNyAxNjQuNTAxQzI0NC44NzMgMTYxLjkzNSAyNDMuODY2IDE1OS42MzUgMjQyLjQyIDE1Ny42MDNDMjQwLjk3IDE1NS41NjkgMjM5LjIxOSAxNTMuODkyIDIzNy4xNiAxNTIuNTc0QzIzNS4xMDMgMTUxLjI1IDIzMi44NTYgMTUwLjM2IDIzMC40MTIgMTQ5Ljg5M0MyMjcuOTY5IDE0OS40MjggMjI1LjQ3NSAxNDkuNDc3IDIyMi45MjMgMTUwLjA0M0MyMjAuMzczIDE1MC42MDkgMjE4LjA2NSAxNTEuNjI2IDIxNS45OTcgMTUzLjA5MkMyMTMuOTM0IDE1NC41NiAyMTIuMjIxIDE1Ni4zMzMgMjEwLjg2IDE1OC40MTNDMjA5LjUwNSAxNjAuNDkgMjA4LjU4OSAxNjIuNzU4IDIwOC4xMjggMTY1LjIxN0MyMDcuNjY1IDE2Ny42NzUgMjA3LjcxNSAxNzAuMTg0IDIwOC4yNzYgMTcyLjc1MkwyMjguODM4IDI2Ni40NzhMMTkxLjY3OSAyNzQuNzI3TDE3MS4xMTggMTgxLjAwMUMxNzAuNTU0IDE3OC40MzUgMTY5LjUyOCAxNzYuMTM5IDE2OC4wMzQgMTc0LjExN0MxNjYuNTM4IDE3Mi4wOTMgMTY0Ljc2NCAxNzAuNDIzIDE2Mi43MDcgMTY5LjEwMUMxNjAuNjUxIDE2Ny43NzggMTU4LjQwMiAxNjYuODg3IDE1NS45NTkgMTY2LjQyQzE1My41MTggMTY1Ljk1NSAxNTEuMDIxIDE2Ni4wMDUgMTQ4LjQ3IDE2Ni41NzFDMTQ1LjkyIDE2Ny4xMzYgMTQzLjYxMSAxNjguMTUzIDE0MS41NDQgMTY5LjYyQzEzOS40NzkgMTcxLjA4OCAxMzcuNzg5IDE3Mi44NTUgMTM2LjQ3NiAxNzQuOTI1QzEzNS4xNjQgMTc2Ljk5MiAxMzQuMjU0IDE3OS4yNiAxMzMuNzQ0IDE4MS43MjlDMTMzLjIzNiAxODQuMTk2IDEzMy4yNjEgMTg2LjcxMSAxMzMuODI1IDE4OS4yNzhMMTU0LjM4NiAyODMuMDA0TDExNy4yMjYgMjkxLjI1M0w5Ni42NjQ2IDE5Ny41MzJaIiBmaWxsPSIjMEY0QzgxIi8+CjxwYXRoIGQ9Ik0zOTkuMDU3IDE2NS42NDdDMzc4Ljc4IDEzOS41ODMgMzY0Ljc1NCAxMTAuNzE4IDM1Ni45ODEgNzkuMDk0OEMzNDkuMjI5IDQ3LjQ1MDQgMzI4LjM1OSAzMS4wNzU5IDI5NC40MTYgMzAuMDEyQzI2MC40OTMgMjguOTI4NyAyMjguMTc2IDIxLjQyNyAxOTcuNDg2IDcuNTI0NUMxNjYuNzk1IC02LjM5NzQ1IDE0MC40MzQgLTEuMDAwMiAxMTguMzgyIDIzLjcxNDNDOTYuMzUyMSA0OC40NTAxIDcwLjA3NTkgNjcuOTUyMiAzOS41NzMzIDgyLjIzOThDOS4wNzI3IDk2LjUyOTMgLTIuOTQ3MDYgMTE5LjYyOSAzLjQ5NTkxIDE1MS41MzhDOS45NTg5NyAxODMuNDQ5IDkuNTE0OCAyMTUuMjU3IDIuMTY1NDggMjQ2Ljk4M0MtNS4xODU4NSAyNzguNzMgNi4xNzg3NiAzMDIuMTE2IDM2LjI3NzQgMzE3LjE4MkM2Ni4zNzYxIDMzMi4yNDcgOTIuMDgzNiAzNTIuNDA1IDExMy40MTYgMzc3LjY5MUMxMzQuNzcxIDQwMi45NzggMTYwLjk2MyA0MDkuMDUgMTkyLjAzNCAzOTUuOTI1QzIyMy4xMDUgMzgyLjgwMSAyNTUuNjEyIDM3Ni4xMzcgMjg5LjU3NyAzNzUuOTUyQzMyMy41NDIgMzc1LjcyNyAzNDQuODMyIDM1OS45MjYgMzUzLjQ5NCAzMjguNDg2QzM2Mi4xMzQgMjk3LjA2NSAzNzYuOTYxIDI2OC41NyAzOTcuOTc4IDI0My4wMzdDNDE4Ljk3NSAyMTcuNTA5IDQxOS4zMzQgMTkxLjcxMSAzOTkuMDU3IDE2NS42NDdaTTM3OC40NDIgMjM4Ljg1MUMzNTkuNTc5IDI2MS43ODggMzQ2LjI3MyAyODcuMzgxIDMzOC41MiAzMTUuNjMyQzMzMC43NDcgMzQzLjg2MyAzMTEuNjMxIDM1OC4wNzEgMjgxLjEzIDM1OC4yNTNDMjUwLjY1IDM1OC40MzggMjIxLjQ1OSAzNjQuNDI3IDE5My41NzcgMzc2LjIyM0MxNjUuNjc1IDM4OC4wMTkgMTQyLjE0NCAzODIuNTU5IDEyMi45ODYgMzU5LjgyOUMxMDMuODUgMzM3LjExOCA4MC43NDExIDMxOC45ODUgNTMuNzI1MyAzMDUuNDUyQzI2LjcwOTUgMjkxLjkxOSAxNi41MDg0IDI3MC45MDQgMjMuMTE4MiAyNDIuMzg3QzI5LjcwNzggMjEzLjg3IDMwLjEwOTggMTg1LjI5MSAyNC4zMDE5IDE1Ni42MTFDMTguNTE0MSAxMjcuOTUgMjkuMzA3OSAxMDcuMjAxIDU2LjcwMzYgOTQuMzY0NUM4NC4wNzkyIDgxLjUyNTkgMTA3LjY3MyA2NC4wMDc4IDEyNy40NDQgNDEuNzg2N0MxNDcuMjM1IDE5LjU2NTcgMTcwLjkxNSAxNC43MjA4IDE5OC40NTcgMjcuMjMyN0MyMjYuMDAxIDM5LjcyMzEgMjU1LjAyMyA0Ni40NjgyIDI4NS40ODEgNDcuNDI5QzMxNS45NjIgNDguMzg5OCAzMzQuNjc2IDYzLjA4NzggMzQxLjY0NSA5MS41MjNDMzQ4LjYxNSAxMTkuOTU4IDM2MS4yMDUgMTQ1Ljg3OCAzNzkuNDEzIDE2OS4zMDVDMzk3LjY0MiAxOTIuNzEzIDM5Ny4zMDUgMjE1Ljg5NSAzNzguNDQyIDIzOC44NTFaIiBmaWxsPSIjMEY0QzgxIi8+Cjwvc3ZnPgo=';
		}

        /**
         * Add plugins to mowomo
         *
         * @since 1.0.0
         * 
         * @return boolean
         */
        public function add_plugin($plugin)
        {
            if (is_a($plugin, 'mwm_plugin')) {
                array_push($this->plugins, $plugin);
                return true;
            } else {
                return false;
            }
        }

        /**
         * Load mowomo plugins
         *
         * @since 1.0.0
         * 
         * @return array
         */
        public function get_plugins($slug = '')
        {
            if ($slug == '') {
				$aux_plugins = array();
				foreach ($this->plugins as $plugin) {
					$aux_plugins[ $plugin->get_info('slug') ] = $plugin;
				}
				return $aux_plugins;
			}

            foreach ($this->plugins as $key => $value) {
                if (strcmp($value->get_info('slug'), $slug) == 0) {
                    return $value;
                }
            }

            return false;
        }

        /**
         * Load mowomo no pro plugins
         *
         * @since 1.0.0
         * 
         * @return array
         */
        public function get_no_pro_plugins()
        {
            $plugins = array();
            
            foreach ($this->plugins as $key => $value) {
                if (!$value->get_info('is_pro')) array_push($plugins, $value);
            }

            return $plugins;
        }

        /**
         * Add notifications to mowomo
         *
         * @since 1.0.0
         * 
         * @return boolean
         */
        public function add_notification($notification)
        {
            if (is_a($notification, 'mwm_notification')) {
                array_push($this->notifications, $notification);
                return true;
            } else {
                return false;
            }
        }

        /**
         * Load mowomo notifications
         *
         * @since 1.0.0
         * 
         * @return array
         */
        public function get_notifications($type = '')
        {
            $notifications = array();

            if ($type == '') return $this->notifications;

            foreach ($this->notifications as $key => $value) {
                if (strcmp($type, $value->get_info('type'))) array_push($notifications, $value);
            }

            return $this->notifications;
        }

    }
}

/**
 * Unique access to instance of mwm_dashboard class.
 * 
 * @since 1.0.0
 *
 * @return \mwm_dashboard
 */
function mwm_dashboard()
{
    return mwm_dashboard::get_instance();
}
