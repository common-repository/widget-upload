<?php
/*
Plugin Name: widget upload
Plugin URI: http://jessai.fr.nf/archives/9
Description: widget upload.
Version: 1.5.1
Author: jessai
Author URI: http://jessai.fr.nf
*/
function widget_upload_init() {
	
	function widget_upload( $args ) {
		$user = wp_get_current_user();
		if (($user->user_login) &&(!$user->wp_user_level)) {
			$niveau_user = 0;
		}
		else {
			$niveau_user = $user->wp_user_level;
		}
		if ($user->ID==0)
			$niveau_user = -1;			
		extract($args);    
		$options = get_option('widget_upload_options');
		if ($niveau_user < $options['upload_autorise']) {
			return;
		}
			$ext_autorises = explode(",",$options['upload_ext']);
			echo $before_widget;
		        echo $before_title . $options['upload_title']. $after_title;
		        //echo 'user :';
		        //print_r($user);
				$user_identity = $user->display_name;
				
				echo '<br />'.utf8_encode('<div align="center" >Les extensions autorisées sont : </div><div>');
				$nbre_ext_autorises = count($ext_autorises);
				$i=0;
				echo '<p align="center" >';
				foreach ($ext_autorises as $ext_autorise)
				{
					$i++;
					echo $ext_autorise;
					if ($i < $nbre_ext_autorises) echo ' - ';
				}
				echo '</p>';
		?>
				</div><br />
		        <ul>
		        
		             <form name="form_upload" method="post" enctype="multipart/form-data">
		             	<input type="file" name="fileupload" id="upfile_0" size="10" tabindex="1" />
						<input align="center" type="submit" name="envoyer" value="envoyer" />
					</form>
		        </ul>
		<?php
			if (is_uploaded_file($_FILES['fileupload']['tmp_name']))
			{
				$ext_fichier = explode(".",$_FILES['fileupload']['name']);
				$ext_autorise_ok = false ;
				
				foreach ($ext_autorises as $ext_autorise)
				{
					if ($ext_autorise == end($ext_fichier))
					{
						$ext_autorise_ok = true ;
						break ;
					}
				}
				if ($ext_autorise_ok)
				{
					$source = $_FILES[fileupload][tmp_name];
		 			$dest = ABSPATH.'/'.$options['upload_chemin'].'/'.$_FILES[fileupload][name];
					if ($source)
					{
						copy($source,$dest);
					}
					echo utf8_encode('le fichier a été envoyé');
					unlink($source);
				}
				else
				{
					echo utf8_encode('opération interdite');
				}
							
			}
			echo $after_widget;	
		

	}
	
	
	function widget_upload_control() {
	        $newoptions = $options = get_option('widget_upload_options');
	        if ( $_POST['submit_essai'] ) {
		       $newoptions['upload_title'] = $_POST['upload_title'];
		       $newoptions['upload_autorise'] = $_POST['upload_autorise'];
		       $newoptions['upload_chemin'] = $_POST['upload_chemin'];
		       $newoptions['upload_ext'] = $_POST['upload_ext'];
		       print_r($newoptions);
	        }
	        if ( $options != $newoptions ) {
	            $options = $newoptions;
	            update_option('widget_upload_options', $options);
	        }
	        
	?>
	    <div><label for="upload_title">Titre     : 
	          <div><input name="upload_title" id="upload_title" value="<?php echo $options['upload_title']; ?>" /></div>
	    </label></div>
	    <div style="margin-top:10px; margin-bottom:5px; " ><label for="upload_autorise"> Qui peut uploader :
	    		<div><select name="upload_autorise" id="upload_autorise">
	    				<option value="-1" <?php if ($options['upload_autorise'] == -1) echo 'selected="selected"'; ?> >Non inscrit</option>
			<?php
			$nom_role = role();
			foreach( $nom_role as $role => $details ) {
				for ($i=0; $i<=10; $i++){
					$niveau="level_".$i;
					if ($details['capabilities'][$niveau]) {
						$role_value = $i;
					}
				}
	          	$name = translate_with_context($details['name']);
	          	if ($role_value == $options['upload_autorise']) {
		          	echo '<option value="'.$role_value.'" selected="selected" >'.$name.'</option>';
	          	}
	          	else {
					echo '<option value="'.$role_value.'" >'.$name.'</option><br />';
				}
			}
			?></select></div>

	    </label></div>
	    
	    <div><label for="upload_chemin">chemin : 
	           <div><input name="upload_chemin" id="upload_chemin" value="<?php echo $options['upload_chemin']; ?>" /></div>
	    </label></div>
	    <div><label for="upload_ext"><?php echo utf8_encode('extensions autorisées (séparer par une virgule) : '); ?> 
	           <div><input name="upload_ext" id="upload_ext" value="<?php echo $options['upload_ext']; ?>" /></div>
	    </label></div>
	    <input id="submit_essai" name="submit_essai" type="hidden" value="1" />
	<?php
	}
  
  register_sidebar_widget( 'Upload', 'widget_upload');
  register_widget_control('Upload', 'widget_upload_control', 200, 500);
}

function role(){
	 global $wp_roles;
	 
     $all_roles = $wp_roles->roles;
     $editable_roles = apply_filters('editable_roles', $all_roles);
     return $editable_roles;
}
function affiche_succes($succes){
	echo utf8_encode($succes);
}

	add_action('widgets_init', 'widget_upload_init');
?>