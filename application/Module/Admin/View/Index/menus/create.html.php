<?php use \Uno\Html; ?>
<div class="span-1">&nbsp;</div>

<div class="span-12">
  <?php Html::OpenForm() ?>
  <fieldset>
    <legend><?php echo _('Create a new menu') ?></legend>
    <p>
      <label><?php echo _('Name') ?></label><br />
      <?php Html::input('name', $name) ?>
      <span class=".quiet">(<?php echo _('i.e. Top Menu, Main Menu, etc.') ?>)</span>
    </p>
    <p>
      <label><?php echo _('Description') ?></label><br />
      <?php Html::input('description', $description) ?>
      <span class=".quiet">(<?php echo _('Optional') ?>)</span>
    </p>
    <p class="span-6">
      <?php Html::input('', 'Submit', 'submit', array('class' => 'button')) ?>
    </p>
  </fieldset>
  <?php Html::CloseForm() ?>
</div>
