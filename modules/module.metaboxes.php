<?php
// Add in Custom Options
function add_wp_cta_post_metaboxes() {
    add_meta_box(
        'wp_cta_tracking_metabox', // $id
        'Call to Action Options', // $title
        'show_wp_cta_post_metaboxes', // $callback
        'wp-call-to-action', // $page
        'normal', // $context
        'high'); // $priority
}

add_action('add_meta_boxes', 'add_wp_cta_post_metaboxes');
// Field Array
$var_id = wp_cta_ab_testing_get_current_variation_id();

$custom_wp_cta_metaboxes = array(
    array(
        'label' => 'CTA Width',
        'desc'  => 'Enter the Width of the CTA in pixels. Example: 300 or 300px',
        'id'    => 'wp_cta_width-'.$var_id,
        'options_area' => 'basic',
        'class' => 'cta-width',
        'type'  => 'text'
        ),
    array(
        'label' => 'CTA Height',
        'desc'  => 'Enter the Height of the CTA in pixels. Example: 300 or 300px',
        'id'    => 'wp_cta_height-'.$var_id,
        'options_area' => 'basic',
        'class' => 'cta-height',
        'type'  => 'text'
        ),
 
);

// The Callback
function show_wp_cta_post_metaboxes() {
    global $custom_wp_cta_metaboxes, $custom_wp_cta_metaboxes_two, $post;
    // Use nonce for verification
    //echo '<input type="hidden" name="custom_wp_cta_metaboxes_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';
    wp_nonce_field('save-custom-wp-cta-boxes','custom_wp_cta_metaboxes_nonce'); 
    // Begin the field table and loop
    echo '<div class="form-table">';
    echo '<div class="cta-description-box"><span class="calc button-secondary">Calculate height/width</span></div>';
   	wp_cta_render_metaboxes($custom_wp_cta_metaboxes);
    do_action( "wordpress_cta_add_meta" ); // Add extra meta boxes/options
    echo '</div>'; // end table
}

add_action( "wordpress_cta_add_meta", "wp_cta_bt_meta_boxes" );

