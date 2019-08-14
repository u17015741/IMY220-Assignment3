<?php
	// See all errors and warnings
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);

	// Your database details might be different
	$mysqli = mysqli_connect("localhost", "root", "", "dbUser");


	$email = isset($_POST["loginName"]) ? $_POST["loginName"] : false;
	$pass = isset($_POST["loginPassw"]) ? $_POST["loginPassw"] : false;	
?>

<!DOCTYPE html>
<html>
<head>
	<title>IMY 220 - Assignment 3</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<meta name="author" content="Brenton Stroberg u17015741">
	<!-- Replace Name Surname with your name and surname -->
</head>
<body>
	<div class="container">
		<?php
			if($email && $pass){
				$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
				$res = $mysqli->query($query);
				if($row = mysqli_fetch_array($res)){
					echo 	"<table class='table table-bordered mt-3'>
								<tr>
									<td>Name</td>
									<td>" . $row['name'] . "</td>
								<tr>
								<tr>
									<td>Surname</td>
									<td>" . $row['surname'] . "</td>
								<tr>
								<tr>
									<td>Email Address</td>
									<td>" . $row['email'] . "</td>
								<tr>
								<tr>
									<td>Birthday</td>
									<td>" . $row['birthday'] . "</td>
								<tr>
							</table>"; $userID = $row["user_id"];
						$query = "SELECT * FROM tbgallery WHERE user_id='".$userID."'";
						$result = mysqli_query($mysqli, $query);
						if(mysqli_num_rows($result) >  0){
							echo "<div class ='row imageGallery'>";
							while($row = mysqli_fetch_assoc($result)){
								echo "<div class='col-3' style='background-image: url(gallery/".$row["filename"].");'></div>";;
							}
							echo "</div>";
						}

					

					echo 	"<form enctype='multipart/form-data' action='login.php' method='POST'>
								<div class='form-group'>
									<input type='file' class='form-control' name='picToUpload[]' id='picToUpload' /> <br/>
									<input type='hidden' id='loginEmail' name='loginName' value='" . $email . "'/>
									<input type='hidden' id='loginPassw' name='loginPassw' value='" . $pass . "'/>
									<input type='hidden' id='shh' name='shh' value='yes'/>
									<input type='submit' class='btn btn-standard' value='Upload Image' name='submit' />
								</div>
						  	</form>";

					if(isset($_POST["submit"]))
					{
						$target_dir = "gallery/";
						$uploadFile = $_FILES["picToUpload"];
						$target_file = $target_dir . basename(implode($uploadFile["name"]));
						$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
						// Check if image is an actual image (Note that this method is unsafe)
						if(isset($_POST["submit"]))
						{
							$check = getimagesize($uploadFile["tmp_name"]);
							if($check !== false)
							{
								if(implode($uploadFile["type"]) =="image/jpeg"){
									if(implode($uploadFile["size"]) < 1048576){
										if(file_exists("gallery/" . $uploadFile["name"])){
											echo $uploadFile["name"] . " already exists.";
										} else {
											move_uploaded_file(implode($uploadFile["tmp_name"]),"gallery/" . implode($uploadFile["name"]));
							$query = "INSERT INTO `tbgallery` ( `user_id`, `filename`) VALUES ('".$userID."', '".implode($uploadFile["name"])."')";
											$result = mysqli_query($mysqli, $query);
											if(mysqli_num_rows($result) >  0){
											}else{
												echo "Error sql insert failed";
											}
										}
									}else{
										echo "Error File to large";
									}
									
								}else{
									echo "Error incorrect type of file";
								}
							}
							else
							{
								echo "File is not an image.";
							}
						}
					}
				}else{
					echo '<div class="alert alert-danger mt-3" role="alert">
	  							You are not registered on this site!
	  						</div>';
				}
			} else{
				echo 	'<div class="alert alert-danger mt-3" role="alert">
	  						Could not log you in
	  					</div>';
			}
		?>
	</div>
</body>
</html>
<?php 	mysqli_close($mysqli); ?>