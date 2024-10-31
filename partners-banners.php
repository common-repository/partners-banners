<?php
/*
Plugin Name: Zedna Partners Banners
Plugin URI: https://sellfy.com/p/F2hR/
Description: Display partners banners in widget
Version: 1.4
Author: Radek Mezulanik
Author URI: https://www.mezulanik.cz
License: GPL2
Text Domain: partners-banners
Domain Path: /languages/
*/

class partners_banners_widgets {
    public function __construct() {
        add_action( 'widgets_init', array( $this, 'load' ), 9 );
        add_action( 'widgets_init', array( $this, 'init' ), 10 );
        register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );
    }

    public function load() {
        $dir = plugin_dir_path( __FILE__ );
        
    include_once( $dir . 'inc/widget-partners-banners.php' );
    
    }

    public function init() {
        if ( ! is_blog_installed() ) {
            return;
        }

        load_plugin_textdomain( 'partners-banners-widgets', false, 'partners-banners/languages' );

        register_widget( 'Partners_Banners_Widget' );
    }

    public function uninstall() {}
}

$partners_banners_widgets = new partners_banners_widgets();


/* Custom post types */
add_action( 'init', 'create_post_type_banner' );
function create_post_type_banner() {
  register_post_type( 'partnersbanner',
    array(
      'labels' => array(
        'name' => __( 'Partner banners' ),
        'singular_name' => __( 'Partners banner' )
      ),
      'supports' => array( 'title', 'thumbnail'),  
      'public' => true,
      'has_archive' => true,
      'taxonomies' => array('category'),
      'rewrite' => array( 'slug' => 'partnersbanners', 'with_front' => true)
    )
  ); 
}
/* // Custom post types */

// Add the Meta Box for partner custom field
function add_partner_meta_box_banner() {
    add_meta_box(
        'partner_meta_box', // $id
        'Partner link', // $title
        'show_partner_meta_box_banner', // $callback
        'partnersbanner', // $page
        'normal', // $context
        'high'); // $priority
}
add_action('add_meta_boxes', 'add_partner_meta_box_banner', 0 );

// Field Array for partner custom field
$partner_meta_fields = array(
     array(
        'label'=> 'Partner URL link',
        'desc'  => 'e.g.: http://www.partnerurl.com <p>Insert partner logo as Thumbnail image.</p>',
        'id'    => 'partnerlink',
        'type'  => 'text'
    )
);

// The Callback for partner and homepage partner custom field
function show_partner_meta_box_banner() {
global $partner_meta_fields, $post;
// Use nonce for verification
echo '<input type="hidden" name="partner_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';
     
    // Begin the field table and loop
    echo '<table class="form-table">';
    foreach ($partner_meta_fields as $field) {
        // get value of this field if it exists for this post
        $meta = get_post_meta($post->ID, $field['id'], true);
        // begin a table row with
        echo '<tr>        
                <th><label for="'.$field['id'].'">'.$field['label'].'</label></th>
                <td>';
                switch($field['type']) {
                case 'select':                    
                  echo '</td><td><select>';
                  echo '<option value="'.$field['options']['partnerlink'].'">'.$field['options']['partnerlink'].'</option>';
                  echo '</select></td></tr>';
                break;
                case 'text':
                  echo '</td><td><input type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="30" />&nbsp;<span class="description">'.$field['desc'].'</span></td></tr>';
                  echo '<tr><th><label>Banners Partners Pro</label></th><td></td><td><a href="https://mezulanik.cz" id="F2hR" class="sellfy-buy-button">Buy now</a><script type="text/javascript" src="https://sellfy.com/js/api_buttons.js"></script>&nbsp;<span class="description">Check out new <a href="https://mezulanik.cz" target="_blank">features</a>.</span></td></tr>';
                break;
                
                    // case items will go here
                } //end switch
        // text
    } // end foreach
    echo '</table>'; // end table    
}


// Save the Data for partner custom field
function save_partner_meta_banner($post_id) {
    global $partner_meta_fields;
     
    // verify nonce
    if (!wp_verify_nonce($_POST['partner_meta_box_nonce'], basename(__FILE__))) 
        return $post_id;
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $post_id;
    // check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id))
            return $post_id;
        } elseif (!current_user_can('edit_post', $post_id)) {
            return $post_id;
    }
     
    // loop through fields and save the data
    foreach ($partner_meta_fields as $field) {
        $old = get_post_meta($post_id, $field['id'], true);
        $new = $_POST[$field['id']];
        if ($new && $new != $old) {
            update_post_meta($post_id, $field['id'], $new);
        } elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field['id'], $old);
        }
    } // end foreach
}
add_action('save_post', 'save_partner_meta_banner');
/* // ADD CUSTOM FIELDS TO partner POSTS */

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'partners_banners_action_links' );

