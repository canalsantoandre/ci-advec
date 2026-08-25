<?php

$htmlAdmin = "<html>
<head>

<meta name=\"viewport\" content=\"width=device-width\" initial-scale=\"1.0\" user-scalable=\"yes\" />
    <title></title>
<style>
	
body {
  margin: 0;
  padding: 0;
}


a:link, a:visited {
	text-decoration: none
	}
a:hover {
	text-decoration: underline; 
	color: #f00
	}
a:active {
	text-decoration: none
	}


body, table, td, p, a, li {
  -webkit-text-size-adjust: 100%;
  -ms-text-size-adjust: 100%;
}

a {
  word-wrap: break-word;
}

table td {
  border-collapse: collapse;
}

table {
  border-spacing: 0;
  border-collapse: collapse;
  mso-table-lspace: 0pt;
  mso-table-rspace: 0pt;
}

table, td {
  mso-table-lspace: 0pt;
  mso-table-rspace: 0pt;
}

.ReadMsgBody { 
	width:100%; 
	background-color: #FFFFFF; 
}

.ExternalClass { 
	width: 100%; 
	background-color: #FFFFFF; 
}

.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { 
	line-height: 100%; 
}

.ExternalClass * {
	line-height: 100%;
}

@media only screen and (max-width: 640px) {

  table[class=\"main\"],td[class=\"main\"] { width:100% !important; min-width: 200px !important; }
  
  table[class=\"logo-img\"] { width:100% !important; float: none; margin-bottom: 15px;}
  table[class=\"logo-img\"] td { text-align: center !important;}
  
  table[class=\"logo-title\"] { width:100% !important; float: none;}
  table[class=\"logo-title\"] td { text-align: center; height: auto}
  table[class=\"logo-title\"] h1 { font-size: 24px !important; }
  table[class=\"logo-title\"] h2 { font-size: 18px !important; }
  
  td[class=\"header-img\"] img { width:100% !important; height:auto !important; }

  td[class=\"title\"] { padding-left: 25px !important; padding-right: 25px !important; }
  td[class=\"title\"] h1 { font-size: 24px !important; }
  
  td[class=\"block-text\"] { padding-left: 25px !important; padding-right: 25px !important; }
  td[class=\"block-text\"] h2 { font-size: 20px !important; line-height: 170% !important; }
  td[class=\"block-text\"] p { font-size: 16px !important; line-height: 170% !important; }
  td[class=\"block-text\"] li { font-size: 16px !important; line-height: 170% !important; }
  
  td[class=\"two-columns\"] { padding-left: 25px !important; padding-right: 25px !important; }
  table[class=\"text-column\"] { width:100% !important; float: none; margin-bottom: 15px;}

  td[class=\"image-caption\"] { padding-left: 25px !important; padding-right: 25px !important; }
  table[class=\"image-caption-container\"] { width:100% !important;}
  table[class=\"image-caption-column\"] { width:100% !important; float: none; margin-bottom: 5px;}
  td[class=\"image-caption-content\"] img { width:100% !important; height:auto !important; }
  td[class=\"image-caption-content\"] h2 { font-size: 20px !important; line-height: 170% !important; }
  td[class=\"image-caption-content\"] p { font-size: 16px !important; line-height: 170% !important; }
  
  td[class=\"text\"] { width:100% !important; }
  td[class=\"text\"] p { font-size: 16px !important; line-height: 170% !important; }
  td[class=\"block-text\"] p { font-size: 16px !important; line-height: 170% !important; }
  td[class=\"text\"] h2 { font-size: 20px !important; line-height: 170% !important; }
  td[class=\"gap\"] { display:none; }
  
  td[class=\"header\"] { padding: 25px 25px 25px 25px !important; }
  td[class=\"header\"] h1 { font-size: 24px !important; }
  td[class=\"header\"] h2 { font-size: 20px !important; }
  
  td[class=\"footer\"] { padding-left: 25px !important; padding-right: 25px !important; }
  td[class=\"footer\"] p { font-size: 13px !important; }
  table[class=\"footer-side\"] { width: 100% !important; float: none !important; }
  td[class=\"footer-side\"] { text-align: center !important; }
  td[class=\"social-links\"] { text-align: center !important; }
  table[class=\"footer-social-icons\"] { float: none !important; margin: 0px auto !important; }
  td[class=\"social-icon-link\"] { padding: 0px 5px !important; }
  
  td[class=\"image\"] img { width:100% !important; height:auto !important; }
  td[class=\"image\"] { padding-left: 25px !important; padding-right: 25px !important; }
  
  td[class=\"image-full\"] img { width:100% !important; height:auto !important; }
  td[class=\"image-full\"] { padding-left: 0px !important; padding-right: 0px !important; }
  
  td[class=\"image-group\"] img { width:100% !important; height:auto !important; margin: 15px 0px 15px 0px !important; }
  td[class=\"image-group\"] { padding-left: 25px !important; padding-right: 25px !important; }
  
  table[class=\"image-in-table\"] { width:100% !important; float: none; margin-bottom: 15px;}
  table[class=\"image-in-table\"] td { width:100% !important;}
  table[class=\"image-in-table\"] img { width:100% !important; height:auto !important; }
  
  
  td[class=\"image-text\"] { padding-left: 25px !important; padding-right: 25px !important; }
  td[class=\"image-text\"] p { font-size: 16px !important; line-height: 170% !important; }
  td[class=\"block-text\"] p { font-size: 16px !important; line-height: 170% !important; }
  
  td[class=\"divider-simple\"] { padding-left: 25px !important; padding-right: 25px !important; }
  
  td[class=\"divider-full\"] { padding-left: 0px !important; padding-right: 0px !important; }
  
  td[class=\"social\"] { padding-left: 25px !important; padding-right: 25px !important; }
  
  table[class=\"preheader\"] { display:none; }
  td[class=\"preheader-gap\"] { display:none; }
  td[class=\"preheader-link\"] { display:none; }
  td[class=\"preheader-text\"] { width:100%; }
  
  td[class=\"buttons\"] { padding-left: 25px !important; padding-right: 25px !important; }
  
  table[class=\"button\"] { width:100% !important; float: none; }
  
  td[class=\"content-buttons\"] { padding-left: 25px !important; padding-right: 25px !important; }
  td[class=\"buttons-full-width\"] { padding-left: 0px !important; padding-right: 0px !important; }
  td[class=\"buttons-full-width\"] a { width:100% !important;}
  td[class=\"buttons-full-width\"] span { width:100% !important;}
  
  table[class=\"content\"] { width:100% !important; float: none !important;} 
  td[class=\"gallery-image\"] { width:100% !important; padding: 0px !important;}
  
  table[class=\"social\"] { width: 100%!important; text-align: center!important; }
  table[class=\"links\"] { width: 100%!important; }
  table[class=\"links\"] td { text-align: center!important; }
  table[class=\"footer-btn\"] { text-align: center!important; width: 100%!important; margin-bottom: 10px; }
  table[class=\"footer-btn-wrap\"] { margin-bottom: 0px; width: 100%!important; }
  
  td[class=\"head-social\"]  { width: 100%!important; text-align: center!important; padding-top: 20px; }
  td[class=\"head-logo\"]  { width: 100%!important; text-align: center!important; }
  tr[class=\"header-nav\"] { display: none; }
  
}

</style>
    
</head>

    

<div id=\"fb-root\" style=\"display:none\"></div>



<body dir=\"ltr\" style=\"padding: 0 0 0 0; margin: 0; -webkit-font-smoothing:antialiased; -webkit-text-size-adjust:none; background: #FFFFFF; \" bgcolor=\"#FFFFFF\" >

<!--[if t]><![endif]--><!--[if t]><![endif]--><!--[if t]><![endif]--><!--[if t]><![endif]--><!--[if t]><![endif]--><!--[if t]><![endif]--><!--[if t]><![endif]--><!--[if t]><![endif]--><!--[if t]><![endif]--><!--[if t]><![endif]-->

<!--[if !mso]><!-- -->
<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#FFFFFF\">
    <tr>
        <td width=\"100%\" class=\"main\" align=\"center\" valign=\"top\" style=\"min-width: 640px;\">
<!--<![endif]-->
       
		<!-- Content starts here-->

                       
                                                
                         <table width=\"640\" class=\"main\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\" style=\"background: #FFFFFF;\">

                            <tr>
                                <td style=\"min-width: 640px;\" class=\"main\" height=\"15\" width=\"640\">
                                    
                                    
                                 <table width=\"640\" class=\"main\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\">
        
                                    <tr>
                                        <td style=\"min-width: 640px;\" class=\"main\" height=\"15\" width=\"640\"></td>
                                    </tr>
        
                                 </table>
                                    
                                    
                                </td>
                            </tr>

                         </table>
                         
                                                 
                        
                          
           
                            <table align=\"center\" width=\"100%\" class=\"main\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">     <tr>         <td>               <table width=\"640\" class=\"main\" bgcolor=\"#FFFFFF\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\">      <tr>          <td align=\"left\" class=\"block-text\" style=\"padding: 10px 50px 10px 50px; font-family: Arial; font-size: 13px; color: #000000; line-height: 22px;\">                              <h2 style=\"line-height: 34px;        margin: 0px 0px 10px 0px;font-family: Arial; font-weight: normal; font-size: 20px; color: #00a154;\">                                                                       Contato realizado</h2>                          <p style=\"margin: 0px 0px 10px 0px;        line-height: 22px;\">
							
							<table cellpadding=\"1\" cellspacing=\"1\" border=\"0\">
							<tr>
								<td><b>Nome</b></td> <td>{nome_do_cliente}</td>
							<tr>
							<tr>
								<td><b>Email</b></td> <td>{email_do_cliente}</td>
							<tr>
							<tr>
								<td><b>Telefone</b></td> <td>{telefone_do_cliente}</td>
							<tr>
							<tr>
								<td><b>Comentario</b></td> <td>{comentario_do_cliente}</td>
							<tr>
							
							<tr>
								<td><b>Data envio</b></td> <td>{data_envio}</td>
							<tr>
							<tr>
								<td><b>IP Inscri&ccedil;&atilde;o</b></td> <td>{ip_envio}</td>
							<tr>
							</table>
							
							</p>
							</td>      </tr>  </table>                             </td>     </tr> </table>
             
                                                     
                          
           
                        
                         <table width=\"640\" class=\"main\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\" style=\"background: #FFFFFF;\">

                            <tr>
                                <td style=\"min-width: 640px;\" class=\"main\" height=\"10\" width=\"640\">
                                    
                                    
                                 <table width=\"640\" class=\"main\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\">
        
                                    <tr>
                                        <td style=\"min-width: 640px;\" class=\"main\" height=\"10\" width=\"640\"></td>
                                    </tr>
        
                                 </table>
                                    
                                    
                                </td>
                            </tr>

                         </table>
						<table align=\"center\" width=\"640\" class=\"main\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
                            
                            <tr>
                                <td style=\"min-width: 640px;\" class=\"main\" width=\"640\">
                                        <table width=\"640\" class=\"main\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\" bgcolor=\"#2c569d\"> 	
										<tr> 		
											<td class=\"footer\" style=\"padding: 10px 50px 10px 50px;\"> 		 			
												<table width=\"260\" class=\"footer-side\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" align=\"left\"> 				
												<tr> 					
												<td align=\"left\" class=\"footer-side\" style=\"padding: 0px 0px 0px 0px; vertical-align: middle; font-family: Arial; font-size: 11px; color: #FFFFFF;\"> 						   
													<p style=\"margin: 0px 0px 5px 0px;        padding: 0px;        line-height: 150%;font-family: Arial; font-weight: bold; font-size: 13px; color: #FFFFFF;\">UnicSoftware</p>                   
													<p style=\"margin: 0px 0px 5px 0px;        padding: 0px;        line-height: 150%;font-family: Arial; font-weight: bold; font-size: 13px; color: #FFFFFF;\">www.unicsoftware.com.br</p>           					
												</td> 				
												</tr> 			
												</table> 			 			
												<table width=\"260\" class=\"footer-side\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" align=\"right\"> 	            
												<tr> 	                
													<td height=\"4\" width=\"260\" class=\"footer-side\"></td> 	            
												</tr>			 				 				
												<tr> 				    
													<td align=\"right\" class=\"footer-side\" style=\"padding: 0px 0px 0px 0px; font-family: Arial; font-size: 11px; color: #FFFFFF;\">                                                                
													<td>
														<img src=\"http://www.unicsoftware.com.br/usie/logos/logo-2017-150px.png\" border=0>
													</td>
													</td> 				
												</tr> 			
												</table> 			 		
												</td> 	
												</tr> 
											</table> 
                                </td>								
                            </tr>

                        </table>

                        
		<!-- Content ends here-->

<!--[if !mso]><!-- -->
        </td>
    </tr>
</table>
<!--<![endif]-->

<script type=\"text/javascript\">window.NREUM||(NREUM={});NREUM.info={\"beacon\":\"beacon-4.newrelic.com\",\"licenseKey\":\"d31d7cf9d8\",\"applicationID\":\"6779719\",\"transactionName\":\"YVYBZEJXDBdZABBbDFgcIEVDQg0JFyYJUwpaHEk=\",\"queueTime\":0,\"applicationTime\":212,\"atts\":\"TRECEgpNHxk=\",\"errorBeacon\":\"bam.nr-data.net\",\"agent\":\"js-agent.newrelic.com\/nr-515.min.js\"}</script></body>
</html>";
if (isset($_REQUEST["print"])){
	echo $htmlAdmin;
}


?>