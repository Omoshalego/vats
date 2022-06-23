<?php
//Aurther : Martin Omondi
// Date : 17/06/2022

// Create a VAT calculator that shows a history of calculations requested that can be exported as a CSV file.
// For user provided monetary value V and VAT percentage rate R, calculate and display both sets of calculations:
// Where V is treated as being ex VAT show the original value V, the value V with VAT added and the amount of VAT calculated at the rate R.
// Where V is treated as being inc VAT show the original value V, the value V with VAT subtracted and the amount of VAT calculated at the rate R.
// The results from each requested set of calculations should be stored, and displayed on screen as a table of historical calculations.
// The history should be able to be cleared and exportable to a CSV file.

// connecting to the database
require("db_connection.php");
$mainDisplay=true; //Setting the landing page using this boolean
?>

<!DOCTYPE html>
 <html lang="en">
  <head>
    <!-- metadata -->
    <title>Tax Calculator</title>
     <meta charset="utf-8"> 
     <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes">
     <link rel="stylesheet" href="style.css">
     <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" crossorigin="anonymous">
 </head>

<body>   
<?php
    if($mainDisplay==true && !isset($_POST["history"]) ){ //Display the landing page

        // Check if the "Calculate" button was pressed, then get the data from inputs
        if (isset($_POST['monetaryAmmount']) && isset($_POST['taxPercentage']) && isset($_POST["Vat_Excl"])) {
            $monetaryAmmount = (float) $_POST['monetaryAmmount']; //capture the user input
            $taxPercentage = (float) $_POST['taxPercentage'];    //capture the user input
            $vatAmount =round(($monetaryAmmount * $taxPercentage) / 100, 2); //rounding off the user input

            $gross =  round($monetaryAmmount - $vatAmount, 2); //calculating the gross pay for the tax
            $details="Value Excl vat:";                        //setting the vat details
        } 

        elseif (isset($_POST['monetaryAmmount']) && isset($_POST['taxPercentage']) && isset($_POST["Vat_Incl"])) {
            $monetaryAmmount = (float) $_POST['monetaryAmmount']; //capturing the user input
            $taxPercentage = (float) $_POST['taxPercentage'];     //capturing the user % rate input
            $vatAmount =round(($monetaryAmmount * $taxPercentage) / 100, 2);  //rounding off the user input

            $gross =  round($monetaryAmmount +  $vatAmount, 2); //calculating the gross pay for the tax
            $details="Value Incl vat:";                          //setting the vat details
        } else {
             //setting the defualt value on the variable if no amount is not set
            $monetaryAmmount = 0; 
            $taxPercentage = 0;
            $gross=0;
            $vatAmount=0;
            $details="No value:";
        }
 
            // Make the calculations
            $taxAmount =  $vatAmount;
            $finalAmount = $gross;   
    ?>
<!-- Tax calculator form to capture the user input -->
<div class="content">
<div class="taxcal">
    <h1>VAT CACULATOR</h1>
    <form action="" method="POST" id="myForm">
        <label for="monetary">Monetary amount</label>
        <br>
        <input type="number" id="monetaryAmmount" name="monetaryAmmount" 
        value="<?=$monetaryAmmount ?>" required pattern="[0-9]" step="0.01" min="0">
        <br>

        <label for="tax">VAT Percentage</label>
        <br>
        <input type="number" id="taxPercentage" name="taxPercentage" 
        value="<?=$taxPercentage ?>" required pattern="[0-9]" step="0.01" min="0" max="100">
        <br>

        <input type="Submit" value="Vat_Excl" name="Vat_Excl" id="calculateBtn">
        <input type="Submit" value="Vat_Incl" name="Vat_Incl" id="calculateBtn">
        <input type="Submit" value="Clear" name="clear" id="calculateBtn">
        <input type="Submit" value="History" name="history" id="calculateBtn">
    </form>
        <!-- Display the calculation output -->
    <div id="summary">

    <p> Amount: 
        <span id="taxAmmount">
            <!-- format the output figure to two decimal place -->
            <?= "£ ". number_format($monetaryAmmount,2) ?>  
        </span>
    </p>

    <p>Vat Rate: 
        <span id="vatRate">
            <?=round( $taxPercentage) . "%"?>
        </span>
    </p>

        <p>Vat Amount: 
        <span id="taxAmmount">
            <?="£ ". number_format($taxAmount,2) ?>
            </span>
        </p>

        <p><?=$details ?>
        <span id="finalAmmount">
            <?= "£ ". number_format($finalAmount,2) ?>
        </span>
        </p>

        <p>
    <?php

        // inserting to the sale table the user value
    if(isset($_POST["Vat_Excl"]))
        {	 
            $val_incl =  $monetaryAmmount;
            $rate = round($_POST['taxPercentage']);

            $vat_val = $taxAmount;
            $val_excl =  $finalAmount;
                
            $sql = "INSERT INTO sale (val_incl,rate,vat_val,val_excl)
            VALUES ('$val_excl','$rate','$vat_val',$val_incl)";

            if (mysqli_query($con, $sql)) {
            
            } else {
                echo "Error: " . $sql . "
        " . mysqli_error($con);
            }
            mysqli_close($con);
        }

     elseif(isset($_POST["Vat_Incl"]))
        {	 
            $val_incl =  $monetaryAmmount;
            $rate = round($_POST['taxPercentage']);
            $vat_val = $taxAmount;
            $val_excl =  $finalAmount;

            $sql = "INSERT INTO sale (val_incl,rate,vat_val,val_excl)
            VALUES ('$val_incl','$rate','$vat_val',$val_excl)";

         if (mysqli_query($con, $sql)) {              
            } else {
                echo "Error: " . $sql . "
        " . mysqli_error($con);
            }
            mysqli_close($con);
        }
            ?>
            </p>
        </div>
    </div>       
<?php
}
     // Displaying the user input to the table
     if(isset($_POST["history"]))
     {	
        $mainDisplay=false; //Removing the input form from the display and setting displaying the outputon the table
    ?>
  <div class="taxDisplay">
    <?php       
      $Sql = "SELECT * FROM sale"; //setting the record from the sale table to display
      $result = mysqli_query($con, $Sql); 

    if (mysqli_num_rows($result) > 0) {  //Checking if there are record on the sale table
             //Displaying the records to the table  
        echo "<div class='table-responsive'><table id='myTable' class='table table-striped table-bordered' >
         <thead>
           <tr>
             <th>Value_exclusive VAT</th>
             <th>Rate</th>
             <th>VAT Amount</th>
             <th>Value inclusive VAT</th>
            </tr></thead><tbody>";

         //Looping through the records from the sales table as they are printed on the table
       while($row = mysqli_fetch_assoc($result)) {
            echo "<tr><td>" . $row['val_incl']."</td>
                   <td>" . $row['rate']."</td>
                   <td>" . $row['vat_val']."</td>
                   <td>" . $row['val_excl']."</td>";        
                   }
            echo "</tbody></table></div>";

       //Otherwise if there are no records from the sale table print no records
       } else {
            echo "You have no records on this table";
       }
        ?>
     <!-- Button to export the records as CSV file and is send to file functions.php where the export as csv take place-->
    <form class="form-horizontal" action="functions.php" method="post" name="upload_excel" enctype="multipart/form-data">
            <div class="form-group">
                <div class="col-md-4 col-md-offset-4">
                <input type="submit" name="Export" class="btn btn-success" value="Export as CSV"/>
                </div>
            </div>                    
    </form>  
    <!-- Button to return to the calculator application -->
    <form class="form-horizontal" action="index.php" method="post" name="upload_excel" enctype="multipart/form-data">
            <div class="form-group">
                <div class="col-md-4 col-md-offset-4">
                
                <input type="Submit" value="VAT Calc" name="vatCal" class="btn btn-success">
                </div>
            </div>                    
        </form>            
    </div>
    </div>
    <?php
     }           
    ?>
 </div>
</body>
</html>