<?php

require_once 'app/deploy_recipes/symfony3.php';

serverList('app/config/servers.yml');

set('repository', 'https://github.com/JustB/placequotes');