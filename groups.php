<?PHP

 require('inc/common.php');
 require('inc/database.php');

 $sql = 'SELECT groupID, groupName, graphicID, published FROM invGroups WHERE categoryID = ' . ((int) $_GET['category']);
 $res = mysql_query($sql);

 header('Content-type: text/xml');

 echo "<?xml version='1.0' encoding='UTF-8'?>\n";
 echo "<eveapi version='2' etversion='1'>\n";
 echo " <currentTime>", date('Y-m-d H:i:s'), "</currentTime>\n";
 echo " <result>\n";
 echo '  <rowset name="groups" key="groupID" columns="groupID,groupName,graphicID,published">', "\n";

 while ($row = mysql_fetch_assoc($res)) {
  echo '   <row';

  foreach ($row as $key => $value) {
   echo ' ', $key, '="', htmlspecialchars($value), '"';
  }

  echo '/>', "\n";
 }

 echo '  </rowset>', "\n";
 echo " </result>\n";
 echo " <cachedUntil>", date('Y-m-d H:i:s', CACHEDUNTIL), "</cachedUntil>\n";
 echo "</eveapi>\n";

?>
