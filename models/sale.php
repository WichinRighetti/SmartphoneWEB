<?php
    //use files
    require_once('mysqlConnection.php');
    require_once('device.php');
    require_once('exceptions/recordNotFoundException.php');

    //class name
    class Sale{
        //attributes
        private $id;
        private $dateTime;
        private $device;
        private $quantity;
        private $total;
        private $status;

        //setters and getters
        public function getId(){ return $this->id; }
        public function setId($value){ $this->id = $value; }
        public function getDateTime(){ return $this->dateTime; }
        public function setDateTime($value){ $this->dateTime = $value; }
        public function getDevice(){ return $this->device; }
        public function setDevice($value){ $this->device = $value; }
        public function getQuantity(){ return $this->quantity; }
        public function setQuantity($value){ $this->quantity = $value; }
        public function getTotal(){ return $this->total; }
        public function setTotal($value){ $this->total = $value; }
        public function getStatus(){ return $this->status; }
        public function setStatus($value){ $this->status = $value; }
        
        //constructors
        public function __construct(){
            //empty constructor
            if(func_num_args() == 0){
                $this->id = 0;
                $this->dateTime = date('Y-m-d H:i:s');
                $this->device = new Device();
                $this->quantity = 0;
                $this->total = 0;
                $this->status = 0;
            }
            //constructor with data from database
            if(func_num_args() == 1){
                //get id
                $id = func_get_arg(0);
                //get connection
                $connection = MysqlConnection::getConnection();
                //query
                $query = "Select s.Id, s.DateTime, s.DeviceId, d.ModelId, m.Name, m.BrandId, b.Name, b.OS, b.Status,
                m.Battery, m.Storage, m.DisplaySize, m.Chip, m.Megapixeles, m.Biometrics, m.Comments, m.Image, 
                m.Status, d.UnitPrice, d.Stock, d.Status, s.Quantity, s.Total, s.Status From Sale s 
                JOIN Device d ON s.DeviceId = d.Id JOIN Model m ON d.ModelId = m.Id JOIN Brand b ON m.BrandId = b.Id Where d.Id = ?;";
                //command
                $command = $connection->prepare($query);
                //bind parameter
                $command->bind_param('i', $id);
                //execute
                $command->execute();
                //bind results
                $command->bind_result($id, $dateTime, $deviceId, $modelId, $modelName, $brandId, $brandName, $brandOS, $brandStatus, 
                $modelBattery, $modelStorage, $modelDisplaySize, $modelChip, $modelMegapixeles, $modeBioMetrics,
                $modelComments, $modelImage, $modelStatus, $deviceUnitPrice, $deviceStock, $deviceStatus, $quantity, $total, $status);
                //record was found
                if($command->fetch()){
                    //pass values to attributes
                    $this->id = $id;
                    $this->dateTime = $dateTime;
                    $brand = new Brand($brandId, $brandName, $brandOS, $brandStatus);
                    $model = new Model($modelId, $modelName, $brand , $modelBattery, $modelStorage, $modelDisplaySize, $modelChip, $modelMegapixeles, $modeBioMetrics,
                    $modelComments, $modelImage, $modelStatus);
                    $this->device = new Device($deviceId, $model, $deviceUnitPrice, $deviceStock, $deviceStatus);
                    $this->quantity = $quantity;
                    $this->total = $total;
                    $this->status = $status;
                }else{
                    //throw exception if record not found
                    throw new RecordNotFoundException($id);
                }
                //close command
                mysqli_stmt_close($command);
                //close connection
                $connection->close();
            }
            //constructor with data from arguments
            if(func_num_args() == 6){
                //get arguments
                $arguments = func_get_args();
                //pass arguments to attributes
                $this->id = $arguments[0];
                $this->dateTime = $arguments[1];
                $this->device = $arguments[2];
                $this->quantity = $arguments[3];
                $this->total = $arguments[4];
                $this->status = $arguments[5];
            }
        }

        //represent the object in JSON format
        public function toJson(){
            return json_encode(array(
                'Id' => $this->id,
                'DateTime' => $this->dateTime,
                'device' => json_decode($this->device->toJson()),
                'Quantity' => $this->quantity,
                'Total' => $this->total,
                'Status' => $this->status
            ));
        }

        //get all
        public static function getAll(){
            //list
            $list = array();
            //get connection
            $connection = MysqlConnection::getConnection();
            //query
            $query = "Select s.Id, s.DateTime, s.DeviceId, d.ModelId, m.Name, m.BrandId, b.Name, b.OS, b.Status,
            m.Battery, m.Storage, m.DisplaySize, m.Chip, m.Megapixeles, m.BioMetrics, m.Comments, m.Image, 
            m.Status, d.UnitPrice, d.Stock, d.Status, s.Quantity, s.Total, s.Status From Sale s 
            JOIN Device d ON s.DeviceId = d.Id JOIN Model m ON d.ModelId = m.Id JOIN Brand b ON m.BrandId = b.Id order by s.DateTime;";
            //command
            $command = $connection->prepare($query);
            //execute
            $command->execute();
            //bind results
            $command->bind_result($id, $dateTime, $deviceId, $modelId, $modelName, $brandId, $brandName, $brandOS, $brandStatus, 
            $modelBattery, $modelStorage, $modelDisplaySize, $modelChip, $modelMegapixeles, $modelBioMetrics,
            $modelComments, $modelImage, $modelStatus, $deviceUnitPrice, $deviceStock, $deviceStatus, $quantity, $total, $status);
            //fetch data
            while($command->fetch()){
                $b = new Brand($brandId, $brandName, $brandOS, $brandStatus);
                $m = new Model($modelId, $modelName, $b, $modelBattery, $modelStorage, $modelDisplaySize, $modelChip, $modelMegapixeles, $modelBioMetrics, $modelComments, $modelImage, $modelStatus);
                $d = new Device($deviceId, $m, $deviceUnitPrice, $deviceStock, $deviceStatus);
                array_push($list, new Sale($id, $dateTime, $d, $quantity, $total, $status));
            }
            //close command
            mysqli_stmt_close($command);
            //close connection
            $connection->close();
            //return list
            return $list;
        }

        //get all json
        public static function getAllByJson(){
            //list
            $list = array();
            //get all
            foreach(self::getAll() as $item){
                array_push($list, json_decode($item->toJson()));
            }
            //return list
            return json_encode($list);
        }

        public static function spBuyPhone($deviceId, $quantity){
            $connection = MysqlConnection::getConnection();
            if($connection){
                $query = "Call spBuyPhone('$deviceId', '$quantity', @result);
                Select @result";
                //prepare status 
                $dataSet = $connection->multi_query($query);
                if($dataSet){
                    do{
                        if($result = $connection->store_result()){
                            while($row = $result->fetch_row()){
                                foreach($row as $cell){
                                    $procedureResult = $cell;
                                }
                            }
                        }
                    }
                    while($connection->next_result());
                }
                $connection->close();
            }
            //return result from SP
            return $procedureResult;
       }
    }
?>