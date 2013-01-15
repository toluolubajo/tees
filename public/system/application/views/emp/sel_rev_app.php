
        <?php
        
        echo form_open('emp/select_reviewer_approver');
        echo form_fieldset('Select Reviewer'); ?>
        <div>
            <?php echo form_label("Reviewer's Name");
            echo form_dropdown('reviewerName',$reviewer); ?>
        </div>
        <?php echo form_fieldset_close(); ?>

        <?php echo form_fieldset('Select Approving Officer 1'); ?>
            <div>
            <?php echo form_label("Name");
            echo form_dropdown('approveName1',$approval); ?>
        </div>
        <?php echo form_fieldset_close(); ?>

        <?php echo form_fieldset('Select Approving Officer 2'); ?>
            <div>
            <?php echo form_label("Name");
            echo form_dropdown('approveName2',$approval); ?>
        </div>
          <div>
            <?php
//            echo form_submit('next','Next'); ?>
        </div>
        <?php echo form_fieldset_close(); ?>
        <?php
            echo form_close();            
        ?>
        <h4 style="color:red"><?php echo validation_errors();?></h4>
   