<div class="container">

  <div class="mybox span-4 border">
    <h3><?php echo _('Menus') ?></h3>
    <div class="in">
      <ul>
        <li><a href="/admin/menus"><?php echo _('Menus') ?></a></li>
        <li><a href="/admin/menus/create"><?php echo _('Create New') ?></a></li>
      </ul>
    </div>
  </div>

  <?php echo isset($content) ? $content : '' ?>

</div>
