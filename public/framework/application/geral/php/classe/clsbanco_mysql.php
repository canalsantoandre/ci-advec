<?php
	function retorno ($erro, $mensagem, $pagina, $tela, $objeto_retorno=null){
	
		$arr = array(
		  'mensagem'=>$mensagem,
		  'erro'=>$erro,
		  'pagina'=>$pagina,
		  'tela'=>$tela,
		  'objeto_retorno'=>$objeto_retorno
		);
		return json_encode($arr);
	}	
	
	class clsBanco {
		var $db;
		var $query;
			
		public function _connect()
		{		
			$this->db = mysql_connect(DB_HOST, DB_USERNAME, DB_PASSWORD);
			mysql_query("SET names UTF8", $this->db);
			if (!$this->db) {
				echo "Erro na conexão!";
			}
			if (!mysql_select_db(DB_DATABASE)) {
				echo "Erro na seleção do banco de dados!";
			}
		}
		
		public function _close()
		{
			mysql_close($this->db);
		}

		public function _query($sql)
		{
			$this->query = mysql_query($sql, $this->db);
			return $this->query;
		}
		
		public function _num_rows()
		{
			if ($this->query){
				return mysql_num_rows($this->query);
			}else{
				return 0;
			}
		}
		
		public function _mysql_insert_id(){
			return mysql_insert_id();
		}
		
		public function _affected_rows()
		{
			return mysql_affected_rows();
		}
		
		public function _insert_id()
		{
			return mysql_insert_id();
		}
		
		
		public function _fetch_array (){
			if ($this->query){
				return mysql_fetch_array($this->query, MYSQL_ASSOC);
			}
		}
		
		
		public function existe_registro($entidade, $clausula){

			$SQL = "SELECT COUNT(*) as QTD FROM ".$entidade." WHERE ".$clausula;						
			$this->_query($SQL);
			$row = $this->_fetch_array();					
			return $row["QTD"];
		}
		
		function _date_formatYYYYmmdd($pData){
			$vData = $this->_extrair_numero($pData);
			return ($vData==""?"":substr($vData,4,4)."/".substr($vData,2,2)."/".substr($vData,0,2));
		}
		
		function _extrair_numero($valor){

			$i=0;
			$cr="";
			$numero="";
			for ($i = 0; $i < strlen($valor); $i++) {
					$cr = substr($valor,$i, 1);
					if (($cr >= "0" && $cr <= "9")) {
							$numero .= $cr;
					}
			}

			return $numero;
		}
	}
?>