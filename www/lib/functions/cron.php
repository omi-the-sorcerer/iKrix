<?
class commands
{
/*------------------------------------------------------------------------------
  Declaramos las variables
------------------------------------------------------------------------------*/
	public $m;
	public $h;
	public $monthday;
	public $month;
	public $weekday;
	public $period;
	public $command;

	public $id;
	
/*------------------------------------------------------------------------------
 recojerdatos("##cron") - sirve para recojer los valores de una tarea del cron
------------------------------------------------------------------------------*/
	public function recojerdatos($dato)
	{
		$file = file('/var/spool/cron/crontabs/www-data');
		foreach ($file as $i => $lines) 
		{
			$id = substr($lines, 2);
			if ($lines == $dato."\n") 
			{
				$linee = $file[$i+1];
				$explode = explode(" ", $linee);
				$commandslice = array_slice($explode, 5);

				$this->id = $dato;
				$this->m = $explode[0];
				$this->h = $explode[1];
				$this->monthday = $explode[2];
				$this->month = $explode[3];
				$this->weekday = $explode[4];
				foreach($commandslice as $i => $l)
				{
					if(end($commandslice) == $commandslice[$i])
						$this->command .= $commandslice[$i];
					else
						$this->command .= $commandslice[$i]." ";
				}
			}
		}
	}

	public function whiledatos()
	{
		$file = file('/var/spool/cron/crontabs/www-data');

		$array = array();

		foreach ($file as $i => $lines) 
		{
			$key = substr($lines, 0, 2);
			$id = substr($lines, 2);

			if ($key == "##") 
			{ 
				$linee = $file[$i+1];
				$explode = explode(" ", $linee);
				$commandslice = array_slice($explode, 5);

				$array["m"][] = $explode[0];
				$array["h"][] = $explode[1];
				$array["monthday"][] = $explode[2];
				$array["month"][] = $explode[3];
				$array["weekday"][] = $explode[4];
				$command = "";
				foreach($commandslice as $i => $l)
					$command .= $commandslice[$i]." ";
				
				$array["command"][] = $command;
				$array["id"][] = str_replace("\n", "", $id);
			}
		}
		return $array;
	}

	public function add($croncommand, $d1 = "")
	{
		require_once('cronadds.php');

		$id = self::RandomString();
		$linea = "##".$id."\n";
		$comando = $cronadd[$croncommand];

		$linea2 = "".rand(0,60)." ".rand(0,24)." * * * ".$comando." ".$d1."\n";

		$txt = fopen("/var/spool/cron/crontabs/www-data","a+");
		fwrite($txt, $linea);
		fwrite($txt, $linea2);
		fclose($txt);
		return $id;
	}
	
	public function edit($m, $h, $monthday, $month, $weekday, $command, $id, $dato)
	{
		$url = file('/var/spool/cron/crontabs/www-data');
		$file = file_get_contents('/var/spool/cron/crontabs/www-data');


		foreach ($url as $i => $lines) 
		{
			if ($lines == $dato."\n") 
			{
				$linee = $url[$i+1];
				$new = str_replace($linee ,$m." ".$h." ".$monthday." ".$month." ".$weekday." ".$command."\n", $file);
				

				if($id."\n" != $url[$i])
				{
					$new = str_replace($url[$i], "\n".$id."\n", $new);
					echo $id."<br>".$url[$i];
				}

				file_put_contents('/var/spool/cron/crontabs/www-data', $new);
				exec("crontab /var/spool/cron/crontabs/www-data");
				self::deletel();
			}
		}
	}


	public function delete($dato)
	{
		$url = file('/var/spool/cron/crontabs/www-data');
		$file = file_get_contents('/var/spool/cron/crontabs/www-data');
		foreach ($url as $i => $lines) 
		{
			if ($lines == $dato."\n") 
			{
				$new = str_replace($url[$i+1] ,"", $file);
				$new = str_replace($url[$i], "", $new);

				file_put_contents('/var/spool/cron/crontabs/www-data', $new);
				exec("crontab /var/spool/cron/crontabs/www-data");
				self::deletel();
			}
		}
	}

	private function deletel()
	{
		$file = file('/var/spool/cron/crontabs/www-data');
		$n = "";

		foreach ($file as $i => $l) {
			if ($i == 0) {
				$n="";
			}
			elseif ($i == 1) {
				$n="";
			}
			elseif ($i == 2) {
				$n="";
			}
			else
			{
				$n.=$file[$i];
			}
			
		}

		$w = fopen("/var/spool/cron/crontabs/www-data", "w+");
		fwrite($w, $n);
		fclose($w);
	}


	public function RandomString($length=10,$uc=TRUE,$n=TRUE,$sc=FALSE)
	{
	    $source = 'abcdefghijklmnopqrstuvwxyz';
	    if($uc==1) $source .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    if($n==1) $source .= '1234567890';
	    if($sc==1) $source .= '|@#~$%()=^*+[]{}-_';
	    if($length>0){
	        $rstr = "";
	        $source = str_split($source,1);
	        for($i=1; $i<=$length; $i++){
	            mt_srand((double)microtime() * 1000000);
	            $num = mt_rand(1,count($source));
	            $rstr .= $source[$num-1];
	        }
	 
	    }
	    return $rstr;
	}


	public function temp()
	{
	  exec('sudo /usr/bin/vcgencmd measure_temp', $output, $return_var);
	  if($return_var) return FALSE;
	  foreach($output as $line)
	    if(substr($line, 0, 5) == 'temp=')
	      return substr($line, 5, -2);
	}
}
?>