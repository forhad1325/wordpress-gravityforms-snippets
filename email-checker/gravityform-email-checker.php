<?php
/**
 * Gravity Forms Email Checker
 * Validates submitted emails against a public Google Sheet allowlist.
 */

add_filter('gform_validation_36', 'check_email_from_google_sheet_form_36');
add_filter('gform_validation_37', 'check_email_from_google_sheet_form_37');

function check_email_from_google_sheet_form_36($validation_result) {
    return gform_email_sheet_validation($validation_result, 2, 'https://docs.google.com/spreadsheets/d/YOUR_SHEET_ID/export?format=csv');
}
function check_email_from_google_sheet_form_37($validation_result) {
    return gform_email_sheet_validation($validation_result, 2, 'https://docs.google.com/spreadsheets/d/YOUR_SHEET_ID/export?format=csv');
}

function gform_email_sheet_validation($validation_result, $email_field_id, $csv_url) {
    $form = $validation_result['form'];
    $entry = GFFormsModel::get_current_lead();
    $submitted_email = isset($entry[$email_field_id]) ? strtolower(trim($entry[$email_field_id])) : '';
    $allowed_emails = get_emails_from_csv_url($csv_url);

    if (!in_array($submitted_email, $allowed_emails)) {
        foreach ($form['fields'] as &$field) {
            if ($field->id == $email_field_id) {
                $field->failed_validation = true;
                $field->validation_message = 'This email is not authorized to submit the form.';
                break;
            }
        }
        $validation_result['form'] = $form;
        $validation_result['is_valid'] = false;
    }
    return $validation_result;
}

function get_emails_from_csv_url($url) {
    $emails = [];
    if (($handle = fopen($url, "r")) !== FALSE) {
        fgetcsv($handle); // Skip header
        while (($data = fgetcsv($handle)) !== FALSE) {
            foreach ($data as $cell) {
                $cell = strtolower(trim($cell));
                if (filter_var($cell, FILTER_VALIDATE_EMAIL)) {
                    $emails[] = $cell;
                    break;
                }
            }
        }
        fclose($handle);
    }
    return $emails;
}
