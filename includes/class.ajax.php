<?php
/**
 * AjaxScripts
 *
 * @class       VibeBP_Register
 * @author      VibeThemes
 * @category    Admin
 * @package     VibeBp
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class VibeBP_Ajax{
	public static $instance;
	public static function init(){

        if ( is_null( self::$instance ) )
            self::$instance = new VibeBP_Ajax();
        return self::$instance;
    }

	private function __construct(){

		add_action('wp_ajax_nopriv_vibebp_wc_login',array($this,'vibebp_wc_login'));
		add_action('wp_ajax_nopriv_vibebp_wp_login',array($this,'vibebp_wp_login'));

		add_action('wp_ajax_nopriv_check_user_group_status',array($this,'check_user_group_status'));
		add_action('wp_ajax_check_user_group_status',array($this,'check_user_group_status'));

		add_action('wp_ajax_nopriv_join_user_group',array($this,'join_user_group'));
		add_action('wp_ajax_join_user_group',array($this,'join_user_group'));

		add_action('wp_ajax_nopriv_leave_user_group',array($this,'leave_user_group'));
		add_action('wp_ajax_leave_user_group',array($this,'leave_user_group'));
		
		add_Action('wp_ajax_request_user_group_membership',array($this,'request_user_group_membership'));
		add_Action('wp_ajax_nopriv_request_user_group_membership',array($this,'request_user_group_membership'));
	}

	function vibebp_wp_login(){

		if($_POST['client_id'] != vibebp_get_setting('client_id')){
			print_r(json_encode(array('status'=>0,'message'=>'Invalid client')));
			die();
		}
		if(!wp_verify_nonce($_POST['security'],'security')){
			print_r(json_encode(array('status'=>0,'message'=>'Invalid security')));
			die();
		}
		$token = $_POST['token'];
		/** Get the Secret Key */
        $secret_key = defined('JWT_AUTH_SECRET_KEY') ? JWT_AUTH_SECRET_KEY : false;

        if(!class_exists('JWT')){
        	include_once 'core/JWT.php';
        }
        //Tougher Security
		$secret_key = apply_filters('vibebp_tougher_security',$secret_key);
        if (!$secret_key) {
            $data = array(
	        	'status' => 0,
	            'data' => 'vibebp_jwt_secret_key_missing',
	            'message'=>_x('Secret key missing','Secret key missing','vibebp')
	        );
	        print_r(json_decode($data));
	        die();
        }
        /** Try to decode the token */ /** Else return exception*/
        try {
            $expanded_token = JWT::decode($token, $secret_key, array('HS256'));
            $expanded_token = apply_filters('vibebp_validate_token',$expanded_token,$token);
            if($expanded_token){
	            $data = array(
		        	'status' => 1, 
		            'data' => $expanded_token,
		            'message'=>_x('Valid Token','Valid Token','vibebp')
		        );
		        //potential security threat if token is captured by another user.
		        if(email_exists($expanded_token->data->user->email) && !user_can($expanded_token->data->user->id,'manage_options')){
		        	//only works for non-admins
		        	wp_set_auth_cookie($expanded_token->data->user->id,false);
		        	print_r(json_encode(apply_filters(VIBEBP.'jwt_auth_token_validate_before_dispatch', $data)));
		        	die();
		        }else{
		        	print_r(json_encode(array('status'=>0,'message'=>'Invalid user')));
		        	die();
		        }
		        
		        
	        }else{
	        	$data = array(
	        	'status' => 0,
	            'data' => 'jwt_auth_invalid_token',
		        );
		        print_r(json_encode($data));
		        die();
	        }
	        

        }catch (Exception $e) {
            $data = array(
	        	'status' => 0,
	            'data' => 'jwt_auth_invalid_token',
	            'message'=>$e->getMessage()
	        );
	        print_r(json_encode($data));
        }
		
		die();
	}

	function vibebp_wc_login(){

		if($_POST['client_id'] != vibebp_get_setting('client_id')){
			print_r(json_encode(array('status'=>0,'message'=>'Invalid client')));
			die();
		}
		if(!wp_verify_nonce($_POST['security'],'security')){
			print_r(json_encode(array('status'=>0,'message'=>'Invalid security')));
			die();
		}
		$token = $_POST['token'];
		/** Get the Secret Key */
        $secret_key = defined('JWT_AUTH_SECRET_KEY') ? JWT_AUTH_SECRET_KEY : false;

        if(!class_exists('JWT')){
        	include_once 'core/JWT.php';
        }
        //Tougher Security
		$secret_key = apply_filters('vibebp_tougher_security',$secret_key);
        if (!$secret_key) {
            $data = array(
	        	'status' => 0,
	            'data' => 'vibebp_jwt_secret_key_missing',
	            'message'=>_x('Secret key missing','Secret key missing','vibebp')
	        );
	        print_r(json_decode($data));
	        die();
        }
        /** Try to decode the token */ /** Else return exception*/
        try {
            $expanded_token = JWT::decode($token, $secret_key, array('HS256'));
            $expanded_token = apply_filters('vibebp_validate_token',$expanded_token,$token);
            if($expanded_token){
	            $data = array(
		        	'status' => 1, 
		            'data' => $expanded_token,
		            'message'=>_x('Valid Token','Valid Token','vibebp')
		        );
		        //potential security threat if token is captured by another user.
		        if(email_exists($expanded_token->data->user->email) && !user_can($expanded_token->data->user->id,'manage_options')){
		        	//only works for non-admins
		        	wp_set_auth_cookie($expanded_token->data->user->id,false);
		        	print_r(json_encode(apply_filters(VIBEBP.'jwt_auth_token_validate_before_dispatch', $data)));
		        	die();
		        }else{
		        	print_r(json_encode(array('status'=>0,'message'=>'Invalid user')));
		        	die();
		        }
		        
		        
	        }else{
	        	$data = array(
	        	'status' => 0,
	            'data' => 'jwt_auth_invalid_token',
		        );
		        print_r(json_encode($data));
		        die();
	        }
	        

        }catch (Exception $e) {
            $data = array(
	        	'status' => 0,
	            'data' => 'jwt_auth_invalid_token',
	            'message'=>$e->getMessage()
	        );
	        print_r(json_encode($data));
        }
		
		die();
	}

	function check_user_group_status(){

		if(!empty($_POST['token'])){
			$user = vibebp_expand_token($_POST['token']);
			if(!empty( $user['data']) && !empty($_POST['group_id'])){
				$user_id = $user['data']->data->user->id; 
				$group_id = $_POST['group_id'];
				$group_label = '';
				if(groups_is_user_member( $user_id, $group_id )){
					$group_label = __('Leave group','vibebp');
					echo json_encode(array('status'=>1,'user_status'=>'joined','group_label'=>$group_label));
				}else{
					if($_POST['status'] == 'public'){
						$group_label = __('Join group','vibebp');
					}
					if($_POST['status'] == 'private'){
						if(groups_is_user_pending( $user_id, $group_id )){
							$group_label = __('Pending Request','vibebp');
							$status = 'pending_request';
						}else{
							$group_label = __('Request Membership','vibebp');	
							$status = 'request_membership';
						}
						
					}
					echo json_encode(array('status'=>1,'user_status'=>$status,'group_label'=>$group_label));
				}
			}
		}

		die();
	}

	function join_user_group(){
		if(!empty($_POST['token'])){
			$user = vibebp_expand_token($_POST['token']);
			if(!empty( $user['data']) && !empty($_POST['group_id'])){
				$user_id = $user['data']->data->user->id; 
				$group_id = $_POST['group_id'];
				$group_label = '';
				if(groups_join_group( $group_id, $user_id)){
					echo json_encode(array('status'=>1,'user_status'=>'joined','group_label'=>__('Leave group','vibebp')));
				}
			}
		}
		die();
	}

	function leave_user_group(){
		if(!empty($_POST['token'])){
			$user = vibebp_expand_token($_POST['token']);
			if(!empty( $user['data']) && !empty($_POST['group_id'])){
				$user_id = $user['data']->data->user->id; 
				$group_id = $_POST['group_id'];
				$group_label = '';
				if(groups_leave_group( $group_id, $user_id)){
					echo json_encode(array('status'=>1,'user_status'=>'rejoin','group_label'=>__('Rejoingroup','vibebp')));
					die();
				}
			}
		}
		echo json_encode(array('status'=>0));
		die();
	}

	function request_user_group_membership(){
		if(!empty($_POST['token'])){
			$user = vibebp_expand_token($_POST['token']);
			if(!empty( $user['data']) && !empty($_POST['group_id'])){
				$user_id = $user['data']->data->user->id; 
				$group_id = $_POST['group_id'];
				$group_label = '';

				if(groups_send_membership_request(array(
					'user_id'       => $user_id,
					'group_id'      => $group_id))){
					echo json_encode(array('status'=>1,'user_status'=>'request_pending','group_label'=>__('Membership Requested','vibebp')));
				}
			}
		}
		die();
	}
}

VibeBP_Ajax::init();