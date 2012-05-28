<?php
/*
Plugin Name: Related post widget
Plugin URI: http://ibnuyahya.com/wordpress-plugins/related-post-widget/
Description: a widget to display related post for single post base on current post category
Version: 1.1
Author: ibnuyahya
Author URI: http://ibnuyahya.com/
*/

add_action('widgets_init', 'fb_load_widgets');

function fb_load_widgets() {
    register_widget( 'Fbrelatedpost_Widget' );
}

class Fbrelatedpost_Widget extends WP_Widget {

    function Fbrelatedpost_Widget() {
        /* Widget settings. */
        $widget_ops     = array( 'classname' => 'fbrelatedpost', 'description' => __('Show related post in a single post base on current post category.', 'Related post') );

        /* Widget control settings. */
        $control_ops    = array( 'width' => 300, 'height' => 350, 'id_base' => 'related-post-widget' );

        /* Create the widget. */
        $this->WP_Widget( 'related-post-widget', __('Related Post Widget', 'Related post'), $widget_ops, $control_ops );
    }


    function widget( $args, $instance ) {
        extract( $args );

        //only display this widget on single page
        if (is_single()) {
            global $post;

            $title              = apply_filters('widget_title', $instance['title'] );
            $number_of_post     = $instance['fb_number_posts'];
            $category_as_title  = isset( $instance['fb_cat_as_title'] ) ? $instance['fb_cat_as_title'] : false;

            $cat                = array();
            $cat_name           = array();
            $categories         = get_the_category($post->ID);

            foreach($categories as $category) {
                $cat[]      = $category->cat_ID;
                $cat_name[] = $category->cat_name;
            }

            $cat        = implode(',',$cat);
            $cat_name   = implode(',',$cat_name);

            $myposts    = get_posts('numberposts=' . $number_of_post . '&category=' . $cat);

            echo $before_widget;


            if($category_as_title) {
                echo $before_title . $cat_name . $after_title;
            }elseif($title) {
                echo $before_title . $title. $after_title;
            }


            echo '<ul>';
            foreach($myposts as $post) {
                setup_postdata($post);

                echo '<li><a href="';
                the_permalink();
                echo '">';
                the_title();
                echo '</a></li>';
            }
            echo '</ul>';

            echo $after_widget;
        }
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['fb_cat_as_title'] = $new_instance['fb_cat_as_title'];
        $instance['fb_number_posts'] = $new_instance['fb_number_posts'];

        return $instance;
    }

    function form( $instance ) {

        /* Set up some default widget settings. */
        $defaults   = array( 'title' => __('Related post', 'hybrid'), 'fb_number_posts' => __(-1, 'fbrelatedpost'), 'fb_cat_as_title' => false );
        $instance   = wp_parse_args( (array) $instance, $defaults );

        $checked    = ( $instance['fb_cat_as_title'])?'checked = "checked"':'';
        echo <<<ST1
        <p>
            <label for='{$this->get_field_id('title')}'>Title:</label>
            <input type='text' value='{$instance['title']}' name='{$this->get_field_name('title')}' id='{$this->get_field_id('title')}' class='widefat'>
        </p>
        <p>
            <input type='checkbox' id='{$this->get_field_id('fb_cat_as_title')}' name='{$this->get_field_name('fb_cat_as_title')}' value='true' {$checked} >
            <label for='{$this->get_field_id('fb_cat_as_title')}' >Show category as title</label>
        </p>
        <p>
            <label for='{$this->get_field_id('fb_number_posts')}'>Show number of posts:</label>
            <input type='text' id='{$this->get_field_id('fb_number_posts')}' name='{$this->get_field_name('fb_number_posts')}' size='3' value='{$instance['fb_number_posts']}'><br/><small>-1 for all post</small>
        </p>
ST1;

    }


}
?>
