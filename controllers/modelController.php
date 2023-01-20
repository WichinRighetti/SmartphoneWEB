<?php
    //allow access from ouside the server
    header('Access-Control-Allow-Origin: *');
    //Allow methods
    header("Access-Control-Method: GET, POST, PUT, DELETE");

    require_once($_SERVER['DOCUMENT_ROOT'].'/Smartphones/models/model.php');

    //GET (read)
    if($_SERVER['REQUEST_METHOD'] == 'GET'){
        //parameters 
        if(isset($_GET['Id'])){
            try{
                $m = new Model($_GET['Id']);
                //display
                echo json_encode(array(
                    'status' => 0,
                    'Model' => json_decode($m->toJson())
                ));
            }catch(RecordNotFoundException $ex){
                echo json_encode(array(
                    'status' => 1,
                    'errorMessage' => $ex->get_message()
                ));
            }
        }else{
            //display 
            echo json_encode(array(
                'status' => 0,
                'Model' => json_decode(Model::getAllByJson())
            ));
        }  
    } 

   //POST (insert)
   if($_SERVER['REQUEST_METHOD'] == 'POST'){
    //check parameters
    if(isset($_POST['name']) && isset($_POST['brandId'])){
        //error
        $error = false;
        //brand id
        try{
            $b = new Brand($_POST['brandId']);
        }catch(RecordNotFoundException $ex){
            echo json_encode(array(
                'status' => 2,
                'errorMessage' => 'Brand Id not found'
            ));
            $error = true;
        }
        //insert model
        if(!$error){
            //create an empty obect
            $m = new Model();
            //set values
            $m->setName($_POST['name']);
            $m->setBrand($b);
            //add
            if($m->add()){
                echo json_encode(array(
                    'status' => 0,
                    'message' => 'Car model added successfully'
                ));
            }else{
                echo json_encode(array(
                    'status' => 3,
                    'message' => 'Could not add car model'
                ));
            }
        }
    }
}
?>