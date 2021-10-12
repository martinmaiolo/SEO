<?php
if (is_front_page()) : ?>
<meta name="Googlebot" content="index">
<meta name="robots" content="index">
<!--this is front-->
<?php elseif (is_search()) : ?>
<meta name="Googlebot" content="noindex">
<meta name="robots" content="noindex">
<!--searchqueries now part of the main conditional logic-->
<?php elseif (is_page('samplePageNameYouWantToNOINDEX')) : ?>
<meta name="Googlebot" content="noindex">
<meta name="robots" content="noindex">
<?php elseif (is_singple('sampleBlogPostNameYouWantToNOINDEX')) : ?>
<meta name="Googlebot" content="noindex">
<meta name="robots" content="noindex">
<?php endif; ?>
