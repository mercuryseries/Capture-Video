<?php 
    session_start(); 
    $path = 'images/'; 
    $extension = 'png'; 
     
    if(isset($_POST['img_data'])){ 
        $data = $_POST['img_data']; 
        $data = str_replace('data:image/png;base64,', '', $data); 
        $data = base64_decode($data); 

        $date = date('YmdHis'); 
        $file_name = "img_".$date.".".$extension; 
         
        file_put_contents($path.$file_name, $data); 
        $_SESSION['save_to_file'] = $file_name; 

        header('Content-type: image/png'); 
        $data = file_get_contents($path.$_SESSION['save_to_file']); 
            
        echo $data; 
         
    } else { 
        header('Content-type: image/png'); 
        header('Content-Disposition: attachment; filename="'.$_SESSION['save_to_file'].'"'); 

        $data = file_get_contents($path.$_SESSION['save_to_file']); 

        echo $data; 
    } 
?>