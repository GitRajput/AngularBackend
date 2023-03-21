<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="description" content="Assignment 3">
        <meta name="author" content="Michael D'mello">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Backend</title>
        <link rel="stylesheet" href="css/style.css">
        <?php include "./functions.php"; ?>
    </head>
    <body>
        <?php
            //Hadle all messages here
            if (isset($_SESSION["message"]))
            {
                [$type, $message] = $_SESSION["message"];

                switch ($type)
                {
                    case "warning":
                        generate_warning($message);
                        break;
                    case "error":
                        generate_error($message);
                        break;
                }

                echo $_SESSION["message"];
                unset($_SESSION["message"]);
            }
        ?>
        <hr>
        <form method="POST">
            <fieldset>
                <legend>Create a record:</legend>
                
                <label for="name">Name:</label>
                <input type="text" name="name" id="name"/>
                <br><br>
                <label for="amount">Amount:</label>
                <input type="text" name="amount" id="amount" pattern="^\d+(\.\d+)?$" required/>
                <br><br>
                <label for="quantity">Quantity:</label>
                <input type="text" name="quantity" id="quantity" pattern="^\d+(\.\d+)?$" required/>
                <br><br>
                <label for="price">Price:</label>
                <input type="text" name="price" id="price" pattern="^\d+(\.\d+)?$" required/>
                <br><br>
                <label for="item">Item:</label>
                <input type="text" name="item" id="item"/>
                <br><br>
                <input type="submit" name="createsubmit" value="Submit"/>
            </fieldset>
        </form>
		<br>
        <hr>
        <!-- Form to search for a record by id -->
        <form method="POST">
            <fieldset>
                <legend>Search for a record:</legend>
                <label for="searchid">ID:</label>
                <input type="text" name="searchid" id="searchid" required/>
                <input type="submit" name="searchsubmit" value="Search"/>
            </fieldset>
        </form>
		<br>
        <hr>
        <!-- Form to filter records by brand -->
        <form method="POST">
            <fieldset>
                <legend>Filter records by item:</legend>
                <label for="name">Name:</label>
                <?php select("brandfilter", array_merge(["--"], get_all_names()), "--", true); ?>
                <input type="submit" name="filtersubmit" value="Filter"/>
            </fieldset>
        </form>
		<br>
        <hr>
        <!-- Add a way to download the tvs.csv file -->
        <a href="data/tvs.csv" download="tvs.csv">Download Records</a>
        <hr>
            <!-- Add a way to upload a new records csv file -->
            <form method="POST" enctype="multipart/form-data">
                <fieldset>
                    <legend>Upload a new records csv file:</legend>
                    <input type="hidden" name="MAX_FILE_SIZE" value="99999999" />
                    <input type="file" name="newrecords" accept=".csv" required/>
                    <input type="submit" name="uploadsubmit" value="Upload"/>
                </fieldset>
            </form>
			<br>
        <hr>
        <table>
            <tr>
                <th>#</th>
                <th>Id</th>
                <th>Name</th>
                <th>Zip</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Item</th>                
                <th>Action</th>
            </tr>
            <?php
			
			
                //Loop over the values via for loop
                for ($i = 0; $i < count($records); $i++) {
                    echo "<tr>";
                    echo "<td>".($i+1)."</td>";
                    echo array_values($records)[$i] -> __toString();
                    echo "<td>";
                    add_update_button(array_values($records)[$i] -> id);
                    add_delete_button(array_values($records)[$i] -> id);
                    echo "</td>";
                    echo "</tr>";
                }
            ?>
        </table>
       
        
       
    </body>
</html>
