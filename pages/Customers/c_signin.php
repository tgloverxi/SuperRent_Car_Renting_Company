<?php
session_save_path('/home/y/yuxinwan/public_html');
session_start();
?>
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
        input[type=text], select {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type=submit] {
            width: 100%;
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
        div {
            border-radius: 5px;
            background-color: #f2f2f2;
            padding: 20px;
            width: 60%;
            margin: 150px auto;
        }
    </style>
    <body>
    <div>
        <form method="POST">
            <input type="hidden" id="signIn" name="signIn">
            Driver License: <input type="text" name="Driver_License" placeholder="Your driver license number..">

            Password:<input type="text" name="password" placeholder="Your password..">

            <input type="submit" value="SUBMIT" name="insertSubmit"></p>
        </form>
        <form action="startPage.php" method="get">
            <input type="submit" value="BACK"></p>
        </form>
    </div>
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
    // <label for="driver license">Driver License</label>
    // <label for="password">Password</label>

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

//    function printResult($result) { //prints results from a select statement
//        echo "<br>Retrieved data from table demoTable:<br>";
//        echo "<table>";
//        echo "<tr><th>ID</th><th>Name</th></tr>";
//
//        while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
//            echo "<tr><td>" . $row["NID"] . "</td><td>" . $row["NAME"] . "</td></tr>"; //or just use "echo $row[0]"
//        }
//
//        echo "</table>";
//    }

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

    function handleSignInRequest() {
        global $db_conn;
        $id = $_POST['Driver_License'];
        $pin = $_POST['password'];
        $_SESSION["C_dlicense"] = $id;

        $result = executePlainSQL("SELECT * FROM customers WHERE dlicense = '$id' AND password = $pin");
        $row = oci_fetch_row($result);

        if ($row != false) {
            header("location:https://www.students.cs.ubc.ca/~yuxinwan/customer.php");
            // echo "<br> The number of tuples in demoTable: " . $row[0] . "<br>";
        } else {  
            echo "<p style= 'color: whitesmoke; text-align: center'> Invalid user driver license or password. Please try again! </p>";
        }
    
        
    }

    // HANDLE ALL GET ROUTES
    // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
    function handleGETRequest() {
        if (connectToDB()) {
            if (array_key_exists('signIn', $_POST)) {
                handleSignInRequest();
            }

            disconnectFromDB();
        }
    }

    if (isset($_POST['insertSubmit'])) {
        handleGETRequest();
    }
    ?>
	</body>
</html>