function wp_cta_render_metaboxes($meta_boxes) {
	global $post, $wpdb;
	 foreach ($meta_boxes as $field) {
        // get value of this field if it exists for this post
        //print_r($meta_boxes);
        $meta = get_post_meta($post->ID, $field['id'], true);
        // begin a table row with
       	$no_label = array('html-block');
        echo '<div id='.$field['id'].' class="'.$field['options_area'].' wp-cta-option-row">';
       if (!in_array($field['type'],$no_label)) {
        	echo'<div class="wp_cta_label"><label class="'.$field['class'].'" for="'.$field['id'].'">'.$field['label'].'</label></div>';
            } 
          echo '<div class="wp-cta-option-area '.$field['class'].' field-'.$field['type'].'">';
                switch($field['type']) {
                    // text
                    case 'text':
                        echo '<input type="text" class="'.$field['class'].'" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="30" />
                                <br /><span class="description">'.$field['desc'].'</span>';
                    break;
                    case 'html-block':
                        echo '<div class="'.$field['class'].'">'.$field['desc'].'</div>';
                    break;
                
                    // textarea
                    case 'textarea':
                        echo '<textarea name="'.$field['id'].'" id="'.$field['id'].'" cols="250" rows="6">'.$meta.'</textarea>
                                <br /><span class="description">'.$field['desc'].'</span>';
                    break;
                    // checkbox
                    case 'checkbox':
                        echo '<input type="checkbox" name="'.$field['id'].'" id="'.$field['id'].'" ',$meta ? ' checked="checked"' : '','/>
                                <label for="'.$field['id'].'">'.$field['desc'].'</label>';
                    break;
                    // select
                    case 'select':
                        echo '<select name="'.$field['id'].'" id="'.$field['id'].'">';
                        foreach ($field['options'] as $option) {
                            echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="'.$option['value'].'">'.$option['label'].'</option>';
                        }
                        echo '</select><br /><span class="description">'.$field['desc'].'</span>';
                    break;
                    // radio
                    case 'radio':
                        foreach ( $field['options'] as $option ) {
                            echo '<input type="radio" name="'.$field['id'].'" id="'.$option['value'].'" value="'.$option['value'].'" ',$meta == $option['value'] ? ' checked="checked"' : '',' />
                                    <label for="'.$option['value'].'">'.$option['label'].'</label><br />';
                        }
                        echo '<span class="description">'.$field['desc'].'</span>';
                    break;
                    // checkbox_group
                    case 'checkbox_group':
                        foreach ($field['options'] as $option) {
                            echo '<input type="checkbox" value="'.$option['value'].'" name="'.$field['id'].'[]" id="'.$option['value'].'"',$meta && in_array($option['value'], $meta) ? ' checked="checked"' : '',' />
                                    <label for="'.$option['value'].'">'.$option['label'].'</label><br />';
                        }
                        echo '<span class="description">'.$field['desc'].'</span>';
                    break;
                    case 'meta_vals':
                    	$post_type = 'wp-lead';
					    $query = "
					        SELECT DISTINCT($wpdb->postmeta.meta_key) 
					        FROM $wpdb->posts 
					        LEFT JOIN $wpdb->postmeta 
					        ON $wpdb->posts.ID = $wpdb->postmeta.post_id 
					        WHERE $wpdb->posts.post_type = 'wp-lead' 
					        AND $wpdb->postmeta.meta_key != '' 
					        AND $wpdb->postmeta.meta_key NOT RegExp '(^[_0-9].+$)' 
					        AND $wpdb->postmeta.meta_key NOT RegExp '(^[0-9]+$)'
					    ";
					    $sql = 'SELECT DISTINCT meta_key FROM '.$wpdb->postmeta;
					    $meta_keys = $wpdb->get_col($wpdb->prepare($query, $post_type));
					   // print_r($fields);
						$list = get_post_meta( $post->ID, 'wp_cta_global_bt_values', true);
						//print_r($list);	
                    echo '<select multiple name="'.$field['id'].'[]" class="inbound-multi-select" id="'.$field['id'].'">';
						$nice_names = array(
					    "wpleads_first_name" => "First Name",
					    "wpleads_last_name" => "Last Name",
					    "wpleads_email_address" => "Email Address",
					    "wpleads_city" => "City",
					    "wpleads_areaCode" => "Area Code",
					    "wpleads_country_name" => "Country Name",
					    "wpleads_region_code" => "State Abbreviation",
					    "wpleads_region_name" => "State Name",
					    "wp_lead_status" => "Lead Status",
					    "events_triggered" => "Number of Events Triggered",
					    "lp_page_views_count" => "Page View Count",
					    "wpl-lead-conversion-count" => "Number of Conversions"
					);

				    foreach ($meta_keys as $meta_key) {
			    	
			    	if (array_key_exists($meta_key, $nice_names)) {
							$label = $nice_names[$meta_key];
							

							(in_array($meta_key, $list)) ? $selected = " selected='selected'" : $selected ="";
				           
				            echo '<option', $selected, ' value="'.$meta_key.'" rel="" >'.$label.'</option>';

				        }
				    }
		 			echo "</select><br><span class='description'>'".$field['desc']."'</span>";
		 			break;

		 			case 'list_type':
                    $categories = get_terms( 'wplead_list_category', array(
					 	'orderby'    => 'count',
					 	'hide_empty' => 0
					 ) );
					   // print_r($categories);
					    $selected_lists = array();
						$selected_lists = get_post_meta( $post->ID, 'wp_cta_global_bt_lists', true);
							
                   echo '<select multiple name="'.$field['id'].'[]" class="inbound-multi-select" id="'.$field['id'].'">';
					

				    foreach ($categories as $cat) {
			    		$term_id = $cat->term_id; 
			    		$cat_name = $cat->name;
						//echo $cat_name;		

							(in_array($term_id, $selected_lists)) ? $selected = " selected='selected'" : $selected ="";
				           
				            echo '<option', $selected, ' value="'.$term_id.'" rel="" >'.$cat_name.'</option>';

				    }
		 			echo "</select><br><span class='description'>'".$field['desc']."'</span>";
		 			break;

                } //end switch
        echo '</div></div>';
    } // end foreach
}	

