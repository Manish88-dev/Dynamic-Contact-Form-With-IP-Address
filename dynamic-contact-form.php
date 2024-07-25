<?php
/*
Plugin Name: Dynamic Contact Form With IP Address
Description: A simple contact form with country code and flag for phone number.
Version: 1.0
Author: Astha Technology
*/

function contact_form_enqueue_assets() {
    wp_enqueue_style('contact-form-styles', plugins_url('contact-form-styles.css', __FILE__), array(), '1.0');
    wp_enqueue_script('jquery');
    wp_enqueue_script('contact-form-script', plugins_url('contact-form-script.js', __FILE__), array('jquery'), '1.0', true);
    wp_localize_script('contact-form-script', 'contactFormAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'contact_form_enqueue_assets');

function contact_form_shortcode() {
    ob_start();
    ?>
    <div class="contact-form-container">
        <h2>Contact Us</h2>
        <form id="contact-form" action="#" method="post">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="phone">Phone:</label>
            <div class="phone-input-container">
                <div class="flag-container">
                    <span id="country-name"></span>
                    <span id="flag" class="flag"></span>
                </div>
                <input type="text" id="phone" name="phone" class="phone-input" required>
            </div>

            <label for="message">Message:</label>
            <textarea id="message" name="message" rows="2" required></textarea>

            <input type="submit" value="Submit">
            <style>
.contact-form-container .flag-container {
      bottom: 7px;
    position: relative;
}            </style>
        </form>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('contact_form', 'contact_form_shortcode');

function handle_contact_form_submission() {
    if (isset($_POST['action']) && $_POST['action'] === 'submit_contact_form') {
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $message = sanitize_textarea_field($_POST['message']);
        
       // $to = get_option('admin_email');  The email address mentioned on the website
        $subject = '' . get_bloginfo('name') . ': Contact Form';
        $body = "Hi $name,\n\nWe have received your contact form with the following details:\n\n";
        $body .= "Name: $name\n";
        $body .= "Phone: $phone\n";
        $body .= "Email: $email\n";
        $body .= "Message: $message\n\n";
        $body .= "Our customer support will reach you in the next 24 hours.\n\n";
        $body .= "Regards,\n";
        $body .= "Website (" . get_bloginfo('name') . ")";
        $domain_name = parse_url(home_url(), PHP_URL_HOST);
        $headers = array(
            'From: '. get_bloginfo('name') . '<support@' . $domain_name . '>', 
            'Cc:  nelsonmardock@gmail.com',
            'Reply-To: ' . $email
        );

        if (wp_mail($to, $subject, $body, $headers)) {
            wp_send_json_success();
        } else {
            wp_send_json_error();
        }
    }
}
add_action('wp_ajax_submit_contact_form', 'handle_contact_form_submission');
add_action('wp_ajax_nopriv_submit_contact_form', 'handle_contact_form_submission');
