<?php
/**
 * Customizer plugin
 *
 * @class       VibeBP_Customizer
 * @author      VibeThemes
 * @category    Admin
 * @package     VibeBp
 * @version     1.0
 * @copyright   VibeThemes
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}




class VibeBP_Customizer{


	public static $instance;
	public static function init(){

        if ( is_null( self::$instance ) )
            self::$instance = new VibeBP_Customizer();
        return self::$instance;
    }

	private function __construct(){

		add_action('customize_register', array($this,'vibebp_customize'));
		add_action('wp_head',array($this,'generate_css'));
        add_filter('vibebp_component_icon',array($this,'elegance_theme'),11,2);
	}
 
    function get_customizer(){
        if(empty($this->customizer)){
            $this->customizer=get_option('vibebp_customizer');    
        }
        return $this->customizer;
    }

	function generate_controls(){
		return apply_filters('vibebp_customizer_config',array(
		    'sections' => array(
                'vibebp_general_settings'=>'VibeBp General Settings',
            	'vibebp_light_colors'=>'VibeBp Light Colors',
            	'vibebp_dark_colors'=>'VibeBp Dark Colors',
            ),
		    'controls' => array(
                'vibebp_general_settings' => array( 
                    'theme' => array(
                        'label' => 'Theme style',
                        'type'  => 'select',
                        'choices'=>array(
                            ''=>'Default',
                            'elegance'=>'Elegance',
                        ),
                        'default' => ''
                    ),
                    'mode' => array(
                        'label' => 'Default Dark Style',
                        'type'  => 'toggle',
                        'default' => ''
                    ),
                    'expanded_menu' => array(
                        'label' => 'Show expanded profile menu',
                        'type'  => 'toggle',
                        'default' => ''
                    ),
                    'loggedin_profile_header' => array(
                        'label' => 'Disable Header in My Profile',
                        'type'  => 'toggle',
                        'default' => ''
                    ),
                    'loggedin_profile_footer' => array(
                        'label' => 'Disable Footer in My Profile',
                        'type'  => 'toggle',
                        'default' => ''
                    ),
                ),
		        'vibebp_light_colors' => array( 
                    'light_primary' => array(
                        'label' => 'Primary Color',
                        'type'  => 'color',
                        'default' => ''
                    ),
                    'light_primarycolor' => array(
                        'label' => 'Text Color on  Primary background',
                        'type'  => 'color',
                        'default' => ''
                    ),
                    
                    'light_body' => array(
                        'label' => 'Body Background',
                        'type'  => 'color',
                        'default' => ''
                    ), 
                    'light_text' => array(
                        'label' => 'Text color',
                        'type'  => 'color',
                        'default' => ''
                    ), 
                    'light_bold' => array(
                        'label' => 'Heading / Title color',
                        'type'  => 'color',
                        'default' => ''
                    ), 
                    'light_sidebar' => array(
                        'label' => 'Sidebar Background',
                        'type'  => 'color',
                        'default' => ''
                    ),
                    'light_border' => array(
                        'label' => 'Border color',
                        'type'  => 'color',
                        'default' => ''
                    ),
                    'light_darkborder' => array(
                        'label' => 'Dark Border',
                        'type'  => 'color',
                        'default' => ''
                    ),
                    'light_shadow' => array(
                        'label' => 'Shadow color',
                        'type'  => 'color',
                        'default' => ''
                    ),
                    'light_dark' => array(
                        'label' => 'Darker Background',
                        'type'  => 'color',
                        'default' => ''
                    ),
                    'light_light' => array(
                        'label' => 'Lighter Background',
                        'type'  => 'color',
                        'default' => ''
                    ),  
                ),
		        'vibebp_dark_colors' => array( 
                    'dark_primary' => array(
                        'label' => 'Primary Color',
                        'type'  => 'color',
                        'default' => ''
                    ),
                    'dark_primarycolor' => array(
                        'label' => 'Text Color on  Primary background',
                        'type'  => 'color',
                        'default' => ''
                    ),
                    
                    'dark_body' => array(
                        'label' => 'Body Background',
                        'type'  => 'color',
                        'default' => ''
                    ), 
                    'dark_text' => array(
                        'label' => 'Text color',
                        'type'  => 'color',
                        'default' => ''
                    ), 
                    'dark_bold' => array(
                        'label' => 'Heading / Title color',
                        'type'  => 'color',
                        'default' => ''
                    ), 
                    'dark_sidebar' => array(
                        'label' => 'Sidebar Background',
                        'type'  => 'color',
                        'default' => ''
                    ),
                    'dark_border' => array(
                        'label' => 'Border color',
                        'type'  => 'color',
                        'default' => ''
                    ),
                    'dark_darkborder' => array(
                        'label' => 'Dark Border',
                        'type'  => 'color',
                        'default' => ''
                    ),
                    'dark_shadow' => array(
                        'label' => 'Shadow color',
                        'type'  => 'color',
                        'default' => ''
                    ),
                    'dark_dark' => array(
                        'label' => 'Darker Background',
                        'type'  => 'color',
                        'default' => ''
                    ),
                    'dark_dark' => array(
                        'label' => 'darker Background',
                        'type'  => 'color',
                        'default' => ''
                    ),  
                )
			)
		));
	}

	function vibebp_customize($wp_customize) {


	    $vibe_customizer = $this->generate_controls();


	    $wp_customize->add_panel( 
			'vibebp_settings',
			array(
				'priority'       => 10001,
				'capability'     => 'edit_theme_options',
				'theme_supports' => '',
				'title'          => __('VibeBP Settings', 'mytheme'),
				'description'    => __('VibeBP Settings', 'mytheme'),
			)
		);

		$i=9991; // Show sections after the WordPress default sections
	    if(isset($vibe_customizer) && is_Array($vibe_customizer)){
	        foreach($vibe_customizer['sections'] as $key=>$value){
	        	

	            $wp_customize->add_section( $key, array(
	            'title'          => $value,
	            'panel'			 => 'vibebp_settings',
	            'priority'       => $i,
	        ) );
	            $i = $i+4;
	        }
	    }
	    if(isset($vibe_customizer) && is_array($vibe_customizer)){
		    foreach($vibe_customizer['controls'] as $section => $settings){ 
		    	$i=1;
		        foreach($settings as $control => $type){
		            $i=$i+2;
		            $wp_customize->add_setting( 'vibebp_customizer['.$control.']', array(
	                    'label'         => $type['label'],
	                    'type'           => 'option',
	                    'capability'     => 'edit_theme_options',
	                    'transport'  => 'refresh',
	                    'sanitize_callback'=> 'vibebp_sanitizer',
	                    'default'       => (empty($type['default'])?'':$type['default'])
	                ) );
		            
		            switch($type['type']){
		                case 'color':
		                        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $control, array(
		                        'label'   => $type['label'],
		                        'section' => $section,
		                        'settings'   => 'vibebp_customizer['.$control.']',
		                        'priority'       => $i
		                        ) ) );            
		                    break;   
		                case 'select':
		                        $wp_customize->add_control( $control, array(
	                                'label'   => $type['label'],
	                                'section' => $section,
	                                'settings'   => 'vibebp_customizer['.$control.']',
	                                'priority'   => $i,
	                                'type'    => 'select',
	                                'choices'    => (empty($type['choices'])?'':$type['choices'])                       
	                                ) );
		                break;
                        case 'toggle':
                            $wp_customize->add_control( new VibeBp_Customizer_Toggle_Control( $wp_customize, $control, array(
                                    'label'       => $type['label'],
                                    'section'     => $section,
                                    'settings'   => 'vibebp_customizer['.$control.']',
                                    'priority'   => $i,
                                    'type'        => 'ios',// light, ios, flat
                            ) ) );
                        break;
		            }
		        }
		    }
		}

	}

	function generate_css(){

		$customizer=$this->get_customizer();
		if(!empty($customizer)){
			echo '<style>';
			$light = $dark = [];
			foreach($customizer as $key=>$customise){
				if(!empty($customise)){
					if(stripos($key, 'light_') !== false){
						$lkey = str_replace('light_','--',$key);
						$light[]= $lkey.':'.$customise;
                        if($lkey == 'primary'){
                            echo '.button.is-primary{background-color:'.$customise.';}';
                        }
					}else if(stripos($key, 'dark_') !== false){
						$dkey = str_replace('dark_','--',$key);
						$dark[]=$dkey.':'.$customise;
                        if($dkey == 'primary'){
                            echo '.vibebp_myprofile.dark_theme .button.is-primary{background-color:'.$customise.';}';
                        }
					}else{
                        if($key == 'loggedin_profile_header'){
                            echo '.vibebp_my_profile header,.vibebp_my_profile #headertop,.vibebp_my_profile .header_content,.vibebp_my_profile #title{display:none;}.pusher{overflow:visible;}.vibebp_my_profile #vibebp_member{padding-top:0;}';
                        }
                        if($key == 'loggedin_profile_footer'){
                            echo '.vibebp_my_profile footer,.vibebp_my_profile #footerbottom{display:none;} #vibebp_member{padding-top:0 !important;} ';
                        }
                    }
                    
				}
			}
			echo '.vibebp_myprofile{'.implode(';',$light).'; --plyr-color-main:'.$customizer['light_primary'].';}';
			echo '.vibebp_myprofile.dark_theme{'.implode(';',$dark).'; --plyr-color-main:'.$customizer['dark_primary'].';}';
			echo '</style>';
		}
	}

    function elegance_theme($icon,$name){
        $customizer = $this->get_customizer();


        if(!empty($customizer['theme'])){
            switch($customizer['theme']){
                case 'elegance':
                    switch($name){
                        case 'dashboard':
                            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-cpu"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line><line x1="20" y1="9" x2="23" y2="9"></line><line x1="20" y1="14" x2="23" y2="14"></line><line x1="1" y1="9" x2="4" y2="9"></line><line x1="1" y1="14" x2="4" y2="14"></line></svg>';
                        break;
                        case 'activity':
                            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-activity"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>';
                        break;
                        case 'groups':
                            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>';
                        break;
                        case 'drive':
                        case 'mydrive':
                            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-server"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>';
                        break;
                        case 'forums':
                            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>';
                        break;
                        case 'profile':
                            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>';
                        break;
                        case 'messages':
                            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-send"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>';
                        break;
                        case 'notifications':
                            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>';
                        break;
                        case 'friends':
                            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-check"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg>';
                        break;
                        case 'followers':
                            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-plus"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>';
                        break;case 'settings':
                            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>';
                        break;case 'shop':
                            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>';
                        break;case 'commissions':
                            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>';
                        break;case 'memberships':
                            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-aperture"><circle cx="12" cy="12" r="10"></circle><line x1="14.31" y1="8" x2="20.05" y2="17.94"></line><line x1="9.69" y1="8" x2="21.17" y2="8"></line><line x1="7.38" y1="12" x2="13.12" y2="2.06"></line><line x1="9.69" y1="16" x2="3.95" y2="6.06"></line><line x1="14.31" y1="16" x2="2.83" y2="16"></line><line x1="16.62" y1="12" x2="10.88" y2="21.94"></line></svg>';
                        break;case 'kb':
                            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
                        break;
                        case 'calendar':
                        case 'appointments':
                            $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>';
                        break;
                        case 'course':
                            $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-book-open"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>';
                        break;
                        case 'light_mode':
                            $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sun"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>';
                        break;
                        case 'dark_mode':
                            $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-moon"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>';
                        break;
                        case 'logout':
                            $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-power"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg>';
                        break;
                        case 'bbb_meeting':
                        case 'zoom_meeting':
                        case 'jitsi_meeting':
                            $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-video"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>';
                        break;
                        case 'clp':
                            $icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="feather feather-git-branch"><line x1="6" y1="3" x2="6" y2="15"></line><circle cx="18" cy="6" r="3"></circle><circle cx="6" cy="18" r="3"></circle><path d="M18 9a9 9 0 0 1-9 9"></path></svg>';
                        break;
                    }
                break;
                case 'modern':
                    switch($name){
                        case 'dashboard':
                            return 'vicon vicon-dashboard';
                        break;
                        case 'groups':
                        break;
                        case 'dashboard':
                        break;
                        case 'drive':
                        break;
                        case 'forums':
                        break;
                        case 'profile':
                        break;
                        case 'messages':
                        break;
                        case 'notifications':
                        break;
                        case 'friends':
                        break;
                        case 'followers':
                        break;case 'settings':
                        break;case 'shop':
                        break;case 'memberships':
                        break;case 'kb':
                        break;
                        
                    }
                break;
            }
        }
        return $icon;
    }
}

VibeBP_Customizer::init();


add_action('customize_register', function(){
class VibeBp_Customizer_Toggle_Control extends WP_Customize_Control {
    public $type = 'ios';

    /**
     * Enqueue scripts/styles.
     *
     * @since 3.4.0
     */
    public function enqueue() {
        wp_enqueue_script( 'customizer-toggle-control', plugins_url('../assets/js/vibebp_toggle.js',__FILE__), array( 'jquery' ), rand(), true );
        wp_enqueue_style( 'customizer-toggle-control', plugins_url('../assets/css/vibebp_toggle.css',__FILE__), array(), rand() );

        $css = '
            .disabled-control-title {
                color: #a0a5aa;
            }
            input[type=checkbox].tgl-light:checked + .tgl-btn {
                background: #0085ba;
            }
            input[type=checkbox].tgl-light + .tgl-btn {
              background: #a0a5aa;
            }
            input[type=checkbox].tgl-light + .tgl-btn:after {
              background: #f7f7f7;
            }
            input[type=checkbox].tgl-ios:checked + .tgl-btn {
              background: #0085ba;
            }
            input[type=checkbox].tgl-flat:checked + .tgl-btn {
              border: 4px solid #0085ba;
            }
            input[type=checkbox].tgl-flat:checked + .tgl-btn:after {
              background: #0085ba;
            }
        ';
        wp_add_inline_style( 'pure-css-toggle-buttons', $css );
    }

    /**
     * Render the control's content.
     *
     * @author soderlind
     * @version 1.2.0
     */
    public function render_content() {
        ?>
        <label class="customize-toogle-label">
            <div style="display:flex;flex-direction: row;justify-content: flex-start;">
                <span class="customize-control-title" style="flex: 2 0 0; vertical-align: middle;"><?php echo esc_html( $this->label ); ?></span>
                <input id="cb<?php echo $this->instance_number; ?>" type="checkbox" class="tgl tgl-<?php echo $this->type; ?>" value="<?php echo esc_attr( $this->value() ); ?>"
                                        <?php
                                        $this->link();
                                        checked( $this->value() );
                                        ?>
                 />
                <label for="cb<?php echo $this->instance_number; ?>" class="tgl-btn"></label>
            </div>
            <?php if ( ! empty( $this->description ) ) : ?>
            <span class="description customize-control-description"><?php echo $this->description; ?></span>
            <?php endif; ?>
        </label>
        <?php
    }

    /**
     * Plugin / theme agnostic path to URL
     *
     * @see https://wordpress.stackexchange.com/a/264870/14546
     * @param string $path  file path
     * @return string       URL
     */
    private function abs_path_to_url( $path = '' ) {
        $url = str_replace(
            wp_normalize_path( untrailingslashit( ABSPATH ) ),
            site_url(),
            wp_normalize_path( $path )
        );
        return esc_url_raw( $url );
    }
}
},9);