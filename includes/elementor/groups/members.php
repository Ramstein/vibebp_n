<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

 class VibeBP_Groups_Members extends \Elementor\Widget_Base  // We'll use this just to avoid function name conflicts 
{

    public function get_name() {
		return 'group_members';
	}

	public function get_title() {
		return __( 'Group Members', 'vibebp' );
	}

	public function get_icon() {
		return 'dashicons dashicons-businessman';
	}

	public function get_categories() {
		return [ 'vibebp' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Controls', 'vibebp' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		
		$this->add_control(
			'role',
			[
				'label' => __( 'Group Role', 'vibebp' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''=>__('All','vibebp'),
					'members' =>__('Members [Excluding Admin & Mods]','vibebp'),
					'mods' =>__('Moderators','vibebp'),
					'admin' =>__('Administrators','vibebp'),
				)
			]
		);

		$this->add_control(
			'style',
			[
				'label' => __( 'Style', 'vibebp' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					'' =>__('Default','vibebp'),
					'names' =>__('Names','vibebp'),
					'pop_names' =>__('Names Popup','vibebp'),
				)
			]
		);
	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		if(bp_get_current_group_id()){
			$group_id = bp_get_current_group_id();
		}else{
			global $groups_template;
			if(!empty($groups_template->group)){
				$group_id = $groups_template->group->id;
			}
		}
		if(empty($group_id)){
			$init = VibeBP_Init::init();
			if(!empty($init->group_id)){
				$group_id = $init->group_id;
			}
		}

		if(empty($group_id) && empty($_GET['action'])){
			$init = VibeBP_Init::init();
			if(empty($init->group_id)){
				global $wpdb,$bp;
				$group_id = $wpdb->get_var("SELECT id FROM {$bp->groups->table_name} LIMIT 0,1");
				$init->group_id = $group_id;
			}else{
				$group_id = $init->group_id;
			}
		}

		
		
		global $wpdb,$bp;

		
		$q = $wpdb->prepare("
			SELECT user_id as id FROM {$bp->groups->table_name_members} WHERE group_id = %d",$group_id);

		if($settings['role'] == 'members'){
			$q = $wpdb->prepare("
			SELECT user_id as id FROM {$bp->groups->table_name_members} WHERE group_id = %d AND is_mod !=1 AND is_admin !=1",$group_id);
		}
		if($settings['role'] == 'mods'){
			$q = $wpdb->prepare("
			SELECT user_id as id FROM {$bp->groups->table_name_members} WHERE group_id = %d AND is_mod = 1",$group_id);
		}
		if($settings['role'] == 'admin'){
			$q = $wpdb->prepare("
			SELECT user_id as id FROM {$bp->groups->table_name_members} WHERE group_id = %d AND is_admin = 1",$group_id);
		}

		$members = $wpdb->get_results($q,ARRAY_A);
		
        echo '<div class="vibebp_group_members '.$style.'"> ';
        if(!empty($members)){
        	foreach($members as $member){ 
        		echo '<div class="vibebp_member">';
        		echo '<a href="'.bp_core_get_user_domain($member['id']).'"><img src="'.bp_core_fetch_avatar(array('item_id'=>$member['id'],'object'  => 'user','type'=>'full','html'    => false)).'" ></a>';
        		echo '<span>'.bp_core_get_user_displayname($member['id']).'</span>';
        		echo '</div>';
        	}
        }
        echo ' </div><style>.vibebp_group_members{display:flex;flex-wrap:wrap;margin:-.5rem}.vibebp_group_members>*{flex:1 0 80px;display:flex;align-items:center;background:var(--border);border-radius:32px;margin:.5rem}.vibebp_group_members>* img{width:32px;height:32px;border-radius:50% !important;margin-right:10px}.vibebp_group_members>* span{text-overflow: ellipsis; white-space: nowrap; max-width: 64px; overflow: hidden;}</style>';
	}

}