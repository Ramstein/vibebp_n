<?php

defined( 'ABSPATH' ) or die();


//Scope => My , public, group,
// Contenxt => information: select dropdown, member card, groups directory, full profile view
if ( ! class_exists( 'Vibe_BP_API_Rest_Groups_Controller' ) ) {
	
	class Vibe_BP_API_Rest_Groups_Controller extends WP_REST_Controller{

		public static $instance;
		public static function init(){
	        if ( is_null( self::$instance ) )
	            self::$instance = new Vibe_BP_API_Rest_Groups_Controller();
	        return self::$instance;
	    }
	    public function __construct( ) {
			$this->namespace = Vibe_BP_API_NAMESPACE;
			$this->type= Vibe_BP_API_GROUPS_TYPE;
			$this->register_routes();
		}
		/**
		 * Register the routes for the objects of the controller.
		 *
		 * @since 3.0.0
		 */
		public function register_routes() {

			register_rest_route( $this->namespace, '/'.$this->type, array(
				array(
					'methods'             => 'POST',
					'callback'            =>  array( $this, 'get_groups' ),
					'permission_callback' => array( $this, 'get_groups_permissions' ),
				),
			));

			
			
			register_rest_route( $this->namespace, '/group_card/(?P<group_id>\d+)', array(
				array(
					'methods'             =>  'POST',
					'permission_callback' => array( $this, 'get_groups_permissions' ),
					'callback'            =>  array( $this, 'get_group_card' ),
				),
			));

			register_rest_route( $this->namespace, '/'.$this->type.'/invites', array(
				array(
					'methods'             => 'POST',
					'callback'            =>  array( $this, 'get_groups_invites' ),
					'permission_callback' => array( $this, 'get_groups_permissions' ),
				),
			));
			register_rest_route( $this->namespace, '/'.$this->type .'/(?P<group_id>\d+)?', array(
				array(
					'methods'             =>  'POST',
					'callback'            =>  array( $this, 'vibe_bp_api_get_group_by_id' ),
					'permission_callback' => array( $this, 'get_groups_permissions' ),
					'args'                     	=>  array(
						'id'                       	=>  array(
							'validate_callback'     =>  function( $param, $request, $key ) {
														return is_numeric( $param );
													}
						),
					),
				),
			));

			register_rest_route( $this->namespace, '/'.$this->type .'/member_actions', array(
				array(
					'methods'             =>  'POST',
					'callback'            =>  array( $this, 'group_member_actions' ),
					'permission_callback' => array( $this, 'get_groups_permissions' ),
				),
			));
			
			
			register_rest_route( $this->namespace, '/'.$this->type .'/create_update_group/', array(
				array(
					'methods'             =>  'POST',
					'callback'            =>  array( $this, 'vibe_bp_api_groups_create_group'),
					'permission_callback' => array( $this, 'create_group_permissions' ),
					'args'                     	=>  array(
						'id'                       	=>  array(
							'validate_callback'     =>  function( $param, $request, $key ) {
														return is_numeric( $param );
													}
						),
					),
				),
			));

			register_rest_route( $this->namespace,'/'.$this->type . '/delete_group/(?P<group_id>\d+)?', array(
				array(
					'methods'             =>  'POST',
					'callback'            =>  array( $this, 'vibe_bp_api_groups_delete_group'),
					'permission_callback' => array( $this, 'get_groups_permissions' ),
					'args'                     	=>  array(
						'id'                       	=>  array(
							'validate_callback'     =>  function( $param, $request, $key ) {
														return is_numeric( $param );
													}
						),
					),
				),
			));
			
			register_rest_route( $this->namespace,'/'.$this->type . '/join_group/(?P<group_id>\d+)/', array(
				array(
					'methods'             =>  'POST',
					'callback'            =>  array( $this, 'vibe_bp_api_groups_join_group'),
					'permission_callback' => array( $this, 'get_groups_permissions' ),
					'args'                     	=>  array(
						'group_id'                       	=>  array(
							'validate_callback'     =>  function( $param, $request, $key ) {
														return is_numeric( $param );
													}
						),
					),
				),
			));

			register_rest_route( $this->namespace,'/'.$this->type . '/invite_member/(?P<group_id>\d+)/', array(
				array(
					'methods'             =>  'POST',
					'callback'            =>  array( $this, 'vibe_bp_api_groups_invite_member'),
					'permission_callback' => array( $this, 'get_groups_permissions' ),
					'args'                     	=>  array(
						'group_id'                       	=>  array(
							'validate_callback'     =>  function( $param, $request, $key ) {
														return is_numeric( $param );
													}
						),
					),
				),
			));

			register_rest_route( $this->namespace, '/'.$this->type .'/leave_group/(?P<group_id>\d+)?/(?P<user_id>\d+)?', array(
				array(
					'methods'             =>  'POST',
					'callback'            =>  array( $this, 'vibe_bp_api_groups_leave_group'),
					'permission_callback' => array( $this, 'get_groups_permissions' ),
					'args'                     	=>  array(
						'id'                       	=>  array(
							'validate_callback'     =>  function( $param, $request, $key ) {
														return is_numeric( $param );
													}
						),
					),
				),
			));

			register_rest_route( $this->namespace,'/'.$this->type . '/members/(?P<group_id>\d+)?', array(
				array(
					'methods'             =>  'POST',
					'callback'            =>  array( $this, 'vibe_bp_api_groups_get_group_members'),
					'permission_callback' => array( $this, 'get_groups_permissions' ),
					'args'                     	=>  array(
						'id'                       	=>  array(
							'validate_callback'     =>  function( $param, $request, $key ) {
														return is_numeric( $param );
													}
						),
					),
				),
			));

			register_rest_route( $this->namespace,'/groups/user/(?P<user_id>\d+)/get_items', array(
				array(
					'methods'             =>  'POST',
					'callback'            =>  array( $this, 'get_members_groups'),
					'permission_callback' => array( $this, 'get_groups_permissions' ),
					'args'                     	=>  array(
						'user_id'                       	=>  array(
							'validate_callback'     =>  function( $param, $request, $key ) {
														return is_numeric( $param );
													}
						),
					),
				),
			));
			register_rest_route( $this->namespace,'/'.$this->type . '/user/(?P<user_id>\d+)/get_items', array(
				array(
					'methods'             =>  'POST',
					'callback'            =>  array( $this, 'get_members_groups'),
					'permission_callback' => array( $this, 'get_groups_permissions' ),
					'args'                     	=>  array(
						'user_id'                       	=>  array(
							'validate_callback'     =>  function( $param, $request, $key ) {
														return is_numeric( $param );
													}
						),
					),
				),
			));


			register_rest_route( $this->namespace,'/'.$this->type . '/invite/(?P<group_id>\d+)/', array(
				array(
					'methods'             =>  'POST',
					'callback'            =>  array( $this, 'accept_reject_invite'),
					'permission_callback' => array( $this, 'get_groups_permissions' ),
					'args'                     	=>  array(
						'group_id'                       	=>  array(
							'validate_callback'     =>  function( $param, $request, $key ) {
														return is_numeric( $param );
													}
						),
					),
				),
			));


		}


		/*
	    PERMISSIONS
	     */
	    function get_groups_permissions($request){

	    	$body = json_decode($request->get_body(),true);
	       	
	        if (empty($body['token'])){
	           	$client_id = $request->get_param('client_id');
	           	if($client_id == vibebp_get_setting('client_id')){
	           		return true;
	           	}
	        }else{
	        	$token = $body['token'];
	        }
	        /** Get the Secret Key */
	        $secret_key = defined('JWT_AUTH_SECRET_KEY') ? JWT_AUTH_SECRET_KEY : false;
	        if (!$secret_key) {
	          	return false;
	        }
	        /** Try to decode the token */ /** Else return exception*/
	        try {
	            $user_data = JWT::decode($token, $secret_key, array('HS256'));
	            /*
		        avatar: "//www.gravatar.com/avatar/73745bceffd75a7e5a1203d9f0e9fe44?s=150&#038;r=g&#038;d=mm"
				caps: ["subscriber"]
				displayname: "test"
				email: "q@q.com"
				id: "2"
				profile_link: "http://localhost/appointments/members/test"
				slug: "test"
				username: "test"*/
		        $this->user = $user_data->data->user;
		        /** Let the user modify the data before send it back */
	        	return true;

	        }catch (Exception $e) {
	            /** Something is wrong trying to decode the token, send back the error */
	            return false;
	        }
	    	

	    	return false;
	    }

	    function create_group_permissions($request){
	    	$body = json_decode(stripslashes($_POST['body']),true);
	       	
	        if (empty($body['token'])){
	           	return false;
	        }else{
	        	$token = $body['token'];
	        }
	        /** Get the Secret Key */
	        $secret_key = defined('JWT_AUTH_SECRET_KEY') ? JWT_AUTH_SECRET_KEY : false;
	        if (!$secret_key) {
	          	return false;
	        }
	        /** Try to decode the token */ /** Else return exception*/
	        try {
	            $user_data = JWT::decode($token, $secret_key, array('HS256'));
	            /*
		        avatar: "//www.gravatar.com/avatar/73745bceffd75a7e5a1203d9f0e9fe44?s=150&#038;r=g&#038;d=mm"
				caps: ["subscriber"]
				displayname: "test"
				email: "q@q.com"
				id: "2"
				profile_link: "http://localhost/appointments/members/test"
				slug: "test"
				username: "test"*/
		        $this->user = $user_data->data->user;
		        /** Let the user modify the data before send it back */
	        	return true;

	        }catch (Exception $e) {
	            /** Something is wrong trying to decode the token, send back the error */
	            return false;
	        }
	    	

	    	return false;
	    }

	    function bp_user_can_create_groups() {
			// Super admin can always create groups.
			if ( user_can($this->user->id,'manage_options')) {
				return true;
			}

			// Get group creation option, default to 0 (allowed).
			$restricted = (int) bp_get_option( 'bp_restrict_group_creation', 0 );

			// Allow by default.
			$can_create = true;

			// Are regular users restricted?
			if ( $restricted ) {
				$can_create = false;
			}

			/**
			 * Filters if the current logged in user can create groups.
			 *
			 * @since 1.5.0
			 *
			 * @param bool $can_create Whether the person can create groups.
			 * @param int  $restricted Whether or not group creation is restricted.
			 */
			
			return apply_filters( 'vibebp_user_can_create_groups', $can_create, $restricted ,$this->user->id);
		}

    	function get_groups($request){
    		$args = json_decode(file_get_contents('php://input'));

    		$args = apply_filters( 'vibe_bp_api_get_groups_args', $args, $request);
    		//print_R($args);

    		
    		$run = groups_get_groups($args); 
    		
    		if( count($run['groups']) ) {

    			foreach($run['groups'] as $k=>$group){
    				$run['groups'][$k]->avatar = bp_core_fetch_avatar(array(
                            'item_id' => $run['groups'][$k]->id,
                            'object'  => 'group',
                            'type'=> empty($args->full_avatar)?'thumb':'full',
                            'html'    => false
                        ));
    				$run['groups'][$k]->url = bp_get_group_permalink($group);
    			}

    	    	$data=array(
	    			'status' => 1,
	    			'data' => $run,
	    			'message' => _x('Groups Exist','Groups Exist','vibebp')
	    		);
    	    }else{
    	    	$data=array(
	    			'status' => 1,
	    			'data' => $run,
	    			'message' => _x('Groups Not Exist','Groups Not Exist','vibebp')
	    		);
    	    }

    	    $data['can_create_groups'] = $this->bp_user_can_create_groups();

    		$data = apply_filters( 'vibe_bp_api_get_groups', $data, $args, $request);
			return new WP_REST_Response( $data, 200 );  
    	}

    	function get_group_card($request){
    		$group_id = $request->get_param('group_id');
    		$layouts = new WP_Query(apply_filters('vibebp_group_card',array(
				'post_type'=>'group-card',
				'posts_per_page'=>1,
				'meta_query'=>array(
					'relation'=>'AND',
					array(
						'key'=>'group_type',
						'compare'=>'NOT EXISTS'
					)
				)
			),$group_id));
			$init = VibeBP_Init::init();
			$init->group_id = $group_id;
			ob_start();

			if($layouts->have_posts()){
				while($layouts->have_posts()){
					$layouts->the_post();
					the_content();
				}
			}else{
				$layouts = new WP_Query(array(
					'post_type'=>'group-card',
					'posts_per_page'=>1,
					'meta_query'=>array(
						'relation'=>'AND',
						array(
							'key'=>'group_type',
							'compare'=>'NOT EXISTS'
						)
					)
				));
				while($layouts->have_posts()){
					$layouts->the_post();
					the_content();
				}
			}
			return ob_get_clean();
    	}

    	function get_groups_invites($request){

    		$args = json_decode($request->get_body(),true);

    		$args = apply_filters( 'vibe_bp_api_get_groups_args', $args, $request);

    		

    		if($args['accepted'] == 'requests'){
    			$pending_invites = groups_get_requests( array(
					'user_id'  => $this->user->id,
					'page'     => $args['page'],
					'per_page' => 12,
				) );
				
    		}
    		if($args['accepted'] == 'pending'){
    			$pending_invites = groups_get_invites( array(
					'user_id'  => $this->user->id,
					'page'     => $args['page'],
					'per_page' => 12,
				) );
    			$run['total']=groups_get_invite_count_for_user($this->user->id);
    		}

    		if($args['accepted'] == 'pending_sent_invitation'){
			
				$pending_invites =  groups_get_invites( array(
					'inviter_id'  => $user_id,
					'page'        => $r['page'],
					'per_page'    => $r['per_page'],
				) );
			}
			

    		$run = array();
			

    		if(!empty($pending_invites)){

    			

    			foreach($pending_invites as $invite){
    				$group = groups_get_group($invite->item_id);

    				if(!is_wp_error($group) && !empty($group)){
    					
    					$invite->date_modified = strtotime($invite->date_modified);
    					$invite->item=$group;
    					$invite->item->avatar =bp_core_fetch_avatar(array(
		                            'item_id' => $invite->item_id,
		                            'object'  => 'group',
		                            'type'=>'thumb',
		                            'html'    => false
		                        ));
    						
    					$run['invites'][] = $invite;
    				}
    			}

    		}
    		

    		if( !empty($run) ) {
    	    	$data=array(
	    			'status' => 1,
	    			'data' => $run,
	    			'message' => _x('Invites Exist','Groups Exist','vibebp')
	    		);
    	    }else{
    	    	$data=array(
	    			'status' => 0,
	    			'data' => $run,
	    			'message' => _x('Invites empty','Invites Not Exist','vibebp')
	    		);
    	    }
    		$data = apply_filters( 'vibe_bp_api_get_groups', $data, $args, $request);
			return new WP_REST_Response( $data, 200 );   
    	}
/*
*	Get Group by group_id
*/
    	function vibe_bp_api_get_group_by_id($request){

    		$group_id = (int)$request->get_param('group_id');
    		$args = json_decode($request->get_body(),true);
    		$data = '';
    		if($args['context'] === 'meta'){
    			$data = groups_get_groupmeta( $group_id, '', false);
    		}

    		$tabs = apply_filters('vibebp_group_tabs',array(
                    'home'=>_x('Home','groups','vibebp'),
                ),$group_id,$this->user->id);

    		$forums = groups_get_groupmeta($group_id,'forum_id',true);
    		if(!empty($forums) && is_array($forums)){
    			$link = '#component=forums&action=forums&id='.$forums[0];
    			$tabs[$link]=_x('Forum','groups','vibebp');
    		}

    		$meta['is_admin'] = $meta['can_add_members'] = $meta['can_invite'] = false;
    	
    		if(user_can('manage_options',$this->user->id) ){
    			$meta['is_admin']  = $meta['can_add_members'] = true;
    		}else{
    			$admins = groups_get_group_admins($group_id);
    			if(!empty($admins)){
					foreach ($admins as $key => $mod) {
						if($mod->user_id==$this->user->id){
							$meta['is_admin']  = $meta['can_add_members'] = true;
							
							break;
						}
					}
				}
				if ( $group_mods = groups_get_group_mods( $group->id ) ) {
					foreach ( (array) $group_mods as $mod ){
						if($mod->user_id==$this->user->id){
							$meta['is_admin']  = $meta['can_add_members'] = true;
							
							break;
						}
					}
	    		}
    		}
    		$invite_status = groups_get_groupmeta($group_id,'invite_status',true);
    		

			if(!empty($invite_status)){
				switch ($invite_status) {
					case 'admins':
						if($meta['is_admin']){
							$meta['can_invite'] = true;
						}
						break;
					case 'mods':
						$mods = groups_get_group_mods( $group_id );
						if(!empty($mods)){
							foreach ($mods as $key => $mod) {
								if($mod->user_id==$this->user->id){
									$meta['can_invite'] = true;
									break;
								}
							}
						}
						if($meta['is_admin']){
							$meta['can_invite'] = true;
						}
						break;
					case 'members':
						$meta['can_invite'] = true;

					break;
					default:
						
						break;
				}
			}
		
    		
    		$meta['is_admin'] = apply_filters('vibebp_groups_api_is_admin',$meta['is_admin'],$group_id,$this->user->id);
    		$meta['can_add_members'] = apply_filters('vibebp_groups_api_can_add_members',$meta['can_add_members'],$group_id,$this->user->id);
    		$meta['can_invite'] = apply_filters('vibebp_groups_api_can_invite',$meta['can_invite'],$group_id,$this->user->id);
    		$data = array_merge($data,$meta);


			if( $group_id ){
    	    	$data=array(
	    			'status' => 1,
	    			'data' => $data,
	    			'tabs' => $tabs,
	    			'message' => _x('Group Exist','Group Exist','vibebp')
	    		);
    	    }else{
    	    	$message = 
    	    	$data=array(
	    			'status' => 0,
	    			'data' => $data,
	    			'tabs' => $tabs,
	    			'message' => _x('No Data for Group','Group Not Exist','vibebp')
	    		);
    	    }	
    	    $data=apply_filters( 'vibe_bp_api_get_group_by_id', $data, $request);
			return new WP_REST_Response( $data, 200 );   
    	}

    	function group_member_actions($request){

    		$args = json_decode($request->get_body(),true);
    		
    		if(empty($args['members'])){
    			return new WP_REST_Response( array('status'=>0,'message'=>__('No members selected','vibebp')), 200 );   
    		}
    		$group_id = $args['group_id'];
    		$members = $args['members'];
    		switch($args['action']){
    			case 'remove':
    				foreach($members as $user_id){
    					$member = new BP_Groups_Member( $user_id, $group_id );	
    					do_action( 'groups_remove_member', $group_id, $user_id );
    					$member->remove();
    					$args['action']= __('Member removed','group action','vibebp');
    				}
    			break;
    			case 'ban':
    				foreach($members as $user_id){
    					$member = new BP_Groups_Member( $user_id, $group_id );
						do_action( 'groups_ban_member', $group_id, $user_id );
						$member->ban();
						$args['action']= __('Member banned','group action','vibebp');
					}
    			break;
    			case 'unban':
    				foreach($members as $user_id){
    					$member = new BP_Groups_Member( $user_id, $group_id );
						do_action( 'groups_unban_member', $group_id, $user_id );
						$member->unban();
						$args['action']= __('Member unbanned','group action','vibebp');
					}
    			break;
    			case 'change_role_member':
    				foreach($members as $user_id){
    					$member = new BP_Groups_Member( $user_id, $group_id );
						do_action( 'groups_demote_member', $group_id, $user_id );
						$member->demote();
						$args['action']= __('Member demoted','group action','vibebp');
					}
    			break;
    			case 'change_role_moderator':
    				foreach($members as $user_id){
    					$member = new BP_Groups_Member( $user_id, $group_id );
						do_action( 'groups_promote_member', $group_id, $user_id, $status );
						$member->promote( 'mod' );
						$args['action']= __('Member promoted','group action','vibebp');
					}
    			break;
    			case 'change_role_admin':
    				foreach($members as $user_id){
    					$member = new BP_Groups_Member( $user_id, $group_id );
						do_action( 'groups_promote_member', $group_id, $user_id, $status );
						$member->promote( 'admin' );
						$args['action']= __('Member converted to administrator ','group action','vibebp');
					}
    			break;
    			case 'accept_request':
    					$invitation = new BP_Groups_Invitation_Manager();
    					if(!empty($members)){
    						foreach($members as $user_id){
								$invitation->run_acceptance_action('request',array('user_id'=>$user_id,'item_id'=>$group_id));
							}
						}
						$args['action']= __('Membership accepted ','group action','vibebp');
    			break;
    			case 'reject_request':
    				if(!empty($members)){
    					foreach($members as $user_id){
    						groups_reject_membership_request('', $user_id, $group_id);
    					}
    				}
    				
    				$args['action']= __('Membership rejected ','group action','vibebp');
    			break;
    		}

    		return new WP_REST_Response( array('status'=>1,'message'=>sprintf(__('%s performed on %d selected members','vibebp'),$args['action'],count($args['members'])) ), 200 );   
		}


    	function vibe_bp_api_groups_create_group($request){
    		
    		$args = json_decode(stripslashes($_POST['body']),true);
    		$group_args = array(
    			'name'=> $args['name'],
    			'slug'=> sanitize_title_with_dashes($args['name']),
    			'description'=>$args['description'],
    			'status' => $args['status'],
    			'creator_id' => $this->user->id
    		);

    		if(!empty($args['id'])){
    			$group_args['group_id'] = $args['id'];
    		}


		    $group_args=apply_filters( 'vibe_bp_api_groups_create_group_args', $group_args, $request);
		    
			$group_id = groups_create_group($group_args);

			if(!empty($args['group_type'])){
    			bp_groups_set_group_type($group_id, $args['group_type'] );
    		}

			if ( bp_is_active( 'activity' ) ) {
				if(!empty($group_args['group_id'])){
					groups_record_activity( array(
						'type' => 'group_details_updated',
						'item_id' => $group_id,
						'user_id' => $this->user->id
					) );
				}else{
					groups_record_activity( array(
						'type' => 'created_group',
						'item_id' => $group_id,
						'user_id' => $this->user->id
					) );
				}
			}

			$this->group_id = $group_id;

			add_filter('bp_get_current_group_id',array($this,'set_group_id'));
			

			$run = false;
			if(is_numeric($group_id)){

				if(!empty($args['invite_status'])){
					groups_update_groupmeta( $group_id, 'invite_status', $args['invite_status'] );
				}
				// assign to coure or forum
				if(!empty($args['meta'])){
					foreach($args['meta'] as $meta){
						groups_update_groupmeta( $group_id, $meta['meta_key'], $meta['meta_value'] );
					}
				}
				$bp = buddypress();
				//Asign avatr
				if ( ! isset( $bp->avatar_admin ) ) {
					$bp->avatar_admin = new stdClass();
				}
				

				if ( !empty( $_FILES )  ) {
					add_filter('bp_attachment_upload_overrides',function($overrides){
						$overrides['test_form'] = FALSE;
						return $overrides;
					});
 					

					if ( bp_core_avatar_handle_upload( $_FILES, 'groups_avatar_upload_dir' ) ) { 
						// Normally we would check a nonce here, but the group save nonce is used instead.
						$cropargs = array(
							'object'        => 'group',
							'avatar_dir'    => 'group-avatars',
							'item_id'       => $group_id,
							'original_file' => $bp->avatar_admin->image->url,
							'crop_x'        => $args['avatar']['cropdata']['x'],
							'crop_y'        => $args['avatar']['cropdata']['y'],
							'crop_w'        => $args['avatar']['cropdata']['width'],
							'crop_h'        => $args['avatar']['cropdata']['height']
						);
						vibebp_avatar_handle_crop($cropargs,$this->user->id);
						
					}
				}

			    //send invites

				if(!empty($args['invitees'])){

					foreach ( $args['invitees'] as $user_id ) {
						groups_invite_user( array( 
							'user_id'  => $user_id,
							'group_id' => $group_id,
						) );
					}
					groups_send_invites( array(	'group_id' => $group_id ) );
				}

				$run =groups_get_group( $group_id );
				$run->avatar =bp_core_fetch_avatar(array(
                            'item_id' 	=> $group_id,
                            'object'  	=> 'group',
                            'type'		=>'thumb',
                            'html'    	=> false
                        ));
			}

			if( $run ){
    	    	$data=array(
	    			'status' => 1,
	    			'data' => $run,
	    			'message' => _x('Group Created','Group Created','vibebp')
	    		);
    	    }else{
    	    	$data=array(
	    			'status' => 0,
	    			'data' => $run,
	    			'message' => _x('Group Not Created','Group Not Created','vibebp')
	    		);
    	    }
    		$data=apply_filters( 'vibe_bp_api_groups_create_group', $data, $request ,$args );
			return new WP_REST_Response( $data, 200 );   
    	}

    	function set_group_id($gid){
			return $this->group_id;
		}


    	function vibe_bp_api_groups_delete_group($request){
			$group_id = (int)$request->get_param('group_id');	 // get param data 'group_id
			$run=groups_delete_group( $group_id );
    	   
    	    if( $run ){
    	    	$data=array(
	    			'status' => 1,
	    			'data' => $run,
	    			'message' => _x('Group Deleted','Group Deleted','vibebp')
	    		);
    	    }else{
    	    	$data=array(
	    			'status' => 0,
	    			'data' => $run,
	    			'message' => _x('Group Not Deleted','Group Not Deleted','vibebp')
	    		);
    	    }

    		$data=apply_filters( 'vibe_bp_api_groups_delete_group', $data ,$request);
			return new WP_REST_Response( $data, 200 );  
    	}

    	function vibe_bp_api_groups_join_group($request){

    		$args = json_decode(file_get_contents('php://input'));
    		$group_id = (int)$request->get_param('group_id');	 // get param data 'group_id
    		$members = $args->invitees;$run=0;
    		if(!empty($members)){
    			foreach ($members as $key => $user_id) {
    				$run = groups_join_group( $group_id, $user_id);
    			}
    		}
    		

    		if( $run ){
    	    	$data=array(
	    			'status' => 1,
	    			'data' => $run,
	    			'message' => _x('Group Joined','Group Joined','vibebp')
	    		);
    	    }else{
    	    	$data=array(
	    			'status' => 0,
	    			'data' => $run,
	    			'message' => _x('Group Not Joined','Group Not Joined','vibebp')
	    		);
    	    }
    		$data=apply_filters( 'vibe_bp_api_groups_join_group', $data ,$request);
			return new WP_REST_Response( $data, 200 );  
    	}

    	function vibe_bp_api_groups_invite_member($request){

    		$args = json_decode($request->get_body(),true);
    		$group_id = (int)$request->get_param('group_id');	 // get param data 'group_id

    		if(!empty($args['invitees'])){

				foreach ( $args['invitees'] as $user_id ) {
					groups_invite_user( array( 
						'user_id'  => $user_id,
						'group_id' => $group_id,
					) );
				}
				groups_send_invites( array(	'group_id' => $group_id ) );
			}

			$data=array(
    			'status' => 1,
    			'message' => _x('Users Invited','invited for group','vibebp')
    		);
			return new WP_REST_Response( $data, 200 );  
    	}


    	function vibe_bp_api_groups_leave_group($request){
    		$args = json_decode(file_get_contents('php://input'));
    		$group_id = (int)$request->get_param('group_id');	 // get param data 'group_id
    		$user_id = (int)$request->get_param('user_id');	 // get param data 'user_id'

    		$run = groups_leave_group( $group_id, $user_id);
    		if( $run ){
    	    	$data=array(
	    			'status' => 1,
	    			'data' => $run,
	    			'message' => _x('Group leaved','Group leaved','vibebp')
	    		);
    	    }else{
    	    	$data=array(
	    			'status' => 0,
	    			'data' => $run,
	    			'message' => _x('Group Not leaved','Group Not leaved','vibebp')
	    		);
    	    }
    		$data=apply_filters( 'vibe_bp_api_groups_leave_group', $data ,$request);
			return new WP_REST_Response( $data, 200 );  
    	}

    	function vibe_bp_api_groups_get_group_members($request){

 
    		$args = json_decode($request->get_body(),true);
    		$group_id = (int)$request->get_param('group_id');	 // get param data 'group_id

    		$group_args = array(
    			'group_id'=>$group_id,
    			'per_page'=>10,
    			'page'=>1,
    			'exclude_admins_mods'=>false,
    			'group_role'=>array($args['role']), //'admin', 'mod', 'member', 'banned'
    			'search_terms'=>$args['search_terms'],
    			'type'=>'last_joined'
    		);

    		$run = array();
			if($args['role'] == 'invited'){
				$invites = groups_get_invites( array('item_id'=> $group_id));

				if(!empty($invites)){
					$run = array('members'=>array());
					foreach($invites as $invite){
						$run['members'][]=array(
							'ID'=>$invite->user_id,
							'display_name'=> bp_core_get_user_displayname($invite->user_id)
						);
					}
					$run['count']=count($invites);
				}else{
					$run['members']=[];
					$run['count']=0;
				}
			}else if($args['role'] == 'requests'){
				$user_ids = groups_get_membership_requested_user_ids($group_id);

				if(!empty($user_ids)){
					$run = array('members'=>array());
					foreach($user_ids as $user_id){
						$run['members'][]=(object)array(
							'ID'=>$user_id,
							'display_name'=> bp_core_get_user_displayname($user_id)
						);
					}
					$run['count']=count($user_ids);
				}else{
					$run['members']=[];
					$run['count']=0;
				}
			}else{
				$run = groups_get_group_members( $group_args );
			}
    		

    		if(!empty($run['members'])){
    			foreach($run['members'] as $key => $user){
					$run['members'][$key]->avatar = bp_core_fetch_avatar(array(
                            'item_id' 	=> $user->ID,
                            'object'  	=> 'user',
                            'type'		=>'thumb',
                            'html'    	=> false
                    ));
                    
					
				}
			}

    		if( $run ){
    	    	$data=array(
	    			'status' => 1,
	    			'data' => $run,
	    			'message' => _x('Group Members','Group Members','vibebp')
	    		);
    	    }else{
    	    	$data=array(
	    			'status' => 0,
	    			'data' => $run,
	    			'message' => _x('Group Not Members','Group Not Members','vibebp')
	    		);
    	    }

    	    $meta['is_admin'] = $meta['can_add_members'] = $meta['can_invite'] = false;
    	    $meta['member_actions']=[];
    		if(user_can($this->user->id,'manage_options')){
    			$meta['is_admin']  = $meta['can_add_members'] = true;
    			$meta['member_actions']=array(
					'remove'=>__('Remove member','vibebp'),
					'ban'=>__('Ban member','vibebp'),
					'unban'=>__('Un Ban member','vibebp'),
					'change_role_member'=>__('Set Member','vibebp'),
					'change_role_moderator'=>__('Set Moderator','vibebp'),
					'change_role_admin'=>__('Set Adminitrator','vibebp'),
					'uninvite_member'=>__('Remove Invite','vibebp'),
				);

				if($args['role'] == 'requests'){
					$meta['member_actions']['accept_request']=__('Accept request','vibebp');
					$meta['member_actions']['reject_request']=__('Reject request','vibebp');
				}
    		}else{
    			$admins = groups_get_group_admins($group_id);
    			if(!empty($admins)){
					foreach ($admins as $key => $mod) {
						if($mod->user_id==$this->user->id){
							$meta['is_admin']  = $meta['can_add_members'] = true;
							$meta['member_actions']=array(
			    				'remove'=>__('Remove member','vibebp'),
			    				'ban'=>__('Ban member','vibebp'),
			    				'unban'=>__('Un Ban member','vibebp'),
			    				'change_role_member'=>__('Set Member','vibebp'),
			    				'change_role_moderator'=>__('Set Moderator','vibebp'),
			    				'change_role_admin'=>__('Set Adminitrator','vibebp'),
			    				'uninvite_member'=>__('Remove Invite','vibebp'),
			    			);

			    			if($args['role'] == 'requests'){
								$meta['member_actions']['accept_request']=__('Accept request','vibebp');
								$meta['member_actions']['reject_request']=__('Reject request','vibebp');
							}
							break;
						}
					}
				}

				if ( $group_mods = groups_get_group_mods( $group->id ) ) {
					foreach ( (array) $group_mods as $mod ){
						if($mod->user_id==$this->user->id){
							$meta['is_admin']  = false;
							$meta['is_mod']  = false;$meta['can_add_members'] = true;
							$meta['member_actions']=array(
			    				'remove'=>__('Remove member','vibebp'),
			    				'ban'=>__('Ban member','vibebp'),
			    				'unban'=>__('Un Ban member','vibebp'),
			    				'uninvite_member'=>__('Remove Invite','vibebp'),
			    			);
							break;
						}
					}
	    		}
    		}
    		$invite_status = groups_get_groupmeta($group_id,'invite_status',true);
    		

			if(!empty($invite_status)){
				switch ($invite_status) {
					case 'admins':
						if($meta['is_admin']){
							$meta['can_invite'] = true;

						}
						break;
					case 'mods':
						$mods = groups_get_group_mods( $group_id );
						if(!empty($mods)){
							foreach ($mods as $key => $mod) {
								if($mod->user_id==$this->user->id){
									$meta['can_invite'] = true;
									break;
								}
							}
						}
						if($meta['is_admin']){
							$meta['can_invite'] = true;
						}
						break;
					case 'members':
						$meta['can_invite'] = true;

					break;
					default:
						
						break;
				}
			}

    		$meta['is_admin'] = apply_filters('vibebp_groups_api_is_admin',$meta['is_admin'],$group_id,$this->user->id);
    		$meta['can_add_members'] = apply_filters('vibebp_groups_api_can_add_members',$meta['can_add_members'],$group_id,$this->user->id);
    		$meta['can_invite'] = apply_filters('vibebp_groups_api_can_invite',$meta['can_invite'],$group_id,$this->user->id);
    		$data['meta'] = $meta;

    		$data=apply_filters( 'vibe_bp_api_groups_get_group_members', $data ,$request);
			return new WP_REST_Response( $data, 200 );  
    	}

    	function accept_reject_invite($request){

    		$body = json_decode($request->get_body(),true);
    		$group_id = (int)$request->get_param('group_id');
    		$data = array(
    			'status'=>0,
    			'message'=>__('User not logged in','vibebp'),
    		);

    		if(empty($this->user)){
    			return new WP_REST_Response( $data, 200 );  
    		}

    		if($body['action'] === 'accept'){
    			$data['status']=1;
    			$data['message']= __('Invitation Accepted','vibebp');
    			groups_accept_invite( $this->user->id, $group_id );
    		}

    		if($body['action'] === 'reject'){
    			$data['status']=1;
    			$data['message']= __('Invitation Rejected','vibebp');
    			groups_reject_invite( $this->user->id, $group_id );
    		}

    		if($body['action'] === 'cancel' || $body['action'] === 'delete'){
    			$data['status']=1;
    			$data['message']= __('Invitation removed','vibebp');
    			groups_delete_invite( $this->user->id, $group_id);
    		}

    		return new WP_REST_Response($data, 200 );  
    	}

	}
}
Vibe_BP_API_Rest_Groups_Controller::init();
