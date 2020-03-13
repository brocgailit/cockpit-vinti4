<?php

$app->on('cockpit.rest.init', function ($routes) {
  $routes['vinti4'] = 'VintiFour\\Controller\\VintiFourPayments';
});