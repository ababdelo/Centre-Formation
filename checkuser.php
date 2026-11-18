<?php
session_start();
require_once __DIR__ . '/config/connection.php';
if(isset($_POST["email"]) && isset($_POST["password"])){
  $email=$_POST["email"];
  $password=$_POST["password"];
   $stmt=$pdo->prepare("select * from utilisateur where email=? and mot_de_passe=?");
   $stmt->execute([$email,$password]);
   $ligne=$stmt->fetch(PDO::FETCH_ASSOC);
   if($ligne["role"]=="ADMIN"){
       $_SESSION["name"]=$ligne["nom"];
       header("location:admin.php");
   }else{
     header("location:index.php?err=1");
   }
}
 
