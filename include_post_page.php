<?php
  /*
    Plugin Name: Include Post or Page
    Plugin URI: http://tribhuj.com
    Description: Using this plugins you can include any post or page inside another popst or page. you just have to use a simple shortcode [tripostinc id=5].
    Version: 1.1
    Author: tribhuj
    Author URI: http://tribhuj.com

    Text Domain: include-post-page
  */
  

function Tribhuj_Post_Include($arg) {
	//var_dump($arg);
	$id = '';
	if(isset($arg['id'])){
		$id = $arg['id'];
	}
	$display = '';
	
	$args = array( 
				'p' => $id
			);

	$the_query = new WP_Query( $args );

	// The Loop
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$display .= get_the_content();
		}
	} 
	else {
		// no posts found
        $args1 = array(
            'page_id' => $id
        );

        $the_query1 = new WP_Query( $args1 );

        // The Loop
        if ( $the_query1->have_posts() ) {
            while ( $the_query1->have_posts() ) {
                $the_query1->the_post();
                $display .= get_the_content();
            }
        }
        else {
            // no posts found
        }
	}
	/* Restore original Post Data */
	wp_reset_postdata();
	
	return $display;
}

add_shortcode('tripostinc', 'Tribhuj_Post_Include');

/**********************************************************
* Testimonial Widget
**********************************************************/

add_action('widgets_init', 'tripostinc_load_cat_widgets' );

function tripostinc_load_cat_widgets() {
	register_widget('TriPostInc_CPost_Menu_Widget');
}

class TriPostInc_CPost_Menu_Widget extends WP_Widget {
	
	function TriPostInc_CPost_Menu_Widget() {
		
		$widget_ops = array( 'classname' => 'tripostinc-cpost-widget', 'description' => __('Use this widget to show post content in your sidebar.') );
		$control_ops = array( 'width' => '100%', 'id_base' => 'tripostinc-cpost-widget' );
		$this->WP_Widget( 'tripostinc-cpost-widget', __('TriPostInc CPost Widget'), $widget_ops, $control_ops );
	}

	function widget($args, $instance) {
		
		$data = array_merge($args, $instance);

		$postId = '';

		if(isset($data['post'])){
			$postId = $data['post'];
		}

		echo $data['before_widget'];
		
		if ( !empty( $data['title'] ) ) { echo $data['before_title'] . $data['title'] . $data['after_title']; };				

		$display = '';
		
		$args = array( 
					'p' => $postId
				);
	
		$the_query = new WP_Query( $args );
	
		// The Loop
		if ( $the_query->have_posts() ) {
			$i = 0;
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$display .= get_the_content();
                break;
			}
		} 
		else {
			// no posts found
            $args1 = array(
                'page_id' => $postId
            );

            $the_query1 = new WP_Query( $args1 );

            // The Loop
            if ( $the_query1->have_posts() ) {
                while ( $the_query1->have_posts() ) {
                    $the_query1->the_post();
                    $display .= get_the_content();
                    break;
                }
            }
            else {
                // no posts found
            }
		}
		/* Restore original Post Data */
		wp_reset_postdata();
		
		echo $display;
		
		echo $data['after_widget'];
	
	}
	
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['post'] = strip_tags($new_instance['post']);
		return $instance;
	}
	
	function form($instance) {
		$defaults = array( 'title' => 'Post');
		$instance = wp_parse_args( (array) $instance, $defaults); 		
		?>		
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
			<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('post'); ?>">Post/Page ID:</label>
			<input type="text" id="<?php echo $this->get_field_id('post'); ?>" name="<?php echo $this->get_field_name('post'); ?>" value="<?php echo $instance['post']; ?>" size="3" />
		</p>
		<?php
	}
	
}

/**********************************************************
 * Admin Menu and Admin Page
 **********************************************************/

add_action('admin_menu', 'TriPostInc');
function TriPostInc()
{
//create custom top-level menu
    add_menu_page('Search Post Id',
        'Include Post/Page',
        'administrator',
        'Tri_GetPost_PageId',
        'Tri_GetPost_PageId_Page',
        plugins_url('/images/tribhuj.com_logo.png', __FILE__)
    );
}

function Tri_GetPost_PageId_Page(){
    $ppTitle ="";
    if(isset($_POST['SaveSettings'])){
        $ppTitle =$_POST['ppTitle'];
    }
    echo '<h1>Search Post/Page ID</h1>
    <form name="Settings" method="post" action="">
        <table border="0" cellspacing="0" cellpadding="0">
            <tr style="text-align: center;"><td colspan="2"><b><u>Enter your Page or Post Title</u></b></td> </tr>
            <tr>
                <th scope="row">Page/Post Title : </th>
            <td>
                <input type="text" name="ppTitle" id="ppTitle" size="60%" value='.$ppTitle.'>
            </td>
            </tr>
            <tr style="text-align: center;">
                <td colspan="2">
                    <input type="SUBMIT" id="SaveSettings" name="SaveSettings" value="Save Settings" />
                </td>
            </tr>
        </table>
     </form>';

    if(isset($_POST['SaveSettings'])){
        $args = array(
            's' => $_POST['ppTitle']
        );
        $the_query = new WP_Query( $args );

        // The Loop
        if ( $the_query->have_posts() ) {
            echo '<table border="1" cellspacing="0" cellpadding="10">';
            echo '<tr><td><b>TITLE</b></td><td><b>ID</b></td></tr>';
            while ( $the_query->have_posts() ) {
                $the_query->the_post();
                echo '<tr><td>'.get_the_title().'</td><td>'.get_the_ID().'</td></tr>';
            }
            echo '</table>';
        }
        else {
            // no posts found
        }
        /* Restore original Post Data */
        wp_reset_postdata();
    }
}
?>