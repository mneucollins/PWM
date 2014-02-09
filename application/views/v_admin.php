<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Peoples Weather Map Admin</title>
        
        <?php 
            echo link_tag('resources/css/style.css');
			include ('includes/jquery_head.incl');
        ?>
    </head>
    <body>
        <div id="container">
            <div id="topsection">
                <div class="innertube">
                    <div id="logo">
                        <img alt="PWM" src="<?php echo resource_url('image/PWM-180.png'); ?>" width="180" height="56">
                        <div class="tagline"> The Peoples Weather Map</div>
                    </div>
                </div>
            </div>
            <div id="contentwrapper">
                <div id="contentcolumn">
                    <div class="innertube">
                        <ul>
                            <li><a href ="admin/import_menu">Load Menu CSV files</a><br />
                                <i>files to be loaded, 'menus.csv' and 'menus_items.csv' must be located in <?php echo base_url("/resources/csv/"); ?></i>
                            </li>
                            <li><a href ="admin/import_popups">Load Popups CSV files</a><br />
                                <i>files to be loaded, must be located in <?php echo FCPATH."resources/csv/"; ?></i>
                            </li>
                        </ul>

                    </div>
                </div>
            </div>
            <div id="footer">
                Peoples Weather Map. Page rendered in <strong>{elapsed_time}</strong> seconds 
            </div>
        </div>
    </body>
</html>