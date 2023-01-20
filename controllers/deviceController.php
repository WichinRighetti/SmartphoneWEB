<?php
    //allow access control from outside the server 
    header('Access-Control-Allow-Origin: *');
    //allow methods
    header('Access-Control-Methods: GET, POST, PUT, DELETE');

    require_once($_SERVER['DOCUMENT_ROOT'].'/Smartphones/models/device.php');

    //GET (read)
    if($_SERVER['REQUEST_METHOD']=='GET'){
        //parameters 
        if(isset($_GET['Id'])){
            try{
                $d = new Device($_GET['Id']);
                // display 
                echo json_encode(array(
                    'status' => 0,
                    'device' => json_decode($d->toJson())
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
                'device' => json_decode(Device::getAllByJson())
            ));
        }
    }

?>