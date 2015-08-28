<style> @import url('<?php echo TRAINING_PATH ?>css/training.css') </style>
<script type='text/javascript' src="<?php echo TRAINING_PATH ?>js/training.js"></script>

<div class="post"> 
    <h1><?php echo $data['results'] ?></h1>
     
<?php
  
validation_errors(); 

echo form_open('', ''); 
 ?>
<input type="text" size="20" name="typed" /><br />

<?php

$options = array(
                  'first_name'   => 'First Name',
                  'last_name' => 'Last Name',
                  'display_name' => 'Display Name'
                );

echo form_dropdown('by', $options);
echo '<br />';

$searchData = array(
    'name' => 'search',
    'value' => 'Search'
);

echo form_submit($searchData);

echo form_close();


if ($data['submit'] == TRUE && $data['searchResult'] == TRUE) {
?>
<table>
    <thead>
    <tr>
    <th>Edit</th>
    <th>First Name</th>
    <th>Last Name</th>
    <th>Courses Completed</th>
    </tr>
    </thead>   
        <?php foreach ($data['users'] as $row) { ?>
        <tr>
        <td><a title="View or update <?php echo $row['firstName'] ?>'s training information." 
               href="/*****.com/index.php/admin/training/user/<?php echo $row['id'] ?>">
                <img src="<?php echo TRAINING_PATH ?>img/edit-icon.png" class="editImg" /></a></td>
        <td><?php echo $row['firstName'] ?></td>
        <td><?php echo $row['lastName'] ?></td>
        <td><b><?php echo $row['count'] ?></b> completed</td>
        </tr>
        <?php } ?>      
</table>
<?php } ?>
</div>




