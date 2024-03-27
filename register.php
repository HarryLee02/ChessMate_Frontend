<?php 
include('dbcon.php');
session_start();

if(isset($_POST['save_push_data']))
{
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $data = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
    ];

   $ref = "contact/";
   $postdata = $database->getReference($ref)->push($data);

   if($postdata){
        $_SESSION['status'] = "Data Inserted Successfully";
        header("Location: index.php");
    }else{
        $_SESSION['status'] = "Data Not Inserted. Something Went Wrong.!";
        header("Location: index.php");
    }

}
