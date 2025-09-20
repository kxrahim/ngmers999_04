<?php
    // ----- email processing -----
    $to = $uDetail[2];
    //$to = "mohdazrin@imu.edu.my";

    $from = 'no-reply@imu.edu.my';
    $fromName = 'IMU DentApp Admin';

    $subject = 'Response Required: EPA Activity Observation Request';

    $htmlContent = "
                    <html>
                    <head>
                        <title>IMU DentApp</title>
                    </head>
                    <body>
                        <p>Hi,</p>
                        <p>You are receiving this email as a reminder to the observation task scheduled for today.</p>
                        <p>Thank you. </p>
                    </body>
                    </html>";
    // Set content-type header for sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

    // Additional headers
    $headers .= 'From: '.$fromName.'<'.$from.'>' . "\r\n";
    $headers .= 'Cc: sivakumar@imu.edu.my' . "\r\n";
    //$headers .= 'Bcc: welcome2@example.com' . "\r\n";

    // Send email
    if(mail($to, $subject, $htmlContent, $headers)){
        echo 'Email has sent successfully.';
        echo "<script type='text/javascript'>
                  alert('Feedback request created successfully.');
                  window.location='request-all.php';
              </script>";
        exit();
    }else{
      echo 'Email sending failed.';
    }
?>