<?php
class RecordNotFoundException extends Exception {
    public function get_message(){
        return 'record not found';
    }
}
?>