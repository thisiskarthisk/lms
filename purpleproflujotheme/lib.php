<?php
defined('MOODLE_INTERNAL') || die();



function local_purpleproflujotheme_before_standard_html_head() {
    global $PAGE;

    // Load general styles for all pages.
    $PAGE->requires->css('/local/purpleproflujotheme/css/style.css');

    // Load login-specific styles only on the login page.
    // if ($PAGE->pagetype === 'login-index') {
    //     $PAGE->requires->css('/local/purpleproflujotheme/css/login.css');
    // }

    // Load signup-specific styles only on the signup page.
    if ($PAGE->pagetype === 'login-signup') { // Adjust this pagetype if needed
        $PAGE->requires->css('/local/purpleproflujotheme/css/signup.css');
    }
}

// Teacher and Student Selection Radio Button
function local_purpleproflujotheme_extend_signup_form($mform) {
    // User Type Selection - Student or Teacher
    $radioarray = [];
    $radioarray[] = $mform->createElement('radio', 'roleid', '', get_string('student', 'local_purpleproflujotheme'), '5');
    $radioarray[] = $mform->createElement('radio', 'roleid', '', get_string('teacher', 'local_purpleproflujotheme'), '2');

    $mform->addElement('text', 'phone1', get_string('phone', 'local_purpleproflujotheme'));
    $mform->setType('phone1', PARAM_NOTAGS);
    $mform->addRule('phone1', get_string('phone_required', 'local_purpleproflujotheme'), 'required', null, 'client');
    $mform->addRule('phone1', get_string('phone_invalid', 'local_purpleproflujotheme'), 'regex', '/^\d{10}$/', 'client'); // Enforce 10-digit format

    // Add User Type to form
    $mform->addGroup($radioarray, 'roleid', get_string('roleid', 'local_purpleproflujotheme'), ['&nbsp;&nbsp;&nbsp;'], false);
    $mform->addRule('roleid', get_string('roleid_required', 'local_purpleproflujotheme'), 'required', null, 'client');
    $mform->setType('roleid', PARAM_ALPHANUMEXT);
}
// Teacher and Student Selection Radio Button


// Validate Form Data
function local_purpleproflujotheme_validate_signup_form($data, $files) {
    global $DB;
    $errors = [];

    // Validate phone number: Ensure it's exactly 10 digits
    if (!preg_match('/^\d{10}$/', $data['phone1'])) {
        $errors['phone1'] = get_string('phone_invalid', 'local_purpleproflujotheme');
    }

    
    // Check if the phone number already exists in the database
    if ($DB->record_exists('user', ['phone1' => $data['phone1']])) {
        $errors['phone1'] = get_string('phone_exists', 'local_purpleproflujotheme');
    }

    return $errors;
}
// Validate Form Data




// Role And Permissions Assignment in Teacher and Student
function local_purpleproflujotheme_user_created($event) {
    global $DB, $CFG;

    require_once($CFG->dirroot . '/lib/accesslib.php');

    $user = $event->get_record_snapshot('user', $event->objectid);
    if (!$user) {
        return;
    }

    // Get the selected role from the signup form
    $roleid = optional_param('roleid', 0, PARAM_INT);

    // Ensure role ID is valid
    if (!in_array($roleid, [2, 5])) { // Teacher (2) and Student (5)
        return;
    }

    // Assign role to user
    role_assign($roleid, $user->id, context_system::instance());

    // Optionally, you can redirect the user or send a confirmation message
}
// Role And Permissions Assignment in Teacher and Student


// Add a new "Subscription" tab to the course navigation
function local_purpleproflujotheme_extend_navigation_course($navigation, $course, $context) {
    global $PAGE, $USER;

    // Ensure we are in the course context.
    if (!$context->contextlevel == CONTEXT_COURSE) {
        return;
    }

    // Add a new "Payment" tab to the more menu.
    $navigation->add(
        get_string('subscriptiontab', 'local_purpleproflujotheme'), // Tab name.
        new \moodle_url('/local/purpleproflujotheme/payment.php', ['courseid' => $course->id]), // Link URL.
        navigation_node::TYPE_CUSTOM, // Node type.
        null,
        'subscriptiontab',
        new \pix_icon('t/subscription', '') // Optional: Icon for the tab.
    );
}
// Add a new "Subscription" tab to the course navigation





function local_purpleproflujotheme_extend_navigation_user_settings($navigation, $user, $context) {
    global $USER, $PAGE;

    // Add custom user menu item
    $node = $navigation->add(
        get_string('paymenthistory', 'local_purpleproflujotheme'), // Menu label
        new moodle_url('/local/purpleproflujotheme/paymenthistory.php'), // Target URL
        navigation_node::TYPE_SETTING,
        null,
        'paymenthistoryid',
        new pix_icon('t/add', '') // Optional icon
    );

    // Only show to logged-in users
    if (!isloggedin() || isguestuser()) {
        $node->hide();
    }
}




// function local_purpleproflujotheme_before_standard_top_of_body_html() {
//     global $PAGE, $CFG;

//     // Ensure the function only runs on the front page
//     if ($PAGE->pagetype !== 'site-index') {
//         return;
//     }

//     // Video File Path (Relative to Moodle)
//     $video_url = $CFG->wwwroot . '/local/purpleproflujotheme/videos/moodle_video.mp4';

//     // Inject custom header HTML with Video Background
//     $custom_header_html = '
//         <div class="custom-header">
//             <div class="video-wrapper">
//                 <video class="slider-video-bg" autoplay loop muted playsinline>
//                     <source src="' . $video_url . '" type="video/mp4">
//                     Your browser does not support the video tag.
//                 </video>
//             </div>
//         </div>
//     ';

//     // Output the HTML
//     echo $custom_header_html;
// }


// function local_purpleproflujotheme_before_standard_html_head() {
//     local_purpleproflujotheme_before_standard_top_of_body_html();
// }


// function local_purpleproflujotheme_before_standard_top_of_body_html() {
//     global $PAGE, $OUTPUT;

//     // Ensure this runs only on the front page
//     if ($PAGE->pagetype !== 'site-index') {
//         return;
//     }

//     // Use the plugin’s custom renderer
//     $output = $PAGE->get_renderer('local_purpleproflujotheme');

//     // Render the custom slider
//     echo $output->render_custom_slider();
// }



function local_purpleproflujotheme_override_login_page() {
    global $PAGE, $OUTPUT;

    if ($PAGE->pagetype === 'login-index') {
        $PAGE->set_pagelayout('login');
        $PAGE->set_context(context_system::instance());
        $PAGE->set_title('Login to Proflujo');
        $PAGE->set_heading('Welcome to Proflujo LMS');

        // Use the custom renderer
        $renderer = $PAGE->get_renderer('local_purpleproflujotheme');
        echo $renderer->render_login_page();
        exit; // Stop Moodle from loading the default login page
    }
}
