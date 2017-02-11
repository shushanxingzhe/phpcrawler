<?php
/**
 * User: liujiafu
 * Date: 2017/2/9
 * Time: 11:30
 */

define('DB_HOST','192.168.117.128');
define('DB_USER','root');
define('DB_PASS','123456');
define('DB_DATABASE','game');

define('ROOT_PATH',__DIR__);


$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
mysqli_select_db($conn, DB_DATABASE);
mysqli_query($conn, "SET NAMES 'utf8'");



