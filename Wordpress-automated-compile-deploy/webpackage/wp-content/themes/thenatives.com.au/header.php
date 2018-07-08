<!DOCTYPE html>
<html <?php language_attributes(); ?> />
<head>
	<meta charset="<?php bloginfo('charset'); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<link rel="profile" href="http://gmgp.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	<?php wp_head(); ?>
    <script type='text/javascript'>
    (function (d, t) {
      var bh = d.createElement(t), s = d.getElementsByTagName(t)[0];
      bh.type = 'text/javascript';
      bh.src = 'https://www.bugherd.com/sidebarv2.js?apikey=z69vdzz2osj1o8wbvxxg8g';
      s.parentNode.insertBefore(bh, s);
      })(document, 'script');
    </script>

</head>
<body <?php body_class(); ?> >
    <?php do_action('thenative_before_body'); ?>
	<div id="wrapper">
        <?php do_action( 'thenatives_header_init' ); ?>
        <div id="body" class="site-main">