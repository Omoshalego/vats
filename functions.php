<?php
//connecting to the database
require("db_connection.php");

//Exporting the sale data as CSV file
  if(isset($_POST["Export"])){ 
    header('Content-Type: text/csv; charset=utf-8');  

    header('Content-Disposition: attachment; filename=sales.csv');  
    $output = fopen("php://output", "w");  

    fputcsv($output, array( 'val_excl', 'rate', 'vat_val', 'val_incl'));  
    $query = "SELECT val_excl,rate,vat_val,val_incl from sale ORDER BY id DESC"; 
     
    $result = mysqli_query($con, $query);  
    while($row = mysqli_fetch_assoc($result))  
    {  
         fputcsv($output, $row);  
    }
    $query = "DELETE  from sale";  //deleting the data from the sale table after exporting the data as CSV
    $result = mysqli_query($con, $query);    
    fclose($output);  
}  
  
 ?>