<?
	##########################################################################################################  
	# Limpa as informações em cache sobre arquivos
	##########################################################################################################
		clearstatcache();

	##########################################################################################################  
	# CASO NÃO EXISTA O DIRETÓRIO PADRÃO PARA UPLOAD, CRIAMOS
	##########################################################################################################	

		if(!file_exists(__DIR__.'/upload-files')){	mkdir(__DIR__.'/upload-files');	}		

	##########################################################################################################  
	# DEFINIMOS O LOCAL PARA O UPLOAD   
	##########################################################################################################
		define("UPLOAD_DIR",__DIR__.'/upload-files');
		
	##########################################################################################################  
	# FUNÇÃO QUE FORMATA O NOME DO ARQUIVO   
	##########################################################################################################
		function url_amigavel_filename($texto){
			$array1 = array("{","}","[","]","´","&",",","/"," ","á","à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç");
			$array2 = array("","","","","","e","","-","_","a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c" );
			return strtolower(str_replace( $array1, $array2, strtolower($texto)));
		}

	##########################################################################################################  
	# VERIFICAMOS SE O DIRETÓRIO EXISTE E ESTÁ EM CONDIÇÕES DE RECEBER UPLOADS   
	##########################################################################################################			
		if(is_dir(UPLOAD_DIR) && is_writable(UPLOAD_DIR)) {

			##########################################################################################################  
			# VERIFICAMOS SE EXISTE ARQUIVOS ANEXADOS  
			##########################################################################################################
			if(count($_FILES)>=1){

				##########################################################################################################  
				# MONTAMOS UM ARRAY QUE IRÁ RECEBER TODOS OS DADOS DE RETORNO  
				##########################################################################################################
				 $_RETURN_FILES = Array();

				##########################################################################################################  
				# EXTENSÕES PERMITIDAS  
				##########################################################################################################
				 $_EXTENSION_ALLOWED = Array("jpg","png","doc","pdf");

				##########################################################################################################  
				# VARREMOS OS ARQUIVOS  
				##########################################################################################################
				foreach ($_FILES as $key => $__FILE__) {

					##########################################################################################################  
					# CASO SEJA MULTIPLOS UIPLOADS  
					##########################################################################################################
						if(is_array($__FILE__['name'])){
							for ($i=0; $i < count($__FILE__['name']); $i++) { 
								$tmp_name 	= $__FILE__["tmp_name"][$i];
								$size 		= $__FILE__["size"][$i];
								$type		= $__FILE__["type"][$i];
								$nome 		= url_amigavel_filename($__FILE__["name"][$i]);
								$ext		= strtolower(substr($nome,(strripos($nome,'.')+1)));
								$ext		= str_replace(array("jpeg"),array("jpg"),$ext);
								if(!in_array($ext, $_EXTENSION_ALLOWED)) {
									echo json_encode(array('status'=>'falha','response'=>'Formato ilegal:"'.$ext.'"', 'error'=>'move_uploaded_file','linha'=>__LINE__)); 
									exit;
								}
								##########################################################################################################  
								# Retorna TRUE se o arquivo com o nome filename foi enviado por POST HTTP  
								# Isto é útil para ter certeza que um usuário malicioso não está tentando levar o script a trabalhar 
								# em arquivos que não deve estar trabalhando --- por exemplo, /etc/passwd.
								##########################################################################################################
								if(is_uploaded_file($tmp_name)){

									##########################################################################################################  
									# MOVEMOS O ARQUIVO PARA O SERVIDOR
									##########################################################################################################  
								

									$token 	= md5(uniqid(rand(), true));


								 	if(move_uploaded_file($tmp_name ,UPLOAD_DIR.'/'.$token.'.'.$ext)){

										##########################################################################################################  
								 		# GUARDAMOS AS VARIÁVEIS DO ARQUIVO UPADO NA ARRAY
										##########################################################################################################  
										$_RETURN_FILES[] = array(
											'status'=>'sucesso',
											'response'=>'Upload efetuado com sucesso!',
											'error'=>0,
											'file'=>array(
												'size'		=>$size,
												'type'		=>$type,
												'name'		=>$nome,
												'newName'	=>$token.'.'.$ext,
												'ext'		=>$ext,
												'token'		=>$token
											)
										);
									 }else{
										$_RETURN_FILES[] = json_encode(array('status'=>'falha','response'=>'Esse arquivo não pode ser upado', 'error'=>'move_uploaded_file','linha'=>__LINE__));
									}					
									
								}else{
									$_RETURN_FILES[] = json_encode(array('status'=>'falha','response'=>'Esse arquivo não pode ser upado','arquivo'=>$tmp_name, 'error'=>'is_uploaded_file','linha'=>__LINE__));
								}
							}
						}else{
					##########################################################################################################  
					# CASO SEJA UM ÚNICO UPLOAD  
					##########################################################################################################

			        	$tmp_name 	= $__FILE__["tmp_name"];
			        	$size 		= $__FILE__["size"];
			        	$type		= $__FILE__["type"];
						$nome 		= url_amigavel_filename($__FILE__["name"]);
						$ext		= strtolower(substr($nome,(strripos($nome,'.')+1)));
						$ext		= str_replace(array("jpeg"),array("jpg"),$ext);

						##########################################################################################################  
						# VERIFICA   
						##########################################################################################################
						if(!in_array($ext, $_EXTENSION_ALLOWED)) {
							echo json_encode(array('status'=>'falha','response'=>'Formato ilegal:"'.$ext.'"', 'error'=>'move_uploaded_file','linha'=>__LINE__)); 
							exit;
						}
						##########################################################################################################  
						# "is_uploaded_file" Retorna TRUE se o arquivo com o nome filename foi enviado por POST HTTP  
						# Isto é útil para ter certeza que um usuário malicioso não está tentando levar o script a trabalhar 
						# em arquivos que não deve estar trabalhando --- por exemplo, /etc/passwd.
						##########################################################################################################
						if(is_uploaded_file($tmp_name)){
							
							$token 		= md5(uniqid(rand(), true));
							
							if(move_uploaded_file( $tmp_name ,UPLOAD_DIR.'/'.$token.'.'.$ext)){
								##########################################################################################################  
						 		# GUARDAMOS AS VARIÁVEIS DO ARQUIVO UPADO NA ARRAY
								##########################################################################################################
								$_RETURN_FILES[] = array(
									'status'=>'sucesso',
									'response'=>'Upload efetuado com sucesso!',
									'error'=>0,
									'file'=>array(
										'size'		=>$size,
										'type'		=>$type,
										'name'		=>$nome,
										'newName'	=>$token.'.'.$ext,
										'ext'		=>$ext,
										'token'		=>$token
									)
								);
							}else{
								$_RETURN_FILES[] = json_encode(array('status'=>'falha','response'=>'Esse arquivo não pode ser upado', 'error'=>'move_uploaded_file','linha'=>__LINE__));
							}
						}else{
							$_RETURN_FILES[] = json_encode(array('status'=>'falha','response'=>'Esse arquivo não pode ser upado', 'error'=>'is_uploaded_file','linha'=>__LINE__));
						}
					}
				}
			}else{
				$_RETURN_FILES[] = json_encode(array('status'=>'falha','response'=>'Não existem arquivos anexados', 'error'=>'UPLOAD_ERR_NO_FILES','linha'=>__LINE__));
			}
		}else{
			$_RETURN_FILES[] = json_encode(array('status'=>'falha','response'=>'Diretório inexistente ou não permite esta ação', 'error'=>'is not writable (is_writable)','linha'=>__LINE__));
		}




