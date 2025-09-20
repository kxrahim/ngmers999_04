<?php
    session_start();

    // Unset all session variables
    $_SESSION = array();

    // Delete session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Destroy the session
    session_destroy();

    // Delete authentication cookies
    setcookie('auth_token', '', time() - 3600, '/');
    setcookie('user_id', '', time() - 3600, '/');

    // Redirect to login page
    header("Location: https://academy.999.gov.my");
    exit();
?>