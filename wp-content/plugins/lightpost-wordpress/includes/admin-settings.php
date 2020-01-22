<?php

if(!class_exists(Admin_Settings)){
    
    class Admin_Settings{
        
        function __construct() {
            
        }
            
        /*
        * Load Settings Page
        * @Since 1.0
        */
        function settings_page(){
            ?>
            <h1>Settings</h1>
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
