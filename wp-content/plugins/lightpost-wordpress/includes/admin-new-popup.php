<?php

if(!class_exists(Admin_New_Popup)){
    
    class Admin_New_Popup{
        
        function __construct() {
            
        }
            
        /*
        * Load New Popup Page
        * @Since 1.0
        */
        function new_popup_page(){
            ?>
            <h1>New popups</h1>
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
