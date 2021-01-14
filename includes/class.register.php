<?php
/**
 * Register Scripts
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

class VibeBP_Register{


    public static $instance;
    public static function init(){

        if ( is_null( self::$instance ) )
            self::$instance = new VibeBP_Register();
        return self::$instance;
    }

    private function __construct(){

        add_action('init',array($this,'register_menus'));
        add_action('wp_enqueue_scripts',array($this,'enqueue_head'),11);

    }

    function register_menus(){

        register_nav_menus(
            array(
                'loggedin' => __( 'LoggedIn DropDown Menu','vibebp' ),
                'profile' => __( 'Profile Menu','vibebp' )
            )
        );

        $enabled = vibebp_get_setting('role_based_menu','bp');
        if(!empty($enabled) && $enabled == 'on'){
            $roles = vibebp_get_registered_user_roles();
            foreach($roles as $key=>$role){
                register_nav_menus(
                    array(
                        'loggedin_'.$key => sprintf(__( 'LoggedIn DropDown Menu for %s [Optional]','vibebp' ),$role['label']),
                        'profile_'.$key => sprintf(__( 'Profile Menu for %s [Optional]','vibebp' ),$role['label'])
                    )
                );
            }
        }

        $member_type_based_menu = vibebp_get_setting('member_type_based_menu','bp');
        if(!empty($member_type_based_menu) && $member_type_based_menu == 'on'){
            $types = bp_get_member_types(array(),'objects');
 
            foreach($types as $type => $labels){
                register_nav_menus(
                    array(
                        'loggedin_'.$type => sprintf(__( 'LoggedIn DropDown Menu for %s [Optional]','vibebp' ),$labels->labels['name']),
                        'profile_'.$type => sprintf(__( 'Profile Menu for %s [Optional]','vibebp' ),$labels->labels['name'])
                    )
                );
            }
        }
    }


    function get_vibebp(){

        $blog_id = '';
        if(function_exists('get_current_blog_id')){
            $blog_id = get_current_blog_id();
        }

        $firebase_config =vibebp_get_setting('firebase_config');
        if(!empty($firebase_config) && is_serialized($firebase_config)){
            $firebase_config = json_encode(unserialize($firebase_config)); 
        }

        $customizer = VibeBP_Customizer::init();
        $customizer->get_customizer();

        $vibebp= array(
            'style'=>empty($customizer->customizer['theme'])?'medium':$customizer->customizer['theme'],
            'dark_mode'=>$customizer->customizer['mode'],
            'expanded_menu'=>$customizer->customizer['expanded_menu'],
            'user_id'=>bp_displayed_user_id(),
            'api'=>array(
                'url'=> apply_filters('vibebp_rest_api',get_rest_url($blog_id,Vibe_BP_API_NAMESPACE)),
                'sw_enabled'=>vibebp_get_setting('service_workers'),
                'sw'=>site_url().'/firebase-messaging-sw.js?v='.vibebp_get_setting('version','service_worker'),
                'endpoints'=>array(
                    'activity'      => Vibe_BP_API_ACTIVITY_TYPE,
                    'members'       => Vibe_BP_API_MEMBERS_TYPE,
                    'groups'        => Vibe_BP_API_GROUPS_TYPE,
                    'friends'       => 'friends',
                    'notifications' => Vibe_BP_API_NOTIFICATIONS_TYPE,
                    'messages'      => Vibe_BP_API_MESSAGES_TYPE,
                    'settings'      => Vibe_BP_API_SETTINGS_TYPE,
                    'xprofile'      => Vibe_BP_API_XPROFILE_TYPE
                ),
            ),
            'icons'=>Array(
                'dark_mode'=>apply_filters('vibebp_component_icon','<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M19 6.734c0 4.164-3.75 6.98-3.75 10.266h-1.992c.001-2.079.997-3.826 1.968-5.513.912-1.585 1.774-3.083 1.774-4.753 0-3.108-2.517-4.734-5.004-4.734-2.483 0-4.996 1.626-4.996 4.734 0 1.67.862 3.168 1.774 4.753.971 1.687 1.966 3.434 1.967 5.513h-1.991c0-3.286-3.75-6.103-3.75-10.266 0-4.343 3.498-6.734 6.996-6.734 3.502 0 7.004 2.394 7.004 6.734zm-4 11.766c0 .276-.224.5-.5.5h-5c-.276 0-.5-.224-.5-.5s.224-.5.5-.5h5c.276 0 .5.224.5.5zm0 2c0 .276-.224.5-.5.5h-5c-.276 0-.5-.224-.5-.5s.224-.5.5-.5h5c.276 0 .5.224.5.5zm-1.701 3.159c-.19.216-.465.341-.753.341h-1.093c-.288 0-.562-.125-.752-.341l-1.451-1.659h5.5l-1.451 1.659z"/></svg>','dark_mode'),
                'light_mode'=>apply_filters('vibebp_component_icon','<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M19 6.734c0 4.164-3.75 6.98-3.75 10.266h-1.992c.001-2.079.997-3.826 1.968-5.513.912-1.585 1.774-3.083 1.774-4.753 0-3.108-2.517-4.734-5.004-4.734-2.483 0-4.996 1.626-4.996 4.734 0 1.67.862 3.168 1.774 4.753.971 1.687 1.966 3.434 1.967 5.513h-1.991c0-3.286-3.75-6.103-3.75-10.266 0-4.343 3.498-6.734 6.996-6.734 3.502 0 7.004 2.394 7.004 6.734zm-4 11.766c0 .276-.224.5-.5.5h-5c-.276 0-.5-.224-.5-.5s.224-.5.5-.5h5c.276 0 .5.224.5.5zm0 2c0 .276-.224.5-.5.5h-5c-.276 0-.5-.224-.5-.5s.224-.5.5-.5h5c.276 0 .5.224.5.5zm-1.701 3.159c-.19.216-.465.341-.753.341h-1.093c-.288 0-.562-.125-.752-.341l-1.451-1.659h5.5l-1.451 1.659z"/></svg>','light_mode'),
                'logout'=>apply_filters('vibebp_component_icon','<svg width="24" height="24" viewBox="0 0 24 24">
                        <path d="M18.213,1.754L17,3.353C19.984,5.085 22,8.308 22,12C22,17.514 17.514,22 12,22C6.486,22 2,17.514 2,12C2,8.308 4.016,5.085 7,3.353L5.787,1.754C2.322,3.857 0,7.651 0,12C0,18.627 5.373,24 12,24C18.627,24 24,18.627 24,12C24,7.651 21.678,3.857 18.213,1.754Z" />
                        <rect x="10" y="0" width="4" height="12" />
                    </svg>','logout')
            ),
            'settings'=>array(
                'timestamp'=>time(),
                'client_id'=>vibebp_get_setting('client_id'),
                'security'=>vibebp_get_api_security(),
                'upload_limit'=>wp_max_upload_size(),
                'email_login'=>vibebp_get_setting('email_login'),
                'google_maps_api_key'=>vibebp_get_setting('google_maps_api_key'),
                'firebase_config'=>$firebase_config,
                'hide_live'=>vibebp_get_setting('disable_firebase_live'),
                'auth'=>array(
                    'google'=>vibebp_get_setting('firebase_google_auth'),
                    'facebook'=>vibebp_get_setting('firebase_facebook_auth'),
                    'twitter'=>vibebp_get_setting('firebase_twitter_auth'),
                    'github'=>vibebp_get_setting('firebase_github_auth'),
                    'apple'=>vibebp_get_setting('firebase_apple_auth')
                ),
                'logout_redirect'=>(vibebp_get_setting('logout_redirect')?get_permalink(vibebp_get_setting('logout_redirect')):home_url()),
                'login_redirect'=>apply_filters('vibebp_login_redirect_link',get_permalink(vibe_get_bp_page_id('members'))),
                'login_redirect_component'=>apply_filters('vibebp_login_redirect_component',false),
                'map_marker'=>plugins_url('../assets/images/marker.png',__FILE__),
                'followers' => vibebp_get_setting('bp_followers','bp'),
                'friends' => (function_exists('bp_is_active') && bp_is_active('friends'))?1:0,
                'likes' => vibebp_get_setting('bp_likes','bp'),
                'profile_page'=>vibebp_get_setting('offline_page','service_worker'),
                'enable_registrations'=>apply_filters('vibebp_enable_registration',true),
                'media_tabs'=>apply_filters('vibebp_media_tabs',array(
                    'media'=>__('Select media','vibebp'),
                    'upload'=>__('Upload media','vibebp'),
                    'embed'=>__('Embed media','vibebp'),
                )),
                'profile_settings'=>array() //array of arrays each array(key=>'CLASS ID TO INITIALIZE REACT COMPONENT','value'=>'xx')
            ),
            
            'translations'=>array(
                'hello'=> _x('Hello','dashboard','vibebp'),
                'online'=> _x('Online','api','vibebp'),
                'offline'=> _x('You are no longer connected to internet.','api','vibebp'),
                'empty_dashboard' => _x('Empty dashboard.','api','vibebp'),
                'facebook' => _x('Sign In with Facebook','api','vibebp'),
                'twitter' => _x('Sign In with Twitter','api','vibebp'),
                'google' => _x('Sign In with Google','api','vibebp'),
                'github' => _x('Sign In with Github','api','vibebp'),
                'apple' => _x('Sign In with  Apple','api','vibebp'),
                'login_heading'=> ''.stripslashes(vibebp_get_setting('login_heading')).'',
                'login_message'=>''.stripslashes(vibebp_get_setting('login_message')).'',
                'email_login'=>_x('Login with Email',' login','vibebp'),
                'no_account'=>_x('No account ?',' login','vibebp'),
                'create_one'=>_x('Create one',' login','vibebp'),
                'create_account'=>_x('Create Account',' login','vibebp'),
                'login_terms'=> stripslashes(vibebp_get_setting('login_terms')),
                'signin_email_heading'=> stripslashes(vibebp_get_setting('signin_email_heading')),
                'signin_email_description'=>stripslashes(vibebp_get_setting('signin_email_description')),
                'login_checkbox'=>stripslashes(vibebp_get_setting('login_checkbox')),
                'registration_checkbox'=>stripslashes(vibebp_get_setting('registration_checkbox')),
                'email'=>_x('Email or Username','login','vibebp'),
                'password'=>_x('Password','login','vibebp'),
                'all_signin_options'=>_x('All sign in options','login','vibebp'),
                'signin'=>_x('Sign In','login','vibebp'),
                'forgotpassword'=>_x('Forgot Password','login','vibebp'),
                'password_recovery_email'=>_x('Send password recovery email','login','vibebp'),
                'missing_subject'=>_x('Missing subject','message error','vibebp'), 
                'missing_recipients'=>_x('Missing recipients','message error','vibebp'), 
                'missing_content'=>_x('Missing message content','message error','vibebp'),
                'light_mode'=>_x('Light mode','profile','vibebp'),
                'dark_mode'=>_x('Dark mode','profile','vibebp'),
                'register_account_heading'=>stripslashes(vibebp_get_setting('register_account_heading')),
                'register_account_description'=>stripslashes(vibebp_get_setting('register_account_description')),
                'account_already'=>_x('Already have an account ? ','login','vibebp'),
                'likes'=>_x('Likes','login','vibebp'),
                'liked'=>_x('Liked','login','vibebp'),
                'followers'=>_x('Followers','login','vibebp'),
                'following'=>_x('Following','login','vibebp'),
                'follow_members'=>_x('Follow more members','login','vibebp'),
                'profile'=>_x('Profile ','login','vibebp'),
                'logout'=>_x('Logout ','login','vibebp'),
                'more'=>_x('Load more.. ','login','vibebp'),
                'years'=>_x('years','login','vibebp'),
                'year'=>_x('year ','login','vibebp'),
                'months'=>_x('months','login','vibebp'),
                'month'=>_x('month','login','vibebp'),
                'weeks'=>_x('weeks','login','vibebp'),
                'week'=>_x('week','login','vibebp'),
                'days'=>_x('days','login','vibebp'),
                'day'=>_x('day','login','vibebp'),
                'hours'=>_x('hours','login','vibebp'),
                'hour'=>_x('hour','login','vibebp'),
                'minutes'=>_x('minutes','login','vibebp'),
                'minute'=>_x('minute','login','vibebp'),
                'seconds'=>_x('seconds','login','vibebp'),
                'second'=>_x('second','login','vibebp'),
                'no_activity_found'=>_x('No activity found !','login','vibebp'),
                'whats_new'=>_x('Whats New','login','vibebp'),
                'post_update'=>_x('Post update','login','vibebp'),
                'select_component'=>_x('Select component','login','vibebp'),
                'justnow'=>_x('Just now','login','vibebp'),
                'cancel'=>_x('Cancel','login','vibebp'),
                'owner'=>_x('Owner','login','vibebp'),
                'date'=>_x('Date','login','vibebp'),
                'apply'=>_x('Apply','login','vibebp'),
                'type_message'=>_x('Type Message','login','vibebp'),
                'drag_to_refresh'=>_x('Drag to refresh','drag','vibebp'),
                'selectaction'=>_x('Select Action','login','vibebp'),
                'no_notifications_found'=>_x('No notifications found !','login','vibebp'),
                'sender'=>_x('Sender','login','vibebp'),
                'no_messages_found'=>_x('No messages found !','login','vibebp'),
                'no_groups_found'=>_x('No groups found !','login','vibebp'),
                'new_message'=>_x('New Message','login','vibebp'),
                'send_notice'=>_x('Send Notice','login','vibebp'),
                'labels'=>_x('Labels','login','vibebp'),
                'add_new'=>_x('Add New','login','vibebp'),
                'search_text'=>_x('Search ...','login','vibebp'),
                'recipients'=>_x('Recipients ...','login','vibebp'),
                'subject'=>_x('Subject','login','vibebp'),
                'message'=>_x('Message','login','vibebp'),
                'attachments'=>_x('Attachment','login','vibebp'),
                'send_message'=>_x('Send Message','login','vibebp'),
                'search_member'=>_x('Search Member','login','vibebp'),
                'add_label'=>_x('Add Label','login','vibebp'),
                'remove_label'=>_x('Remove Label','login','vibebp'),
                'select_image'=>_x('Upload File','login','vibebp'),
                'group_name'=>_x('Group Name','login','vibebp'),
                'group_description'=>_x('Group Description','login','vibebp'),
                'group_status'=>_x('Group Status','login','vibebp'),
                'group_type'=>_x('Group Type','login','vibebp'),
                'invite_members'=>_x('Invite members','login','vibebp'),
                'add_members'=>_x('Add members','login','vibebp'),
                'create_group'=>_x('Create Group','login','vibebp'),
                'group_invitations'=>_x('Group Invite Permissions','login','vibebp'),
                'image_size_error'=>sprintf(_x('Image size should be less than upload limit %s','login','vibebp'),'( '.floor(wp_max_upload_size()/1024).' kb )'),
                'admin'=>_x('Admin','login','vibebp'),
                'mod'=>_x('Mod','login','vibebp'),
                'select_option'=>_x('Select Option','login','vibebp'),
                'accept_invite'=>_x('Accept Invite','login','vibebp'),
                'cancel_invite'=>_x('Cancel Invite','login','vibebp'),
                'no_friends_found'=>_x('No Friends found !','login','vibebp'),
                'requester'=>_x('Requested','login','vibebp'),
                'requestee'=>_x('Requests','login','vibebp'),
                'no_requests_found'=>_x('No Requests found !','login','vibebp'),
                'add_friend'=>_x('Add Friend','login','vibebp'),
                'send_friend_request'=>_x('Send friend request','login','vibebp'),
                'account_email'=>_x('Account Email','login','vibebp'),
                'confirm_old_password'=>_x('Confirm Old Password','login','vibebp'),  
                'change_password'=>_x('Change Password','login','vibebp'), 
                'change_email' => _x('Change Email','','vibebp'),
                'repeat_new_password'=>_x('Repeat Password','login','vibebp'), 
                'save_changes'=>_x('Save Changes','login','vibebp'),
                'send_email_notice'=>_x('Send email notice','login','vibebp'),
                'visibility_settings'=>_x('Profile Field Visibility Settings','login','vibebp'),
                'export_data_settings'=>_x('Export data settings','login','vibebp'),
                'download_data'=>__( 'Download personal data', 'vibebp' ),
                'new_data'=>__('Request new data export', 'vibebp' ),
                'request_data'=>__('Request personal data export', 'vibebp'),
                'update_image'=>__('Update Image', 'vibebp'),
                'change_image'=>__('Change Image', 'vibebp'),
                'address'=>__('Address', 'vibebp'),
                'city'=>__('City', 'vibebp'),
                'country'=>__('Country', 'vibebp'),
                'country'=>__('ZipCode', 'vibebp'),
                'no_followers'=>__('No followers found !', 'vibebp'),
                'no_following'=>__('You are not following anyone !', 'vibebp'),
                'set_icon'=>__('Set Icon', 'vibebp'),
                'submit'=>__('Submit', 'vibebp'),
                'topic_title'=>__('Topic Title', 'vibebp'),
                'select_forum'=>__('Select a forum', 'vibebp'),
                'topic_content'=>__('Write content in topic', 'vibebp'),
                'subscribed'=>__('Subscribed', 'vibebp'),
                'subscribe'=>__('Subscribe', 'vibebp'),
                'no_orders'=>__('No Orders', 'vibebp'),
                'coupons_applied'=>__('Coupons Applied', 'vibebp'),
                'shipping_rates'=>__('Shipping Rates', 'vibebp'),
                'fees'=>__('Fees', 'vibebp'),
                'select_order_status'=>__('Select Order Status', 'vibebp'),
                'order_number'=>__('Order Number', 'vibebp'),
                'order_date'=>__('Order Date', 'vibebp'), 
                'order_status'=>__('Order Status', 'vibebp'), 
                'order_quantity'=>__('Order Quantity', 'vibebp'), 
                'order_amount'=>__('Order Total', 'vibebp'),
                'order_payment_method'=>__('Payment Method', 'vibebp'),
                'item_name'=>__('Item Name', 'vibebp'),
                'item_total'=>__('Item Total', 'vibebp'),
                'billing_address'=>__('Billing Address', 'vibebp'),
                'shipping_address'=>__('Shipping Address', 'vibebp'),
                'no_downloads'=>__('No Downloads found !', 'vibebp'),
                'download'=>__('Download', 'vibebp'),
                'access_expires'=>__('Access Expires', 'vibebp'),
                'product_name'=>__('Product Name', 'vibebp'),
                'remaining_downloads'=>__('Remaining Downloads', 'vibebp'),
                'allMedia' =>__('All Media', 'vibebp'),
                'uploaded_media'=>__('Uploads', 'vibebp'),
                'choose_column_type'=>__('Choose Columns', 'vibebp'),
                'type_here'=>__('Type here...', 'vibebp'),
                'no_media'=>__('No media found!', 'vibebp'),
                'preview'=>__('Preview', 'vibebp'),
                'select_widget'=>__('Select Widget', 'vibebp'),
                'missing_data'=>__('Missing data', 'vibebp'),
                'invalid_email'=>__('Invalid email id.', 'vibebp'),
                'password_too_short'=>__('Too short password.', 'vibebp'),
                'enter_emabed_name'=>__('Enter Embed Name', 'vibebp'),
                'enter_embed_url'=>__('Enter Embed Url', 'vibebp'),
                'embed'=>__('Embed', 'vibebp'),
                'hide_panel'=>__('Hide Panel', 'vibebp'),
                'show_panel'=>__('Show Panel', 'vibebp'),
                'title'=>__('Title', 'vibebp'),
                'description'=>__('Description', 'vibebp'),
                'site_activity'=>__('Site Activity', 'vibebp'),
                'select_action'=>__('Select Action', 'vibebp'),
                'select_group_type'=>__('Select group type', 'vibebp'),
                'set_group_forum'=>__('Select group forum', 'vibebp'),
                'back_to_signin'=>__('Back to sign in', 'vibebp')
            ),
        );
        
        $vibebp['components'] = array(

            'dashboard'=>array(
                    'sidebar'=>apply_filters('vibebp_member_dashboard','vibebp-dashboard',$vibebp['user_id']),
                    'widgets'=>apply_filters('vibebp_member_dashboard_widgets',array())
                ),
            'profile'=>array(
                'label'=>__('Profile','vibebp'),
            )
        );
        $social_icons = apply_filters('vibebp_social_icons',array(
            array(
                'icon'=>'vicon vicon-flickr',
                'label'=>__('Flickr','vibebp')
            ),
            array(
                'icon'=>'vicon vicon-instagram',
                'label'=>__('Instagram','vibebp')
            ),
            array(
                'icon'=>'vicon vicon-google',
                'label'=>__('Google','vibebp')
            ),
            array(
                'icon'=>'vicon vicon-github',
                'label'=>__('Github','vibebp')
            ),
            array(
                'icon'=>'vicon vicon-facebook',
                'label'=>__('Facebook','vibebp')
            ),
            array(
                'icon'=>'vicon vicon-dropbox',
                'label'=>__('Dropbox','vibebp')
            ),
            array(
                'icon'=>'vicon vicon-dribbble',
                'label'=>__('Dribbble','vibebp')
            ),
            array(
                'icon'=>'vicon vicon-apple',
                'label'=>__('Apple','vibebp')
            ),
            array(
                'icon'=>'vicon vicon-yahoo',
                'label'=>__('Yahoo','vibebp')
            ),
            array(
                'icon'=>'vicon vicon-trello',
                'label'=>__('Trello','vibebp')
            ),
            array(
                'icon'=>'vicon vicon-stack-overflow',
                'label'=>__('Stack-overflow','vibebp')
            ),
            array(
                'icon'=>'vicon vicon-soundcloud',
                'label'=>__('Soundcloud','vibebp')
            ),
            array(
                'icon'=>'vicon vicon-sharethis',
                'label'=>__('Sharethis','vibebp')
            ),
            array(
                'icon'=>'vicon vicon-reddit',
                'label'=>__('Reddit','vibebp')
            ),
            array(
                'icon'=>'vicon vicon-microsoft',
                'label'=>__('Microsoft','vibebp')
            ),
            array(
                'icon'=>'vicon vicon-linux',
                'label'=>__('Linux','vibebp')
            ),
            array(
                'icon'=>'vicon vicon-jsfiddle',
                'label'=>__('Jsfiddle','vibebp')
            ),
            array(
                'icon'=>'vicon vicon-joomla',
                'label'=>__('Joomla','vibebp')
            ),
            array(
                'icon'=>'vicon vicon-html5',
                'label'=>__('Html5','vibebp')
            ),
            array(
                'icon'=>'vicon vicon-css3',
                'label'=>__('Css3','vibebp')
            ),
            array(
                'icon'=>'vicon vicon-drupal',
                'label'=>__('Drupal','vibebp')
            ),
            array(
                'icon'=>'vicon vicon-wordpress',
                'label'=>__('Wordpress','vibebp')
            ),
            array(
                'icon'=>'vicon vicon-tumblr',
                'label'=>__('Tumblr','vibebp')
            ),
            array(
                'icon'=>'vicon vicon-skype',
                'label'=>__('Skype','vibebp')
            ),
            array(
                'icon'=>'vicon vicon-youtube',
                'label'=>__('Youtube','vibebp')
            ),
            array(
                'icon'=>'vicon vicon-vimeo',
                'label'=>__('Vimeo','vibebp')
            ),
            array(
                'icon'=>'vicon vicon-twitter',
                'label'=>__('Twitter','vibebp')
            ),
            array(
                'icon'=>'vicon vicon-linkedin',
                'label'=>__('Linkedin','vibebp')
            ),
            array(
                'icon'=>'vicon vicon-pinterest',
                'label'=>__('Pinterest','vibebp')
            )
        ));
        $vibebp['social_icons']=$social_icons;


        $vibebp['repeatable_icons']=apply_filters('vibebp_repeatable_icons',array(
                'vicon vicon-arrow-up','vicon vicon-arrow-right','vicon vicon-arrow-left','vicon vicon-arrow-down','vicon vicon-arrows-vertical','vicon vicon-arrows-horizontal','vicon vicon-angle-up','vicon vicon-angle-right','vicon vicon-angle-left','vicon vicon-angle-down','vicon vicon-angle-double-up','vicon vicon-angle-double-right','vicon vicon-angle-double-left','vicon vicon-angle-double-down','vicon vicon-move','vicon vicon-fullscreen','vicon vicon-arrow-top-right','vicon vicon-arrow-top-left','vicon vicon-arrow-circle-up','vicon vicon-arrow-circle-right','vicon vicon-arrow-circle-left','vicon vicon-arrow-circle-down','vicon vicon-arrows-corner','vicon vicon-split-v','vicon vicon-split-v-alt','vicon vicon-split-h','vicon vicon-hand-point-up','vicon vicon-hand-point-right','vicon vicon-hand-point-left','vicon vicon-hand-point-down','vicon vicon-back-right','vicon vicon-back-left','vicon vicon-exchange-vertical
','vicon vicon-wand','vicon vicon-save','vicon vicon-save-alt','vicon vicon-direction','vicon vicon-direction-alt','vicon vicon-user','vicon vicon-link','vicon vicon-unlink','vicon vicon-trash','vicon vicon-target','vicon vicon-tag','vicon vicon-desktop','vicon vicon-tablet','vicon vicon-mobile','vicon vicon-email','vicon vicon-star','vicon vicon-spray','vicon vicon-signal','vicon vicon-shopping-cart','vicon vicon-shopping-cart-full','vicon vicon-settings','vicon vicon-search','vicon vicon-zoom-in','vicon vicon-zoom-out','vicon vicon-cut','vicon vicon-ruler','vicon vicon-ruler-alt-2','vicon vicon-ruler-pencil','vicon vicon-ruler-alt','vicon vicon-bookmark','vicon vicon-bookmark-alt','vicon vicon-reload','vicon vicon-plus','vicon vicon-minus','vicon vicon-close','vicon vicon-pin','vicon vicon-pencil','vicon vicon-pencil-alt','vicon vicon-paint-roller','vicon vicon-paint-bucket','vicon vicon-na','vicon vicon-medall','vicon vicon-medall-alt','vicon vicon-marker','vicon vicon-marker-alt','vicon vicon-lock','vicon vicon-unlock','vicon vicon-location-arrow','vicon vicon-layout','vicon vicon-layers','vicon vicon-layers-alt','vicon vicon-key','vicon vicon-image','vicon vicon-heart','vicon vicon-heart-broken','vicon vicon-hand-stop','vicon vicon-hand-open','vicon vicon-hand-drag','vicon vicon-flag','vicon vicon-flag-alt','vicon vicon-flag-alt-2','vicon vicon-eye','vicon vicon-import','vicon vicon-export','vicon vicon-cup','vicon vicon-crown','vicon vicon-comments','vicon vicon-comment','vicon vicon-comment-alt','vicon vicon-thought','vicon vicon-clip','vicon vicon-check','vicon vicon-check-box','vicon vicon-camera','vicon vicon-announcement','vicon vicon-brush','vicon vicon-brush-alt','vicon vicon-palette','vicon vicon-briefcase','vicon vicon-bolt','vicon vicon-bolt-alt','vicon vicon-blackboard','vicon vicon-bag','vicon vicon-world','vicon vicon-wheelchair','vicon vicon-car','vicon vicon-truck','vicon vicon-timer','vicon vicon-ticket','vicon vicon-thumb-up','vicon vicon-thumb-down','vicon vicon-stats-up','vicon vicon-stats-down','vicon vicon-shine','vicon vicon-shift-right','vicon vicon-shift-left','vicon vicon-shift-right-alt','vicon vicon-shift-left-alt','vicon vicon-shield','vicon vicon-notepad','vicon vicon-server','vicon vicon-pulse','vicon vicon-printer','vicon vicon-power-off','vicon vicon-plug','vicon vicon-pie-chart','vicon vicon-panel','vicon vicon-package','vicon vicon-music','vicon vicon-music-alt','vicon vicon-mouse','vicon vicon-mouse-alt','vicon vicon-money','vicon vicon-microphone','vicon vicon-menu','vicon vicon-menu-alt','vicon vicon-map','vicon vicon-map-alt','vicon vicon-location-pin','vicon vicon-light-bulb','vicon vicon-info','vicon vicon-infinite','vicon vicon-id-badge','vicon vicon-hummer','vicon vicon-home','vicon vicon-help','vicon vicon-headphone','vicon vicon-harddrives','vicon vicon-harddrive','vicon vicon-gift','vicon vicon-game','vicon vicon-filter','vicon vicon-files','vicon vicon-file','vicon vicon-zip','vicon vicon-folder','vicon vicon-envelope','vicon vicon-dashboard','vicon vicon-cloud','vicon vicon-cloud-up','vicon vicon-cloud-down','vicon vicon-clipboard','vicon vicon-calendar','vicon vicon-book','vicon vicon-bell','vicon vicon-basketball','vicon vicon-bar-chart','vicon vicon-bar-chart-alt','vicon vicon-archive','vicon vicon-anchor','vicon vicon-alert','vicon vicon-alarm-clock','vicon vicon-agenda','vicon vicon-write','vicon vicon-wallet','vicon vicon-video-clapper','vicon vicon-video-camera','vicon vicon-vector','vicon vicon-support','vicon vicon-stamp','vicon vicon-slice','vicon vicon-shortcode','vicon vicon-receipt','vicon vicon-pin2','vicon vicon-pin-alt','vicon vicon-pencil-alt2','vicon vicon-eraser','vicon vicon-more','vicon vicon-more-alt','vicon vicon-microphone-alt','vicon vicon-magnet','vicon vicon-line-double','vicon vicon-line-dotted','vicon vicon-line-dashed','vicon vicon-ink-pen','vicon vicon-info-alt','vicon vicon-help-alt','vicon vicon-headphone-alt','vicon vicon-gallery','vicon vicon-face-smile','vicon vicon-face-sad','vicon vicon-credit-card','vicon vicon-comments-smiley','vicon vicon-time','vicon vicon-share','vicon vicon-share-alt','vicon vicon-rocket','vicon vicon-new-window','vicon vicon-rss','vicon vicon-rss-alt','vicon vicon-control-stop','vicon vicon-control-shuffle','vicon vicon-control-play','vicon vicon-control-pause','vicon vicon-control-forward','vicon vicon-control-backward','vicon vicon-volume','vicon vicon-control-skip-forward','vicon vicon-control-skip-backward','vicon vicon-control-record','vicon vicon-control-eject','vicon vicon-paragraph','vicon vicon-uppercase','vicon vicon-underline','vicon vicon-text','vicon vicon-Italic','vicon vicon-smallcap','vicon vicon-list','vicon vicon-list-ol','vicon vicon-align-right','vicon vicon-align-left','vicon vicon-align-justify','vicon vicon-align-center','vicon vicon-quote-right','vicon vicon-quote-left','vicon vicon-layout-width-full','vicon vicon-layout-width-default','vicon vicon-layout-width-default-alt','vicon vicon-layout-tab','vicon vicon-layout-tab-window','vicon vicon-layout-tab-v','vicon vicon-layout-tab-min','vicon vicon-layout-slider','vicon vicon-layout-slider-alt','vicon vicon-layout-sidebar-right','vicon vicon-layout-sidebar-none','vicon vicon-layout-sidebar-left','vicon vicon-layout-placeholder','vicon vicon-layout-menu','vicon vicon-layout-menu-v','vicon vicon-layout-menu-separated','vicon vicon-layout-menu-full','vicon vicon-layout-media-right','vicon vicon-layout-media-right-alt','vicon vicon-layout-media-overlay','vicon vicon-layout-media-overlay-alt','vicon vicon-layout-media-overlay-alt-2','vicon vicon-layout-media-left','vicon vicon-layout-media-left-alt','vicon vicon-layout-media-center','vicon vicon-layout-media-center-alt','vicon vicon-layout-list-thumb','vicon vicon-layout-list-thumb-alt','vicon vicon-layout-list-post','vicon vicon-layout-list-large-image','vicon vicon-layout-line-solid','vicon vicon-layout-grid4','vicon vicon-layout-grid3','vicon vicon-layout-grid2','vicon vicon-layout-grid2-thumb','vicon vicon-layout-cta-right','vicon vicon-layout-cta-left','vicon vicon-layout-cta-center','vicon vicon-layout-cta-btn-right','vicon vicon-layout-cta-btn-left','vicon vicon-layout-column4','vicon vicon-layout-column3','vicon vicon-layout-column2','vicon vicon-layout-accordion-separated','vicon vicon-layout-accordion-merged','vicon vicon-layout-accordion-list','vicon vicon-widgetized','vicon vicon-widget','vicon vicon-widget-alt','vicon vicon-view-list','vicon vicon-view-list-alt','vicon vicon-view-grid','vicon vicon-upload','vicon vicon-download','vicon vicon-loop','vicon vicon-layout-sidebar-2','vicon vicon-layout-grid4-alt','vicon vicon-layout-grid3-alt','vicon vicon-layout-grid2-alt','vicon vicon-layout-column4-alt','vicon vicon-layout-column3-alt','vicon vicon-layout-column2-alt','<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
    <path fill="#000" fill-rule="nonzero" d="M12.074 7.31h9.852l-.898 10.174-4.034 1.131-4.023-1.13-.897-10.174zm-10.01 0h9.873l-.9 10.244-4.043 1.068-4.032-1.089-.899-10.222zm17.714-1.306v.407H18V5.85h.932L18 4.991V4.5h1.778v.562h-.795l.795.942zm-2.111 0v.407H16V5.85h.86L16 4.991V4.5h1.667v.562h-.757l.757.942zm-2-.155v.562H14.11V4.5h1.556v.562h-.89v.787h.89zM2.995 4.57h.625v.625h.571V4.57h.625v1.892h-.625v-.633H3.62v.633h-.625V4.57zm2.643.628h-.55V4.57h1.725v.628h-.55v1.264h-.625V5.198zm1.448-.628h.652l.4.665.4-.665h.652v1.892h-.622v-.938l-.43.673h-.01l-.43-.673v.938h-.612V4.57zm2.415 0h.625v1.267h.879v.625H9.5V4.57zM19.503 15.45l.261-2.462.299-3.39.03-.375h-6.178l.03.38.056.678.024.291h4.72l-.113 1.237h-3.098l.029.337.056.635.025.265h2.878l-.145 1.636-1.377.395-1.371-.37-.088-.986h-1.238l.17 1.95 2.527.697 2.504-.707v-.211zm-9.435-5.724l.03-.278H3.905l.03.356.303 3.467h4.284l-.144 1.543L7 15.111v-.001.026l-1.377-.353-.088-.951h-1.24l.171 1.918 2.531.665.003-.002 2.532-.689.022-.19.29-3.25.03-.25H5.37l-.113-1.35h4.73l.024-.303.057-.656z"/>
</svg>','<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
    <path fill="#000" fill-rule="nonzero" d="M19.482 6.413c.441.268.664.768.664 1.303v8.257c0 .536-.223 1.035-.664 1.305l-6.76 4.13a1.377 1.377 0 0 1-1.43 0l-2.275-1.413c-.34-.2-.175-.27-.063-.311.454-.165.543-.204 1.027-.491.05-.03.113-.019.166.013l1.74 1.09c.063.037.139.037.197 0l6.783-4.13c.064-.037.05-.114.05-.193V7.716c0-.08.01-.155-.055-.196l-6.757-4.126a.222.222 0 0 0-.217 0L5.067 7.52c-.066.04-.137.118-.137.195v8.257c0 .08.039.154.104.191l1.863 1.132c1.012.532 1.568-.094 1.568-.723V8.421c0-.115.217-.286.326-.286h.864c.108 0 .04.171.04.287v8.151c0 1.42-.666 2.233-1.947 2.233-.393 0-.633 0-1.498-.448l-1.803-1.08a1.558 1.558 0 0 1-.747-1.305V7.716c0-.535.3-1.036.74-1.303L11.28 2.28c.43-.256.99-.256 1.418 0l6.785 4.134zm-5.372 8.252c1.434 0 2.045-.34 2.045-1.141 0-.46-.173-.803-2.402-1.032-1.864-.194-3.016-.627-3.016-2.192 0-1.444 1.158-2.305 3.1-2.305 2.182 0 3.26.795 3.398 2.501a.216.216 0 0 1-.052.158.197.197 0 0 1-.144.064h-.885a.197.197 0 0 1-.19-.16c-.213-.99-.728-1.306-2.128-1.306-1.566 0-1.748.572-1.748 1.002 0 .52.215.668 2.33.962 2.093.291 3.087.697 3.087 2.242 0 1.559-1.237 2.44-3.395 2.44-2.981 0-3.605-1.461-3.605-2.667 0-.114.088-.252.197-.252h.88c.099 0 .18.12.196.222.133.942.53 1.464 2.332 1.464z"/>
</svg>','<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
    <path fill="#000" fill-rule="nonzero" d="M11.917 6.61l7.916-4.57v19.92l-7.916-4.57L4 21.96V2.04l7.917 4.57zM8.154 12l3.938 2.277L16.029 12l-3.937-2.277L8.154 12zM6.747 6.415v3.259l2.816-1.63-2.816-1.629zm0 7.911v3.014l2.605-1.507-2.605-1.507zm10.501 3.014v-3.014l-2.605 1.507 2.605 1.507zm-2.816-9.296l2.816 1.63V6.415l-2.816 1.63z"/>
</svg>'

        ));

        if(vibebp_get_setting('firebase_config')){
            $url = site_url();
            $parsedUrl = parse_url($url);
            $vibebp['components']['firebase'] = array(
                'root'=>(isset($parsedUrl['path'])?strstr($url, $parsedUrl['path'], true):$url),
                'tabs'=>Array(
                    array('tab'=>'notes','label'=>__('Notes','vibebp'),'icon'=>'vicon vicon-notepad'),
                    array('tab'=>'notifications','label'=>__('Notifications','vibebp'),'icon'=>'vicon vicon-bell'),
                    array('tab'=>'chat','label'=>__('Chat','vibebp'),'icon'=>'vicon vicon-comments'),
                )
            );
        }   
        global $post;
        if((function_exists('bp_is_user') && bp_is_user()) || (!empty($post) &&  !empty($post->post_content) && has_shortcode($post->post_content,'vibebp_profile'))){
            //$vibebp['user_id'] = bp_displayed_user_id();
            $nav = get_option('bp_setup_nav');
            if(empty($nav) && !empty(bp_get_nav_menu_items())){
                $nav = bp_get_nav_menu_items();
            }
            if(!is_array($nav)){$nav=array();}
            if(!empty($nav)){
                foreach($nav as $key => $item){
                    $name = strip_tags($item->name);
                    if(is_numeric(substr($name, -1,1))){
                        $end = strrpos($name, ' ');
                        $name = substr($name, 0,$end);
                        $count = substr($name, $end);
                        $nav[$key]->count = $count;
                    }
                    $nav[$key]->name = $name;
                    if(in_array("current-menu-parent",$item->class) ){
                        unset($nav[$key]->class[array_search("current-menu-parent",$item->class)]);
                    }
                    if(in_array("current-menu-item",$item->class)){
                        unset($nav[$key]->class[array_search("current-menu-item",$item->class)]);
                    }

                    

                    if($item->parent === 0){
                       // if(in_Array($item->css_id,array('activity','profile','')
                    }
                }
            }
            if(!is_array($nav)){$nav=array();}
            array_unshift($nav, array(
                'css_id'=>'dashboard',
                'name'=>__('Dashboard','vibebp'),
                'parent'=>0,
                'class'=>["menu-parent","current-menu-parent"]
            ));

            $vibebp['nav'] = $nav;

            if(bp_is_active('xprofile')){
                $vibebp['components']['xprofile']=array(
                    'visibility'=>array(
                        'public'=>__('Public','vibebp'),
                        'loggedin'=>__('All members','vibebp'),
                        'friends'=>__('Friends','vibebp'),
                        'adminsonly'=>__('Only me','vibebp'),
                    ),
                    'countries'=>vibebp_get_countries()
                );
            }
            if(bp_is_active('activity')){
                $vibebp['components']['activity']=array(
                    'label'=>__('Activity','vibebp'),
                    'post'=>array(
                        'components'=>array(
                            '' => __('Personal','vibebp'),
                            'groups' => __('Groups','vibebp'),
                        ),
                    ),
                    'sorters'=>array(
                        '-1'=>_x('Everything','login','vibebp'),
                        'activity_update'=>_x('Updates','login','vibebp'),
                        'friendship_accepted,friendship_created'=>_x('Friendships','login','vibebp'),
                        'created_group'=>_x('New Groups','login','vibebp'),
                        'joined_group'=>_x('Group Memberships','login','vibebp'),
                        'group_details_updated'=>_x('Group Updates','login','vibebp'),
                        
                    ),
                );
            }
            
            if(bp_is_active('notifications')){
                $vibebp['components']['notifications']=array(
                    'label'=>__('Notifications','vibebp'),
                    'sorters'=>array(
                        'DESC'=>_x('Newest First','login','vibebp'),
                        'ASC'=>_x('Oldest First','login','vibebp'),
                    ),
                    'actions'=>Array(
                        'read'=>_x('Read','login','vibebp'),
                        'unread'=>_x('Unread','login','vibebp'),
                        'delete'=>_x('Delete','login','vibebp'),
                    )
                );
            }


            if(bp_is_active('messages')){
                $vibebp['components']['messages']=array(
                    'label'=>__('Messages','vibebp'),
                    'sorters'=>array(
                        'DESC'=>_x('Newest First','login','vibebp'),
                        'ASC'=>_x('Oldest First','login','vibebp'),
                    ),
                    'actions'=>Array(
                        'read'=>_x('Mark Read','login','vibebp'),
                        'unread'=>_x('Mark Unread','login','vibebp'),
                        'delete'=>_x('Delete','login','vibebp'),
                        'star'=>_x('Add Star','login','vibebp'),
                        'unstar'=>_x('Remove Star','login','vibebp'),
                    )
                );
            }

            if(bp_is_active('friends')){
                $vibebp['components']['friends']=array(
                    'label'=>__('Friends','vibebp'),
                    'sorters'=>array(
                        'active'=>_x('Active','login','vibebp'),
                        'alphabetical'=>_x('Alphabetical','login','vibebp'),
                        'newest'=>_x('Newest','login','vibebp'),
                    ),
                    'requests_sorter'=>array(
                        'DESC'=>_x('Newest first','login','vibebp'),
                        'ASC'=>_x('Earliest first','login','vibebp'),
                    ),
                    'actions'=>Array(
                        'read'=>_x('Mark Read','login','vibebp'),
                        'unread'=>_x('Mark Unread','login','vibebp'),
                        'delete'=>_x('Delete','login','vibebp'),
                        'star'=>_x('Add Star','login','vibebp'),
                        'unstar'=>_x('Remove Star','login','vibebp'),
                    )
                );
            }
            
            if(bp_is_active('groups')){

                $vibebp['components']['activity']['post']['components']['groups']=__('Groups','vibebp');
                $vibebp['components']['groups']=array(
                    'label'=>__('Groups','vibebp'),
                    'sorters'=>array(
                        'active'=>_x('Last Active','login','vibebp'),
                        'popular'=>_x('Most Members','login','vibebp'),
                        'newest'=>_x('Newly Created','login','vibebp'),
                        'alphabetical'=>_x('Alphabetical','login','vibebp'),
                    ),
                );

                
              
                if(function_exists('bp_groups_get_group_types')){
                    $types = bp_groups_get_group_types(array(),'objects');    
                    $gt=[];
                    if(!empty($types)){
                        foreach($types as $key=>$t){
                            $gt[$key]=$t->labels['name'];
                        }    
                    }
                    $vibebp['components']['groups']['type'] = $gt;
                }
                
                $vibebp['components']['groups']['status'] = array(
                    'public'=>_x('Public','login','vibebp'),
                    'private'=>_x('Private','login','vibebp'),
                    'hidden'=>_x('Hidden','login','vibebp'),
                );
                $vibebp['components']['groups']['invite_type'] = array(
                    'pending'=>_x('Pending Invites','login','vibebp'),
                    'accepted'=>_x('Accepted invites','login','vibebp'),
                );
                $vibebp['components']['groups']['invite_sort'] = array(
                    'DESC'=>_x('Recently invited','login','vibebp'),
                    'ASC'=>_x('Last Invited','login','vibebp'),
                );
                $vibebp['components']['groups']['membertypes'] = array(
                    ''=>_x('All Members','login','vibebp'),
                    'mod'=>_x('Moderators','login','vibebp'),
                    'admin'=>_x('Administrators','login','vibebp'),
                    'banned'=>_x('Banned Users','login','vibebp'),
                    'invited'=>_x('Invited Users','login','vibebp'),
                    'requests'=>__('Membership Requests', 'vibebp'),
                );

                if(function_exists('bbpress') && function_exists('vibe_helpdesk_translations')){
                    $vibebp['components']['groups']['forum'] =VIBE_HELPDESK_API_NAMESPACE;
                }
                
                $vibebp['components']['groups']['invite_status'] = array(
                    'members'=>_x('All Group Members','login','vibebp'),
                    'mods'=>_x('Group Moderators and Administrators','login','vibebp'),
                    'admins'=>_x('Group Administrators only','login','vibebp'),
                );

                $vibebp['components']['groups']['nav'] = array(
                    'activty'=>_x('Activity','login','vibebp'),
                    'members'=>_x('Members','login','vibebp'),
                    'manage'=>_x('Manage','login','vibebp'),
                );
            }

            if(vibebp_get_setting('bp_followers','bp')){
                $vibebp['components']['followers']=array(
                    'label'=>__('Followers','vibebp'),
                    'sorters'=>array(
                        'active'=>_x('Last Active','login','vibebp'),
                        'popular'=>_x('Most Members','login','vibebp'),
                        'newest'=>_x('Newly Created','login','vibebp'),
                        'alphabetical'=>_x('Alphabetical','login','vibebp'),
                    ),
                );
            }



            if(function_exists('WC')){
                $vibebp['components']['shop']=array(
                    'label'=>__('Shop','vibebp'),
                    'order_type'=>array(
                        'shop_order_refund'=>__('Refunds','vibebp')
                    ),
                    'order_statuses'=>wc_get_order_statuses(),
                );
            }
            if(function_exists('pmpro_hasMembershipLevel')){
                $vibebp['components']['pmpro']=array(
                    'label'=>__('My Memberships','vibebp'),
                    'translations' => array(
                        'level' => _x('Level','','vibebp'),
                        'billing' =>  _x('Billing','','vibebp'),
                        'expiration' => _x('Expiration','','vibebp'), 
                        'renew' => _x('Renew','','vibebp'), 
                        'change' => _x('Change','','vibebp'), 
                        'cancel' => _x('Cancel','','vibebp'), 
                        'view_all_levels' => _x('View all membership Options','','vibebp'), 
                        'past_invoices' => _x('Past Invoices','','vibebp'), 
                        'date' => _x('Date','','vibebp'), 
                        'amount' => _x('Amount','','vibebp'), 
                        'status' => _x('Status','','vibebp'), 
                        'username' => _x('Username','','vibebp'), 
                        'email' => _x('Email','','vibebp'), 
                        'edit_profile' => _x('Edit Profile','','vibebp'), 
                        'change_password' => _x('Change Password','','vibebp'), 
                        'member_links' => _x('Member Links','','vibebp'), 
                        'view_print' => _x('View and print membership cards','','vibebp'), 
                        'no_invoices' => _x('No invoices found','','vibebp'),
                        'no_memberships' => _x('No memberships found','','vibebp'),

                    ),
                    
                );
            }
            $vibebp['components']=apply_filters('vibebp_active_compontents',$vibebp['components']);
        }
        return apply_filters('vibebp_vars',$vibebp);
    }

    function enqueue_head(){
        
         wp_enqueue_script('localforage',plugins_url('../assets/js/localforage.min.js',__FILE__),array(),VIBEBP_VERSION);

        if(!vibebp_get_setting('global_login') && !apply_filters('vibebp_enqueue_login_script',false))
            return;

        wp_enqueue_script('wp-element');
        wp_enqueue_script('wp-data');
       
        wp_enqueue_style('vicons',plugins_url('../assets/vicons.css',__FILE__),array(),VIBEBP_VERSION);

        wp_enqueue_script('vibebplogin',plugins_url('../assets/js/login.js',__FILE__),array('wp-element','wp-data','localforage'),VIBEBP_VERSION);
        wp_localize_script('vibebplogin','vibebp',$this->get_vibebp());
        wp_enqueue_style('vibebp_main',plugins_url('../assets/css/front.css',__FILE__),array(),VIBEBP_VERSION);

        if(vibebp_get_setting('firebase_config')){

            wp_enqueue_script('firebase',plugins_url('../assets/js/firebase-app.js',__FILE__),array(),VIBEBP_VERSION);
            wp_enqueue_script('firebase-auth',plugins_url('../assets/js/firebase-auth.js',__FILE__),array(),VIBEBP_VERSION);
            

            if((empty(vibebp_get_setting('disable_firebase_live') || vibebp_get_setting('disable_firebase_live') !='on')) || (function_exists('bp_is_user') && bp_is_user() || apply_filters('vibebp_enqueue_profile_script',false))){
                
                wp_enqueue_script('firebase-database',plugins_url('../assets/js/firebase-database.js',__FILE__),array(),VIBEBP_VERSION);
                wp_enqueue_script('firebase-messaging',plugins_url('../assets/js/firebase-messaging.js',__FILE__),array(),VIBEBP_VERSION);

                wp_enqueue_script('vibebp_live',plugins_url('../assets/js/live.js',__FILE__),array('wp-element','wp-data','vibebplogin'),VIBEBP_VERSION,true);

                $blog_id = '';
                if(function_exists('get_current_blog_id')){
                    $blog_id = get_current_blog_id();
                }

                wp_localize_script('vibebp_live','vibelive',array(
                    'api'=>array(
                        'url'=> apply_filters('vibebp_rest_api',get_rest_url($blog_id,Vibe_BP_API_NAMESPACE)),
                    ),
                    'settings'=>array(
                        'upload_limit'=>wp_max_upload_size(),
                    ),
                    'translations'=>array(
                        'add_new_note'=>__('Add new Note', 'vibebp'),
                        'set_reminders'=>__('Set Reminders', 'vibebp'),
                        'add_note'=>__('Add Note', 'vibebp'),
                        'edit_note'=>__('Edit Note', 'vibebp'),
                        'no_notifications'=>__('No more notifications !', 'vibebp'),
                        'mark_all_read'=>__('Mark all read', 'vibebp'),
                        'delete_all'=>__('Delete all', 'vibebp'),
                        'no_members_online'=>__('No members online.', 'vibebp'),
                        'send'=>__('Send', 'vibebp'),
                        'cancel'=>__('Cancel', 'vibebp'),
                        'mychats'=>__('MyChats', 'vibebp'),
                        'online'=>__('Online', 'vibebp'),
                        'start_new_chat'=>__('Start new chat', 'vibebp'),
                        'back_to_all_chats'=>__('Back to all chats', 'vibebp'),
                        'exit_chat'=>__('Exit Chat', 'vibebp'),
                        'add_new_message'=>__('Add new message', 'vibebp'),
                        'invited'=>__('Invited', 'vibebp'),
                        'is_typing'=>__('is typing', 'vibebp'),
                        'members_online'=>__(' Members Online !', 'vibebp'),
                        'select_attachment'=>__('Select Attachment', 'vibebp'),
                        'loading' =>__('Loading...', 'vibebp'),
                        'joined' =>_x('Joined chat','joined chat', 'vibebp'),
                        'attachment_size_error'=>sprintf(_x('Attachment size should be less than upload limit %s','login','vibebp'),'( '.floor(wp_max_upload_size()/1024).' kb )'),
                    )
                ));
            }
        }

        $enqueue_script = false;
        if(function_exists('bp_is_user') && bp_is_user()){
            $enqueue_script = true;
        }
        
        if(apply_filters('vibebp_enqueue_profile_script',$enqueue_script)){
            //wp_dequeue_script('jquery');

            wp_enqueue_script('tus',plugins_url('../assets/js/tus.min.js',__FILE__),array(),VIBEBP_VERSION,true);
        
            wp_enqueue_script('chartjs',plugins_url('../assets/js/Chart.min.js',__FILE__),array(),VIBEBP_VERSION,true);
            wp_enqueue_script('cropprjs',plugins_url('../assets/js/croppr.min.js',__FILE__),array(),VIBEBP_VERSION,true);

            wp_enqueue_script('flatpickr',plugins_url('../assets/js/flatpickr.min.js',__FILE__),array(),VIBEBP_VERSION,true);
            wp_enqueue_script('colorpickr',plugins_url('../assets/js/vanilla-picker.min.js',__FILE__),array(),VIBEBP_VERSION,true);
            wp_enqueue_script('plyr',plugins_url('../assets/js/plyr.js',__FILE__),array(),VIBEBP_VERSION,true);
            wp_enqueue_script('vibebpprofile',plugins_url('../assets/js/profile.js',__FILE__),array('wp-element','wp-data'),VIBEBP_VERSION,true);
            wp_enqueue_style('vibebp_profile_libs',plugins_url('../assets/css/profile.css',__FILE__),array(),VIBEBP_VERSION);

            wp_enqueue_style('vicons',plugins_url('../assets/vicons.css',__FILE__),array(),VIBEBP_VERSION);
            wp_enqueue_style('plyr',plugins_url('../assets/css/plyr.css',__FILE__),array(),VIBEBP_VERSION);
            wp_enqueue_script('vibe_editor',plugins_url('../assets/js/editor.js',__FILE__),array(),VIBEBP_VERSION,true);

            wp_localize_script('vibe_editor','vibeEditor',array(
                'media_order'=>array(
                    'recent'=>__('Recently uploaded','vibebp'),
                    'alphabetical'=>__('Alphabetical','vibebp'),
                ),
                'embed_types'=>array(
                    'image'=> __('image','vibebp'),
                    'video'=> __('Video','vibebp'),
                    'audio'=> __('Audio','vibebp'),
                    'file'=> __('File','vibebp')
                ),
                'shortcodes' => array(
                    array(
                        'key'=> 'note',
                        'title'=> __('Note','vibebp'),
                        'icon'=> 'vicon vicon-notepad',
                        'attributes'=>array(
                            array(
                                'field_type'=> 'number',
                                'output'=> 'style',
                                'label'=> __('Margin','vibebp'),
                                'parameter'=> 'margin',
                                'suffix'=> 'px',
                                'default'=> 0
                            ),
                            array(
                                'field_type'=> 'number',
                                'output'=> 'style',
                                'label'=> __('Padding','vibebp'),
                                'parameter'=> 'padding',
                                'suffix'=> 'px',
                                'default'=> 0
                            ),
                            array(
                                'field_type'=> 'color',
                                'output'=> 'style',
                                'label'=> __('Background','vibebp'),
                                'parameter'=> 'background',
                                'default'=> ''
                            ),
                            array(
                                'field_type'=> 'color',
                                'output'=> 'style',
                                'label'=> __('Color','vibebp'),
                                'parameter'=> 'color',
                                'default'=> ''
                            )
                        )
                    ),
                    array(
                        'key'=> 'tab',
                        'title'=> __('Tab','vibebp'),
                        'icon'=> 'vicon vicon-layout-tab',
                        'tabs'=> []  // [{title:'',content:'editordata}]
                    ),
                    array(
                        'key'=> 'accordion',
                        'title'=> __('Accordion','vibebp'),
                        'icon'=> 'vicon vicon-layout-accordion-merged',
                        'accordions'=> []  // [{title:'',content:'editordata}]
                    ),
                    array(
                        'key'=> 'vibe_form',
                        'title'=> __('Forms','vibebp'),
                        'icon'=> 'vicon vicon-layout-accordion-merged',
                        'forms'=> [] // [{id,content,..}]
                    )
                ),
                'translations'=>array(
                    'add_tab'=> __('Add Tab','vibebp'),
                    'add_accordion_tab'=> __('Add Accordion Tab','vibebp'),
                    'enter_title'=> __('Enter Title...','vibebp'),
                    'search'=> __('Search...','vibebp'),
                    'view_form'=> __('View Form','vibebp'),
                    'advance_elements'=> __('Advance Elements','vibebp'),
                )
            ));
            wp_enqueue_style('vibe_editor',plugins_url('../assets/css/editor.css',__FILE__),array(),VIBEBP_VERSION);
        }

        if(vibebp_get_setting('service_workers')){  
            $css = "@font-face {
                font-family: 'vicon';
                src:url('".plugins_url('../assets/fonts/vicon.eot',__FILE__)."');
                src:url('".plugins_url('../assets/fonts/vicon.eot',__FILE__)."') format('embedded-opentype'),
                    url('".plugins_url('../assets/fonts/vicon.woff',__FILE__)."') format('woff'),
                    url('".plugins_url('../assets/fonts/vicon.ttf',__FILE__)."') format('truetype'),
                    url('".plugins_url('../assets/fonts/vicon.svg',__FILE__)."') format('svg');
                font-weight: normal;
                font-style: normal;
            }";
            wp_add_inline_style('vicons',$css);
        }
    }
}
VibeBP_Register::init();
