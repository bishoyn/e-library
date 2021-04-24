<?php
//creat database fill bot 


//mysql connect
$mysqli = new mysqli("localhost","root","toor","elibrary");
// Check connection
if ($mysqli -> connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli -> connect_error . "\n";
  exit();
}


$sql = "SELECT * FROM books WHERE category_name = 'UNKNOWN'";
$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    submitData($row["id"], $mysqli);
    sleep(0.2);
  }
} else {
  echo "0 results". "\n";;
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

