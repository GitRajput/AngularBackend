 <?php 
 header("Access-Control-Allow-Origin: *");

	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: *');
	header('Access-Control-Allow-Headers: *');
	header('Content-Type: application/json');
    include "./api.php"; 

    get_json_data();
    return true;
 
 
 ?>