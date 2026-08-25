<?php

	class clsEnviaEmail {
	
		private $_nome_destinatario;
		private $_email_destinatario;
		private $_mensagem ;
		private $_assunto;
	
		function __construct ( $nome, $email , $assunto, $mensagem, $attachment=null){				
				$this->_nome_destinatario	= $nome;
				$this->_email_destinatario  = $email;
				$this->_mensagem 			= $mensagem;
				$this->_assunto				= $assunto;
		}
		
		public function envia_email(){
		
			include_once("class.phpmailer.php");
			$usuario 	= "contato@admsbc.com.br";
			
			$nomeDestinatario = $this->_nome_destinatario;			
			$To 		= $this->_email_destinatario;
			$Subject	= $this->_assunto; 
			$Message	= $this->_mensagem;
						
			$Host = 'smtp.admsbc.com.br'; //'200.147.36.31';
			$Username = 'contato@admsbc.com.br';
			$Password = 'AdMsbc@0330';
			$Port = "587";

			$mail = new PHPMailer();
			$body = $Message;
			$mail->IsSMTP(); // telling the class to use SMTP
			$mail->Host = $Host; // SMTP server
			$mail->SMTPDebug = 0; // enables SMTP debug information (for testing)
			// 1 = errors and messages
			// 2 = messages only
			$mail->SMTPAuth = true; // enable SMTP authentication
			$mail->Port = $Port; // set the SMTP port for the service server
			$mail->Username = $Username; // account username
			$mail->Password = $Password; // account password
			$mail->SetFrom($usuario, "Portal ADMSBC");
			$mail->SetReplyTo = $usuario ;
			$mail->Subject = $Subject;
			$mail->MsgHTML($body);			
			$mail->AddAddress($To,$nomeDestinatario );
			
			if(!$mail->Send()) {
				$mensagemRetorno = 'Erro ao enviar e-mail: '. print($mail->ErrorInfo);
			} else {
				$mensagemRetorno = 'E-mail enviado com sucesso!';
			}
			//echo $mensagemRetorno;
		}
				
	}
?>