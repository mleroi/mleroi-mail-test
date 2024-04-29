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

                    add_filter( 'wp_mail_from', [__CLASS__, 'fromEmail'] );
                    add_filter( 'wp_mail_from_name', [__CLASS__, 'fromName'] );

                    $feedback['ok'] = wp_mail( $email, 'Test email from ' . get_option( 'siteurl' ), 'This is a test email sent on '. date('Y-m-d H:i:s') );

                    remove_filter( 'wp_mail_from', [__CLASS__, 'fromEmail'] );
                    remove_filter( 'wp_mail_from_name', [__CLASS__, 'fromName'] );

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
                        <label>From email</label>: <input type="text" name="from_email" value="<?php echo get_option('admin_email'); ?>"><br>
                        <label>From name</label>: <input type="text" name="from_name" value="<?php echo get_option('blogname'); ?>"><br>
                        <label>Recipient email</label>: <input type="email" name="email" value=""><br>
                        <input type="submit" name="submit" value="Send test email">
                    </form>
                </div>
            <?php
        }

        public static function fromEmail( $from_email ) {
            if ( isset( $_POST['from_email'] ) ) {
                $email = sanitize_email( trim( $_POST['from_email'] ) );
                if ( !empty( $email ) ) {
                    $from_email = $email;
                }
            }
            return $from_email;
        }

        public static function fromName( $from_name ) {
            if ( isset( $_POST['from_name'] ) ) {
                $name = trim( $_POST['from_name'] );
                if ( !empty( $name ) ) {
                    $from_name = $name;
                }
            }
            return $from_name;
        }
    }

    MailTest::run();

}
