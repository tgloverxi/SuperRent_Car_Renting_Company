<html>
<style>
    body {
        background-image: url("img.jpg");
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-position: center;
        background-size: cover;
    }
    .header {
        background-color: whitesmoke;
        padding: 20px;
        text-align: center;
        opacity: 0.75;
    }
    .button {
        border-radius: 8px;
        background-color: hsla(30,100%,50%,0.5);
        border: none;
        color: #FFFFFF;
        text-align: center;
        font-size: 28px;
        padding: 20px;
        display: block;
        width: 200px;
        transition: all 0.5s;
        cursor: pointer;
        margin: 120px auto;
    }
    .button span {
        cursor: pointer;
        display: inline-block;
        position: relative;
        transition: 0.5s;
    }
    .button span:after {
        content: '\00bb';
        position: absolute;
        opacity: 0;
        top: 0;
        right: -20px;
        transition: 0.5s;
    }
    .button:hover span {
        padding-right: 25px;
    }

    .button:hover span:after {
        opacity: 1;
        right: 0;
    }
</style>

<body>
<center><IMG SRC="WGM logo.png" ALT="" WIDTH=200 HEIGHT=200></center>>
<div class="header">
    <h1>WGM Super Rent</h1>
    <p>With us</p>
    <p>Good quality</p>
    <p>Make an efficient order</p>
</div>

<form action="c_signin.php" method="get">
    <button class="button" style="vertical-align:middle"><span>Customer Sign in</span></button>
</form>

<form action="cle_signin.php" method="get">
    <button class="button" style="vertical-align:middle"><span>Clerk Sign in</span></button>
</form>

<form action="signup.php" method="get">
    <button class="button" style="vertical-align:middle"><span>Sign up</span></button>
</form>

<?php
//function connectToDB() {
//    global $db_conn;
//
//    // Your username is ora_(CWL_ID) and the password is a(student number). For example,
//    // ora_platypus is the username and a12345678 is the password.
//    $db_conn = OCILogon("ora_yuxinwan", "a23838436", "dbhost.students.cs.ubc.ca:1522/stu");
//
//    if ($db_conn) {
//        debugAlertMessage("Database is Connected");
//        return true;
//    } else {
//        debugAlertMessage("Cannot connect to Database");
//        $e = OCI_Error(); // For OCILogon errors pass no handle
//        echo htmlentities($e['message']);
//        return false;
//    }
//}
//
//function disconnectFromDB() {
//    global $db_conn;
//
//    debugAlertMessage("Disconnect from Database");
//    OCILogoff($db_conn);
//}
?>
</body>
</html>