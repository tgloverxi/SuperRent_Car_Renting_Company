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
    </style>
    <center><IMG SRC="WGM logo.png" ALT="" WIDTH=200 HEIGHT=200></center>
    <body>
        <div class="header">
            <h1>Daily Rental Report for Specific Branch</h1>

            <form action = "dailyrentforbranch.php" method="POST">
                <fieldset class="box">
                    <legend class="headerText">City:</legend>
                    <input name="city" type="radio" value="The Great Vancouver"/> The Great Vancouver <br />
                    <input name="city" type="radio" value="Toronto" /> Toronto <br />
                </fieldset>

                <fieldset class="box">
                    <legend class="headerText">Location:</legend>
                    <label for="From">The Great Vancouver:</label><br />
                    <div class="fromBox">
                        <select name = "Van_loc">
                            <option value="downtown"> downtown </option>
                            <option value="UBC"> UBC </option>
                            <option value="SFU"> SFU </option>
                            <option value="granville"> granville </option>
                        </select>
                    </div>
                    <label for="From">Toronto:</label><br/>
                    <div class="fromBox">
                        <select name = "T_loc">
                            <option value="UT"> UT </option>
                            <option value="downTown"> downtown </option>
                        </select>
                    </div>
                </fieldset>

                <form action = "dailyreportforbranch.php" method="GET">
                    <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
                    <button class="button" name="insertSubmit" id="insertSubmit" style="vertical-align: middle"><span>Generate Report</span></button>
                </form>

            </form>
            <form action="clerk.php" method="GET">
                <button class="button" style="vertical-align: middle"><span>Back</span></button>
            </form>
        </div>


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

        function printResult($result) { //prints results from a select statement
            echo "<center><table></center>";
            echo "<h3>All the Cars that rented out today</h3>";
            $list = array();
            while ($r = OCI_Fetch_Array($result, OCI_BOTH)) {
                array_push($list, $r);
            }
            if (sizeof($list) != 0) {
                echo "<tr><th>Vehicle License</th><th>Make</th><th>Model</th><th>Year</th><th>Color</th><th>Odometer</th><th>Vehicle Type</th><th>Location</th><th>City</th></tr>";
                foreach ($list AS $row) {
                    echo "<tr><td>" . $row["0"] . "</td><td>" . $row["1"] . "</td><td>" . $row["2"] . "</td><td>" . $row["3"] . "</td><td>" . $row["4"] . "</td><td>" . $row["5"] . "</td><td>" . $row["6"] . "</td><td>" . $row["7"] . "</td><td>" . $row["8"] . "</td></tr>"; //or just use "echo $row[0]"
                }
            } else {
                echo "There is no car rented today!!!";
            }
            echo "</table>";
        }

        function printResultPerCat($result) { //prints results from a select statement
            echo "<center><table></center>";
            echo "<h3>The number of vehicles rented out per category</h3>";
            $list = array();
            while ($r = OCI_Fetch_Array($result, OCI_BOTH)) {
                array_push($list, $r);
            }
            if (sizeof($list) != 0) {
                echo "<tr><th>Number Of Vehicles per Category</th><th>City</th><th>Location</th><th>Car Type</th></tr>";
                foreach ($list AS $row) {
                    echo "<tr><td>" . $row["0"] . "</td><td>" . $row["1"] . "</td><td>" . $row["2"] . "</td><td>" . $row["3"] . "</td></tr>"; //or just use "echo $row[0]"
                }
            } else {
                echo "There is no car rented today!!!";
            }
            echo "</table>";
        }

        function printResultPerBranch($result) { //prints results from a select statement
            echo "<center><table></center>";
            echo "<h3>The number of vehicles rented out per branch</h3>";
            $list = array();
            while ($r = OCI_Fetch_Array($result, OCI_BOTH)) {
                array_push($list, $r);
            }
            if (sizeof($list) != 0) {
                echo "<tr><th>Number Of Vehicles per Branch</th><th>City</th><th>Location</th></tr>";
                foreach ($list AS $row) {
                    echo "<tr><td>" . $row["0"] . "</td><td>" . $row["1"] . "</td><td>" . $row["2"] . "</td></tr>"; //or just use "echo $row[0]"
                }
            } else {
                echo "There is no car rented today!!!";
            }
            echo "</table>";
        }

