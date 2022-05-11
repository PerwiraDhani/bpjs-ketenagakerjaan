<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$title = $author = $genre = $status = "";
$title_err = $author_err = "";
 
// Processing form data when form is submitted
if(isset($_POST["id"]) && !empty($_POST["id"])){
    // Get hidden input value
    $id = $_POST["id"];
    
    // Validate name
    $input_title = trim($_POST["title"]);
    if(empty($input_title)){
        $title_err = "Please enter the title.";
    } elseif(!filter_var($input_title, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $title_err = "Please enter a valid title.";
    } else{
        $title = $input_title;
    }
    
    // Validate author
    $input_author = trim($_POST["author"]);
    if(empty($input_author)){
        $author_err = "Please enter the author of this book.";     
    } else{
        $author = $input_author;
    }
    
    //Validate genre
    $input_genre=$_POST['genre'];  
    foreach($input_genre as $chk1) {  
      $genre .= $chk1.", ";  
    }

    //Validate status
    $status = $_POST["status"];

    //Validate dates
    $dates = $_POST['dates'];
    $date = date('m-d-Y', strtotime($dates));

    //Validate photo
    $photo = $_FILES['fileToUpload']['name'];
    $tmp = $_FILES['fileToUpload']['tmp_name'];
    $dir = "img/";
    
    // Check input errors before inserting in database
    if(empty($title_err) && empty($author_err)){
        // Prepare an update statement
        $sql = "UPDATE books SET title=:title, author=:author, status=:status
                WHERE id=:id";
        $stmt = $pdo->prepare($sql);

        $params = array(
            ":title" => $title,
            ":author" => $author,
            ":status" => $status,
            ":id" => $id
        );
 
            
        // Attempt to execute the prepared statement
        if($stmt->execute($params)){
        // Records updated successfully. Redirect to landing page
        header("location: index.php");
            exit();
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }

         
        // Close statement
        unset($stmt);
    }
    
    // Close connection
    unset($pdo);
} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        // Get URL parameter
        $id =  trim($_GET["id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM books WHERE id = :id";
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":id", $param_id);
            
            // Set parameters
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                    // Retrieve individual field value
                    $title = $row["title"];
                    $author = $row["author"];
                    $genre = $row["genre"];
                    $status = $row["status"];
                    $dates = $row["date"];
                    $photo = $row["photo"];

                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        unset($stmt);
        
        // Close connection
        unset($pdo);
    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Update Availability Status</h2>
                    <p>Please edit the input values and submit to update the availability status.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control <?php echo (!empty($title_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $title; ?>">
                            <span class="invalid-feedback"><?php echo $title_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Author</label>
                            <input type="text" name="author" class="form-control <?php echo (!empty($author_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $author; ?>">
                            <span class="invalid-feedback"><?php echo $author_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <br>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="customRadioInline1" name="status" value="Available" class="custom-control-input">
                                    <label class="custom-control-label" for="customRadioInline1">Available</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="customRadioInline2" name="status" value="Not Available" class="custom-control-input">
                                    <label class="custom-control-label" for="customRadioInline2">Not Available</label>
                                </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                            <input type="submit" class="btn btn-primary" value="Submit">
                            <a href="index.php" class="btn btn-secondary ml-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>