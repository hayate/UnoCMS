<div class="span-1">&nbsp;</div>

<div class="span-12">
  <?php if (count($menus)) { ?>
  <ul>
    <?php foreach ($menus as $menu) { ?>
    <li>
      <?php echo $menu->name ?>
    </li>
    <?php } ?>
  </ul>
  <?php } else { ?>
  <?php echo _('There are no menus created yet.') ?>
  <?php } ?>
</div>