//        function printTotalResult($result) { //prints results from a select statement
//            echo "<center><table></center>";
//            echo "<h3>The total number of car rented out today</h3>";
//            $r = OCI_Fetch_Array($result, OCI_BOTH);
//            $count = 0;
//            if ($r != false) {
//                echo "<tr><th>Number Of Vehicles</th></tr>";
//                while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
//                    $count++;
//                }
//                echo "<tr><th>$count</th></tr>";
//            } else {
//                echo "There is no car rented today!!!";
//            }
//            echo "</table>";
//        }

        function displayDailyReport() {
            $city="";
            $location="";
            if ($_POST["city"] == "The Great Vancouver") {
                $city = "GT Van";
                $location = $_POST['Van_loc'];
            } else if ($_POST["city"] == "Toronto") {
                $city = "Toronto";
                $location = $_POST['T_loc'];
            }
            $t = time();
            $startDateTime = date("Y-m-d", $t) . " 00:00:00";
            $endDateTime = date("Y-m-d", $t). " 23:59:59";
            // echo $startDateTime. $endDateTime;
            $result = executePlainSQL(
                    "SELECT rentals.vlicense, make, model, year, color, rentals.odometer, vtName, '$location', '$city' FROM rentals JOIN vehicles ON rentals.vlicense = vehicles.vlicense WHERE fromtime >= TO_DATE('$startDateTime','yyyy-mm-dd hh24:mi:ss') AND fromtime <= TO_DATE('$endDateTime', 'yyyy-mm-dd hh24:mi:ss') AND vehicles.city = '$city' AND vehicles.location = '$location' ORDER BY vehicles.vtName");
//            SELECT rentals.vlicense, make, model, year, color, rentals.odometer, vtName, location, city From rentals JOIN vehicles ON rentals.vlicense = vehicles.vlicense WHERE fromtime >= TO_DATE('2019-11-23 00:00:00','yyyy-mm-dd hh24:mi:ss') AND fromtime <= TO_DATE('2019-11-23 23:59:59', 'yyyy-mm-dd hh24:mi:ss') AND city = 'GT Van' AND location = 'downtown' ORDER BY vtName;
            // insert into rentals values ('a11112', 'JN33W68', '000889A', (timestamp '2019-11-23 10:00:00'), (timestamp '2019-12-23 10:00:00'), 90, 'VISA', '22668802', '2028-06-01', 'T880821');
            printResult($result);
//            $numOfVehicles = executePlainSQL(
//                "SELECT * FROM rentals JOIN vehicles ON rentals.vlicense = vehicles.vlicense WHERE fromtime >= TO_DATE('$startDateTime','yyyy-mm-dd hh24:mi:ss') AND fromtime <= TO_DATE('$endDateTime', 'yyyy-mm-dd hh24:mi:ss')");
//            printTotalResult($numOfVehicles);
            $numOfVehiclesPerCat = executePlainSQL(
                "SELECT COUNT(*), city, location, vehicles.vtName FROM rentals JOIN vehicles ON rentals.vlicense = vehicles.vlicense WHERE fromtime >= TO_DATE('$startDateTime','yyyy-mm-dd hh24:mi:ss') AND fromtime <= TO_DATE('$endDateTime', 'yyyy-mm-dd hh24:mi:ss') AND vehicles.city = '$city' AND vehicles.location = '$location' GROUP BY city, location, vehicles.vtName");
            // SELECT COUNT(*), vehicles.city, vehicles.location FROM rentals JOIN vehicles ON rentals.vlicense = vehicles.vlicense WHERE fromtime >= TO_DATE('2019-11-23 00:00:00','yyyy-mm-dd hh24:mi:ss') AND fromtime <= TO_DATE('2019-11-23 23:59:59', 'yyyy-mm-dd hh24:mi:ss') GROUP BY vehicles.city, vehicles.location ORDER BY vehicles.city, vehicles.location;
            printResultPerCat($numOfVehiclesPerCat);
            $numOfVehiclesPerBranch = executePlainSQL(
                "SELECT COUNT(*), vehicles.city, vehicles.location FROM rentals JOIN vehicles ON rentals.vlicense = vehicles.vlicense WHERE fromtime >= TO_DATE('$startDateTime','yyyy-mm-dd hh24:mi:ss') AND fromtime <= TO_DATE('$endDateTime', 'yyyy-mm-dd hh24:mi:ss') AND vehicles.city = '$city' AND vehicles.location = '$location' GROUP BY city, location ORDER BY city, location");
            // SELECT COUNT(*), vehicles.city, vehicles.location FROM rentals JOIN vehicles ON rentals.vlicense = vehicles.vlicense WHERE fromtime >= TO_DATE('2019-11-23 00:00:00','yyyy-mm-dd hh24:mi:ss') AND fromtime <= TO_DATE('2019-11-23 23:59:59', 'yyyy-mm-dd hh24:mi:ss') AND city = 'GT Van' AND location = 'downtown' GROUP BY city, location ORDER BY city, location;
            printResultPerBranch($numOfVehiclesPerBranch);
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
                if (array_key_exists('insertQueryRequest', $_POST)) {
                    displayDailyReport ();
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