// Save the Data
add_action('save_post', 'save_wp_cta_post_metaboxes', 15);
function save_wp_cta_post_metaboxes($post_id) {
    global $custom_wp_cta_metaboxes, $custom_wp_cta_metaboxes_two, $post;
	
	if ( isset($post) && 'wp-call-to-action' == $post->post_type ) 
    { 
	
		// verify nonce
		if (isset($_POST['custom_wp_cta_metaboxes_nonce'])&&!wp_verify_nonce($_POST['custom_wp_cta_metaboxes_nonce'], 'save-custom-wp-cta-boxes'))
			return $post_id;
			
		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			return $post_id;
			
		// check permissions
		if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {
			if (!current_user_can('edit_page', $post_id))
				return $post_id;
			} elseif (!current_user_can('edit_post', $post_id)) {
				return $post_id;
		}
		
		wp_cta_meta_save_loop($custom_wp_cta_metaboxes);
		//wp_cta_meta_save_loop($custom_wp_cta_metaboxes_two);
		//exit;
		// save taxonomies
		$post = get_post($post_id);
		if (isset($_POST['category']))
		{
			$category = $_POST['category'];
			wp_set_object_terms( $post_id, $category, 'category' );
		}
    
    }
}	

function wp_cta_meta_save_loop($save_values){
	global $post;
    // loop through fields and save the data
    foreach ($save_values as $field) {
        if($field['type'] == 'tax_select') continue;
		
		//print_r($field);
        $old = get_post_meta($post->ID, $field['id'], true);
        (isset($_POST[$field['id']])) ? $new = $_POST[$field['id']] : $new = '' ;
		
        if ($new && $new != $old) 
		{
            update_post_meta($post->ID, $field['id'], $new);
        } 
		elseif ('' == $new && $old) 
		{
            delete_post_meta($post->ID, $field['id'], $old);
        }
    } // end foreach
	//exit;
}

// Add in Main Headline
add_action( 'edit_form_after_title', 'wp_cta_wp_call_to_action_header_area' );
add_action( 'save_post', 'wp_cta_save_notes_area' );

function wp_cta_wp_call_to_action_header_area()
{
   global $post;
	$wp_cta_variation = (isset($_GET['wp-cta-variation-id'])) ? $_GET['wp-cta-variation-id'] : '0';

	$varaition_notes = get_post_meta( $post->ID , 'wp-cta-variation-notes', true );
    if ( empty ( $post ) || 'wp-call-to-action' !== get_post_type( $GLOBALS['post'] ) )
        return;

    if ( ! $varaition_notes = get_post_meta( $post->ID , 'wp-cta-variation-notes',true ) )
        $varaition_notes = '';
	
	$varaition_notes = apply_filters('wp_cta_edit_varaition_notes', $varaition_notes, 1);
		echo "<div id='wp-cta-notes-area'>";
   		wp_cta_display_notes_input('wp-cta-variation-notes',$varaition_notes);
    	echo '</div><div id="wp-cta-current-view">'.$wp_cta_variation.'</div><div id="switch-wp-cta">0</div>';

}

function wp_cta_save_notes_area( $post_id )
{
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;

    if ( ! current_user_can( 'edit_post', $post_id ) )
        return;

    $key = 'wp-cta-variation-notes';

    if ( isset ( $_POST[ $key ] ) )
        return update_post_meta( $post_id, $key, $_POST[ $key ] );

	//echo 1; exit;
    delete_post_meta( $post_id, $key );
}


add_filter( 'enter_title_here', 'wp_cta_change_enter_title_text', 10, 2 );  
function wp_cta_change_enter_title_text( $text, $post ) {  
	if ($post->post_type=='wp-call-to-action')
	{
        return 'Enter Call to Action Description';  
	}
	else
	{
		return $text;
	}
}  

// Add template select metabox
add_action('add_meta_boxes', 'wp_cta_add_custom_meta_box_select_templates');
function wp_cta_add_custom_meta_box_select_templates() { 
	
	add_meta_box(
		'wp_cta_metabox_select_template', // $id
		__( 'Template Options', 'wpcta_custom_meta' ),
		'wp_cta_display_meta_box_select_template', // $callback
		'wp-call-to-action', // $page
		'normal', // $context
		'high'); // $priority 
}

