<?php
    //allow access control from outside the server 
    header('Access-Control-Allow-Origin: *');
    //allow methods
    header('Access-Control-Methods: GET, POST, PUT, DELETE');

    require_once($_SERVER['DOCUMENT_ROOT'].'/Smartphones/models/brand.php');

    //GET (read)
    if($_SERVER['REQUEST_METHOD']=='GET'){
        //parameters 
        if(isset($_GET['Id'])){
            try{
                $b = new Brand($_GET['Id']);
                // display 
                echo json_encode(array(
                    'status' => 0,
                    'brand' => json_decode($b->toJson())
                ));
            }catch(recordNotFoundException $ex){
                echo json_encode(array(
                    'status' => 1,
                    'errormessage' => $ex->get_message()
                ));
            }
        }else{
            //display 
            echo json_encode(array(
                'status' => 0,
                'brand' => json_decode(Brand::getAllByJson())
            ));
        }
    }

    if($_SERVER['REQUEST_METHOD']=='POST'){
        if(isset($_POST['Name'])){
            //create an empty object
            $b = new Brand();
            //set values 
            $b->setName($_POST['Name']);
            //add values
            if($b->add()){
                echo json_encode(array(
                    'status' => 0,
                    'message' => 'Brand added succesfully'
                ));
            }else{
                echo json_encode(array(
                    'status' => 3,
                    'message' => 'Could not add brand'
                ));
            }
        }
    }

?>