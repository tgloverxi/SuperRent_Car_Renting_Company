<html>
<head>
    <title>CPSC 304 PHP/Oracle Demonstration</title>
</head>
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
    input[type=text], select {
            width: 30%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
    }
    input[type=submit] {
        width: 100%;
        position: center;
        background-color: hsla(30,100%,50%,0.5);
        color: white;
        padding: 14px 20px;
        margin: 8px 0;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    input[type=submit]:hover {
        background-color: hsla(30, 100%, 50%, 0.65);
    }
    .headerText {
        background-color: hsla(30,100%,50%,0.5);
        color: whitesmoke;
        letter-spacing: 3px;
    }
    .box {
        background-color: whitesmoke;
        width: 600px;
        padding: 10px;
        margin: 40px auto;
    }
    .buttonlocation {
        position: center;
    }
    .fromBox {
        padding: 10px;
        margin: 50px auto;
    }
</style>

<body>

<div class="header">
    <h1>Sign Up</h1>
</div>

<div>
    <form method="POST">
    <fieldset class="box">
        <legend class="headerText">Your Personal Information: </legend>
        <p><label for="drive license">Your Driver License: </label>
            <input type="text" id="driver license" name="driverlicense" placeholder="your driver license"></p>

        <p><label for="name">Your Name: </label>
            <input type="text" id="name" name="name" placeholder="your first name and last name"></p>

        <p><label for="password">Your password: </label>
        <input type="text" id="password" name="password" placeholder="your password"></p>

        <p><label for="phoneNo.">Your Phone Number: </label>
        <input type="text" id="phoneNo" name="phoneNo" placeholder="your phone number"></p>

        <p><label for="address">Your address: </label>
        <input type="text" id="address" name="address" placeholder="your address"></p>
        
        <input type="hidden" id="signUp" name="signUp">
        <input type = "submit" id="sign_Up" name = "sign_up"></p>
    </fieldset>
    
</form>
</div>
<form action="startPage.php" method="GET">
    <button class="button" name = "back" style="vertical-align: middle"><span>BACK</span></button>
</form>


<?php
//this tells the system that it's no longer just parsing html; it's now parsing PHP

$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = NULL; // edit the login credentials in connectToDB()
$show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

function debugAlertMessage($message) {
    global $show_debug_alert_messages;

    if ($show_debug_alert_messages) {
        echo "<script type='text/javascript'>alert('" . $message . "');</script>";
    }
}

function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
    //echo "<br>running ".$cmdstr."<br>";
    global $db_conn, $success;

    $statement = OCIParse($db_conn, $cmdstr);
    //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

    if (!$statement) {
        echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
        echo htmlentities($e['message']);
        $success = False;
    }

    $r = OCIExecute($statement, OCI_DEFAULT);
    if (!$r) {
        echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
        $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
        echo htmlentities($e['message']);
        $success = False;
    }

    return $statement;
}

function executeBoundSQL($cmdstr, $list) {
    /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
In this case you don't need to create the statement several times. Bound variables cause a statement to only be
parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
See the sample code below for how this function is used */

    global $db_conn, $success;
    $statement = OCIParse($db_conn, $cmdstr);

    if (!$statement) {
        echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn);
        echo htmlentities($e['message']);
        $success = False;
    }

    foreach ($list as $tuple) {
        foreach ($tuple as $bind => $val) {
            //echo $val;
            //echo "<br>".$bind."<br>";
            OCIBindByName($statement, $bind, $val);
            unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
        }

        $r = OCIExecute($statement, OCI_DEFAULT);
        if (!$r) {
            echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
            echo htmlentities($e['message']);
            echo "<br>";
            $success = False;
        }
    }
}

function printResult($result) { //prints results from a select statement
    echo "<br>Retrieved data from table demoTable:<br>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row["NID"] . "</td><td>" . $row["NAME"] . "</td></tr>"; //or just use "echo $row[0]"
    }

    echo "</table>";
}

function connectToDB() {
    global $db_conn;

    // Your username is ora_(CWL_ID) and the password is a(student number). For example,
    // ora_platypus is the username and a12345678 is the password.
    $db_conn = OCILogon("ora_yuxinwan", "a23838436", "dbhost.students.cs.ubc.ca:1522/stu");

    if ($db_conn) {
        debugAlertMessage("Database is Connected");
        return true;
    } else {
        debugAlertMessage("Cannot connect to Database");
        $e = OCI_Error(); // For OCILogon errors pass no handle
        echo htmlentities($e['message']);
        return false;
    }
}

function disconnectFromDB() {
    global $db_conn;

    debugAlertMessage("Disconnect from Database");
    OCILogoff($db_conn);
}

function signUpRequest() {
    global $db_conn;
    // $id = $_POST['driver_license'];
    $tuple = array (
        ":dlicense" => $_POST['driverlicense'],
        ":password" => $_POST['password'],
        ":cellphone" => $_POST['phoneNo'],
        ":address" => $_POST['address'],
        ":name" => $_POST['name']
    );


    $alltuples = array ($tuple);
    executeBoundSQL("INSERT INTO customers VALUES (:dlicense, :password, :cellphone, :address, :name)", $alltuples);
    echo "<p style= 'color: whitesmoke; text-align: center'> Sign Up completed! Please go back to main page to sign in!</p>";
    OCICommit($db_conn);
        
}

// HANDLE ALL POST ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
function handleSignUpRequest() {
    if (connectToDB()) {
        if (array_key_exists('signUp', $_POST)) {
            signUpRequest();
        }
        disconnectFromDB();
    }
}


if (isset($_POST['sign_up'])) {
    handleSignUpRequest();
}
?>
</body>
</html>

