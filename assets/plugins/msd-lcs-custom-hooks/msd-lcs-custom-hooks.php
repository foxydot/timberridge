<?php
/*
Plugin Name: MSD/LCS Custom Hooks
Description: Custom hooks to tie together varius aspects of The Events Calendar, Gravity Forms, WP Job Manager, and MSD Custom Roles
Author: Catherine Sandrick
Version: 0.0.1 
Author URI: http://msdlab.com
*/

        ///events hooks
        $pop_events_id = '3'; //give number of form to use for RSVP
        add_filter( 'gform_pre_render_'.$pop_events_id, 'msdlab_populate_events' );
        add_filter( 'gform_pre_validation_'.$pop_events_id, 'msdlab_populate_events' );
        add_filter( 'gform_pre_submission_filter_'.$pop_events_id, 'msdlab_populate_events' );
        add_filter( 'gform_admin_pre_render_'.$pop_events_id, 'msdlab_populate_events' );
        function msdlab_populate_events( $form ) {
            if(class_exists('GFForms')){
                if(class_exists('Tribe__Events__Main')){
                        foreach ( $form['fields'] as &$field ) {
                    
                            if ( $field->type != 'select' || strpos( $field->cssClass, 'populate-events' ) === false ) {
                                continue;
                            }
                            
                            $events = tribe_get_events( apply_filters(
                                'tribe_events_list_widget_query_args', array(
                                    'posts_per_page' => -1,
                                    'start_date' => time(),
                                )
                            ) );
                            $choices = array();
                    
                            foreach ( $events as $event ) {
                                $choices[] = array( 'text' => $event->post_title, 'value' => $event->post_title );
                            }
                    
                            $field->placeholder = 'Select an event';
                            $field->choices = $choices;
                    
                        }
                
                        return $form;
                    }
                }
            }

            function msdlcs_event_shortcode_handler(){
                $events = tribe_get_events( apply_filters(
                                'tribe_events_list_widget_query_args', array(
                                    'posts_per_page' => -1,
                                    'start_date' => time(),
                                )
                            ) );
                foreach($events AS $event){
                    $ret .= '
                    [su_spoiler title="<strong>'.$event->post_title.'</strong> '.date("M d, Y H:ia",strtotime($event->EventStartDate)).'"]
                    '.$event->post_content.'[/su_spoiler]';
                }            
                $ret = '[su_accordion]'.$ret.'[/su_accordion]';
                if(shortcode_exists('su_accordion')){
                    return do_shortcode($ret);
                }
            }
            add_shortcode('list-events','msdlcs_event_shortcode_handler');
            
            
            //jobs hooks
            $pop_jobs_id = '1'; //give number of form to use for RSVP
        add_filter( 'gform_pre_render_'.$pop_jobs_id, 'msdlab_populate_jobs' );
        add_filter( 'gform_pre_validation_'.$pop_jobs_id, 'msdlab_populate_jobs' );
        add_filter( 'gform_pre_submission_filter_'.$pop_jobs_id, 'msdlab_populate_jobs' );
        add_filter( 'gform_admin_pre_render_'.$pop_jobs_id, 'msdlab_populate_jobs' );
        function msdlab_populate_jobs( $form ) {
            if(class_exists('GFForms')){
                if(class_exists('WP_Job_Manager')){
                        foreach ( $form['fields'] as &$field ) {
                    
                            if ( $field->type != 'select' || strpos( $field->cssClass, 'populate-jobs' ) === false ) {
                                continue;
                            }
                            
                            $jobs_query = get_job_listings();
                            $jobs = $jobs_query->posts;
                            $choices = array();
                    
                            foreach ( $jobs as $job ) {
                                $choices[] = array( 'text' => $job->post_title, 'value' => $job->post_title );
                            }
                    
                            $field->placeholder = 'Select a position';
                            $field->choices = $choices;
                    
                        }
                
                        return $form;
                    }
                }
            }

            function msdlcs_jobs_shortcode_handler(){
                $jobs_query = get_job_listings();
                $jobs = $jobs_query->posts;
                foreach($jobs AS $job){
                    $ret .= '
                    [su_spoiler title="<strong>'.$job->post_title.'</strong>"]
                    '.$job->post_content.'[/su_spoiler]';
                }            
                $ret = '[su_accordion]'.$ret.'[/su_accordion]';
                if(shortcode_exists('su_accordion')){
                    return do_shortcode($ret);
                }
            }
            add_shortcode('list-jobs','msdlcs_jobs_shortcode_handler');
