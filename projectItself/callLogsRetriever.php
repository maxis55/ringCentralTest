<?php
if (isset($_POST['action'])) {


        require('../vendor/autoload.php');
        require_once "database.php";
        $sdk = new RingCentral\SDK\SDK('HERE WAS KEY TO AUTH IN RINGCNETRAL', 'HERE WAS SECRET KEY TO AUTH IN RINGCENTRAL', RingCentral\SDK\SDK::SERVER_PRODUCTION);
        $platform = $sdk->platform();
        $json = file_get_contents('HERE WAS JSON FOR AUTH IN RINGCENTRAL');
        $array = json_decode($json, true);
        $platform->auth()->setData($array);
        $dateNow = new \DateTime();
        $dateStart = (new \DateTime())->modify('-2 day')->format(DateTime::ISO8601); //Date format as yyyy-mm-ddThh:mm:ss+0200 -> GMT+2. Change?
        $dateTo = (new \DateTime())->format(DateTime::ISO8601);

        $sql = "SELECT * FROM operator";
        $result = $dbconnection->query($sql);

        $sqlPhoneLog_insert = "INSERT INTO phonelog(
            call_date,
            numb_inbound,
            numb_outbound,
            duration,
            operator_id) VALUES (
            :call_date, 
            :numb_inbound,
            :numb_outbound,
            :duration,
            :operator_id)";

        $sqlOperator_update = "UPDATE operator SET
            numberofcalls6h = :numberofcalls6h,
            numberofcalls24h = :numberofcalls24h,
            numberofcalls48h = :numberofcalls48h,
            date_lastcall = :date_lastcall
            WHERE id = :operatorid";

        $calllogInsert = $dbconnection->prepare($sqlPhoneLog_insert);
        $operatorUpdate = $dbconnection->prepare($sqlOperator_update);



    set_time_limit(300); //set time out because project cant handle a lot of cycles sometimes
        if ($result->rowCount() > 0) {
            $vowels = array(")", "(", "-", " ");
            while ($row = $result->fetch()) {
                $tempPhone = str_replace($vowels, "", $row['phonenumber']); //make all phones same
                $numberofcalls6h = 0;
                $numberofcalls24h = 0;
                $numberofcalls48h = 0;
                $date_lastcall = $row['date_lastcall'];
                $callLogRecords = $platform->get('/account/~/extension/~/call-log', array(
                    'dateFrom' => $dateStart,
                    //date format is correct, handle pagination?
                    "perPage" => 1000, //48h==2880min==calls 1min each, 1k per page is max numb, pagination??
                    'phoneNumber' => $tempPhone,
                    'dateTo' => $dateTo))->json()->records;

                foreach ($callLogRecords as $item) {
                    $duration = $item->duration;
                    $call_date = date("Y-m-d H:i:s", strtotime($item->startTime));
                    $operatorId = $row['id'];
                    $numbInbound = $item->to->phoneNumber;
                    $numbOutbound = $item->from->phoneNumber;
//                    if ($numbOutbound == NULL) do something with no outbound numbers??
//                        print_r($item);
                    $calllogInsert->bindParam(':call_date', $call_date, PDO::PARAM_STR);
                    $calllogInsert->bindParam(':numb_inbound', $numbInbound, PDO::PARAM_STR);
                    $calllogInsert->bindParam(':numb_outbound', $numbOutbound, PDO::PARAM_STR);
                    $calllogInsert->bindParam(':duration', $duration, PDO::PARAM_INT);
                    $calllogInsert->bindParam(':operator_id', $operatorId, PDO::PARAM_INT);
                    $calllogInsert->execute();
                    if ((strtotime($item->startTime) - strtotime($dateNow->format(DateTime::ISO8601) . ' -6 hour')) > 0) {
                        $numberofcalls6h++;
                        $numberofcalls24h++;
                        $numberofcalls48h++;
                    } elseif ((strtotime($item->startTime) - strtotime($dateNow->format(DateTime::ISO8601) . ' -24 hour')) > 0) {
                        $numberofcalls24h++;
                        $numberofcalls48h++;
                    } else {
                        $numberofcalls48h++;
                    }
                    if ($date_lastcall == NULL || (strtotime($date_lastcall) - strtotime($item->startTime)) < 0) {
                        $date_lastcall = date("Y-m-d H:i:s", strtotime($item->startTime));
                    }
                }
                $operatorUpdate->bindParam(':numberofcalls6h', $numberofcalls6h, PDO::PARAM_INT);
                $operatorUpdate->bindParam(':numberofcalls24h', $numberofcalls24h, PDO::PARAM_INT);
                $operatorUpdate->bindParam(':numberofcalls48h', $numberofcalls48h, PDO::PARAM_INT);
                $operatorUpdate->bindParam(':date_lastcall', $date_lastcall, PDO::PARAM_STR);
                $operatorUpdate->bindParam(':operatorid', $row['id'], PDO::PARAM_INT);
                $operatorUpdate->execute();
            }

        } else {
            echo "0 results";
        }

}
//$json_data = json_encode($callLogRecords,JSON_PRETTY_PRINT);
//file_put_contents('projectItself/
