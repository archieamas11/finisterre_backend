<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    <?php
		// Database connection
		$connect = mysqli_connect("localhost", "root", "", "finisterre_db");
		
		if (!$connect) {
			die('Connection failed: ' . mysqli_connect_error());
		}

		// Read and decode JSON file
		$file_name = "map_col.json";
		$data = file_get_contents($file_name); 
		$features = json_decode($data, true);

		if (!$features) {
			die('Error: Could not decode JSON file');
		}

		$success_count = 0;
		$error_count = 0;

		// Process each feature in the JSON array
		foreach ($features as $feature) {
			// Extract coordinates array and convert to comma-separated string with space
			$coords_array = $feature['geometry']['coordinates'];
			$coordinates = $coords_array[0] . ', ' . $coords_array[1]; // longitude, latitude (with space)
			
			// Use prepared statement to prevent SQL injection
			$stmt = mysqli_prepare(
				$connect,
				"INSERT INTO tbl_plots_col(coordinates, created_at, updated_at) VALUES (?, NOW(), NOW())"
			);
			mysqli_stmt_bind_param($stmt, "s", $coordinates);

			if (mysqli_stmt_execute($stmt)) {
				$success_count++;
			} else {
				$error_count++;
				echo "Error inserting record: " . mysqli_error($connect) . "<br>";
			}
			
			mysqli_stmt_close($stmt);
		}

		echo "Transfer completed: $success_count records inserted successfully";
		if ($error_count > 0) {
			echo ", $error_count errors occurred";
		}

		mysqli_close($connect);
	?>

</body>

</html>