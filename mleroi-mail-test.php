<?php

namespace mleroi\mailtest;

/*
Plugin Name: Mleroi Mail Test
Description: Send test emails from WordPress
Version: 1.0.0
Author: mleroi
*/

if( ! class_exists( 'mleroi\\mailtest\\MailTest' ) ) {

    class MailTest {

        public static function run() {
            add_action( 'admin_menu', [__CLASS__, 'addMailTestMenu'] );
        }

        public static function addMailTestMenu() {
            add_options_page( 'Mail Test', 'Mail Test', 'manage_options', 'mlrmt_settings', [__CLASS__, 'settingsPage'] );
        }

        public static function settingsPage() {
            $feedback = array();

            if ( !empty( $_POST['submit'] ) ) {

                $email = isset( $_POST['email'] ) ? sanitize_email( trim( $_POST['email'] ) ) : '';

                if ( !empty( $email ) ) {
                    $email = sanitize_email( $_POST['email'] );
                    $feedback['ok'] = wp_mail( $email, 'Test email from ' . get_option( 'siteurl' ), 'This is a test email sent on '. date('Y-m-d H:i:s') );
                    $feedback['message'] = $feedback['ok'] ? 'Email sent successfully! Check your email client :)' : 'An error occured: email not sent';
                } else {
                    $feedback['ok'] = false;
                    $feedback['message'] = 'Please provide an email';
                }

            }

            ?>
                <div class="wrap">

                    <?php if ( !empty( $feedback ) ): ?>
                        <div class="<?php echo $feedback['ok'] ? 'updated' : 'error'; ?>">
                            <?php echo $feedback['message']; ?>
                        </div>
                    <?php endif; ?>

                    <h2>Send test email</h2>
                    <form method="post">
                        <input type="email" name="email" value="">
                        <input type="submit" name="submit" value="Send test email">
                    </form>
                </div>
            <?php
        }
    }

    MailTest::run();

}
