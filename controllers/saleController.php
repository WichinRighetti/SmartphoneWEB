<?php
    //allow access control from outside the server 
    header('Access-Control-Allow-Origin: *');
    //allow methods
    header('Access-Control-Methods: GET, POST, PUT, DELETE');

    require_once($_SERVER['DOCUMENT_ROOT'].'/Smartphones/models/sale.php');

    //GET (read)
    if($_SERVER['REQUEST_METHOD']=='GET'){
        //parameters 
        if(isset($_GET['Id'])){
            try{
                $s = new Sale($_GET['Id']);
                // display 
                echo json_encode(array(
                    'status' => 0,
                    'sale' => json_decode($s->toJson())
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
                'sale' => json_decode(Sale::getAllByJson())
            ));
        }
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if(isset($_POST['inDeviceId']) && isset($_POST['inQuantity'])){
        try {
           $result = sale::spBuyPhone($_POST['inDeviceId'], $_POST['inQuantity']);
           $status = 0;
           switch($result){
            case 'SQL Error':
                $status = 999;
            break;   
            case 'Insufficent Stock, reduce quantity':
                $status = 3;
            break;
            case 'OK':
                $status = 0;
            break;
        }
        if($status == 0){
            $result = "Buyed Phone.";
        }
        //display result 
        echo json_encode(array(
            'status' => $status,
            'message' => $result
        ));
        }catch(RecordNotFoundException $ex){
            echo json_encode(array(
                'status' => 1,
                'errorMessage' => "Could not buy Phone."
            ));   
        }
        }else{
            echo json_encode(array(
                'status' => 3,
                'errorMessage' => 'Missing Parameters.'
            ));
        }
    }


?>