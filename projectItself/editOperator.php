<?php
require_once "database.php";
if (isset($_REQUEST['operator_edit_name'])) {
    $allnull = true;
    $operatorid=$_REQUEST['operator_edit_id'];
    $name =$_REQUEST['operator_edit_name'];
    $phonenumber =$_REQUEST['operator_edit_phonenumber'];
    $numberofcalls6h =$_REQUEST['operator_edit_calls6h'];
    $numberofcalls24h =$_REQUEST['operator_edit_calls24h'];
    $numberofcalls48h =$_REQUEST['operator_edit_calls48h'];
    $date_lastcall_time =$_REQUEST['operator_edit_lastcall_time'];
    $date_lastcall =$_REQUEST['operator_edit_lastcall'].$date_lastcall_time;

    $sqlOperator_update_all = "UPDATE operator SET
            name=:name,
            phonenumber=:phonenumber,
            numberofcalls6h = :numberofcalls6h,
            numberofcalls24h = :numberofcalls24h,
            numberofcalls48h = :numberofcalls48h,
            date_lastcall = :date_lastcall
            WHERE id = :operatorid";
    $stmt = $dbconnection->prepare($sqlOperator_update_all);
    $stmt->execute(array(':name' => $name,':phonenumber'=>$phonenumber,':numberofcalls6h'=>$numberofcalls6h,
        ':numberofcalls24h'=>$numberofcalls24h,':numberofcalls48h'=>$numberofcalls48h,
        ':date_lastcall'=>$date_lastcall,':operatorid'=>$operatorid));

    $smtn=$dbconnection->prepare("SELECT * from operator");
    $smtn->execute();
    $lines=$smtn->rowCount();
    $resultToFile='{ "data": [ ';
    $j=0;
    //filling array for DataTable with buttons+info in ~json format
    while ($item=$smtn->fetch(PDO::FETCH_ASSOC)){
        $resultToFile=$resultToFile."[";
        $len=count($item);
        $idtemp=0;
        foreach ($item as $key=>$som){
//            foreach ($item as $som){
            if (!in_array($key, array('id')))
                if($som===null||$som===0)
                    $resultToFile = $resultToFile."\"None yet\", ";
                else
                    $resultToFile = $resultToFile."\"" .$som. "\", ";
            $idtemp=$item['id'];
        }
        $resultToFile=$resultToFile.'"<button data-toggle=\'modal\' data-target=\'#view-modal\' data-id=\''.$idtemp.'\' id=\'getCalls\' class=\'btn btn-sm btn-info\'><i class=\'glyphicon glyphicon-eye-open\'></i>View</button>", ';

        $resultToFile=$resultToFile.'"<button class=\'btn btn-sm btn-info operatoredit\' href=\'#operator_edit_form\' id=\'operator_id_'. $idtemp.'\' >Edit</button>"';
        $j++;
        if($j!=$lines)
            $resultToFile=$resultToFile."],";
        else
            $resultToFile=$resultToFile."]";
    }
    $resultToFile=$resultToFile."] }";
    $resultToFile=stripslashes(trim($resultToFile));
    file_put_contents('arrays.txt', $resultToFile);


}