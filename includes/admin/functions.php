<?php

// Security
if (!defined('ABSPATH')) exit;

// Include files
include_once mwmpc_INC.'admin/admin_pages.php';
include_once mwmpc_INC.'admin/maintenance.php';



add_action( 'rest_api_init', function () {
    register_rest_route( 'care/v1', '/set/', array(
        'methods' => 'POST',
        'callback' => 'mwmpc_api_set',
    ) );
});

function mwmpc_api_set($data){

    if(!empty($data['customer_name'])){

        update_option( 'mwm_customer_name', $data['customer_name'] );
        update_option( 'mwm_end_contract', $data['end_contract'] );
        update_option( 'mwm_hosting', $data['hosting'] );
        update_option( 'mwm_hosting_plan', $data['hosting_plan'] );
        update_option( 'mwm_url_plan', $data['url_plan'] );
        update_option( 'mwm_url_plan_img', $data['url_plan_img'] );
        update_option( 'mwm_contenido', $data['contenido'] );
        update_option( 'mwm_url_holded', $data['url_holded'] );
        update_option( 'mwm_email_contacto',$data['email_contacto'] );
        return 'Datos actualizados';
    }

}

add_action( 'rest_api_init', function(){
    register_rest_route( 'care/v1', '/send_email/', array(
        'methods' => 'GET',
        'callback' => 'mwmpc_send_support_email',
    ) );

});


function mwmpc_send_support_email($data){

    if(get_option( 'customer_name')){

        if($data['name']){

            $asunto = 'Nuevo contacto de soporte '. $_GET['name'] .' desde Plugin Care';
            $message = 
            'Se ha recibido una nueva petición de soporte desde el Plugin Care.'.'<br /><br />'.
            '<b>Datos de contacto de cliente:</b> '. $_GET['name'] .' - '. $_GET['email'] .''.'<br />'.
            '<b>Desde el sitio web:</b> '.get_site_url().'<br />'.
            '<b>Asunto del contacto:</b> '.urldecode($_GET['asunto']).'<br />'.
            '<b>Mensaje del usuario:</b> '.urldecode($_GET['message']);

            return wp_mail( 'soporte@mowomo.com', $asunto, $message , array('MIME-Version: 1.0','Content-Type: text/html', 'charset=UTF-8
            '));

        }
      
    } else {        
        return _e('You are not registered as a mowomo Care customer.','mwmpc');
    }
      

}