<?php
session_start();
if(!isset($_SESSION)){
  header('location:http://localhost:8000/include/connexion.php');
} 

 //  echo $_GET['idUser'];
  
 //*______________________(panier.php)______________________ */
 //*_______(<121>form input valider input vider /form)_______*/

 //*____________________________________________________________*/
 //*PDO("mysql:host=localhost;dbname=caisse1124","root","root");*/
 //*____________________________________________________________*/
 //* Input this file  :
 //* - $panier, id_user, nr_ticket, 
 //* Update with this file :
 //* 

 //*_______(SELECT * FROM products)__________________________*/
 //* products :
 //* 1:id_product	2:name_product 3:id_category	4:description
 //* 5:price	6:discount	7:imgSrc	8:created_at	
 
 //*_______(INSERT INTO tickets)_____________________________*/
 //* tickets :
 //* 1:id_ticket	2:id_user	3:id_z	4:nr_ticket	
 //* 5:total_ticket	6:created_at	7:valider	

 //*_______(INSERT INTO lignes_ticket)_______________________*/
 //* lignes_ticket:
 //* 1:id_ligne_ticket  2:id_ticket  3:id_product
 //* 4:price            5:quantity   6:total_ligne

 
  include "count_items.php";

  // $idUser=1;
  // $idz=1;

  //echo "<pre>";
  //var_dump($panier);
  //echo "</pre>";
  
 
  //sqlValue
  if(!empty($panier)){

    $prd=array_keys($panier);
    $prdPanier=implode(",",$prd);
  
    require_once 'C:/caisse191124/include/database.php';
    
    $sqlstm = $pdo -> prepare('SELECT * FROM products 
                               WHERE id_product IN ('.$prdPanier.') ');
    $sqlstm -> execute();
    $prdPanier = $sqlstm -> fetchAll(PDO::FETCH_ASSOC);
 
    $itemsPanier=[];       
    $totalPanier=0; 
    foreach ($prdPanier as $row) { 

      $idProduct=$row['id_product'];
      $price=$row['price'];
      $qantity = $panier[$idProduct];      
      $totalItem=$price*$qantity;
      $totalPanier+= $totalItem  ;

      //Creer une table detail panier
      $itemsPanier[] = [
       "id_product" => $idProduct,
            "price" => $price,
         "quantity" => $panier[$idProduct],
      "total_ligne" => $totalItem
               ];
    }
  
   
  // Update table tickets :

  $nr_ticket = lastNrTicket()+1;

  $nr_ticket = str_pad($nr_ticket, 8, '0', STR_PAD_LEFT);
  
  //var_dump($nr_ticket);die();

  $id_user=$_SESSION['user']['id_user'];

  //echo $_SESSION['user']['login'];

  // var_dump($id_user); die();

  $sql="INSERT INTO tickets (id_user,nr_ticket,total_ticket) 
        VALUES (?,?,?);";
  $sqlStatement = $pdo ->prepare($sql);
  $sqlStatement -> execute([$id_user,$nr_ticket,$totalPanier]);
  $id_ticket  = $pdo -> lastInsertId();

  // Update table lignes_ticket :
  $fieldsLineTicket = ['id_product','price','quantity','total_ligne']; 

  $sqlValue="";
  for ($i=0; $i < $countItems ; $i++) {       
      $sqlBind[$i][0]="id_product$i";
      $sqlBind[$i][1]="price$i";
      $sqlBind[$i][2]="quantity$i";
      $sqlBind[$i][3]="total_ligne$i";

    $sqlValue.="(:id_ticket, :id_product$i, :price$i, :quantity$i, :total_ligne$i),";                        
  }

  $sqlValue=substr($sqlValue, 0, -1);
  
  $sql = "INSERT INTO lignes_ticket (id_ticket,id_product,price,quantity,total_ligne)
          VALUES $sqlValue ";

  $sqlStatement = $pdo->prepare($sql);

  for ($i=0; $i < $countItems ; $i++) {       

    $sqlStatement->bindParam(':id_ticket', $id_ticket);  
    
    for ($j=0; $j < count($fieldsLineTicket) ; $j++) { 
      $sqlStatement->bindParam(':'.$sqlBind[$i][$j], $itemsPanier[$i][$fieldsLineTicket[$j]]);       
    }

  }
    
  $sqlStatement->execute();

  for ($i=0; $i < 1000; $i++) { 
    $cookie_name=$i;
    setcookie($cookie_name,"", time() - 3600, '/');   
  }

  
  header( "location:panier.php");          

  }//if(!empty($panier))

  function lastNrTicket()
  {
    require 'C:caisse191124/include/database.php';

    $sql = 'SELECT * FROM tickets
            ORDER BY id_ticket DESC LIMIT 1';

    $sqlstm = $pdo -> prepare($sql);
    $sqlstm -> execute();
    
    $listTickets = $sqlstm -> fetch(PDO::FETCH_ASSOC);
    
    return (int) $listTickets['nr_ticket'];
  }

/*
    echo "<pre>";
    var_dump($prdPanier);
    echo "</pre>";die();
*/


 