// Render select template box
function wp_cta_display_meta_box_select_template() {
	global $post;
	$template =  get_post_meta($post->ID, 'wp-cta-selected-template', true);
	$template = apply_filters('wp_cta_selected_template',$template); 
	
	if (!isset($template)||isset($template)&&!$template){ $template = 'default';}
	
	$name = apply_filters('wp_cta_selected_template_id','wp-cta-selected-template');
	
	// Use nonce for verification
	echo "<input type='hidden' name='wp_cta_wp-cta_custom_fields_nonce' value='".wp_create_nonce('wp-cta-nonce')."' />";
	?>
	
	<div id="wp_cta_template_change"><h2><a class="button-primary" id="wp-cta-change-template-button">Choose Another Template</a></div>
	<input type='hidden' id='wp_cta_select_template' name='<?php echo $name; ?>' value='<?php echo $template; ?>'>
		<div id="template-display-options"></div>							
	
	<?php
}

add_action('admin_notices', 'wp_cta_display_meta_box_select_template_container'); 	

// Render select template box
function wp_cta_display_meta_box_select_template_container() {
	global $wp_cta_data, $post,  $extension_data_cats, $current_url;
	
	if (isset($post)&&$post->post_type!='wp-call-to-action'||!isset($post)){ return false; }
	
	( !strstr( $current_url, 'post-new.php')) ?  $toggle = "display:none" : $toggle = "";
	
	$extension_data = wp_cta_get_extension_data();
	unset($extension_data['wp-cta']);

	$uploads = wp_upload_dir();
	$uploads_path = $uploads['basedir'];
	$extended_path = $uploads_path.'/wp-call-to-actions/templates/';

	$template =  get_post_meta($post->ID, 'wp-cta-selected-template', true);
	$template = apply_filters('wp_cta_selected_template',$template); 
	
	echo "<div class='wp-cta-template-selector-container' style='{$toggle}'>";
	echo "<div class='wp-cta-selection-heading'>";
	echo "<h1>Select Your Call to Action Template!</h1>"; 
	echo '<a class="button-secondary" style="display:none;" id="wp-cta-cancel-selection">Cancel Template Change</a>';
	echo "</div>";
		echo '<ul id="template-filter" >';
			echo '<li><a href="#" data-filter="*">All</a></li>';
			$categories = array();
			foreach ($extension_data_cats as $cat)
			{
				
				$slug = str_replace(' ','-',$cat['value']);
				$slug = strtolower($slug);
				$cat['value'] = ucwords($cat['value']);
				if (!in_array($cat['value'],$categories))
				{
					echo '<li><a href="#" data-filter=".'.$slug.'">'.$cat['value'].'</a></li>';
					$categories[] = $cat['value'];
				}
				
			}
		echo "</ul>";
		echo '<div id="templates-container" >';
		
		foreach ($extension_data as $this_template=>$data)
		{	 

			if (substr($this_template,0,4)=='ext-')
				continue;
		
			$cat_slug = str_replace(' ', '-', $data['info']['category']);
			$cat_slug = strtolower($cat_slug);
			
			// Get Thumbnail
			if (file_exists(WP_CTA_PATH.'templates/'.$this_template."/thumbnail.png"))
			{
				$thumbnail = WP_CTA_URLPATH.'templates/'.$this_template."/thumbnail.png"; 
			}				
			else
			{
				$thumbnail = WP_CTA_UPLOADS_URLPATH.$this_template."/thumbnail.png";
			} 
			?>
			<div id='template-item' class="<?php echo $cat_slug; ?>">
				<div id="template-box">
					<div class="wp_cta_tooltip_templates" title="<?php echo $data['info']['description']; ?>"></div>
				<a class='wp_cta_select_template' href='#' label='<?php echo $data['info']['label']; ?>' id='<?php echo $this_template; ?>'>
					<img src="<?php echo $thumbnail; ?>" class='template-thumbnail' alt="<?php echo $data['info']['label']; ?>" id='wp_cta_<?php echo $this_template; ?>'>
				</a>
				<p>
					<div id="template-title"><?php echo $data['info']['label']; ?></div>
					<a href='#' label='<?php echo $data['info']['label']; ?>' id='<?php echo $this_template; ?>' class='wp_cta_select_template'>Select</a> | 
					<a class='thickbox <?php echo $cat_slug;?>' href='<?php echo $data['info']['demo'];?>' id='wp_cta_preview_this_template'>Preview</a> 
				</p>
				</div>
			</div>
			<?php
		}
	echo '</div>';
	echo "<div class='clear'></div>";
	echo "</div>";
	echo "<div style='display:none;' class='currently_selected'>This is Currently Selected</a></div>";
}

