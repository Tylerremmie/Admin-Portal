<?php
include("database.php");

$adminIDs = unserialize($_POST['input_name']);
$userid = array_keys($adminIDs);
//if user does not choose any options this advises them to update at least one field.
function checkIfEmpty($array){
    $count= 0;
    foreach ($array as $v) {
        if ($v == "BLANK"){
            $count++;
        }
    }
    if($count == count($array)){
        header("location:dashboard.php?msg=emptysubmit");
    }
    else return;
}
//Adding Permission Levels
if(isset($_POST['addlevel'])){

    $addition = $_POST['add'];
    $roleid = array_keys($addition);
    checkIfEmpty($addition);
    for($i = 0; $i < count($adminIDs); $i++){
        $u = $adminIDs[$userid[$i]];
        $r = $addition[$roleid[$i]];
        if($r!="BLANK"){
            //conditional query only runs if admin did  not previously have the permission
            $insert = "INSERT INTO user_roles (user_id, role_id)
                    SELECT $u, $r
                    WHERE NOT EXISTS( SELECT * FROM user_roles WHERE user_id = $u AND role_id = $r)";
            $result = mysqli_query($con, $insert);
            //inform whether the records were updated or already existed
            if(mysqli_affected_rows($con)>0){
				header("location:dashboard.php?msg=successadd");
                //echo nl2br("Permission for Admin ID $u Granted\n");
                
                //write a back button
            }else{
				header("location:dashboard.php?msg=failureadd");
                //echo 'Permission already existed'; 
            }
        }
    }

}
//Removing Permission levels
else if(isset($_POST['removelevel'])) {

    $remove = $_POST['remove'];
    $roleid = array_keys($remove);
    checkIfEmpty($remove);
    for($i = 0; $i < count($adminIDs); $i++){
        $u = $adminIDs[$userid[$i]];
        $r = $remove[$roleid[$i]];
        if($r!="BLANK"){
            $drop = "DELETE FROM user_roles
            WHERE user_id = $u AND role_id = $r";
            $result = mysqli_query($con, $drop);
            if(mysqli_affected_rows($con)>0){
				header("location:dashboard.php?msg=successdelete");
            
            }else{
                
				header("location:dashboard.php?msg=failuredelete");
            }
        
        }

    }
}

//Removing User Entirely. Cannot be undone
else if (isset($_POST['deleteadmin'])){
    //counts the number of _POST items in the form. 5 means that the user wants to delete so we return an error on counts below that
    if(count($_POST) < 5){
        header("location:dashboard.php?msg=failadminremove");
    }
    
    else{
        $deleteuser = $_POST['listtodelete'];
        foreach($deleteuser as $key => $value){
            $u = $adminIDs[$key];
            $delete = "DELETE FROM users WHERE id = $u";
            $result = mysqli_query($con, $delete) or die(mysqli_error($con));
            if(mysqli_affected_rows($con)>0){
                header("location:dashboard.php?msg=successadminremove");
            }
        }
    }
}


?>