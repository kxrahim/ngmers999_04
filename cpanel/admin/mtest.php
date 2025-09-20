<?php
/*
    $to_email = 'mohdazrin@imu.edu.my';
    $subject = 'IMU DentApp Email Test';
    $message = 'This mail is sent using the PHP mail function for IMU DentApp Application';
    $headers = 'From: no-reply@imu.edu.my';
    mail($to_email,$subject,$message,$headers);
    */

    $subject = "Response Required: EPA Activity Observation Request";
    $message = "
            <html>
            <head>
                <title>EPA Management System</title>

                <style>
                body{
                    font-family: Arial, Helvetica, sans-serif;
                }
                </style>
            </head>
            <body>
                <p>
                Dear <b>Mohd Azrin</b>,
                </p>
                <p>
                You've been invited to observe an EPA activity. Please take a moment to confirm your availability by logging into your account.

                <br>Please click <a href='https://imudentapp.imu.edu.my/dentapp'>here</a> to login.

                <br>Thank you.
                </p>
            </body>
            </html>";
    //$emailto = $uDetail'[2];
    $emailto = 'mohdazrin@imu.edu.my';

    $headers = "MIME-Version: 1.0" . "\r\n";
    //$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

    // More headers
    //$headers .= 'From: <elearning@imu.edu.my>' . "\r\n";
    //$headers .= 'Cc: myboss@example.com' . "\r\n";

    $headers .= "From: <noreply@imu.edu.my>" . "\r\n"."X-Mailer: php";
    //$headers .= "Cc: sivakumar@imu.edu.my" . "\r\n";

    mail($emailto, $subject, $message, $headers);
?>
