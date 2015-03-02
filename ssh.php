<?php

class ssh{

	public $connection = false;
	public $stream = false;
	public $errorStream = false;
    public $errno = 0;

	public function __construct($ip, $user, $pass, $port=22){
		if(!($this->connection = ssh2_connect($ip,$port)))
            $this->errno = 2;
		elseif(!ssh2_auth_password($this->connection, $user, $pass))
            $this->errno = 3;
	}

	public function exec($query = false, $functions = false){
		$this->stream = ssh2_exec($this->connection, $query);
		if($functions){
			$this->fetch();
			$this->set_blocking();
			return $this->get_contents();
		}
	}

	public function fetch(){
		$this->errorStream = ssh2_fetch_stream($this->stream, SSH2_STREAM_STDERR);
	}

	public function set_blocking(){
		stream_set_blocking($this->errorStream, true);
		stream_set_blocking($this->stream, true);
	}

	public function get_contents(){
		$errContent = stream_get_contents($this->errorStream);
		if(strlen($errContent)>0)
			return "Error: ".$errContent;
		return stream_get_contents($this->stream);
	}

	public function __destruct(){
		
		fclose($this->errorStream);
		fclose($this->stream);

	}


}

?>