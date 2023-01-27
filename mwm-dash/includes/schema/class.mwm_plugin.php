<?php
/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt.
 */

/**
 * Detects if the plugin has been entered directly.
 *
 * @since 1.0.0
 */
if (!defined('ABSPATH') || !defined('MWM_FRA_VERSION')) {
    exit; // Exit if accessed directly.
}

/**
 * Check if exists the class 'mwm_plugin'.
 *
 * @since 1.0.0
 */
if (!class_exists('mwm_plugin')) {
    /**
     * Implements the mwm_plugin class.
     * 
     * This is the class that controls the entire plugin.
     *
     * @since 1.0.0
     */
    class mwm_plugin
    {
        /**
         * Variable
         * 
         * Plugin Slug
         *
         * @since 1.0.0
         * 
         * @return string
         */
        protected $slug = '';

        /**
         * Variable
         * 
         * Plugin name
         *
         * @since 1.0.0
         * 
         * @return string
         */
        protected $name = '';

        /**
         * Variable
         * 
         * Version of the plugin
         *
         * @since 1.0.0
         * 
         * @return string
         */
        protected $version = '';

        /**
         * Variable
         * 
         * Plugin pro version
         *
         * @since 1.0.0
         * 
         * @return bool
         */
        protected $pro = false;

        /**
         * Variable
         * 
         * Plugin pro license
         *
         * @since 1.0.0
         * 
         * @return string
         */
        protected $pro_license = '';

        /**
         * Variable
         * 
         * Plugin update message
         *
         * @since 1.0.0
         * 
         * @return string
         */
        protected $update_message = '';

		/**
         * Variable
         * 
         * File url
         *
         * @since 1.0.0
         * 
         * @return string
         */
        protected $file = '';

        /**
         * Variable
         * 
         * Plugin notifications
         *
         * @since 1.0.0
         * 
         * @return array
         */
        protected $notifications = array();

		/**
         * Variable
         * 
         * Plugin updater
         *
         * @since 1.0.0
         * 
         * @return mwm_updater
         */
        protected $updater = null;

        /**
         * Constructor
         *
         * Initialice plugin and registers actions and filters to be used.
         *
         * @since 1.0.0
         * 
         * @return \mwm_plugin
         */
        public function __construct( $data )
        {
            // Initialization of all information
            if (isset( $data['slug'] ) && is_string( $data['slug'])) $this->slug = $data['slug'];
			if (isset( $data['name'] ) && is_string( $data['name'])) $this->name = $data['name'];
			if (isset( $data['version'] ) && is_string( $data['version'])) $this->version = $data['version'];
			if (isset( $data['pro'] ) && is_bool( $data['pro'])) $this->pro = $data['pro'];
			if (isset( $data['update_message'] ) && is_string( $data['update_message'])) $this->update_message = $data['update_message'];
			if (isset( $data['file'] ) && is_string( $data['file'])) $this->file = $data['file'];

			$this->updater = new mwm_updater( $this );
			$this->updater->init();
        }

        /**
         * Return an object with plugin info
         * 
         * @since      1.0.0
         *
         * @return array
        */
        public function get_info($properties = '')
        {
            $object = new stdClass();

            if (is_array($properties) && sizeof($properties) > 0) {
                foreach ($properties as $key => $property) {
                    if (property_exists($this, $property)) {
                        $object->$property = $this->$property;
                    }
                }
            } else if ($properties != '') {
                if (property_exists($this, $properties)) {
                    $object = $this->$properties;
                } else {
                    $object = false;
                }
            } else {
                $object->slug 			= $this->slug;
                $object->name 			= $this->name;
                $object->version 		= $this->version;
                $object->pro			= $this->pro;
                $object->update_message	= $this->update_message;
                $object->file 			= $this->file;
                $object->notifications 	= $this->notifications;
				$object->updater 		= $this->updater;
            }
            
            return $object;
        }

		/**
		 * Return if is validated or not
		 *
		 * @return boolean
		 */
		public function is_activated()
		{
			return $this->updater->is_activated();
		}

        /**
         * Add a notification to the notifications array
         * 
         * @since      1.0.0
         *
         * @return array
        */
        public function add_notification($notification)
        {
            if (!is_a($notification, 'mwm_notification')) return false;
            return array_push($this->notifications, $notification);
        }
    }
}