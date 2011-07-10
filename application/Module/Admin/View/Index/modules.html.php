<div class="container">

  <div class="mybox span-24">
    <h3><?php echo _('Installed Modules') ?></h3>
    <div class="in">
      <?php if (count($installed)) { ?>
      <table border="0" cellpadding="0" cellspacing="0">
        <thead>
          <tr>
            <th><?php echo _('Module') ?></th>
            <th><?php echo _('Version') ?></th>
            <th><?php echo _('Description') ?></th>
            <th><?php echo _('Actions') ?></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th><?php echo _('Module') ?></th>
            <th><?php echo _('Version') ?></th>
            <th><?php echo _('Description') ?></th>
            <th><?php echo _('Actions') ?></th>
          </tr>
        </tfoot>
        <tbody>
          <?php foreach ($installed as $m) { ?>
          <tr>
            <td>
              <?php echo $m->name ?>
            </td>
            <td><?php echo $m->version ?></td>
            <td><?php echo $m->description ?></td>
            <td>
              <p>
                <a href="/admin/uninstall/<?php echo $m->name ?>"><?php echo _('Uninstall') ?></a> |
                <?php if ($m->enabled) { ?>
                <a href="/admin/deactivate/<?php echo $m->name ?>"><?php echo _('Deactivate') ?></a>
                <?php } else { ?>
                <a href="/admin/activate/<?php echo $m->name ?>"><?php echo _('Activate') ?></a>
                <?php } ?>
              </p>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <?php } else { ?>
      <?php echo _('There are no installed modules.') ?>
      <?php } ?>
    </div>
  </div>

  <div class="mybox span-24">
    <h3><?php echo _('Available Modules') ?></h3>
    <div class="in">
      <?php if (count($uninstalled)) { ?>
      <table border="0" cellpadding="0" cellspacing="0">
        <thead>
          <tr>
            <th><?php echo _('Module') ?></th>
            <th><?php echo _('Description') ?></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th><?php echo _('Module') ?></th>
            <th><?php echo _('Description') ?></th>
          </tr>
        </tfoot>
        <tbody>
          <?php foreach ($uninstalled as $m) { ?>
          <tr>
            <td>
              <?php echo $m->name() ?>
              <p>
                <a href="/admin/install/<?php echo $m->name() ?>"><?php echo _('Install') ?></a>
              </p>
            </td>
            <td><?php echo $m->description() ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <?php } else { ?>
      <?php echo _('Available modules is currently empty.') ?>
      <?php } ?>
    </div>
  </div>

</div>
