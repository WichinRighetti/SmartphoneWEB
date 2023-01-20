<?php
    //use files
    require_once('mysqlConnection.php');
    require_once('brand.php');
    require_once('exceptions/recordNotFoundException.php');
    //class name
    class Model{
        //attributes
        private $id;
        private $name;
        private $brand;
        private $battery;
        private $storage;
        private $displaySize;
        private $chip;
        private $megapixeles;
        private $biometrics;
        private $comments;
        private $image;
        private $status;
        //setters and getters
        public function getId(){ return $this->id; }
        public function setId($value){ $this->id = $value; }
        public function getName(){ return $this->name; }
        public function setName($value){ $this->name = $value; }
        public function getBrand(){ return $this->brand; }
        public function setBrand($value){ $this->brand = $value; }
        public function getBattery(){ return $this->battery; }
        public function setBattery($value){ $this->battery = $value; }
        public function getStorage(){ return $this->storage; }
        public function setStorage($value){ $this->storage = $value; }
        public function getDisplaySize(){ return $this->displaySize; }
        public function setDisplaySize($value){ $this->displaySize = $value; }
        public function getChip(){ return $this->chip; }
        public function setChip($value){ $this->chip = $value; }
        public function getMegapixeles(){ return $this->megapixeles; }
        public function setMegapixeles($value){ $this->megapixeles = $value;; }
        public function getBiometrics(){ return $this->biometrics; }
        public function setBiometrics($value){ $this->biometrics = $value;; }
        public function getComments(){ return $this->comments; }
        public function setComments($value){ $this->comments = $value;; }
        public function getImage(){ return $this->image; }
        public function setImage($value){ $this->image = $value; }
        public function getStatus(){ return $this->status; }
        public function setStatus($value){ $this->status = $value; }

        //constructors
        public function __construct(){
            //empty constructor
            if(func_num_args() == 0){
                $this->id = 0;
                $this->name = "";
                $this->brand = new Brand();
                $this->battery = 0;
                $this->storage = 0;
                $this->displaySize = 0;
                $this->chip = "";
                $this->megapixeles = 0;
                $this->biometrics = 0;
                $this->comments = "";
                $this->image = "";
                $this->status = 0;
            }
            //constructor with data from database
            if(func_num_args() == 1){
                //get id
                $id = func_get_arg(0);
                //get connection
                $connection = MysqlConnection::getConnection();
                //query
                $query = "Select m.Id, m.Name, m.BrandId, b.Name, b.OS, b.Status, m.Battery, m.Storage, 
                m.DisplaySize, m.Chip, m.Megapixeles, m.BioMetrics, m.Comments, m.Image, 
                m.Status From Model m join Brand b on m.BrandId = b.Id Where m.Id = ?";
                //command
                $command = $connection->prepare($query);
                //bind parameter
                $command->bind_param('i', $id);
                //execute
                $command->execute();
                //bind results
                $command->bind_result($id, $name, $brandId, $brandName, $brandOS, $brandStatus, $battery, 
                $storage, $displaySize, $chip, $megapixeles, $biometrics, $comments, 
                $image, $status);
                //record was found
                if($command->fetch()){
                    //pass values to attributes
                    $this->id = $id;
                    $this->name = $name;
                    $this->brand = new Brand($brandId, $brandName, $brandOS, $brandStatus);
                    $this->battery = $battery;
                    $this->storage = $storage;
                    $this->displaySize = $displaySize;
                    $this->chip = $chip;
                    $this->megapixeles = $megapixeles;
                    $this->biometrics = $biometrics;
                    $this->comments = $comments;
                    $this->image = $image;
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
            if(func_num_args() == 12){
                //get arguments
                $arguments = func_get_args();
                //pass arguments to attributes
                $this->id = $arguments[0];
                $this->name = $arguments[1];
                $this->brand = $arguments[2];
                $this->battery = $arguments[3];
                $this->storage = $arguments[4];
                $this->displaySize = $arguments[5];
                $this->chip = $arguments[6];
                $this->megapixeles = $arguments[7];
                $this->biometrics = $arguments[8];
                $this->comments = $arguments[9];
                $this->image = $arguments[10];
                $this->status = $arguments[11];
            }
        }

        //represent the object in JSON format
        public function toJson(){
            return json_encode(array(
                'Id' => $this->id,
                'Name' => $this->name,
                'Brand' => json_decode($this->brand->toJson()),
                'Battery' => $this->battery,
                'Storage' => $this->storage,
                'DisplaySize' => $this->displaySize,
                'Chip' => $this->chip,
                'Megapixeles' => $this->megapixeles,
                'Biometrics' => $this->biometrics,
                'Comments' => $this->comments,
                'Image' => $this->image,
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
            $query = "Select m.Id, m.Name, m.BrandId, b.Name, b.OS, b.Status, m.Battery, m.Storage, 
            m.DisplaySize, m.Chip, m.Megapixeles, m.BioMetrics, m.Comments, m.Image, 
            m.Status From Model m join Brand b ON m.BrandId = b.Id Order BY m.Name";
            //command
            $command = $connection->prepare($query);
            //execute
            $command->execute();
            //bind results
            $command->bind_result($id, $name, $brandId, $brandName, $brandOS, $brandStatus, $battery, 
            $storage, $displaySize, $chip, $megapixeles, $biometrics, $comments, 
            $image, $status);
            //fetch data
            while($command->fetch()){
                $b = new Brand($brandId, $brandName, $brandOS, $brandStatus);
                array_push($list, new Model($id, $name, $b ,$battery, 
                $storage, $displaySize, $chip, $megapixeles, $biometrics, $comments, 
                $image, $status));
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

        function add(){
            $connection = MysqlConnection::getConnection();
            $query = "Insert Into Model (Name) Values (?)";
            //command 
            $command = $connection->prepare($query);
            $command->bind_param('s', $this->name);
            $result = $command->execute();
            //close command
            mysqli_stmt_close($command);
            $connection->close();
            return $result;
        }
    }
?>