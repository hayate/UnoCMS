<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo ucfirst($_SERVER['SERVER_NAME']) ?></title>
    <!--link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" /-->
    <script type="text/javascript" src="/js/jquery-1.6.1.min.js"></script>
    <?php echo $jscript ?>
  </head>
  <body>
    <div class="container">

      <?php if ($user->hasIdentity()) { ?>
      <ul>
        <li>Welcome, <?php echo $user->username ?></li>
        <li>With role: <?php echo $user->role ?></li>
        <li><a href="logout">Log Out</a></li>
      </ul>
      <?php } ?>

      <?php if ($error) { ?>
      <div style="background-color:pink;border:1px solid red;">
        <?php echo $error ?>
      </div>
      <?php } ?>

      <?php Uno\Html::OpenForm() ?>
      <fieldset>
        <legend>Log In</legend>
        <div>
          <label style="display:inline-block;width:100px;">Username</label>
          <input type="text" name="username" value="" />
        </div>
        <div>
          <label style="display:inline-block;width:100px;">Password</label>
          <input type="password" name="password" value="" />
        </div>
        <div>
          <input type="submit" value="Submit" />
        </div>

      </fieldset>

      <?php Uno\Html::CloseForm() ?>

    </div>
  </body>
</html>
