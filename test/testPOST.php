<?php

?>
<!DOCTYPE html>
<html>
<head>

</head>
<body>

<button type="button" onclick="loadDoc()">Request data</button>

<p id="demo"></p>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
var data = {};
data.name = "a";
data.email = "a";
data.username = "a";
data.password = "a";
data.isDisable = "a";
data.status = "a";
var json = JSON.stringify(data);

function loadDoc() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.write(this.responseText);
    }
  };
  xhttp.open("POST", "api.php?table=user", true);
  xhttp.send(json);
}
</script>
</html>