<html>

<style>
    body {
        background-color: white;
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-position: center;
        background-size: cover;
    }
    .header {
        background-color: whitesmoke;
        padding: 10px;
        text-align: center;
    }
    .fromBox {
        padding: 10px;
        margin: 50px auto;
    }
    .button {
        border-radius: 8px;
        background-color: hsla(30,100%,50%,0.5);
        border: none;
        color: #FFFFFF;
        text-align: center;
        font-size: 20px;
        padding: 20px;
        display: block;
        width: 250px;
        transition: all 0.5s;
        cursor: pointer;
        margin: 50px auto;
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
    .box {
        background-color: whitesmoke;
        width: 600px;
        padding: 10px;
        margin: 40px auto;
    }
    .headerText {
        background-color: hsla(30,100%,50%,0.5);
        color: whitesmoke;
        letter-spacing: 3px;
    }
    input[type=submit] {
        width: 600px;
        position: center;
        background-color: hsla(30,100%,50%,0.5);
        color: white;
        padding: 14px 20px;
        margin: 8px 0;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
</style>
<center><IMG SRC="WGM logo.png" ALT="" WIDTH=200 HEIGHT=200></center>
<body>
<div class="header">
    <h1>View Data of The Company</h1>

    <form action = "ViewDataOfTheCompany.php" method="GET">
        <fieldset class="box">
            <legend class="headerText">Summary:</legend>
            <input type="hidden" id="viewalltable" name="viewalltable">
            <input type="submit" name="viewall" value = "viewall" style="horiz-align: left">
        </fieldset>
    </form>

    <form action = "ViewDataOfTheCompany.php" method="POST">
        <fieldset class="box">
            <legend class="headerText">View Single Table:</legend>
            <label for="From">Please choose one table to view:</label><br />
            <div class="fromBox">
                <select name = "single">
                    <option value="customers"> customers </option>
                    <option value="vehicles"> vehicles </option>
                    <option value="vehicleTypes"> vehicleTypes </option>
                    <option value="reservations"> reservations </option>
                    <option value="rentals"> rentals </option>
                    <option value="returns"> returns </option>
                    <option value="clerks"> clerks </option>
                </select>
            </div>
            <input type="hidden" id="viewtable" name="viewtable">
            <input type="submit" name="view" value = "View" style="horiz-align: left">
        </fieldset>
    </form>
</div>

<form action="clerk.php" method="GET">
    <button class="button" style="vertical-align: middle"><span>Back</span></button>
</form>


<?php
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
    // echo "<br>begin to read the sql!!!!!!!!!!!!!!!!!!!!!!<br>";

    $statement = OCIParse($db_conn, $cmdstr);
    //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

    if (!$statement) {
        echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
        echo htmlentities($e['message']);
        $success = False;
    }

//            echo "<br>begin to execute the sql!!!!!!!!!!!!!!!!!!!!!!<br>";
//            echo "<br> here's the sql: " . $cmdstr ."<br>";
    $r = OCIExecute($statement, OCI_DEFAULT);
    if (!$r) {
        //  echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
        $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
        echo htmlentities($e['message']);
        $success = False;
    }
//            echo "<br>after executing the sql!!!!!!!!!!!!!!!!!!!!!!<br>";
//            echo "<br> here's the result: " . var_dump(oci_fetch_row($statement)) ."<br>";
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

function printVehicle($result) { //prints results from a select statement
    echo "<center><table></center>";
    echo "<h3>Vehicles</h3>";
    $list = array();
    while ($r = OCI_Fetch_Array($result, OCI_BOTH)) {
        array_push($list, $r);
    }
    if (sizeof($list) != 0) {
        echo "<tr><th>Vehicle License</th><th>Make</th><th>Model</th><th>Year</th><th>Color</th><th>Odometer</th><th>Vehicle Type</th><th>Location</th><th>City</th><th>Status</th></tr>";
        foreach ($list AS $row) {
            echo "<tr><td>" . $row["0"] . "</td><td>" . $row["1"] . "</td><td>" . $row["2"] . "</td><td>" . $row["3"] . "</td><td>" . $row["4"] . "</td><td>" . $row["5"] . "</td><td>" . $row["6"] . "</td><td>" . $row["7"] . "</td><td>" . $row["8"] . "</td><td>" . $row["9"] . "</td></tr>"; //or just use "echo $row[0]"
        }
    } else {
        echo "There is no car in the system!!!";
    }
    echo "</table>";
}

function printCustomers($result) { //prints results from a select statement
    echo "<center><table></center>";
    echo "<h3>Customers</h3>";
    $list = array();
    while ($r = OCI_Fetch_Array($result, OCI_BOTH)) {
        array_push($list, $r);
    }
    if (sizeof($list) != 0) {
        echo "<tr><th>Driver License</th><th>customer password</th><th>Phone Number</th><th>Address</th><th>Customer Name</th></tr>";
        foreach ($list AS $row) {
            echo "<tr><td>" . $row["0"] . "</td><td>" . $row["1"] . "</td><td>" . $row["2"] . "</td><td>" . $row["3"] . "</td><td>" . $row["4"] . "</td></tr>"; //or just use "echo $row[0]"
        }
    } else {
        echo "There is no customer in the system!!!";
    }
    echo "</table>";
}

function printVehicleTypes($result) { //prints results from a select statement
    echo "<center><table></center>";
    echo "<h3>Vehicle Types</h3>";
    $list = array();
    while ($r = OCI_Fetch_Array($result, OCI_BOTH)) {
        array_push($list, $r);
    }
    if (sizeof($list) != 0) {
        echo "<tr><th>Vehicle Types</th><th>features</th><th>Week Rate</th><th>Day Rate</th><th>Hour Rate</th><th>Week Insurance Rate</th><th>Day Insurance Rate</th><th>Hour Insurance Rate</th><th>Kilo Rate</th></tr>";
        foreach ($list AS $row) {
            echo "<tr><td>" . $row["0"] . "</td><td>" . $row["1"] . "</td><td>" . $row["2"] . "</td><td>" . $row["3"] . "</td><td>" . $row["4"] . "</td><td>" . $row["5"] . "</td><td>" . $row["6"] . "</td><td>" . $row["7"] . "</td><td>" . $row["8"] . "</td><td>" . $row["9"] . "</td></tr>"; //or just use "echo $row[0]"
        }
    } else {
        echo "There is no vehicle type in the system!!!";
    }
    echo "</table>";
}
function printReservations($result) { //prints results from a select statement
    echo "<center><table></center>";
    echo "<h3>Reservations</h3>";
    $list = array();
    while ($r = OCI_Fetch_Array($result, OCI_BOTH)) {
        array_push($list, $r);
    }
    if (sizeof($list) != 0) {
        echo "<tr><th>Confirmation Number</th><th>Car Types</th><th>Car Plate Number</th><th>Driver License</th><th>From Time</th><th>To time</th></tr>";
        foreach ($list AS $row) {
            echo "<tr><td>" . $row["0"] . "</td><td>" . $row["1"] . "</td><td>" . $row["2"] . "</td><td>" . $row["3"] . "</td><td>" . $row["4"] . "</td><td>" . $row["5"] . "</td></tr>"; //or just use "echo $row[0]"
        }
    } else {
        echo "There is no reservation in the system!!!";
    }
    echo "</table>";
}

function printRentals($result) { //prints results from a select statement
    echo "<center><table></center>";
    echo "<h3>Rentals</h3>";
    $list = array();
    while ($r = OCI_Fetch_Array($result, OCI_BOTH)) {
        array_push($list, $r);
    }
    if (sizeof($list) != 0) {
        echo "<tr><th>rental id </th><th>Car Plate Number</th><th>Driver license</th><th>From Time</th><th>To Time</th><th>Odometer</th><th>card type</th><th>card number</th><th>expire date</th><th>confirmation number</th></tr>";
        foreach ($list AS $row) {
            echo "<tr><td>" . $row["0"] . "</td><td>" . $row["1"] . "</td><td>" . $row["2"] . "</td><td>" . $row["3"] . "</td><td>" . $row["4"] . "</td><td>" . $row["5"] . "</td><td>" . $row["6"] . "</td><td>" . $row["7"] . "</td><td>" . $row["8"] . "</td><td>" . $row["9"] . "</td></tr>"; //or just use "echo $row[0]"
        }
    } else {
        echo "There is no rentals record in the system!!!";
    }
    echo "</table>";
}
function printReturns($result) { //prints results from a select statement
    echo "<center><table></center>";
    echo "<h3>Returns</h3>";
    $list = array();
    while ($r = OCI_Fetch_Array($result, OCI_BOTH)) {
        array_push($list, $r);
    }
    if (sizeof($list) != 0) {
        echo "<tr><th>return id </th><th>return time</th><th>odometer</th><th>Full tank</th><th>Total price</th></tr>";
        foreach ($list AS $row) {
            echo "<tr><td>" . $row["0"] . "</td><td>" . $row["1"] . "</td><td>" . $row["2"] . "</td><td>" . $row["3"] . "</td><td>" . $row["4"] . "</td></tr>"; //or just use "echo $row[0]"
        }
    } else {
        echo "There is no return record in the system!!!";
    }
    echo "</table>";
}
function printClerks($result) { //prints results from a select statement
    echo "<center><table></center>";
    echo "<h3>Clerks</h3>";
    $list = array();
    while ($r = OCI_Fetch_Array($result, OCI_BOTH)) {
        array_push($list, $r);
    }
    if (sizeof($list) != 0) {
        echo "<tr><th>clerk id</th><th>clerk password</th><th>clerk name</th></tr>";
        foreach ($list AS $row) {
            echo "<tr><td>" . $row["0"] . "</td><td>" . $row["1"] . "</td><td>" . $row["2"] . "</td></tr>"; //or just use "echo $row[0]"
        }
    } else {
        echo "There is no return record in the system!!!";
    }
    echo "</table>";
}

function displayAllTables() {
    echo "<center><h4>ALL TABLES</h4></center>";
    echo "<center><br> vehicleTypes <br></center>";
    echo "<center><br> vehicles <br></center>";
    echo "<center><br> customers <br></center>";
    echo "<center><br> reservations <br></center>";
    echo "<center><br> rentals <br></center>";
    echo "<center><br> returns <br></center>";
    echo "<center><br> clerks <br></center>";
        }

function displaySingleTable() {
    global $db_conn;
    if ($_POST["single"] == "customers") {
        $result = executePlainSQL("SELECT * FROM customers");
        printCustomers($result);
    } else if ($_POST["single"] == "vehicles") {
        $result = executePlainSQL("SELECT * FROM vehicles");
        printVehicle($result);
    } else if  ($_POST["single"] == "vehicleTypes"){
        $result = executePlainSQL("SELECT * FROM vehicleTypes");
        printVehicleTypes($result);
    } else if  ($_POST["single"] == "reservations") {
        $result = executePlainSQL("SELECT confNO, vtName, vlicense, dlicense, CAST(fromTime AS DATE), CAST(toTime AS DATE) FROM reservations");
        printReservations($result);
    }else if  ($_POST["single"] == "rentals") {
        $result = executePlainSQL("SELECT rid, vlicense, dlicense, CAST(fromTime AS DATE), CAST(toTime AS DATE), odometer, cardName, cardNo, expDate, confNo FROM rentals");
        printRentals($result);
    }else if  ($_POST["single"] == "returns") {
        $result = executePlainSQL("SELECT rid, CAST(retime AS DATE), odometer, fulltank, value FROM returns ");
        printReturns($result);
    }else if  ($_POST["single"] == "clerks") {
        $result = executePlainSQL("SELECT * FROM clerks");
        printClerks($result);
    }
    OCICommit($db_conn);

}

function disconnectFromDB() {
    global $db_conn;

    debugAlertMessage("Disconnect from Database");
    OCILogoff($db_conn);
}

// HANDLE ALL GET ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
function handleGETRequest() {
    if (connectToDB()) {
        if (array_key_exists('viewall', $_GET)) {
            displayAllTables();
        }

        disconnectFromDB();
    }
}

function handlePOSTRequest() {
    if (connectToDB()) {
        if (array_key_exists('view', $_POST)) {
            displaySingleTable() ;
        }

        disconnectFromDB();
    }
}
if (isset($_POST['viewtable'])) {
    handlePOSTRequest();
}else if (isset($_GET['viewalltable'])) {
    handleGETRequest();
}
?>
</body>
</html>