// Custom CSS Widget
add_action('add_meta_boxes', 'add_custom_meta_box_wp_cta_custom_css');
add_action('save_post', 'wp_call_to_actions_save_custom_css');

function add_custom_meta_box_wp_cta_custom_css() {
   add_meta_box('wp_cta_3_custom_css', 'Custom CSS', 'wp_cta_custom_css_input', 'wp-call-to-action', 'normal', 'low');
}

function wp_cta_custom_css_input() {
	global $post;
		
	echo "<em>Custom CSS may be required to remove sidebars, increase the widget of the post content container to 100%, and sometimes to manually remove comment boxes.</em>";
	echo '<input type="hidden" name="wp-cta-custom-css-noncename" id="wp_cta_custom_css_noncename" value="'.wp_create_nonce(basename(__FILE__)).'" />';
	$custom_css_name = apply_filters('wp-cta-custom-css-name','wp-cta-custom-css');
	echo '<textarea name="'.$custom_css_name.'" id="wp-cta-custom-css" rows="5" cols="30" style="width:100%;">'.get_post_meta($post->ID,$custom_css_name,true).'</textarea>';
}

function wp_call_to_actions_save_custom_css($post_id) {
	global $post;
	if (!isset($post)||!isset($_POST['wp-cta-custom-css']))
		return;
	
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;

	
	$custom_css_name = apply_filters('wp-cta-custom-css-name','wp-cta-custom-css');
	
	$wp_cta_custom_css = $_POST[$custom_css_name];
	update_post_meta($post_id, 'wp-cta-custom-css', $wp_cta_custom_css);
}

//Insert custom JS box to landing page
add_action('add_meta_boxes', 'add_custom_meta_box_wp_cta_custom_js');
add_action('save_post', 'wp_call_to_actions_save_custom_js');

function add_custom_meta_box_wp_cta_custom_js() {
   add_meta_box('wp_cta_3_custom_js', 'Custom JS', 'wp_cta_custom_js_input', 'wp-call-to-action', 'normal', 'low');
}

function wp_cta_custom_js_input() {
	global $post;
	echo "<em></em>";
	//echo wp_create_nonce('wp-cta-custom-js');exit;
	$custom_js_name = apply_filters('wp-cta-custom-js-name','wp-cta-custom-js');
	
	echo '<input type="hidden" name="wp_cta_custom_js_noncename" id="wp_cta_custom_js_noncename" value="'.wp_create_nonce(basename(__FILE__)).'" />';
	echo '<textarea name="'.$custom_js_name.'" id="wp_cta_custom_js" rows="5" cols="30" style="width:100%;">'.get_post_meta($post->ID,$custom_js_name,true).'</textarea>';
}

function wp_call_to_actions_save_custom_js($post_id) {
	global $post;
	if (!isset($post)||!isset($_POST['wp-cta-custom-js']))
		return;
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
	
	$custom_js_name = apply_filters('wp-cta-custom-js-name','wp-cta-custom-js');
	
	$wp_cta_custom_js = $_POST[$custom_js_name];
	
	update_post_meta($post_id, 'wp-cta-custom-js', $wp_cta_custom_js);
}

