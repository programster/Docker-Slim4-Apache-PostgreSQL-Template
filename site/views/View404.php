<?php

/*
 * This is your current 404 page. Swap the content of renderContent() with something better.
 */

class View404 extends Programster\AbstractView\AbstractView
{
    public function __construct()
    {

    }


    protected function renderContent()
    {
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Application</title>
</head>
<body>
    <h1>404 - Page Not Found</h1>
    <p>The page you were looking for could not be found.</p>
</body>
</html>


<?php
    }

}
