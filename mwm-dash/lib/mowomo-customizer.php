<?php

/**
 * Control para mostrar textos
 */
if ( class_exists( 'WP_Customize_Control' ) ) {

    class mwm_Label_Control extends WP_Customize_Control {

        public $type = 'mwm_label_control';

        private $tag = 'p';

        /**
		 * Constructor
		 */
		public function __construct( $manager, $id, $args = array(), $options = array() ) {

            parent::__construct( $manager, $id, $args );
            
			// Check if this is a multi-select field
			if ( isset( $this->input_attrs['tag'] ) && $this->input_attrs['tag'] ) {
				$this->tag =  $this->input_attrs['tag'];
            }
            
		}

        /**
         * Render
         */
        public function render_content() {

            ?>
                <div class="mwm-label-control">
                    <?php echo '<'.$this->tag.'>'.$this->label.'</'.$this->tag.'>'; ?>
                </div>
            <?php

        }

    }

    function mwm_label_control_display( array $args ) {
    
        $customizer = $args['customizer'];
        $section = $args['section'];
        $setting = $args['setting'];
        $label = $args['label'];
        $tag = $args['tag'];

        $customizer->add_setting( $setting, array(
            'default' => '',
            'type' => 'mwm_label_control',
            'capability' => 'edit_theme_options',
            'transport' => 'refresh',
        ));

        $customizer->add_control( new mwm_Label_Control( $customizer, $setting, array(
            'label' => $label,
            'section' => $section,
            'settings' => $setting,
            'input_attrs' => array(
                'tag' => $tag,
            ),
        )));

    }

}