<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<html>
<body>
<script>
#sboard{
	font-size:40px;
}
.scoreBoard{
	background-color : teal; 	border-collapse:collapse; 	color: white; 	table-layout: auto; 	width: 100%; 
} 

#ballByBallTable{
	background-color:lightgreen;
	border-collapse:collapse; 
	table-layout: auto; 	
	width: 100%;
	color:black;
	
}
</script>
<?php
echo "Requested at-".date("h:i:sa");
if ($_SERVER["REQUEST_METHOD"] == "GET") {
	

	$inningsId = @$_GET["inningsId"];
	
	if($inningsId==""){
		echo "Looks like you missed a moon in the night!";
		return;
	}	
		function db_connect()
    {
        static $connection;
        if (!isset($connection)) {
            $config     = parse_ini_file('./private/configram.ini');
            $connection = mysqli_connect($config['servername'], $config['username'], $config['password'], $config['dbname']);
        }
        
        if ($connection === false) {
            return mysqli_connect_error();
        }
        return $connection;
    }
  ?>
  <a href="view.php?inningsId=<?php echo $inningsId ?>&time=<?php echo time()?>"><button>Refresh</button></a>
  <?php
    $connection = db_connect();
    
    // Check connection
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }
	
	$selectQry = "select * from gully_cricket where innings_id=?";
	$stmt = $connection->prepare($selectQry);
	$stmt->bind_param('s', $inningsId); 
	$stmt->execute();
	$result = $stmt->get_result();
	
	while ($row = $result->fetch_assoc()) {		
		//echo $row["score"];
		echo "<table  class=\"scoreBoard\" align=\"center\" style=\"background-color : teal; 	border-collapse:collapse; 	color: white; 	table-layout: auto; 	width: 100%; font-size:40px;\"><tr>".
				"<td><b id=\"sboard\"><i id=\"totScore\">".$row["score"]."</i>/<i id=\"wikts\">".$row["wickets"]."</i></b>(<i id=\"overNum\">".$row["overs"]."</i> ovrs)</td>".
				"<td><b>CRR: <i id=\"runRate\">".$row["run_rate"]."</i></b></td></tr></table><table id=\"ballByBallTable\"><th>Over Wise Score</th><tr><td>".$row["ball_by_ball"]."</td></tr></table>";
	}
	if(mysqli_num_rows($result) ==0){
		echo "Please provide a valid inningsId";
	}
	
	$stmt->close();
	$connection->close();
}
?>
</body>
</html>