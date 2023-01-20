<?php
    //use files
    require_once('mysqlConnection.php');
    require_once('exceptions/recordNotFoundException.php');

    //class name
    class Brand{
        //attributes
        private $id;
        private $name;
        private $OS;
        private $status;

        //setters and getters
        public function getId(){ return $this->id; }
        public function setId($value){ $this->id = $value; }
        public function getName(){ return $this->name; }
        public function setName($value){ $this->name = $value; }
        public function getOS(){ return $this->OS; }
        public function setOS($value){ $this->OS = $value; }
        public function getStatus(){ return $this->status; }
        public function setStatus($value){ $this->status = $value; }
        
        //constructors
        public function __construct(){
            //empty constructor
            if(func_num_args() == 0){
                $this->id = 0;
                $this->name = "";
                $this->OS = "";
                $this->status = 1;
            }
            //constructor with data from database
            if(func_num_args() == 1){
                //get id
                $id = func_get_arg(0);
                //get connection
                $connection = MysqlConnection::getConnection();
                //query
                $query = "Select Id, Name, OS, Status From Brand Where Id = ?";
                //command
                $command = $connection->prepare($query);
                //bind parameter
                $command->bind_param('i', $id);
                //execute
                $command->execute();
                //bind results
                $command->bind_result($id, $name, $OS, $status);
                //record was found
                if($command->fetch()){
                    //pass values to attributes
                    $this->id = $id;
                    $this->name = $name;
                    $this->OS = $OS;
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
            if(func_num_args() == 4){
                //get arguments
                $arguments = func_get_args();
                //pass arguments to attributes
                $this->id = $arguments[0];
                $this->name = $arguments[1];
                $this->OS = $arguments[2];
                $this->status = $arguments[3];
            }
        }

        //represent the object in JSON format
        public function toJson(){
            return json_encode(array(
                'Id' => $this->id,
                'Name' => $this->name,
                'OS' => $this->OS,
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
            $query = "Select Id, Name, OS, Status From Brand Order By Name";
            //command
            $command = $connection->prepare($query);
            //execute
            $command->execute();
            //bind results
            $command->bind_result($id, $name, $OS, $status);
            //fetch data
            while($command->fetch()){
                array_push($list, new Brand($id, $name, $OS, $status));
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
            $query = "Insert Into Brand (Name) Values (?)";
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