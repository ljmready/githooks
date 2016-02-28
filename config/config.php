<?php
return array(
    'web_user' => 'apache',
    'web_group' => 'apache',
    'projects' => array(
        'hometown_market' => array(
            'password' => '123321',
            'web_path' => '/var/web/hometown_market',
	    'branch' => 'master',
        ),
        'test' => array(
            'password' => '123321',
            'web_path' => '/var/web/test',
	    'branch' => 'master',
        ),
        'lostAndFound' => array(
            'password' => '123321',
            'web_path' => '/var/web/lostAndFound',
	    'branch' => 'master',
        ),
    )

);
