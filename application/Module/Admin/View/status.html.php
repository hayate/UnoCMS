<script type="text/javascript">
/* <![CDATA[ */
$(function() {
$('.error a').click(function(e) {e.stopPropagation();e.preventDefault();$('.error').fadeOut();});
$('.success a').click(function(e) {e.stopPropagation();e.preventDefault();$('.success').fadeOut();});
$('.info a').click(function(e) {e.stopPropagation();e.preventDefault();$('.info').fadeOut();});
$('.notice a').click(function(e) {e.stopPropagation();e.preventDefault();$('.notice').fadeOut();});
});
/* ]]> */
</script>

<?php
$ses = \Uno\Session::getInstance();
$error = $ses->getOnce('error', FALSE);
$success = $ses->getOnce('success', FALSE);
$info = $ses->getOnce('info', FALSE);
$notice = $ses->getOnce('notice', FALSE);

if (! empty($error)) { ?>
<div class="error">
  <div style="float:right;">
    <a href="#"><img src="/admin/resource/error-close.gif" alt="<?php echo _('Close') ?>" /></a>
  </div>
  <div style="float:left:">
    <?php echo is_array($error) ? implode('<br />', $error) : $error ?>
  </div>
</div>
<?php } if (! empty($success)) { ?>
<div class="success">
  <div style="float:right;">
    <a href="#"><img src="/admin/resource/success-close.gif" alt="<?php echo _('Close') ?>" /></a>
  </div>
  <div style="float:left:">
    <?php echo is_array($success) ? implode('<br />', $success) : $success ?>
  </div>
</div>
<?php } if (! empty($info)) { ?>
<div class="info">
  <div style="float:right;">
    <a href="#"><img src="/admin/resource/info-close.gif" alt="<?php echo _('Close') ?>" /></a>
  </div>
  <div style="float:left:">
    <?php echo is_array($info) ? implode('<br />', $info) : $info ?>
  </div>
</div>
<?php } if (! empty($notice)) { ?>
<div class="notice">
  <div style="float:right;">
    <a href="#"><img src="/admin/resource/notice-close.gif" alt="<?php echo _('Close') ?>" /></a>
  </div>
  <div style="float:left:">
    <?php echo is_array($notice) ? implode('<br />', $notice) : $notice ?>
  </div>
</div>
<?php } ?>