//hook add_meta_box action into custom call fuction 
//wp_cta_generate_meta is contained in functions.php
add_action('add_meta_boxes', 'wp_cta_generate_meta');
function wp_cta_generate_meta()
{
	global $post;
	if ($post->post_type!='wp-call-to-action')
		return;
		
	$extension_data = wp_cta_get_extension_data();
	$current_template = get_post_meta( $post->ID , 'wp-cta-selected-template' , true);
	$current_template = apply_filters('wp_cta_variation_selected_template',$current_template, $post);

	foreach ($extension_data as $key=>$array)
	{
		if ($key!='wp-cta'&&substr($key,0,4)!='ext-' && $key==$current_template)
		{
			$template_name = ucwords(str_replace('-',' ',$key));
			$id = strtolower(str_replace(' ','-',$key));
			//echo $key."<br>";
			add_meta_box(
				"wp_cta_{$id}_custom_meta_box", // $id
				__( "<small>$template_name Options:</small>", "wp_cta_{$key}_custom_meta" ),
				'wp_cta_show_metabox', // $callback
				'wp-call-to-action', // post-type
				'normal', // $context
				'default',// $priority
				array('key'=>$key)
				); //callback args
		}
	}
	foreach ($extension_data as $key=>$array)
	{
		if (substr($key,0,4)=='ext-')
		{
			//echo 1; exit;
			$id = strtolower(str_replace(' ','-',$key));
			$name = ucwords(str_replace(array('-','ext '),' ',$key));
			if($key = "ext-cta-options") {
				$name = "Call to Action Options";
			} else {
				$name = $name . "Extension Options";
			}
			//echo $key."<br>";
			add_meta_box(
				"wp_cta_{$id}_custom_meta_box", // $id
				__( "$name", "wp_cta_{$key}_custom_meta" ),
				'wp_cta_show_metabox', // $callback
				'wp-call-to-action', // post-type
				'normal', // $context
				'default',// $priority
				array('key'=>$key)
				); //callback args
		}
	}
	
}

add_action('save_post', 'wp_cta_save_meta');
function wp_cta_save_meta($post_id) {
	global $post;
	
	$extension_data = wp_cta_get_extension_data();
	
	if (!isset($post))
		return;
		
	if ($post->post_type=='revision')
	{
		return;
	}
	
	if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) ||(isset($_POST['post_type'])&&$_POST['post_type']=='revision'))
	{
		return;
	}
		
	if ($post->post_type=='wp-call-to-action')
	{
		//print_r($extension_data);exit;
		//print_r($_POST);exit;
		//echo $_POST['wp-cta-selected-template'];exit;
		foreach ($extension_data as $key=>$data)
		{	
			if ($key=='wp-cta')
			{
				// verify nonce
				if (!isset($_POST["wp_cta_{$key}_custom_fields_nonce"])||!wp_verify_nonce($_POST["wp_cta_{$key}_custom_fields_nonce"], 'wp-cta-nonce'))
				{
					return $post_id;
				}
				
				$wp_cta_custom_fields = $extension_data[$key]['settings'];	
				
				foreach ($wp_cta_custom_fields as $field)
				{
					$id = $key."-".$field['id'];
					$old = get_post_meta($post_id, $id, true);				
					$new = $_POST[$id];	

					if (isset($new) && $new != $old ) {
						update_post_meta($post_id, $id, $new);
					} elseif ('' == $new && $old) {
						delete_post_meta($post_id, $id, $old);
					}
				}
			}
			else if ((isset($_POST['wp-cta-selected-template'])&&$_POST['wp-cta-selected-template']==$key)||substr($key,0,4)=='ext-')
			{	
				$wp_cta_custom_fields = $extension_data[$key]['settings'];		

				// verify nonce
				if (!wp_verify_nonce($_POST["wp_cta_{$key}_custom_fields_nonce"], 'wp-cta-nonce'))
				{
					return $post_id;
				}
				
				// loop through fields and save the data
				foreach ($wp_cta_custom_fields as $field) {
					$id = $key."-".$field['id'];

					
					if($field['type'] == 'tax_select') 
						continue;
					
					if (!isset($_POST[$id]))
					{
						continue;
					}
					$old = get_post_meta($post_id, $id, true);				
					$new = $_POST[$id];
					//echo "id:$id";
					//echo "<br>old:$old:<br>new:".$new."<br>";
					//echo "<br>";
					
					if (isset($new) && $new != $old ) {
						update_post_meta($post_id, $id, $new);
					} elseif ('' == $new && $old) {
						delete_post_meta($post_id, $id, $old);
					}
				} // end foreach		
				//exit;
			}
		}
		
		//make sure template is saved
		//$selected_template = $_POST['wp-cta-selected-template'];
		//update_post_meta($post_id, $field['id'], $new);
		
		// save taxonomies
		$post = get_post($post_id);
		//$category = $_POST['wp_call_to_action_category'];
		//wp_set_object_terms( $post_id, $category, 'wp_call_to_action_category' );
	}
}
