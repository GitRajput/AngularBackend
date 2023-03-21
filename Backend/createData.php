 <?php 
 header("Access-Control-Allow-Origin: *");

	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: *');
	header('Access-Control-Allow-Headers: *');
	header('Content-Type: application/json');
    include "./api.php"; 

    if (isset($_POST))
    {
        try
            { 
			create_record(); 
			return true;
			}

        //General catchall
        catch (Exception $e)
            { generate_error($e -> getMessage()); }
    }
 
 
 
 ?>