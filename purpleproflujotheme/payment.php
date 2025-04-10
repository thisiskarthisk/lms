<?php
require_once('../../config.php');

$courseid = required_param('courseid', PARAM_INT);
$course = get_course($courseid);
$context = context_course::instance($courseid);

require_login($course);

// Check the correct capability
// require_capability('local/purpleproflujotheme:viewpayment', $context);

$PAGE->set_url(new moodle_url('/local/purpleproflujotheme/payment.php', ['courseid' => $courseid]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('payment', 'local_purpleproflujotheme'));
$PAGE->set_heading(get_string('paynow', 'local_purpleproflujotheme'));

echo $OUTPUT->header();

echo html_writer::tag('h2', get_string('payment', 'local_purpleproflujotheme'));
echo html_writer::tag('p', 'Please complete your payment to access this course.');

echo $OUTPUT->footer();
