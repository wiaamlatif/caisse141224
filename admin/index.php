<?php

  session_start();

  //var_dump($_SESSION['user']);die();

  if(!isset($_SESSION['user'])){
    header('location:../include/connexion.php');
  }
  
$title ="Dashboard";
ob_start();
?>
    <h3>Bonjour <?=$_SESSION['user']['login']?></h3>
 

<?php $content = ob_get_clean(); ?>

<?php $role=1; //$role= array(0 => Visitor, 1 => Admin, 2 => Seller)?>
<?php $varSell="Sell";$varData="Data";?>
<?php include "c:/caisse191124/layout.php" ?>





