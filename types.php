<?PHP

 require('inc/common.php');
 require('inc/database.php');

 header('Content-type: text/xml');

 echo "<?xml version='1.0' encoding='UTF-8'?>\n";
 echo "<eveapi version='2' etversion='1'>\n";
 echo " <currentTime>", date('Y-m-d H:i:s'), "</currentTime>\n";
 echo " <result>\n";

 if (isset($_GET['category']) && isset($_GET['groups'])) {
  $sql2 = 'SELECT groupID, groupName, graphicID, published FROM invGroups WHERE categoryID = ' . ((int) $_GET['category']);
  if (isset($_GET['published'])) { $sql2 .= ' AND published = 1'; }
  $res2 = mysql_query($sql2);

  echo '  <rowset name="groups" key="groupID" columns="groupID,groupName,graphicID', isset($_GET['published']) ? '' : ',published', '">', "\n";

  while ($row2 = mysql_fetch_assoc($res2)) {
   echo '   <row';

   if (isset($_GET['published'])) { unset($row2['published']); }

   foreach ($row2 as $key => $value) {
    echo ' ', $key, '="', htmlspecialchars($value), '"';
   }

   echo '>', "\n";
   $_GET['group'] = $row2['groupID'];
   doTypes();
   echo '</row>';
  }

  echo '  </rowset>', "\n";

 } else {
  doTypes();
 }

 function doTypes() {
 $sql = 'SELECT typeID, groupID, typeName, invTypes.graphicID, published, raceID FROM invTypes WHERE ';

 if (isset($_GET['group'])) { $sql .= 'groupID = ' . ((int) $_GET['group']); }
 else if (isset($_GET['type'])) { $sql .= 'typeID = ' . ((int) $_GET['type']); }
 else if (isset($_GET['category'])) { $sql .= 'groupID IN (SELECT groupID from invGroups WHERE categoryID = ' . ((int) $_GET['category']) . ')'; }

 if (isset($_GET['published'])) { $sql .= ' AND published = 1'; }

 $res = mysql_query($sql) or print(mysql_error());


 echo '  <rowset name="types" key="typeID" columns="typeID', isset($_GET['group']) ? '' : ',groupID', ',typeName,graphicID', isset($_GET['published']) ? '' : ',published', ',raceID">', "\n";

 while ($row = mysql_fetch_assoc($res)) {
  echo '   <row';

  if (isset($_GET['group'])) { unset($row['groupID']); }
  if (isset($_GET['published'])) { unset($row['published']); }

  foreach ($row as $key => $value) {
   echo ' ', $key, '="', htmlspecialchars($value), '"';
  }

  echo '>', "\n";

  $sql2 = 'SELECT attributeID, valueInt, valueFloat, attributeName, published, highIsGood FROM dgmTypeAttributes NATURAL JOIN dgmAttributeTypes WHERE typeID = ' . $row['typeID'];

  if (isset($_GET['attributes'])) { $sql2 .= ' AND attributeName LIKE \'' . mysql_real_escape_string($_GET['attributes']) . '\''; }

  $res2 = mysql_query($sql2);

  echo '    <rowset name="attributes" key="attributeID" columns="attributeID,value,attributeName,published,highIsGood">', "\n";

  while ($row2 = mysql_fetch_assoc($res2)) {
   $row2['value'] = $row2['valueInt'] . $row2['valueFloat'];
   unset($row2['valueInt'], $row2['valueFloat']);

   echo '     <row';
   foreach ($row2 as $key => $value) {
    echo ' ', $key, '="', htmlspecialchars($value), '"';
   }

   echo '/>', "\n";
  }

  echo "    </rowset>\n";
  echo "   </row>\n";
 }

 echo '  </rowset>', "\n";
 }
 echo " </result>\n";
 echo " <cachedUntil>", date('Y-m-d H:i:s', CACHEDUNTIL), "</cachedUntil>\n";
 echo "</eveapi>\n";

?>
