<?php
//creat database fill bot 
error_reporting(E_ERROR | E_PARSE);


//mysql connect
$mysqli = new mysqli("localhost","root","toor","elibrary");
// Check connection
if ($mysqli -> connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli -> connect_error . "\n";
  exit();
}

//test githup

//loop throught folder
if ($handle = opendir("corrupted")) {
  while (false !== ($file = readdir($handle))) {
      if ('.' === $file) continue;
      if ('..' === $file) continue;

      // do something with the file
      $sql = "SELECT * FROM books WHERE `image` LIKE '%$file%'";
      $result = $mysqli->query($sql);
      
      if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
          $id = $row['id'];
          echo "$id deleted\n";
          $sql = "DELETE FROM books WHERE id = '$id'";
          $mysqli->query($sql);

          sleep(0.2);
        }
      } else {
        echo "0 results". "\n";;
      }      

  }
  closedir($handle);
}

function detect($fileName) {
  if(getimagesize($fileName) === false){
    return false;
  } 
  return true;
}


function downloadimages($id, $link, $mysqli) {
  
  try {
    $parts = explode("/", $link);
    $name = $parts[count($parts) - 1];
    $content = file_get_contents($link);
    file_put_contents('images' . '/' . $name, $content);
  
    $new_path = "assets/images/" . $name;
  
  
    if(detect('images' . '/' . $name) === true) {
      //update database
      $sql = "UPDATE books SET image='$new_path' WHERE id = '$id'";
      if ($mysqli->query($sql) === TRUE) {
          echo "$id updated successfully\n";
      } else {
        //delete the record
        echo "$id deleted\n";
        $sql = "DELETE FROM books WHERE id = '$id'";
        $mysqli->query($sql);
      }
    } else {
      //delete the record
      echo "$id deleted\n";
      $sql = "DELETE FROM books WHERE id = '$id'";
      $mysqli->query($sql);
    }
  } catch (Exception $e) {
    echo "$id failed: " . $e->getMessage() . "\n";
    //delete the record
    $sql = "DELETE FROM books WHERE id = '$id'";
    $mysqli->query($sql);
  }

}

function submitData($id, $mysqli) {
    $url = 'https://api2.isbndb.com/book/'. $id; 
    $restKey = '45908_42f1306a2ee4ad619f34afebb641e9be';
    
    $headers = array(  
      "Content-Type: application/json",  
      "Authorization: " . $restKey  
    ); 
    
    try {
        $rest = curl_init();  
        curl_setopt($rest,CURLOPT_URL,$url);  
        curl_setopt($rest,CURLOPT_HTTPHEADER,$headers);  
        curl_setopt($rest,CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($rest);
        $book_data = json_decode($response, true);

        if(isset($book_data["book"]["subjects"], $book_data["book"]["authors"], $book_data["book"]["language"], $book_data["book"]["pages"])) {
            

            //TODO:Replace title with full title if exist
            
            $subject = $book_data["book"]["subjects"][0];
            $title = $book_data["book"]["title"] ?? $book_data["book"]["title_long"];
            $author = $book_data["book"]["authors"][0];
            $language = $book_data["book"]["language"];
            $page = $book_data["book"]["pages"];
            $subject =str_replace("'","\'", $subject);
            $title =str_replace("'","\'", $title);
            $author =str_replace("'","\'", $author);

            // echo $subject . "\n";
            // echo $title . "\n";
            // echo $author . "\n";
            // echo $language . "\n";
            // echo $page . "\n";


            //update database
            $sql = "UPDATE books SET title='$title',category_name='$subject',auther_name='$author',language='$language',pages=$page WHERE id = '$id'";
            if ($mysqli->query($sql) === TRUE) {
                echo "$id updated successfully\n";
            } else {
                echo "$id error updating: " . $mysqli->error . "\n";;
              //delete the record
              $sql = "DELETE FROM books WHERE id = '$id'";
              $mysqli->query($sql);
            }

        } else if(isset($book_data["message"])) {     
          if($book_data["message"] == "Forbidden"){
            echo $book_data["message"]. "\n";
            sleep(30);
          }     
            
        } else {
            echo "$id data not complete\n";
            //delete the record
            $sql = "DELETE FROM books WHERE id = '$id'";
            $mysqli->query($sql);
        }


        curl_close($rest);
    }
    catch(Exception $e) {
        echo "$id failed: " . $e->getMessage() . "\n";
        //delete the record
        $sql = "DELETE FROM books WHERE id = '$id'";
        $mysqli->query($sql);
    }
}

