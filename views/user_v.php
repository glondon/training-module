<style> @import url('<?php echo TRAINING_PATH ?>css/training.css') </style>
<!-- load jQuery start -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
<!-- load jQuery end -->
<script type='text/javascript' src="<?php echo TRAINING_PATH ?>js/training.js"></script>
<div class="post"> 
    <?php if ($data['user_id'] == NULL) {
        // this will likely never be used, but just in case
        echo '<h1>No user is selected, click on Edit Training to select an Employee</h1>';
    } else { ?>
    <h1>Update <?php echo $data['firstName'] . ' ' . $data['lastName'] ?>'s completed training(s):</h1>
    <h3><?php echo $data['notify'] ?></h3>
    <?php 
    validation_errors(); 
     echo form_open('', '');
foreach($data['trainingNamesDropdown'] as $v) {    
    $options[] = array($v['id'] => $v['name']);
}
echo '<div class="dropDownLabel">Select a Training Course:</div>';
echo form_dropdown('training', $options);
echo '<br />';
echo '<div class="date">Choose a Date: <input type="text" id="datepicker" name="date" /></div>';
$add = array('name' => 'add', 'value' => 'Add');
echo form_submit($add);
echo form_close();
    if ($data['count'] > 0) {
    echo form_open();
    validation_errors();
    ?>
    <table>
        <thead>
            <tr>
                <th>Select</th>
                <th>Training</th>
                <th>Date Completed</th>
            </tr>
        </thead>
        <?php foreach($data['trainings'] as $item) { ?>
        <tr>
            <td>
                <?php 
               
                    $checks = array(
                    'name'        => 'checks[]',
                    'value'       => $item['trainingId'],
                    'checked'     => FALSE
                    );
                    echo form_checkbox($checks); 
                ?>
            </td>
            <td><?php echo $item['name'] ?></td>
            <td><?php echo date('M jS Y', strtotime($item['date'])) ?></td>
        </tr>    
        <?php } ?>        
    </table>
<?php
$js = 'onclick="return confirmSubmit()"';
$delete = array('name' => 'delete', 'value' => 'Delete');
    echo form_submit($delete, '', $js);
    echo form_close();
    } // close data count if statement
    echo '<p class="showRight"><b>' . $data['count'] . '</b> completed trainings</p>';
}  // close begginng else statement
?>       
</div>

