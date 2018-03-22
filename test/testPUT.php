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

function loadDoc() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.write(this.responseText);
    }
  };
  xhttp.open("PUT", "api.php?table=user&id=23&newName=differentname", true);
  xhttp.send();
}
</script>
</html>