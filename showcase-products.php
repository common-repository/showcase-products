<?php
/*
Plugin Name: Showcase Products
Plugin URI: http://greenlemon.in
Description: Showcase your products
Author: Anupa
Version: 1.0
Author URI: http://greenlemon.in/
*/
/**
 * Showcase your products.
 */
if ( ! defined( 'SP_BASE_FILE' ) )
    define( 'SP_BASE_FILE', __FILE__ );
if ( ! defined( 'SP_BASE_DIR' ) )
    define( 'SP_BASE_DIR', dirname( SP_BASE_FILE ) );
if ( ! defined( 'SP_PLUGIN_URL' ) )
    define( 'SP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
   
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
function sp_init() {
  load_plugin_textdomain( 'showcase_products', false, 'showcase_products/languages' );
}
add_action('init', 'sp_init');

add_action( 'init', 'sp_create_posttype' );
function sp_create_posttype() {
  
 $labels = array(
		'name' => _x( 'Products', 'showcase_products' ),
		'singular_name' => _x( 'Product', 'showcase_products' ),
		'add_new' => _x( 'Add New ', 'showcase_products' ),
		'add_new_item' => _x( 'Add New Product', 'showcase_products' ),
		'edit_item' => _x( 'Edit Product', 'cshowcase_products' ),
		'new_item' => _x( 'New Product', 'showcase_products' ),
		'view_item' => _x( 'View Product', 'showcase_products' ),
		'search_items' => _x( 'Search Products', 'showcase_products' ),
		'not_found' => _x( 'No Products found', 'showcase_products' ),
		'not_found_in_trash' => _x( 'No Products found in Trash', 'showcase_products' ),
		'menu_name' => _x( 'Products', 'showcase_products' ),
    );
$args = array(
		'labels' => $labels,
		'hierarchical' => false,
		'description' => 'Custom Products Posts',
		'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'comments', 'revisions' ),
		//'taxonomies' => array( ''),
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 5,
		
		'menu_icon' => plugin_dir_url( __FILE__ ) . 'images/sp_icon.png',
		'show_in_nav_menus' => true,
		'publicly_queryable' => true,
		'exclude_from_search' => false,
		'query_var' => true,
		'can_export' => true,
		'rewrite' => array('slug' => 'products/%products_categories%','with_front' => TRUE),
		'public' => true,
		'has_archive' => 'products',
		'capability_type' => 'post'
    );

register_post_type( 'sp_products', $args );

}

function products_taxonomy() {  
    register_taxonomy(  
        'products_categories',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces). 
        'sp_products',        //post type name
        array(  
            'hierarchical' => true,  
            'label' => _x( 'Products Categories', 'showcase_products' ), //Display name
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'products', // This controls the base slug that will display before each term
                'with_front' => false // Don't display the category base before 
            )
        )  
    );  
}  
add_action( 'init', 'products_taxonomy');
/*------------------------------------*/
add_action( 'add_meta_boxes', 'add_product_metaboxes' );
function add_product_metaboxes() {
	    add_meta_box('sp_product_price', _x('Product Price','showcase_products'), 'sp_product_price', 'sp_products', 'side', 'default');
	    add_meta_box('sp_product_short_description',  _x('Product Short Description','showcase_products'), 'sp_product_short_description', 'sp_products', 'normal', 'high');
	}

	
function sp_product_short_description(){
	    global $post;
	    // Noncename needed to verify where the data originated
	    echo '<input type="hidden" name="productmeta_noncename" id="productmeta_noncename" value="' .
	    wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	    // Get the location data if its already been entered
	    $short_description = get_post_meta($post->ID, '_short_description', true);	     
	    echo '<textarea name="_short_description"  class="widefat" />' . $short_description  . '</textarea>';
}

function sp_product_price() {
	    global $post;
	    $price = get_post_meta($post->ID, '_price', true);	     
	    echo '<input type="text" name="_price" value="' . $price  . '" class="widefat" />';
	}

/*
 * 
 * Save Product Meta
 */
