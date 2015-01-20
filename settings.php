<?php

class PricingTableDynamite{

    private $plugin_path;
    private $plugin_url;
    private $l10n;
    private $pricingTableDynamite;
    private $namespace = _pricingTableDynamite;
    private $settingName = 'Pricing Table Dynamite Settings';

    function __construct() 
    {	
        $this->plugin_path = plugin_dir_path( __FILE__ );
        $this->plugin_url = plugin_dir_url( __FILE__ );
        $this->l10n = 'wp-settings-framework';
        add_action( 'admin_menu', array(&$this, 'admin_menu'), 99 );
        
        // Include and create a new WordPressSettingsFramework
        require_once( $this->plugin_path .'wp-settings-framework.php' );
        //$settings_file = $this->plugin_path .'settings/settings-general.php';
        
        $this->pricingTableDynamite = new WordPressSettingsFramework( $settings_file, $this->namespace, $this->get_settings() );
        // Add an optional settings validation filter (recommended)
        //add_filter( $this->pricingTableDynamite->get_option_group() .'_settings_validate', array(&$this, 'validate_settings') );
        
       // add_action( 'init', array(&$this, 'plugin_template_register_shortcodes'));
        //for tinymce button add_action('init', array(&$this, 'add_pricingTableDynamite_icon'));
        //add_action( 'wp_enqueue_scripts', array(&$this,'plugin_template_stylesheet' ));
       
    }
    
    function admin_menu()
    {
        $page_hook = add_menu_page( __( $this->settingName, $this->l10n ), __( $this->settingName, $this->l10n ), 'update_core', $this->settingName, array(&$this, 'settings_page') );
        add_submenu_page( $this->settingName, __( 'Settings', $this->l10n ), __( 'Settings', $this->l10n ), 'update_core', $this->settingName, array(&$this, 'settings_page') );
    }
    
    function settings_page()
	{
	    // Your settings page
	    
	    ?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h2><?php $this->settingName ?></h2>
			
			<h3>Pricing Table Dynamite</h3>
			<p>Put a table on your site by placing the shortcode tag <code>[pricing_table_dynamite tableid="%tableid%"]</code> on your page or post</p>
			<p>Replace %tableid% with the tableid for the pricing table you want to show
			<?php //$this->plugin_template_stylesheet(); 
			?>			
			
			<?php 
			//$this->plugin_template_shortcode();
			//echo do_shortcode('[plugin_template/]');
			// Output your settings form
			$this->pricingTableDynamite->settings(); 
			?>
			
		</div>
		<?php
		
		// Get settings
		//$settings = pricingTableDynamite_get_settings( $this->plugin_path .'settings/settings-general.php' );
		//echo '<pre>'.print_r($settings,true).'</pre>';
		
		// Get individual setting
		//$setting = pricingTableDynamite_get_setting( pricingTableDynamite_get_option_group( $this->plugin_path .'settings/settings-general.php' ), 'general', 'text' );
		//var_dump($setting);
	}
	
	function validate_settings( $input )
	{
	    // Do your settings validation here
	    // Same as $sanitize_callback from http://codex.wordpress.org/Function_Reference/register_setting
    	return $input;
	}
	
	
        
        function get_settings(){
        	$wpsf_settings[] = array(
		    'section_id' => 'general',
		    'section_title' => $this->settingName.' Settings',
		    //'section_description' => 'Some intro description about this section.',
		    'section_order' => 5,
		    'fields' => array(
		      		 array(
			            'id' => 'to_email',
			            'title' => 'To Email',
			            'desc' => 'Set the email address you want your forms submitted to.',
			            'type' => 'text',
			            'std' => '',
			        ),        
		        )
		        
        
    );
    return $wpsf_settings;
        }
  /*      
        function plugin_template_shortcode( $atts ) {
		extract( shortcode_atts( array(
			'color' => 'gray',
			'text' => 'Submit',
			'url' => '',
			'font_size' => '',
		), $atts ) );
		//Example of how to get options
		
		//$my_option_string = $this->pricingTableDynamite->get_option_group().'_settings';
		//	$my_options = get_option($my_option_string);
			
		//	$the_option = $this->pricingTableDynamite->get_option_group() .'_general_to_email';
		
		
	}
	function plugin_template_register_shortcodes(){
		add_shortcode( 'plugin_template', array(&$this, 'plugin_template_shortcode') );
		
	}
	
	function plugin_template_stylesheet() {
        	wp_register_style( 'plugin-template-style', plugin_dir_url(__FILE__).'css/plugin-template.css' );
        	wp_enqueue_style( 'plugin-template-style' );
    	}
*/

}
new PricingTableDynamite();

?>