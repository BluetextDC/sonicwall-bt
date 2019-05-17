<?php

if(!class_exists(Admin_Analytics)){
    
    class Admin_Analytics{
        
        function __construct() {
            
        }
            
        /*
        * Load Settings Page
        * @Since 1.0
        */
        function analytics_page(){
            ?>
            <h1>Analytics</h1>
            <table>
                <thead>
                    <tr>
                        <th>name</th>
                        <th>enable</th>
                        <th>actions</th>
                    </tr>
                </thead>
            </table>
            <?php
        }
    }
}