function sp_save_products_meta($post_id, $post)
	{
	    if ( isset($_POST['productmeta_noncename']) && !wp_verify_nonce( $_POST['productmeta_noncename'], plugin_basename(__FILE__) ) ) {return $post->ID;}
	    if ( !current_user_can( 'edit_post', $post->ID ))
	        return $post->ID; 
		$products_meta[]='';
		if(isset($_POST['_price'])){
       $products_meta['_price'] = $_POST['_price'];
 	   }
		if(isset( $_POST['_short_description'])){
		$products_meta['_short_description'] = $_POST['_short_description'];
		}	     

	    // Add values of $products_meta as custom fields
	    
	     foreach ($products_meta as $key => $value){
		  // Cycle through the $products_meta array!
	        if( $post->post_type == 'revision' ) return; 
	        $value = implode(',', (array)$value); 
	        if(get_post_meta($post->ID, $key, FALSE)) { 
	        update_post_meta($post->ID, $key, $value);
	        } else { 
	        add_post_meta($post->ID, $key, $value);
	        }
	        if(!$value) delete_post_meta($post->ID, $key); 
	    }	 
	}	 
	add_action('save_post', 'sp_save_products_meta', 1, 2); 
/*
 * 
 * Custom Pagination
 */
        
function custom_pagination($numpages = '', $pagerange = '', $paged='') {
    global $pagerange;
    if (empty($pagerange)) {
        $pagerange = 2;
    }
    if( get_query_var( 'paged' ) )
    $paged = get_query_var( 'paged' );
    else {
        if( get_query_var( 'page' ) )
        $paged = get_query_var( 'page' );
        else
        $paged = 1;
        set_query_var( 'paged',  $paged );
        $paged =  $paged;
    }
    $big = 999999999; 
    $pagination_args = array(
        'base' => preg_replace('/\?.*/', '/', get_pagenum_link()) . '%_%',
        'format' => '?page=%#%&page_id='.get_the_ID().'',
        'total'           => $numpages,
        'current'         => $paged,
        'show_all'        => False,
        'end_size'        => 1,
        'mid_size'        => $pagerange,
        'prev_next'       => True,
        'prev_text'       => __('&laquo;'),
        'next_text'       => __('&raquo;'),
        'type'            => 'plain',
        'add_args'        => false,
        'add_fragment'    => ''
    );
    $paginate_links = paginate_links($pagination_args);
    if ($paginate_links) {
        echo "<nav class='custom-pagination'>";
        echo "<span class='page-numbers page-num'>Page " . $paged . " of " . $numpages . "</span> ";
        echo $paginate_links;
        echo "</nav>";
    }
}
/*
 * Custom Pagination Archive
 */
 
function custom_pagination_archive($numpages = '', $pagerange = '', $page='') {

  if (empty($pagerange)) {
    $pagerange = 2;
  }
  global $paged;
  if (empty($paged)) {
    $paged = 1;
  }
  if ($numpages == '') {
    global $wp_query;
    $numpages = $wp_query->max_num_pages;
    if(!$numpages) {
        $numpages = 1;
    }
  }
$big = 999999999; 
  $pagination_args = array(
    //'base'            => get_pagenum_link(1) . '%_%',
   // 'format'          => '&page/%#%',
   // 'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
    'base' => preg_replace('/\?.*/', '/', get_pagenum_link()) . '%_%',
'format' => '?paged=%#%',
    'total'           => $numpages,
    'current'         => $paged,
    'show_all'        => False,
    'end_size'        => 1,
    'mid_size'        => $pagerange,
    'prev_next'       => True,
    'prev_text'       => __('&laquo;'),
    'next_text'       => __('&raquo;'),
    'type'            => 'plain',
    'add_args'        => false,
    'add_fragment'    => ''
  );

  $paginate_links = paginate_links($pagination_args);

  if ($paginate_links) {
    echo "<nav class='custom-pagination'>";
      echo "<span class='page-numbers page-num'>Page " . $paged . " of " . $numpages . "</span> ";
      echo $paginate_links;
    echo "</nav>";
  }

}
function show_sp_products() {
        $paged = ( get_query_var('page') ) ? get_query_var('page') : 1;
        $query_args = array(
        'post_type' => 'sp_products',
        'paged' => $paged
        );
    // create a new instance of WP_Query
    $the_query = new WP_Query( $query_args );
    echo "<div class='features_items'>";
    if ( $the_query->have_posts() ) : while ( $the_query->have_posts() ) : $the_query->the_post();
        $post_id = get_the_ID();
        echo '<article>';
        echo "<div class='col-sm-4'><div class='product-image-wrapper'><div class='single-products'>
	<div class='productinfo text-center'>"; 
	$src = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'medium' );
	$url = $src[0];
	echo "<img src='$url'/>"; ?>
	<p><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
	<h2><?php the_title(); ?></h2></a></p>
	<p>Price: <?php echo get_post_meta($post_id, "_price", true); ?></p>
	<?php echo '</div><div class="product-overlay"><div class="overlay-content">';?>
	<h2><?php echo get_post_meta($post_id, "_price", true); ?></h2>
	<p><?php echo get_post_meta($post_id, "_short_description", true); ?></p>
	<a href="<?php the_permalink() ?>" class="btn btn-default add-to-cart"><i class="fa fa-shopping-cart"></i>View</a>
        <?php echo '</div></div></div></div></div>';
        endwhile;
        wp_reset_postdata();
        
        if (function_exists('custom_pagination')) {
        //echo $the_query->max_num_pages;
        custom_pagination($the_query->max_num_pages,"",$paged);
        }
    ?>
    <?php else:  ?>
    <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
    <?php endif; 
    }
    add_shortcode( 'all_products', 'show_sp_products' );
