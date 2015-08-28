<style> @import url('../<?php echo TRAINING_PATH ?>css/training.css') </style>
<script type='text/javascript' src="../<?php echo TRAINING_PATH ?>js/training.js"></script>
<div class="post">
<h1><?php echo $data['title'] ?></h1>
<?php
    if ($data['group'] == 2) {
        // user
    if ($data['noTraining']) { 
    echo '<p>' . $data['firstName'] . ', you currently have no courses completed yet.</p>';
    echo '<p>If you would like to get started on some training, view the <a href="' . LUBER_URL . 'training-exams">full list of courses</a> to get started.</p>';
} else { ?>
<p><?php echo $data['firstName'] ?>, here is a list of training courses you have completed so far.</p>
<p>To complete more courses please visit the <a href="<?php echo LUBER_URL ?>training-exams">training exams</a> page.</p>
<table width="100%" border="1" class="frontTable">
    <thead>
    <tr>
    <th>ID</th>
    <th>Course Name</th>
    <th>Date Completed</th>
    </tr>
    </thead>   
    <?php foreach ($data['trainings'] as $list) { ?>
    <tr>
        <td><?php echo $list['id'] ?></td>
        <td><?php echo $list['trainingName'] ?></td>
        <td><?php echo date('M jS Y', strtotime($list['dateCompleted'])) ?></td>
    </tr>
    <?php } ?>    
</table>
<p class="showRight"><?php echo $data['trainingCount'] ?> courses completed</p>
<?php } 
    } else {
        // admin or management
     if ($data['noTraining']) {
         echo '<p>No Employees have completed training yet.</p>';
     } else {   
     ?>
<p>Employees who have completed training so far.</p>
<table width="100%" border="1" class="frontTable">
    <thead>
    <tr>
    <th>ID</th>
    <th>First Name</th>
    <th>Last Name</th>
    <th>Courses Completed</th>
    </tr>
    </thead>   
    <?php foreach ($data['rows'] as $item) { ?>
    <tr>
        <td><a title="Click to view <?php echo $item['firstName'] ?>'s completed training courses." 
               href="<?php echo LUBER_URL ?>training/ulist/<?php echo $item['userId']  ?>">
                <img src="../<?php echo TRAINING_PATH ?>img/view-icon.png" height="50" width="50" /></a></td>
        <td><?php echo $item['firstName']  ?></td>
        <td><?php echo $item['lastName']  ?></td>
        <td><?php echo $item['tCount'] ?></td>
    </tr>
    <?php } ?>
</table>
<p>Visit the <a href="<?php echo LUBER_URL ?>training-exams">training exams</a> page for more information.</p>
<?php
        } // end noTraining if/else
    }
?>
</div>

