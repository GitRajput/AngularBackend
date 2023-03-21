 <?php 
    header("Access-Control-Allow-Origin: *");

	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: *');
	header('Access-Control-Allow-Headers: *');
	header('Content-Type: application/json');
    include "./api.php"; 

    //D
    
    if (isset($_GET["id"]))
    {
        try
        {
            delete_record($_GET["id"]);
            return true;
        }

        //General catchall
        catch (Exception $e)
            { generate_error($e -> getMessage()); }
    }
 
 
 
 ?>