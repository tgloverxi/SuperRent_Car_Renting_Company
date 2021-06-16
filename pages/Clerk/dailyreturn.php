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
    </style>
    <center><IMG SRC="WGM logo.png" ALT="" WIDTH=200 HEIGHT=200></center>
    <body>
        <div class="header">
            <h1>Daily Return Report</h1>
            <form action = "dailyreturn.php" method="GET">
                <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
                <button class="button" name="insertSubmit" id="insertSubmit" style="vertical-align: middle"><span>Generate Report</span></button>
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

        function printResult($result) { //prints results from a select statement
            echo "<center><table></center>";
            echo "<h3>All the cars that return back today</h3>";
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
                echo "There is no car returned today!!!";
            }
            echo "</table>";
        }

        function printResultPerCat($result) { //prints results from a select statement
            echo "<center><table></center>";
            echo "<h3>The number of vehicles return back per category</h3>";
            $list = array();
            while ($r = OCI_Fetch_Array($result, OCI_BOTH)) {
                array_push($list, $r);
            }
            if (sizeof($list) != 0) {
                echo "<tr><th>Number Of Vehicles per Category</th><th>Vehicle Type</th></tr>";
                foreach ($list AS $row) {
                    echo "<tr><td>" . $row["0"] . "</td><td>" . $row["1"] . "</td></tr>"; //or just use "echo $row[0]"
                }
            } else {
                echo "There is no car returned today!!!";
            }
            echo "</table>";
        }

        function printResultPerBranch($result) { //prints results from a select statement
            echo "<center><table></center>";
            echo "<h3>The number of vehicles returned back per branch</h3>";
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
                echo "There is no car returned today!!!";
            }
            echo "</table>";
        }

        function printTotalResult($result) { //prints results from a select statement
            echo "<center><table></center>";
            echo "<h3>The total number of car returned back today</h3>";
            $list = array();
            while ($r = OCI_Fetch_Array($result, OCI_BOTH)) {
                array_push($list, $r);
            }
            $count = 0;
            if (sizeof($list) != 0) {
                echo "<tr><th>Number Of Vehicles</th></tr>";
                foreach ($list AS $row) {
                    $count++;
                }
                echo "<tr><th>$count</th></tr>";
            } else {
                echo "There is no car returned today!!!";
            }
            echo "</table>";
        }

        function printTotalGrand($result) { //prints results from a select statement
            echo "<center><table></center>";
            echo "<h3>The total revenue for today</h3>";
            $list = array();
            while ($r = OCI_Fetch_Array($result, OCI_BOTH)) {
                array_push($list, $r);
            }
            if (sizeof($list) != 0) {
                echo "<tr><th>Revenue</th></tr>";
                foreach ($list AS $row) {
                    echo "<tr><th>".$row["0"]."</th></tr>";
                }
            } else {
                echo "There is no car returned today!!!";
            }
            echo "</table>";
        }

        function printRevenuePerCat($result) { //prints results from a select statement
            echo "<center><table></center>";
            echo "<h3>The total revenue per category</h3>";
            $list = array();
            while ($r = OCI_Fetch_Array($result, OCI_BOTH)) {
                array_push($list, $r);
            }
            if (sizeof($list) != 0) {
                echo "<tr><th>Revenue</th><th>Vehicle Type</th></tr>";
                foreach ($list AS $row) {
                    echo "<tr><th>".$row["0"]."</th><th>".$row["1"]."</th></tr>";
                }
            } else {
                echo "There is no car returned today!!!";
            }
            echo "</table>";
        }

        function printRevenuePerBranch($result) { //prints results from a select statement
            echo "<center><table></center>";
            echo "<h3>The total revenue per branch</h3>";
            $list = array();
            while ($r = OCI_Fetch_Array($result, OCI_BOTH)) {
                array_push($list, $r);
            }
            if (sizeof($list) != 0) {
                echo "<tr><th>Revenue</th><th>City</th><th>Location</th></tr>";
                foreach ($list AS $row) {
                    echo "<tr><th>".$row["0"]."</th><th>".$row["1"]."</th><th>".$row["2"]."</th></tr>";
                }
            } else {
                echo "There is no car returned today!!!";
            }
            echo "</table>";
        }

        function displayDailyReport() {
           // global $db_conn;
            $t = time();
            $startDateTime = date("Y-m-d", $t) . " 00:00:00";
            $endDateTime = date("Y-m-d", $t). " 23:59:59";
            // all information of all returned vehicles
            $result = executePlainSQL(
                    "SELECT rentals.vlicense, make, model, year, color, rentals.odometer, vtName, location, city FROM rentals JOIN vehicles ON rentals.vlicense = vehicles.vlicense JOIN returns ON returns.rid = rentals.rid WHERE retime >= TO_DATE('$startDateTime','yyyy-mm-dd hh24:mi:ss') AND retime <= TO_DATE('$endDateTime', 'yyyy-mm-dd hh24:mi:ss') ORDER BY vehicles.city, vehicles.location, vehicles.vtName");
            printResult($result);
            // number of vehicles return today
            $numOfVehicles = executePlainSQL(
                "SELECT * FROM rentals JOIN vehicles ON rentals.vlicense = vehicles.vlicense JOIN returns ON returns.rid = rentals.rid WHERE retime >= TO_DATE('$startDateTime','yyyy-mm-dd hh24:mi:ss') AND retime <= TO_DATE('$endDateTime', 'yyyy-mm-dd hh24:mi:ss')");
            printTotalResult($numOfVehicles);
            // number of vehicles per category return today
            $numOfVehiclesPerCat = executePlainSQL(
                "SELECT COUNT(*), vehicles.vtName FROM rentals JOIN vehicles ON rentals.vlicense = vehicles.vlicense JOIN returns ON returns.rid = rentals.rid WHERE retime >= TO_DATE('$startDateTime','yyyy-mm-dd hh24:mi:ss') AND retime <= TO_DATE('$endDateTime', 'yyyy-mm-dd hh24:mi:ss') GROUP BY vehicles.vtName ORDER BY vehicles.vtName");
            printResultPerCat($numOfVehiclesPerCat);
            // number of vehicle per branch return today
            $numOfVehiclesPerBranch = executePlainSQL(
                "SELECT COUNT(*), vehicles.city, vehicles.location FROM rentals JOIN vehicles ON rentals.vlicense = vehicles.vlicense JOIN returns ON returns.rid = rentals.rid WHERE retime >= TO_DATE('$startDateTime','yyyy-mm-dd hh24:mi:ss') AND retime <= TO_DATE('$endDateTime', 'yyyy-mm-dd hh24:mi:ss') GROUP BY vehicles.city, vehicles.location ORDER BY vehicles.city, vehicles.location");
            printResultPerBranch($numOfVehiclesPerBranch);
            // grand totals for today
            $grandToday = executePlainSQL("SELECT SUM(value) FROM rentals JOIN vehicles ON rentals.vlicense = vehicles.vlicense JOIN returns ON returns.rid = rentals.rid WHERE retime >= TO_DATE('$startDateTime','yyyy-mm-dd hh24:mi:ss') AND retime <= TO_DATE('$endDateTime', 'yyyy-mm-dd hh24:mi:ss')");
            printTotalGrand($grandToday);
            // revenue per category
            $grandPerCat = executePlainSQL("SELECT SUM(value), vehicles.vtName FROM rentals JOIN vehicles ON rentals.vlicense = vehicles.vlicense JOIN returns ON returns.rid = rentals.rid WHERE retime >= TO_DATE('$startDateTime','yyyy-mm-dd hh24:mi:ss') AND retime <= TO_DATE('$endDateTime', 'yyyy-mm-dd hh24:mi:ss') GROUP BY vehicles.vtName ORDER BY vehicles.vtName");
            printRevenuePerCat($grandPerCat);
            // revenue per branch
            $grandPerBranch = executePlainSQL("SELECT SUM(value), vehicles.city, vehicles.location FROM rentals JOIN vehicles ON rentals.vlicense = vehicles.vlicense JOIN returns ON returns.rid = rentals.rid WHERE retime >= TO_DATE('$startDateTime','yyyy-mm-dd hh24:mi:ss') AND retime <= TO_DATE('$endDateTime', 'yyyy-mm-dd hh24:mi:ss') GROUP BY vehicles.city, vehicles.location ORDER BY vehicles.city, vehicles.location");
            printRevenuePerBranch($grandPerBranch);
        }

        function disconnectFromDB() {
            global $db_conn;

            debugAlertMessage("Disconnect from Database");
            OCILogoff($db_conn);
        }

        // HANDLE ALL POST ROUTES
        // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
//        function handlePOSTRequest() {
//            if (connectToDB()) {
//                if (array_key_exists('insertQueryRequest', $_POST)) {
//                    displayDailyReport ();
//                }
//
//                disconnectFromDB();
//            }
//        }

        // HANDLE ALL GET ROUTES
        // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('insertQueryRequest', $_GET)) {
                    displayDailyReport ();
                }

                disconnectFromDB();
            }
        }

        if (isset($_GET['insertSubmit'])) {
            handleGETRequest();
        }
		?>
	</body>
</html>

