

<?php

require_once "database.php";
if (isset($_REQUEST['id'])) {
    $allnull=true;
    $id = intval($_REQUEST['id']);
    $query = "SELECT * FROM phonelog WHERE operator_id=:id";
    $stmt = $dbconnection->prepare( $query );
    $stmt->execute(array(':id'=>$id));

    ?>


        <div class="table-responsive">

            <table id="callers" class="table table-striped table-bordered"  width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th>Call Date</th>
                    <th>Number Inbound</th>
                    <th>Number Outbound</th>
                    <th>Duration</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>Call Date</th>
                    <th>Number Inbound</th>
                    <th>Number Outbound</th>
                    <th>Duration</th>
                </tr>
                </tfoot>
                <tbody>
                    <?php

                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        $allnull=false;
                        extract($row);
                            echo "<td>" . $call_date . "</td>";
                            echo "<td>" . $numb_inbound . "</td>";
                            echo "<td>" . $numb_outbound . "</td>";
                            echo "<td>" . $duration . "</td>";
                        echo "</tr>";
                    }
                        if ($allnull) {
                            echo "<td>" . "None yet" . "</td>";
                            echo "<td>" . "None yet" . "</td>";
                            echo "<td>" . "None yet" . "</td>";
                            echo "<td>" . "None yet" . "</td>";
                            echo "</tr>";
                        }

                    ?>

                </tbody>
            </table>

    </div>

    <?php
    }
