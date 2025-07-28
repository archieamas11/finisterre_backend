<?php
include __DIR__ . '/../config.php';

// Query to get grave data
$query = "SELECT tbl_deceased.*, grave_points.*, tbl_files.record_id AS file_record_id 
          FROM tbl_deceased
          RIGHT JOIN grave_points ON tbl_deceased.grave_id=grave_points.grave_id
          LEFT JOIN tbl_files ON tbl_files.record_id=tbl_deceased.record_id";

$result = mysqli_query($mysqli, $query);

$features = [];
while ($row = mysqli_fetch_array($result)) {
    // Get deceased names
    $graveno = $row['grave_id'];
    $deceased_names = '';
    $multiple_names = '';
    $birth_dates = '';
    $death_dates = '';
    $years_buried = '';
    $photos = '';
    
    // Get deceased names
    $sql = "SELECT * FROM tbl_deceased WHERE grave_id = $graveno";
    if ($records = mysqli_query($mysqli, $sql)) {
        $counter4deceased = 1;
        while ($record = mysqli_fetch_assoc($records)) {
            $deceased_names .= $record['dead_fullname'];
            $counter4deceased++;
        }
    }
    
    // Get contact person
    $contact_person = '';
    $sql = "SELECT c.first_name, c.last_name, l.grave_id FROM tbl_lot l JOIN tbl_customers c ON c.customer_id = l.customer_id WHERE l.grave_id = $graveno";
    if ($records = mysqli_query($mysqli, $sql)) {
        while ($record = mysqli_fetch_assoc($records)) {
            $contact_person .= $record['first_name'] . ' ' . $record['last_name'];
        }
    }
    
    // Get multiple names with details
    $sql = "SELECT dead_fullname, dead_birth_date, dead_date_death FROM tbl_deceased WHERE grave_id = $graveno";
    $output = '';
    if ($records = mysqli_query($mysqli, $sql)) {
        while ($record = mysqli_fetch_assoc($records)) {
            $output .= '<div class="deceased-record">';
            $output .= '<strong>' . addslashes($record['dead_fullname']) . '</strong><br>';
            $output .= '<small>Birth: ' . addslashes($record['dead_birth_date'] ?: 'N/A') . '</small><br>';
            $output .= '<small>Death: ' . addslashes($record['dead_date_death'] ?: 'N/A') . '</small>';
            $output .= '</div>';
        }
    }
    
    // Get birth and death dates
    $sql = "SELECT dead_birth_date, dead_date_death FROM tbl_deceased WHERE grave_id = $graveno";
    if ($duplicate = mysqli_query($mysqli, $sql)) {
        while ($dup = mysqli_fetch_assoc($duplicate)) {
            if (!empty($dup['dead_birth_date'])) {
                $date = new DateTime($dup['dead_birth_date']);
                $birth_dates .= $date->format('m/d/Y');
            } else {
                $birth_dates .= 'N/A';
            }
            
            if (!empty($dup['dead_date_death'])) {
                $date = new DateTime($dup['dead_date_death']);
                $death_dates .= $date->format('m/d/Y');
            } else {
                $death_dates .= 'N/A';
            }
            
            // Calculate years buried
            $current_date = new DateTime();
            $death_date = new DateTime($dup['dead_date_death']);
            $interval = $current_date->diff($death_date);
            $years_buried_calc = $interval->y;
            $months_buried_calc = $interval->m;
            
            if ($years_buried_calc < 1 && $months_buried_calc < 12) {
                $years_buried .= 'Less than a year<br>';
            } else {
                $years_buried .= $years_buried_calc . ' year(s) ' . $months_buried_calc . ' months<br>';
            }
        }
    }
    
    // Get photos
    $counter = 0;
    $sql = "SELECT * FROM tbl_files WHERE record_id = $graveno";
    if ($duplicate = mysqli_query($mysqli, $sql)) {
        while ($dup = mysqli_fetch_assoc($duplicate)) {
            $image_url = htmlspecialchars($dup['grave_filename'], ENT_QUOTES);
            $photos .= "<a href='$image_url' target='_blank'><img src='$image_url' class='grave-photo' alt='Grave photo'></a>";
            $counter++;
        }
    }
    
    $features[] = [
        'type' => 'Feature',
        'properties' => [
            'Grave No.' => $row['grave_id'],
            'Visibility' => $row['dead_visibility'],
            'id' => $row['record_id'],
            'category' => $row['category'],
            'Name' => $deceased_names,
            'Contact Person' => $contact_person,
            'Multiple Names' => $output,
            'DeceasedCount' => $counter4deceased - 1,
            'Birth' => $birth_dates,
            'Death' => $death_dates,
            'Years Buried' => $years_buried,
            'Block' => $row['block'],
            'Status' => $row['status'],
            'Photos' => $photos,
            'PhotoCount' => $counter,
            'auxiliary_storage_labeling_offsetquad' => $row['label']
        ],
        'geometry' => [
            'type' => 'Point',
            'coordinates' => array_map('floatval', explode(',', str_replace(['"', ' '], '', $row['coordinates'])))
        ]
    ];
}

$response = [
    'type' => 'FeatureCollection',
    'name' => 'category_5',
    'crs' => [
        'type' => 'name',
        'properties' => [
            'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
        ]
    ],
    'features' => $features
];

echo json_encode($response);
mysqli_close($mysqli);
?>