<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$title = $author = $genre = $status = "";
$title_err = $author_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate title
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
      $genre .= $chk1." ";  
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
    if(empty($title_err) && empty($author_err) && move_uploaded_file($tmp, $dir.$photo)){
        // Prepare a query for insert statement
        $sql = "INSERT INTO books (title, author, genre, status, date, photo) 
                VALUES (:title, :author, :genre, :status, :date, :photo)";
        $stmt = $pdo->prepare($sql);
        
        //bind parameter ke quey
        $params = array(
                ":title" => $title,
                ":author" => $author,
                ":genre" => $genre,
                ":status" => $status,
                ":date" => $dates,
                ":photo" => $photo
        );
            // Attempt to execute the prepared statement
        if($stmt->execute($params)){
            // Records created successfully. Redirect to landing page
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
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create</title>
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
                    <h2 class="mt-5">Add Books </h2>
                    <p>Please fill this form and submit to add new books to the library.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
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
                            <label>Genre</label>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="genre[]" value="Action" class="custom-control-input" id="customCheck1">
                                    <label class="custom-control-label" for="customCheck1">Action</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="genre[]" value="Adventure" class="custom-control-input" id="customCheck2">
                                    <label class="custom-control-label" for="customCheck2">Adventure</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="genre[]" value="Fantasy" class="custom-control-input" id="customCheck3">
                                    <label class="custom-control-label" for="customCheck3">Fantasy</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="genre[]" value="Autobiography" class="custom-control-input" id="customCheck4">
                                    <label class="custom-control-label" for="customCheck4">Autobiography</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="genre[]" value="Biography" class="custom-control-input" id="customCheck5">
                                    <label class="custom-control-label" for="customCheck5">Biography</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="genre[]" value="Non-fiction" class="custom-control-input" id="customCheck6">
                                    <label class="custom-control-label" for="customCheck6">Non-fiction</label>
                                </div>
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
                            <label>Date</label>
                            <br>
                            <div id="date-picker" class="md-form md-outline input-with-post-icon datepicker" inline="true">
                                <input placeholder="Select date" name="dates" type="date" id="datepicker" class="form-control">
                                <i class="fas fa-calendar input-prefix"></i>
                                </div>
                        </div>
                        <div class="form-group">
                            <label>Cover</label>
                            <div class="mb-3">
                                <input class="form-control" type="file" name="fileToUpload" id="fileToUpload">
                                </div>
                        </div>
                        <div class="form-group">
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