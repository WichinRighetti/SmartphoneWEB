<?php
    //class 
    class MysqlConnection{
        //return a MYSQL connection object
        public static function getConnection(){
            //open config file
            $configPath = $_SERVER['DOCUMENT_ROOT'].'/Smartphones/config/mysqlConnection.json';
            $configData = json_decode(file_get_contents($configPath), true);
            // check parameters
            if(isset($configData['server'])){
                $server = $configData['server'];
            }else{
                echo 'configuration error, server name not found';die;
            }

            if(isset($configData['database'])){
                $database = $configData['database'];
            }else{
                echo 'configuration error, database name not found';die;
            }

            if(isset($configData['user'])){
                $user = $configData['user'];
            }else{
                echo 'configuration error, user name not found';die;
            }

            if(isset($configData['password'])){
                $password = $configData['password'];
            }else{
                echo 'configuration error, password name not found';die;
            }

            // create connection 
            $connection = mysqli_connect($server, $user, $password, $database);
            //character set 
            $connection->set_charset('utf8');
            // check connection 
            if(!$connection){
                echo 'Could not connect to MYSQL';die;
            }
            // return connection
            return $connection;
        }   
    }
?>