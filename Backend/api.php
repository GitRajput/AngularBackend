<?php
    //Log errors to file
    ini_set('log_errors', 1);
    ini_set('error_log', "./error.log");
	
?>
<?php
    define("FILE_MODE_READ", "r"); //NOTE: DOES NOT CREATE A FILE IF IT DOESN'T EXIST
    define("FILE_MODE_READWRITE", "r+"); //NOTE: DOES NOT CREATE A FILE IF IT DOESN'T EXIST
    define("FILE_MODE_WRITE", "w");
    define("FILE_MODE_READWRITE_TRUNCATE", "w+");
    define("FILE_MODE_WRITE_APPEND", "a");
    define("FILE_MODE_READWRITE_APPEND", "a+");

     define("RECORD_CSV_HEADER", ["Id","Name","Zip","Amount","Quantity","Item"]);
    //id : TVRecord
    //This is essentially a runtime map of TVRecord objects.
    $records = array();

    //Load all records from persistent
    $records = get_all_records();

    //C
    if (isset($_POST["createsubmit"]))
    {
        try
            { create_record(); }

        //General catchall
        catch (Exception $e)
            { generate_error($e -> getMessage()); }
    }

    //R
    if (isset($_POST["searchsubmit"]))
        { $records = [find_single_record($_POST["searchid"])]; }

    elseif (isset($_POST["filtersubmit"]))
        { $records = filter_by_brand($_POST["brandfilter"]); }

    elseif (isset($_POST["uploadsubmit"]))
    {
        debug_log(print_r($_FILES["newrecords"], true));

        if ($_FILES["newrecords"]["error"])
        {
            generate_error("Error uploading file ({$_FILES['newrecords']['error']})");
            return;
        }

        $is_safe = open_file_context_manager(
            $_FILES["newrecords"]["tmp_name"], FILE_MODE_READ,
            function($file)
            {
                $cols = fgetcsv($file);
                return $cols == RECORD_CSV_HEADER;
            }
        );

        if (!$is_safe)
        {
            generate_error("File has invalid headers. It must have the following: Id,Type,Brand,Model,Size,Price,Sale,Desc");
            return;
        }

        //Checks pass, let's accept the file
        move_uploaded_file(
            $_FILES["newrecords"]["tmp_name"],
            "./data/data.csv"
        );

        //Reload the page w/ new file
        header("Refresh:0; Location: index.php");
    }

    //Udate
    if (isset($_POST["updatesubmit"]))
    {
        try
            { update_record($_POST["id"]); }

        //General catchall
        catch (Exception $e)
            { generate_error($e -> getMessage()); }
    }

    //D
    if (isset($_POST["deleterecord"]))
    {
        try
        {
            delete_record($_POST["deleteid"]);
            //Redirect to the index page, clear the requests
            header("Refresh:0; Location: index.php");
        }

        //General catchall
        catch (Exception $e)
            { generate_error($e -> getMessage()); }
    }

    //Confirm deletion function
    //Echos a form to confirm record deletion w/ a confirm onsubmit event.
    //If user confirms, send post info to delete the record
    function add_delete_button($id)
    {
        echo "<form method='POST' onsubmit=\"return confirm('Are you sure you want to delete this record?');\">";
        echo "<input type='hidden' name='deleteid' value='$id'/>";
        echo "<input type='submit' name='deleterecord' value='Delete' style='background: rgba(211, 44, 44, 0.5);'>";
        echo "</form>";
    }

    //Echos a form that allows the user to update the record its attached to
    function add_update_button($id)
    {
        echo "<form method='get' action='update.php'>";
        echo "<input type='hidden' name='id' value='$id'/>";
        echo "<button type='submit' style='background: rgba(234, 159, 53, 0.5);'>Update</button>";
        echo "</form>";
    }

    //Function that generates a warning we can display to the user
    function generate_warning($message) {
        echo "<div class='warning'>$message</div>";
    }

    //Function that generates an error we can display to the user
    function generate_error($message) {
        echo "<div class='error'>$message</div>";
    }

    class TVRecord
    {
        public $id;
        public $item;
        public $quantity; 
        public $amount;		
        public $zip;
        public $name;
        
       
        public function __construct(
            $id,
            $item,
            $quantity,
            $amount,
            $zip,            
            $name           
        )
        {
            $this -> id = $id;
			$this -> item = $item;
			$this -> quantity = intval($quantity);
			$this -> amount = floatval($amount);  
            $this -> zip = $zip;            
            $this -> name = $name;
            
                      
        }
        

        public function dump_update_form()
        {
            echo "<legend>Editing TV: {$this->id}</legend>";
            echo "<input type='hidden' name='id' value='{$this->id}'/>";
           
            echo "<br><br>";
            

            echo "<br><br>";
            echo "<label for='name'>Name:</label>";
            echo "<input type='text' name='name' id='name' value='{$this->name}' required/>";
            echo "<br><br>";
            echo "<label for='quantity'>Quantity:</label>";
            echo "<input type='text' name='quantity' id='quantity' pattern='^\d+(\.\d+)?$' value='{$this->quantity}' required/>";
            echo "<br><br>";
            echo "<label for='amount'>Amount:</label>";
            echo "<input type='text' name='amount' id='amount' value='{$this->amount}' pattern='^\d+(\.\d+)?$' required/>";
            echo "<br><br>";
            echo "<label for='zip'>Zip:</label>";
            echo "<input type='text' name='zip' id='zip' value='{$this->zip}' required/>";
			echo "<br><br>";
            echo "<label for='item'>Item:</label>";
            echo "<input type='text' name='item' id='item' value='{$this->item}' required/>";
            echo "<br><br>";
            echo "<br><br>"; echo "<br><br><input type='submit' name='updatesubmit' value='Submit'/>";
        }

        public function __toString()
        {
            $rv = "<td><a href=info.php?id={$this -> id} style='background: rgba(80, 181, 65, 0.5);'>{$this->id}</a></td>";
            $rv .= "<td>{$this -> name}</td>";
			$rv .= "<td>{$this -> quantity}</td>";
			
            $rv .= "<td>{$this -> zip}</td>";
            if ($this -> amount)
            {
                $amount = number_format($this -> amount, 2);
                $rv .= "<td class='red'>\$$amount</td>";
            }

            
            
            $rv .= "<td>{$this -> item}</td>";

            

            return $rv;
        }

        public function to_array()
        {
            return array(
                $this -> id,
                $this -> item,
                $this -> name,
                $this -> zip,
                $this -> quantity,
                $this -> amount               
            );
        }

        public static function generate_id(): string
        {
            global $records;

            $hex_value = null;
            do
            {
                //Generate a unique id for this record
                $hex_value = substr("0x" . dechex(rand(10000, 99999)), -5);
                debug_log("Generated id: $hex_value");

            } while (in_array($hex_value, array_keys($records)));

            //Return the last 5 chars
            return $hex_value;
        }
    }

    //Context manager for files. Pass in a callable to have it executed assuming the given file
    //NOTE: callable MUST accept the file handle as an argument
    function open_file_context_manager($file, string $mode, callable $callable)
    {
		
        //Open file
        $file = fopen($file, $mode) or die(error_get_last()["message"]);

        //Execute func
        $result = $callable($file);
//print_r($result);exit;
        //Dispose file
        fclose($file);

        //If the work function returned anything, we should return it
        return $result;
    }

    //Debug log to file
    function debug_log($message)
    {
        open_file_context_manager(
            "debug/log.txt", FILE_MODE_READWRITE_APPEND,
            function($file) use ($message) {
                //Get currdate
                $date = date("Y-m-d H:i:s");
                fwrite($file, "[$date]: $message\n");
            }
        );
    }

    
    //Internal function to add a record
    //NOTE: DOES NO VALIDATION TO CHECK IF RECORD ALREADY EXISTS
    function _create($record)
    {
        //First check if the record already exists
        open_file_context_manager(
            "data/data.csv", FILE_MODE_WRITE_APPEND,
            function($file) use ($record) {
                fputcsv(
                    $file,
                    [
                        $record->id,
                        $record->name,
                        $record->zip,
                        $record->amount,
                        $record->quantity,
                        $record->item
                    ]
                );
            }
        );
    }

    //Finds a record by id. Returns the record if found or null if not.
    function find_single_record($id)
    {
        global $records;
        return $records[$id] ?? null;
    }

    //Helper function to get all the items in the current records
    function get_all_names()
    {
        global $records;

        $items = [];

        foreach ($records as $record)
        {
            if (!in_array($record->item, $items))
                { $items[] = $record->item; }
        }

        return $items;
    }

    //Helper function to filter records by items name
    function filter_by_item($item)
    {
        global $records;

        $filtered_records = [];

        foreach ($records as $record)
        {
            if ($record -> item == $item)
                { $filtered_records[] = $record; }
        }

        return $filtered_records;
    }

    //CRUD WRAPPER FUNCTIONS (Frontend use only)
    //CREATE
    function create_record($id=null)
    {
        //Get the form data and validate

        //We reuse create to regen a record if we're updating, in which case we use the same id
        if ($id == null)
            { $id = TVRecord::generate_id(); }

        //$type = $_POST["tv_type"]; //From dropdown, no need to validate the string

        $postdata = file_get_contents("php://input");
			// Extract the data.
			$request = json_decode($postdata);

        $item = $request->item;
        $name = $request->name;
        $quantity = $request->quantity;
        $amount = $request->amount;
        $zip = $request->zip;
        
  
        //initialize a TVRecord
        $record = new TVRecord(
            $id,
            $item,
            $name,
            $quantity,
            $amount,
            $zip
        );

        //Checks passed, create the record in the runtime map
        _create($record);

        debug_log("Created record: " . print_r($record, true));

        //Redirect to the index page, clearing requests
        header("Refresh:0; Location: index.php");
    }

    //READ
    function get_all_records()
    {
        return open_file_context_manager(
            "data/data.csv", FILE_MODE_READ,
            function($file) {
                $data = array();

                //Dispose the first line
                fgetcsv($file);

                //Iter over the file and populate the records array
                if ($file)
                {
                    while($entries = fgetcsv($file, 1024))
                        { 
						//echo "<pre>";
						//print_r($entries);
						
						$data[$entries[0]] = new TVRecord(...$entries); }
                }
				//exit;
                return $data;
            }
        );
    }
	
	function get_json_data(){
		$josnData=get_all_records();
		$jsonObject=[];
		$array_product = array();
		$i=0;
		foreach($josnData as $val){
			$array_product [$i]["id"]= $val->id;
            $array_product [$i]["name"]= $val->name;
			$array_product [$i]["zip"]= $val->zip;
			$array_product [$i]["item"]= $val->item;
			$array_product [$i]["amount"]= $val->amount;
			$array_product [$i]["quantity"]= $val->quantity;
            $i++;
		
		}
		$json_product =  json_encode($array_product, true);
        echo($json_product);
	}

    //UPDATE
    function update_record($id)
    {
        debug_log("Updating record: $id");

        //Delete the record
        delete_record($id);

        //Create a new record with the new data
        create_record($id);
    }

    //DELETE
    function delete_record($id)
    {
         $records =get_all_records();;
		

        debug_log("Deleting record: $id");
        $jsonObject=[];
		$array_product = array();
		$i=1;
		foreach($records as $val){
			if($val->id==$id){
			$array_product [$i]["id"]= $val->id;
            $array_product [$i]["name"]= $val->name;
			$array_product [$i]["zip"]= $val->zip;
			$array_product [$i]["item"]= $val->item;
			$array_product [$i]["amount"]= $val->amount;
			$array_product [$i]["quantity"]= $val->quantity;
			}
           $i++;
		
		}
        //Get the record
        $record = $array_product!=""?$array_product:null;
 
        //If the record doesn't exist, throw an error
        if (!$record)
        {
            debug_log("Record not found.");
            generate_error("Record not found ({$record->id})");
            return;
        }

        //Pop from the runtime map
        unset($records[$id]);

        //Delete the record by rewriting the file
        open_file_context_manager(
            "data/data.csv", FILE_MODE_WRITE,
            function($file) use ($records) {
                //Add columns
                fputcsv($file, RECORD_CSV_HEADER);

                //Repopulate the file
                foreach ($records as $dataline)
                    { fputcsv($file, $dataline->to_array()); }
            }
        );
    }
?>
