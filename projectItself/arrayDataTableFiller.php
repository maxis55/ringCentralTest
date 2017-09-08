<?php
include_once "database.php";
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'insert':{
            //this is needed to fill arrays.txt, which is used for ajax update in table
            $smtn=$dbconnection->prepare("SELECT * from operator");
            $smtn->execute();
            $lines=$smtn->rowCount();
            $resultToFile='{ "data": [ ';
            $j=0;
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

        }
            file_put_contents('arrays.txt', $resultToFile);
            insert();
            break;
        case 'select':
            select();
            break;
    }
}
function select() { //this is left for tests, otherwise useless code

    echo "The select function is called.";
    exit;
}

function insert() {
    echo "The insert function is called.";
    exit;
}
?>