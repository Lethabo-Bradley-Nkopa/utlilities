<?php 

$localhost = "sql105.infinityfree.com";
$username = "if0_38470331_";
$password = "J3I0NX94Jj4By";
$conn = new mysqli($localhost, $username,$password ,"if0_38470331_chat_net");
if($conn->connect_error){
    die("Connection failed: ".$conn->connect_error );
}

?>
