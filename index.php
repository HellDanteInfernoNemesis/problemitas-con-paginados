<?php

if(!isset($_SESSION['id'], $_SESSION['user'],$_SESSION['email'])){
 
    if($_POST){
        if ($_POST['status'] == 'login'){
            include('core/models/class.Security.Access.php');
            $acceder = new securityconexion();
            $acceder->login(); 
            exit;
        } else if ($_POST['status'] == 'register'){
            include('core/models/class.Security.Access.php');
            $acceder = new securityconexion();
            $acceder->registrar(); 
            exit;
        }
    } else{
        
        $template = new Smarty();
        $template->display('index.tpl');        
     }
} else {
$template = new Smarty();

$db  = new conexion();
$sql = $db->query("SELECT * FROM categories ORDER BY id;");
while($x = $db->recorrer($sql)){
        $categories[] = array(
            'id' => $x['id'],
            'category' => $x['category'],
            'iduser' => $x['iduser'],
            'iduserallow' => $x['iduserallow']
        );
}
$template->assign('categories',$categories);
/* cierra los recursos */
$db->liberar($sql);


$sql2 = $db->query("SELECT * FROM subcategories ORDER BY id;");
while($y = $db->recorrer($sql2)){
        $subcategories[] = array(
            'id' => $y['id'],
            'father' => $y['father'],
            'subcategory' => $y['subcategory'],
            'iduser' => $y['iduser'],
            'iduserallow' => $y['iduserallow']
        );
}
$template->assign('subcategories',$subcategories);
/* cierra los recursos */
$db->liberar($sql2);

/*  estrctura de permisos quiero efectuar lo esto dependiendo si estas habilitado para realizar tal moviento o ajuste  */

$micategory = isset($_GET['Category']) ? $_GET['Category'] : null;
if(isset($_GET['Category'])){
$misubcategoria = isset($_GET['SubCategory']) ? $_GET['SubCategory'] : null;
}

$sql3 =  $db->query("SELECT * FROM permisos ORDER BY userid DESC");
while($z = $db->recorrer($sql3)){
    $Permisos[] = array(
        'userid' => $z['userid'],
        'AllowDownload' => $z['AllowDownload'],
        'Hidden' => $z['Hidden'],
        'mask' => $z['mask'],
        'blogsRead' => $z['blogsRead'],
        'blogsWrite' => $z['blogsWrite'],
        'blogsEdit' => $z['blogsEdit'],
        'blogsDelete' => $z['blogsDelete'],
        'CategoryRead' => $z['CategoryRead'],
        'CategoryWrite' => $z['CategoryWrite'],
        'CategoryEdit' => $z['CategoryEdit'],
        'CategoryDelete' => $z['CategoryDelete'],
        'SubCategoryRead' => $z['SubCategoryRead'],
        'SubCategoryWrite' => $z['SubCategoryWrite'],
        'SubCategoryEdit' => $z['SubCategoryEdit'],
        'SubCategoryDelete' => $z['SubCategoryDelete'],
        'UserRead' => $z['UserRead'],
        'UserWrite' => $z['UserWrite'],
        'UserEdit' => $z['UserEdit'],
        'UserDelete' => $z['UserDelete'],
        'MonitorRead' => $z['MonitorRead'],
        'MonitorWrite' => $z['MonitorWrite'],
        'MonitorEdit' => $z['MonitorEdit'],
        'MonitorDelete' => $z['MonitorDelete'],
        'UserTagRead' => $z['UserTagRead'],
        'UserTagWrite' => $z['UserTagWrite'],
        'UserTagEdit' => $z['UserTagEdit'],
        'UserTagDelete' => $z['UserTagDelete'],
        'PrivateMsgRead' => $z['PrivateMsgRead'],
        'PrivateMsgWrite' => $z['PrivateMsgWrite'],
        'PrivateMsgEdit' => $z['PrivateMsgEdit'],
        'PrivateMsgDelete' => $z['PrivateMsgDelete'],
        'PublicMsgRead' => $z['PublicMsgRead'],
        'PublicMsgWrite' => $z['PublicMsgWrite'],
        'PublicMsgEdit' => $z['PublicMsgEdit'],
        'PublicMsgDelete' => $z['PublicMsgDelete']
    );
}
$template->assign('Permisos',$Permisos);
/* cierra los recursos */
$db->liberar($sql3);


/* Area de posteos depende tambien de la categoria y subcategoria */

    if (isset($_GET['Category'],$_GET['SubCategory'])){
        $sql4 = $db->query("SELECT * FROM post ORDER BY id DESC;");
        $sheetsql = "SELECT user FROM members WHERE id=?;";
        $prepare_sql = $db->prepare($sheetsql);
        $prepare_sql->bind_param('i',$id);

        while($p = $db->recorrer($sql4)){
        if ($p['Category'] == $_GET['Category']){
            if ($p['SubCategory'] == $_GET['SubCategory']){
                $id = $p['iduser'];
                $prepare_sql->execute();
                $prepare_sql->bind_result($author);
                $prepare_sql->fetch();
                $posts[] = array(
                    'id' => $p['id'],
                    'title' => $p['title'],
                    'TextTumbnail' => $p['TextTumbnail'],
                    'content' => $p['content'],
                    'iduser' => $author,
                    'points' => $p['points'],
                    'date' => $p['date'],
                    'borrador' => $p['borrador'],
                    'Category' => $p['Category'],
                    'SubCategory' => $p['SubCategory'],
                    'Edit' => $p['Edit']
                    ); 
                }
            }
        }
    } else{
    
        $sql4 = $db->query("SELECT * FROM post ORDER BY id DESC;");
        $sheetsql = "SELECT user FROM members WHERE id=?;";
        $prepare_sql = $db->prepare($sheetsql);
        $prepare_sql->bind_param('i',$id);


    while($p = $db->recorrer($sql4)){ 
    $id = $p['iduser'];
    $prepare_sql->execute();
    $prepare_sql->bind_result($author);
    $prepare_sql->fetch();
    $posts[] = array(
        'id' => $p['id'],
        'title' => $p['title'],
        'TextTumbnail' => $p['TextTumbnail'],
        'content' => $p['content'],
        'iduser' => $author,
        'points' => $p['points'],
        'date' => $p['date'],
        'borrador' => $p['borrador'],
        'Category' => $p['Category'],
        'SubCategory' => $p['SubCategory'],
        'Edit' => $p['Edit']
        );
     }
    }
    
/*  cierra los recursos  */
$prepare_sql->close();
$db->liberar($sql4);
if (isset($posts)){
    $template->assign('posts',$posts);
}


$db->close();
$template->display('index.tpl');
}

?>
