<?php 

add_action( 'wp_enqueue_scripts', 'ajax_localize', 1 );
function ajax_localize(){
	wp_localize_script( 'odin-main', 'odin_main', array('ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}

function altera_cidade_func(){
	$cod_estado= $_POST['cod_estado'];

	global $wpdb;
	$results = $wpdb->get_results( 'SELECT cod_cidades, nome
			FROM '.$wpdb->prefix.'w_cidades
			WHERE estados_cod_estados='.$cod_estado.'
			ORDER BY nome', OBJECT );
	echo '<option value="0">Cidade</option>';	

	foreach ($results as $key ) {
		echo '<option value="'.$key->cod_cidades.'">'.$key->nome.'</option>';	
	}
	wp_die();

	
}
add_action( 'wp_ajax_altera_cidade', 'altera_cidade_func' );
add_action( 'wp_ajax_nopriv_altera_cidade', 'altera_cidade_func' );


function pega_sub_func(){
	$mae= $_POST['mae'];
  	if ( count( get_term_children( $mae, 'tema' ) ) !== 0 ) {
		drop_tags('SubTema', 'tema', 0, "sub-tema-continua-cadastro_".$mae, 0, $mae); 
	 }

	
	wp_die();

	
}
add_action( 'wp_ajax_pega_sub', 'pega_sub_func' );
add_action( 'wp_ajax_nopriv_pega_sub', 'pega_sub_func' );

function pega_sub_func2(){
	$mae= $_POST['mae'];
  	if ( count( get_term_children( $mae, 'tema' ) ) !== 0 ) {
		drop_tags('SubTema', 'tema', 0, "sub-tema-continua-cadastro_".$mae, 0, $mae); 
	 }


	
	wp_die();

	
}
add_action( 'wp_ajax_pega_sub2', 'pega_sub_func2' );
add_action( 'wp_ajax_nopriv_pega_sub2', 'pega_sub_func2' );
// cadastro de pratica
function cadastra_pratica_func(){
	$data= $_POST;
	if ($data['title']=="") {
		echo '<p>Preencha o campo Nome da prática</p>';
		wp_die();	
	}
	elseif ($data['uf']==0) {
		echo '<p>Preencha o campo UF</p>';
		wp_die();	
	}
	elseif ($data['login']=="") {
		echo '<p>Preencha o campo nome de usuário</p>';
		wp_die();	
	}
	elseif ($data['e-mail_de_cadastro']=="") {
		echo '<p>Preencha o campo email de cadastro</p>';
		wp_die();	
	}
	
	elseif ($data['senha']=="") {
		echo '<p>Preencha o campo senha</p>';
		wp_die();	
	}
	elseif ($data['senha']!=$data['senha-repetir']) {
		echo '<p>A confirmação é diferente da senha</p>';
		wp_die();	
	}
	
	elseif ($data['nome_da_entidade']=="") {
		echo '<p>Preencha o campo Nome da entidade ou pessoa</p>';
		wp_die();	
	}
	elseif ($data['e-mail_de_contato']=="") {
		echo '<p>Preencha o campo email de contato</p>';
		wp_die();	
	}
	elseif ($data['tax']==',0') {
		echo '<p>Preencha o campo tema</p>';
		wp_die();	
	}
	elseif ($data['resumo_da_pratica']=="") {
		echo '<p>Preencha o campo resumo</p>';
		wp_die();	
	}
	elseif ($data['objetivo']=="") {
		echo '<p>Preencha o campo objetivo</p>';
		wp_die();	
	}
	elseif ($data['publico-alvo']=="") {
		echo '<p>Preencha o campo público alvo</p>';
		wp_die();	
	}
	elseif ($data['descricao_das_acoes']=="") {
		echo '<p>Preencha o campo descrição das ações</p>';
		wp_die();	
	}
	elseif ($data['resultados']=="") {
		echo '<p>Preencha o campo resultados</p>';
		wp_die();	
	}

	$cadastra_usuario = ajax_cadastra_usuario($data['login'], $data['e-mail_de_cadastro'], $data['senha'], $data['site_da_entidade']);
	if (is_int($cadastra_usuario)){
		$pratica = array(
    		'post_title' => $data['title'],
    		'post_status' => 'draft',
    		'post_type' => 'pratica',
    		'post_author' => $cadastra_usuario
    	);
		$pratica_id =  wp_insert_post( $pratica );
		$anexos = $data['anexos'];
		$galeria = $data['galeria'];
		$anexosvetor = explode(",", $anexos);
		$galeriavetor = explode(",", $galeria);
		foreach ($galeriavetor as $id) {
			// Update post 37
 			 $my_post = array(
   				 'ID'           => $id,
     			'post_parent'   =>$pratica_id
 			 );

			// Update the post into the database
 			 wp_update_post( $my_post );
		}
		foreach ($anexosvetor as $id) {
			// Update post 37
 			 $my_post = array(
   				 'ID'           => $id,
     			'post_parent'   =>$pratica_id
 			 );

			// Update the post into the database
 			 wp_update_post( $my_post );
		}

		foreach ($data as $key => $value) {
			if ($key!=='action' && $key !== 'title' && $key!=='tax') {
				update_field($key, $value,$pratica_id );
			}

		}

		wp_set_post_terms( $pratica_id, $data['tax'], 'tema', true );
		// update_field('video',$att['url'],'https://www.youtube.com/watch?v=I38EcMJX8A8');
		if (isset($data['imagem_destacada'])){
			set_post_thumbnail( $pratica_id, $data['imagem_destacada'] ); 
		}
			echo '<h3>Obrigado, sua prática será ser analisada e publicada futuramente.</h3>';

		wp_die();

	}
	else {
		echo '<p>'.$cadastra_usuario.'</p>';
		wp_die();
	}

	print_r($data);
	
		wp_die();


}
add_action( 'wp_ajax_cadastra_pratica', 'cadastra_pratica_func' );
add_action( 'wp_ajax_nopriv_cadastra_pratica', 'cadastra_pratica_func' );
// cadastro de pratica




// cadastro de usuario
function ajax_cadastra_usuario($nome, $email, $senha, $site){
	$user_email = get_user_by( 'email', $email);
	
	if ( $user_email !== false && wp_check_password(  $senha, $user_email->data->user_pass, $user_email->ID ) ){
		 return $user_email->ID;
	wp_die();

	}
	else if (email_exists($email) !== false  || username_exists( $nome )){
		return 'E-mail ja cadastrado e senha não confere';
			wp_die();

	}
	else if ( !empty($nome) && !empty($email) && !empty($senha)) {
		$userdata = array(
 		   'user_login'  =>  $nome,
 		   'user_email'  =>  $email,
 		   'user_url'    =>  $site,
 		   'user_pass'   =>  $senha, // When creating an user, `user_pass` is expected.
 		   'role'		 =>  'entidade'
	);
	$user_id = wp_insert_user( $userdata );	

	return $user_id;
	} 
	else{
		return 'Algo não foi preenchido';
	}
	wp_die();
}
// cadastro de usuario


// add_action( 'wp_enqueue_scripts', 'ibenic_enqueue' );



add_action('wp_ajax_ibenic_file_upload', 'ibenic_file_upload' );
add_action('wp_ajax_nopriv_ibenic_file_upload', 'ibenic_file_upload' );

function ibenic_file_upload() {
	$usingUploader = 2;
	$fileErrors = array(
		0 => "There is no error, the file uploaded with success",
		1 => "The uploaded file exceeds the upload_max_files in server settings",
		2 => "The uploaded file exceeds the MAX_FILE_SIZE from html form",
		3 => "The uploaded file uploaded only partially",
		4 => "No file was uploaded",
		6 => "Missing a temporary folder",
		7 => "Failed to write file to disk",
		8 => "A PHP extension stoped file to upload" );
	$posted_data =  isset( $_POST ) ? $_POST : array();
	$file_data = isset( $_FILES ) ? $_FILES : array();
	$data = array_merge( $posted_data, $file_data );
	$response = array();
	if( $usingUploader == 1 ) {
		$uploaded_file = wp_handle_upload( $data['ibenic_file_upload'], array( 'test_form' => false ) );
		if( $uploaded_file && ! isset( $uploaded_file['error'] ) ) {
			$response['response'] = "SUCCESS";
			$response['filename'] = basename( $uploaded_file['url'] );
			$response['url'] = $uploaded_file['url'];
			$response['type'] = $uploaded_file['type'];
		} else {
			$response['response'] = "ERROR";
			$response['error'] = $uploaded_file['error'];
		}
	} elseif ( $usingUploader == 2) {
		$attachment_id = media_handle_upload( 'ibenic_file_upload', 0 );
		if ( is_wp_error( $attachment_id ) ) { 
			$response['response'] = "ERROR";
			$response['error'] = $fileErrors[ $data['ibenic_file_upload']['error'] ];
		} else {
			$fullsize_path = get_attached_file( $attachment_id );
			$pathinfo = pathinfo( $fullsize_path );
			$url = wp_get_attachment_url( $attachment_id );
			$response['response'] = "SUCCESS";
			$response['filename'] = $pathinfo['filename'];
			$response['url'] = $url;
			$response['id'] = $attachment_id;
			$type = $pathinfo['extension'];
			if( $type == "jpeg"
			|| $type == "jpg"
			|| $type == "png"
			|| $type == "gif" ) {
				$type = "image/" . $type;
			}
			$response['type'] = $type;
		}
	}
	echo json_encode( $response );
	die();
}


add_action('wp_ajax_nopriv_ibenic_file_delete', 'ibenic_file_delete');
add_action('wp_ajax_ibenic_file_delete', 'ibenic_file_delete');
function ibenic_file_delete() {
	if( isset( $_POST ) ){
		global $wpdb;
	
		$fileurl = $_POST['fileurl'];
		$response = array();
	
		$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $fileurl ));
		
		if( $attachment ){
			$attachmentID = $attachment[0];
			if ( false === wp_delete_attachment( $attachmentID ) ) {
	
				$response['response'] = "ERROR";
				$response['error'] = 'File could not be deleted';
	
			} else {
				$response['response'] = "SUCCESS";
			}
		} else {
			$filename = basename( $fileurl );
			$upload_dir = wp_upload_dir();
	    		$upload_path = $upload_dir["basedir"]."/custom/";
	    		$uploaded_file = $upload_path . $filename;
			if(file_exists($uploaded_file)){
			
				@unlink($uploaded_file);
				$response['response'] = "SUCCESS";
			
			} else {
				$response['response'] = "ERROR";
				$response['error'] = 'File does not exist';
			}
		}
		
		echo json_encode( $response );
	} 
	die();
}



// uploads
add_action('wp_ajax_cvf_upload_files', 'cvf_upload_files');
add_action('wp_ajax_nopriv_cvf_upload_files', 'cvf_upload_files'); // Allow front-end submission 

function cvf_upload_files(){
    
    $parent_post_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : 0;  // The parent ID of our attachments
    $valid_formats = array("jpg", "png", "gif", "bmp", "jpeg"); // Supported file types
    $max_file_size = 1024 * 5000; // in kb
    $max_image_upload = 10; // Define how many images can be uploaded to the current post
    $wp_upload_dir = wp_upload_dir();
    $path = $wp_upload_dir['path'] . '/';
    $count = 0;
    $response =array();
    $response['idsAnexos']='';
    // print_r($_FILES);
    $attachments = get_posts( array(
        'post_type'         => 'attachment',
        'posts_per_page'    => -1,
        'post_parent'       => $parent_post_id,
        'exclude'           => get_post_thumbnail_id() // Exclude post thumbnail to the attachment count
    ) );

    // Image upload handler
    if( $_SERVER['REQUEST_METHOD'] == "POST" ){
        if(!isset( $_FILES['files']['name'])){
    	$response['msg'] = 'nenhum arquivo selecionado';
    	
  		}
        // Check if user is trying to upload more than the allowed number of images for the current post
        else if( (count( $_FILES['files']['name'] ) ) > $max_image_upload ) {
            $upload_message[] = "Só são permitidos " . $max_image_upload . " anexos para cada prática.";
        } else {
            
            foreach ( $_FILES['files']['name'] as $f => $name ) {
                $extension = pathinfo( $name, PATHINFO_EXTENSION );
                // Generate a randon code for each file name
                // $new_filename = cvf_td_generate_random_code( 20 )  . '.' . $extension;
                $new_filename = $name;

                if ( $_FILES['files']['error'][$f] == 4 ) {
                    continue; 
                }
                
                if ( $_FILES['files']['error'][$f] == 0 ) {
                    // Check if image size is larger than the allowed file size
                    if ( $_FILES['files']['size'][$f] > $max_file_size ) {
                        $upload_message[] = "$name é muito grande!.";
                        continue;
                    
                    // Check if the file being uploaded is in the allowed file types
                    }  else{ 
                        // If no errors, upload the file...
                        if( move_uploaded_file( $_FILES["files"]["tmp_name"][$f], $path.$new_filename ) ) {
                            
                            $count++; 

                            $filename = $path.$new_filename;
                            $filetype = wp_check_filetype( basename( $filename ), null );
                            $wp_upload_dir = wp_upload_dir();
                            $attachment = array(
                                'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
                                'post_mime_type' => $filetype['type'],
                                'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
                                'post_content'   => '',
                                'post_status'    => 'inherit'
                            );
                            // Insert attachment to the database
                            $attach_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );

                            require_once( ABSPATH . 'wp-admin/includes/image.php' );
                            
                            // Generate meta data
                            $attach_data = wp_generate_attachment_metadata( $attach_id, $filename ); 
                            wp_update_attachment_metadata( $attach_id, $attach_data );
                            $response['idsAnexos'] .= $attach_id.',';
                        }
                    }
                }
            }
        }
    }
    // Loop through each error then output it to the screen
    if ( isset( $upload_message ) ) :
        foreach ( $upload_message as $msg ){        
            $response['msg']= $msg;
        }
    endif;
    
    // If no error, show success message
    if( $count != 0 ){
        $response['msg']= 'Upload feito com sucesso!';   
    }
    echo json_encode($response);
    exit();
}


// uploads
add_action('wp_ajax_cvf_upload_files_gal', 'cvf_upload_files_gal');
add_action('wp_ajax_nopriv_cvf_upload_files_gal', 'cvf_upload_files_gal'); // Allow front-end submission 

// function cvf_upload_files_gal(){
    
//     $parent_post_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : 0;  // The parent ID of our attachments
//     $valid_formats = array("jpg", "png", "gif", "bmp", "jpeg"); // Supported file types
//     $max_file_size = 1024 * 5000; // in kb
//     $max_image_upload = 10; // Define how many images can be uploaded to the current post
//     $wp_upload_dir = wp_upload_dir();
//     $path = $wp_upload_dir['path'] . '/';
//     $count = 0;
//     $response =array();
//     $response['idsAnexos']='';
//     // print_r($_FILES);
//     $attachments = get_posts( array(
//         'post_type'         => 'attachment',
//         'posts_per_page'    => -1,
//         'post_parent'       => $parent_post_id,
//         'exclude'           => get_post_thumbnail_id() // Exclude post thumbnail to the attachment count
//     ) );

//     // Image upload handler
//     if( $_SERVER['REQUEST_METHOD'] == "POST" ){
//         if(!isset( $_FILES['files']['name'])){
//     	$response['msg'] = 'nenhum arquivo selecionado';
    	
//   		}
//         // Check if user is trying to upload more than the allowed number of images for the current post
//         else if( (count( $_FILES['files']['name'] ) ) > $max_image_upload ) {
//             $upload_message[] = "Só são permitidas " . $max_image_upload . " imagens para cada prática.";
//         } else {
            
//             foreach ( $_FILES['files']['name'] as $f => $name ) {
//                 $extension = pathinfo( $name, PATHINFO_EXTENSION );
//                 // Generate a randon code for each file name
//                 $new_filename = $name;
//                 // $new_filename = cvf_td_generate_random_code( 20 )  . '.' . $extension;
                
//                 if ( $_FILES['files']['error'][$f] == 4 ) {
//                     continue; 
//                 }
                
//                 if ( $_FILES['files']['error'][$f] == 0 ) {
//                     // Check if image size is larger than the allowed file size
//                     if ( $_FILES['files']['size'][$f] > $max_file_size ) {
//                         $upload_message[] = "$name é muito grande!.";
//                         continue;
                    
//                     // Check if the file being uploaded is in the allowed file types
//                     } elseif( ! in_array( strtolower( $extension ), $valid_formats ) ){
//                         $upload_message[] = "$name não é uma imagem de formato válido";
//                         continue; 
                    
//                     } else{ 
//                         // If no errors, upload the file...
//                         if( move_uploaded_file( $_FILES["files"]["tmp_name"][$f], $path.$new_filename ) ) {
                            
//                             $count++; 

//                             $filename = $path.$new_filename;
//                             $filetype = wp_check_filetype( basename( $filename ), null );
//                             $wp_upload_dir = wp_upload_dir();
//                             $attachment = array(
//                                 'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
//                                 'post_mime_type' => $filetype['type'],
//                                 'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
//                                 'post_content'   => '',
//                                 'post_status'    => 'inherit'
//                             );
//                             // Insert attachment to the database
//                             $attach_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );

//                             require_once( ABSPATH . 'wp-admin/includes/image.php' );
                            
//                             // Generate meta data
//                             $attach_data = wp_generate_attachment_metadata( $attach_id, $filename ); 
//                             wp_update_attachment_metadata( $attach_id, $attach_data );
//                             $response['idsAnexos'] .= $attach_id.',';
//                         }
//                     }
//                 }
//             }
//         }
//     }
//     // Loop through each error then output it to the screen
//     if ( isset( $upload_message ) ) :
//         foreach ( $upload_message as $msg ){        
//             $response['msg']= $msg;
//         }
//     endif;
    
//     // If no error, show success message
//     if( $count != 0 ){
//         $response['msg']= 'Upload feito com sucesso!';   
//     }
//     echo json_encode($response);
//     exit();
// }

// Random code generator used for file names.
function cvf_td_generate_random_code($length=10) {
 
   $string = '';
   $characters = "23456789ABCDEFHJKLMNPRTVWXYZabcdefghijklmnopqrstuvwxyz";
 
   for ($p = 0; $p < $length; $p++) {
       $string .= $characters[mt_rand(0, strlen($characters)-1)];
   }
 
   return $string;
 
}

// edita_pega
add_action('wp_ajax_edita_pratica_pega', 'edita_pratica_pega_func');
add_action('wp_ajax_nopriv_edita_pratica_pega', 'edita_pratica_pega_func'); // Allow front-end submission 

function edita_pratica_pega_func(){
	$pratica = get_post($_POST['id']);
	$id=$_POST['id'];
	$resposta=array();
	$resposta['nome-projeto']=get_the_title( $pratica );
	$resposta['estado']=get_post_meta( $id, 'uf', 1 );
	$resposta['cidade']=get_post_meta( $id, 'cidade', 1 );
	$temas=wp_get_post_terms( $id, 'tema');
	$resposta['tema']=$temas[0]->term_id;
	if (!empty($temas[1])) {
		$resposta['subtema']=$temas[1]->term_id;
		if (!empty($temas[2])) {
		$resposta['subtema2']=$temas[2]->term_id;
		
		}
	}
	$resposta['nome_da_entidade']=get_field('nome_da_entidade', $_POST['id']);
	$resposta['telefone_da_entidade']=get_field('telefone_da_entidade', $_POST['id']);
	$resposta['endereco_da_entidade']=get_field('endereco_da_entidade', $_POST['id']);
	$resposta['site_da_entidade']=get_field('site_da_entidade', $_POST['id']);
	$resposta['e-mail_de_contato']=get_field('e-mail_de_contato', $_POST['id']);
	$resposta['resumo_da_pratica']=get_field('resumo_da_pratica', $_POST['id']);
	$resposta['objetivo']=get_field('objetivo', $_POST['id']);
	$resposta['publico-alvo']=get_field('nome_da_entidade', $_POST['id']);
	$iframe = get_field('video', $_POST['id']);
	preg_match('/src="(.+?)"/', $iframe, $matches);
	$url=explode('?', $matches[1]);
	$resposta['video']=$url[0];
	$resposta['local_de_implementacao']=get_field('local_de_implementacao', $_POST['id']);
	$resposta['descricao_das_acoes']=get_field('descricao_das_acoes', $_POST['id']);
	$resposta['resultados']=get_field('resultados', $_POST['id']);
	$resposta['imagem_destacada'] = wp_get_attachment_url( get_post_thumbnail_id($id) );
	$anexos= $attachments = get_posts( array(
         'post_type' => 'attachment',
         'posts_per_page' => -1,
         'post_parent' => $id,
         'exclude'     => get_post_thumbnail_id($id)

        ) );
	$resposta['galeria'] = '';
	$resposta['anexos'] = "";
	foreach ($anexos as $anexo ) {
		if (wp_attachment_is_image( $anexo->ID )) {
			$resposta['galeria'] .= '<img class="thumb-gal" src="'.wp_get_attachment_thumb_url( $anexo->ID ).'">';
		}
		else{
			$resposta['anexos'] .= '<p>'.$anexo->guid.'</p>';
		}
			// print_r( $anexo->ID);
	
	}
	$resposta['anexos'] =
	print_r($resposta);
	wp_die();


	// <input class="acf" name="login" placeholder="Nome de usuário *" type="text">
	// <input class="acf" name="e-mail_de_cadastro" placeholder="E-mail de cadastro *" type="email">
	// <input class="acf" name="senha" placeholder="Cadastre uma senha *" type="password">
	// <input class="acf" name="senha-repetir" placeholder="Repita a senha *" type="password">
	// <h4 class="subtitulo-cadastro">Responsável pela prática</h4>
	// <input class="acf" name="nome_da_entidade" placeholder="Nome da Entidade ou pessoa *" type="text">
	// <input class="acf" name="telefone_da_entidade" placeholder="Telefone" type="text">
	// <input class="acf" name="endereco_da_entidade" placeholder="Endereço" type="text">
	// <input class="acf" name="site_da_entidade" placeholder="Site" type="text">
	// <label>Você pode colocar multiplos valores separados por virgula</label>
	// <input class="acf" name="e-mail_de_contato" placeholder="E-mail de contato *" type="email">
	// <label>Você pode colocar multiplos valores separados por virgula</label>
	
	// <textarea class="acf" maxlength="800" rows="5"name='resumo_da_pratica' placeholder="Resumo da prática *"></textarea>

	// <textarea class='acf' rows="5"name='objetivo' placeholder="Objetivo *"></textarea>
	// <input class='acf' name="publico-alvo" placeholder="Público-alvo *" type="text">
	// <input class='acf' name="video" placeholder="Link do Vídeo" type="text">
	// <input class='acf' name="local_de_implementacao" placeholder="Local de implementação" type="text">
	// <textarea class='acf' rows="5"name='descricao_das_acoes' placeholder="Descrição da Ação *"></textarea>
	// <textarea class='acf' rows="5"name='resultados' placeholder="Resultados *"></textarea>
}