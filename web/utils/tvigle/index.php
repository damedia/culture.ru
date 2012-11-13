<?php
session_start();

if (empty($_SESSION['culture_util_tvigle_logged_in'])) {
    if (!isset($_POST['password'])) {
        echo '
            <form method="post">
                <input type="password" name="password" value="">
                <input type="submit" value="Login">
            </form>
        ';
        exit();
    } elseif (isset($_POST['password']) && $_POST['password'] === 'ldop789kar') {
        $_SESSION['culture_util_tvigle_logged_in'] = 1;
    } else {
        exit('Access denied');
    }
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'default';

$ownId = isset($_REQUEST['ownid']) ? $_REQUEST['ownid'] : '';
$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : '';
$desc = isset($_REQUEST['description']) ? $_REQUEST['description'] : '';
$url = isset($_REQUEST['video_url']) ? $_REQUEST['video_url'] : '';
$tvigleId = isset($_REQUEST['tvigleid']) ? $_REQUEST['tvigleid'] : '';
$hd = isset($_REQUEST['hd']) ? $_REQUEST['hd'] : '9000';

$serviceUrl = 'http://pub.tvigle.ru/soap/index.php?wsdl';
$Login = 'Stella_l';
$Password = 'armada';

$soap = new SoapClient
(
      $serviceUrl,
      array
      (
            'login'     =>    $Login,
            'password'  =>    $Password
      )
);
$result = '';
switch($action) {
    case 'default':
        break;
    case 'getvideo':
        if(!$tvigleId) { $result = 'No TvigleId!'; break; }
        $result = $soap->VideoItem
        (
            (int)$tvigleId
        );

        break;
    case 'addvideo':
        if(!$ownId) { $result = 'No Own Id!'; break; }
        if(!$title) { $result = 'No title!'; break; }
        if(!$desc) { $result = 'No description!'; break; }
        if(!$url) { $result = 'No URL!'; break; }

        $result = $soap->AddTask
        (
            (int)$ownId,                                            // id
            $title,                                                 // name
            $url,                                                   // url_download
            'http://zilot.mmc-galant.com/tvigle/tvigle.php',        // url_response
            $desc,                                                  // text
            '',                                                     // tags
            0,                                                      // cat_id
            1,                                                      // rs
            $hd                                                     // hd
        );
        break;

    default:
        break;
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
        <meta http-equiv="Content-Type" content="text/html; Charset=utf8"/>
        <title>Tvigle API Tool</title>
    </head>
<body>


<h1>Tvigle API Tool</h1>
<hr />
<style>
    label {
        width: 170px;
        display: block;
        border: 0px solid red;
        float: left;
    }
    br {
        clear: both;
    }
</style>

<div>
    <h3>Get Video Item</h3>
    <form method="post">
        <input type="hidden" name="action" value="getvideo" />
        <label>Tvigle ID:</label>
        <input type="text" name="tvigleid" value="<?php echo $tvigleId ?>" />
        <input type="submit" value="Show video data" />
    </form>
</div>
<div>
    <h3>Add new video</h3>
    <form method="post">
        <input type="hidden" name="action" value="addvideo" />

        <label>Own ID (<b>Unique!</b>):</label>
        <input type="text" name="ownid" value="<?php echo $ownId ?>" /><br />

        <label>Title:</label>
        <input type="text" name="title" value="<?php echo $title ?>" /><br />

        <label>Descrption:</label>
        <input type="text" name="description" value="<?php echo $desc ?>" /><br />

        <label>Direct link:</label>
        <input type="text" name="video_url" value="<?php echo $url ?>" /><br />

        <label>HD:</label>
        <input type="text" name="hd" value="<?php echo $hd ?>" /><br />

        <label>&nbsp;</label>
        <input type="submit" value="Add video" />
    </form>
</div>
<hr />

<?php

if('' !== $result) {
?>
    <h3>Result:</h3>
    <pre>
    <?php var_dump($result) ?>
    </pre>
<?php
}



?>

</body>
</html>
