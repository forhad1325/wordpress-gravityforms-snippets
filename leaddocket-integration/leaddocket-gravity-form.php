<?php
/**
 * LeadDocket + Gravity Forms Integration
 * Sends form submission data to the LeadDocket CRM API.
 * Update the form ID, field IDs and endpoint URL to match your setup.
 */

// gform_after_submission_4 = runs after form ID 4 submits
add_action('gform_after_submission_4', 'send_lead_leaddocket', 10, 2);

function send_lead_leaddocket($entry, $form) {
    $endpoint_url = 'https://YOUR_DOMAIN.leaddocket.com/opportunities/form/YOUR_FORM_ID';

    $body = array(
        'First'   => rgar($entry, '1'), // First Name field ID
        'Last'    => rgar($entry, '2'), // Last Name field ID
        'Phone'   => rgar($entry, '3'), // Phone field ID
        'Email'   => rgar($entry, '4'), // Email field ID
        'Summary' => rgar($entry, '5'), // Summary field ID
    );

    wp_remote_post($endpoint_url, array(
        'body'    => $body,
        'headers' => array('Content-Type' => 'application/x-www-form-urlencoded'),
    ));
}
