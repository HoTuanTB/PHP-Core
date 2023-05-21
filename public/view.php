<?php

session_start();
require __DIR__ . '/write_session.php';

flash('greeting', 'Loi roi ban oi', FLASH_ERROR);

echo '<a href="read_session.php" title="Go To Page 2">Go To Page read Sesstion</a>';

