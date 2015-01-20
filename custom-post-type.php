<?php

/*
*Instruction - if you are using this as a template for a plugin, change the class name, the call to create an object from this class *at the bottom, and modify the private variables to meet your needs.
*/

class PricingTableDynamiteCustomPostType{

private $post_type = 'pricingtabledynamite';
private $post_label = 'Pricing Table Dynamite';

function __construct() {
	/**
	 * Hack to fi media loader
	 */
	//add_action( 'load-async-upload.php', array(&$this,'insert_button_hack' ));
	//add_action( 'load-media-upload.php', array(&$this,'insert_button_hack' ));
	//add_filter( 'cmb_meta_boxes', array(&$this,'metaboxes' ));
	//add_action( 'cmb_render_change_template', array(&$this,'cmb_render_change_template'), 10, 2 );
	//add_action( 'init', array(&$this,'initialize_meta_boxes'), 9999 );
	
	add_action("init", array(&$this,"ptd_change_wpautop"));
	add_action("init", array(&$this,"create_post_type"));
	add_action( 'init', array(&$this,'ptd_register_shortcodes'));
	add_action('admin_menu', array(&$this,"ptd_add_menus"));
	if(is_admin()){
		add_action('admin_menu', array(&$this,'ptd_enqueue_admin_scripts'));
		add_action('admin_menu', array(&$this,'ptd_enqueue_general_scripts'));
	}else{
		add_action('wp_print_scripts', array(&$this,'ptd_enqueue_frontend_scripts'));
		add_action('wp_print_scripts', array(&$this,'ptd_enqueue_general_scripts'));
	}
	add_action('load-post-new.php', array(&$this,'ptd_switch_post_screen'));
	add_action('posts_selection', array(&$this,'ptd_switch_post_edit_screen'));
	add_action('wp_print_scripts', array(&$this,'test_ajax_load_scripts'));
	add_action('wp_ajax_test_response', array(&$this,'text_ajax_process_request'));
	register_activation_hook( __FILE__, array(&$this,'activate' ));
}

function ptd_change_wpautop(){	
	//remove_filter( 'the_content', 'wpautop' );
	//add_filter( 'the_content', 'wpautop' , 199);
	//add_filter( 'the_content', 'shortcode_unautop',200 );
}

function text_ajax_process_request() {
	check_ajax_referer( 'my-special-string', 'security' );
	if(is_admin()){
		// first check if data is being sent and that it is the data we want
	  	if ( isset( $_POST["post_var"] ) ) {
			// now set our response var equal to that of the POST var (this will need to be sanitized based on what you're doing with with it)
			$response = $_POST["post_var"];
			
			$postId = isset($_POST["postId"]) ? $_POST["postId"] : '';
			$title = isset($_POST["title"]) ? $_POST["title"] : '';
			if($postId == ''){
				// Create post object
				$my_post = array(
				  'post_title'    => $title,
				  'post_status'   => 'publish',
				  'post_type'	  => $this->post_type
				);
				
				// Insert the post into the database
				$postId = wp_insert_post( $my_post );
			}
			else{
				$thePost = get_post($postId);
				$thePost->post_title = $title;
				wp_update_post($thePost);
			}
			
			add_post_meta( $postId, 'data', $response, true ) || update_post_meta( $postId, 'data', $response );
			/*
			//********this was working
			header('Content-Type: application/json');
			echo stripslashes($response);
			//********this was working
			*/
			//header('Content-Type: application/json');
			//echo '{"postId":'.$postId.'}';
			echo $postId;
			//echo json_encode(stripslashes($response));
			//echo '{"columns":[{"id":"0","title":"Column","price":"$19","rows":[{"id":"","name":"","text":"feature+1","$$hashKey":"008"}],"$$hashKey":"006"}]}';
			die();
		}
	}
}


function test_ajax_load_scripts() {
	if(is_admin()){
		// load our jquery file that sends the $.post request
		wp_enqueue_script( "ajax-test", plugin_dir_url( __FILE__ ) . 'js/ajax-test.js', array( 'jquery' ) );
	 
		// make the ajaxurl var available to the above script
		wp_localize_script( 'ajax-test', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );	
	}
}

function create_post_type(){


	register_post_type($this->post_type, array(
	         'label' => _x($this->post_label, $this->post_type.' label'), 
	         'singular_label' => _x('All '.$this->post_label, $this->post_type.' singular label'), 
	         'public' => true, // These will be public
	         'show_ui' => true, // Show the UI in admin panel
	         '_builtin' => false, // This is a custom post type, not a built in post type
	         '_edit_link' => 'post.php?post=%d',
	         //'_edit_link' => 'http://www.chloejems.com/wp-content/plugins/pricing-table-dynamite/mypost.php?post=%d',
	         'capability_type' => 'page',
	         'hierarchical' => false,
	         'rewrite' => array("slug" => $this->post_type), // This is for the permalinks
	         'query_var' => $this->post_type, // This goes to the WP_Query schema
	         //'supports' =>array('title', 'editor', 'custom-fields', 'revisions', 'excerpt'),
	         'supports' =>array('title', 'author'),
	         'add_new' => _x('Add New', 'Event')
	         ));
	         
	         
}

function ptd_add_menus(){
	//add_menu_page('Pricing Table Dynamite', 'Pricing Table Dynamite', 'manage_options', 'pricingtable', array(&$this,'ptd_test_page'));
	add_submenu_page(null, 'Pricing Table Dynamite', 'Pricing Table Dynamite', 'manage_options', 'pricingtable', array(&$this,'ptd_test_page'));
}



function ptd_switch_post_screen(){
	if(is_admin()){
		$screen = get_current_screen();
		
		if($screen->post_type == 'pricingtabledynamite' && $screen->base == 'post'){
			wp_redirect(admin_url("admin.php?page=pricingtable"));
			exit;
		}
	}
}

function ptd_switch_post_edit_screen(){
	if(is_admin()){
		//global $post;
		$screen = get_current_screen();
		$postId = isset($_GET['post']) ? $_GET['post'] : null;
		$thePost = get_post($postId);
		if($screen->base == 'post' && $thePost->post_type == 'pricingtabledynamite'){
			wp_redirect(admin_url("admin.php?page=pricingtable&post=".$postId));
			exit;
		}
	}
	
}


function ptd_test_page(){
	include plugin_dir_path( __FILE__ ).'admin/ptd_add_pricing_table.php';
}

/**
 * Hack to fi media loader
 */
//add_action( 'load-async-upload.php', array(&$this,'plugin_template_insert_button_hack' ));
//add_action( 'load-media-upload.php', array(&$this,'plugin_template_insert_button_hack' ));

 
function insert_button_hack(){
 
      /*  if ( 'image' != $_REQUEST['type'] )
                return;
 */
        $the_post_type = get_post_type( $_REQUEST['post_id'] );
 
        if (  $the_post_type == $this->post_type ){
                add_post_type_support( $this->post_type, 'editor' );
 	}
}




/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
function metaboxes( array $meta_boxes ) {
	
	// Start with an underscore to hide fields from custom fields list
	$prefix = '_plugin_template_';
	
	$meta_boxes[] = array(
		'id'         => 'template_metabox',
		'title'      => 'Template',
		'pages'      => array( $this->post_type, ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		//'show_on'    => array( 'key' => 'id', 'value' => array( 2, ), ), // Specific post IDs to display this metabox
		'fields' => array(
			array(
				'name'    => 'Template',
				'desc'    => 'Choose a pre-designed template. Options with an asterick (*) will change some or all of your content.  These should only be chosen when you are starting a new page or want to replace an existing page.  <a href="http://www.thinkplugintemplates.com/theme-previews">See theme previews.</a>',
				'id'      => $prefix . 'pre_designed_template',
				'type'    => 'select',
				'options' => array(
					array( 'name' => 'Choose a template', 'value' => '', ),
					array( 'name' => 'Theme 1', 'value' => 'theme_1', ),
					array( 'name' => 'Theme 2', 'value' => 'theme_2', ),
					array( 'name' => 'Theme 3', 'value' => 'theme_3', ),
					array( 'name' => 'Theme 4', 'value' => 'theme_4', ),
					array( 'name' => 'Theme 5', 'value' => 'theme_5', ),
					array( 'name' => 'Theme 6', 'value' => 'theme_6', ),
				),
			),
			//Example of custom metabox.  See change_template_button function below
			/*
			array(
				'id'   => $prefix . 'change_template_button',
				'type' => 'change_template',
				'std' => 'Change',
			),
			*/
		)
	);

	$meta_boxes[] = array(
		'id'         => 'content_metabox',
		'title'      => 'Content',
		'pages'      => array( $this->post_type ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'name' => 'Background Options',
				//'desc' => 'This is a title description',
				'id'   => $prefix . 'background_options',
				'type' => 'title',
			),
			array(
			            'name' => 'Background Color',
			            'desc' => 'Use this to set the backgroud of the entire screen.',
			            'id'   => $prefix . 'background_color',
			            'type' => 'colorpicker',
					'std'  => '#ffffff'
		        ),
		        array(
				'name' => 'Background Image',
				'desc' => 'Sets an image as the background of the entire screen (it will be stretched to fit).  Overrides the background color.',
				'id'   => $prefix . 'background_image',
				'type' => 'file',
			),
			array(
				'name' => 'Background Image Scroll',
				'desc' => 'Check here if you want the background image to scroll with the text.',
				'id'   => $prefix . 'is_scroll_text',
				'type' => 'checkbox',
			),
			array(
				'name' => 'Logo Options',
				//'desc' => 'This is a title description',
				'id'   => $prefix . 'logo_options',
				'type' => 'title',
			),
			
			array(
				'name' => 'Text Logo',
				'desc' => 'Enter text if you would like to use it for your logo.',
				'id'   => $prefix . 'logo_text',
				'type' => 'text',
				'std'  => 'Logo'
			),
			array(
				'name'    => 'Text Logo Font',
				//'desc'    => 'field description (optional)',
				'id'      => $prefix . 'logo_text_font',
				'type'    => 'select',
				//'options' => plugin_template_create_fonts(),
				'std'     => 'Candal'
			),
			array(
				'name'    => 'Text Logo Font Size',
				//'desc'    => 'field description (optional)',
				'id'      => $prefix . 'logo_text_font_size',
				'type'    => 'select',
				//'options' => plugin_template_create_options(plugin_template_get_font_sizes()),
				'std'     => '24',
			),
			
		        array(
				'name'    => 'Layout',
				'desc'    => 'Choose where you want to display Content Area 1 and the Email Form.',
				'id'      => $prefix . 'layout',
				'type'    => 'radio',
				'options' => array(
					array( 'name' => 'Content Area 1 (left), Email Form (right)', 'value' => 'layout1', ),
					array( 'name' => 'Content Area 1 (right), Email Form (left)', 'value' => 'layout2', ),
					array( 'name' => 'Content Area 1 (top), Email Form (bottom)', 'value' => 'layout3', ),
				),
				'std' 	  => 'layout3'
			),
			
		        array(
				'name'    => 'Content Background Opacity',
				//'desc'    => 'field description (optional)',
				'id'      => $prefix . 'content_background_opacity',
				'type'    => 'radio_inline',
				'options' => array(
					array( 'name' => '0', 'value' => '0', ),
					array( 'name' => '10', 'value' => '.1', ),
					array( 'name' => '20', 'value' => '.2', ),
					array( 'name' => '30', 'value' => '.3', ),
					array( 'name' => '40', 'value' => '.4', ),
					array( 'name' => '50', 'value' => '.5', ),
					array( 'name' => '60', 'value' => '.6', ),
					array( 'name' => '70', 'value' => '.7', ),
					array( 'name' => '80', 'value' => '.8', ),
					array( 'name' => '90', 'value' => '.9', ),
					array( 'name' => '100', 'value' => '1.0', )
				),
				'std' => '.5',
			),
		        array(
				'name'    => 'Content Area 1',
				//'desc'    => 'field description (optional)',
				'id'      => $prefix . 'content_area_1',
				'type'    => 'wysiwyg',
				'options' => array(	'textarea_rows' => 8, 'wpautop' => true ),
			),
			array(
				'name' => 'Content Area 1 width',
				'desc' => 'Change the width of content area 1 in pixels.',
				'id'   => $prefix . 'content_area_1_width',
				'type' => 'text_small',
			),
		        array(
				'name' => 'Email Options',
				//'desc' => 'This is a title description',
				'id'   => $prefix . 'email_options',
				'type' => 'title',
			),
		        array(
				'name' => 'Email Button Text',
				'desc' => 'Enter the text you would like to appear on the email submit button.',
				'id'   => $prefix . 'email_button_text',
				'type' => 'text_medium',
				'std'  => 'Get Access'
			),
			array(
				'name' => 'Custom Email Form for Shortcode',
				'desc' => 'Put your custom email form code here. Use the shortcode - [plugin_template_email_form/] - where ever you want the form to appear.',
				'id'   => $prefix . 'real_custom_email_form',
				'type' => 'textarea_code',
			),

			array(
				'name' => 'Content Area 2 Options',
				//'desc' => 'This is a title description',
				'id'   => $prefix . 'content_area_2_options',
				'type' => 'title',
			),
			
			array(
				'name' => 'Analytics',
				'desc' => 'Put your analytics code here.',
				'id'   => $prefix . 'analytics_code',
				'type' => 'textarea_code',
			),

		),
	);

	

	// Add other metaboxes as needed

	return $meta_boxes;
}
/*
//add_action( 'cmb_render_change_template', 'plugin_template_cmb_render_change_template', 10, 2 );
function cmb_render_change_template( $field, $meta ) {
    echo '<button name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '">Change</button>','<p class="cmb_metabox_description">', $field['desc'], '</p>';
}

*/


function ptd_shortcode($atts){
		extract( shortcode_atts( array(
			'tableid' => '',
		), $atts ) );
		ob_start();
		$postId = $tableid;
		//echo 'postId: '.$postId;
		
		include 'template/ptd-pricing-table-1.php';
		return ob_get_clean();
		
}

function destination_url_shortcode($atts){
	//global $plugin_template_destination_url;
	//return $plugin_template_destination_url;
}

function ptd_register_shortcodes(){
		add_shortcode( 'pricing_table_dynamite', array(&$this,'ptd_shortcode' ));
		//add_shortcode( 'ptd_pricing_table', array(&$this,'ptd_shortcode' ));
		//add_shortcode( 'dest_url', array(&$this,'destination_url_shortcode' ));
	}



function ptd_enqueue_general_scripts(){
	wp_register_style('ptd-ng-modal-css', plugin_dir_url(__FILE__).'ngModal/ng-modal.css');
	wp_enqueue_style('ptd-ng-modal-css');
	wp_register_script( 'ptd-ng-modal-javascript', plugin_dir_url(__FILE__).'ngModal/ng-modal.js', array( 'jquery' ) );
	wp_register_script( 'ptd-app-javascript', plugin_dir_url(__FILE__).'js/app.js', array( 'jquery' ) );
 	wp_enqueue_script('angular-js', 'http://code.angularjs.org/1.2.13/angular.js', array(), '3', false);
 	wp_enqueue_script('angular-sanitize-js', 'http://cdnjs.cloudflare.com/ajax/libs/angular.js/1.2.13/angular-sanitize.min.js', array(), '3', false);
 	wp_enqueue_script('ptd-app-javascript');
 	wp_register_script( 'ptd-ng-modal-javascript', plugin_dir_url(__FILE__).'ngModal/ng-modal.js', array( 'jquery' ) );
 	wp_enqueue_script('ptd-ng-modal-javascript');
 	
}

function ptd_enqueue_admin_scripts(){
	wp_register_style('ptd-pricing-table-css', plugin_dir_url(__FILE__).'css/ptd-pricing-table.css');
	wp_enqueue_style('ptd-pricing-table-css');
	//wp_register_style('ptd-ng-modal-css', plugin_dir_url(__FILE__).'ngModal/ng-modal.css');
	//wp_enqueue_style('ptd-ng-modal-css');
	//wp_register_script( 'ptd-ng-modal-javascript', plugin_dir_url(__FILE__).'ngModal/ng-modal.js', array( 'jquery' ) );
	//wp_register_script( 'ptd-app-javascript', plugin_dir_url(__FILE__).'js/app.js', array( 'jquery' ) );
 	//wp_enqueue_script('angular-js', 'http://code.angularjs.org/1.2.13/angular.js', array(), '3', false);
 	//wp_enqueue_script('ptd-ng-modal-javascript');
	//wp_enqueue_script('ptd-app-javascript');
}

function ptd_enqueue_frontend_scripts(){
	wp_register_style('ptd-frontend-pricing-table-css', plugin_dir_url(__FILE__).'css/ptd-frontend-pricing-table.css');
	wp_enqueue_style('ptd-frontend-pricing-table-css');
}


//add_action( 'init', 'plugin_template_initialize_plugin_template_meta_boxes', 9999 );
//add_action("init", "plugin_template_create_post_type");


//add_action('wp_insert_post', 'plugin_template_change_theme');
//add_action('admin_init', 'plugin_template_admin_init');
//add_action('init', 'ptd_enqueue_scripts');
//add_action( 'init', 'plugin_template_register_shortcodes');

function activate() {
	// register taxonomies/post types here
	$this->plugin_template_create_post_type();
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

//register_activation_hook( __FILE__, 'plugin_template_activate' );

/*
 * Initialize the metabox class.
 */
 
function initialize_meta_boxes() {

	if ( ! class_exists( 'cmb_Meta_Box' ) )
		require_once 'lib/metabox/init.php';

}


}

new PricingTableDynamiteCustomPostType();


?>