<?php
/**
 * Complete Quiz
 *
 * @package     AutomatorWP\Integrations\MasterStudy_LMS\Triggers\Complete_Quiz
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_MasterStudy_LMS_Complete_Quiz extends AutomatorWP_Integration_Trigger {

    public $integration = 'masterstudy_lms';
    public $trigger = 'masterstudy_lms_complete_quiz';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User attempts a quiz', 'automatorwp-masterstudy-lms-integration' ),
            'select_option'     => __( 'User <strong>attempts</strong> a quiz', 'automatorwp-masterstudy-lms-integration' ),
            /* translators: %1$s: Post title. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User attempts %1$s %2$s time(s)', 'automatorwp-masterstudy-lms-integration' ), '{post}', '{times}' ),
            /* translators: %1$s: Post title. */
            'log_label'         => sprintf( __( 'User attempts %1$s', 'automatorwp-masterstudy-lms-integration' ), '{post}' ),
            'action'            => array(
                'stm_lms_quiz_passed',
                'stm_lms_quiz_failed',
            ),
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 3,
            'options'           => array(
                'post' => automatorwp_utilities_post_option( array(
                    'name' => __( 'Quiz:', 'automatorwp-masterstudy-lms-integration' ),
                    'option_none_label' => __( 'any quiz', 'automatorwp-masterstudy-lms-integration' ),
                    'post_type' => 'stm-quizzes'
                ) ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_post_tags(),
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int $user_id
     * @param int $quiz_id
     * @param int $progress
     */
    public function listener( $user_id, $quiz_id, $progress ) {

        $course_id = intval( $_GET['course_id'] );

        automatorwp_trigger_event( array(
            'trigger'   => $this->trigger,
            'user_id'   => $user_id,
            'post_id'   => $quiz_id,
            'course_id' => $course_id,
        ) );

    }

    /**
     * User deserves check
     *
     * @since 1.0.0
     *
     * @param bool      $deserves_trigger   True if user deserves trigger, false otherwise
     * @param stdClass  $trigger            The trigger object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return bool                          True if user deserves trigger, false otherwise
     */
    public function user_deserves_trigger( $deserves_trigger, $trigger, $user_id, $event, $trigger_options, $automation ) {

        // Don't deserve if post is not received
        if( ! isset( $event['post_id'] ) ) {
            return false;
        }

        // Don't deserve if post doesn't match with the trigger option
        if( ! automatorwp_posts_matches( $event['post_id'], $trigger_options['post'] ) ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_MasterStudy_LMS_Complete_Quiz();