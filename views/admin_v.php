<style> @import url('<?php echo TRAINING_PATH ?>css/training.css') </style>
<script type='text/javascript' src="<?php echo TRAINING_PATH ?>js/training.js"></script>

<div class="post">

    <h1><?php echo $data['message'] ?></h1>
    <?php echo $data['notify']; ?>
<?php
  
validation_errors(); 

echo form_open('', ''); 
 ?>
<input type="text" size="20" id="name" name="name" />

<?php

$btnData = array(
    'name' => 'add',
    'value' => 'Add',
    'id' => 'btnAdd'
);

echo form_submit($btnData);

// list training data

?>

<table>
    
    <thead> 
    <tr>
        <th>Select</th>
        <th>Training</th>
        <th>Date Modified</th>
        
    </tr>
    </thead>
    
    <?php  foreach ($data['courses'] as $row) { ?>
    
    <tr>
    <td>
        <?php 
    
        $checks = array(
        'name'        => 'checks[]',
        'value'       => $row['id'],
        'checked'     => FALSE
         );
        
        echo form_checkbox($checks); 
        
        ?>
    </td>
    <td><?php echo $row['name'] ?></td>
    <td><?php echo date('M jS Y', strtotime($row['date'])) ?></td>
    </tr>
    
    <?php } ?>
    
</table>

<?php
$js = 'onclick="return confirmSubmit()"';
$delete = array(
  'name' => 'delete',
  'value' => 'Delete'
);
 
echo form_submit($delete, '', $js);

echo form_close();

?>

<p class="showRight"><?php echo '<b>' . $data['count'] . '</b> total' ?></p>
    
</div> 

