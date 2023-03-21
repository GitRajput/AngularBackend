<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Assignment 3">
    <meta name="author" content="Michael D'mello">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment 3 - Info</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include "functions.php"; ?>
    <!-- Table head for all the properties of the TVRecord object -->
    <table>
        <?php
            $record = find_single_record($_GET["id"]);

            if ($record)
            {
                $out = "<tr><th>Id</th><th>Name</th><th>Item</th><th>Quantity</th><th>Amount</th><th>Zip</th></tr>";
                $out .= "<tr><td>{$record->id}</td><td>{$record->name}</td><td>{$record->item}</td><td>{$record->quantity}</td><td>{$record->amount}</td><td>{$record->zip}</td>";
                $out .= "</tr>";
                echo $out;
            }
            else
                { generate_error("Record not found"); }
        ?>
    </table>

    <?php
        $prevpage = $_SERVER["HTTP_REFERER"];
        echo "<a href=\"$prevpage\">Back</a>";
    ?>
    
</body>
</html>
