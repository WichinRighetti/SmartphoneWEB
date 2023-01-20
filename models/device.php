<?php
    //use files
    require_once('mysqlConnection.php');
    require_once('model.php');
    require_once('exceptions/recordNotFoundException.php');

    //class name
    class Device{
        //attributes
        private $id;
        private $model;
        private $unitPrice;
        private $stock;
        private $status;

        //setters and getters
        public function getId(){ return $this->id; }
        public function setId($value){ $this->id = $value; }
        public function getModel(){ return $this->model; }
        public function setModel($value){ $this->model = $value; }
        public function getUnitPrice(){ return $this->unitPrice; }
        public function setUnitPrice($value){ $this->unitPrice = $value; }
        public function getStock(){ return $this->stock; }
        public function setStock($value){ $this->stock = $value; }
        public function getStatus(){ return $this->status; }
        public function setStatus($value){ $this->status = $value; }
        
        //constructors
        public function __construct(){
            //empty constructor
            if(func_num_args() == 0){
                $this->id = 0;
                $this->model = new Model();
                $this->unitPrice = 0;
                $this->stock = 0;
                $this->status = 0;
            }
            //constructor with data from database
            if(func_num_args() == 1){
                //get id
                $id = func_get_arg(0);
                //get connection
                $connection = MysqlConnection::getConnection();
                //query
                $query = "Select d.Id, d.ModelId, m.Name, m.BrandId, b.Name, b.OS, b.Status,
                m.Battery, m.Storage, m.DisplaySize, m.Chip, m.Megapixeles,
                m.Biometrics, m.Comments, m.Image, m.Status, d.UnitPrice, d.Stock, d.Status From Device d 
                JOIN Model m ON d.ModelId = m.Id JOIN Brand b ON m.BrandId = b.Id Where d.Id = ?;";
                //command
                $command = $connection->prepare($query);
                //bind parameter
                $command->bind_param('i', $id);
                //execute
                $command->execute();
                //bind results
                $command->bind_result($id, $modelId, $modelName, $brandId, $brandName, $brandOS, $brandStatus, 
                $modelBattery, $modelStorage, $modelDisplaySize, $modelChip, $modelMegapixeles, $modelBioMetrics,
                $modelComments, $modelImage, $modelStatus, $unitPrice, $stock, $status);
                //record was found
                if($command->fetch()){
                    //pass values to attributes
                    $this->id = $id;
                    $brand = new Brand($brandId, $brandName, $brandOS, $brandStatus);
                    $this->model = new Model($modelId, $modelName, $brand , $modelBattery, $modelStorage, $modelDisplaySize, $modelChip, $modelMegapixeles, $modelBioMetrics,
                    $modelComments, $modelImage, $modelStatus);
                    $this->unitPrice = $unitPrice;
                    $this->stock = $stock;
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
            if(func_num_args() == 5){
                //get arguments
                $arguments = func_get_args();
                //pass arguments to attributes
                $this->id = $arguments[0];
                $this->model = $arguments[1];
                $this->unitPrice = $arguments[2];
                $this->stock = $arguments[3];
                $this->status = $arguments[4];
            }
        }

        //represent the object in JSON format
        public function toJson(){
            return json_encode(array(
                'Id' => $this->id,
                'model' => json_decode($this->model->toJson()),
                'UnitPrice' => $this->unitPrice,
                'Stock' => $this->stock,
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
            $query = "Select d.Id, d.ModelId, m.Name, m.BrandId, b.Name, b.OS, b.Status,
            m.Battery, m.Storage, m.DisplaySize, m.Chip, m.Megapixeles,
            m.Biometrics, m.Comments, m.Image, m.Status, d.UnitPrice, d.Stock, d.Status From Device d 
            JOIN Model m ON d.ModelId = m.Id JOIN Brand b ON m.BrandId = b.Id order by d.ModelId;";
            //command
            $command = $connection->prepare($query);
            //execute
            $command->execute();
            //bind results
            $command->bind_result($id, $modelId, $modelName, $brandId, $brandName, $brandOS, $brandStatus, 
            $modelBattery, $modelStorage, $modelDisplaySize, $modelChip, $modelMegapixeles, $modeBioMetrics,
            $modelComments, $modelImage, $modelStatus, $unitPrice, $stock, $status);
            //fetch data
            while($command->fetch()){
                $b = new Brand($brandId, $brandName, $brandOS, $brandStatus);
                $m = new Model($modelId, $modelName, $b, $modelBattery, $modelStorage, $modelDisplaySize, $modelChip, $modelMegapixeles, $modeBioMetrics,
                $modelComments, $modelImage, $modelStatus);
                array_push($list, new Device($id, $m, $unitPrice, $stock, $status));
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
    }
?>