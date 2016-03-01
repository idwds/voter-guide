<?php
header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
header("Status: 404 Not Found");
$_SERVER['REDIRECT_STATUS'] = 404;
$GLOBALS['page']->title = "Page not found!";
get_header() ?>
<h2>The page you requested cannot be found! - <?php echo $_SERVER['REDIRECT_STATUS'] ?></h2>
To use our voter guide, <a href="/?token=<?php echo Session::$token ?>">click here</a>.  To view all candidates, <a href="/2015-candidates?token=<?php echo Session::$token ?>">click here</a>.
<?php get_footer();