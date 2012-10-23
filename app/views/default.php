<!DOCTYPE html>
<html>
<head>
    <title>Rope Framework - Default View</title>
    <link type="text/css" rel="stylesheet" href="/css/default.css?t=<?php echo time() ?>" />
    <link href='http://fonts.googleapis.com/css?family=Press+Start+2P' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Ropa+Sans' rel='stylesheet' type='text/css'>
</head>
<body>
    <div id="wrapper">
        <div id="welcome-box">
            <h1>Welcome to Rope</h1>
            <p>
                Thank you for using the Rope Framework! You are likely seeing this page because you just installed
                the framework and have not yet changed the default view. The default view file is located at
                <code>/app/view/default.php</code>. 
            </p>
            <h3>Configuration</h3>
            <p>
                Rope Framework is an Open Source PHP Framework that integrates with a RIAK NoSQL Database cluster.
                To get the most out of our framework, please ensure that you have a working RIAK cluster installed
                and change the RIAK configuration settings in <code>/app/config/Default.php</code>. While you're in
                the configuration file, feel free to adjust the settings according what you need for your application.
                In addition to the default configuration settings provided, you are free to add any configuration settings
                of your own. You can do this in the <code>/app/config/Default.php</code> file or you can create entirely
                seprate configuration files as you see fit. Accessing your custom configuration options within your application 
                is incredibly easy! After defining a <code>short_name</code> in your configuration file, just use the following
                code to start looking up options!<br />
                <div class="code-box">
                    <code>
                    $config = $this->getApplication()->getConfig('short_name');<br />
                    $option = $config->get('option');
                    </code>
                </div>
            </p>
            <h3>Urls</h3>
            <p>
                Rope provides an easy to use URL scheme to manage routing within your application. A Rope URL is structured
                as follows: 
                <div class="code-box">
                    <code>http://example.com/index.php/controller/action/param1/value1/.../paramN/valueN</code>.
                </div>
            </p>
            <h3>Overrides</h3>
            <p>
                In the event that Rope Framework does not provide all of the functionality you are looking for, we have made it
                very easy to override or extend core functionality. Simply create the override file in <code>/app/overrides/</code>
                and Rope will automatically detect your override file. You can create an override for any of the files you
                find in the <code>/core/</code> directory at the root of your application. 
            </p>
        </div>
    </div>
</body>
</html>
