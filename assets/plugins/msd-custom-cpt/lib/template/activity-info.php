<?php
global $wpalchemy_media_access;
?>
<div class="msdlab_meta_control" id="msdlab_meta_control">
    <div class="table">
    <div class="row">
    <div class="cell">
        <?php $mb->the_field('pdf-activity-label'); ?>
        <label>Activity PDF Label (Title of attachment)</label>            
        <div class="input_container">
            <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/>
        </div>
    </div>
    </div>
        
    <div class="row">
    <div class="cell file">
        <label>Activity PDF File</label>
        <div class="input_container">
            <?php $mb->the_field('pdf-activity'); ?>
            <?php $group_name = 'pdf-activity'; ?>
            <?php $wpalchemy_media_access->setGroupName($group_name)->setInsertButtonLabel('Insert This')->setTab('gallery'); ?>
            <?php echo $wpalchemy_media_access->getField(array('name' => $mb->get_the_name(), 'value' => $mb->get_the_value())); ?>
            <?php echo $wpalchemy_media_access->getButton(array('label' => '+')); ?>
        </div>
    </div>
    </div>
    </div>
</div>
