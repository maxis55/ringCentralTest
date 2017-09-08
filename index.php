<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
    <title>This is a big demo for tests</title>
    <link rel="stylesheet" type="text/css" href="./src/css/jquery.fancybox.css">
    <link rel="stylesheet" type="text/css" href="src/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="./src/jquery-ui-1.12.1/jquery-ui.min.css">
    <link rel="stylesheet" href="./src/css/bootstrap.min.css">
</head>
<body>

<script src="./src/js/jquery-3.2.1.js"></script>
<script src="./src/js/jquery.fancybox.js"></script>
<script src="src/js/jquery.dataTables.min.js"></script>
<script src="./src/js/bootstrap.min.js"></script>
<script src="./src/jquery-ui-1.12.1/jquery-ui.min.js"></script>
<script>
    $(document).ready(function() {
        $("#login-button").fancybox({
            'scrolling'		: 'no',
            'titleShow'		: false,
            'onClosed'		: function() {
                $("#login_error").hide();
            }
        });

        $("#registration-button").fancybox({
            'scrolling'		: 'no',
            'titleShow'		: false,
            'onClosed'		: function() {
                $("#registration_error").hide();
            }
        });

        $("#message-button").fancybox({
            'scrolling'		: 'no',
            'titleShow'		: false,
            'onClosed'		: function() {
                $("#message_error_error").hide();
            }
        });

        var table=$('#operators').DataTable({
            "ajax": 'projectItself/arrays.txt', //dynamic data
            "fnDrawCallback": function () {
                $(".btn.btn-sm.btn-info.operatoredit").fancybox({
                    'scrolling'		: 'no',
                    'titleShow'		: false,
                    'onClosed'		: function() {
                        $("#operator_edit_error").hide();
                    }

                });
                $('[id^="operator_id_"]').click(function() {  //all that have operator_id_ in id will be bound to this
                    var uid = $(this).attr('id');
                    uid=uid.replace('operator_id_', ''); // it will get id of clicked row
                    var d = table.row($(this).parent()).data();
                    var name = d[0];
                    var phone = d[1];
                    var calls6h = d[2];
                    var calls24h = d[3];
                    var calls48h = d[4];
                    var lastcall = d[5];
                    var lastcalltime=lastcall.substr(lastcall.length - 8);
                    lastcall=lastcall.replace(lastcalltime,'');  //filling for for editing operator
                    $("#operator_edit_id").val(uid);
                    $("#operator_edit_name").val(name);
                    $("#operator_edit_phonenumber").val(phone);
                    $("#operator_edit_calls6h").val(calls6h);
                    $("#operator_edit_calls24h").val(calls24h);
                    $("#operator_edit_calls48h").val(calls48h);
                    $("#operator_edit_lastcall").val(lastcall);
                    $("#operator_edit_lastcall_time").val(lastcalltime);
                });

                $(document).on('click', '#operator_edit_send', function(e){
                    e.preventDefault();
                    var name = $("#operator_edit_name").val();
                    var phone = $("#operator_edit_phonenumber").val();
                    var calls6h = $("#operator_edit_calls6h").val();
                    var calls24h = $("#operator_edit_calls24h").val();
                    var calls48h = $("#operator_edit_calls48h").val();
                    var lastcall = $("#operator_edit_lastcall").val();
                    var lastcall_time = $("#operator_edit_lastcall_time").val();
                    var parts = lastcall_time.split(':');

                    if(phone!=''&&name!=''&&calls6h!=''&&calls24h!=''
                        &&calls48h!=''&&lastcall!=''&&lastcall_time!='') {
                        if(calls6h>=0&&calls24h>=0&&calls48h>=0) {
                            if((/^\d{2}:\d{2}:\d{2}$/.test(lastcall_time))&&(parts[0] < 23 && parts[1] < 59 && parts[2] < 59)) {
                                $.ajax({
                                    type: "POST",
                                    cache: false,
                                    url: "projectItself/editOperator.php",
                                    data: $('#operator_edit_form').serializeArray(),
                                    success: function () {
                                        table.ajax.reload();
                                        parent.$.fancybox.close();
                                    }
                                });
                            }
                            else {
                                $("#operator_edit_error").html("Time is incorrect").show();
                            }
                        }
                        else {
                            $("#operator_edit_error").html("Number of calls cant be lower than 0").show();
                        }
                    }else {
                        $("#operator_edit_error").show();
                    }

                });
            }
        } );

        $(document).on('click', '#getCalls', function(e){
            e.preventDefault();
            var uid = $(this).data('id');   // it will get id of clicked row
            $('#dynamic-content').html(''); // leave it blank before ajax call
            $('#modal-loader').show();      // load ajax loader
            $.ajax({
                url: 'projectItself/getcalls.php',
                type: 'POST',
                data: 'id='+uid,
                dataType: 'html'
            })
                .done(function(data){
                    console.log(data);
                    $('#dynamic-content').html(data); // load response
                    $('#callers').DataTable();
                    $('#modal-loader').hide();		  // hide ajax loader
                })
                .fail(function(){
                    $('#dynamic-content').html('<i class="glyphicon glyphicon-info-sign"></i> Something went wrong, Please try again...');
                    $('#modal-loader').hide();
                });
        });




        $(document).on("click","#login_send", function(e) { //authentification
            e.preventDefault();
            var login_name = $("#login_name").val();
            var login_pass = $("#login_pass").val();
            if(login_name!=''&&login_pass!=''){ //validation of textfields not being empty
                $.ajax({
                type: "POST",
                cache: false,
                url: "projectItself/loginPage.php",  //final validation, check in DB
                data: $('#login_form').serializeArray(),
                    success: function (data) {
                        if(data==="success"){
                            $("#login_error").hide();
                            alert("Successfull authorization");
                            parent.$.fancybox.close();
                        }else{
                            $("#login_error").hide();
                            alert("Login and password do not match the database");
                        }
                    }
                })
            }
            else {
                $("#login_error").show();
            }
        });


        $(document).on("click","#registration_send", function(e) {  //register new user in DB
            e.preventDefault();
            var registration_name = $("#registration_name").val();
            var registration_pass = $("#registration_pass").val();
            var registration_pass_conf = $("#registration_pass_conf").val();
            if(registration_name!=''&&registration_pass!=''&&registration_pass_conf!='') {//validation
                if(registration_pass===registration_pass_conf) {
                    $.ajax({
                        type: "POST",
                        cache: false,
                        url: "projectItself/registrationPage.php",
                        data: $('#registration_form').serializeArray(),
                        success: function () {
                                alert("Successfull registration");
                                parent.$.fancybox.close();
                        }
                    });
                }
                else {
                    $("#registration_error").html("Password and password confirmation do not match").show();
                }
            }else {
                $("#registration_error").show();
            }
        });


        $( "#operator_edit_lastcall" ).datepicker({ //date picked in for editing operator
            dateFormat: "yy-mm-dd"
        });

        $('#buttonForArray').click(function(){  //update table with data of operators, not needed, left for tests
            var clickBtnValue = 'insert';
            var ajaxurl = 'projectItself/arrayDataTableFiller.php',
                data =  {'action': clickBtnValue};
            $.post(ajaxurl, data, function () {
                table.ajax.reload();
                alert("Array for DataTables updated");

            });
        });

        $('#buttonLogs').click(function(){
            var clickBtnValue = $(this).val();
            var ajaxurl = 'projectItself/callLogsRetriever.php',  //get logs from ringcentral
                data =  {'action': clickBtnValue};
            var ajaxurl2 = 'projectItself/arrayDataTableFiller.php', //update table with data of operators
                data2 =  {'action': 'insert'};
            $.post(ajaxurl, data, function () {
                alert("CallLogs retrieved");
                $.post(ajaxurl2, data2, function () {
                    table.ajax.reload();
                    alert("Array for DataTables updated");
                });
            });
        });

        $('#message_send').click(function(e){
            e.preventDefault();
            var radioSelected = $('input[name=radioForMessage]:checked','#message_form');
            if(radioSelected!=null) {
            var requestFromUser=$('#message_request').val();
            //regular expression to test that template used in a right way
                var patt = /\{[\$#]\w*\}|\[[#]\w*\]|\{\w*\}|\[\w*\]/gi;
                if (patt.test(requestFromUser)) {
                    var tempRequest=requestFromUser.replace(/[\[#\$\{}\]]/gi,'');
                    var data="";
                    var result="";
                    switch (tempRequest){ //find html of item that needs to be proccesed
                        case "Name"||"name":
                            data=radioSelected.closest("tr").find(".message-name").text();
                            break;
                        case "Phone_number"||"Phone number":
                            data=radioSelected.closest("tr").find(".message-phone").text();
                            break;
                        case "Calls_last_6_hours"||"Number of calls in 6 hours":
                            data=radioSelected.closest("tr").find(".message-calls6h").text();
                            break;
                        case "Calls_last_24_hours"||"Number of calls in 24 hours":
                            data=radioSelected.closest("tr").find(".message-calls24h").text();
                            break;
                        case "Calls_last_48_hours"||"Number of calls in 48 hours":
                            data=radioSelected.closest("tr").find(".message-calls48h").text();
                            break;
                        case "Date_of_last_call"||"Date of last call":
                            data=radioSelected.closest("tr").find(".message-lastcall").text();
                            break;
                        default: data="Wrong column name!"; break;
                    }

                    if(data!="Wrong column name!"){
                        if(/\{#\w*\}/.test(requestFromUser)){ //format if phone number->(xxx) xxx-xxxx, else just show
                            if(tempRequest!="Phone_number"&&tempRequest!="Phone number"){
                                result=data;
                            }else {
                                result=data.replace(/[()-]/gi,'').replace(" ",'');
                                result='('+result.substr(0, 3) + ') ' + result.substr(3, 3) + '-' + result.substr(6,4);
                            }
                        }else{
                            if(/\{\$\w*\}/.test(requestFromUser)){//Every word capitalized, else - show
                                if(tempRequest!="Name"&&tempRequest!="name"){
                                    result=data;
                                }else {
                                    result=data.toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
                                }
                            }else {
                                if (/\{\w*\}/.test(requestFromUser)){ //show data of chosen column
                                    result=data;
                                }
                                else {
                                    if(/\[#\w*\]/.test(requestFromUser)){//show date as m/d/Y H:i:s, else - show chosen column
                                        if(tempRequest!="Date_of_last_call"&&tempRequest!="Date of last call"){
                                            result=data;
                                        }else {
                                            var tempDate=data.replace(" ",'-').split('-'); //convert to Date format?
                                            result=tempDate[2]+"/"+tempDate[1]+"/"+tempDate[0]+" "+tempDate[3];

                                        }
                                    }
                                    else {
                                        if(/\[\w*\]/.test(requestFromUser)){ //show date as m/d/Y, else show chosen column
                                            if(tempRequest!="Date_of_last_call"&&tempRequest!="Date of last call"){
                                                result=data;
                                            }else {
                                                var tempDate2=data.replace(" ",'-').split('-');//convert to Date format?
                                                result=tempDate2[2]+"/"+tempDate2[1]+"/"+tempDate2[0];
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        alert(result); //show result of convertation
                    }else {
                        alert(data); //say Wrong column name!
                    }
                }
                else {
                    alert("Wrong pattern!");
                }
            }
            else{
                alert("Radio button not selected!");
            }
        });


        $('.table tbody tr').click(function(event) { //this allows select radiobutton after clicking on row
            if (event.target.type !== 'radio') {
                $(':radio', this).trigger('click');
            }
        });
    });

</script>
<table id="operators" class="table table-striped table-bordered" width="100%" cellspacing="0">
<thead>
<tr>
    <th>Name</th>
    <th>Phone number</th>
    <th>Number of calls in 6 hours</th>
    <th>Number of calls in 24 hours</th>
    <th>Number of calls in 48 hours</th>
    <th>Date of last call</th>
    <th>View calls</th>
    <th>Edit operator</th>
</tr>
</thead>
<tfoot>
<tr>
    <th>Name</th>
    <th>Phone number</th>
    <th>Number of calls in 6 hours</th>
    <th>Number of calls in 24 hours</th>
    <th>Number of calls in 48 hours</th>
    <th>Date of last call</th>
    <th>View calls</th>
    <th>Edit operator</th>
</tr>
</tfoot>
<tbody>
</tbody>
</table>
<a id="login-button" href="#login_form"  class="btn btn-sm btn-info">Open login form</a>
<a id="registration-button" href="#registration_form" class="btn btn-sm btn-info" >Open registration form</a>
<a id="message-button" href="#message_form" class="btn btn-sm btn-info" >Open message</a>
<div style="display:none">
    <form id="login_form" method="post" action="" style="text-align: center;">
        <p style="display: none; background-color: red;" id="login_error">Please, enter data</p>
        <p>
            <label for="login_name">Login: </label>
            <input type="text" id="login_name" name="login_name" size="30" />
        </p>
        <p>
            <label for="login_pass">Password: </label>
            <input type="password" id="login_pass" name="login_pass" size="30" />
        </p>
        <p>
            <button id="login_send" class="btn btn-sm btn-info">Login</button>
        </p>
    </form>

    <form id="message_form" method="post" action="" style="text-align: center;">

        <table id="operators2" class="table table-striped table-bordered" width="100%" cellspacing="0">
            <thead>
            <tr>
                <th>Name</th>
                <th>Phone_number</th>
                <th>Calls_last_6_hours</th>
                <th>Calls_last_24_hours</th>
                <th>Calls_last_48_hours</th>
                <th>Date_of_last_call</th>
                <th>Chose row</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th>Name</th>
                <th>Phone_number</th>
                <th>Calls_last_6_hours</th>
                <th>Calls_last_24_hours</th>
                <th>Calls_last_48_hours</th>
                <th>Date_of_last_call</th>
                <th>Chose row</th>
            </tr>
            </tfoot>
            <tbody>
            <?php
            require_once "projectItself/database.php";
            $sql = "SELECT * FROM operator";
            $result = $dbconnection->query($sql);
            if ($result->rowCount() > 0) {
                $i=0;
                while ($row = $result->fetch()) {
                    echo "<tr>";

                    echo "<td class='message-name'>".$row['name']."</td>";
                    echo "<td class='message-phone'>".$row['phonenumber']."</td>";
                    echo "<td class='message-calls6h'>".$row['numberofcalls6h']."</td>";
                    echo "<td class='message-calls24h'>".$row['numberofcalls24h']."</td>";
                    echo "<td class='message-calls48h'>".$row['numberofcalls48h']."</td>";
                    if($row['date_lastcall']!=null)
                        echo "<td class='message-lastcall'>".$row['date_lastcall']."</td>";
                    else
                        echo "<td class='message-lastcall'>None yet</td>";
                    echo '<td>'.'<input type="radio" name="radioForMessage" value="messageFromOperator'.$row['id'].'"/>'.
          '<label for="radio">'.$row['id'].'</label>'.'</td>';
                    echo "</tr>";
                }
            }
            ?>
            </tbody>
        </table>
        <p>
            <label for="message_request">Message: </label>
            <input type="text" id="message_request" name="message_request" size="30" />
        </p>
        <p>
            <button id="message_send" class="btn btn-sm btn-info">Show</button>
        </p>
    </form>

    <form id="registration_form" method="post" action="" style="text-align: center;">
        <p style="display: none; background-color: red;" id="registration_error">Please, enter data</p>
        <p>
            <label for="registration_name">Login: </label>
            <input type="text" id="registration_name" name="registration_name" size="30" />
        </p>
        <p>
            <label for="registration_pass">Password: </label>
            <input type="password" id="registration_pass" name="registration_pass" size="30" />
        </p>
        <p>
            <label for="registration_pass_conf">Password confirmation: </label>
            <input type="password" id="registration_pass_conf" name="registration_pass_conf" size="30" />
        </p>
        <p>
            <button id="registration_send" class="btn btn-sm btn-info">Register</button>
        </p>
    </form>

    <form id="operator_edit_form" method="post" action="" style="text-align: center;">
        <p style="display: none; background-color: red;" id="operator_edit_error">Please, enter data</p>
        <p style="display: none">
            <input type="text" id="operator_edit_id" name="operator_edit_id" size="30" />
        </p>
        <p>
            <label for="operator_edit_name">Name: </label>
            <input type="text" id="operator_edit_name" name="operator_edit_name" size="30" />
        </p>
        <p>
            <label for="operator_edit_phonenumber">Phonenumber: </label>
            <input type="text" id="operator_edit_phonenumber" name="operator_edit_phonenumber" size="30" />
        </p>
        <p>
            <label for="operator_edit_calls6h">Calls in 6 hours: </label>
            <input type="number" id="operator_edit_calls6h" name="operator_edit_calls6h" size="30" />
        </p>
        <p>
            <label for="operator_edit_calls24h">Calls in 24 hours: </label>
            <input type="number" id="operator_edit_calls24h" name="operator_edit_calls24h" size="30" />
        </p>
        <p>
            <label for="operator_edit_calls48h">Calls in 48 hours: </label>
            <input type="number" id="operator_edit_calls48h" name="operator_edit_calls48h" size="30" />
        </p>
        <p>
            <label for="operator_edit_lastcall">Last call: </label>
            <input type="datetime" id="operator_edit_lastcall" name="operator_edit_lastcall" size="30" />
        </p>
        <p>
            <label for="operator_edit_lastcall_time">Last call: </label>
            <input type="datetime" id="operator_edit_lastcall_time" name="operator_edit_lastcall_time" size="30" />
        </p>
        <p>
            <button id="operator_edit_send" class="btn btn-sm btn-info">Edit</button>
        </p>
    </form>
</div>

<div id="view-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">
                    <i class="glyphicon glyphicon-user"></i> Call Logs
                </h4>
            </div>

            <div class="modal-body">
                <div id="modal-loader" style="display: none; text-align: center;">
                    <!-- ajax loader -->
                    <img src="images/ajax-loader.gif">
                </div>

                <!-- mysql data will be load here -->
                <div id="dynamic-content"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div><!-- /.modal -->


<!--update array with data of operators-->
<input type="submit" id="buttonForArray" value="Update array for DataTable if needed" />
<!--update logs in last 48h-->
<input type="submit" id="buttonLogs" value="Get more call logs+user info" />
<!--<input type="submit" class="buttonf" name="select" value="select" />-->
</body>
</html>
