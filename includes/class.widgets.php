<?php
/**
 * Widgets for VibeBP 
 *
 * @author      VibeThemes
 * @category    Admin
 * @package     VibeBP Plugin
 * @version     4.0
 */

 if ( ! defined( 'ABSPATH' ) ) exit;

class VibeBP_Widgets{

    public static $instance;
    public static function init(){
    if ( is_null( self::$instance ) )
        self::$instance = new VibeBP_Widgets();
        return self::$instance;
    }

    private function __construct(){
      require_once(dirname(__FILE__).'/widgets/server_stats_widget.php');
      require_once(dirname(__FILE__).'/widgets/users_report_widget.php');
      require_once(dirname(__FILE__).'/widgets/sales_stats_widget.php');
    }

}

VibeBP_Widgets::init();



function vibebp_load_widgets_functions(){
    require_once(dirname(__FILE__).'/widgets/functions.php');
}


			