##########################################################################################################  
# AQUI EU PRINTEI NA TELA APENAS PARA SER VISUALIZADO NO CONSOLE LOG DO HTML
##########################################################################################################

	print_r($_POST);
	print_r($_RETURN_FILES);
	exit;



/*##########################################################################################################  
*
*
*
*
*
*	DEPOIS DE EFETUADO O UPLOAD DOS ARQUIVOS, VAMOS TRATAR OS DADOS DA BASE
*	AQUI PODEMOS PROCESSAR OS DADOS ENVIADOS VIA POST TAMBÉM
*
*
*	AGORA VARREMOS OS ARQUIVOS ANEXADOS PARA POSSIVELMENTE GRAVAR NA BASE
*
*  
##########################################################################################################*/
	foreach($_RETURN_FILES AS $FILE){
		// aqui estão os dados disponíveis da variavel $_RETURN_FILES;
		uma_funcao_qualquer_de_insert_na_base_de_dados(
			$FILE['file']['size'],
			$FILE['file']['type'],
			$FILE['file']['name'],
			$FILE['file']['newName'],
			$FILE['file']['ext'],
			$FILE['file']['token'],
			$_POST['minha_variável_do_formulario'],
			$_POST['minha_variável_do_formulario'],
			$_POST['minha_variável_do_formulario']
		);
	}