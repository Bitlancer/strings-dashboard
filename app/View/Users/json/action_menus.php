<?php
     $enabledUserActionMenuItems = array(
        array(
            'type' => 'modal',
            'text' => 'Edit User',
            'source' => '/Users/edit/%__id__%.json',
        ),
        array(
            'type' => 'modal',
            'text' => 'Reset Password',
            'source' => '/Users/changePassword/%__id__%.json'
        ),
        array(
            'type' => 'modal',
            'text' => 'Disable User',
            'source' => '/Users/disable/%__id__%.json'
        )
    );

    $disabledUserActionMenuItems = array(
        array(
            'type' => 'modal',
            'text' => 'Re-enable User',
            'source' => '/Users/enable/%__id__%.json'
        )
    );
