<?php
/**
 * Created by PhpStorm.
 * User: mouhamed aly sidibe
 * Date: 16/08/2017
 * Time: 11:38
 */

session_start() ;
include("config.php");
if ( !isset( $_SESSION['login'] ) )
{
    header('location: index.php?page=medocs' );
    exit;
}
include("inc/header.php");
include("inc/menu-horiz.php");

echo $_SESSION['login'];
?>
<title>
    CNQP - M&eacute;dicaments - Utilisateur <?php echo $_SESSION['login'] ?>
</title>
