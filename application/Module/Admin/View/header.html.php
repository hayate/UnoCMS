<div class="container">
  <div class="span-24">
    <h1 class="logo"><a href="/admin" title="<?php echo _('Admin Home') ?>">UnoCMS</a></h1>
  </div>
</div>
<div id="menubar">
  <div class="container">

    <div class="span-24">
      <ul>
        <?php foreach ($topmenu as $menu) { ?>
        <li>
          <a class="<?php echo $menu['active'] ? 'active' : '' ?>" href="<?php echo $menu['link'] ?>"><?php echo $menu['name'] ?></a>
        </li>
        <?php } ?>
      </ul>
    </div>

  </div>
</div>
<div class="container">
  <?php echo $this->merge('status') ?>
</div>

