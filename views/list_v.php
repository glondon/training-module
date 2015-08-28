<style> @import url('../../../<?php echo TRAINING_PATH ?>css/training.css') </style>
<script type='text/javascript' src="../../../<?php echo TRAINING_PATH ?>js/training.js"></script>
<div class="post">
<h1><?php echo $data['title'] ?></h1>
<table width="100%" border="1" class="frontTable">
        <thead>
            <tr>
                <th>Training</th>
                <th>Date Completed</th>
            </tr>
        </thead>
        <tr>
        <?php foreach ($data['uData'] as $item) { ?>    
            <td><?php echo $item['trainingName'] ?></td>         
            <td><?php echo date('M jS Y', strtotime($item['dates'])) ?></td>
        </tr>
        <?php } ?>
</table>
<p class="showRight"><b><?php echo $data['count'] ?></b> courses completed</p>
<p>Go back to view other <a href="<?php echo LUBER_URL ?>training">completed Employee training</a>.</p>
</div>
