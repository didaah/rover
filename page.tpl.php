<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><?php echo $head_title; ?></title>
  <?php echo $heads; ?>
  <?php echo $styles; ?>
  <?php echo $scripts; ?>
</head>
  <body class="<?php echo $user_login_status;?>">
	<div id="wrapper" class="container"><div class="<?php echo $args_id;?>">
    <div id="header-top"></div>
    <?php echo $lang_links; ?>
		<div id="header-wrap"><div id="header">
      <div class="menu"><?php echo $menu; ?></div>
      <div class="site-logo">
        <a href="<?php echo $base_path?>" title="<?php echo $site_global->name; ?>" class="logo">
          <img src="<?php echo $site_global->logo; ?>" alt="<?php echo $site_global->name?>" />
        </a>
      </div>
    </div></div>

    <?php echo _rover_get_content_top(); ?>

    <div id="main"> 
      <?php if (dd_is_front()) : ?><div class="site-description"><p><?php echo $site_global->description; ?></p></div> <?php endif;?>
      <?php if ($left) : ?>
				<div id="sidebar-left" class="sidebar span-6">
          <?php echo $left; ?>
        </div>
			<?php endif; ?>

      <?php
        if ($left && $right) {
          $content_class = 'span-12';
        } else if ($left) {
          $content_class = 'span-18 last';
        } else if ($right) {
          $content_class = 'span-18';
        } else {
          $content_class = '';
        }
      ?>
      
      <div id="content" class="<?php echo $content_class;?>">
        <?php echo $breadcrumb; ?>
        <?php echo $tabs; ?>
        <?php echo $sub_tabs; ?>
        <?php echo $messages; ?>
        <?php echo $help; ?>
        <?php if ($content_top) : ?><div id="content-block-top"><div class="content-block-column"><?php echo $content_top; ?></div></div> <?php endif;?>
        <?php echo $content; ?>
        <?php if($content_bottom) : ?><div id="content-block-bottom"><div class="content-block-column"><?php echo $content_bottom; ?></div></div><?php endif?>
      </div>
      
      <?php if ($right) : ?>
        <div id="sidebar-right" class="sidebar span-6 last">
          <?php echo $right; ?>
        </div>
      <?php endif; ?>
			
		</div>
    <?php if (!empty($main_bottom)) : ?><div id="main-bottom"><div class="content-column"><?php echo $main_bottom; ?></div></div> <?php endif ;?>
		<div id="contact"><div class="content-column"><?php echo _rover_get_contact(); ?></div></div>
		<div id="footer"><div class="content-column"><?php echo $site_global->footer; ?><?php echo $footer; ?></div></div>
    <?php if (!empty($debug)) : ?><div id="debug-info"><?php echo $debug; ?></div> <?php endif; ?>
	</div></div>
	<?php echo $closure; ?>
</body>
</html>
