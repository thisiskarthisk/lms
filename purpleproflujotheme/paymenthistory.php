<?php
require_once('../../config.php');

global $DB, $USER, $PAGE, $OUTPUT;

$PAGE->set_url(new moodle_url('/local/paymenthistory/paymenthistory.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');

echo $OUTPUT->header();
echo $OUTPUT->heading('Payment History');

$userid = $USER->id;

// Check if table exists
if (!$DB->get_manager()->table_exists('local_paymenthistory')) {
    echo '<p>Error: Payment history table does not exist.</p>';
    echo $OUTPUT->footer();
    exit;
}

// Fetch payments
$payments = $DB->get_records('local_paymenthistory', ['userid' => $userid], 'timecreated DESC');

if ($payments) {
    echo '<table class="generaltable">
            <tr>
                <th>Course</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
            </tr>';

    foreach ($payments as $payment) {
        $course = $DB->get_record('course', ['id' => $payment->courseid], 'fullname');
        $coursename = $course ? $course->fullname : 'Unknown Course';

        echo '<tr>
                <td>' . format_string($coursename) . '</td>
                <td>$' . number_format($payment->amount, 2) . '</td>
                <td>' . ucfirst($payment->status) . '</td>
                <td>' . userdate($payment->timecreated, '%d %B %Y') . '</td>
              </tr>';
    }

    echo '</table>';
} else {
    echo '<p>No payment history found.</p>';
}

echo $OUTPUT->footer();
