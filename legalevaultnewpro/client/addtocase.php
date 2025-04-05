<?php
    require_once("includes/db.php"); // Include the database connection file

    if(isset($_POST["addcase"])){
        $casetype = $_POST["ctype"];
        $casedetails = $_POST["cdetails"];
        $case_status = "pending";
        
        $con = getDBConnection(); // Get database connection
        
        if ($con) {
            // Sanitize inputs
            $clientid = mysqli_real_escape_string($con, $_SESSION['client_id']);
            $lawyerid = mysqli_real_escape_string($con, $_GET['id']);

            $stmt = $con->prepare("INSERT INTO cases (
                case_type, case_details, case_status, lawyer_id_assigned, clientforcase_id)
                VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('sssss', $casetype, $casedetails, $case_status, $lawyerid, $clientid);
            $stmt->execute();
            
            if ($stmt->affected_rows === -1) {
                echo "Error";
            } else {
                $stmt->close();
                echo "Case Added"; // Echo only once
            }

            $accepted_status = "not yet accepted";
            $stmt = $con->prepare("INSERT INTO notifications (
                 client_id, lawyer_id, case_type, case_detail, accepted_status)
                VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('sssss', $clientid, $lawyerid, $casetype, $casedetails, $accepted_status);

            $stmt->execute();
            if ($stmt->affected_rows === -1) {
                echo "Error";
            } else {
                $stmt->close();
                header("Location: client_dashboard.php?q=currentcase"); // Redirect after successful insertion
                exit(); // Exit after redirection
            }
        }
    }
?>


<div class="container-fluid">
    <div class="row">
        <div class="col-sm-2">
            <h1>
                <?php echo $_SESSION["client_name"]; ?>
            </h1>
            <br>
            <ul id="side_menu" class="nav nav-pills nav-stacked">
                <li class="">
                    <a href="client_dashboard.php">
                        <span class="glyphicon glyphicon-user"></span>
                        &nbsp; Profile
                     </a>
                </li>
                <li class="">
                    <a href="client_dashboard.php?q=addcase">
                        <span class="glyphicon glyphicon-list-alt"></span>
                        &nbsp; Add case
                    </a>
                </li>
                <li class="">
                    <a href="client_dashboard.php?q=sendfeedback">
                        <span class="glyphicon glyphicon-comment"></span>
                        &nbsp; Send Feedback
                    </a>
                </li>
                <li class="">
                    <a href="client_dashboard.php?q=currentcase">
                        <span class="glyphicon glyphicon-ok"></span>
                        &nbsp; Current Case Info
                    </a>
                </li>
                <li class="">
                    <a href="client_dashboard.php?q=notifications">
                        <span class="glyphicon glyphicon-bullhorn"></span>
                        &nbsp; Notifications
                    </a>
                </li>
            </ul>
        </div>   <!--div ending of vertical nav -->

        <div class="col-sm-10" style="font-weight: bold; padding-bottom: 30px;">

            <h1>Enter Case details</h1><br>
            <form action='client_dashboard.php?q=addtocase&id=<?php echo $_GET['id']; ?>' method='post'>
                <label for='case-type'>
                    Case Type:
                </label>
                <input type='text' class='form-control'
                placeholder='Case Type' name='ctype' required><br>

                <label for='case-details'>
                    Case Details:
                </label>
                <input type='text' class='form-control'
                placeholder='Case Details' name='cdetails' required><br>

                <button class='btn btn-primary btn-lg btn-block' type='submit' name='addcase'>
                    Add Case
                </button>
            </form>

        </div>
   </div>
</div>
