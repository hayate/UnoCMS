<div class="container">

  <div class="mybox span-4">
    <h3><?php echo $module->name() ?></h3>
    <div class="in">
      <ul>
        <?php foreach ($module->adminMenu() as $key => $val) { ?>
        <li>
          <a href="/admin/module/<?php echo $module->name().'/'.$val ?>"><?php echo $key ?></a>
        </li>
        <?php } ?>
      </ul>
    </div>
  </div>

  <?php echo isset($content) ? $content : '' ?>
</div>