/*-------------------------------------*/
function sp_template_chooser( $template ) {
    // Post ID
    $post_id = get_the_ID();
    // For all other CPT
    if ( get_post_type( $post_id ) != 'sp_products' ) {
        return $template;
    }
    // Else use custom template
    if ( is_single() ) {
        return sp_get_template_hierarchy( 'single' );
    }
     if ( is_archive() ) {
        return sp_get_template_hierarchy( 'archive' );
    }
}

function sp_get_template_hierarchy( $template ) {
 
    // Get the template slug
    $template_slug = rtrim( $template, '.php' );
    $template = $template_slug . '.php';
 
    // Check if a custom template exists in the theme folder, if not, load the plugin template file
    if ( $theme_file = locate_template( array( 'plugin_template/' . $template ) ) ) {
        $file = $theme_file;
    }
    else {
        $file = SP_BASE_DIR . '/templates/' . $template;
    }
 
    return apply_filters( 'rc_repl_template_' . $template, $file );
}

add_filter( 'template_include', 'sp_template_chooser' );
/*=================================widget===============================================*/
 class sp_categories extends WP_Widget {
	    // constructor
	    function sp_categories() {

	              // parent::WP_Widget(false, $name = __('SP Categories', 'showcase-products') );
	               parent::__construct(false, $name = __('SP Categories', 'showcase-products') );

	    }
	    // widget form creation

 	  function form($instance) { 
	   if( $instance) 
	   {
		$title = esc_attr($instance['title']); 
		
	   }
	   else
	   {$title='';}
	   ?>
	   <p>
	   <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Menu Title', 'page-wise-gallery'); ?></label>
	   <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
	   
	   </p>	
   		   
	  <?php	    }
	 
	    // widget update

 	    function update($new_instance, $old_instance) {

	        $instance = $old_instance;
	        $instance['title'] = strip_tags($new_instance['title']);
	       
	        return $instance;

	    }
	
	    // widget display

	    function widget($args, $instance) {

	       extract( $args );
	       $title = apply_filters('widget_title', $instance['title']);
	     
	       echo $before_widget;
	       if ( $title ) { ?>
	        <?php //echo $before_title . $title . $after_title; ?>
	     
	      <?php }
	       //echo do_shortcode('[pwg_gallery]');
	        
	    $args = array(
	'show_option_all'    => '',
	'orderby'            => 'name',
	'order'              => 'ASC',
	'style'              => 'list',
	'show_count'         => 0,
	'hide_empty'         => 1,
	'use_desc_for_title' => 1,
	'child_of'           => 0,
	'feed'               => '',
	'feed_type'          => '',
	'feed_image'         => '',
	'exclude'            => '',
	'exclude_tree'       => '',
	'include'            => '',
	'hierarchical'       => 1,
	'title_li'           => __( '' ),
	'show_option_none'   => __( '' ),
	'number'             => null,
	'echo'               => 1,
	'depth'              => 0,
	'current_category'   => 0,
	'pad_counts'         => 0,
	'taxonomy'           => 'products_categories',
	'walker'             => null
    );?>
    
    <div class="cat-list">
  <h3 class="title">  <?php echo  $title; ?></h3>
  <?php  wp_list_categories( $args );       ?>
    
</div>       
</div>
</div>       
</ul>
<?php echo $after_widget;

	    }
	}
	 
// register widget
	add_action('widgets_init', create_function('', 'return register_widget("sp_categories");'));


/*--------------scripts------------------------*/
function sp_products_scripts() {
	wp_enqueue_style( 'sp', plugin_dir_url( __FILE__ ) . 'css/sp_products.css' );
	wp_enqueue_style( 'main', plugin_dir_url( __FILE__ ) . 'css/main.css' );
	wp_enqueue_style( 'bootstrap', plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css' );
}
add_action( 'wp_enqueue_scripts', 'sp_products_scripts' );
?>