function partners_banners_action_links( $links ) {
   $links[] = '<a href="https://profiles.wordpress.org/zedna/#content-plugins" target="_blank">More plugins by Zedna Brickick Website</a>';
   return $links;
}

require_once( plugin_dir_path( __FILE__ ) . 'page-template-class.php' );
add_action( 'plugins_loaded', array( 'Partners_Banners_Layout', 'get_instance' ) );

/* Partners posts expiration */
function partnerbanner_add_expiry_date_metabox() {
    add_meta_box( 
        'partnerbanner_expiry_date_metabox', 
        __( 'Expiry Date', 'partnerbanner'), 
        'partnerbanner_expiry_date_metabox_callback', 
        'partnersbanner', 
        'side', 
        'high'
    );
}
add_action( 'add_meta_boxes', 'partnerbanner_add_expiry_date_metabox' );

function partnerbanner_expiry_date_metabox_callback( $post ) { ?>
     
    <form action="" method="post">
         
        <?php        
        // add nonce for security
        wp_nonce_field( 'partnerbanner_expiry_date_metabox_nonce', 'partnerbanner_nonce' );
         
        //retrieve metadata value if it exists
        $partnerbanner_expiry_date = get_post_meta( $post->ID, 'partnerbanner_expires', true );
        ?>
         
        <label for "partnerbanner_expiry_date"><?php __('Expiry Date', 'partnerbanner' ); ?></label>
                 
        <input type="date" class="partnerbannerExpiryDate" name="partnerbanner_expiry_date" value=<?php echo esc_attr( $partnerbanner_expiry_date ); ?> >    
     
    </form>
     
<?php }

function partnerbanner_save_expiry_date_meta( $post_id ) {
     
    // Check if the current user has permission to edit the post. */
    if ( !current_user_can( 'edit_post', $post->ID ) )
    return;
     
    if ( isset( $_POST['partnerbanner_expiry_date'] ) ) {        
        $new_expiry_date = ( $_POST['partnerbanner_expiry_date'] );
        update_post_meta( $post_id, 'partnerbanner_expires', $new_expiry_date );      
    }
     
}
add_action( 'save_post', 'partnerbanner_save_expiry_date_meta' );

function delete_expired_partnersbanners_daily() {
    if ( ! wp_next_scheduled( 'delete_expired_partnersbanners' ) ) {
        wp_schedule_event( time(), 'hourly', 'delete_expired_partnersbanners');
    }
}
add_action( 'wp', 'delete_expired_partnersbanners_daily' );

function delete_expired_partnersbanners_callback() {
    $args = array(
        'post_type' => 'partnersbanner',
        'posts_per_page' => -1
    );

    $partnersbanners = new WP_Query($args);
    if ($partnersbanners->have_posts()):
        while($partnersbanners->have_posts()): $partnersbanners->the_post();    

            $expiration_date = get_post_meta( get_the_ID(), 'partnerbanner_expires', true );
            
            if ($expiration_date){
                $expiration_date_time = strtotime($expiration_date);

                if (($expiration_date_time < time())) {
                    $post = array( 'ID' =>  get_the_ID(), 'post_status' => 'draft' );
                    wp_update_post($post);          
                }
            }

        endwhile;
    endif;
}
add_action( 'delete_expired_partnersbanners', 'delete_expired_partnersbanners_callback' );
/* #Partners posts expiration */

/* Partners expiration in post list*/
// Get expiry date
function partnerbanner_get_expire_date($post_ID) {
    return get_post_meta( $post_ID, 'partnerbanner_expires', true );
}
// ADD NEW COLUMN
function partnerbanner_columns_head($defaults) {
    $defaults['expires'] = __( 'Expires', 'partners-banners' );
    return $defaults;
}
add_filter('manage_posts_columns', 'partnerbanner_columns_head');
 
// SHOW THE EXPIRY DATE
function partnerbanner_columns_content($column_name, $post_ID) {
    if ($column_name == 'expires') {
        $partner_expire_date = partnerbanner_get_expire_date($post_ID);
        if ($partner_expire_date) {
            echo date(get_option('date_format'), strtotime($partner_expire_date));
        }
    }
}
add_action('manage_posts_custom_column', 'partnerbanner_columns_content', 10, 2);
